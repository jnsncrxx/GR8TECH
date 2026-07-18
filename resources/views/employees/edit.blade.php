@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employees.index'])

@section('title', 'Edit Employee')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Edit Employee</h1>
                <p class="mt-1 text-sm text-gray-500">Update employee information and account details</p>
            </div>
            <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                <span class="hidden sm:inline">Back to Employees</span>
                <span class="sm:hidden">Back</span>
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Personal Information -->
                <div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>

                        <!-- Profile Photo -->
                        <div class="flex flex-col items-center shrink-0">
                            <label for="profile_photo" class="cursor-pointer group relative">
                                <div class="w-20 h-20 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden group-hover:border-blue-400 transition-colors">
                                    <img id="profile_photo_preview" src="{{ $employee->profile_photo ? asset('storage/' . $employee->profile_photo) : '#' }}" alt="Profile preview" class="{{ $employee->profile_photo ? '' : 'hidden' }} w-full h-full object-cover">
                                    <i class="fas fa-camera text-gray-400 text-xl" id="profile_photo_icon" style="{{ $employee->profile_photo ? 'display:none;' : '' }}"></i>
                                </div>
                            </label>
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="hidden">
                            <span class="mt-1 text-xs text-gray-500">Upload photo</span>
                            @error('profile_photo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="sm:col-span-2">
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                            <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id', $employee->employee_id) }}"
                                placeholder="e.g., EMP-0001 or leave blank for auto-generation"
                                maxlength="20" autocomplete="off" autocorrect="off" spellcheck="false"
                                pattern="[A-Za-z0-9\-]*" title="Letters, numbers, and hyphens only"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('employee_id') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate (EMP-XXXX format)</p>
                            @error('employee_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $employee->first_name) }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('first_name') border-red-500 @enderror">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-2">Middle Name <span class="text-gray-400 font-normal">(optional)</span></label>
                                <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $employee->middle_name) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('middle_name') border-red-500 @enderror">
                                @error('middle_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $employee->last_name) }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('last_name') border-red-500 @enderror">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $employee->account?->email) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date_of_birth') border-red-500 @enderror">
                            @error('date_of_birth')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="civil_status" class="block text-sm font-medium text-gray-700 mb-2">Civil Status</label>
                            <select name="civil_status" id="civil_status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('civil_status') border-red-500 @enderror">
                                <option value="">Select Civil Status</option>
                                <option value="single" {{ old('civil_status', $employee->civil_status) == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('civil_status', $employee->civil_status) == 'married' ? 'selected' : '' }}>Married</option>
                                <option value="widowed" {{ old('civil_status', $employee->civil_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="divorced" {{ old('civil_status', $employee->civil_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="separated" {{ old('civil_status', $employee->civil_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                            @error('civil_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sex" class="block text-sm font-medium text-gray-700 mb-2">Sex</label>
                            <select name="sex" id="sex"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sex') border-red-500 @enderror">
                                <option value="">Select Sex</option>
                                <option value="male" {{ old('sex', $employee->sex) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('sex', $employee->sex) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('sex')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Work Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Work Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <select name="department_id" id="department_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('department_id') border-red-500 @enderror">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                            <input type="text" name="position" id="position" value="{{ old('position', optional($employee->position)->name) }}" required list="positions-list"
                                placeholder="Enter position name (e.g., Software Engineer)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('position') border-red-500 @enderror">
                            <datalist id="positions-list">
                                @foreach($positions as $position)
                                    <option value="{{ $position->name }}">
                                @endforeach
                            </datalist>
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="salary" class="block text-sm font-medium text-gray-700 mb-2">Monthly Salary (₱)</label>
                            <input type="number" name="salary" id="salary" value="{{ old('salary', $employee->salary) }}" min="0" step="0.01" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('salary') border-red-500 @enderror">
                            @error('salary')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="payroll_template_id" class="block text-sm font-medium text-gray-700 mb-2">Payroll Template (Optional)</label>
                            <select name="payroll_template_id" id="payroll_template_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payroll_template_id') border-red-500 @enderror">
                                <option value="">System Default (No Template)</option>
                                @foreach($payrollTemplates as $template)
                                    <option value="{{ $template->id }}" {{ old('payroll_template_id', $employee->payroll_template_id) == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Overrides Position and System Default templates.</p>
                            @error('payroll_template_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-2">Hire Date</label>
                            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('hire_date') border-red-500 @enderror">
                            @error('hire_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Employee Details -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Employee Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Employee's Address</label>
                            <textarea name="address" id="address" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">Employee's Mobile Number</label>
                            <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $employee->mobile_number) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('mobile_number') border-red-500 @enderror">
                            @error('mobile_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="facebook_link" class="block text-sm font-medium text-gray-700 mb-2">Facebook Link</label>
                            <input type="url" name="facebook_link" id="facebook_link" value="{{ old('facebook_link', $employee->facebook_link) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('facebook_link') border-red-500 @enderror">
                            @error('facebook_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="linkedin_link" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn Link</label>
                            <input type="url" name="linkedin_link" id="linkedin_link" value="{{ old('linkedin_link', $employee->linkedin_link) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('linkedin_link') border-red-500 @enderror">
                            @error('linkedin_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="ig_link" class="block text-sm font-medium text-gray-700 mb-2">IG Link</label>
                            <input type="url" name="ig_link" id="ig_link" value="{{ old('ig_link', $employee->ig_link) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ig_link') border-red-500 @enderror">
                            @error('ig_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="other_link" class="block text-sm font-medium text-gray-700 mb-2">Others Link</label>
                            <input type="url" name="other_link" id="other_link" value="{{ old('other_link', $employee->other_link) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('other_link') border-red-500 @enderror">
                            @error('other_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Emergency Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Emergency Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="emergency_full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="emergency_full_name" id="emergency_full_name" value="{{ old('emergency_full_name', $employee->emergency_full_name) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_full_name') border-red-500 @enderror">
                            @error('emergency_full_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="emergency_relationship" class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                            <input type="text" name="emergency_relationship" id="emergency_relationship" value="{{ old('emergency_relationship', $employee->emergency_relationship) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_relationship') border-red-500 @enderror">
                            @error('emergency_relationship')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="emergency_address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="emergency_address" id="emergency_address" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_address') border-red-500 @enderror">{{ old('emergency_address', $employee->emergency_address) }}</textarea>
                            @error('emergency_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="emergency_mobile_number" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                            <input type="text" name="emergency_mobile_number" id="emergency_mobile_number" value="{{ old('emergency_mobile_number', $employee->emergency_mobile_number) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_mobile_number') border-red-500 @enderror">
                            @error('emergency_mobile_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="emergency_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="emergency_email" id="emergency_email" value="{{ old('emergency_email', $employee->emergency_email) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_email') border-red-500 @enderror">
                            @error('emergency_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Employee Loans -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Loans</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="loan_start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="loan_start_date" id="loan_start_date" value="{{ old('loan_start_date', $employee->loan_start_date?->format('Y-m-d')) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('loan_start_date') border-red-500 @enderror">
                            @error('loan_start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="loan_end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="loan_end_date" id="loan_end_date" value="{{ old('loan_end_date', $employee->loan_end_date?->format('Y-m-d')) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('loan_end_date') border-red-500 @enderror">
                            @error('loan_end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="loan_total_amount" class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                            <input type="number" step="0.01" min="0" name="loan_total_amount" id="loan_total_amount" value="{{ old('loan_total_amount', $employee->loan_total_amount) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('loan_total_amount') border-red-500 @enderror">
                            @error('loan_total_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="loan_monthly_amortization" class="block text-sm font-medium text-gray-700 mb-2">Monthly Amortization</label>
                            <input type="number" step="0.01" min="0" name="loan_monthly_amortization" id="loan_monthly_amortization" value="{{ old('loan_monthly_amortization', $employee->loan_monthly_amortization) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('loan_monthly_amortization') border-red-500 @enderror">
                            @error('loan_monthly_amortization')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <select name="role" id="role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror">
                                <option value="employee" {{ old('role', $employee->account?->role) == 'employee' ? 'selected' : '' }}>Employee</option>
                                <option value="manager" {{ old('role', $employee->account?->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="hr" {{ old('role', $employee->account?->role) == 'hr' ? 'selected' : '' }}>HR</option>
                                <option value="admin" {{ old('role', $employee->account?->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 focus:outline-none text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password. Must be at least 8 characters if changing.</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 focus:outline-none text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-red-600 hidden" id="password_mismatch_error">Passwords do not match</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('employees.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggles
    function wireToggle(buttonId, inputId, iconId) {
        const toggle = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (toggle && input) {
            toggle.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);

                if (type === 'password') {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        }
    }

    wireToggle('togglePassword', 'password', 'toggleIcon');
    wireToggle('toggleConfirmPassword', 'password_confirmation', 'toggleConfirmIcon');

    // Confirm password match validation (only enforced if a new password is entered)
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const mismatchError = document.getElementById('password_mismatch_error');
    const form = document.querySelector('form');

    function checkPasswordMatch() {
        if (passwordInput.value || confirmInput.value) {
            if (passwordInput.value !== confirmInput.value) {
                mismatchError.classList.remove('hidden');
                confirmInput.setCustomValidity('Passwords do not match');
                return;
            }
        }
        mismatchError.classList.add('hidden');
        confirmInput.setCustomValidity('');
    }

    if (passwordInput && confirmInput) {
        passwordInput.addEventListener('input', checkPasswordMatch);
        confirmInput.addEventListener('input', checkPasswordMatch);
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            checkPasswordMatch();
            if (confirmInput && passwordInput.value !== confirmInput.value) {
                e.preventDefault();
            }
        });
    }

    // Profile photo preview
    const photoInput = document.getElementById('profile_photo');
    const photoPreview = document.getElementById('profile_photo_preview');
    const photoIcon = document.getElementById('profile_photo_icon');

    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const file = this.files && this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoPreview.classList.remove('hidden');
                    if (photoIcon) photoIcon.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employees.index'])

@section('title', 'Edit Employee')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Edit Employee</h1>
                <p class="mt-1 text-sm text-gray-500">Update employee information and account details</p>
            </div>
            <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                <span class="hidden sm:inline">Back to Employees</span>
                <span class="sm:hidden">Back</span>
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Personal Information -->
                <div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>

                        <!-- Profile Photo -->
                        <div class="flex flex-col items-center shrink-0">
                            <label for="profile_photo" class="cursor-pointer group relative">
                                <div class="w-20 h-20 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden group-hover:border-blue-400 transition-colors">
                                    <img id="profile_photo_preview" src="{{ $employee->profile_photo ? asset('storage/' . $employee->profile_photo) : '#' }}" alt="Profile preview" class="{{ $employee->profile_photo ? '' : 'hidden' }} w-full h-full object-cover">
                                    <i class="fas fa-camera text-gray-400 text-xl" id="profile_photo_icon" style="{{ $employee->profile_photo ? 'display:none;' : '' }}"></i>
                                </div>
                            </label>
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="hidden">
                            <span class="mt-1 text-xs text-gray-500">Upload photo</span>
                            @error('profile_photo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="sm:col-span-2">
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                            <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id', $employee->employee_id) }}"
                                placeholder="e.g., EMP-0001 or leave blank for auto-generation"
                                maxlength="20" autocomplete="off" autocorrect="off" spellcheck="false"
                                pattern="[A-Za-z0-9\-]*" title="Letters, numbers, and hyphens only"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('employee_id') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate (EMP-XXXX format)</p>
                            @error('employee_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $employee->first_name) }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('first_name') border-red-500 @enderror">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-2">Middle Name <span class="text-gray-400 font-normal">(optional)</span></label>
                                <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $employee->middle_name) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('middle_name') border-red-500 @enderror">
                                @error('middle_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $employee->last_name) }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('last_name') border-red-500 @enderror">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $employee->account?->email) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date_of_birth') border-red-500 @enderror">
                            @error('date_of_birth')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="civil_status" class="block text-sm font-medium text-gray-700 mb-2">Civil Status</label>
                            <select name="civil_status" id="civil_status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('civil_status') border-red-500 @enderror">
                                <option value="">Select Civil Status</option>
                                <option value="single" {{ old('civil_status', $employee->civil_status) == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('civil_status', $employee->civil_status) == 'married' ? 'selected' : '' }}>Married</option>
                                <option value="widowed" {{ old('civil_status', $employee->civil_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="divorced" {{ old('civil_status', $employee->civil_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="separated" {{ old('civil_status', $employee->civil_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                            @error('civil_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sex" class="block text-sm font-medium text-gray-700 mb-2">Sex</label>
                            <select name="sex" id="sex"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sex') border-red-500 @enderror">
                                <option value="">Select Sex</option>
                                <option value="male" {{ old('sex', $employee->sex) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('sex', $employee->sex) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('sex')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Work Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Work Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <select name="department_id" id="department_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('department_id') border-red-500 @enderror">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                            <input type="text" name="position" id="position" value="{{ old('position', optional($employee->position)->name) }}" required list="positions-list"
                                placeholder="Enter position name (e.g., Software Engineer)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('position') border-red-500 @enderror">
                            <datalist id="positions-list">
                                @foreach($positions as $position)
                                    <option value="{{ $position->name }}">
                                @endforeach
                            </datalist>
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="salary" class="block text-sm font-medium text-gray-700 mb-2">Monthly Salary (₱)</label>
                            <input type="number" name="salary" id="salary" value="{{ old('salary', $employee->salary) }}" min="0" step="0.01" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('salary') border-red-500 @enderror">
                            @error('salary')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="payroll_template_id" class="block text-sm font-medium text-gray-700 mb-2">Payroll Template (Optional)</label>
                            <select name="payroll_template_id" id="payroll_template_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payroll_template_id') border-red-500 @enderror">
                                <option value="">System Default (No Template)</option>
                                @foreach($payrollTemplates as $template)
                                    <option value="{{ $template->id }}" {{ old('payroll_template_id', $employee->payroll_template_id) == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Overrides Position and System Default templates.</p>
                            @error('payroll_template_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-2">Hire Date</label>
                            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('hire_date') border-red-500 @enderror">
                            @error('hire_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Employee Details -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Employee Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Employee's Address</label>
                            <textarea name="address" id="address" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">Employee's Mobile Number</label>
                            <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $employee->mobile_number) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('mobile_number') border-red-500 @enderror">
                            @error('mobile_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="facebook_link" class="block text-sm font-medium text-gray-700 mb-2">Facebook Link</label>
                            <input type="url" name="facebook_link" id="facebook_link" value="{{ old('facebook_link', $employee->facebook_link) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('facebook_link') border-red-500 @enderror">
                            @error('facebook_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="linkedin_link" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn Link</label>
                            <input type="url" name="linkedin_link" id="linkedin_link" value="{{ old('linkedin_link', $employee->linkedin_link) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('linkedin_link') border-red-500 @enderror">
                            @error('linkedin_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="ig_link" class="block text-sm font-medium text-gray-700 mb-2">IG Link</label>
                            <input type="url" name="ig_link" id="ig_link" value="{{ old('ig_link', $employee->ig_link) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ig_link') border-red-500 @enderror">
                            @error('ig_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="other_link" class="block text-sm font-medium text-gray-700 mb-2">Others Link</label>
                            <input type="url" name="other_link" id="other_link" value="{{ old('other_link', $employee->other_link) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('other_link') border-red-500 @enderror">
                            @error('other_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Emergency Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Emergency Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="emergency_full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="emergency_full_name" id="emergency_full_name" value="{{ old('emergency_full_name', $employee->emergency_full_name) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_full_name') border-red-500 @enderror">
                            @error('emergency_full_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="emergency_relationship" class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                            <input type="text" name="emergency_relationship" id="emergency_relationship" value="{{ old('emergency_relationship', $employee->emergency_relationship) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_relationship') border-red-500 @enderror">
                            @error('emergency_relationship')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="emergency_address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="emergency_address" id="emergency_address" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_address') border-red-500 @enderror">{{ old('emergency_address', $employee->emergency_address) }}</textarea>
                            @error('emergency_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="emergency_mobile_number" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                            <input type="text" name="emergency_mobile_number" id="emergency_mobile_number" value="{{ old('emergency_mobile_number', $employee->emergency_mobile_number) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_mobile_number') border-red-500 @enderror">
                            @error('emergency_mobile_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="emergency_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="emergency_email" id="emergency_email" value="{{ old('emergency_email', $employee->emergency_email) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_email') border-red-500 @enderror">
                            @error('emergency_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Employee Loans -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Loans</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="loan_start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="loan_start_date" id="loan_start_date" value="{{ old('loan_start_date', $employee->loan_start_date?->format('Y-m-d')) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('loan_start_date') border-red-500 @enderror">
                            @error('loan_start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="loan_end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="loan_end_date" id="loan_end_date" value="{{ old('loan_end_date', $employee->loan_end_date?->format('Y-m-d')) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('loan_end_date') border-red-500 @enderror">
                            @error('loan_end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="loan_total_amount" class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                            <input type="number" step="0.01" min="0" name="loan_total_amount" id="loan_total_amount" value="{{ old('loan_total_amount', $employee->loan_total_amount) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('loan_total_amount') border-red-500 @enderror">
                            @error('loan_total_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="loan_monthly_amortization" class="block text-sm font-medium text-gray-700 mb-2">Monthly Amortization</label>
                            <input type="number" step="0.01" min="0" name="loan_monthly_amortization" id="loan_monthly_amortization" value="{{ old('loan_monthly_amortization', $employee->loan_monthly_amortization) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('loan_monthly_amortization') border-red-500 @enderror">
                            @error('loan_monthly_amortization')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <select name="role" id="role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror">
                                <option value="employee" {{ old('role', $employee->account?->role) == 'employee' ? 'selected' : '' }}>Employee</option>
                                <option value="manager" {{ old('role', $employee->account?->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="hr" {{ old('role', $employee->account?->role) == 'hr' ? 'selected' : '' }}>HR</option>
                                <option value="admin" {{ old('role', $employee->account?->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 focus:outline-none text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password. Must be at least 8 characters if changing.</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 focus:outline-none text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-red-600 hidden" id="password_mismatch_error">Passwords do not match</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('employees.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggles
    function wireToggle(buttonId, inputId, iconId) {
        const toggle = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (toggle && input) {
            toggle.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);

                if (type === 'password') {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        }
    }

    wireToggle('togglePassword', 'password', 'toggleIcon');
    wireToggle('toggleConfirmPassword', 'password_confirmation', 'toggleConfirmIcon');

    // Confirm password match validation (only enforced if a new password is entered)
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const mismatchError = document.getElementById('password_mismatch_error');
    const form = document.querySelector('form');

    function checkPasswordMatch() {
        if (passwordInput.value || confirmInput.value) {
            if (passwordInput.value !== confirmInput.value) {
                mismatchError.classList.remove('hidden');
                confirmInput.setCustomValidity('Passwords do not match');
                return;
            }
        }
        mismatchError.classList.add('hidden');
        confirmInput.setCustomValidity('');
    }

    if (passwordInput && confirmInput) {
        passwordInput.addEventListener('input', checkPasswordMatch);
        confirmInput.addEventListener('input', checkPasswordMatch);
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            checkPasswordMatch();
            if (confirmInput && passwordInput.value !== confirmInput.value) {
                e.preventDefault();
            }
        });
    }

    // Profile photo preview
    const photoInput = document.getElementById('profile_photo');
    const photoPreview = document.getElementById('profile_photo_preview');
    const photoIcon = document.getElementById('profile_photo_icon');

    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const file = this.files && this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoPreview.classList.remove('hidden');
                    if (photoIcon) photoIcon.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection