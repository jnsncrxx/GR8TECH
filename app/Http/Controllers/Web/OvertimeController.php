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
        $isReviewer = in_array($user->role, ['admin', 'hr', 'manager'], true);

        // Keep displayed and filtered statuses authoritative between scheduled
        // expiry sweeps, matching the Official Business reviewer portal.
        \App\Models\OvertimeRequest::pastDeadline()->update([
            'status' => \App\Models\OvertimeRequest::EXPIRED,
            'updated_at' => now(),
        ]);

        $applyFilters = function ($query) use ($request, $user, $isReviewer) {
            if (!$isReviewer) {
                $user->employee_id
                    ? $query->where('employee_id', $user->employee_id)
                    : $query->whereRaw('1 = 0');
            } elseif ($request->filled('employee_id')) {
                $query->where('employee_id', $request->query('employee_id'));
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
        
        $departments = \App\Models\Department::orderBy('name')->get();
        $employees = \App\Models\Employee::with('department')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $employeeOvertimeDates = collect();
        if ($user->role === 'employee' && $user->employee_id) {
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
            
            if ($overtime->isPastDeadline()) {
                return response()->json(['error' => 'Cannot update an expired request.'], 403);
            }
            
            if ($overtime->status !== \App\Models\OvertimeRequest::PENDING) {
                return response()->json(['error' => 'Only pending requests can be updated.'], 403);
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

            if (!in_array($overtime->status, [\App\Models\OvertimeRequest::PENDING, \App\Models\OvertimeRequest::APPROVED], true)) {
                return response()->json(['error' => 'Only pending or approved requests can be cancelled.'], 422);
            }

            if ($overtime->status === \App\Models\OvertimeRequest::APPROVED && !$isReviewer) {
                return response()->json(['error' => 'Only admin, HR, or manager can cancel an approved overtime request.'], 403);
            }

            $request->validate([
                'cancellation_reason' => ['nullable', 'string', 'max:500'],
            ]);

            $overtime->update([
                'status' => \App\Models\OvertimeRequest::CANCELED,
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
    public function getStatistics(Request $request) { return response()->json([]); }
}