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

           
                    <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Breakdown</th>
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
                <div>{{ number_format($payroll['worked_hours'] ?? 0, 2) }} worked</div>
                <div class="text-xs text-gray-500">{{ number_format($payroll['scheduled_hours'] ?? 0, 2) }} scheduled</div>
                @if(($payroll['earnings_details']['overtime_hours'] ?? 0) > 0)
                    <div class="text-xs text-blue-600">{{ number_format($payroll['earnings_details']['overtime_hours'], 2) }} OT</div>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ₱{{ number_format($payroll['gross_pay'] ?? 0, 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-700">
                ₱{{ number_format(($payroll['deductions'] ?? 0) + ($payroll['tax_amount'] ?? 0), 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                ₱{{ number_format($payroll['net_pay'] ?? 0, 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    {{ ucfirst($payroll['status'] ?? 'preview') }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
                <button type="button"
                        data-preview-toggle="{{ $loop->index }}"
                        aria-controls="preview-breakdown-{{ $loop->index }}"
                        aria-expanded="false"
                        onclick="togglePreviewBreakdown('{{ $loop->index }}', this)"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span>View details</span>
                    <i class="fas fa-chevron-down ml-2 text-xs" aria-hidden="true"></i>
                </button>
            </td>
        </tr>
        <tr id="preview-breakdown-{{ $loop->index }}" data-preview-breakdown class="hidden bg-gray-50">
            <td colspan="8" class="px-6 py-5">
                @php
                    $earningComponents = [
                        'Basic Salary' => ['amount' => $payroll['earnings_details']['basic_salary'] ?? 0, 'note' => null],
                        'Allowance' => ['amount' => $payroll['earnings_details']['allowances'] ?? 0, 'note' => null],
                        'Paid Leave' => ['amount' => $payroll['earnings_details']['paid_leave'] ?? 0, 'note' => null],
                        'Bonuses' => ['amount' => $payroll['earnings_details']['bonuses'] ?? 0, 'note' => null],
                        'Holiday Pay' => ['amount' => $payroll['earnings_details']['holiday_pay'] ?? 0, 'note' => null],
                        'Overtime' => ['amount' => $payroll['earnings_details']['overtime'] ?? 0, 'note' => number_format($payroll['earnings_details']['overtime_hours'] ?? 0, 2) . ' hrs'],
                        'Night Differential' => ['amount' => $payroll['earnings_details']['night_differential'] ?? 0, 'note' => null],
                        'Rest Day Premium' => ['amount' => $payroll['earnings_details']['rest_day_premium'] ?? 0, 'note' => null],
                        'Other Earnings' => ['amount' => $payroll['earnings_details']['other'] ?? 0, 'note' => null],
                    ];
                    $deductionComponents = [
                        'Late' => ['amount' => $payroll['deductions_details']['total_late_deduction'] ?? 0, 'note' => ($payroll['deductions_details']['total_late_minutes'] ?? 0) . ' min'],
                        'Undertime' => ['amount' => $payroll['deductions_details']['undertime'] ?? 0, 'note' => ($payroll['deductions_details']['undertime_minutes'] ?? 0) . ' min'],
                        'Absence' => ['amount' => $payroll['deductions_details']['absence'] ?? 0, 'note' => null],
                        'Unpaid Leave' => ['amount' => $payroll['deductions_details']['unpaid_leave'] ?? 0, 'note' => null],
                        'SSS' => ['amount' => $payroll['deductions_details']['sss'] ?? 0, 'note' => null],
                        'PhilHealth' => ['amount' => $payroll['deductions_details']['philhealth'] ?? 0, 'note' => null],
                        'Pag-IBIG' => ['amount' => $payroll['deductions_details']['pagibig'] ?? 0, 'note' => null],
                        'Withholding Tax' => ['amount' => $payroll['tax_amount'] ?? 0, 'note' => null],
                        'Loan Amortization' => ['amount' => $payroll['deductions_details']['loan'] ?? 0, 'note' => null],
                        'Other Deductions' => ['amount' => $payroll['deductions_details']['other'] ?? 0, 'note' => null],
                    ];
                @endphp
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">{{ $payroll['employee_name'] ?? 'Unknown Employee' }} — Pay breakdown</h3>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg border border-green-200 overflow-hidden">
                        <div class="flex justify-between px-4 py-3 bg-green-50 border-b border-green-200">
                            <span class="text-sm font-semibold text-green-900">Earnings</span>
                            <span class="text-sm font-bold text-green-900">₱{{ number_format($payroll['gross_pay'] ?? 0, 2) }}</span>
                        </div>
                        <dl class="divide-y divide-gray-100">
                            @foreach($earningComponents as $label => $component)
                                <div class="flex justify-between px-4 py-2.5 text-sm">
                                    <dt class="text-gray-600">{{ $label }} @if($component['note'])<span class="text-xs text-gray-400">({{ $component['note'] }})</span>@endif</dt>
                                    <dd class="font-medium text-gray-900">₱{{ number_format($component['amount'], 2) }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                    <div class="bg-white rounded-lg border border-red-200 overflow-hidden">
                        <div class="flex justify-between px-4 py-3 bg-red-50 border-b border-red-200">
                            <span class="text-sm font-semibold text-red-900">Deductions</span>
                            <span class="text-sm font-bold text-red-900">₱{{ number_format(($payroll['deductions'] ?? 0) + ($payroll['tax_amount'] ?? 0), 2) }}</span>
                        </div>
                        <dl class="divide-y divide-gray-100">
                            @foreach($deductionComponents as $label => $component)
                                <div class="flex justify-between px-4 py-2.5 text-sm">
                                    <dt class="text-gray-600">{{ $label }} @if($component['note'])<span class="text-xs text-gray-400">({{ $component['note'] }})</span>@endif</dt>
                                    <dd class="font-medium text-gray-900">₱{{ number_format($component['amount'], 2) }}</dd>
                                </div>
                            @endforeach
                        </dl>
                        @if(($payroll['deductions_details']['deferred'] ?? 0) > 0)
                            <div class="px-4 py-2 text-xs text-amber-700 border-t border-gray-100">
                                ₱{{ number_format($payroll['deductions_details']['deferred'], 2) }} capped/deferred to prevent negative net pay
                            </div>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="bg-gray-50">
        <tr class="font-semibold">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" colspan="3">TOTAL</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($summaryData['total_gross_pay'], 2) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-700">₱{{ number_format($summaryData['total_deductions'] + $summaryData['total_tax'], 2) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">₱{{ number_format($summaryData['total_net_pay'], 2) }}</td>
            <td class="px-6 py-4 whitespace-nowrap"></td>
            <td class="px-6 py-4 whitespace-nowrap"></td>
        </tr>
    </tfoot>
</table>


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

<script>
function togglePreviewBreakdown(id, button) {
    const target = document.getElementById('preview-breakdown-' + id);
    if (!target) return;
    const isHidden = target.classList.contains('hidden');
    document.querySelectorAll('[data-preview-breakdown]').forEach(row => row.classList.add('hidden'));
    document.querySelectorAll('[data-preview-toggle]').forEach(btn => btn.setAttribute('aria-expanded', 'false'));
    if (isHidden) {
        target.classList.remove('hidden');
        button.setAttribute('aria-expanded', 'true');
    }
}
</script>


@include('components.payroll-generation-modal', [
    'generationPeriodName' => $period['name'] ?? 'this cutoff',
    'generationEmployeeCount' => count($previewPayrolls),
    'generationGrossPay' => $summaryData['total_gross_pay'] ?? 0,
    'generationNetPay' => $summaryData['total_net_pay'] ?? 0,
])

@endsection
