<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CompanyHelper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\AttendanceRecord;
use App\Models\Payroll;
use App\Models\Period;
use App\Services\PayrollGenerationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ReflectionMethod;
use Throwable;

class PeriodManagementController extends Controller
{
    /**
     * Display all payroll periods.
     */
    public function index()
    {
        $user = auth()->user() ?? (object) ['role' => 'admin'];
        $currentCompany = CompanyHelper::getCurrentCompany();

        $periods = Period::with(['company', 'previousPeriod'])
            ->when($currentCompany, function ($query) use ($currentCompany) {
                $query->where(function ($companyQuery) use ($currentCompany) {
                    $companyQuery
                        ->where('company_id', $currentCompany->id)
                        ->orWhereNull('company_id');
                });
            })
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->orderByDesc('period_no')
            ->orderByDesc('created_at')
            ->get();

        return view(
            'attendance.period-management.index',
            compact('user', 'periods', 'currentCompany')
        );
    }


    /**
     * Show the create payroll-period form.
     */
    public function create()
    {
        $user = auth()->user() ?? (object) ['role' => 'admin'];
        $currentCompany = CompanyHelper::getCurrentCompany();

        $departments = Department::query()
            ->when($currentCompany, function ($query) use ($currentCompany) {
                $query->where('company_id', $currentCompany->id);
            })
            ->orderBy('name')
            ->get();

        $employees = Employee::with('department')
            ->when($currentCompany, function ($query) use ($currentCompany) {
                $query->where(function ($employeeQuery) use ($currentCompany) {
                    $employeeQuery
                        ->where('company_id', $currentCompany->id)
                        ->orWhereHas('department', function ($departmentQuery) use ($currentCompany) {
                            $departmentQuery->where('company_id', $currentCompany->id);
                        });
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view(
            'attendance.period-management.create',
            compact('user', 'departments', 'employees', 'currentCompany')
        );
    }


    /**
     * Save a new payroll period.
     */
    public function store(Request $request)
    {
        if (
            $request->input('period_type', 'regular') === 'regular'
            && in_array((int) $request->input('period_no'), [1, 2], true)
            && $request->filled(['period_month', 'period_year'])
        ) {
            $request->merge($this->standardPeriodDates(
                (int) $request->input('period_year'),
                (int) $request->input('period_month'),
                (int) $request->input('period_no')
            ));
        }

        $validated = $request->validate([
            'period_month' => ['required', 'integer', 'between:1,12'],
            'period_year' => ['required', 'integer', 'between:2000,2100'],
            'period_no' => ['required', 'integer', 'between:1,5'],
            'payroll_date' => ['required', 'date', 'after_or_equal:end_date'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'period_type' => ['required', 'in:regular,special,final_pay,13th_month'],
            'processing_type' => ['required', 'in:regular,resigned_only,leaves_only'],
            'description' => ['nullable', 'string', 'max:2000'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
        ]);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->startOfDay();
        $payrollDate = Carbon::parse($validated['payroll_date'])->startOfDay();

        $company = CompanyHelper::getCurrentCompany();

        if (!empty($validated['department_id'])) {
            $department = Department::findOrFail($validated['department_id']);

            if ($company && $department->company_id !== $company->id) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'department_id' => 'The selected department does not belong to the active company.',
                    ]);
            }

            if (!$company && $department->company_id) {
                $company = Company::find($department->company_id);
            }
        }

        if (!$company) {
            return back()
                ->withInput()
                ->withErrors([
                    'company_id' => 'Select an active company before creating a payroll period.',
                ]);
        }

        $companyId = $company->id;

        $duplicateSequenceExists = Period::query()
            ->where('company_id', $companyId)
            ->where('period_month', $validated['period_month'])
            ->where('period_year', $validated['period_year'])
            ->where('period_no', $validated['period_no'])
            ->exists();

        if ($duplicateSequenceExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'period_no' => 'This month, year, and period number already exists for the active company.',
                ]);
        }

        $overlappingPeriod = Period::query()
            ->where('company_id', $companyId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query
                    ->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('end_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhere(function ($coversQuery) use ($startDate, $endDate) {
                        $coversQuery
                            ->where('start_date', '<=', $startDate->toDateString())
                            ->where('end_date', '>=', $endDate->toDateString());
                    });
            })
            ->first();

