@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'loans.index'])

@section('title', 'Loan History')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6">
            <a href="{{ route('loans.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-1"></i>Back to loans
            </a>
            <h1 class="text-2xl font-semibold text-gray-900 mt-2">{{ $employee->full_name }}'s Loan History</h1>
            <p class="text-sm text-gray-600">{{ $employee->department->name ?? 'N/A' }}</p>
        </div>

        @if($loans->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center text-gray-500">
            No loans on record for this employee.
        </div>
        @else
        <div class="space-y-4">
            @foreach($loans as $loan)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ $loan->loanType->name }}</div>
                        <div class="text-xs text-gray-500">Requested {{ $loan->created_at->format('M j, Y') }}</div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
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
                <div class="grid grid-cols-3 gap-4 mt-3 text-sm">
                    <div><span class="text-gray-500">Principal:</span> ₱{{ number_format((float) $loan->principal_amount, 2) }}</div>
                    @if($loan->status !== 'pending')
                    <div><span class="text-gray-500">Per Cutoff:</span> ₱{{ number_format((float) $loan->amortization_amount, 2) }}</div>
                    <div><span class="text-gray-500">Balance:</span> ₱{{ number_format((float) $loan->remaining_balance, 2) }}</div>
                    @endif
                </div>
                <a href="{{ route('loans.show', $loan) }}" class="inline-block mt-3 text-xs text-blue-600 hover:text-blue-700">View details →</a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection