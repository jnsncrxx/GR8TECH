@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'attendance.leave-management'])

@section('title', 'New Leave Request')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">New Leave Request</h1>
                <p class="mt-1 text-sm text-gray-500">Submit a leave request for approval</p>
            </div>
            <a href="{{ route('attendance.leave-management') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                <span class="hidden sm:inline">Back to Leave Management</span>
                <span class="sm:hidden">Back</span>
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <p class="text-sm font-medium text-green-900">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                <p class="text-sm font-medium text-red-900">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @if(session('overlap_error'))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-0.5"></i>
                <div>
                    <h4 class="text-sm font-medium text-yellow-900">Overlap Detected</h4>
                    <p class="text-sm text-yellow-700">{{ session('overlap_error') }}</p>
                    @php
                        $overlapDetails = session('overlap_details') ? json_decode(session('overlap_details'), true) : [];
                    @endphp
                    @if(!empty($overlapDetails))
                        <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside">
                            @foreach($overlapDetails as $detail)
                                <li>{{ ucfirst($detail['type']) }}: {{ $detail['start'] }} to {{ $detail['end'] }} ({{ $detail['status'] }})</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Validation Errors - Styled like the reason validation -->
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-600 mr-3 mt-0.5"></i>
                <div>
                    <h4 class="text-sm font-medium text-red-900">Please fix the following errors:</h4>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Leave Balance Info -->
        <div id="leaveBalanceContainer" class="bg-blue-50 border border-blue-200 rounded-lg p-4 {{ ($employee && $leaveBalance) ? '' : 'hidden' }}">
            <h3 class="text-sm font-medium text-blue-900 mb-3" id="balanceTitle">{{ in_array($user->role, ['admin', 'hr']) ? 'Employee Leave Balance' : 'Your Leave Balance' }}</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="balanceGrid">
                @if($employee && $leaveBalance)
                <div>
                    <div class="text-xs text-blue-700">Vacation</div>
                    <div class="text-sm font-semibold text-blue-900" id="balance-vacation">{{ $availableDays['vacation'] ?? 0 }} days</div>
                </div>
                <div>
                    <div class="text-xs text-blue-700">Sick</div>
                    <div class="text-sm font-semibold text-blue-900" id="balance-sick">{{ $availableDays['sick'] ?? 0 }} days</div>
                </div>
                <div>
                    <div class="text-xs text-blue-700">Personal</div>
                    <div class="text-sm font-semibold text-blue-900" id="balance-personal">{{ $availableDays['personal'] ?? 0 }} days</div>
                </div>
                <div>
                    <div class="text-xs text-blue-700">Emergency</div>
                    <div class="text-sm font-semibold text-blue-900" id="balance-emergency">{{ $availableDays['emergency'] ?? 0 }} days</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('attendance.leave-management.store') }}" class="p-4 sm:p-6 space-y-6" id="leaveRequestForm">
                @csrf
                
                <!-- Employee Selection (HR/Admin only) -->
                @if(in_array($user->role, ['admin', 'hr']))
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="sm:col-span-2">
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Select Employee</label>
                            <select name="employee_id" id="employee_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('employee_id') border-red-500 @enderror">
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }} - {{ $emp->department->name ?? 'No Department' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @else
                    <input type="hidden" name="employee_id" id="employee_id" value="{{ $employee->id }}">
                @endif

                <!-- Leave Request Details -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Leave Request Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="leave_type" class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                            <select name="leave_type" id="leave_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('leave_type') border-red-500 @enderror">
                                <option value="">Select Leave Type</option>
                                <option value="vacation" {{ old('leave_type') == 'vacation' ? 'selected' : '' }}>Vacation Leave</option>
                                <option value="sick" {{ old('leave_type') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                                <option value="personal" {{ old('leave_type') == 'personal' ? 'selected' : '' }}>Personal Leave</option>
                                <option value="emergency" {{ old('leave_type') == 'emergency' ? 'selected' : '' }}>Emergency Leave</option>
                                <option value="maternity" {{ old('leave_type') == 'maternity' ? 'selected' : '' }}>Maternity Leave</option>
                                <option value="paternity" {{ old('leave_type') == 'paternity' ? 'selected' : '' }}>Paternity Leave</option>
                                <option value="bereavement" {{ old('leave_type') == 'bereavement' ? 'selected' : '' }}>Bereavement Leave</option>
                                <option value="study" {{ old('leave_type') == 'study' ? 'selected' : '' }}>Study Leave</option>
                            </select>
                            @error('leave_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="days_requested" class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)</label>
                            <input type="number" name="days_requested" id="days_requested" min="1" readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed @error('days_requested') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Auto-calculated from dates (Sundays excluded)</p>
                            @error('days_requested')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="text" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                                placeholder="Select start date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="text" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                                placeholder="Select end date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date Info Display -->
                        <div class="sm:col-span-2">
                            <div id="dateInfo" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <div class="flex flex-wrap gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Working Days:</span>
                                        <span id="workingDaysCount" class="font-medium text-gray-900">0</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Sundays Excluded:</span>
                                        <span id="sundaysCount" class="font-medium text-orange-600">0</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Conflicting Dates:</span>
                                        <span id="conflictingDates" class="font-medium text-red-600">None</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                            <textarea name="reason" id="reason" rows="4" required
                                placeholder="Please provide a reason for your leave request..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('reason') border-red-500 @enderror">{{ old('reason') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Maximum 500 characters</p>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Overlap Modal -->
                <div id="overlapModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center px-4">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                Overlapping Leave Request
                            </h3>
                            <button type="button" id="closeModalBtn" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="px-6 py-5">
                            <p class="text-sm text-gray-600 mb-4">You have existing leave requests that overlap with these dates:</p>
                            <div id="overlapList" class="space-y-3 mb-4">
                                <!-- Overlap items will be inserted here -->
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-sm text-yellow-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Do you want to <strong>replace</strong> the existing pending leave request with this new one?
                                </p>
                                <p class="text-xs text-yellow-600 mt-1">Note: Only pending leaves can be replaced. Approved leaves cannot be modified.</p>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 px-6 py-4 border-t border-gray-200">
                            <button type="button" id="cancelRequestBtn"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancel Request
                            </button>
                            <button type="button" id="replaceLeaveBtn"
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-white hover:bg-blue-700 transition-colors">
                                <i class="fas fa-exchange-alt mr-2"></i>Replace & Submit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Conflict Warning (Approved Leaves) -->
                <div id="conflictWarning" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-red-900">Date Conflict Detected</h4>
                            <p class="mt-1 text-sm text-red-700" id="conflictMessage">You have an existing approved leave on one or more of these dates.</p>
                        </div>
                    </div>
                </div>

                <!-- Balance Warning -->
                <div id="balanceWarning" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-900">Insufficient Leave Balance</h4>
                            <p class="mt-1 text-sm text-yellow-700" id="balanceInfo"></p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('attendance.leave-management') }}" class="px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Cancel
                    </a>
                    <button type="button" id="submitBtn" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Hidden data for JavaScript -->
<div id="leave-request-data" 
     data-available-days='{!! json_encode($availableDays ?? []) !!}' 
     data-should-check-balance='{!! json_encode($employee && $leaveBalance ? true : false) !!}'
     data-employee-id='{{ $employee->id ?? '' }}'
     data-current-year='{{ now()->year }}'
     style="display: none;"></div>

<style>
    /* Ensure all form inputs and selects are visible with dark text */
    #employee_id,
    #leave_type,
    #start_date,
    #end_date {
        color: #111827 !important;
        background-color: #ffffff !important;
    }

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

    input[type="date"],
    input[type="text"],
    input[type="number"],
    textarea {
        color: #111827 !important;
        background-color: #ffffff !important;
    }

    input::placeholder,
    textarea::placeholder {
        color: #9ca3af !important;
        opacity: 1;
    }

    /* Flatpickr Calendar Styling - Clean Design */
    .flatpickr-calendar {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        width: 320px !important;
        padding: 0.5rem;
    }
    
    .flatpickr-months {
        background: transparent;
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 0.5rem 0.25rem;
        margin-bottom: 0.25rem;
    }
    
    .flatpickr-month {
        color: #111827;
        height: 40px;
    }
    
    .flatpickr-current-month {
        color: #111827;
        font-weight: 600;
        font-size: 1rem;
        padding-top: 0.25rem;
    }
    
    .flatpickr-weekdays {
        background: transparent;
        border-bottom: none;
        padding: 0.25rem 0;
    }
    
    .flatpickr-weekday {
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
    }
    
    .flatpickr-day {
        color: #111827;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        height: 38px;
        line-height: 38px;
        margin: 2px;
        max-width: 38px;
        width: 38px;
        transition: all 0.15s ease;
    }
    
    .flatpickr-day:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }
    
    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        font-weight: 600;
    }
    
    .flatpickr-day.selected:hover,
    .flatpickr-day.startRange:hover,
    .flatpickr-day.endRange:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
    }
    
    .flatpickr-day.inRange {
        background: #dbeafe !important;
        border-color: #93c5fd !important;
        color: #1e40af !important;
    }
    
    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
        color: #d1d5db;
    }
    
    .flatpickr-day.today {
        border-color: #2563eb;
        font-weight: 600;
        background: transparent;
    }
    
    .flatpickr-day.today:hover {
        background: #f3f4f6;
    }
    
    .flatpickr-prev-month,
    .flatpickr-next-month {
        color: #6b7280;
        padding: 0.25rem;
        border-radius: 0.375rem;
    }
    
    .flatpickr-prev-month:hover,
    .flatpickr-next-month:hover {
        color: #2563eb;
        background: #f3f4f6;
    }
    
    .flatpickr-prev-month svg,
    .flatpickr-next-month svg {
        width: 14px;
        height: 14px;
    }

    /* Custom date styling - Clean orange for pending, red for approved */
    .flatpickr-day.occupied-pending {
        background: #fff6d4 !important;
        color: #92400e !important;
        border-color: #f7b441 !important;
        font-weight: 600;
        border-width: 1px;
    }
    
    .flatpickr-day.occupied-pending:hover {
        background: #fde68a !important;
        border-color: #d97706 !important;
    }
    
    .flatpickr-day.occupied-approved {
        background: #fecaca !important;
        color: #ff8d8d !important;
        border-color: #f64b4b !important;
        text-decoration: line-through;
        opacity: 0.8;
        border-width: 1px;
    }
    
    .flatpickr-day.occupied-approved:hover {
        background: #fca5a5 !important;
        border-color: #dc2626 !important;
    }
    
    .flatpickr-day.sunday-disabled {
        background: #f3f4f6 !important;
        color: #9ca3af !important;
        border-color: #e5e7eb !important;
        cursor: not-allowed !important;
        opacity: 0.6;
    }
    
    .flatpickr-day.sunday-disabled:hover {
        background: #f3f4f6 !important;
    }

    /* Modal overlay fix */
    .fixed {
        position: fixed;
    }
    
    .inset-0 {
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
    
    .z-50 {
        z-index: 50;
    }
</style>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data from PHP
    const dataElement = document.getElementById('leave-request-data');
    let availableDays = dataElement ? JSON.parse(dataElement.getAttribute('data-available-days') || '{}') : {};
    const shouldCheckBalance = dataElement ? JSON.parse(dataElement.getAttribute('data-should-check-balance') || 'false') : false;
    const employeeId = dataElement ? dataElement.getAttribute('data-employee-id') : '';
    const currentYear = dataElement ? dataElement.getAttribute('data-current-year') : new Date().getFullYear();
    
    // Store occupied dates with their status
    let occupiedDates = [];
    let pendingOverlaps = [];
    let approvedOverlaps = [];
    
    // Get form elements
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const daysRequestedInput = document.getElementById('days_requested');
    const leaveTypeSelect = document.getElementById('leave_type');
    const balanceWarning = document.getElementById('balanceWarning');
    const balanceInfo = document.getElementById('balanceInfo');
    const conflictWarning = document.getElementById('conflictWarning');
    const conflictMessage = document.getElementById('conflictMessage');
    const dateInfo = document.getElementById('dateInfo');
    const workingDaysCount = document.getElementById('workingDaysCount');
    const sundaysCount = document.getElementById('sundaysCount');
    const conflictingDates = document.getElementById('conflictingDates');
    const form = document.getElementById('leaveRequestForm');
    const submitBtn = document.getElementById('submitBtn');
    const overlapModal = document.getElementById('overlapModal');
    const overlapList = document.getElementById('overlapList');
    const replaceBtn = document.getElementById('replaceLeaveBtn');
    const cancelRequestBtn = document.getElementById('cancelRequestBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    
    let currentOverlaps = [];
    let selectedReplaceId = null;
    let hasPendingOverlap = false;
    let hasApprovedOverlap = false;
    
    const leaveTypeLabels = {
        'vacation': 'Vacation',
        'sick': 'Sick',
        'personal': 'Personal',
        'emergency': 'Emergency',
        'maternity': 'Maternity',
        'paternity': 'Paternity',
        'bereavement': 'Bereavement',
        'study': 'Study'
    };

    // Check if a date is a Sunday
    function isSunday(date) {
        return date.getDay() === 0;
    }

    // Get all dates between start and end (inclusive)
    function getDateRange(startDate, endDate) {
        const dates = [];
        const current = new Date(startDate);
        const end = new Date(endDate);
        
        while (current <= end) {
            dates.push(new Date(current));
            current.setDate(current.getDate() + 1);
        }
        
        return dates;
    }

    // Count working days (excluding Sundays)
    function countWorkingDays(startDate, endDate) {
        const allDates = getDateRange(startDate, endDate);
        const workingDates = allDates.filter(date => !isSunday(date));
        return {
            total: allDates.length,
            working: workingDates.length,
            sundays: allDates.length - workingDates.length,
            dates: workingDates,
            allDates: allDates
        };
    }

    // Check for conflicts with occupied dates
    function checkConflicts(startDate, endDate) {
        const allDates = getDateRange(startDate, endDate);
        const pendingConflicts = [];
        const approvedConflicts = [];
        
        allDates.forEach(date => {
            const dateStr = flatpickr.formatDate(date, "Y-m-d");
            const occupied = occupiedDates.find(o => o.date === dateStr);
            if (occupied) {
                if (occupied.status === 'pending') {
                    pendingConflicts.push({
                        date: dateStr,
                        leave_id: occupied.leave_id,
                        type: occupied.type
                    });
                } else if (occupied.status === 'approved') {
                    approvedConflicts.push({
                        date: dateStr,
                        leave_id: occupied.leave_id,
                        type: occupied.type
                    });
                }
            }
        });
        
        return {
            pending: pendingConflicts,
            approved: approvedConflicts
        };
    }

    // Get disabled dates array for flatpickr
    function getDisabledDates() {
        const disabled = [];
        
        // Add Sundays (day 0 = Sunday)
        disabled.push(function(date) {
            return date.getDay() === 0;
        });
        
        // Add occupied dates with approved status (cannot be selected)
        occupiedDates.forEach(item => {
            if (item.status === 'approved') {
                disabled.push(item.date);
            }
        });
        
        return disabled;
    }

    // Fetch occupied leave dates for the employee
    function fetchOccupiedDates(empId) {
        if (!empId) return;
        
        fetch(`/attendance/leave-management/balance?employee_id=${empId}&year=${currentYear}`)
            .then(response => response.json())
            .then(data => {
                if (data.available_days) {
                    availableDays = data.available_days;
                    updateBalanceDisplay(availableDays);
                }
                if (data.occupied_dates) {
                    occupiedDates = data.occupied_dates;
                    updateDatePickers();
                }
            })
            .catch(error => console.error('Error fetching balance:', error));
    }

    // Update date pickers with new disabled dates
    function updateDatePickers() {
        const disabledDates = getDisabledDates();
        if (startDatePicker) {
            startDatePicker.set('disable', disabledDates);
        }
        if (endDatePicker) {
            endDatePicker.set('disable', disabledDates);
        }
    }

    // Initialize Flatpickr for date inputs - Clean Design
    const startDatePicker = flatpickr("#start_date", {
        dateFormat: "Y-m-d",
        minDate: "today",
        disable: getDisabledDates(),
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                endDatePicker.set('minDate', dateStr);
                updateDateInfo();
            }
        },
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            const date = new Date(dayElem.dateObj);
            const dateStr = flatpickr.formatDate(date, "Y-m-d");
            
            if (date.getDay() === 0) {
                dayElem.classList.add('sunday-disabled');
                dayElem.title = 'Sundays are not counted as leave days';
            }
            
            const occupied = occupiedDates.find(o => o.date === dateStr);
            if (occupied) {
                if (occupied.status === 'pending') {
                    dayElem.classList.add('occupied-pending');
                    dayElem.title = `Pending leave on this date (${occupied.type})`;
                } else if (occupied.status === 'approved') {
                    dayElem.classList.add('occupied-approved');
                    dayElem.title = `Approved leave on this date (${occupied.type}) - Cannot select`;
                }
            }
        }
    });

    const endDatePicker = flatpickr("#end_date", {
        dateFormat: "Y-m-d",
        minDate: "today",
        disable: getDisabledDates(),
        onChange: function(selectedDates, dateStr, instance) {
            updateDateInfo();
        },
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            const date = new Date(dayElem.dateObj);
            const dateStr = flatpickr.formatDate(date, "Y-m-d");
            
            if (date.getDay() === 0) {
                dayElem.classList.add('sunday-disabled');
                dayElem.title = 'Sundays are not counted as leave days';
            }
            
            const occupied = occupiedDates.find(o => o.date === dateStr);
            if (occupied) {
                if (occupied.status === 'pending') {
                    dayElem.classList.add('occupied-pending');
                    dayElem.title = `Pending leave on this date (${occupied.type})`;
                } else if (occupied.status === 'approved') {
                    dayElem.classList.add('occupied-approved');
                    dayElem.title = `Approved leave on this date (${occupied.type}) - Cannot select`;
                }
            }
        }
    });

    // Update date info without showing modal
    function updateDateInfo() {
        const startDate = startDatePicker.selectedDates[0];
        const endDate = endDatePicker.selectedDates[0];

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            if (end >= start) {
                const result = countWorkingDays(start, end);
                const days = result.working;
                const sundays = result.sundays;
                
                daysRequestedInput.value = days;
                dateInfo.classList.remove('hidden');
                workingDaysCount.textContent = days;
                sundaysCount.textContent = sundays;
                
                // Check conflicts but don't show modal yet
                const conflictResult = checkConflicts(start, end);
                pendingOverlaps = conflictResult.pending;
                approvedOverlaps = conflictResult.approved;
                hasPendingOverlap = pendingOverlaps.length > 0;
                hasApprovedOverlap = approvedOverlaps.length > 0;
                
                if (hasApprovedOverlap) {
                    conflictWarning.classList.remove('hidden');
                    conflictMessage.textContent = `You have existing APPROVED leaves on: ${approvedOverlaps.map(o => o.date).join(', ')}.`;
                    conflictingDates.textContent = approvedOverlaps.map(o => o.date).join(', ');
                } else {
                    conflictWarning.classList.add('hidden');
                    if (hasPendingOverlap) {
                        conflictingDates.textContent = pendingOverlaps.map(o => o.date).join(', ');
                    } else {
                        conflictingDates.textContent = 'None';
                    }
                }
                
                // Check balance
                if (Object.keys(availableDays).length > 0) {
                    checkBalance(days);
                }
            } else {
                daysRequestedInput.value = '';
                dateInfo.classList.add('hidden');
                balanceWarning.classList.add('hidden');
                conflictWarning.classList.add('hidden');
            }
        } else {
            daysRequestedInput.value = '';
            dateInfo.classList.add('hidden');
            balanceWarning.classList.add('hidden');
            conflictWarning.classList.add('hidden');
        }
    }

    function checkBalance(days) {
        const leaveType = leaveTypeSelect.value;
        if (!leaveType || !availableDays[leaveType]) {
            balanceWarning.classList.add('hidden');
            return;
        }

        const available = availableDays[leaveType];
        if (days > available) {
            balanceWarning.classList.remove('hidden');
            balanceInfo.textContent = `Available ${leaveTypeLabels[leaveType]} Leave: ${available} days. Requested: ${days} days.`;
        } else {
            balanceWarning.classList.add('hidden');
        }
    }

    // Show overlap modal (only on submit)
    function showOverlapModal(overlaps) {
        currentOverlaps = overlaps;
        overlapList.innerHTML = '';
        
        overlaps.forEach((overlap, index) => {
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors';
            div.innerHTML = `
                <div>
                    <span class="font-medium text-gray-900">${leaveTypeLabels[overlap.type] || overlap.type}</span>
                    <span class="text-sm text-gray-500 ml-2">${overlap.date}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 ml-2">
                        pending
                    </span>
                </div>
                <div class="flex items-center">
                    <input type="radio" name="replace_leave_radio" value="${overlap.leave_id}" 
                           class="mr-2 replace-radio w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" 
                           ${index === 0 ? 'checked' : ''}>
                    <label class="text-sm text-gray-600 cursor-pointer">Replace this</label>
                </div>
            `;
            overlapList.appendChild(div);
        });
        
        overlapModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Set default selected
        const firstRadio = document.querySelector('.replace-radio');
        if (firstRadio) {
            firstRadio.checked = true;
            selectedReplaceId = firstRadio.value;
        }
        
        // Radio change handler
        document.querySelectorAll('.replace-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                selectedReplaceId = this.value;
            });
        });
    }

    // CLOSE MODAL FUNCTION - Used by both buttons and X
    function closeOverlapModal() {
        overlapModal.classList.add('hidden');
        document.body.style.overflow = '';
        currentOverlaps = [];
        selectedReplaceId = null;
    }

    // Replace button handler
    replaceBtn.addEventListener('click', function() {
        if (selectedReplaceId) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'replace_leave_id';
            input.value = selectedReplaceId;
            form.appendChild(input);
            
            closeOverlapModal();
            form.submit();
        } else {
            showValidationMessage('Please select a leave request to replace.', 'error');
        }
    });

    // CANCEL REQUEST BUTTON - Fixed
    cancelRequestBtn.addEventListener('click', function(e) {
        e.preventDefault();
        closeOverlapModal();
    });

    // CLOSE/X BUTTON - Fixed
    closeModalBtn.addEventListener('click', function(e) {
        e.preventDefault();
        closeOverlapModal();
    });

    // Close modal when clicking outside
    overlapModal.addEventListener('click', function(event) {
        if (event.target === overlapModal) {
            closeOverlapModal();
        }
    });

    // Update balance display
    function updateBalanceDisplay(days) {
        const balanceGrid = document.getElementById('balanceGrid');
        if (balanceGrid && Object.keys(days).length > 0) {
            const types = ['vacation', 'sick', 'personal', 'emergency'];
            types.forEach(type => {
                const el = document.getElementById(`balance-${type}`);
                if (el) {
                    el.textContent = `${days[type] || 0} days`;
                }
            });
        }
    }

    // Recalculate when leave type changes
    leaveTypeSelect.addEventListener('change', function() {
        if (startDatePicker.selectedDates[0] && endDatePicker.selectedDates[0]) {
            updateDateInfo();
        }
    });

    // Fetch occupied dates on load
    if (employeeId) {
        fetchOccupiedDates(employeeId);
    }

    // Show validation message like the reason error style
    function showValidationMessage(message, type) {
        // Remove existing validation messages
        const existing = document.querySelectorAll('.custom-validation-message');
        existing.forEach(el => el.remove());
        
        const div = document.createElement('div');
        div.className = 'custom-validation-message bg-red-50 border border-red-200 rounded-lg p-4 mb-4';
        div.innerHTML = `
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-600 mr-3 mt-0.5"></i>
                <p class="text-sm text-red-700">${message}</p>
            </div>
        `;
        
        // Insert after the form or before the submit button
        const form = document.getElementById('leaveRequestForm');
        const submitSection = form.querySelector('.flex.justify-end.space-x-3.pt-6');
        if (submitSection) {
            form.insertBefore(div, submitSection);
        } else {
            form.appendChild(div);
        }
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            div.remove();
        }, 5000);
    }

    // SUBMIT BUTTON HANDLER - Only shows modal on submit
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const days = parseInt(daysRequestedInput.value);
        const leaveType = leaveTypeSelect.value;
        const startDate = startDatePicker.selectedDates[0];
        const endDate = endDatePicker.selectedDates[0];
        const reason = document.getElementById('reason').value.trim();
        
        // Validate fields with styled messages
        if (!leaveType) {
            showValidationMessage('Please select a leave type.', 'error');
            return;
        }
        
        if (!startDate || !endDate) {
            showValidationMessage('Please select start and end dates.', 'error');
            return;
        }
        
        if (!reason) {
            showValidationMessage('Please provide a reason for your leave request.', 'error');
            return;
        }
        
        if (reason.length < 2) {
            showValidationMessage('Please provide a valid reason (at least 2 characters).', 'error');
            return;
        }
        
        if (!days || days <= 0) {
            showValidationMessage('Please select valid working days. Sundays are automatically excluded.', 'error');
            return;
        }
        
        // Check for approved conflicts
        if (hasApprovedOverlap) {
            showValidationMessage(`You have existing APPROVED leaves on: ${approvedOverlaps.map(o => o.date).join(', ')}. Please remove these dates from your request.`, 'error');
            return;
        }
        
        // Check balance
        if (shouldCheckBalance && leaveType && availableDays[leaveType]) {
            const available = availableDays[leaveType];
            if (days > available) {
                showValidationMessage(`Insufficient leave balance. Available: ${available} days, Requested: ${days} days.`, 'error');
                return;
            }
        }
        
        // If there are pending overlaps, show modal
        if (hasPendingOverlap) {
            // Fetch fresh overlap data
            const start = new Date(startDate);
            const end = new Date(endDate);
            const conflictResult = checkConflicts(start, end);
            
            if (conflictResult.pending.length > 0) {
                showOverlapModal(conflictResult.pending);
                return;
            }
        }
        
        // No overlaps - submit directly
        // Set date values
        startDateInput.value = startDatePicker.formatDate(startDate, 'Y-m-d');
        endDateInput.value = endDatePicker.formatDate(endDate, 'Y-m-d');
        
        // Submit the form
        form.submit();
    });
});

