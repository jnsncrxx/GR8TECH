<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Notifications\RequestStatusChanged;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $personalRequested = $request->query('scope') === 'mine';
        if ($personalRequested && !$user->employee_id) {
            return redirect()->route('dashboard')->with('error', 'No employee record is linked to this account.');
        }
        $personalMode = $personalRequested;
        $isReviewer = in_array($user->role, ['admin', 'hr', 'manager'], true) && !$personalMode;

        // Keep displayed and filtered statuses authoritative between scheduled
        // expiry sweeps, matching the Official Business reviewer portal.
        \App\Models\OvertimeRequest::pastDeadline()->update([
            'status' => \App\Models\OvertimeRequest::EXPIRED,
            'updated_at' => now(),
        ]);

        $applyFilters = function ($query) use ($request, $user, $isReviewer, $personalMode) {
            if ($personalMode) {
                return $query->where('employee_id', $user->employee_id);
            }

            if (!$isReviewer) {
                $user->employee_id
                    ? $query->where('employee_id', $user->employee_id)
                    : $query->whereRaw('1 = 0');
            } else {
                if (($user->role ?? null) === 'manager') {
                    $query->whereHas('employee.department', fn ($dept) => $dept->where('manager_id', $user->employee_id));
                }

                if ($request->filled('employee_id')) {
                    $query->where('employee_id', $request->query('employee_id'));
                }
            }

            if ($isReviewer && $request->filled('department_id')) {
                $departmentId = $request->query('department_id');
                $query->whereHas('employee', fn ($employee) => $employee->where('department_id', $departmentId));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->query('status'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->query('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->query('date_to'));
            }

            return $query;
        };

        $query = $applyFilters(
            \App\Models\OvertimeRequest::with(['employee.department', 'approver.employee'])
        );

        if (($user->role ?? null) === 'manager') {
            $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END");
        }

        $overtimeRequests = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $summaryQuery = $applyFilters(\App\Models\OvertimeRequest::query());
        
        $summary = [
            "total" => (clone $summaryQuery)->count(),
            "approved" => (clone $summaryQuery)->where('status', 'approved')->count(),
            "pending" => (clone $summaryQuery)->where('status', 'pending')->count(),
            "rejected" => (clone $summaryQuery)->where('status', 'rejected')->count(),
            "expired" => (clone $summaryQuery)->where('status', 'expired')->count(),
            "total_hours" => (clone $summaryQuery)->where('status', 'approved')->sum('hours'),
        ];
        
        $departments = ($user->role ?? null) === 'manager'
            ? \App\Models\Department::where('manager_id', $user->employee_id)->orderBy('name')->get()
            : \App\Models\Department::orderBy('name')->get();
        $employeesQuery = \App\Models\Employee::with('department')
            ->orderBy('first_name')
            ->orderBy('last_name');
        if (($user->role ?? null) === 'manager') {
            $employeesQuery->managedBy($user->employee_id);
        }
        $employees = $employeesQuery->get();

        $employeeOvertimeDates = collect();
        if (!$isReviewer && $user->employee_id) {
            $employeeOvertimeDates = clone $summaryQuery;
            $employeeOvertimeDates = $employeeOvertimeDates->whereIn('status', ['pending', 'approved'])
                ->get(['date', 'status'])
                ->toBase()
                ->mapToGroups(function ($item) {
                    return [$item->date->format('Y-m-d') => $item->status];
                })
                ->map(function ($statuses) {
                    return $statuses->contains('approved') ? 'approved' : 'pending';
                });
        }

        return view("attendance.overtime", [
            "user" => $user,
            "summary" => $summary,
            "overtimeRequests" => $overtimeRequests,
            "departments" => $departments,
            "employees" => $employees,
            "employeeOvertimeDates" => $employeeOvertimeDates,
            "isReviewer" => $isReviewer,
            "personalMode" => $personalMode,
            "currentEmployeeId" => $user->employee_id,
        ]);
    }

    public function exportOvertime(Request $request, $format) { return back(); }
    
    public function store(Request $request) 
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'reason' => 'required|string',
            ]);
            
            $startTimeStr = date('H:i', strtotime($request->start_time));
            $endTimeStr = date('H:i', strtotime($request->end_time));
            
            $user = Auth::user();
            if (!$user->employee_id) {
                return response()->json(['error' => 'No associated employee record found.'], 400);
            }
            
            $startTime = \Carbon\Carbon::parse($request->date . ' ' . $request->start_time);
            $endTime = \Carbon\Carbon::parse($request->date . ' ' . $request->end_time);
            
            if ($endTime->lte($startTime)) {
                $endTime->addDay();
            }
            
            $existingRequest = \App\Models\OvertimeRequest::where('employee_id', $user->employee_id)
                ->whereIn('status', [\App\Models\OvertimeRequest::PENDING, \App\Models\OvertimeRequest::APPROVED])
                ->whereDate('date', $request->date)
                ->exists();
                
            if ($existingRequest) {
                return response()->json(['error' => 'You already have a pending or approved overtime request for this date. Please choose another day.'], 422);
            }

            if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($user->employee_id, $request->date)) {
                return response()->json([
                    'error' => 'Overtime cannot be filed for a date covered by a locked payroll period.',
                ], 422);
            }

            $conflicts = app(\App\Services\PayrollRequestConflictService::class);
            if ($conflicts->leaveOnDate($user->employee_id, $request->date)) {
                return response()->json(['error' => 'Overtime cannot be filed on a date covered by pending or approved leave.'], 422);
            }
            if ($conflicts->officialBusinessOnDate($user->employee_id, $request->date)) {
                return response()->json(['error' => 'Overtime cannot be filed on a date with pending or approved Official Business.'], 422);
            }
            
            $hasAttendanceRecord = \App\Models\AttendanceRecord::where('employee_id', $user->employee_id)
                ->whereDate('date', $request->date)
                ->exists();

            if (!$hasAttendanceRecord) {
                return response()->json(['error' => 'Overtime can only be requested for a date with an existing attendance record.'], 422);
            }
            
            $hours = $startTime->diffInMinutes($endTime) / 60;
            
            $overtime = \App\Models\OvertimeRequest::create([
                'employee_id' => $user->employee_id,
                'date' => $request->date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'hours' => round($hours, 2),
                'rate_multiplier' => (float) \App\Models\AttendanceSetting::getValue('overtime_rate_multiplier', 1.5),
                'reason' => $request->reason,
                'status' => \App\Models\OvertimeRequest::PENDING,
                'expires_at' => app(\App\Services\CutoffPeriodService::class)->graceDeadlineFor($request->date),
            ]);
            
            return response()->json([
                'message' => 'Overtime request submitted successfully',
                'overtime' => $overtime
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Overtime submission error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to submit overtime request: ' . $e->getMessage()], 500);
        }
    }
    
    public function updateStatus(Request $request, $id) 
    { 
        try {
            $user = Auth::user();
            $isReviewer = in_array($user->role ?? null, ['admin', 'hr', 'manager'], true);

            if (!$isReviewer) {
                return response()->json(['error' => 'You are not authorized to review overtime requests.'], 403);
            }

            $request->validate(['status' => 'required|in:approved,rejected']);
            
            $overtime = \App\Models\OvertimeRequest::findOrFail($id);

            if ($user->employee_id && $overtime->employee_id === $user->employee_id) {
                return response()->json(['error' => 'You cannot approve or reject your own overtime request.'], 403);
            }

            if (($user->role ?? null) === 'manager') {
                $overtime->loadMissing('employee.department');
                if (!$overtime->employee || !$overtime->employee->isManagedBy($user->employee_id)) {
                    return response()->json(['error' => 'You can only review overtime requests for employees in your department.'], 403);
                }
            }
            
            if ($overtime->isPastDeadline()) {
                return response()->json(['error' => 'Cannot update an expired request.'], 403);
            }
            
            if ($overtime->status !== \App\Models\OvertimeRequest::PENDING) {
                return response()->json(['error' => 'Only pending requests can be updated.'], 403);
            }

            if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate(
                $overtime->employee_id,
                $overtime->date->toDateString()
            )) {
                return response()->json([
                    'error' => 'This overtime request belongs to a locked payroll period and can no longer be reviewed or changed.',
                ], 422);
            }

            if ($request->status === 'approved') {
                $conflicts = app(\App\Services\PayrollRequestConflictService::class);
                if ($conflicts->leaveOnDate($overtime->employee_id, $overtime->date->toDateString())) {
                    return response()->json(['error' => 'Cannot approve overtime because this date is covered by leave.'], 422);
                }
                if ($conflicts->officialBusinessOnDate($overtime->employee_id, $overtime->date->toDateString())) {
                    return response()->json(['error' => 'Cannot approve overtime because this date has Official Business.'], 422);
                }
            }
            
            $overtime->update([
                'status' => $request->status,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            ]);

            $this->notifyRequester($overtime);

            return response()->json([
                'message' => 'Overtime status updated successfully.',
                'overtime' => $overtime
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating overtime status: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating status: ' . $e->getMessage()], 500);
        }
    }
    
    protected function notifyRequester(\App\Models\OvertimeRequest $overtime): void
    {
        $account = $overtime->employee?->account;
        if (!$account) {
            return;
        }

        $dateLabel = Carbon::parse($overtime->date)->format('M d, Y');

        $account->notify(new RequestStatusChanged(
            RequestStatusChanged::TYPE_OVERTIME,
            $overtime->id,
            $overtime->status,
            $dateLabel,
            $overtime->rejection_reason,
        ));
    }

    /**
     * Cancel a pending overtime request, or reverse an already-approved one.
     *
     * Unlike Leave/OB, approving overtime never touches AttendanceRecord —
     * updateStatus() only writes to the overtime request's own columns — so
     * reversal here is just a status flip, with no attendance recalculation
     * to cascade.
     */
    public function cancel(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $overtime = \App\Models\OvertimeRequest::findOrFail($id);

            $isReviewer = in_array($user->role ?? null, ['admin', 'hr', 'manager'], true);
            $ownsRequest = $user->employee_id && $overtime->employee_id === $user->employee_id;

            if (!$ownsRequest && !$isReviewer) {
                return response()->json(['error' => 'You are not authorized to cancel this request.'], 403);
            }

            if (!$ownsRequest && ($user->role ?? null) === 'manager') {
                $overtime->loadMissing('employee.department');
                if (!$overtime->employee || !$overtime->employee->isManagedBy($user->employee_id)) {
                    return response()->json(['error' => 'You can only cancel requests for employees in your department.'], 403);
                }
            }

            if (!in_array($overtime->status, [\App\Models\OvertimeRequest::PENDING, \App\Models\OvertimeRequest::APPROVED], true)) {
                return response()->json(['error' => 'Only pending or approved requests can be cancelled.'], 422);
            }

            if ($overtime->status === \App\Models\OvertimeRequest::APPROVED && !$isReviewer) {
                return response()->json(['error' => 'Only admin, HR, or manager can cancel an approved overtime request.'], 403);
            }

            if ($overtime->status === \App\Models\OvertimeRequest::APPROVED) {
                $conflicts = app(\App\Services\PayrollRequestConflictService::class);
                if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($overtime->employee_id, $overtime->date->toDateString())) {
                    return response()->json([
                        'error' => 'Cannot cancel — payroll has already been generated for this date. The payroll period is locked and this request can no longer be changed.',
                    ], 422);
                }
            }

            $request->validate([
                'cancellation_reason' => ['nullable', 'string', 'max:500'],
            ]);

            $overtime->update([
                'status' => \App\Models\OvertimeRequest::CANCELED,
                'approved_by' => $isReviewer ? Auth::id() : $overtime->approved_by,
                'rejection_reason' => $request->input('cancellation_reason'),
            ]);

            $this->notifyRequester($overtime);

            return response()->json([
                'message' => 'Overtime request cancelled successfully.',
                'overtime' => $overtime,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error cancelling overtime: ' . $e->getMessage());
            return response()->json(['error' => 'Error cancelling overtime request: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Edit an already-approved overtime request. Reserved for admin/hr/manager
     * (manager scoped to their department), and only while no payroll has
     * been generated for the original or new date. Unlike Leave/OB, overtime
     * approval never touches AttendanceRecord, so this is a straightforward
     * field update plus the same conflict re-checks approval itself runs —
     * no reversal/reapply cascade needed. Writes a before/after audit row.
     */
    public function updateApproved(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $overtime = \App\Models\OvertimeRequest::findOrFail($id);

            if ($overtime->status !== \App\Models\OvertimeRequest::APPROVED) {
                return response()->json(['error' => 'Only approved overtime requests can be edited here.'], 422);
            }

            $isReviewer = in_array($user->role ?? null, ['admin', 'hr', 'manager'], true);
            if (!$isReviewer) {
                return response()->json(['error' => 'Only admin, HR, or manager can edit an approved overtime request.'], 403);
            }

            if (($user->role ?? null) === 'manager' && $overtime->employee_id !== $user->employee_id) {
                $overtime->loadMissing('employee.department');
                if (!$overtime->employee || !$overtime->employee->isManagedBy($user->employee_id)) {
                    return response()->json(['error' => 'You can only edit requests for employees in your department.'], 403);
                }
            }

            $conflicts = app(\App\Services\PayrollRequestConflictService::class);
            $originalDate = $overtime->date->toDateString();

            if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($overtime->employee_id, $originalDate)) {
                return response()->json([
                    'error' => 'Cannot edit — payroll has already been generated for this date. The payroll period is locked and this request can no longer be changed.',
                ], 422);
            }

            $validated = $request->validate([
                'date' => ['required', 'date'],
                'start_time' => ['required', 'date_format:H:i'],
                'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
                'hours' => ['required', 'numeric', 'min:0.5'],
                'rate_multiplier' => ['required', 'numeric', 'min:1'],
                'reason' => ['required', 'string', 'max:1000'],
                'correction_reason' => ['required', 'string', 'max:500'],
            ]);

            $newDate = \Carbon\Carbon::parse($validated['date'])->toDateString();

            if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($overtime->employee_id, $newDate)) {
                return response()->json([
                    'error' => 'Cannot edit — payroll has already been generated for the new date. That payroll period is locked and can no longer be modified.',
                ], 422);
            }

            if ($newDate !== $originalDate) {
                if ($conflicts->leaveOnDate($overtime->employee_id, $newDate)) {
                    return response()->json(['error' => 'Cannot move this overtime — the new date is covered by leave.'], 422);
                }
                if ($conflicts->officialBusinessOnDate($overtime->employee_id, $newDate)) {
                    return response()->json(['error' => 'Cannot move this overtime — the new date already has Official Business.'], 422);
                }
            }

            $auditFields = ['date', 'start_time', 'end_time', 'hours', 'rate_multiplier', 'reason'];
            $originalValues = $overtime->only($auditFields);

            \Illuminate\Support\Facades\DB::transaction(function () use ($overtime, $validated, $newDate, $originalValues, $auditFields, $user) {
                $overtime->fill([
                    'date' => $newDate,
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'hours' => $validated['hours'],
                    'rate_multiplier' => $validated['rate_multiplier'],
                    'reason' => $validated['reason'],
                ]);
                $overtime->save();

                \Illuminate\Support\Facades\DB::table('request_corrections')->insert([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'request_type' => 'overtime',
                    'request_id' => $overtime->id,
                    'corrected_by' => $user->id,
                    'reason' => $validated['correction_reason'],
                    'original_values' => json_encode($originalValues),
                    'corrected_values' => json_encode($overtime->fresh()->only($auditFields)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            $this->notifyRequester($overtime);

            return response()->json([
                'message' => 'Approved overtime request corrected successfully.',
                'overtime' => $overtime,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error editing approved overtime: ' . $e->getMessage());
            return response()->json(['error' => 'Error editing overtime request: ' . $e->getMessage()], 500);
        }
    }

    public function getStatistics(Request $request) { return response()->json([]); }
    /**
     * Auto / Quick Submit Overtime Request from Clock-Out or Reminder Prompt.
     */
    public function quickSubmit(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->employee_id) {
                return response()->json(['error' => 'No associated employee record found.'], 400);
            }

            $validated = $request->validate([
                'date' => 'required|date',
                'extra_hours' => 'required|numeric|min:0.01',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'reason' => 'nullable|string|max:1000',
                'reminder_id' => 'required|uuid',
            ]);

            $dateStr = Carbon::parse($validated['date'])->toDateString();
            $reminder = \App\Models\OvertimeReminder::whereKey($validated['reminder_id'])
                ->where('employee_id', $user->employee_id)
                ->whereDate('date', $dateStr)
                ->where('status', \App\Models\OvertimeReminder::PENDING)
                ->first();

            if (!$reminder) {
                return response()->json([
                    'error' => 'This overtime reminder is no longer available.',
                ], 422);
            }

            // Check if OT request already exists for this workday
            $existingRequest = \App\Models\OvertimeRequest::where('employee_id', $user->employee_id)
                ->whereDate('date', $dateStr)
                ->whereIn('status', [\App\Models\OvertimeRequest::PENDING, \App\Models\OvertimeRequest::APPROVED])
                ->first();

            if ($existingRequest) {
                $reminder->update(['status' => \App\Models\OvertimeReminder::SUBMITTED]);

                return response()->json([
                    'success' => true,
                    'message' => 'An overtime request is already pending or approved for this date.',
                    'overtime' => $existingRequest,
                ]);
            }

            if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($user->employee_id, $dateStr)) {
                return response()->json([
                    'error' => 'Overtime cannot be filed for a date covered by a locked payroll period.',
                ], 422);
            }

            $conflicts = app(\App\Services\PayrollRequestConflictService::class);
            if ($conflicts->leaveOnDate($user->employee_id, $dateStr)) {
                return response()->json(['error' => 'Overtime cannot be filed on a date covered by pending or approved leave.'], 422);
            }
            if ($conflicts->officialBusinessOnDate($user->employee_id, $dateStr)) {
                return response()->json(['error' => 'Overtime cannot be filed on a date with pending or approved Official Business.'], 422);
            }

            $attendanceRecord = \App\Models\AttendanceRecord::where('employee_id', $user->employee_id)
                ->whereDate('date', $dateStr)
                ->first();

            if (!$attendanceRecord) {
                return response()->json(['error' => 'Overtime can only be requested for a date with an existing attendance record.'], 422);
            }

            $startTime = Carbon::parse($dateStr.' '.$validated['start_time']);
            $endTime = Carbon::parse($dateStr.' '.$validated['end_time']);

            if ($endTime->lte($startTime)) {
                $endTime->addDay();
            }

            $hours = round($startTime->diffInMinutes($endTime) / 60, 2);
            $detectedHours = (float) $reminder->extra_hours;
            if ($hours > $detectedHours + 0.01 || abs($hours - (float) $validated['extra_hours']) > 0.02) {
                return response()->json([
                    'error' => 'Requested overtime must match the selected time range and cannot exceed the detected extra hours.',
                ], 422);
            }

            $reason = $validated['reason'] ?? 'Auto-detected rendered overtime after clock out';

            $overtime = \Illuminate\Support\Facades\DB::transaction(function () use (
                $user,
                $dateStr,
                $startTime,
                $endTime,
                $hours,
                $reason,
                $reminder
            ) {
                $overtime = \App\Models\OvertimeRequest::create([
                    'employee_id' => $user->employee_id,
                    'date' => $dateStr,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'hours' => round($hours, 2),
                    'rate_multiplier' => (float) \App\Models\AttendanceSetting::getValue('overtime_rate_multiplier', 1.5),
                    'reason' => $reason,
                    'status' => \App\Models\OvertimeRequest::PENDING,
                    'expires_at' => app(\App\Services\CutoffPeriodService::class)->graceDeadlineFor($dateStr),
                ]);

                $reminder->update(['status' => \App\Models\OvertimeReminder::SUBMITTED]);

                return $overtime;
            });

            return response()->json([
                'success' => true,
                'message' => 'Overtime request submitted successfully!',
                'overtime' => $overtime,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Quick Overtime submission error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to submit overtime request: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Dismiss pending overtime reminder.
     */
    public function dismissReminder(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->employee_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $reminder = \App\Models\OvertimeReminder::where('id', $id)
                ->where('employee_id', $user->employee_id)
                ->first();

            if ($reminder) {
                $reminder->update(['status' => \App\Models\OvertimeReminder::DISMISSED]);
            }

            return response()->json(['success' => true, 'message' => 'Reminder dismissed']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
