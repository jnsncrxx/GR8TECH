@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'attendance.official-business'])

@section('title', 'Official Business')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-input[readonly] {
        background-color: #ffffff;
        color: #111827;
    }

    /* Match the Leave Management Flatpickr design. */
    .flatpickr-calendar {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        width: 320px !important;
        padding: 0.5rem;
    }

    .flatpickr-months {
        background: transparent;
        padding: 0.5rem 0.25rem;
        margin-bottom: 0.25rem;
    }

    .flatpickr-month {
        color: #111827;
        height: 40px;
    }

    .flatpickr-current-month {
        color: #111827;
        font-weight: 600;
        font-size: 1rem;
        padding-top: 0.25rem;
    }

    .flatpickr-weekdays {
        background: transparent;
        padding: 0.25rem 0;
    }

    .flatpickr-weekday {
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
    }

    .flatpickr-day {
        color: #111827;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        height: 38px;
        line-height: 38px;
        margin: 2px;
        max-width: 38px;
        width: 38px;
        transition: all 0.15s ease;
    }

    .flatpickr-day:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .flatpickr-day.selected {
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        font-weight: 600;
    }

    .flatpickr-day.today {
        border-color: #2563eb;
        font-weight: 600;
        background: transparent;
    }

    .flatpickr-day.occupied-pending {
        background: #fff6d4 !important;
        color: #92400e !important;
        border-color: #f7b441 !important;
        font-weight: 600;
        cursor: not-allowed !important;
    }

    .flatpickr-day.occupied-approved {
        background: #fecaca !important;
        color: #991b1b !important;
        border-color: #ef4444 !important;
        text-decoration: line-through;
        opacity: 0.85;
        cursor: not-allowed !important;
    }
