@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employees.index'])

@section('title', 'Edit Employee')

@section('content')
<style>
/* grid helpers */
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; }
.col-span-2 { grid-column: span 2; }
.col-span-3 { grid-column: span 3; }

/* password toggle */
.pw-wrap { position: relative; }
.pw-toggle {
    position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
    background: none; border: none; color: #9ca3af; cursor: pointer; padding: 0;
    font-size: .875rem; line-height: 1;
}
.pw-toggle:hover { color: #4b5563; }

/* submit bar */
.submit-bar {
    display: flex; align-items: center; justify-content: flex-end; gap: .75rem;
    padding: 1.25rem 1.5rem;
    background: #fff;
    border-top: 1px solid #e5e7eb;
    border-radius: 0 0 12px 12px;
    position: sticky; bottom: 0; z-index: 10;
    box-shadow: 0 -2px 8px rgba(0,0,0,.06);
}
.btn-cancel {
    padding: .5rem 1.25rem; border: 1px solid #d1d5db; border-radius: 8px;
    background: #fff; color: #374151; font-weight: 500; font-size: .875rem;
    cursor: pointer; transition: background .15s;
    text-decoration: none; display: inline-flex; align-items: center; gap: .4rem;
}
.btn-cancel:hover { background: #f9fafb; }
.btn-save {
    padding: .5rem 1.5rem; border: none; border-radius: 8px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff; font-weight: 600; font-size: .875rem;
    cursor: pointer; transition: opacity .15s, transform .1s;
    display: inline-flex; align-items: center; gap: .5rem;
    box-shadow: 0 2px 6px rgba(37,99,235,.35);
}
.btn-save:hover { opacity: .92; transform: translateY(-1px); }

@media (max-width: 768px) {
    .grid-2, .grid-3 { grid-template-columns: 1fr; }
    .col-span-2, .col-span-3 { grid-column: span 1; }
    .emp-card-body { padding: 1rem; }
}
</style>

<div style="max-width:960px; margin:0 auto;">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.5rem;gap:1rem;flex-wrap:wrap;">
        <div>
            <h1 style="font-size:1.375rem;font-weight:700;color:#111827;margin:0;">Edit Employee</h1>
            <p style="margin:.25rem 0 0;font-size:.875rem;color:#6b7280;">Update employee information and details.</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Employees
        </a>
    </div>

    {{-- Global Error Banner --}}
    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;gap:.75rem;align-items:flex-start;">
        <i class="fas fa-exclamation-circle" style="color:#ef4444;margin-top:.1rem;flex-shrink:0;"></i>
        <div>
            <p style="font-weight:600;color:#b91c1c;margin:0 0 .4rem;">Please fix the following errors:</p>
            <ul style="margin:0;padding-left:1.25rem;color:#dc2626;font-size:.875rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data" id="addEmpForm">
        @csrf
        @method('PUT')

        {{-- ═══════════════════════════════════════════════
             HEADER CARD — Photo + Employee Number
        ════════════════════════════════════════════════ --}}
        <div class="emp-card">
            <div style="padding:1.5rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap;">
                {{-- Profile Photo --}}
                <div>
                    <p class="form-label" style="margin-bottom:.5rem;">Profile Photo</p>
                    <div class="photo-wrap" onclick="document.getElementById('profile_photo').click();" title="Click to change photo">
                        @if($employee->profile_photo)
                            <div class="photo-placeholder" id="photoPlaceholder" style="display:none;">
                                <i class="fas fa-user"></i>
                            </div>
                            <img id="photoPreview" src="{{ asset('storage/' . $employee->profile_photo) }}" alt="Profile" style="display:block;width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;">
                        @else
                            <div class="photo-placeholder" id="photoPlaceholder">
                                <i class="fas fa-user"></i>
                            </div>
                            <img id="photoPreview" src="" alt="Profile" style="display:none;width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;">
                        @endif
                        <div class="photo-overlay"><i class="fas fa-camera"></i></div>
                    </div>
                    <input type="file" name="profile_photo" id="profile_photo" accept="image/jpg,image/jpeg,image/png,image/webp" style="display:none;">
                    <p class="field-hint" style="margin-top:.4rem;text-align:center;">JPG, PNG, WEBP — max 3 MB</p>
                    @error('profile_photo')
                        <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Employee Number --}}
                <div style="flex:1;min-width:220px;">
                    <p class="form-label">Employee Number</p>
                    <div class="emp-num-badge">
                        <i class="fas fa-id-badge"></i>
                        <span id="empNumDisplay">{{ $employee->employee_id }}</span>
                    </div>
                    <p class="field-hint" style="margin-top:.5rem;"><i class="fas fa-lock" style="font-size:.65rem;"></i> Auto-generated — not editable</p>
                    {{-- Hidden field for consistency --}}
                    <input type="hidden" name="employee_number_preview" value="{{ $employee->employee_id }}">
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             1. PERSONAL INFORMATION
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-personal">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-user"></i></div>
                <div>
                    <h3>Personal Information</h3>
                    <p>Basic identification and demographic details</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-3">
                    {{-- Last Name --}}
                    <div>
                        <label for="last_name" class="form-label">Last Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $employee->last_name) }}" required
                            class="form-control @error('last_name') is-error @enderror"
                            placeholder="e.g. Dela Cruz">
                        @error('last_name')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- First Name --}}
                    <div>
                        <label for="first_name" class="form-label">First Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $employee->first_name) }}" required
                            class="form-control @error('first_name') is-error @enderror"
                            placeholder="e.g. Juan">
                        @error('first_name')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Middle Name --}}
                    <div>
                        <label for="middle_name" class="form-label">Middle Name <span style="color:#9ca3af;font-weight:400;">(Optional)</span></label>
                        <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $employee->middle_name) }}"
                            class="form-control @error('middle_name') is-error @enderror"
                            placeholder="e.g. Santos">
                        @error('middle_name')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Sex --}}
                    <div>
                        <label for="sex" class="form-label">Sex</label>
                        <select name="sex" id="sex" class="form-control @error('sex') is-error @enderror">
                            <option value="">— Select —</option>
                            <option value="Male"              {{ old('sex', $employee->sex) == 'Male'               ? 'selected' : '' }}>Male</option>
                            <option value="Female"            {{ old('sex', $employee->sex) == 'Female'             ? 'selected' : '' }}>Female</option>
                            <option value="Prefer not to say" {{ old('sex', $employee->sex) == 'Prefer not to say'  ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                        @error('sex')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Civil Status --}}
                    <div>
                        <label for="civil_status" class="form-label">Civil Status</label>
                        <select name="civil_status" id="civil_status" class="form-control @error('civil_status') is-error @enderror">
                            <option value="">— Select —</option>
                            <option value="Single"    {{ old('civil_status', $employee->civil_status) == 'Single'     ? 'selected' : '' }}>Single</option>
                            <option value="Married"   {{ old('civil_status', $employee->civil_status) == 'Married'    ? 'selected' : '' }}>Married</option>
                            <option value="Widowed"   {{ old('civil_status', $employee->civil_status) == 'Widowed'    ? 'selected' : '' }}>Widowed</option>
                            <option value="Separated" {{ old('civil_status', $employee->civil_status) == 'Separated'  ? 'selected' : '' }}>Separated</option>
                            <option value="Annulled"  {{ old('civil_status', $employee->civil_status) == 'Annulled'   ? 'selected' : '' }}>Annulled</option>
                        </select>
                        @error('civil_status')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Date of Birth --}}
                    <div>
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth) }}"
                            class="form-control @error('date_of_birth') is-error @enderror">
                        @error('date_of_birth')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Age (auto-computed) --}}
                    <div>
                        <label for="age_display" class="form-label">Age</label>
                        <input type="text" id="age_display" class="form-control" readonly placeholder="Auto-computed from DOB">
                        <p class="field-hint"><i class="fas fa-calculator" style="font-size:.65rem;"></i> Calculated automatically</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             IDENTIFICATION
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-id">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-id-card-clip"></i></div>
                <div>
                    <h3>Identification & Status</h3>
                    <p>Internal tracking numbers and status</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-3">
                    {{-- ID/Card No --}}
                    <div>
                        <label for="id_card_no" class="form-label">ID/Card No</label>
                        <input type="text" name="id_card_no" id="id_card_no" value="{{ old('id_card_no', $employee->id_card_no) }}"
                            class="form-control @error('id_card_no') is-error @enderror">
                        @error('id_card_no')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Control No --}}
                    <div>
                        <label for="control_no" class="form-label">Control No.</label>
                        <input type="text" name="control_no" id="control_no" value="{{ old('control_no', $employee->control_no) }}"
                            class="form-control @error('control_no') is-error @enderror">
                        @error('control_no')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Active Status --}}
                    <div>
                        <label for="active_status" class="form-label">Active Status</label>
                        <select name="active_status" id="active_status" class="form-control @error('active_status') is-error @enderror">
                            <option value="">— Select —</option>
                            <option value="Active" {{ old('active_status', $employee->active_status) == 'Active'  ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('active_status', $employee->active_status) == 'Inactive'  ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('active_status')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             2. WORK INFORMATION
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-work">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-briefcase"></i></div>
                <div>
                    <h3>Work Information</h3>
                    <p>Employment classification and compensation details</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-2">
                    {{-- Department --}}
                    <div>
                        <label for="department_id" class="form-label">Department <span style="color:#ef4444;">*</span></label>
                        <select name="department_id" id="department_id" required
                            class="form-control @error('department_id') is-error @enderror">
                            <option value="">— Select Department —</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id  ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Employee Status --}}
                    <div>
                        <label for="employee_status" class="form-label">Employee Status</label>
                        <select name="employee_status" id="employee_status"
                            class="form-control @error('employee_status') is-error @enderror">
                            <option value="">— Select Status —</option>
                            <option value="Regular"       {{ old('employee_status', $employee->employee_status) == 'Regular'        ? 'selected' : '' }}>Regular</option>
                            <option value="Probationary"  {{ old('employee_status', $employee->employee_status) == 'Probationary'   ? 'selected' : '' }}>Probationary</option>
                            <option value="Contractual"   {{ old('employee_status', $employee->employee_status) == 'Contractual'    ? 'selected' : '' }}>Contractual</option>
                            <option value="Part-time"     {{ old('employee_status', $employee->employee_status) == 'Part-time'      ? 'selected' : '' }}>Part-time</option>
                            <option value="Project-based" {{ old('employee_status', $employee->employee_status) == 'Project-based'  ? 'selected' : '' }}>Project-based</option>
                        </select>
                        @error('employee_status')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Position --}}
                    <div>
                        <label for="position_id" class="form-label">Employee Position <span style="color:#ef4444;">*</span></label>
                        <select name="position_id" id="position_id" required
                            class="form-control @error('position_id') is-error @enderror">
                            <option value="">— Select Position —</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ old('position_id', $employee->position_id) == $position->id  ? 'selected' : '' }}>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Date Employed --}}
                    <div>
                        <label for="hire_date" class="form-label">Date Employed <span style="color:#ef4444;">*</span></label>
                        <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $employee->hire_date) }}" required
                            class="form-control @error('hire_date') is-error @enderror">
                        @error('hire_date')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Contract End --}}
                    <div>
                        <label for="contract_end_date" class="form-label">Contract End <span style="color:#9ca3af;font-weight:400;">(Optional)</span></label>
                        <input type="date" name="contract_end_date" id="contract_end_date" value="{{ old('contract_end_date', $employee->contract_end_date) }}"
                            class="form-control @error('contract_end_date') is-error @enderror">
                        @error('contract_end_date')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Salary Range --}}
                    <div>
                        <label for="salary" class="form-label">Salary Range (₱) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="salary" id="salary" value="{{ old('salary', $employee->salary) }}" min="0" step="0.01" required
                            class="form-control @error('salary') is-error @enderror"
                            placeholder="0.00">
                        @error('salary')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Paycode --}}
                    <div>
                        <label for="paycode" class="form-label">Paycode</label>
                        <input type="text" name="paycode" id="paycode" value="{{ old('paycode', $employee->paycode) }}" class="form-control">
                    </div>
                    {{-- Period Type --}}
                    <div>
                        <label for="period_type" class="form-label">Period Type</label>
                        <input type="text" name="period_type" id="period_type" value="{{ old('period_type', $employee->period_type) }}" class="form-control">
                    </div>
                    {{-- Paylevel --}}
                    <div>
                        <label for="paylevel" class="form-label">Paylevel</label>
                        <input type="text" name="paylevel" id="paylevel" value="{{ old('paylevel', $employee->paylevel) }}" class="form-control">
                    </div>
                    {{-- Job Grade --}}
                    <div>
                        <label for="job_grade" class="form-label">Job Grade</label>
                        <input type="text" name="job_grade" id="job_grade" value="{{ old('job_grade', $employee->job_grade) }}" class="form-control">
                    </div>
                    {{-- Rate --}}
                    <div>
                        <label for="rate" class="form-label">Rate (₱)</label>
                        <input type="number" step="0.01" name="rate" id="rate" value="{{ old('rate', $employee->rate) }}" class="form-control">
                    </div>
                    {{-- Basic Pay 2 --}}
                    <div>
                        <label for="basic_pay2" class="form-label">Basic Pay 2 (₱)</label>
                        <input type="number" step="0.01" name="basic_pay2" id="basic_pay2" value="{{ old('basic_pay2', $employee->basic_pay2) }}" class="form-control">
                    </div>
                    {{-- Regular Date --}}
                    <div>
                        <label for="regular_date" class="form-label">Regular Date</label>
                        <input type="date" name="regular_date" id="regular_date" value="{{ old('regular_date', $employee->regular_date) }}" class="form-control">
                    </div>
                    {{-- Resigned Date --}}
                    <div>
                        <label for="resigned_date" class="form-label">Resigned Date</label>
                        <input type="date" name="resigned_date" id="resigned_date" value="{{ old('resigned_date', $employee->resigned_date) }}" class="form-control">
                    </div>
                    {{-- Resign Process --}}
                    <div>
                        <label for="resign_process" class="form-label">Resign Process</label>
                        <select name="resign_process" id="resign_process" class="form-control">
                            <option value="">— Select —</option>
                            <option value="Yes" {{ old('resign_process', $employee->resign_process) == 'Yes'  ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('resign_process', $employee->resign_process) == 'No'  ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    {{-- Resign on next payroll --}}
                    <div class="col-span-2" style="display:flex;align-items:center;gap:0.5rem;padding-top:1rem;">
                        <input type="checkbox" name="resign_on_next_payroll" id="resign_on_next_payroll" value="1" {{ old('resign_on_next_payroll', $employee->resign_on_next_payroll) ? 'checked' : '' }} style="width:1.2rem;height:1.2rem;">
                        <label for="resign_on_next_payroll" class="form-label" style="margin:0;">Include Resigned on Next Payroll</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             3. ADDITIONAL EMPLOYEE DETAILS
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-details">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-address-card"></i></div>
                <div>
                    <h3>Additional Employee Details</h3>
                    <p>Contact information and social links</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-2">
                    {{-- Home Address --}}
                    <div class="col-span-2">
                        <label for="home_address" class="form-label">Home Address</label>
                        <textarea name="home_address" id="home_address" rows="2"
                            class="form-control @error('home_address') is-error @enderror"
                            placeholder="Permanent / home address">{{ old('home_address', $employee->home_address) }}</textarea>
                        @error('home_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Current Address --}}
                    <div class="col-span-2">
                        <label for="current_address" class="form-label">Current Address</label>
                        <textarea name="current_address" id="current_address" rows="2"
                            class="form-control @error('current_address') is-error @enderror"
                            placeholder="Present / current address">{{ old('current_address', $employee->current_address) }}</textarea>
                        @error('current_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Email Address --}}
                    <div>
                        <label for="email" class="form-label">Email Address <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $employee->account?->email ?? $employee->email) }}" required
                            class="form-control @error('email') is-error @enderror"
                            placeholder="employee@example.com">
                        <p class="field-hint"><i class="fas fa-info-circle" style="font-size:.65rem;"></i> This will also be used as the login credential</p>
                        @error('email')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Mobile Number --}}
                    <div>
                        <label for="mobile_number" class="form-label">Mobile Number</label>
                        <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $employee->mobile_number) }}"
                            class="form-control @error('mobile_number') is-error @enderror"
                            placeholder="09XX-XXX-XXXX">
                        @error('mobile_number')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Facebook --}}
                    <div>
                        <label for="facebook_link" class="form-label"><i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook Link</label>
                        <input type="url" name="facebook_link" id="facebook_link" value="{{ old('facebook_link', $employee->facebook_link) }}"
                            class="form-control @error('facebook_link') is-error @enderror"
                            placeholder="https://facebook.com/...">
                        @error('facebook_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- LinkedIn --}}
                    <div>
                        <label for="linkedin_link" class="form-label"><i class="fab fa-linkedin" style="color:#0a66c2;"></i> LinkedIn Link</label>
                        <input type="url" name="linkedin_link" id="linkedin_link" value="{{ old('linkedin_link', $employee->linkedin_link) }}"
                            class="form-control @error('linkedin_link') is-error @enderror"
                            placeholder="https://linkedin.com/in/...">
                        @error('linkedin_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Instagram --}}
                    <div>
                        <label for="ig_link" class="form-label"><i class="fab fa-instagram" style="color:#e1306c;"></i> Instagram Link</label>
                        <input type="url" name="ig_link" id="ig_link" value="{{ old('ig_link', $employee->ig_link) }}"
                            class="form-control @error('ig_link') is-error @enderror"
                            placeholder="https://instagram.com/...">
                        @error('ig_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Other Link --}}
                    <div>
                        <label for="other_link" class="form-label"><i class="fas fa-link" style="color:#6b7280;"></i> Other Link</label>
                        <input type="url" name="other_link" id="other_link" value="{{ old('other_link', $employee->other_link) }}"
                            class="form-control @error('other_link') is-error @enderror"
                            placeholder="https://...">
                        @error('other_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             4. IN CASE OF EMERGENCY
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-emergency">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-phone-alt"></i></div>
                <div>
                    <h3>In Case of an Emergency</h3>
                    <p>Emergency contact person details</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-2">
                    {{-- Full Name --}}
                    <div>
                        <label for="emergency_full_name" class="form-label">Full Name</label>
                        <input type="text" name="emergency_full_name" id="emergency_full_name" value="{{ old('emergency_full_name', $employee->emergency_full_name) }}"
                            class="form-control @error('emergency_full_name') is-error @enderror"
                            placeholder="Emergency contact full name">
                        @error('emergency_full_name')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Relationship --}}
                    <div>
                        <label for="emergency_relationship" class="form-label">Relationship</label>
                        <input type="text" name="emergency_relationship" id="emergency_relationship" value="{{ old('emergency_relationship', $employee->emergency_relationship) }}"
                            class="form-control @error('emergency_relationship') is-error @enderror"
                            placeholder="e.g. Spouse, Parent, Sibling">
                        @error('emergency_relationship')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Home Address --}}
                    <div class="col-span-2">
                        <label for="emergency_home_address" class="form-label">Home Address</label>
                        <textarea name="emergency_home_address" id="emergency_home_address" rows="2"
                            class="form-control @error('emergency_home_address') is-error @enderror"
                            placeholder="Contact's home address">{{ old('emergency_home_address', $employee->emergency_home_address) }}</textarea>
                        @error('emergency_home_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Current Address --}}
                    <div class="col-span-2">
                        <label for="emergency_current_address" class="form-label">Current Address</label>
                        <textarea name="emergency_current_address" id="emergency_current_address" rows="2"
                            class="form-control @error('emergency_current_address') is-error @enderror"
                            placeholder="Contact's current address">{{ old('emergency_current_address', $employee->emergency_current_address) }}</textarea>
                        @error('emergency_current_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Email --}}
                    <div>
                        <label for="emergency_email" class="form-label">Email Address</label>
                        <input type="email" name="emergency_email" id="emergency_email" value="{{ old('emergency_email', $employee->emergency_email) }}"
                            class="form-control @error('emergency_email') is-error @enderror"
                            placeholder="contact@example.com">
                        @error('emergency_email')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Mobile Number --}}
                    <div>
                        <label for="emergency_mobile_number" class="form-label">Mobile Number</label>
                        <input type="text" name="emergency_mobile_number" id="emergency_mobile_number" value="{{ old('emergency_mobile_number', $employee->emergency_mobile_number) }}"
                            class="form-control @error('emergency_mobile_number') is-error @enderror"
                            placeholder="09XX-XXX-XXXX">
                        @error('emergency_mobile_number')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Facebook --}}
                    <div>
                        <label for="emergency_facebook_link" class="form-label"><i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook Link</label>
                        <input type="url" name="emergency_facebook_link" id="emergency_facebook_link" value="{{ old('emergency_facebook_link', $employee->emergency_facebook_link) }}"
                            class="form-control @error('emergency_facebook_link') is-error @enderror"
                            placeholder="https://facebook.com/...">
                        @error('emergency_facebook_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             5. EMPLOYEE LOANS
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-loans">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <div>
                    <h3>Employee Loans</h3>
                    <p>Loan details and amortization schedule <span style="font-style:italic;">(optional)</span></p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-2">
                    {{-- Start Date --}}
                    <div>
                        <label for="loan_start_date" class="form-label">Start Date</label>
                        <input type="date" name="loan_start_date" id="loan_start_date" value="{{ old('loan_start_date', $employee->loan_start_date) }}"
                            class="form-control @error('loan_start_date') is-error @enderror">
                        @error('loan_start_date')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- End Date --}}
                    <div>
                        <label for="loan_end_date" class="form-label">End Date</label>
                        <input type="date" name="loan_end_date" id="loan_end_date" value="{{ old('loan_end_date', $employee->loan_end_date) }}"
                            class="form-control @error('loan_end_date') is-error @enderror">
                        @error('loan_end_date')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Total Amount --}}
                    <div>
                        <label for="loan_total_amount" class="form-label">Total Amount (₱)</label>
                        <input type="number" step="0.01" min="0" name="loan_total_amount" id="loan_total_amount" value="{{ old('loan_total_amount', $employee->loan_total_amount) }}"
                            class="form-control @error('loan_total_amount') is-error @enderror"
                            placeholder="0.00">
                        @error('loan_total_amount')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Monthly Amortization --}}
                    <div>
                        <label for="loan_monthly_amortization" class="form-label">Monthly Amortization (₱)</label>
                        <input type="number" step="0.01" min="0" name="loan_monthly_amortization" id="loan_monthly_amortization" value="{{ old('loan_monthly_amortization', $employee->loan_monthly_amortization) }}"
                            class="form-control @error('loan_monthly_amortization') is-error @enderror"
                            placeholder="0.00">
                        @error('loan_monthly_amortization')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             BANKING & STATUTORY
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-statutory">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-landmark"></i></div>
                <div>
                    <h3>Banking & Government IDs</h3>
                    <p>Financial and statutory information</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-3">
                    {{-- Payment Method --}}
                    <div>
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control">
                            <option value="">— Select —</option>
                            <option value="Cash" {{ old('payment_method', $employee->info?->payment_method) == 'Cash'  ? 'selected' : '' }}>Cash</option>
                            <option value="Bank" {{ old('payment_method', $employee->info?->payment_method) == 'Bank'  ? 'selected' : '' }}>Bank</option>
                            <option value="Cheque" {{ old('payment_method', $employee->info?->payment_method) == 'Cheque'  ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>
                    {{-- Bank Name --}}
                    <div>
                        <label for="bank" class="form-label">Bank Name <span id="bank_required" style="color:#ef4444;display:none;">*</span></label>
                        <input type="text" name="bank" id="bank" value="{{ old('bank', $employee->info?->bank) }}" class="form-control">
                    </div>
                    {{-- Account No --}}
                    <div>
                        <label for="account_no" class="form-label">Account No <span id="account_no_required" style="color:#ef4444;display:none;">*</span></label>
                        <input type="text" name="account_no" id="account_no" value="{{ old('account_no', $employee->info?->account_no) }}" class="form-control">
                    </div>
                    {{-- Tax Code --}}
                    <div>
                        <label for="tax_code" class="form-label">Tax Code</label>
                        <input type="text" name="tax_code" id="tax_code" value="{{ old('tax_code', $employee->tax_code) }}" class="form-control">
                    </div>
                    {{-- TIN No --}}
                    <div>
                        <label for="tin_no" class="form-label">TIN No</label>
                        <input type="text" name="tin_no" id="tin_no" value="{{ old('tin_no', $employee->tin_no) }}" class="form-control">
                    </div>
                    {{-- SSS No --}}
                    <div>
                        <label for="sss_no" class="form-label">SSS No</label>
                        <input type="text" name="sss_no" id="sss_no" value="{{ old('sss_no', $employee->sss_no) }}" class="form-control">
                    </div>
                    {{-- HDMF No --}}
                    <div>
                        <label for="hdmf_no" class="form-label">Pag-IBIG / HDMF No</label>
                        <input type="text" name="hdmf_no" id="hdmf_no" value="{{ old('hdmf_no', $employee->hdmf_no) }}" class="form-control">
                    </div>
                    {{-- PhilHealth No --}}
                    <div>
                        <label for="philhealth_no" class="form-label">PhilHealth No</label>
                        <input type="text" name="philhealth_no" id="philhealth_no" value="{{ old('philhealth_no', $employee->philhealth_no) }}" class="form-control">
                    </div>
                    {{-- HMO No --}}
                    <div>
                        <label for="hmo_no" class="form-label">HMO No</label>
                        <input type="text" name="hmo_no" id="hmo_no" value="{{ old('hmo_no', $employee->hmo_no) }}" class="form-control">
                    </div>
                    {{-- Tax Comp Method --}}
                    <div>
                        <label for="tax_computation_method" class="form-label">Tax Computation Method</label>
                        <select name="tax_computation_method" id="tax_computation_method" class="form-control">
                            <option value="">— Select —</option>
                            <option value="Annualized" {{ old('tax_computation_method', $employee->tax_computation_method) == 'Annualized'  ? 'selected' : '' }}>Annualized</option>
                            <option value="Standard" {{ old('tax_computation_method', $employee->tax_computation_method) == 'Standard'  ? 'selected' : '' }}>Standard</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             MFG GROUPS & PROJECTS
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-mfg">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-industry"></i></div>
                <div>
                    <h3>Manufacturing Groups & Projects</h3>
                    <p>Assignments and project tracking</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-3">
                    {{-- Cluster --}}
                    <div>
                        <label for="mfg_cluster" class="form-label">Cluster</label>
                        <input type="text" name="mfg_cluster" id="mfg_cluster" value="{{ old('mfg_cluster', $employee->mfg_cluster) }}" class="form-control">
                    </div>
                    {{-- Section --}}
                    <div>
                        <label for="mfg_section" class="form-label">Section</label>
                        <input type="text" name="mfg_section" id="mfg_section" value="{{ old('mfg_section', $employee->mfg_section) }}" class="form-control">
                    </div>
                    {{-- Sub Section --}}
                    <div>
                        <label for="mfg_sub_section" class="form-label">Sub Section</label>
                        <input type="text" name="mfg_sub_section" id="mfg_sub_section" value="{{ old('mfg_sub_section', $employee->mfg_sub_section) }}" class="form-control">
                    </div>
                    {{-- Group --}}
                    <div>
                        <label for="mfg_group" class="form-label">Group</label>
                        <input type="text" name="mfg_group" id="mfg_group" value="{{ old('mfg_group', $employee->mfg_group) }}" class="form-control">
                    </div>
                    {{-- Line --}}
                    <div>
                        <label for="mfg_line" class="form-label">Line</label>
                        <input type="text" name="mfg_line" id="mfg_line" value="{{ old('mfg_line', $employee->mfg_line) }}" class="form-control">
                    </div>
                    {{-- Position --}}
                    <div>
                        <label for="mfg_position" class="form-label">Position (MFG)</label>
                        <input type="text" name="mfg_position" id="mfg_position" value="{{ old('mfg_position', $employee->mfg_position) }}" class="form-control">
                    </div>
                    {{-- Project Contract --}}
                    <div>
                        <label for="project_contract" class="form-label">Project/Contract #</label>
                        <input type="text" name="project_contract" id="project_contract" value="{{ old('project_contract', $employee->project_contract) }}" class="form-control">
                    </div>
                    {{-- Project Name --}}
                    <div>
                        <label for="project_name" class="form-label">Project Name</label>
                        <input type="text" name="project_name" id="project_name" value="{{ old('project_name', $employee->project_name) }}" class="form-control">
                    </div>
                    {{-- Project Category --}}
                    <div>
                        <label for="project_category" class="form-label">Project Category</label>
                        <input type="text" name="project_category" id="project_category" value="{{ old('project_category', $employee->project_category) }}" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             STATUTORY OVERRIDES
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-overrides">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-sliders-h"></i></div>
                <div>
                    <h3>Statutory Overrides</h3>
                    <p>Force computation overrides for this employee</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-3" style="gap:2rem 1.25rem;">
                    {{-- SSS --}}
                    <div>
                        <h4 style="margin:0 0 1rem;font-size:.9rem;border-bottom:1px solid #e5e7eb;padding-bottom:.5rem;">SSS Options</h4>
                        <div style="margin-bottom:.75rem;">
                            <input type="checkbox" name="override_sss_exclude" id="override_sss_exclude" value="1" {{ old('override_sss_exclude', $employee->override_sss_exclude) ? 'checked' : '' }}>
                            <label for="override_sss_exclude" class="form-label" style="display:inline-block;margin:0 0 0 .35rem;">Exclude from SSS</label>
                        </div>
                        <div style="margin-bottom:.75rem;">
                            <label for="override_sss_employee" class="form-label">Override Employee Amt (₱)</label>
                            <input type="number" step="0.01" name="override_sss_employee" id="override_sss_employee" value="{{ old('override_sss_employee', $employee->override_sss_employee) }}" class="form-control form-control-sm">
                        </div>
                        <div>
                            <label for="override_sss_employer" class="form-label">Override Employer Amt (₱)</label>
                            <input type="number" step="0.01" name="override_sss_employer" id="override_sss_employer" value="{{ old('override_sss_employer', $employee->override_sss_employer) }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    
                    {{-- Philhealth --}}
                    <div>
                        <h4 style="margin:0 0 1rem;font-size:.9rem;border-bottom:1px solid #e5e7eb;padding-bottom:.5rem;">PhilHealth Options</h4>
                        <div style="margin-bottom:.75rem;">
                            <input type="checkbox" name="override_philhealth_exclude" id="override_philhealth_exclude" value="1" {{ old('override_philhealth_exclude', $employee->override_philhealth_exclude) ? 'checked' : '' }}>
                            <label for="override_philhealth_exclude" class="form-label" style="display:inline-block;margin:0 0 0 .35rem;">Exclude from PhilHealth</label>
                        </div>
                        <div style="margin-bottom:.75rem;">
                            <label for="override_philhealth_employee" class="form-label">Override Employee Amt (₱)</label>
                            <input type="number" step="0.01" name="override_philhealth_employee" id="override_philhealth_employee" value="{{ old('override_philhealth_employee', $employee->override_philhealth_employee) }}" class="form-control form-control-sm">
                        </div>
                        <div>
                            <label for="override_philhealth_employer" class="form-label">Override Employer Amt (₱)</label>
                            <input type="number" step="0.01" name="override_philhealth_employer" id="override_philhealth_employer" value="{{ old('override_philhealth_employer', $employee->override_philhealth_employer) }}" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- Pag-ibig --}}
                    <div>
                        <h4 style="margin:0 0 1rem;font-size:.9rem;border-bottom:1px solid #e5e7eb;padding-bottom:.5rem;">Pag-IBIG Options</h4>
                        <div style="margin-bottom:.75rem;">
                            <input type="checkbox" name="override_pagibig_exclude" id="override_pagibig_exclude" value="1" {{ old('override_pagibig_exclude', $employee->override_pagibig_exclude) ? 'checked' : '' }}>
                            <label for="override_pagibig_exclude" class="form-label" style="display:inline-block;margin:0 0 0 .35rem;">Exclude from Pag-IBIG</label>
                        </div>
                        <div style="margin-bottom:.75rem;">
                            <label for="override_pagibig_employee" class="form-label">Override Employee Amt (₱)</label>
                            <input type="number" step="0.01" name="override_pagibig_employee" id="override_pagibig_employee" value="{{ old('override_pagibig_employee', $employee->override_pagibig_employee) }}" class="form-control form-control-sm">
                        </div>
                        <div style="margin-bottom:.75rem;">
                            <label for="override_pagibig_employer" class="form-label">Override Employer Amt (₱)</label>
                            <input type="number" step="0.01" name="override_pagibig_employer" id="override_pagibig_employer" value="{{ old('override_pagibig_employer', $employee->override_pagibig_employer) }}" class="form-control form-control-sm">
                        </div>
                        <div style="margin-bottom:.75rem;">
                            <input type="checkbox" name="pagibig_voluntary" id="pagibig_voluntary" value="1" {{ old('pagibig_voluntary', $employee->pagibig_voluntary) ? 'checked' : '' }}>
                            <label for="pagibig_voluntary" class="form-label" style="display:inline-block;margin:0 0 0 .35rem;">Voluntary Deduction</label>
                        </div>
                        <div>
                            <label for="pagibig_vol_amt" class="form-label">Voluntary Amount (₱)</label>
                            <input type="number" step="0.01" name="pagibig_vol_amt" id="pagibig_vol_amt" value="{{ old('pagibig_vol_amt', $employee->pagibig_vol_amt) }}" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- Tax --}}
                    <div>
                        <h4 style="margin:0 0 1rem;font-size:.9rem;border-bottom:1px solid #e5e7eb;padding-bottom:.5rem;">Tax Options</h4>
                        <div>
                            <input type="checkbox" name="override_tax_exclude" id="override_tax_exclude" value="1" {{ old('override_tax_exclude', $employee->override_tax_exclude) ? 'checked' : '' }}>
                            <label for="override_tax_exclude" class="form-label" style="display:inline-block;margin:0 0 0 .35rem;">Exclude from Tax</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             6. ACCOUNT INFORMATION
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-account">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-shield-halved"></i></div>
                <div>
                    <h3>Account Information</h3>
                    <p>System login credentials and role assignment</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-2">
                    {{-- Role --}}
                    <div>
                        <label for="role" class="form-label">Role <span style="color:#ef4444;">*</span></label>
                        <select name="role" id="role" class="form-control @error('role') is-error @enderror">
                            <option value="employee" {{ old('role', $employee->account?->role ?? 'employee') == 'employee' ? 'selected' : '' }}>Employee</option>
                            <option value="manager"  {{ old('role', $employee->role) == 'manager'   ? 'selected' : '' }}>Manager</option>
                            <option value="hr"       {{ old('role', $employee->role) == 'hr'        ? 'selected' : '' }}>HR</option>
                            <option value="admin"    {{ old('role', $employee->role) == 'admin'     ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email (read-only, mirrors #email) --}}
                    <div>
                        <label for="login_email_display" class="form-label">
                            Email Address
                            <span style="background:#dbeafe;color:#1d4ed8;font-size:.68rem;font-weight:600;
                                         border-radius:4px;padding:.1rem .4rem;margin-left:.35rem;letter-spacing:.02em;">
                                LOGIN CREDENTIAL
                            </span>
                        </label>
                        <input type="text" id="login_email_display" readonly
                            class="form-control"
                            placeholder="Will mirror the Email Address above">
                        <p class="field-hint"><i class="fas fa-lock" style="font-size:.65rem;"></i> Synced from Additional Details → Email Address</p>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="form-label">Password <span style="color:#9ca3af;font-weight:400;">(Leave blank to keep current)</span></label>
                        <div class="pw-wrap">
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-error @enderror"
                                placeholder="Minimum 8 characters"
                                style="padding-right:2.5rem;">
                            <button type="button" class="pw-toggle" id="togglePassword" title="Show/hide password">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        <p class="field-hint">Must be at least 8 characters long</p>
                        @error('password')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="form-label">Confirm Password <span style="color:#9ca3af;font-weight:400;">(Leave blank to keep current)</span></label>
                        <div class="pw-wrap">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control @error('password_confirmation') is-error @enderror"
                                placeholder="Re-enter your password"
                                style="padding-right:2.5rem;">
                            <button type="button" class="pw-toggle" id="toggleConfirmPassword" title="Show/hide password">
                                <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                            </button>
                        </div>
                        {{-- Live mismatch alert --}}
                        <p class="field-error" id="passwordMismatchAlert" style="display:none;">
                            <i class="fas fa-circle-exclamation"></i>
                            Passwords do not match. Please re-enter the same password.
                        </p>
                        @error('password_confirmation')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Bar --}}
        <div class="submit-bar">
            <a href="{{ route('employees.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-save" id="submitBtn">
                <i class="fas fa-user-plus"></i> Update Employee
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Payment Method → Account No. / Bank required only when "Bank" ── */
    const paymentMethodSelect = document.getElementById('payment_method');
    const accountNoInput      = document.getElementById('account_no');
    const bankInput           = document.getElementById('bank');
    const accountNoRequired   = document.getElementById('account_no_required');
    const bankRequired        = document.getElementById('bank_required');

    if (paymentMethodSelect && accountNoInput && bankInput) {
        function togglePaymentRequiredFields() {
            const isBank = paymentMethodSelect.value === 'Bank';
            accountNoInput.required = isBank;
            bankInput.required = isBank;
            if (accountNoRequired) accountNoRequired.style.display = isBank ? 'inline' : 'none';
            if (bankRequired) bankRequired.style.display = isBank ? 'inline' : 'none';
        }

        paymentMethodSelect.addEventListener('change', togglePaymentRequiredFields);
        togglePaymentRequiredFields(); // in case old() has a value
    }

    /* ── Profile Photo Preview ── */
    const photoInput       = document.getElementById('profile_photo');
    const photoPreview     = document.getElementById('photoPreview');
    const photoPlaceholder = document.getElementById('photoPlaceholder');

    photoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            photoPreview.src = e.target.result;
            photoPreview.style.display = 'block';
            photoPlaceholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });

    /* ── Age Auto-Compute ── */
    const dobInput   = document.getElementById('date_of_birth');
    const ageDisplay = document.getElementById('age_display');

    function computeAge() {
        const val = dobInput.value;
        if (!val) { ageDisplay.value = ''; return; }
        const dob   = new Date(val);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
        ageDisplay.value = age >= 0 ? age + ' years old' : '';
    }

    dobInput.addEventListener('change', computeAge);
    computeAge(); // in case old() has a value

    /* ── Email Mirror → Login Credential ── */
    const emailInput       = document.getElementById('email');
    const loginEmailMirror = document.getElementById('login_email_display');

    function mirrorEmail() {
        loginEmailMirror.value = emailInput.value;
    }
    emailInput.addEventListener('input', mirrorEmail);
    mirrorEmail(); // in case old() has a value

    /* ── Password Toggle (main) ── */
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pw   = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        const isHidden = pw.type === 'password';
        pw.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('fa-eye',      !isHidden);
        icon.classList.toggle('fa-eye-slash', isHidden);
    });

    /* ── Password Toggle (confirm) ── */
    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
        const pw   = document.getElementById('password_confirmation');
        const icon = document.getElementById('toggleConfirmIcon');
        const isHidden = pw.type === 'password';
        pw.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('fa-eye',      !isHidden);
        icon.classList.toggle('fa-eye-slash', isHidden);
    });

    /* ── Confirm Password — live mismatch alert ── */
    const pwInput      = document.getElementById('password');
    const pwConfirm    = document.getElementById('password_confirmation');
    const mismatchAlert = document.getElementById('passwordMismatchAlert');

    function checkPasswordMatch() {
        if (pwConfirm.value.length === 0) {
            mismatchAlert.style.display = 'none';
            pwConfirm.classList.remove('is-error');
            return;
        }
        const mismatch = pwInput.value !== pwConfirm.value;
        mismatchAlert.style.display = mismatch ? 'flex' : 'none';
        pwConfirm.classList.toggle('is-error', mismatch);
    }

    pwInput.addEventListener('input', checkPasswordMatch);
    pwConfirm.addEventListener('input', checkPasswordMatch);
    pwConfirm.addEventListener('blur',  checkPasswordMatch);

    /* ── Block form submit if passwords don't match ── */
    document.getElementById('addEmpForm').addEventListener('submit', function (e) {
        if (pwInput.value !== pwConfirm.value) {
            e.preventDefault();
            checkPasswordMatch();
            pwConfirm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

});
</script>
@endsection