{{-- employee/dashboard.blade.php --}}

@extends('layouts.dashboard-base')

@section('title', 'Employee Dashboard')

@php
    $user = auth()->user();
    $pageTitle = 'Employee Dashboard';
    $activeRoute = 'dashboard';
@endphp

@section('content')
<!-- Confirmation Modal -->
<div id="confirmation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4" id="modal-icon-container">
                <i id="modal-icon" class="fas fa-question-circle text-blue-600 text-xl"></i>
            </div>
            
            <!-- Modal Content -->
            <div class="text-center">
                <h3 id="modal-title" class="text-lg font-medium text-gray-900 mb-2"></h3>
                <p id="modal-message" class="text-sm text-gray-500 mb-4"></p>
                
                <!-- Action Buttons -->
                <div class="flex justify-center space-x-4 mt-6">
                    <button id="modal-cancel-btn" type="button" class="px-5 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button id="modal-confirm-btn" type="button" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Time In/Out Modal -->
<div id="forgot-time-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="mt-1">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mx-auto mb-4">
                <i class="fas fa-clock text-yellow-700 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-1">Forgot to time in / out?</h3>
            <p class="text-sm text-gray-500 text-center mb-5">Please confirm what you forgot and provide your reason.</p>

            <form id="forgot-time-form" method="POST" action="{{ route('hr.help-support-ticket-store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="subject" id="forgot-subject">
                <input type="hidden" name="category" value="attendance">
                <input type="hidden" name="message" id="forgot-message">

                <div>
                    <label for="forgot-type" class="block text-sm font-medium text-gray-700 mb-2">What did you forget?</label>
                    <select id="forgot-type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                        <option value="">Select an option</option>
                        <option value="time in">I forgot to Time In</option>
                        <option value="time out">I forgot to Time Out</option>
                    </select>
                </div>

                <div>
                    <label for="forgot-reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea id="forgot-reason" rows="4" placeholder="Please explain why you forgot..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeForgotTimeModal()" class="px-5 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Immediate clock start script -->
