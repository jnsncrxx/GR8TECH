@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'dashboard'])

@section('title', 'HR Dashboard')

@php
    $pageTitle = 'HR Dashboard';
@endphp

@section('content')
            <!-- Welcome Section -->
            <div class="mb-6 sm:mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">Welcome back, HR Manager!</h2>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Here's what's happening with your workforce today.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <x-dashboard.stats-card 
                    title="Total Employees" 
                    :value="number_format($stats['total_employees'])" 
                    icon="fas fa-users" 
                    color="blue" 
                />
                
                <x-dashboard.stats-card 
                    title="Departments" 
                    :value="number_format($stats['total_departments'])" 
                    icon="fas fa-building" 
                    color="green" 
                />
                
                <x-dashboard.stats-card 
                    title="New This Month" 
                    :value="number_format($stats['new_employees_this_month'])" 
                    icon="fas fa-user-plus" 
                    color="yellow" 
                />
                
                <x-dashboard.stats-card 
                    title="Avg. Salary" 
                    :value="'₱' . number_format($stats['average_salary'], 2)" 
                    icon="fas fa-money-bill-wave" 
                    color="purple" 
                />
            </div>

            @if(auth()->user()->employee)
                @php
                    $hrTodayAttendance = auth()->user()->employee->getTodayAttendance();
                    $hrIsClockedIn = $hrTodayAttendance && $hrTodayAttendance->hasActiveTimeEntry();
                @endphp
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4 sm:p-6 mb-6 sm:mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Time In / Time Out</h3>
                    </div>
                    @if($hrIsClockedIn)
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex flex-1 items-center justify-center px-4 py-3 bg-green-50 text-green-700 rounded-lg font-medium">
                                <span class="w-2.5 h-2.5 rounded-full bg-green-500 mr-2"></span>
                                You're Clocked In
                            </div>
                            <button onclick="sidebarConfirmTimeOut()" class="flex-1 flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Time Out
                            </button>
                        </div>
                    @else
                        <button onclick="sidebarConfirmTimeIn()" class="w-full flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Time In
                        </button>
                    @endif
                </div>
            @endif

            <!-- Payroll Overview Section -->
