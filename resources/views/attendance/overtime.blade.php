@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'attendance.overtime'])

@section('title', 'Overtime Management')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Clean up Flatpickr background to match inputs */
    .flatpickr-input[readonly] {
        background-color: #fff;
    }
    
    /* Highlight pending dates in flatpickr */
    .flatpickr-day.is-pending {
        background-color: #fef3c7 !important; /* amber-100 */
        border-color: #f59e0b !important; /* amber-500 */
        color: #92400e !important; /* amber-900 */
        border-radius: 0.25rem !important;
    }
    
    /* Highlight approved dates in flatpickr */
    .flatpickr-day.is-approved {
        background-color: #fee2e2 !important; /* red-100 */
        border-color: #ef4444 !important; /* red-500 */
        color: #991b1b !important; /* red-900 */
        border-radius: 0.25rem !important;
    }
    
    .calendar-legend {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.5rem;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    .legend-box {
        width: 1rem;
        height: 1rem;
        border: 1px solid;
        border-radius: 0.125rem;
    }
    .legend-box.pending {
        background-color: #fef3c7;
        border-color: #f59e0b;
    }
    .legend-box.approved {
        background-color: #fee2e2;
        border-color: #ef4444;
    }
</style>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Overtime Management</h1>
            @if($user->role === 'employee')
            <p class="mt-1 text-sm text-gray-600">Apply for overtime and track your requests</p>
            @else
            <p class="mt-1 text-sm text-gray-600">Track and manage employee overtime hours</p>
            @endif
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
                        <a href="{{ route('attendance.overtime.export', ['format' => 'pdf']) . '?' . http_build_query(request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-pdf mr-2 text-red-500"></i>Export as PDF
                        </a>
                        <a href="{{ route('attendance.overtime.export', ['format' => 'csv']) . '?' . http_build_query(request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-csv mr-2 text-green-500"></i>Export as CSV
                        </a>
                        <a href="{{ route('attendance.overtime.export', ['format' => 'xls']) . '?' . http_build_query(request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2 text-green-600"></i>Export as Excel
                        </a>
                    </div>
                </div>
            </div>
            @if($user->role === 'employee')
            <button id="applyOvertimeBtn" onclick="openOvertimeModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Apply for Overtime
            </button>
            @endif
        </div>
    </div>

    <!-- Overtime Summary -->
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
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Hours</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($summary['total_hours'], 1) }}h</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <form method="GET" action="{{ route('attendance.overtime') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $user->role === 'employee' ? '2' : '4' }} gap-4">
            @if($user->role !== 'employee')
            <div>
                <label for="employee" class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                <select id="employee" name="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors bg-white text-gray-900" style="background-color: white !important; color: #111827 !important;">
                    <option value="" style="color: #111827 !important;">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }} style="color: #111827 !important;">
                            {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select id="department" name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors bg-white text-gray-900" style="background-color: white !important; color: #111827 !important;">
                    <option value="" style="color: #111827 !important;">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }} style="color: #111827 !important;">
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors bg-white text-gray-900" style="background-color: white !important; color: #111827 !important;">
                    <option value="" style="color: #111827 !important;">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }} style="color: #111827 !important;">Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }} style="color: #111827 !important;">Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }} style="color: #111827 !important;">Rejected</option>
                </select>
            </div>
            @endif
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" id="dateFrom" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors bg-white text-gray-900" style="background-color: white !important; color: #111827 !important;">
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="w-full px-10 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Apply
                </button>
                <a href="{{ route('attendance.overtime') }}" class="w-full px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                    <i class="fas fa-times mr-2"></i>Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Overtime Records -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Overtime Records</h3>
            <p class="mt-1 text-sm text-gray-600">Employee overtime requests and approvals</p>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Overtime Hours
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rate
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Reason
                        </th>
                        <th class="px-10 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        @if(in_array($user->role, ['admin', 'hr', 'manager']))
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($overtimeRequests as $request)
                          @php
                              $initials = strtoupper(substr($request->employee->first_name, 0, 1) . substr($request->employee->last_name, 0, 1));
                              $hourlyRate = $request->employee->hourly_rate ?? 0;
                              $amount = $request->hours * $request->rate_multiplier * $hourlyRate;
                              
                              $displayStatus = method_exists($request, 'isPastDeadline') && $request->isPastDeadline() 
                                    ? \App\Models\OvertimeRequest::EXPIRED 
                                    : $request->status;

                              $statusColors = [
                                  'pending' => 'bg-yellow-100 text-yellow-800',
                                  'approved' => 'bg-green-100 text-green-800',
                                  'rejected' => 'bg-red-100 text-red-800',
                                  'expired' => 'bg-gray-100 text-gray-600'
                              ];
                              $statusColor = $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800';
                          @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ $initials }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->employee->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->employee->department->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ \App\Helpers\TimezoneHelper::formatHours($request->hours) }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($request->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($request->end_time)->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $request->rate_multiplier }}x</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">₱{{ number_format($amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 whitespace-normal break-words min-w-[150px] max-w-xs">{{ $request->reason }}</div>
                                @if($request->status === 'rejected' && $request->rejection_reason)
                                <div class="text-xs text-red-600 mt-1 whitespace-normal break-words min-w-[150px] max-w-xs">
                                    <span class="font-medium">{{ ucfirst($request->approver->role ?? 'Admin') }} Reason:</span> {{ $request->rejection_reason }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }} min-w-[80px]">
                                    {{ ucfirst($displayStatus) }}
                                </span>
                            </td>
                            @if(in_array($user->role, ['admin', 'hr', 'manager']))
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                @if($displayStatus === 'pending')
                                <div class="flex space-x-2 justify-center">
                                    <button onclick="approveOvertime('{{ $request->id }}')" class="text-green-600 hover:text-green-900 transition-colors" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="rejectOvertime('{{ $request->id }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @else
                                    <span class="text-gray-400 font-bold text-lg">&mdash;</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array($user->role, ['admin', 'hr', 'manager']) ? 8 : 7 }}" class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <i class="fas fa-clock text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-500 text-lg font-medium mb-2">No overtime requests found</p>
                                    <p class="text-gray-400 text-sm">Try adjusting your filters or date range.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($overtimeRequests->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $overtimeRequests->firstItem() }} to {{ $overtimeRequests->lastItem() }} of {{ $overtimeRequests->total() }} results
                </div>
                <div class="flex items-center space-x-2">
                    @if($overtimeRequests->onFirstPage())
                        <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded">Previous</span>
                    @else
                        <a href="{{ $overtimeRequests->previousPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif
                    
                    @for($i = 1; $i <= $overtimeRequests->lastPage(); $i++)
                        @if($i == $overtimeRequests->currentPage())
                            <span class="px-3 py-2 text-sm text-white bg-blue-600 rounded">{{ $i }}</span>
                        @else
                            <a href="{{ $overtimeRequests->url($i) }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $i }}</a>
                        @endif
                    @endfor
                    
                    @if($overtimeRequests->hasMorePages())
                        <a href="{{ $overtimeRequests->nextPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
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
                @forelse($overtimeRequests as $request)
                    @php
                        $initials = strtoupper(substr($request->employee->first_name, 0, 1) . substr($request->employee->last_name, 0, 1));
                        $hourlyRate = $request->employee->hourly_rate ?? 0;
                        $amount = $request->hours * $request->rate_multiplier * $hourlyRate;
                        
                        $displayStatus = method_exists($request, 'isPastDeadline') && $request->isPastDeadline() 
                              ? \App\Models\OvertimeRequest::EXPIRED 
                              : $request->status;

                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'expired' => 'bg-gray-100 text-gray-600'
                        ];
                        $statusColor = $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ $initials }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $request->employee->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->employee->department->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }} min-w-[80px]">
                                {{ ucfirst($displayStatus) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                            <div>
                                <div class="text-gray-500">Date</div>
                                <div class="font-medium">{{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Overtime Hours</div>
                                <div class="font-medium">{{ \App\Helpers\TimezoneHelper::formatHours($request->hours) }}</div>
                                <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($request->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($request->end_time)->format('h:i A') }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Rate</div>
                                <div class="font-medium">{{ $request->rate_multiplier }}x</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Amount</div>
                                <div class="font-medium">₱{{ number_format($amount, 2) }}</div>
                            </div>
                        </div>
                        <div class="text-sm mb-3">
                            <div class="text-gray-500">Reason</div>
                            <div class="font-medium whitespace-normal break-words">{{ $request->reason }}</div>
                        </div>
                        @if($request->status === 'rejected' && $request->rejection_reason)
                        <div class="text-sm mb-3">
                            <div class="text-red-500 font-medium">{{ ucfirst($request->approver->role ?? 'Admin') }} Reason</div>
                            <div class="font-medium text-red-600 whitespace-normal break-words">{{ $request->rejection_reason }}</div>
                        </div>
                        @endif
                        @if(in_array($user->role, ['admin', 'hr', 'manager']) && $displayStatus === 'pending')
                        <div class="flex justify-end space-x-2">
                            <button onclick="approveOvertime('{{ $request->id }}')" class="text-green-600 hover:text-green-900 transition-colors">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                            <button onclick="rejectOvertime('{{ $request->id }}')" class="text-red-600 hover:text-red-900 transition-colors">
                                <i class="fas fa-times mr-1"></i>Reject
                            </button>
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-clock text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500 text-lg font-medium mb-2">No overtime requests found</p>
                            <p class="text-gray-400 text-sm">Try adjusting your filters or date range.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Overtime Application Modal -->
<div id="overtimeModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999;" onclick="closeOvertimeModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6" style="max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Apply for Overtime</h3>
                <button onclick="closeOvertimeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="overtimeForm" class="space-y-4">
                @csrf
                <div>
                    <label for="overtimeDate" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" id="overtimeDate" name="date" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    <div class="calendar-legend">
                        <div class="legend-item">
                            <div class="legend-box pending"></div>
                            <span>Pending request</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-box approved"></div>
                            <span>Approved request</span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="startTime" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                        <input type="time" id="startTime" name="start_time" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="endTime" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                        <input type="time" id="endTime" name="end_time" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    </div>
                </div>
                
                <div>
                    <label for="overtimeReason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea id="overtimeReason" name="reason" rows="3" required 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                              placeholder="Please provide a reason for your overtime request..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeOvertimeModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-white hover:bg-blue-700 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(in_array($user->role, ['admin', 'hr', 'manager']))
<!-- Overtime Approve Modal -->
<div id="overtimeApproveModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999;" onclick="closeApproveModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6" style="max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Approve Overtime Request</h3>
                <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="overtimeApproveForm" class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 mb-4">Are you sure you want to approve this overtime request?</p>
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

<!-- Overtime Reject Modal -->
<div id="overtimeRejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999;" onclick="closeRejectModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6" style="max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Reject Overtime Request</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="overtimeRejectForm" class="space-y-4">
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

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const overtimeDates = @json($user->role === 'employee' ? $employeeOvertimeDates : new \stdClass());

    flatpickr("#overtimeDate", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        disableMobile: "true",
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            // Get date in YYYY-MM-DD format taking timezone into account
            const date = new Date(dayElem.dateObj.getTime() - (dayElem.dateObj.getTimezoneOffset() * 60000)).toISOString().split('T')[0];
            
            if (overtimeDates[date]) {
                if (overtimeDates[date] === 'pending') {
                    dayElem.classList.add('is-pending');
                    dayElem.title = "Pending Overtime request";
                } else if (overtimeDates[date] === 'approved') {
                    dayElem.classList.add('is-approved');
                    dayElem.title = "Approved Overtime request";
                }
            }
        }
    });
    
    flatpickr("#dateFrom", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        disableMobile: "true"
    });
});
</script>
<script>
console.log('Overtime page JavaScript loaded successfully');

