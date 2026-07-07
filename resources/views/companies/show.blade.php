@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'companies.index'])

@section('title', 'Company Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $company->name }}</h1>
            <p class="text-gray-600">Company details and contact information</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('companies.edit', $company) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-edit mr-2"></i>
                Edit Company
            </a>
            <a href="{{ route('companies.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Companies
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Overview</h3>
                        <p class="text-sm text-gray-500">General company information</p>
                    </div>
                    @if($company->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Company Code</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Website</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($company->website)
                                <a href="{{ $company->website }}" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-700">{{ $company->website }}</a>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">Description</label>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $company->description ?? 'No description provided.' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->address ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">City</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->city ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">State/Province</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->state ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Postal Code</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->postal_code ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Country</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->country ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Phone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tax ID</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->tax_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Registration Number</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->registration_number ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Current State</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->is_active ? 'Active' : 'Inactive' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Current Company</label>
                        @php
                            $currentCompany = \App\Helpers\CompanyHelper::getCurrentCompany();
                        @endphp
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $currentCompany && $currentCompany->id === $company->id ? 'Yes' : 'No' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection