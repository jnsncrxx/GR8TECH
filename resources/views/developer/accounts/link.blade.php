@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => $activeRoute])

@section('content')
<div class="w-full px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Link Account to Employee</h1>
        <a href="{{ route('developer.accounts.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Back to Accounts
        </a>
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

    <!-- Link Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Create a New Link</h2>
        <p class="text-sm text-gray-500 mb-4">Every user account can be linked to exactly one employee record, and every employee record can be linked to exactly one account.</p>
        <form action="{{ route('developer.accounts.link.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Unlinked Account *</label>
                <select name="account_id" required class="w-full border rounded-lg px-3 py-2">
                    <option value="">Select account</option>
                    @foreach($unlinkedAccounts as $account)
                        <option value="{{ $account->id }}">{{ $account->email }} ({{ ucfirst($account->role) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Unlinked Employee *</label>
                <select name="employee_id" required class="w-full border rounded-lg px-3 py-2">
                    <option value="">Select employee</option>
                    @foreach($unlinkedEmployees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-link mr-2"></i> Link
                </button>
            </div>
        </form>
        @if($unlinkedAccounts->isEmpty() || $unlinkedEmployees->isEmpty())
            <p class="text-sm text-gray-500 mt-3">
                @if($unlinkedAccounts->isEmpty())
                    There are no unlinked accounts available.
                @endif
                @if($unlinkedEmployees->isEmpty())
                    There are no unlinked employees available.
                @endif
            </p>
        @endif
    </div>

    <!-- Existing Links -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">Current Links</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Linked Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($linkedAccounts as $account)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $account->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($account->role) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $account->employee->full_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form action="{{ route('developer.accounts.unlink', $account) }}" method="POST" onsubmit="return confirm('Unlink this account from its employee record?')">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-unlink mr-1"></i> Unlink
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No linked accounts yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
