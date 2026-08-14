@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'hr.my-information.bio-zk'])

@section('title', 'Bio-ZK')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3 mb-2">
        <div style="width:38px;height:38px;border-radius:10px;background:#fce7f3;color:#db2777;display:flex;align-items:center;justify-content:center;font-size:1rem;">
            <i class="fas fa-fingerprint"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bio-ZK</h1>
            <p class="text-sm text-gray-500">Your biometric capture record</p>
        </div>
    </div>

    @if(!$employee)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800 text-sm">
            No employee record is linked to your account yet. Please contact HR/Admin.
        </div>
    @else

    <div class="emp-card sec-id">
        <div class="emp-card-header">
            <div class="section-icon"><i class="fas fa-sliders-h"></i></div>
            <div>
                <h3>Your Details</h3>
                <p>Status, branch, department and position on file</p>
            </div>
        </div>
        <div class="emp-card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="form-label">Employee status</label>
                <input type="text" value="{{ $employee->info?->active_status ?? 'Active' }}" class="form-control bg-gray-50" readonly>
            </div>

            <div>
                <label class="form-label">Period type</label>
                <input type="text" value="{{ $employee->period_type }}" class="form-control bg-gray-50" readonly>
            </div>

            <div>
                <label class="form-label">Department</label>
                <input type="text" value="{{ $employee->department?->name }}" class="form-control bg-gray-50" readonly>
            </div>

            <div>
                <label class="form-label">Position</label>
                <input type="text" value="{{ $employee->position }}" class="form-control bg-gray-50" readonly>
            </div>
        </div>
        </div>
    </div>

    <div class="emp-card sec-overrides">
        <div class="emp-card-header">
            <div class="section-icon"><i class="fas fa-cog"></i></div>
            <div>
                <h3>Capture Options</h3>
                <p>Biometric data captured for your employee ID</p>
            </div>
        </div>
        <div class="emp-card-body">
        <div class="space-y-3">
            <label class="flex items-center gap-3">
                <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" disabled>
                <span class="text-sm text-gray-700">With face</span>
            </label>

            <label class="flex items-center gap-3">
                <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" disabled>
                <span class="text-sm text-gray-700">With Fingerprint</span>
            </label>

            <label class="flex items-center gap-3">
                <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked disabled>
                <span class="text-sm text-gray-700">With EMP ID Bio ID ({{ $employee->employee_id }})</span>
            </label>
        </div>
        <p class="text-xs text-gray-500 mt-4">
            Biometric capture status is not yet tracked per-employee in the database on this system, so these
            options are shown for reference only.
        </p>
        </div>
    </div>
    @endif
</div>
@endsection
