@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'payrolls.summary'])

@section('title', 'Payroll Summary Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Payroll Summary Report</h1>
            <p class="mt-1 text-sm text-gray-600">Overview of payroll statistics and trends</p>
        </div>
        <!-- Features Section -->
        <div class="mt-4 sm:mt-0">
            <details class="group">
                <summary class="cursor-pointer inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-list mr-2"></i>Available Reports
                    <i class="fas fa-chevron-down ml-2 group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="mt-2 bg-white border border-gray-200 rounded-lg shadow-lg p-4 space-y-2">
                    <a href="{{ route('payrolls.summary') }}" class="block text-sm text-blue-600 hover:text-blue-800">• Payroll Summary</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'payslips']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• Payslips Report</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'account-entries']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• Account Entries</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'allowance-special']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• Allowance Special Report</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'bank-remittance']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• Bank Remittance</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'denominations']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• Denominations</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'pagibig']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• Pag Ibig Report</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'philhealth']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• Philhealth Report & RF-1</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'undeducted']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• Print Undeducted Items</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'received']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• Received List</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'sss']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• SSS Report</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'tax']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• Tax Report</a>
                    <a href="{{ route('payrolls.summary', ['report' => 'text-files']) }}" class="block text-sm text-blue-600 hover:text-blue-800">• Text File Reports</span>
                </div>
            </details>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('payrolls.monthly', ['year' => now()->year, 'month' => now()->month]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50">
                <i class="fas fa-calendar-alt mr-2"></i>
                Monthly Report
            </a>
            <a href="{{ route('payroll.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Payroll
            </a>
        </div>
    </div>

    <!-- Date Range -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center text-sm text-gray-600">
            <i class="fas fa-calendar mr-2"></i>
            <span>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Payrolls -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Payrolls</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($summary['total_payrolls'] ?? 0) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-file-invoice-dollar text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Gross Pay -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Gross Pay</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ number_format($summary['total_gross_pay'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Deductions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Deductions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ number_format($summary['total_deductions'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-minus-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Net Pay -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Net Pay</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ number_format($summary['total_net_pay'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-wallet text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Monthly Breakdown</h2>
            <p class="text-sm text-gray-600 mt-1">Last 12 months payroll data</p>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Payrolls</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($monthly_data as $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::createFromDate($data->year, $data->month, 1)->format('F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                {{ number_format($data->count) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                ₱{{ number_format($data->gross_pay, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                ₱{{ number_format($data->deductions, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                ₱{{ number_format($data->net_pay, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No payroll data available
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
