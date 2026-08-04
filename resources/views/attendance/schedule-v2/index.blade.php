@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'schedule-v2.index'])

@section('title', 'Schedule Management')

@section('content')
<div class="schedule-management-page min-h-screen bg-gray-50">
    <style>
        .delete-mode {
            background-color: #fef2f2 !important;
        }

        .delete-mode .schedule-checkbox {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .schedule-calendar-scroll { max-height: min(68vh, 760px); scrollbar-gutter: stable; }
        .schedule-calendar-table { width: max-content; min-width: 100%; }
        .schedule-employee-column { width: 232px; min-width: 232px; max-width: 232px; }
        .schedule-date-column { width: 58px; min-width: 58px; }
        .schedule-grid-cell { height: 64px; }
        .schedule-calendar.is-compact .schedule-employee-column { width: 190px; min-width: 190px; max-width: 190px; }
        .schedule-calendar.is-compact .schedule-date-column { width: 42px; min-width: 42px; }
        .schedule-calendar.is-compact .calendar-cell-detail { display: none; }
        .schedule-calendar .calendar-cell-compact-label { display: none; }
        .schedule-calendar.is-compact .calendar-cell-detailed-label { display: none; }
        .schedule-calendar.is-compact .calendar-cell-compact-label { display: inline-flex; }
        .schedule-calendar.is-compact .schedule-grid-cell { height: 52px; }
        .schedule-calendar.is-compact .schedule-calendar-table { width: 100%; min-width: 1450px; table-layout: fixed; }
        .schedule-density-button[aria-pressed="true"] { background: #fff; color: #1f2937; box-shadow: 0 1px 2px rgb(0 0 0 / 0.08); }
    </style>
    <!-- Filters -->
    <div class="w-full px-3 sm:px-4 lg:px-5 py-6">
        <div class="schedule-filter-card bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-filter mr-2 text-blue-600"></i>
                    Filter & Search
                </h3>
                <p class="text-sm text-gray-600 mt-1">Use filters to find specific schedules or employees</p>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('schedule-v2.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-1"></i>Search Employee
                        </label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ $searchQuery }}" placeholder="Search by name..." autocomplete="off" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div id="employeeSuggestions" class="hidden absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
                        </div>
                    </div>

                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building mr-1"></i>Department
                        </label>
                        <select name="department_id" id="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-1"></i>Month
                        </label>
                        <select name="month" id="month" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($i)->format('F') }}
                                </option>
                                @endfor
                        </select>
                    </div>

                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-year mr-1"></i>Year
                        </label>
                        <select name="year" id="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @for($i = now()->year - 2; $i <= now()->year + 2; $i++)
                                <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                                @endfor
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-3 lg:space-y-0">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('schedule-v2.create', array_filter(['department_id' => $selectedDepartment, 'month' => $selectedMonth, 'year' => $selectedYear, 'search' => $searchQuery])) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                                <i class="fas fa-plus mr-2"></i>
                                Add Schedule
                            </a>
                            <button type="button" onclick="openBulkModalAjax(event)" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm">
                                <i class="fas fa-layer-group mr-2"></i>
                                Bulk Create
                            </button>
                            <button type="button" id="deleteModeBtn" onclick="toggleDeleteMode()" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors shadow-sm">
                                <i class="fas fa-trash mr-2"></i>
                                Delete Mode
                            </button>
                            <button type="button" id="dateSelectModeBtn" onclick="toggleDateSelectMode()" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors shadow-sm whitespace-nowrap">
                                <i class="fas fa-calendar-day mr-2"></i>
                                Select Dates
                            </button>
                            <button type="button" id="doneDateSelectBtn" onclick="showDateReviewModal()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm whitespace-nowrap hidden">
                                <i class="fas fa-edit mr-2"></i>
                                Edit All (<span id="selectedDatesCount">0</span>)
                            </button>
                            <button type="button" id="cancelDateSelectBtn" onclick="exitDateSelectMode()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors shadow-sm whitespace-nowrap hidden">
                                <i class="fas fa-times mr-2"></i>
                                Cancel Selection
                            </button>
                            <button type="button" id="bulkDeleteBtn" onclick="openBulkDeleteModal()" class="inline-flex items-center px-4 py-2 bg-red-700 border border-transparent rounded-lg font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors shadow-sm hidden">
                                <i class="fas fa-trash mr-2"></i>
                                <span id="bulkDeleteText">Delete Selected</span>
                            </button>
                            <button type="button" id="cancelDeleteBtn" onclick="exitDeleteMode()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors shadow-sm hidden">
                                <i class="fas fa-times mr-2"></i>
                                Cancel Delete
                            </button>
                        </div>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Schedule Grid -->
    @if($employees->count() > 0)
    <div class="w-full px-3 sm:px-4 lg:px-5 pb-6">
        <div class="schedule-calendar-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Calendar Header -->
            <div class="schedule-calendar-header bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b border-gray-200">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                                {{ Carbon\Carbon::create($selectedYear, $selectedMonth)->format('F Y') }} Schedule
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-users mr-1"></i>
                                {{ $employees->count() }} employee{{ $employees->count() !== 1 ? 's' : '' }}
                                @if($selectedDepartment)
                                in {{ $departments->where('id', $selectedDepartment)->first()->name ?? 'selected department' }}
                                @endif
                            </p>
                        </div>
                        <div id="selectAllContainer" class="flex items-center space-x-2 hidden">
                            <div class="flex items-center bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                <label class="flex items-center text-sm text-red-700 cursor-pointer">
                                    <input type="checkbox" id="selectAllSchedules" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 mr-2" onchange="toggleSelectAll()">
                                    <span class="font-medium">Select All Schedules</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-gray-500">
                        <div class="inline-flex rounded-lg bg-white/70 p-1 ring-1 ring-gray-200" aria-label="Calendar density">
                            <button type="button" class="schedule-density-button rounded-md px-2.5 py-1 text-gray-500" data-density="detailed" aria-pressed="true">Detailed</button>
                            <button type="button" class="schedule-density-button rounded-md px-2.5 py-1 text-gray-500" data-density="compact" aria-pressed="false">Compact</button>
                        </div>
                        <span class="flex items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 mr-1.5"></div>
                            Shift
                        </span>
                        <span class="flex items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-400 mr-1.5"></div>
                            OFF
                        </span>
                        <span class="flex items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-violet-500 mr-1.5"></div>
                            OB / LV
                        </span>
                        <span class="flex items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-500 mr-1.5"></div>
                            Holiday
                        </span>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="schedule-calendar-help border-b border-gray-200 bg-white px-4 py-2 text-xs text-gray-500">
                <i class="fas fa-arrows-alt-h mr-1.5 text-gray-400"></i>
                Scroll inside the calendar to view more dates. Select a cell to see its full schedule.
            </div>
            <div id="scheduleCalendar" class="schedule-calendar schedule-calendar-scroll overflow-auto">
                <table class="schedule-calendar-table border-separate border-spacing-0">
                    <thead class="sticky top-0 z-30">
                        <tr class="border-b border-gray-200">
                            <th class="schedule-employee-column sticky left-0 top-0 z-40 bg-gray-50 px-4 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide border-r border-b border-gray-200 shadow-[4px_0_8px_-8px_rgba(15,23,42,0.45)]">
                                Employee
                            </th>
                            @foreach($calendarDays as $day)
                            @php
                                $isWeekend = $day['date']->isWeekend();
                                $isToday = $day['date']->isToday();
                            @endphp
                            <th class="calendar-day schedule-date-column sticky top-0 z-30 px-1 py-2.5 text-center border-r border-b border-gray-200 {{ $isToday ? 'bg-indigo-100 ring-1 ring-inset ring-indigo-300' : ($isWeekend ? 'bg-gray-100' : 'bg-white') }}" data-date="{{ $day['date']->format('Y-m-d') }}">
                                <div class="flex flex-col items-center leading-tight">
                                    <span class="text-[9px] font-medium {{ $isToday ? 'text-indigo-500' : 'text-gray-400' }} uppercase">{{ $day['date']->format('D') }}</span>
                                    <span class="text-[13px] font-semibold {{ $isToday ? 'text-indigo-700' : 'text-gray-700' }}">{{ $day['day'] }}</span>
                                    @if($isToday)<span class="mt-0.5 h-1 w-1 rounded-full bg-indigo-500"></span>@endif
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employees as $employee)
                        <tr class="employee-row group hover:bg-gray-50 transition-colors border-b border-gray-100" data-employee-id="{{ $employee->id }}">
                            <td class="schedule-employee-column sticky left-0 z-20 bg-white group-hover:bg-gray-50 px-4 py-3 border-r border-b border-gray-200 shadow-[4px_0_8px_-8px_rgba(15,23,42,0.45)]">
                                <div class="flex items-center min-w-0">
                                    <div class="flex-shrink-0 h-7 w-7 rounded-full bg-indigo-50 flex items-center justify-center">
                                        <span class="text-[10px] font-semibold text-indigo-600">
                                            {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="ml-2.5 min-w-0 leading-tight">
                                        <div class="text-xs font-semibold text-gray-800 whitespace-normal break-words" title="{{ $employee->full_name }}">{{ $employee->full_name }}</div>
                                        <div class="mt-1 text-[10px] text-gray-500 whitespace-normal break-words" title="{{ $employee->department->name }}">{{ $employee->department->name }}</div>
                                    </div>
                                </div>
                            </td>
                            @foreach($calendarDays as $day)
                            @php
                            $scheduleKey = $employee->id . '_' . $day['date']->format('Y-m-d');
                            $schedule = $schedules->get($scheduleKey);
                            $history = $attendanceHistory->get($scheduleKey);
                            @endphp
                            <td class="calendar-day schedule-date-column schedule-grid-cell group/cell relative text-center border-r border-b border-gray-200 {{ $day['date']->isToday() ? 'bg-indigo-50 ring-1 ring-inset ring-indigo-200' : ($day['date']->isWeekend() ? 'bg-gray-50' : 'bg-white') }} hover:bg-indigo-50 transition-colors" data-date="{{ $day['date']->format('Y-m-d') }}">
                                @if($schedule)
                                @php
                                    $isWorkDay = in_array($schedule->status, ['Working', 'Overtime']);
                                    $isApprovedOb = ($history['label'] ?? null) === 'Official Business';
                                    $isApprovedLeave = ($history['tone'] ?? null) === 'indigo'
                                        && !$isApprovedOb
                                        && str_contains((string) ($history['label'] ?? ''), 'Leave');
                                    $hasRequestConflict = ($history['label'] ?? null) === 'Leave / OB Conflict';
                                    $statusAbbr = match($schedule->status) {
                                        'Day Off' => 'OFF',
                                        'Leave' => 'LV',
                                        'Official Business' => 'OB',
                                        'Absent' => 'AB',
                                        'Regular Holiday' => 'RH',
                                        'Special Holiday' => 'SH',
                                        'Holiday' => 'HOL',
                                        default => null,
                                    };
                                    $cellLabel = match(true) {
                                        $hasRequestConflict => '!',
                                        $isApprovedOb => 'OB',
                                        $isApprovedLeave => 'LV',
                                        $schedule->status === 'Overtime' => 'OT',
                                        $schedule->isFlexible() => 'Flex ' . rtrim(rtrim(number_format((float) $schedule->required_hours, 2), '0'), '.') . 'h',
                                        $isWorkDay && $schedule->time_in && $schedule->time_out =>
                                            \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_in)->format('g')
                                            . strtolower(substr(\Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_in)->format('A'), 0, 1))
                                            . '–'
                                            . \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_out)->format('g')
                                            . strtolower(substr(\Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_out)->format('A'), 0, 1)),
                                        default => $statusAbbr,
                                    };
                                    $compactCellLabel = match(true) {
                                        $hasRequestConflict => '!',
                                        $isApprovedOb => 'OB',
                                        $isApprovedLeave => 'LV',
                                        $schedule->status === 'Day Off' => 'OFF',
                                        $schedule->status === 'Absent' => 'AB',
                                        in_array($schedule->status, ['Holiday', 'Regular Holiday', 'Special Holiday'], true) => $statusAbbr,
                                        filled($schedule->schedule_template_id) && filled($schedule->scheduleTemplate?->code) => $schedule->scheduleTemplate->code,
                                        default => $cellLabel,
                                    };
                                    $badgeColor = match(true) {
                                        $hasRequestConflict => 'bg-red-100 text-red-800 ring-1 ring-inset ring-red-300',
                                        $isApprovedOb, $isApprovedLeave => 'bg-violet-100 text-violet-800 ring-1 ring-inset ring-violet-300',
                                        $isWorkDay => 'bg-emerald-50 text-emerald-700',
                                        $schedule->status === 'Day Off' => 'bg-amber-50 text-amber-700',
                                        $schedule->status === 'Absent' => 'bg-red-100 text-red-800 ring-1 ring-inset ring-red-300',
                                        in_array($schedule->status, ['Holiday', 'Regular Holiday', 'Special Holiday'], true) => 'bg-red-100 text-red-800 ring-1 ring-inset ring-red-300',
                                        in_array($schedule->status, ['Leave', 'Official Business'], true) => 'bg-violet-100 text-violet-800 ring-1 ring-inset ring-violet-300',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                    $badgeTone = match(true) {
                                        $hasRequestConflict, $schedule->status === 'Absent' => 'absent',
                                        $isApprovedOb, $isApprovedLeave, in_array($schedule->status, ['Leave', 'Official Business'], true) => 'covered',
                                        in_array($schedule->status, ['Holiday', 'Regular Holiday', 'Special Holiday'], true) => 'holiday',
                                        $schedule->status === 'Day Off' => 'off',
                                        default => 'shift',
                                    };
                                @endphp
                                <div class="schedule-detail-trigger cursor-pointer h-full flex flex-col items-center justify-center gap-1 px-1 py-1 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                     role="button"
                                     tabindex="0"
                                     title="{{ $employee->full_name }} — {{ $day['date']->format('M j') }}: {{ $hasRequestConflict || $isApprovedOb || $isApprovedLeave ? $history['label'] : $schedule->status_label }}{{ $schedule->time_in && $schedule->time_out ? ' (' . \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_in)->format('g:i A') . '–' . \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_out)->format('g:i A') . ')' : '' }}"
                                     data-employee="{{ $employee->full_name }}"
                                     data-date="{{ $day['date']->format('l, F j, Y') }}"
                                     data-status="{{ $hasRequestConflict || $isApprovedOb || $isApprovedLeave ? $history['label'] : $schedule->status_label }}"
                                     data-template-code="{{ $schedule->scheduleTemplate->code ?? '' }}"
                                     data-template-name="{{ $schedule->scheduleTemplate->name ?? '' }}"
                                     data-time-in="{{ $schedule->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_in)->format('g:i A') : '' }}"
                                     data-time-out="{{ $schedule->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_out)->format('g:i A') : '' }}"
                                     data-required-hours="{{ $schedule->isFlexible() ? \App\Helpers\TimezoneHelper::formatHours((float) $schedule->required_hours) : '' }}"
                                     data-notes="{{ $schedule->notes ?? '' }}"
                                     data-edit-url="{{ route('schedule-v2.edit', array_merge(['schedule' => $schedule], array_filter(['department_id' => $selectedDepartment, 'month' => $selectedMonth, 'year' => $selectedYear, 'search' => $searchQuery]))) }}">
                                    <input type="checkbox"
                                        class="schedule-checkbox absolute top-0.5 left-0.5 rounded border-gray-300 text-red-600 h-3 w-3 hidden"
                                        value="{{ $schedule->id }}"
                                        onclick="event.stopPropagation()"
                                        onchange="updateBulkDeleteButton()">
                                    <span class="schedule-status-badge calendar-cell-detailed-label inline-flex max-w-full items-center rounded px-1.5 py-1 {{ $badgeColor }} text-[10px] font-bold leading-none whitespace-nowrap" data-schedule-tone="{{ $badgeTone }}">
                                        {{ $cellLabel }}
                                    </span>
                                    <span class="schedule-status-badge calendar-cell-compact-label max-w-full items-center rounded px-1.5 py-1 {{ $badgeColor }} text-[10px] font-bold leading-none whitespace-nowrap" data-schedule-tone="{{ $badgeTone }}">
                                        {{ $compactCellLabel }}
                                    </span>
                                    <span class="calendar-cell-detail max-w-full truncate text-[9px] font-medium text-gray-500" title="{{ $hasRequestConflict || $isApprovedOb || $isApprovedLeave ? $history['label'] : ($schedule->scheduleTemplate->name ?? $schedule->status_label) }}">
                                        {{ $hasRequestConflict || $isApprovedOb || $isApprovedLeave ? $history['label'] : ($schedule->scheduleTemplate->code ?? $schedule->status_label) }}
                                    </span>
                                    @if($history)
                                    <span class="absolute bottom-1 right-1 h-1.5 w-1.5 rounded-full {{ match($history['tone']) {
                                        'red' => 'bg-red-500',
                                        'amber' => 'bg-amber-500',
                                        'green' => 'bg-green-500',
                                        'indigo' => 'bg-violet-500',
                                        default => 'bg-gray-400',
                                    } }}" title="Attendance: {{ $history['label'] }}"></span>
                                    @endif
                                    <a href="{{ route('schedule-v2.edit', array_merge(['schedule' => $schedule], array_filter(['department_id' => $selectedDepartment, 'month' => $selectedMonth, 'year' => $selectedYear, 'search' => $searchQuery]))) }}"
                                       onclick="event.stopPropagation()"
                                       class="opacity-0 group-hover/cell:opacity-100 transition-opacity text-gray-400 hover:text-indigo-600 text-[10px]">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </div>
                                @else
                                <a href="{{ route('schedule-v2.create', array_merge(['employee_id' => $employee->id, 'date' => $day['date']->format('Y-m-d')], array_filter(['department_id' => $selectedDepartment, 'month' => $selectedMonth, 'year' => $selectedYear, 'search' => $searchQuery]))) }}"
                                   class="h-full w-full flex flex-col items-center justify-center gap-1 text-gray-300 hover:text-indigo-600 transition-colors"
                                   title="Create schedule">
                                    <span class="text-sm leading-none">—</span>
                                    <i class="fas fa-plus text-[9px] opacity-0 group-hover/cell:opacity-100"></i>
                                </a>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="w-full px-3 sm:px-4 lg:px-5 pb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="mx-auto w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-calendar-alt text-3xl text-blue-600"></i>
            </div>
            @if(!$selectedDepartment && !$searchQuery)
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Select a Department or Search</h3>
            <p class="text-gray-600 mb-6">Please select a department or search for an employee to view schedules.</p>
            <div class="flex justify-center space-x-3">
                <button onclick="document.getElementById('department_id').focus()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-building mr-2"></i>Select Department
                </button>
                <button onclick="document.getElementById('search').focus()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search Employee
                </button>
            </div>
            @else
            <h3 class="text-xl font-semibold text-gray-900 mb-3">No Employees Found</h3>
            <p class="text-gray-600 mb-6">
                @if($searchQuery && !$selectedDepartment)
                No employees found matching "{{ $searchQuery }}".
                @elseif($selectedDepartment && !$searchQuery)
                No employees found in the selected department.
                @else
                No employees found matching "{{ $searchQuery }}" in the selected department.
                @endif
            </p>
            <button onclick="document.querySelector('form').submit()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-refresh mr-2"></i>Clear Filters
            </button>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Schedule Detail Modal -->
