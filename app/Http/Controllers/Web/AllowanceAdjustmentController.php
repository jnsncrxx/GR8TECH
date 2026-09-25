<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CompanyHelper;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PayrollAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllowanceAdjustmentController extends Controller
{
    public function index()
    {
        $company = CompanyHelper::getCurrentCompany();

        $employees = Employee::query()
            ->when($company?->id, fn ($query) => $query->where('company_id', $company->id))
            ->with('department')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('allowances.index', [
            'user' => Auth::user(),
            'employees' => $employees,
            'today' => now()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employees' => ['nullable', 'array'],
            'employees.*.type' => ['nullable', 'in:ot_adjustment,basic_adjustment'],
            'employees.*.amount' => ['nullable', 'numeric', 'min:0'],
            'employees.*.effective_date' => ['nullable', 'date'],
        ]);

        $company = CompanyHelper::getCurrentCompany();
        $applied = 0;

        foreach ($request->input('employees', []) as $employeeId => $payload) {
            $type = $payload['type'] ?? null;
            $amount = $payload['amount'] ?? null;
            $effectiveDate = $payload['effective_date'] ?? now()->toDateString();

            if (!in_array($type, ['ot_adjustment', 'basic_adjustment'], true) || !is_numeric($amount) || (float) $amount <= 0) {
                continue;
            }

            $employee = Employee::query()
                ->when($company?->id, fn ($query) => $query->where('company_id', $company->id))
                ->find($employeeId);

            if (! $employee) {
                continue;
            }

            PayrollAdjustment::create([
                'company_id' => $company?->id,
                'employee_id' => $employee->id,
                'name' => $type === 'ot_adjustment' ? 'OT Adjustment' : 'Basic Adjustment',
                'category' => 'allowance',
                'direction' => 'earning',
                'frequency' => 'one_time',
                'amount' => (float) $amount,
                'effective_from' => $effectiveDate,
                'effective_to' => $effectiveDate,
                'is_taxable' => true,
                'is_active' => true,
                'reason' => 'Bulk allowance adjustment applied from Allowances screen.',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $applied++;
        }

        return redirect()->route('allowances.index')
            ->with('success', $applied > 0 ? 'Allowance adjustments applied successfully.' : 'No valid allowance adjustments were selected.');
    }
}
