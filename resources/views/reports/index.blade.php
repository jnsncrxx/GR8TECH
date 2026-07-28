@extends('layouts.dashboard-base')

@section('title', 'Reports')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
        <p class="mt-1 text-sm text-gray-600">Generate and export attendance, leave, and payroll reports.</p>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <form action="{{ route('reports.generate') }}" method="GET" id="reportForm">
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Report Type <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Attendance -->
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="report_type" value="attendance" class="peer sr-only" required>
                            <div class="flex rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 ease-in-out hover:border-green-300 hover:bg-green-50 hover:shadow-md peer-checked:bg-green-50 peer-checked:border-green-500 peer-checked:ring-1 peer-checked:ring-green-500 active:scale-[0.98]">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">Attendance Report</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">Time-in, time-out, and hours worked.</span>
                                    </span>
                                </span>
                                <i class="fas fa-clock text-green-500 mt-1"></i>
                            </div>
                        </label>
                        
                        <!-- Leave -->
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="report_type" value="leave" class="peer sr-only">
                            <div class="flex rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 ease-in-out hover:border-purple-300 hover:bg-purple-50 hover:shadow-md peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:ring-1 peer-checked:ring-purple-500 active:scale-[0.98]">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">Leave Report</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">Leave requests and usage history.</span>
                                    </span>
                                </span>
                                <i class="fas fa-calendar-times text-purple-500 mt-1"></i>
                            </div>
                        </label>
                        
                        <!-- Payroll -->
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="report_type" value="payroll" class="peer sr-only">
                            <div class="flex rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 ease-in-out hover:border-green-300 hover:bg-green-50 hover:shadow-md peer-checked:bg-green-50 peer-checked:border-green-500 peer-checked:ring-1 peer-checked:ring-green-500 active:scale-[0.98]">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">Payroll Report</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">Gross pay, deductions, and net pay.</span>
                                    </span>
                                </span>
                                <i class="fas fa-money-bill-wave text-green-500 mt-1"></i>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <div class="flex items-center space-x-2">
                            <input type="date" name="start_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-gray-500">to</span>
                            <input type="date" name="end_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department (Optional)</label>
                        <select name="department_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee (Optional)</label>
                        <select name="employee_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-search mr-2"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
