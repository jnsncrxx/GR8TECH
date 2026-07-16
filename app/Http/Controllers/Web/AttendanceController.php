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

        // Get all employees
        $employees = Employee::with('department')
            ->orderBy('first_name')
            ->paginate(15);

        // Get attendance records for the date
        $attendanceRecords = AttendanceRecord::where('date', $date->format('Y-m-d'))
            ->get()
            ->keyBy('employee_id');

        // Load approved OB details linked to attendance records so the daily
        // table can display OB Time In, Time Out, and credited hours.
        $officialBusinessByAttendanceId = OfficialBusinessRequest::query()
            ->whereDate('date', $date->format('Y-m-d'))
            ->where('status', OfficialBusinessRequest::APPROVED)
            ->whereNotNull('attendance_record_id')
            ->get()
            ->keyBy('attendance_record_id');

        // Calculate summary statistics
        // Get total count globally rather than just from the paginator's current page
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

        return view('attendance.daily', [
            'user' => Auth::user(),
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

        $employees = Employee::with('department')
            ->orderBy('first_name')
            ->get();

        $departments = \App\Models\Department::orderBy('name')
            ->get();

        // Default to last 30 days
        $dateFrom = $request->query('date_from') ? Carbon::parse($request->query('date_from')) : Carbon::now()->subDays(30);
        $dateTo = $request->query('date_to') ? Carbon::parse($request->query('date_to')) : Carbon::now();

        $baseQuery = AttendanceRecord::whereDate('date', '>=', $dateFrom->toDateString())
            ->whereDate('date', '<=', $dateTo->toDateString());

        if ($request->filled('employee_id')) {
            $baseQuery->where('employee_id', $request->employee_id);
        }

        if ($request->filled('department_id')) {
            $departmentId = $request->department_id;
            $baseQuery->whereHas('employee', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
        }

        $attendanceRecords = (clone $baseQuery)
            ->with(['employee.department', 'breaks', 'timeEntries'])
            ->orderBy('date', 'desc')
            ->paginate(50);

        // Calculate summary statistics
        // Timekeeping expects: total_hours, regular_hours, overtime_hours, average_hours
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
            'employees' => $employees,
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

        if ($departmentId) {
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

        if ($departmentId) {
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

        $employees = Employee::with('department')
            ->orderBy('first_name')
            ->get();

        $departments = \App\Models\Department::orderBy('name')
            ->get();

        return view('attendance.reports', [
            'user' => Auth::user(),
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
            'recentImports' => collect([]) // Mocking empty collection for now to clear the error
        ]);
    }

    /**
     * Process DTR import
     */
    public function processImportDtr(Request $request)
    {
        // Validate that file is present
        $request->validate([
            'dtr_file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {
            // Store uploaded file temporarily
            $file = $request->file('dtr_file');
            $filePath = $file->store('temp_dtr', 'local');

            // Construct the full path using Storage disk path
            $fullPath = Storage::disk('local')->path($filePath);

            // Initialize the DTR Import Service
            $dtrService = new DtrImportService();

            // Parse the DTR data from the file
            $parsedData = $dtrService->parseDtrData($fullPath);

            // Validate the parsed data
            $validation = $dtrService->validateParsedData($parsedData);

            // Store the parsed data in session for review
            session(['imported_records' => $parsedData->toArray()]);
            session(['import_validation' => $validation]);
            session(['import_file_path' => $fullPath]);

            // Clean up the temporary file
            Storage::disk('local')->delete($filePath);

            // Return success response with redirect to review page
            if ($validation['is_valid']) {
                return redirect()->route('attendance.import-dtr.review')
                    ->with('success', 'DTR file processed successfully. Please review the records before confirming.');
            } else {
                // Return to import page with errors
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
            // Get the imported records from session
            $importedRecords = session('imported_records', []);

            if (empty($importedRecords)) {
                return redirect()->route('attendance.import-dtr')
                    ->with('error', 'No imported records found. Please upload a DTR file first.');
            }

            // Get current user for created_by field
            $user = Auth::user();
            $hasCreatedByColumn = Schema::hasColumn('attendance_records', 'created_by');

            // Process and store each record
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($importedRecords as $record) {
                try {
                    // Find the employee by employee_id
                    $employee = Employee::where('employee_id', $record['employee_id'])->first();

                    if (!$employee) {
                        $errorCount++;
                        $errors[] = "Employee {$record['employee_id']} not found";
                        continue;
                    }

                    // Check if attendance record already exists
                    $existingRecord = AttendanceRecord::where('employee_id', $employee->id)
                        ->where('date', $record['date'])
                        ->first();

                    if ($existingRecord) {
                        // Update existing record
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
                        // Create new record
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

            // Clear session data
            session()->forget(['imported_records', 'import_validation', 'import_file_path']);

            // Prepare message
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
        // TODO: Implement temp timekeeping approval
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
        // Validate inputs
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

        // Official Business always requires Time In/Time Out now — no full-day
        // option, matching the employee-facing "Apply for Official Business"
        // form (see OfficialBusinessController::store()). The base validation
        // above exempts official_business from the generic time_in requirement
        // (a holdover from when full-day OB needed no times), so enforce it
        // explicitly here instead.
        if ($isOfficialBusiness && (empty($validated['time_in']) || empty($validated['time_out']))) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Time In and Time Out are required for an Official Business record.');
        }

        // Official Business requires a reason so it's clear why the employee was out
        if ($isOfficialBusiness && empty($validated['notes'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please provide a reason/notes for Official Business.');
        }

        // Cutoff check: manual entries should respect the same payroll cutoff
        // window as the employee-facing OB form (see
        // OfficialBusinessController::store()), not bypass it silently.
        // Admin/HR may still record a closed-period entry (backfills, disputes,
        // outage recovery), but only with an explicit acknowledgment — never
        // as a silent default — so there's always a visible trail of when this
        // happened. Managers get the same hard block as employees would.
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

        // Check existing attendance status for this employee on this date
        $existingRecord = AttendanceRecord::where('employee_id', $validated['employee_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($existingRecord) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An attendance record already exists for this employee on this date. Please edit the existing record instead.');
        }

        // Build the payload
        $payload = [
            'employee_id' => $validated['employee_id'],
            'date' => $validated['date'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        // Time In/Out/Break are populated identically regardless of status.
        // Official Business is not a special case here — per the finalized
        // spec, it's just a clock-based record like any other, so a break
        // taken during an OB span (e.g. it happens to cross lunchtime) is
        // handled the exact same way it would be for a regular attendance
        // record. total_hours/regular_hours/overtime_hours are computed after
        // the record is created via AttendanceRecord's own calculateTotalHours()
        // / calculateRegularAndOvertimeHours() (see below) for every status,
        // not just OB, so there's a single source of truth for this math
        // instead of duplicating it here.
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

        // Audit trail for the cutoff override above, so a backfilled record
        // outside the normal window is visibly flagged rather than
        // indistinguishable from a normal in-window entry.
        if (Schema::hasColumn('attendance_records', 'created_outside_cutoff')) {
            $payload['created_outside_cutoff'] = !$isCutoffOpen;
        }

        try {
            $record = AttendanceRecord::create($payload);

            // Keep the Official Business module in sync: manually recording an
            // Official Business attendance entry here should also create the
            // matching (already-approved) OfficialBusinessRequest, otherwise it
            // never shows up on the Official Business page.
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
                    // Same reviewer-tracking as the employee-request approval
                    // path (see OfficialBusinessController::updateStatus()) —
                    // without this, a backfilled OB record would silently
                    // break the reviewer-role reporting everywhere else.
                    'approved_by_role' => Auth::user()->role ?? null,
                    'attendance_record_id' => $record->id,
                    'created_by' => Auth::id(),
                    // credited_hours filled in right after, once total_hours is
                    // computed below via the same canonical path every status uses.
                ]);
            }

            // Compute hours the same way for every status, via AttendanceRecord's
            // own canonical methods — no separate manual calculation for
            // non-OB statuses, so this can't drift out of sync with OB's math
            // (e.g. if the break-subtraction logic ever changes).
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
}