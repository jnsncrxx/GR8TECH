@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'schedule-v2.index'])

@section('title', 'Edit Schedule')

@section('content')
<div class="schedule-management-page min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Schedule</h1>
                        <p class="mt-1 text-sm text-gray-600">Update work schedule for {{ $schedule->employee->full_name }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('schedule-v2.show', $schedule) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-eye mr-2"></i>
                            View Schedule
                        </a>
                        <a href="{{ isset($currentFilters) ? route('schedule-v2.index', array_filter($currentFilters)) : route('schedule-v2.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Schedules
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form id="scheduleForm" action="{{ route('schedule-v2.update', $schedule) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Hidden inputs to preserve filter state -->
                @if(isset($currentFilters))
                    @foreach($currentFilters as $key => $value)
                        @if($value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                @endif
                
                <!-- Employee Info (Read-only) -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-lg font-medium text-blue-600">
                                    {{ substr($schedule->employee->first_name, 0, 1) }}{{ substr($schedule->employee->last_name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $schedule->employee->full_name }}</h3>
                            <p class="text-sm text-gray-600">{{ $schedule->employee->position?->name ?? 'N/A' }} - {{ $schedule->employee->department?->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $schedule->date->format('l, F j, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Schedule Template -->
                <div>
                    <label for="schedule_template_id" class="block text-sm font-medium text-gray-700 mb-2">Schedule Template</label>
                    <select name="schedule_template_id" id="schedule_template_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('schedule_template_id') border-red-500 @enderror">
                        <option value="">Custom schedule (no template)</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" {{ (string) old('schedule_template_id', $schedule->schedule_template_id) === (string) $template->id ? 'selected' : '' }}>
                                {{ $template->code }} — {{ $template->name }} ({{ $template->window_label }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Selecting a template fills its schedule type and hours. Choose custom schedule to enter hours manually.</p>
                    @error('schedule_template_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                        <option value="">Select status</option>
                        <option value="Working" {{ old('status', $schedule->status) == 'Working' ? 'selected' : '' }}>Scheduled Workday</option>
                        <option value="Day Off" {{ old('status', $schedule->status) == 'Day Off' ? 'selected' : '' }}>Day Off</option>
                        <option value="Leave" {{ old('status', $schedule->status) == 'Leave' ? 'selected' : '' }}>Leave</option>
                        <option value="Official Business" {{ old('status', $schedule->status) == 'Official Business' ? 'selected' : '' }}>Official Business</option>
                        <option value="Absent" {{ old('status', $schedule->status) == 'Absent' ? 'selected' : '' }}>Absent</option>
                        <option value="Regular Holiday" {{ old('status', $schedule->status) == 'Regular Holiday' ? 'selected' : '' }}>Regular Holiday</option>
                        <option value="Special Holiday" {{ old('status', $schedule->status) == 'Special Holiday' ? 'selected' : '' }}>Special Holiday</option>
                        <option value="Overtime" {{ old('status', $schedule->status) == 'Overtime' ? 'selected' : '' }}>Overtime</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="schedule_type" class="block text-sm font-medium text-gray-700 mb-2">Schedule Type</label>
                    <select name="schedule_type" id="schedule_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('schedule_type') border-red-500 @enderror">
                        <option value="fixed" {{ old('schedule_type', $schedule->schedule_type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed hours</option>
                        <option value="flexible" {{ old('schedule_type', $schedule->schedule_type) === 'flexible' ? 'selected' : '' }}>Flexible hours</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Fixed hours are editable. Flexible schedules are evaluated using required hours.</p>
                </div>
                <div id="timeFields" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="time_in" class="block text-sm font-medium text-gray-700 mb-2">Time In</label>
                        <input type="time" name="time_in" id="time_in" value="{{ old('time_in', $schedule->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_in)->format('H:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('time_in') border-red-500 @enderror">
                        @error('time_in')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="time_out" class="block text-sm font-medium text-gray-700 mb-2">Time Out</label>
                        <input type="time" name="time_out" id="time_out" value="{{ old('time_out', $schedule->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_out)->format('H:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('time_out') border-red-500 @enderror">
                        @error('time_out')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div id="requiredHoursField" style="display: none;">
                    <label for="required_hours" class="block text-sm font-medium text-gray-700 mb-2">Required Hours</label>
                    <input type="number" name="required_hours" id="required_hours" min="1" max="24" step="0.25" value="{{ old('required_hours', $schedule->required_hours ?: 8) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('required_hours') border-red-500 @enderror">
                    @error('required_hours')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror" placeholder="Optional notes about this schedule...">{{ old('notes', $schedule->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Schedule Info -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Schedule Information</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-blue-800">Created:</span>
                            <span class="text-blue-700">{{ $schedule->created_at->format('M j, Y g:i A') }}</span>
                        </div>
                        @if($schedule->creator)
                            <div>
                                <span class="font-medium text-blue-800">Created by:</span>
                                <span class="text-blue-700">{{ $schedule->creator->full_name }}</span>
                            </div>
                        @endif
                        @if($schedule->time_in && $schedule->time_out)
                            <div>
                                <span class="font-medium text-blue-800">Working Hours:</span>
                                <span class="text-blue-700">{{ $schedule->working_hours }} hours</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <!-- Delete Button (left side) -->
                    <button type="button" id="deleteBtn" class="px-4 py-2 bg-red-600 border border-transparent rounded-lg font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Schedule
                    </button>
                    
                    <!-- Update and Cancel Buttons (right side) -->
                    <div class="flex space-x-3">
                        <a href="{{ isset($currentFilters) ? route('schedule-v2.index', array_filter($currentFilters)) : route('schedule-v2.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" id="updateBtn" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Update Schedule
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Schedule template data, keyed by id, for auto-filling schedule fields on selection
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
    const scheduleTemplates = {!! $scheduleTemplatesJson !!};

    document.getElementById('schedule_template_id').addEventListener('change', function() {
        const template = scheduleTemplates[this.value];
        if (!template) {
            return;
        }
        document.getElementById('schedule_type').value = template.schedule_type;
        document.getElementById('time_in').value = template.time_in;
        document.getElementById('time_out').value = template.time_out;
        syncScheduleFields();
    });

// Handle form actions dynamically and initialize
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scheduleForm');
    const updateBtn = document.getElementById('updateBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const statusSelect = document.getElementById('status');
    
    syncScheduleFields();
    
    // Delete button - set form to delete action
    deleteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (confirm('Are you sure you want to delete this schedule?')) {
            form.action = '{{ route("schedule-v2.destroy", $schedule) }}';
            form.method = 'POST';
            form.querySelector('input[name="_method"]').value = 'DELETE';
            
            form.submit();
        }
    });
});

function syncScheduleFields() {
    const statusField = document.getElementById('status');
    const scheduleTypeField = document.getElementById('schedule_type');
    const timeFields = document.getElementById('timeFields');
    const timeInField = document.getElementById('time_in');
    const timeOutField = document.getElementById('time_out');
    const requiredHoursField = document.getElementById('requiredHoursField');
    const requiredHoursInput = document.getElementById('required_hours');
    const isWorkSchedule = statusField.value === 'Working' || statusField.value === 'Overtime';
    const isFlexible = scheduleTypeField.value === 'flexible';

    if (isWorkSchedule && !isFlexible) {
        timeFields.style.display = 'grid';
        requiredHoursField.style.display = 'none';
        timeInField.required = true;
        timeOutField.required = true;
        requiredHoursInput.required = false;
        if (!timeInField.value) timeInField.value = '08:00';
        if (!timeOutField.value) timeOutField.value = '17:00';
    } else if (isWorkSchedule && isFlexible) {
        timeFields.style.display = 'none';
        requiredHoursField.style.display = 'block';
        timeInField.required = false;
        timeOutField.required = false;
        requiredHoursInput.required = true;
    } else {
        timeFields.style.display = 'none';
        requiredHoursField.style.display = 'none';
        timeInField.required = false;
        timeOutField.required = false;
        requiredHoursInput.required = false;
    }
}

document.getElementById('status').addEventListener('change', syncScheduleFields);
document.getElementById('schedule_type').addEventListener('change', syncScheduleFields);

</script>
@endsection
