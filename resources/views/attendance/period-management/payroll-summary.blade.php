@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'payroll.runs'])

@section('title', 'Payroll Summary - ' . $period['name'])

@php
    function formatCurrency($amount) {
        return '₱' . number_format($amount, 2);
    }
@endphp

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Payroll Summary</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($period['start_date'])->format('M j, Y') }} - 
                            {{ \Carbon\Carbon::parse($period['end_date'])->format('M j, Y') }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">{{ $period['name'] }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('payroll.periods.export', $period['id']) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Export CSV
                        </a>
                        <a href="{{ route('payroll.runs') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Payroll
                        </a>
                        <a href="{{ route('attendance.period-management.show', $period['id']) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-700 hover:text-blue-900">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Source Period
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
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

            <!-- Total Gross Pay -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-money-bill-wave text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">₱{{ number_format($summaryData['total_gross_pay'], 2) }}</h3>
                        <p class="text-xs text-gray-600">Gross Pay</p>
                    </div>
                </div>
            </div>

            <!-- Total Deductions -->
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

            <!-- Total Net Pay -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-wallet text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">₱{{ number_format($summaryData['total_net_pay'], 2) }}</h3>
                        <p class="text-xs text-gray-600">Net Pay</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Payroll Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Payroll Details</h2>
                    <div class="flex space-x-3">
                        <button onclick="exportToCSV()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-download mr-2"></i>
                            Export CSV
                        </button>
                        <button onclick="printPayroll()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-print mr-2"></i>
                            Print
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department / Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Breakdown</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payrolls as $payroll)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payroll->employee->employee_id }}</div>
                                <div class="text-sm text-gray-500">{{ $payroll->employee->full_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $payroll->employee->department->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $payroll->employee->position->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ number_format($payroll->worked_hours, 2) }} worked</div>
                                <div class="text-xs text-gray-500">{{ number_format($payroll->scheduled_hours, 2) }} scheduled</div>
                                @if($payroll->overtime_hours > 0)
                                    <div class="text-xs text-blue-600">{{ number_format($payroll->overtime_hours, 2) }} OT</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ₱{{ number_format($payroll->gross_pay, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-700">
                                ₱{{ number_format($payroll->deductions + $payroll->tax_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                ₱{{ number_format($payroll->net_pay, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($payroll->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($payroll->status === 'processed') bg-blue-100 text-blue-800
                                    @elseif(in_array($payroll->status, ['approved', 'paid'], true)) bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button type="button"
                                        data-payroll-toggle="{{ $payroll->id }}"
                                        aria-controls="payroll-breakdown-{{ $payroll->id }}"
                                        aria-expanded="false"
                                        onclick="togglePayrollBreakdown('{{ $payroll->id }}', this)"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span>View details</span>
                                    <i class="fas fa-chevron-down ml-2 text-xs" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                        <tr id="payroll-breakdown-{{ $payroll->id }}" data-payroll-breakdown class="hidden bg-gray-50">
                            <td colspan="8" class="px-6 py-5">
                                @php
                                   $earningComponents = [
                                        'Basic Salary' => ['amount' => $payroll->basic_salary, 'note' => null],
                                        'Allowance' => ['amount' => $payroll->allowances, 'note' => null],
                                        'Bonuses' => ['amount' => $payroll->bonuses, 'note' => null],
                                        'Regular Holiday' => ['amount' => $payroll->holiday_basic_pay + $payroll->holiday_premium, 'note' => null],
                                        'Special Holiday' => ['amount' => $payroll->special_holiday_premium, 'note' => null],
                                        'Overtime' => ['amount' => $payroll->overtime_pay, 'note' => number_format($payroll->overtime_hours, 2) . ' hrs'],
                                        'Night Differential' => ['amount' => $payroll->night_differential_pay, 'note' => null],
                                        'Rest Day Premium' => ['amount' => $payroll->rest_day_premium_pay, 'note' => null],
                                        'Other Earnings' => ['amount' => $payroll->other_earnings, 'note' => null],
                                    ];
                                    $deductionComponents = [
                                        'Late' => ['amount' => $payroll->late_deduction, 'note' => $payroll->late_minutes . ' min'],
                                        'Undertime' => ['amount' => $payroll->undertime_deduction, 'note' => $payroll->undertime_minutes . ' min'],
                                        'Absence' => ['amount' => $payroll->absence_deduction, 'note' => null],
                                        'Unpaid Leave' => ['amount' => $payroll->unpaid_leave_deduction, 'note' => null],
                                        'SSS' => ['amount' => $payroll->sss, 'note' => null],
                                        'PhilHealth' => ['amount' => $payroll->phic, 'note' => null],
                                        'Pag-IBIG' => ['amount' => $payroll->hdmf, 'note' => null],
                                        'Withholding Tax' => ['amount' => $payroll->tax_amount, 'note' => null],
                                        'Loan Amortization' => ['amount' => $payroll->loan_deduction, 'note' => null],
                                        'Other Deductions' => ['amount' => $payroll->other_deductions, 'note' => null],
                                    ];
                                @endphp
                               <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $payroll->employee->full_name }} — Pay breakdown</h3>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="bg-white rounded-lg border border-green-200 overflow-hidden">
                                        <div class="flex justify-between px-4 py-3 bg-green-50 border-b border-green-200">
                                            <span class="text-sm font-semibold text-green-900">Earnings</span>
                                            <span class="text-sm font-bold text-green-900">₱{{ number_format($payroll->gross_pay, 2) }}</span>
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
                                            <span class="text-sm font-bold text-red-900">₱{{ number_format($payroll->deductions + $payroll->tax_amount, 2) }}</span>
                                        </div>
                                        <dl class="divide-y divide-gray-100">
                                            @foreach($deductionComponents as $label => $component)
                                                <div class="flex justify-between px-4 py-2.5 text-sm">
                                                    <dt class="text-gray-600">{{ $label }} @if($component['note'])<span class="text-xs text-gray-400">({{ $component['note'] }})</span>@endif</dt>
                                                    <dd class="font-medium text-gray-900">₱{{ number_format($component['amount'], 2) }}</dd>
                                                </div>
                                            @endforeach
                                        </dl>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-700">₱{{ number_format($summaryData['total_deductions'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">₱{{ number_format($summaryData['total_net_pay'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap"></td>
                            <td class="px-6 py-4 whitespace-nowrap"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if(empty($payrolls))
            <div class="text-center py-12">
                <div class="mx-auto h-16 w-16 text-gray-400">
                    <i class="fas fa-calculator text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No payroll records found</h3>
                <p class="mt-2 text-sm text-gray-600">No payroll data is available. Return to the period, review its payroll preview, then confirm generation.</p>
                <div class="mt-6">
                    <a href="{{ route('attendance.period-management.show', $period['id']) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-white hover:bg-green-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Return to Period Review
                    </a>
                </div>
            </div>
        @endif

        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Payroll Review Actions</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Current status: <strong>{{ $periodModel->status_label }}</strong>
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @if($periodModel->status === \App\Models\Period::STATUS_PROCESSING)
                        <form id="submit-payroll-review-form" method="POST" action="{{ route('attendance.period-management.submit-for-review', $periodModel->id) }}">
                            @csrf
                            <button type="button" onclick="openAppConfirmationModal('submit-payroll-review-form', 'Submit payroll for review?', 'This sends the corrected payroll to the final review stage.', 'Submit for Review', 'blue')" class="px-4 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700">
                                Submit for Review
                            </button>
                        </form>
                    @elseif($periodModel->status === \App\Models\Period::STATUS_FOR_REVIEW)
                        <form id="return-payroll-processing-form" method="POST" action="{{ route('payroll.periods.return-to-processing', $periodModel->id) }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="reason" required maxlength="1000"
                                   placeholder="Reason for correction"
                                   class="rounded-lg border-gray-300 text-sm">
                            <button type="button" onclick="openAppConfirmationModal('return-payroll-processing-form', 'Return payroll for correction?', 'The payroll will return to Processing using the correction reason entered beside this button.', 'Return to Processing', 'amber')" class="px-4 py-2 rounded-lg border border-yellow-300 bg-yellow-50 text-yellow-800 font-medium hover:bg-yellow-100">
                                Return to Processing
                            </button>
                        </form>

                        <form id="finalize-payroll-form" method="POST" action="{{ route('payroll.periods.finalize', $periodModel->id) }}">
                            @csrf
                            <button type="button" onclick="openAppConfirmationModal('finalize-payroll-form', 'Finalize payroll?', 'Confirm that employee earnings, deductions, tax, and net pay have been reviewed. Finalization prepares this run for locking.', 'Finalize Payroll', 'green')" class="px-4 py-2 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700">
                                Finalize Payroll
                            </button>
                        </form>
                    @elseif($periodModel->status === \App\Models\Period::STATUS_FINALIZED)
                        <form id="lock-payroll-form-{{ $periodModel->id }}" method="POST" action="{{ route('payroll.periods.lock', $periodModel->id) }}">
                            @csrf
                            <button type="button"
                                    onclick="openPayrollLockModal('lock-payroll-form-{{ $periodModel->id }}', @js($periodModel->name))"
                                    class="inline-flex items-center px-4 py-2 rounded-lg border-2 border-amber-800 bg-amber-400 text-black font-bold shadow-sm hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2">
                                <i class="fas fa-lock mr-2"></i>Lock Payroll
                            </button>
                        </form>

                    
                    @elseif($periodModel->status === \App\Models\Period::STATUS_LOCKED)
                        <div class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-700 font-medium">
                            <i class="fas fa-lock mr-2"></i>
                            Locked — View/Export Only
                        </div>

                        <p class="text-sm text-gray-500 mt-2">
                            This payroll period has been locked and can no longer be modified.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.payroll-lock-modal')

@include('components.confirmation-modal')

<script>
function exportToCSV() {
    window.location.href = "{{ route('attendance.period-management.export-payroll', $period['id']) }}";
}

function printPayroll() {
    window.print();
}

function togglePayrollBreakdown(id, button) {
    const target = document.getElementById('payroll-breakdown-' + id);
    const willOpen = target.classList.contains('hidden');

    document.querySelectorAll('[data-payroll-breakdown]').forEach(row => row.classList.add('hidden'));
    document.querySelectorAll('[data-payroll-toggle]').forEach(toggle => {
        toggle.setAttribute('aria-expanded', 'false');
        toggle.querySelector('span').textContent = 'View details';
        toggle.querySelector('i').classList.remove('fa-chevron-up');
        toggle.querySelector('i').classList.add('fa-chevron-down');
    });

    if (willOpen) {
        target.classList.remove('hidden');
        button.setAttribute('aria-expanded', 'true');
        button.querySelector('span').textContent = 'Hide details';
        button.querySelector('i').classList.remove('fa-chevron-down');
        button.querySelector('i').classList.add('fa-chevron-up');
    }
}

/*function toggleZeroComponents(id, button) {
    const zeroRows = Array.from(document.querySelectorAll('[data-zero-for="' + id + '"][data-is-zero="true"]'));
    const willShow = zeroRows.some(row => row.classList.contains('hidden'));
    zeroRows.forEach(row => row.classList.toggle('hidden', !willShow));
    button.textContent = willShow ? 'Hide zero components' : 'Show zero components';
}*/

// Auto-refresh every 30 seconds if there are pending payrolls
@if($payrolls->where('status', 'pending')->count() > 0)
setTimeout(function() {
    location.reload();
}, 30000);
@endif
</script>
@endsection