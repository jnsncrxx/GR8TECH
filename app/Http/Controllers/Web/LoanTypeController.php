<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CompanyHelper;
use App\Http\Controllers\Controller;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanTypeController extends Controller
{
    public function index(Request $request)
    {
        $currentCompany = CompanyHelper::getCurrentCompany();

        $loanTypes = LoanType::query()
            ->when($currentCompany, fn ($query) => $query->forCompany($currentCompany->id))
            ->when(!$currentCompany, fn ($query) => $query->whereNull('company_id'))
            ->withCount('loans')
            ->orderBy('name')
            ->get();

        return view('payroll.loan-types.index', [
            'loanTypes' => $loanTypes,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        return view('payroll.loan-types.create', ['user' => Auth::user()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $currentCompany = CompanyHelper::getCurrentCompany();

        LoanType::create([
            ...$validated,
            'company_id' => $currentCompany?->id,
        ]);

        return redirect()->route('loans.index')
            ->with('loan_type_success', "Loan type \"{$validated['name']}\" created successfully.")
            ->with('open_loan_types_modal', true);
    }

    public function edit(LoanType $loanType)
    {
        return view('payroll.loan-types.edit', [
            'loanType' => $loanType,
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, LoanType $loanType)
    {
        $validated = $this->validated($request);
        $loanType->update($validated);

        return redirect()->route('loan-types.index')
            ->with('success', "Loan type \"{$validated['name']}\" updated successfully.");
    }

    public function destroy(LoanType $loanType)
    {
        if ($loanType->loans()->exists()) {
            return redirect()->route('loans.index')
                ->with('loan_type_error', "Cannot delete \"{$loanType->name}\" - it has loans associated with it. Deactivate it instead.")
                ->with('open_loan_types_modal', true);
        }

        $name = $loanType->name;
        $loanType->delete();

        return redirect()->route('loans.index')
            ->with('loan_type_success', "Loan type \"{$name}\" deleted successfully.")
            ->with('open_loan_types_modal', true);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'default_interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'interest_type' => ['required', 'in:flat,diminishing'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active', true)];
    }
}