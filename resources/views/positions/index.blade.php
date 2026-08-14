@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'positions.index'])

@section('title', 'Positions')

@section('content')
<x-page-header
    title="Positions"
    description="Manage active and archived company positions"
    :actions="[
        ['type' => 'link', 'label' => 'Add Position', 'href' => route('positions.create'), 'icon' => 'plus', 'variant' => 'primary']
    ]"
>
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-briefcase text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Positions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalPositions }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Positions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activePositions }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-archive text-gray-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Archived Positions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $archivedPositions }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-layer-group text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Senior Level</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $seniorPositions }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm font-medium text-gray-700 mr-2">Show:</span>

            <a href="{{ route('positions.index', ['status' => 'active']) }}"
               class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border
                    {{ $status === 'active'
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                <i class="fas fa-check-circle mr-2"></i>
                Active
            </a>

            <a href="{{ route('positions.index', ['status' => 'archived']) }}"
               class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border
                    {{ $status === 'archived'
                        ? 'bg-gray-700 text-white border-gray-700'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                <i class="fas fa-archive mr-2"></i>
                Archived
            </a>

            <a href="{{ route('positions.index', ['status' => 'all']) }}"
               class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border
                    {{ $status === 'all'
                        ? 'bg-indigo-600 text-white border-indigo-600'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                <i class="fas fa-list mr-2"></i>
                All
            </a>
        </div>
    </div>

    <!-- Positions Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $status === 'archived' ? 'Archived Positions' : ($status === 'all' ? 'All Positions' : 'Active Positions') }}
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Archived positions remain connected to existing employee and historical records.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Employees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($positions as $position)
                        <tr class="{{ $position->is_active ? 'hover:bg-gray-50' : 'bg-gray-50 hover:bg-gray-100' }}" data-search-row="{{ $position->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg {{ $position->is_active ? 'bg-blue-100' : 'bg-gray-200' }} flex items-center justify-center">
                                            <i class="fas fa-briefcase {{ $position->is_active ? 'text-blue-600' : 'text-gray-500' }}"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium {{ $position->is_active ? 'text-gray-900' : 'text-gray-600' }}">
                                            {{ $position->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $position->code }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $position->department->name ?? 'No Department' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $position->level === 'Senior' ? 'bg-purple-100 text-purple-800' :
                                       ($position->level === 'Mid' ? 'bg-blue-100 text-blue-800' :
                                       ($position->level === 'Lead' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ $position->level }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $position->employees_count }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($position->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                                        <i class="fas fa-archive mr-1"></i>
                                        Archived
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('positions.show', $position) }}"
                                       title="View"
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($position->is_active)
                                        <a href="{{ route('positions.edit', $position) }}"
                                           title="Edit"
                                           class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button"
                                                title="Archive"
                                                class="text-red-600 hover:text-red-900"
                                                onclick="openArchivePositionModal(
                                                    @js($position->id),
                                                    @js($position->name),
                                                    {{ $position->employees_count }}
                                                )">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    @else
                                        <button type="button"
                                                title="Restore"
                                                class="text-green-600 hover:text-green-900"
                                                onclick="openRestorePositionModal(
                                                    @js($position->id),
                                                    @js($position->name)
                                                )">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                @if($status === 'archived')
                                    No archived positions found.
                                @elseif($status === 'active')
                                    No active positions found.
                                @else
                                    No positions found.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($positions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $positions->links() }}
            </div>
        @endif
    </div>
</x-page-header>

<!-- Archive Position Modal -->
<div id="archivePositionModal"
     class="fixed inset-0 z-50 hidden overflow-y-auto"
     aria-labelledby="archivePositionTitle"
     role="dialog"
     aria-modal="true">
    <div class="flex min-h-screen items-center justify-center px-4 py-6">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
             onclick="closeArchivePositionModal()"></div>

        <div class="relative w-full max-w-md transform overflow-hidden rounded-xl bg-white shadow-xl">
            <div class="px-6 pt-6 pb-4">
                <div class="flex items-start">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                        <i class="fas fa-archive text-red-600"></i>
                    </div>

                    <div class="ml-4">
                        <h3 id="archivePositionTitle" class="text-lg font-semibold text-gray-900">
                            Archive Position
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Are you sure you want to archive
                            <strong id="archivePositionName"></strong>?
                        </p>
                    </div>
                </div>

                <div class="mt-5 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                    <p class="text-sm text-yellow-800">
                        The position will no longer appear in active position selections.
                        Existing employees and historical records will remain connected.
                    </p>
                    <p id="archiveEmployeeWarning" class="mt-2 hidden text-sm font-medium text-yellow-900"></p>
                </div>
            </div>

            <div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
                <button type="button"
                        onclick="closeArchivePositionModal()"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>

                <form id="archivePositionForm" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                        <i class="fas fa-archive mr-2"></i>
                        Archive Position
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Restore Position Modal -->
<div id="restorePositionModal"
     class="fixed inset-0 z-50 hidden overflow-y-auto"
     aria-labelledby="restorePositionTitle"
     role="dialog"
     aria-modal="true">
    <div class="flex min-h-screen items-center justify-center px-4 py-6">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
             onclick="closeRestorePositionModal()"></div>

        <div class="relative w-full max-w-md transform overflow-hidden rounded-xl bg-white shadow-xl">
            <div class="px-6 pt-6 pb-4">
                <div class="flex items-start">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100">
                        <i class="fas fa-undo text-green-600"></i>
                    </div>

                    <div class="ml-4">
                        <h3 id="restorePositionTitle" class="text-lg font-semibold text-gray-900">
                            Restore Position
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Restore <strong id="restorePositionName"></strong>?
                        </p>
                        <p class="mt-2 text-sm text-gray-500">
                            It will become active and available for employee assignment again.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
                <button type="button"
                        onclick="closeRestorePositionModal()"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>

                <form id="restorePositionForm" method="POST">
                    @csrf

                    <button type="submit"
                            class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                        <i class="fas fa-undo mr-2"></i>
                        Restore Position
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const archiveRouteTemplate = @json(route('positions.destroy', ['position' => '__POSITION__']));
    const restoreRouteTemplate = @json(route('positions.restore', ['position' => '__POSITION__']));

    function openArchivePositionModal(positionId, positionName, employeeCount) {
        document.getElementById('archivePositionName').textContent = positionName;
        document.getElementById('archivePositionForm').action =
            archiveRouteTemplate.replace('__POSITION__', positionId);

        const warning = document.getElementById('archiveEmployeeWarning');

        if (employeeCount > 0) {
            warning.textContent =
                employeeCount + (employeeCount === 1 ? ' employee is' : ' employees are') +
                ' currently assigned. Their position assignment will remain unchanged.';
            warning.classList.remove('hidden');
        } else {
            warning.textContent = '';
            warning.classList.add('hidden');
        }

        document.getElementById('archivePositionModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeArchivePositionModal() {
        document.getElementById('archivePositionModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openRestorePositionModal(positionId, positionName) {
        document.getElementById('restorePositionName').textContent = positionName;
        document.getElementById('restorePositionForm').action =
            restoreRouteTemplate.replace('__POSITION__', positionId);

        document.getElementById('restorePositionModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeRestorePositionModal() {
        document.getElementById('restorePositionModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeArchivePositionModal();
            closeRestorePositionModal();
        }
    });
</script>
@endsection