@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'payrolls.summary'])

@section('title', $reportName)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-green-700">Financial Reports</p>
            <h1 class="text-2xl font-bold text-gray-900">{{ $reportName }}</h1>
            <p class="mt-1 text-sm text-gray-600">Report period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        </div>
        <a href="{{ route('payrolls.summary') }}" class="inline-flex items-center gap-2 self-start rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="fas fa-arrow-left"></i><span>Back to Reports</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600">Total Withholding Tax</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ number_format($totalTax, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600">Total Gross Pay</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ number_format($totalGross, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600">Employees Covered</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $employeeCount }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600">Avg Tax Rate</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $totalGross > 0 ? number_format(($totalTax / $totalGross) * 100, 1) : 0 }}%</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Tax Breakdown by Employee</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Period</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Gross Pay</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Tax Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Tax Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payrolls as $payroll)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $payroll->employee->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $payroll->employee->employee_id }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d') }} -
                            {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm tabular-nums text-gray-900">
                            ₱{{ number_format($payroll->gross_pay, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm tabular-nums text-gray-900">
                            ₱{{ number_format($payroll->tax_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900">
                            {{ $payroll->gross_pay > 0 ? number_format(($payroll->tax_amount / $payroll->gross_pay) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No tax deductions found for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