<div id="scheduleDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="detailEmployeeName"></h3>
            <button onclick="closeScheduleDetailModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Date</span><span id="detailDate" class="font-medium text-gray-900"></span></div>
            <div class="flex justify-between"><span class="text-gray-500">Status</span><span id="detailStatus" class="font-medium text-gray-900"></span></div>
            <div id="detailTemplateRow" class="flex justify-between hidden"><span class="text-gray-500">Template</span><span id="detailTemplate" class="font-medium text-gray-900"></span></div>
            <div id="detailTimeRow" class="flex justify-between hidden"><span class="text-gray-500">Time</span><span id="detailTime" class="font-medium text-gray-900"></span></div>
            <div id="detailHoursRow" class="flex justify-between hidden"><span class="text-gray-500">Required Hours</span><span id="detailHours" class="font-medium text-gray-900"></span></div>
            <div id="detailNotesRow" class="hidden"><span class="text-gray-500 block mb-1">Notes</span>
                <p id="detailNotes" class="text-gray-800"></p>
            </div>
        </div>
        <div class="flex justify-end pt-5 mt-2 border-t border-gray-200">
            <a id="detailEditLink" href="#" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>Edit Schedule
            </a>
        </div>
    </div>
</div>

<script>
    function setCalendarDensity(density) {
        const calendar = document.getElementById('scheduleCalendar');
        if (!calendar) return;

        const compact = density === 'compact';
        calendar.classList.toggle('is-compact', compact);
        document.querySelectorAll('.schedule-density-button').forEach((button) => {
            button.setAttribute('aria-pressed', String(button.dataset.density === density));
        });
        localStorage.setItem('scheduleCalendarDensity', density);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const savedDensity = localStorage.getItem('scheduleCalendarDensity') || 'detailed';
        setCalendarDensity(savedDensity);
        document.querySelectorAll('.schedule-density-button').forEach((button) => {
            button.addEventListener('click', () => setCalendarDensity(button.dataset.density));
        });

        const calendar = document.getElementById('scheduleCalendar');
        const today = calendar?.querySelector('thead [data-date="{{ now()->format('Y-m-d') }}"]');
        if (calendar && today) {
            calendar.scrollLeft = Math.max(0, today.offsetLeft - 280);
        }
    });

    document.addEventListener('click', function(e) {
        // Ignore clicks in date-select mode - that mode has its own click handling
        if (dateSelectMode) {
            return;
        }
        const trigger = e.target.closest('.schedule-detail-trigger');
        if (!trigger) {
            return;
        }
        // Don't open the modal if the click landed on the edit pencil or a checkbox
        if (e.target.closest('a') || e.target.closest('input')) {
            return;
        }

        document.getElementById('detailEmployeeName').textContent = trigger.dataset.employee;
        document.getElementById('detailDate').textContent = trigger.dataset.date;
        document.getElementById('detailStatus').textContent = trigger.dataset.status;
        document.getElementById('detailEditLink').href = trigger.dataset.editUrl;

        const templateRow = document.getElementById('detailTemplateRow');
        if (trigger.dataset.templateCode) {
            document.getElementById('detailTemplate').textContent = `${trigger.dataset.templateCode} — ${trigger.dataset.templateName}`;
            templateRow.classList.remove('hidden');
        } else {
            templateRow.classList.add('hidden');
        }

        const timeRow = document.getElementById('detailTimeRow');
        if (trigger.dataset.timeIn && trigger.dataset.timeOut) {
            document.getElementById('detailTime').textContent = `${trigger.dataset.timeIn} - ${trigger.dataset.timeOut}`;
            timeRow.classList.remove('hidden');
        } else {
            timeRow.classList.add('hidden');
        }

        const hoursRow = document.getElementById('detailHoursRow');
        if (trigger.dataset.requiredHours) {
            document.getElementById('detailHours').textContent = trigger.dataset.requiredHours;
            hoursRow.classList.remove('hidden');
        } else {
            hoursRow.classList.add('hidden');
        }

        const notesRow = document.getElementById('detailNotesRow');
        if (trigger.dataset.notes) {
            document.getElementById('detailNotes').textContent = trigger.dataset.notes;
            notesRow.classList.remove('hidden');
        } else {
            notesRow.classList.add('hidden');
        }

        document.getElementById('scheduleDetailModal').classList.remove('hidden');
    });

    document.addEventListener('keydown', function(e) {
        if (!['Enter', ' '].includes(e.key)) return;
        const trigger = e.target.closest('.schedule-detail-trigger');
        if (!trigger) return;
        e.preventDefault();
        trigger.click();
    });

    function closeScheduleDetailModal() {
        document.getElementById('scheduleDetailModal').classList.add('hidden');
    }
