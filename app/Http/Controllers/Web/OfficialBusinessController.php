<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\OfficialBusinessRequest;
use App\Services\CutoffPeriodService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficialBusinessController extends Controller
{
    public function __construct(private CutoffPeriodService $cutoffPeriods)
    {
    }

    /**
     * Which users can approve/reject OB requests.
     * Matches the role:admin,hr,manager middleware used elsewhere in web.php.
     */
    private function isReviewer(): bool
    {
        $user = Auth::user();
        return $user && in_array($user->role ?? null, ['admin', 'hr', 'manager']);
    }

    /**
     * Resolve the employee_id tied to the logged-in account.
     * NOTE: adjust if the relation from User -> Employee is named differently.
     */
    private function currentEmployeeId()
    {
        $user = Auth::user();
        return $user->employee->id ?? $user->employee_id ?? null;
    }

    /**
     * Flip a pending request to 'expired' if it's past its grace deadline but
     * hasn't been swept yet by the Phase 3 scheduled command. Called lazily on
     * read/update paths so the UI never shows a stale "pending" for a request
     * that's actually no longer actionable, even between cron runs.
     */
    private function expireIfPastDeadline(OfficialBusinessRequest $obRequest): OfficialBusinessRequest
    {
        if ($obRequest->isPastDeadline()) {
            $obRequest->update(['status' => OfficialBusinessRequest::EXPIRED]);
        }

        return $obRequest;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isReviewer = $this->isReviewer();

        $query = OfficialBusinessRequest::with(['employee.department', 'reviewer.employee']);

        if (!$isReviewer) {
            $query->where('employee_id', $this->currentEmployeeId());
        } else {
            if ($request->filled('status')) {
                $query->where('status', $request->query('status'));
            }
            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->query('employee_id'));
            }
            if ($request->filled('department_id')) {
                $departmentId = $request->query('department_id');
                $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }
        }

        $obRequests = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Lazy-expire anything past its grace deadline that the sweep hasn't
        // caught yet, so what the reviewer/employee sees is always accurate.
        $obRequests->getCollection()->each(fn ($obRequest) => $this->expireIfPastDeadline($obRequest));

        $summaryBase = $isReviewer
            ? OfficialBusinessRequest::query()
            : OfficialBusinessRequest::where('employee_id', $this->currentEmployeeId());

        $summary = [
            'total' => (clone $summaryBase)->count(),
            'pending' => (clone $summaryBase)->pending()->count(),
            'approved' => (clone $summaryBase)->approved()->count(),
            'rejected' => (clone $summaryBase)->rejected()->count(),
            'expired' => (clone $summaryBase)->expired()->count(),
        ];

        $departments = Department::orderBy('name')->get();
        $employees = Employee::orderBy('first_name')->get();

        return view('attendance.official-business', [
            'user' => $user,
            'activeRoute' => 'attendance.official-business',
            'pageTitle' => 'Official Business',
            'isReviewer' => $isReviewer,
            'obRequests' => $obRequests,
            'summary' => $summary,
            'departments' => $departments,
            'employees' => $employees,
        ]);
    }

    /**
     * Employee submits a new OB request. Does NOT touch attendance_records yet.
     *
     * OB is a manual time-in/time-out replacement, not a leave — Time In and
     * Time Out are always required (no full-day/partial-day toggle). Both past
     * (retroactive) and future (advance) dates are allowed, subject to the
     * date's cutoff period still being open for action.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'reason' => 'required|string|max:500',
            'ob_start_time' => 'required|date_format:H:i',
            'ob_end_time' => 'required|date_format:H:i|after:ob_start_time',
        ]);

        $employeeId = $this->currentEmployeeId();

        if (!$employeeId) {
            return redirect()->back()->withInput()
                ->with('error', 'No employee profile is linked to this account.');
        }

        // Cutoff check: the date's period must still be open (its grace
        // deadline — cutoff end + grace hours — hasn't passed). This applies
        // uniformly to retroactive and advance filing.
        if (!$this->cutoffPeriods->isOpenForAction($validated['date'])) {
            return redirect()->back()->withInput()
                ->with('error', 'This date is not included in the current payroll cutoff period.');
        }

        $duplicateRequest = OfficialBusinessRequest::where('employee_id', $employeeId)
            ->where('date', $validated['date'])
            ->whereIn('status', [OfficialBusinessRequest::PENDING, OfficialBusinessRequest::APPROVED])
            ->exists();

        if ($duplicateRequest) {
            return redirect()->back()->withInput()
                ->with('error', 'You already have a pending or approved OB request for this date.');
        }

        $existingAttendance = AttendanceRecord::where('employee_id', $employeeId)
            ->where('date', $validated['date'])
            ->exists();

        if ($existingAttendance) {
            return redirect()->back()->withInput()
                ->with('error', 'An attendance record already exists for this date.');
        }

        $period = $this->cutoffPeriods->periodFor($validated['date']);

        OfficialBusinessRequest::create([
            'employee_id' => $employeeId,
            'date' => $validated['date'],
            'reason' => $validated['reason'],
            'status' => OfficialBusinessRequest::PENDING,
            'is_full_day' => false,
            'ob_start_time' => $validated['ob_start_time'],
            'ob_end_time' => $validated['ob_end_time'],
            'cutoff_period_key' => $period['key'],
            'expires_at' => $this->cutoffPeriods->graceDeadlineFor($validated['date']),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('attendance.official-business')
            ->with('success', 'Official Business request submitted. Waiting for approval.');
    }

    /**
     * Admin/HR/manager approves or rejects a pending request.
     * Approval creates the actual AttendanceRecord (OFFICIAL_BUSINESS status).
     * Manager is the primary approver, HR/Admin is secondary/backup — both can
     * act on any pending request; approved_by_role just records who did.
     */
    public function updateStatus(Request $request, $id)
    {
        if (!$this->isReviewer()) {
            abort(403, 'You are not authorized to review Official Business requests.');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string|max:500',
        ]);

        $obRequest = OfficialBusinessRequest::findOrFail($id);
        $this->expireIfPastDeadline($obRequest);

        if ($obRequest->isExpired()) {
            return redirect()->back()->with('error', 'This request expired before it was reviewed and can no longer be actioned.');
        }

        if (!$obRequest->isPending()) {
            return redirect()->back()->with('error', 'This request has already been reviewed.');
        }

        $reviewerRole = Auth::user()->role ?? null;

        if ($validated['status'] === 'approved') {
            $existingAttendance = AttendanceRecord::where('employee_id', $obRequest->employee_id)
                ->where('date', $obRequest->date)
                ->first();

            if ($existingAttendance) {
                return redirect()->back()
                    ->with('error', 'An attendance record already exists for this employee on this date. Resolve it before approving.');
            }

            // Credited hours: exact duration between ob_start_time/ob_end_time.
            // See OfficialBusinessRequest::computeCreditedHours().
            $creditedHours = $obRequest->computeCreditedHours();

            $attendanceRecord = AttendanceRecord::create([
                'employee_id' => $obRequest->employee_id,
                'date' => $obRequest->date,
                'status' => AttendanceRecord::OFFICIAL_BUSINESS,
                'notes' => $obRequest->reason,
                'time_in' => $obRequest->ob_start_time,
                'time_out' => $obRequest->ob_end_time,
                'break_start' => null,
                'break_end' => null,
                'total_hours' => $creditedHours,
                'regular_hours' => $creditedHours,
                'overtime_hours' => 0,
            ]);

            $obRequest->update([
                'status' => OfficialBusinessRequest::APPROVED,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => Carbon::now(),
                'approved_by_role' => $reviewerRole,
                'attendance_record_id' => $attendanceRecord->id,
                'credited_hours' => $creditedHours,
            ]);

            return redirect()->back()->with('success', 'OB request approved and attendance record created.');
        }

        $obRequest->update([
            'status' => OfficialBusinessRequest::REJECTED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => Carbon::now(),
            'approved_by_role' => $reviewerRole,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->back()->with('success', 'OB request rejected.');
    }

    /**
     * Employee cancels their own pending request (reviewers can also cancel any pending request).
     */
    public function cancel(Request $request, $id)
    {
        $obRequest = OfficialBusinessRequest::findOrFail($id);
        $ownsRequest = $obRequest->employee_id === $this->currentEmployeeId();

        if (!$ownsRequest && !$this->isReviewer()) {
            abort(403, 'You are not authorized to cancel this request.');
        }

        if (!$obRequest->isPending()) {
            return redirect()->back()->with('error', 'Only pending requests can be cancelled.');
        }

        $obRequest->delete();

        return redirect()->back()->with('success', 'OB request cancelled.');
    }

    public function getStatistics(Request $request)
    {
        $isReviewer = $this->isReviewer();

        $query = $isReviewer
            ? OfficialBusinessRequest::query()
            : OfficialBusinessRequest::where('employee_id', $this->currentEmployeeId());

        return response()->json([
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->pending()->count(),
            'approved' => (clone $query)->approved()->count(),
            'rejected' => (clone $query)->rejected()->count(),
            'expired' => (clone $query)->expired()->count(),
        ]);
    }
}