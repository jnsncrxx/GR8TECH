<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CompanyHelper;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LoanController extends Controller
{
    private function ensureLoanCompany(Loan $loan): void
    {
        abort_unless($loan->company_id === CompanyHelper::getCurrentCompanyId(), 404);
    }

    private function ensureEmployeeCompany(Employee $employee): void
    {
        abort_unless($employee->company_id === CompanyHelper::getCurrentCompanyId(), 404);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isLoanManager = in_array($user->role, ['admin', 'hr'], true);
        $isEmployeeView = $request->query('scope') === 'mine' || !$isLoanManager;
        $currentCompany = CompanyHelper::getCurrentCompany();

        $query = Loan::with(['employee.department', 'loanType', 'approvedBy'])
            ->when($currentCompany, fn ($q) => $q->forCompany($currentCompany->id))
            ->latest();

        if ($isEmployeeView) {
            if (!$user->employee) {
                abort(403, 'No employee record linked to your account.');
            }
            // Employees only ever see their own requests - never another
            // employee's loans, regardless of query params.
            $query->where('employee_id', $user->employee->id);
        } else {
            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }
            if ($request->filled('department_id')) {
                $query->whereHas('employee', fn ($eq) => $eq->where('department_id', $request->department_id));
            }
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $loans = $query->paginate(20)->withQueryString();

        $employees = collect();
        $departments = collect();
        $loanTypes = collect();
        if (!$isEmployeeView) {
            $employeesQuery = Employee::with('department')->orderBy('first_name');
            if ($currentCompany) {
                $employeesQuery->forCompany($currentCompany->id);
            }
            $employees = $employeesQuery->get();

            $departmentsQuery = Department::orderBy('name');
            if ($currentCompany) {
                $departmentsQuery->forCompany($currentCompany->id);
            }
            $departments = $departmentsQuery->get();

            $loanTypesQuery = \App\Models\LoanType::withCount('loans')->orderBy('name');
            if ($currentCompany) {
                $loanTypesQuery->forCompany($currentCompany->id);
            } else {
                $loanTypesQuery->whereNull('company_id');
            }
            $loanTypes = $loanTypesQuery->get();
        }

        return view('payroll.loans.index', [
            'loans' => $loans,
            'employees' => $employees,
            'departments' => $departments,
            'loanTypes' => $loanTypes,
            'currentFilters' => $request->only(['employee_id', 'status', 'department_id']),
            'isEmployeeView' => $isEmployeeView,
            'user' => $user,
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        if (!$user->employee) {
            abort(403, 'No employee record linked to your account.');
        }

        $currentCompany = CompanyHelper::getCurrentCompany();

        $loanTypesQuery = LoanType::active()->orderBy('name');
        if ($currentCompany) {
            $loanTypesQuery->forCompany($currentCompany->id);
        } else {
            $loanTypesQuery->whereNull('company_id');
        }

        return view('payroll.loans.create', [
            'loanTypes' => $loanTypesQuery->get(),
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->employee) {
            abort(403, 'No employee record linked to your account.');
        }

        $validated = $request->validate([
            'loan_type_id' => [
                'required',
                Rule::exists('loan_types', 'id')->where(
                    fn ($query) => $query->where('company_id', CompanyHelper::getCurrentCompanyId())
                ),
            ],
            'principal_amount' => ['required', 'numeric', 'min:1'],
            'term_months' => ['required', 'integer', 'min:1', 'max:60'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $loanType = LoanType::forCompany(CompanyHelper::getCurrentCompanyId())
            ->findOrFail($validated['loan_type_id']);
        $currentCompany = CompanyHelper::getCurrentCompany();

        $loan = new Loan([
            // The employee_id is never taken from the request body - always
            // the authenticated user's own employee record, so no one can
            // request a loan on someone else's behalf.
            'employee_id' => $user->employee->id,
            'loan_type_id' => $validated['loan_type_id'],
            'company_id' => $currentCompany?->id,
            'principal_amount' => $validated['principal_amount'],
            'interest_rate' => $loanType->default_interest_rate,
            'interest_type' => $loanType->interest_type,
            'term_months' => $validated['term_months'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'requested_by' => Auth::id(),
        ]);
        $loan->computeAmortization();
        $loan->save();

        return redirect()->route('loans.index', ['scope' => 'mine'])
            ->with('success', 'Loan request submitted for approval.');
    }

    public function show(Loan $loan)
    {
        $this->ensureLoanCompany($loan);
        $user = Auth::user();

        // Only HR/Admin may inspect another employee's loan. Every other
        // employee-linked role is restricted to its own record.
        if (!in_array($user->role, ['admin', 'hr'], true)
            && (!$user->employee || $loan->employee_id !== $user->employee->id)) {
            abort(403, 'You can only view your own loan requests.');
        }

        $loan->load(['employee.department', 'loanType', 'approvedBy', 'requestedBy', 'payments' => fn ($q) => $q->orderByDesc('payment_date')]);

        return view('payroll.loans.show', [
            'loan' => $loan,
            'user' => $user,
        ]);
    }

    /**
     * Employee loan history - all loans (any status) for one employee.
     */
    public function employeeHistory(Employee $employee)
    {
        $this->ensureEmployeeCompany($employee);
        $loans = Loan::with(['loanType', 'payments'])
            ->where('employee_id', $employee->id)
            ->latest()
            ->get();

        return view('payroll.loans.employee-history', [
            'employee' => $employee,
            'loans' => $loans,
            'user' => Auth::user(),
        ]);
    }

    public function approve(Request $request, Loan $loan)
    {
        $this->ensureLoanCompany($loan);
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loan requests can be approved.');
        }

        $loan->approve(Auth::id());

        return back()->with('success', "Loan approved. Deducting {$loan->amortization_amount}/cutoff over {$loan->term_months} months.");
    }

    public function reject(Request $request, Loan $loan)
    {
        $this->ensureLoanCompany($loan);
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loan requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $loan->reject($validated['rejection_reason']);

        return back()->with('success', 'Loan request rejected.');
    }

    public function destroy(Loan $loan)
    {
        $this->ensureLoanCompany($loan);
        if ($loan->status === 'approved' && $loan->payments()->exists()) {
            return back()->with('error', 'Cannot delete a loan that already has recorded payments. Cancel it instead.');
        }

        $loan->delete();

        return redirect()->route('loans.index')->with('success', 'Loan request deleted.');
    }
}
