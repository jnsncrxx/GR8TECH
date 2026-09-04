<?php

namespace App\Http\Controllers;

use App\Helpers\CompanyHelper;
use App\Models\Department;
use App\Models\PayrollTemplate;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(Request $request): View
    {
        $companyId = CompanyHelper::getCurrentCompanyId();
        $status = $request->input('status', 'active');

        $baseQuery = Position::query();

        if ($companyId) {
            $baseQuery->where('company_id', $companyId);
        }

        $statsQuery = clone $baseQuery;

        $totalPositions = (clone $statsQuery)->count();
        $activePositions = (clone $statsQuery)->where('is_active', true)->count();
        $archivedPositions = (clone $statsQuery)->where('is_active', false)->count();
        $seniorPositions = (clone $statsQuery)
            ->where('is_active', true)
            ->where('level', 'Senior')
            ->count();

        $positionsQuery = $baseQuery
            ->with(['department', 'payrollTemplate'])
            ->withCount('employees');

        if ($status === 'archived') {
            $positionsQuery->where('is_active', false);
        } elseif ($status !== 'all') {
            $positionsQuery->where('is_active', true);
            $status = 'active';
        }

        $positions = $positionsQuery
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('positions.index', [
            'user' => Auth::user(),
            'positions' => $positions,
            'status' => $status,
            'totalPositions' => $totalPositions,
            'activePositions' => $activePositions,
            'archivedPositions' => $archivedPositions,
            'seniorPositions' => $seniorPositions,
        ]);
    }

    public function create(Request $request): View
    {
        $companyId = CompanyHelper::getCurrentCompanyId();

        return view('positions.form', [
            'user' => Auth::user(),
            'payrollTemplates' => PayrollTemplate::query()
                ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
                ->orderBy('name')
                ->get(),
            'departments' => Department::query()
                ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePosition($request);

        Position::create($this->payload($request, $validated));

        return redirect()
            ->route('positions.index')
            ->with('success', 'Position created successfully.');
    }

    public function show(Position $position): View
    {
        $this->ensurePositionBelongsToCurrentCompany($position);
        $position->load(['department', 'company', 'payrollTemplate', 'employees']);

        return view('positions.show', [
            'position' => $position,
            'user' => Auth::user(),
        ]);
    }

    public function edit(Position $position): View
    {
        $this->ensurePositionBelongsToCurrentCompany($position);
        $companyId = CompanyHelper::getCurrentCompanyId();

        return view('positions.form', [
            'position' => $position,
            'user' => Auth::user(),
            'payrollTemplates' => PayrollTemplate::query()
                ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
                ->orderBy('name')
                ->get(),
            'departments' => Department::query()
                ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $this->ensurePositionBelongsToCurrentCompany($position);
        $validated = $this->validatePosition($request, $position);

        $position->update($this->payload($request, $validated));

        return redirect()
            ->route('positions.index')
            ->with('success', 'Position updated successfully.');
    }

    /**
     * Archive the position instead of permanently deleting it.
     *
     * Existing employees remain assigned, but the position is removed
     * from active position selections.
     */
    public function destroy(Position $position): RedirectResponse
    {
        $this->ensurePositionBelongsToCurrentCompany($position);

        if (! $position->is_active) {
            return redirect()
                ->route('positions.index', ['status' => 'archived'])
                ->with('error', 'This position is already archived.');
        }

        $position->update([
            'is_active' => false,
        ]);

        $message = $position->employees()->exists()
            ? 'Position archived successfully. Existing employees remain assigned to this position.'
            : 'Position archived successfully.';

        return redirect()
            ->route('positions.index', ['status' => 'active'])
            ->with('success', $message);
    }

    /**
     * Restore an archived position.
     */
    public function restore(Position $position): RedirectResponse
    {
        $this->ensurePositionBelongsToCurrentCompany($position);

        if ($position->is_active) {
            return redirect()
                ->route('positions.index')
                ->with('error', 'This position is already active.');
        }

        $position->update([
            'is_active' => true,
        ]);

        return redirect()
            ->route('positions.index', ['status' => 'archived'])
            ->with('success', 'Position restored successfully and is now available for assignment.');
    }

    /**
     * Get positions filtered by department for AJAX dropdowns.
     */
    public function getByDepartment(Request $request)
    {
        $companyId = CompanyHelper::getCurrentCompanyId();
        $departmentId = $request->query('department_id');

        $query = Position::active();

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $positions = $query->orderBy('name')->get(['id', 'name', 'department_id']);

        return response()->json($positions);
    }

    private function validatePosition(Request $request, ?Position $position = null): array
    {
        $companyId = CompanyHelper::getCurrentCompanyId();

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('positions', 'code')
                    ->when($companyId, fn ($rule) => $rule->where('company_id', $companyId))
                    ->ignore($position?->id),
            ],
            'description' => ['nullable', 'string'],
            'level' => ['required', Rule::in(['Entry', 'Mid', 'Senior', 'Lead'])],
            'department_id' => [
                'required',
                Rule::exists('departments', 'id')
                    ->when($companyId, fn ($rule) => $rule->where('company_id', $companyId)),
            ],
            'payroll_template_id' => [
                'nullable',
                Rule::exists('payroll_templates', 'id')
                    ->when($companyId, fn ($rule) => $rule->where('company_id', $companyId)),
            ],
            'min_salary' => ['required', 'numeric', 'min:0'],
            'max_salary' => ['required', 'numeric', 'gte:min_salary'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['nullable', 'string', 'max:255'],
            'responsibilities' => ['nullable', 'array'],
            'responsibilities.*' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function payload(Request $request, array $validated): array
    {
        return [
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'department_id' => $validated['department_id'],
            'company_id' => CompanyHelper::getCurrentCompanyId(),
            'payroll_template_id' => $validated['payroll_template_id'] ?? null,
            'min_salary' => $validated['min_salary'],
            'max_salary' => $validated['max_salary'],
            'is_active' => $request->boolean('is_active'),
            'requirements' => $this->cleanList($validated['requirements'] ?? []),
            'responsibilities' => $this->cleanList($validated['responsibilities'] ?? []),
        ];
    }

    private function cleanList(array $items): array
    {
        return array_values(array_filter(
            array_map(
                static fn ($item) => is_string($item) ? trim($item) : $item,
                $items
            ),
            static fn ($item) => $item !== null && $item !== ''
        ));
    }

    private function ensurePositionBelongsToCurrentCompany(Position $position): void
    {
        $companyId = CompanyHelper::getCurrentCompanyId();

        abort_if($companyId && $position->company_id !== $companyId, 404);
    }
}
