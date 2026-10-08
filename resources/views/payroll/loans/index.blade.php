@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'loans.index'])

@section('title', $isEmployeeView ? 'My Loans' : 'Loan Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $isEmployeeView ? 'My Loans' : 'Loan Management' }}</h1>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $isEmployeeView ? 'Your loan requests and repayment status' : 'Employee loans, payment tracking, generation, and utilities' }}
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

        @if(!$isEmployeeView)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <nav class="flex flex-wrap gap-2" aria-label="Loan Management tabs">
                        @foreach(['Edit', 'Utilities'] as $tab)
                            <button type="button" data-tab="{{ strtolower(str_replace(' ', '-', $tab)) }}" class="loan-tab rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ $loop->first ? 'bg-blue-600 text-white hover:bg-blue-700' : 'text-gray-700 hover:bg-transparent' }}">
                                {{ $tab }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                <div id="tab-edit" class="loan-panel p-5">
                    <section class="mb-6 rounded-lg border border-gray-200 bg-white shadow-sm" aria-labelledby="loan-employee-selection-heading">
                        <div class="border-b border-gray-200 px-5 py-4">
                            <h2 id="loan-employee-selection-heading" class="text-base font-semibold text-gray-900">Select Employee</h2>
                        </div>
                        <div class="grid gap-5 p-5 lg:grid-cols-[minmax(0,1fr)_minmax(18rem,0.8fr)]">
                            <div>
                                <label for="employeeSearch" class="mb-1.5 block text-sm font-medium text-gray-700">Search by employee name</label>
                                <div class="relative mb-3">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400" aria-hidden="true"></i>
                                    <input id="employeeSearch" type="search" placeholder="Search employees" autocomplete="off"
                                        class="w-full rounded-lg border-gray-300 py-2.5 pl-9 text-sm focus:border-gray-400 focus:ring-0">
                                </div>
                                <div id="employeeList" class="max-h-64 divide-y divide-gray-100 overflow-y-auto rounded-md border border-gray-200" role="listbox" aria-label="Employees">
                                    @foreach($employees as $employee)
                                        <a href="{{ route('loans.index', ['employee_id' => $employee->id]) }}"
                                            data-employee-option
                                            data-search="{{ strtolower($employee->full_name.' '.($employee->employee_id ?? '').' '.($employee->department?->name ?? '').' '.($employee->position?->name ?? '')) }}"
                                            class="block px-3 py-2.5 text-sm transition hover:bg-blue-50 {{ $selectedEmployee?->id === $employee->id ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}"
                                            role="option" aria-selected="{{ $selectedEmployee?->id === $employee->id ? 'true' : 'false' }}">
                                            <span class="block font-medium">{{ $employee->full_name }}</span>
                                            <span class="mt-0.5 block text-xs text-gray-500">{{ $employee->employee_id ?? 'No ID' }} · {{ $employee->department?->name ?? 'No department' }} · {{ $employee->position?->name ?? 'No position' }}</span>
                                        </a>
                                    @endforeach
                                </div>
                                <p id="employeeNoResults" class="hidden px-1 py-3 text-sm text-gray-500">No employees match your search.</p>
                            </div>

                            <div class="rounded-md border border-gray-200 bg-gray-50 p-4">
                                @if($selectedEmployee)
                                    <p class="text-xs font-semibold uppercase text-gray-500">Selected employee</p>
                                    <h3 class="mt-2 text-lg font-semibold text-gray-900">{{ $selectedEmployee->full_name }}</h3>
                                    <dl class="mt-3 grid grid-cols-1 gap-y-2 text-sm sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                                        <div><dt class="text-gray-500">Employee ID</dt><dd class="font-medium text-gray-900">{{ $selectedEmployee->employee_id ?? 'N/A' }}</dd></div>
                                        <div><dt class="text-gray-500">Department</dt><dd class="font-medium text-gray-900">{{ $selectedEmployee->department?->name ?? 'N/A' }}</dd></div>
                                        <div><dt class="text-gray-500">Position</dt><dd class="font-medium text-gray-900">{{ $selectedEmployee->position?->name ?? 'N/A' }}</dd></div>
                                        <div><dt class="text-gray-500">Employment status</dt><dd class="font-medium capitalize text-gray-900">{{ $selectedEmployee->employee_status ?? 'N/A' }}</dd></div>
                                    </dl>
                                @else
                                    <div class="flex h-full min-h-32 items-center justify-center text-center text-sm text-gray-500">
                                        Select an employee to view details and edit their loan record.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>

                    @if($selectedEmployee)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs text-gray-500">Employee Name</div>
                                <div class="mt-1 font-semibold text-gray-900">{{ $selectedEmployee->full_name }}</div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs text-gray-500">Employee Status</div>
                                <div class="mt-1 font-semibold {{ $selectedEmployee->isResigned() ? 'text-red-600' : 'text-green-600' }}">{{ $selectedEmployee->isResigned() ? 'Resigned' : 'Active' }}</div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs text-gray-500">Loan Reference</div>
                                <div class="mt-1 font-semibold text-gray-900">{{ $selectedLoan?->notes ?? '—' }}</div>
                            </div>
                        </div>

                        <form method="POST" action="{{ $selectedLoan ? route('loans.management.payment') : route('loans.management.generate') }}" class="space-y-6">
                            @csrf
                            @if($selectedLoan)
                                <input type="hidden" name="loan_id" value="{{ $selectedLoan->id }}">
                            @endif
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Employee's Name</label>
                                    <input type="text" value="{{ $selectedEmployee->full_name }}" class="w-full rounded-lg border-gray-300 bg-gray-100 text-sm" readonly>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                                    <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="w-full rounded-lg border-gray-300 text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Amort Date</label>
                                    <input type="date" name="amort_date" value="{{ old('amort_date', $selectedLoan?->start_date?->format('Y-m-d') ?? now()->toDateString()) }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Loan Reference</label>
                                    <input type="text" name="loan_reference" value="{{ old('loan_reference', $selectedLoan?->notes ?? '') }}" class="w-full rounded-lg border-gray-300 text-sm" placeholder="reference">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Code</label>
                                    <select name="loan_type_id" class="w-full rounded-lg border-gray-300 text-sm">
                                        @foreach($loanTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('loan_type_id', $selectedLoan?->loan_type_id ?? $loanTypes->first()?->id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Principal Amount</label>
                                    <input type="number" step="0.01" min="0" name="principal_amount" value="{{ old('principal_amount', $selectedLoan?->principal_amount ?? 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Manual Payments</label>
                                    <input type="number" step="0.01" min="0" name="manual_payments" value="{{ old('manual_payments', $selectedLoan?->total_payments ?? 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Monthly Payments</label>
                                    <input type="number" step="0.01" min="0" name="monthly_payment" value="{{ old('monthly_payment', $selectedLoan?->amortization_amount ?? 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Freq</label>
                                    <select name="freq" class="w-full rounded-lg border-gray-300 text-sm">
                                        <option value="monthly" {{ old('freq', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="semi_monthly" {{ old('freq', 'monthly') == 'semi_monthly' ? 'selected' : '' }}>Semi-Monthly</option>
                                        <option value="weekly" {{ old('freq', 'monthly') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Only in Periods</label>
                                    <input type="number" min="0" name="only_in_periods" value="{{ old('only_in_periods', 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Cancelled Loans</label>
                                    <select name="cancelled_loans" class="w-full rounded-lg border-gray-300 text-sm">
                                        <option value="0" {{ old('cancelled_loans', 0) == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('cancelled_loans', 0) == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">P1</label>
                                    <input type="number" step="0.01" min="0" name="p1" value="{{ old('p1', 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">P2</label>
                                    <input type="number" step="0.01" min="0" name="p2" value="{{ old('p2', 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">P3</label>
                                    <input type="number" step="0.01" min="0" name="p3" value="{{ old('p3', 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">P4</label>
                                    <input type="number" step="0.01" min="0" name="p4" value="{{ old('p4', 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">P5</label>
                                    <input type="number" step="0.01" min="0" name="p5" value="{{ old('p5', 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                            </div>

                            @if($selectedLoan)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="text-xs text-gray-500">Total Payments</div>
                                        <div class="mt-1 text-lg font-semibold text-gray-900">₱{{ number_format((float) $selectedLoan->total_payments, 2) }}</div>
                                    </div>
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="text-xs text-gray-500">Current Payment</div>
                                        <div class="mt-1 text-lg font-semibold text-gray-900">₱{{ number_format((float) $selectedLoan->current_payment, 2) }}</div>
                                    </div>
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="text-xs text-gray-500">MTD Payments</div>
                                        <div class="mt-1 text-lg font-semibold text-gray-900">₱{{ number_format((float) $selectedLoan->mtd_payments, 2) }}</div>
                                    </div>
                                </div>
                            @endif

                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                    {{ $selectedLoan ? 'Save' : 'Generate Loan' }}
                                </button>
                                <button type="reset" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">Cancel</button>
                            </div>
                        </form>

                        @if($selectedLoan)
                            <div class="mt-8 rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-gray-900">Edit Payments</h3>
                                    <span class="text-xs text-gray-500">{{ $selectedLoan->payments->count() }} payment entries</span>
                                </div>
                                @if($selectedLoan->payments->isEmpty())
                                    <p class="text-sm text-gray-500">No recorded payments yet.</p>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                            <tr class="border-b border-gray-200 text-left text-gray-500">
                                                <th class="py-2 pr-4">Date</th>
                                                <th class="py-2 pr-4">Amount</th>
                                                <th class="py-2 pr-4">Balance</th>
                                                <th class="py-2">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($selectedLoan->payments as $payment)
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-2 pr-4">{{ $payment->payment_date?->format('M d, Y') }}</td>
                                                    <td class="py-2 pr-4">₱{{ number_format((float) $payment->amount, 2) }}</td>
                                                    <td class="py-2 pr-4">₱{{ number_format((float) $payment->balance_after, 2) }}</td>
                                                    <td class="py-2">
                                                        <form action="{{ route('loans.management.payment.delete', $payment) }}" method="POST" onsubmit="return confirm('Delete this payment?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-sm text-gray-500">
                            Select an employee to view or edit their loan details.
                        </div>
                    @endif
                </div>

                <div id="tab-utilities" class="loan-panel hidden p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Export Loans</h3>
                            <form method="POST" action="{{ route('loans.management.export') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">Download CSV</button>
                            </form>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Import Loans</h3>
                            <form method="POST" action="{{ route('loans.management.import') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="import_file" accept=".csv,.xls,.xlsx" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700" required>
                                <button type="submit" class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Import</button>
                            </form>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Delete All Loan Master Records</h3>
                            <form method="POST" action="{{ route('loans.management.delete-master') }}" onsubmit="return confirm('Delete all loan master records?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">Delete Master Records</button>
                            </form>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Delete All Previous Loan Details</h3>
                            <form method="POST" action="{{ route('loans.management.delete-details') }}" onsubmit="return confirm('Delete all previous loan details?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">Delete Loan Details</button>
                            </form>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Import Loans Special</h3>
                            <form method="POST" action="{{ route('loans.management.import-special') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="import_file" accept=".csv,.xls,.xlsx" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700" required>
                                <button type="submit" class="mt-3 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Import Special</button>
                            </form>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Clean Loan Codes</h3>
                            <form method="POST" action="{{ route('loans.management.clean-codes') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700">Clean Codes</button>
                            </form>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Check Codes with CR</h3>
                            <form method="POST" action="{{ route('loans.management.check-codes') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700">Check Codes</button>
                            </form>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Download Current Loans</h3>
                            <form method="POST" action="{{ route('loans.management.download-current') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-cyan-600 text-white rounded-lg text-sm font-medium hover:bg-cyan-700">Download Current Loans</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@unless($isEmployeeView)
@if(in_array($user->role, ['admin', 'hr'], true))
<div id="loanTypesModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/60 p-4 overflow-y-auto backdrop-blur-sm" onclick="if(event.target === this) closeLoanTypesModal()">
    <div class="relative w-full max-w-4xl my-8 max-h-[calc(100vh-4rem)] overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-2xl">
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white px-6 py-4">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-sliders"></i></span>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Loan Types</h3>
                    <p class="text-xs text-gray-500">Configure the loan options available to employees.</p>
                </div>
            </div>
            <button type="button" onclick="closeLoanTypesModal()" class="ui-icon-action ui-action-cancel" title="Close" aria-label="Close loan types modal">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            @if(session('loan_type_success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('loan_type_success') }}</div>
            @endif
            @if(session('loan_type_error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">{{ session('loan_type_error') }}</div>
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
                                <td class="px-4 py-2">
                                    @if($type->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('loan-types.edit', $type) }}" class="ui-icon-action ui-action-edit" title="Edit loan type"><i class="fas fa-pen"></i></a>
                                        <form action="{{ route('loan-types.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('Delete loan type {{ $type->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ui-icon-action ui-action-delete" title="Delete loan type"><i class="fas fa-trash"></i></button>
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
                            <input type="text" name="name" required placeholder="e.g. Salary Loan" class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Interest Rate (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="default_interest_rate" value="0" required class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
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
                        <button type="submit" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700"><i class="fas fa-plus mr-2"></i>Add Loan Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endunless

@unless($isEmployeeView)
<script>
    document.querySelectorAll('.loan-tab').forEach((button) => {
        button.addEventListener('click', () => {
            const target = button.dataset.tab;
            document.querySelectorAll('.loan-tab').forEach((tab) => {
                const isSelected = tab === button;
                tab.classList.toggle('bg-blue-600', isSelected);
                tab.classList.toggle('text-white', isSelected);
                tab.classList.toggle('hover:bg-blue-700', isSelected);
                tab.classList.toggle('text-gray-700', !isSelected);
                tab.classList.toggle('hover:bg-transparent', !isSelected);
            });

            document.querySelectorAll('.loan-panel').forEach((panel) => {
                panel.classList.toggle('hidden', panel.id !== 'tab-' + target);
            });
        });
    });

    const employeeSearchInput = document.getElementById('employeeSearch');
    const employeeOptions = Array.from(document.querySelectorAll('[data-employee-option]'));
    const employeeNoResults = document.getElementById('employeeNoResults');

    function applyEmployeeFilter() {
        if (!employeeSearchInput || !employeeOptions.length) return;

        const query = employeeSearchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        employeeOptions.forEach((option) => {
            const matches = option.dataset.search.includes(query);
            option.hidden = !matches;
            if (matches) visibleCount++;
        });

        if (employeeNoResults) {
            employeeNoResults.classList.toggle('hidden', visibleCount !== 0);
        }
    }

    if (employeeSearchInput) {
        employeeSearchInput.addEventListener('input', applyEmployeeFilter);
    }

    function openLoanTypesModal() {
        const modal = document.getElementById('loanTypesModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeLoanTypesModal() {
        const modal = document.getElementById('loanTypesModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endunless
@endsection
