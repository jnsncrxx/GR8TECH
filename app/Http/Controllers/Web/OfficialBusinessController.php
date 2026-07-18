<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\CalculatesAttendanceWithOfficialBusiness;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\OfficialBusinessRequest;
use App\Notifications\RequestStatusChanged;
use App\Services\CutoffPeriodService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficialBusinessController extends Controller
{
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
     * Display OB requests.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $isReviewer = $this->isReviewer();

        $employeeId = $this->currentEmployeeId();

        if (
            !$isReviewer
            && $employeeId
        ) {
            $this->expirePendingRequestsForEmployee(
                $employeeId
            );
        }

        $query = OfficialBusinessRequest::with([
            'employee.department',
            'reviewer.employee',
        ]);

        if (!$isReviewer) {
            $query->where(
                'employee_id',
                $employeeId
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->query('status')
            );
        }

        if (
            $isReviewer
            && $request->filled('employee_id')
        ) {
            $query->where(
                'employee_id',
                $request->query('employee_id')
            );
        }

        if (
            $isReviewer
            && $request->filled('department_id')
        ) {
            $departmentId = $request->query(
                'department_id'
            );

            $query->whereHas(
                'employee',
                fn ($employeeQuery) =>
                    $employeeQuery->where(
                        'department_id',
                        $departmentId
                    )
            );
        }

        if (
            $isReviewer
            && $request->filled('date_from')
        ) {
            $query->whereDate(
                'date',
                '>=',
                $request->query('date_from')
            );
        }

        if (
            $isReviewer
            && $request->filled('date_to')
        ) {
            $query->whereDate(
                'date',
                '<=',
                $request->query('date_to')
            );
        }

        $reviewerRole = $isReviewer
            ? ($user->role ?? null)
            : null;

        if ($reviewerRole === 'manager') {
            $query->orderByRaw(
                "CASE
                    WHEN status = 'pending' THEN 0
                    ELSE 1
                END"
            );
        }

        $obRequests = $query
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $obRequests
            ->getCollection()
            ->each(
                fn (OfficialBusinessRequest $obRequest) =>
                    $this->expireIfPastDeadline($obRequest)
            );

        $summaryBase = $isReviewer
            ? OfficialBusinessRequest::query()
            : OfficialBusinessRequest::where(
                'employee_id',
                $employeeId
            );

        $summary = [
            'total' => (clone $summaryBase)->count(),

            'pending' => (clone $summaryBase) 
                ->pending()
                ->count(),

            'approved' => (clone $summaryBase)
                ->approved()
                ->count(),

            'rejected' => (clone $summaryBase)
                ->rejected()
                ->count(),

            'expired' => (clone $summaryBase)
                ->expired()
                ->count(),
        ];

        return view(
            'attendance.official-business',
            [
                'user' => $user,

                'activeRoute' =>
                    'attendance.official-business',

                'pageTitle' =>
                    'Official Business',

                'isReviewer' =>
                    $isReviewer,

                'reviewerRole' =>
                    $reviewerRole,

                'currentEmployeeId' =>
                    $employeeId,

                'cutoffDays' =>
                    config('attendance_cutoff.cutoff_days', [10, 25]),

                'graceHours' =>
                    config('attendance_cutoff.grace_period_hours', 24),

                'obRequests' =>
                    $obRequests,

                'summary' =>
                    $summary,

                'departments' =>
                    Department::orderBy('name')->get(),

                'employees' =>
                    Employee::orderBy('first_name')
                        ->orderBy('last_name')
                        ->get(),
            ]
        );
    }

    /**
     * Submit an OB request.
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
            OfficialBusinessRequest::findOrFail($id);

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

        $reviewerRole = Auth::user()->role ?? null;

        if (
            $validated['status']
            === OfficialBusinessRequest::APPROVED
        ) {
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

                    $obMinutes = (int) $obInterval['start']
                        ->diffInMinutes(
                            $obInterval['end']
                        );

                    $obHours = round(
                        $obMinutes / 60,
                        2
                    );

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
     * Cancel a pending OB request.
     */
    public function cancel(
        Request $request,
        $id
    ) {
        $obRequest =
            OfficialBusinessRequest::findOrFail($id);

        $this->expireIfPastDeadline(
            $obRequest
        );

        $ownsRequest =
            $obRequest->employee_id
            === $this->currentEmployeeId();

        if (
            !$ownsRequest
            && !$this->isReviewer()
        ) {
            abort(
                403,
                'You are not authorized to cancel this request.'
            );
        }

        if (!$obRequest->isPending()) {
            return back()->with(
                'error',
                'Only pending requests can be cancelled.'
            );
        }

        $obRequest->delete();

        return back()->with(
            'success',
            'OB request cancelled.'
        );
    }

    /**
     * Return OB statistics.
     */
    public function getStatistics(
        Request $request
    ) {
        $isReviewer = $this->isReviewer();

        $employeeId = $this->currentEmployeeId();

        if (
            !$isReviewer
            && $employeeId
        ) {
            $this->expirePendingRequestsForEmployee(
                $employeeId
            );
        }

        $query = $isReviewer
            ? OfficialBusinessRequest::query()
            : OfficialBusinessRequest::where(
                'employee_id',
                $employeeId
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