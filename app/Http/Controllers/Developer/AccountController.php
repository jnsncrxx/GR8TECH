<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Helpers\ActivityLogger;
use App\Mail\PasswordResetMail;
use App\Models\Account;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::with('employee')->orderBy('created_at', 'desc')->get();
        $trashedAccounts = Account::onlyTrashed()->with('employee')->orderBy('deleted_at', 'desc')->get();
        $employees = Employee::doesntHave('account')->get();
        $user = auth()->user();
        $activeRoute = 'developer.accounts.index';

        $stats = [
            'total' => $accounts->count(),
            'admin' => $accounts->where('role', 'admin')->count(),
            'hr' => $accounts->where('role', 'hr')->count(),
            'manager' => $accounts->where('role', 'manager')->count(),
            'employee' => $accounts->where('role', 'employee')->count(),
        ];

        return view('developer.accounts.index', compact('accounts', 'trashedAccounts', 'employees', 'user', 'activeRoute', 'stats'));
    }

    /**
     * Only admins may manage other admin accounts. HR is blocked from
     * creating, editing, deleting, or (de)activating accounts with the
     * 'admin' role, and from promoting any account to 'admin'.
     */
    protected function assertCanManageRole(string $targetRole): void
    {
        if ($targetRole === 'admin' && auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can manage admin accounts.');
        }
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

        $this->assertCanManageRole($request->role);

        $account = Account::create([
            'employee_id' => $request->employee_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        ActivityLogger::log('create', 'Account', "Created account {$account->email} with role {$account->role}.");

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

        // Block HR from touching existing admin accounts or promoting anyone to admin.
        $this->assertCanManageRole($account->role);
        $this->assertCanManageRole($request->role);

        $data = [
            'employee_id' => $request->employee_id,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $account->update($data);

        ActivityLogger::log('update', 'Account', "Updated account {$account->email} (role: {$account->role}).");

        return redirect()->route('developer.accounts.index')->with('success', 'Account updated successfully!');
    }

    public function destroy(Account $account)
    {
        if ($account->id === auth()->id()) {
            return redirect()->route('developer.accounts.index')->with('error', 'You cannot delete your own account!');
        }

        $this->assertCanManageRole($account->role);

        $account->delete();

        ActivityLogger::log('delete', 'Account', "Deleted account {$account->email}.");

        return redirect()->route('developer.accounts.index')->with('success', 'Account deleted successfully!');
    }

    /**
     * Restore a soft-deleted account.
     */
    public function restore(string $id)
    {
        $account = Account::onlyTrashed()->findOrFail($id);

        $this->assertCanManageRole($account->role);

        $account->restore();

        ActivityLogger::log('restore', 'Account', "Restored account {$account->email}.");

        return redirect()->route('developer.accounts.index')->with('success', "Account {$account->email} restored successfully!");
    }

    /* Activate or deactivate an account (account status management). */
    public function toggleStatus(Account $account)
    {
        if ($account->id === auth()->id()) {
            return redirect()->route('developer.accounts.index')->with('error', 'You cannot deactivate your own account!');
        }

        $this->assertCanManageRole($account->role);

        $account->update(['is_active' => ! $account->is_active]);

        $status = $account->is_active ? 'activated' : 'deactivated';
        ActivityLogger::log('update', 'Account', "Account {$account->email} {$status}.");

        return redirect()->route('developer.accounts.index')->with('success', "Account {$status} successfully!");
    }

    /**
     * Admin/HR-triggered password reset: generates a reset token and emails
     * the account holder a reset link (password/reset handling).
     */
    public function sendResetLink(Account $account)
    {
        $this->assertCanManageRole($account->role);

        if (! $account->is_active) {
            return redirect()->route('developer.accounts.index')->with('error', 'Cannot send a reset link to an inactive account.');
        }

        $resetToken = Str::random(60);

        $account->update([
            'password_reset_token' => $resetToken,
            'password_reset_expires_at' => now()->addHours(1),
        ]);

        $employeeName = $account->employee
            ? trim($account->employee->first_name . ' ' . $account->employee->last_name)
            : 'User';

        try {
            Mail::to($account->email)->send(new PasswordResetMail($resetToken, $employeeName));
        } catch (\Exception $e) {
            \Log::error('Failed to send admin-triggered password reset email: ' . $e->getMessage());
            return redirect()->route('developer.accounts.index')->with('error', 'Failed to send reset email. Please try again later.');
        }

        ActivityLogger::log('update', 'Account', "Password reset link sent to {$account->email}.");

        return redirect()->route('developer.accounts.index')->with('success', "Password reset link sent to {$account->email}.");
    }

    /**
     * Show the dedicated page for linking a single user account to a
     * single employee record (and unlinking existing pairs).
     */
    public function linkForm()
    {
        $unlinkedAccounts = Account::whereNull('employee_id')->orderBy('email')->get();
        $unlinkedEmployees = Employee::doesntHave('account')->orderBy('first_name')->get();
        $linkedAccounts = Account::whereNotNull('employee_id')->with('employee')->orderBy('email')->get();
        $user = auth()->user();
        $activeRoute = 'developer.accounts.link';

        return view('developer.accounts.link', compact('unlinkedAccounts', 'unlinkedEmployees', 'linkedAccounts', 'user', 'activeRoute'));
    }

    /**
     * Link one account to one employee record. Enforces the 1:1 constraint:
     * both the account and the employee must currently be unlinked.
     */
    public function linkStore(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $account = Account::findOrFail($request->account_id);

        if ($account->employee_id) {
            return back()->with('error', 'That account is already linked to an employee.');
        }

        if (Account::where('employee_id', $request->employee_id)->exists()) {
            return back()->with('error', 'That employee is already linked to another account.');
        }

        $account->update(['employee_id' => $request->employee_id]);

        $employee = Employee::find($request->employee_id);
        ActivityLogger::log('update', 'Account', "Linked account {$account->email} to employee {$employee?->full_name}.");

        return redirect()->route('developer.accounts.link')->with('success', 'Account linked to employee successfully!');
    }

    /**
     * Unlink an account from its employee record.
     */
    public function unlink(Account $account)
    {
        $employeeName = $account->employee->full_name ?? 'N/A';

        $account->update(['employee_id' => null]);

        ActivityLogger::log('update', 'Account', "Unlinked account {$account->email} from employee {$employeeName}.");

        return redirect()->route('developer.accounts.link')->with('success', 'Account unlinked from employee.');
    }

    /**
     * Read-only Role & Permission Mapping matrix: shows which modules each
     * role (Employee, Manager, HR, Admin) can access, mirroring the
     * `role:` route middleware enforced across the app.
     */
    public function permissions()
    {
        $roles = ['admin', 'hr', 'manager', 'employee'];

        $modules = [
            'User Account Management' => ['admin', 'hr'],
            'Role & Permission Mapping' => ['admin'],
            'Activity Logs' => ['admin'],
            'Employee Records' => ['admin', 'hr'],
            'Departments' => ['admin', 'hr'],
            'Payroll Processing' => ['admin', 'hr'],
            'Payroll Period Lock/Unlock' => ['admin'],
            'Attendance & Schedule (Own)' => ['admin', 'hr', 'manager', 'employee'],
            'Attendance & Schedule (Approvals)' => ['admin', 'hr', 'manager'],
            'Overtime Approvals' => ['admin', 'hr', 'manager'],
            'Leave Approvals' => ['admin', 'hr', 'manager'],
            'Reports' => ['admin', 'hr', 'manager'],
            'Notifications (Team-wide)' => ['admin', 'hr', 'manager'],
        ];

        $user = auth()->user();
        $activeRoute = 'developer.permissions.index';

        return view('developer.accounts.permissions', compact('roles', 'modules', 'user', 'activeRoute'));
    }
}
