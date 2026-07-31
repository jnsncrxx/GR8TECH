@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employees.index'])

@section('title', 'Add Employee')

@section('content')
<style>
/* ── Add Employee — Custom Styles ── */
.emp-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.05);
    border: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
    overflow: hidden;
}
.emp-card-header {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f1f3f5;
    background: #fafafa;
}
.emp-card-header .section-icon {
    width: 34px; height: 34px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .875rem; flex-shrink: 0;
}
.emp-card-header h3 {
    font-size: 1rem; font-weight: 600; color: #111827; margin: 0;
}
.emp-card-header p {
    font-size: .75rem; color: #6b7280; margin: 0;
}
.emp-card-body { padding: 1.5rem; }

/* section accent colours */
.sec-personal .section-icon { background:#eff6ff; color:#2563eb; }
.sec-work     .section-icon { background:#f0fdf4; color:#16a34a; }
.sec-details  .section-icon { background:#fef9c3; color:#ca8a04; }
.sec-emergency .section-icon { background:#fff1f2; color:#e11d48; }
.sec-loans    .section-icon { background:#f5f3ff; color:#7c3aed; }
.sec-payment  .section-icon { background:#ecfdf5; color:#059669; }
.sec-account  .section-icon { background:#e0f2fe; color:#0284c7; }

/* form controls */
.form-label {
    display: block; font-size: .8125rem; font-weight: 500;
    color: #374151; margin-bottom: .375rem;
}
.form-control {
    width: 100%;
    padding: .5rem .75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: .875rem; color: #111827;
    background: #fff;
    transition: border-color .15s, box-shadow .15s;
    outline: none;
}
.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}
.form-control.is-error { border-color: #ef4444; }
.form-control[readonly], .form-control:disabled {
    background: #f9fafb; color: #6b7280; cursor: not-allowed;
}
.field-error { font-size: .75rem; color: #ef4444; margin-top: .25rem; display: flex; align-items: center; gap: .25rem; }
.field-hint  { font-size: .72rem; color: #9ca3af; margin-top: .25rem; }

/* Employee number badge */
.emp-num-badge {
    display: inline-flex; align-items: center; gap: .5rem;
    background: #eff6ff; border: 1.5px solid #bfdbfe;
    border-radius: 8px; padding: .45rem .9rem;
    font-size: .875rem; font-weight: 700; color: #1d4ed8;
    letter-spacing: .03em;
}
.emp-num-badge i { color: #93c5fd; }

/* Profile photo */
.photo-wrap {
    position: relative; width: 100px; height: 100px; cursor: pointer;
}
.photo-wrap img, .photo-wrap .photo-placeholder {
    width: 100px; height: 100px; border-radius: 50%;
    object-fit: cover; border: 3px solid #e5e7eb;
}
.photo-wrap .photo-placeholder {
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg,#e0f2fe,#ddd6fe);
    color: #64748b; font-size: 2rem;
}
.photo-overlay {
    position: absolute; bottom: 2px; right: 2px;
    width: 28px; height: 28px; border-radius: 50%;
    background: #2563eb; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem; border: 2px solid #fff;
    pointer-events: none;
}
.photo-wrap:hover .photo-placeholder,
.photo-wrap:hover img { border-color: #93c5fd; }

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
            <h1 style="font-size:1.375rem;font-weight:700;color:#111827;margin:0;">Add Employee</h1>
            <p style="margin:.25rem 0 0;font-size:.875rem;color:#6b7280;">Fill in the details below to create a new employee record.</p>
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

    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" id="addEmpForm">
        @csrf

        {{-- ═══════════════════════════════════════════════
             HEADER CARD — Photo + Employee Number
        ════════════════════════════════════════════════ --}}
        <div class="emp-card">
            <div style="padding:1.5rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap;">
                {{-- Profile Photo --}}
                <div>
                    <p class="form-label" style="margin-bottom:.5rem;">Profile Photo</p>
                    <div class="photo-wrap" onclick="document.getElementById('profile_photo').click();" title="Click to upload photo">
                        <div class="photo-placeholder" id="photoPlaceholder">
                            <i class="fas fa-user"></i>
                        </div>
                        <img id="photoPreview" src="" alt="Profile" style="display:none;width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;">
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
                        <span id="empNumDisplay">{{ $nextEmployeeNumber }}</span>
                    </div>
                    <p class="field-hint" style="margin-top:.5rem;"><i class="fas fa-lock" style="font-size:.65rem;"></i> Auto-generated — not editable</p>
                    {{-- Hidden field preserves the pre-computed number on validation failure --}}
                    <input type="hidden" name="employee_number_preview" value="{{ old('employee_number_preview', $nextEmployeeNumber) }}">
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
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            class="form-control @error('last_name') is-error @enderror"
                            placeholder="e.g. Dela Cruz">
                        @error('last_name')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- First Name --}}
                    <div>
                        <label for="first_name" class="form-label">First Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                            class="form-control @error('first_name') is-error @enderror"
                            placeholder="e.g. Juan">
                        @error('first_name')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Middle Name --}}
                    <div>
                        <label for="middle_name" class="form-label">Middle Name <span style="color:#9ca3af;font-weight:400;">(Optional)</span></label>
                        <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}"
                            class="form-control @error('middle_name') is-error @enderror"
                            placeholder="e.g. Santos">
                        @error('middle_name')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Sex--}}
                    <div>
                        <label for="sex" class="form-label">Sex/Gender</label>
                        <select name="sex" id="sex" class="form-control @error('sex') is-error @enderror">
                            <option value="">— Select —</option>
                            <option value="Male"              {{ old('sex') == 'Male'              ? 'selected' : '' }}>Male</option>
                            <option value="Female"            {{ old('sex') == 'Female'            ? 'selected' : '' }}>Female</option>
                            <option value="Prefer not to say" {{ old('sex') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
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
                            <option value="Single"    {{ old('civil_status') == 'Single'    ? 'selected' : '' }}>Single</option>
                            <option value="Married"   {{ old('civil_status') == 'Married'   ? 'selected' : '' }}>Married</option>
                            <option value="Widowed"   {{ old('civil_status') == 'Widowed'   ? 'selected' : '' }}>Widowed</option>
                            <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                            <option value="Annulled"  {{ old('civil_status') == 'Annulled'  ? 'selected' : '' }}>Annulled</option>
                        </select>
                        @error('civil_status')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Date of Birth --}}
                    <div>
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
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
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
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
                            <option value="Regular"       {{ old('employee_status') == 'Regular'       ? 'selected' : '' }}>Regular</option>
                            <option value="Probationary"  {{ old('employee_status') == 'Probationary'  ? 'selected' : '' }}>Probationary</option>
                            <option value="Contractual"   {{ old('employee_status') == 'Contractual'   ? 'selected' : '' }}>Contractual</option>
                            <option value="Part-time"     {{ old('employee_status') == 'Part-time'     ? 'selected' : '' }}>Part-time</option>
                            <option value="Project-based" {{ old('employee_status') == 'Project-based' ? 'selected' : '' }}>Project-based</option>
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
                                <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
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
                        <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date') }}" required
                            class="form-control @error('hire_date') is-error @enderror">
                        @error('hire_date')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Contract End --}}
                    <div>
                        <label for="contract_end_date" class="form-label">Contract End <span style="color:#9ca3af;font-weight:400;">(Optional)</span></label>
                        <input type="date" name="contract_end_date" id="contract_end_date" value="{{ old('contract_end_date') }}"
                            class="form-control @error('contract_end_date') is-error @enderror">
                        @error('contract_end_date')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Salary Range --}}
                    <div>
                        <label for="salary" class="form-label">Salary Range (₱) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="salary" id="salary" value="{{ old('salary') }}" min="0" step="0.01" required
                            class="form-control @error('salary') is-error @enderror"
                            placeholder="0.00">
                        @error('salary')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
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
                            placeholder="Permanent / home address">{{ old('home_address') }}</textarea>
                        @error('home_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Current Address --}}
                    <div class="col-span-2">
                        <label for="current_address" class="form-label">Current Address</label>
                        <textarea name="current_address" id="current_address" rows="2"
                            class="form-control @error('current_address') is-error @enderror"
                            placeholder="Present / current address">{{ old('current_address') }}</textarea>
                        @error('current_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Email Address --}}
                    <div>
                        <label for="email" class="form-label">Email Address <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
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
                        <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number') }}"
                            class="form-control @error('mobile_number') is-error @enderror"
                            placeholder="09XX-XXX-XXXX">
                        @error('mobile_number')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Facebook --}}
                    <div>
                        <label for="facebook_link" class="form-label"><i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook Link</label>
                        <input type="url" name="facebook_link" id="facebook_link" value="{{ old('facebook_link') }}"
                            class="form-control @error('facebook_link') is-error @enderror"
                            placeholder="https://facebook.com/...">
                        @error('facebook_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- LinkedIn --}}
                    <div>
                        <label for="linkedin_link" class="form-label"><i class="fab fa-linkedin" style="color:#0a66c2;"></i> LinkedIn Link</label>
                        <input type="url" name="linkedin_link" id="linkedin_link" value="{{ old('linkedin_link') }}"
                            class="form-control @error('linkedin_link') is-error @enderror"
                            placeholder="https://linkedin.com/in/...">
                        @error('linkedin_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Instagram --}}
                    <div>
                        <label for="ig_link" class="form-label"><i class="fab fa-instagram" style="color:#e1306c;"></i> Instagram Link</label>
                        <input type="url" name="ig_link" id="ig_link" value="{{ old('ig_link') }}"
                            class="form-control @error('ig_link') is-error @enderror"
                            placeholder="https://instagram.com/...">
                        @error('ig_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Other Link --}}
                    <div>
                        <label for="other_link" class="form-label"><i class="fas fa-link" style="color:#6b7280;"></i> Other Link</label>
                        <input type="url" name="other_link" id="other_link" value="{{ old('other_link') }}"
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
                        <input type="text" name="emergency_full_name" id="emergency_full_name" value="{{ old('emergency_full_name') }}"
                            class="form-control @error('emergency_full_name') is-error @enderror"
                            placeholder="Emergency contact full name">
                        @error('emergency_full_name')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Relationship --}}
                    <div>
                        <label for="emergency_relationship" class="form-label">Relationship</label>
                        <input type="text" name="emergency_relationship" id="emergency_relationship" value="{{ old('emergency_relationship') }}"
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
                            placeholder="Contact's home address">{{ old('emergency_home_address') }}</textarea>
                        @error('emergency_home_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Current Address --}}
                    <div class="col-span-2">
                        <label for="emergency_current_address" class="form-label">Current Address</label>
                        <textarea name="emergency_current_address" id="emergency_current_address" rows="2"
                            class="form-control @error('emergency_current_address') is-error @enderror"
                            placeholder="Contact's current address">{{ old('emergency_current_address') }}</textarea>
                        @error('emergency_current_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Email --}}
                    <div>
                        <label for="emergency_email" class="form-label">Email Address</label>
                        <input type="email" name="emergency_email" id="emergency_email" value="{{ old('emergency_email') }}"
                            class="form-control @error('emergency_email') is-error @enderror"
                            placeholder="contact@example.com">
                        @error('emergency_email')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Mobile Number --}}
                    <div>
                        <label for="emergency_mobile_number" class="form-label">Mobile Number</label>
                        <input type="text" name="emergency_mobile_number" id="emergency_mobile_number" value="{{ old('emergency_mobile_number') }}"
                            class="form-control @error('emergency_mobile_number') is-error @enderror"
                            placeholder="09XX-XXX-XXXX">
                        @error('emergency_mobile_number')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Facebook --}}
                    <div>
                        <label for="emergency_facebook_link" class="form-label"><i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook Link</label>
                        <input type="url" name="emergency_facebook_link" id="emergency_facebook_link" value="{{ old('emergency_facebook_link') }}"
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
                        <input type="date" name="loan_start_date" id="loan_start_date" value="{{ old('loan_start_date') }}"
                            class="form-control @error('loan_start_date') is-error @enderror">
                        @error('loan_start_date')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- End Date --}}
                    <div>
                        <label for="loan_end_date" class="form-label">End Date</label>
                        <input type="date" name="loan_end_date" id="loan_end_date" value="{{ old('loan_end_date') }}"
                            class="form-control @error('loan_end_date') is-error @enderror">
                        @error('loan_end_date')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Total Amount --}}
                    <div>
                        <label for="loan_total_amount" class="form-label">Total Amount (₱)</label>
                        <input type="number" step="0.01" min="0" name="loan_total_amount" id="loan_total_amount" value="{{ old('loan_total_amount') }}"
                            class="form-control @error('loan_total_amount') is-error @enderror"
                            placeholder="0.00">
                        @error('loan_total_amount')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Monthly Amortization --}}
                    <div>
                        <label for="loan_monthly_amortization" class="form-label">Monthly Amortization (₱)</label>
                        <input type="number" step="0.01" min="0" name="loan_monthly_amortization" id="loan_monthly_amortization" value="{{ old('loan_monthly_amortization') }}"
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
             6. PAYMENT INFORMATION
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-payment">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-money-check-dollar"></i></div>
                <div>
                    <h3>Payment Information</h3>
                    <p>How this employee receives their pay</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-3">
                    {{-- Payment Method --}}
                    <div>
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-error @enderror">
                            <option value="">Select...</option>
                            <option value="Bank" {{ old('payment_method') == 'Bank' ? 'selected' : '' }}>Bank</option>
                            <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                        @error('payment_method')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Account No. --}}
                    <div>
                        <label for="account_no" class="form-label">Account No. <span id="account_no_required" style="color:#ef4444;display:none;">*</span></label>
                        <input type="text" name="account_no" id="account_no" value="{{ old('account_no') }}"
                            class="form-control @error('account_no') is-error @enderror">
                        @error('account_no')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Bank --}}
                    <div>
                        <label for="bank" class="form-label">Bank <span id="bank_required" style="color:#ef4444;display:none;">*</span></label>
                        <input type="text" name="bank" id="bank" value="{{ old('bank') }}"
                            class="form-control @error('bank') is-error @enderror">
                        @error('bank')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             7. ACCOUNT INFORMATION
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
                            <option value="employee" {{ old('role','employee') == 'employee' ? 'selected' : '' }}>Employee</option>
                            <option value="manager"  {{ old('role') == 'manager'  ? 'selected' : '' }}>Manager</option>
                            <option value="hr"       {{ old('role') == 'hr'       ? 'selected' : '' }}>HR</option>
                            <option value="admin"    {{ old('role') == 'admin'    ? 'selected' : '' }}>Admin</option>
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
                        <label for="password" class="form-label">Password <span style="color:#ef4444;">*</span></label>
                        <div class="pw-wrap">
                            <input type="password" name="password" id="password" required
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
                        <label for="password_confirmation" class="form-label">Confirm Password <span style="color:#ef4444;">*</span></label>
                        <div class="pw-wrap">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
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
                <i class="fas fa-user-plus"></i> Create Employee
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

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

    /* ── Payment Method → Account No. / Bank required only when "Bank" ── */
    const paymentMethodSelect = document.getElementById('payment_method');
    const accountNoInput      = document.getElementById('account_no');
    const bankInput           = document.getElementById('bank');
    const accountNoRequired   = document.getElementById('account_no_required');
    const bankRequired        = document.getElementById('bank_required');

    function togglePaymentRequiredFields() {
        const isBank = paymentMethodSelect.value === 'Bank';
        accountNoInput.required = isBank;
        bankInput.required = isBank;
        accountNoRequired.style.display = isBank ? 'inline' : 'none';
        bankRequired.style.display = isBank ? 'inline' : 'none';
    }

    paymentMethodSelect.addEventListener('change', togglePaymentRequiredFields);
    togglePaymentRequiredFields(); // in case old() has a value

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