<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\EmployeeBreak;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeInOutController extends Controller
{
    public function index(Request $request)
    {
        return view('attendance.time-in-out', ['user' => Auth::user()]);
    }

    public function timeIn(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;

            if (!$employee) {
                return response()->json(['error' => 'Employee record not found'], 404);
            }

            $today = Carbon::today();
            
            // Get or create today's attendance record
            $attendanceRecord = AttendanceRecord::firstOrCreate(
                ['employee_id' => $employee->id, 'date' => $today],
                ['status' => 'present']
            );

            // Check if already has an active session
            if ($attendanceRecord->hasActiveTimeEntry()) {
                return response()->json(['error' => 'You are already clocked in'], 400);
            }

            // Record the time in via TimeEntry
            $now = Carbon::now();
            
            TimeEntry::create([
                'attendance_record_id' => $attendanceRecord->id,
                'time_in' => $now,
                'entry_type' => 'regular'
            ]);

            // Update main record's first time_in if not set
            if (!$attendanceRecord->time_in) {
                $attendanceRecord->time_in = $now;
                $attendanceRecord->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully clocked in at ' . $now->format('g:i A'),
                'time_in' => $now->toIso8601String(),
                'status' => 'clocked_in'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function timeOut(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;

            if (!$employee) {
                return response()->json(['error' => 'Employee record not found'], 404);
            }

            $today = Carbon::today();

            // Get today's attendance record
            $attendanceRecord = AttendanceRecord::where('employee_id', $employee->id)
                ->where('date', $today->toDateString())
                ->first();

            if (!$attendanceRecord) {
                return response()->json(['error' => 'No clock in record found for today'], 400);
            }

            // Get active time entry
            $activeEntry = $attendanceRecord->getActiveTimeEntry();

            if (!$activeEntry) {
                return response()->json(['error' => 'No active clock-in session found'], 400);
            }

            // Record the time out
            $now = Carbon::now();
            $activeEntry->time_out = $now;
            $activeEntry->save(); // This triggers calculation of hours_worked in TimeEntry model

            // Update main attendance record
            $attendanceRecord->time_out = $now; // Store latest timeout
            $attendanceRecord->total_hours = $attendanceRecord->calculateTotalHours();
            $attendanceRecord->status = 'completed';
            $attendanceRecord->save();

            return response()->json([
                'success' => true,
                'message' => 'Successfully clocked out at ' . $now->format('g:i A'),
                'time_out' => $now->toIso8601String(),
                'total_hours' => $attendanceRecord->total_hours,
                'status' => 'clocked_out'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function breakStart(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;

            if (!$employee) {
                return response()->json(['error' => 'Employee record not found'], 404);
            }

            $today = Carbon::today();

            // Get today's attendance record
            $attendanceRecord = AttendanceRecord::where('employee_id', $employee->id)
                ->where('date', $today->toDateString())
                ->first();

            if (!$attendanceRecord || !$attendanceRecord->hasActiveTimeEntry()) {
                return response()->json(['error' => 'You must be clocked in to start a break'], 400);
            }

            // Check if already on break
            $activeBreak = EmployeeBreak::where('attendance_record_id', $attendanceRecord->id)
                ->whereNull('break_end')
                ->first();

            if ($activeBreak) {
                return response()->json(['error' => 'You are already on break'], 400);
            }

            // Check break count (max 2 per day)
            $breakCount = EmployeeBreak::where('attendance_record_id', $attendanceRecord->id)
                ->whereNotNull('break_end')
                ->count();

            if ($breakCount >= 2) {
                return response()->json(['error' => 'Maximum of 2 breaks per day allowed'], 400);
            }

            // Record break start
            $now = Carbon::now();
            $break = EmployeeBreak::create([
                'attendance_record_id' => $attendanceRecord->id,
                'break_start' => $now,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Break started at ' . $now->format('g:i A'),
                'break_start' => $now->toIso8601String(),
                'status' => 'on_break'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function breakEnd(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;

            if (!$employee) {
                return response()->json(['error' => 'Employee record not found'], 404);
            }

            $today = Carbon::today();

            // Get today's attendance record
            $attendanceRecord = AttendanceRecord::where('employee_id', $employee->id)
                ->where('date', $today->toDateString())
                ->first();

            if (!$attendanceRecord || !$attendanceRecord->hasActiveTimeEntry()) {
                return response()->json(['error' => 'You must be clocked in to end a break'], 400);
            }

            // Get active break
            $activeBreak = EmployeeBreak::where('attendance_record_id', $attendanceRecord->id)
                ->whereNull('break_end')
                ->first();

            if (!$activeBreak) {
                return response()->json(['error' => 'You are not on break'], 400);
            }

            // Record break end
            $now = Carbon::now();
            $activeBreak->break_end = $now;
            $activeBreak->break_duration_minutes = $activeBreak->break_start->diffInMinutes($now);
            $activeBreak->save();

            // Check if over break limit (more than 2 hours)
            $isOverBreak = $activeBreak->break_duration_minutes > 120;
            $overBreakMinutes = max(0, $activeBreak->break_duration_minutes - 120);

            return response()->json([
                'success' => true,
                'message' => 'Break ended at ' . $now->format('g:i A'),
                'break_end' => $now->toIso8601String(),
                'break_duration_minutes' => $activeBreak->break_duration_minutes,
                'is_over_break' => $isOverBreak,
                'over_break_minutes' => $overBreakMinutes,
                'status' => 'working'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function getStatus(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;

            if (!$employee) {
                return response()->json(['error' => 'Employee record not found'], 404);
            }

            $today = Carbon::today();

            // Get today's attendance record
            $attendanceRecord = AttendanceRecord::where('employee_id', $employee->id)
                ->where('date', $today->toDateString())
                ->first();

            $status = [
                'employee_id' => $employee->id,
                'has_clocked_in' => false,
                'has_clocked_out' => false,
                'is_on_break' => false,
                'time_in' => null,
                'time_out' => null,
                'break_start' => null,
                'break_end' => null,
                'active_break' => null,
                'breaks' => [],
                'break_count' => 0,
                'total_hours' => 0,
                'can_time_in' => true,
                'can_time_out' => false,
                'can_break_start' => false,
                'can_break_end' => false,
                'status' => 'offline',
            ];

            if ($attendanceRecord) {
                $status['has_clocked_in'] = (bool) $attendanceRecord->time_in;
                $status['has_clocked_out'] = (bool) $attendanceRecord->time_out;
                $status['is_currently_clocked_in'] = $attendanceRecord->hasActiveTimeEntry();
                $status['time_in'] = $attendanceRecord->time_in?->toIso8601String();
                $status['time_out'] = $attendanceRecord->time_out?->toIso8601String();
                $status['total_hours'] = $attendanceRecord->total_hours;
                $status['attendance_record'] = $attendanceRecord;
                
                $status['can_time_in'] = !$attendanceRecord->hasActiveTimeEntry();
                $status['can_time_out'] = $attendanceRecord->hasActiveTimeEntry();

                // Check breaks
                $breaks = $attendanceRecord->breaks()->get();
                $status['breaks'] = $breaks->map(function ($break) {
                    return [
                        'id' => $break->id,
                        'break_start' => $break->break_start->toIso8601String(),
                        'break_end' => $break->break_end?->toIso8601String(),
                        'break_duration_minutes' => $break->break_duration_minutes,
                        'is_active' => !$break->break_end,
                    ];
                })->toArray();

                $status['break_count'] = $breaks->count();

                // Check for active break
                $activeBreak = $breaks->firstWhere('break_end', null);
                if ($activeBreak) {
                    $status['is_on_break'] = true;
                    $status['active_break'] = [
                        'id' => $activeBreak->id,
                        'break_start' => $activeBreak->break_start->toIso8601String(),
                    ];
                    $status['can_break_end'] = true;
                }

                // Determine if can clock in/out
                if ($attendanceRecord->time_in && !$attendanceRecord->time_out) {
                    // Clocked in but not out
                    $status['can_time_in'] = false;
                    $status['can_time_out'] = true;
                    $status['can_break_start'] = !$status['is_on_break'];
                    $status['status'] = $status['is_on_break'] ? 'on_break' : 'working';
                } elseif ($attendanceRecord->time_out) {
                    // Already clocked out
                    $status['can_time_in'] = true;
                    $status['can_time_out'] = false;
                    $status['status'] = 'completed';
                }
            }

            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
