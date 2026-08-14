@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'attendance.period-management.index'])

@section('title', 'Create Payroll Period')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Payroll Period</h1>
                    <p class="mt-1 text-sm text-gray-600">Set the payroll dates, cutoff coverage, processing type, and employee scope.</p>
                </div>
                <a href="{{ route('attendance.period-management.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Periods
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(!$currentCompany)
            <div class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Development Mode:</strong>
                No company is selected. This payroll period will be saved without a company assignment.
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
                <p class="font-medium text-red-800">Please correct the following:</p>
                <ul class="mt-2 list-disc pl-5 text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('attendance.period-management.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Payroll Dates and Period</h2>
                            <p class="text-sm text-gray-500">Based on the period-setting workflow shown by your supervisor.</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Initial Status: Draft</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="period_month" class="block text-sm font-medium text-gray-700 mb-2">Payroll Month *</label>
                            <select name="period_month" id="period_month" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}" @selected((int) old('period_month', now()->month) === $month)>
                                        {{ \Carbon\Carbon::create(null, $month, 1)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="period_year" class="block text-sm font-medium text-gray-700 mb-2">Year *</label>
                            <input type="number" min="2000" max="2100" name="period_year" id="period_year" required
                                   value="{{ old('period_year', now()->year) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label for="period_no" class="block text-sm font-medium text-gray-700 mb-2">Period No. *</label>
                            <select name="period_no" id="period_no" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @foreach(range(1, 5) as $number)
                                    <option value="{{ $number }}" @selected((int) old('period_no', 1) === $number)>Period {{ $number }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 p-4 rounded-lg bg-blue-50 border border-blue-200">
                        <p class="text-xs uppercase tracking-wide font-medium text-blue-700">Automatic Period Name</p>
                        <p id="generatedName" class="mt-1 text-lg font-semibold text-blue-900"></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">From Date *</label>
                            <input type="date" name="start_date" id="start_date" required value="{{ old('start_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">To Date *</label>
                            <input type="date" name="end_date" id="end_date" required value="{{ old('end_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label for="payroll_date" class="block text-sm font-medium text-gray-700 mb-2">Pay Date *</label>
                            <input type="date" name="payroll_date" id="payroll_date" required value="{{ old('payroll_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>

                    <div id="datePreview" class="hidden mt-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                        <div class="min-h-[92px] rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 flex flex-col justify-between">
                            <p class="text-xs font-medium leading-5 text-gray-500">Calendar Days</p>
                            <p id="calendarDays" class="mt-3 text-xl font-semibold leading-none text-gray-900">0</p>
                        </div>
                        <div class="min-h-[92px] rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 flex flex-col justify-between">
                            <p class="text-xs font-medium leading-5 text-gray-500">Workdays (Mon–Sat)</p>
                            <p id="workingDays" class="mt-3 text-xl font-semibold leading-none text-gray-900">0</p>
                        </div>
                        <div class="min-h-[92px] rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 flex flex-col justify-between">
                            <p class="text-xs font-medium leading-5 text-gray-500">Sunday Off Days</p>
                            <p id="weekendDays" class="mt-3 text-xl font-semibold leading-none text-gray-900">0</p>
                        </div>
                        <div class="min-h-[92px] rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 flex flex-col justify-between">
                            <p class="text-xs font-medium leading-5 text-gray-500">Pay Date</p>
                            <p id="payDatePreview" class="mt-3 text-lg font-semibold leading-none text-gray-900">—</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                        <div>
                            <label for="period_type" class="block text-sm font-medium text-gray-700 mb-2">Period Type *</label>
                            <select name="period_type" id="period_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="regular" @selected(old('period_type', 'regular') === 'regular')>Regular Payroll</option>
                                <option value="special" @selected(old('period_type') === 'special')>Special Payroll</option>
                                <option value="final_pay" @selected(old('period_type') === 'final_pay')>Final Pay</option>
                                <option value="13th_month" @selected(old('period_type') === '13th_month')>13th Month</option>
                            </select>
                        </div>
                        <div>
                            <label for="processing_type" class="block text-sm font-medium text-gray-700 mb-2">Processing Type *</label>
                            <select name="processing_type" id="processing_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="regular" @selected(old('processing_type', 'regular') === 'regular')>Regular Process</option>
                                <option value="resigned_only" @selected(old('processing_type') === 'resigned_only')>Resigned Employees Only</option>
                                <option value="leaves_only" @selected(old('processing_type') === 'leaves_only')>Leaves Only</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Previous Period</p>
                                <p id="previousPeriodName" class="mt-1 font-semibold text-gray-900">Checking selected cutoff…</p>
                                <p id="previousPeriodDetails" class="mt-1 text-sm text-gray-600">The previous cutoff is based on the selected month and period number.</p>
                            </div>
                            <span id="previousPeriodStatus" class="hidden shrink-0 inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-700 ring-1 ring-gray-200"></span>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" maxlength="2000"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                  placeholder="Optional notes for this payroll period...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Employee Coverage</h2>
                    <p class="mt-1 text-sm text-gray-500">Leave both filters empty to include all eligible employees in the active company.</p>

                    <div class="mt-5">
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department_id" id="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" @selected(old('department_id') === $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <label for="employeeSearch" class="block text-sm font-medium text-gray-700 mb-2">Search Employees</label>
                        <input type="text" id="employeeSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Search by name...">
                    </div>

                    <div class="mt-4 flex items-center justify-between rounded-lg bg-green-50 border border-green-200 p-3">
                        <label class="flex items-center text-sm font-medium text-green-900">
                            <input type="checkbox" id="selectAllEmployees" class="mr-2 rounded border-gray-300">
                            Select all visible
                        </label>
                        <span id="selectedCount" class="text-xs font-medium text-green-800">0 selected</span>
                    </div>

                    <div id="employeeList" class="mt-3 max-h-80 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100"></div>
                </div>
            </div>

            <div class="flex justify-end gap-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <a href="{{ route('attendance.period-management.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                <button type="submit"
                        class="px-5 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-save mr-2"></i>Create Draft Period
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $employeeOptions = $employees->map(function ($employee) {
        return [
            'id' => $employee->id,
            'name' => trim(
                ($employee->first_name ?? '') . ' ' .
                ($employee->last_name ?? '')
            ),
            'code' => $employee->employee_code ?? '',
            'department_id' => $employee->department_id,
            'department' => optional($employee->department)->name
                ?? 'No Department',
        ];
    })->values();

    $oldSelectedEmployeeIds = old('employee_ids', []);
@endphp

<script>
const employees = {{ Illuminate\Support\Js::from($employeeOptions) }};
const periodOptions = {{ Illuminate\Support\Js::from($periodOptions) }};

const oldSelected = new Set(
    {{ Illuminate\Support\Js::from($oldSelectedEmployeeIds) }}
);


function updateGeneratedName() {
    const month = document.getElementById('period_month');
    const year = document.getElementById('period_year').value;
    const number = document.getElementById('period_no').value;
    document.getElementById('generatedName').textContent = `${month.options[month.selectedIndex].text} ${year} - Period ${number}`;
}

function updatePreviousPeriodPreview() {
    const year = Number(document.getElementById('period_year').value);
    const month = Number(document.getElementById('period_month').value);
    const periodNo = Number(document.getElementById('period_no').value);
    const name = document.getElementById('previousPeriodName');
    const details = document.getElementById('previousPeriodDetails');
    const status = document.getElementById('previousPeriodStatus');

    if (!year || !month || !periodNo) return;

    let previousYear = year;
    let previousMonth = month;
    let previousNo = periodNo - 1;

    if (previousNo < 1) {
        previousNo = 2;
        previousMonth -= 1;
        if (previousMonth < 1) {
            previousMonth = 12;
            previousYear -= 1;
        }
    }

    const previous = periodOptions.find(period =>
        Number(period.period_year) === previousYear &&
        Number(period.period_month) === previousMonth &&
        Number(period.period_no) === previousNo
    );

    if (!previous) {
        const expectedMonth = new Date(previousYear, previousMonth - 1, 1)
            .toLocaleDateString(undefined, { month: 'long' });
        name.textContent = `${expectedMonth} ${previousYear} - Period ${previousNo}`;
        details.textContent = 'Not yet created. Create or verify this cutoff before continuing.';
        status.classList.add('hidden');
        status.textContent = '';
        return;
    }

    name.textContent = previous.name;
    const formatDate = value => new Date(`${value}T00:00:00`).toLocaleDateString(undefined, {
        month: 'short', day: 'numeric', year: 'numeric'
    });
    details.textContent = `${formatDate(previous.start_date)} – ${formatDate(previous.end_date)} · ${previous.working_days} workdays`;
    status.textContent = previous.status_label;
    status.classList.remove('hidden');
}

function updateStandardPeriodDates() {
    const type = document.querySelector('[name="period_type"]').value;
    const periodNo = Number(document.getElementById('period_no').value);
    const year = Number(document.getElementById('period_year').value);
    const month = Number(document.getElementById('period_month').value);

    if (type !== 'regular' || ![1, 2].includes(periodNo) || !year || !month) return;

    const pad = value => String(value).padStart(2, '0');
    const currentMonth = `${year}-${pad(month)}`;

    if (periodNo === 1) {
        const previous = new Date(year, month - 2, 1);
        document.getElementById('start_date').value = `${previous.getFullYear()}-${pad(previous.getMonth() + 1)}-26`;
        document.getElementById('end_date').value = `${currentMonth}-10`;
        document.getElementById('payroll_date').value = `${currentMonth}-15`;
    } else {
        const lastDay = new Date(year, month, 0).getDate();
        document.getElementById('start_date').value = `${currentMonth}-11`;
        document.getElementById('end_date').value = `${currentMonth}-25`;
        document.getElementById('payroll_date').value = `${currentMonth}-${pad(Math.min(30, lastDay))}`;
    }

    updateDatePreview();
}

function updateDatePreview() {
    const startValue = document.getElementById('start_date').value;
    const endValue = document.getElementById('end_date').value;
    const payValue = document.getElementById('payroll_date').value;
    const preview = document.getElementById('datePreview');

    if (!startValue || !endValue || new Date(endValue) < new Date(startValue)) {
        preview.classList.add('hidden');
        return;
    }

    let cursor = new Date(`${startValue}T00:00:00`);
    const end = new Date(`${endValue}T00:00:00`);
    let calendar = 0;
    let weekdays = 0;

    while (cursor <= end) {
        calendar++;
        const day = cursor.getDay();
        if (day !== 0) weekdays++;
        cursor.setDate(cursor.getDate() + 1);
    }

    document.getElementById('calendarDays').textContent = calendar;
    document.getElementById('workingDays').textContent = weekdays;
    document.getElementById('weekendDays').textContent = calendar - weekdays;
    document.getElementById('payDatePreview').textContent = payValue ? new Date(`${payValue}T00:00:00`).toLocaleDateString() : '—';
    preview.classList.remove('hidden');
}

function renderEmployees() {
    const departmentId = document.getElementById('department_id').value;
    const search = document.getElementById('employeeSearch').value.toLowerCase().trim();
    const list = document.getElementById('employeeList');
    const filtered = employees.filter(employee => {
        const departmentMatches = !departmentId || employee.department_id === departmentId;
        const searchMatches = !search || `${employee.name} ${employee.code} ${employee.department}`.toLowerCase().includes(search);
        return departmentMatches && searchMatches;
    });

    if (!filtered.length) {
        list.innerHTML = '<p class="p-4 text-sm text-center text-gray-500">No employees found.</p>';
        return;
    }

    list.innerHTML = filtered.map(employee => `
        <label class="flex items-start p-3 hover:bg-gray-50 cursor-pointer">
            <input type="checkbox" name="employee_ids[]" value="${employee.id}" class="employee-checkbox mt-1 mr-3 rounded border-gray-300" ${oldSelected.has(employee.id) ? 'checked' : ''}>
            <span>
                <span class="block text-sm font-medium text-gray-900">${employee.code ? employee.code + ' - ' : ''}${employee.name}</span>
                <span class="block text-xs text-gray-500">${employee.department}</span>
            </span>
        </label>
    `).join('');

    list.querySelectorAll('.employee-checkbox').forEach(box => box.addEventListener('change', updateSelectedCount));
    updateSelectedCount();
}

function updateSelectedCount() {
    document.getElementById('selectedCount').textContent = `${document.querySelectorAll('.employee-checkbox:checked').length} selected`;
}

['period_month', 'period_year', 'period_no'].forEach(id => document.getElementById(id).addEventListener('change', function () {
    updateGeneratedName();
    updatePreviousPeriodPreview();
    updateStandardPeriodDates();
}));
document.querySelector('[name="period_type"]').addEventListener('change', updateStandardPeriodDates);
['start_date', 'end_date', 'payroll_date'].forEach(id => document.getElementById(id).addEventListener('change', updateDatePreview));
document.getElementById('department_id').addEventListener('change', renderEmployees);
document.getElementById('employeeSearch').addEventListener('input', renderEmployees);
document.getElementById('selectAllEmployees').addEventListener('change', function () {
    document.querySelectorAll('.employee-checkbox').forEach(box => box.checked = this.checked);
    updateSelectedCount();
});

updateGeneratedName();
updatePreviousPeriodPreview();
updateStandardPeriodDates();
updateDatePreview();
renderEmployees();
</script>
@endsection