function openOvertimeModal() {
    console.log('Opening overtime modal');
    try {
        const modal = document.getElementById('overtimeModal');
        console.log('Modal element found:', !!modal);
        if (!modal) {
            console.error('Modal element not found');
            return;
        }
        
        // Force show the modal
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        
        console.log('Modal styles applied, display:', modal.style.display);
        
        // Reset form
        const form = document.getElementById('overtimeForm');
        if (form) {
            form.reset();
            console.log('Form reset successfully');
        } else {
            console.error('Form not found inside modal');
        }
        
        // Date input is handled by flatpickr
        const dateInput = document.getElementById('overtimeDate');
        if (dateInput) {
            console.log('Date input configured');
        } else {
            console.error('Date input not found');
        }
        
        console.log('Modal opening process completed successfully');
    } catch (error) {
        console.error('Error opening modal:', error);
    }
}

function closeOvertimeModal() {
    console.log('Closing overtime modal');
    const modal = document.getElementById('overtimeModal');
    if (modal) {
        modal.style.display = 'none';
        console.log('Modal closed successfully');
    } else {
        console.error('Modal not found for closing');
    }
}

async function submitOvertimeRequest(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Validate times
    if (data.start_time === data.end_time) {
        showError('Start time and end time cannot be the same');
        return;
    }
    
    try {
        const response = await fetch('{{ route("attendance.overtime.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showSuccess(result.message || 'Overtime request submitted successfully');
            closeOvertimeModal();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showError(result.error || 'Failed to submit overtime request');
        }
    } catch (error) {
        console.error('Error submitting overtime request:', error);
        showError('Failed to submit overtime request');
    }
}

