@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'departments.index'])

@section('title', 'Departments')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Departments</h1>
            <p class="mt-1 text-sm text-gray-600">Manage company departments and their information</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('departments.archived') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-box-archive mr-2"></i>
                Archived Departments
            </a>
            <a href="{{ route('departments.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Add Department
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
            <div class="flex-1">
                <input type="text" id="searchInput" placeholder="Search departments..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
            </div>
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 lg:flex-shrink-0">
                <select id="locationFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    <option value="">All Locations</option>
                    <option value="Main Office - Floor 1">Main Office - Floor 1</option>
                    <option value="Main Office - Floor 2">Main Office - Floor 2</option>
                    <option value="Main Office - Floor 3">Main Office - Floor 3</option>
                </select>
                <button type="button" onclick="clearFilters()" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-1"></i>Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Departments Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($departments as $department)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow department-card flex flex-col"
             data-name="{{ strtolower($department->name) }}"
             data-location="{{ $department->location }}">
            <!-- Department Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $department->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $department->department_id }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-users mr-1"></i>
                            {{ $department->employees_count }} {{ \Illuminate\Support\Str::plural('employee', $department->employees_count) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Department Details -->
            <div class="p-6 space-y-4 flex-1">
                <!-- Supervisor -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Department Head</h4>
                    <p class="text-sm text-gray-600 flex items-center">
                        <i class="fas fa-user-tag mr-2 text-gray-400"></i>
                        {{ $department->supervisor?->full_name ?: 'Not assigned' }}
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Description</h4>
                    <p class="text-sm text-gray-600">{{ $department->description ?: 'No description provided' }}</p>
                </div>

                <!-- Location -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Location</h4>
                    <p class="text-sm text-gray-600 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                        {{ $department->location ?: 'Not specified' }}
                    </p>
                </div>

                <!-- Budget -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Annual Budget</h4>
                    <p class="text-lg font-semibold text-gray-900">₱{{ number_format($department->budget, 2) }}</p>
                </div>
            </div>

            <!-- Department Actions - Fixed at Bottom -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 mt-auto">
                <div class="flex items-center justify-between">
                    <div class="flex space-x-2">
                        <a href="{{ route('departments.show', $department) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-900 transition-colors">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('departments.edit', $department) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-900 transition-colors">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <a href="{{ route('departments.employees', $department) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 hover:text-green-900 transition-colors">
                            <i class="fas fa-users mr-1"></i>Employees
                        </a>
                    </div>
                    <button type="button" onclick="openRemoveModal('{{ $department->id }}', '{{ $department->name }}', {{ $department->employees_count }})" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-900 transition-colors">
                        <i class="fas fa-box-archive mr-1"></i>Remove
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-building text-4xl mb-4"></i>
                    <p class="text-lg font-medium">No departments found</p>
                    <p class="text-sm">Get started by creating your first department.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($departments->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 rounded-lg">
        {{ $departments->links() }}
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const locationFilter = document.getElementById('locationFilter');
    const departmentCards = document.querySelectorAll('.department-card');

    function filterDepartments() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedLocation = locationFilter.value;

        departmentCards.forEach(card => {
            const name = card.dataset.name || '';
            const location = card.dataset.location || '';

            // Check search term
            const matchesSearch = searchTerm === '' || name.includes(searchTerm);

            // Check location filter
            const matchesLocation = selectedLocation === '' || location === selectedLocation;

            // Show/hide card based on all filters
            if (matchesSearch && matchesLocation) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        // Update "No departments found" message
        updateNoResultsMessage();
    }

    function updateNoResultsMessage() {
        const visibleCards = Array.from(departmentCards).filter(card => card.style.display !== 'none');
        const noResultsMessage = document.querySelector('.no-results-message');

        if (visibleCards.length === 0) {
            if (!noResultsMessage) {
                // Create no results message
                const messageDiv = document.createElement('div');
                messageDiv.className = 'no-results-message col-span-full';
                messageDiv.innerHTML = `
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                        <div class="text-gray-500">
                            <i class="fas fa-search text-4xl mb-4"></i>
                            <p class="text-lg font-medium">No departments found</p>
                            <p class="text-sm">Try adjusting your search or filters.</p>
                        </div>
                    </div>
                `;

                // Insert after the grid
                const grid = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3.gap-6');
                grid.appendChild(messageDiv);
            }
        } else {
            if (noResultsMessage) {
                noResultsMessage.remove();
            }
        }
    }

    // Add event listeners
    searchInput.addEventListener('input', filterDepartments);
    locationFilter.addEventListener('change', filterDepartments);
});

// Clear filters function
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('locationFilter').value = '';

    // Trigger filter function
    const event = new Event('input');
    document.getElementById('searchInput').dispatchEvent(event);
}

// Remove Modal Functions
function openRemoveModal(departmentId, departmentName, employeesCount) {
    document.getElementById('removeDepartmentId').value = departmentId;
    document.getElementById('removeDepartmentName').textContent = departmentName;
    document.getElementById('removeForm').action = `/departments/${departmentId}`;

    const warning = document.getElementById('removeActiveEmployeesWarning');
    if (employeesCount > 0) {
        document.getElementById('removeActiveEmployeesCount').textContent = employeesCount;
        warning.classList.remove('hidden');
    } else {
        warning.classList.add('hidden');
    }

    document.getElementById('removeModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRemoveModal() {
    document.getElementById('removeModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('removeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRemoveModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRemoveModal();
    }
});
</script>

<!-- Remove Confirmation Modal -->
<div id="removeModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeRemoveModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <!-- Modal header -->
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>

            <!-- Modal content -->
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Remove Department</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Are you sure you want to remove <span id="removeDepartmentName" class="font-semibold text-gray-900"></span>?
                    It will be archived and hidden from the active departments list.
                </p>
                <div id="removeActiveEmployeesWarning" class="hidden mb-6 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-left">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        This department still has <span id="removeActiveEmployeesCount" class="font-semibold"></span> active employee(s) assigned to it.
                        Are you sure you want to remove it anyway?
                    </p>
                </div>
            </div>

            <!-- Modal actions -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-3">
                <button type="button" onclick="closeRemoveModal()"
                    class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    Cancel
                </button>
                <form id="removeForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="removeDepartmentId" name="department_id" value="">
                    <button type="submit"
                        class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        Remove Department
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
