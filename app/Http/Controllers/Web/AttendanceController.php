<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OfficialBusinessRequest;
use App\Services\CutoffPeriodService;
use App\Services\DtrImportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
            $employees = Employee::with('department')
                ->orderBy('first_name')
                ->paginate(15);

            $attendanceRecords = AttendanceRecord::where('date', $date->format('Y-m-d'))
                ->get()
                ->keyBy('employee_id');

            $total = Employee::count();
            $present = $attendanceRecords->count();
            $absent = $total - $present;
            $late = $attendanceRecords->where('status', 'late')->count();
            $attendanceRate = $total > 0 ? round(($present / $total) * 100, 2) : 0;

            $summary = [
                'total_employees' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'attendance_rate' => $attendanceRate,
            ];

        } else {
            // === EMPLOYEE: Sarili lang ===
            $employee = Employee::find($user->employee_id);

            if (!$employee) {
                return redirect()->route('dashboard')->with('error', 'No employee record found.');
            }

            $employees = Employee::where('id', $employee->id)
                ->with('department')
                ->paginate(15);

            $attendanceRecords = AttendanceRecord::where('date', $date->format('Y-m-d'))
                ->where('employee_id', $employee->id)
                ->get()
                ->keyBy('employee_id');

            $summary = [
                'total_employees' => 1,
                'present' => $attendanceRecords->count(),
                'absent' => 0,
                'late' => $attendanceRecords->where('status', 'late')->count(),
                'attendance_rate' => $attendanceRecords->count() > 0 ? 100 : 0,
            ];
        }

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
            'summary' => $summary,
        ]);
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
        $isHrOrAdmin = in_array($userRole, ['admin', 'hr']);

        // Default to last 30 days
        $dateFrom = $request->query('date_from') ? Carbon::parse($request->query('date_from')) : Carbon::now()->subDays(30);
        $dateTo = $request->query('date_to') ? Carbon::parse($request->query('date_to')) : Carbon::now();

        $baseQuery = AttendanceRecord::whereDate('date', '>=', $dateFrom->toDateString())
            ->whereDate('date', '<=', $dateTo->toDateString());

        // If employee (not HR/Admin), filter by their own employee_id
        if (!$isHrOrAdmin) {
            $employee = Employee::find($user->employee_id);
            if ($employee) {
                $baseQuery->where('employee_id', $employee->id);
            }
        }

        if ($request->filled('employee_id') && $isHrOrAdmin) {
            $baseQuery->where('employee_id', $request->employee_id);
        }

        if ($request->filled('department_id') && $isHrOrAdmin) {
            $departmentId = $request->department_id;
            $baseQuery->whereHas('employee', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
        }

        $attendanceRecords = (clone $baseQuery)
            ->with(['employee.department', 'breaks', 'timeEntries'])
            ->orderBy('date', 'desc')
            ->paginate(50);

        // Get employees for filter (HR/Admin only)
        if ($isHrOrAdmin) {
            $employees = Employee::with('department')
                ->orderBy('first_name')
                ->get();
        } else {
            $employee = Employee::find($user->employee_id);
            $employees = $employee ? collect([$employee]) : collect();
        }

        $departments = \App\Models\Department::orderBy('name')->get();

        // Calculate summary statistics
        $summary = [
            'total_hours' => 0,
            'regular_hours' => 0,
            'overtime_hours' => 0,
            'average_hours' => 0,
        ];

        $summaryRecords = (clone $baseQuery)
            ->with(['breaks', 'timeEntries'])
            ->get();

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
        ]);
    }

    /**
     * Export timekeeping records
     */
    public function exportTimekeeping(Request $request, $format)
    {
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
            'time_in' => 'nullable|required_unless:status,official_business|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after:time_in',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
            'notes' => 'nullable|string|max:500',
        ]);

        $isOfficialBusiness = $validated['status'] === AttendanceRecord::OFFICIAL_BUSINESS;

        if ($isOfficialBusiness && (empty($validated['time_in']) || empty($validated['time_out']))) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Time In and Time Out are required for an Official Business record.');
        }

        if ($isOfficialBusiness && empty($validated['notes'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please provide a reason/notes for Official Business.');
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

        $payload['time_in'] = $validated['time_in'] ? Carbon::parse($validated['date'] . ' ' . $validated['time_in']) : null;
        $payload['time_out'] = $validated['time_out'] ? Carbon::parse($validated['date'] . ' ' . $validated['time_out']) : null;
        $payload['break_start'] = $validated['break_start'] ? Carbon::parse($validated['date'] . ' ' . $validated['break_start']) : null;
        $payload['break_end'] = $validated['break_end'] ? Carbon::parse($validated['date'] . ' ' . $validated['break_end']) : null;
        $payload['total_hours'] = 0;
        $payload['regular_hours'] = 0;
        $payload['overtime_hours'] = 0;

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
                    'is_full_day' => false,
                    'ob_start_time' => $validated['time_in'],
                    'ob_end_time' => $validated['time_out'],
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
        $employees = Employee::with('department')
            ->orderBy('first_name')
            ->get();

        return view('attendance.edit-record', [
            'id' => $id,
            'user' => Auth::user(),
            'employees' => $employees
        ]);
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

        // Get employee's attendance records
        $records = AttendanceRecord::where('employee_id', $employee->id)
            ->whereBetween('date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->orderBy('date', 'desc')
            ->paginate(15);

        // Calculate summary
        $summary = [
            'total_hours' => $records->sum('total_hours'),
            'present' => $records->whereIn('status', ['present', 'late'])->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
        ];

        return view('employee.attendance', [
            'records' => $records,
            'summary' => $summary,
            'month' => $month,
            'employee' => $employee,
        ]);
    }
}