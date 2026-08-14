@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employees.bio-zk'])

@section('title', 'Bio-ZK')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3 mb-2">
        <div style="width:38px;height:38px;border-radius:10px;background:#fce7f3;color:#db2777;display:flex;align-items:center;justify-content:center;font-size:1rem;">
            <i class="fas fa-fingerprint"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bio-ZK</h1>
            <p class="text-sm text-gray-500">Manage Bio-ZK filter and capture options</p>
        </div>
    </div>

    <div class="emp-card sec-id">
        <div class="emp-card-header">
            <div class="section-icon"><i class="fas fa-sliders-h"></i></div>
            <div>
                <h3>Filters</h3>
                <p>Filter employees by status, type, branch, department and position</p>
            </div>
        </div>
        <div class="emp-card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="employee_status" class="form-label">Employee status</label>
                <select id="employee_status" class="form-control">
                    <option value="">Select employee status</option>
                </select>
            </div>

            <div>
                <label for="period_type" class="form-label">Period type</label>
                <select id="period_type" class="form-control">
                    <option value="">Select period type</option>
                </select>
            </div>

            <div>
                <label for="branch" class="form-label">Branch</label>
                <select id="branch" class="form-control">
                    <option value="">Select branch</option>
                </select>
            </div>

            <div>
                <label for="department" class="form-label">Department</label>
                <select id="department" class="form-control">
                    <option value="">Select department</option>
                </select>
            </div>

            <div>
                <label for="position" class="form-label">Position</label>
                <select id="position" class="form-control">
                    <option value="">Select position</option>
                </select>
            </div>
        </div>
        </div>
    </div>

    <div class="emp-card sec-overrides">
        <div class="emp-card-header">
            <div class="section-icon"><i class="fas fa-cog"></i></div>
            <div>
                <h3>Options</h3>
                <p>Configure capture mode for biometric data</p>
            </div>
        </div>
        <div class="emp-card-body">
        <div class="space-y-3">
            <label class="flex items-center gap-3">
                <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="text-sm text-gray-700">With face</span>
            </label>

            <label class="flex items-center gap-3">
                <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="text-sm text-gray-700">With Fingerprint</span>
            </label>

            <label class="flex items-center gap-3">
                <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="text-sm text-gray-700">With EMP ID Bio ID</span>
            </label>
        </div>
        </div>
    </div>
</div>
@endsection
