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

    /**
     * Apply role-based Official Business filters.
     *
     * Reviewers may filter by department, employee, status, and date range.
     * Employees may filter only their own requests by status and exact date.
     */
    private function applyFilters($query, Request $request, bool $isReviewer)
    {
        if (!$isReviewer) {
            $query->where('employee_id', $this->currentEmployeeId());

            if ($request->filled('status')) {
                $query->where('status', $request->query('status'));
            }

            if ($request->filled('date')) {
                $query->whereDate('date', $request->query('date'));
            }

            return $query;
        }

        if ($request->filled('department_id')) {
            $departmentId = $request->query('department_id');

            $query->whereHas('employee', function ($employeeQuery) use ($departmentId) {
                $employeeQuery->where('department_id', $departmentId);
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->query('employee_id'));
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
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isReviewer = $this->isReviewer();

        $query = $this->applyFilters(
            OfficialBusinessRequest::with(['employee.department', 'reviewer.employee']),
            $request,
            $isReviewer
        );

        $reviewerRole = $isReviewer ? ($user->role ?? null) : null;

        // Manager is the primary approver — surface pending requests first so
        // their "needs action" queue isn't buried under already-reviewed ones.
        // HR/Admin (backup) keep the plain chronological view since they're
        // scanning everything, not just their own action items.
        if ($reviewerRole === 'manager') {
            $obRequests = $query
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();
        } else {
            $obRequests = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        }

        // Lazy-expire anything past its grace deadline that the sweep hasn't
        // caught yet, so what the reviewer/employee sees is always accurate.
        $obRequests->getCollection()->each(fn ($obRequest) => $this->expireIfPastDeadline($obRequest));

        $summaryBase = $this->applyFilters(
            OfficialBusinessRequest::query(),
            $request,
            $isReviewer
        );

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
            'reviewerRole' => $reviewerRole,
            'obRequests' => $obRequests,
            'summary' => $summary,
            'departments' => $departments,
            'employees' => $employees,
        ]);
    }

    /**
     * Export Official Business requests.
     *
     * CSV is used as the transport format for CSV, XLS, and PDF menu options
     * until dedicated PDF/Excel renderers are added.
     */
    public function exportOfficialBusiness(Request $request, string $format)
    {
        $allowedFormats = ['pdf', 'csv', 'xls'];

        if (!in_array($format, $allowedFormats, true)) {
            return back()->with('error', 'Unsupported export format.');
        }

        $query = $this->applyFilters(
            OfficialBusinessRequest::with(['employee.department', 'reviewer.employee']),
            $request,
            $this->isReviewer()
        );

        $requests = $query->orderBy('created_at', 'desc')->get();
        $fileName = 'official-business-' . now()->format('YmdHis') . '.csv';

        return response()->streamDownload(function () use ($requests) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Employee',
                'Department',
                'Date',
                'Reason',
                'OB Hours',
                'Time In',
                'Time Out',
                'Status',
                'Reviewed By',
                'Admin Reason',
            ]);

            foreach ($requests as $obRequest) {
                $reviewerName = trim(
                    ($obRequest->reviewer->employee->first_name ?? '') . ' ' .
                    ($obRequest->reviewer->employee->last_name ?? '')
                );

                fputcsv($handle, [
                    $obRequest->employee->full_name ?? 'N/A',
                    $obRequest->employee->department->name ?? 'N/A',
                    $obRequest->date?->format('Y-m-d') ?? '',
                    $obRequest->reason,
                    number_format(
                        (float) ($obRequest->credited_hours ?? $obRequest->computeCreditedHours()),
                        2,
                        '.',
                        ''
                    ),
                    $obRequest->ob_start_time
                        ? Carbon::parse($obRequest->ob_start_time)->format('h:i A')
                        : '',
                    $obRequest->ob_end_time
                        ? Carbon::parse($obRequest->ob_end_time)->format('h:i A')
                        : '',
                    ucfirst($obRequest->status),
                    $reviewerName,
                    $obRequest->rejection_reason ?? '',
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
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

            // Create the attendance record first, then let AttendanceRecord compute
            // its own hours the same way it would for any other record (see
            // calculateTotalHours() / calculateRegularAndOvertimeHours()). This
            // guarantees OB-derived records are categorized identically to regular
            // attendance — including the 8-hour regular/overtime split — rather than
            // duplicating that logic here and risking drift if it changes later.
            $attendanceRecord = AttendanceRecord::create([
                'employee_id' => $obRequest->employee_id,
                'date' => $obRequest->date,
                'status' => AttendanceRecord::OFFICIAL_BUSINESS,
                'notes' => $obRequest->reason,
                'time_in' => $obRequest->ob_start_time,
                'time_out' => $obRequest->ob_end_time,
                'break_start' => null,
                'break_end' => null,
                'total_hours' => 0,
                'regular_hours' => 0,
                'overtime_hours' => 0,
            ]);

            $totalHours = $attendanceRecord->calculateTotalHours();
            $hoursSplit = $attendanceRecord->calculateRegularAndOvertimeHours();

            $attendanceRecord->update([
                'total_hours' => $totalHours,
                'regular_hours' => $hoursSplit['regular_hours'],
                'overtime_hours' => $hoursSplit['overtime_hours'],
            ]);

            $creditedHours = $totalHours;

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