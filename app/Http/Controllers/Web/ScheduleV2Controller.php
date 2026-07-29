<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ScheduleV2Controller extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->query('search', '');
        $selectedDepartment = $request->query('department_id', '');
        $selectedMonth = $request->query('month', now()->month);
        $selectedYear = $request->query('year', now()->year);

        $user = Auth::user();
        $isManager = $user->role === 'manager';

        $departments = $isManager
            ? \App\Models\Department::where('manager_id', $user->employee_id)->orderBy('name')->get()
            : \App\Models\Department::orderBy('name')->get();
        $allEmployeesQuery = \App\Models\Employee::with(['department', 'position'])->orderBy('first_name');
        if ($isManager) {
            $allEmployeesQuery->managedBy($user->employee_id);
        }
        $allEmployees = $allEmployeesQuery->get();

        // always run the query now, so a fresh page load shows everyone by default
        // (empty department/search just means no WHERE clause = all employees)
        $query = \App\Models\Employee::with(['department', 'position']);
        if ($isManager) {
            // Managers never see other departments' employees, regardless of
            // what department_id a crafted request tries to pass.
            $query->managedBy($user->employee_id);
        } elseif ($selectedDepartment) {
            $query->where('department_id', $selectedDepartment);
        }
        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('first_name', 'like', "%{$searchQuery}%")
                    ->orWhere('last_name', 'like', "%{$searchQuery}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchQuery}%"]);
            });
        }
        $employees = $query->get();

        // Build the list of days in the selected month, e.g. for July 2026:
        // [['day' => 1, 'date' => Carbon('2026-07-01')], ['day' => 2, 'date' => Carbon('2026-07-02')], ...]
        // The view loops over this to build one calendar column per day.
        $monthStart = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1);
        $calendarDays = [];
        for ($d = 1; $d <= $monthStart->daysInMonth; $d++) {
            $date = $monthStart->copy()->day($d);
            $calendarDays[] = ['day' => $d, 'date' => $date];
        }

        // Fetch all schedules for the visible employees in this month, keyed by
        // "employee_id_date" so the view can instantly look up "does this
        // employee have a schedule on this day" without a query per cell.
        $schedules = collect();
        $attendanceHistory = collect();
        if ($employees->isNotEmpty()) {
            $employeeIds = $employees->pluck('id');
            $monthEnd = $monthStart->copy()->endOfMonth();
            $schedules = \App\Models\EmployeeSchedule::with('scheduleTemplate')
                ->whereIn('employee_id', $employeeIds)
                ->whereBetween('date', [$monthStart->copy()->startOfMonth(), $monthStart->copy()->endOfMonth()])
                ->get()
                ->keyBy(fn($schedule) => $schedule->employee_id . '_' . $schedule->date->format('Y-m-d'));

            $attendance = \App\Models\AttendanceRecord::with(['breaks', 'timeEntries'])
                ->whereIn('employee_id', $employeeIds)
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->get()
                ->keyBy(fn($record) => $record->employee_id . '_' . $record->date->format('Y-m-d'));
            $officialBusiness = \App\Models\OfficialBusinessRequest::whereIn('employee_id', $employeeIds)
                ->where('status', \App\Models\OfficialBusinessRequest::APPROVED)
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->get()
                ->keyBy(fn($request) => $request->employee_id . '_' . $request->date->format('Y-m-d'));
            $leaves = \App\Models\LeaveRequest::whereIn('employee_id', $employeeIds)
                ->where('status', \App\Models\LeaveRequest::APPROVED)
                ->whereDate('start_date', '<=', $monthEnd->toDateString())
                ->whereDate('end_date', '>=', $monthStart->toDateString())
                ->get();
            $leaveByDay = collect();
            foreach ($leaves as $leave) {
                for ($date = $leave->start_date->copy()->max($monthStart); $date->lte($leave->end_date->copy()->min($monthEnd)); $date->addDay()) {
                    $leaveByDay->put($leave->employee_id . '_' . $date->format('Y-m-d'), $leave);
                }
            }

            foreach ($employees as $employee) {
                foreach ($calendarDays as $day) {
                    $date = $day['date'];
                    $key = $employee->id . '_' . $date->format('Y-m-d');
                    $schedule = $schedules->get($key);
                    $record = $attendance->get($key);
                    $leave = $leaveByDay->get($key);
                    $ob = $officialBusiness->get($key);

                    if ($date->isFuture()) {
                        continue;
                    }

                    if ($leave && $ob) {
                        $history = ['label' => 'Leave / OB Conflict', 'tone' => 'red'];
                    } elseif ($leave) {
                        $history = ['label' => \App\Models\LeaveRequest::labelFor($leave->leave_type), 'tone' => 'indigo'];
                    } elseif ($ob) {
                        $history = ['label' => 'Official Business', 'tone' => 'indigo'];
                    } elseif ($record && (($record->time_in && !$record->time_out) || (!$record->time_in && $record->time_out))) {
                        $history = ['label' => 'Incomplete Log', 'tone' => 'red'];
                    } elseif ($record && $record->hasInvalidTimeSpan()) {
                        $history = ['label' => 'Invalid Duration', 'tone' => 'red'];
                    } elseif ($record && $record->time_in && $record->time_out) {
                        $history = ['label' => $record->isLate() ? 'Late' : 'Present', 'tone' => $record->isLate() ? 'amber' : 'green'];
                    } elseif ($date->isToday()) {
                        $history = ['label' => 'Not Yet Recorded', 'tone' => 'gray'];
                    } elseif ($schedule?->status === 'Working') {
                        $history = ['label' => 'Absent', 'tone' => 'red'];
                    } else {
                        continue;
                    }

                    // late can happen on top of any of the labels above (e.g.
                    // clocked in late AND hasn't clocked out yet) - shown as
                    // its own separate badge instead of fighting for priority
                    if ($record && $record->isLate()) {
                        $history['is_late'] = true;
                        $history['late_minutes_formatted'] = $record->getLateMinutesFormatted();
                    }

                    $attendanceHistory->put($key, $history);
                }
            }
        }

        return view('attendance.schedule-v2.index', [
            'user' => Auth::user(),
            'searchQuery' => $searchQuery,
            'departments' => $departments,
            'allEmployees' => $allEmployees,
            'selectedDepartment' => $selectedDepartment,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'employees' => $employees,
            'calendarDays' => $calendarDays,
            'schedules' => $schedules,
            'attendanceHistory' => $attendanceHistory,
            'scheduleSummary' => [] // Or mock summary data if needed
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $isManager = $user->role === 'manager';

        $departments = $isManager
            ? \App\Models\Department::where('manager_id', $user->employee_id)->orderBy('name')->get()
            : \App\Models\Department::orderBy('name')->get();
        $employeesQuery = \App\Models\Employee::with('department')->orderBy('first_name');
        if ($isManager) {
            $employeesQuery->managedBy($user->employee_id);
        }
        $employees = $employeesQuery->get();

        // if the user clicked "create schedule" for a specific employee/date
        // from the index page, pre-fill those fields instead of leaving them blank
        $employee = $request->filled('employee_id')
            ? \App\Models\Employee::find($request->query('employee_id'))
            : null;

        // A manager can't pre-fill (or later submit) a schedule for someone
        // outside their department, even via a crafted employee_id query param.
        if ($isManager && $employee && !$employee->isManagedBy($user->employee_id)) {
            abort(403, 'You can only manage schedules for your own department.');
        }

        $date = $request->query('date', now()->format('Y-m-d'));

        $currentCompany = \App\Helpers\CompanyHelper::getCurrentCompany();
        $templates = \App\Models\ScheduleTemplate::query()
            ->when($currentCompany, fn ($query) => $query->forCompany($currentCompany->id))
            ->when(!$currentCompany, fn ($query) => $query->whereNull('company_id'))
            ->orderBy('code')
            ->get();

        return view('attendance.schedule-v2.create', [
            'user' => Auth::user(),
            'departments' => $departments,
            'employees' => $employees,
            'employee' => $employee,
            'date' => $date,
            'defaultTimeIn' => '08:00',
            'defaultTimeOut' => '17:00',
            'currentFilters' => $request->only(['department_id', 'month', 'year', 'search']),
            'templates' => $templates,
        ]);
    }

    /**
     * Blocks a manager from creating, editing, or deleting a schedule for an
     * employee outside their own department. Admin/HR are unrestricted.
     * Guards against a manager submitting a crafted employee_id/schedule_id
     * for someone else's team, since the create/edit forms only *display*
     * the manager's own department — the server has to enforce it too.
     */
    private function assertEmployeeManageable(\App\Models\Employee $employee): void
    {
        $user = Auth::user();
        if ($user->role === 'manager' && !$employee->isManagedBy($user->employee_id)) {
            abort(403, 'You can only manage schedules for your own department.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:Working,Day Off,Leave,Holiday,Overtime,Regular Holiday,Special Holiday,Absent'],
            'schedule_template_id' => ['nullable', 'exists:schedule_templates,id'],
            ...$this->scheduleDetailRules($request),
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $employee = \App\Models\Employee::findOrFail($validated['employee_id']);
        $this->assertEmployeeManageable($employee);

        if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($validated['employee_id'], $validated['date'])) {
            return redirect()->back()->withInput()->with(
                'error',
                'Cannot set a schedule — payroll has already been generated for this date. The payroll period is locked and can no longer be modified.'
            );
        }

        $details = $this->normalizedScheduleDetails($validated);

        // updateOrCreate so re-submitting for the same employee+date edits
        // the existing schedule instead of throwing a duplicate error
        \App\Models\EmployeeSchedule::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
            ],
            [
                'department_id' => $validated['department_id'],
                'status' => $validated['status'],
                ...$details,
                'schedule_template_id' => $validated['schedule_template_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]
        );

        return redirect()->route('schedule-v2.index')
            ->with('success', 'Schedule created successfully.');
    }

    public function bulkCreate(Request $request)
    {
        // This one endpoint is used by two different features on the page:
        // 1. The "Bulk Create" modal - one department, a group of employees,
        //    one shared date RANGE (start_date to end_date).
        // 2. The "Select Dates" calendar mode - specific employees each with
        //    their OWN individually-picked dates, sent as JSON.
        // We tell them apart by checking which fields showed up in the request.
        if ($request->has('employee_schedules')) {
            return $this->bulkCreateFromSelectedDates($request);
        }

        return $this->bulkCreateFromDateRange($request);
    }

    /**
     * Handles the "Select Dates" calendar flow: each employee has their own
     * specific list of dates (not a shared range), sent as JSON via fetch().
     */
    private function bulkCreateFromSelectedDates(Request $request)
    {
        $validated = $request->validate([
            'employee_schedules' => ['required', 'array', 'min:1'],
            'employee_schedules.*.employee_id' => ['required', 'exists:employees,id'],
            'employee_schedules.*.dates' => ['required', 'array', 'min:1'],
            'employee_schedules.*.dates.*' => ['required', 'date'],
            'status' => ['required', 'in:Working,Day Off,Leave,Holiday,Overtime,Regular Holiday,Special Holiday,Absent'],
            ...$this->scheduleDetailRules($request),
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $details = $this->normalizedScheduleDetails($validated);
        $conflicts = app(\App\Services\PayrollRequestConflictService::class);

        foreach ($validated['employee_schedules'] as $entry) {
            foreach ($entry['dates'] as $date) {
                if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($entry['employee_id'], $date)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot save — payroll has already been generated for {$date}. The payroll period is locked and can no longer be modified.",
                    ], 422);
                }
            }
        }

        $createdCount = 0;

        foreach ($validated['employee_schedules'] as $entry) {
            $employee = \App\Models\Employee::find($entry['employee_id']);
            if (!$employee) {
                continue;
            }
            $this->assertEmployeeManageable($employee);

            foreach ($entry['dates'] as $date) {
                \App\Models\EmployeeSchedule::updateOrCreate(
                    ['employee_id' => $employee->id, 'date' => $date],
                    [
                        'department_id' => $employee->department_id,
                        'status' => $validated['status'],
                        ...$details,
                        'notes' => $validated['notes'] ?? null,
                        'created_by' => Auth::id(),
                    ]
                );
                $createdCount++;
            }
        }

        // this flow's JS expects a JSON response with a "success" key
        return response()->json([
            'success' => true,
            'message' => "Created {$createdCount} schedule(s) successfully.",
        ]);
    }

    /**
     * Handles the "Bulk Create" modal flow: a group of employees, all sharing
     * one department and one date range, submitted as a normal HTML form.
     */
    private function bulkCreateFromDateRange(Request $request)
    {
        $validated = $request->validate([
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['exists:employees,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:Working,Day Off,Leave,Holiday,Overtime,Regular Holiday,Special Holiday,Absent'],
            ...$this->scheduleDetailRules($request),
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $details = $this->normalizedScheduleDetails($validated);
        $conflicts = app(\App\Services\PayrollRequestConflictService::class);

        foreach ($validated['employee_ids'] as $employeeId) {
            $this->assertEmployeeManageable(\App\Models\Employee::findOrFail($employeeId));

            if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForRange($employeeId, $validated['start_date'], $validated['end_date'])) {
                return redirect()->back()->withInput()->with(
                    'error',
                    'Cannot save — payroll has already been generated for part of this date range. The payroll period is locked and can no longer be modified.'
                );
            }
        }

        $start = \Carbon\Carbon::parse($validated['start_date']);
        $end = \Carbon\Carbon::parse($validated['end_date']);
        $createdCount = 0;

        foreach ($validated['employee_ids'] as $employeeId) {
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                \App\Models\EmployeeSchedule::updateOrCreate(
                    ['employee_id' => $employeeId, 'date' => $date->format('Y-m-d')],
                    [
                        'department_id' => $validated['department_id'],
                        'status' => $validated['status'],
                        ...$details,
                        'notes' => $validated['notes'] ?? null,
                        'created_by' => Auth::id(),
                    ]
                );
                $createdCount++;
            }
        }

        // this flow submits as a normal form, so it expects a redirect, not JSON
        return redirect()->route('schedule-v2.index')
            ->with('success', "Created {$createdCount} schedule(s) successfully.");
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'schedule_ids' => ['required', 'array', 'min:1'],
            'schedule_ids.*' => ['exists:employee_schedules,id'],
        ]);

        $schedules = \App\Models\EmployeeSchedule::with('employee')
            ->whereIn('id', $validated['schedule_ids'])
            ->get();

        $conflicts = app(\App\Services\PayrollRequestConflictService::class);

        foreach ($schedules as $schedule) {
            if ($schedule->employee) {
                $this->assertEmployeeManageable($schedule->employee);
            }
            if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($schedule->employee_id, $schedule->date->toDateString())) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete — payroll has already been generated for {$schedule->date->toDateString()}. The payroll period is locked and can no longer be modified.",
                ], 422);
            }
        }

        $deletedCount = \App\Models\EmployeeSchedule::whereIn('id', $validated['schedule_ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deletedCount} schedule(s) successfully.",
            'deleted_count' => $deletedCount,
        ]);
    }

    public function getStatistics(Request $request)
    {
        return response()->json(['statistics' => []]);
    }

    public function show($schedule)
    {
        $schedule = \App\Models\EmployeeSchedule::with(['employee.department', 'employee.position'])->findOrFail($schedule);
        if ($schedule->employee) {
            $this->assertEmployeeManageable($schedule->employee);
        }

        return view('attendance.schedule-v2.show', ['schedule' => $schedule, 'user' => Auth::user()]);
    }

    public function edit($schedule)
    {
        $schedule = \App\Models\EmployeeSchedule::with(['employee.department', 'employee.position'])->findOrFail($schedule);
        if ($schedule->employee) {
            $this->assertEmployeeManageable($schedule->employee);
        }

        $currentCompany = \App\Helpers\CompanyHelper::getCurrentCompany();
        $templates = \App\Models\ScheduleTemplate::query()
            ->when($currentCompany, fn ($query) => $query->forCompany($currentCompany->id))
            ->when(!$currentCompany, fn ($query) => $query->whereNull('company_id'))
            ->orderBy('code')
            ->get();

        return view('attendance.schedule-v2.edit', [
            'schedule' => $schedule,
            'user' => Auth::user(),
            'templates' => $templates,
        ]);
    }

    public function update(Request $request, $schedule)
    {
        $schedule = \App\Models\EmployeeSchedule::with('employee')->findOrFail($schedule);
        if ($schedule->employee) {
            $this->assertEmployeeManageable($schedule->employee);
        }

        if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($schedule->employee_id, $schedule->date->toDateString())) {
            return redirect()->back()->withInput()->with(
                'error',
                'Cannot edit this schedule — payroll has already been generated for this date. The payroll period is locked and can no longer be modified.'
            );
        }

        $validated = $request->validate([
            'status' => ['required', 'in:Working,Day Off,Leave,Holiday,Overtime,Regular Holiday,Special Holiday,Absent'],
            'schedule_template_id' => ['nullable', 'exists:schedule_templates,id'],
            ...$this->scheduleDetailRules($request),
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $details = $this->normalizedScheduleDetails($validated);

        $schedule->update([
            'status' => $validated['status'],
            ...$details,
            'schedule_template_id' => $validated['schedule_template_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('schedule-v2.index')
            ->with('success', 'Schedule updated successfully.');
    }

    public function destroy($schedule)
    {
        $schedule = \App\Models\EmployeeSchedule::with('employee')->findOrFail($schedule);
        if ($schedule->employee) {
            $this->assertEmployeeManageable($schedule->employee);
        }

        if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($schedule->employee_id, $schedule->date->toDateString())) {
            return redirect()->back()->with(
                'error',
                'Cannot delete this schedule — payroll has already been generated for this date. The payroll period is locked and can no longer be modified.'
            );
        }

        $schedule->delete();

        return redirect()->route('schedule-v2.index')
            ->with('success', 'Schedule deleted successfully.');
    }

    private function scheduleDetailRules(Request $request): array
    {
        // Preserve compatibility with older clients while making fixed the
        // explicit system default for every newly submitted schedule.
        if (!$request->filled('schedule_type')) {
            $request->merge(['schedule_type' => 'fixed']);
        }

        $isWorkSchedule = in_array($request->input('status'), ['Working', 'Overtime'], true);
        $isFixed = $request->input('schedule_type', 'fixed') === 'fixed';

        return [
            'schedule_type' => ['required', Rule::in(['fixed', 'flexible'])],
            'required_hours' => [
                Rule::requiredIf($isWorkSchedule && !$isFixed),
                'nullable',
                'numeric',
                'min:1',
                'max:24',
            ],
            'time_in' => [
                Rule::requiredIf($isWorkSchedule && $isFixed),
                'nullable',
                'date_format:H:i',
            ],
            'time_out' => [
                Rule::requiredIf($isWorkSchedule && $isFixed),
                'nullable',
                'date_format:H:i',
                Rule::when($isWorkSchedule && $isFixed, ['after:time_in']),
            ],
        ];
    }

    private function normalizedScheduleDetails(array $validated): array
    {
        $isWorkSchedule = in_array($validated['status'], ['Working', 'Overtime'], true);
        $scheduleType = $validated['schedule_type'] ?? 'fixed';

        if (!$isWorkSchedule) {
            return [
                'schedule_type' => $scheduleType,
                'required_hours' => 0,
                'time_in' => null,
                'time_out' => null,
            ];
        }

        if ($scheduleType === 'flexible') {
            return [
                'schedule_type' => 'flexible',
                'required_hours' => (float) $validated['required_hours'],
                'time_in' => null,
                'time_out' => null,
            ];
        }

        return [
            'schedule_type' => 'fixed',
            'required_hours' => $this->fixedRequiredHours($validated['time_in'], $validated['time_out']),
            'time_in' => $validated['time_in'],
            'time_out' => $validated['time_out'],
        ];
    }

    private function fixedRequiredHours(string $timeIn, string $timeOut): float
    {
        $start = \Carbon\Carbon::createFromFormat('H:i', $timeIn);
        $end = \Carbon\Carbon::createFromFormat('H:i', $timeOut);
        $minutes = $start->diffInMinutes($end);

        return round(max(0, $minutes - 60) / 60, 2);
    }
}