// HR/Admin: Load balance when employee changes
@if(in_array($user->role, ['admin', 'hr']))
document.addEventListener('DOMContentLoaded', function() {
    const employeeSelect = document.getElementById('employee_id');
    const leaveBalanceContainer = document.getElementById('leaveBalanceContainer');
    const balanceGrid = document.getElementById('balanceGrid');
    
    if (employeeSelect) {
        employeeSelect.addEventListener('change', function() {
            const empId = this.value;
            const currentYear = document.getElementById('leave-request-data')?.getAttribute('data-current-year') || new Date().getFullYear();
            
            if (empId) {
                balanceGrid.innerHTML = '<div class="col-span-4 text-center text-sm text-blue-700">Loading leave balance...</div>';
                leaveBalanceContainer.classList.remove('hidden');
                
                fetch(`/attendance/leave-management/balance?employee_id=${empId}&year=${currentYear}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.leave_balance && data.available_days) {
                            if (typeof availableDays !== 'undefined') {
                                availableDays = data.available_days;
                            }
                            
                            if (data.occupied_dates && typeof occupiedDates !== 'undefined') {
                                occupiedDates = data.occupied_dates;
                                if (typeof updateDatePickers === 'function') {
                                    updateDatePickers();
                                }
                            }
                            
                            balanceGrid.innerHTML = `
                                <div>
                                    <div class="text-xs text-blue-700">Vacation</div>
                                    <div class="text-sm font-semibold text-blue-900" id="balance-vacation">${data.available_days.vacation || 0} days</div>
                                </div>
                                <div>
                                    <div class="text-xs text-blue-700">Sick</div>
                                    <div class="text-sm font-semibold text-blue-900" id="balance-sick">${data.available_days.sick || 0} days</div>
                                </div>
                                <div>
                                    <div class="text-xs text-blue-700">Personal</div>
                                    <div class="text-sm font-semibold text-blue-900" id="balance-personal">${data.available_days.personal || 0} days</div>
                                </div>
                                <div>
                                    <div class="text-xs text-blue-700">Emergency</div>
                                    <div class="text-sm font-semibold text-blue-900" id="balance-emergency">${data.available_days.emergency || 0} days</div>
                                </div>
                            `;
                            
                            if (typeof updateDateInfo === 'function') {
                                updateDateInfo();
                            }
                        } else {
                            balanceGrid.innerHTML = '<div class="col-span-4 text-center text-sm text-red-600">No leave balance found for this employee.</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading leave balance:', error);
                        balanceGrid.innerHTML = '<div class="col-span-4 text-center text-sm text-red-600">Error loading leave balance.</div>';
                    });
            } else {
                leaveBalanceContainer.classList.add('hidden');
            }
        });
    }
});
@endif
</script>
@endsection