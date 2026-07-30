@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'attendance.leave-management'])

@php use Illuminate\Support\Str; @endphp

@section('title', ($personalMode ?? false) ? 'My Leave' : 'Leave Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ ($personalMode ?? false) ? 'My Leave' : 'Leave Management' }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ ($personalMode ?? false) ? 'Review and submit your leave requests' : 'Manage employee leave requests and balances' }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <!-- Export Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export Report
                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div class="py-1">
                        <a href="{{ route('attendance.leave-management.export', ['format' => 'pdf']) . '?' . http_build_query(request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-pdf mr-2 text-red-500"></i>Export as PDF
                        </a>
                        <a href="{{ route('attendance.leave-management.export', ['format' => 'csv']) . '?' . http_build_query(request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-csv mr-2 text-green-500"></i>Export as CSV
                        </a>
                        <a href="{{ route('attendance.leave-management.export', ['format' => 'xlsx']) . '?' . http_build_query(request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2 text-green-600"></i>Export as Excel
                        </a>
                    </div>
                </div>
            </div>
            @if($isReviewer && ($hasEmployeesWithoutBalances ?? true))
            <button onclick="openSetLeaveBalanceModal()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium" title="Set Leave Balance">
                <i class="fas fa-calendar-plus mr-2"></i><span class="hidden sm:inline">Set Leave Balance</span><span class="sm:hidden">Set Balance</span>
            </button>
            @endif
            @if(!$isReviewer)
            <a href="{{ route('attendance.leave-management.create', ['scope' => 'mine']) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                New Leave Request
            </a>
            @endif
        </div>
    </div>

    <!-- Leave Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-indigo-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['total'] ?? 0 }}</p>
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
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['approved'] ?? 0 }}</p>
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
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['pending'] ?? 0 }}</p>
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
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['rejected'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-history text-gray-500"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Expired</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['expired'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if($isReviewer)
            <div>
                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select id="department" name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    <option value="">All Departments</option>
                    @if(isset($departments))
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div>
                <label for="employee" class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                <select id="employee" name="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    <option value="">All Employees</option>
                    @if(isset($employees) && $employees->count() > 0)
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}"
                                    data-department-id="{{ $emp->department_id }}"
                                    data-full-label="{{ $emp->full_name }} - {{ $emp->department->name ?? 'No Department' }}"
                                    data-short-label="{{ $emp->full_name }}"
                                    {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }} - {{ $emp->department->name ?? 'No Department' }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            @endif
            <div>
                <label for="leaveType" class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                <select id="leaveType" name="leave_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    <option value="">All Types</option>
                    <option value="vacation" {{ request('leave_type') == 'vacation' ? 'selected' : '' }}>Vacation Leave</option>
                    <option value="sick" {{ request('leave_type') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                    <option value="personal" {{ request('leave_type') == 'personal' ? 'selected' : '' }}>Personal Leave/Leave Without Pay</option>
                    <option value="emergency" {{ request('leave_type') == 'emergency' ? 'selected' : '' }}>Emergency Leave</option>
                    <option value="maternity" {{ request('leave_type') == 'maternity' ? 'selected' : '' }}>Maternity Leave</option>
                    <option value="paternity" {{ request('leave_type') == 'paternity' ? 'selected' : '' }}>Paternity Leave</option>
                    <option value="bereavement" {{ request('leave_type') == 'bereavement' ? 'selected' : '' }}>SIL (Service Incentive Leave)</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" id="dateFrom" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
            </div>
            <div>
                <label for="dateTo" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" id="dateTo" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
            </div>
        </div>
        <div class="mt-4 flex flex-col sm:flex-row justify-end gap-3">
            <button onclick="clearFilters()" class="w-full sm:w-auto px-6 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                <i class="fas fa-times mr-2"></i>Clear Filters
            </button>
            <button onclick="applyFilters()" class="w-full sm:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i class="fas fa-search mr-2"></i>Apply Filters
            </button>
        </div>
    </div>

    <!-- Leave Requests -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Leave Requests</h3>
            <p class="mt-1 text-sm text-gray-600">Employee leave requests and approvals</p>
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
                            Leave Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Start Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            End Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Duration
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
                    @forelse($leaveRequests as $leaveRequest)
                        @php
                            $employee = $leaveRequest->employee;
                            $initials = strtoupper(substr($employee->first_name ?? '', 0, 1) . substr($employee->last_name ?? '', 0, 1));
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                                'expired' => 'bg-gray-200 text-gray-600'
                            ];
                            $leaveTypeColors = [
                                'vacation' => 'bg-blue-100 text-blue-800',
                                'sick' => 'bg-red-100 text-red-800',
                                'personal' => 'bg-purple-100 text-purple-800',
                                'emergency' => 'bg-orange-100 text-orange-800',
                                'maternity' => 'bg-pink-100 text-pink-800',
                                'paternity' => 'bg-indigo-100 text-indigo-800',
                                'bereavement' => 'bg-gray-100 text-gray-800',
                                'study' => 'bg-teal-100 text-teal-800'
                            ];

                            // Same reviewer display pattern used by the OB/OT tables.
                            $reviewerName = trim(
                                ($leaveRequest->approver?->employee?->first_name ?? '') . ' ' .
                                ($leaveRequest->approver?->employee?->last_name ?? '')
                            );

                            // Fallback when the account is not linked to an employee profile.
                            if ($reviewerName === '') {
                                $reviewerName = trim(
                                    ($leaveRequest->approver?->first_name ?? '') . ' ' .
                                    ($leaveRequest->approver?->last_name ?? '')
                                );
                            }

                            if ($reviewerName === '') {
                                $reviewerName = $leaveRequest->approver?->name
                                    ?? $leaveRequest->approver?->username
                                    ?? '';
                            }
                        @endphp
                    <tr class="hover:bg-gray-50 transition-colors" data-search-row="{{ $leaveRequest->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ $initials }}</span>
                                        </div>
                                </div>
                                <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->full_name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->department->name ?? 'No Department' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $leaveTypeColors[$leaveRequest->leave_type] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ \App\Models\LeaveRequest::labelFor($leaveRequest->leave_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $leaveRequest->days_requested }} {{ $leaveRequest->days_requested == 1 ? 'day' : 'days' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ Str::limit($leaveRequest->reason, 30) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$leaveRequest->status] ?? 'bg-gray-100 text-gray-800' }} min-w-[80px]">
                                @if($leaveRequest->status == 'pending' || $leaveRequest->status == 'approved')
                                    <div class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $leaveRequest->status == 'pending' ? 'bg-yellow-400' : 'bg-green-400' }}"></div>
                                @endif
                                {{ ucfirst($leaveRequest->status) }}
                            </span>

                            @if($leaveRequest->status === 'rejected' && $leaveRequest->rejection_reason)
                                <div class="text-xs text-red-600 mt-1 max-w-[220px] mx-auto"
                                     title="{{ $leaveRequest->rejection_reason }}">
                                    <span class="font-medium">Admin Reason:</span>
                                    {{ Str::limit($leaveRequest->rejection_reason, 40) }}
                                </div>
                            @elseif($leaveRequest->status === 'cancelled' && $leaveRequest->rejection_reason)
                                <div class="text-xs text-gray-600 mt-1 max-w-[220px] mx-auto"
                                     title="{{ $leaveRequest->rejection_reason }}">
                                    <span class="font-medium">Cancellation Reason:</span>
                                    {{ Str::limit($leaveRequest->rejection_reason, 40) }}
                                </div>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $reviewerName !== '' ? $reviewerName : '—' }}
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex justify-center items-center space-x-2">
                                    @if($isReviewer && $leaveRequest->status == 'pending')
                                        <button data-leave-id="{{ $leaveRequest->id }}" data-action="approve" class="time-action-btn inline-flex items-center justify-center w-10 h-10 rounded-full border border-green-200 bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-900 transition-colors" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button data-leave-id="{{ $leaveRequest->id }}" data-action="reject" class="time-action-btn inline-flex items-center justify-center w-10 h-10 rounded-full border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-900 transition-colors" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @elseif($isReviewer && $leaveRequest->status == 'approved')
                                        <button data-leave-id="{{ $leaveRequest->id }}" data-action="cancel" class="time-action-btn inline-flex items-center justify-center w-10 h-10 rounded-full border border-orange-200 bg-orange-50 text-orange-600 hover:bg-orange-100 hover:text-orange-900 transition-colors" title="Cancel approved leave">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @elseif($leaveRequest->status == 'pending' && (!$isReviewer && $leaveRequest->employee_id == $user->employee?->id))
                                        <button data-leave-id="{{ $leaveRequest->id }}" data-action="cancel" class="time-action-btn inline-flex items-center justify-center w-10 h-10 rounded-full border border-orange-200 bg-orange-50 text-orange-600 hover:bg-orange-100 hover:text-orange-900 transition-colors" title="Cancel">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @else
                                        <div class="flex justify-center">
                                            <span class="inline-block w-8 h-px bg-gray-300 rounded-full"></span>
                                        </div>
                                    @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">No leave requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leaveRequests->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $leaveRequests->appends(request()->query())->links() }}
            </div>
        @endif

        <!-- Mobile Cards -->
        <div class="lg:hidden">
            <div class="p-4 space-y-4">
                @forelse($leaveRequests as $leaveRequest)
                    @php
                        $employee = $leaveRequest->employee;
                        $initials = strtoupper(substr($employee->first_name ?? '', 0, 1) . substr($employee->last_name ?? '', 0, 1));
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'cancelled' => 'bg-gray-100 text-gray-800',
                            'expired' => 'bg-gray-200 text-gray-600'
                        ];
                        $leaveTypeColors = [
                            'vacation' => 'bg-blue-100 text-blue-800',
                            'sick' => 'bg-red-100 text-red-800',
                            'personal' => 'bg-purple-100 text-purple-800',
                            'emergency' => 'bg-orange-100 text-orange-800',
                            'maternity' => 'bg-pink-100 text-pink-800',
                            'paternity' => 'bg-indigo-100 text-indigo-800',
                            'bereavement' => 'bg-gray-100 text-gray-800',
                            'study' => 'bg-teal-100 text-teal-800'
                        ];

                        // Same reviewer display pattern used by the OB/OT tables.
                        $reviewerName = trim(
                            ($leaveRequest->approver?->employee?->first_name ?? '') . ' ' .
                            ($leaveRequest->approver?->employee?->last_name ?? '')
                        );

                        if ($reviewerName === '') {
                            $reviewerName = trim(
                                ($leaveRequest->approver?->first_name ?? '') . ' ' .
                                ($leaveRequest->approver?->last_name ?? '')
                            );
                        }

                        if ($reviewerName === '') {
                            $reviewerName = $leaveRequest->approver?->name
                                ?? $leaveRequest->approver?->username
                                ?? '';
                        }
                    @endphp
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ $initials }}</span>
                            </div>
                            <div>
                                    <div class="font-medium text-gray-900">{{ $employee->full_name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $employee->department->name ?? 'No Department' }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$leaveRequest->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    @if($leaveRequest->status == 'pending' || $leaveRequest->status == 'approved')
                                        <div class="w-1.5 h-1.5 rounded-full mr-1 {{ $leaveRequest->status == 'pending' ? 'bg-yellow-400' : 'bg-green-400' }}"></div>
                                    @endif
                                    {{ ucfirst($leaveRequest->status) }}
                                </span>
                                @if($leaveRequest->status == 'rejected' && $leaveRequest->rejection_reason)
                                    <div class="text-xs text-red-600 mt-1 max-w-[140px]"><span class="font-medium">Admin Reason:</span> {{ Str::limit($leaveRequest->rejection_reason, 50) }}</div>
                                @elseif($leaveRequest->status == 'cancelled' && $leaveRequest->rejection_reason)
                                    <div class="text-xs text-gray-600 mt-1 max-w-[140px]"><span class="font-medium">Cancellation Reason:</span> {{ Str::limit($leaveRequest->rejection_reason, 50) }}</div>
                                @endif
                            </div>
                    </div>
                    <div class="mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $leaveTypeColors[$leaveRequest->leave_type] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ \App\Models\LeaveRequest::labelFor($leaveRequest->leave_type) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                        <div>
                            <div class="text-gray-500">Start Date</div>
                                <div class="font-medium">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">End Date</div>
                                <div class="font-medium">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Duration</div>
                                <div class="font-medium">{{ $leaveRequest->days_requested }} {{ $leaveRequest->days_requested == 1 ? 'day' : 'days' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Type</div>
                            <div class="font-medium">{{ \App\Models\LeaveRequest::labelFor($leaveRequest->leave_type) }}</div>
                        </div>
                        <div class="col-span-2">
                            <div class="text-gray-500">Reviewed By</div>
                            <div class="font-medium">{{ $reviewerName !== '' ? $reviewerName : '—' }}</div>
                        </div>
                    </div>
                    <div class="text-sm mb-3">
                        <div class="text-gray-500">Reason</div>
                            <div class="font-medium">{{ Str::limit($leaveRequest->reason, 50) }}</div>
                    </div>
                    <div class="flex justify-center space-x-2">
                            @if($isReviewer && $leaveRequest->status == 'pending')
                                <button data-leave-id="{{ $leaveRequest->id }}" data-action="approve" class="time-action-btn inline-flex items-center justify-center w-10 h-10 rounded-full border border-green-200 bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-900 transition-colors" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button data-leave-id="{{ $leaveRequest->id }}" data-action="reject" class="time-action-btn inline-flex items-center justify-center w-10 h-10 rounded-full border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-900 transition-colors" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            @elseif($isReviewer && $leaveRequest->status == 'approved')
                                <button data-leave-id="{{ $leaveRequest->id }}" data-action="cancel" class="time-action-btn inline-flex items-center justify-center w-10 h-10 rounded-full border border-orange-200 bg-orange-50 text-orange-600 hover:bg-orange-100 hover:text-orange-900 transition-colors" title="Cancel approved leave">
                                    <i class="fas fa-ban"></i>
                                </button>
                            @elseif($leaveRequest->status == 'pending' && (!$isReviewer && $leaveRequest->employee_id == $user->employee?->id))
                                <button data-leave-id="{{ $leaveRequest->id }}" data-action="cancel" class="time-action-btn inline-flex items-center justify-center w-10 h-10 rounded-full border border-orange-200 bg-orange-50 text-orange-600 hover:bg-orange-100 hover:text-orange-900 transition-colors" title="Cancel">
                                    <i class="fas fa-ban"></i>
                                </button>
                            @else
                                <div class="flex justify-center">
                                    <span class="inline-block w-8 h-px bg-gray-300 rounded-full"></span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-sm text-gray-500 py-8">No leave requests found.</div>
                @endforelse

                @if($leaveRequests->hasPages())
                    <div class="mt-4">
                        {{ $leaveRequests->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Leave Balances -->
    @php
        $selectedEmployeeId = request('employee_id');
        $showLeaveBalances = false;
        $selectedEmployeeBalance = null;
        $selectedEmployee = null;

        // For employees, always show their own balance
        if (!$isReviewer && $user->employee) {
            $showLeaveBalances = true;
            $selectedEmployee = $user->employee;
            // Always refresh balance from database to ensure we have latest data
            // Check which year has the most recent approved leaves and prioritize that year
            $currentYear = now()->year;
            $approvedLeaves = \App\Models\LeaveRequest::where('employee_id', $user->employee->id)
                ->where('status', 'approved')
                ->get();

            // Find the year with the most recent approved leave
            $mostRecentYear = $currentYear;
            if ($approvedLeaves->count() > 0) {
                $mostRecentLeave = $approvedLeaves->sortByDesc('start_date')->first();
                $mostRecentYear = $mostRecentLeave->start_date instanceof \Carbon\Carbon
                    ? $mostRecentLeave->start_date->year
                    : \Carbon\Carbon::parse($mostRecentLeave->start_date)->year;
            }

            // Prioritize the year with approved leaves, then current year, then others
            $selectedEmployeeBalance = \App\Models\LeaveBalance::where('employee_id', $user->employee->id)
                ->where(function($q) use ($currentYear, $mostRecentYear) {
                    $q->where('year', $mostRecentYear)
                      ->orWhere('year', $currentYear)
                      ->orWhere('year', $currentYear - 1)
                      ->orWhere('year', $currentYear + 1);
                })
                ->orderByRaw("CASE WHEN year = {$mostRecentYear} THEN 1 WHEN year = {$currentYear} THEN 2 ELSE 3 END")
                ->orderBy('year', 'desc')
                ->first();

            // If no balance found, leave the selected employee balance null.
            if (!$selectedEmployeeBalance) {
                $selectedEmployeeBalance = null;
            }
        }
        // For HR/Admin, only show when a specific employee is selected (not "All Employees")
        elseif ($isReviewer && $selectedEmployeeId && $selectedEmployeeId !== '') {
            // Find employee from the filtered employees collection (respects company filtering)
            // Use firstWhere with string comparison to handle UUID properly
            $selectedEmployee = $employees->first(function($emp) use ($selectedEmployeeId) {
                return (string)$emp->id === (string)$selectedEmployeeId;
            });

            if ($selectedEmployee) {
                $showLeaveBalances = true;

                // Always refresh balance from database to ensure we have latest data
                // Check which year has the most recent approved leaves and prioritize that year
                $currentYear = now()->year;
                $approvedLeaves = \App\Models\LeaveRequest::where('employee_id', $selectedEmployeeId)
                    ->where('status', 'approved')
                    ->get();

                // Find the year with the most recent approved leave
                $mostRecentYear = $currentYear;
                if ($approvedLeaves->count() > 0) {
                    $mostRecentLeave = $approvedLeaves->sortByDesc('start_date')->first();
                    $mostRecentYear = $mostRecentLeave->start_date instanceof \Carbon\Carbon
                        ? $mostRecentLeave->start_date->year
                        : \Carbon\Carbon::parse($mostRecentLeave->start_date)->year;
                }

                // Prioritize the year with approved leaves, then current year, then others
                $selectedEmployeeBalance = \App\Models\LeaveBalance::where('employee_id', $selectedEmployeeId)
                    ->where(function($q) use ($currentYear, $mostRecentYear) {
                        $q->where('year', $mostRecentYear)
                          ->orWhere('year', $currentYear)
                          ->orWhere('year', $currentYear - 1)
                          ->orWhere('year', $currentYear + 1);
                    })
                    ->orderByRaw("CASE WHEN year = {$mostRecentYear} THEN 1 WHEN year = {$currentYear} THEN 2 ELSE 3 END")
                    ->orderBy('year', 'desc')
                    ->first();

                // If no balance found, leave the selected employee balance null.
                if (!$selectedEmployeeBalance) {
                    $selectedEmployeeBalance = null;
                }
            }
        }
    @endphp

    @if($showLeaveBalances && $selectedEmployee)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">
                Leave Balances
                @if($isReviewer && $selectedEmployee)
                    - {{ $selectedEmployee->full_name }}
                @endif
            </h3>
        </div>
        @if($selectedEmployeeBalance)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $leaveTypes = [
                    'vacation'    => ['label' => 'Vacation Leave',                    'color' => 'blue'],
                    'sick'        => ['label' => 'Sick Leave',                        'color' => 'red'],
                    'sil'         => ['label' => 'SIL (Service Incentive Leave)',     'color' => 'gray'],
                    'personal'    => ['label' => 'Personal Leave / Leave Without Pay','color' => 'purple'],
                    'emergency'   => ['label' => 'Emergency Leave',                   'color' => 'yellow'],
                    'maternity'   => ['label' => 'Maternity Leave',                   'color' => 'pink'],
                    'paternity'   => ['label' => 'Paternity Leave',                   'color' => 'indigo'],
                    'spl'         => ['label' => 'Solo Parent Leave',                 'color' => 'green'],
                    'vawc'        => ['label' => 'VAWC Leave',                        'color' => 'red'],
                    'bl'          => ['label' => 'Bereavement Leave',                 'color' => 'gray'],
                ];
                // VL, SL, and SIL are capped and always visible (even at 0)
                $alwaysVisibleTypes = ['vacation', 'sick', 'sil'];
                
                $colorClasses = [
                    'blue'   => 'bg-blue-600',
                    'red'    => 'bg-red-600',
                    'green'  => 'bg-green-600',
                    'yellow' => 'bg-yellow-600',
                    'pink'   => 'bg-pink-600',
                    'indigo' => 'bg-indigo-600',
                    'gray'   => 'bg-gray-600',
                    'purple' => 'bg-purple-600',
                ];
            @endphp
            @foreach($leaveTypes as $type => $config)
                @php
                    $totalField      = $type . '_days_total';
                    $usedField       = $type . '_days_used';
                    $total           = $selectedEmployeeBalance->$totalField ?? 0;
                    $used            = $selectedEmployeeBalance->$usedField  ?? 0;
                    $isCapped        = in_array($type, $alwaysVisibleTypes, true);
                @endphp
                
                
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900 text-sm">{{ $config['label'] }}</h4>
                    @if($isCapped)
                        <span class="text-sm text-gray-500">{{ $total }} days</span>
                    @endif
                </div>
                
                @if(!$isCapped)
                <div class="text-xs text-gray-500 mt-1">
                    <span class="font-medium text-gray-900">{{ $used }}</span> days used
                </div>
                @else
                @php
                    $remaining       = $total - $used;
                    $percentage      = $total > 0 ? ($used / $total) * 100 : 0;
                    $widthPercentage = min((float)$percentage, 100);
                    $barColor        = $colorClasses[$config['color']] ?? 'bg-blue-600';
                @endphp
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ $widthPercentage }}%"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    <span class="font-medium text-gray-900">{{ $used }}</span> days used,
                    <span class="font-medium text-green-600">{{ max($remaining, 0) }}</span> remaining
                </div>
                @endif
            </div>
            
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-info-circle text-4xl mb-4 text-gray-400"></i>
            <p class="text-lg font-medium mb-2">No leave balance record found</p>
            <p class="text-sm">This employee doesn't have a leave balance record for {{ now()->year }}.</p>
        </div>
        @endif
    </div>
    @endif
</div>

<script>
function applyFilters() {
    const departmentEl = document.getElementById('department');
    const department = departmentEl ? departmentEl.value : '';
    const employee = document.getElementById('employee').value;
    const leaveType = document.getElementById('leaveType').value;
    const status = document.getElementById('status').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    // Build query string
    const params = new URLSearchParams();
    if (department) params.append('department_id', department);
    if (employee) params.append('employee_id', employee);
    if (leaveType) params.append('leave_type', leaveType);
    if (status) params.append('status', status);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);

    // Redirect with filters
    window.location.href = '{{ route("attendance.leave-management") }}?' + params.toString();
}

function clearFilters() {
    // Redirect with no query params, resetting every filter
    window.location.href = '{{ route("attendance.leave-management") }}';
}

// --- Department <-> Employee cascading filters ---
document.addEventListener('DOMContentLoaded', function () {
    const departmentSelect = document.getElementById('department');
    const employeeSelect = document.getElementById('employee');

    if (!departmentSelect || !employeeSelect) return;

    // Snapshot every employee option's data once, before we start mutating
    // the live <select> (rebuilding it per department, swapping labels).
    const allEmployeeOptions = Array.from(employeeSelect.options)
        .filter(opt => opt.value)
        .map(opt => ({
            value: opt.value,
            departmentId: opt.dataset.departmentId || '',
            fullLabel: opt.dataset.fullLabel || opt.textContent.trim(),
            shortLabel: opt.dataset.shortLabel || opt.textContent.trim().split(' - ')[0].trim(),
        }));

    // Rebuild the Employee dropdown to only contain employees in
    // `departmentId` (or everyone, if empty). Keeps `keepSelected` selected
    // if it still belongs; otherwise resets to "All Employees".
    function rebuildEmployeeOptions(departmentId, keepSelected) {
        const currentValue = keepSelected !== undefined ? keepSelected : employeeSelect.value;
        employeeSelect.innerHTML = '';

        const allOpt = document.createElement('option');
        allOpt.value = '';
        allOpt.textContent = 'All Employees';
        employeeSelect.appendChild(allOpt);

        let matchedCurrent = false;
        allEmployeeOptions.forEach(function (emp) {
            if (departmentId && emp.departmentId !== departmentId) return;

            const opt = document.createElement('option');
            opt.value = emp.value;
            opt.dataset.departmentId = emp.departmentId;
            opt.dataset.fullLabel = emp.fullLabel;
            opt.dataset.shortLabel = emp.shortLabel;

            const isSelected = emp.value === currentValue;
            opt.textContent = isSelected ? emp.shortLabel : emp.fullLabel;
            if (isSelected) {
                opt.selected = true;
                matchedCurrent = true;
            }
            employeeSelect.appendChild(opt);
        });

        if (!matchedCurrent) {
            employeeSelect.value = '';
        }
    }

    // While the dropdown is open, every option shows "Name - Department"
    // (matches how the list should look). While closed, the selected
    // option shows just the name.
    function showFullEmployeeLabels() {
        Array.from(employeeSelect.options).forEach(function (opt) {
            if (opt.dataset.fullLabel) opt.textContent = opt.dataset.fullLabel;
        });
    }

    function showShortSelectedLabel() {
        const selected = employeeSelect.options[employeeSelect.selectedIndex];
        if (selected && selected.dataset.shortLabel) {
            selected.textContent = selected.dataset.shortLabel;
        }
    }

    // Department changed -> narrow the Employee list to that department.
    departmentSelect.addEventListener('change', function () {
        rebuildEmployeeOptions(departmentSelect.value);
        showShortSelectedLabel();
    });

    // Employee changed -> auto-select their Department, and collapse the
    // displayed label back down to just their name.
    employeeSelect.addEventListener('change', function () {
        const selected = employeeSelect.options[employeeSelect.selectedIndex];
        const deptId = selected ? selected.dataset.departmentId : '';
        if (deptId) {
            departmentSelect.value = deptId;
        }
        showShortSelectedLabel();
    });

    // Show the full "Name - Department" text for every option only while
    // the list is actually open.
    employeeSelect.addEventListener('mousedown', showFullEmployeeLabels);
    employeeSelect.addEventListener('focus', showFullEmployeeLabels);
    employeeSelect.addEventListener('blur', showShortSelectedLabel);

    // Respect a department already selected on page load (e.g. from the
    // URL query string) by narrowing the employee list immediately.
    if (departmentSelect.value) {
        rebuildEmployeeOptions(departmentSelect.value, employeeSelect.value);
    }
    showShortSelectedLabel();
});

// Notification system
function showNotification(message, type = 'success') {
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll('.dynamic-notification');
    existingNotifications.forEach(notif => notif.remove());

    const notification = document.createElement('div');
    notification.className = 'dynamic-notification fixed top-4 right-4 z-50 max-w-md w-full';
    notification.style.opacity = '0';
    notification.style.transform = 'translateX(100%)';

    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
    const iconColor = type === 'success' ? 'text-green-400' : 'text-red-400';
    const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    notification.innerHTML = `
        <div class="${bgColor} border rounded-lg p-4 shadow-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas ${icon} ${iconColor} text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium ${textColor}">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button onclick="this.closest('.dynamic-notification').remove()" class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 10);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

let currentLeaveActionId = null;

function openApproveLeaveModal(leaveId) {
    currentLeaveActionId = leaveId;
    document.getElementById('approveLeaveModal').classList.remove('hidden');
}

function closeApproveLeaveModal() {
    currentLeaveActionId = null;
    document.getElementById('approveLeaveModal').classList.add('hidden');
}

function confirmApproveLeave() {
    if (!currentLeaveActionId) return;
    const leaveId = currentLeaveActionId;
    closeApproveLeaveModal();
    updateLeaveStatus(leaveId, 'approved');
}

function openRejectLeaveModal(leaveId) {
    currentLeaveActionId = leaveId;
    document.getElementById('rejectLeaveReason').value = '';
    document.getElementById('rejectLeaveModal').classList.remove('hidden');
}

function closeRejectLeaveModal() {
    currentLeaveActionId = null;
    document.getElementById('rejectLeaveModal').classList.add('hidden');
}

function confirmRejectLeave() {
    if (!currentLeaveActionId) return;
    const reason = document.getElementById('rejectLeaveReason').value.trim();
    if (!reason) {
        showNotification('Rejection reason is required.', 'error');
        return;
    }
    const leaveId = currentLeaveActionId;
    closeRejectLeaveModal();
    updateLeaveStatus(leaveId, 'rejected', reason);
}

function updateLeaveStatus(leaveRequestId, status, rejectionReason = null) {
    const url = '{{ route("attendance.leave-management.update-status", ["id" => ":id"]) }}'.replace(':id', leaveRequestId);

    // Build request body - only include rejection_reason if rejecting
    const requestBody = {
        status: status
    };
    if (status === 'rejected' && rejectionReason) {
        requestBody.rejection_reason = rejectionReason;
    }

    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(requestBody)
    })
    .then(async response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            throw new Error('Server returned non-JSON response. Status: ' + response.status);
        }

        const data = await response.json();

        // Check HTTP status code - handle validation errors specially
        if (!response.ok) {
            // For 422 validation errors, show detailed error messages
            if (response.status === 422 && data.errors) {
                const errorMessages = [];
                for (const field in data.errors) {
                    errorMessages.push(...data.errors[field]);
                }
                throw new Error(errorMessages.join(', ') || data.error || 'Validation failed');
            }
            throw new Error(data.error || data.message || 'Request failed with status ' + response.status);
        }

        return data;
    })
    .then(data => {
        if (data.success) {
            showNotification('Leave request ' + status + ' successfully', 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification(data.error || 'Failed to update leave request status.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMessage = error.message || 'An error occurred while updating the leave request.';
        showNotification(errorMessage, 'error');
    });
}

function openCancelLeaveModal(leaveRequestId) {
    currentLeaveActionId = leaveRequestId;
    document.getElementById('cancelLeaveReason').value = '';
    document.getElementById('cancelLeaveModal').classList.remove('hidden');
}

function closeCancelLeaveModal() {
    currentLeaveActionId = null;
    document.getElementById('cancelLeaveModal').classList.add('hidden');
}

function confirmCancelLeave() {
    if (!currentLeaveActionId) return;
    const reason = document.getElementById('cancelLeaveReason').value.trim();
    const leaveRequestId = currentLeaveActionId;
    closeCancelLeaveModal();
    cancelLeaveRequest(leaveRequestId, reason);
}

function cancelLeaveRequest(leaveRequestId, cancellationReason = '') {
    const url = '{{ route("attendance.leave-management.cancel", ["id" => ":id"]) }}'.replace(':id', leaveRequestId);

    fetch(url, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ cancellation_reason: cancellationReason || null })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Leave request cancelled successfully', 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification(data.error || 'Failed to cancel leave request.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while cancelling the leave request.', 'error');
    });
}

// Event delegation for action buttons
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.time-action-btn');
        if (!btn) return;

        const leaveId = btn.getAttribute('data-leave-id');
        const action = btn.getAttribute('data-action');

        if (action === 'approve') {
            openApproveLeaveModal(leaveId);
        } else if (action === 'reject') {
            openRejectLeaveModal(leaveId);
        } else if (action === 'cancel') {
            openCancelLeaveModal(leaveId);
        }
    });
    });
</script>

<!-- Approve Leave Request Modal -->
<div id="approveLeaveModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Approve Leave Request</h3>
            <button onclick="closeApproveLeaveModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-sm text-gray-600">Are you sure you want to approve this leave request?</p>
        </div>
        <div class="flex justify-end space-x-3 px-6 py-4 border-t border-gray-200">
            <button type="button" onclick="closeApproveLeaveModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="button" onclick="confirmApproveLeave()"
                    class="px-4 py-2 bg-green-600 border border-transparent rounded-lg text-white hover:bg-green-700 transition-colors">
                <i class="fas fa-check mr-2"></i>Approve Request
            </button>
        </div>
    </div>
</div>

<!-- Reject Leave Request Modal -->
<div id="rejectLeaveModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Reject Leave Request</h3>
            <button onclick="closeRejectLeaveModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="px-6 py-5">
            <label for="rejectLeaveReason" class="block text-sm font-medium text-gray-700 mb-2">Reason for rejection</label>
            <textarea id="rejectLeaveReason" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                      placeholder="Provide a reason for rejecting this leave request..."></textarea>
        </div>
        <div class="flex justify-end space-x-3 px-6 py-4 border-t border-gray-200">
            <button type="button" onclick="closeRejectLeaveModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="button" onclick="confirmRejectLeave()"
                    class="px-4 py-2 bg-red-600 border border-transparent rounded-lg text-white hover:bg-red-700 transition-colors">
                <i class="fas fa-times mr-2"></i>Reject Request
            </button>
        </div>
    </div>
</div>

<!-- Cancel Leave Request Modal -->
<div id="cancelLeaveModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Cancel Leave Request</h3>
            <button onclick="closeCancelLeaveModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-sm text-gray-600 mb-4">Are you sure you want to cancel this leave request? If it was already approved, the employee's leave balance and any linked attendance will be restored.</p>
            <label for="cancelLeaveReason" class="block text-sm font-medium text-gray-700 mb-2">Cancellation reason <span class="text-gray-400 font-normal">(optional)</span></label>
            <textarea id="cancelLeaveReason" rows="3" maxlength="500"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                      placeholder="Add a note about why this request is being cancelled..."></textarea>
        </div>
        <div class="flex justify-end space-x-3 px-6 py-4 border-t border-gray-200">
            <button type="button" onclick="closeCancelLeaveModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Keep Request
            </button>
            <button type="button" onclick="confirmCancelLeave()"
                    class="px-4 py-2 bg-orange-600 border border-transparent rounded-lg text-white hover:bg-orange-700 transition-colors">
                <i class="fas fa-ban mr-2"></i>Cancel Request
            </button>
        </div>
    </div>
</div>

<!-- Set Leave Balance Modal -->
<div id="setLeaveBalanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Set Leave Balance</h3>
                <button onclick="closeSetLeaveBalanceModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="setLeaveBalanceForm" class="space-y-4">
                @csrf
                <input type="hidden" id="balanceYear" name="year" value="{{ now()->year }}">

                <div>
                    <label for="balanceEmployeeSelect" class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                    <select id="balanceEmployeeSelect" name="employee_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Employee</option>
                        <option value="all">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} - {{ $emp->department->name ?? 'No Department' }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Select "All Employees" to set leave balance for all employees at once</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Leave Types</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @php
                            $leaveTypes = [
                                'vacation' => ['label' => 'Vacation Leave', 'default' => 15],
                                'sick' => ['label' => 'Sick Leave', 'default' => 10],
                                'sil' => ['label' => 'SIL (Service Incentive Leave)', 'default' => 5],
                            ];
                            // Personal and Emergency are excluded here: they're incremental
                            // (see LeaveRequest::UNCAPPED_LEAVE_TYPES) with no cap to set.
                            // Maternity and Paternity are excluded here: not managed through
                            // this bulk form, only settable another way.
                        @endphp
                        @foreach($leaveTypes as $type => $config)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $config['label'] }}</label>
                            <input type="number"
                                   name="{{ $type }}_days_total"
                                   id="{{ $type }}_days_total"
                                   min="0"
                                   value="{{ $config['default'] }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Days">
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeSetLeaveBalanceModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-white hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Leave Balance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Leave Balance Modal -->
<div id="editLeaveBalanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Leave Balance</h3>
                <button onclick="closeEditLeaveBalanceModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="editLeaveBalanceForm" class="space-y-4">
                @csrf
                <input type="hidden" id="editBalanceId" name="balance_id">
                <input type="hidden" id="editBalanceEmployeeId" name="employee_id">
                <input type="hidden" id="editBalanceYear" name="year">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Leave Types</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @php
                            $leaveTypes = [
                                'vacation' => ['label' => 'Vacation Leave'],
                                'sick' => ['label' => 'Sick Leave'],
                                'sil' => ['label' => 'SIL (Service Incentive Leave)'],
                            ];
                            // Everything else (Personal, Emergency, Maternity, Paternity,
                            // Bereavement, Study) is incremental with no cap to set — see
                            // LeaveRequest::UNCAPPED_LEAVE_TYPES.
                        @endphp
                        @foreach($leaveTypes as $type => $config)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $config['label'] }}</label>
                            <input type="number"
                                   name="{{ $type }}_days_total"
                                   id="edit_{{ $type }}_days_total"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Days">
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeEditLeaveBalanceModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-white hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Leave Balance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSetLeaveBalanceModal() {
    const selectedEmployeeId = document.getElementById('employee').value;
    if (selectedEmployeeId) {
        document.getElementById('balanceEmployeeSelect').value = selectedEmployeeId;
    }
    document.getElementById('setLeaveBalanceModal').classList.remove('hidden');
}

function closeSetLeaveBalanceModal() {
    document.getElementById('setLeaveBalanceModal').classList.add('hidden');
    document.getElementById('setLeaveBalanceForm').reset();
}

function openEditLeaveBalanceModal(employeeId) {
    fetch(`{{ route('attendance.leave-management.balance') }}?employee_id=${employeeId}&year={{ now()->year }}`)
        .then(response => response.json())
        .then(data => {
            if (data.leave_balance) {
                const balance = data.leave_balance;
                document.getElementById('editBalanceId').value = balance.id;
                document.getElementById('editBalanceEmployeeId').value = balance.employee_id;
                document.getElementById('editBalanceYear').value = balance.year;

                document.getElementById('edit_vacation_days_total').value = balance.vacation_days_total || 0;
                document.getElementById('edit_sick_days_total').value = balance.sick_days_total || 0;
                document.getElementById('edit_bereavement_days_total').value = balance.bereavement_days_total || 0;

                document.getElementById('editLeaveBalanceModal').classList.remove('hidden');
            } else {
                showNotification('Leave balance not found', 'error');
            }
        })
        .catch(error => {
            console.error('Error fetching leave balance:', error);
            showNotification('Failed to load leave balance', 'error');
        });
}

function closeEditLeaveBalanceModal() {
    document.getElementById('editLeaveBalanceModal').classList.add('hidden');
}

document.getElementById('setLeaveBalanceForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    // Check if "All Employees" is selected
    if (data.employee_id === 'all') {
        if (!confirm('Are you sure you want to set leave balance for ALL employees? This will create/update leave balances for every employee.')) {
            return;
        }
    }

   const submitData = {
        employee_id: data.employee_id === 'all' ? 'all' : data.employee_id,
        year: parseInt(data.year),
        vacation_days_total: parseInt(data.vacation_days_total || 0),
        sick_days_total: parseInt(data.sick_days_total || 0),
        sil_days_total: parseInt(data.sil_days_total || 0),
    };

    try {
        const response = await fetch('{{ route("attendance.leave-management.balance.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(submitData)
        });

        const result = await response.json();

        if (response.ok) {
            showNotification(result.message || 'Leave balance set successfully', 'success');
            closeSetLeaveBalanceModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(result.error || 'Failed to set leave balance', 'error');
        }
    } catch (error) {
        console.error('Error setting leave balance:', error);
        showNotification('Failed to set leave balance', 'error');
    }
});

document.getElementById('editLeaveBalanceForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    const submitData = {
        vacation_days_total: parseInt(data.vacation_days_total || 0),
        sick_days_total: parseInt(data.sick_days_total || 0),
        bereavement_days_total: parseInt(data.bereavement_days_total || 0),
    };

    const balanceId = document.getElementById('editBalanceId').value;

    try {
        const response = await fetch(`{{ route("attendance.leave-management.balance.update", ":id") }}`.replace(':id', balanceId), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(submitData)
        });

        const result = await response.json();

        if (response.ok) {
            showNotification(result.message || 'Leave balance updated successfully', 'success');
            closeEditLeaveBalanceModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(result.error || 'Failed to update leave balance', 'error');
        }
    } catch (error) {
        console.error('Error updating leave balance:', error);
        showNotification('Failed to update leave balance', 'error');
    }
});
</script>

<style>
    /* Ensure all form inputs and selects are visible with dark text */
    #employee,
    #leaveType,
    #status,
    #dateFrom {
        color: #111827 !important; /* text-gray-900 */
        background-color: #ffffff !important; /* bg-white */
    }

    /* Ensure select options are visible */
    select option {
        color: #111827 !important;
        background-color: #ffffff !important;
    }

    select option:checked {
        color: #111827 !important;
        background-color: #f3f4f6 !important;
    }

    select option:hover {
        background-color: #e5e7eb !important;
        color: #111827 !important;
    }

    /* Date input styling */
    input[type="date"] {
        color: #111827 !important;
        background-color: #ffffff !important;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(0);
    }

    input[type="date"]::-webkit-datetime-edit-text,
    input[type="date"]::-webkit-datetime-edit-month-field,
    input[type="date"]::-webkit-datetime-edit-day-field,
    input[type="date"]::-webkit-datetime-edit-year-field {
        color: #111827 !important;
    }
</style>
@endsection
