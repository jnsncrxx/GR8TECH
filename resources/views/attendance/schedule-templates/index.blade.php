@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'schedule-templates.index'])

@section('title', 'Schedule Templates')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Schedule Templates</h1>
                <p class="text-sm text-gray-600 mt-1">Reusable shift codes you can apply when creating schedules</p>
            </div>
            <a href="{{ route('schedule-templates.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i>
                Add Template
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
        @endif

        @unless($currentCompany)
        <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm">
            <i class="fas fa-info-circle mr-2"></i>No company is currently selected - templates created now will only apply when no company is selected. Pick a company from the top bar to manage that company's templates.
        </div>
        @endunless

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($templates->isEmpty())
            <div class="text-center py-16">
                <i class="fas fa-clock text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No schedule templates yet.</p>
                <a href="{{ route('schedule-templates.create') }}" class="inline-flex items-center mt-3 text-blue-600 hover:text-blue-700 text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i>Create your first template
                </a>
            </div>
            @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">In Use</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($templates as $template)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-100 text-blue-800">
                                {{ $template->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $template->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">{{ $template->schedule_type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $template->window_label }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $template->employeeSchedules()->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-3">
                            <a href="{{ route('schedule-templates.edit', $template) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" onclick="confirmDeleteTemplate('{{ $template->id }}', '{{ $template->code }}')" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="delete-template-{{ $template->id }}" action="{{ route('schedule-templates.destroy', $template) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

<script>
function confirmDeleteTemplate(id, code) {
    if (confirm(`Delete schedule template "${code}"? This can't be undone.`)) {
        document.getElementById('delete-template-' + id).submit();
    }
}
</script>
@endsection