</style>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Official Business</h1>
            @if($isReviewer)
                <p class="mt-1 text-sm text-gray-600">Review and manage employee OB requests</p>
            @else
                <p class="mt-1 text-sm text-gray-600">Submit and track your Official Business requests</p>
            @endif
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export Report
                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                </button>

                <div x-show="open" x-cloak @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
                    <div class="py-1">
                        <a href="{{ route('attendance.official-business.export', ['format' => 'pdf']) . '?' . http_build_query(request()->query()) }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-pdf mr-2 text-red-500"></i>Export as PDF
                        </a>
                        <a href="{{ route('attendance.official-business.export', ['format' => 'csv']) . '?' . http_build_query(request()->query()) }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-csv mr-2 text-green-500"></i>Export as CSV
                        </a>
                        <a href="{{ route('attendance.official-business.export', ['format' => 'xls']) . '?' . http_build_query(request()->query()) }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2 text-green-600"></i>Export as Excel
                        </a>
                    </div>
                </div>
            </div>

            @unless($isReviewer)
            <button id="applyObBtn" onclick="openObModal()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Apply for Official Business
            </button>
            @endunless
        </div>
    </div>

    @if(session('success'))
        <div class="p-3 rounded-lg bg-green-50 text-green-700 border border-green-200 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($isReviewer)
        @if($reviewerRole === 'manager')
            <div class="p-3 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-sm">
                <i class="fas fa-circle-check mr-1"></i>
                You're the primary approver. Pending requests are shown first below.
            </div>
        @else
            <div class="p-3 rounded-lg bg-gray-50 text-gray-600 border border-gray-200 text-sm">
                <i class="fas fa-circle-info mr-1"></i>
                You're viewing as a backup approver ({{ ucfirst($reviewerRole ?? 'reviewer') }}). Managers are notified first for pending requests, but you can approve or reject any request here if needed.
            </div>
        @endif
    @endif

    <!-- Official Business Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Approved</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['approved'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['pending'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Rejected</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['rejected'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock-rotate-left text-gray-500"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Expired</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['expired'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <form method="GET"
              action="{{ route('attendance.official-business') }}"
              class="space-y-4">

            @if($isReviewer)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Department
                        </label>
                        <select id="department_id"
                                name="department_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}"
                                        {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Employee
                        </label>
                        <select id="employee_id"
                                name="employee_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}"
                                        data-department-id="{{ $employee->department_id }}"
                                        {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }}
                                    @if($employee->department)
                                        - {{ $employee->department->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select id="status"
                                name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                            From Date
                        </label>
                        <input type="date"
                               id="date_from"
                               name="date_from"
                               value="{{ request('date_from') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                            To Date
                        </label>
                        <input type="date"
                               id="date_to"
                               name="date_to"
                               value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select id="status"
                                name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date
                        </label>
                        <input type="date"
                               id="date"
                               name="date"
                               value="{{ request('date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                <a href="{{ route('attendance.official-business') }}"
                   class="inline-flex items-center justify-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Clear Filters
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-search mr-2"></i>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    @if($isReviewer)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const departmentSelect = document.getElementById('department_id');
                const employeeSelect = document.getElementById('employee_id');

                if (!departmentSelect || !employeeSelect) {
                    return;
                }

                const filterEmployees = () => {
                    const selectedDepartment = departmentSelect.value;

                    Array.from(employeeSelect.options).forEach((option, index) => {
                        if (index === 0) {
                            option.hidden = false;
                            return;
                        }

                        option.hidden = selectedDepartment !== ''
                            && option.dataset.departmentId !== selectedDepartment;
                    });

                    const selectedEmployee = employeeSelect.options[employeeSelect.selectedIndex];

                    if (selectedEmployee && selectedEmployee.hidden) {
                        employeeSelect.value = '';
                    }
                };

                departmentSelect.addEventListener('change', filterEmployees);
                filterEmployees();
            });
        </script>
    @endif

    <!-- Official Business Records -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Official Business Records</h3>
            <p class="mt-1 text-sm text-gray-600">Employee OB requests and approvals</p>
        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Time In
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Time Out
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Hours
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Reason
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Reviewed By
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($obRequests as $ob)
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'expired' => 'bg-gray-200 text-gray-600',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                            ];
                            $obStatus = $ob->status;
                            $statusColor = $statusColors[$obStatus] ?? 'bg-gray-100 text-gray-600';
                            $initials = strtoupper(substr($ob->employee->first_name ?? '', 0, 1) . substr($ob->employee->last_name ?? '', 0, 1));
                            $reviewerName = trim(($ob->reviewer?->employee?->first_name ?? '') . ' ' . ($ob->reviewer?->employee?->last_name ?? ''));
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors" data-search-row="{{ $ob->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ $initials }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $ob->employee->first_name ?? '' }} {{ $ob->employee->last_name ?? '' }}</div>
                                        <div class="text-sm text-gray-500">{{ $ob->employee->department->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $ob->date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($ob->ob_start_time)
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($ob->ob_start_time)->format('h:i A') }}
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($ob->ob_end_time)
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($ob->ob_end_time)->format('h:i A') }}
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($ob->ob_start_time && $ob->ob_end_time)
                                    <div class="text-sm font-semibold {{ $obStatus === 'approved' ? 'text-green-700' : 'text-gray-700' }}">
                                        {{ number_format((float) ($ob->credited_hours ?? $ob->computeCreditedHours()), 2) }} hrs
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $ob->reason }}">
                                    {{ \Illuminate\Support\Str::limit($ob->reason, 30) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }} min-w-[80px]">
                                    {{ ucfirst($obStatus) }}
                                </span>
                                @if($obStatus === 'rejected' && $ob->rejection_reason)
                                    <div class="text-xs text-red-600 mt-1 max-w-[220px] mx-auto" title="{{ $ob->rejection_reason }}">
                                        <span class="font-medium">Admin Reason:</span>
                                        {{ \Illuminate\Support\Str::limit($ob->rejection_reason, 40) }}
                                    </div>
                                @elseif($obStatus === 'cancelled' && $ob->rejection_reason)
                                    <div class="text-xs text-gray-600 mt-1 max-w-[220px] mx-auto" title="{{ $ob->rejection_reason }}">
                                        <span class="font-medium">Cancellation Reason:</span>
                                        {{ \Illuminate\Support\Str::limit($ob->rejection_reason, 40) }}
                                    </div>
                                @endif
                                @include('attendance.partials.request-expiry-workflow', [
                                    'requestRecord' => $ob,
                                    'resubmitRoute' => route('attendance.official-business.resubmit', $ob->id),
                                    'showAction' => false,
                                ])
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $reviewerName !== '' ? $reviewerName : '—' }}</div>
                                @if($ob->reviewed_at)
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($ob->reviewed_at)->format('M d, Y h:i A') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center items-center space-x-2">
                                    @if($ob->isPending() && $ob->employee_id === $currentEmployeeId)
                                        <button type="button" onclick="openObEditModal({{ Illuminate\Support\Js::from(['id' => $ob->id, 'date' => $ob->date->format('Y-m-d'), 'start_time' => \Carbon\Carbon::parse($ob->ob_start_time)->format('H:i'), 'end_time' => \Carbon\Carbon::parse($ob->ob_end_time)->format('H:i'), 'reason' => $ob->reason]) }})"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-violet-200 bg-violet-50 text-violet-600 hover:bg-violet-100 transition-colors" title="Edit pending OB request"><i class="fas fa-pen"></i></button>
                                        <button onclick="cancelOb('{{ $ob->id }}', false)" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-orange-200 bg-orange-50 text-orange-600 hover:bg-orange-100 transition-colors" title="Cancel"><i class="fas fa-ban"></i></button>
                                    @elseif($ob->isPending() && $isReviewer && $ob->employee_id !== $currentEmployeeId)
                                        <button onclick="approveOb('{{ $ob->id }}')"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-green-200 bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-900 transition-colors"
                                                title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="rejectOb('{{ $ob->id }}')"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-900 transition-colors"
                                                title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @elseif($ob->isApproved() && $isReviewer)
                                        <button onclick="cancelOb('{{ $ob->id }}', true)"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-orange-200 bg-orange-50 text-orange-600 hover:bg-orange-100 hover:text-orange-900 transition-colors"
                                                title="Cancel approved OB">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @elseif($ob->canBeResubmitted() && $ob->employee_id === $currentEmployeeId)
                                        @include('attendance.partials.request-expiry-workflow', [
                                            'requestRecord' => $ob,
                                            'resubmitRoute' => route('attendance.official-business.resubmit', $ob->id),
                                        ])
                                    @else
                                        <span class="inline-block w-8 h-px bg-gray-300 rounded-full"></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <i class="fas fa-briefcase text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-500 text-lg font-medium mb-2">No official business requests found</p>
                                    <p class="text-gray-400 text-sm">Try adjusting your filters or date range.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($obRequests->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $obRequests->firstItem() }} to {{ $obRequests->lastItem() }} of {{ $obRequests->total() }} results
                </div>
                <div class="flex items-center space-x-2">
                    @if($obRequests->onFirstPage())
                        <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded">Previous</span>
                    @else
                        <a href="{{ $obRequests->previousPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif

                    @for($i = 1; $i <= $obRequests->lastPage(); $i++)
                        @if($i == $obRequests->currentPage())
                            <span class="px-3 py-2 text-sm text-white bg-blue-600 rounded">{{ $i }}</span>
                        @else
                            <a href="{{ $obRequests->url($i) }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $i }}</a>
                        @endif
                    @endfor

                    @if($obRequests->hasMorePages())
                        <a href="{{ $obRequests->nextPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                    @else
                        <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded">Next</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Mobile Cards -->
        <div class="lg:hidden">
            <div class="p-4 space-y-4">
                @forelse($obRequests as $ob)
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'expired' => 'bg-gray-200 text-gray-600',
                            'cancelled' => 'bg-gray-100 text-gray-800',
                        ];
                        $obStatus = $ob->status;
                        $statusColor = $statusColors[$obStatus] ?? 'bg-gray-100 text-gray-600';
                        $initials = strtoupper(substr($ob->employee->first_name ?? '', 0, 1) . substr($ob->employee->last_name ?? '', 0, 1));
                       $reviewerName = trim(($ob->reviewer?->employee?->first_name ?? '') . ' ' . ($ob->reviewer?->employee?->last_name ?? ''));
                    @endphp
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ $initials }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $ob->employee->first_name ?? '' }} {{ $ob->employee->last_name ?? '' }}</div>
                                    <div class="text-sm text-gray-500">{{ $ob->employee->department->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ ucfirst($obStatus) }}
                                </span>
                                @if($obStatus === 'rejected' && $ob->rejection_reason)
                                    <div class="text-xs text-red-600 mt-1 max-w-[180px]">
                                        <span class="font-medium">Admin Reason:</span>
                                        {{ \Illuminate\Support\Str::limit($ob->rejection_reason, 35) }}
                                    </div>
                                @elseif($obStatus === 'cancelled' && $ob->rejection_reason)
                                    <div class="text-xs text-gray-600 mt-1 max-w-[180px]">
                                        <span class="font-medium">Cancellation Reason:</span>
                                        {{ \Illuminate\Support\Str::limit($ob->rejection_reason, 35) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                            <div>
                                <div class="text-gray-500">Date</div>
                                <div class="font-medium">{{ $ob->date->format('M d, Y') }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Time In</div>
                                <div class="font-medium">
                                    {{ $ob->ob_start_time ? \Carbon\Carbon::parse($ob->ob_start_time)->format('h:i A') : '—' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500">Time Out</div>
                                <div class="font-medium">
                                    {{ $ob->ob_end_time ? \Carbon\Carbon::parse($ob->ob_end_time)->format('h:i A') : '—' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500">Total Hours</div>
                                @if($ob->ob_start_time && $ob->ob_end_time)
                                    <div class="font-semibold {{ $obStatus === 'approved' ? 'text-green-700' : 'text-gray-900' }}">
                                        {{ number_format((float) ($ob->credited_hours ?? $ob->computeCreditedHours()), 2) }} hrs
                                    </div>
                                @else
                                    <div class="font-medium">—</div>
                                @endif
                            </div>
                            <div>
                                <div class="text-gray-500">Reviewed By</div>
                                <div class="font-medium">{{ $reviewerName !== '' ? $reviewerName : '—' }}</div>
                                @if($ob->reviewed_at)
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($ob->reviewed_at)->format('M d, Y h:i A') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="text-sm mb-3">
                            <div class="text-gray-500">Reason</div>
                            <div class="font-medium">{{ \Illuminate\Support\Str::limit($ob->reason, 50) }}</div>
                        </div>
                        @include('attendance.partials.request-expiry-workflow', [
                            'requestRecord' => $ob,
                            'resubmitRoute' => route('attendance.official-business.resubmit', $ob->id),
                            'showAction' => false,
                        ])
                        @if($ob->isPending() || ($ob->isApproved() && $isReviewer) || ($ob->canBeResubmitted() && $ob->employee_id === $currentEmployeeId))
                        <div class="flex justify-end items-center space-x-2">
                            @if($ob->isPending() && $ob->employee_id === $currentEmployeeId)
                                <button type="button" onclick="openObEditModal({{ Illuminate\Support\Js::from(['id' => $ob->id, 'date' => $ob->date->format('Y-m-d'), 'start_time' => \Carbon\Carbon::parse($ob->ob_start_time)->format('H:i'), 'end_time' => \Carbon\Carbon::parse($ob->ob_end_time)->format('H:i'), 'reason' => $ob->reason]) }})"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-violet-200 bg-violet-50 text-violet-600" title="Edit pending OB request"><i class="fas fa-pen"></i></button>
                                <button onclick="cancelOb('{{ $ob->id }}', false)" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-orange-200 bg-orange-50 text-orange-600" title="Cancel"><i class="fas fa-ban"></i></button>
                            @elseif($ob->isPending() && $isReviewer && $ob->employee_id !== $currentEmployeeId)
                                <button onclick="approveOb('{{ $ob->id }}')"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-green-200 bg-green-50 text-green-600 hover:bg-green-100 transition-colors"
                                        title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="rejectOb('{{ $ob->id }}')"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                        title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            @elseif($ob->isApproved() && $isReviewer)
                                <button onclick="cancelOb('{{ $ob->id }}', true)"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-orange-200 bg-orange-50 text-orange-600 hover:bg-orange-100 transition-colors"
                                        title="Cancel approved OB">
                                    <i class="fas fa-ban"></i>
                                </button>
                            @elseif($ob->canBeResubmitted() && $ob->employee_id === $currentEmployeeId)
                                @include('attendance.partials.request-expiry-workflow', [
                                    'requestRecord' => $ob,
                                    'resubmitRoute' => route('attendance.official-business.resubmit', $ob->id),
                                ])
                            @endif
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-briefcase text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500 text-lg font-medium mb-2">No official business requests found</p>
                            <p class="text-gray-400 text-sm">Try adjusting your filters or date range.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- OB Application Modal -->
@unless($isReviewer)
<div id="obModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999;" onclick="closeObModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6" style="max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="obModalTitle" class="text-lg font-medium text-gray-900">Request Official Business</h3>
                <button onclick="closeObModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="obForm" method="POST" action="{{ route('attendance.official-business.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" id="obEditRequestId" value="">
                <input type="hidden" id="obReplaceRequestId" name="replace_request_id" value="">
                <div>
                    <label for="obDate" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" id="obDate" name="date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           value="{{ old('date') }}">
                    <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-600">
                        <span class="inline-flex items-center">
                            <span class="w-3 h-3 rounded-sm bg-yellow-200 border border-yellow-500 mr-1.5"></span>
                            Pending request
                        </span>
                        <span class="inline-flex items-center">
                            <span class="w-3 h-3 rounded-sm bg-red-200 border border-red-500 mr-1.5"></span>
                            Approved request
                        </span>
                    </div>
                    <div id="obCutoffWarning"
                         class="hidden mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5 mr-2"></i>
                            <div>
                                <p class="text-sm font-semibold text-amber-800">
                                    Outside Payroll Cutoff
                                </p>
                                <p class="mt-1 text-xs leading-5 text-amber-700">
                                    The selected date is outside the current payroll cutoff period.
                                    Retroactive and advance requests are allowed only while their
                                    payroll cutoff is still open.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="obStartTime" class="block text-sm font-medium text-gray-700 mb-2">Time In</label>
                        <input type="time" id="obStartTime" name="ob_start_time" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="obEndTime" class="block text-sm font-medium text-gray-700 mb-2">Time Out</label>
                        <input type="time" id="obEndTime" name="ob_end_time" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                    <div id="obDuration" class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-600">
                        Select a Time In and Time Out
                    </div>
                    <p id="obTimeError" class="hidden mt-1 text-xs text-red-600">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Time Out must be after Time In.
                    </p>
                </div>

                <div>
                    <label for="obReason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea id="obReason" name="reason" rows="3" required maxlength="500"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                              placeholder="Please provide a reason for your Official Business request...">{{ old('reason') }}</textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeObModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button id="obSubmitButton" type="submit"
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-white hover:bg-blue-700 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="obOverlapModal" class="fixed inset-0 z-[10020] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" onclick="if(event.target === this) closeObOverlapModal()">
    <div class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-neutral-800" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-slate-700">
            <h3 class="flex items-center gap-3 text-lg font-semibold text-gray-900 dark:text-white"><i class="fas fa-exclamation-triangle text-amber-500"></i>Overlapping OB Request</h3>
            <button type="button" onclick="closeObOverlapModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <div class="space-y-4 p-6">
            <p class="text-sm leading-6 text-gray-600 dark:text-gray-200">Your new time range overlaps an existing pending Official Business request. Replace it without re-entering the form?</p>
            <div id="obOverlapList" class="space-y-2"></div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-500/40 dark:bg-amber-950/40 dark:text-amber-200">
                Only a pending request can be replaced. An approved request cannot be modified.
            </div>
        </div>
        <div class="flex flex-wrap justify-end gap-3 border-t border-gray-200 px-6 py-4 dark:border-slate-700">
            <button type="button" onclick="closeObOverlapModal()" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 dark:border-slate-600 dark:bg-neutral-700 dark:text-white">Cancel</button>
            <button type="button" onclick="replaceAndSubmitOb()" class="rounded-lg border border-green-600 bg-green-600 px-4 py-2 text-sm font-semibold !text-white hover:bg-green-700"><i class="fas fa-exchange-alt mr-2"></i>Replace &amp; Submit</button>
        </div>
    </div>
</div>
@endunless

<!-- Reviewer: Approve Modal -->
@if($isReviewer)
<div id="obApproveModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999;" onclick="closeApproveModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6" style="max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Approve OB Request</h3>
                <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="obApproveForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="approved">
                <div>
                    <p class="text-sm text-gray-600 mb-4">Are you sure you want to approve this Official Business request?</p>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeApproveModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 border border-transparent rounded-lg text-white hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Approve Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reviewer: Reject Modal -->
<div id="obRejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999;" onclick="closeRejectModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6" style="max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Reject OB Request</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="obRejectForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected">
                <div>
                    <label for="rejectionReason" class="block text-sm font-medium text-gray-700 mb-2">Reason for rejection</label>
                    <textarea id="rejectionReason" name="rejection_reason" rows="3" required maxlength="500"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-lg text-white hover:bg-red-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Cancel OB Modal (shared by pending-cancel and approved-cancel) -->
<div id="obCancelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999;" onclick="closeObCancelModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6" style="max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Cancel OB Request</h3>
                <button onclick="closeObCancelModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="obCancelForm" method="POST" class="space-y-4">
                @csrf
                @method('DELETE')
                <div>
                    <p id="obCancelModalMessage" class="text-sm text-gray-600 mb-4">Are you sure you want to cancel this OB request?</p>
                    <label for="obCancellationReason" class="block text-sm font-medium text-gray-700 mb-2">Cancellation reason <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea id="obCancellationReason" name="cancellation_reason" rows="3" maxlength="500"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-colors"
                              placeholder="Add a note about why this request is being cancelled..."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeObCancelModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Keep Request
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-orange-600 border border-transparent rounded-lg text-white hover:bg-orange-700 transition-colors">
                        <i class="fas fa-ban mr-2"></i>
                        Cancel Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const obOccupiedDates = @json($calendarRequests ?? []);

function obDateKey(date) {
    return flatpickr.formatDate(date, 'Y-m-d');
}

function getOccupiedOb(date) {
    const key = typeof date === 'string' ? date : obDateKey(date);

    return obOccupiedDates.find(item => item.date === key) ?? null;
}

document.addEventListener('DOMContentLoaded', function () {
    const obDateInput = document.getElementById('obDate');

    if (!obDateInput) {
        return;
    }

    window.obDatePicker = flatpickr(obDateInput, {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'F j, Y',
        disableMobile: true,
        onDayCreate: function (_dObj, _dStr, _instance, dayElem) {
            const occupied = getOccupiedOb(dayElem.dateObj);

            if (!occupied) {
                return;
            }

            if (occupied.status === 'pending') {
                dayElem.classList.add('occupied-pending');
                dayElem.title = 'Pending Official Business request';
            }

            if (occupied.status === 'approved') {
                dayElem.classList.add('occupied-approved');
                dayElem.title = 'Approved Official Business request';
            }
        },
        onChange: function () {
            checkObCutoffWarning();
        }
    });
});
</script>
<script>
function updateObDuration() {
    const durationEl = document.getElementById('obDuration');
    const errorEl = document.getElementById('obTimeError');
    const startInput = document.getElementById('obStartTime');
    const endInput = document.getElementById('obEndTime');
    const start = startInput?.value;
    const end = endInput?.value;

    if (!durationEl || !errorEl) {
        return false;
    }

    errorEl.classList.add('hidden');
    startInput?.classList.remove('border-red-500');
    endInput?.classList.remove('border-red-500');

    if (!start || !end) {
        durationEl.textContent = 'Select a Time In and Time Out';
        durationEl.classList.remove('text-red-600');
        return false;
    }

    const [startHour, startMinute] = start.split(':').map(Number);
    const [endHour, endMinute] = end.split(':').map(Number);
    const minutes = (endHour * 60 + endMinute)
        - (startHour * 60 + startMinute);

    if (minutes <= 0) {
        durationEl.textContent = 'Invalid time range';
        durationEl.classList.add('text-red-600');
        errorEl.classList.remove('hidden');
        startInput?.classList.add('border-red-500');
        endInput?.classList.add('border-red-500');

        return false;
    }

    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;

    durationEl.textContent =
        `${hours}h ${remainingMinutes}m (${(minutes / 60).toFixed(2)} hrs)`;

    durationEl.classList.remove('text-red-600');

    return true;
}

// Client-side warning only, purely to give early feedback. The server (via
// CutoffPeriodService) is the actual source of truth and re-checks this on
// submit. Values below come from config/attendance_cutoff.php via the
// controller, so this can never drift out of sync with the server rule.
const OB_CUTOFF_DAYS = @json($cutoffDays ?? [10, 25]);
const OB_GRACE_HOURS = @json($graceHours ?? 24);

function checkObCutoffWarning() {
    const warningEl = document.getElementById('obCutoffWarning');
    const dateInput = document.getElementById('obDate');

    if (!warningEl || !dateInput?.value) {
        warningEl?.classList.add('hidden');
        return;
    }

    const selected = new Date(`${dateInput.value}T00:00:00`);
    const cutoffDays = OB_CUTOFF_DAYS;
    const graceHours = OB_GRACE_HOURS;

    // Find the end-of-period cutoff date on/after the selected date.
    let periodEnd = null;

    // Mirror the server cutoff calendar only for early UI feedback.
    for (let offset = -1; offset <= 2; offset++) {
        const candidateMonth = new Date(
            selected.getFullYear(),
            selected.getMonth() + offset,
            1
        );

        const lastDay = new Date(
            candidateMonth.getFullYear(),
            candidateMonth.getMonth() + 1,
            0
        ).getDate();

        for (const cutoffDay of cutoffDays) {
            const candidate = new Date(
                candidateMonth.getFullYear(),
                candidateMonth.getMonth(),
                Math.min(cutoffDay, lastDay),
                23,
                59,
                59
            );

            if (
                candidate >= selected
                && (!periodEnd || candidate < periodEnd)
            ) {
                periodEnd = candidate;
            }
        }
    }

    if (!periodEnd) {
        warningEl.classList.remove('hidden');
        return;
    }

    const deadline = new Date(periodEnd);
    deadline.setHours(deadline.getHours() + graceHours);

    const isOutsideCutoff = new Date() > deadline;
    warningEl.classList.toggle('hidden', !isOutsideCutoff);
}

function openObModal() {
    const modal = document.getElementById('obModal');
    if (!modal) return;
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    const form = document.getElementById('obForm');
    if (form) form.reset();
    document.getElementById('obEditRequestId').value = '';
    document.getElementById('obReplaceRequestId').value = '';
    document.getElementById('obModalTitle').textContent = 'Request Official Business';
    document.getElementById('obSubmitButton').lastChild.textContent = ' Submit Request';
    if (window.obDatePicker) window.obDatePicker.clear();
    updateObDuration();
    checkObCutoffWarning();
}

document.addEventListener('DOMContentLoaded', function () {
    const obForm = document.getElementById('obForm');
    if (!obForm) return;

    document.getElementById('obStartTime')?.addEventListener('change', updateObDuration);
    document.getElementById('obEndTime')?.addEventListener('change', updateObDuration);
    document.getElementById('obDate')?.addEventListener('change', checkObCutoffWarning);

    obForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        if (!updateObDuration()) {
            return;
        }

        const editId = document.getElementById('obEditRequestId').value;
        const url = editId
            ? '{{ route("attendance.official-business.update-pending", ["id" => ":id"]) }}'.replace(':id', editId)
            : obForm.action;
        const payload = Object.fromEntries(new FormData(obForm));

        const response = await fetch(url, {
            method: editId ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(payload),
        });
        const result = await response.json();

        if (response.ok) {
            window.location.reload();
            return;
        }
        if (response.status === 409 && result.overlap) {
            showObOverlapModal(result.replaceable_requests || []);
            return;
        }
        const validationError = result.errors ? Object.values(result.errors).flat()[0] : null;
        if (typeof showError === 'function') showError(result.error || validationError || result.message || 'Unable to save the request.');
    });
});

function openObEditModal(request) {
    openObModal();
    document.getElementById('obEditRequestId').value = request.id;
    document.getElementById('obModalTitle').textContent = 'Edit Pending Official Business';
    document.getElementById('obSubmitButton').lastChild.textContent = ' Update Request';
    window.obDatePicker?.setDate(request.date, true);
    document.getElementById('obStartTime').value = request.start_time;
    document.getElementById('obEndTime').value = request.end_time;
    document.getElementById('obReason').value = request.reason;
    updateObDuration();
}

function showObOverlapModal(requests) {
    const list = document.getElementById('obOverlapList');
    list.innerHTML = requests.map((request, index) => `
        <label class="flex cursor-pointer items-center justify-between gap-3 rounded-xl border border-gray-200 p-4 dark:border-slate-600">
            <span class="text-sm text-gray-800 dark:text-gray-100"><strong>${request.date}</strong><br>${request.start_time}–${request.end_time}</span>
            <span class="flex items-center gap-2 text-sm font-medium text-green-700 dark:text-green-300"><input type="radio" name="ob_overlap_choice" value="${request.id}" ${index === 0 ? 'checked' : ''}> Replace this</span>
        </label>`).join('');
    const modal = document.getElementById('obOverlapModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeObOverlapModal() {
    const modal = document.getElementById('obOverlapModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function replaceAndSubmitOb() {
    const selected = document.querySelector('input[name="ob_overlap_choice"]:checked');
    if (!selected) return;
    document.getElementById('obReplaceRequestId').value = selected.value;
    closeObOverlapModal();
    document.getElementById('obForm').requestSubmit();
}

function closeObModal() {
    const modal = document.getElementById('obModal');
    if (modal) modal.style.display = 'none';
}

@if($isReviewer)
function approveOb(requestId) {
    const modal = document.getElementById('obApproveModal');
    const form = document.getElementById('obApproveForm');
    if (!modal || !form) return;
    form.action = '{{ route("attendance.official-business.update-status", ["id" => ":id"]) }}'.replace(':id', requestId);
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeApproveModal() {
    const modal = document.getElementById('obApproveModal');
    if (modal) modal.style.display = 'none';
}

function rejectOb(requestId) {
    const modal = document.getElementById('obRejectModal');
    const form = document.getElementById('obRejectForm');
    if (!modal || !form) return;
    form.action = '{{ route("attendance.official-business.update-status", ["id" => ":id"]) }}'.replace(':id', requestId);
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeRejectModal() {
    const modal = document.getElementById('obRejectModal');
    if (modal) modal.style.display = 'none';
}
@endif

function cancelOb(requestId, isApproved) {
    const modal = document.getElementById('obCancelModal');
    const form = document.getElementById('obCancelForm');
    const reasonField = document.getElementById('obCancellationReason');
    const messageEl = document.getElementById('obCancelModalMessage');
    if (!modal || !form) return;

    form.action = '{{ route("attendance.official-business.cancel", ["id" => ":id"]) }}'.replace(':id', requestId);
    if (reasonField) reasonField.value = '';
    if (messageEl) {
        messageEl.textContent = isApproved
            ? 'Cancel this approved OB request? Attendance hours for that date will be recalculated.'
            : 'Cancel this OB request?';
    }

    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeObCancelModal() {
    const modal = document.getElementById('obCancelModal');
    if (modal) modal.style.display = 'none';
}
</script>
@endsection