<script>
// Start clock immediately when this script loads
(function() {
    function getPhilippineTime() {
        // Get current UTC time
        const now = new Date();
        // Philippine Standard Time is UTC+8
        const philippineTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Manila"}));
        return philippineTime;
    }

    // Format time in 12-hour format with AM/PM
    function format12HourTime(date) {
        let hours = date.getHours();
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const seconds = date.getSeconds().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        const hoursStr = hours.toString().padStart(2, '0');
        return `${hoursStr}:${minutes}:${seconds} ${ampm}`;
    }

    function startClockNow() {
        const philippineTime = getPhilippineTime();

        const timeString = format12HourTime(philippineTime);

        // Format date
        const dateOptions = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const dateString = philippineTime.toLocaleDateString('en-US', dateOptions);

        const timeElement = document.getElementById('current-times');
        const dateElement = document.getElementById('current-date');
        const lastUpdatedElement = document.getElementById('last-updated');

        if (timeElement) {
            timeElement.textContent = timeString;
        }
        if (dateElement) {
            dateElement.textContent = dateString;
        }
        if (lastUpdatedElement) {
            lastUpdatedElement.textContent = `Last updated: ${timeString}`;
        }
    }

    // Start immediately
    startClockNow();

    // Set up interval to update every second
    setInterval(startClockNow, 1000);
})();
</script>

    <!-- Welcome Section -->
    <div class="mb-6 sm:mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Welcome back, {{ $stats['employee_name'] }}!</h2>
        <p class="text-sm sm:text-base text-gray-600">Here's your personal information and payroll history.</p>

        @php
            $completedHoursToday = $todayAttendance ? $todayAttendance->calculateTotalHours() : 0;
            $isActive = $todayAttendance && $todayAttendance->hasActiveTimeEntry();
            $hasLoggedToday = $todayAttendance && $todayAttendance->time_in;

            // Fixed schedule's actual configured start/end (falls back to 8-5
            // only if somehow unset) - used for both the badge text and the
            // JS bar math, so this stays correct now that Fixed is editable
            $fixedStart = $todaySchedule && $todaySchedule->time_in
                ? \Carbon\Carbon::createFromFormat('H:i:s', $todaySchedule->time_in)
                : \Carbon\Carbon::createFromFormat('H:i:s', '08:00:00');
            $fixedEnd = $todaySchedule && $todaySchedule->time_out
                ? \Carbon\Carbon::createFromFormat('H:i:s', $todaySchedule->time_out)
                : \Carbon\Carbon::createFromFormat('H:i:s', '17:00:00');
        @endphp

        @if($todaySchedule)
            @php
                // map schedule color name -> soft badge bg + text tailwind classes.
                // written out in full here on purpose - tailwind can't see
                // classes assembled from a variable at build time
                [$scheduleBg, $scheduleTextClass] = match($todaySchedule->status_color) {
                    'green' => ['bg-green-50 border-green-200', 'text-green-700'],
                    'yellow' => ['bg-yellow-50 border-yellow-200', 'text-yellow-700'],
                    'red' => ['bg-red-50 border-red-200', 'text-red-700'],
                    'blue' => ['bg-blue-50 border-blue-200', 'text-blue-700'],
                    default => ['bg-gray-50 border-gray-200', 'text-gray-700'],
                };
            @endphp
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border {{ $scheduleBg }} {{ $scheduleTextClass }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    {{ $todaySchedule->status_label }}
                </span>
                @if($todaySchedule->status === 'Working')
                    @if($todaySchedule->isFlexible())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border bg-purple-50 border-purple-200 text-purple-700">
                            <i class="fas fa-sliders-h"></i>
                            Flexible &middot; {{ rtrim(rtrim(number_format($todaySchedule->required_hours, 1), '0'), '.') }}h required
                        </span>
                        @php
                            $requiredHours = (float) $todaySchedule->required_hours;
                            $shortHours = max(0, $requiredHours - $completedHoursToday);
                        @endphp
                        @if(!$isActive && $hasLoggedToday)
                            @if($shortHours > 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border bg-amber-50 border-amber-200 text-amber-700">
                                    <i class="fas fa-pause-circle"></i>{{ number_format($completedHoursToday, 1) }}h logged &middot; {{ number_format($shortHours, 1) }}h left
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border bg-green-50 border-green-200 text-green-700">
                                    <i class="fas fa-check-circle"></i>Hours Complete
                                </span>
                            @endif
                        @elseif($isActive)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border bg-blue-50 border-blue-200 text-blue-700" id="time-remaining-badge">
                                <i class="fas fa-hourglass-half"></i>Calculating...
                            </span>
                            <script>
                                window.todayRequiredHours = {{ (float) $todaySchedule->required_hours }};
                                window.completedHoursBeforeSession = {{ (float) $completedHoursToday }};
                            </script>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border bg-gray-50 border-gray-200 text-gray-500">
                                {{ rtrim(rtrim(number_format($todaySchedule->required_hours, 1), '0'), '.') }}h needed today
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border bg-gray-50 border-gray-200 text-gray-700">
                            <i class="fas fa-clock"></i>Fixed &middot; {{ $fixedStart->format('g:i A') }} &ndash; {{ $fixedEnd->format('g:i A') }}
                        </span>
                        @if($todayAttendance && $todayAttendance->time_in)
                            @if($todayAttendance->isLate())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border bg-red-50 border-red-200 text-red-700">
                                    <i class="fas fa-exclamation-circle"></i>Late by {{ $todayAttendance->getLateMinutes() }}m
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border bg-green-50 border-green-200 text-green-700">
                                    <i class="fas fa-check-circle"></i>On Time
                                </span>
                            @endif
                        @endif
                        <script>
                            window.isFixedSchedule = true;
                            window.todayLateMinutes = {{ $todayAttendance && $todayAttendance->time_in ? $todayAttendance->getLateMinutes() : 0 }};
                            window.scheduledStartTime = "{{ $fixedStart->format('H:i:s') }}";
                            window.scheduledEndTime = "{{ $fixedEnd->format('H:i:s') }}";
                            window.completedHoursBeforeSession = {{ (float) $completedHoursToday }};
                        </script>
                    @endif
                @endif
            </div>
        @endif
    </div>

    <!-- Time In/Out Section -->
    <div class="mb-6 sm:mb-8">
        <!-- Current Time Display -->
        <div class="relative bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>

            <div class="absolute top-4 right-4">
                <div class="inline-flex items-center gap-1.5 bg-green-50 border border-green-200 px-2.5 py-1 rounded-full">
                    <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse" id="live-indicator"></div>
                    <span class="text-xs font-semibold text-green-700">LIVE</span>
                </div>
            </div>

            <div class="text-center pt-8 pb-6 px-6">
                <div class="text-4xl sm:text-5xl font-bold text-gray-900 font-mono tracking-tight mb-2" id="current-times">--:--:--</div>
                <div class="text-base font-medium text-gray-600" id="current-date">Loading...</div>
                <div class="text-xs text-gray-400 mt-1">Philippine Standard Time &middot; <span id="last-updated">Last updated: --:--:--</span></div>
            </div>

            @if($hasLoggedToday)
                @php
                    $activeEntry = $isActive ? $todayAttendance->getActiveTimeEntry() : null;
                    $displayMinutes = (int) round($completedHoursToday * 60);
                    $displayHours = intdiv($displayMinutes, 60);
                    $displayMins = $displayMinutes % 60;
                @endphp
                <div class="border-t border-gray-100 bg-gray-50 px-6 py-5">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-500">{{ $isActive ? 'Working for' : 'Logged today' }}</span>
                        <span class="text-xl font-bold text-gray-900" id="working-time">
                            @if($isActive)
                                {{ $displayHours }}h {{ $displayMins }}m
                            @else
                                {{ $displayHours }}h {{ $displayMins }}m
                            @endif
                        </span>
                    </div>

                    @if($todaySchedule && !$todaySchedule->isFlexible())
                        <div id="shift-progress-wrap" class="mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-1.5 flex overflow-hidden">
                                <div id="shift-late-bar" class="bg-red-500 h-1.5 transition-all" style="width: 0%"></div>
                                <div id="shift-progress-bar" class="bg-blue-600 h-1.5 transition-all" style="width: 0%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>{{ $fixedStart->format('g:i A') }}</span>
                                <span id="shift-progress-text">0% of shift</span>
                                <span>{{ $fixedEnd->format('g:i A') }}</span>
                            </div>
                            <div id="overtime-note" class="mt-2 text-xs text-amber-700 hidden"></div>
                        </div>
                    @else
                        <div id="flex-progress-wrap" class="mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div id="flex-progress-bar" class="bg-purple-600 h-1.5 rounded-full transition-all" style="width: {{ min(100, round(($completedHoursToday / max(0.01, (float) $todaySchedule->required_hours)) * 100)) }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>0h</span>
                                <span id="flex-progress-text">{{ number_format($completedHoursToday, 1) }}h of {{ rtrim(rtrim(number_format($todaySchedule->required_hours, 1), '0'), '.') }}h</span>
                                <span>{{ rtrim(rtrim(number_format($todaySchedule->required_hours, 1), '0'), '.') }}h</span>
                            </div>
                        </div>
                    @endif

                    @if(!$isActive)
                        <div class="mt-2 text-xs text-blue-600">
                            <i class="fas fa-info-circle mr-1"></i>You're currently clocked out. Clock back in to continue.
                        </div>
                    @endif

                    <!-- Pass active entry time in to JS for live update -->
                    @if($activeEntry)
                        <script>
                            window.activeSessionStart = "{{ $activeEntry->time_in->toIso8601String() }}";
                        </script>
                    @endif
                </div>
            @endif
        </div>

        <!-- Time In/Out Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Time In Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-50 border border-green-100 rounded-full mb-3">
                        <i class="fas fa-sign-in-alt text-xl text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Time In</h3>
                    <p class="text-gray-500 text-sm mb-4">Start your workday</p>
                    @if(!$isActive)
                    <button id="time-in-btn" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors text-sm shadow-sm" onclick="confirmTimeIn()">
                        <i class="fas fa-play mr-2"></i>
                        {{ $hasLoggedToday ? 'Clock In Again' : 'Clock In Now' }}
                    </button>
                    @else
                    <button disabled class="w-full bg-gray-100 text-gray-400 font-semibold py-2.5 px-4 rounded-lg cursor-not-allowed text-sm border border-gray-200">
                        <i class="fas fa-check mr-2"></i>
                        Already Clocked In
                    </button>
                    @endif
                </div>
            </div>

            <!-- Time Out Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all" id="time-out-card">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-red-50 border border-red-100 rounded-full mb-3">
                        <i class="fas fa-sign-out-alt text-xl text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Time Out</h3>
                    <p class="text-gray-500 text-sm mb-4">End your workday</p>
                    @if($isActive)
                    <button id="time-out-btn" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors text-sm shadow-sm" onclick="confirmTimeOut()">
                        <i class="fas fa-stop mr-2"></i>
                        Clock Out
                    </button>
                    @else
                    <button disabled class="w-full bg-gray-100 text-gray-400 font-semibold py-2.5 px-4 rounded-lg cursor-not-allowed text-sm border border-gray-200">
                        <i class="fas fa-stop mr-2"></i>
                        Clock Out
                    </button>
                    @endif
                </div>
            </div>
        </div>

        @if(!$isActive)
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Access Restricted
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>You must be currently timed in to access other modules of the system. Please clock in to continue with your work.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Personal Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-id-badge text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Employee ID</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['employee_id'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-briefcase text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Position</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['position'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Department</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['department'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary and Hire Date -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Monthly Salary</p>
                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($stats['salary'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-indigo-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Hire Date</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['hire_date']->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Yearly Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Yearly Summary</h3>
                <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</button>
            </div>
            <div class="space-y-4">
                @forelse($yearly_summary as $summary)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $summary->year }}</p>
                        <p class="text-xs text-gray-500">{{ $summary->payroll_count }} payrolls</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">₱{{ number_format($summary->total_net_pay, 2) }}</p>
                        <p class="text-xs text-gray-500">Total Net Pay</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-chart-line text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No payroll data available</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="space-y-4">
                <!-- Time In Button -->
                @if(!$todayAttendance || !$todayAttendance->hasActiveTimeEntry())
                <button id="quick-time-in-btn" onclick="confirmTimeIn()" class="w-full flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Time In
                </button>
                @else
                <button disabled class="w-full flex items-center justify-center px-4 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed">
                    <i class="fas fa-check mr-2"></i>
                    Already Clocked In
                </button>
                @endif

                <!-- Time Out Button -->
                @if($todayAttendance && $todayAttendance->hasActiveTimeEntry())
                <button id="quick-time-out-btn" onclick="confirmTimeOut()" class="w-full flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Time Out
                </button>
                @else
                <button disabled class="w-full flex items-center justify-center px-4 py-3 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Time Out (Clock In First)
                </button>
                @endif

                <!-- Update Profile Button -->
                <a href="{{ route('hr.profile') }}" class="w-full flex items-center justify-center px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Update Profile
                </a>

                <!-- Forgot Time In/Out Button -->
                <button type="button" onclick="openForgotTimeModal()" class="w-full flex items-center justify-center px-4 py-3 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-colors">
                    <i class="fas fa-clock mr-2"></i>
                    Forgot to time in / out?
                </button>
                
                <!-- Contact HR Button -->
                <button class="w-full flex items-center justify-center px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-question-circle mr-2"></i>
                    Contact HR
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Payrolls Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Recent Payrolls</h3>
                <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recent_payrolls as $payroll)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $payroll->pay_period_start->format('M d') }} - {{ $payroll->pay_period_end->format('M d, Y') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($payroll->gross_pay - $payroll->net_pay, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($payroll->net_pay, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payroll->status === 'processed' ? 'bg-green-100 text-green-800' : ($payroll->status === 'paid' ? 'bg-blue-100 text-blue-800' : ($payroll->status === 'canceled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                {{ ucfirst($payroll->status) }}
                            </span>
                            @if($payroll->status === 'canceled' && $payroll->rejection_reason)
                                <div class="text-xs text-red-500 mt-1 max-w-[150px] truncate" title="{{ $payroll->rejection_reason }}">
                                    Reason: {{ $payroll->rejection_reason }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if(in_array($payroll->status, ['approved', 'processed', 'paid']))
                                <button onclick="downloadSinglePayslip('{{ $payroll->id }}')" 
                                      class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-download mr-1"></i> Download
                                </button>
                            @else
                                <span class="text-gray-400">Not available</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-money-bill-wave text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">No payroll records found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<div id="attendance-data"
     data-today-attendance='@json($todayAttendance)'
     data-recent-activity='@json($recentActivity)'
     style="display: none;"></div>

<script>
// Global variables
let currentStatus = null;
const dataElement = document.getElementById('attendance-data');
let attendanceRecord = dataElement ? JSON.parse(dataElement.getAttribute('data-today-attendance') || 'null') : null;
let recentActivity = dataElement ? JSON.parse(dataElement.getAttribute('data-recent-activity') || '[]') : [];

// Modal variables
let pendingAction = null; // Will store the function to execute after confirmation

// Get Philippine Standard Time (UTC+8)
function getPhilippineTime() {
    const now = new Date();
    return new Date(now.toLocaleString("en-US", {timeZone: "Asia/Manila"}));
}

// Format time in 12-hour format with AM/PM
function format12HourTime(date) {
    let hours = date.getHours();
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const seconds = date.getSeconds().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    const hoursStr = hours.toString().padStart(2, '0');
    return `${hoursStr}:${minutes}:${seconds} ${ampm}`;
}

// ============================================================
// CONFIRMATION MODAL FUNCTIONS
// ============================================================

// Show confirmation modal
function showConfirmationModal(title, message, confirmAction, options = {}) {
    // Store the action to execute after confirmation
    pendingAction = confirmAction;
    
    // Set modal color and icon based on options
    const modalColor = options.color || 'blue';
    const modalIcon = options.icon || 'fa-question-circle';
    
    // Update modal content
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-message').textContent = message;
    document.getElementById('modal-icon').className = `fas ${modalIcon} text-${modalColor}-600 text-xl`;
    
    // Update modal styling
    const modalIconContainer = document.getElementById('modal-icon-container');
    const confirmBtn = document.getElementById('modal-confirm-btn');
    
    // Update modal background color
    modalIconContainer.className = `mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-${modalColor}-100 mb-4`;
    
    // Update confirm button color
    confirmBtn.className = `px-5 py-2 bg-${modalColor}-600 text-white rounded-md hover:bg-${modalColor}-700 focus:outline-none focus:ring-2 focus:ring-${modalColor}-500 transition-colors`;
    
    // Show modal
    const modal = document.getElementById('confirmation-modal');
    modal.classList.remove('hidden');
    modal.classList.add('block');
}

// Hide confirmation modal
function hideConfirmationModal() {
    const modal = document.getElementById('confirmation-modal');
    modal.classList.remove('block');
    modal.classList.add('hidden');
    pendingAction = null;
}

// ============================================================
// FORGOT TIME IN/OUT MODAL FUNCTIONS
// ============================================================
function openForgotTimeModal() {
    const modal = document.getElementById('forgot-time-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('block');
}

function closeForgotTimeModal() {
    const modal = document.getElementById('forgot-time-modal');
    if (!modal) return;
    modal.classList.remove('block');
    modal.classList.add('hidden');
}

// Initialize modal event listeners
function initializeModal() {
    const modal = document.getElementById('confirmation-modal');
    const cancelBtn = document.getElementById('modal-cancel-btn');
    const confirmBtn = document.getElementById('modal-confirm-btn');
    
    // Close modal when clicking cancel button
    cancelBtn.addEventListener('click', hideConfirmationModal);
    
    // Execute pending action when clicking confirm button
    confirmBtn.addEventListener('click', function() {
        if (pendingAction) {
            pendingAction();
        }
        hideConfirmationModal();
    });
    
    // Close modal when clicking outside of it
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            hideConfirmationModal();
        }
    });
}

// ============================================================
// TIME IN/OUT CONFIRMATION FUNCTIONS
// ============================================================

// Confirm Time In
function confirmTimeIn() {
    // Get current time for the confirmation message
    const currentTime = getPhilippineTime();
    const formattedTime = format12HourTime(currentTime);
    
    showConfirmationModal(
        'Confirm Time In',
        `Are you sure you want to clock in at ${formattedTime}?`,
        timeIn, // This is the original timeIn function that will be called on confirmation
        {
            color: 'green',
            icon: 'fa-sign-in-alt'
        }
    );
}

// Confirm Time Out
function confirmTimeOut() {
    // Get current time for the confirmation message
    const currentTime = getPhilippineTime();
    const formattedTime = format12HourTime(currentTime);
    
    showConfirmationModal(
        'Confirm Time Out',
        `Are you sure you want to clock out at ${formattedTime}?`,
        timeOut, // This is the original timeOut function that will be called on confirmation
        {
            color: 'red',
            icon: 'fa-sign-out-alt'
        }
    );
}

// Update the clock every second
function updateClock() {
    const now = getPhilippineTime();
    const timeDisplay = document.getElementById('current-times');
    const dateDisplay = document.getElementById('current-date');
    const lastUpdated = document.getElementById('last-updated');
    
    if (timeDisplay) timeDisplay.textContent = format12HourTime(now);
    if (dateDisplay) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateDisplay.textContent = now.toLocaleDateString('en-US', options);
    }
    if (lastUpdated) lastUpdated.textContent = `Last updated: ${format12HourTime(now)}`;
}

// Update working time display
function updateWorkingTime() {
    if (!window.activeSessionStart) {
        return;
    }

    const timeIn = new Date(window.activeSessionStart);
    const now = getPhilippineTime();
    const diffMs = Math.max(0, now - timeIn);
    const priorHours = window.completedHoursBeforeSession || 0;
    const priorMs = priorHours * 60 * 60 * 1000;
    const totalMs = priorMs + diffMs;

    const diffHours = Math.floor(totalMs / (1000 * 60 * 60));
    const diffMinutes = Math.floor((totalMs % (1000 * 60 * 60)) / (1000 * 60));

    const workingTimeElement = document.getElementById('working-time');
    if (workingTimeElement) {
        workingTimeElement.textContent = `${diffHours}h ${diffMinutes}m`;
    }

    // Flexible schedule: remaining hours, then overtime (still recorded/shown
    // even though it isn't approved yet - approval only affects whether it
    // gets paid, not whether it's visible)
    if (window.todayRequiredHours !== undefined) {
        const badge = document.getElementById('time-remaining-badge');
        const pillBase = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border';
        const elapsedHours = totalMs / (1000 * 60 * 60);
        const remaining = window.todayRequiredHours - elapsedHours;
        const overtimeHours = elapsedHours - window.todayRequiredHours;
        const isOvertime = remaining <= 0 && overtimeHours > 0.0167; // more than ~1 minute over

        if (badge) {
            if (isOvertime) {
                const otH = Math.floor(overtimeHours);
                const otM = Math.round((overtimeHours - otH) * 60);
                const otLabel = otH > 0 ? `${otH}h ${otM}m` : `${otM}m`;
                badge.innerHTML = `<i class="fas fa-exclamation-triangle"></i>${otLabel} overtime - pending approval`;
                badge.className = `${pillBase} bg-amber-50 border-amber-200 text-amber-700`;
            } else if (remaining <= 0) {
                badge.innerHTML = '<i class="fas fa-check-circle"></i>Hours Complete';
                badge.className = `${pillBase} bg-green-50 border-green-200 text-green-700`;
            } else {
                const remHours = Math.floor(remaining);
                const remMinutes = Math.round((remaining - remHours) * 60);
                badge.innerHTML = `<i class="fas fa-hourglass-half"></i>${remHours}h ${remMinutes}m remaining`;
                badge.className = `${pillBase} bg-blue-50 border-blue-200 text-blue-700`;
            }
        }

        const flexBar = document.getElementById('flex-progress-bar');
        const flexText = document.getElementById('flex-progress-text');
        if (flexBar && flexText) {
            const pct = Math.min(100, Math.max(0, Math.round((elapsedHours / window.todayRequiredHours) * 100)));
            flexBar.style.width = `${pct}%`;
            flexBar.className = isOvertime
                ? 'bg-amber-500 h-1.5 rounded-full transition-all'
                : 'bg-purple-600 h-1.5 rounded-full transition-all';
            flexText.textContent = isOvertime
                ? `${elapsedHours.toFixed(1)}h of ${window.todayRequiredHours}h - in overtime`
                : (remaining <= 0
                    ? `${elapsedHours.toFixed(1)}h of ${window.todayRequiredHours}h - complete`
                    : `${elapsedHours.toFixed(1)}h of ${window.todayRequiredHours}h`);
        }
    }

    // Fixed schedule: timeline bar across the actual scheduled start/end,
    // red = late arrival, blue = hours worked, amber = overtime past shift end
    if (window.isFixedSchedule && window.scheduledStartTime && window.scheduledEndTime) {
        const lateBar = document.getElementById('shift-late-bar');
        const progressBar = document.getElementById('shift-progress-bar');
        const progressText = document.getElementById('shift-progress-text');
        const overtimeNote = document.getElementById('overtime-note');

        if (progressBar && progressText) {
            const [startH, startM] = window.scheduledStartTime.split(':').map(Number);
            const [endH, endM] = window.scheduledEndTime.split(':').map(Number);

            const shiftStart = new Date(timeIn);
            shiftStart.setHours(startH, startM, 0, 0);
            const shiftEnd = new Date(timeIn);
            shiftEnd.setHours(endH, endM, 0, 0);

            const totalShiftMinutes = Math.max(1, (shiftEnd - shiftStart) / 60000);
            const lateMinutes = window.todayLateMinutes || 0;
            const lateWidthPct = Math.min(100, (lateMinutes / totalShiftMinutes) * 100);

            const workedMinutes = totalMs / 60000;
            const nonOvertimeWorkedMinutes = Math.min(workedMinutes, totalShiftMinutes - (lateMinutes > totalShiftMinutes ? totalShiftMinutes : 0));
            const workWidthPct = Math.min(100 - lateWidthPct, (workedMinutes / totalShiftMinutes) * 100);

            if (lateBar) lateBar.style.width = `${lateWidthPct}%`;

            const isOvertime = now > shiftEnd;

            if (isOvertime) {
                progressBar.className = 'bg-amber-500 h-1.5 transition-all';
                progressBar.style.width = `${Math.max(0, 100 - lateWidthPct)}%`;

                const overtimeMinutes = Math.max(0, (now - shiftEnd) / 60000);
                const otH = Math.floor(overtimeMinutes / 60);
                const otM = Math.round(overtimeMinutes % 60);
                const otLabel = otH > 0 ? `${otH}h ${otM}m` : `${otM}m`;

                progressText.textContent = `${otLabel} overtime`;

                if (overtimeNote) {
                    overtimeNote.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i>${otLabel} overtime - requires Manager/HR approval. <a href="/attendance/overtime" class="underline font-medium">Submit Overtime Request &rarr;</a>`;
                    overtimeNote.classList.remove('hidden');
                }
            } else {
                progressBar.className = 'bg-blue-600 h-1.5 transition-all';
                progressBar.style.width = `${workWidthPct}%`;

                const minutesLeft = Math.max(0, (shiftEnd - now) / 60000);
                if (minutesLeft <= 0) {
                    progressText.textContent = 'Shift complete';
                } else {
                    const leftHours = Math.floor(minutesLeft / 60);
                    const leftMins = Math.round(minutesLeft % 60);
                    progressText.textContent = `${leftHours}h ${leftMins}m left`;
                }

                if (overtimeNote) {
                    overtimeNote.classList.add('hidden');
                }
            }
        }
    }
}

// Update UI based on attendance status
function updateAttendanceUI() {
    const timeInBtn = document.getElementById('time-in-btn');
    const timeOutBtn = document.getElementById('time-out-btn');
    const quickTimeInBtn = document.getElementById('quick-time-in-btn');
    const quickTimeOutBtn = document.getElementById('quick-time-out-btn');

    // We rely on the PHP-rendered state since we reload the page after each action
    // but this function handles the immediate state change if needed.
    // Since we reload, the most important thing is the PHP logic updated above.
}

// Time In function (original - called after confirmation)
async function timeIn() {
    const btn = document.getElementById('time-in-btn');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

    try {
        const response = await fetch('{{ route("attendance.time-in") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (response.ok) {
            showSuccess(data.message);
            attendanceRecord = data.attendance_record;
            updateAttendanceUI();
            // Refresh the page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showError(data.error || 'Failed to clock in');
        }
    } catch (error) {
        console.error('Error clocking in:', error);
        showError('Failed to clock in');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// Time Out function (original - called after confirmation)
async function timeOut() {
    const btn = document.getElementById('time-out-btn');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

    try {
        const response = await fetch('{{ route("attendance.time-out") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (response.ok) {
            showSuccess(data.message);
            attendanceRecord = data.attendance_record;
            updateAttendanceUI();
            // Refresh the page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showError(data.error || 'Failed to clock out');
        }
    } catch (error) {
        console.error('Error clocking out:', error);
        showError('Failed to clock out');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// Show success message
function showSuccess(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Show error message
function showError(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// ============================================================
// PAYSLIP DOWNLOAD FUNCTIONS
// ============================================================

// Get payslip download URL
function getPayslipDownloadUrl(payrollId) {
    return `/employee/payslip/download/${payrollId}`;
}

function getTestDownloadUrl(payrollId) {
    return `/employee/test-download/${payrollId}`;
}

// Test function to check if download works
async function testDownloadRoute(payrollId) {
    try {
        const url = getTestDownloadUrl(payrollId);
        console.log('Testing download route:', url);
        
        const response = await fetch(url);
        const data = await response.json();
        console.log('Test download route response:', data);
        
        if (data.success) {
            return { success: true, downloadable: data.downloadable, message: data.message };
        } else {
            return { success: false, error: data.error || 'Route test failed' };
        }
    } catch (error) {
        console.error('Route test failed:', error);
        return { success: false, error: 'Route test failed: ' + error.message };
    }
}

// Show loading overlay
function showLoadingOverlay(message = 'Generating PDF...') {
    hideLoadingOverlay();
    
    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay-payslip';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        flex-direction: column;
        color: white;
        font-size: 18px;
    `;
    
    overlay.innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-3x mb-4"></i>
            <div>${message}</div>
            <div class="text-sm mt-2 text-gray-300">Please wait while we generate your payslip...</div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
}

// Hide loading overlay
function hideLoadingOverlay() {
    const overlay = document.getElementById('loading-overlay-payslip');
    if (overlay) {
        overlay.remove();
        document.body.style.overflow = '';
    }
}

// Main download function (for navigation button)
async function downloadEmployeePayslip(payrollId) {
    console.log('Download Employee Payslip called for ID:', payrollId);
    
    // Show loading state for navigation button
    const navBtn = document.getElementById('nav-download-payslip-btn');
    if (navBtn) {
        const originalText = navBtn.innerHTML;
        navBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating...';
        navBtn.disabled = true;
        
        // Restore button after 5 seconds even if error
        setTimeout(() => {
            navBtn.innerHTML = originalText;
            navBtn.disabled = false;
        }, 5000);
    }
    
    try {
        // First test the route
        const testResult = await testDownloadRoute(payrollId);
        console.log('Test result:', testResult);
        
        if (!testResult.success) {
            throw new Error(testResult.error || 'Cannot connect to server');
        }
        
        if (!testResult.downloadable) {
            throw new Error('Payslip is not available for download yet. Status: ' + (testResult.payroll_status || 'unknown'));
        }
        
        // Direct download approach
        const downloadUrl = getPayslipDownloadUrl(payrollId);
        console.log('Opening download URL:', downloadUrl);
        
        // Open in new tab (most reliable)
        window.open(downloadUrl, '_blank');
        
        // Show success message
        showSuccess('Payslip download started!');
        
    } catch (error) {
        console.error('Download error:', error);
        showError('Error: ' + error.message);
    } finally {
        // Hide any loading overlay
        hideLoadingOverlay();
    }
}

// Download function for table row buttons
async function downloadSinglePayslip(payrollId) {
    console.log('Download Single Payslip called for ID:', payrollId);
    
    // Find and update the specific button
    const buttonSelector = `button[onclick*="downloadSinglePayslip('${payrollId}')"]`;
    const buttons = document.querySelectorAll(buttonSelector);
    
    let btn = null;
    let originalText = '';
    
    if (buttons.length > 0) {
        btn = buttons[0];
        originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
        
        // Restore button after 5 seconds
        setTimeout(() => {
            if (btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }, 5000);
    }
    
    try {
        // Test the route first
        const testResult = await testDownloadRoute(payrollId);
        console.log('Single test result:', testResult);
        
        if (!testResult.success) {
            throw new Error(testResult.error || 'Cannot connect to server');
        }
        
        if (!testResult.downloadable) {
            throw new Error('Payslip not available for download');
        }
        
        // Direct download in new tab
        const downloadUrl = getPayslipDownloadUrl(payrollId);
        window.open(downloadUrl, '_blank');
        
        showSuccess('Payslip download started in new tab!');
        
    } catch (error) {
        console.error('Single download error:', error);
        showError('Error: ' + error.message);
    } finally {
        hideLoadingOverlay();
        
        // Restore button after a short delay
        setTimeout(() => {
            if (btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }, 1000);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the confirmation modal
    initializeModal();
    
    // Update clock and working time
    updateClock();
    updateAttendanceUI();
    updateWorkingTime();
    
    setInterval(updateClock, 1000);
    setInterval(updateWorkingTime, 60000); // Update working time every minute
    
    // Add event listener for page visibility
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            hideLoadingOverlay();
        }
    });

    // Handle Forgot Time form submission
    const forgotForm = document.getElementById('forgot-time-form');
    if (forgotForm) {
        forgotForm.addEventListener('submit', function(event) {
            const forgotType = document.getElementById('forgot-type');
            const forgotReason = document.getElementById('forgot-reason');
            const subjectField = document.getElementById('forgot-subject');
            const messageField = document.getElementById('forgot-message');

            if (!forgotType.value || !forgotReason.value.trim()) {
                event.preventDefault();
                showError('Please select what you forgot and provide your reason.');
                return;
            }

            const typeLabel = forgotType.value;
            const submittedAtPh = new Date().toLocaleString('en-PH', {
                timeZone: 'Asia/Manila',
                year: 'numeric',
                month: 'long',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
            });
            subjectField.value = `Forgot to ${typeLabel}`;
            messageField.value = `I forgot to ${typeLabel}.\n\nReason: ${forgotReason.value.trim()}\n\nSubmitted at (PH Time): ${submittedAtPh}`;
        });
    }

    // Close forgot-time modal when clicking outside
    const forgotModal = document.getElementById('forgot-time-modal');
    if (forgotModal) {
        forgotModal.addEventListener('click', function(event) {
            if (event.target === forgotModal) {
                closeForgotTimeModal();
            }
        });
    }
});
</script>
@endsection