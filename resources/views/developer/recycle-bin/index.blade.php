@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => $activeRoute])

@section('content')
<div class="w-full px-4 py-8" x-data="{ tab: '{{ array_key_first($groups) }}' }">
    <div class="flex items-start space-x-3 mb-6">
        <div class="w-10 h-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center mt-0.5">
            <i class="fas fa-trash-can"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Recycle Bin</h1>
            <p class="text-sm text-gray-500">Restore or permanently delete soft-deleted records, grouped by module</p>
        </div>
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

    <!-- Module Tabs -->
    <div class="border-b border-gray-200 mb-4">
        <nav class="flex space-x-1 overflow-x-auto">
            @foreach($groups as $key => $group)
                <button @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="flex items-center space-x-2 px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap">
                    <i class="fas {{ $group['icon'] }}"></i>
                    <span>{{ $group['label'] }}</span>
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">{{ $group['records']->count() }}</span>
                </button>
            @endforeach
        </nav>
    </div>

    @foreach($groups as $key => $group)
        <div x-show="tab === '{{ $key }}'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Record</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Deleted At</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($group['records'] as $record)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 align-top">
                                    <div class="text-sm font-semibold text-gray-800">{{ $record['title'] }}</div>
                                    <div class="text-xs text-gray-400">{{ $record['subtitle'] }}</div>
                                </td>
                                <td class="px-4 py-4 align-top whitespace-nowrap text-sm text-gray-600">
                                    {{ $record['deleted_at']?->format('M d, Y g:i A') }}
                                </td>
                                <td class="px-4 py-4 align-top whitespace-nowrap text-right space-x-3">
                                    <form action="{{ route('developer.recycle-bin.restore', [$key, $record['id']]) }}" method="POST" class="inline" onsubmit="return confirm('Restore \'{{ $record['title'] }}\'?')">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Restore">
                                            <i class="fas fa-trash-restore mr-1"></i> Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('developer.recycle-bin.force-delete', [$key, $record['id']]) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete \'{{ $record['title'] }}\'? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete permanently">
                                            <i class="fas fa-trash mr-1"></i> Delete Forever
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">Recycle bin is empty for this module.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
