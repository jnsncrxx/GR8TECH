<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CompanyHelper;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PayrollAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeductionRegisterController extends Controller
{
    private const REGISTER_REASON = 'Manually recorded in the Deduction Register.';

    public function index(Request $request)
    {
        $company = CompanyHelper::getCurrentCompany();
        $employees = $this->employees($company?->id)->get();
        $selectedEmployee = $employees->firstWhere('id', $request->query('employee_id'));

        $deductions = $selectedEmployee
            ? PayrollAdjustment::query()
                ->forCompany($company?->id)
                ->where('employee_id', $selectedEmployee->id)
                ->where('category', 'deduction')
                ->where('direction', 'deduction')
                ->where('frequency', 'one_time')
                ->orderByDesc('effective_from')
                ->get()
            : collect();

        return view('payroll.deduction-register.index', [
            'user' => Auth::user(),
            'employees' => $employees,
            'selectedEmployee' => $selectedEmployee,
            'deductions' => $deductions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'deductions' => ['required', 'array', 'min:1'],
            'deductions.*.type' => ['required', 'string', 'max:255'],
            'deductions.*.date' => ['required', 'date'],
            'deductions.*.amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $company = CompanyHelper::getCurrentCompany();
        $employee = $this->employees($company?->id)->findOrFail($validated['employee_id']);

        DB::transaction(function () use ($validated, $company, $employee) {
            foreach ($validated['deductions'] as $deduction) {
                PayrollAdjustment::create([
                    'company_id' => $company?->id,
                    'employee_id' => $employee->id,
                    'name' => trim($deduction['type']),
                    'category' => 'deduction',
                    'direction' => 'deduction',
                    'frequency' => 'one_time',
                    'amount' => $deduction['amount'],
                    'effective_from' => $deduction['date'],
                    'effective_to' => $deduction['date'],
                    'is_taxable' => false,
                    'is_active' => true,
                    'reason' => self::REGISTER_REASON,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('deduction-register.index', ['employee_id' => $employee->id])
            ->with('success', 'Deduction records created successfully.');
    }

    public function update(Request $request, PayrollAdjustment $deduction)
    {
        $this->authorizeRegisterDeduction($deduction);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $deduction->update([
            'name' => trim($validated['type']),
            'amount' => $validated['amount'],
            'effective_from' => $validated['date'],
            'effective_to' => $validated['date'],
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('deduction-register.index', ['employee_id' => $deduction->employee_id])
            ->with('success', 'Deduction record updated successfully.');
    }

    public function destroy(PayrollAdjustment $deduction)
    {
        $this->authorizeRegisterDeduction($deduction);
        $employeeId = $deduction->employee_id;
        $deduction->delete();

        return redirect()->route('deduction-register.index', ['employee_id' => $employeeId])
            ->with('success', 'Deduction record deleted successfully.');
    }

    private function employees(?string $companyId)
    {
        return Employee::query()
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId), fn ($query) => $query->whereNull('company_id'))
            ->with(['department', 'position'])
            ->orderBy('first_name')
            ->orderBy('last_name');
    }

    private function authorizeRegisterDeduction(PayrollAdjustment $deduction): void
    {
        $company = CompanyHelper::getCurrentCompany();

        abort_unless(
            $deduction->company_id === $company?->id
            && $deduction->category === 'deduction'
            && $deduction->direction === 'deduction'
            && $deduction->frequency === 'one_time',
            404
        );
    }
}
