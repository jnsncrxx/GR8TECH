<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::with('employee')->orderBy('created_at', 'desc')->get();
        $employees = Employee::doesntHave('account')->get();
        $user = auth()->user();
        $activeRoute = 'developer.accounts.index';
        
        return view('developer.accounts.index', compact('accounts', 'employees', 'user', 'activeRoute'));
    }

    public function edit(Account $account)
    {
        $user = auth()->user();
        $employees = Employee::all();
        $activeRoute = 'developer.accounts.edit';
        
        return view('developer.accounts.edit', compact('account', 'user', 'employees', 'activeRoute'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['nullable', 'exists:employees,id', Rule::unique('accounts', 'employee_id')],
            'email' => 'required|email|unique:accounts,email',
            'role' => 'required|in:admin,hr,manager,employee',
            'password' => 'required|min:8',
        ]);

        Account::create([
            'employee_id' => $request->employee_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('developer.accounts.index')->with('success', 'Account created successfully!');
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'employee_id' => [
                'nullable',
                'exists:employees,id',
                Rule::unique('accounts', 'employee_id')->ignore($account->id),
            ],
            'email' => ['required', 'email', Rule::unique('accounts')->ignore($account->id)],
            'role' => 'required|in:admin,hr,manager,employee',
            'password' => 'nullable|min:8',
        ]);

        $data = [
            'employee_id' => $request->employee_id,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $account->update($data);

        return redirect()->route('developer.accounts.index')->with('success', 'Account updated successfully!');
    }

    public function destroy(Account $account)
    {
        if ($account->id === auth()->id()) {
            return redirect()->route('developer.accounts.index')->with('error', 'You cannot delete your own account!');
        }

        $account->delete();
        return redirect()->route('developer.accounts.index')->with('success', 'Account deleted successfully!');
    }
}
