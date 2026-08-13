@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'loans.index'])

@section('title', $isEmployeeView ? 'My Loans' : 'Loan Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $isEmployeeView ? 'My Loans' : 'Loan Management' }}</h1>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $isEmployeeView ? 'Your loan requests and repayment status' : 'Employee loans, repayment schedules, and approvals' }}
                </p>
            </div>
            @if($isEmployeeView)
            <a href="{{ route('loans.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i>New Loan Request
            </a>
            @elseif(in_array($user->role, ['admin', 'hr'], true))
            <button type="button" onclick="openLoanTypesModal()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fas fa-sliders mr-2"></i>Manage Loan Types
            </button>
            @endif
        </div>

        @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('loans.index') }}" class="grid grid-cols-1 {{ $isEmployeeView ? 'sm:grid-cols-2' : 'sm:grid-cols-4' }} gap-4">
                @if($isEmployeeView)
                <input type="hidden" name="scope" value="mine">
                @endif
                @unless($isEmployeeView)
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Employee</label>
                    <select name="employee_id" class="w-full text-sm rounded-lg border-gray-300">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ ($currentFilters['employee_id'] ?? '') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Department</label>
                    <select name="department_id" class="w-full text-sm rounded-lg border-gray-300">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ ($currentFilters['department_id'] ?? '') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endunless
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full text-sm rounded-lg border-gray-300">
                        <option value="all">All Statuses</option>
                        @foreach(['pending', 'approved', 'rejected', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}" {{ ($currentFilters['status'] ?? '') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($loans->isEmpty())
            <div class="text-center py-16">
                <i class="fas fa-hand-holding-dollar text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No loan requests found.</p>
            </div>
            @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loan Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Principal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Per Cutoff</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($loans as $loan)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $loan->employee->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $loan->employee->department->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $loan->loanType->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">₱{{ number_format((float) $loan->principal_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $loan->status === 'pending' ? '—' : '₱' . number_format((float) $loan->amortization_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $loan->status === 'pending' ? '—' : '₱' . number_format((float) $loan->remaining_balance, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ match($loan->status) {
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'approved' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'rejected', 'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('loans.show', $isEmployeeView ? ['loan' => $loan, 'scope' => 'mine'] : ['loan' => $loan]) }}" class="ui-icon-action ui-action-view" title="View loan" aria-label="View loan">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(!$isEmployeeView && $loan->status === 'pending' && (!$user->employee || $loan->employee_id !== $user->employee->id))
                            <form action="{{ route('loans.approve', $loan) }}" method="POST" class="inline loan-approval-form"
                                  data-employee="{{ $loan->employee->full_name }}" data-deduction="₱{{ number_format((float) $loan->amortization_amount, 2) }}">
                                @csrf
                                <button type="submit" class="ui-icon-action ui-action-approve" title="Approve loan" aria-label="Approve loan"><i class="fas fa-check"></i></button>
                            </form>
                            @elseif(!$isEmployeeView && $loan->status === 'pending')
                            <span class="ui-icon-action cursor-not-allowed border-gray-200 bg-gray-50 text-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-500" title="Another reviewer must process your request"><i class="fas fa-user-shield"></i></span>
                            @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-200">{{ $loans->links() }}</div>
            @endif
        </div>
    </div>
</div>

@unless($isEmployeeView)
<div id="loanApproveModal" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" onclick="if(event.target === this) closeLoanApproveModal()">
    <div class="w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-neutral-700"><div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-600 dark:bg-green-950/50 dark:text-green-300"><i class="fas fa-check"></i></span><div><h3 class="text-lg font-semibold text-gray-900 dark:text-white">Approve Loan Request</h3><p class="text-xs text-gray-500 dark:text-neutral-300">Confirm the payroll deduction.</p></div></div><button type="button" onclick="closeLoanApproveModal()" class="ui-icon-action ui-action-cancel" aria-label="Close"><i class="fas fa-times"></i></button></div>
        <div class="space-y-4 p-6"><p class="text-sm text-gray-600 dark:text-neutral-200">Approve <strong id="approveLoanEmployee" class="text-gray-900 dark:text-white"></strong>'s loan request?</p><div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950/40"><p class="text-xs text-green-700 dark:text-green-300">Deduction per cutoff</p><p id="approveLoanDeduction" class="mt-1 text-lg font-semibold text-green-800 dark:text-green-200"></p></div><p class="text-xs text-gray-500 dark:text-neutral-300">The request must be processed by someone other than the requesting employee.</p></div>
        <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-900/40"><button type="button" onclick="closeLoanApproveModal()" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">Cancel</button><button type="button" onclick="submitLoanApproval()" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700"><i class="fas fa-check"></i>Approve Loan</button></div>
    </div>
</div>
@endunless

@unless($isEmployeeView)
@if(in_array($user->role, ['admin', 'hr'], true))
<!-- Manage Loan Types Modal -->
<div id="loanTypesModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/60 p-4 overflow-y-auto backdrop-blur-sm" onclick="if(event.target === this) closeLoanTypesModal()">
    <div class="relative w-full max-w-4xl my-8 max-h-[calc(100vh-4rem)] overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-neutral-700 dark:bg-neutral-800">
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white px-6 py-4 dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300"><i class="fas fa-sliders"></i></span>
                <div><h3 class="text-lg font-semibold text-gray-900 dark:text-white">Loan Types</h3><p class="text-xs text-gray-500 dark:text-neutral-300">Configure the loan options available to employees.</p></div>
            </div>
            <button type="button" onclick="closeLoanTypesModal()" class="ui-icon-action ui-action-cancel" title="Close" aria-label="Close loan types modal">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">

        @if(session('loan_type_success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('loan_type_success') }}
        </div>
        @endif
        @if(session('loan_type_error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            {{ session('loan_type_error') }}
        </div>
        @endif

        <div class="border border-gray-200 rounded-lg overflow-hidden mb-5">
            @if($loanTypes->isEmpty())
            <div class="text-center py-8 text-sm text-gray-500">No loan types yet — add one below.</div>
            @else
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Interest</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Loans</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($loanTypes as $type)
                    <tr>
                        <td class="px-4 py-2 text-gray-900">{{ $type->name }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ number_format((float) $type->default_interest_rate, 2) }}%</td>
                        <td class="px-4 py-2 text-gray-600">{{ ucfirst($type->interest_type) }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $type->loans_count }}</td>
                        <td class="px-4 py-2">
                            @if($type->is_active)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('loan-types.edit', $type) }}" class="ui-icon-action ui-action-edit" title="Edit loan type" aria-label="Edit loan type"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('loan-types.destroy', $type) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Delete loan type {{ $type->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ui-icon-action ui-action-delete" title="Delete loan type" aria-label="Delete loan type"><i class="fas fa-trash"></i></button>
                            </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div class="border-t border-gray-100 pt-4">
            <h4 class="text-sm font-semibold text-gray-800 mb-3">Add New Loan Type</h4>
            <form action="{{ route('loan-types.store') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Name</label>
                        <input type="text" name="name" required placeholder="e.g. Salary Loan"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Interest Rate (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="default_interest_rate" value="0" required
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Description (optional)</label>
                    <input type="text" name="description" class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Interest Type</label>
                        <select name="interest_type" class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="flat">Flat</option>
                            <option value="diminishing">Diminishing Balance</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 pb-2">
                        <input type="checkbox" name="is_active" value="1" checked id="modal_is_active" class="rounded border-gray-300 text-blue-600">
                        <label for="modal_is_active" class="text-sm text-gray-700">Active</label>
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:ring-offset-neutral-800">
                        <i class="fas fa-plus mr-2"></i>Add Loan Type
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>
@endif

<script>
    let pendingLoanApprovalForm = null;
    document.querySelectorAll('.loan-approval-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') return;
            event.preventDefault();
            pendingLoanApprovalForm = form;
            document.getElementById('approveLoanEmployee').textContent = form.dataset.employee;
            document.getElementById('approveLoanDeduction').textContent = form.dataset.deduction;
            const modal = document.getElementById('loanApproveModal');
            modal.classList.remove('hidden'); modal.classList.add('flex');
        });
    });
    function closeLoanApproveModal() {
        pendingLoanApprovalForm = null;
        const modal = document.getElementById('loanApproveModal');
        modal.classList.add('hidden'); modal.classList.remove('flex');
    }
    function submitLoanApproval() {
        if (!pendingLoanApprovalForm) return;
        pendingLoanApprovalForm.dataset.confirmed = 'true';
        pendingLoanApprovalForm.submit();
    }

    function openLoanTypesModal() {
        const modal = document.getElementById('loanTypesModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeLoanTypesModal() {
        const modal = document.getElementById('loanTypesModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    @if(session('open_loan_types_modal'))
    document.addEventListener('DOMContentLoaded', openLoanTypesModal);
    @endif
</script>
@endunless
@endsection
