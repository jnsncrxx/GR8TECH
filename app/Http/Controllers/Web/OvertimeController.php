<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = \App\Models\OvertimeRequest::with(['employee', 'employee.department']);
        
        if ($user->role === 'employee') {
            if ($user->employee_id) {
                $query->where('employee_id', $user->employee_id);
            } else {
                $query->where('id', -1); // No records if no employee ID
            }
        }
        
        $overtimeRequests = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Base queries for summary
        $summaryQuery = \App\Models\OvertimeRequest::query();
        if ($user->role === 'employee') {
            $summaryQuery->where('employee_id', $user->employee_id);
        }
        
        $summary = [
            "total" => (clone $summaryQuery)->count(),
            "approved" => (clone $summaryQuery)->where('status', 'approved')->count(),
            "pending" => (clone $summaryQuery)->where('status', 'pending')->count(),
            "rejected" => (clone $summaryQuery)->where('status', 'rejected')->count(),
            "total_hours" => (clone $summaryQuery)->where('status', 'approved')->sum('hours')
        ];
        
        $departments = \App\Models\Department::all();
        $employees = \App\Models\Employee::all();

        return view("attendance.overtime", [
            "user" => $user,
            "summary" => $summary,
            "overtimeRequests" => $overtimeRequests,
            "departments" => $departments,
            "employees" => $employees
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
            
            if ($request->start_time < '17:00') {
                return response()->json(['error' => 'Overtime must start at or after 5:00 PM.'], 422);
            }
            
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
            
            $hours = $startTime->diffInMinutes($endTime) / 60;
            
            $overtime = \App\Models\OvertimeRequest::create([
                'employee_id' => $user->employee_id,
                'date' => $request->date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'hours' => round($hours, 2),
                'rate_multiplier' => 1.25,
                'reason' => $request->reason,
                'status' => \App\Models\OvertimeRequest::PENDING,
                'expires_at' => $endTime,
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
            $request->validate(['status' => 'required|in:approved,rejected']);
            
            $overtime = \App\Models\OvertimeRequest::findOrFail($id);
            
            if ($overtime->isPastDeadline()) {
                return response()->json(['error' => 'Cannot update an expired request.'], 403);
            }
            
            if ($overtime->status !== \App\Models\OvertimeRequest::PENDING) {
                return response()->json(['error' => 'Only pending requests can be updated.'], 403);
            }
            
            $overtime->update([
                'status' => $request->status,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            ]);
            
            return response()->json([
                'message' => 'Overtime status updated successfully.',
                'overtime' => $overtime
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating overtime status: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating status: ' . $e->getMessage()], 500);
        }
    }
    
    public function cancel(Request $request, $id) { return back(); }
    public function getStatistics(Request $request) { return response()->json([]); }
}
