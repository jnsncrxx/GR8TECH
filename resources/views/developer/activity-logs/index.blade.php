@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => $activeRoute])

@section('content')
<div class="w-full px-4 py-8">
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-start space-x-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mt-0.5">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Activity Logs</h1>
                <p class="text-sm text-gray-500">Full audit trail — every login, record change, and deletion</p>
            </div>
        </div>
        <a href="{{ route('developer.activity-logs.export', request()->query()) }}"
           class="flex items-center space-x-2 border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fas fa-download"></i>
            <span>Export CSV</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
        <form method="GET" action="{{ route('developer.activity-logs.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <div class="lg:col-span-1">
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Staff ID, description..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Staff</label>
                    <select name="account_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Staff</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->employee->full_name ?? $account->email }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Action</label>
                    <select name="action" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Module</label>
                    <select name="module" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center space-x-2 mt-3">
                <button type="submit" class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('developer.activity-logs.index') }}" class="flex items-center space-x-2 border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-times"></i>
                    <span>Clear</span>
                </a>
            </div>
        </form>
    </div>

    <p class="text-sm text-gray-500 mb-3">
        Showing <span class="font-semibold text-gray-700">{{ $logs->count() }}</span> of
        <span class="font-semibold text-gray-700">{{ $logs->total() }}</span> log entries
    </p>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Date / Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Staff</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Module</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">IP Address</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        @php
                            $rowNumber = $logs->total() - $logs->firstItem() + 1 - $loop->index;
                            $account = $log->account;
                            $role = $account->role ?? null;
                            $roleClasses = match ($role) {
                                'admin' => 'bg-red-100 text-red-800',
                                'hr' => 'bg-blue-100 text-blue-800',
                                'manager' => 'bg-yellow-100 text-yellow-800',
                                'employee' => 'bg-gray-100 text-gray-800',
                                default => 'bg-gray-100 text-gray-600',
                            };
                            $moduleIcon = match ($log->module) {
                                'Authentication' => 'fa-shield-halved',
                                'Account' => 'fa-user-cog',
                                default => 'fa-folder',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 align-top text-sm text-gray-400">{{ $rowNumber }}</td>
                            <td class="px-4 py-4 align-top whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-800">{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $log->created_at->format('g:i:s A') }}</div>
                            </td>
                            <td class="px-4 py-4 align-top whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-800">
                                    {{ $log->actor_name ?? $account?->employee?->full_name ?? $account?->email ?? 'System' }}
                                </div>
                                <div class="flex items-center space-x-2 mt-0.5">
                                    <span class="text-xs text-gray-400">{{ $account?->employee?->employee_id ?? '—' }}</span>
                                    @if($role)
                                        <span class="px-2 py-0.5 text-[11px] font-medium rounded-full {{ $roleClasses }}">{{ ucfirst($role) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 align-top whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($log->action === 'delete') bg-red-100 text-red-800
                                    @elseif($log->action === 'create') bg-green-100 text-green-800
                                    @elseif($log->action === 'update') bg-yellow-100 text-yellow-800
                                    @elseif($log->action === 'restore') bg-purple-100 text-purple-800
                                    @elseif($log->action === 'login') bg-green-100 text-green-800
                                    @elseif($log->action === 'logout') bg-gray-200 text-gray-700
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 align-top whitespace-nowrap">
                                <span class="inline-flex items-center space-x-1.5 text-sm text-gray-600">
                                    <i class="fas {{ $moduleIcon }} text-blue-400 text-xs"></i>
                                    <span>{{ $log->module }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-4 align-top text-sm text-gray-600 max-w-md">{{ $log->description }}</td>
                            <td class="px-4 py-4 align-top whitespace-nowrap text-sm text-gray-400 font-mono">{{ $log->ip_address ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No activity logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
