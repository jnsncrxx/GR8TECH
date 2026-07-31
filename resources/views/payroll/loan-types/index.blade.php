@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'loan-types.index'])

@section('title', 'Loan Types')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Loan Types</h1>
                <p class="text-sm text-gray-600 mt-1">Configurable loan categories with default interest and amortization rules</p>
            </div>
            <a href="{{ route('loan-types.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i>Add Loan Type
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

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($loanTypes->isEmpty())
            <div class="text-center py-16">
                <i class="fas fa-hand-holding-dollar text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No loan types yet.</p>
                <a href="{{ route('loan-types.create') }}" class="inline-flex items-center mt-3 text-blue-600 hover:text-blue-700 text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i>Create your first loan type
                </a>
            </div>
            @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loans</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($loanTypes as $type)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $type->name }}</div>
                            @if($type->description)
                            <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($type->description, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ number_format((float) $type->default_interest_rate, 2) }}%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">{{ $type->interest_type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $type->loans_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($type->is_active)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-3">
                            <a href="{{ route('loan-types.edit', $type) }}" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('loan-types.destroy', $type) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Delete loan type {{ $type->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection