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

    <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
        <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-file-alt text-2xl text-blue-600"></i>
        </div>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $reportName }}</h2>
        <p class="text-gray-600 max-w-md mx-auto">{{ $message }}</p>
        <div class="mt-6">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-lg text-sm text-gray-700">
                <i class="fas fa-clock"></i>
                <span>Report period: {{ $startDate }} to {{ $endDate }}</span>
            </div>
        </div>
    </div>

    @if($company)
    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
        <p class="text-sm text-gray-600"><strong>Company:</strong> {{ $company->name }}</p>
    </div>
    @endif
</div>
@endsection
