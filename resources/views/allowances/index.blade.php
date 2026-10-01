@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'allowances.index'])

@section('title', 'Allowances')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Allowances</h1>
            <p class="mt-1 text-sm text-gray-500">Apply OT and basic adjustments by employee.</p>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="mb-6 rounded-lg border border-gray-200 bg-white shadow-sm" aria-labelledby="allowance-employees-heading">
            <div class="border-b border-gray-200 px-5 py-4">
                <h2 id="allowance-employees-heading" class="text-base font-semibold text-gray-900">Select Employee</h2>
            </div>
            <div class="grid gap-5 p-5 lg:grid-cols-[minmax(0,1fr)_minmax(18rem,0.8fr)]">
                <div>
                    <label for="employeeSearch" class="mb-1.5 block text-sm font-medium text-gray-700">Search by employee, ID, department, or position</label>
                    <div class="relative mb-3">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400" aria-hidden="true"></i>
                        <input id="employeeSearch" type="search" placeholder="Search employees" autocomplete="off"
                            class="w-full rounded-lg border-gray-300 py-2.5 pl-9 text-sm focus:border-gray-400 focus:ring-0">
                    </div>
                    <div id="employeeList" class="max-h-64 divide-y divide-gray-100 overflow-y-auto rounded-md border border-gray-200" role="listbox" aria-label="Employees">
                        @forelse ($employees as $employee)
                            <a href="{{ route('allowances.index', ['employee_id' => $employee->id]) }}"
                                data-employee-option
                                data-search="{{ strtolower($employee->full_name.' '.$employee->employee_id.' '.($employee->department?->name ?? '').' '.($employee->position?->name ?? '')) }}"
                                class="block px-3 py-2.5 text-sm transition hover:bg-blue-50 {{ $selectedEmployee?->id === $employee->id ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}"
                                role="option" aria-selected="{{ $selectedEmployee?->id === $employee->id ? 'true' : 'false' }}">
                                <span class="block font-medium">{{ $employee->full_name }}</span>
                                <span class="mt-0.5 block text-xs text-gray-500">{{ $employee->employee_id }} · {{ $employee->department?->name ?? 'No department' }} · {{ $employee->position?->name ?? 'No position' }}</span>
                            </a>
                        @empty
                            <p class="px-3 py-4 text-sm text-gray-500">No employees available.</p>
                        @endforelse
                    </div>
                    <p id="employeeNoResults" class="hidden px-1 py-3 text-sm text-gray-500">No employees match your search.</p>
                </div>

                <div class="rounded-md border border-gray-200 bg-gray-50 p-4">
                    @if ($selectedEmployee)
                        <p class="text-xs font-semibold uppercase text-gray-500">Selected employee</p>
                        <h3 class="mt-2 text-lg font-semibold text-gray-900">{{ $selectedEmployee->full_name }}</h3>
                        <dl class="mt-3 grid grid-cols-1 gap-y-2 text-sm sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                            <div><dt class="text-gray-500">Employee ID</dt><dd class="font-medium text-gray-900">{{ $selectedEmployee->employee_id }}</dd></div>
                            <div><dt class="text-gray-500">Department</dt><dd class="font-medium text-gray-900">{{ $selectedEmployee->department?->name ?? 'N/A' }}</dd></div>
                            <div><dt class="text-gray-500">Position</dt><dd class="font-medium text-gray-900">{{ $selectedEmployee->position?->name ?? 'N/A' }}</dd></div>
                            <div><dt class="text-gray-500">Employment status</dt><dd class="font-medium capitalize text-gray-900">{{ $selectedEmployee->employee_status ?? 'N/A' }}</dd></div>
                        </dl>
                    @else
                        <div class="flex h-full min-h-32 items-center justify-center text-center text-sm text-gray-500">
                            Select an employee to view their details and manage allowances.
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @if ($selectedEmployee)
        <form method="POST" action="{{ route('allowances.store') }}" class="space-y-5">
            @csrf

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm" aria-labelledby="allowance-adjustments-heading">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 id="allowance-adjustments-heading" class="text-base font-semibold text-gray-900">Allowance Adjustments</h2>
                    <p class="mt-1 text-sm text-gray-500">Choose an adjustment type and enter a taxable or non-taxable amount. Incomplete rows are highlighted.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Adjustment Type</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Date</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Taxable Amount</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Non Taxable Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr data-employee-row>
                                <td class="px-5 py-4">
                                    <select name="employees[{{ $selectedEmployee->id }}][type]" class="adjustment-type w-full rounded-md border-gray-300 text-sm focus:border-gray-400 focus:ring-0">
                                        <option value="">Select adjustment</option>
                                        <option value="ot_adjustment">OT Adjustment</option>
                                        <option value="basic_adjustment">Basic Adjustment</option>
                                    </select>
                                </td>
                                <td class="px-5 py-4">
                                    <input
                                        type="date"
                                        name="employees[{{ $selectedEmployee->id }}][effective_date]"
                                        value="{{ $today }}"
                                        class="adjustment-date w-full rounded-md border-gray-300 text-sm focus:border-gray-400 focus:ring-0"
                                    >
                                </td>
                                <td class="px-5 py-4">
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="employees[{{ $selectedEmployee->id }}][taxable_amount]"
                                        placeholder="0.00"
                                        class="adjustment-taxable-amount w-full rounded-md border-gray-300 text-sm focus:border-gray-400 focus:ring-0"
                                    >
                                </td>
                                <td class="px-5 py-4">
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="employees[{{ $selectedEmployee->id }}][non_taxable_amount]"
                                        placeholder="0.00"
                                        class="adjustment-non-taxable-amount w-full rounded-md border-gray-300 text-sm focus:border-gray-400 focus:ring-0"
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>
                    Save Adjustments
                </button>
            </div>
        </form>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('employeeSearch');
        const rows = Array.from(document.querySelectorAll('[data-employee-row]'));
        const employeeOptions = Array.from(document.querySelectorAll('[data-employee-option]'));
        const noResults = document.getElementById('employeeNoResults');

        function validateRow(row) {
            const typeInput = row.querySelector('.adjustment-type');
            const dateInput = row.querySelector('.adjustment-date');
            const taxableInput = row.querySelector('.adjustment-taxable-amount');
            const nonTaxableInput = row.querySelector('.adjustment-non-taxable-amount');
            const hasAnyEntry = typeInput.value || dateInput.value || taxableInput.value || nonTaxableInput.value;
            const isInvalid = hasAnyEntry && (
                !typeInput.value ||
                !dateInput.value ||
                (!taxableInput.value && !nonTaxableInput.value) ||
                (Number(taxableInput.value || 0) < 0) ||
                (Number(nonTaxableInput.value || 0) < 0)
            );

            row.classList.toggle('bg-red-50', !!isInvalid);
            row.classList.toggle('border-red-200', !!isInvalid);
            [typeInput, dateInput, taxableInput, nonTaxableInput].forEach((input) => {
                input.classList.toggle('border-red-300', !!isInvalid);
                input.classList.toggle('focus:border-red-500', !!isInvalid);
                input.classList.toggle('focus:ring-red-200', !!isInvalid);
            });

            return !isInvalid;
        }

        searchInput?.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            let visibleCount = 0;

            employeeOptions.forEach((option) => {
                const matches = option.dataset.search.includes(query);
                option.hidden = !matches;
                if (matches) visibleCount++;
            });
            noResults?.classList.toggle('hidden', visibleCount !== 0);
        });

        rows.forEach((row) => {
            row.querySelectorAll('.adjustment-type, .adjustment-date, .adjustment-taxable-amount, .adjustment-non-taxable-amount').forEach((input) => {
                input.addEventListener('input', () => validateRow(row));
                input.addEventListener('change', () => validateRow(row));
            });
            validateRow(row);
        });
    });
</script>
@endsection
