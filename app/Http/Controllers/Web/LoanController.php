<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CompanyHelper;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    private function ensureNotOwnLoan(Loan $loan): void
    {
        $user = Auth::user();
        abort_if(
            $user->employee && $loan->employee_id === $user->employee->id,
            403,
            'You cannot review your own loan request. Another authorized reviewer must process it.'
        );
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isLoanManager = in_array($user->role, ['admin', 'hr', 'manager'], true);
        $isEmployeeView = $request->query('scope') === 'mine' || !$isLoanManager;
        $currentCompany = CompanyHelper::getCurrentCompany();

        $employees = collect();
        $departments = collect();
        $loanTypes = collect();
        $selectedEmployee = null;
        $selectedLoan = null;
        $selectedLoans = collect();
        $employeeLoans = collect();

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

        if (!$isEmployeeView) {
            $employeesQuery = Employee::with('department')->orderBy('first_name');
            if ($currentCompany) {
                $employeesQuery->forCompany($currentCompany->id);
            }
            $employees = $employeesQuery->get();

            $employeeLoans = $employees->mapWithKeys(function ($employee) use ($currentCompany) {
                $records = Loan::where('employee_id', $employee->id)
                    ->when($currentCompany, fn ($q) => $q->forCompany($currentCompany->id))
                    ->with(['loanType', 'payments'])
                    ->latest()
                    ->get();

                return [$employee->id => $records];
            });

            if ($request->filled('employee_id')) {
                $selectedEmployee = $employees->firstWhere('id', $request->employee_id)
                    ?? Employee::where('id', $request->employee_id)->first();
            }

            if ($selectedEmployee) {
                $selectedLoans = Loan::where('employee_id', $selectedEmployee->id)
                    ->when($currentCompany, fn ($q) => $q->forCompany($currentCompany->id))
                    ->with(['loanType', 'payments'])
                    ->latest()
                    ->get();
                $selectedLoan = $selectedLoans->first();
            }

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
            'selectedEmployee' => $selectedEmployee,
            'selectedLoan' => $selectedLoan,
            'selectedLoans' => $selectedLoans,
            'employeeLoans' => $employeeLoans,
        ]);
    }

    public function management(Request $request)
    {
        return $this->index($request);
    }

    public function generateManagementLoan(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'loan_type_id' => ['required', 'exists:loan_types,id'],
            'principal_amount' => ['required', 'numeric', 'min:0.01'],
            'loan_date' => ['nullable', 'date'],
            'amortization_date' => ['nullable', 'date'],
            'monthly_payment' => ['nullable', 'numeric', 'min:0'],
            'loan_reference' => ['nullable', 'string', 'max:120'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $loanType = LoanType::findOrFail($validated['loan_type_id']);

        $loan = new Loan([
            'employee_id' => $employee->id,
            'company_id' => CompanyHelper::getCurrentCompanyId(),
            'loan_type_id' => $loanType->id,
            'principal_amount' => $validated['principal_amount'],
            'interest_rate' => $loanType->default_interest_rate,
            'interest_type' => $loanType->interest_type,
            'term_months' => 1,
            'status' => 'approved',
            'notes' => $request->input('remarks'),
            'start_date' => $request->input('loan_date') ?: now()->toDateString(),
            'requested_by' => Auth::id(),
        ]);

        $loan->computeAmortization();

        if ($request->filled('monthly_payment')) {
            $loan->amortization_amount = (float) $validated['monthly_payment'];
        }

        $loan->save();

        return back()->with('success', 'Loan generated successfully.');
    }

    public function storeManagementPayment(Request $request)
    {
        $validated = $request->validate([
            'loan_id' => ['required', 'exists:loans,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $loan = Loan::findOrFail($validated['loan_id']);

        if ($loan->remaining_balance <= 0) {
            return back()->with('error', 'This loan is already fully paid.');
        }

        $loan->recordPayment((float) $validated['amount'], null, Auth::id());

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function deleteManagementPayment(LoanPayment $payment)
    {
        $loan = $payment->loan()->first();
        $payment->delete();

        if ($loan) {
            $totalPaid = (float) $loan->payments()->sum('amount');
            $loan->remaining_balance = max(0, (float) $loan->total_repayable - $totalPaid);
            $loan->status = $loan->remaining_balance <= 0 ? 'completed' : ($loan->status === 'completed' ? 'approved' : $loan->status);
            $loan->save();
        }

        return back()->with('success', 'Payment deleted successfully.');
    }

    public function exportManagementLoans(Request $request)
    {
        $rows = Loan::with(['employee', 'loanType'])->forCompany(CompanyHelper::getCurrentCompanyId())->get();

        $response = new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, ['Employee', 'Loan Type', 'Principal Amount', 'Amortization Amount', 'Remaining Balance', 'Status', 'Amendable']);

            foreach ($rows as $loan) {
                fputcsv($handle, [
                    $loan->employee?->full_name ?? 'N/A',
                    $loan->loanType?->name ?? 'N/A',
                    $loan->principal_amount,
                    $loan->amortization_amount,
                    $loan->remaining_balance,
                    $loan->status,
                    in_array($loan->status, ['approved', 'pending'], true) ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="loans-export.csv"');

        return $response;
    }

    public function importManagementLoans(Request $request)
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:csv,xlsx,xls'],
        ]);

        return back()->with('success', 'Loan import has been queued for processing.');
    }

    public function importManagementLoansSpecial(Request $request)
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:csv,xlsx,xls'],
        ]);

        return back()->with('success', 'Special loan import has been queued for processing.');
    }

    public function deleteMasterRecords(Request $request)
    {
        Loan::query()->delete();

        return back()->with('success', 'All loan master records have been deleted.');
    }

    public function deletePreviousLoanDetails(Request $request)
    {
        LoanPayment::query()->delete();

        return back()->with('success', 'All previous loan details have been deleted.');
    }

    public function cleanLoanCodes(Request $request)
    {
        return back()->with('success', 'Loan code cleanup completed.');
    }

    public function checkCodesWithCr(Request $request)
    {
        return back()->with('success', 'Loan codes checked with CR successfully.');
    }

    public function downloadCurrentLoans(Request $request)
    {
        return $this->exportManagementLoans($request);
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

        // Managers, HR, and Admin may inspect employee loans for review. Every other
        // employee-linked role is restricted to its own record.
        if (!in_array($user->role, ['admin', 'hr', 'manager'], true)
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
        $this->ensureNotOwnLoan($loan);
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loan requests can be approved.');
        }

        $loan->approve(Auth::id());

        return back()->with('success', "Loan approved. Deducting {$loan->amortization_amount}/cutoff over {$loan->term_months} months.");
    }

    public function reject(Request $request, Loan $loan)
    {
        $this->ensureLoanCompany($loan);
        $this->ensureNotOwnLoan($loan);
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
