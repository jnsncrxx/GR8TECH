@extends('layouts.dashboard-base')

@section('title', 'Report Results')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 uppercase">{{ $type }} Report</h1>
            <p class="mt-1 text-sm text-gray-600">
                @if($startDate && $endDate)
                    From: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - To: {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                @else
                    All Time
                @endif
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
            
            <form action="{{ route('reports.export') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="report_type" value="{{ $type }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <input type="hidden" name="department_id" value="{{ $departmentId }}">
                <input type="hidden" name="employee_id" value="{{ $employeeId }}">
                
                <button type="submit" name="format" value="csv" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-file-csv mr-2"></i> CSV
                </button>
                <button type="submit" name="format" value="excel" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-file-excel mr-2"></i> Excel
                </button>
                <button type="submit" name="format" value="pdf" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-file-pdf mr-2"></i> PDF
                </button>
            </form>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @if($type === 'attendance')
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        @elseif($type === 'leave')
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        @elseif($type === 'payroll')
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Period</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data as $row)
                        <tr>
                            @if($type === 'attendance')
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->time_in ? \Carbon\Carbon::parse($row->time_in)->format('h:i A') : '--' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('h:i A') : '--' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($row->status === 'present') bg-green-100 text-green-800 
                                        @elseif($row->status === 'late') bg-yellow-100 text-yellow-800
                                        @elseif($row->status === 'absent') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                </td>
                            @elseif($type === 'leave')
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->leave_type ? \App\Models\LeaveRequest::labelFor($row->leave_type) : 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($row->start_date)->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($row->end_date)->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($row->status === 'approved') bg-green-100 text-green-800 
                                        @elseif($row->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($row->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                </td>
                            @elseif($type === 'payroll')
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($row->pay_period_start)->format('M d') }} - {{ \Carbon\Carbon::parse($row->pay_period_end)->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($row->gross_pay, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($row->deductions, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($row->net_pay, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($row->status === 'paid' || $row->status === 'approved') bg-green-100 text-green-800 
                                        @elseif($row->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($row->status === 'canceled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-folder-open text-gray-300 text-4xl mb-4 block"></i>
                                No records found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
