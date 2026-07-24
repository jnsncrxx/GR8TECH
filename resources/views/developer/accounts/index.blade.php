@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => $activeRoute])

@section('content')
<div class="w-full px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">User Account Management</h1>
        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Add Account
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Login</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($accounts as $account)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $account->employee->full_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $account->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($account->role === 'admin') bg-red-100 text-red-800
                            @elseif($account->role === 'hr') bg-blue-100 text-blue-800
                            @elseif($account->role === 'manager') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($account->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($account->is_active) bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $account->formatted_last_login ?? 'Never' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('developer.accounts.edit', $account) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="openDeleteModal('{{ $account->id }}', '{{ $account->email }}', '{{ $account->employee->full_name ?? 'N/A' }}')" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No accounts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h2 class="text-xl font-bold mb-4">Create Account</h2>
        <form action="{{ route('developer.accounts.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Employee (Optional)</label>
                <select name="employee_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">None</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email *</label>
                <input type="email" name="email" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password *</label>
                <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Role *</label>
                <select name="role" required class="w-full border rounded-lg px-3 py-2">
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="hr">HR</option>
                    <option value="manager">Manager</option>
                    <option value="employee">Employee</option>
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 bg-gray-300 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            
            <h2 class="text-xl font-bold text-gray-900 mb-2">Delete Account</h2>
            
            <p class="text-gray-600 mb-4">
                Are you sure you want to delete this account?
            </p>
            
            <!-- Account Details -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4 text-left">
                <p class="text-sm font-medium text-gray-700">Account Details:</p>
                <p class="text-sm text-gray-600" id="deleteAccountEmail">-</p>
                <p class="text-sm text-gray-600" id="deleteAccountEmployee">-</p>
            </div>
            
            <p class="text-sm text-red-500 mb-4">
                <i class="fas fa-exclamation-circle mr-1"></i>
                This action cannot be undone.
            </p>
            
            <div class="flex justify-center space-x-3">
                <button onclick="closeModal('deleteModal')" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition">
                    Cancel
                </button>
                <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
let deleteAccountId = null;

function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function openDeleteModal(id, email, employee) {
    deleteAccountId = id;
    document.getElementById('deleteAccountEmail').textContent = '📧 ' + email;
    document.getElementById('deleteAccountEmployee').textContent = '👤 ' + employee;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function confirmDelete() {
    if (deleteAccountId) {
        const form = document.getElementById('deleteForm');
        form.action = `/developer/accounts/${deleteAccountId}`;
        form.submit();
    }
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    deleteAccountId = null;
}

// Event listener for confirm delete button
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);
});
</script>
@endsection