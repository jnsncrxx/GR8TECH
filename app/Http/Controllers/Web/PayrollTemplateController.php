<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PayrollTemplate;
use App\Helpers\CompanyHelper;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Auth;

class PayrollTemplateController extends Controller
{
    private function ensureCurrentCompany(PayrollTemplate $payrollTemplate): void
    {
        abort_unless($payrollTemplate->company_id === CompanyHelper::getCurrentCompanyId(), 404);
    }

    public function index(Request $request)
    {
        $currentCompany = CompanyHelper::getCurrentCompany();

        $query = PayrollTemplate::query();
        if ($currentCompany) {
            $query->where('company_id', $currentCompany->id);
        }

        if ($request->has('status') && $request->status === 'archived') {
            $query->onlyTrashed();
        }

        $templates = $query->latest()->paginate(15);
        $templates->appends($request->all());

        return view('payroll-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('payroll-templates.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'monthly_rate' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'night_differential_rate' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'sss' => 'nullable|numeric|min:0',
            'phic' => 'nullable|numeric|min:0',
            'hdmf' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $currentCompany = CompanyHelper::getCurrentCompany();
        if ($currentCompany) {
            $validated['company_id'] = $currentCompany->id;
        }

        PayrollTemplate::create($validated);

        return redirect()->route('payroll-templates.index')->with('success', 'Payroll template created successfully.');
    }

    public function edit(PayrollTemplate $payrollTemplate)
    {
        $this->ensureCurrentCompany($payrollTemplate);
        return view('payroll-templates.form', ['template' => $payrollTemplate]);
    }

    public function update(Request $request, PayrollTemplate $payrollTemplate)
    {
        $this->ensureCurrentCompany($payrollTemplate);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'monthly_rate' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'night_differential_rate' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'sss' => 'nullable|numeric|min:0',
            'phic' => 'nullable|numeric|min:0',
            'hdmf' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // In case checkbox is unchecked
        if (!$request->has('is_active')) {
            $validated['is_active'] = false;
        }

        $payrollTemplate->update($validated);

        return redirect()->route('payroll-templates.index')->with('success', 'Payroll template updated successfully.');
    }

    public function destroy(PayrollTemplate $payrollTemplate)
    {
        $this->ensureCurrentCompany($payrollTemplate);
        // Prevent deletion if in use
        if ($payrollTemplate->employees()->exists() || $payrollTemplate->positions()->exists()) {
            return redirect()->route('payroll-templates.index')->with('error', 'Cannot archive template because it is currently assigned to one or more employees or positions. Please reassign them first.');
        }

        $payrollTemplate->delete();

        ActivityLogger::log('delete', 'Payroll Template', "Archived payroll template \"{$payrollTemplate->name}\".");

        return redirect()->route('payroll-templates.index')->with('success', 'Payroll template archived successfully.');
    }

    public function restore($id)
    {
        $payrollTemplate = PayrollTemplate::onlyTrashed()
            ->where('company_id', CompanyHelper::getCurrentCompanyId())
            ->findOrFail($id);
        $payrollTemplate->restore();

        ActivityLogger::log('restore', 'Payroll Template', "Restored payroll template \"{$payrollTemplate->name}\".");

        return redirect()->route('payroll-templates.index')->with('success', 'Payroll template restored successfully.');
    }
}