</script>

<!-- Bulk Create Modal -->
<div id="bulkModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-gray-900/50 p-4 overflow-y-auto">
    <div class="relative w-full max-w-6xl my-8 max-h-[calc(100vh-4rem)] overflow-y-auto rounded-lg bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Bulk Create Schedules</h3>
                <p class="text-sm text-gray-600 mt-1">Create schedules for multiple employees at once</p>
            </div>
            <button onclick="closeBulkModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('schedule-v2.bulk-create') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Hidden inputs to preserve filter state -->
            <input type="hidden" name="month" value="{{ $selectedMonth }}">
            <input type="hidden" name="year" value="{{ $selectedYear }}">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Employee Selection Panel -->
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-users mr-2 text-blue-600"></i>
                            Employee Selection
                        </h4>

                        <div class="space-y-4">
                            <div>
                                <label for="bulk_department_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-building mr-1"></i>Department
                                </label>
                                <select name="department_id" id="bulk_department_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Department</option>
                                    <option value="all">All Departments</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="employeeSearch" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-search mr-1"></i>Search Employees
                                </label>
                                <input type="text" id="employeeSearch" placeholder="Search by name..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="flex items-center space-x-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <input type="checkbox" id="selectAllEmployees" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="selectAllEmployees" class="text-sm font-medium text-blue-800">
                                    <i class="fas fa-check-double mr-1"></i>Select All Employees
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-list mr-1"></i>Selected Employees
                                </label>
                                <div id="employeeList" class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-3 bg-white">
                                    <p class="text-sm text-gray-500 text-center py-4">
                                        <i class="fas fa-info-circle mr-1"></i>Select a department first
                                    </p>
                                </div>
                                <div id="selectedCount" class="text-xs text-gray-600 mt-2 hidden">
                                    <i class="fas fa-check-circle mr-1"></i><span id="countText">0 employees selected</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Details Panel -->
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-green-600"></i>
                            Schedule Details
                        </h4>

                        <div class="space-y-4">
                            <div>
                                <label for="bulk_schedule_type" class="block text-sm font-medium text-gray-700 mb-2">Schedule Type</label>
                                <select name="schedule_type" id="bulk_schedule_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="fixed">Fixed hours</option>
                                    <option value="flexible">Flexible hours</option>
                                </select>
                            </div>

                            <div>
                                <label for="bulk_schedule_template_id" class="block text-sm font-medium text-gray-700 mb-2">Schedule Template</label>
                                <select name="schedule_template_id" id="bulk_schedule_template_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="">No template — set manually</option>
                                    @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->code }} — {{ $template->name }} ({{ $template->window_label }})</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Picking a template fills in the fields below — still editable after.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-calendar-day mr-1"></i>Start Date
                                    </label>
                                    <input type="date" name="start_date" id="start_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-calendar-day mr-1"></i>End Date
                                    </label>
                                    <input type="date" name="end_date" id="end_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div id="bulk_time_fields" class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="bulk_time_in" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-clock mr-1"></i>Time In
                                    </label>
                                    <input type="time" name="time_in" id="bulk_time_in" value="08:00" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="bulk_time_out" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-clock mr-1"></i>Time Out
                                    </label>
                                    <input type="time" name="time_out" id="bulk_time_out" value="17:00" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div id="bulk_required_hours_field" class="hidden">
                                <label for="bulk_required_hours" class="block text-sm font-medium text-gray-700 mb-2">Required Hours</label>
                                <input type="number" name="required_hours" id="bulk_required_hours" min="1" max="24" step="0.25" value="8" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>

                            <div>
                                <label for="bulk_status" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tasks mr-1"></i>Status
                                </label>
                                <select name="status" id="bulk_status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="Working">Scheduled Workday</option>
                                    <option value="Day Off">Day Off</option>
                                    <option value="Leave">Leave</option>
                                    <option value="Official Business">Official Business</option>
                                    <option value="Regular Holiday">Regular Holiday</option>
                                    <option value="Special Holiday">Special Holiday</option>
                                    <option value="Overtime">Overtime</option>
                                </select>
                            </div>

                            <div>
                                <label for="bulk_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-sticky-note mr-1"></i>Notes (Optional)
                                </label>
                                <textarea name="notes" id="bulk_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add any notes about these schedules..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeBulkModal()" class="px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create Schedules
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // AJAX version to prevent form conflicts
    function openBulkModalAjax(event) {
        // Prevent any form submission
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // Open the modal - toggle both classes since Tailwind's `hidden`
        // and `flex` both set `display`, so only one can apply at a time
        const modal = document.getElementById('bulkModal');
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeBulkModal() {
        const modal = document.getElementById('bulkModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Date range validation for bulk create
    function validateDateRange() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const endDateField = document.getElementById('end_date');

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);

            // Clear previous error styling
            endDateField.classList.remove('border-red-500');

            // Check if end date is before start date
            if (end < start) {
                endDateField.classList.add('border-red-500');
                endDateField.setCustomValidity('The end date cannot be earlier than the start date.');
                return false;
            }

            // Check if dates are in different months
            if (start.getMonth() !== end.getMonth() || start.getFullYear() !== end.getFullYear()) {
                endDateField.classList.add('border-red-500');
                endDateField.setCustomValidity('Please choose an end date within the same month as the selected start date.');
                return false;
            }

            // Clear any custom validity if validation passes
            endDateField.setCustomValidity('');
        }

        return true;
    }

    // Add event listeners for date validation
    document.addEventListener('DOMContentLoaded', function() {
        const startDateField = document.getElementById('start_date');
        const endDateField = document.getElementById('end_date');

        if (startDateField && endDateField) {
            startDateField.addEventListener('change', validateDateRange);
            endDateField.addEventListener('change', validateDateRange);

            // Also validate on form submission
            const bulkForm = document.querySelector('form[action*="bulk-create"]');
            if (bulkForm) {
                bulkForm.addEventListener('submit', function(e) {
                    if (!validateDateRange()) {
                        e.preventDefault();
                    }
                });
            }
        }
    });

    // Handle main filter form submission
    document.getElementById('department_id').addEventListener('change', function() {
        // Auto-submit the filter form when department changes
        this.form.submit();
    });
    document.getElementById('month').addEventListener('change', function() {
        // Auto-submit the filter form when month changes
        this.form.submit();
    });
    document.getElementById('year').addEventListener('change', function() {
        // Auto-submit the filter form when year changes
        this.form.submit();
    });

    // Global variables for bulk modal
    let allEmployees = @json($allEmployees);
    let filteredEmployees = [];
    let selectedEmployees = new Set();

    // Schedule template data, keyed by id, for auto-filling bulk create fields on selection
