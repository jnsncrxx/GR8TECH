<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CompanyHelper;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Document;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\LeaveRequest;
use App\Models\OfficialBusinessRequest;
use App\Models\OvertimeRequest;
use App\Models\Payment;
use App\Models\Payroll;
use App\Models\Position;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /** Max rows returned per module group. */
    private const PER_MODULE_LIMIT = 5;

    /** Minimum query length before we hit the database. */
    private const MIN_QUERY_LENGTH = 2;

    /** Maximum query length accepted — guards against abusive/oversized input. */
    private const MAX_QUERY_LENGTH = 100;

    /**
     * GET /search?q=...&module=...
     *
     * Returns JSON grouped by module:
     * {
     *   "query": "juan",
     *   "modules": [
     *     { "key": "employees", "label": "Employees", "count": 3, "results": [ {...} ] },
     *     ...
     *   ]
     * }
     */
    public function index(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $moduleFilter = $request->query('module');

        // Cap query length before anything else touches it — an oversized
        // string offers no extra match value and only costs DB cycles
        // across every module's LIKE clause.
        if (mb_strlen($query) > self::MAX_QUERY_LENGTH) {
            $query = mb_substr($query, 0, self::MAX_QUERY_LENGTH);
        }

        if ($query === '' || mb_strlen($query) < self::MIN_QUERY_LENGTH) {
            return response()->json([
                'query' => $query,
                'modules' => [],
            ]);
        }

        // Escape LIKE wildcard characters so the raw term is always matched
        // literally — otherwise "%" or "_" in user input broadens every
        // module query far beyond what the searcher typed.
        $query = $this->escapeLikeValue($query);

        $user = $request->user();
        $companyId = CompanyHelper::getCurrentCompanyId();

        if (! $user || ! $companyId) {
            return response()->json(['query' => $query, 'modules' => []]);
        }

        // Every module handler below MUST scope its results using
        // $this->visibleEmployeeIds() / role checks. Never trust the
        // client-provided module filter to bypass permissions - it is
        // only used to narrow which handlers run.
        $handlers = [
            'employees' => fn () => $this->searchEmployees($query, $user, $companyId),
            'attendance' => fn () => $this->searchAttendance($query, $user, $companyId),
            'schedules' => fn () => $this->searchSchedules($query, $user, $companyId),
            'leave' => fn () => $this->searchLeave($query, $user, $companyId),
            'overtime' => fn () => $this->searchOvertime($query, $user, $companyId),
            'official_business' => fn () => $this->searchOfficialBusiness($query, $user, $companyId),
            'payroll' => fn () => $this->searchPayroll($query, $user, $companyId),
            'payments' => fn () => $this->searchPayments($query, $user, $companyId),
            'departments' => fn () => $this->searchDepartments($query, $user, $companyId),
            'positions' => fn () => $this->searchPositions($query, $user, $companyId),
            'accounts' => fn () => $this->searchAccounts($query, $user, $companyId),
            'documents' => fn () => $this->searchDocuments($query, $user, $companyId),
        ];

        $modules = [];

        foreach ($handlers as $key => $handler) {
            if ($moduleFilter && $moduleFilter !== $key) {
                continue;
            }

            $results = $handler();

            if (empty($results)) {
                continue;
            }

            $modules[] = [
                'key' => $key,
                'label' => $this->moduleLabel($key),
                'count' => count($results),
                'results' => $results,
            ];
        }

        return response()->json([
            'query' => $query,
            'modules' => $modules,
        ]);
    }

    /**
     * Return the list of module keys/labels for the filter chips in the UI.
     */
    public function modules(Request $request)
    {
        return response()->json([
            'modules' => collect($this->moduleLabels())->map(fn ($label, $key) => [
                'key' => $key,
                'label' => $label,
            ])->values(),
        ]);
    }

    private function moduleLabel(string $key): string
    {
        return $this->moduleLabels()[$key] ?? ucfirst($key);
    }

    private function moduleLabels(): array
    {
        return [
            'employees' => 'Employees',
            'attendance' => 'Attendance',
            'schedules' => 'Schedules',
            'leave' => 'Leave Requests',
            'overtime' => 'Overtime Requests',
            'official_business' => 'Official Business',
            'payroll' => 'Payroll',
            'payments' => 'Payments',
            'departments' => 'Departments',
            'positions' => 'Positions',
            'accounts' => 'Accounts',
            'documents' => 'Documents',
        ];
    }

    /**
     * Escape MySQL LIKE wildcard characters so a raw user-typed term is
     * always matched literally inside "%...%" clauses. Backslash must be
     * escaped first, since it is itself the LIKE escape character.
     */
    private function escapeLikeValue(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    // -----------------------------------------------------------------
    // Access-control helpers
    // -----------------------------------------------------------------

    /**
     * Employee IDs the current user is allowed to see records for.
     * Returns null to mean "no restriction beyond company" (admin/hr).
     *
     * @return array<int, string>|null
     */
    private function visibleEmployeeIds($user, string $companyId): ?array
    {
        if (in_array($user->role, ['admin', 'hr'], true)) {
            return null; // company-wide, no per-employee restriction
        }

        if ($user->role === 'manager' && $user->employee_id) {
            $teamIds = Employee::forCompany($companyId)
                ->whereHas('department', fn ($q) => $q->where('manager_id', $user->employee_id))
                ->pluck('id')
                ->all();

            return array_values(array_unique(array_merge($teamIds, [$user->employee_id])));
        }

        // employee role (or anything unrecognised) -> self only
        return $user->employee_id ? [$user->employee_id] : [];
    }

    // -----------------------------------------------------------------
    // Module search handlers
    // -----------------------------------------------------------------

    private function searchEmployees(string $q, $user, string $companyId): array
    {
        $employeeIds = $this->visibleEmployeeIds($user, $companyId);

        $query = Employee::query()
            ->with(['department', 'position', 'account'])
            ->forCompany($companyId);

        if ($employeeIds !== null) {
            $query->whereIn('id', $employeeIds);
        }

        $query->where(function ($w) use ($q) {
            $w->where('first_name', 'like', "%{$q}%")
                ->orWhere('last_name', 'like', "%{$q}%")
                ->orWhere('employee_id', 'like', "%{$q}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$q}%"])
                ->orWhereHas('account', fn ($a) => $a->where('email', 'like', "%{$q}%"))
                ->orWhereHas('department', fn ($d) => $d->where('name', 'like', "%{$q}%"))
                ->orWhereHas('position', fn ($p) => $p->where('name', 'like', "%{$q}%"))
                ->orWhere('employee_status', 'like', "%{$q}%")
                ->orWhere('hire_date', 'like', "%{$q}%");
        });

        return $query->limit(self::PER_MODULE_LIMIT)->get()->map(function (Employee $e) {
            return [
                'id' => $e->id,
                'title' => $e->full_name,
                'subtitle' => trim(($e->position->name ?? '').' · '.($e->department->name ?? '')),
                'meta' => $e->employee_status,
                'url' => route('employees.show', $e->id).'?highlight='.$e->id,
                'icon' => 'fa-user',
            ];
        })->all();
    }

    private function searchAttendance(string $q, $user, string $companyId): array
    {
        $employeeIds = $this->visibleEmployeeIds($user, $companyId);

        $query = AttendanceRecord::query()
            ->with('employee')
            ->whereHas('employee', fn ($e) => $e->forCompany($companyId));

        if ($employeeIds !== null) {
            $query->whereIn('employee_id', $employeeIds);
        }

        $query->where(function ($w) use ($q) {
            $w->whereHas('employee', function ($e) use ($q) {
                $e->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('employee_id', 'like', "%{$q}%")
                    ->orWhereHas('account', fn ($a) => $a->where('email', 'like', "%{$q}%"))
                    ->orWhereHas('department', fn ($d) => $d->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('position', fn ($p) => $p->where('name', 'like', "%{$q}%"));
            })
                ->orWhere('status', 'like', "%{$q}%")
                ->orWhere('date', 'like', "%{$q}%");
        });

        return $query->latest('date')->limit(self::PER_MODULE_LIMIT)->get()->map(function (AttendanceRecord $r) {
            return [
                'id' => $r->id,
                'title' => $r->employee->full_name ?? 'Unknown employee',
                'subtitle' => optional($r->date)->format('M d, Y').' · '.ucfirst($r->status ?? ''),
                'meta' => $r->time_in ? 'In '.$r->time_in : null,
                // attendance.daily renders one row per employee (not per record),
                // so the highlight anchor must be the employee id.
                'url' => route('attendance.daily', ['date' => optional($r->date)->format('Y-m-d')]).'&highlight='.$r->employee_id,
                'icon' => 'fa-clock',
            ];
        })->all();
    }

    private function searchSchedules(string $q, $user, string $companyId): array
    {
        $employeeIds = $this->visibleEmployeeIds($user, $companyId);

        $query = EmployeeSchedule::query()
            ->with('employee')
            ->whereHas('employee', fn ($e) => $e->forCompany($companyId));

        if ($employeeIds !== null) {
            $query->whereIn('employee_id', $employeeIds);
        }

        $query->where(function ($w) use ($q) {
            $w->whereHas('employee', function ($e) use ($q) {
                $e->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('employee_id', 'like', "%{$q}%")
                    ->orWhereHas('account', fn ($a) => $a->where('email', 'like', "%{$q}%"))
                    ->orWhereHas('department', fn ($d) => $d->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('position', fn ($p) => $p->where('name', 'like', "%{$q}%"));
            })
                ->orWhere('status', 'like', "%{$q}%")
                ->orWhere('schedule_type', 'like', "%{$q}%")
                ->orWhere('date', 'like', "%{$q}%");
        });

        // Schedule Management (schedule-v2) is admin/hr/manager only; an
        // employee's own schedule lives on their "My Schedule" page instead.
        $isEmployee = $user->role === 'employee';

        return $query->latest('date')->limit(self::PER_MODULE_LIMIT)->get()->map(function (EmployeeSchedule $s) use ($isEmployee) {
            return [
                'id' => $s->id,
                'title' => ($s->employee->full_name ?? 'Unknown').' — Schedule',
                'subtitle' => optional($s->date)->format('M d, Y').' · '.($s->status ?? ''),
                'meta' => $s->time_in ? $s->time_in.' - '.$s->time_out : null,
                'url' => $isEmployee ? route('employee.schedule') : route('schedule-v2.show', $s->id),
                'icon' => 'fa-calendar-alt',
            ];
        })->all();
    }

    private function searchLeave(string $q, $user, string $companyId): array
    {
        $employeeIds = $this->visibleEmployeeIds($user, $companyId);

        $query = LeaveRequest::query()
            ->with('employee')
            ->whereHas('employee', fn ($e) => $e->forCompany($companyId));

        if ($employeeIds !== null) {
            $query->whereIn('employee_id', $employeeIds);
        }

        $query->where(function ($w) use ($q) {
            $w->whereHas('employee', function ($e) use ($q) {
                $e->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('employee_id', 'like', "%{$q}%")
                    ->orWhereHas('account', fn ($a) => $a->where('email', 'like', "%{$q}%"))
                    ->orWhereHas('department', fn ($d) => $d->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('position', fn ($p) => $p->where('name', 'like', "%{$q}%"));
            })
                ->orWhere('leave_type', 'like', "%{$q}%")
                ->orWhere('status', 'like', "%{$q}%")
                ->orWhere('reason', 'like', "%{$q}%")
                ->orWhere('start_date', 'like', "%{$q}%")
                ->orWhere('end_date', 'like', "%{$q}%");
        });

        return $query->latest('created_at')->limit(self::PER_MODULE_LIMIT)->get()->map(function (LeaveRequest $r) {
            return [
                'id' => $r->id,
                'title' => ($r->employee->full_name ?? 'Unknown').' — '.ucfirst($r->leave_type ?? 'Leave'),
                'subtitle' => optional($r->start_date)->format('M d').' – '.optional($r->end_date)->format('M d, Y'),
                'meta' => ucfirst($r->status ?? ''),
                'url' => route('attendance.leave-management').'?highlight='.$r->id,
                'icon' => 'fa-calendar-minus',
            ];
        })->all();
    }

    private function searchOvertime(string $q, $user, string $companyId): array
    {
        $employeeIds = $this->visibleEmployeeIds($user, $companyId);

        $query = OvertimeRequest::query()
            ->with('employee')
            ->whereHas('employee', fn ($e) => $e->forCompany($companyId));

        if ($employeeIds !== null) {
            $query->whereIn('employee_id', $employeeIds);
        }

        $query->where(function ($w) use ($q) {
            $w->whereHas('employee', function ($e) use ($q) {
                $e->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('employee_id', 'like', "%{$q}%")
                    ->orWhereHas('account', fn ($a) => $a->where('email', 'like', "%{$q}%"))
                    ->orWhereHas('department', fn ($d) => $d->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('position', fn ($p) => $p->where('name', 'like', "%{$q}%"));
            })
                ->orWhere('status', 'like', "%{$q}%")
                ->orWhere('reason', 'like', "%{$q}%")
                ->orWhere('date', 'like', "%{$q}%");
        });

        return $query->latest('created_at')->limit(self::PER_MODULE_LIMIT)->get()->map(function (OvertimeRequest $r) {
            return [
                'id' => $r->id,
                'title' => ($r->employee->full_name ?? 'Unknown').' — Overtime',
                'subtitle' => optional($r->date)->format('M d, Y').' · '.$r->hours.' hrs',
                'meta' => ucfirst($r->status ?? ''),
                'url' => route('attendance.overtime').'?highlight='.$r->id,
                'icon' => 'fa-business-time',
            ];
        })->all();
    }

    private function searchOfficialBusiness(string $q, $user, string $companyId): array
    {
        $employeeIds = $this->visibleEmployeeIds($user, $companyId);

        $query = OfficialBusinessRequest::query()
            ->with('employee')
            ->whereHas('employee', fn ($e) => $e->forCompany($companyId));

        if ($employeeIds !== null) {
            $query->whereIn('employee_id', $employeeIds);
        }

        $query->where(function ($w) use ($q) {
            $w->whereHas('employee', function ($e) use ($q) {
                $e->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('employee_id', 'like', "%{$q}%")
                    ->orWhereHas('account', fn ($a) => $a->where('email', 'like', "%{$q}%"))
                    ->orWhereHas('department', fn ($d) => $d->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('position', fn ($p) => $p->where('name', 'like', "%{$q}%"));
            })
                ->orWhere('status', 'like', "%{$q}%")
                ->orWhere('reason', 'like', "%{$q}%")
                ->orWhere('date', 'like', "%{$q}%");
        });

        return $query->latest('created_at')->limit(self::PER_MODULE_LIMIT)->get()->map(function (OfficialBusinessRequest $r) {
            return [
                'id' => $r->id,
                'title' => ($r->employee->full_name ?? 'Unknown').' — Official Business',
                'subtitle' => optional($r->date)->format('M d, Y'),
                'meta' => ucfirst($r->status ?? ''),
                'url' => route('attendance.official-business').'?highlight='.$r->id,
                'icon' => 'fa-briefcase',
            ];
        })->all();
    }

    private function searchPayroll(string $q, $user, string $companyId): array
    {
        // Payroll & payments are financial records; keep this strictly
        // limited to admin/hr and the employee's own record — managers
        // do not get blanket access to team pay data via search.
        if (! in_array($user->role, ['admin', 'hr', 'employee'], true)) {
            return [];
        }

        $query = Payroll::query()->with('employee')->where('company_id', $companyId);

        if ($user->role === 'employee') {
            if (! $user->employee_id) {
                return [];
            }
            $query->where('employee_id', $user->employee_id);
        }

        $query->where(function ($w) use ($q) {
            $w->whereHas('employee', function ($e) use ($q) {
                $e->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('employee_id', 'like', "%{$q}%")
                    ->orWhereHas('account', fn ($a) => $a->where('email', 'like', "%{$q}%"));
            })
                ->orWhere('status', 'like', "%{$q}%")
                ->orWhere('pay_period_start', 'like', "%{$q}%")
                ->orWhere('pay_period_end', 'like', "%{$q}%");
        });

        return $query->latest('pay_period_end')->limit(self::PER_MODULE_LIMIT)->get()->map(function (Payroll $p) {
            return [
                'id' => $p->id,
                'title' => ($p->employee->full_name ?? 'Unknown').' — Payroll',
                'subtitle' => optional($p->pay_period_start)->format('M d').' – '.optional($p->pay_period_end)->format('M d, Y'),
                'meta' => ucfirst($p->status ?? ''),
                // payroll.show has no corresponding view on disk; the payroll.manage
                // list is the working page and already renders one row per payroll.
                'url' => route('payroll.manage').'?highlight='.$p->id,
                'icon' => 'fa-money-check-alt',
            ];
        })->all();
    }

    private function searchPayments(string $q, $user, string $companyId): array
    {
        if (! in_array($user->role, ['admin', 'hr', 'employee'], true)) {
            return [];
        }

        $query = Payment::query()
            ->with('employee')
            ->whereHas('employee', fn ($e) => $e->forCompany($companyId));

        if ($user->role === 'employee') {
            if (! $user->employee_id) {
                return [];
            }
            $query->where('employee_id', $user->employee_id);
        }

        $query->where(function ($w) use ($q) {
            $w->where('transaction_id', 'like', "%{$q}%")
                ->orWhere('payment_reference', 'like', "%{$q}%")
                ->orWhere('status', 'like', "%{$q}%")
                ->orWhereHas('employee', function ($e) use ($q) {
                    $e->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('employee_id', 'like', "%{$q}%")
                        ->orWhereHas('account', fn ($a) => $a->where('email', 'like', "%{$q}%"));
                });
        });

        return $query->latest('created_at')->limit(self::PER_MODULE_LIMIT)->get()->map(function (Payment $p) {
            return [
                'id' => $p->id,
                'title' => ($p->employee->full_name ?? 'Unknown').' — Payment',
                'subtitle' => $p->transaction_id ?? $p->payment_reference,
                'meta' => ucfirst($p->status ?? ''),
                // Payments have no dedicated row in the UI — anchor the
                // highlight on the parent payroll run they belong to.
                'url' => $p->payroll_id
                    ? route('payroll.manage').'?highlight='.$p->payroll_id
                    : route('payroll.manage'),
                'icon' => 'fa-receipt',
            ];
        })->all();
    }

    private function searchDepartments(string $q, $user, string $companyId): array
    {
        // General company info: visible to all authenticated roles,
        // but always scoped to the active company only.
        $query = Department::query()
            ->where('company_id', $companyId)
            ->whereNull('archived_at')
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('department_id', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            });

        return $query->limit(self::PER_MODULE_LIMIT)->get()->map(function (Department $d) {
            return [
                'id' => $d->id,
                'title' => $d->name,
                'subtitle' => $d->location,
                'meta' => $d->department_id,
                'url' => route('departments.show', $d->id).'?highlight='.$d->id,
                'icon' => 'fa-sitemap',
            ];
        })->all();
    }

    private function searchPositions(string $q, $user, string $companyId): array
    {
        $query = Position::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });

        return $query->limit(self::PER_MODULE_LIMIT)->get()->map(function (Position $p) {
            return [
                'id' => $p->id,
                'title' => $p->name,
                'subtitle' => $p->code,
                'meta' => $p->level,
                'url' => route('positions.show', $p->id).'?highlight='.$p->id,
                'icon' => 'fa-id-badge',
            ];
        })->all();
    }

    private function searchAccounts(string $q, $user, string $companyId): array
    {
        // Sensitive: only admin/hr may search accounts, and only within
        // the active company.
        if (! in_array($user->role, ['admin', 'hr'], true)) {
            return [];
        }

        $query = Account::query()
            ->with('employee')
            ->whereHas('employee', fn ($e) => $e->forCompany($companyId))
            ->where(function ($w) use ($q) {
                $w->where('email', 'like', "%{$q}%")
                    ->orWhere('role', 'like', "%{$q}%")
                    ->orWhereHas('employee', function ($e) use ($q) {
                        $e->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%");
                    });
            });

        return $query->limit(self::PER_MODULE_LIMIT)->get()->map(function (Account $a) {
            return [
                'id' => $a->id,
                'title' => $a->full_name,
                'subtitle' => $a->email,
                'meta' => ucfirst($a->role ?? ''),
                'url' => route('developer.accounts.edit', $a->id),
                'icon' => 'fa-user-shield',
            ];
        })->all();
    }

    private function searchDocuments(string $q, $user, string $companyId): array
    {
        $employeeIds = $this->visibleEmployeeIds($user, $companyId);

        // Non admin/hr users may only ever see their own documents,
        // regardless of "team" visibility used elsewhere.
        if (! in_array($user->role, ['admin', 'hr'], true)) {
            $employeeIds = $user->employee_id ? [$user->employee_id] : [];
        }

        $query = Document::query()
            ->with('employee')
            ->whereHas('employee', fn ($e) => $e->forCompany($companyId));

        if ($employeeIds !== null) {
            $query->whereIn('employee_id', $employeeIds);
        }

        $query->where(function ($w) use ($q) {
            $w->where('name', 'like', "%{$q}%")
                ->orWhere('type', 'like', "%{$q}%")
                ->orWhere('created_at', 'like', "%{$q}%")
                ->orWhereHas('employee', function ($e) use ($q) {
                    $e->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('employee_id', 'like', "%{$q}%")
                        ->orWhereHas('account', fn ($a) => $a->where('email', 'like', "%{$q}%"));
                });
        });

        return $query->latest('created_at')->limit(self::PER_MODULE_LIMIT)->get()->map(function (Document $d) {
            return [
                'id' => $d->id,
                'title' => $d->name,
                'subtitle' => $d->employee->full_name ?? null,
                'meta' => $d->type,
                // documents.index lists employees, not individual documents, so
                // there's no document-level row to highlight — go straight to
                // that employee's document page (controller reads employee_id
                // from the query string, not the {id} route segment).
                'url' => route('employees.documents', ['id' => $d->employee_id, 'employee_id' => $d->employee_id]),
                'icon' => 'fa-file-alt',
            ];
        })->all();
    }
}
