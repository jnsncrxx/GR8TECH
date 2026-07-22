@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'attendance.period-management.index'])

@section('title', 'Payroll Preview - ' . $period['name'])

@section('content')
@php
    $previewTotalAllowances = collect($previewPayrolls)->sum(fn ($row) => (float) ($row['allowances'] ?? 0));
@endphp
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Payroll Preview</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($period['start_date'])->format('M j, Y') }} - 
                            {{ \Carbon\Carbon::parse($period['end_date'])->format('M j, Y') }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">{{ $period['name'] }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('attendance.period-management.show', $period['id']) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Period
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Period Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Period Information</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Period</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($period['start_date'])->format('M j, Y') }} - 
                            {{ \Carbon\Carbon::parse($period['end_date'])->format('M j, Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $period['department_name'] ?? 'All Departments' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Generated</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ isset($generatedAt) ? $generatedAt->format('M j, Y H:i:s') : \Carbon\Carbon::now()->format('M j, Y H:i:s') }}
                            @if(isset($generatedAt))
                                <br><span class="text-green-600 text-xs"><i class="fas fa-sync-alt mr-1"></i>Fresh Data</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Preview
                            </span>
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
            <!-- Total Employees -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-users text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $summaryData['total_employees'] }}</h3>
                        <p class="text-xs text-gray-600">Employees</p>
                    </div>
                </div>
            </div>

            <!-- Basic Salary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-money-bill-wave text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">₱{{ number_format($summaryData['total_basic_salary'] + $summaryData['total_holiday_basic_pay'], 2) }}</h3>
                        <p class="text-xs text-gray-600">Basic Salary</p>
                    </div>
                </div>
            </div>

            <!-- Holiday Pay -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-alt text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">₱{{ number_format($summaryData['total_holiday_premium'] + $summaryData['total_special_holiday_premium'], 2) }}</h3>
                        <p class="text-xs text-gray-600">Holiday Pay</p>
                    </div>
                </div>
            </div>

            <!-- Overtime -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-clock text-yellow-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">₱{{ number_format($summaryData['total_overtime_pay'], 2) }}</h3>
                        <p class="text-xs text-gray-600">Overtime</p>
                    </div>
                </div>
            </div>

            <!-- Deductions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-minus-circle text-red-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">₱{{ number_format($summaryData['total_deductions'], 2) }}</h3>
                        <p class="text-xs text-gray-600">Deductions</p>
                    </div>
                </div>
            </div>

            <!-- Net Pay -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-wallet text-indigo-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">₱{{ number_format($summaryData['total_net_pay'], 2) }}</h3>
                        <p class="text-xs text-gray-600">Net Pay</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Payroll Preview - Review Before Finalizing</h2>
                    <div class="flex space-x-3">
                        <a href="{{ route('payroll.periods.preview', $period['id']) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Refresh Preview
                        </a>
                        <a href="{{ route('payroll.periods.preview-pdf', $period['id']) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-file-pdf mr-2 text-red-600"></i>
                            Export PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                @php
                    $earningLabels = [
                        'basic_salary' => 'Basic salary',
                        'allowances' => 'Allowances',
                        'paid_leave' => 'Paid leave',
                        'overtime' => 'Overtime',
                        'night_differential' => 'Night differential',
                        'holiday_pay' => 'Holiday pay',
                        'rest_day_premium' => 'Rest-day premium',
                        'bonuses' => 'Bonuses',
                        'other' => 'Other earnings',
                    ];
                    $deductionLabels = [
                        'total_late_deduction' => 'Late',
                        'absence' => 'Absence',
                        'unpaid_leave' => 'Unpaid leave',
                        'undertime' => 'Undertime',
                        'sss' => 'SSS',
                        'philhealth' => 'PhilHealth',
                        'pagibig' => 'Pag-IBIG',
                        'other' => 'Other deductions',
                        'loan' => 'Loan',
                    ];
                @endphp
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Deductions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($previewPayrolls as $payroll)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payroll['employee_code'] ?? $payroll['employee_id'] ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $payroll['employee_name'] ?? 'Unknown Employee' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payroll['department_name'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-medium">₱{{ number_format($payroll['basic_salary'] ?? 0, 2) }}</div>
                                <div class="text-blue-600 text-xs">
                                    <i class="fas fa-clock mr-1"></i>{{ number_format($payroll['worked_hours'] ?? 0, 1) }} hrs worked
                                </div>
                                <div class="text-gray-500 text-xs">
                                    {{ number_format($payroll['scheduled_hours'] ?? 0, 1) }} hrs scheduled
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <div>₱{{ number_format($payroll['gross_pay'] ?? 0, 2) }}</div>
                                <details class="mt-1 text-xs font-normal text-gray-600">
                                    <summary class="cursor-pointer select-none text-green-700 hover:text-green-900">View earnings</summary>
                                    <div class="mt-2 min-w-48 space-y-1 rounded border border-gray-200 bg-white p-2 shadow-sm">
                                        @foreach($earningLabels as $key => $label)
                                            <div class="flex justify-between gap-3 {{ ($payroll['earnings_details'][$key] ?? 0) > 0 ? '' : 'text-gray-400' }}">
                                                <span>{{ $label }}</span>
                                                <span class="font-medium">₱{{ number_format($payroll['earnings_details'][$key] ?? 0, 2) }}</span>
                                            </div>
                                        @endforeach
                                        <div class="mt-1 flex justify-between gap-3 border-t border-gray-200 pt-1 font-semibold text-gray-800">
                                            <span>Gross pay</span>
                                            <span>₱{{ number_format($payroll['gross_pay'] ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                </details>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                <div class="font-medium">₱{{ number_format($payroll['deductions'] ?? 0, 2) }}</div>
                                @if(isset($payroll['deductions_details']))
                                    <details class="mt-1 text-xs text-gray-600">
                                        <summary class="cursor-pointer select-none text-blue-600 hover:text-blue-800">View breakdown</summary>
                                        <div class="mt-2 min-w-44 space-y-1 rounded border border-gray-200 bg-white p-2 shadow-sm">
                                            @foreach($deductionLabels as $key => $label)
                                                <div class="flex justify-between gap-3 {{ ($payroll['deductions_details'][$key] ?? 0) > 0 ? '' : 'text-gray-400' }}">
                                                    <span>{{ $label }}</span>
                                                    <span class="font-medium">₱{{ number_format($payroll['deductions_details'][$key] ?? 0, 2) }}</span>
                                                </div>
                                            @endforeach
                                            <div class="mt-1 flex justify-between gap-3 border-t border-gray-200 pt-1 font-semibold text-gray-800">
                                                <span>Total deductions</span>
                                                <span>₱{{ number_format($payroll['deductions'] ?? 0, 2) }}</span>
                                            </div>
                                            <div class="text-[11px] text-gray-500">Tax is displayed separately in the Tax column.</div>
                                            @if(($payroll['deductions_details']['deferred'] ?? 0) > 0)
                                                <div class="mt-1 border-t border-gray-200 pt-1 text-amber-700">
                                                    ₱{{ number_format($payroll['deductions_details']['deferred'], 2) }} capped/deferred to prevent negative net pay
                                                </div>
                                            @endif
                                        </div>
                                    </details>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₱{{ number_format($payroll['tax_amount'] ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                ₱{{ number_format($payroll['net_pay'] ?? 0, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr class="font-semibold">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" colspan="2">TOTAL</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($summaryData['total_basic_salary'] + $summaryData['total_holiday_basic_pay'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($summaryData['total_gross_pay'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">₱{{ number_format($summaryData['total_deductions'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($summaryData['total_tax'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">₱{{ number_format($summaryData['total_net_pay'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>


        <!-- Action Buttons -->
        <div class="mt-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-8 text-center">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Important</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Please review all calculations carefully. Confirming this preview creates the payroll records and moves the cutoff into payroll processing.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('attendance.period-management.show', $period['id']) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        
                        <form id="generate-payroll-form" method="POST" action="{{ route('payroll.periods.generate', $period['id']) }}" class="inline">
                            @csrf
                            <button type="button" onclick="openPayrollGenerationModal()" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                <i class="fas fa-check mr-2"></i>
                                Confirm & Generate Payroll
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.payroll-generation-modal', [
    'generationPeriodName' => $period['name'] ?? 'this cutoff',
    'generationEmployeeCount' => count($previewPayrolls),
    'generationGrossPay' => $summaryData['total_gross_pay'] ?? 0,
    'generationNetPay' => $summaryData['total_net_pay'] ?? 0,
])

@endsection
