@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'hr.my-information.info'])

@section('title', 'Personal Information')

@section('content')
<style>
/* grid helpers */
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; }
.grid-4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1.25rem; }
.col-span-2 { grid-column: span 2; }
.col-span-3 { grid-column: span 3; }

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
    .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
    .col-span-2, .col-span-3 { grid-column: span 1; }
    .emp-card-body { padding: 1rem; }
}
</style>

<div style="max-width:960px; margin:0 auto;">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.5rem;gap:1rem;flex-wrap:wrap;">
        <div>
            <h1 style="font-size:1.375rem;font-weight:700;color:#111827;margin:0;">Personal Information</h1>
            <p style="margin:.25rem 0 0;font-size:.875rem;color:#6b7280;">View and update your own employee profile.</p>
        </div>
    </div>

    {{-- Session Banners --}}
    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#15803d;font-size:.875rem;">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#b91c1c;font-size:.875rem;">
        {{ session('error') }}
    </div>
    @endif

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

    @if(!$employee)
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:1rem 1.25rem;color:#92400e;font-size:.875rem;">
            No employee record is linked to your account yet. Please contact HR/Admin.
        </div>
    @else
    @php $canEditRestricted = in_array($user->role, ['admin', 'hr']); @endphp

    {{-- ═══════════════════════════════════════════════
         PROFILE PHOTO
    ════════════════════════════════════════════════ --}}
    <div class="emp-card">
        <div style="padding:1.5rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap;">
            <div>
                <p class="form-label" style="margin-bottom:.5rem;">Profile Photo</p>
                <div class="photo-wrap" onclick="document.getElementById('photo').click();" title="Click to change photo">
                    @if($photoUrl)
                        <div class="photo-placeholder" id="photoPlaceholder" style="display:none;">
                            <i class="fas fa-user"></i>
                        </div>
                        <img id="photoPreview" src="{{ $photoUrl }}" alt="Profile" style="display:block;width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;">
                    @else
                        <div class="photo-placeholder" id="photoPlaceholder">
                            <i class="fas fa-user"></i>
                        </div>
                        <img id="photoPreview" src="" alt="Profile" style="display:none;width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;">
                    @endif
                    <div class="photo-overlay"><i class="fas fa-camera"></i></div>
                </div>
                <p class="field-hint" style="margin-top:.4rem;text-align:center;">JPG, PNG — max 3 MB</p>
            </div>

            <form method="POST" action="{{ route('hr.my-information.info.photo.upload') }}" enctype="multipart/form-data" id="photoForm" style="display:flex;align-items:center;gap:.75rem;">
                @csrf
                <input type="file" name="photo" id="photo" accept="image/jpg,image/jpeg,image/png" style="display:none;">
                <button type="submit" class="btn-save">
                    <i class="fas fa-upload"></i> Upload / Change
                </button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('hr.my-information.info.update') }}">
        @csrf
        @method('PUT')

        {{-- ═══════════════════════════════════════════════
             PERSONAL INFORMATION
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-personal">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-user"></i></div>
                <div>
                    <h3>Personal Information</h3>
                    <p>Your basic identification and demographic details</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-3">
                    {{-- Empno --}}
                    <div>
                        <label class="form-label">Empno</label>
                        <input type="text" value="{{ $employee->employee_id }}" class="form-control bg-gray-50" readonly>
                    </div>
                    {{-- Last Name --}}
                    <div>
                        <label for="last_name" class="form-label">Last Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $employee->last_name) }}" required
                            class="form-control @error('last_name') is-error @enderror">
                        @error('last_name')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- First Name --}}
                    <div>
                        <label for="first_name" class="form-label">First Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $employee->first_name) }}" required
                            class="form-control @error('first_name') is-error @enderror">
                        @error('first_name')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Sex --}}
                    <div>
                        <label for="sex" class="form-label">Sex</label>
                        <select name="sex" id="sex" class="form-control @error('sex') is-error @enderror">
                            <option value="">— Select —</option>
                            @php $sex = old('sex', $employee->sex); @endphp
                            <option value="Male" {{ $sex == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $sex == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Prefer not to say" {{ $sex == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                        @error('sex')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Date of Birth --}}
                    <div>
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', optional($employee->date_of_birth)->format('Y-m-d')) }}"
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
                    {{-- Civil Status --}}
                    <div>
                        <label for="civil_status" class="form-label">Civil Status</label>
                        <select name="civil_status" id="civil_status" class="form-control @error('civil_status') is-error @enderror">
                            @php $civilStatus = old('civil_status', $employee->civil_status); @endphp
                            <option value="">— Select —</option>
                            @foreach(['Single', 'Married', 'Widowed', 'Separated', 'Divorced'] as $status)
                                <option value="{{ $status }}" {{ $civilStatus === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('civil_status')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}"
                            class="form-control @error('phone') is-error @enderror">
                        @error('phone')
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
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             ADDRESS & CONTACT
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-details">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-address-card"></i></div>
                <div>
                    <h3>Address & Contact</h3>
                    <p>Where and how to reach you</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-2">
                    {{-- Home Address --}}
                    <div class="col-span-2">
                        <label for="home_address" class="form-label">Home Address</label>
                        <textarea name="home_address" id="home_address" rows="2"
                            class="form-control @error('home_address') is-error @enderror">{{ old('home_address', $employee->home_address) }}</textarea>
                        @error('home_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Current Address --}}
                    <div class="col-span-2">
                        <label for="current_address" class="form-label">Current Address</label>
                        <textarea name="current_address" id="current_address" rows="2"
                            class="form-control @error('current_address') is-error @enderror">{{ old('current_address', $employee->current_address) }}</textarea>
                        @error('current_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Email --}}
                    <div>
                        <label for="email" class="form-label">Email Address <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="form-control @error('email') is-error @enderror">
                        <p class="field-hint"><i class="fas fa-info-circle" style="font-size:.65rem;"></i> This will also be used as your login credential</p>
                        @error('email')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Facebook --}}
                    <div>
                        <label for="facebook_link" class="form-label"><i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook</label>
                        <input type="url" name="facebook_link" id="facebook_link" value="{{ old('facebook_link', $employee->facebook_link) }}"
                            class="form-control @error('facebook_link') is-error @enderror" placeholder="https://facebook.com/...">
                        @error('facebook_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- LinkedIn --}}
                    <div>
                        <label for="linkedin_link" class="form-label"><i class="fab fa-linkedin" style="color:#0a66c2;"></i> LinkedIn</label>
                        <input type="url" name="linkedin_link" id="linkedin_link" value="{{ old('linkedin_link', $employee->linkedin_link) }}"
                            class="form-control @error('linkedin_link') is-error @enderror" placeholder="https://linkedin.com/in/...">
                        @error('linkedin_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Instagram --}}
                    <div>
                        <label for="ig_link" class="form-label"><i class="fab fa-instagram" style="color:#e1306c;"></i> Instagram</label>
                        <input type="url" name="ig_link" id="ig_link" value="{{ old('ig_link', $employee->ig_link) }}"
                            class="form-control @error('ig_link') is-error @enderror" placeholder="https://instagram.com/...">
                        @error('ig_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Other Link --}}
                    <div>
                        <label for="other_link" class="form-label"><i class="fas fa-link" style="color:#6b7280;"></i> Other Link</label>
                        <input type="url" name="other_link" id="other_link" value="{{ old('other_link', $employee->other_link) }}"
                            class="form-control @error('other_link') is-error @enderror" placeholder="https://...">
                        @error('other_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             IN CASE OF EMERGENCY
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-emergency">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-phone-alt"></i></div>
                <div>
                    <h3>In Case of an Emergency</h3>
                    <p>Who we should contact on your behalf</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-2">
                    <div>
                        <label for="emergency_full_name" class="form-label">Full Name</label>
                        <input type="text" name="emergency_full_name" id="emergency_full_name" value="{{ old('emergency_full_name', $employee->emergency_full_name) }}"
                            class="form-control @error('emergency_full_name') is-error @enderror">
                        @error('emergency_full_name')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="emergency_relationship" class="form-label">Relationship</label>
                        <input type="text" name="emergency_relationship" id="emergency_relationship" value="{{ old('emergency_relationship', $employee->emergency_relationship) }}"
                            class="form-control @error('emergency_relationship') is-error @enderror" placeholder="e.g. Spouse, Parent, Sibling">
                        @error('emergency_relationship')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="emergency_home_address" class="form-label">Home Address</label>
                        <textarea name="emergency_home_address" id="emergency_home_address" rows="2"
                            class="form-control @error('emergency_home_address') is-error @enderror">{{ old('emergency_home_address', $employee->emergency_home_address) }}</textarea>
                        @error('emergency_home_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="emergency_current_address" class="form-label">Current Address</label>
                        <textarea name="emergency_current_address" id="emergency_current_address" rows="2"
                            class="form-control @error('emergency_current_address') is-error @enderror">{{ old('emergency_current_address', $employee->emergency_current_address) }}</textarea>
                        @error('emergency_current_address')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="emergency_email" class="form-label">Email</label>
                        <input type="email" name="emergency_email" id="emergency_email" value="{{ old('emergency_email', $employee->emergency_email) }}"
                            class="form-control @error('emergency_email') is-error @enderror">
                        @error('emergency_email')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="emergency_mobile_number" class="form-label">Mobile Number</label>
                        <input type="text" name="emergency_mobile_number" id="emergency_mobile_number" value="{{ old('emergency_mobile_number', $employee->emergency_mobile_number) }}"
                            class="form-control @error('emergency_mobile_number') is-error @enderror">
                        @error('emergency_mobile_number')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-2">
                        <label for="emergency_facebook_link" class="form-label">Facebook</label>
                        <input type="url" name="emergency_facebook_link" id="emergency_facebook_link" value="{{ old('emergency_facebook_link', $employee->emergency_facebook_link) }}"
                            class="form-control @error('emergency_facebook_link') is-error @enderror">
                        @error('emergency_facebook_link')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        @if($canEditRestricted)
        {{-- ═══════════════════════════════════════════════
             WORK INFORMATION (admin / hr only)
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-work">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-briefcase"></i></div>
                <div>
                    <h3>Work Information</h3>
                    <p>Editable because your account has admin/HR privileges</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-3">
                    <div>
                        <label for="position" class="form-label">Position</label>
                        <input type="text" name="position" id="position" value="{{ old('position', $employee->position) }}"
                            class="form-control @error('position') is-error @enderror">
                        @error('position')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-control @error('department_id') is-error @enderror">
                            <option value="">Select department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ (string) old('department_id', $employee->department_id) === (string) $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="employment_type" class="form-label">Employment Type</label>
                        <input type="text" name="employment_type" id="employment_type" value="{{ old('employment_type', $employee->employment_type) }}"
                            class="form-control @error('employment_type') is-error @enderror">
                        @error('employment_type')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="hire_date" class="form-label">Hire Date</label>
                        <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d')) }}"
                            class="form-control @error('hire_date') is-error @enderror">
                        @error('hire_date')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="salary" class="form-label">Salary</label>
                        <input type="number" step="0.01" min="0" name="salary" id="salary" value="{{ old('salary', $employee->salary) }}"
                            class="form-control @error('salary') is-error @enderror">
                        @error('salary')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- ═══════════════════════════════════════════════
             WORK INFORMATION (read-only)
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-work">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-briefcase"></i></div>
                <div>
                    <h3>Work Information</h3>
                    <p>Managed by HR/Admin</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-3">
                    <div>
                        <label class="form-label">Position</label>
                        <input type="text" value="{{ $employee->position }}" class="form-control bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="form-label">Department</label>
                        <input type="text" value="{{ $employee->department?->name }}" class="form-control bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="form-label">Hire Date</label>
                        <input type="text" value="{{ optional($employee->hire_date)->format('M d, Y') }}" class="form-control bg-gray-50" readonly>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════
             BANKING & GOVERNMENT IDS
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-statutory">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-landmark"></i></div>
                <div>
                    <h3>Banking & Government IDs</h3>
                    <p>Your financial and statutory information</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-3">
                    {{-- Payment Method --}}
                    <div>
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-error @enderror">
                            @php $paymentMethod = old('payment_method', $employee->info?->payment_method); @endphp
                            <option value="">— Select —</option>
                            <option value="Cash" {{ $paymentMethod == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Bank" {{ $paymentMethod == 'Bank' ? 'selected' : '' }}>Bank</option>
                            <option value="Cheque" {{ $paymentMethod == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                        @error('payment_method')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Bank Name --}}
                    <div>
                        <label for="bank" class="form-label">Bank Name <span id="bank_required" style="color:#ef4444;display:none;">*</span></label>
                        <input type="text" name="bank" id="bank" value="{{ old('bank', $employee->info?->bank) }}"
                            class="form-control @error('bank') is-error @enderror">
                        @error('bank')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Account No --}}
                    <div>
                        <label for="account_no" class="form-label">Account No <span id="account_no_required" style="color:#ef4444;display:none;">*</span></label>
                        <input type="text" name="account_no" id="account_no" value="{{ old('account_no', $employee->info?->account_no) }}"
                            class="form-control @error('account_no') is-error @enderror">
                        @error('account_no')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
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
                    {{-- Tax Computation Method --}}
                    <div>
                        <label for="tax_computation_method" class="form-label">Tax Computation Method</label>
                        <select name="tax_computation_method" id="tax_computation_method" class="form-control">
                            @php $taxMethod = old('tax_computation_method', $employee->tax_computation_method); @endphp
                            <option value="">— Select —</option>
                            <option value="Annualized" {{ $taxMethod == 'Annualized' ? 'selected' : '' }}>Annualized</option>
                            <option value="Standard" {{ $taxMethod == 'Standard' ? 'selected' : '' }}>Standard</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             EMPLOYEE LOANS
        ════════════════════════════════════════════════ --}}
        <div class="emp-card sec-loans">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <div>
                    <h3>Employee Loans</h3>
                    <p>Loan schedule tied to your record</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div class="grid-2">
                    <div>
                        <label for="loan_start_date" class="form-label">Loan Start Date</label>
                        <input type="date" name="loan_start_date" id="loan_start_date" value="{{ old('loan_start_date', optional($employee->loan_start_date)->format('Y-m-d')) }}"
                            class="form-control @error('loan_start_date') is-error @enderror">
                        @error('loan_start_date')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="loan_end_date" class="form-label">Loan End Date</label>
                        <input type="date" name="loan_end_date" id="loan_end_date" value="{{ old('loan_end_date', optional($employee->loan_end_date)->format('Y-m-d')) }}"
                            class="form-control @error('loan_end_date') is-error @enderror">
                        @error('loan_end_date')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="loan_total_amount" class="form-label">Total Amount</label>
                        <input type="number" step="0.01" min="0" name="loan_total_amount" id="loan_total_amount" value="{{ old('loan_total_amount', $employee->loan_total_amount) }}"
                            class="form-control @error('loan_total_amount') is-error @enderror">
                        @error('loan_total_amount')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="loan_monthly_amortization" class="form-label">Monthly Amortization</label>
                        <input type="number" step="0.01" min="0" name="loan_monthly_amortization" id="loan_monthly_amortization" value="{{ old('loan_monthly_amortization', $employee->loan_monthly_amortization) }}"
                            class="form-control @error('loan_monthly_amortization') is-error @enderror">
                        @error('loan_monthly_amortization')
                            <p class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Bar --}}
        <div class="submit-bar">
            <button type="submit" class="btn-save">
                <i class="fas fa-check"></i> Save Changes
            </button>
        </div>
    </form>
    @endif
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
        togglePaymentRequiredFields();
    }

    /* ── Profile Photo Preview ── */
    const photoInput       = document.getElementById('photo');
    const photoPreview     = document.getElementById('photoPreview');
    const photoPlaceholder = document.getElementById('photoPlaceholder');

    if (photoInput) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                photoPreview.src = e.target.result;
                photoPreview.style.display = 'block';
                if (photoPlaceholder) photoPlaceholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    }

    /* ── Age Auto-Compute ── */
    const dobInput   = document.getElementById('date_of_birth');
    const ageDisplay = document.getElementById('age_display');

    if (dobInput && ageDisplay) {
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
        computeAge();
    }
});
</script>
@endsection