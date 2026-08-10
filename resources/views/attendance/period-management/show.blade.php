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
        <div class="w-full max-w-none px-4 sm:px-6 lg:px-8 xl:px-10">
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
    <div class="w-full max-w-none px-4 sm:px-6 lg:px-8 xl:px-10 py-4">

        @if($period->needsLockReminder())
            <div class="mb-6 flex flex-col gap-3 rounded-xl border border-amber-300 bg-amber-50 p-5 text-amber-900 shadow-sm dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-100 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-3">
                    <i class="fas fa-lock-open mt-0.5 text-xl text-amber-600 dark:text-amber-400"></i>
                    <div>
                        <h2 class="font-semibold">Payroll cutoff completed — manual lock pending</h2>
                        <p class="mt-1 text-sm text-amber-800 dark:text-amber-200">
                            This cutoff ended {{ $period->end_date->format('M j, Y') }}. Complete validation, generation, and review, then manually lock the payroll.
                        </p>
                    </div>
                </div>
                <span class="whitespace-nowrap rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase text-amber-800 dark:bg-amber-900/60 dark:text-amber-200">
                    {{ $period->status_label }}
                </span>
            </div>
        @endif

        <!-- Phase 2 Validation Workflow -->
        <div class="w-full max-w-none rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6 mb-6">
            <div class="flex w-full flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Pre-Payroll Validation
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Live checks for Attendance, Leave, Official Business, and Overtime. Resolve all blocking items before payroll generation.
                    </p>
                </div>

                <div class="min-w-56">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">Progress</span>
                        <span class="font-semibold text-gray-900">
                            {{ collect($validationSummary)->where('validated', true)->count() * 25 }}%
                        </span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                        <div class="h-full bg-green-600"
                             style="width: {{ collect($validationSummary)->where('validated', true)->count() * 25 }}%">
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

            @php
                $attendanceReviewEndDate = $period->end_date->copy()->min(
                    now(config('app.timezone'))->startOfDay()
                );

                $componentResolveRoutes = [
                    'attendance' => route('attendance.timekeeping', [
                        'date_from' => $period->start_date->format('Y-m-d'),
                        'date_to' => $attendanceReviewEndDate->format('Y-m-d'),
                        'exception' => 'blocking',
                    ]),
                    'leave' => route('attendance.leave-management', [
                        'from_date' => $period->start_date->format('Y-m-d'),
                        'to_date' => $period->end_date->format('Y-m-d'),
                        'status' => 'pending',
                    ]),
                    'ob' => route('attendance.official-business', [
                        'date_from' => $period->start_date->format('Y-m-d'),
                        'date_to' => $period->end_date->format('Y-m-d'),
                        'status' => 'pending',
                    ]),
                    'overtime' => route('attendance.overtime', [
                        'date_from' => $period->start_date->format('Y-m-d'),
                        'date_to' => $period->end_date->format('Y-m-d'),
                        'status' => 'pending',
                    ]),
                ];
            @endphp

            <div class="mt-5 grid w-full grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach($validationSummary as $component => $item)
                    @php
                        $hasErrors = !empty($item['errors']);
                        $hasWarnings = !empty($item['warnings']);
                        $tone = $item['validated'] ? 'green' : ($hasErrors ? 'red' : 'amber');
                    @endphp

                    <div class="rounded-lg border border-{{ $tone }}-200 bg-{{ $tone }}-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $item['label'] }}</p>
                                <p class="mt-1 text-xs text-{{ $tone }}-700">
                                    @if($item['validated'])
                                        Passed {{ optional($item['validated_at'])->format('M j, Y g:i A') }}
                                    @elseif($hasErrors)
                                        {{ count($item['errors']) }} blocking issue(s)
                                    @else
                                        Ready to validate
                                    @endif
                                </p>
                            </div>

                            <span class="h-8 w-8 shrink-0 rounded-full flex items-center justify-center bg-{{ $tone }}-100 text-{{ $tone }}-700">
                                <i class="fas {{ $item['validated'] ? 'fa-check' : ($hasErrors ? 'fa-exclamation-triangle' : 'fa-hourglass-half') }}"></i>
                            </span>
                        </div>

                        @if($hasErrors)
                            <ul class="mt-3 space-y-1 text-xs text-red-700 list-disc pl-4">
                                @foreach(array_slice($item['errors'], 0, 3) as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>

                            <a href="{{ $componentResolveRoutes[$component] }}"
                               class="mt-3 inline-flex items-center text-xs font-semibold text-red-700 hover:text-red-900">
                                <i class="fas fa-external-link-alt mr-1"></i>Resolve issues
                            </a>
                        @elseif($hasWarnings)
                            <p class="mt-3 text-xs text-amber-700">{{ $item['warnings'][0] }}</p>
                        @endif

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
                                                @disabled($hasErrors)
                                                title="{{ $hasErrors ? 'Resolve all blocking issues first.' : 'Run the current validation checks.' }}"
                                                class="w-full px-3 py-2 rounded-lg text-sm font-medium text-white {{ $hasErrors ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }}">
                                            {{ $hasErrors ? 'Cannot Validate' : 'Run Validation' }}
                                        </button>
                                    </form>
                                @elseif($existingPayrolls->isEmpty())
                                    <form id="reset-validation-form-{{ $component }}" method="POST"
                                          action="{{ route('attendance.period-management.reset-validation-component', [$period->id, $component]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="openAppConfirmationModal('reset-validation-form-{{ $component }}', 'Reset {{ $item['label'] }} validation?', 'This gate must be reviewed and validated again before payroll can proceed.', 'Reset Validation', 'amber')"
                                                class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Revalidate
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @php
                $blockingValidationItems = collect($validationSummary)
                    ->filter(fn ($item) => !empty($item['errors']))
                    ->map(function ($item, $component) use ($componentResolveRoutes) {
                        return [
                            'component' => $component,
                            'label' => $item['label'],
                            'errors' => $item['errors'] ?? [],
                            'details' => $item['details'] ?? [],
                            'url' => $componentResolveRoutes[$component] ?? '#',
                        ];
                    });
                $blockingIssueCount = $blockingValidationItems->sum(
                    fn ($item) => count($item['details']) ?: count($item['errors'])
                );
            @endphp

            @if($blockingValidationItems->isNotEmpty())
                <div class="mt-5 w-full max-w-none overflow-hidden rounded-xl border border-red-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-3 border-b border-red-200 bg-red-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-red-900">
                                <i class="fas fa-ban mr-2"></i>Payroll Blocking Issues
                                <span class="ml-1 rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-700">
                                    {{ $blockingIssueCount }}
                                </span>
                            </h3>
                            <p class="mt-1 text-sm text-red-700">
                                Payroll cannot proceed until all listed items within this cutoff are resolved.
                            </p>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach($blockingValidationItems as $blockingItem)
                            <section class="w-full py-4">
                                <div class="mb-3 flex flex-wrap items-center justify-between gap-3 px-5">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-700">
                                            <i class="fas fa-exclamation-triangle text-sm"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $blockingItem['label'] }}</h4>
                                            <p class="text-xs text-gray-500">
                                                {{ count($blockingItem['details']) ?: count($blockingItem['errors']) }} item(s) require action
                                            </p>
                                        </div>
                                    </div>

                                    <a href="{{ $blockingItem['url'] }}"
                                       class="inline-flex items-center rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                                        Review {{ $blockingItem['label'] }}
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                </div>

                                @if(!empty($blockingItem['details']))
                                    <div class="w-full max-h-[340px] overflow-x-auto overflow-y-auto border-y border-gray-200">
                                        <table class="w-full table-auto divide-y divide-gray-200 text-sm">
                                            <thead class="sticky top-0 z-10 bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left font-medium text-gray-600">Employee</th>
                                                    <th class="px-6 py-3 text-left font-medium text-gray-600">Date / Coverage</th>
                                                    <th class="px-6 py-3 text-left font-medium text-gray-600">Issue</th>
                                                    <th class="px-6 py-3 text-left font-medium text-gray-600">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 bg-white">
                                                @foreach(array_slice($blockingItem['details'], 0, 25) as $detail)
                                                    <tr class="hover:bg-red-50/40">
                                                        <td class="px-6 py-3">
                                                            <div class="font-medium text-gray-900">
                                                                {{ $detail['employee_code'] ?? '—' }} - {{ $detail['employee_name'] ?? 'Unknown employee' }}
                                                            </div>
                                                            @if(!empty($detail['department']))
                                                                <div class="text-xs text-gray-500">{{ $detail['department'] }}</div>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-3 text-gray-700">{{ $detail['date_label'] ?? '—' }}</td>
                                                        <td class="px-6 py-3">
                                                            <div class="font-medium text-gray-800">{{ $detail['summary'] ?? 'Requires review' }}</div>
                                                            @if(!empty($detail['reason']))
                                                                <div class="mt-0.5 max-w-md truncate text-xs text-gray-500" title="{{ $detail['reason'] }}">
                                                                    {{ $detail['reason'] }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-3">
                                                            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                                {{ $detail['status'] ?? 'Blocked' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    @if(count($blockingItem['details']) > 25)
                                        <p class="mt-2 px-5 text-xs text-gray-500">
                                            Showing the first 25 of {{ count($blockingItem['details']) }} items. Open the module to review all records.
                                        </p>
                                    @endif
                                @else
                                    <div class="px-5">
                                        <ul class="space-y-1.5 rounded-lg border border-red-100 bg-red-50 p-3 text-sm text-red-800">
                                            @foreach($blockingItem['errors'] as $error)
                                                <li class="flex gap-2">
                                                    <i class="fas fa-circle mt-1.5 text-[6px]"></i>
                                                    <span>{{ $error }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </section>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($period->status === \App\Models\Period::STATUS_READY)
                <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    <i class="fas fa-check-circle mr-2"></i>
                    All validation gates are complete. This period is Ready for Payroll.
                </div>
            @endif
        </div>

        {{-- The former Schedule & Attendance Exceptions table was removed
             from this page because the same blocking rows are already shown
             in Payroll Blocking Issues above. The full operational exception
             table and filters remain available in Timekeeping. --}}

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

            <div class="space-y-3 p-3 sm:p-4">
                @foreach($groupedData as $employeeId => $employeeRecords)
                <div class="attendance-employee-accordion border border-gray-200 rounded-lg" x-data="{ open: false }">
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
                                        $scheduledCount = $employeeRecords->where('attendance_status', 'Scheduled')->count();
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
                                    @if($scheduledCount > 0)
                                        <span class="text-blue-600 font-medium">{{ $scheduledCount }}S</span>
                                    @endif
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
                        <div class="w-full overflow-x-auto">
                            <table class="w-full min-w-[1180px] table-auto divide-y divide-gray-200">
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
                                                @elseif($record['attendance_status'] === 'Scheduled') bg-blue-50 text-blue-700 border border-blue-200
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
                                                        🟢 <span class="text-green-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @endif

                                                @elseif($record['attendance_status'] === 'Absent')
                                                    @if($record['schedule_status'] === 'Regular Holiday')
                                                        🔴 <span class="text-yellow-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @elseif($record['schedule_status'] === 'Special Holiday')
                                                        🔴 <span class="text-pink-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @else
                                                        🔴 <span class="text-red-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @endif

                                                @elseif($record['attendance_status'] === 'Scheduled')
                                                    🔵 <span class="text-black-600 font-semibold">{{ $record['combined_status'] }}</span>

                                                @elseif($record['attendance_status'] === 'Error')
                                                    @if($record['schedule_status'] === 'Regular Holiday')
                                                        🟡 <span class="text-yellow-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @elseif($record['schedule_status'] === 'Special Holiday')
                                                        🟡 <span class="text-pink-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @else
                                                        🟡 <span class="text-amber-600 font-semibold">{{ $record['combined_status'] }}</span>
                                                    @endif

                                                @elseif($record['attendance_status'] === 'Day Off')
                                                    ⚪ <span class="text-gray-500 font-semibold">{{ $record['combined_status'] }}</span>

                                                @elseif($record['attendance_status'] === 'No Schedule')
                                                    ⚫ <span class="text-gray-500 font-semibold">{{ $record['combined_status'] }}</span>

                                                @else
                                                    ⚪ <span class="text-gray-500">{{ $record['combined_status'] }}</span>
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
function setAllAttendanceAccordions(open) {
    document.querySelectorAll('.attendance-employee-accordion').forEach((element) => {
        const state = element._x_dataStack?.[0];

        if (state && Object.prototype.hasOwnProperty.call(state, 'open')) {
            state.open = open;
        }
    });
}

function expandAll() {
    setAllAttendanceAccordions(true);
}

function collapseAll() {
    setAllAttendanceAccordions(false);
}
</script>
@endsection