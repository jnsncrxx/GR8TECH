@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'attendance.period-management.index'])

@section('title', 'Period Details - ' . $period->name)

@php
    function formatHoursToReadable($decimalHours) {
        if ($decimalHours <= 0) {
            return '0 hrs';
        }
        
        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);
        
        // Handle minute rounding that might exceed 59
        if ($minutes >= 60) {
            $hours += 1;
            $minutes = 0;
        }
        
        $result = '';
        
        if ($hours > 0) {
            $result .= $hours . ' hr' . ($hours > 1 ? 's' : '');
        }
        
        if ($minutes > 0) {
            if ($hours > 0) {
                $result .= ' ';
            }
            $result .= $minutes . ' min' . ($minutes > 1 ? 's' : '');
        }
        
        return $result ?: '0 hrs';
    }
@endphp

@php
    // Used only to prevent resetting validation after payroll records exist.
    $existingPayrolls = $existingPayrolls ?? \App\Models\Payroll::query()
        ->whereDate('pay_period_start', $period->start_date->format('Y-m-d'))
        ->whereDate('pay_period_end', $period->end_date->format('Y-m-d'))
        ->get();
@endphp

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $period->name }}</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $period->start_date->format('M j, Y') }} - 
                            {{ $period->end_date->format('M j, Y') }}
                        </p>
                        @if($period->description)
                            <p class="mt-1 text-sm text-gray-500">{{ $period->description }}</p>
                        @endif
                        @if(!empty($period->department_id) || !empty($period->employee_ids))
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-filter mr-1"></i>
                                    @if(!empty($period->employee_ids) && count($period->employee_ids) > 0)
                                        {{ count($period->employee_ids) }} Employee(s) Analysis
                                    @elseif(!empty($period->department_id))
                                        Department Filtered Analysis
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        @if($user->role !== 'employee')
                            @if($period->status === \App\Models\Period::STATUS_READY)
                                <a href="{{ route('payroll.runs', ['period_id' => $period->id]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-white hover:bg-green-700">
                                    <i class="fas fa-arrow-right mr-2"></i>
                                    Send to Payroll
                                </a>
                            @elseif(in_array($period->status, [
                                \App\Models\Period::STATUS_PROCESSING,
                                \App\Models\Period::STATUS_FOR_REVIEW,
                                \App\Models\Period::STATUS_FINALIZED,
                                \App\Models\Period::STATUS_LOCKED,
                            ], true))
                                <a href="{{ route('payroll.periods.review', $period->id) }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700">
                                    <i class="fas fa-clipboard-check mr-2"></i>
                                    Open in Payroll
                                </a>
                            @endif


                        @endif
                        <a href="{{ route('attendance.period-management.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Periods
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

        <!-- Phase 2 Validation Workflow -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Pre-Payroll Validation
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Confirm Attendance, Leave, Official Business, and Overtime before payroll generation.
                    </p>
                </div>

                <div class="min-w-56">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">Progress</span>
                        <span class="font-semibold text-gray-900">
                            {{ $period->validation_progress }}%
                        </span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                        <div class="h-full bg-green-600"
                             style="width: {{ $period->validation_progress }}%">
                        </div>
                    </div>
                </div>
            </div>

            @if(!in_array($period->status, [
                \App\Models\Period::STATUS_FOR_VALIDATION,
                \App\Models\Period::STATUS_READY,
                \App\Models\Period::STATUS_PROCESSING,
            ], true))
                <div class="mt-4 rounded-lg border border-yellow-200 bg-yellow-50 p-3 text-sm text-yellow-800">
                    Move this period to <strong>For Validation</strong> before confirming the validation gates.
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mt-5">
                @foreach($validationSummary as $component => $item)
                    <div class="rounded-lg border {{ $item['validated'] ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-white' }} p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $item['label'] }}
                                </p>
                                <p class="mt-1 text-xs {{ $item['validated'] ? 'text-green-700' : 'text-gray-500' }}">
                                    @if($item['validated'])
                                        Validated {{ optional($item['validated_at'])->format('M j, Y g:i A') }}
                                    @else
                                        Pending validation
                                    @endif
                                </p>
                            </div>

                            <span class="h-8 w-8 rounded-full flex items-center justify-center {{ $item['validated'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                <i class="fas {{ $item['validated'] ? 'fa-check' : 'fa-hourglass-half' }}"></i>
                            </span>
                        </div>

                        @if($user->role !== 'employee'
                            && in_array($period->status, [
                                \App\Models\Period::STATUS_FOR_VALIDATION,
                                \App\Models\Period::STATUS_READY,
                            ], true))
                            <div class="mt-4">
                                @if(!$item['validated'])
                                    <form method="POST"
                                          action="{{ route('attendance.period-management.validate-component', [$period->id, $component]) }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-3 py-2 rounded-lg bg-green-600 text-sm font-medium text-white hover:bg-green-700">
                                            Confirm Validation
                                        </button>
                                    </form>
                                @elseif($existingPayrolls->isEmpty())
                                    <form id="reset-validation-form-{{ $component }}" method="POST"
                                          action="{{ route('attendance.period-management.reset-validation-component', [$period->id, $component]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="openAppConfirmationModal('reset-validation-form-{{ $component }}', 'Reset {{ $item['label'] }} validation?', 'This gate must be reviewed and confirmed again before payroll can proceed.', 'Reset Validation', 'amber')"
                                                class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Reset
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($period->status === \App\Models\Period::STATUS_READY)
                <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    <i class="fas fa-check-circle mr-2"></i>
                    All validation gates are complete. This period is Ready for Payroll.
                </div>
            @endif
        </div>

        @if(isset($scheduleExceptions) && $scheduleExceptions->isNotEmpty())
            @php
                $warningOnlyIssues = ['Rest Day Duty Review'];
                $hasBlockingScheduleExceptions = $scheduleExceptions->contains(
                    fn ($exception) => collect($exception['validation_issues'] ?? [])
                        ->contains(fn ($issue) => !in_array($issue, $warningOnlyIssues, true))
                );
                $exceptionTone = $hasBlockingScheduleExceptions ? 'red' : 'amber';
            @endphp
            <div class="bg-white rounded-lg shadow-sm border border-{{ $exceptionTone }}-200 mb-6 overflow-hidden">
                <div class="px-6 py-4 bg-{{ $exceptionTone }}-50 border-b border-{{ $exceptionTone }}-200">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-{{ $exceptionTone }}-900">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Schedule & Attendance Exceptions
                        </h2>
                        <a href="{{ route('attendance.timekeeping', ['date_from' => $period->start_date->format('Y-m-d'), 'date_to' => $period->end_date->format('Y-m-d'), 'exception' => 'attention']) }}" class="ui-solid-danger-action inline-flex items-center rounded-lg border border-{{ $exceptionTone }}-300 bg-white px-3 py-2 text-sm font-medium text-{{ $exceptionTone }}-700 hover:bg-{{ $exceptionTone }}-100">
                            <i class="fas fa-external-link-alt mr-2"></i>Review in Timekeeping
                        </a>
                    </div>
                    <p class="mt-1 text-sm text-{{ $exceptionTone }}-700">
                        {{ $hasBlockingScheduleExceptions
                            ? 'Resolve the blocking items before confirming Attendance Validation.'
                            : 'Manager review required. A valid rest-day duty may remain a warning when its approved duty/overtime filing is present.' }}
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Employee</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Date</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Schedule</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Actual Log</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Issue(s)</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Review</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @php
                                // Where each issue type gets resolved. Attendance/schedule-shape
                                // issues live in Timekeeping; the rest belong to their own module.
                                $issueRouteMap = [
                                    'Leave Conflict' => 'attendance.leave-management',
                                    'OT Without Attendance' => 'attendance.overtime',
                                    'OT Before Required Hours' => 'attendance.overtime',
                                    'OT Overlaps Leave' => 'attendance.overtime',
                                    'Unverified Official Business' => 'attendance.official-business',
                                ];
                            @endphp
                            @foreach($scheduleExceptions->take(100) as $exception)
                                @php
                                    $issues = $exception['validation_issues'] ?? (
                                        !empty($exception['validation_issue']) ? [$exception['validation_issue']] : []
                                    );
                                    $primaryIssue = $issues[0] ?? null;
                                    $reviewRoute = $issueRouteMap[$primaryIssue] ?? 'attendance.timekeeping';
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-gray-900">
                                        {{ $exception['employee_code'] ?? '—' }} - {{ $exception['employee_name'] ?? 'Unknown' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $exception['date_formatted'] ?? $exception['date'] ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $exception['schedule_in_out'] ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $exception['actual_in_out'] ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($issues as $issue)
                                                @php
                                                    $isWarning = in_array($issue, $warningOnlyIssues, true);
                                                @endphp
                                                <span class="inline-flex rounded-full bg-{{ $isWarning ? 'amber' : 'red' }}-100 px-2.5 py-1 text-xs font-semibold text-{{ $isWarning ? 'amber' : 'red' }}-700">
                                                    {{ $issue }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if(\Illuminate\Support\Facades\Route::has($reviewRoute))
                                            <a href="{{ route($reviewRoute, ['date_from' => $exception['date'] ?? null, 'date_to' => $exception['date'] ?? null]) }}"
                                               class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                                Review
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($scheduleExceptions->count() > 100)
                    <div class="px-6 py-3 bg-gray-50 text-xs text-gray-600">
                        Showing the first 100 of {{ $scheduleExceptions->count() }} exceptions.
                    </div>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <!-- Total Employees -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-users text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $summaryData['total_employees'] }}</h3>
                        <p class="text-xs text-gray-600">Employees</p>
                    </div>
                </div>
            </div>

            <!-- Present Days -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-check-circle text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $summaryData['present_days'] }}</h3>
                        <p class="text-xs text-gray-600">Present Days</p>
                    </div>
                </div>
            </div>

            <!-- Absent Days -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-times text-red-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $summaryData['absent_days'] }}</h3>
                        <p class="text-xs text-gray-600">Absent Days</p>
                    </div>
                </div>
            </div>

            <!-- Scheduled Hours -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-clock text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ formatHoursToReadable($summaryData['total_scheduled_hours']) }}</h3>
                        <p class="text-xs text-gray-600">Scheduled</p>
                    </div>
                </div>
            </div>

            <!-- Total Overtime -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-plus text-orange-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ formatHoursToReadable($summaryData['total_morning_overtime_hours'] + $summaryData['total_evening_overtime_hours']) }}</h3>
                        <p class="text-xs text-gray-600">Overtime</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comprehensive Attendance Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Attendance Records</h2>
                    <div class="flex space-x-3">
                        <button onclick="expandAll()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-expand-alt mr-2"></i>
                            Expand All
                        </button>
                        <button onclick="collapseAll()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-compress-alt mr-2"></i>
                            Collapse All
                        </button>
                        <button onclick="exportToCSV()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-download mr-2"></i>
                            Export CSV
                        </button>
                        <button onclick="exportToExcel()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-file-excel mr-2"></i>
                            Export Excel
                        </button>
                    </div>
                </div>
            </div>

            @php
                // Group data by employee
                $groupedData = collect($comprehensiveData)->groupBy('employee_id');
            @endphp

            <div class="space-y-4 p-6">
                @foreach($groupedData as $employeeId => $employeeRecords)
                <div class="border border-gray-200 rounded-lg" x-data="{ open: false }">
                    <!-- Employee Header (Always Visible) -->
                    <div class="bg-gray-50 px-4 py-3 cursor-pointer" @click="open = !open">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">
                                        {{ $employeeRecords->first()['employee_code'] ?? '—' }} - {{ $employeeRecords->first()['employee_name'] ?? 'Unknown Employee' }}
                                    </h4>
                                    <p class="text-sm text-gray-500">{{ $employeeRecords->count() }} record(s)</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-sm text-gray-500">
                                    @php
                                        $presentStatuses = ['Present', 'Late', 'Half Day', 'Official Business'];
                                        $dayOffStatuses = ['Day Off', 'Rest Day'];
                                        $holidayStatuses = ['Regular Holiday', 'Special Holiday', 'Holiday'];

                                        $presentCount = $employeeRecords->whereIn('attendance_status', $presentStatuses)->count();
                                        $absentCount = $employeeRecords->where('attendance_status', 'Absent')->count();
                                        $dayOffCount = $employeeRecords->whereIn('attendance_status', $dayOffStatuses)->count();
                                        $holidayCount = $employeeRecords->whereIn('attendance_status', $holidayStatuses)->count();
                                        $incompleteCount = $employeeRecords->filter(function ($record) {
                                            return in_array($record['attendance_status'] ?? null, [
                                                'Incomplete Log',
                                                'Present (No Time Out)',
                                            ], true);
                                        })->count();
                                    @endphp
                                    <span class="text-green-600 font-medium">{{ $presentCount }}P</span>
                                    <span class="text-red-600 font-medium">{{ $absentCount }}A</span>
                                    @if($dayOffCount > 0)
                                        <span class="text-slate-600 font-medium">{{ $dayOffCount }}D</span>
                                    @endif
                                    @if($holidayCount > 0)
                                        <span class="text-yellow-600 font-medium">{{ $holidayCount }}H</span>
                                    @endif
                                    @if($incompleteCount > 0)
                                        <span class="text-orange-600 font-medium">{{ $incompleteCount }}I</span>
                                    @endif
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Employee Records (Collapsible) -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule (In–Out)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Working Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual (In–Out)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Worked Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pre-Shift OT</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Post-Shift OT</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Late Arrival</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Night Shift</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($employeeRecords as $index => $record)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50 cursor-pointer" onclick="showEmployeeDetails('{{ $record['employee_id'] }}', '{{ $record['date'] }}')">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $record['date_formatted'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($record['schedule_status'] === 'Regular Holiday')
                                                <span class="text-yellow-600 font-semibold">{{ $record['schedule_in_out'] }}</span>
                                            @elseif($record['schedule_status'] === 'Special Holiday')
                                                <span class="text-pink-600 font-semibold">{{ $record['schedule_in_out'] }}</span>
                                            @elseif($record['schedule_status'] === 'Day Off')
                                                <span class="text-slate-600 font-medium">{{ $record['schedule_in_out'] }}</span>
                                            @elseif($record['schedule_status'] === 'Leave')
                                                <span class="text-purple-600 font-medium">{{ $record['schedule_in_out'] }}</span>
                                            @elseif($record['schedule_status'] === 'Holiday')
                                                <span class="text-red-600 font-medium">{{ $record['schedule_in_out'] }}</span>
                                            @else
                                                {{ $record['schedule_in_out'] }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($record['schedule_status'] === 'Regular Holiday')
                                                <span class="text-yellow-600 font-semibold">{{ $record['working_hours'] }}</span>
                                            @elseif($record['schedule_status'] === 'Special Holiday')
                                                <span class="text-pink-600 font-semibold">{{ $record['working_hours'] }}</span>
                                            @elseif($record['schedule_status'] === 'Day Off')
                                                <span class="text-slate-600 font-medium">{{ $record['working_hours'] }}</span>
                                            @elseif($record['schedule_status'] === 'Leave')
                                                <span class="text-purple-600 font-medium">{{ $record['working_hours'] }}</span>
                                            @elseif($record['schedule_status'] === 'Holiday')
                                                <span class="text-red-600 font-medium">{{ $record['working_hours'] }}</span>
                                            @else
                                                {{ $record['working_hours'] }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $record['actual_in_out'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($record['worked_hours'] === '—')
                                                <span class="text-gray-400">{{ $record['worked_hours'] }}</span>
                                            @else
                                                {{ $record['worked_hours'] }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($record['scheduled_hours'] === '—')
                                                <span class="text-gray-400">{{ $record['scheduled_hours'] }}</span>
                                            @else
                                                {{ $record['scheduled_hours'] }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($record['morning_overtime'] > 0)
                                                {{ formatHoursToReadable($record['morning_overtime']) }}
                                            @else
                                                <span class="text-gray-400">0 hrs</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($record['evening_overtime'] > 0)
                                                {{ formatHoursToReadable($record['evening_overtime']) }}
                                            @else
                                                <span class="text-gray-400">0 hrs</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($record['late_minutes'] > 0)
                                                <span class="text-red-600 font-medium">{{ $record['late_minutes'] }} min</span>
                                            @else
                                                <span class="text-gray-400">0</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($record['is_night_shift'] && $record['night_differential_hours'] > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                     {{ formatHoursToReadable($record['night_differential_hours']) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($record['attendance_status'] === 'Present') bg-green-100 text-green-800
                                                @elseif($record['attendance_status'] === 'Absent') bg-red-100 text-red-800
                                                @elseif($record['attendance_status'] === 'Error') bg-yellow-100 text-yellow-800
                                                @elseif($record['attendance_status'] === 'Day Off') bg-gray-100 text-gray-800
                                                @elseif($record['attendance_status'] === 'No Schedule') bg-gray-100 text-gray-500
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                @if($record['attendance_status'] === 'Present')
                                                    @if($record['schedule_status'] === 'Regular Holiday')
                                                        🟢 <span class="text-yellow-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @elseif($record['schedule_status'] === 'Special Holiday')
                                                        🟢 <span class="text-pink-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @else
                                                        🟢 {{ $record['combined_status'] }}
                                                    @endif
                                                @elseif($record['attendance_status'] === 'Absent')
                                                    @if($record['schedule_status'] === 'Regular Holiday')
                                                        🔴 <span class="text-yellow-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @elseif($record['schedule_status'] === 'Special Holiday')
                                                        🔴 <span class="text-pink-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @else
                                                        🔴 {{ $record['combined_status'] }}
                                                    @endif
                                                @elseif($record['attendance_status'] === 'Error')
                                                    @if($record['schedule_status'] === 'Regular Holiday')
                                                        🟡 <span class="text-yellow-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @elseif($record['schedule_status'] === 'Special Holiday')
                                                        🟡 <span class="text-pink-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @else
                                                        🟡{{ $record['combined_status'] }}
                                                    @endif
                                                @elseif($record['attendance_status'] === 'Day Off')
                                                    ⚪ {{ $record['combined_status'] }}
                                                @elseif($record['attendance_status'] === 'No Schedule')
                                                    <span class="text-gray-500">{{ $record['combined_status'] }}</span>
                                                @else
                                                    ⚪ {{ $record['combined_status'] }}
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if(empty($comprehensiveData))
                <div class="text-center py-12">
                    <div class="mx-auto h-16 w-16 text-gray-400">
                        <i class="fas fa-calendar-times text-4xl"></i>
                                </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No attendance records found</h3>
                    <p class="mt-2 text-sm text-gray-600">No attendance data available for the selected period.</p>
                                </div>
            @endif
        </div>
    </div>
</div>

@include('components.confirmation-modal')

<script>
// Export functions
function exportToCSV() {
    const data = @json($comprehensiveData);
    const headers = ['Employee', 'Date', 'Schedule (In–Out)', 'Working Hours', 'Actual (In–Out)', 'Worked Hours', 'Scheduled Hours', 'Pre-Shift OT', 'Post-Shift OT', 'Late Arrival', 'Status'];
    
    let csvContent = headers.join(',') + '\n';
    
    data.forEach(record => {
        const row = [
            `"${record.employee_code} - ${record.employee_name}"`,
            record.date_formatted,
            record.schedule_in_out,
            record.working_hours,
            record.actual_in_out,
            record.worked_hours,
            record.scheduled_hours,
            record.morning_overtime > 0 ? `${record.morning_overtime} hrs` : '0',
            record.evening_overtime > 0 ? `${record.evening_overtime} hrs` : '0',
            record.late_minutes > 0 ? `${record.late_minutes} min` : '0',
            `"${record.combined_status}"`
        ];
        csvContent += row.join(',') + '\n';
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'attendance_records_{{ $period["name"] }}.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToExcel() {
    // For now, we'll export as CSV with .xlsx extension
    // In a real implementation, you'd use a library like SheetJS
    exportToCSV();
}

function showEmployeeDetails(employeeId, date) {
    // This could open a modal or navigate to a detailed view
    alert(`Employee ID: ${employeeId}\nDate: ${date}\n\nDetailed view coming soon!`);
}

// Expand/Collapse all functionality
function expandAll() {
    document.querySelectorAll('[x-data]').forEach(element => {
        element._x_dataStack[0].open = true;
    });
}

function collapseAll() {
    document.querySelectorAll('[x-data]').forEach(element => {
        element._x_dataStack[0].open = false;
    });
}
</script>
@endsection
