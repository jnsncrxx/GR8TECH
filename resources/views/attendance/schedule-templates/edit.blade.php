@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'schedule-templates.index'])

@section('title', 'Edit Schedule Template')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6">
            <a href="{{ route('schedule-templates.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-1"></i>Back to templates
            </a>
            <h1 class="text-2xl font-semibold text-gray-900 mt-2">Edit Schedule Template</h1>
        </div>

        @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('schedule-templates.update', $template) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" name="code" maxlength="20" value="{{ old('code', $template->code) }}"
                           class="w-full rounded-lg border-gray-300 uppercase focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Required Hours</label>
                    <input type="number" step="0.25" min="0" max="24" name="required_hours"
                           value="{{ old('required_hours', $template->required_hours) }}"
                           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $template->name) }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Type</label>
                <select name="schedule_type" id="schedule_type"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="fixed" {{ old('schedule_type', $template->schedule_type) === 'fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="flexible" {{ old('schedule_type', $template->schedule_type) === 'flexible' ? 'selected' : '' }}>Flexible</option>
                </select>
            </div>

            <div id="fixed_time_fields" class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time In</label>
                    <input type="time" name="time_in"
                           value="{{ old('time_in', $template->time_in ? \Carbon\Carbon::parse($template->time_in)->format('H:i') : '08:00') }}"
                           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Out</label>
                    <input type="time" name="time_out"
                           value="{{ old('time_out', $template->time_out ? \Carbon\Carbon::parse($template->time_out)->format('H:i') : '17:00') }}"
                           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('schedule-templates.index') }}"
                   class="px-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100">Cancel</a>
                <button type="submit"
                        class="inline-flex items-center px-5 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm text-sm">
                    <i class="fas fa-save mr-2"></i>Update Template
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFixedFields() {
    const type = document.getElementById('schedule_type').value;
    document.getElementById('fixed_time_fields').style.display = type === 'flexible' ? 'none' : 'grid';
}
document.getElementById('schedule_type').addEventListener('change', toggleFixedFields);
toggleFixedFields();
</script>
@endsection