@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employees.index'])

@section('title', 'Employee Details')

@section('content')
<div style="max-width:960px; margin:0 auto;">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.5rem;gap:1rem;flex-wrap:wrap;">
        <div>
            <h1 style="font-size:1.375rem;font-weight:700;color:#111827;margin:0;">{{ $employee->full_name }}</h1>
            <p style="margin:.25rem 0 0;font-size:.875rem;color:#6b7280;">Employee Details</p>
        </div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <a href="{{ route('employees.index') }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Back to Employees
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 260px;gap:1.5rem;align-items:start;">

        {{-- ══ LEFT COLUMN ══ --}}
        <div>

        {{-- Profile Photo & Number --}}
        <div class="emp-card sec-personal" style="margin-bottom:1.5rem;">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-user-circle"></i></div>
                <div>
                    <h3>Profile Overview</h3>
                    <p>Photo and employee identification</p>
                </div>
            </div>
            <div class="emp-card-body" style="display:flex;gap:1.5rem;align-items:center;flex-wrap:wrap;">
                {{-- Photo / Avatar --}}
                @if($employee->profile_photo)
                    <img src="{{ asset('storage/' . $employee->profile_photo) }}" alt="{{ $employee->full_name }}"
                         style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;flex-shrink:0;">
                @else
                    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center;flex-shrink:0;border:3px solid #e5e7eb;">
                        <span style="font-size:1.5rem;font-weight:700;color:#fff;">{{ strtoupper(substr($employee->first_name,0,1).substr($employee->last_name,0,1)) }}</span>
                    </div>
                @endif
                <div>
                    <p style="font-size:1.125rem;font-weight:700;color:#111827;margin:0;">{{ $employee->full_name }}</p>
                    <p style="font-size:.875rem;color:#6b7280;margin:.25rem 0;">{{ $employee->position?->name }} &mdash; {{ $employee->department->name }}</p>
                    <div class="emp-num-badge" style="margin-top:.5rem;">
                        <i class="fas fa-id-badge"></i>
                        <span>{{ $employee->employee_id }}</span>
                    </div>
                </div>
                <div style="margin-left:auto;">
                    @php
                        $statusColor = match($employee->employee_status) {
                            'Regular'     => ['bg'=>'#f0fdf4','border'=>'#86efac','text'=>'#16a34a'],
                            'Probationary'=> ['bg'=>'#fefce8','border'=>'#fde68a','text'=>'#ca8a04'],
                            'Contractual' => ['bg'=>'#eff6ff','border'=>'#93c5fd','text'=>'#2563eb'],
                            'Resigned'    => ['bg'=>'#fef2f2','border'=>'#fca5a5','text'=>'#dc2626'],
                            default       => ['bg'=>'#f9fafb','border'=>'#d1d5db','text'=>'#6b7280'],
                        };
                    @endphp
                    <span style="display:inline-flex;align-items:center;gap:.4rem;padding:.35rem .75rem;border-radius:9999px;font-size:.75rem;font-weight:600;
                                 background:{{ $statusColor['bg'] }};border:1px solid {{ $statusColor['border'] }};color:{{ $statusColor['text'] }};">
                        <span style="width:7px;height:7px;border-radius:50%;background:{{ $statusColor['text'] }};display:inline-block;"></span>
                        {{ $employee->employee_status }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Personal Information --}}
        <div class="emp-card sec-personal" style="margin-bottom:1.5rem;">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-user"></i></div>
                <div>
                    <h3>Personal Information</h3>
                    <p>Basic identification and demographic details</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div style="display:grid;grid-template-columns:repeat(3, minmax(0, 1fr));gap:1.25rem;align-items:start;">
                    <div>
                        <label class="form-label">Last Name</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;word-break:break-word;">{{ $employee->last_name }}</p>
                    </div>
                    <div>
                        <label class="form-label">First Name</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;word-break:break-word;">{{ $employee->first_name }}</p>
                    </div>
                    <div>
                        <label class="form-label">Middle Name</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;word-break:break-word;">{{ $employee->middle_name ?? '—' }}</p>
                    </div>
                    
                    <div>
                        <label class="form-label">Date of Birth</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;word-break:break-word;">{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : '—' }}</p>
                    </div>
                    <div>
                        <label class="form-label">Civil Status</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;word-break:break-word;">{{ $employee->civil_status ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="form-label">Email Address</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;word-break:break-word;">{{ $employee->account?->email ?? '—' }}</p>
                    </div>

                    <div>
                        <label class="form-label">Mobile Number</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;word-break:break-word;">{{ $employee->mobile_number ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="form-label">Home Address</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->home_address ?? '—' }}</p>
                    </div>

                    <div>
                        <label class="form-label">Current Address</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->current_address ?? '—' }}</p>
                    </div>

                    @if($employee->facebook_link || $employee->linkedin_link || $employee->ig_link || $employee->other_link)
                        @if($employee->facebook_link)
                        <div>
                            <label class="form-label">Facebook</label>
                            <a href="{{ $employee->facebook_link }}" target="_blank" rel="noopener" style="font-size:.875rem;color:#2563eb;">{{ $employee->facebook_link }}</a>
                        </div>
                        @endif
                        @if($employee->linkedin_link)
                        <div>
                            <label class="form-label">LinkedIn</label>
                            <a href="{{ $employee->linkedin_link }}" target="_blank" rel="noopener" style="font-size:.875rem;color:#2563eb;">{{ $employee->linkedin_link }}</a>
                        </div>
                        @endif
                        @if($employee->ig_link)
                        <div>
                            <label class="form-label">Instagram</label>
                            <a href="{{ $employee->ig_link }}" target="_blank" rel="noopener" style="font-size:.875rem;color:#2563eb;">{{ $employee->ig_link }}</a>
                        </div>
                        @endif
                        @if($employee->other_link)
                        <div>
                            <label class="form-label">Other Link</label>
                            <a href="{{ $employee->other_link }}" target="_blank" rel="noopener" style="font-size:.875rem;color:#2563eb;">{{ $employee->other_link }}</a>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Emergency Contact --}}
        @if($employee->emergency_full_name || $employee->emergency_relationship || $employee->emergency_home_address || $employee->emergency_mobile_number || $employee->emergency_email)
        <div class="emp-card sec-emergency" style="margin-bottom:1.5rem;">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-phone-alt"></i></div>
                <div>
                    <h3>In Case of an Emergency</h3>
                    <p>Emergency contact person details</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div style="display:grid;grid-template-columns:repeat(3, minmax(0, 1fr));gap:1.25rem;align-items:start;">
                    @if($employee->emergency_full_name)
                    <div>
                        <label class="form-label">Full Name</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->emergency_full_name }}</p>
                    </div>
                    @endif
                    @if($employee->emergency_relationship)
                    <div>
                        <label class="form-label">Relationship</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->emergency_relationship }}</p>
                    </div>
                    @endif
                    @if($employee->emergency_home_address)
                    <div>
                        <label class="form-label">Home Address</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->emergency_home_address }}</p>
                    </div>
                    @endif
                    @if($employee->emergency_current_address)
                    <div>
                        <label class="form-label">Current Address</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->emergency_current_address }}</p>
                    </div>
                    @endif
                    @if($employee->emergency_mobile_number)
                    <div>
                        <label class="form-label">Mobile Number</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->emergency_mobile_number }}</p>
                    </div>
                    @endif
                    @if($employee->emergency_email)
                    <div>
                        <label class="form-label">Email</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->emergency_email }}</p>
                    </div>
                    @endif
                    @if($employee->emergency_facebook_link)
                    <div>
                        <label class="form-label">Facebook</label>
                        <a href="{{ $employee->emergency_facebook_link }}" target="_blank" rel="noopener" style="font-size:.875rem;color:#2563eb;">{{ $employee->emergency_facebook_link }}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Work Information --}}
        <div class="emp-card sec-work" style="margin-bottom:1.5rem;">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-briefcase"></i></div>
                <div>
                    <h3>Work Information</h3>
                    <p>Position, salary, and employment dates</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div style="display:grid;grid-template-columns:repeat(3, minmax(0, 1fr));gap:1.25rem;align-items:start;">
                    <div>
                        <label class="form-label">Position</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->position?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="form-label">Department</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->department->name }}</p>
                    </div>
                    <div>
                        <label class="form-label">Monthly Salary</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">₱{{ number_format($employee->salary, 2) }}</p>
                    </div>
                    <div>
                        <label class="form-label">Hire Date</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->hire_date->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loans --}}
        @if($employee->loan_start_date || $employee->loan_end_date || $employee->loan_total_amount || $employee->loan_monthly_amortization)
        <div class="emp-card sec-loans" style="margin-bottom:1.5rem;">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <div>
                    <h3>Employee Loans</h3>
                    <p>Loan details and amortization schedule</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div style="display:grid;grid-template-columns:repeat(3, minmax(0, 1fr));gap:1.25rem;align-items:start;">
                    @if($employee->loan_start_date)
                    <div>
                        <label class="form-label">Start Date</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->loan_start_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($employee->loan_end_date)
                    <div>
                        <label class="form-label">End Date</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->loan_end_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if(!is_null($employee->loan_total_amount))
                    <div>
                        <label class="form-label">Total Amount</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">₱{{ number_format($employee->loan_total_amount, 2) }}</p>
                    </div>
                    @endif
                    @if(!is_null($employee->loan_monthly_amortization))
                    <div>
                        <label class="form-label">Monthly Amortization</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">₱{{ number_format($employee->loan_monthly_amortization, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Account Information --}}
        <div class="emp-card sec-account" style="margin-bottom:1.5rem;">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-user-shield"></i></div>
                <div>
                    <h3>Account Information</h3>
                    <p>System access role and activity</p>
                </div>
            </div>
            <div class="emp-card-body">
                <div style="display:grid;grid-template-columns:repeat(3, minmax(0, 1fr));gap:1.25rem;align-items:start;">
                    <div>
                        <label class="form-label">Role</label>
                        @php
                            $roleClass = match($employee->account?->role) {
                                'admin'   => 'background:#fef2f2;color:#b91c1c;border-color:#fca5a5;',
                                'hr'      => 'background:#fdf4ff;color:#7e22ce;border-color:#e9d5ff;',
                                'manager' => 'background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;',
                                default   => 'background:#f0fdf4;color:#15803d;border-color:#86efac;',
                            };
                        @endphp
                        <span style="display:inline-flex;padding:.25rem .625rem;border-radius:9999px;font-size:.75rem;font-weight:600;border:1px solid;{{ $roleClass }}">
                            {{ ucfirst($employee->account?->role ?? 'No role') }}
                        </span>
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <span style="display:inline-flex;align-items:center;gap:.375rem;padding:.25rem .625rem;border-radius:9999px;font-size:.75rem;font-weight:600;
                                     {{ $employee->account?->is_active ? 'background:#f0fdf4;color:#15803d;border:1px solid #86efac;' : 'background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5;' }}">
                            <span style="width:6px;height:6px;border-radius:50%;background:{{ $employee->account?->is_active ? '#22c55e' : '#ef4444' }};display:inline-block;"></span>
                            {{ $employee->account?->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div>
                        <label class="form-label">Last Login</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->account?->last_login_at ? $employee->account->last_login_at->format('M d, Y g:i A') : 'Never' }}</p>
                    </div>
                    <div>
                        <label class="form-label">Account Created</label>
                        <p style="font-size:.9rem;color:#111827;margin:0;">{{ $employee->account?->created_at?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attendance Records --}}
        <div class="emp-card sec-work" style="margin-bottom:1.5rem;">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-clock"></i></div>
                <div style="flex:1;">
                    <h3>Attendance Records</h3>
                    <p>Recent time-in/time-out log</p>
                </div>
                <a href="{{ route('attendance.timekeeping', ['employee_id' => $employee->id]) }}"
                   style="font-size:.8rem;color:#2563eb;font-weight:500;text-decoration:none;">
                    View All →
                </a>
            </div>
            <div class="emp-card-body">
                @if($attendanceRecords->count() > 0)
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:.8125rem;">
                        <thead>
                            <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left;padding:.5rem .75rem;color:#6b7280;font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;">Date</th>
                                <th style="text-align:left;padding:.5rem .75rem;color:#6b7280;font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;">Time In</th>
                                <th style="text-align:left;padding:.5rem .75rem;color:#6b7280;font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;">Time Out</th>
                                <th style="text-align:left;padding:.5rem .75rem;color:#6b7280;font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;">Total Hours</th>
                                <th style="text-align:left;padding:.5rem .75rem;color:#6b7280;font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceRecords as $record)
                            <tr style="border-bottom:1px solid #f1f3f5;">
                                <td style="padding:.5rem .75rem;color:#111827;">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                                <td style="padding:.5rem .75rem;color:#111827;">
                                    {{ $record->time_in ? \Carbon\Carbon::parse($record->time_in)->format('g:i A') : '—' }}
                                </td>
                                <td style="padding:.5rem .75rem;color:#111827;">
                                    @if($record->time_out)
                                        {{ \Carbon\Carbon::parse($record->time_out)->format('g:i A') }}
                                    @elseif($record->time_in && \Carbon\Carbon::parse($record->date)->isToday())
                                        <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;background:#dbeafe;color:#1d4ed8;padding:.2rem .5rem;border-radius:9999px;">
                                            <span style="width:6px;height:6px;border-radius:50%;background:#3b82f6;" class="animate-pulse"></span> Working
                                        </span>
                                    @else
                                        <span style="color:#9ca3af;">{{ $record->time_in ? 'Not Clocked Out' : '—' }}</span>
                                    @endif
                                </td>
                                <td style="padding:.5rem .75rem;color:#111827;">
                                    {{ ($record->time_out && $record->total_hours) ? \App\Helpers\TimezoneHelper::formatHours($record->total_hours) : '—' }}
                                </td>
                                <td style="padding:.5rem .75rem;">
                                    @php
                                        $sc = ['present'=>'#f0fdf4;color:#16a34a','absent'=>'#fef2f2;color:#dc2626','late'=>'#fefce8;color:#ca8a04','half_day'=>'#dbeafe;color:#1d4ed8','on_leave'=>'#eef2ff;color:#4338ca'];
                                        $s = $sc[$record->status] ?? '#f9fafb;color:#4b5563';
                                    @endphp
                                    <span style="font-size:.7rem;font-weight:600;padding:.2rem .5rem;border-radius:9999px;background:{{ $s }};">
                                        {{ ucfirst(str_replace('_',' ',$record->status)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:1rem;">{{ $attendanceRecords->links() }}</div>
                @else
                <div style="text-align:center;padding:2rem 0;">
                    <i class="fas fa-clock" style="font-size:2rem;color:#d1d5db;margin-bottom:.75rem;display:block;"></i>
                    <p style="font-size:.875rem;color:#6b7280;margin:0;">No attendance records found.</p>
                </div>
                @endif
            </div>
        </div>

        </div>{{-- end left col --}}

        {{-- ══ RIGHT SIDEBAR ══ --}}
        <div>

        {{-- Quick Actions --}}
        <div class="emp-card sec-account" style="margin-bottom:1.5rem;">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-bolt"></i></div>
                <div>
                    <h3>Quick Actions</h3>
                    <p>Employee management shortcuts</p>
                </div>
            </div>
            <div class="emp-card-body" style="padding:1rem;">
                <div style="display:flex;flex-direction:column;gap:.5rem;">
                    @if(in_array($user->role, ['admin', 'hr', 'manager']))
                    <a href="{{ route('employees.edit', $employee) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.6rem 1rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;font-weight:500;color:#374151;background:#fff;text-decoration:none;transition:background .15s;">
                        <i class="fas fa-edit"></i> Edit Employee
                    </a>
                    @endif
                    <a href="{{ route('employees.payroll', $employee) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.6rem 1rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;font-weight:500;color:#374151;background:#fff;text-decoration:none;">
                        <i class="fas fa-money-bill-wave"></i> View Payroll
                    </a>
                    <a href="{{ route('attendance.timekeeping', ['employee_id' => $employee->id]) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.6rem 1rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;font-weight:500;color:#374151;background:#fff;text-decoration:none;">
                        <i class="fas fa-clock"></i> View Attendance
                    </a>
                    @if(in_array($user->role, ['admin', 'hr', 'manager']))
                    <button type="button" onclick="openDeleteModal('{{ $employee->id }}', '{{ $employee->full_name }}')"
                            style="display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.6rem 1rem;border:1px solid #fca5a5;border-radius:8px;font-size:.875rem;font-weight:500;color:#dc2626;background:#fff;cursor:pointer;">
                        <i class="fas fa-trash"></i> Delete Employee
                    </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recent Payrolls --}}
        @if($employee->payrolls->count() > 0)
        <div class="emp-card sec-loans" style="margin-bottom:1.5rem;">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-receipt"></i></div>
                <div>
                    <h3>Recent Payrolls</h3>
                    <p>Last 3 payroll records</p>
                </div>
            </div>
            <div class="emp-card-body" style="padding:1rem;">
                <div style="display:flex;flex-direction:column;gap:.5rem;">
                    @foreach($employee->payrolls->take(3) as $payroll)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:.625rem .75rem;background:#f9fafb;border-radius:8px;border:1px solid #f1f3f5;">
                        <div>
                            <p style="font-size:.8125rem;font-weight:600;color:#111827;margin:0;">{{ $payroll->pay_period_start->format('M d') }} – {{ $payroll->pay_period_end->format('M d, Y') }}</p>
                            <p style="font-size:.7rem;color:#9ca3af;margin:.1rem 0 0;">{{ $payroll->created_at->format('M d, Y') }}</p>
                        </div>
                        <div style="text-align:right;">
                            <p style="font-size:.8125rem;font-weight:600;color:#111827;margin:0;">₱{{ number_format($payroll->gross_pay, 2) }}</p>
                            @php $pc = $payroll->status === 'paid' ? 'background:#f0fdf4;color:#15803d;' : ($payroll->status === 'pending' ? 'background:#fefce8;color:#ca8a04;' : 'background:#fef2f2;color:#dc2626;'); @endphp
                            <span style="font-size:.65rem;font-weight:600;padding:.15rem .45rem;border-radius:9999px;{{ $pc }}">{{ ucfirst($payroll->status) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div style="margin-top:.75rem;">
                    <a href="{{ route('employees.payroll', $employee) }}" style="font-size:.8rem;color:#2563eb;text-decoration:none;">View all payrolls →</a>
                </div>
            </div>
        </div>
        @endif

        </div>{{-- end right col --}}
    </div>

</div>

<script>
function openDeleteModal(employeeId, employeeName) {
    document.getElementById('deleteEmployeeId').value = employeeId;
    document.getElementById('deleteEmployeeName').textContent = employeeName;
    document.getElementById('deleteForm').action = `/employees/${employeeId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeDeleteModal()"></div>
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Employee</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Are you sure you want to delete <span id="deleteEmployeeName" class="font-semibold text-gray-900"></span>?
                    This action cannot be undone and will permanently remove all employee data including payroll records.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="deleteEmployeeId" name="employee_id" value="">
                    <button type="submit"
                        class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 transition-colors">
                        Delete Employee
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
