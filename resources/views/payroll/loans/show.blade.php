@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'loans.index'])

@section('title', 'Loan Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6">
            <a href="{{ route('loans.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-1"></i>Back to loans
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">{{ $loan->employee->full_name }}</h1>
                    <p class="text-sm text-gray-500">{{ $loan->loanType->name }} · Requested {{ $loan->created_at->format('M j, Y') }}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    {{ match($loan->status) {
                        'pending' => 'bg-amber-100 text-amber-700',
                        'approved' => 'bg-blue-100 text-blue-700',
                        'completed' => 'bg-green-100 text-green-700',
                        'rejected', 'cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-600',
                    } }}">
                    {{ ucfirst($loan->status) }}
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-gray-500">Principal</div>
                    <div class="font-medium text-gray-900">₱{{ number_format((float) $loan->principal_amount, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Interest Rate</div>
                    <div class="font-medium text-gray-900">{{ number_format((float) $loan->interest_rate, 2) }}% ({{ $loan->interest_type }})</div>
                </div>
                <div>
                    <div class="text-gray-500">Term</div>
                    <div class="font-medium text-gray-900">{{ $loan->term_months }} months</div>
                </div>
                @if($loan->status !== 'pending')
                <div>
                    <div class="text-gray-500">Total Repayable</div>
                    <div class="font-medium text-gray-900">₱{{ number_format((float) $loan->total_repayable, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Per Cutoff</div>
                    <div class="font-medium text-gray-900">₱{{ number_format((float) $loan->amortization_amount, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Remaining Balance</div>
                    <div class="font-medium text-gray-900">₱{{ number_format((float) $loan->remaining_balance, 2) }}</div>
                </div>
                @endif
            </div>

            @if($loan->notes)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="text-xs text-gray-500 mb-1">Notes</div>
                <p class="text-sm text-gray-700">{{ $loan->notes }}</p>
            </div>
            @endif

            @if($loan->status === 'rejected' && $loan->rejection_reason)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="text-xs text-red-500 mb-1">Rejection Reason</div>
                <p class="text-sm text-red-700">{{ $loan->rejection_reason }}</p>
            </div>
            @endif

            @if($loan->status === 'pending' && in_array($user->role, ['admin', 'hr']))
            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden'); document.getElementById('rejectModal').classList.add('flex')"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50">
                    Reject
                </button>
                <form action="{{ route('loans.approve', $loan) }}" method="POST"
                      onsubmit="return confirm('Approve this loan?');">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>Approve Loan
                    </button>
                </form>
            </div>
            @elseif($loan->status === 'pending')
            <div class="mt-6 pt-4 border-t border-gray-100 text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>Waiting for HR/Admin approval.
            </div>
            @endif
        </div>

        @if($loan->payments->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Payment History</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Balance After</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($loan->payments as $payment)
                    <tr>
                        <td class="px-6 py-3 text-sm text-gray-700">{{ $payment->payment_date->format('M j, Y') }}</td>
                        <td class="px-6 py-3 text-sm text-gray-700">₱{{ number_format((float) $payment->amount, 2) }}</td>
                        <td class="px-6 py-3 text-sm text-gray-700">₱{{ number_format((float) $payment->balance_after, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-gray-900/50 p-4 overflow-y-auto">
    <div class="relative w-full max-w-md my-8 max-h-[calc(100vh-4rem)] overflow-y-auto rounded-lg bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Loan Request</h3>
        <form action="{{ route('loans.reject', $loan) }}" method="POST">
            @csrf
            <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
            <textarea name="rejection_reason" rows="3" required class="w-full rounded-lg border-gray-300 mb-4" placeholder="Explain why this request is being rejected..."></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden'); document.getElementById('rejectModal').classList.remove('flex')" class="px-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700">Reject</button>
            </div>
        </form>
    </div>
</div>
@endsection