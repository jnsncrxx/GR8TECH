@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employees.prev-emp-oth'])

@section('title', 'Previous Employer & Other')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center gap-3 mb-2">
        <div style="width:38px;height:38px;border-radius:10px;background:#f5f3ff;color:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:1rem;">
            <i class="fas fa-history"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Previous Employer &amp; Other</h1>
            <p class="text-sm text-gray-500">Employee previous employment information</p>
        </div>
    </div>

    @if(isset($hasPreviousEmploymentsTable) && !$hasPreviousEmploymentsTable)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800 text-sm">
            Previous employment database table is not ready. Run migrations to enable loading and saving data.
        </div>
    @endif

    <form method="GET" action="{{ route('employees.prev-emp-oth') }}" class="emp-card sec-personal">
        <div class="emp-card-header">
            <div class="section-icon"><i class="fas fa-user"></i></div>
            <div>
                <h3>Employee Selection</h3>
                <p>Select an employee to manage their previous employment history</p>
            </div>
        </div>
        <div class="emp-card-body">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label for="employee_picker" class="form-label">Employee Name</label>
                <select id="employee_picker" name="employee_id" onchange="this.form.submit()" class="form-control">
                    <option value="">Select employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ data_get($emp, 'id') }}" data-empno="{{ data_get($emp, 'employee_id') }}" {{ ($selectedEmployeeId ?? '') === data_get($emp, 'id') ? 'selected' : '' }}>
                            {{ data_get($emp, 'first_name') }} {{ data_get($emp, 'last_name') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="empno" class="form-label">Empno</label>
                <input type="text" id="empno" class="form-control bg-gray-50" value="{{ old('empno', data_get($selectedEmployee, 'employee_id')) }}" readonly>
            </div>
        </div>
        </div>
    </form>

    <form method="POST" action="{{ route('employees.prev-emp-oth.save') }}" class="emp-card sec-loans">
        <div class="emp-card-header">
            <div class="section-icon"><i class="fas fa-building"></i></div>
            <div>
                <h3>Previous Employment</h3>
                <p>Prior employment history and positions held</p>
            </div>
        </div>
        <div class="emp-card-body space-y-6">
        @csrf
        <input type="hidden" name="employee_id" value="{{ old('employee_id', $selectedEmployeeId) }}">

        <div class="space-y-6">
                @for($i = 1; $i <= 5; $i++)
                    @php
                        $row = data_get($prefilledRows, $i);
                    @endphp
                    <div class="border border-gray-100 rounded-lg p-4 space-y-3">
                        <div class="flex items-center gap-2">
                            <label class="w-52 shrink-0 text-sm font-medium text-gray-700 whitespace-nowrap text-right">Previous Employment {{ $i }}</label>
                            <span class="text-gray-500 shrink-0">:</span>
                            <input type="text" name="rows[{{ $i - 1 }}][employment_name]" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('rows.' . ($i - 1) . '.employment_name', data_get($row, 'employment_name')) }}" placeholder="Enter Previous Employment {{ $i }}">
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="w-52 shrink-0 text-sm font-medium text-gray-700 whitespace-nowrap text-right">Position</label>
                            <span class="text-gray-500 shrink-0">:</span>
                            <input type="text" name="rows[{{ $i - 1 }}][position]" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('rows.' . ($i - 1) . '.position', data_get($row, 'position')) }}" placeholder="Enter Position">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center gap-2">
                                <label class="w-52 shrink-0 text-sm font-medium text-gray-700 whitespace-nowrap text-right">Date Started</label>
                                <span class="text-gray-500 shrink-0">:</span>
                                <div class="grid grid-cols-2 gap-2 w-full">
                                    <select name="rows[{{ $i - 1 }}][start_month]" class="w-full h-10 px-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Month</option>
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ (string) old('rows.' . ($i - 1) . '.start_month', data_get($row, 'start_month')) === (string) $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                        @endfor
                                    </select>
                                    <select name="rows[{{ $i - 1 }}][start_year]" class="w-full h-10 px-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Year</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ (string) old('rows.' . ($i - 1) . '.start_year', data_get($row, 'start_year')) === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="w-52 shrink-0 text-sm font-medium text-gray-700 whitespace-nowrap text-right">Date Ended</label>
                                <span class="text-gray-500 shrink-0">:</span>
                                <div class="grid grid-cols-2 gap-2 w-full">
                                    <select name="rows[{{ $i - 1 }}][end_month]" class="w-full h-10 px-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Month</option>
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ (string) old('rows.' . ($i - 1) . '.end_month', data_get($row, 'end_month')) === (string) $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                        @endfor
                                    </select>
                                    <select name="rows[{{ $i - 1 }}][end_year]" class="w-full h-10 px-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Year</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ (string) old('rows.' . ($i - 1) . '.end_year', data_get($row, 'end_year')) === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-6 h-10 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors" {{ empty($selectedEmployeeId) ? 'disabled' : '' }}>
                Save
            </button>
        </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const picker = document.getElementById('employee_picker');
    const empnoInput = document.getElementById('empno');

    if (!picker || !empnoInput) {
        return;
    }

    const selectedOption = picker.options[picker.selectedIndex];
    empnoInput.value = selectedOption ? (selectedOption.getAttribute('data-empno') || empnoInput.value) : empnoInput.value;
});
</script>
@endsection
