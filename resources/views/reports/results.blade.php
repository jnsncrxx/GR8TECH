@extends('layouts.dashboard-base')

@section('title', 'Report Results')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 uppercase">
                @if(count($types) > 1)
                    Consolidated Reports ({{ count($types) }} Modules Selected)
                @else
                    {{ str_replace('_', ' ', $types[0] ?? 'Consolidated') }} Report
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                @if($startDate && $endDate)
                    From: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - To: {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                @else
                    All Time
                @endif
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-full font-semibold text-xs text-gray-700 uppercase tracking-wider hover:bg-gray-300 focus:outline-none transition shadow-sm">
                <i class="fas fa-arrow-left mr-1.5"></i> Back
            </a>
            
            @php
                $exportParams = array_merge(request()->query(), [
                    'report_types' => $types,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'department_id' => $departmentId,
                    'employee_id' => $employeeId,
                ]);
            @endphp
            <div class="inline-flex space-x-2">
                <a href="{{ route('reports.export', array_merge($exportParams, ['format' => 'csv'])) }}" download class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-wider hover:bg-green-700 focus:outline-none transition shadow-sm">
                    <i class="fas fa-file-csv mr-1.5"></i> CSV
                </a>
                <a href="{{ route('reports.export', array_merge($exportParams, ['format' => 'excel'])) }}" download class="inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-wider hover:bg-green-900 focus:outline-none transition shadow-sm">
                    <i class="fas fa-file-excel mr-1.5"></i> Excel
                </a>
                <a href="{{ route('reports.export', array_merge($exportParams, ['format' => 'pdf'])) }}" download class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-wider hover:bg-red-700 focus:outline-none transition shadow-sm">
                    <i class="fas fa-file-pdf mr-1.5"></i> PDF
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Render Each Selected Report Module -->
    @foreach($types as $currentType)
        @php
            $currentData = $reportsData[$currentType] ?? collect();
            $titles = [
                'attendance' => 'Attendance Records',
                'leave' => 'Leave Requests',
                'overtime' => 'Overtime Requests',
                'official_business' => 'Official Business Requests',
                'payroll' => 'Payroll Records',
            ];
            $titleName = $titles[$currentType] ?? ucfirst(str_replace('_', ' ', $currentType));
        @endphp

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Section Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    @if($currentType === 'attendance')
                        <i class="fas fa-clock text-green-500 text-lg"></i>
                    @elseif($currentType === 'leave')
                        <i class="fas fa-calendar-times text-purple-500 text-lg"></i>
                    @elseif($currentType === 'overtime')
                        <i class="fas fa-user-clock text-blue-500 text-lg"></i>
                    @elseif($currentType === 'official_business')
                        <i class="fas fa-briefcase text-violet-500 text-lg"></i>
                    @elseif($currentType === 'payroll')
                        <i class="fas fa-money-bill-wave text-emerald-500 text-lg"></i>
                    @endif
                    <h2 class="text-lg font-bold text-gray-900">{{ $titleName }}</h2>
                </div>
                <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-semibold">
                    {{ count($currentData) }} {{ count($currentData) === 1 ? 'Record' : 'Records' }}
                </span>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if($currentType === 'attendance')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            @elseif($currentType === 'leave')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            @elseif($currentType === 'overtime')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            @elseif($currentType === 'payroll')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            @elseif($currentType === 'official_business')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OB Schedule</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($currentData as $row)
                            <tr class="hover:bg-gray-50 transition">
                                @if($currentType === 'attendance')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->time_in ? \Carbon\Carbon::parse($row->time_in)->format('h:i A') : '--' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('h:i A') : '--' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-3 py-1 inline-flex items-center justify-center min-w-[90px] text-xs font-semibold rounded-full border text-center leading-none
                                            @if($row->status === 'present') bg-green-50 text-green-700 border-green-200 
                                            @elseif($row->status === 'late') bg-yellow-50 text-yellow-700 border-yellow-200
                                            @elseif($row->status === 'absent') bg-red-50 text-red-700 border-red-200
                                            @elseif($row->status === 'on_leave') bg-blue-50 text-blue-700 border-blue-200
                                            @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                                            {{ ucwords(str_replace('_', ' ', $row->status ?? 'pending')) }}
                                        </span>
                                    </td>
                                @elseif($currentType === 'leave')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->leave_type ? \App\Models\LeaveRequest::labelFor($row->leave_type) : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($row->start_date)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($row->end_date)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-3 py-1 inline-flex items-center justify-center min-w-[90px] text-xs font-semibold rounded-full border text-center leading-none
                                            @if($row->status === 'approved') bg-green-50 text-green-700 border-green-200 
                                            @elseif($row->status === 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                            @elseif($row->status === 'rejected') bg-red-50 text-red-700 border-red-200
                                            @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                                            {{ ucwords(str_replace('_', ' ', $row->status ?? 'pending')) }}
                                        </span>
                                    </td>
                                @elseif($currentType === 'overtime')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->start_time ? \Carbon\Carbon::parse($row->start_time)->format('h:i A') : '--' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->end_time ? \Carbon\Carbon::parse($row->end_time)->format('h:i A') : '--' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">{{ number_format((float)($row->hours ?? 0), 2) }}h</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $row->reason ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-3 py-1 inline-flex items-center justify-center min-w-[90px] text-xs font-semibold rounded-full border text-center leading-none
                                            @if($row->status === 'approved') bg-green-50 text-green-700 border-green-200 
                                            @elseif($row->status === 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                            @elseif($row->status === 'rejected') bg-red-50 text-red-700 border-red-200
                                            @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                                            {{ ucwords(str_replace('_', ' ', $row->status ?? 'pending')) }}
                                        </span>
                                    </td>
                                @elseif($currentType === 'payroll')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($row->pay_period_start)->format('M d') }} - {{ \Carbon\Carbon::parse($row->pay_period_end)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($row->gross_pay, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($row->deductions, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($row->net_pay, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-3 py-1 inline-flex items-center justify-center min-w-[90px] text-xs font-semibold rounded-full border text-center leading-none
                                            @if($row->status === 'paid' || $row->status === 'approved') bg-green-50 text-green-700 border-green-200 
                                            @elseif($row->status === 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                            @elseif($row->status === 'canceled') bg-red-50 text-red-700 border-red-200
                                            @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                                            {{ ucwords(str_replace('_', ' ', $row->status ?? 'pending')) }}
                                        </span>
                                    </td>
                                @elseif($currentType === 'official_business')
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $row->ob_start_time?->format('h:i A') ?? '--' }} – {{ $row->ob_end_time?->format('h:i A') ?? '--' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ number_format((float) ($row->credited_hours ?? $row->computeCreditedHours()), 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $row->reason }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap text-center">
                                        <span class="px-3 py-1 inline-flex items-center justify-center min-w-[90px] text-xs font-semibold rounded-full border text-center leading-none
                                            @if($row->status === 'approved') bg-green-50 text-green-700 border-green-200 
                                            @elseif($row->status === 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                            @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                                            {{ ucwords(str_replace('_', ' ', $row->status ?? 'pending')) }}
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-folder-open text-gray-300 text-3xl mb-2 block"></i>
                                    No {{ strtolower($titleName) }} records found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
