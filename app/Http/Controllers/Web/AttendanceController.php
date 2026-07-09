<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Services\DtrImportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
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

        // Official Business requires a reason so it's clear why the employee was out
        if ($isOfficialBusiness && empty($validated['notes'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please provide a reason/notes for Official Business.');
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

        if ($isOfficialBusiness) {
            // Official Business is not clock-based; no time in/out, breaks, or computed hours
            $payload['time_in'] = null;
            $payload['time_out'] = null;
            $payload['break_start'] = null;
            $payload['break_end'] = null;
            $payload['total_hours'] = 0;
            $payload['regular_hours'] = 0;
            $payload['overtime_hours'] = 0;
        } else {
            $payload['time_in'] = $validated['time_in'] ? Carbon::parse($validated['date'] . ' ' . $validated['time_in']) : null;
            $payload['time_out'] = $validated['time_out'] ? Carbon::parse($validated['date'] . ' ' . $validated['time_out']) : null;
            $payload['break_start'] = $validated['break_start'] ? Carbon::parse($validated['date'] . ' ' . $validated['break_start']) : null;
            $payload['break_end'] = $validated['break_end'] ? Carbon::parse($validated['date'] . ' ' . $validated['break_end']) : null;
        }

        if (Schema::hasColumn('attendance_records', 'created_by') && Auth::check()) {
            $payload['created_by'] = Auth::id();
        }

        try {
            $record = AttendanceRecord::create($payload);

            // For clock-based statuses, calculate hours from the time fields just saved
            if (!$isOfficialBusiness && $record->time_in && $record->time_out) {
                $timeIn = Carbon::parse($record->time_in);
                $timeOut = Carbon::parse($record->time_out);
                $totalMinutes = $timeIn->diffInMinutes($timeOut);

                $breakMinutes = 0;
                if ($record->break_start && $record->break_end) {
                    $breakMinutes = Carbon::parse($record->break_start)->diffInMinutes(Carbon::parse($record->break_end));
                }

                $totalHours = round(max(0, $totalMinutes - $breakMinutes) / 60, 2);
                $regularHours = min($totalHours, 8);
                $overtimeHours = max(0, $totalHours - 8);

                $record->update([
                    'total_hours' => $totalHours,
                    'regular_hours' => $regularHours,
                    'overtime_hours' => $overtimeHours,
                ]);
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