// Add form submit event listener and button click listener
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners');
    try {
        // Form submit listener
        const form = document.getElementById('overtimeForm');
        if (form) {
            form.addEventListener('submit', submitOvertimeRequest);
            console.log('Form submit listener attached');
        } else {
            console.error('Form not found');
        }
        
        // Button click listener
        const button = document.getElementById('applyOvertimeBtn');
        if (button) {
            button.addEventListener('click', function(e) {
                console.log('Button clicked via event listener');
                openOvertimeModal();
            });
            console.log('Button click listener attached');
        } else {
            console.error('Button not found');
        }
    } catch (error) {
        console.error('Error setting up event listeners:', error);
    }
    const approveForm = document.getElementById('overtimeApproveForm');
    if (approveForm) {
        approveForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!currentApproveId) return;
            try {
                const response = await fetch(`{{ url('attendance/overtime') }}/${currentApproveId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: 'approved' })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showSuccess(data.message || 'Overtime request approved successfully');
                    closeApproveModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showError(data.error || 'Failed to approve overtime request');
                }
            } catch (error) {
                console.error('Error approving overtime:', error);
                showError('Failed to approve overtime request');
            }
        });
    }

    const rejectForm = document.getElementById('overtimeRejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!currentRejectId) return;
            const reason = document.getElementById('rejectionReason').value;
            try {
                const response = await fetch(`{{ url('attendance/overtime') }}/${currentRejectId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: 'rejected', rejection_reason: reason })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showSuccess(data.message || 'Overtime request rejected successfully');
                    closeRejectModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showError(data.error || 'Failed to reject overtime request');
                }
            } catch (error) {
                console.error('Error rejecting overtime:', error);
                showError('Failed to reject overtime request');
            }
        });
    }
});

let currentApproveId = null;
function approveOvertime(requestId) {
    currentApproveId = requestId;
    const modal = document.getElementById('overtimeApproveModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
    }
}

function closeApproveModal() {
    const modal = document.getElementById('overtimeApproveModal');
    if (modal) modal.style.display = 'none';
}

let currentRejectId = null;
function rejectOvertime(requestId) {
    currentRejectId = requestId;
    const modal = document.getElementById('overtimeRejectModal');
    const form = document.getElementById('overtimeRejectForm');
    if (form) form.reset();
    if (modal) {
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
    }
}

function closeRejectModal() {
    const modal = document.getElementById('overtimeRejectModal');
    if (modal) modal.style.display = 'none';
}

function showSuccess(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function showError(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}
</script>
@endsection
