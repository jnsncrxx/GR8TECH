@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'allowances.index'])

@section('title', 'Allowances')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Allowances</h1>
                <p class="text-sm text-gray-500">Apply OT and basic adjustments by employee.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-sm">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                <input
                    id="employeeSearch"
                    type="text"
                    placeholder="Search employee"
                    class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >
            </div>

            <div class="flex items-center gap-2">
                <button type="button" id="selectAllRows" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                    Select all
                </button>
                <button type="button" id="clearSelectedRows" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm hover:bg-gray-50">
                    Clear
                </button>
            </div>
        </div>

        <p class="mb-4 text-xs text-amber-700">Incomplete rows will be highlighted in red until all required fields are filled.</p>

        <form method="POST" action="{{ route('allowances.store') }}" class="space-y-5">
            @csrf

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Select</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Adjustment Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Taxable Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Non Taxable Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($employees as $employee)
                                <tr data-employee-row data-employee-name="{{ strtolower($employee->full_name) }}" data-employee-id="{{ strtolower($employee->employee_id) }}" class="transition-colors">
                                    <td class="px-3 py-4 align-top">
                                        <input type="checkbox" class="employee-row-select h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <div class="font-medium text-gray-900">{{ $employee->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $employee->employee_id }} · {{ $employee->department?->name ?? 'No department' }}</div>
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <select name="employees[{{ $employee->id }}][type]" class="adjustment-type w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select adjustment</option>
                                            <option value="ot_adjustment">OT Adjustment</option>
                                            <option value="basic_adjustment">Basic Adjustment</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <input
                                            type="date"
                                            name="employees[{{ $employee->id }}][effective_date]"
                                            value="{{ $today }}"
                                            class="adjustment-date w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            name="employees[{{ $employee->id }}][taxable_amount]"
                                            placeholder="0.00"
                                            class="adjustment-taxable-amount w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            name="employees[{{ $employee->id }}][non_taxable_amount]"
                                            placeholder="0.00"
                                            class="adjustment-non-taxable-amount w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No employees available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>
                    Save Adjustments
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('employeeSearch');
        const rows = Array.from(document.querySelectorAll('[data-employee-row]'));
        const selectAllRowsBtn = document.getElementById('selectAllRows');
        const clearSelectedRowsBtn = document.getElementById('clearSelectedRows');

        function toggleRowSelection(row, selected) {
            const checkbox = row.querySelector('.employee-row-select');
            if (checkbox) checkbox.checked = selected;
            row.classList.toggle('bg-blue-50', selected);
            row.setAttribute('data-selected', selected ? 'true' : 'false');
        }

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

        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();

            rows.forEach((row) => {
                const name = row.dataset.employeeName || '';
                const id = row.dataset.employeeId || '';
                const matches = !query || name.includes(query) || id.includes(query);
                row.style.display = matches ? '' : 'none';
            });
        });

        selectAllRowsBtn.addEventListener('click', function () {
            rows.forEach((row) => toggleRowSelection(row, true));
        });

        clearSelectedRowsBtn.addEventListener('click', function () {
            rows.forEach((row) => toggleRowSelection(row, false));
        });

        rows.forEach((row) => {
            const checkbox = row.querySelector('.employee-row-select');
            checkbox?.addEventListener('change', function () {
                toggleRowSelection(row, this.checked);
            });

            row.querySelectorAll('.adjustment-type, .adjustment-date, .adjustment-taxable-amount, .adjustment-non-taxable-amount').forEach((input) => {
                input.addEventListener('input', () => validateRow(row));
                input.addEventListener('change', () => validateRow(row));
            });
            toggleRowSelection(row, false);
            validateRow(row);
        });
    });
</script>
@endsection
