@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employee.payroll.history'])

@section('title', 'Salary & Payslips')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Salary & Payslips</h1>
        <p class="mt-1 text-sm text-gray-600">Your approved and paid payroll history</p>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-gray-600">Pay Period</th>
                        <th class="px-5 py-3 text-right font-medium text-gray-600">Gross Pay</th>
                        <th class="px-5 py-3 text-right font-medium text-gray-600">Deductions</th>
                        <th class="px-5 py-3 text-right font-medium text-gray-600">Net Pay</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600">Payment Status</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600">Payslip</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payrolls as $payroll)
                        <tr>
                            <td class="px-5 py-4 font-medium text-gray-900">{{ $payroll->pay_period_start->format('M j') }}–{{ $payroll->pay_period_end->format('M j, Y') }}</td>
                            <td class="px-5 py-4 text-right text-gray-900">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                            <td class="px-5 py-4 text-right text-red-700">₱{{ number_format(max(0, $payroll->gross_pay - $payroll->net_pay), 2) }}</td>
                            <td class="px-5 py-4 text-right font-semibold text-gray-900">₱{{ number_format($payroll->net_pay, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $payroll->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">{{ $payroll->status === 'paid' ? 'Paid' : 'Approved' }}</span>
                                @if($payroll->paid_at)<span class="mt-1 block text-xs text-gray-500">{{ $payroll->paid_at->format('M j, Y g:i A') }}</span>@endif
                            </td>
                            <td class="px-5 py-4"><a href="{{ route('employee.payslip.download', $payroll->id) }}" class="font-medium text-blue-600 hover:text-blue-800"><i class="fas fa-download mr-1"></i>Download</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-gray-500">No approved payroll history is available yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-200 px-5 py-4">{{ $payrolls->links() }}</div>
    </div>
    <p class="text-sm text-gray-500">Draft, pending, rejected, and cancelled payroll records are not shown to employees.</p>
</div>
@endsection
