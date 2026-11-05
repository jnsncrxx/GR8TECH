@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'payslips.report'])

@section('title', 'Payslips')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-green-700">Other Financial Reports</p>
            <h1 class="text-2xl font-bold text-gray-900">Payslips</h1>
            <p class="mt-1 text-sm text-gray-600">Review employee salary, recorded payroll earnings, and deductions.</p>
        </div>
        <a href="{{ route('payrolls.summary') }}" class="inline-flex items-center gap-2 self-start rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="fas fa-chart-bar"></i><span>Payroll Reports</span>
        </a>
    </div>

    <section class="border-y border-gray-200 py-4" aria-labelledby="employee-list-heading">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 id="employee-list-heading" class="text-lg font-semibold text-gray-900">Employees</h2>
                <p class="text-sm text-gray-500">{{ $employees->count() }} employee(s) in the active company</p>
            </div>
            <form method="GET" action="{{ route('payrolls.summary') }}" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-end">
                <input type="hidden" name="report" value="payslips">
                <div class="w-full sm:min-w-72">
                    <label for="employee_id" class="mb-1 block text-sm font-medium text-gray-700">Select employee</label>
                    <select id="employee_id" name="employee_id" class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 focus:border-green-600 focus:ring-green-600">
                        <option value="">Choose an employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected($selectedEmployee?->id === $employee->id)>
                                {{ $employee->employee_id }} · {{ $employee->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">
                    <i class="fas fa-eye"></i><span>View Payslip</span>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Employee</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Department</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Monthly Salary</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Payslip</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500"><span class="sr-only">Action</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employees as $employee)
                        <tr class="{{ $selectedEmployee?->id === $employee->id ? 'bg-green-50' : 'hover:bg-gray-50' }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $employee->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $employee->employee_id }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $employee->department?->name ?? 'No department' }}</td>
                            <td class="px-4 py-3 text-right text-sm tabular-nums text-gray-900">₱{{ number_format((float) $employee->salary, 2) }}</td>
                            <td class="px-4 py-3">
                                @php
                            $hasPayslip = \App\Models\Payroll::where('employee_id', $employee->id)
                                ->whereIn('status', ['approved', 'paid'])
                                ->exists();
                        @endphp
                        @if($hasPayslip)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700"><span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>Available</span>
                                @else
                                    <span class="text-xs text-gray-500">No approved or paid payroll</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('payrolls.summary', ['report' => 'payslips', 'employee_id' => $employee->id]) }}" class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-sm font-medium text-green-800 hover:bg-green-100" aria-label="View payslip for {{ $employee->full_name }}">
                                    <span>View</span><i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">No employees found for the active company.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if($selectedEmployee)
    <section id="selected-payslip" class="space-y-4" aria-labelledby="selected-employee-heading">
        <div class="flex flex-col gap-1 border-b border-gray-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 id="selected-employee-heading" class="text-xl font-semibold text-gray-900">{{ $selectedEmployee->full_name }}</h2>
                <p class="text-sm text-gray-500">{{ $selectedEmployee->employee_id }} · {{ $selectedEmployee->department?->name ?? 'No department' }}</p>
            </div>
            @if($selectedPayroll)
                <p class="text-sm text-gray-600">Pay period: <span class="font-medium text-gray-900">{{ $selectedPayroll->pay_period_start?->format('M j, Y') }} – {{ $selectedPayroll->pay_period_end?->format('M j, Y') }}</span></p>
            @endif
        </div>

        @if($selectedPayroll)
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="border-l-4 border-gray-400 bg-gray-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase text-gray-500">Current Monthly Salary</p>
                    <p class="mt-1 text-xl font-semibold tabular-nums text-gray-900">₱{{ number_format((float) $selectedEmployee->salary, 2) }}</p>
                </div>
                <div class="border-l-4 border-blue-600 bg-blue-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase text-blue-800">Basic Salary This Period</p>
                    <p class="mt-1 text-xl font-semibold tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->basic_salary, 2) }}</p>
                </div>
                <div class="border-l-4 border-green-700 bg-green-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase text-green-800">Computed Gross Pay</p>
                    <p class="mt-1 text-xl font-semibold tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->gross_pay, 2) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="border-y border-gray-200 py-4">
                    <h3 class="mb-3 text-sm font-semibold uppercase text-gray-700">Recorded Earnings</h3>
                    <dl class="divide-y divide-gray-100 text-sm">
                        <div class="flex justify-between gap-4 py-2"><dt class="text-gray-600">Basic salary</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->basic_salary, 2) }}</dd></div>
                        <div class="flex justify-between gap-4 py-2"><dt class="text-gray-600">Overtime pay</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->overtime_pay, 2) }}</dd></div>
                        <div class="flex justify-between gap-4 py-2"><dt class="text-gray-600">Allowances</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->allowances, 2) }}</dd></div>
                        <div class="flex justify-between gap-4 py-2"><dt class="text-gray-600">Bonuses</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->bonuses, 2) }}</dd></div>
                        <div class="flex justify-between gap-4 py-2 font-semibold"><dt class="text-gray-900">Gross pay</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->gross_pay, 2) }}</dd></div>
                    </dl>
                </div>

                <div class="border-y border-gray-200 py-4">
                    <h3 class="mb-3 text-sm font-semibold uppercase text-gray-700">Recorded Deductions</h3>
                    <dl class="divide-y divide-gray-100 text-sm">
                        <div class="flex justify-between gap-4 py-2"><dt class="text-gray-600">Withholding tax</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->tax_amount, 2) }}</dd></div>
                        <div class="flex justify-between gap-4 py-2"><dt class="text-gray-600">SSS</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->sss, 2) }}</dd></div>
                        <div class="flex justify-between gap-4 py-2"><dt class="text-gray-600">PhilHealth</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->phic, 2) }}</dd></div>
                        <div class="flex justify-between gap-4 py-2"><dt class="text-gray-600">Pag-IBIG</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->hdmf, 2) }}</dd></div>
                        <div class="flex justify-between gap-4 py-2"><dt class="text-gray-600">Other payroll deductions</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->deductions, 2) }}</dd></div>
                        <div class="flex justify-between gap-4 py-2"><dt class="text-gray-600">Loan deduction</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->loan_deduction, 2) }}</dd></div>
                        <div class="flex justify-between gap-4 py-2 font-semibold"><dt class="text-gray-900">Net pay</dt><dd class="tabular-nums text-gray-900">₱{{ number_format((float) $selectedPayroll->net_pay, 2) }}</dd></div>
                    </dl>
                </div>
            </div>
            <p class="text-xs text-gray-500">Amounts shown are stored payroll results for this pay period; this report does not recalculate salary or deductions.</p>
        @else
            <div class="border-y border-gray-200 py-8 text-center">
                <p class="font-medium text-gray-800">No approved or paid payslip is available for this employee yet.</p>
                <p class="mt-1 text-sm text-gray-500">Payroll amounts appear here after an eligible payroll record exists.</p>
            </div>
        @endif
    </section>
    @endif
</div>
@endsection