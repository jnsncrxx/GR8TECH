@props(['user', 'activeRoute' => 'dashboard'])

@php
    $currentCompany = \App\Helpers\CompanyHelper::getCurrentCompany();

    $isCurrentlyTimedIn = false;
    $todayAttendance = null;
    if ($user->employee) {
        $todayAttendance = $user->employee->getTodayAttendance();
        $isCurrentlyTimedIn = $todayAttendance && $todayAttendance->hasActiveTimeEntry();
    }

    // ---- group membership, used to decide which group starts open ----
    // attendance.overtime / leave-management / official-business are shared
    // pages: HR/admin can view either "my own" (My Portal, ?scope=mine) or
    // the team-wide management view (Time & Workforce). Only the query
    // param tells them apart, so they're deliberately left out of the plain
    // route-name arrays below and handled via $isMineScope instead -
    // otherwise both sections would light up/open at once regardless of
    // which view is actually showing.
    $isMineScope = request()->query('scope') === 'mine';
    $activeReportType = request()->query('report_type', 'daily');
    $sharedScopedRoutes = ['attendance.overtime', 'attendance.leave-management', 'attendance.official-business'];

    $myPortalRoutes = [
        'attendance.time-in-out', 'attendance.my', 'employee.schedule',
        'employee.payroll.history', 'loans.index', 'loans.create', 'loans.show',
    ];
    $myPortalActive = $isMineScope || in_array($activeRoute, $myPortalRoutes, true);

    // Force My Portal to be active if on Time In/Out page
    if ($activeRoute === 'attendance.time-in-out') {
        $myPortalActive = true;
    }

    $workforceRoutes = [
        'employees.index', 'employees.info', 'employees.other-employee-info', 'employees.education-training-rating',
        'employees.prev-emp-oth', 'employees.documents', 'employees.ytd-info', 'employees.bio-zk',
        'departments.index', 'positions.index', 'companies.index', 'documents.index',
    ];

    $timeRoutes = [
        'attendance.daily', 'attendance.timekeeping', 'attendance.import-dtr',
        'schedule-v2.index', 'schedule-v2.create', 'schedule-v2.show', 'schedule-v2.edit',
        'schedule-templates.index', 'schedule-templates.create', 'schedule-templates.edit',
        'attendance.leave-management.create', 'attendance.period-management.index',
        'attendance.period-management.create', 'attendance.period-management.show',
    ];
    $timeRoutesActive = in_array($activeRoute, $timeRoutes, true)
        || (!$isMineScope && in_array($activeRoute, $sharedScopedRoutes, true));
    $timeAttendanceActive = $timeRoutesActive
        || (!$isMineScope && in_array($activeRoute, ['attendance.leave-management', 'attendance.overtime', 'attendance.official-business'], true));

    $payrollFinanceRoutes = [
    'payroll.index', 'payroll.team', 'payroll.runs',
    'payroll-templates.index', 'payroll-templates.create', 'payroll-templates.edit', 
    'payroll-adjustments.index', 'payroll-adjustments.create', 'payroll-adjustments.edit',
    'loans.index', 'loans.create', 'loans.show',
    'loan-types.index', 'loan-types.create', 'loan-types.edit',
    'tax-brackets.index', 'reports.index', 'payrolls.summary',
    ];

    $accessSecurityRoutes = [
        'developer.accounts.index', 'developer.accounts.edit',
        'developer.permissions.index', 'developer.accounts.link', 'developer.activity-logs.index',
    ];

    $systemSettingsRoutes = ['hr.settings', 'developer.recycle-bin.index', 'developer.database-backup.index', 'attendance.settings'];

    $reportsHubRoutes = [
        'attendance.reports', 'attendance.daily', 'attendance.timekeeping', 'attendance.import-dtr',
        'reports.index', 'payrolls.summary'
    ];

    $adminRoutes = ['developer.accounts.index', 'developer.accounts.edit', 'developer.accounts.link', 'developer.permissions.index', 'developer.activity-logs.index', 'developer.recycle-bin.index', 'developer.database-backup.index', 'attendance.settings'];

    // real pending-approval counts for individual sections (HR/admin only)
    $pendingLeaveCount = 0;
    $pendingOvertimeCount = 0;
    $pendingOfficialBusinessCount = 0;
    $pendingApprovalsCount = 0;

    if (in_array($user->role, ['admin', 'hr'], true)) {
        try {
            $pendingLeaveCount = \App\Models\LeaveRequest::where('status', 'pending')->count();
            $pendingOvertimeCount = \App\Models\OvertimeRequest::where('status', 'pending')->count();
            $pendingOfficialBusinessCount = \App\Models\OfficialBusinessRequest::where('status', 'pending')->count();
            $pendingApprovalsCount = $pendingLeaveCount + $pendingOvertimeCount + $pendingOfficialBusinessCount;
        } catch (\Exception $e) {
            $pendingLeaveCount = 0;
            $pendingOvertimeCount = 0;
            $pendingOfficialBusinessCount = 0;
            $pendingApprovalsCount = 0;
        }
    }
