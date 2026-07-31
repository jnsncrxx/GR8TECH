@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'loans.index'])

@section('title', 'New Loan Request')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6">
            <a href="{{ route('loans.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-1"></i>Back to loans
            </a>
            <h1 class="text-2xl font-semibold text-gray-900 mt-2">New Loan Request</h1>
        </div>

        @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        @if($loanTypes->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>No active loan types exist yet.
            <a href="{{ route('loan-types.create') }}" class="underline font-medium">Create one first</a>.
        </div>
        @else
        <form action="{{ route('loans.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf

            <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-800">
                <i class="fas fa-user mr-2"></i>Requesting for: <strong>{{ $user->employee->full_name }}</strong>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Loan Type</label>
                <select name="loan_type_id" id="loan_type_id" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a loan type</option>
                    @foreach($loanTypes as $type)
                    <option value="{{ $type->id }}"
                            data-rate="{{ $type->default_interest_rate }}"
                            {{ old('loan_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }} ({{ number_format((float) $type->default_interest_rate, 2) }}% {{ $type->interest_type }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loan Amount</label>
                    <input type="number" step="0.01" min="1" name="principal_amount" id="principal_amount"
                           value="{{ old('principal_amount') }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term (months)</label>
                    <input type="number" min="1" max="60" name="term_months" id="term_months"
                           value="{{ old('term_months', 6) }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
            </div>

            <div id="preview" class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800 hidden">
                Total repayable: <strong id="previewTotal">—</strong> ·
                Per cutoff (semi-monthly): <strong id="previewPerCutoff">—</strong>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('loans.index') }}" class="px-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100">Cancel</a>
                <button type="submit" class="inline-flex items-center px-5 py-2 bg-blue-600 rounded-lg font-medium text-white hover:bg-blue-700 text-sm">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Request
                </button>
            </div>
        </form>
        @endif
    </div>
</div>

<script>
    function updatePreview() {
        const typeSelect = document.getElementById('loan_type_id');
        const rate = parseFloat(typeSelect.selectedOptions[0]?.dataset.rate ?? 0);
        const principal = parseFloat(document.getElementById('principal_amount').value) || 0;
        const term = parseInt(document.getElementById('term_months').value) || 0;
        const preview = document.getElementById('preview');

        if (!principal || !term || !typeSelect.value) {
            preview.classList.add('hidden');
            return;
        }

        const total = principal * (1 + rate / 100);
        const perMonth = total / term;
        const perCutoff = perMonth / 2;

        document.getElementById('previewTotal').textContent = '₱' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('previewPerCutoff').textContent = '₱' + perCutoff.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        preview.classList.remove('hidden');
    }

    ['loan_type_id', 'principal_amount', 'term_months'].forEach(id => {
        document.getElementById(id).addEventListener('input', updatePreview);
    });
    updatePreview();
</script>
@endsection