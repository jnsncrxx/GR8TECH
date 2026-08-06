<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CompanyHelper;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PayrollAdjustment;
use App\Services\PayrollPeriodLockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $company = CompanyHelper::getCurrentCompany();
        $adjustments = PayrollAdjustment::query()
            ->forCompany($company?->id)
            ->with(['employee.department', 'creator'])
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('employee_id'), fn ($q) => $q->where('employee_id', $request->employee_id))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->latest('effective_from')
            ->paginate(20)
            ->withQueryString();

        return view('payroll.adjustments.index', [
            'user' => Auth::user(),
            'adjustments' => $adjustments,
            'employees' => $this->employees($company?->id),
        ]);
    }

    public function create()
    {
        $company = CompanyHelper::getCurrentCompany();
        return view('payroll.adjustments.form', [
            'user' => Auth::user(),
            'adjustment' => new PayrollAdjustment(),
            'employees' => $this->employees($company?->id),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $company = CompanyHelper::getCurrentCompany();
        $this->ensureEmployeeCompany($validated['employee_id'], $company?->id);
        $this->ensurePeriodIsEditable($validated['employee_id'], $validated['effective_from'], $validated['effective_to'] ?? null);

        PayrollAdjustment::create($validated + [
            'company_id' => $company?->id,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('payroll-adjustments.index')->with('success', 'Payroll adjustment created successfully.');
    }

    public function edit(PayrollAdjustment $payrollAdjustment)
    {
        $this->authorizeCompany($payrollAdjustment);
        $company = CompanyHelper::getCurrentCompany();
        return view('payroll.adjustments.form', [
            'user' => Auth::user(),
            'adjustment' => $payrollAdjustment,
            'employees' => $this->employees($company?->id),
        ]);
    }

    public function update(Request $request, PayrollAdjustment $payrollAdjustment)
    {
        $this->authorizeCompany($payrollAdjustment);
        $validated = $this->validated($request);
        $company = CompanyHelper::getCurrentCompany();
        $this->ensureEmployeeCompany($validated['employee_id'], $company?->id);
        $this->ensurePeriodIsEditable($payrollAdjustment->employee_id, $payrollAdjustment->effective_from?->toDateString(), $payrollAdjustment->effective_to?->toDateString());
        $this->ensurePeriodIsEditable($validated['employee_id'], $validated['effective_from'], $validated['effective_to'] ?? null);

        $payrollAdjustment->update($validated + ['updated_by' => Auth::id()]);

        return redirect()->route('payroll-adjustments.index')->with('success', 'Payroll adjustment updated successfully.');
    }

    public function destroy(PayrollAdjustment $payrollAdjustment)
    {
        $this->authorizeCompany($payrollAdjustment);
        $this->ensurePeriodIsEditable($payrollAdjustment->employee_id, $payrollAdjustment->effective_from?->toDateString(), $payrollAdjustment->effective_to?->toDateString());
        $payrollAdjustment->delete();
        return redirect()->route('payroll-adjustments.index')->with('success', 'Payroll adjustment deleted successfully.');
    }


            

    private function validated(Request $request): array
    {
        $category = $request->input('category');

        if (in_array($category, ['bonus', 'allowance'], true)) {
            $request->merge(['direction' => 'earning']);
        } elseif ($category === 'deduction') {
            $request->merge(['direction' => 'deduction']);
        }

        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:bonus,allowance,deduction,manual'],
            'direction' => ['required', 'in:earning,deduction'],
            'frequency' => ['required', 'in:one_time,recurring'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'is_taxable' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $data['is_taxable'] = $request->boolean('is_taxable');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($data['frequency'] === 'one_time') {
            $data['effective_to'] = $data['effective_from'];
        }

        return $data;
    }

    private function employees(?string $companyId)
    {
        return Employee::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->with('department')->orderBy('first_name')->orderBy('last_name')->get();
    }

    private function ensureEmployeeCompany(string $employeeId, ?string $companyId): void
    {
        abort_unless(Employee::whereKey($employeeId)->when($companyId, fn ($q) => $q->where('company_id', $companyId))->exists(), 403);
    }

    private function authorizeCompany(PayrollAdjustment $adjustment): void
    {
        $company = CompanyHelper::getCurrentCompany();
        abort_unless($adjustment->company_id === $company?->id, 403);
    }

    private function ensurePeriodIsEditable(string $employeeId, ?string $start, ?string $end): void
    {
        if (!$start) return;
        $lock = app(PayrollPeriodLockService::class);
        $cursor = \Carbon\Carbon::parse($start);
        $last = \Carbon\Carbon::parse($end ?: $start);
        while ($cursor->lte($last)) {
            if ($lock->isLockedForDate($employeeId, $cursor->toDateString())) {
                abort(422, 'This adjustment affects a locked payroll period and can no longer be modified.');
            }
            $cursor->addDay();
        }
    }
}