@endphp

<nav class="brand-sidebar-nav mt-4 px-4 pb-4">
    <div class="space-y-1">

        {{-- ============ HOME ============ --}}
        <div class="px-2 pt-2 pb-1">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-home text-[10px]"></i> Home
            </h3>
        </div>

        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'dashboard' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-tachometer-alt mr-3 text-lg {{ $activeRoute === 'dashboard' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Dashboard</span>
        </a>

        @if($user->employee)
        <div class="relative" x-data="{ open: {{ $myPortalActive ? 'true' : 'false' }} }">
            <button @click="open = !open" class="group flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 {{ $myPortalActive ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                <span class="flex items-center">
                    <i class="fas fa-user-circle mr-3 text-lg {{ $myPortalActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>My Portal</span>
                </span>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 rounded-lg border border-gray-200 bg-gray-50 p-2">
                <a href="{{ route('attendance.time-in-out') }}" class="flex items-center rounded-md px-3 py-2 text-sm transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'attendance.time-in-out' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                    <i class="fas fa-sign-in-alt mr-3 text-sm {{ $activeRoute === 'attendance.time-in-out' ? 'text-blue-600' : 'text-gray-400' }}"></i>
                    <span>Time In / Out</span>
                    <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">Live</span>
                </a>
                <a href="{{ route('attendance.my') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'attendance.my' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-calendar-check mr-3 text-sm {{ $activeRoute === 'attendance.my' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Attendance</span>
                </a>
                <a href="{{ route('employee.schedule') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'employee.schedule' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-calendar-alt mr-3 text-sm {{ $activeRoute === 'employee.schedule' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Schedule</span>
                </a>
                <a href="{{ route('attendance.overtime', ['scope' => 'mine']) }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'attendance.overtime' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-clock mr-3 text-sm {{ $activeRoute === 'attendance.overtime' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Overtime</span>
                </a>
                <a href="{{ route('attendance.leave-management', ['scope' => 'mine']) }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'attendance.leave-management' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-calendar-times mr-3 text-sm {{ $activeRoute === 'attendance.leave-management' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Leave</span>
                </a>
                <a href="{{ route('attendance.official-business', ['scope' => 'mine']) }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $isMineScope && $activeRoute === 'attendance.official-business' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-briefcase mr-3 text-sm {{ $isMineScope && $activeRoute === 'attendance.official-business' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Official Business</span>
                </a>
                <a href="{{ route('employee.payroll.history') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'employee.payroll.history' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-receipt mr-3 text-sm {{ $activeRoute === 'employee.payroll.history' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Payslips</span>
                </a>
                <a href="{{ route('loans.index', ['scope' => 'mine']) }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'loans.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-hand-holding-dollar mr-3 text-sm {{ $activeRoute === 'loans.index' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Loans</span>
                </a>
            </div>
        </div>
        @endif

        {{-- ============ WORKFORCE & OPERATIONS ============ --}}
        <div class="px-2 pt-4 pb-1">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-people-group text-[10px]"></i> Workforce & Operations
            </h3>
        </div>

        @if($user->role === 'admin' || $user->role === 'hr')
            @php
                $employeeCount = $currentCompany ? \App\Models\Employee::forCompany($currentCompany->id)->count() : \App\Models\Employee::count();
                $employeeRoutes = ['employees.index', 'employees.info', 'employees.other-employee-info', 'employees.education-training-rating', 'employees.prev-emp-oth', 'employees.documents', 'employees.ytd-info', 'employees.bio-zk'];
            @endphp
            <div class="relative" x-data="{ open: {{ in_array($activeRoute, $employeeRoutes) ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $employeeRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-address-book mr-3 text-lg {{ in_array($activeRoute, $employeeRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                        <span>Employee Directory</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                    <a href="{{ route('employees.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'employees.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                        <span>Employee List</span>
                        <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $employeeCount }}</span>
                    </a>
                    <a href="{{ route('employees.info') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'employees.info' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-id-card mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                        <span>Employee Info</span>
                    </a>
                    <a href="{{ route('employees.other-employee-info') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'employees.other-employee-info' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-user-circle mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                        <span>Other Employee Info</span>
                    </a>
                    <a href="{{ route('employees.education-training-rating') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'employees.education-training-rating' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-book mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                        <span>Education/ Training/ Rating</span>
                    </a>
                    <a href="{{ route('employees.prev-emp-oth') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'employees.prev-emp-oth' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-briefcase mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                        <span>Previous Employer & Other</span>
                    </a>
                    <a href="{{ route('employees.documents') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'employees.documents' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-file-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                        <span>Documents</span>
                    </a>
                    <a href="{{ route('employees.ytd-info') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'employees.ytd-info' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-passport mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                        <span>YTD - INFO</span>
                    </a>
                    <a href="{{ route('employees.bio-zk') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'employees.bio-zk' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-dna mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                        <span>Bio ZK</span>
                    </a>
                </div>
            </div>
        @endif

        <div class="relative" x-data="{ open: {{ $timeAttendanceActive ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ $timeAttendanceActive ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-clock mr-3 text-lg {{ $timeAttendanceActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Time & Attendance</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($pendingApprovalsCount > 0)
                        <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full normal-case tracking-normal">{{ $pendingApprovalsCount }}</span>
                    @endif
                    <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                <a href="{{ route('attendance.daily') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.daily' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-calendar-day mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Attendance Record</span></a>
                @if($user->role !== 'employee')
                    <a href="{{ route('attendance.timekeeping') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.timekeeping' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-stopwatch mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Timekeeping</span></a>
                    <a href="{{ route('attendance.import-dtr') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.import-dtr' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-file-import mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Import DTR</span></a>
                @endif
                @if(in_array($user->role, ['admin', 'hr', 'manager']))
                    <a href="{{ route('schedule-v2.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ in_array($activeRoute, ['schedule-v2.index', 'schedule-v2.create', 'schedule-v2.show', 'schedule-v2.edit']) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-calendar-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Schedules</span></a>
                @endif
                <a href="{{ route('attendance.leave-management') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.leave-management' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-calendar-times mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Leave</span>@if($pendingLeaveCount > 0)<span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $pendingLeaveCount }}</span>@endif</a>
                <a href="{{ route('attendance.overtime') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.overtime' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-business-time mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Overtime</span>@if($pendingOvertimeCount > 0)<span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $pendingOvertimeCount }}</span>@endif</a>
                <a href="{{ route('attendance.official-business') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ !$isMineScope && $activeRoute === 'attendance.official-business' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-briefcase mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Official Business</span>@if($pendingOfficialBusinessCount > 0)<span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $pendingOfficialBusinessCount }}</span>@endif</a>
                @if(in_array($user->role, ['admin', 'hr']))
                    <a href="{{ route('attendance.period-management.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.period-management.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-calendar-week mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Period Management</span></a>
                @endif
            </div>
        </div>

        <a href="{{ route('documents.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('documents.*') ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-folder mr-3 text-lg {{ request()->routeIs('documents.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Documents & Files</span>
        </a>

        {{-- ============ PAYROLL & FINANCE ============ --}}
        @if($user->role === 'admin' || $user->role === 'hr' || $user->role === 'manager')
        <div class="px-2 pt-4 pb-1">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-sack-dollar text-[10px]"></i> Payroll & Finance
            </h3>
        </div>

        @php $payrollRunRoutes = ['payroll.index', 'payroll.team', 'payroll.runs', 'payroll-templates.index', 'payroll-templates.create', 'payroll-templates.edit', 'loans.index', 'loans.show', 'loan-types.index', 'loan-types.create', 'loan-types.edit', 'payroll-adjustments.index', 'payroll-adjustments.create', 'payroll-adjustments.edit']; @endphp
        <div class="relative" x-data="{ open: {{ in_array($activeRoute, $payrollRunRoutes) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $payrollRunRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-money-bill-wave mr-3 text-lg {{ in_array($activeRoute, $payrollRunRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Run Payroll</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                @if($user->role === 'admin' || $user->role === 'hr')
                    <a href="{{ route('payroll.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'payroll.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Payroll Payments</span></a>
                    <a href="{{ route('payroll.runs') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'payroll.runs' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-layer-group mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Payroll Runs</span></a>
                    <a href="{{ route('payroll-templates.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ in_array($activeRoute, ['payroll-templates.index', 'payroll-templates.create', 'payroll-templates.edit'], true) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-file-invoice mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Payroll Templates</span></a>
                    <a href="{{ route('payroll-adjustments.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ in_array($activeRoute, ['payroll-adjustments.index', 'payroll-adjustments.create', 'payroll-adjustments.edit'], true) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-sliders-h mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Payroll Adjustments</span></a>
                    <a href="{{ route('loans.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ in_array($activeRoute, ['loans.index', 'loans.create', 'loans.show', 'loan-types.index', 'loan-types.create', 'loan-types.edit'], true) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-hand-holding-dollar mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Loan Management</span></a>
                @else
                    <a href="{{ route('loans.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ in_array($activeRoute, ['loans.index', 'loans.show'], true) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-hand-holding-dollar mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Loan Approvals</span></a>
                    <a href="{{ route('payroll.team') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'payroll.team' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-eye mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>My Team's Payroll</span></a>
                @endif
            </div>
        </div>

        @if($user->role === 'admin' || $user->role === 'hr')
        <a href="{{ route('tax-brackets.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'tax-brackets.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-percentage mr-3 text-lg {{ $activeRoute === 'tax-brackets.index' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Tax & Government Tables</span>
            <span class="ml-auto bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">New</span>
        </a>
        @endif
        @endif

        {{-- ============ REPORTS HUB ============ --}}
        <div class="px-2 pt-4 pb-1">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-chart-pie text-[10px]"></i> Reports Hub
            </h3>
        </div>

        <div class="relative" x-data="{ open: {{ in_array($activeRoute, $reportsHubRoutes) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $reportsHubRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-chart-bar mr-3 text-lg {{ in_array($activeRoute, $reportsHubRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Reports</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                <a href="{{ route('attendance.reports', ['report_type' => 'monthly']) }}" class="flex items-center px-3 py-2 text-sm hover:bg-white hover:text-blue-600 rounded-md group {{ $activeReportType === 'monthly' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700' }}"><i class="fas fa-chart-pie mr-3 text-sm {{ $activeReportType === 'monthly' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i><span>Time Summary</span></a>
                <a href="{{ route('attendance.reports', ['report_type' => 'absences']) }}" class="flex items-center px-3 py-2 text-sm hover:bg-white hover:text-blue-600 rounded-md group {{ $activeReportType === 'absences' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700' }}"><i class="fas fa-user-slash mr-3 text-sm {{ $activeReportType === 'absences' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i><span>Absences</span></a>
                @if(in_array($user->role, ['admin', 'hr', 'manager']))
                    <a href="{{ route('attendance.timekeeping', ['exception' => 'incomplete']) }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-exclamation-triangle mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Undertime & Tardiness</span></a>
                @endif
                <a href="{{ route('attendance.reports', ['report_type' => 'overtime']) }}" class="flex items-center px-3 py-2 text-sm hover:bg-white hover:text-blue-600 rounded-md group {{ $activeReportType === 'overtime' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700' }}"><i class="fas fa-chart-line mr-3 text-sm {{ $activeReportType === 'overtime' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i><span>Overtime Report</span></a>
                <a href="{{ route('attendance.reports', ['report_type' => 'employee_list']) }}" class="flex items-center px-3 py-2 text-sm hover:bg-white hover:text-blue-600 rounded-md group {{ $activeReportType === 'employee_list' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700' }}"><i class="fas fa-users mr-3 text-sm {{ $activeReportType === 'employee_list' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i><span>Employee Reports</span></a>
                <a href="{{ route('attendance.reports', ['report_type' => 'leave_balance']) }}" class="flex items-center px-3 py-2 text-sm hover:bg-white hover:text-blue-600 rounded-md group {{ $activeReportType === 'leave_balance' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700' }}"><i class="fas fa-calendar-check mr-3 text-sm {{ $activeReportType === 'leave_balance' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i><span>Balance of Leaves</span></a>
                <a href="{{ route('attendance.reports', ['report_type' => 'filings']) }}" class="flex items-center px-3 py-2 text-sm hover:bg-white hover:text-blue-600 rounded-md group {{ $activeReportType === 'filings' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700' }}"><i class="fas fa-file-contract mr-3 text-sm {{ $activeReportType === 'filings' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i><span>Employee Filings</span></a>
                @if($user->role === 'admin' || $user->role === 'hr' || $user->role === 'manager')
                    <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'reports.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-file-export mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Financial Reports</span></a>
                @endif
            </div>
        </div>

        {{-- ============ ADMINISTRATION ============ --}}
        @if($user->role === 'admin' || $user->role === 'hr')
        <div class="px-2 pt-4 pb-1">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-user-gear text-[10px]"></i> Administration
            </h3>
        </div>

        <div class="relative" x-data="{ open: {{ in_array($activeRoute, ['developer.accounts.index', 'developer.accounts.edit', 'developer.permissions.index', 'developer.accounts.link', 'developer.activity-logs.index']) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, ['developer.accounts.index', 'developer.accounts.edit', 'developer.permissions.index', 'developer.accounts.link', 'developer.activity-logs.index']) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-user-shield mr-3 text-lg {{ in_array($activeRoute, ['developer.accounts.index', 'developer.accounts.edit', 'developer.permissions.index', 'developer.accounts.link', 'developer.activity-logs.index']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Roles & Access</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                <a href="{{ route('developer.accounts.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ in_array($activeRoute, ['developer.accounts.index', 'developer.accounts.edit']) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-user-cog mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>User Accounts</span></a>
                <a href="{{ route('developer.accounts.link') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'developer.accounts.link' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-link mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Link Accounts</span></a>
                @if($user->role === 'admin')
                    <a href="{{ route('developer.permissions.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'developer.permissions.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-user-lock mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Roles & Permissions</span></a>
                @endif
                @if($user->role === 'admin')
                    <a href="{{ route('developer.activity-logs.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'developer.activity-logs.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-clipboard-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Activity Logs</span></a>
                @endif
            </div>
        </div>
        @endif

        @if($user->role === 'admin')
        <div class="relative" x-data="{ open: {{ in_array($activeRoute, ['developer.recycle-bin.index', 'developer.database-backup.index', 'attendance.settings']) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, ['developer.recycle-bin.index', 'developer.database-backup.index', 'attendance.settings']) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-gear mr-3 text-lg {{ in_array($activeRoute, ['developer.recycle-bin.index', 'developer.database-backup.index', 'attendance.settings']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>System & Integrations</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                <a href="{{ route('attendance.settings') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.settings' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-cog mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Attendance Settings</span></a>
                <a href="{{ route('developer.recycle-bin.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'developer.recycle-bin.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-trash-can mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Recycle Bin</span></a>
                <a href="{{ route('developer.database-backup.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'developer.database-backup.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-database mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Database Backup</span></a>
            </div>
        </div>
        @endif
    </div>
</nav>

<script>
// Function to handle payslip download
function downloadEmployeePayslip(payrollId) {
    // Check if the function exists in the main dashboard
    if (typeof window.downloadEmployeePayslip === 'function') {
        // Use the dashboard's function
        window.downloadEmployeePayslip(payrollId);
    } else {
        // Fallback: direct download
        window.open(`/employee/payslip/download/${payrollId}`, '_blank');
    }
}

// Get Philippine Standard Time (UTC+8)
function getPhilippineTime() {
    const now = new Date();
    return new Date(now.toLocaleString("en-US", {timeZone: "Asia/Manila"}));
}

// Format time in 12-hour format with AM/PM
function format12HourTime(date) {
    let hours = date.getHours();
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const seconds = date.getSeconds().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    const hoursStr = hours.toString().padStart(2, '0');
    return `${hoursStr}:${minutes}:${seconds} ${ampm}`;
}

// showConfirmationModal / hideConfirmationModal are provided globally by
// the shared dashboard layout (available on every dashboard - admin, hr,
// manager, employee) - no need to redefine them here.

// ============================================================
// SIDEBAR TIME IN/OUT CONFIRMATION FUNCTIONS
// ============================================================

// Confirm Time In from sidebar
function sidebarConfirmTimeIn() {
    // Get current time for the confirmation message
    const currentTime = window.getPhilippineTime ? window.getPhilippineTime() : getPhilippineTime();
    const formattedTime = window.format12HourTime ? window.format12HourTime(currentTime) : format12HourTime(currentTime);

    showConfirmationModal(
        'Confirm Time In',
        `Are you sure you want to clock in at ${formattedTime}?`,
        function() {
            sidebarTimeIn();
        },
        {
            color: 'green',
            icon: 'fa-sign-in-alt'
        }
    );
}

// Confirm Time Out from sidebar
function sidebarConfirmTimeOut() {
    // Get current time for the confirmation message
    const currentTime = window.getPhilippineTime ? window.getPhilippineTime() : getPhilippineTime();
    const formattedTime = window.format12HourTime ? window.format12HourTime(currentTime) : format12HourTime(currentTime);

    showConfirmationModal(
        'Confirm Time Out',
        `Are you sure you want to clock out at ${formattedTime}?`,
        function() {
            // Call the global timeOut function
            sidebarTimeOut();
        },
        {
            color: 'red',
            icon: 'fa-sign-out-alt'
        }
    );
}

// Show success message (uses global if available)
function showSuccess(message) {
    if (typeof window.showSuccess === 'function') {
        window.showSuccess(message);
        return;
    }
    alert('Success: ' + message);
}

// Show error message (uses global if available)
function showError(message) {
    if (typeof window.showError === 'function') {
        window.showError(message);
        return;
    }
    alert('Error: ' + message);
}

document.addEventListener('DOMContentLoaded', function() {
    // ----------------------------------------------------------------
    // Sidebar scroll-position persistence
    // When the user navigates to a sub-page the browser performs a full
    // page reload and the sidebar's scroll container resets to the top.
    // We save the scroll position to sessionStorage on every scroll event
    // and restore it as soon as the DOM is ready, so the sidebar appears
    // to stay exactly where it was left.
    // ----------------------------------------------------------------
    const sidebarScroll = document.getElementById('sidebar-scroll-container');

    if (sidebarScroll) {
        const STORAGE_KEY = 'sidebarScrollTop';

        // Restore scroll position immediately (before paint)
        const savedScroll = sessionStorage.getItem(STORAGE_KEY);
        if (savedScroll !== null) {
            sidebarScroll.scrollTop = parseInt(savedScroll, 10);
        }

        // Save scroll position on every scroll event (debounced)
        let saveScrollTimer = null;
        sidebarScroll.addEventListener('scroll', function() {
            clearTimeout(saveScrollTimer);
            saveScrollTimer = setTimeout(function() {
                sessionStorage.setItem(STORAGE_KEY, sidebarScroll.scrollTop);
            }, 100);
        }, { passive: true });

        // Also save immediately before the page unloads (navigation click)
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem(STORAGE_KEY, sidebarScroll.scrollTop);
        });
    }
});
</script>
