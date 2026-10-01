@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'deduction-register.index'])

@section('title', 'Deduction Register')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Deduction Register</h1>
            <p class="mt-1 text-sm text-gray-500">Record deductions that were missed during regular payroll processing.</p>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="mb-6 rounded-lg border border-gray-200 bg-white shadow-sm" aria-labelledby="employee-selection-heading">
            <div class="border-b border-gray-200 px-5 py-4">
                <h2 id="employee-selection-heading" class="text-base font-semibold text-gray-900">Select Employee</h2>
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
                            <a href="{{ route('deduction-register.index', ['employee_id' => $employee->id]) }}"
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
                            Select an employee to view their details and manage deductions.
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @if ($selectedEmployee)
            <section class="mb-6 rounded-lg border border-gray-200 bg-white shadow-sm" aria-labelledby="new-deduction-heading">
                <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 id="new-deduction-heading" class="text-base font-semibold text-gray-900">Add Deductions</h2>
                        <p class="mt-1 text-sm text-gray-500">Enter the actual amount to deduct. Add one or more entries before saving.</p>
                    </div>
                    <button type="button" id="addDeductionRow" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700">
                        <i class="fas fa-plus mr-2" aria-hidden="true"></i>Add Deduction
                    </button>
                </div>

                <form method="POST" autocomplete="off" action="{{ route('deduction-register.store', ['employee_id' => $selectedEmployee->id]) }}" class="p-5">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $selectedEmployee->id }}">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Deduction Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Amount</th>
                                    <th class="w-16 px-3 py-3"><span class="sr-only">Row actions</span></th>
                                </tr>
                            </thead>
                            <tbody id="deductionRows" class="divide-y divide-gray-200">
                                <tr data-deduction-row>
                                    <td class="px-4 py-3">
                                        <input type="text" name="deductions[0][type]" required maxlength="255" placeholder="Enter deduction type"
                                            class="w-full rounded-md border-gray-300 text-sm focus:border-gray-400 focus:ring-0">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="date" name="deductions[0][date]" required value="{{ old('deductions.0.date', now()->toDateString()) }}"
                                            class="w-full rounded-md border-gray-300 text-sm focus:border-gray-400 focus:ring-0">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="relative">
                                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">₱</span>
                                            <input type="number" name="deductions[0][amount]" required min="0.01" step="0.01" placeholder="0.00"
                                                class="w-full rounded-md border-gray-300 pl-7 text-sm focus:border-gray-400 focus:ring-0">
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <button type="button" data-remove-deduction class="text-gray-400" aria-label="Remove deduction row" title="Remove row" disabled>
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-5 flex justify-end">
                        <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-5 py-2.5 text-sm font-medium text-white">
                            <i class="fas fa-save mr-2" aria-hidden="true"></i>Create Deduction
                        </button>
                    </div>
                </form>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm" aria-labelledby="deduction-table-heading">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 id="deduction-table-heading" class="text-base font-semibold text-gray-900">Deduction Entries</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Deduction Type</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Date</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Amount</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($deductions as $deduction)
                                <tr>
                                    <td class="px-5 py-4">
                                        <input type="text" name="type" form="edit-deduction-{{ $deduction->id }}" required maxlength="255" autocomplete="off" value="{{ $deduction->name }}" aria-label="Deduction type"
                                            class="w-full min-w-40 rounded-md border-gray-300 text-sm focus:border-gray-400 focus:ring-0">
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <input type="date" name="date" form="edit-deduction-{{ $deduction->id }}" required value="{{ $deduction->effective_from->toDateString() }}" aria-label="Deduction date"
                                            class="rounded-md border-gray-300 text-sm focus:border-gray-400 focus:ring-0">
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <div class="relative w-36">
                                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">₱</span>
                                            <input type="number" name="amount" form="edit-deduction-{{ $deduction->id }}" required min="0.01" step="0.01" value="{{ number_format((float) $deduction->amount, 2, '.', '') }}" aria-label="Deduction amount"
                                                class="w-full rounded-md border-gray-300 pl-7 text-sm focus:border-gray-400 focus:ring-0">
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <form id="edit-deduction-{{ $deduction->id }}" method="POST" action="{{ route('deduction-register.update', $deduction) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="mr-3 text-sm font-medium text-blue-600" aria-label="Save changes to {{ $deduction->name }}">Save</button>
                                        </form>
                                        <form method="POST" action="{{ route('deduction-register.destroy', $deduction) }}" onsubmit="return confirm('Delete this deduction record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600" aria-label="Delete {{ $deduction->name }}" title="Delete deduction">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500">No deduction records for this employee yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>
</div>

<template id="deductionRowTemplate">
    <tr data-deduction-row>
        <td class="px-4 py-3">
            <input type="text" name="deductions[__INDEX__][type]" required maxlength="255" autocomplete="off" placeholder="Enter deduction type"
                class="w-full rounded-md border-gray-300 text-sm focus:border-gray-400 focus:ring-0">
        </td>
        <td class="px-4 py-3">
            <input type="date" name="deductions[__INDEX__][date]" required value="{{ now()->toDateString() }}"
                class="w-full rounded-md border-gray-300 text-sm focus:border-gray-400 focus:ring-0">
        </td>
        <td class="px-4 py-3">
            <div class="relative">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">₱</span>
                <input type="number" name="deductions[__INDEX__][amount]" required min="0.01" step="0.01" placeholder="0.00"
                    class="w-full rounded-md border-gray-300 pl-7 text-sm focus:border-gray-400 focus:ring-0">
            </div>
        </td>
        <td class="px-3 py-3 text-center">
            <button type="button" data-remove-deduction class="text-gray-400" aria-label="Remove deduction row" title="Remove row">
                <i class="fas fa-trash" aria-hidden="true"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const search = document.getElementById('employeeSearch');
        const employeeOptions = Array.from(document.querySelectorAll('[data-employee-option]'));
        const noResults = document.getElementById('employeeNoResults');

        search?.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            let visibleCount = 0;

            employeeOptions.forEach((option) => {
                const matches = option.dataset.search.includes(query);
                option.hidden = !matches;
                if (matches) visibleCount++;
            });

            noResults?.classList.toggle('hidden', visibleCount !== 0);
        });

        const deductionRows = document.getElementById('deductionRows');
        const addDeductionRow = document.getElementById('addDeductionRow');
        const template = document.getElementById('deductionRowTemplate');

        function updateRemoveButtons() {
            const rowElements = deductionRows?.querySelectorAll('[data-deduction-row]') ?? [];
            rowElements.forEach((row) => {
                row.querySelector('[data-remove-deduction]').disabled = rowElements.length === 1;
            });
        }

        addDeductionRow?.addEventListener('click', function () {
            const nextIndex = deductionRows.querySelectorAll('[data-deduction-row]').length;
            const rowMarkup = template.innerHTML.replaceAll('__INDEX__', String(nextIndex));
            deductionRows.insertAdjacentHTML('beforeend', rowMarkup);
            updateRemoveButtons();
        });

        deductionRows?.addEventListener('click', function (event) {
            const removeButton = event.target.closest('[data-remove-deduction]');
            if (!removeButton || deductionRows.querySelectorAll('[data-deduction-row]').length === 1) return;
            removeButton.closest('[data-deduction-row]').remove();
            updateRemoveButtons();
        });

        updateRemoveButtons();
    });
</script>
@endsection
