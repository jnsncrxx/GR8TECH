<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\LeaveRequest;
use App\Models\OfficialBusinessRequest;
use App\Services\CutoffPeriodService;
use App\Services\DtrImportService;
use App\Helpers\CompanyHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AttendanceController extends Controller
{
    public function __construct(private CutoffPeriodService $cutoffPeriods)
    {
    }

    /**
     * Display daily attendance records
     */
    public function daily(Request $request)
    {
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::now();
        $user = Auth::user();
        $userRole = $user->role ?? 'employee';

        // Check if user is HR or Admin
        $isHrOrAdmin = in_array($userRole, ['admin', 'hr']);

        if ($isHrOrAdmin) {
            // === HR/ADMIN: Makikita LAHAT ng employees ===
            $allEmployees = Employee::with('department')
                ->orderBy('first_name')
                ->get()
                ->values();

            $employees = Employee::with('department')
                ->orderBy('first_name')
                ->paginate(15);

        } elseif ($userRole === 'manager' && $user->employee_id) {
            // === MANAGER: Sariling department/team lang ===
            $allEmployees = Employee::with('department')
                ->managedBy($user->employee_id)
                ->orderBy('first_name')
                ->get()
                ->values();

            $employees = Employee::with('department')
                ->managedBy($user->employee_id)
                ->orderBy('first_name')
                ->paginate(15);

        } else {
            // === EMPLOYEE: Sarili lang ===
            $employee = Employee::find($user->employee_id);

            if (!$employee) {
                return redirect()->route('dashboard')->with('error', 'No employee record found.');
            }

            $employees = Employee::where('id', $employee->id)
                ->with('department')
                ->paginate(15);
            $allEmployees = collect([$employee]);
        }

        $dailySnapshots = $this->buildDailyAttendanceSnapshots($allEmployees, $date);
        $attendanceRecords = $dailySnapshots
            ->map(fn (array $snapshot) => $snapshot['attendance'])
            ->filter()
            ->keyBy('employee_id');

        $scheduledWorking = $dailySnapshots->where('is_scheduled_working', true)->count();
        $presentStatuses = ['present', 'late', 'half_day', 'official_business'];
        $present = $dailySnapshots->whereIn('code', $presentStatuses)->count();
        $absent = $dailySnapshots->where('code', 'absent')->count();
        $late = $dailySnapshots->where('code', 'late')->count();

        $summary = [
            'total_employees' => $allEmployees->count(),
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'attendance_rate' => $scheduledWorking > 0
                ? round(($present / $scheduledWorking) * 100, 2)
                : 0,
        ];

        // Load approved OB details linked to attendance records
        $officialBusinessByAttendanceId = OfficialBusinessRequest::query()
            ->whereDate('date', $date->format('Y-m-d'))
            ->where('status', OfficialBusinessRequest::APPROVED)
            ->whereNotNull('attendance_record_id')
            ->get()
            ->keyBy('attendance_record_id');

        return view('attendance.daily', [
            'user' => $user,
            'date' => $date,
            'employees' => $employees,
            'attendanceRecords' => $attendanceRecords,
            'officialBusinessByAttendanceId' => $officialBusinessByAttendanceId,
            'dailySnapshots' => $dailySnapshots,
            'summary' => $summary,
        ]);
    }

    private function buildDailyAttendanceSnapshots(Collection $employees, Carbon $date): Collection
    {
        $dateString = $date->toDateString();
        $employeeIds = $employees->pluck('id');

        $attendance = AttendanceRecord::query()
            ->with(['breaks', 'timeEntries'])
            ->whereIn('employee_id', $employeeIds)
            ->whereDate('date', $dateString)
            ->get()
            ->keyBy('employee_id');

        $schedules = EmployeeSchedule::query()
            ->whereIn('employee_id', $employeeIds)
            ->whereDate('date', $dateString)
            ->get()
            ->keyBy('employee_id');

        $leaves = LeaveRequest::query()
            ->whereIn('employee_id', $employeeIds)
            ->where('status', LeaveRequest::APPROVED)
            ->whereDate('start_date', '<=', $dateString)
            ->whereDate('end_date', '>=', $dateString)
            ->get()
            ->keyBy('employee_id');

        $officialBusiness = OfficialBusinessRequest::query()
            ->whereIn('employee_id', $employeeIds)
            ->whereDate('date', $dateString)
            ->where('status', OfficialBusinessRequest::APPROVED)
            ->get()
            ->keyBy('employee_id');

        return $employees->mapWithKeys(function (Employee $employee) use ($attendance, $schedules, $leaves, $officialBusiness) {
            $record = $attendance->get($employee->id);
            $schedule = $schedules->get($employee->id);
            $leave = $leaves->get($employee->id);
            $ob = $officialBusiness->get($employee->id);
            $isWorking = $schedule?->status === 'Working';

            if (!$schedule) {
                $code = 'missing_schedule';
                $label = 'Missing Schedule';
                $severity = 'blocking';
            } elseif ($leave && $ob) {
                $code = 'request_conflict';
                $label = 'Leave / OB Conflict';
                $severity = 'blocking';
            } elseif ($leave && $record && $record->time_in && $record->time_out && $record->status !== AttendanceRecord::ON_LEAVE) {
                $code = 'leave_attendance_conflict';
                $label = 'Leave / Attendance Conflict';
                $severity = 'blocking';
            } elseif ($leave) {
                $code = 'on_leave';
                $label = LeaveRequest::labelFor($leave->leave_type);
                $severity = 'covered';
            } elseif ($ob) {
                $code = 'official_business';
                $label = 'Official Business';
                $severity = 'covered';
            } elseif ($record && (($record->time_in && !$record->time_out) || (!$record->time_in && $record->time_out))) {
                $code = 'incomplete';
                $label = 'Incomplete Log';
                $severity = 'blocking';
            } elseif ($record && $record->hasInvalidTimeSpan()) {
                $code = 'invalid_duration';
                $label = 'Invalid Duration';
                $severity = 'blocking';
            } elseif (in_array($schedule->status, ['Day Off', 'Rest Day'], true)) {
                $code = $record && $record->time_in && $record->time_out ? 'rest_day_duty' : 'day_off';
                $label = $code === 'rest_day_duty' ? 'Rest-day Duty Review' : $schedule->status;
                $severity = $code === 'rest_day_duty' ? 'review' : 'neutral';
            } elseif ($isWorking && (!$record || (!$record->time_in && !$record->time_out))) {
                $code = 'absent';
                $label = 'Absent';
                $severity = 'blocking';
            } elseif ($record) {
                $code = in_array($record->status, [AttendanceRecord::LATE, AttendanceRecord::HALF_DAY], true)
                    ? $record->status
                    : 'present';
                $label = ucfirst(str_replace('_', ' ', $code));
                $severity = $code === 'late' ? 'review' : 'clear';
            } else {
                $code = 'non_working';
                $label = $schedule->status;
                $severity = 'neutral';
            }

            return [$employee->id => [
                'attendance' => $record,
                'schedule' => $schedule,
                'leave' => $leave,
                'official_business' => $ob,
                'code' => $code,
                'label' => $label,
                'severity' => $severity,
                'is_scheduled_working' => $isWorking,
            ]];
        });
    }

    /**
     * Export daily attendance records
     */
    public function exportDaily(Request $request, $format)
    {
        // TODO: Implement daily attendance export
        return response()->json(['message' => 'Export not yet implemented'], 501);
    }
    /**
     * Display timekeeping records
     */
    public function timekeeping(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'employee';

        // Employees use the simpler My Attendance page. The full timekeeping
        // exception/review dashboard is reserved for management users.
        if ($userRole === 'employee') {
            return redirect()->route('attendance.my');
        }

        $isHrOrAdmin = in_array($userRole, ['admin', 'hr']);
        $isManager = $userRole === 'manager';

        // Default to last 30 days
        $dateFrom = $request->query('date_from') ? Carbon::parse($request->query('date_from')) : Carbon::now()->subDays(30);
        $dateTo = $request->query('date_to') ? Carbon::parse($request->query('date_to')) : Carbon::now();

        $baseQuery = AttendanceRecord::whereDate('date', '>=', $dateFrom->toDateString())
            ->whereDate('date', '<=', $dateTo->toDateString());

        if ($isManager && $user->employee_id) {
            // Manager sees their own department/team only
            $baseQuery->whereHas('employee', function ($query) use ($user) {
                $query->managedBy($user->employee_id);
            });
        } elseif (!$isHrOrAdmin) {
            // Employee: filter by their own employee_id
            $employee = Employee::find($user->employee_id);
            if ($employee) {
                $baseQuery->where('employee_id', $employee->id);
            }
        }

        if ($request->filled('employee_id') && ($isHrOrAdmin || $isManager)) {
            $baseQuery->where('employee_id', $request->employee_id);
        }

        if ($request->filled('department_id') && $isHrOrAdmin) {
            $departmentId = $request->department_id;
            $baseQuery->whereHas('employee', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
        }

        $allAttendanceRecords = (clone $baseQuery)
            ->with(['employee.department', 'breaks', 'timeEntries'])
            ->orderBy('date', 'desc')
            ->get();

        $scheduleMap = EmployeeSchedule::query()
            ->whereIn('employee_id', $allAttendanceRecords->pluck('employee_id')->unique())
            ->whereBetween('date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->get()
            ->keyBy(fn (EmployeeSchedule $schedule) => $schedule->employee_id . '|' . $schedule->date->format('Y-m-d'));

        $allAttendanceRecords->each(function (AttendanceRecord $record) use ($scheduleMap) {
            $schedule = $scheduleMap->get($record->employee_id . '|' . Carbon::parse($record->date)->format('Y-m-d'));
            $workedHours = $record->calculateTotalHours();
            $exception = $this->timekeepingException($record, $schedule, $workedHours);

            $record->setRelation('assignedSchedule', $schedule);
            $record->setAttribute('display_worked_hours', $workedHours);
            $record->setAttribute('exception_code', $exception['code']);
            $record->setAttribute('exception_label', $exception['label']);
            $record->setAttribute('exception_severity', $exception['severity']);
        });

        $exceptionFilter = $request->query('exception');
        $filteredRecords = match ($exceptionFilter) {
            'manager_review' => $allAttendanceRecords
                ->where('exception_severity', 'review')
                ->values(),
            'blocking' => $allAttendanceRecords
                ->where('exception_severity', 'blocking')
                ->values(),
            'attention' => $allAttendanceRecords
                ->whereIn('exception_severity', ['blocking', 'review'])
                ->values(),
            null, '' => $allAttendanceRecords,
            default => $allAttendanceRecords
                ->where('exception_code', $exceptionFilter)
                ->values(),
        };

        $page = max(1, (int) $request->query('page', 1));
        $perPage = 50;
        $attendanceRecords = new LengthAwarePaginator(
            $filteredRecords->forPage($page, $perPage)->values(),
            $filteredRecords->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get employees for filter (HR/Admin sees everyone, manager sees own team)
        if ($isHrOrAdmin) {
            $employees = Employee::with('department')
                ->orderBy('first_name')
                ->get();
        } elseif ($isManager && $user->employee_id) {
            $employees = Employee::with('department')
                ->managedBy($user->employee_id)
                ->orderBy('first_name')
                ->get();
        } else {
            $employee = Employee::find($user->employee_id);
            $employees = $employee ? collect([$employee]) : collect();
        }

        $departments = $isManager && $user->employee_id
            ? \App\Models\Department::where('manager_id', $user->employee_id)->orderBy('name')->get()
            : \App\Models\Department::orderBy('name')->get();

        // Calculate summary statistics
        $summary = [
            'total_hours' => 0,
            'regular_hours' => 0,
            'overtime_hours' => 0,
            'average_hours' => 0,
        ];

        $summaryRecords = $filteredRecords;

        if ($summaryRecords->count() > 0) {
            $totalHours = 0;
            $regularHours = 0;
            $overtimeHours = 0;

            foreach ($summaryRecords as $record) {
                $workedHours = method_exists($record, 'calculateTotalHours')
                    ? $record->calculateTotalHours()
                    : (float) ($record->total_hours ?? 0);

                $totalHours += $workedHours;
                $regularHours += min($workedHours, 8);
                $overtimeHours += max(0, $workedHours - 8);
            }

            $summary['total_hours'] = round($totalHours, 2);
            $summary['regular_hours'] = round($regularHours, 2);
            $summary['overtime_hours'] = round($overtimeHours, 2);
            $summary['average_hours'] = round($totalHours / max(1, $summaryRecords->count()), 2);
        }

        return view('attendance.timekeeping', [
            'user' => $user,
            'employees' => $employees ?? collect(),
            'departments' => $departments,
            'attendanceRecords' => $attendanceRecords,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'summary' => $summary,
            'exceptionCounts' => $allAttendanceRecords->groupBy('exception_code')->map->count(),
        ]);
    }

    private function timekeepingException(
        AttendanceRecord $record,
        ?EmployeeSchedule $schedule,
        float $workedHours
    ): array {
        if ($record->status === AttendanceRecord::ON_LEAVE) {
            return ['code' => 'clear', 'label' => 'Approved leave', 'severity' => 'clear'];
        }

        if ($record->status === AttendanceRecord::OFFICIAL_BUSINESS) {
            return ['code' => 'clear', 'label' => 'Approved official business', 'severity' => 'clear'];
        }

        if ($record->status === AttendanceRecord::ABSENT && !$record->time_in && !$record->time_out) {
            return ['code' => 'clear', 'label' => 'Recorded absence', 'severity' => 'clear'];
        }

        if (($record->time_in && !$record->time_out) || (!$record->time_in && $record->time_out)) {
            return ['code' => 'incomplete', 'label' => 'Incomplete log', 'severity' => 'blocking'];
        }

        if ($record->hasInvalidTimeSpan() || $workedHours > 24) {
            return ['code' => 'invalid_duration', 'label' => 'Invalid duration', 'severity' => 'blocking'];
        }

        if (!$schedule) {
            return ['code' => 'missing_schedule', 'label' => 'Missing schedule', 'severity' => 'blocking'];
        }

        if (in_array($schedule->status, ['Day Off', 'Rest Day'], true) && $workedHours > 0) {
            return ['code' => 'rest_day_attendance', 'label' => 'Rest-day duty review', 'severity' => 'review'];
        }

        if ($schedule->status === 'Working' && $record->time_in && $schedule->time_in) {
            $date = Carbon::parse($record->date)->format('Y-m-d');
            $scheduledIn = Carbon::parse($date . ' ' . Carbon::parse($schedule->time_in)->format('H:i:s'));
            $actualIn = Carbon::parse($record->time_in);

            if (abs($scheduledIn->diffInMinutes($actualIn, false)) >= 4 * 60) {
                return ['code' => 'possible_wrong_schedule', 'label' => 'Possible wrong schedule', 'severity' => 'review'];
            }
        }

        return ['code' => 'clear', 'label' => 'No exception', 'severity' => 'clear'];
    }

    /**
     * Export timekeeping records
     */
    public function exportTimekeeping(Request $request, $format)
    {
        if ((Auth::user()->role ?? 'employee') === 'employee') {
            return redirect()->route('attendance.my');
        }

        // TODO: Implement timekeeping export
        return response()->json(['message' => 'Export not yet implemented'], 501);
    }

    /**
     * Display attendance reports
     */
    public function reports(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'employee';
        $isHrOrAdmin = in_array($userRole, ['admin', 'hr']);

        $reportType = $request->query('report_type', 'daily');
        $departmentId = $request->query('department_id');

        switch ($reportType) {
            case 'weekly':
                $dateFrom = $request->query('date_from') ? Carbon::parse($request->query('date_from'))->startOfDay() : Carbon::now()->startOfWeek();
                $dateTo = $request->query('date_to') ? Carbon::parse($request->query('date_to'))->endOfDay() : $dateFrom->copy()->endOfWeek();
                break;
            case 'monthly':
                if ($request->filled('month')) {
                    $month = Carbon::parse($request->query('month') . '-01');
                    $dateFrom = $month->copy()->startOfMonth();
                    $dateTo = $month->copy()->endOfMonth();
                } else {
                    $dateFrom = $request->query('date_from') ? Carbon::parse($request->query('date_from'))->startOfDay() : Carbon::now()->startOfMonth();
                    $dateTo = $request->query('date_to') ? Carbon::parse($request->query('date_to'))->endOfDay() : Carbon::now()->endOfMonth();
                }
                break;
            case 'yearly':
                $year = $request->query('year', Carbon::now()->year);
                $dateFrom = Carbon::parse($year . '-01-01')->startOfDay();
                $dateTo = Carbon::parse($year . '-12-31')->endOfDay();
                break;
            default:
                $dateFrom = $request->query('date_from') ? Carbon::parse($request->query('date_from'))->startOfDay() : Carbon::now()->startOfMonth();
                $dateTo = $request->query('date_to') ? Carbon::parse($request->query('date_to'))->endOfDay() : Carbon::now()->endOfMonth();
                break;
        }

        $baseQuery = AttendanceRecord::with(['employee.department'])
            ->whereDate('date', '>=', $dateFrom->toDateString())
            ->whereDate('date', '<=', $dateTo->toDateString());

        // If employee (not HR/Admin), filter by their own employee_id
        if (!$isHrOrAdmin) {
            $employee = Employee::find($user->employee_id);
            if ($employee) {
                $baseQuery->where('employee_id', $employee->id);
            }
        }

        if ($departmentId && $isHrOrAdmin) {
            $baseQuery->whereHas('employee', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
        }

        $attendanceRecords = $baseQuery->get();

        $presentDays = $attendanceRecords->whereIn('status', ['present', 'late', 'half_day'])->count();
        $absentDays = $attendanceRecords->where('status', 'absent')->count();
        $lateArrivals = $attendanceRecords->where('status', 'late')->count();
        $attendanceLeaveDays = $attendanceRecords->where('status', 'on_leave')->count();
        $officialBusiness = 0;
        $totalRecords = $attendanceRecords->count();
        $workingRecords = $presentDays + $absentDays;
        $attendanceRate = $workingRecords > 0 ? round(($presentDays / $workingRecords) * 100, 2) : 0;

        $approvedLeaveQuery = LeaveRequest::where('status', 'approved')
            ->where(function ($query) use ($dateFrom, $dateTo) {
                $query->whereDate('start_date', '>=', $dateFrom->toDateString())
                    ->whereDate('start_date', '<=', $dateTo->toDateString())
                    ->orWhere(function ($subQuery) use ($dateFrom, $dateTo) {
                        $subQuery->whereDate('end_date', '>=', $dateFrom->toDateString())
                            ->whereDate('end_date', '<=', $dateTo->toDateString());
                    })
                    ->orWhere(function ($subQuery) use ($dateFrom, $dateTo) {
                        $subQuery->whereDate('start_date', '<=', $dateFrom->toDateString())
                            ->whereDate('end_date', '>=', $dateTo->toDateString());
                    });
            });

        if (!$isHrOrAdmin) {
            $employee = Employee::find($user->employee_id);
            if ($employee) {
                $approvedLeaveQuery->where('employee_id', $employee->id);
            }
        } elseif ($departmentId) {
            $approvedLeaveQuery->whereHas('employee', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
        }

        $approvedLeaveCount = $approvedLeaveQuery->count();

        $summary = [
            'report' => $totalRecords,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_arrivals' => $lateArrivals,
            'attendance_rate' => $attendanceRate,
            'official_business' => $officialBusiness,
            'leave' => $approvedLeaveCount,
        ];

        $attendanceTrend = [
            'labels' => [],
            'data' => [],
        ];

        $currentDate = $dateFrom->copy();
        while ($currentDate->lte($dateTo)) {
            $dailyRecords = $attendanceRecords->where('date', $currentDate->format('Y-m-d'));
            $dailyPresent = $dailyRecords->whereIn('status', ['present', 'late', 'half_day'])->count();
            $dailyTotal = $dailyRecords->count();
            $attendanceTrend['labels'][] = $currentDate->format('M d');
            $attendanceTrend['data'][] = $dailyTotal > 0 ? round(($dailyPresent / $dailyTotal) * 100, 2) : 0;
            $currentDate->addDay();
        }

        $departmentStats = $attendanceRecords
            ->groupBy(fn ($record) => $record->employee?->department?->name ?? 'No Department')
            ->map(function ($records, $departmentName) {
                $present = $records->whereIn('status', ['present', 'late', 'half_day'])->count();
                $absent = $records->where('status', 'absent')->count();
                $late = $records->where('status', 'late')->count();
                $uniqueEmployees = $records->pluck('employee_id')->unique()->count();
                $attendanceRate = $present + $absent > 0 ? round(($present / ($present + $absent)) * 100, 2) : 0;

                return [
                    'department' => $departmentName,
                    'total_employees' => $uniqueEmployees,
                    'present' => $present,
                    'absent' => $absent,
                    'late' => $late,
                    'attendance_rate' => $attendanceRate,
                ];
            })
            ->values()
            ->toArray();

        $employeeStats = [];
        foreach ($attendanceRecords as $record) {
            if (!$record->employee) {
                continue;
            }

            $employeeId = $record->employee_id;
            if (!isset($employeeStats[$employeeId])) {
                $employeeStats[$employeeId] = [
                    'employee' => $record->employee,
                    'present' => 0,
                    'absent' => 0,
                    'late' => 0,
                    'total' => 0,
                    'last_attendance_date' => $record->date,
                ];
            }

            $employeeStats[$employeeId]['total']++;
            $employeeStats[$employeeId]['last_attendance_date'] = max($employeeStats[$employeeId]['last_attendance_date'], $record->date);

            if (in_array($record->status, ['present', 'late', 'half_day'])) {
                $employeeStats[$employeeId]['present']++;
            }
            if ($record->status === 'absent') {
                $employeeStats[$employeeId]['absent']++;
            }
            if ($record->status === 'late') {
                $employeeStats[$employeeId]['late']++;
            }
        }

        $employeeTotals = collect($employeeStats)
            ->transform(function ($stats) {
                $rate = $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100, 2) : 0;
                return array_merge($stats, ['rate' => $rate]);
            });

        $bestAttendance = $employeeTotals
            ->sortByDesc('rate')
            ->take(5)
            ->values()
            ->toArray();

        $needsAttention = $employeeTotals
            ->filter(fn ($stats) => $stats['rate'] < 90)
            ->sortBy('rate')
            ->take(5)
            ->values()
            ->toArray();

        if ($isHrOrAdmin) {
            $employees = Employee::with('department')
                ->orderBy('first_name')
                ->get();
        } else {
            $employee = Employee::find($user->employee_id);
            $employees = $employee ? collect([$employee]) : collect();
        }

        $departments = \App\Models\Department::orderBy('name')
            ->get();

        return view('attendance.reports', [
            'user' => $user,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'employees' => $employees,
            'departments' => $departments,
            'summary' => $summary,
            'reportType' => $reportType,
            'overtimeData' => [],
            'leaveData' => [],
            'attendanceTrend' => $attendanceTrend,
            'departmentStats' => $departmentStats,
            'bestAttendance' => $bestAttendance,
            'needsAttention' => $needsAttention,
        ]);
    }

    /**
     * Export attendance reports
     */
    public function exportReports(Request $request, $format)
    {
        // TODO: Implement reports export
        return response()->json(['message' => 'Export not yet implemented'], 501);
    }

    /**
     * Get attendance statistics
     */
    public function getStatistics(Request $request)
    {
        // TODO: Implement statistics retrieval
        return response()->json(['message' => 'Statistics not yet implemented'], 501);
    }

    /**
     * Show DTR import form
     */
    public function importDtr(Request $request)
    {
        return view('attendance.import-dtr', [
            'user' => Auth::user(),
            'recentImports' => collect([])
        ]);
    }

    /**
     * Process DTR import
     */
    public function processImportDtr(Request $request)
    {
        $request->validate([
            'dtr_file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('dtr_file');
            $filePath = $file->store('temp_dtr', 'local');
            $fullPath = Storage::disk('local')->path($filePath);
            $dtrService = new DtrImportService();
            $parsedData = $dtrService->parseDtrData($fullPath);
            $validation = $dtrService->validateParsedData($parsedData);

            session(['imported_records' => $parsedData->toArray()]);
            session(['import_validation' => $validation]);
            session(['import_file_path' => $fullPath]);

            Storage::disk('local')->delete($filePath);

            if ($validation['is_valid']) {
                return redirect()->route('attendance.import-dtr.review')
                    ->with('success', 'DTR file processed successfully. Please review the records before confirming.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'DTR file has validation errors. Please check the data and try again.')
                    ->with('validation_errors', $validation['errors'])
                    ->with('validation_warnings', $validation['warnings']);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process DTR file: ' . $e->getMessage());
        }
    }

    /**
     * Review imported DTR records
     */
    public function reviewImportDtr(Request $request)
    {
        $importedRecords = session('imported_records', []);
        $validation = session('import_validation', ['errors' => collect(), 'warnings' => collect(), 'is_valid' => true]);
        $filePath = session('import_file_path', '');

        if (empty($importedRecords)) {
            return redirect()->route('attendance.import-dtr')
                ->with('error', 'No imported records found. Please upload a DTR file first.');
        }

        $parsedData = collect($importedRecords);
        $validation['errors'] = collect($validation['errors'] ?? []);
        $validation['warnings'] = collect($validation['warnings'] ?? []);

        return view('attendance.import-dtr-review', [
            'user' => Auth::user(),
            'parsedData' => $parsedData,
            'importedRecords' => $importedRecords,
            'validation' => $validation,
            'fileName' => basename($filePath),
        ]);
    }

    /**
     * Confirm DTR import
     */
    public function confirmImportDtr(Request $request)
    {
        try {
            $importedRecords = session('imported_records', []);

            if (empty($importedRecords)) {
                return redirect()->route('attendance.import-dtr')
                    ->with('error', 'No imported records found. Please upload a DTR file first.');
            }

            $user = Auth::user();
            $hasCreatedByColumn = Schema::hasColumn('attendance_records', 'created_by');

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($importedRecords as $record) {
                try {
                    $employee = Employee::where('employee_id', $record['employee_id'])->first();

                    if (!$employee) {
                        $errorCount++;
                        $errors[] = "Employee {$record['employee_id']} not found";
                        continue;
                    }

                    $existingRecord = AttendanceRecord::where('employee_id', $employee->id)
                        ->where('date', $record['date'])
                        ->first();

                    if ($existingRecord) {
                        $updatePayload = [
                            'time_in' => $record['time_in'] ? Carbon::parse($record['date'] . ' ' . $record['time_in']) : null,
                            'time_out' => $record['time_out'] ? Carbon::parse($record['date'] . ' ' . $record['time_out']) : null,
                            'total_hours' => $record['total_hours'] ?? 0,
                            'overtime_hours' => $record['overtime_hours'] ?? 0,
                            'status' => $record['status'] ?? 'present',
                        ];

                        if ($hasCreatedByColumn && $user) {
                            $updatePayload['created_by'] = $user->id;
                        }

                        $existingRecord->update($updatePayload);
                    } else {
                        $createPayload = [
                            'employee_id' => $employee->id,
                            'date' => $record['date'],
                            'time_in' => $record['time_in'] ? Carbon::parse($record['date'] . ' ' . $record['time_in']) : null,
                            'time_out' => $record['time_out'] ? Carbon::parse($record['date'] . ' ' . $record['time_out']) : null,
                            'total_hours' => $record['total_hours'] ?? 0,
                            'overtime_hours' => $record['overtime_hours'] ?? 0,
                            'status' => $record['status'] ?? 'present',
                        ];

                        if ($hasCreatedByColumn && $user) {
                            $createPayload['created_by'] = $user->id;
                        }

                        AttendanceRecord::create($createPayload);
                    }

                    $successCount++;

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Error importing {$record['employee_id']} on {$record['date']}: " . $e->getMessage();
                }
            }

            session()->forget(['imported_records', 'import_validation', 'import_file_path']);

            $message = "Successfully imported {$successCount} attendance records.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} records failed to import.";
            }

            if ($successCount > 0) {
                return redirect()->route('attendance.timekeeping')
                    ->with('success', $message);
            } else {
                return redirect()->route('attendance.import-dtr')
                    ->with('error', $message . ' ' . implode('; ', $errors));
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to confirm DTR import: ' . $e->getMessage());
        }
    }

    /**
     * Display temporary timekeeping records
     */
    public function tempTimekeeping(Request $request)
    {
        $employees = Employee::with('department')
            ->orderBy('first_name')
            ->get();

        return view('attendance.temp-timekeeping', [
            'user' => Auth::user(),
            'employees' => $employees,
        ]);
    }

    /**
     * Approve temporary timekeeping records
     */
    public function approveTempTimekeeping(Request $request)
    {
        return response()->json(['message' => 'Approval not yet implemented'], 501);
    }

    /**
     * Show create attendance record form
     */
    public function createRecord(Request $request)
    {
        $employees = Employee::with('department')
            ->orderBy('first_name')
            ->get();

        return view('attendance.create-record', [
            'user' => Auth::user(),
            'employees' => $employees
        ]);
    }

    /**
     * Store new attendance record (including Official Business)
     */
    public function storeRecord(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|string|in:' . implode(',', AttendanceRecord::STATUSES),
            'is_full_day' => 'nullable|in:0,1',
            'time_in' => 'nullable|required_unless:status,official_business|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after:time_in',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
            'notes' => 'nullable|string|max:500',
        ]);

        $isOfficialBusiness = $validated['status'] === AttendanceRecord::OFFICIAL_BUSINESS;
        // Default to full day when OB is selected but the radio somehow didn't come through
        $isFullDayOb = $isOfficialBusiness ? (($validated['is_full_day'] ?? '1') === '1') : null;

        // Partial-day OB needs an explicit start/end window. Full-day OB
        // doesn't use clock times at all — scheduled shift hours are
        // credited automatically via OfficialBusinessRequest::computeCreditedHours().
        if ($isOfficialBusiness && !$isFullDayOb && (empty($validated['time_in']) || empty($validated['time_out']))) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please provide both an OB start time and end time for partial-day Official Business.');
        }

        if ($isOfficialBusiness && empty($validated['notes'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please provide a reason/notes for Official Business.');
        }


        $conflicts = app(\App\Services\PayrollRequestConflictService::class);

        if ($conflicts->payrollGeneratedForDate($validated['employee_id'], $validated['date'])) {
            return redirect()->back()->withInput()->with(
                'error',
                'Cannot add a record — payroll has already been generated for this date. An Admin must reopen the payroll period before this date can be edited.'
            );
        }

        if ($isOfficialBusiness) {
            if ($conflicts->leaveOnDate($validated['employee_id'], $validated['date'])) {
                return redirect()->back()->withInput()->with(
                    'error',
                    'Official Business cannot be manually added on a date covered by pending or approved leave.'
                );
            }
            if ($conflicts->overtimeOnDate($validated['employee_id'], $validated['date'])) {
                return redirect()->back()->withInput()->with(
                    'error',
                    'Official Business cannot be manually added on a date with pending or approved overtime.'
                );
            }
        }

        $isCutoffOpen = $this->cutoffPeriods->isOpenForAction($validated['date']);
        $userRole = Auth::user()->role ?? null;
        $canOverrideCutoff = in_array($userRole, ['admin', 'hr']);

        if (!$isCutoffOpen) {
            if (!$canOverrideCutoff) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This date is not included in the current payroll cutoff period.');
            }

            if (!$request->boolean('override_cutoff')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This date falls outside the current payroll cutoff period. Check "Add anyway" below to confirm and resubmit.')
                    ->with('cutoff_override_needed', true);
            }
        }

        $existingRecord = AttendanceRecord::where('employee_id', $validated['employee_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($existingRecord) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An attendance record already exists for this employee on this date. Please edit the existing record instead.');
        }

        $payload = [
            'employee_id' => $validated['employee_id'],
            'date' => $validated['date'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        // Credited hours for full-day OB, computed up front so it can go
        // straight onto the attendance record (which never gets time_in/out).
        $fullDayCreditedHours = null;

        if ($isOfficialBusiness && $isFullDayOb) {
            $obLookup = new OfficialBusinessRequest([
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
                'is_full_day' => true,
                'ob_start_time' => null,
                'ob_end_time' => null,
            ]);
            $fullDayCreditedHours = $obLookup->computeCreditedHours();

            $payload['time_in'] = null;
            $payload['time_out'] = null;
            $payload['break_start'] = null;
            $payload['break_end'] = null;
            $payload['total_hours'] = $fullDayCreditedHours;
            $payload['regular_hours'] = min(8, $fullDayCreditedHours);
            $payload['overtime_hours'] = 0;
        } else {
            $payload['time_in'] = $validated['time_in'] ? Carbon::parse($validated['date'] . ' ' . $validated['time_in']) : null;
            $payload['time_out'] = $validated['time_out'] ? Carbon::parse($validated['date'] . ' ' . $validated['time_out']) : null;
            $payload['break_start'] = $validated['break_start'] ? Carbon::parse($validated['date'] . ' ' . $validated['break_start']) : null;
            $payload['break_end'] = $validated['break_end'] ? Carbon::parse($validated['date'] . ' ' . $validated['break_end']) : null;
            $payload['total_hours'] = 0;
            $payload['regular_hours'] = 0;
            $payload['overtime_hours'] = 0;
        }

        if (Schema::hasColumn('attendance_records', 'created_by') && Auth::check()) {
            $payload['created_by'] = Auth::id();
        }

        if (Schema::hasColumn('attendance_records', 'created_outside_cutoff')) {
            $payload['created_outside_cutoff'] = !$isCutoffOpen;
        }

        try {
            $record = AttendanceRecord::create($payload);

            if ($isOfficialBusiness) {
                OfficialBusinessRequest::create([
                    'employee_id' => $validated['employee_id'],
                    'date' => $validated['date'],
                    'reason' => $validated['notes'],
                    'status' => OfficialBusinessRequest::APPROVED,
                    'is_full_day' => $isFullDayOb,
                    'ob_start_time' => $isFullDayOb ? null : $validated['time_in'],
                    'ob_end_time' => $isFullDayOb ? null : $validated['time_out'],
                    'credited_hours' => $fullDayCreditedHours,
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => Carbon::now(),
                    'approved_by_role' => Auth::user()->role ?? null,
                    'attendance_record_id' => $record->id,
                    'created_by' => Auth::id(),
                ]);
            }

            if ($record->time_in && $record->time_out) {
                $totalHours = $record->calculateTotalHours();
                $hoursSplit = $record->calculateRegularAndOvertimeHours();

                $record->update([
                    'total_hours' => $totalHours,
                    'regular_hours' => $hoursSplit['regular_hours'],
                    'overtime_hours' => $hoursSplit['overtime_hours'],
                ]);

                if ($isOfficialBusiness) {
                    // Partial-day OB: credited hours are the exact OB window
                    // duration (via computeCreditedHours()), not the generic
                    // regular/overtime split used for clock-based statuses.
                    $obRequest = OfficialBusinessRequest::where(
                        'attendance_record_id',
                        $record->id
                    )->first();

                    $creditedHours = $obRequest
                        ? $obRequest->computeCreditedHours()
                        : $totalHours;

                    $record->update([
                        'total_hours' => $creditedHours,
                        'regular_hours' => min(8, $creditedHours),
                        'overtime_hours' => 0,
                    ]);

                    $obRequest?->update([
                        'credited_hours' => $creditedHours,
                    ]);
                }
            }

            $statusLabel = ucwords(str_replace('_', ' ', $validated['status']));

            return redirect()->route('attendance.daily')
                ->with('success', "{$statusLabel} record saved successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save attendance record: ' . $e->getMessage());
        }
    }

    /**
     * Show edit attendance record form
     */
    public function editRecord(Request $request, $id)
    {
        $attendanceRecord = AttendanceRecord::with('employee.department')->findOrFail($id);
        $employees = Employee::with('department')
            ->orderBy('first_name')
            ->get();

        return view('attendance.edit-record', [
            'id' => $id,
            'user' => Auth::user(),
            'employees' => $employees,
            'attendanceRecord' => $attendanceRecord,
        ]);
    }

    /**
     * Apply an audited manual correction. Imported time entries remain intact;
     * AttendanceRecord::calculateTotalHours() gives this explicit correction
     * precedence for payroll.
     */
    public function updateRecord(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:present,absent,late,half_day,on_leave,official_business'],
            'time_in' => ['nullable', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i'],
            'break_start' => ['nullable', 'date_format:H:i'],
            'break_end' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'correction_reason' => ['required', 'string', 'max:1000'],
        ]);

        $attendanceRecord = AttendanceRecord::findOrFail($id);
        $date = Carbon::parse($validated['date'])->toDateString();

        $conflicts = app(\App\Services\PayrollRequestConflictService::class);
        $originalDate = Carbon::parse($attendanceRecord->date)->toDateString();
        if ($conflicts->payrollGeneratedForDate($validated['employee_id'], $originalDate)
            || ($date !== $originalDate && $conflicts->payrollGeneratedForDate($validated['employee_id'], $date))) {
            return redirect()->back()->withInput()->with(
                'error',
                'Cannot edit this record — payroll has already been generated for this date. An Admin must reopen the payroll period first.'
            );
        }

        $toDateTime = static fn (?string $time) => $time ? Carbon::parse("{$date} {$time}") : null;
        $timeIn = $toDateTime($validated['time_in'] ?? null);
        $timeOut = $toDateTime($validated['time_out'] ?? null);

        if ($timeIn && $timeOut && $timeOut->lte($timeIn)) {
            $timeOut->addDay();
        }

        $correctedValues = [
            'employee_id' => $validated['employee_id'],
            'date' => $date,
            'status' => $validated['status'],
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'break_start' => $toDateTime($validated['break_start'] ?? null),
            'break_end' => $toDateTime($validated['break_end'] ?? null),
            'notes' => $validated['notes'] ?? null,
        ];

        $auditFields = array_keys($correctedValues);
        $originalValues = $attendanceRecord->only($auditFields);

        DB::transaction(function () use ($attendanceRecord, $correctedValues, $originalValues, $validated) {
            $attendanceRecord->fill($correctedValues);
            $attendanceRecord->corrected_by = Auth::id();
            $attendanceRecord->correction_reason = $validated['correction_reason'];
            $attendanceRecord->corrected_at = now();
            $attendanceRecord->save();

            $hours = $attendanceRecord->calculateRegularAndOvertimeHours();
            $attendanceRecord->update([
                'total_hours' => $attendanceRecord->calculateTotalHours(),
                'regular_hours' => $hours['regular_hours'],
                'overtime_hours' => $hours['overtime_hours'],
            ]);

            DB::table('attendance_corrections')->insert([
                'id' => (string) Str::uuid(),
                'attendance_record_id' => $attendanceRecord->id,
                'corrected_by' => Auth::id(),
                'reason' => $validated['correction_reason'],
                'original_values' => json_encode($originalValues),
                'corrected_values' => json_encode($attendanceRecord->fresh()->only(array_keys($correctedValues))),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()
            ->route('attendance.daily', ['date' => $date])
            ->with('success', 'Attendance correction saved with an audit trail.');
    }

    /**
     * Display employee's own attendance records (EMPLOYEE ONLY)
     */
    public function myAttendance(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'employee';

        // Only employees can access this - HR/Admin redirect to main attendance
        if (in_array($userRole, ['admin', 'hr'])) {
            return redirect()->route('attendance.daily')
                ->with('info', 'HR and Admin should use the main attendance page.');
        }

        $employee = Employee::find($user->employee_id);

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'No employee record found. Please contact HR.');
        }

        // Filter by month (default: current month)
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        try {
            $dateFrom = Carbon::parse($month . '-01')->startOfMonth();
            $dateTo = Carbon::parse($month . '-01')->endOfMonth();
        } catch (\Exception $e) {
            $month = Carbon::now()->format('Y-m');
            $dateFrom = Carbon::now()->startOfMonth();
            $dateTo = Carbon::now()->endOfMonth();
        }

        $recordsQuery = AttendanceRecord::where('employee_id', $employee->id)
            ->whereBetween('date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->with(['breaks', 'timeEntries']);

        $summaryRecords = (clone $recordsQuery)->get();
        $records = $recordsQuery->orderBy('date', 'desc')->paginate(15);
        $scheduleMap = EmployeeSchedule::where('employee_id', $employee->id)
            ->whereBetween('date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->get()
            ->keyBy(fn (EmployeeSchedule $schedule) => $schedule->date->toDateString());

        $records->getCollection()->each(function (AttendanceRecord $record) use ($scheduleMap) {
            $record->setRelation('assignedSchedule', $scheduleMap->get($record->date->toDateString()));
            $record->setAttribute('display_worked_hours', $record->calculateTotalHours());
        });

        // Calculate summary
        $summary = [
            'total_hours' => round($summaryRecords->sum(fn (AttendanceRecord $record) => $record->calculateTotalHours()), 2),
            'present' => $summaryRecords->whereIn('status', ['present', 'late'])->count(),
            'absent' => $summaryRecords->where('status', 'absent')->count(),
            'late' => $summaryRecords->where('status', 'late')->count(),
        ];

        return view('employee.attendance', [
            'records' => $records,
            'summary' => $summary,
            'month' => $month,
            'employee' => $employee,
            'scheduleMap' => $scheduleMap,
        ]);
    }

    /**
     * Display the authenticated employee's own monthly schedule (view only).
     */
    public function mySchedule(Request $request)
    {
        $user = Auth::user();
        abort_unless(($user->role ?? null) === 'employee', 403);

        $employee = Employee::with(['department', 'position'])->find($user->employee_id);

        if (!$employee) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'No employee record found. Please contact HR.');
        }

        $month = $request->query('month', Carbon::now()->format('Y-m'));

        try {
            $monthStart = Carbon::createFromFormat('Y-m-d', $month . '-01')->startOfMonth();
        } catch (\Throwable $exception) {
            $monthStart = Carbon::now()->startOfMonth();
            $month = $monthStart->format('Y-m');
        }

        $monthEnd = $monthStart->copy()->endOfMonth();
        $schedules = EmployeeSchedule::where('employee_id', $employee->id)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('date')
            ->get()
            ->keyBy(fn (EmployeeSchedule $schedule) => $schedule->date->toDateString());

        $attendance = AttendanceRecord::with(['breaks', 'timeEntries'])
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->keyBy(fn (AttendanceRecord $record) => $record->date->toDateString());

        $officialBusiness = OfficialBusinessRequest::where('employee_id', $employee->id)
            ->where('status', OfficialBusinessRequest::APPROVED)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->keyBy(fn (OfficialBusinessRequest $request) => $request->date->toDateString());

        $approvedLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', LeaveRequest::APPROVED)
            ->whereDate('start_date', '<=', $monthEnd->toDateString())
            ->whereDate('end_date', '>=', $monthStart->toDateString())
            ->get();
        $leaveByDay = collect();

        foreach ($approvedLeaves as $leave) {
            $firstDay = $leave->start_date->copy()->max($monthStart);
            $lastDay = $leave->end_date->copy()->min($monthEnd);
            for ($date = $firstDay; $date->lte($lastDay); $date->addDay()) {
                $leaveByDay->put($date->toDateString(), $leave);
            }
        }

        $dayStatuses = collect();
        for ($date = $monthStart->copy(); $date->lte($monthEnd); $date->addDay()) {
            $dateKey = $date->toDateString();
            $schedule = $schedules->get($dateKey);
            $record = $attendance->get($dateKey);
            $leave = $leaveByDay->get($dateKey);
            $ob = $officialBusiness->get($dateKey);

            if ($leave && $ob) {
                $status = ['label' => 'Leave / OB Conflict', 'tone' => 'red'];
            } elseif ($leave) {
                $status = ['label' => 'Approved ' . LeaveRequest::labelFor($leave->leave_type), 'tone' => 'indigo'];
            } elseif ($ob) {
                $status = ['label' => 'Approved Official Business', 'tone' => 'indigo'];
            } elseif (!$schedule || $date->isFuture()) {
                continue;
            } elseif ($record && (($record->time_in && !$record->time_out) || (!$record->time_in && $record->time_out))) {
                $status = ['label' => 'Incomplete Log', 'tone' => 'red'];
            } elseif ($record && $record->hasInvalidTimeSpan()) {
                $status = ['label' => 'Invalid Duration', 'tone' => 'red'];
            } elseif ($record && $record->time_in && $record->time_out) {
                $isLate = $record->status === AttendanceRecord::LATE;
                $status = ['label' => $isLate ? 'Late' : 'Present', 'tone' => $isLate ? 'amber' : 'green'];
            } elseif ($date->isToday() && $schedule->status === 'Working') {
                $status = ['label' => 'Not Yet Recorded', 'tone' => 'gray'];
            } elseif ($date->isPast() && $schedule->status === 'Working') {
                $status = ['label' => 'Absent', 'tone' => 'red'];
            } else {
                continue;
            }

            $dayStatuses->put($dateKey, $status);
        }

        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);
        $calendarDays = collect();

        for ($date = $calendarStart->copy(); $date->lte($calendarEnd); $date->addDay()) {
            $calendarDays->push([
                'date' => $date->copy(),
                'in_month' => $date->month === $monthStart->month,
                'schedule' => $date->month === $monthStart->month
                    ? $schedules->get($date->toDateString())
                    : null,
                'day_status' => $date->month === $monthStart->month
                    ? $dayStatuses->get($date->toDateString())
                    : null,
            ]);
        }

        return view('employee.schedule', [
            'user' => $user,
            'employee' => $employee,
            'month' => $month,
            'monthStart' => $monthStart,
            'calendarDays' => $calendarDays,
        ]);
    }

    /**
     * Display attendance settings, loaded from persisted AttendanceSetting rows.
     */
    public function settings(Request $request)
    {
        return view('attendance.settings', [
            'user' => Auth::user(),
            'overtimeRateMultiplier' => \App\Models\AttendanceSetting::getValue('overtime_rate_multiplier', '1.5'),
            'maxOvertimeHours' => \App\Models\AttendanceSetting::getValue('max_overtime_hours', '4'),
            'requireOvertimeApproval' => \App\Models\AttendanceSetting::getValue('require_overtime_approval', '0'),
            'autoCalculateOvertime' => \App\Models\AttendanceSetting::getValue('auto_calculate_overtime', '1'),
        ]);
    }

    /**
     * Persist attendance/overtime settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'overtime_rate_multiplier' => 'required|numeric|min:1|max:3',
            'max_overtime_hours' => 'required|numeric|min:1|max:12',
            'require_overtime_approval' => 'nullable|boolean',
            'auto_calculate_overtime' => 'nullable|boolean',
        ]);

        \App\Models\AttendanceSetting::setValue('overtime_rate_multiplier', (string) $validated['overtime_rate_multiplier'], 'Rate multiplier for overtime hours (e.g., 1.5 = 150%)');
        \App\Models\AttendanceSetting::setValue('max_overtime_hours', (string) $validated['max_overtime_hours'], 'Maximum overtime hours allowed per day');
        \App\Models\AttendanceSetting::setValue('require_overtime_approval', $request->boolean('require_overtime_approval') ? '1' : '0', 'Overtime must be approved by supervisor');
        \App\Models\AttendanceSetting::setValue('auto_calculate_overtime', $request->boolean('auto_calculate_overtime') ? '1' : '0', 'Automatically calculate overtime hours');

        return redirect()->route('attendance.settings')->with('success', 'Settings updated successfully');
    }
}