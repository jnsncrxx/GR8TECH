<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleV2Controller extends Controller
{

    public function index(Request $request)
    {
        $searchQuery = $request->query('search', '');
        $selectedDepartment = $request->query('department_id', '');
        $selectedMonth = $request->query('month', now()->month);
        $selectedYear = $request->query('year', now()->year);

        $departments = \App\Models\Department::orderBy('name')->get();
        $allEmployees = \App\Models\Employee::with('department')->orderBy('first_name')->get();

        // always run the query now, so a fresh page load shows everyone by default
        // (empty department/search just means no WHERE clause = all employees)
        $query = \App\Models\Employee::with(['department', 'position']);
        if ($selectedDepartment) {
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
        $attendanceRecords = collect();
        if ($employees->isNotEmpty()) {
            $schedules = \App\Models\EmployeeSchedule::whereIn('employee_id', $employees->pluck('id'))
                ->whereBetween('date', [$monthStart->copy()->startOfMonth(), $monthStart->copy()->endOfMonth()])
                ->get()
                ->keyBy(fn($schedule) => $schedule->employee_id . '_' . $schedule->date->format('Y-m-d'));

            // needed to show actual hours logged (vs just required_hours) on
            // flexible-schedule calendar cells - keyed the same way as
            // $schedules so the view can look both up together per cell
            $attendanceRecords = \App\Models\AttendanceRecord::whereIn('employee_id', $employees->pluck('id'))
                ->whereBetween('date', [$monthStart->copy()->startOfMonth(), $monthStart->copy()->endOfMonth()])
                ->get()
                ->keyBy(fn($record) => $record->employee_id . '_' . $record->date->format('Y-m-d'));
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
            'attendanceRecords' => $attendanceRecords,
            'scheduleSummary' => []
        ]);
    }

    public function create(Request $request)
    {
        $departments = \App\Models\Department::orderBy('name')->get();
        $employees = \App\Models\Employee::with('department')->orderBy('first_name')->get();

        // if the user clicked "create schedule" for a specific employee/date
        // from the index page, pre-fill those fields instead of leaving them blank
        $employee = $request->filled('employee_id')
            ? \App\Models\Employee::find($request->query('employee_id'))
            : null;

        $date = $request->query('date', now()->format('Y-m-d'));

        return view('attendance.schedule-v2.create', [
            'user' => Auth::user(),
            'departments' => $departments,
            'employees' => $employees,
            'employee' => $employee,
            'date' => $date,
            'defaultTimeIn' => '08:00',
            'defaultTimeOut' => '17:00',
            'currentFilters' => $request->only(['department_id', 'month', 'year', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:Working,Day Off,Leave,Holiday,Overtime,Regular Holiday,Special Holiday,Absent'],
            'schedule_type' => ['required', 'in:fixed,flexible'],
            'required_hours' => ['required_if:schedule_type,flexible', 'nullable', 'numeric', 'min:1', 'max:24'],
            'time_in' => ['nullable', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Fixed schedules keep whatever time_in/time_out the admin sets for
        // that employee - "fixed" means unchanging day-to-day for that
        // person, not that every employee must use 8-5. required_hours for
        // Fixed is informational only (getExpectedHours() on AttendanceRecord
        // computes it fresh from time_in/time_out every time), so 8.00 below
        // is just a placeholder, not used in any late/undertime calculation.
        //
        // Flexible schedules never have an admin-set time window - the
        // employee can clock in/out whenever, they just need to hit
        // required_hours - so time_in/time_out are forced to null here
        // regardless of what was submitted.
        if ($validated['schedule_type'] === 'flexible') {
            $validated['time_in'] = null;
            $validated['time_out'] = null;
        }

        $exists = \App\Models\EmployeeSchedule::where('employee_id', $validated['employee_id'])
            ->where('date', $validated['date'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'date' => 'A schedule already exists for this employee on this date. Please edit the existing schedule instead of creating a new one.',
            ]);
        }

        \App\Models\EmployeeSchedule::create([
            'employee_id' => $validated['employee_id'],
            'date' => $validated['date'],
            'department_id' => $validated['department_id'],
            'status' => $validated['status'],
            'schedule_type' => $validated['schedule_type'],
            'required_hours' => $validated['schedule_type'] === 'flexible' ? $validated['required_hours'] : 8.00,
            'time_in' => $validated['time_in'] ?? null,
            'time_out' => $validated['time_out'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

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
            'time_in' => ['nullable', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $createdCount = 0;

        foreach ($validated['employee_schedules'] as $entry) {
            $employee = \App\Models\Employee::find($entry['employee_id']);
            if (!$employee) {
                continue;
            }

            foreach ($entry['dates'] as $date) {
                \App\Models\EmployeeSchedule::updateOrCreate(
                    ['employee_id' => $employee->id, 'date' => $date],
                    [
                        'department_id' => $employee->department_id,
                        'status' => $validated['status'],
                        'time_in' => $validated['time_in'] ?? null,
                        'time_out' => $validated['time_out'] ?? null,
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
            'time_in' => ['nullable', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

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
                        'time_in' => $validated['time_in'] ?? null,
                        'time_out' => $validated['time_out'] ?? null,
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
        $schedule = \App\Models\EmployeeSchedule::with('employee.department')->findOrFail($schedule);

        return view('attendance.schedule-v2.show', ['schedule' => $schedule, 'user' => Auth::user()]);
    }

    public function edit($schedule)
    {
        $schedule = \App\Models\EmployeeSchedule::with('employee.department')->findOrFail($schedule);

        return view('attendance.schedule-v2.edit', ['schedule' => $schedule, 'user' => Auth::user()]);
    }

    public function update(Request $request, $schedule)
    {
        $schedule = \App\Models\EmployeeSchedule::findOrFail($schedule);

       $validated = $request->validate([
            'status' => ['required', 'in:Working,Day Off,Leave,Holiday,Overtime,Regular Holiday,Special Holiday,Absent'],
            'schedule_type' => ['required', 'in:fixed,flexible'],
            'required_hours' => ['required_if:schedule_type,flexible', 'nullable', 'numeric', 'min:1', 'max:24'],
            'time_in' => ['nullable', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Same as store() - Flexible schedules have no admin-set time
        // window, so force these to null regardless of what was submitted.
        if ($validated['schedule_type'] === 'flexible') {
            $validated['time_in'] = null;
            $validated['time_out'] = null;
        }

        $schedule->update([
            'status' => $validated['status'],
            'schedule_type' => $validated['schedule_type'],
            'required_hours' => $validated['schedule_type'] === 'flexible' ? $validated['required_hours'] : 8.00,
            'time_in' => $validated['time_in'] ?? null,
            'time_out' => $validated['time_out'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('schedule-v2.index')
            ->with('success', 'Schedule updated successfully.');
    }

    public function destroy($schedule)
    {
        $schedule = \App\Models\EmployeeSchedule::findOrFail($schedule);
        $schedule->delete();

        return redirect()->route('schedule-v2.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
