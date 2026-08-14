<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Helpers\CompanyHelper;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::orderBy('name')->paginate(15);

        return view('companies.index', [
            'user' => Auth::user(),
            'companies' => $companies,
        ]);
    }

    public function create(Request $request)
    {
        return view('companies.create', ['user' => Auth::user()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:companies,code'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'payroll_frequency' => ['nullable', 'in:weekly,semi_monthly,monthly'],
            'cutoff_day_1' => ['nullable', 'integer', 'min:1', 'max:31'],
            'cutoff_day_2' => ['nullable', 'integer', 'min:1', 'max:31'],
            'payroll_release_offset' => ['nullable', 'integer', 'min:0', 'max:31'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $company = Company::create($validated);

        if (!CompanyHelper::hasCompany()) {
            CompanyHelper::setCurrentCompany($company);
        }

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        return view('companies.show', ['company' => $company, 'user' => Auth::user()]);
    }

    public function edit(Company $company)
    {
        return view('companies.edit', ['company' => $company, 'user' => Auth::user()]);
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'payroll_frequency' => ['required', 'in:weekly,semi_monthly,monthly'],
            'cutoff_day_1' => ['required', 'integer', 'min:1', 'max:31'],
            'cutoff_day_2' => ['required', 'integer', 'min:1', 'max:31', 'different:cutoff_day_1'],
            'payroll_release_offset' => ['required', 'integer', 'min:0', 'max:31'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $company->update($validated);

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        return response()->json(['message' => 'Delete not yet implemented'], 501);
    }

    public function switchCompany(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['required', 'uuid', 'exists:companies,id'],
        ]);

        $company = Company::query()
            ->whereKey($validated['company_id'])
            ->where('is_active', true)
            ->first();

        if (!$company) {
            return back()->with('error', 'The selected company is inactive or unavailable.');
        }

        CompanyHelper::setCurrentCompany($company);

        return back()->with('success', 'Switched to '.$company->name.'.');
    }
}