        if ($overlappingPeriod) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => "The selected dates overlap with {$overlappingPeriod->name}.",
                ]);
        }

        $employeeIds = $validated['employee_ids'] ?? [];

        // Every selected employee must belong to GR8 TECH ENTERPRISE INC.
        if (!empty($employeeIds)) {
            $validEmployeeCount = Employee::query()
                ->whereIn('id', $employeeIds)
                ->where(function ($query) use ($companyId) {
                    $query
                        ->where('company_id', $companyId)
                        ->orWhereHas(
                            'department',
                            function ($departmentQuery) use ($companyId) {
                                $departmentQuery->where(
                                    'company_id',
                                    $companyId
                                );
                            }
                        );
                })
                ->count();

            if ($validEmployeeCount !== count(array_unique($employeeIds))) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'employee_ids' => 'One or more selected employees do not belong to the active company.',
                    ]);
            }
        }

        $previousPeriod = Period::query()
            ->where('company_id', $companyId)
            ->where('end_date', '<', $startDate->toDateString())
            ->orderByDesc('end_date')
            ->first();

        $workingDays = $this->countWeekdays($startDate, $endDate);
        $periodName = Carbon::create(
            $validated['period_year'],
            $validated['period_month'],
            1
        )->format('F Y') . ' - Period ' . $validated['period_no'];

        DB::transaction(function () use (
            $validated,
            $companyId,
            $previousPeriod,
            $workingDays,
            $periodName,
            $employeeIds,
            $startDate,
            $endDate,
            $payrollDate
        ) {
            Period::create([
                'company_id' => $companyId,
                'previous_period_id' => $previousPeriod?->id,
                'name' => $periodName,
                'description' => $validated['description'] ?? null,
                'period_month' => $validated['period_month'],
                'period_year' => $validated['period_year'],
                'period_no' => $validated['period_no'],
                'period_type' => $validated['period_type'],
                'processing_type' => $validated['processing_type'],
                'payroll_date' => $payrollDate->toDateString(),
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'working_days' => $workingDays,
                'status' => Period::STATUS_DRAFT,
                'department_id' => $validated['department_id'] ?? null,
                'employee_ids' => $employeeIds,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('attendance.period-management.index')
            ->with('success', "{$periodName} was created as Draft.");
    }


    /**
     * Show one period with comprehensive attendance information.
     */
    public function show($id)
    {
        $user = auth()->user() ?? (object) ['role' => 'admin'];

        $period = Period::with([
            'company',
            'department',
        ])->findOrFail($id);

        $startDate = Carbon::parse($period->start_date)->startOfDay();
        $endDate = Carbon::parse($period->end_date)->startOfDay();

        /*
         * Find the employees included in this period.
         */
        $employeesQuery = Employee::with('department');

        $employeeIds = $this->normalizeEmployeeIds($period->employee_ids);

        if (!empty($employeeIds)) {
            $employeesQuery->whereIn('id', $employeeIds);
        } elseif (!empty($period->department_id)) {
            $employeesQuery->where(
                'department_id',
                $period->department_id
            );
        } else {
            /*
             * No employee or department filter was selected.
             * Restrict employees to the current company where possible.
             */
            $currentCompany = CompanyHelper::getCurrentCompany();

            if ($currentCompany) {
                $employeesQuery->where(function ($query) use ($currentCompany) {
                    $query
                        ->where('company_id', $currentCompany->id)
                        ->orWhereHas(
                            'department',
                            function ($departmentQuery) use ($currentCompany) {
                                $departmentQuery->where(
                                    'company_id',
                                    $currentCompany->id
                                );
                            }
                        );
                });
            }
        }

        $employees = $employeesQuery
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        /*
         * Build the same comprehensive attendance structure used by
         * PayrollController. This prevents the period page and payroll
         * generation from calculating attendance differently.
         */
        $comprehensiveData = $this->getComprehensiveAttendanceData(
            $startDate,
            $endDate,
            $employees
        );

        /*
         * Build the summary required by show.blade.php.
         */
        $summaryData = $this->buildSummaryData(
            $comprehensiveData,
            $employees
        );

        $scheduleExceptions = collect($comprehensiveData)
            ->filter(fn ($record) => !empty($record['validation_issues']))
            ->values();

        $validationSummary = [
            'attendance' => [
                'label' => 'Attendance',
                'validated' => $period->isComponentValidated('attendance'),
                'validated_at' => $period->attendance_validated_at,
            ],
            'leave' => [
                'label' => 'Leave',
                'validated' => $period->isComponentValidated('leave'),
                'validated_at' => $period->leave_validated_at,
            ],
            'ob' => [
                'label' => 'Official Business',
                'validated' => $period->isComponentValidated('ob'),
                'validated_at' => $period->ob_validated_at,
            ],
            'overtime' => [
                'label' => 'Overtime',
                'validated' => $period->isComponentValidated('overtime'),
                'validated_at' => $period->overtime_validated_at,
            ],
        ];

        $existingPayrolls = Payroll::query()
            ->where('period_id', $period->id)
            ->get();

        return view(
            'attendance.period-management.show',
            compact(
                'user',
                'period',
                'employees',
                'comprehensiveData',
                'summaryData',
                'scheduleExceptions',
                'validationSummary',
                'existingPayrolls'
            )
        );
    }

    /**
     * Delete a draft period that has no generated payroll records.
     */
    public function destroy($id)
    {
        $period = Period::findOrFail($id);

        if (!$period->canBeDeleted()) {
            return back()->with(
                'error',
                'Only Draft periods without generated payroll records can be deleted.'
            );
        }

        $period->delete();

        return redirect()
            ->route('attendance.period-management.index')
            ->with('success', 'Payroll period deleted successfully.');
    }

    /**
     * Move a payroll period to its next allowed status.
     */
    public function updateStatus(Request $request, $period)
    {
        $periodModel = Period::findOrFail($period);

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', Period::STATUSES)],
        ]);

        $targetStatus = $validated['status'];

        if (!$periodModel->canTransitionTo($targetStatus)) {
            return back()->with(
                'error',
                "The period cannot move from {$periodModel->status_label} to "
                . Period::labelForStatus($targetStatus)
                . '.'
            );
        }

        if (
            $targetStatus === Period::STATUS_READY
            && !$periodModel->hasCompletedValidation()
        ) {
            return back()->with(
                'error',
                'Attendance, Leave, Official Business, and Overtime must all be validated before the period can become Ready for Payroll.'
            );
        }

        if ($targetStatus === Period::STATUS_PROCESSING) {
            return back()->with(
                'warning',
                'Review the payroll preview, then use Confirm & Generate Payroll to move this period into Processing.'
            );
        }

        /*
         * PHASE 3:
         * These transitions remain preserved but unavailable until review,
         * finalization, and locking controls are completed.
         */
        if (in_array($targetStatus, [
            Period::STATUS_PROCESSING,
            Period::STATUS_FOR_REVIEW,
            Period::STATUS_FINALIZED,
            Period::STATUS_LOCKED,
        ], true)) {
            return back()->with(
                'warning',
                'Use the dedicated payroll action for Processing, Review, Finalize, or Lock.'
            );
        }

        if ($targetStatus === Period::STATUS_FOR_VALIDATION) {
            $createdSchedules = $this->ensureDefaultSchedulesForPeriod($periodModel);
        } else {
            $createdSchedules = 0;
        }

        $updates = ['status' => $targetStatus];

        if ($targetStatus === Period::STATUS_READY) {
            $updates['ready_at'] = now();
            $updates['ready_by'] = auth()->id();
        }

        $periodModel->update($updates);

        $message = 'Period status updated to '
            . $periodModel->fresh()->status_label
            . '.';

        if ($createdSchedules > 0) {
            $message .= " {$createdSchedules} default schedule record(s) were assigned (Mon-Sat 8:00 AM-5:00 PM, Sunday day off).";
        }

        return back()->with('success', $message);
    }

    /**
     * Confirm one pre-payroll validation component.
     */
    public function validateComponent(
        Request $request,
        $period,
        string $component
    ) {
        $periodModel = Period::findOrFail($period);

        if (!in_array($component, Period::VALIDATION_COMPONENTS, true)) {
            abort(404);
        }

        if (!in_array($periodModel->status, [
            Period::STATUS_FOR_VALIDATION,
            Period::STATUS_READY,
        ], true)) {
            return back()->with(
                'error',
                'Move the period to For Validation before confirming validation items.'
            );
        }

        $validated = $request->validate([
            'validation_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $validationReport = $this->inspectValidationComponent($periodModel, $component);

        if (!empty($validationReport['errors'])) {
            return back()->with('error', implode(' ', $validationReport['errors']));
        }

        $fields = Period::validationFieldsFor($component);
        $results = $periodModel->validation_results ?? [];
        $results[$component] = $validationReport;

        $periodModel->update([
            $fields['date'] => now(),
            $fields['user'] => auth()->id(),
            'validation_notes' => $validated['validation_notes']
                ?? $periodModel->validation_notes,
            'validation_results' => $results,
        ]);

        $freshPeriod = $periodModel->fresh();

        if (
            $freshPeriod->hasCompletedValidation()
            && $freshPeriod->status === Period::STATUS_FOR_VALIDATION
        ) {
            $freshPeriod->update([
                'status' => Period::STATUS_READY,
                'ready_at' => now(),
                'ready_by' => auth()->id(),
            ]);

            return back()->with(
                'success',
                ucfirst($component)
                . ' validated. All validation gates are complete, so the period is now Ready for Payroll.'
            );
        }

        return back()->with(
            'success',
            ucfirst($component) . ' validation confirmed.'
        );
    }

    /**
     * Reset one validation component before payroll generation.
     */
    public function resetValidationComponent($period, string $component)
    {
        $periodModel = Period::findOrFail($period);

        if (!in_array($component, Period::VALIDATION_COMPONENTS, true)) {
            abort(404);
        }

        if ($periodModel->hasGeneratedPayrolls()) {
            return back()->with(
                'error',
                'Validation cannot be reset because payroll records already exist for this period.'
            );
        }

        $fields = Period::validationFieldsFor($component);

        $updates = [
            $fields['date'] => null,
            $fields['user'] => null,
        ];

        if ($periodModel->status === Period::STATUS_READY) {
            $updates['status'] = Period::STATUS_FOR_VALIDATION;
            $updates['ready_at'] = null;
            $updates['ready_by'] = null;
        }

        $periodModel->update($updates);

        return back()->with(
            'success',
            ucfirst($component) . ' validation was reset.'
        );
    }


    /**
     * Refresh the cutoff snapshot and invalidate validations when source data changed.
     */
    public function refreshCutoffData($period)
    {
        $periodModel = Period::findOrFail($period);

        if (in_array($periodModel->status, [Period::STATUS_FINALIZED, Period::STATUS_LOCKED], true)) {
            return back()->with('error', 'Finalized or locked periods cannot be refreshed.');
        }

        if ($periodModel->hasGeneratedPayrolls()) {
            return back()->with('error', 'Return and remove/regenerate payroll records before refreshing cutoff data.');
        }

        $employees = $this->employeesForPeriod(
            $periodModel,
            $this->normalizeEmployeeIds($periodModel->employee_ids)
        );
        $data = $this->getComprehensiveAttendanceData(
            Carbon::parse($periodModel->start_date),
            Carbon::parse($periodModel->end_date),
            $employees
        );
        $hash = hash('sha256', json_encode($data));
        $changed = $periodModel->source_data_hash !== null
            && !hash_equals((string) $periodModel->source_data_hash, $hash);

        $updates = [
            'last_refreshed_at' => now(),
            'last_refreshed_by' => auth()->id(),
            'source_data_hash' => $hash,
        ];

        if ($changed) {
            foreach (Period::VALIDATION_COMPONENTS as $component) {
                $fields = Period::validationFieldsFor($component);
                $updates[$fields['date']] = null;
                $updates[$fields['user']] = null;
            }
            $updates['validation_results'] = null;
            $updates['ready_at'] = null;
            $updates['ready_by'] = null;
            $updates['status'] = Period::STATUS_FOR_VALIDATION;
        }

        $periodModel->update($updates);

        return back()->with(
            'success',
            $changed
                ? 'Cutoff data changed. All validation gates were reset.'
                : 'Current cutoff data was refreshed successfully.'
        );
    }

    /**
     * Run practical blocking checks before HR confirms a validation gate.
     */
    private function inspectValidationComponent(Period $periodModel, string $component): array
    {
        $employees = $this->employeesForPeriod(
            $periodModel,
            $this->normalizeEmployeeIds($periodModel->employee_ids)
        );
        $records = collect($this->getComprehensiveAttendanceData(
            Carbon::parse($periodModel->start_date),
            Carbon::parse($periodModel->end_date),
            $employees
        ));

        $errors = [];
        $warnings = [];

        if ($employees->isEmpty()) {
            $errors[] = 'No eligible employees are covered by this payroll period.';
        }

        if ($component === 'attendance') {
            if ($records->isEmpty()) {
                $errors[] = 'No attendance data was found for the cutoff.';
            }

            $hasIssue = fn ($record, string $issue) => in_array($issue, $record['validation_issues'] ?? [], true);

            $missingSchedules = $records->filter(fn ($r) => $hasIssue($r, 'No Schedule'))->count();
            $incompleteLogs = $records->filter(fn ($r) => $hasIssue($r, 'Incomplete Log'))->count();
            $invalidDurations = $records->filter(fn ($r) => $hasIssue($r, 'Invalid Duration'))->count();
            $possibleWrongSchedules = $records->filter(fn ($r) => $hasIssue($r, 'Possible Wrong Schedule'))->count();
            $restDayDutyReviews = $records->filter(fn ($r) => $hasIssue($r, 'Rest Day Duty Review'))->count();

            if ($missingSchedules > 0) {
                $errors[] = "$missingSchedules employee-day record(s) have no assigned schedule. Assign or correct the schedule before validating attendance.";
            }

            if ($incompleteLogs > 0) {
                $errors[] = "$incompleteLogs attendance record(s) have an incomplete time-in/time-out pair.";
            }

            if ($invalidDurations > 0) {
                $errors[] = "$invalidDurations attendance record(s) contain an impossible or cross-date duration. Correct the time entries before validating attendance.";
            }

            if ($possibleWrongSchedules > 0) {
                $errors[] = "$possibleWrongSchedules attendance record(s) are four or more hours outside the assigned shift and may have the wrong schedule.";
            }

            if ($restDayDutyReviews > 0) {
                $warnings[] = "$restDayDutyReviews rest-day attendance record(s) require manager review and the applicable approved duty/overtime filing.";
            }

            $presentWithoutWorkedHours = $records->filter(fn ($r) => $hasIssue($r, 'Zero Worked Hours'))->count();

            if ($presentWithoutWorkedHours > 0) {
                $errors[] = "$presentWithoutWorkedHours present attendance record(s) have zero or invalid computed working hours.";
            }

            $assumedAbsences = $records->filter(function ($record) {
                return ($record['schedule_status'] ?? null) === 'Working'
                    && ($record['attendance_status'] ?? null) === 'Absent'
                    && empty($record['has_attendance_record']);
            })->count();

            if ($assumedAbsences > 0) {
                $warnings[] = "$assumedAbsences scheduled working day(s) have no bio, leave, or OB and will be treated as absent for payroll.";
            }

            $orphanOfficialBusiness = $records->filter(fn ($r) => $hasIssue($r, 'Unverified Official Business'))->count();

            if ($orphanOfficialBusiness > 0) {
                $errors[] = "$orphanOfficialBusiness attendance record(s) are marked Official Business without a matching approved OB request.";
            }

            $invalidRestDayHours = EmployeeSchedule::query()
                ->whereIn('employee_id', $employees->pluck('id'))
                ->whereBetween('date', [$periodModel->start_date, $periodModel->end_date])
                ->whereIn('status', ['Day Off', 'Rest Day'])
                ->where('required_hours', '>', 0)
                ->count();

            if ($invalidRestDayHours > 0) {
                $errors[] = "$invalidRestDayHours day-off/rest-day schedule(s) still contain required hours. Set required hours to zero or mark the schedule Working.";
            }
        } elseif ($component === 'leave') {
            $hasIssue = fn ($record, string $issue) => in_array($issue, $record['validation_issues'] ?? [], true);

            $leaveConflicts = $records->filter(fn ($r) => $hasIssue($r, 'Leave Conflict'))->count();

            if ($leaveConflicts > 0) {
                $errors[] = "$leaveConflicts approved leave day(s) overlap worked attendance, approved OB, or approved overtime. Correct or cancel the conflicting request before validating leave.";
            }
        } elseif ($component === 'ob') {
            $hasIssue = fn ($record, string $issue) => in_array($issue, $record['validation_issues'] ?? [], true);

            $orphanOfficialBusiness = $records->filter(fn ($r) => $hasIssue($r, 'Unverified Official Business'))->count();

            if ($orphanOfficialBusiness > 0) {
                $errors[] = "$orphanOfficialBusiness attendance record(s) are marked Official Business without a matching approved OB request.";
            }
        } elseif ($component === 'overtime') {
            $hasIssue = fn ($record, string $issue) => in_array($issue, $record['validation_issues'] ?? [], true);

            $otWithoutAttendance = $records->filter(fn ($r) => $hasIssue($r, 'OT Without Attendance'))->count();
            if ($otWithoutAttendance > 0) {
                $errors[] = "$otWithoutAttendance overtime record(s) do not have complete attendance logs.";
            }

            $otBeforeRequiredHours = $records->filter(fn ($r) => $hasIssue($r, 'OT Before Required Hours'))->count();

            if ($otBeforeRequiredHours > 0) {
                $errors[] = "$otBeforeRequiredHours approved overtime record(s) were filed before the employee completed the required working hours.";
            }

            $overtimeOnLeave = $records->filter(fn ($r) => $hasIssue($r, 'OT Overlaps Leave'))->count();

            if ($overtimeOnLeave > 0) {
                $errors[] = "$overtimeOnLeave approved overtime record(s) overlap approved leave.";
            }
        }

        return [
            'passed' => empty($errors),
            'checked_at' => now()->toIso8601String(),
            'record_count' => $records->count(),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Re-run blocking rules immediately before Preview or Generate.
     * Validation timestamps are an audit trail, not a replacement for
     * checking the current cutoff data.
     */
    private function currentPayrollReadinessErrors(Period $periodModel): array
    {
        $componentErrors = collect(Period::VALIDATION_COMPONENTS)
            ->flatMap(function (string $component) use ($periodModel) {
                $report = $this->inspectValidationComponent($periodModel, $component);

                return $report['errors'] ?? [];
            })
            ->unique()
            ->values()
            ->all();

        $loanErrors = $this->employeesForPeriod(
            $periodModel,
            $this->normalizeEmployeeIds($periodModel->employee_ids)
        )->filter(function (Employee $employee) {
            $hasLoan = (float) ($employee->loan_total_amount ?? 0) > 0
                || (float) ($employee->loan_monthly_amortization ?? 0) > 0;

            if (!$hasLoan) {
                return false;
            }

            return !$employee->loan_start_date
                || !$employee->loan_end_date
                || (float) ($employee->loan_total_amount ?? 0) <= 0
                || (float) ($employee->loan_monthly_amortization ?? 0) <= 0
                || $employee->loan_end_date->lt($employee->loan_start_date);
        })->count();

        if ($loanErrors > 0) {
            $componentErrors[] = "$loanErrors employee loan record(s) have incomplete dates, total amount, or monthly amortization.";
        }

        return collect($componentErrors)->unique()->values()->all();
    }

    /**
     * Preview payroll without saving records.
     */
    public function previewPayroll(
        $period,
        PayrollGenerationService $payrollService
    ) {
        $periodModel = Period::findOrFail($period);

        if ($periodModel->status !== Period::STATUS_READY
            || !$periodModel->hasCompletedValidation()) {
            return back()->with(
                'error',
                'Complete all four validation gates before previewing payroll.'
            );
        }

        $readinessErrors = $this->currentPayrollReadinessErrors($periodModel);
        if (!empty($readinessErrors)) {
            return back()->with(
                'error',
                'Payroll preview is blocked because current cutoff data no longer passes validation. '
                . implode(' ', $readinessErrors)
                . ' Reset the affected validation gate, resolve the issue, and validate again.'
            );
        }

        try {
            $startDate = Carbon::parse($periodModel->start_date);
            $endDate = Carbon::parse($periodModel->end_date);
            $employeeIds = $this->normalizeEmployeeIds($periodModel->employee_ids);
            $employees = $this->employeesForPeriod($periodModel, $employeeIds);

            if ($employees->isEmpty()) {
                return back()->with('error', 'No employees were found for this period.');
            }

            $comprehensiveData = $this->getComprehensiveAttendanceData(
                $startDate,
                $endDate,
                $employees
            );

            if (empty($comprehensiveData)) {
                return back()->with('error', 'No attendance records were found for this period.');
            }

            $periodData = $this->periodData($periodModel, $employeeIds);
            $previewPayrolls = $payrollService->generatePayrollPreview(
                $periodData,
                $comprehensiveData,
                !empty($employeeIds) ? $employeeIds : null
            );

            if (empty($previewPayrolls)) {
                return back()->with('error', 'No payroll preview could be generated.');
            }

            $summaryData = $this->buildPayrollArraySummary($previewPayrolls);
            $period = array_merge($periodData, [
                'department_name' => optional($periodModel->department)->name,
            ]);
            $user = auth()->user() ?? (object) ['role' => 'admin'];
            $generatedAt = now();

            if (request()->routeIs('payroll.periods.preview-pdf')) {
                return \Barryvdh\DomPDF\Facade\Pdf::loadView(
                    'payroll.preview-pdf',
                    compact('user', 'period', 'previewPayrolls', 'summaryData', 'generatedAt')
                )
                    ->setPaper('a4', 'landscape')
                    ->download('payroll-preview-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.pdf');
            }

            return view(
                'payroll.preview',
                compact('user', 'period', 'previewPayrolls', 'summaryData', 'generatedAt')
            );
        } catch (Throwable $exception) {
            Log::error('Period payroll preview failed', [
                'period_id' => $periodModel->id,
                'message' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Unable to preview payroll: ' . $exception->getMessage());
        }
    }

    /**
     * Generate payroll for the selected period.
     */
    public function generatePayroll(
        Request $request,
        $period,
        PayrollGenerationService $payrollService
    ) {
        $periodModel = Period::findOrFail($period);

        if ($periodModel->status !== Period::STATUS_READY) {
            return back()->with(
                'error',
                'Payroll can only be generated when the period is Ready for Payroll.'
            );
        }

        if (!$periodModel->hasCompletedValidation()) {
            return back()->with(
                'error',
                'Attendance, Leave, Official Business, and Overtime must all be validated before payroll generation.'
            );
        }


        $readinessErrors = $this->currentPayrollReadinessErrors($periodModel);
        if (!empty($readinessErrors)) {
            return back()->with(
                'error',
                'Payroll generation is blocked because current cutoff data no longer passes validation. '
                . implode(' ', $readinessErrors)
                . ' Reset the affected validation gate, resolve the issue, and validate again.'
            );
        }

        $validated = $request->validate([
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
            'payroll_template_id' => [
                'nullable',
                'exists:payroll_templates,id',
            ],
        ]);

        try {
            $startDate = Carbon::parse($periodModel->start_date);
            $endDate = Carbon::parse($periodModel->end_date);

            $employeeIds = $validated['employee_ids']
                ?? $this->normalizeEmployeeIds($periodModel->employee_ids);

            $existingPayrollQuery = Payroll::query()
                ->where('period_id', $periodModel->id);

            if (!empty($employeeIds)) {
                $existingPayrollQuery->whereIn('employee_id', $employeeIds);
            }

            if ($existingPayrollQuery->exists()) {
                return back()->with(
                    'warning',
                    'Payroll records already exist for this period. Duplicate generation was prevented.'
                );
            }

            $employees = $this->employeesForPeriod($periodModel, $employeeIds);

            if ($employees->isEmpty()) {
                return back()->with(
                    'error',
                    'No employees were found for this period.'
                );
            }

            $comprehensiveData = $this->getComprehensiveAttendanceData(
                $startDate,
                $endDate,
                $employees
            );

            if (empty($comprehensiveData)) {
                return back()->with(
                    'error',
                    'No attendance records were found for this period.'
                );
            }

            $periodData = [
                'id' => $periodModel->id,
                'name' => $periodModel->name,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'days_in_period' => $startDate->diffInDays($endDate) + 1,
                'department_id' => $periodModel->department_id,
                'employee_ids' => $employeeIds,
            ];

            $claimed = Period::query()
                ->whereKey($periodModel->id)
                ->where('status', Period::STATUS_READY)
                ->update(['status' => Period::STATUS_PROCESSING]);

            if ($claimed !== 1) {
                return back()->with('warning', 'Payroll generation is already in progress or the period status changed.');
            }

            $periodModel->refresh();

            $generatedPayrolls =
                $payrollService->generatePayrollFromComprehensiveData(
                    $periodData,
                    $comprehensiveData,
                    !empty($employeeIds) ? $employeeIds : null,
                    $validated['payroll_template_id'] ?? null
                );

            if (empty($generatedPayrolls)) {
                return back()->with(
                    'error',
                    'No payroll records were generated. Check ' .
                    'storage/logs/laravel.log for the specific payroll error.'
                );
            }

            $periodModel->update([
                'status' => Period::STATUS_FOR_REVIEW,
                'reviewed_at' => null,
                'reviewed_by' => null,
                'finalized_at' => null,
                'finalized_by' => null,
                'locked_at' => null,
                'locked_by' => null,
            ]);

            return redirect()
                ->route('payroll.periods.review', $periodModel->id)
                ->with(
                    'success',
                    count($generatedPayrolls)
                    . ' payroll record(s) generated successfully. Review the results before finalizing.'
                );
        } catch (Throwable $exception) {
            if (isset($periodModel) && $periodModel->fresh()?->status === Period::STATUS_PROCESSING) {
                $periodModel->update(['status' => Period::STATUS_READY]);
            }

            Log::error('Period payroll generation failed', [
                'period_id' => $periodModel->id,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return back()->with(
                'error',
                'Failed to generate payroll: ' .
                $exception->getMessage()
            );
        }
    }

    /**
     * Display payroll records belonging to the period.
     */
    public function showPayrollSummary($period)
    {
        $periodModel = Period::findOrFail($period);

        $payrolls = Payroll::with(['employee', 'employee.department', 'employee.position'])
            ->where('period_id', $periodModel->id)
            ->get();

        if ($payrolls->isEmpty()) {
            return back()->with(
                'info',
                'No payroll records are available for this period.'
            );
        }

        $summaryData = $this->buildPayrollModelSummary($payrolls);
        $period = array_merge($this->periodData($periodModel, $this->normalizeEmployeeIds($periodModel->employee_ids)), [
            'department_name' => optional($periodModel->department)->name,
            'status' => $periodModel->status,
            'status_label' => $periodModel->status_label,
        ]);
        $user = auth()->user() ?? (object) ['role' => 'admin'];

        return view(
            'attendance.period-management.payroll-summary',
            compact('user', 'period', 'periodModel', 'payrolls', 'summaryData')
        );
    }

    /**
     * Submit corrected payroll records back for review.
     */
    public function submitForReview($period)
    {
        $periodModel = Period::findOrFail($period);

        if ($periodModel->status !== Period::STATUS_PROCESSING) {
            return back()->with('error', 'Only a payroll in Processing can be submitted for review.');
        }

        if (!$periodModel->hasGeneratedPayrolls()) {
            return back()->with('error', 'No payroll records exist for this period.');
        }

        $readinessErrors = $this->currentPayrollReadinessErrors($periodModel);
        if (!empty($readinessErrors)) {
            return back()->with(
                'error',
                'Payroll cannot be submitted for review because the source data no longer passes validation. '
                . implode(' ', $readinessErrors)
            );
        }

        $periodModel->update([
            'status' => Period::STATUS_FOR_REVIEW,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ]);

        return redirect()
            ->route('payroll.periods.review', $periodModel->id)
            ->with('success', 'Corrected payroll was submitted for review.');
    }

    /**
     * Return a generated payroll to Processing for corrections/regeneration.
     */
    public function returnToProcessing(Request $request, $period)
    {
        $periodModel = Period::findOrFail($period);

        if ($periodModel->status !== Period::STATUS_FOR_REVIEW) {
            return back()->with('error', 'Only payrolls under review can be returned to Processing.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($periodModel, $validated) {
            $this->payrollsForPeriod($periodModel)->each->delete();

            $updates = [
                'status' => Period::STATUS_FOR_VALIDATION,
                'ready_at' => null,
                'ready_by' => null,
                'reviewed_at' => null,
                'reviewed_by' => null,
                'validation_results' => null,
                'validation_notes' => trim(($periodModel->validation_notes ? $periodModel->validation_notes . "
" : '')
                    . 'Returned for source correction and regeneration: ' . $validated['reason']),
            ];

            foreach (Period::VALIDATION_COMPONENTS as $component) {
                $fields = Period::validationFieldsFor($component);
                $updates[$fields['date']] = null;
                $updates[$fields['user']] = null;
            }

            $periodModel->update($updates);
        });

        return redirect()
            ->route('attendance.period-management.show', $periodModel->id)
            ->with('success', 'Generated payroll was removed. Correct the source records, refresh the cutoff, validate again, and regenerate payroll.');
    }

    /**
     * Mark reviewed payroll as final.
     */
    public function finalizePayroll(Request $request, $period)
    {
        $periodModel = Period::findOrFail($period);

        if ($periodModel->status !== Period::STATUS_FOR_REVIEW) {
            return back()->with('error', 'Only payrolls under review can be finalized.');
        }

        $payrolls = $this->payrollsForPeriod($periodModel);
        if ($payrolls->isEmpty()) {
            return back()->with('error', 'No payroll records exist for this period.');
        }

        $readinessErrors = $this->currentPayrollReadinessErrors($periodModel);
        if (!empty($readinessErrors)) {
            return back()->with(
                'error',
                'Payroll cannot be finalized because the current source data has blocking conflicts. '
                . implode(' ', $readinessErrors)
                . ' Return it to Processing, correct the source data, validate again, and regenerate payroll.'
            );
        }

        $expectedEmployees = $this->employeesForPeriod(
            $periodModel,
            $this->normalizeEmployeeIds($periodModel->employee_ids)
        );
        $missingEmployees = $expectedEmployees->pluck('id')->diff($payrolls->pluck('employee_id'));
        if ($missingEmployees->isNotEmpty()) {
            return back()->with('error', $missingEmployees->count() . ' expected employee(s) do not have generated payroll records.');
        }

        $invalidNetPay = $payrolls->filter(fn ($payroll) => (float) $payroll->net_pay < 0);
        if ($invalidNetPay->isNotEmpty()) {
            return back()->with('error', $invalidNetPay->count() . ' payroll record(s) have negative net pay. Resolve them before finalization.');
        }

        DB::transaction(function () use ($periodModel, $payrolls) {
            foreach ($payrolls as $payroll) {
                $payroll->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'processed_at' => $payroll->processed_at ?? now(),
                ]);
            }

            $periodModel->update([
                'status' => Period::STATUS_FINALIZED,
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'finalized_at' => now(),
                'finalized_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Payroll finalized successfully. Review is complete and values are now frozen for locking.');
    }

    /**
     * Lock a finalized payroll period. Locked periods are view/export only.
     */
    public function lockPayroll(Request $request, $period)
    {
        $periodModel = Period::findOrFail($period);

        if ($periodModel->status !== Period::STATUS_FINALIZED) {
            return back()->with('error', 'Only finalized payroll can be locked.');
        }

        if (!$periodModel->hasGeneratedPayrolls()) {
            return back()->with('error', 'No payroll records exist for this period.');
        }

        $periodModel->update([
            'status' => Period::STATUS_LOCKED,
            'locked_at' => now(),
            'locked_by' => auth()->id(),
        ]);

        return back()->with('success', 'Payroll period locked. Payroll records are now view/export only.');
    }

    /**
     * Reverse a lock. Brings a Locked period back to Finalized — view/export
     * restrictions are lifted, but the period is still "generated" (payroll
     * data is untouched and Leave/OB/Overtime edits remain blocked). This is
     * the lightweight half of correcting a mistake; reopenPeriod() is the
     * heavier half that actually unblocks editing.
     */
    public function unlockPayroll(Request $request, $period)
    {
        if ((auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Only an Admin can unlock a payroll period.');
        }

        $periodModel = Period::findOrFail($period);

        if ($periodModel->status !== Period::STATUS_LOCKED) {
            return back()->with('error', 'Only a locked payroll period can be unlocked.');
        }

        $periodModel->update([
            'status' => Period::STATUS_FINALIZED,
            'unlocked_at' => now(),
            'unlocked_by' => auth()->id(),
        ]);

        return back()->with('success', 'Payroll period unlocked and returned to Finalized.');
    }

    /**
     * Wind a Finalized or Locked period back to Ready — before payroll
     * generation — so Admin/HR/Manager can edit approved Leave/OB/Overtime
     * requests again and the Admin can regenerate payroll afterward.
     *
     * Blocked outright if any payroll for the period has already been
     * marked Paid: money has moved, so that data can no longer be silently
     * recalculated and needs manual reconciliation instead.
     */
    public function reopenPeriod(Request $request, $period)
    {
        if ((auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Only an Admin can reopen a payroll period.');
        }

        $periodModel = Period::findOrFail($period);

        if (!in_array($periodModel->status, [Period::STATUS_FINALIZED, Period::STATUS_LOCKED], true)) {
            return back()->with('error', 'Only a finalized or locked payroll period can be reopened.');
        }

        $validated = $request->validate([
            'reopen_reason' => ['required', 'string', 'max:1000'],
        ]);

        $payrolls = $this->payrollsForPeriod($periodModel);

        if ($payrolls->contains(fn ($payroll) => $payroll->status === 'paid')) {
            return back()->with(
                'error',
                'Cannot reopen — one or more payroll records for this period are already marked Paid. Those require manual reconciliation instead of a reopen.'
            );
        }

        DB::transaction(function () use ($periodModel, $payrolls, $validated) {
            // If still locked, lift the lock first — Payroll::assertLockedPeriodUpdateIsAllowed()
            // only permits a locked payroll to move to "paid", so the period
            // must leave Locked status before payroll rows below can be reset.
            if ($periodModel->status === Period::STATUS_LOCKED) {
                $periodModel->update([
                    'status' => Period::STATUS_FINALIZED,
                    'unlocked_at' => now(),
                    'unlocked_by' => auth()->id(),
                ]);
            }

            foreach ($payrolls as $payroll) {
                if (in_array($payroll->status, ['approved', 'processed'], true)) {
                    $payroll->update([
                        'status' => 'pending',
                        'approved_by' => null,
                        'approved_at' => null,
                    ]);
                }
            }

            $periodModel->update([
                'status' => Period::STATUS_READY,
                'reviewed_at' => null,
                'reviewed_by' => null,
                'finalized_at' => null,
                'finalized_by' => null,
                'locked_at' => null,
                'locked_by' => null,
                'reopened_at' => now(),
                'reopened_by' => auth()->id(),
                'reopen_reason' => $validated['reopen_reason'],
            ]);
        });

        return back()->with(
            'success',
            'Payroll period reopened. Approved Leave/OB/Overtime requests can now be corrected, then regenerate and re-lock payroll when ready.'
        );
    }

    /**
     * Export payroll for the period.
     */
    public function exportPayroll(
        $period,
        PayrollGenerationService $payrollService
    ) {
        $periodModel = Period::findOrFail($period);

        try {
            $periodData = [
                'start_date' => Carbon::parse(
                    $periodModel->start_date
                )->format('Y-m-d'),
                'end_date' => Carbon::parse(
                    $periodModel->end_date
                )->format('Y-m-d'),
            ];

            $employeeIds = $this->normalizeEmployeeIds(
                $periodModel->employee_ids
            );

            $relativePath = $payrollService->exportPayrollToExcel(
                $periodData,
                !empty($employeeIds) ? $employeeIds : null,
                'csv'
            );

            $absolutePath = storage_path('app/' . $relativePath);

            if (!file_exists($absolutePath)) {
                throw new \RuntimeException(
                    'The payroll export file was not created.'
                );
            }

            return response()->download($absolutePath)->deleteFileAfterSend();
        } catch (Throwable $exception) {
            Log::error('Period payroll export failed', [
                'period_id' => $periodModel->id,
                'message' => $exception->getMessage(),
            ]);

            return back()->with(
                'error',
                'Unable to export payroll: ' .
                $exception->getMessage()
            );
        }
    }

    private function employeesForPeriod(Period $periodModel, array $employeeIds): Collection
    {
        $query = Employee::with('department');

        if (!empty($employeeIds)) {
            $query->whereIn('id', $employeeIds);
        } elseif (!empty($periodModel->department_id)) {
            $query->where('department_id', $periodModel->department_id);
        } else {
            $currentCompany = CompanyHelper::getCurrentCompany();
            if ($currentCompany) {
                $query->where(function ($employeeQuery) use ($currentCompany) {
                    $employeeQuery->where('company_id', $currentCompany->id)
                        ->orWhereHas('department', function ($departmentQuery) use ($currentCompany) {
                            $departmentQuery->where('company_id', $currentCompany->id);
                        });
                });
            }
        }

        return $query->get();
    }

    private function periodData(Period $periodModel, array $employeeIds): array
    {
        $startDate = Carbon::parse($periodModel->start_date);
        $endDate = Carbon::parse($periodModel->end_date);

        return [
            'id' => $periodModel->id,
            'name' => $periodModel->name,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'days_in_period' => $startDate->diffInDays($endDate) + 1,
            'department_id' => $periodModel->department_id,
            'employee_ids' => $employeeIds,
        ];
    }

    private function payrollsForPeriod(Period $periodModel): Collection
    {
        return Payroll::with(['employee', 'employee.department', 'employee.position'])
            ->where('period_id', $periodModel->id)
            ->get();
    }

    private function buildPayrollArraySummary(array $payrolls): array
    {
        $rows = collect($payrolls);

        return [
            'total_employees' => $rows->count(),
            'total_basic_salary' => (float) $rows->sum('basic_salary'),
            'total_holiday_basic_pay' => (float) $rows->sum('holiday_basic_pay'),
            'total_holiday_premium' => (float) $rows->sum('holiday_premium'),
            'total_special_holiday_premium' => (float) $rows->sum('special_holiday_premium'),
            'total_overtime_hours' => (float) $rows->sum('overtime_hours'),
            'total_overtime_pay' => (float) $rows->sum('overtime_pay'),
            'total_bonuses' => (float) $rows->sum('bonuses'),
            'total_deductions' => (float) $rows->sum('deductions'),
            'total_tax' => (float) $rows->sum('tax_amount'),
            'total_gross_pay' => (float) $rows->sum('gross_pay'),
            'total_net_pay' => (float) $rows->sum('net_pay'),
        ];
    }

    private function buildPayrollModelSummary(Collection $payrolls): array
    {
        return [
            'total_employees' => $payrolls->count(),
            'total_basic_salary' => (float) $payrolls->sum('basic_salary'),
            'total_overtime_hours' => (float) $payrolls->sum('overtime_hours'),
            'total_overtime_pay' => (float) $payrolls->sum('overtime_pay'),
            'total_bonuses' => (float) $payrolls->sum('bonuses'),
            'total_deductions' => (float) $payrolls->sum(function ($payroll) {
                return (float) ($payroll->deductions ?? 0) + (float) ($payroll->tax_amount ?? 0);
            }),
            'total_tax' => (float) $payrolls->sum('tax_amount'),
            'total_gross_pay' => (float) $payrolls->sum('gross_pay'),
            'total_net_pay' => (float) $payrolls->sum('net_pay'),
        ];
    }

    /**
     * Use PayrollController's existing comprehensive attendance builder.
     *
     * This keeps period management and payroll generation synchronized.
     */
    private function getComprehensiveAttendanceData(
        Carbon $startDate,
        Carbon $endDate,
        Collection $employees
    ): array {
        if ($employees->isEmpty()) {
            return [];
        }

        try {
            $payrollController = app(PayrollController::class);

            $method = new ReflectionMethod(
                PayrollController::class,
                'getComprehensiveAttendanceData'
            );

            $method->setAccessible(true);

            $result = $method->invoke(
                $payrollController,
                $startDate,
                $endDate,
                $employees
            );

            return is_array($result)
                ? $result
                : collect($result)->values()->all();
        } catch (Throwable $exception) {
            Log::error(
                'Unable to build period comprehensive attendance data',
                [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'employee_count' => $employees->count(),
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ]
            );

            return [];
        }
    }

    /**
     * Build the summary cards required by the period details Blade.
     */
    private function buildSummaryData(
        array $comprehensiveData,
        Collection $employees
    ): array {
        $records = collect($comprehensiveData);

        $presentStatuses = [
            'Present',
            'Late',
            'Half Day',
            'Official Business',
        ];

        return [
            'total_employees' => $records->pluck('employee_id')
                ->filter()
                ->unique()
                ->count() ?: $employees->count(),

            'present_days' => $records
                ->whereIn('attendance_status', $presentStatuses)
                ->count(),

            'absent_days' => $records
                ->where('attendance_status', 'Absent')
                ->count(),

            'total_scheduled_hours' => round(
                $records->sum(function ($record) {
                    return $this->convertHoursToDecimal(
                        $record['scheduled_hours'] ?? 0
                    );
                }),
                2
            ),

            'total_morning_overtime_hours' => round(
                (float) $records->sum(function ($record) {
                    return (float) (
                        $record['morning_overtime']
                        ?? $record['pre_shift_overtime']
                        ?? 0
                    );
                }),
                2
            ),

            'total_evening_overtime_hours' => round(
                (float) $records->sum(function ($record) {
                    return (float) (
                        $record['evening_overtime']
                        ?? $record['post_shift_overtime']
                        ?? $record['overtime']
                        ?? 0
                    );
                }),
                2
            ),
        ];
    }

    /**
     * Convert formatted time values such as:
     *
     * 8 hrs
     * 8 hrs 30 mins
     * 08:30
     * 8.5
     *
     * into decimal hours.
     */
    private function convertHoursToDecimal($value): float
    {
        if (is_numeric($value)) {
            return max(0, (float) $value);
        }

        if (!$value || in_array($value, ['—', '-', 'N/A'], true)) {
            return 0;
        }

        $value = trim((string) $value);

        if (preg_match('/^(\d{1,2}):(\d{2})$/', $value, $matches)) {
            return (float) $matches[1] + ((float) $matches[2] / 60);
        }

        $hours = 0;
        $minutes = 0;

        if (
            preg_match(
                '/(\d+(?:\.\d+)?)\s*(?:hr|hrs|hour|hours)/i',
                $value,
                $hourMatch
            )
        ) {
            $hours = (float) $hourMatch[1];
        }

        if (
            preg_match(
                '/(\d+(?:\.\d+)?)\s*(?:min|mins|minute|minutes)/i',
                $value,
                $minuteMatch
            )
        ) {
            $minutes = (float) $minuteMatch[1];
        }

        return max(0, $hours + ($minutes / 60));
    }

    /**
     * Assign the company default schedule for every covered employee/date.
     * Monday-Saturday: 8:00 AM-5:00 PM with 8 required paid hours.
     * Sunday: Day Off. Existing schedules are never overwritten.
     */
    private function ensureDefaultSchedulesForPeriod(Period $periodModel): int
    {
        $employees = $this->employeesForPeriod(
            $periodModel,
            $this->normalizeEmployeeIds($periodModel->employee_ids)
        );

        if ($employees->isEmpty()) {
            return 0;
        }

        $created = 0;
        $date = Carbon::parse($periodModel->start_date)->startOfDay();
        $end = Carbon::parse($periodModel->end_date)->startOfDay();

        while ($date->lte($end)) {
            foreach ($employees as $employee) {
                $exists = EmployeeSchedule::query()
                    ->where('employee_id', $employee->id)
                    ->whereDate('date', $date->toDateString())
                    ->exists();

                if ($exists) {
                    continue;
                }

                $isSunday = $date->isSunday();

                EmployeeSchedule::create([
                    'employee_id' => $employee->id,
                    'department_id' => $employee->department_id,
                    'date' => $date->toDateString(),
                    'time_in' => $isSunday ? null : '08:00:00',
                    'time_out' => $isSunday ? null : '17:00:00',
                    'status' => $isSunday ? 'Day Off' : 'Working',
                    'schedule_type' => 'fixed',
                    'required_hours' => $isSunday ? 0 : 8,
                    'notes' => 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.',
                    'created_by' => auth()->id(),
                ]);

                $created++;
            }

            $date->addDay();
        }

        return $created;
    }

    /**
     * Normalize employee_ids regardless of whether the Period model casts
     * the database field as an array or returns a JSON string.
     */
    private function normalizeEmployeeIds($employeeIds): array
    {
        if (empty($employeeIds)) {
            return [];
        }

        if ($employeeIds instanceof Collection) {
            return $employeeIds
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        if (is_array($employeeIds)) {
            return collect($employeeIds)
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        if (is_string($employeeIds)) {
            $decoded = json_decode($employeeIds, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return collect($decoded)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            }

            return collect(explode(',', $employeeIds))
                ->map(fn ($id) => trim($id))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        return [];
    }

    /**
     * Check whether the dates match the configured semi-monthly cutoff.
     *
     * Period A:
     * cutoff day 2 + 1 of the previous month through cutoff day 1.
     *
     * Period B:
     * cutoff day 1 + 1 through cutoff day 2 of the current month.
     */
    private function matchesConfiguredCutoff(
        Carbon $startDate,
        Carbon $endDate,
        int $cutoffDay1,
        int $cutoffDay2
    ): bool {
        $year = $startDate->year;
        $month = $startDate->month;

        $periodBStart = Carbon::create(
            $year,
            $month,
            $cutoffDay1
        )->addDay();

        $periodBEnd = Carbon::create(
            $year,
            $month,
            $cutoffDay2
        );

        $previousMonth = Carbon::create(
            $year,
            $month,
            1
        )->subMonthNoOverflow();

        $periodAStart = Carbon::create(
            $previousMonth->year,
            $previousMonth->month,
            $cutoffDay2
        )->addDay();

        $periodAEnd = Carbon::create(
            $year,
            $month,
            $cutoffDay1
        );

        return (
            $startDate->isSameDay($periodAStart)
            && $endDate->isSameDay($periodAEnd)
        ) || (
            $startDate->isSameDay($periodBStart)
            && $endDate->isSameDay($periodBEnd)
        );
    }
    private function standardPeriodDates(int $year, int $month, int $periodNo): array
    {
        $payMonth = Carbon::create($year, $month, 1)->startOfDay();

        if ($periodNo === 1) {
            return [
                'start_date' => $payMonth->copy()->subMonthNoOverflow()->day(26)->toDateString(),
                'end_date' => $payMonth->copy()->day(10)->toDateString(),
                'payroll_date' => $payMonth->copy()->day(15)->toDateString(),
            ];
        }

        return [
            'start_date' => $payMonth->copy()->day(11)->toDateString(),
            'end_date' => $payMonth->copy()->day(25)->toDateString(),
            'payroll_date' => $payMonth->copy()->day(min(30, $payMonth->daysInMonth))->toDateString(),
        ];
    }

    /** Count company workdays (Monday-Saturday; Sunday is the default off day). */
    private function countWeekdays(Carbon $startDate, Carbon $endDate): int
    {
        $count = 0;
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate)) {
            if (!$cursor->isSunday()) {
                $count++;
            }

            $cursor->addDay();
        }

        return $count;
    }

}