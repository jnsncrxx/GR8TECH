@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'payroll-templates.index'])

@section('title', 'Payroll Templates')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Payroll Templates</h1>
            <p class="mt-1 text-sm text-gray-600">Manage payroll templates for overriding standard calculations.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('payroll-templates.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create Template
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div class="flex items-center space-x-2 border-b border-gray-200 w-full sm:w-auto">
            <a href="{{ route('payroll-templates.index') }}" 
               class="px-4 py-2 border-b-2 font-medium text-sm {{ !request('status') || request('status') !== 'archived' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
               Active Templates
            </a>
            <a href="{{ route('payroll-templates.index', ['status' => 'archived']) }}" 
               class="px-4 py-2 border-b-2 font-medium text-sm {{ request('status') === 'archived' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
               Archived Templates
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <!-- Templates List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($templates as $template)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $template->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($template->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($template->trashed())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Archived</span>
                            @elseif($template->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($template->trashed())
                                <button type="button" onclick="openRestoreModal('{{ $template->id }}', '{{ addslashes($template->name) }}')" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-undo"></i> Restore
                                </button>
                            @else
                                <a href="{{ route('payroll-templates.edit', $template) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" onclick="openDeleteModal('{{ $template->id }}', '{{ addslashes($template->name) }}')" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-archive"></i> Archive
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            No payroll templates found. <a href="{{ route('payroll-templates.create') }}" class="text-blue-600 hover:text-blue-800">Create one</a> to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($templates->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $templates->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeDeleteModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <!-- Modal header -->
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>

            <!-- Modal content -->
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Archive Template</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Are you sure you want to archive <span id="deleteTemplateName" class="font-semibold text-gray-900"></span>? 
                    This will hide the template from active lists, but it can be restored if needed.
                </p>
            </div>

            <!-- Modal actions -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-3">
                <button type="button" onclick="closeDeleteModal()" 
                    class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        Archive Template
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div id="restoreModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeRestoreModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <!-- Modal header -->
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-green-100 rounded-full">
                <i class="fas fa-undo text-green-600 text-xl"></i>
            </div>

            <!-- Modal content -->
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Restore Template</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Are you sure you want to restore <span id="restoreTemplateName" class="font-semibold text-gray-900"></span>? 
                    This will move the template back to the active list, allowing it to be assigned to employees again.
                </p>
            </div>

            <!-- Modal actions -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-3">
                <button type="button" onclick="closeRestoreModal()" 
                    class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    Cancel
                </button>
                <form id="restoreForm" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" 
                        class="w-full px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Restore Template
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Delete Modal Functions
    function openDeleteModal(templateId, templateName) {
        document.getElementById('deleteTemplateName').textContent = templateName;
        document.getElementById('deleteForm').action = `/payroll-templates/${templateId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Restore Modal Functions
    function openRestoreModal(templateId, templateName) {
        document.getElementById('restoreTemplateName').textContent = templateName;
        document.getElementById('restoreForm').action = `/payroll-templates/${templateId}/restore`;
        document.getElementById('restoreModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRestoreModal() {
        document.getElementById('restoreModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modals when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    document.getElementById('restoreModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRestoreModal();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
            closeRestoreModal();
        }
    });
</script>
@endsection
