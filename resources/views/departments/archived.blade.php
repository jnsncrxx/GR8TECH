@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'departments.index'])

@section('title', 'Archived Departments')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Archived Departments</h1>
            <p class="mt-1 text-sm text-gray-600">Departments that have been removed from the active list</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('departments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Departments
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <form method="GET" action="{{ route('departments.archived') }}" class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search archived departments..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
            </div>
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 lg:flex-shrink-0">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-1"></i>Search
                </button>
                @if(request('search'))
                <a href="{{ route('departments.archived') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-1"></i>Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Archived Departments Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($departments as $department)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col opacity-90">
            <!-- Department Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $department->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $department->department_id }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-box-archive mr-1"></i>
                            Archived
                        </span>
                    </div>
                </div>
            </div>

            <!-- Department Details -->
            <div class="p-6 space-y-4 flex-1">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Employees</h4>
                    <p class="text-sm text-gray-600 flex items-center">
                        <i class="fas fa-users mr-2 text-gray-400"></i>
                        {{ $department->employees_count }} {{ \Illuminate\Support\Str::plural('employee', $department->employees_count) }}
                    </p>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Description</h4>
                    <p class="text-sm text-gray-600">{{ $department->description ?: 'No description provided' }}</p>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Archived On</h4>
                    <p class="text-sm text-gray-600 flex items-center">
                        <i class="fas fa-calendar-times mr-2 text-gray-400"></i>
                        {{ $department->archived_at?->format('M d, Y g:i A') }}
                    </p>
                </div>
            </div>

            <!-- Department Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 mt-auto">
                <div class="flex items-center justify-end">
                    <form method="POST" action="{{ route('departments.restore', $department) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 hover:text-green-900 transition-colors">
                            <i class="fas fa-rotate-left mr-1"></i>Restore
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-box-archive text-4xl mb-4"></i>
                    <p class="text-lg font-medium">No archived departments</p>
                    <p class="text-sm">Departments you remove will show up here.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($departments->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 rounded-lg">
        {{ $departments->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