</script>
@php
    $scheduleTemplatesJson = $templates->mapWithKeys(function ($t) {
        return [
            $t->id => [
                'schedule_type' => $t->schedule_type,
                'time_in' => $t->time_in ? \Carbon\Carbon::parse($t->time_in)->format('H:i') : '',
                'time_out' => $t->time_out ? \Carbon\Carbon::parse($t->time_out)->format('H:i') : '',
                'required_hours' => (float) $t->required_hours,
            ],
        ];
    })->toJson();
@endphp
<script>
    const bulkScheduleTemplates = {!! $scheduleTemplatesJson !!};

    document.getElementById('bulk_schedule_template_id').addEventListener('change', function() {
        const template = bulkScheduleTemplates[this.value];
        if (!template) {
            return;
        }
        document.getElementById('bulk_schedule_type').value = template.schedule_type;
        document.getElementById('bulk_time_in').value = template.time_in;
        document.getElementById('bulk_time_out').value = template.time_out;
        document.getElementById('bulk_required_hours').value = template.required_hours;
        document.getElementById('bulk_schedule_type').dispatchEvent(new Event('change'));
    });

    // Load employees when department is selected in bulk modal
    document.getElementById('bulk_department_id').addEventListener('change', function() {
        const departmentId = this.value;
        const employeeList = document.getElementById('employeeList');
        const employeeSearch = document.getElementById('employeeSearch');
        const selectAllCheckbox = document.getElementById('selectAllEmployees');

        if (departmentId) {
            filteredEmployees = departmentId === 'all'
                ? [...allEmployees]
                : allEmployees.filter(emp => emp.department_id === departmentId);

            // Clear search and reset selections
            employeeSearch.value = '';
            selectedEmployees.clear();
            if (departmentId === 'all') {
                filteredEmployees.forEach(emp => selectedEmployees.add(emp.id));
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.checked = false;
            }

            // Render employee list
            renderEmployeeList();
        } else {
            employeeList.innerHTML = '<p class="text-sm text-gray-500 text-center py-4"><i class="fas fa-info-circle mr-1"></i>Select a department first</p>';
            filteredEmployees = [];
            selectedEmployees.clear();
            updateSelectedCount();
        }
    });

    // Search functionality
    document.getElementById('employeeSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const departmentId = document.getElementById('bulk_department_id').value;

        if (departmentId) {
            const departmentEmployees = departmentId === 'all'
                ? allEmployees
                : allEmployees.filter(emp => emp.department_id === departmentId);
            filteredEmployees = departmentEmployees.filter(emp =>
                emp.first_name.toLowerCase().includes(searchTerm) ||
                emp.last_name.toLowerCase().includes(searchTerm) ||
                `${emp.first_name} ${emp.last_name}`.toLowerCase().includes(searchTerm)
            );

            renderEmployeeList();
        }
    });

    // Select All functionality
    document.getElementById('selectAllEmployees').addEventListener('change', function() {
        const isChecked = this.checked;

        if (isChecked) {
            // Select all filtered employees
            filteredEmployees.forEach(emp => {
                selectedEmployees.add(emp.id);
            });
        } else {
            // Deselect all filtered employees
            filteredEmployees.forEach(emp => {
                selectedEmployees.delete(emp.id);
            });
        }

        renderEmployeeList();
        updateSelectedCount();
    });

    // Render employee list
    function renderEmployeeList() {
        const employeeList = document.getElementById('employeeList');
        const selectAllCheckbox = document.getElementById('selectAllEmployees');

        employeeList.innerHTML = '';

        if (filteredEmployees.length === 0) {
            employeeList.innerHTML = '<p class="text-sm text-gray-500 text-center py-4"><i class="fas fa-search mr-1"></i>No employees found</p>';
            selectAllCheckbox.checked = false;
            return;
        }

        // Check if all filtered employees are selected
        const allSelected = filteredEmployees.every(emp => selectedEmployees.has(emp.id));
        selectAllCheckbox.checked = allSelected;

        filteredEmployees.forEach(employee => {
            const isSelected = selectedEmployees.has(employee.id);
            const checkbox = document.createElement('div');
            checkbox.className = 'flex items-center space-x-3 py-2 px-3 hover:bg-gray-50 rounded-lg transition-colors';
            checkbox.innerHTML = `
            <input type="checkbox" name="employee_ids[]" value="${employee.id}" id="emp_${employee.id}" 
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" ${isSelected ? 'checked' : ''}>
            <label for="emp_${employee.id}" class="text-sm text-gray-700 cursor-pointer flex-1">
                <div class="font-medium">${employee.first_name} ${employee.last_name}</div>
                <div class="text-xs text-gray-500">${employee.position?.name ?? 'N/A'}</div>
            </label>
        `;

            // Add click handler for individual checkboxes
            const checkboxInput = checkbox.querySelector('input[type="checkbox"]');
            checkboxInput.addEventListener('change', function() {
                if (this.checked) {
                    selectedEmployees.add(employee.id);
                } else {
                    selectedEmployees.delete(employee.id);
                }
                updateSelectedCount();

                // Update select all checkbox
                const allSelected = filteredEmployees.every(emp => selectedEmployees.has(emp.id));
                selectAllCheckbox.checked = allSelected;
            });

            employeeList.appendChild(checkbox);
        });
    }

    // Update selected count
    function updateSelectedCount() {
        const countElement = document.getElementById('selectedCount');
        const countText = document.getElementById('countText');

        if (selectedEmployees.size > 0) {
            countElement.classList.remove('hidden');
            countText.textContent = `${selectedEmployees.size} employee${selectedEmployees.size !== 1 ? 's' : ''} selected`;
        } else {
            countElement.classList.add('hidden');
        }
    }

    // Delete Mode Functions
    function toggleDeleteMode() {
        const deleteModeBtn = document.getElementById('deleteModeBtn');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const selectAllContainer = document.getElementById('selectAllContainer');
        const checkboxes = document.querySelectorAll('.schedule-checkbox');

        // Hide delete mode button and show action buttons
        deleteModeBtn.classList.add('hidden');
        bulkDeleteBtn.classList.remove('hidden');
        cancelDeleteBtn.classList.remove('hidden');
        selectAllContainer.classList.remove('hidden');

        // Show all checkboxes
        checkboxes.forEach(checkbox => {
            checkbox.classList.remove('hidden');
        });

        // Add visual indicator that we're in delete mode
        document.body.classList.add('delete-mode');
    }

    function exitDeleteMode() {
        const deleteModeBtn = document.getElementById('deleteModeBtn');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const selectAllContainer = document.getElementById('selectAllContainer');
        const checkboxes = document.querySelectorAll('.schedule-checkbox');
        const selectAllCheckbox = document.getElementById('selectAllSchedules');

        // Show delete mode button and hide action buttons
        deleteModeBtn.classList.remove('hidden');
        bulkDeleteBtn.classList.add('hidden');
        cancelDeleteBtn.classList.add('hidden');
        selectAllContainer.classList.add('hidden');

        // Hide all checkboxes and uncheck them
        checkboxes.forEach(checkbox => {
            checkbox.classList.add('hidden');
            checkbox.checked = false;
        });

        // Uncheck select all
        selectAllCheckbox.checked = false;

        // Remove visual indicator
        document.body.classList.remove('delete-mode');
    }

    // Bulk Delete Functions
    function updateBulkDeleteButton() {
        const checkboxes = document.querySelectorAll('.schedule-checkbox:checked');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteText = document.getElementById('bulkDeleteText');

        if (checkboxes.length > 0) {
            bulkDeleteBtn.classList.remove('hidden');
            bulkDeleteText.textContent = `Delete Selected (${checkboxes.length})`;
        } else {
            bulkDeleteBtn.classList.add('hidden');
        }
    }

    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAllSchedules');
        const checkboxes = document.querySelectorAll('.schedule-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });

        updateBulkDeleteButton();
    }

    function openBulkDeleteModal() {
        const checkboxes = document.querySelectorAll('.schedule-checkbox:checked');
        const count = checkboxes.length;

        if (count === 0) {
            alert('Please select at least one schedule to delete.');
            return;
        }

        const modal = document.getElementById('bulkDeleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('deleteCount').textContent = count;
    }

    function closeBulkDeleteModal() {
        const modal = document.getElementById('bulkDeleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    function confirmBulkDelete() {
        const checkboxes = document.querySelectorAll('.schedule-checkbox:checked');
        const scheduleIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        // Show loading state
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';
        confirmBtn.disabled = true;

        // Send AJAX request
        const requestData = {
            schedule_ids: scheduleIds
        };

        fetch('{{ route("schedule-v2.bulk-delete") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    showNotification(data.message, 'success');

                    // Exit delete mode
                    exitDeleteMode();

                    // Reload the page to reflect changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while deleting schedules', 'error');
            })
            .finally(() => {
                // Reset button state
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
                closeBulkDeleteModal();
            });
    }

    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
        notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;

        document.body.appendChild(notification);

        // Remove notification after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    // Date Select Mode Functions
    let dateSelectMode = false;
    let selectedDatesPerEmployee = new Map(); // Changed to track per employee

    function toggleDateSelectMode() {
        dateSelectMode = !dateSelectMode;

        if (dateSelectMode) {
            // Enter date select mode
            document.getElementById('dateSelectModeBtn').classList.add('hidden');
            document.getElementById('doneDateSelectBtn').classList.remove('hidden');
            document.getElementById('cancelDateSelectBtn').classList.remove('hidden');

            // Initialize selected dates per employee
            const employeeRows = document.querySelectorAll('.employee-row');
            employeeRows.forEach(row => {
                const employeeId = row.getAttribute('data-employee-id');
                if (employeeId && !selectedDatesPerEmployee.has(employeeId)) {
                    selectedDatesPerEmployee.set(employeeId, new Set());
                }
            });

            updateSelectedDatesCount();

            // Add visual indicator to calendar days
            const calendarDays = document.querySelectorAll('.calendar-day');
            calendarDays.forEach(day => {
                day.classList.add('date-select-mode');
                day.style.cursor = 'pointer';
                day.style.border = '2px solid #8b5cf6';
                day.style.borderRadius = '8px';
            });

            // Show instruction
            showDateSelectInstruction();
        } else {
            exitDateSelectMode();
        }
    }

    function exitDateSelectMode() {
        dateSelectMode = false;
        selectedDatesPerEmployee.clear();

        // Hide all buttons, show select button
        document.getElementById('dateSelectModeBtn').classList.remove('hidden');
        document.getElementById('doneDateSelectBtn').classList.add('hidden');
        document.getElementById('cancelDateSelectBtn').classList.add('hidden');

        // Remove visual indicators
        const calendarDays = document.querySelectorAll('.calendar-day');
        calendarDays.forEach(day => {
            day.classList.remove('date-select-mode', 'date-selected');
            day.style.cursor = 'default';
            day.style.border = '';
            day.style.borderRadius = '';
            day.style.backgroundColor = '';
        });

        // Hide instruction
        hideDateSelectInstruction();
    }

    function updateSelectedDatesCount() {
        // Count total selected dates across all employees
        let totalCount = 0;
        selectedDatesPerEmployee.forEach(dates => {
            totalCount += dates.size;
        });

        document.getElementById('selectedDatesCount').textContent = totalCount;

        // Enable/disable Done button based on selection
        const doneBtn = document.getElementById('doneDateSelectBtn');
        if (totalCount > 0) {
            doneBtn.disabled = false;
            doneBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            doneBtn.disabled = true;
            doneBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function showDateSelectInstruction() {
        // Create instruction banner
        const instruction = document.createElement('div');
        instruction.id = 'dateSelectInstruction';
        instruction.className = 'bg-purple-100 border border-purple-300 rounded-lg p-3 mb-4';
        instruction.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-info-circle text-purple-600 mr-2"></i>
            <span class="text-purple-800 font-medium">Date Select Mode Active</span>
        </div>
        <p class="text-purple-700 text-sm mt-1">Click on dates in each employee's row to select them individually. Each employee can have different selected dates. Click "Edit All" when finished to set schedules for all selected dates.</p>
    `;

        // Insert before the schedule grid
        const scheduleGrid = document.querySelector('.schedule-grid');
        if (scheduleGrid) {
            scheduleGrid.parentNode.insertBefore(instruction, scheduleGrid);
        }
    }

    function hideDateSelectInstruction() {
        const instruction = document.getElementById('dateSelectInstruction');
        if (instruction) {
            instruction.remove();
        }
    }

    // Add click handler for calendar days in date select mode
    document.addEventListener('click', function(e) {
        if (dateSelectMode && e.target.closest('.calendar-day')) {
            const dayElement = e.target.closest('.calendar-day');
            const dateStr = dayElement.getAttribute('data-date');

            if (dateStr) {
                // Find the employee row this date belongs to
                const employeeRow = dayElement.closest('tr');
                const employeeId = employeeRow ? employeeRow.getAttribute('data-employee-id') : null;

                if (employeeId) {
                    // Prevent default behavior of links/buttons inside the cell
                    e.preventDefault();
                    e.stopPropagation();

                    // Toggle date selection for this specific employee
                    toggleDateSelectionForEmployee(dateStr, dayElement, employeeId);
                }
            }
        }
    });

    function toggleDateSelectionForEmployee(dateStr, dayElement, employeeId) {
        // Ensure employee has a date set
        if (!selectedDatesPerEmployee.has(employeeId)) {
            selectedDatesPerEmployee.set(employeeId, new Set());
        }

        const employeeDates = selectedDatesPerEmployee.get(employeeId);

        if (employeeDates.has(dateStr)) {
            // Deselect date for this employee
            employeeDates.delete(dateStr);
            dayElement.classList.remove('date-selected');
            dayElement.style.backgroundColor = '';
            dayElement.style.border = '';
        } else {
            // Select date for this employee
            employeeDates.add(dateStr);
            dayElement.classList.add('date-selected');
            dayElement.style.backgroundColor = '#e0e7ff'; // Light purple background
            dayElement.style.border = '2px solid #8b5cf6'; // Purple border
        }

        updateSelectedDatesCount();
    }

    // Date Review Modal Functions
    function showDateReviewModal() {
        // Check if any employee has selected dates
        let hasAnySelections = false;
        selectedDatesPerEmployee.forEach(dates => {
            if (dates.size > 0) {
                hasAnySelections = true;
            }
        });

        if (!hasAnySelections) {
            alert('Please select at least one date');
            return;
        }

        // Create modal if it doesn't exist
        let modal = document.getElementById('dateReviewModal');
        if (!modal) {
            modal = createDateReviewModal();
            document.body.appendChild(modal);
        }

        // Collect all unique dates from all employees
        const allDates = new Set();
        selectedDatesPerEmployee.forEach(dates => {
            dates.forEach(date => allDates.add(date));
        });


        const datesArray = Array.from(allDates).sort();
        document.getElementById('selectedDateDisplay').textContent = formatMultipleDates(datesArray);
        document.getElementById('selectedDateValue').value = datesArray.join(',');

        // Populate employee list in left panel
        populateSelectedEmployeesList();

        syncSelectedDateScheduleFields();

        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function createDateReviewModal() {
        const modal = document.createElement('div');
        modal.id = 'dateReviewModal';
        modal.className = 'fixed inset-0 z-[9999] hidden items-center justify-center bg-gray-900/50 p-4 overflow-y-auto';
        modal.innerHTML = `
        <div class="relative w-full max-w-6xl my-8 max-h-[calc(100vh-4rem)] overflow-y-auto rounded-lg bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-check mr-3 text-purple-600"></i>
                        Review Selected Dates
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Set the status for all selected dates</p>
                </div>
                <button onclick="closeDateReviewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Panel - Selected Dates & Employees -->
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>
                            Selected Dates & Employees
                        </h4>
                        
                <div class="space-y-4">
                    <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-day mr-1"></i>Selected Dates
                                </label>
                                <div class="px-3 py-2 bg-white border border-gray-300 rounded-lg">
                            <span id="selectedDateDisplay" class="text-gray-900 font-medium"></span>
                        </div>
                        <input type="hidden" id="selectedDateValue" value="">
                    </div>
                    
                    <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-users mr-1"></i>Employees with Selected Dates
                                </label>
                                <div id="selectedEmployeesList" class="max-h-40 overflow-y-auto border border-gray-300 rounded-lg p-3 bg-white">
                                    <!-- Employee list will be populated here -->
                                </div>
                            </div>
                            
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-purple-500 mt-0.5 mr-2"></i>
                                    <div class="text-sm text-purple-700">
                                        <p class="font-medium">Selection Summary:</p>
                                        <ul class="mt-1 space-y-1 text-xs">
                                            <li>• Each employee can have different date selections</li>
                                            <li>• Only employees with selected dates will be processed</li>
                                            <li>• All selected dates will be applied to their respective employees</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Panel - Schedule Details -->
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-cog mr-2 text-green-600"></i>
                            Schedule Details
                        </h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="statusSelect" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tasks mr-1"></i>Status
                                </label>
                        <select id="statusSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="Working">Scheduled Workday</option>
                            <option value="Day Off">Day Off</option>
                            <option value="Leave">Leave</option>
                            <option value="Official Business">Official Business</option>
                            <option value="Absent">Absent</option>
                            <option value="Regular Holiday">Regular Holiday</option>
                            <option value="Special Holiday">Special Holiday</option>
                            <option value="Overtime">Overtime</option>
                        </select>
                    </div>

                    <div>
                        <label for="scheduleTypeSelect" class="block text-sm font-medium text-gray-700 mb-2">Schedule Type</label>
                        <select id="scheduleTypeSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="fixed">Fixed hours</option>
                            <option value="flexible">Flexible hours</option>
                        </select>
                    </div>

                    <div>
                        <label for="selectedDateTemplateId" class="block text-sm font-medium text-gray-700 mb-2">Schedule Template</label>
                        <select id="selectedDateTemplateId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Custom schedule (no template)</option>
                            @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->code }} — {{ $template->name }} ({{ $template->window_label }})</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Selecting a template fills the schedule fields for every selected date.</p>
                    </div>
                    
                            <div id="selectedDateTimeFields" class="grid grid-cols-2 gap-4">
                    <div>
                                    <label for="timeIn" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-clock mr-1"></i>Time In (Optional)
                                    </label>
                        <input type="time" id="timeIn" value="08:00" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                                    <label for="timeOut" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-clock mr-1"></i>Time Out (Optional)
                                    </label>
                        <input type="time" id="timeOut" value="17:00" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                </div>
                    </div>

                    <div id="selectedDateRequiredHoursField" class="hidden">
                        <label for="selectedDateRequiredHours" class="block text-sm font-medium text-gray-700 mb-2">Required Hours</label>
                        <input type="number" id="selectedDateRequiredHours" min="1" max="24" step="0.25" value="8" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    
                    <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-sticky-note mr-1"></i>Notes (Optional)
                                </label>
                        <textarea id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Add any notes..."></textarea>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-start">
                                    <i class="fas fa-lightbulb text-blue-500 mt-0.5 mr-2"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium">What this does:</p>
                                <ul class="mt-1 space-y-1 text-xs">
                                    <li>• Creates schedule entries for all selected dates</li>
                                            <li>• Applies only to employees who have selected dates</li>
                                    <li>• Can be customized with time and notes</li>
                                </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                <button onclick="closeDateReviewModal()" class="px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
                <button onclick="saveDateSchedule()" class="px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>Save Schedule
                </button>
            </div>
        </div>
    `;
        return modal;
    }

    function populateSelectedEmployeesList() {
        const employeeListContainer = document.getElementById('selectedEmployeesList');
        if (!employeeListContainer) return;

        employeeListContainer.innerHTML = '';

        // Get all employees from the current view
        const allEmployees = @json($allEmployees);

        selectedDatesPerEmployee.forEach((dates, employeeId) => {
            if (dates.size > 0) {
                const employee = allEmployees.find(emp => emp.id === employeeId);
                if (employee) {
                    const employeeItem = document.createElement('div');
                    employeeItem.className = 'flex items-center justify-between py-2 px-3 bg-purple-50 border border-purple-200 rounded-lg mb-2';

                    const datesArray = Array.from(dates).sort();
                    const datesText = datesArray.length === 1 ?
                        datesArray[0] :
                        `${datesArray.length} dates`;

                    employeeItem.innerHTML = `
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center shadow-sm mr-3">
                            <span class="text-xs font-bold text-purple-700">
                                ${employee.first_name.charAt(0)}${employee.last_name.charAt(0)}
                            </span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">${employee.first_name} ${employee.last_name}</div>
                            <div class="text-xs text-gray-500">${employee.position?.name ?? 'N/A'}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-medium text-purple-600">${datesText}</div>
                        <div class="text-xs text-gray-500">selected</div>
                    </div>
                `;

                    employeeListContainer.appendChild(employeeItem);
                }
            }
        });

        if (employeeListContainer.children.length === 0) {
            employeeListContainer.innerHTML = '<p class="text-sm text-gray-500 text-center py-4"><i class="fas fa-info-circle mr-1"></i>No employees with selected dates</p>';
        }
    }

    function closeDateReviewModal() {
        const modal = document.getElementById('dateReviewModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Clear the selected dates and exit date select mode when modal is closed
        exitDateSelectMode();
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function formatMultipleDates(datesArray) {
        if (datesArray.length === 1) {
            return formatDate(datesArray[0]);
        } else if (datesArray.length <= 3) {
            return datesArray.map(date => formatDate(date)).join(', ');
        } else {
            return `${datesArray.length} dates selected`;
        }
    }

    // Add event listener for status change in the modal
    document.addEventListener('DOMContentLoaded', function() {
        const bulkType = document.getElementById('bulk_schedule_type');
        const bulkStatus = document.getElementById('bulk_status');
        const syncBulkFields = function() {
            const isWork = bulkStatus && (bulkStatus.value === 'Working' || bulkStatus.value === 'Overtime');
            const isFlexible = bulkType && bulkType.value === 'flexible';
            document.getElementById('bulk_time_fields')?.classList.toggle('hidden', !isWork || isFlexible);
            document.getElementById('bulk_required_hours_field')?.classList.toggle('hidden', !isWork || !isFlexible);
            if (document.getElementById('bulk_time_in')) document.getElementById('bulk_time_in').required = isWork && !isFlexible;
            if (document.getElementById('bulk_time_out')) document.getElementById('bulk_time_out').required = isWork && !isFlexible;
            if (document.getElementById('bulk_required_hours')) document.getElementById('bulk_required_hours').required = isWork && isFlexible;
        };
        bulkType?.addEventListener('change', syncBulkFields);
        bulkStatus?.addEventListener('change', syncBulkFields);
        syncBulkFields();

        // Add event listener for status change in the modal
        document.addEventListener('change', function(e) {
            if (e.target && (e.target.id === 'statusSelect' || e.target.id === 'scheduleTypeSelect')) {
                syncSelectedDateScheduleFields();
            }
            if (e.target?.id === 'selectedDateTemplateId') {
                const template = bulkScheduleTemplates[e.target.value];
                if (template) {
                    document.getElementById('scheduleTypeSelect').value = template.schedule_type;
                    document.getElementById('timeIn').value = template.time_in;
                    document.getElementById('timeOut').value = template.time_out;
                    document.getElementById('selectedDateRequiredHours').value = template.required_hours;
                    syncSelectedDateScheduleFields();
                }
            }
        });
    });

    function syncSelectedDateScheduleFields() {
        const status = document.getElementById('statusSelect')?.value;
        const scheduleType = document.getElementById('scheduleTypeSelect')?.value || 'fixed';
        const isWork = status === 'Working' || status === 'Overtime';
        const isFlexible = scheduleType === 'flexible';
        document.getElementById('selectedDateTimeFields')?.classList.toggle('hidden', !isWork || isFlexible);
        document.getElementById('selectedDateRequiredHoursField')?.classList.toggle('hidden', !isWork || !isFlexible);
    }

    function saveDateSchedule() {
        const status = document.getElementById('statusSelect').value;
        const scheduleType = document.getElementById('scheduleTypeSelect').value;
        const scheduleTemplateId = document.getElementById('selectedDateTemplateId').value;
        const timeIn = document.getElementById('timeIn').value;
        const timeOut = document.getElementById('timeOut').value;
        const requiredHours = document.getElementById('selectedDateRequiredHours').value;
        const notes = document.getElementById('notes').value;

        // Prepare employee-specific data
        const employeeSchedules = [];
        selectedDatesPerEmployee.forEach((dates, employeeId) => {
            if (dates.size > 0) {
                employeeSchedules.push({
                    employee_id: employeeId,
                    dates: Array.from(dates)
                });
            }
        });

        if (employeeSchedules.length === 0) {
            alert('No employees with selected dates found');
            return;
        }

        // Show loading state
        const saveBtn = event.target;
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        saveBtn.disabled = true;

        // Prepare data for employee-specific schedules
        const requestData = {
            employee_schedules: employeeSchedules,
            status: status,
            schedule_template_id: scheduleTemplateId || null,
            schedule_type: scheduleType,
            required_hours: scheduleType === 'flexible' ? requiredHours : null,
            time_in: timeIn || null,
            time_out: timeOut || null,
            notes: notes || null
        };

        // Debug: Log the request data
        console.log('Sending request data:', requestData);

        // Send AJAX request
        fetch('{{ route("schedule-v2.bulk-create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Error response:', text);
                        throw new Error(`HTTP error! status: ${response.status} - ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    const totalSchedules = employeeSchedules.reduce((total, emp) => total + emp.dates.length, 0);
                    showNotification(`Schedule created successfully for ${totalSchedules} schedule(s)!`, 'success');
                    // Clear the selected dates and exit date select mode
                    exitDateSelectMode();
                    closeDateReviewModal();
                    // Refresh the page to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Failed to create schedule');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error creating schedule: ' + error.message, 'error');
            })
            .finally(() => {
                // Restore button state
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            });
    }
