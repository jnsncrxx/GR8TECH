<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    protected array $leaveTypes = [
        'vacation',
        'sick',
        'personal',
        'emergency',
        'maternity',
        'paternity',
        'bereavement',
        'study',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $this->applyFilters(LeaveRequest::with(['employee', 'approver']), $request, $user);
        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $summaryQuery = $this->applyFilters(LeaveRequest::query(), $request, $user);
        $summary = [
            'total' => $leaveRequests->total(),
            'pending' => (clone $summaryQuery)->where('status', 'pending')->count(),
            'approved' => (clone $summaryQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $summaryQuery)->where('status', 'rejected')->count(),
        ];

        $employees = Employee::with('department')->get();

        $hasEmployeesWithoutBalances = Employee::whereDoesntHave('leaveBalances', function ($query) {
            $query->where('year', Carbon::now()->year);
        })->exists();

        return view('attendance.leave-management', [
            'user' => $user,
            'summary' => $summary,
            'leaveRequests' => $leaveRequests,
            'employees' => $employees,
            'statusList' => ['pending', 'approved', 'rejected', 'cancelled'],
            'hasEmployeesWithoutBalances' => $hasEmployeesWithoutBalances,
        ]);
    }

    public function exportLeave($format)
    {
        $allowed = ['pdf', 'csv', 'xls'];
        if (!in_array($format, $allowed, true)) {
            return back()->with('error', 'Unsupported export format.');
        }

        $leaveRequests = LeaveRequest::with(['employee'])->orderBy('created_at', 'desc')->get();
        $fileName = 'leave-requests-' . Carbon::now()->format('YmdHis') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($leaveRequests) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Employee', 'Leave Type', 'Start Date', 'End Date', 'Days Requested', 'Status', 'Reason', 'Approved By', 'Approved At']);

            foreach ($leaveRequests as $request) {
                fputcsv($handle, [
                    $request->employee->full_name ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $request->leave_type)),
                    $request->start_date,
                    $request->end_date,
                    $request->days_requested,
                    ucfirst($request->status),
                    $request->reason,
                    $request->approvedBy?->email ?? '',
                    $request->approved_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $employees = [];
        $leaveBalance = null;
        $availableDays = [];

        if (in_array($user->role, ['admin', 'hr'], true)) {
            $employees = Employee::with('department')->get();
        }

        if ($employee) {
            $leaveBalance = LeaveBalance::where('employee_id', $employee->id)
                ->where('year', Carbon::now()->year)
                ->first();

            if ($leaveBalance) {
                foreach ($this->leaveTypes as $type) {
                    $availableDays[$type] = max(0, $leaveBalance->getRemainingDays($type));
                }
            }
        }

        return view('attendance.leave-request-create', [
            'user' => $user,
            'employee' => $employee,
            'employees' => $employees,
            'leaveBalance' => $leaveBalance,
            'availableDays' => $availableDays,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        $rules = [
            'leave_type' => ['required', 'in:' . implode(',', $this->leaveTypes)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:500'],
            'employee_id' => ['required', 'exists:employees,id'],
        ];

        if (!in_array($role, ['admin', 'hr'], true)) {
            $employee = $user->employee;
            if (!$employee) {
                return back()->with('error', 'Employee record not found.');
            }
            $request->merge(['employee_id' => $employee->id]);
        }

        $data = $request->validate($rules);

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $daysRequested = $startDate->diffInDays($endDate) + 1;

        $employee = Employee::find($data['employee_id']);
        if (!$employee) {
            return back()->with('error', 'Employee not found.');
        }

        $leaveBalance = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', Carbon::now()->year)
            ->first();

        if (!$leaveBalance && $role === 'employee') {
            return back()->with('error', 'Leave balance not configured. Please contact HR.');
        }

        if ($leaveBalance && !$leaveBalance->hasEnoughBalance($data['leave_type'], $daysRequested)) {
            return back()->with('error', 'Insufficient leave balance for the selected leave type and duration.');
        }

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'days_requested' => $daysRequested,
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('attendance.leave-management')
            ->with('success', 'Leave request submitted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'hr', 'manager'], true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $leaveRequest = LeaveRequest::find($id);
        if (!$leaveRequest) {
            return response()->json(['error' => 'Leave request not found'], 404);
        }

        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'rejection_reason' => ['nullable', 'required_if:status,rejected', 'string', 'max:500'],
        ]);

        $leaveRequest->status = $data['status'];
        $leaveRequest->approved_by = $user->id;
        $leaveRequest->approved_at = Carbon::now();

        if ($data['status'] === 'rejected') {
            $leaveRequest->rejection_reason = $data['rejection_reason'] ?? null;
        }

        $leaveRequest->save();

        return response()->json(['success' => true, 'message' => 'Leave request status updated successfully.']);
    }

    public function cancel($id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::find($id);

        if (!$leaveRequest) {
            return response()->json(['error' => 'Leave request not found'], 404);
        }

        if ($user->role === 'employee' && $leaveRequest->employee_id !== $user->employee?->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $leaveRequest->status = 'cancelled';
        $leaveRequest->save();

        return response()->json(['success' => true, 'message' => 'Leave request cancelled successfully.']);
    }

    public function getLeaveBalance(Request $request)
    {
        $user = Auth::user();
        $employeeId = $request->query('employee_id');
        $year = $request->query('year', Carbon::now()->year);

        if (!$employeeId && $user->role === 'employee') {
            $employeeId = $user->employee?->id;
        }

        if (!$employeeId) {
            return response()->json(['error' => 'Employee ID is required'], 400);
        }

        if ($user->role === 'employee' && $user->employee?->id !== $employeeId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $leaveBalance = LeaveBalance::where('employee_id', $employeeId)
            ->where('year', $year)
            ->first();

        $availableDays = [];
        if ($leaveBalance) {
            foreach ($this->leaveTypes as $type) {
                $availableDays[$type] = max(0, $leaveBalance->getRemainingDays($type));
            }
        } else {
            foreach ($this->leaveTypes as $type) {
                $availableDays[$type] = 0;
            }
        }

        return response()->json(['leave_balance' => $leaveBalance, 'available_days' => $availableDays]);
    }

    public function storeBalance(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'hr'], true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate(array_merge([
            'employee_id' => ['required', 'string'],
            'year' => ['required', 'integer'],
        ], array_combine(array_map(fn($type) => "{$type}_days_total", $this->leaveTypes), array_fill(0, count($this->leaveTypes), ['required', 'integer', 'min:0']))));

        $employeeIds = [];
        if ($data['employee_id'] === 'all') {
            $employeeIds = Employee::pluck('id')->toArray();
        } else {
            $employeeIds = [$data['employee_id']];
        }

        foreach ($employeeIds as $employeeId) {
            $balance = LeaveBalance::firstOrNew([
                'employee_id' => $employeeId,
                'year' => $data['year'],
            ]);

            foreach ($this->leaveTypes as $type) {
                $balance->{"{$type}_days_total"} = $data["{$type}_days_total"] ?? 0;
                $balance->{"{$type}_days_used"} = $balance->{"{$type}_days_used"} ?? 0;
            }

            $balance->save();
        }

        return response()->json(['success' => true, 'message' => 'Leave balance saved successfully.']);
    }

    public function updateBalance(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'hr'], true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $balance = LeaveBalance::find($id);
        if (!$balance) {
            return response()->json(['error' => 'Leave balance record not found'], 404);
        }

        $data = $request->validate(array_combine(array_map(fn($type) => "{$type}_days_total", $this->leaveTypes), array_fill(0, count($this->leaveTypes), ['required', 'integer', 'min:0'])));

        foreach ($this->leaveTypes as $type) {
            $balance->{"{$type}_days_total"} = $data["{$type}_days_total"];
        }

        $balance->save();

        return response()->json(['success' => true, 'message' => 'Leave balance updated successfully.']);
    }

    public function getStatistics(Request $request)
    {
        $user = Auth::user();
        $query = LeaveRequest::query();

        if ($user->role === 'employee') {
            $query->where('employee_id', $user->employee?->id);
        }

        return response()->json([
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
        ]);
    }

    private function applyFilters($query, Request $request, $user)
    {
        if ($user->role === 'employee' && $user->employee) {
            $query->where('employee_id', $user->employee->id);
        } elseif ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        return $query;
    }
}
