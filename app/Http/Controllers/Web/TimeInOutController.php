<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\CalculatesAttendanceWithOfficialBusiness;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\EmployeeBreak;
use App\Models\OfficialBusinessRequest;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimeInOutController extends Controller
{
    use CalculatesAttendanceWithOfficialBusiness;

    /**
     * Display Time In / Time Out page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user?->employee;

        $todayAttendance = null;
        $recentActivity = collect();
        $pendingOtReminders = collect();

        if ($employee) {
            $todayAttendance = AttendanceRecord::where(
                'employee_id',
                $employee->id
            )
                ->whereDate(
                    'date',
                    Carbon::today()->toDateString()
                )
                ->with([
                    'timeEntries',
                    'breaks',
                ])
                ->first();

            $recentActivity = AttendanceRecord::where(
                'employee_id',
                $employee->id
            )
                ->with([
                    'timeEntries',
                    'breaks',
                ])
                ->orderByDesc('date')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            $pendingOtReminders = \App\Models\OvertimeReminder::where('employee_id', $employee->id)
                ->where('status', \App\Models\OvertimeReminder::PENDING)
                ->orderBy('date', 'desc')
                ->get()
                ->filter(function ($reminder) use ($employee) {
                    return !\App\Models\OvertimeRequest::where('employee_id', $employee->id)
                        ->whereDate('date', $reminder->date)
                        ->whereIn('status', [\App\Models\OvertimeRequest::PENDING, \App\Models\OvertimeRequest::APPROVED])
                        ->exists();
                })
                ->values()
                ->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'date' => $r->date->format('Y-m-d'),
                        'date_formatted' => $r->date->format('M j, Y'),
                        'extra_hours' => (float) $r->extra_hours,
                        'start_time' => $r->start_time ? $r->start_time->format('H:i') : '17:00',
                        'end_time' => $r->end_time ? $r->end_time->format('H:i') : '18:30',
                        'start_time_formatted' => $r->start_time ? $r->start_time->format('g:i A') : '5:00 PM',
                        'end_time_formatted' => $r->end_time ? $r->end_time->format('g:i A') : '6:30 PM',
                    ];
                });
        }

        return view('attendance.time-in-out', [
            'user' => $user,
            'todayAttendance' => $todayAttendance,
            'recentActivity' => $recentActivity,
            'pendingOtReminders' => $pendingOtReminders,
            'activeRoute' => 'attendance.time-in-out',
        ]);
    }

    private function findActiveAttendanceRecord($employee): ?AttendanceRecord
    {
        $today = Carbon::today();

        $todayRecord = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('date', $today->toDateString())
            ->with(['timeEntries', 'breaks'])
            ->first();

        if ($todayRecord && $todayRecord->hasActiveTimeEntry()) {
            return $todayRecord;
        }

        $yesterdayRecord = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('date', $today->copy()->subDay()->toDateString())
            ->with(['timeEntries', 'breaks'])
            ->first();

        if ($yesterdayRecord && $yesterdayRecord->hasActiveTimeEntry()) {
            return $yesterdayRecord;
        }

        return $todayRecord;
    }

    /**
     * Time In.
     */
    public function timeIn(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user?->employee;

            if (!$employee) {
                return response()->json([
                    'error' => 'Employee record not found',
                ], 404);
            }

            $today = Carbon::today();
            $now = Carbon::now();

            if (app(\App\Services\PayrollPeriodLockService::class)
                ->isLockedForDate($employee->id, $today)) {
                return response()->json([
                    'error' => 'Attendance is locked for this payroll period. Ask an authorized user to reopen the period before clocking in.',
                ], 422);
            }

            $attendanceRecord = AttendanceRecord::where(
                'employee_id',
                $employee->id
            )
                ->whereDate('date', $today->toDateString())
                ->first();

            if (!$attendanceRecord) {
                $attendanceRecord = AttendanceRecord::create([
                        'employee_id' => $employee->id,
                        'date' => $today->toDateString(),
                        'status' => 'present',
                        'total_hours' => 0,
                        'regular_hours' => 0,
                        'overtime_hours' => 0,
                ]);
            }

            if ($attendanceRecord->timeEntries()
                ->whereNull('time_out')
                ->exists()) {
                return response()->json([
                    'error' =>
                        'You are already clocked in',
                ], 400);
            }

            $hasRecordedEntries = $attendanceRecord->timeEntries()->exists();

            TimeEntry::create([
                'attendance_record_id' =>
                    $attendanceRecord->id,

                'time_in' => $now,

                'entry_type' => 'regular',
            ]);

            /*
             * Store the first actual Time In.
             */
            if (
                !$hasRecordedEntries
                && !$attendanceRecord->corrected_at
            ) {
                $attendanceRecord->time_in = $now;
            } elseif (!$attendanceRecord->time_in) {
                $attendanceRecord->time_in = $now;
            }

            /*
             * OB-created attendance becomes present
             * once actual attendance starts.
             */
            if (
                $attendanceRecord->status
                === AttendanceRecord::OFFICIAL_BUSINESS
            ) {
                $attendanceRecord->status = 'present';
            }

            /*
             * Clear previous latest time out because
             * there is now an active session.
             */
            $attendanceRecord->time_out = null;

            $attendanceRecord->save();

            return response()->json([
                'success' => true,

                'message' =>
                    'Successfully clocked in at '
                    . $now->format('g:i A'),

                'time_in' =>
                    $now->toIso8601String(),

                'status' => 'clocked_in',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' =>
                    'An error occurred: '
                    . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Time Out.
     */
    public function timeOut(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user?->employee ?: ($user?->employee_id ? \App\Models\Employee::find($user->employee_id) : null);

            if (!$employee) {
                return response()->json([
                    'error' => 'Employee record not found',
                ], 404);
            }

            $attendanceRecord = $this->findActiveAttendanceRecord($employee);

            if (!$attendanceRecord) {
                return response()->json([
                    'error' =>
                        'No clock in record found for today',
                ], 400);
            }

            if (app(\App\Services\PayrollPeriodLockService::class)
                ->isLockedForDate($employee->id, $attendanceRecord->date)) {
                return response()->json([
                    'error' => 'Attendance is locked for this payroll period. Ask an authorized user to reopen the period before clocking out.',
                ], 422);
            }

            $activeEntry =
                $attendanceRecord->getActiveTimeEntry();

            if (!$activeEntry) {
                return response()->json([
                    'error' =>
                        'No active clock-in session found',
                ], 400);
            }

            /*
             * Do not allow Time Out while on break.
             */
            $activeBreak = EmployeeBreak::where(
                'attendance_record_id',
                $attendanceRecord->id
            )
                ->whereNull('break_end')
                ->first();

            if ($activeBreak) {
                return response()->json([
                    'error' =>
                        'Please end your active break before timing out.',
                ], 400);
            }

            $now = Carbon::now();

            DB::transaction(function () use (
                $activeEntry,
                $attendanceRecord,
                $now
            ) {
                /*
                 * Complete active work interval.
                 */
                $activeEntry->update([
                    'time_out' => $now,
                ]);

                /*
                 * Store latest Time Out.
                 */
                $attendanceRecord->time_out = $now;
                $attendanceRecord->status = $attendanceRecord->getCalculatedStatus();
                $attendanceRecord->save();

                /*
                 * Critical:
                 *
                 * Recalculate actual work + approved OB.
                 *
                 * Do not call calculateTotalHours() here
                 * because that may overwrite OB credit.
                 */
                $this
                    ->recalculateAttendanceWithOfficialBusiness(
                        $attendanceRecord
                    );
            });

            $attendanceRecord->refresh();

            // Detect rendered extra working hours for Overtime Reminder prompt
            $assignedSchedule = \App\Models\EmployeeSchedule::where('employee_id', $employee->id)
                ->whereDate('date', $attendanceRecord->date)
                ->first();

            $requiredHours = (float) ($assignedSchedule?->required_hours ?? 8.00);
            // Only actual rendered work can create an OT reminder. Credited OB
            // hours are included in total_hours but must never generate OT.
            $workedHours = (float) $attendanceRecord->calculateTotalHours();
            $extraHours = round(max(0, $workedHours - $requiredHours), 2);

            $overtimeDetected = false;
            $reminderData = null;

            if ($extraHours >= 0.01) {
                $hasOtRequest = \App\Models\OvertimeRequest::where('employee_id', $employee->id)
                    ->whereDate('date', $attendanceRecord->date)
                    ->whereIn('status', [\App\Models\OvertimeRequest::PENDING, \App\Models\OvertimeRequest::APPROVED])
                    ->exists();

                if (!$hasOtRequest) {
                    $startTime = $assignedSchedule?->time_out
                        ? Carbon::parse(
                            $attendanceRecord->date->format('Y-m-d').' '.$assignedSchedule->time_out
                        )
                        : $now->copy()->subMinutes(round($extraHours * 60));
                    $endTime = $now;

                    $reminder = \App\Models\OvertimeReminder::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'date' => $attendanceRecord->date->format('Y-m-d'),
                        ],
                        [
                            'attendance_record_id' => $attendanceRecord->id,
                            'required_hours' => $requiredHours,
                            'worked_hours' => $workedHours,
                            'extra_hours' => $extraHours,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'status' => \App\Models\OvertimeReminder::PENDING,
                        ]
                    );

                    $overtimeDetected = true;
                    $reminderData = [
                        'id' => $reminder->id,
                        'extra_hours' => $extraHours,
                        'date' => $attendanceRecord->date->format('Y-m-d'),
                        'date_formatted' => Carbon::parse($attendanceRecord->date)->format('M j, Y'),
                        'start_time' => $startTime->format('H:i'),
                        'end_time' => $endTime->format('H:i'),
                        'start_time_formatted' => $startTime->format('g:i A'),
                        'end_time_formatted' => $endTime->format('g:i A'),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully clocked out at ' . $now->format('g:i A'),
                'time_out' => $now->toIso8601String(),
                'total_hours' => $attendanceRecord->total_hours,
                'regular_hours' => $attendanceRecord->regular_hours,
                'overtime_hours' => $attendanceRecord->overtime_hours,
                'status' => 'clocked_out',
                'overtime_detected' => $overtimeDetected,
                'reminder' => $reminderData,
                'pending_overtime_reminder' => $reminderData,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' =>
                    'An error occurred: '
                    . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Start Break.
     */
    public function breakStart(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user?->employee;

            if (!$employee) {
                return response()->json([
                    'error' => 'Employee record not found',
                ], 404);
            }

            $attendanceRecord = $this->findActiveAttendanceRecord($employee);

            if (
                !$attendanceRecord
                || !$attendanceRecord->hasActiveTimeEntry()
            ) {
                return response()->json([
                    'error' =>
                        'You must be clocked in to start a break',
                ], 400);
            }

            $activeBreak = EmployeeBreak::where(
                'attendance_record_id',
                $attendanceRecord->id
            )
                ->whereNull('break_end')
                ->first();

            if ($activeBreak) {
                return response()->json([
                    'error' =>
                        'You are already on break',
                ], 400);
            }

            $breakCount = EmployeeBreak::where(
                'attendance_record_id',
                $attendanceRecord->id
            )
                ->count();

            if ($breakCount >= 2) {
                return response()->json([
                    'error' =>
                        'Maximum of 2 breaks per day allowed',
                ], 400);
            }

            $now = Carbon::now();

            EmployeeBreak::create([
                'attendance_record_id' =>
                    $attendanceRecord->id,

                'break_start' => $now,
            ]);

            return response()->json([
                'success' => true,

                'message' =>
                    'Break started at '
                    . $now->format('g:i A'),

                'break_start' =>
                    $now->toIso8601String(),

                'status' => 'on_break',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' =>
                    'An error occurred: '
                    . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * End Break.
     */
    public function breakEnd(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user?->employee;

            if (!$employee) {
                return response()->json([
                    'error' => 'Employee record not found',
                ], 404);
            }

            $attendanceRecord = $this->findActiveAttendanceRecord($employee);

            if (
                !$attendanceRecord
                || !$attendanceRecord->hasActiveTimeEntry()
            ) {
                return response()->json([
                    'error' =>
                        'You must be clocked in to end a break',
                ], 400);
            }

            $activeBreak = EmployeeBreak::where(
                'attendance_record_id',
                $attendanceRecord->id
            )
                ->whereNull('break_end')
                ->first();

            if (!$activeBreak) {
                return response()->json([
                    'error' =>
                        'You are not on break',
                ], 400);
            }

            $now = Carbon::now();

            $breakDurationMinutes = Carbon::parse(
                $activeBreak->break_start
            )->diffInMinutes($now);

            $activeBreak->update([
                'break_end' => $now,

                'break_duration_minutes' =>
                    $breakDurationMinutes,
            ]);

            $isOverBreak =
                $breakDurationMinutes > 120;

            $overBreakMinutes = max(
                0,
                $breakDurationMinutes - 120
            );

            return response()->json([
                'success' => true,

                'message' =>
                    'Break ended at '
                    . $now->format('g:i A'),

                'break_end' =>
                    $now->toIso8601String(),

                'break_duration_minutes' =>
                    $breakDurationMinutes,

                'is_over_break' =>
                    $isOverBreak,

                'over_break_minutes' =>
                    $overBreakMinutes,

                'status' => 'working',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' =>
                    'An error occurred: '
                    . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current attendance status.
     */
    public function getStatus(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user?->employee ?: ($user?->employee_id ? \App\Models\Employee::find($user->employee_id) : null);

            if (!$employee) {
                return response()->json([
                    'error' => 'Employee record not found',
                ], 404);
            }

            $attendanceRecord = $this->findActiveAttendanceRecord($employee);

            $pendingOtReminders = \App\Models\OvertimeReminder::where('employee_id', $employee->id)
                ->where('status', \App\Models\OvertimeReminder::PENDING)
                ->orderBy('date', 'desc')
                ->get()
                ->filter(function ($reminder) use ($employee) {
                    return !\App\Models\OvertimeRequest::where('employee_id', $employee->id)
                        ->whereDate('date', $reminder->date)
                        ->whereIn('status', [\App\Models\OvertimeRequest::PENDING, \App\Models\OvertimeRequest::APPROVED])
                        ->exists();
                })
                ->values()
                ->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'date' => $r->date->format('Y-m-d'),
                        'date_formatted' => $r->date->format('M j, Y'),
                        'extra_hours' => (float) $r->extra_hours,
                        'worked_hours' => (float) $r->worked_hours,
                        'required_hours' => (float) $r->required_hours,
                        'start_time' => $r->start_time ? $r->start_time->format('H:i') : null,
                        'end_time' => $r->end_time ? $r->end_time->format('H:i') : null,
                    ];
                })
                ->toArray();

            $status = [
                'employee_id' => $employee->id,
                'has_clocked_in' => false,
                'has_clocked_out' => false,
                'is_currently_clocked_in' => false,
                'is_on_break' => false,
                'time_in' => null,
                'time_out' => null,
                'break_start' => null,
                'break_end' => null,
                'active_break' => null,
                'breaks' => [],
                'break_count' => 0,
                'total_hours' => 0,
                'regular_hours' => 0,
                'overtime_hours' => 0,
                'can_time_in' => true,
                'can_time_out' => false,
                'can_break_start' => false,
                'can_break_end' => false,
                'status' => 'offline',
                'attendance_record' => null,
                'pending_overtime_reminders' => $pendingOtReminders,
                'active_time_entry' => null,
                'time_entries' => [],
                'entry_count' => 0,
            ];

            if (!$attendanceRecord) {
                return response()->json($status);
            }

            /*
             * Ensure totals include approved OB.
             */
            $this
                ->recalculateAttendanceWithOfficialBusiness(
                    $attendanceRecord
                );

            $attendanceRecord->refresh();

            // Use the same active TimeEntry lookup as the clock-out endpoint.
            // This keeps the dashboard, status API, and Time In/Out page in sync.
            $activeTimeEntry =
                $attendanceRecord->getActiveTimeEntry();

            $attendanceRecord->load(['timeEntries', 'breaks']);
            $isClockedIn = (bool) $activeTimeEntry;

            $status['time_entries'] = $attendanceRecord->timeEntries
                ->map(fn ($entry) => [
                    'id' => $entry->id,
                    'time_in' => $entry->time_in?->toIso8601String(),
                    'time_out' => $entry->time_out?->toIso8601String(),
                    'hours_worked' => (float) ($entry->hours_worked ?? 0),
                    'entry_type' => $entry->entry_type,
                ])
                ->values()
                ->toArray();

            $status['entry_count'] = count($status['time_entries']);
            $status['active_time_entry'] = $activeTimeEntry
                ? [
                    'id' => $activeTimeEntry->id,
                    'time_in' => $activeTimeEntry->time_in?->toIso8601String(),
                    'time_out' => $activeTimeEntry->time_out?->toIso8601String(),
                ]
                : null;

            $status['has_clocked_in'] =
                (bool) $attendanceRecord->time_in;

            $status['has_clocked_out'] =
                (bool) $attendanceRecord->time_out;

            $status['is_currently_clocked_in'] =
                $isClockedIn;

            $status['time_in'] =
                $attendanceRecord->time_in
                    ? Carbon::parse(
                        $attendanceRecord->time_in
                    )->toIso8601String()
                    : null;

            $status['time_out'] =
                $attendanceRecord->time_out
                    ? Carbon::parse(
                        $attendanceRecord->time_out
                    )->toIso8601String()
                    : null;

            $status['total_hours'] =
                $attendanceRecord->total_hours;

            $status['regular_hours'] =
                $attendanceRecord->regular_hours;

            $status['overtime_hours'] =
                $attendanceRecord->overtime_hours;

            $status['attendance_record'] =
                $attendanceRecord;

            $breaks = $attendanceRecord
                ->breaks()
                ->orderBy('break_start')
                ->get();

            $status['breaks'] = $breaks
                ->map(function ($break) {
                    return [
                        'id' => $break->id,

                        'break_start' =>
                            Carbon::parse(
                                $break->break_start
                            )->toIso8601String(),

                        'break_end' =>
                            $break->break_end
                                ? Carbon::parse(
                                    $break->break_end
                                )->toIso8601String()
                                : null,

                        'break_duration_minutes' =>
                            $break
                                ->break_duration_minutes,

                        'is_active' =>
                            !$break->break_end,
                    ];
                })
                ->values()
                ->toArray();

            $status['break_count'] =
                $breaks->count();

            $activeBreak = $breaks->first(
                fn ($break) => !$break->break_end
            );

            if ($activeBreak) {
                $status['is_on_break'] = true;

                $status['break_start'] =
                    Carbon::parse(
                        $activeBreak->break_start
                    )->toIso8601String();

                $status['active_break'] = [
                    'id' => $activeBreak->id,

                    'break_start' =>
                        Carbon::parse(
                            $activeBreak->break_start
                        )->toIso8601String(),
                ];
            }

            if ($isClockedIn) {
                $status['can_time_in'] = false;

                $status['can_time_out'] =
                    !$status['is_on_break'];

                $status['can_break_start'] =
                    !$status['is_on_break'];

                $status['can_break_end'] =
                    $status['is_on_break'];

                $status['status'] =
                    $status['is_on_break']
                        ? 'on_break'
                        : 'working';
            } else {
                $status['can_time_in'] = true;
                $status['can_time_out'] = false;
                $status['can_break_start'] = false;
                $status['can_break_end'] = false;

                $status['status'] =
                    $attendanceRecord->time_out
                        ? 'completed'
                        : 'offline';
            }

            return response()->json($status);
        } catch (\Throwable $e) {
            return response()->json([
                'error' =>
                    'An error occurred: '
                    . $e->getMessage(),
            ], 500);
        }
    }
}
