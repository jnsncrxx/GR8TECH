@extends('layouts.dashboard-base', [
    'user' => $user,
    'activeRoute' => 'payroll-adjustments.index',
])

@section('title', $adjustment->exists ? 'Edit Payroll Adjustment' : 'Add Payroll Adjustment')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 py-6">

        <div class="mb-6">
            <a href="{{ route('payroll-adjustments.index') }}"
               class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>

            <h1 class="mt-3 text-2xl font-semibold text-gray-900">
                {{ $adjustment->exists ? 'Edit' : 'Add' }} Payroll Adjustment
            </h1>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="ml-5 list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ $adjustment->exists
                ? route('payroll-adjustments.update', $adjustment)
                : route('payroll-adjustments.store') }}"
            class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
        >
            @csrf

            @if ($adjustment->exists)
                @method('PUT')
            @endif

            {{-- Employee --}}
            <div>
                <label for="employee_id" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Employee
                </label>

                <select
                    id="employee_id"
                    name="employee_id"
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Select employee</option>

                    @foreach ($employees as $employee)
                        <option
                            value="{{ $employee->id }}"
                            @selected(old('employee_id', $adjustment->employee_id) === $employee->id)
                        >
                            {{ $employee->employee_id }} — {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Name and Amount --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Name
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name', $adjustment->name) }}"
                        required
                        maxlength="255"
                        placeholder="e.g., Performance Bonus"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label for="amount" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Amount
                    </label>

                    <input
                        id="amount"
                        type="number"
                        name="amount"
                        value="{{ old('amount', $adjustment->amount) }}"
                        required
                        step="0.01"
                        min="0.01"
                        placeholder="0.00"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>
            </div>

            {{-- Category, Direction and Frequency --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label for="category" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Category
                    </label>

                    <select
                        id="category"
                        name="category"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                        @foreach ([
                            'bonus' => 'Bonus',
                            'allowance' => 'Allowance',
                            'deduction' => 'Deduction',
                            'manual' => 'Manual Adjustment',
                        ] as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(old('category', $adjustment->category ?? 'bonus') === $value)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="direction-display" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Direction
                    </label>

                    <select
                        id="direction-display"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="earning">Positive / Earning</option>
                        <option value="deduction">Negative / Deduction</option>
                    </select>

                    <input
                        id="direction"
                        type="hidden"
                        name="direction"
                        value="{{ old('direction', $adjustment->direction ?? 'earning') }}"
                    >

                    <p id="direction-help" class="mt-1 text-xs text-gray-500">
                        Direction is automatically assigned based on the selected category.
                    </p>
                </div>

                <div>
                    <label for="frequency" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Frequency
                    </label>

                    <select
                        id="frequency"
                        name="frequency"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option
                            value="one_time"
                            @selected(old('frequency', $adjustment->frequency ?? 'one_time') === 'one_time')
                        >
                            One-time
                        </option>

                        <option
                            value="recurring"
                            @selected(old('frequency', $adjustment->frequency) === 'recurring')
                        >
                            Recurring
                        </option>
                    </select>
                </div>
            </div>

            {{-- Effective Dates --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="effective_from" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Effective From
                    </label>

                    <input
                        id="effective_from"
                        type="date"
                        name="effective_from"
                        required
                        value="{{ old(
                            'effective_from',
                            $adjustment->effective_from?->format('Y-m-d')
                        ) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div id="end-date-wrap">
                    <label for="effective_to" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Effective To
                        <span class="font-normal text-gray-400">(optional)</span>
                    </label>

                    <input
                        id="effective_to"
                        type="date"
                        name="effective_to"
                        value="{{ old(
                            'effective_to',
                            $adjustment->effective_to?->format('Y-m-d')
                        ) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>
            </div>

            {{-- Reason --}}
            <div>
                <label for="reason" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Remarks / Reason
                </label>

                <textarea
                    id="reason"
                    name="reason"
                    required
                    maxlength="2000"
                    rows="4"
                    placeholder="Reason for this adjustment"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                >{{ old('reason', $adjustment->reason) }}</textarea>
            </div>

            {{-- Checkboxes --}}
            <div class="flex flex-wrap items-center gap-6">
                <label class="inline-flex items-center gap-2">
                    <input
                        type="checkbox"
                        name="is_taxable"
                        value="1"
                        @checked(old('is_taxable', $adjustment->is_taxable))
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span class="text-sm text-gray-700">Taxable earning</span>
                </label>

                <label class="inline-flex items-center gap-2">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked(old(
                            'is_active',
                            $adjustment->exists ? $adjustment->is_active : true
                        ))
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('payroll-adjustments.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <i class="fas fa-save mr-2"></i>
                    Save Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const category = document.getElementById('category');
    const direction = document.getElementById('direction');
    const directionDisplay = document.getElementById('direction-display');
    const directionHelp = document.getElementById('direction-help');
    const frequency = document.getElementById('frequency');
    const endDateWrap = document.getElementById('end-date-wrap');
    const effectiveTo = document.getElementById('effective_to');

    function syncDirection() {
        if (category.value === 'bonus' || category.value === 'allowance') {
            direction.value = 'earning';
            directionDisplay.value = 'earning';
            directionDisplay.disabled = true;

            directionDisplay.classList.add('bg-gray-100', 'text-gray-500');

            directionHelp.textContent =
                'Bonus and allowance categories are automatically treated as earnings.';
        } else if (category.value === 'deduction') {
            direction.value = 'deduction';
            directionDisplay.value = 'deduction';
            directionDisplay.disabled = true;

            directionDisplay.classList.add('bg-gray-100', 'text-gray-500');

            directionHelp.textContent =
                'Deduction categories are automatically treated as negative adjustments.';
        } else {
            directionDisplay.disabled = false;
            directionDisplay.classList.remove('bg-gray-100', 'text-gray-500');

            direction.value = directionDisplay.value;

            directionHelp.textContent =
                'Choose whether the manual adjustment increases or decreases payroll.';
        }
    }

    function syncFrequency() {
        const isOneTime = frequency.value === 'one_time';

        endDateWrap.classList.toggle('hidden', isOneTime);

        if (isOneTime) {
            effectiveTo.value = '';
        }
    }

    directionDisplay.value = direction.value || 'earning';

    category.addEventListener('change', syncDirection);

    directionDisplay.addEventListener('change', function () {
        direction.value = this.value;
    });

    frequency.addEventListener('change', syncFrequency);

    syncDirection();
    syncFrequency();
});
</script>
@endsection