</script>

<!-- Bulk Delete Confirmation Modal -->
<div id="bulkDeleteModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-gray-900/50 p-4 overflow-y-auto">
    <div class="relative w-full max-w-md my-8 max-h-[calc(100vh-4rem)] overflow-y-auto rounded-lg bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>
                    Confirm Bulk Delete
                </h3>
            </div>
            <button onclick="closeBulkDeleteModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="mb-6">
            <p class="text-gray-700">
                Are you sure you want to delete <span id="deleteCount" class="font-semibold text-red-600">0</span> schedule(s)?
            </p>
            <p class="text-sm text-gray-500 mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                This action cannot be undone. Schedules older than 7 days cannot be deleted.
            </p>
        </div>

        <div class="flex justify-end space-x-3">
            <button onclick="closeBulkDeleteModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button id="confirmDeleteBtn" onclick="confirmBulkDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>
                Delete Schedules
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const suggestionsBox = document.getElementById('employeeSuggestions');

        // allEmployees is already declared earlier on this page (used by other
        // features), so we just reuse it here instead of loading the data again.
        // Each employee object already has full_name, department_id, etc.
        function getMatches(typedText) {
            const search = typedText.trim().toLowerCase();
            if (search.length === 0) {
                return [];
            }

            return allEmployees
                .filter(emp => emp.full_name.toLowerCase().includes(search))
                .slice(0, 8); // only show the first 8 matches, so the list doesn't get huge
        }

        function renderSuggestions(matches) {
            if (matches.length === 0) {
                suggestionsBox.classList.add('hidden');
                suggestionsBox.innerHTML = '';
                return;
            }

            suggestionsBox.innerHTML = matches.map(emp => `
            <div class="suggestion-item px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm text-gray-800 border-b border-gray-100 last:border-b-0"
                 data-name="${emp.full_name}">
                ${emp.full_name}
                <span class="text-xs text-gray-400 ml-1">${emp.department ? emp.department.name : ''}</span>
            </div>
        `).join('');

            suggestionsBox.classList.remove('hidden');
        }

        // fires every time the user types or deletes a letter
        searchInput.addEventListener('input', function() {
            const matches = getMatches(this.value);
            renderSuggestions(matches);
        });

        // when a suggestion is clicked, fill the box with that name and submit the filter form
        suggestionsBox.addEventListener('click', function(e) {
            const item = e.target.closest('.suggestion-item');
            if (!item) return;

            searchInput.value = item.dataset.name;
            suggestionsBox.classList.add('hidden');
            searchInput.closest('form').submit();
        });

        // hide the dropdown if the user clicks anywhere else on the page
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.classList.add('hidden');
            }
        });
    });
</script>

@endsection
