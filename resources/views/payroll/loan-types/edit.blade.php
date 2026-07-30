@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'loan-types.index'])

@section('title', 'Edit Loan Type')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6">
            <a href="{{ route('loan-types.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-1"></i>Back to loan types
            </a>
            <h1 class="text-2xl font-semibold text-gray-900 mt-2">Edit Loan Type</h1>
        </div>

        @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('loan-types.update', $loanType) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf
            @method('PUT')
            @include('payroll.loan-types._form', ['loanType' => $loanType])
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('loan-types.index') }}" class="px-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100">Cancel</a>
                <button type="submit" class="inline-flex items-center px-5 py-2 bg-blue-600 rounded-lg font-medium text-white hover:bg-blue-700 text-sm">
                    <i class="fas fa-save mr-2"></i>Update Loan Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection