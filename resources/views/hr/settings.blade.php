@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'hr.settings'])

@php
    $pageTitle = match ($user->role) {
        'admin' => 'Admin Settings',
        'hr' => 'HR Settings',
        'employee' => 'Employee Settings',
        default => 'Account Settings',
    };

    $preferences = session('user_preferences', []);
    $timezone = old('timezone', $preferences['timezone'] ?? 'Asia/Manila');
    $dateFormat = old('date_format', $preferences['date_format'] ?? 'MM/DD/YYYY');
    $darkMode = old('dark_mode', $preferences['dark_mode'] ?? false);
    $emailNotifications = old('email_notifications', $preferences['email_notifications'] ?? true);
    $autoSave = old('auto_save', $preferences['auto_save'] ?? true);
@endphp

@section('title', $pageTitle)

@section('content')
<div style="max-width:840px; margin:0 auto;">

    <div style="margin-bottom:1.5rem;">
        <h1 style="font-size:1.375rem;font-weight:700;color:#111827;margin:0;">{{ $pageTitle }}</h1>
        <p style="margin:.25rem 0 0;font-size:.875rem;color:#6b7280;">Manage your account details and preferences</p>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0"><i class="fas fa-check-circle text-green-600"></i></div>
                <div class="ml-3"><p class="text-sm font-medium text-green-800">{{ session('success') }}</p></div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-600"></i></div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">Please fix the following:</p>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Account Information --}}
    <form action="{{ route('hr.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="emp-card sec-personal">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-user"></i></div>
                <div>
                    <h3>Account Information</h3>
                    <p>Your name, contact details, and login email</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div style="display:grid;grid-template-columns:repeat(2, minmax(0, 1fr));gap:1.25rem;">
                    <div>
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control"
                            value="{{ old('first_name', $employee->first_name ?? $user->first_name ?? '') }}" required>
                    </div>
                    <div>
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control"
                            value="{{ old('last_name', $employee->last_name ?? $user->last_name ?? '') }}" required>
                    </div>
                    <div>
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div>
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control"
                            value="{{ old('phone', $employee->phone ?? '') }}">
                    </div>

                    @if($canManageHrSettings)
                        <div style="grid-column: span 2;">
                            <label for="department_id" class="form-label">Department</label>
                            <select id="department_id" name="department_id" class="form-control">
                                <option value="">— Unassigned —</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ old('department_id', $employee->department_id ?? null) === $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Only HR and Admin accounts can reassign departments.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Preferences --}}
        <div class="emp-card sec-account">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-sliders-h"></i></div>
                <div>
                    <h3>Preferences</h3>
                    <p>Display and notification preferences for your account</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div style="display:grid;grid-template-columns:repeat(2, minmax(0, 1fr));gap:1.25rem;margin-bottom:1.25rem;">
                    <div>
                        <label for="timezone" class="form-label">Timezone</label>
                        <select id="timezone" name="timezone" class="form-control">
                            @foreach(['Asia/Manila' => 'Philippines (Asia/Manila)', 'UTC' => 'UTC', 'Asia/Singapore' => 'Singapore', 'Asia/Tokyo' => 'Tokyo', 'America/New_York' => 'US Eastern'] as $tzValue => $tzLabel)
                                <option value="{{ $tzValue }}" {{ $timezone === $tzValue ? 'selected' : '' }}>{{ $tzLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="date_format" class="form-label">Date Format</label>
                        <select id="date_format" name="date_format" class="form-control">
                            @foreach(['MM/DD/YYYY' => 'MM/DD/YYYY', 'DD/MM/YYYY' => 'DD/MM/YYYY', 'YYYY-MM-DD' => 'YYYY-MM-DD'] as $dfValue => $dfLabel)
                                <option value="{{ $dfValue }}" {{ $dateFormat === $dfValue ? 'selected' : '' }}>{{ $dfLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:.75rem;">
                    <label class="flex items-center">
                        <input type="hidden" name="dark_mode" value="0">
                        <input type="checkbox" name="dark_mode" value="1" {{ $darkMode ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Dark Mode</span>
                    </label>
                    <label class="flex items-center">
                        <input type="hidden" name="email_notifications" value="0">
                        <input type="checkbox" name="email_notifications" value="1" {{ $emailNotifications ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Email Notifications</span>
                    </label>
                    <label class="flex items-center">
                        <input type="hidden" name="auto_save" value="0">
                        <input type="checkbox" name="auto_save" value="1" {{ $autoSave ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Auto-save Drafts</span>
                    </label>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;margin-top:1rem;">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                Save Changes
            </button>
        </div>
    </form>

    {{-- Password --}}
    <form action="{{ route('hr.settings.password') }}" method="POST" style="margin-top:1.5rem;">
        @csrf
        @method('PUT')

        <div class="emp-card sec-emergency">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-lock"></i></div>
                <div>
                    <h3>Change Password</h3>
                    <p>Update the password used to sign in</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div style="display:grid;grid-template-columns:repeat(3, minmax(0, 1fr));gap:1.25rem;">
                    <div>
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" autocomplete="current-password">
                    </div>
                    <div>
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" id="password" name="password" class="form-control" autocomplete="new-password">
                    </div>
                    <div>
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">Password must be at least 8 characters and different from your current password.</p>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;margin-top:1rem;">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 text-sm font-medium">
                Update Password
            </button>
        </div>
    </form>

</div>
@endsection