<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CompanyHelper;
use App\Http\Controllers\Concerns\CalculatesAttendanceWithOfficialBusiness;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\OfficialBusinessRequest;
use App\Notifications\RequestStatusChanged;
use App\Exports\OfficialBusinessExport;
use App\Services\CutoffPeriodService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficialBusinessController extends Controller
{
    private function currentCompanyObOrFail(string $id): OfficialBusinessRequest
    {
        return OfficialBusinessRequest::query()
            ->whereHas('employee', fn ($query) => $query->forCompany(CompanyHelper::getCurrentCompanyId()))
            ->findOrFail($id);
    }

    use CalculatesAttendanceWithOfficialBusiness;

    public function __construct(
        private CutoffPeriodService $cutoffPeriods
    ) {}

    /**
     * Check if the logged-in user can review OB requests.
     */
    private function isReviewer(): bool
    {
        $user = Auth::user();

        return $user
            && in_array(
                $user->role ?? null,
                ['admin', 'hr', 'manager'],
                true
            );
    }

    /**
     * Get the current employee ID.
     */
    private function currentEmployeeId(): ?string
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        return $user->employee?->id
            ?? $user->employee_id
            ?? null;
    }

    /**
     * Expire a request if its deadline has passed.
     */
    private function expireIfPastDeadline(
        OfficialBusinessRequest $obRequest
    ): OfficialBusinessRequest {
        if (
            $obRequest->isPending()
            && $obRequest->isPastDeadline()
        ) {
            $obRequest->update([
                'status' => OfficialBusinessRequest::EXPIRED,
            ]);

            $obRequest->refresh();
        }

        return $obRequest;
    }

    /**
     * Expire pending requests for an employee.
     */
    private function expirePendingRequestsForEmployee(
        string $employeeId
    ): void {
        OfficialBusinessRequest::query()
            ->where('employee_id', $employeeId)
            ->where(
                'status',
                OfficialBusinessRequest::PENDING
            )
            ->get()
            ->each(
                fn (OfficialBusinessRequest $obRequest) =>
                    $this->expireIfPastDeadline($obRequest)
            );
    }

    /**
     * Apply role-based Official Business filters.
     *
     * Reviewers may filter by department, employee, status, and date range.
     * Employees may filter only their own requests by status and exact date.
     */
    private function applyFilters($query, Request $request, bool $isReviewer)
    {
        $currentCompany = CompanyHelper::getCurrentCompany();
        if ($currentCompany) {
            $query->whereHas('employee', fn ($employee) => $employee->forCompany($currentCompany->id));
        }

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

        if ((Auth::user()->role ?? null) === 'manager') {
            $query->whereHas('employee.department', fn ($dept) => $dept->where('manager_id', $this->currentEmployeeId()));
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
        $employeeId = $this->currentEmployeeId();
        $personalRequested = $request->query('scope') === 'mine';
        if ($personalRequested && !$employeeId) {
            return redirect()->route('dashboard')->with('error', 'No employee record is linked to this account.');
        }
        $personalMode = $personalRequested;
        $isReviewer = $this->isReviewer() && !$personalMode;

        if (!$isReviewer && $employeeId) {
            $this->expirePendingRequestsForEmployee($employeeId);
        }

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

        $currentCompany = CompanyHelper::getCurrentCompany();

        $departmentsQuery = $reviewerRole === 'manager'
            ? Department::where('manager_id', $user->employee_id)->orderBy('name')
            : Department::orderBy('name');
        if ($currentCompany) {
            $departmentsQuery->forCompany($currentCompany->id);
        }
        $departments = $departmentsQuery->get();

        $employeesQuery = Employee::with('department')
            ->orderBy('first_name')
            ->orderBy('last_name');
        if ($reviewerRole === 'manager') {
            $employeesQuery->managedBy($user->employee_id);
        }
        if ($currentCompany) {
            $employeesQuery->forCompany($currentCompany->id);
        }
        $employees = $employeesQuery->get();


        $calendarRequests = collect();

        if (!$isReviewer && $employeeId) {
            $calendarRequests = OfficialBusinessRequest::query()
                ->where('employee_id', $employeeId)
                ->whereIn('status', [
                    OfficialBusinessRequest::PENDING,
                    OfficialBusinessRequest::APPROVED,
                ])
                ->orderBy('date')
                ->get(['date', 'status'])
                ->map(fn (OfficialBusinessRequest $obRequest) => [
                    'date' => $obRequest->date->format('Y-m-d'),
                    'status' => $obRequest->status,
                ])
                ->values();
        }

        return view('attendance.official-business', [
            'user' => $user,
            'activeRoute' => 'attendance.official-business',
            'pageTitle' => 'Official Business',
            'isReviewer' => $isReviewer,
            'personalMode' => $personalMode,
            'reviewerRole' => $reviewerRole,
            'currentEmployeeId' => $employeeId,
            'cutoffDays' => config('attendance_cutoff.cutoff_days', [10, 25]),
            'graceHours' => config('attendance_cutoff.grace_period_hours', 24),
            'obRequests' => $obRequests,
            'summary' => $summary,
            'departments' => $departments,
            'employees' => $employees,
            'calendarRequests' => $calendarRequests,
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
        $format = strtolower($format);

        if (!in_array($format, ['pdf', 'csv', 'xlsx', 'xls'], true)) {
            return back()->with('error', 'Unsupported export format.');
        }

        $personalMode = $request->query('scope') === 'mine';
        if ($personalMode && !$this->currentEmployeeId()) {
            return back()->with('error', 'No employee record is linked to this account.');
        }

        $query = $this->applyFilters(
            OfficialBusinessRequest::with([
                'employee.department',
                'reviewer.employee',
            ]),
            $request,
            $this->isReviewer() && !$personalMode
        );

        $requests = $query
            ->orderBy('created_at', 'desc')
            ->get();

        $timestamp = now()->format('Ymd_His');

        if ($format === 'pdf') {
            return Pdf::loadView(
                'attendance.exports.official-business-pdf',
                [
                    'requests' => $requests,
                    'generatedAt' => now(),
                    'filters' => $request->query(),
                ]
            )->setPaper('a4', 'landscape')
                ->download("official-business_{$timestamp}.pdf");
        }


        $export = new OfficialBusinessExport($requests);

        if ($format === 'csv') {
            return Excel::download(
                $export,
                "official-business_{$timestamp}.csv",
                \Maatwebsite\Excel\Excel::CSV
            );
        }

        return Excel::download(
            $export,
            "official-business_{$timestamp}.xlsx",
            \Maatwebsite\Excel\Excel::XLSX
        );
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
            'date' => [
                'required',
                'date',
            ],

            'reason' => [
                'required',
                'string',
                'max:500',

                function ($attribute, $value, $fail) {
                    if (trim((string) $value) === '') {
                        $fail(
                            'Remarks field is required.'
                        );
                    }
                },
            ],

            'ob_start_time' => [
                'required',
                'date_format:H:i',
            ],

            'ob_end_time' => [
                'required',
                'date_format:H:i',
                'after:ob_start_time',
            ],
        ]);

        $employeeId = $this->currentEmployeeId();

        if (!$employeeId) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'No employee profile is linked to this account.'
                );
        }

        $obDate = $this->normalizeDate(
            $validated['date']
        );

        if (
            !$this->cutoffPeriods
                ->isOpenForAction($obDate)
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'This date is not included in the current payroll cutoff period.'
                );
        }

        $this->expirePendingRequestsForEmployee(
            $employeeId
        );

        $startTime = $this->normalizeTime(
            $validated['ob_start_time']
        );

        $endTime = $this->normalizeTime(
            $validated['ob_end_time']
        );

        $newInterval = $this->createInterval(
            $obDate,
            $startTime,
            $endTime
        );

        if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($employeeId, $obDate)) {
            return back()->withInput()->with(
                'error',
                'Official Business cannot be filed for a date covered by a locked payroll period.'
            );
        }

        $conflicts = app(\App\Services\PayrollRequestConflictService::class);
        if ($conflicts->leaveOnDate($employeeId, $obDate)) {
            return back()->withInput()->with(
                'error',
                'Official Business cannot be filed on a date covered by pending or approved leave.'
            );
        }
        if ($conflicts->overtimeOnDate($employeeId, $obDate)) {
            return back()->withInput()->with(
                'error',
                'Official Business cannot be filed on a date with pending or approved overtime.'
            );
        }

        $existingRequests =
            OfficialBusinessRequest::query()
                ->where(
                    'employee_id',
                    $employeeId
                )
                ->whereDate(
                    'date',
                    $obDate
                )
                ->whereIn('status', [
                    OfficialBusinessRequest::PENDING,
                    OfficialBusinessRequest::APPROVED,
                ])
                ->whereNotNull('ob_start_time')
                ->whereNotNull('ob_end_time')
                ->get();

        $overlappingRequest =
            $existingRequests->contains(
                function (
                    OfficialBusinessRequest $existing
                ) use (
                    $obDate,
                    $newInterval
                ): bool {
                    $existingInterval =
                        $this->createInterval(
                            $obDate,
                            $existing->ob_start_time,
                            $existing->ob_end_time
                        );

                    return
                        $newInterval['start']->lt(
                            $existingInterval['end']
                        )
                        &&
                        $newInterval['end']->gt(
                            $existingInterval['start']
                        );
                }
            );

        if ($overlappingRequest) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'This Official Business request overlaps with an existing pending or approved OB request.'
                );
        }

        $period = $this->cutoffPeriods
            ->periodFor($obDate);

        OfficialBusinessRequest::create([
            'employee_id' => $employeeId,

            'date' => $obDate,

            'reason' => trim(
                $validated['reason']
            ),

            'status' =>
                OfficialBusinessRequest::PENDING,

            'is_full_day' => false,

            'ob_start_time' => $startTime,

            'ob_end_time' => $endTime,

            'cutoff_period_key' =>
                $period['key'],

            'expires_at' =>
                $this->cutoffPeriods
                    ->graceDeadlineFor($obDate),

            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route(
                'attendance.official-business'
            )
            ->with(
                'success',
                'Official Business request submitted. Waiting for approval.'
            );
    }

    /**
     * Approve or reject an OB request.
     */
    public function updateStatus(
        Request $request,
        $id
    ) {
        if (!$this->isReviewer()) {
            abort(
                403,
                'You are not authorized to review Official Business requests.'
            );
        }

        $validated = $request->validate([
            'status' => [
                'required',
                'in:approved,rejected',
            ],

            'rejection_reason' => [
                'nullable',
                'required_if:status,rejected',
                'string',
                'max:500',
            ],
        ]);

        $obRequest =
            $this->currentCompanyObOrFail($id);

        $this->expireIfPastDeadline(
            $obRequest
        );

        if ($obRequest->isExpired()) {
            return back()->with(
                'error',
                'This request expired before it was reviewed.'
            );
        }

        if (!$obRequest->isPending()) {
            return back()->with(
                'error',
                'This request has already been reviewed.'
            );
        }

        if (
            $this->currentEmployeeId()
            && $obRequest->employee_id === $this->currentEmployeeId()
        ) {
            abort(
                403,
                'You cannot approve or reject your own Official Business request.'
            );
        }

        if ((Auth::user()->role ?? null) === 'manager') {
            $obRequest->loadMissing('employee.department');
            if (!$obRequest->employee || !$obRequest->employee->isManagedBy($this->currentEmployeeId())) {
                abort(403, 'You can only review Official Business requests for employees in your department.');
            }
        }

        $reviewerRole = Auth::user()->role ?? null;
        $reviewDate = $this->normalizeDate($obRequest->date);

        if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate(
            $obRequest->employee_id,
            $reviewDate
        )) {
            return back()->with('error', 'This Official Business request belongs to a locked payroll period and can no longer be reviewed or changed.');
        }

        if (
            $validated['status']
            === OfficialBusinessRequest::APPROVED
        ) {
            $conflicts = app(\App\Services\PayrollRequestConflictService::class);
            $date = $this->normalizeDate($obRequest->date);
            if ($conflicts->leaveOnDate($obRequest->employee_id, $date)) {
                return back()->with('error', 'Cannot approve Official Business because this date is covered by leave.');
            }
            if ($conflicts->overtimeOnDate($obRequest->employee_id, $date)) {
                return back()->with('error', 'Cannot approve Official Business because this date has overtime.');
            }

            DB::transaction(
                function () use (
                    $obRequest,
                    $reviewerRole
                ) {
                    $date = $this->normalizeDate(
                        $obRequest->date
                    );

                    $obInterval = $this->createInterval(
                        $date,
                        $obRequest->ob_start_time,
                        $obRequest->ob_end_time
                    );

                    $obHours =
                        $obRequest->computeCreditedHours();

                    $attendanceRecord =
                        AttendanceRecord::query()
                            ->where(
                                'employee_id',
                                $obRequest->employee_id
                            )
                            ->whereDate(
                                'date',
                                $date
                            )
                            ->first();

                    if (!$attendanceRecord) {
                        $attendanceRecord =
                            AttendanceRecord::create([
                                'employee_id' =>
                                    $obRequest->employee_id,

                                'date' => $date,

                                'status' =>
                                    AttendanceRecord::OFFICIAL_BUSINESS,

                                'notes' =>
                                    'Approved OB: '
                                    . $obRequest->reason
                                    . ' ('
                                    . number_format(
                                        $obHours,
                                        2
                                    )
                                    . ' hrs)',

                                'time_in' => null,

                                'time_out' => null,

                                'break_start' => null,

                                'break_end' => null,

                                'total_hours' => 0,

                                'regular_hours' => 0,

                                'overtime_hours' => 0,
                            ]);
                    } else {
                        $notes = $attendanceRecord->notes
                            ? $attendanceRecord->notes
                                . PHP_EOL
                            : '';

                        $notes .=
                            'Approved OB: '
                            . $obRequest->reason
                            . ' ('
                            . number_format(
                                $obHours,
                                2
                            )
                            . ' hrs)';

                        $attendanceRecord->update([
                            'notes' => trim($notes),
                        ]);
                    }

                    /*
                     * Approve first because recalculation reads
                     * all APPROVED OB requests.
                     */
                    $obRequest->update([
                        'status' =>
                            OfficialBusinessRequest::APPROVED,

                        'reviewed_by' =>
                            Auth::id(),

                        'reviewed_at' =>
                            Carbon::now(
                                $this->timezone()
                            ),

                        'approved_by_role' =>
                            $reviewerRole,

                        'attendance_record_id' =>
                            $attendanceRecord->id,

                        'credited_hours' =>
                            $obHours,

                        'rejection_reason' =>
                            null,
                    ]);

                    $this
                        ->recalculateAttendanceWithOfficialBusiness(
                            $attendanceRecord
                        );

                    $this->notifyRequester($obRequest->fresh());
                }
            );

            return back()->with(
                'success',
                'OB request approved. Attendance hours were recalculated.'
            );
        }

        $obRequest->update([
            'status' =>
                OfficialBusinessRequest::REJECTED,

            'reviewed_by' =>
                Auth::id(),

            'reviewed_at' =>
                Carbon::now($this->timezone()),

            'approved_by_role' =>
                $reviewerRole,

            'rejection_reason' => trim(
                $validated['rejection_reason']
            ),
        ]);

        $this->notifyRequester($obRequest->fresh());

        return back()->with(
            'success',
            'OB request rejected.'
        );
    }

    protected function notifyRequester(OfficialBusinessRequest $obRequest): void
    {
        $account = $obRequest->employee?->account;
        if (!$account) {
            return;
        }

        $dateLabel = Carbon::parse($this->normalizeDate($obRequest->date))->format('M d, Y');

        $account->notify(new RequestStatusChanged(
            RequestStatusChanged::TYPE_OFFICIAL_BUSINESS,
            $obRequest->id,
            $obRequest->status,
            $dateLabel,
            $obRequest->rejection_reason,
        ));
    }

    /**
     * Cancel a pending OB request, or reverse an already-approved one.
     *
     * Pending: any owner or reviewer can withdraw it outright (row is deleted,
     * matching the original behavior — nothing was ever applied to attendance).
     *
     * Approved: reviewers only (admin/hr/manager). This walks back what
     * updateStatus() applied on approval: the request is marked cancelled,
     * and recalculateAttendanceWithOfficialBusiness() is re-run so the
     * attendance record's hours reflect only what's still actually approved
     * for that date (it re-queries approved OB requests directly from the
     * DB, so excluding this one is automatic). If that leaves the attendance
     * record with no real clock-in and zero credited hours — i.e. it only
     * ever existed to carry this OB's time — the now-stale record is removed.
     */
    public function cancel(
        Request $request,
        $id
    ) {
        $obRequest =
            $this->currentCompanyObOrFail($id);

        $this->expireIfPastDeadline(
            $obRequest
        );

        $ownsRequest =
            $obRequest->employee_id
            === $this->currentEmployeeId();

        $isReviewer = $this->isReviewer();

        if (
            !$ownsRequest
            && !$isReviewer
        ) {
            abort(
                403,
                'You are not authorized to cancel this request.'
            );
        }

        if (!$ownsRequest && (Auth::user()->role ?? null) === 'manager') {
            $obRequest->loadMissing('employee.department');
            if (!$obRequest->employee || !$obRequest->employee->isManagedBy($this->currentEmployeeId())) {
                abort(403, 'You can only cancel requests for employees in your department.');
            }
        }

        if ($obRequest->isPending()) {
            $obRequest->delete();

            return back()->with(
                'success',
                'OB request cancelled.'
            );
        }

        if ($obRequest->isApproved()) {
            if (!$isReviewer) {
                return back()->with(
                    'error',
                    'Only admin, HR, or manager can cancel an approved Official Business request.'
                );
            }

            $conflicts = app(\App\Services\PayrollRequestConflictService::class);
            if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($obRequest->employee_id, $this->normalizeDate($obRequest->date))) {
                return back()->with(
                    'error',
                    'Cannot cancel — payroll has already been generated for this date. The payroll period is locked and this request can no longer be changed.'
                );
            }

            $validated = $request->validate([
                'cancellation_reason' => ['nullable', 'string', 'max:500'],
            ]);

            DB::transaction(function () use ($obRequest, $validated) {
                $attendanceRecord = $obRequest->attendanceRecord;

                $obRequest->update([
                    'status' => OfficialBusinessRequest::CANCELLED,
                    'reviewed_by' => Auth::id(),
                    'rejection_reason' => $validated['cancellation_reason'] ?? null,
                ]);

                if (!$attendanceRecord) {
                    return;
                }

                $this->recalculateAttendanceWithOfficialBusiness($attendanceRecord);
                $attendanceRecord->refresh();

                // Only delete a record that never had a real clock-in and no
                // longer carries any credited hours — i.e. it existed solely
                // to hold this OB's time and nothing still justifies it.
                if (
                    is_null($attendanceRecord->time_in)
                    && (float) $attendanceRecord->total_hours === 0.0
                ) {
                    $obRequest->update(['attendance_record_id' => null]);
                    $attendanceRecord->delete();
                }
            });

            return back()->with(
                'success',
                'Approved OB request cancelled. Attendance hours were recalculated.'
            );
        }

        return back()->with(
            'error',
            'Only pending or approved requests can be cancelled.'
        );
    }

    /**
     * Edit an already-approved Official Business request. Reserved for
     * admin/hr/manager (manager scoped to their department), and only while
     * no payroll has been generated for the original or new date. Detaches
     * from the old date's attendance record and re-runs
     * recalculateAttendanceWithOfficialBusiness() there (same cleanup cancel()
     * uses), then attaches/creates the attendance record for the new date and
     * recalculates it too. Writes a before/after audit row either way.
     */
    public function updateApproved(Request $request, $id)
    {
        $obRequest = $this->currentCompanyObOrFail($id);

        if (!$obRequest->isApproved()) {
            return back()->with('error', 'Only approved Official Business requests can be edited here.');
        }

        if (!$this->isReviewer()) {
            abort(403, 'Only admin, HR, or manager can edit an approved Official Business request.');
        }

        if ((Auth::user()->role ?? null) === 'manager' && $obRequest->employee_id !== $this->currentEmployeeId()) {
            $obRequest->loadMissing('employee.department');
            if (!$obRequest->employee || !$obRequest->employee->isManagedBy($this->currentEmployeeId())) {
                abort(403, 'You can only edit requests for employees in your department.');
            }
        }

        $conflicts = app(\App\Services\PayrollRequestConflictService::class);
        $originalDate = $this->normalizeDate($obRequest->date);

        if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($obRequest->employee_id, $originalDate)) {
            return back()->with(
                'error',
                'Cannot edit — payroll has already been generated for this date. The payroll period is locked and this request can no longer be changed.'
            );
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'is_full_day' => ['required', 'boolean'],
            'ob_start_time' => ['nullable', 'required_if:is_full_day,0', 'date_format:H:i'],
            'ob_end_time' => ['nullable', 'required_if:is_full_day,0', 'date_format:H:i', 'after:ob_start_time'],
            'reason' => ['required', 'string', 'max:1000'],
            'correction_reason' => ['required', 'string', 'max:500'],
        ]);

        $newDate = $this->normalizeDate($validated['date']);

        if (app(\App\Services\PayrollPeriodLockService::class)->isLockedForDate($obRequest->employee_id, $newDate)) {
            return back()->with(
                'error',
                'Cannot edit — payroll has already been generated for the new date. That payroll period is locked and can no longer be modified.'
            );
        }

        if ($newDate !== $originalDate) {
            if ($conflicts->leaveOnDate($obRequest->employee_id, $newDate)) {
                return back()->with('error', 'Cannot move this OB — the new date is covered by leave.');
            }
            if ($conflicts->overtimeOnDate($obRequest->employee_id, $newDate)) {
                return back()->with('error', 'Cannot move this OB — the new date already has overtime.');
            }
        }

        $auditFields = ['date', 'is_full_day', 'ob_start_time', 'ob_end_time', 'reason', 'credited_hours'];
        $originalValues = $obRequest->only($auditFields);
        $oldAttendanceRecord = $obRequest->attendanceRecord;

        DB::transaction(function () use ($obRequest, $validated, $newDate, $originalDate, $oldAttendanceRecord, $originalValues, $auditFields) {
            // Detach from the old date's attendance record and let the
            // recalculation re-derive it without this OB's contribution —
            // same cleanup cancel() does.
            if ($oldAttendanceRecord) {
                $obRequest->update(['attendance_record_id' => null]);
                $this->recalculateAttendanceWithOfficialBusiness($oldAttendanceRecord);
                $oldAttendanceRecord->refresh();

                if (
                    $newDate !== $originalDate
                    && is_null($oldAttendanceRecord->time_in)
                    && (float) $oldAttendanceRecord->total_hours === 0.0
                ) {
                    $oldAttendanceRecord->delete();
                }
            }

            $obRequest->fill([
                'date' => $newDate,
                'is_full_day' => $validated['is_full_day'],
                'ob_start_time' => $validated['ob_start_time'] ?? null,
                'ob_end_time' => $validated['ob_end_time'] ?? null,
                'reason' => $validated['reason'],
            ]);
            $obHours = $obRequest->computeCreditedHours();

            $attendanceRecord = AttendanceRecord::query()
                ->where('employee_id', $obRequest->employee_id)
                ->whereDate('date', $newDate)
                ->first();

            if (!$attendanceRecord) {
                $attendanceRecord = AttendanceRecord::create([
                    'employee_id' => $obRequest->employee_id,
                    'date' => $newDate,
                    'status' => AttendanceRecord::OFFICIAL_BUSINESS,
                    'notes' => 'Corrected OB: ' . $obRequest->reason . ' (' . number_format($obHours, 2) . ' hrs)',
                    'time_in' => null,
                    'time_out' => null,
                    'break_start' => null,
                    'break_end' => null,
                    'total_hours' => 0,
                    'regular_hours' => 0,
                    'overtime_hours' => 0,
                ]);
            } else {
                $notes = $attendanceRecord->notes ? $attendanceRecord->notes . PHP_EOL : '';
                $notes .= 'Corrected OB: ' . $obRequest->reason . ' (' . number_format($obHours, 2) . ' hrs)';
                $attendanceRecord->update(['notes' => trim($notes)]);
            }

            $obRequest->update([
                'attendance_record_id' => $attendanceRecord->id,
                'credited_hours' => $obHours,
            ]);

            $this->recalculateAttendanceWithOfficialBusiness($attendanceRecord);

            DB::table('request_corrections')->insert([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'request_type' => 'official_business',
                'request_id' => $obRequest->id,
                'corrected_by' => Auth::id(),
                'reason' => $validated['correction_reason'],
                'original_values' => json_encode($originalValues),
                'corrected_values' => json_encode($obRequest->fresh()->only($auditFields)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $this->notifyRequester($obRequest->fresh());

        return back()->with('success', 'Approved Official Business request corrected. Attendance hours were recalculated.');
    }

    /**
     * Return OB statistics.
     */
    public function getStatistics(
        Request $request
    ) {
        $personalMode = $request->query('scope') === 'mine';
        $isReviewer = $this->isReviewer() && !$personalMode;

        $employeeId = $this->currentEmployeeId();

        if (
            !$isReviewer
            && $employeeId
        ) {
            $this->expirePendingRequestsForEmployee(
                $employeeId
            );
        }

        $query = $this->applyFilters(
            OfficialBusinessRequest::query(),
            $request,
            $isReviewer
        );

        return response()->json([
            'total' =>
                (clone $query)->count(),

            'pending' =>
                (clone $query)
                    ->pending()
                    ->count(),

            'approved' =>
                (clone $query)
                    ->approved()
                    ->count(),

            'rejected' =>
                (clone $query)
                    ->rejected()
                    ->count(),

            'expired' =>
                (clone $query)
                    ->expired()
                    ->count(),
        ]);
    }
}
