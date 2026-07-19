<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CompanyHelper;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use App\Services\PayrollGenerationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use ReflectionMethod;
use Throwable;

class PeriodManagementController extends Controller
{
    /**
     * Display all payroll/attendance periods.
     */
    public function index()
    {
        $user = auth()->user() ?? (object) ['role' => 'admin'];

        $periods = Period::orderBy('created_at', 'desc')->get();

        foreach ($periods as $period) {
            if (!isset($period->duration)) {
                $period->duration = $period->start_date && $period->end_date
                    ? Carbon::parse($period->start_date)
                        ->diffInDays(Carbon::parse($period->end_date)) + 1
                    : 0;
            }
        }

        return view(
            'attendance.period-management.index',
            compact('user', 'periods')
        );
    }

    /**
     * Show the create-period form.
     */
    public function create()
    {
        $user = auth()->user() ?? (object) ['role' => 'admin'];

        $departments = Department::orderBy('name')->get();

        $employees = Employee::with('department')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view(
            'attendance.period-management.create',
            compact('user', 'departments', 'employees')
        );
    }

    /**
     * Save a new period.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        /*
         * Determine which company cutoff schedule should be used.
         */
        $company = null;

        if (!empty($validated['department_id'])) {
            $department = Department::find($validated['department_id']);

            if ($department && $department->company_id) {
                $company = \App\Models\Company::find($department->company_id);
            }
        }

        if (!$company) {
            $company = CompanyHelper::getCurrentCompany();
        }

        $cutoffWarning = null;

        if ($company) {
            $cutoffDay1 = (int) ($company->cutoff_day_1 ?? 10);
            $cutoffDay2 = (int) ($company->cutoff_day_2 ?? 25);

            if (
                !$this->matchesConfiguredCutoff(
                    $startDate,
                    $endDate,
                    $cutoffDay1,
                    $cutoffDay2
                )
            ) {
                $cutoffWarning =
                    "Heads up: this period " .
                    "({$startDate->format('M j')}–{$endDate->format('M j')}) " .
                    "doesn't match {$company->name}'s configured " .
                    "semi-monthly cutoff (day {$cutoffDay1} / day {$cutoffDay2}). " .
                    'It was still created—double-check that this was intentional.';
            }
        }

        Period::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'department_id' => $validated['department_id'] ?? null,
            'employee_ids' => $validated['employee_ids'] ?? [],
            'created_by' => auth()->id(),
        ]);

        $redirect = redirect()
            ->route('attendance.period-management.index')
            ->with('success', 'Period created successfully.');

        if ($cutoffWarning) {
            $redirect->with('warning', $cutoffWarning);
        }

        return $redirect;
    }

    /**
     * Show one period with comprehensive attendance information.
     */
    public function show($id)
    {
        $user = auth()->user() ?? (object) ['role' => 'admin'];

        $period = Period::findOrFail($id);

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

        return view(
            'attendance.period-management.show',
            compact(
                'user',
                'period',
                'employees',
                'comprehensiveData',
                'summaryData'
            )
        );
    }

    /**
     * Delete a period.
     */
    public function destroy($id)
    {
        $period = Period::findOrFail($id);
        $period->delete();

        return redirect()
            ->route('attendance.period-management.index')
            ->with('success', 'Period deleted successfully.');
    }

    /**
     * Preview payroll for the period.
     */
    public function previewPayroll($period)
    {
        return back()->with(
            'info',
            'Preview Payroll functionality coming soon.'
        );
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

            $employeesQuery = Employee::with('department');

            if (!empty($employeeIds)) {
                $employeesQuery->whereIn('id', $employeeIds);
            } elseif (!empty($periodModel->department_id)) {
                $employeesQuery->where(
                    'department_id',
                    $periodModel->department_id
                );
            } else {
                $currentCompany = CompanyHelper::getCurrentCompany();

                if ($currentCompany) {
                    $employeesQuery->where(
                        function ($query) use ($currentCompany) {
                            $query
                                ->where('company_id', $currentCompany->id)
                                ->orWhereHas(
                                    'department',
                                    function ($departmentQuery) use (
                                        $currentCompany
                                    ) {
                                        $departmentQuery->where(
                                            'company_id',
                                            $currentCompany->id
                                        );
                                    }
                                );
                        }
                    );
                }
            }

            $employees = $employeesQuery->get();

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

            return redirect()
                ->route('payroll.index')
                ->with(
                    'success',
                    count($generatedPayrolls) .
                    ' payroll record(s) generated successfully.'
                );
        } catch (Throwable $exception) {
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

        $payrolls = Payroll::with(['employee', 'employee.department'])
            ->whereDate(
                'pay_period_start',
                Carbon::parse($periodModel->start_date)->format('Y-m-d')
            )
            ->whereDate(
                'pay_period_end',
                Carbon::parse($periodModel->end_date)->format('Y-m-d')
            )
            ->get();

        if ($payrolls->isEmpty()) {
            return back()->with(
                'info',
                'No payroll records are available for this period.'
            );
        }

        /*
         * Keep the existing placeholder until a separate summary Blade
         * page is available.
         */
        return redirect()
            ->route('payroll.index', [
                'start_date' => Carbon::parse(
                    $periodModel->start_date
                )->format('Y-m-d'),
                'end_date' => Carbon::parse(
                    $periodModel->end_date
                )->format('Y-m-d'),
            ]);
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
}