<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4 sm:p-6 mb-6 sm:mb-8">
    <div class="flex items-center justify-between mb-4 sm:mb-6">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Payroll Overview</h3>
        <a href="{{ route('payroll.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-xs sm:text-sm font-medium">Manage Payroll</a>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
        <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 dark:bg-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Total Payroll</p>
                    <p class="text-lg font-semibold text-green-900 dark:text-green-100">₱{{ number_format($payroll_stats['total_payroll'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 dark:bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Processed</p>
                    <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{ $payroll_stats['processed'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 dark:bg-yellow-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pending</p>
                    <p class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">{{ $payroll_stats['pending'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 dark:bg-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-credit-card text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Paid</p>
                    <p class="text-lg font-semibold text-purple-900 dark:text-purple-100">{{ $payroll_stats['paid'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons Section -->
    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-700">
        <div class="space-y-3">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Payroll Actions</h4>
            <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                <a href="{{ route('payroll.index') }}" 
                   class="inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-lg font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-slate-900 transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Generate Payroll
                </a>
                
                <button class="inline-flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-lg font-medium text-gray-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-slate-900 transition-colors shadow-sm">
                    <i class="fas fa-download mr-2"></i>
                    Export Payroll
                </button>
                
                <button class="inline-flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-lg font-medium text-gray-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-slate-900 transition-colors shadow-sm">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Generate Payslips
                </button>
            </div>
        </div>
    </div>
</div>

            <!-- Charts and Tables Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 mb-6 sm:mb-8">
                <!-- Department Breakdown -->
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Department Breakdown</h3>
                        <button class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-xs sm:text-sm font-medium">View All</button>
                    </div>
                    <div class="space-y-4">
                        @foreach($department_breakdown as $dept)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 dark:bg-blue-400 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $dept->name }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $dept->employees_count }} employees</span>
                                <div class="w-16 bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                    <div class="bg-blue-500 dark:bg-blue-400 h-2 rounded-full" style="width: {{ $dept->employees_count > 0 ? ($dept->employees_count / max($stats['total_employees'], 1)) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Recent Activity</h3>
                        <a href="{{ route('employees.index') }}" class="text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-medium">View All</a>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user-plus text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">New employee added</p>
                                <p class="text-xs text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-edit text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Employee record updated</p>
                                <p class="text-xs text-gray-500">4 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-money-bill-wave text-yellow-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Payroll processed</p>
                                <p class="text-xs text-gray-500">6 hours ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Payroll Runs Section -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 mb-6">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Recent Payroll Runs</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Latest payroll periods from Period Management</p>
                        </div>
                        <a href="{{ route('attendance.period-management.index') }}"
                           class="shrink-0 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-xs sm:text-sm font-medium">
                            View All
                        </a>
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full table-auto divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-800/70">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payroll Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employees</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gross Payroll</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Payroll</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payroll Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-700">
                            @forelse($recent_payroll_runs as $run)
                                @php
                                    $statusClasses = match($run->dashboard_status) {
                                        'paid' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'locked' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                        'finalized' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'for_review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                        'processing' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                                        'ready' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60">
                                    <td class="px-6 py-4 min-w-[240px]">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $run->name }}</div>
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $run->start_date?->format('M d, Y') ?? 'N/A' }} – {{ $run->end_date?->format('M d, Y') ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                        {{ number_format($run->payrolls_count) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                        ₱{{ number_format($run->payrolls_sum_gross_pay ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        ₱{{ number_format($run->payrolls_sum_net_pay ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses }}">
                                            {{ $run->dashboard_status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $run->payroll_date?->format('M d, Y') ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('attendance.period-management.show', $run->id) }}"
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No payroll runs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="lg:hidden divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($recent_payroll_runs as $run)
                        @php
                            $statusClasses = match($run->dashboard_status) {
                                'paid' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'locked' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                'finalized' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'for_review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'processing' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                                'ready' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300',
                                default => 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300',
                            };
                        @endphp
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $run->name }}</div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $run->start_date?->format('M d, Y') ?? 'N/A' }} – {{ $run->end_date?->format('M d, Y') ?? 'N/A' }}
                                    </div>
                                </div>
                                <span class="shrink-0 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClasses }}">
                                    {{ $run->dashboard_status_label }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Employees</div>
                                    <div class="mt-1 font-medium text-gray-900 dark:text-white">{{ number_format($run->payrolls_count) }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Payroll Date</div>
                                    <div class="mt-1 font-medium text-gray-900 dark:text-white">{{ $run->payroll_date?->format('M d, Y') ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Gross Payroll</div>
                                    <div class="mt-1 font-medium text-gray-900 dark:text-white">₱{{ number_format($run->payrolls_sum_gross_pay ?? 0, 2) }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Net Payroll</div>
                                    <div class="mt-1 font-medium text-gray-900 dark:text-white">₱{{ number_format($run->payrolls_sum_net_pay ?? 0, 2) }}</div>
                                </div>
                            </div>

                            <a href="{{ route('attendance.period-management.show', $run->id) }}"
                               class="mt-4 inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                View payroll run
                                <i class="fas fa-arrow-right ml-2 text-xs"></i>
                            </a>
                        </div>
                    @empty
                        <div class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No payroll runs found.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Employees Table -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Recent Employees</h3>
                        <button class="text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-medium">View All</button>
                    </div>
                </div>
                
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full min-w-full table-auto divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hire Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-700">
                            @foreach($recent_employees as $employee)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $employee->employee_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $employee->department?->name ?? 'No department' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->position?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($employee->salary, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $employee->hire_date?->format('M d, Y') ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Mobile Cards -->
                <div class="lg:hidden">
                    @foreach($recent_employees as $employee)
                    <div class="border-b border-gray-200 p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->employee_id }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->position?->name ?? 'N/A' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">₱{{ number_format($employee->salary, 2) }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->hire_date?->format('M d, Y') ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
@endsection