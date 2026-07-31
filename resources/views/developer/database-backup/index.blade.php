@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => $activeRoute])

@section('content')
<div class="w-full px-4 py-8">
    <div class="flex items-start space-x-3 mb-6">
        <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mt-0.5">
            <i class="fas fa-database"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Database Backup</h1>
            <p class="text-sm text-gray-500">Export a full copy of the database. Admin-only.</p>
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

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl">
        <dl class="divide-y divide-gray-100">
            <div class="py-3 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Format</dt>
                <dd class="text-sm text-gray-900 col-span-2">SQL (.sql)</dd>
            </div>
            <div class="py-3 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Scope</dt>
                <dd class="text-sm text-gray-900 col-span-2">All {{ $tableCount }} tables and their data in the <span class="font-mono">{{ $database }}</span> database</dd>
            </div>
            <div class="py-3 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Filename</dt>
                <dd class="text-sm text-gray-900 col-span-2 font-mono">{{ $filenamePattern }}</dd>
            </div>
        </dl>

        <div class="mt-4 flex items-start space-x-3 bg-amber-50 border border-amber-200 rounded-lg p-4">
            <i class="fas fa-triangle-exclamation text-amber-500 mt-0.5"></i>
            <p class="text-sm text-amber-800">
                This file contains the complete database, including password hashes, personal information, and payroll data.
                Store it securely, never share it, and delete it once it is no longer needed.
            </p>
        </div>

        <form action="{{ route('developer.database-backup.download') }}" method="POST" class="mt-6"
              onsubmit="return confirm('This will download a full backup of the database, including sensitive data. Continue?')">
            @csrf
            <button type="submit" class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-download"></i>
                <span>Download Backup</span>
            </button>
        </form>
    </div>
</div>
@endsection
