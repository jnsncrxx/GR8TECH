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
    $sharedScopedRoutes = ['attendance.overtime', 'attendance.leave-management', 'attendance.official-business'];

    $myPortalRoutes = [
        'attendance.time-in-out', 'attendance.my', 'employee.schedule',
        'employee.payroll.history', 'loans.index', 'loans.create', 'loans.show',
        'hr.my-information.info', 'hr.my-information.other-info', 'hr.my-information.education-training-rating',
        'hr.my-information.prev-emp-oth', 'hr.my-information.documents', 'hr.my-information.ytd-info', 'hr.my-information.bio-zk',
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
        'attendance.leave-management.create', 'attendance.period-management.index',
        'attendance.period-management.create', 'attendance.period-management.show',
    ];
    $timeRoutesActive = in_array($activeRoute, $timeRoutes, true)
        || (!$isMineScope && in_array($activeRoute, $sharedScopedRoutes, true));

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

    $systemSettingsRoutes = ['hr.settings', 'developer.recycle-bin.index', 'developer.database-backup.index'];

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

        {{-- ============ OVERVIEW ============ --}}
        <div class="px-2 pt-2 pb-1">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-thumbtack text-[10px]"></i> Overview
            </h3>
        </div>

        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'dashboard' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-tachometer-alt mr-3 text-lg {{ $activeRoute === 'dashboard' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Dashboard</span>
        </a>

        {{-- ============ MY PORTAL ============ --}}
        @if($user->employee)
        @php
            $myInfoRoutes = [
                'hr.my-information.info', 'hr.my-information.other-info', 'hr.my-information.education-training-rating',
                'hr.my-information.prev-emp-oth', 'hr.my-information.documents', 'hr.my-information.ytd-info', 'hr.my-information.bio-zk',
            ];
            $isMyInfoActive = in_array($activeRoute, $myInfoRoutes);
        @endphp
        <x-dashboard.sidebar.flyout-nav
            label="My Portal"
            class="{{ $myPortalActive ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}"
        >
            <x-slot:trigger>
                <span class="flex items-center">
                    <i class="fas fa-user-circle mr-3 text-lg {{ $myPortalActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>My Portal</span>
                </span>
                <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
            </x-slot:trigger>

            <a href="{{ route('attendance.time-in-out') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'attendance.time-in-out' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-sign-in-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Time In / Out</span>
                <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">Live</span>
            </a>
            <a href="{{ route('attendance.my') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'attendance.my' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-calendar-check mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>My Attendance</span>
            </a>
            <a href="{{ route('employee.schedule') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'employee.schedule' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-calendar-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>My Schedule</span>
            </a>
            <a href="{{ route('attendance.overtime', ['scope' => 'mine']) }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'attendance.overtime' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-clock mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>My Overtime</span>
            </a>
            <a href="{{ route('attendance.leave-management', ['scope' => 'mine']) }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'attendance.leave-management' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-calendar-times mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>My Leave</span>
            </a>
            <a href="{{ route('attendance.official-business', ['scope' => 'mine']) }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'attendance.official-business' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-briefcase mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>My Official Business</span>
            </a>
            <a href="{{ route('employee.payroll.history') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'employee.payroll.history' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-receipt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>My Payslips</span>
            </a>
            <a href="{{ route('loans.index', ['scope' => 'mine']) }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'loans.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-hand-holding-dollar mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>My Loans</span>
            </a>

            <x-dashboard.sidebar.flyout-nav
                nested
                label="My Information"
                panel-z-index="z-[101]"
                class="mx-2 text-gray-700 hover:bg-gray-50 hover:text-blue-600 {{ $isMyInfoActive ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"
            >
                <x-slot:trigger>
                    <span class="flex items-center"><i class="fas fa-id-badge mr-3 text-sm text-gray-400"></i><span>My Information</span></span>
                    <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
                </x-slot:trigger>
                <a href="{{ route('hr.my-information.info') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'hr.my-information.info' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-id-card mr-3 text-xs text-gray-400 group-hover:text-blue-600"></i><span>Personal Information</span></a>
                <a href="{{ route('hr.my-information.other-info') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'hr.my-information.other-info' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-user-circle mr-3 text-xs text-gray-400 group-hover:text-blue-600"></i><span>Other Info</span></a>
                <a href="{{ route('hr.my-information.education-training-rating') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'hr.my-information.education-training-rating' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-book mr-3 text-xs text-gray-400 group-hover:text-blue-600"></i><span>Educational/ Training/ Rating</span></a>
                <a href="{{ route('hr.my-information.prev-emp-oth') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'hr.my-information.prev-emp-oth' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-briefcase mr-3 text-xs text-gray-400 group-hover:text-blue-600"></i><span>Previous Employer & Other</span></a>
                <a href="{{ route('hr.my-information.documents') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'hr.my-information.documents' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-file-alt mr-3 text-xs text-gray-400 group-hover:text-blue-600"></i><span>Documents</span></a>
                <a href="{{ route('hr.my-information.ytd-info') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'hr.my-information.ytd-info' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-passport mr-3 text-xs text-gray-400 group-hover:text-blue-600"></i><span>YTD - INFO</span></a>
                <a href="{{ route('hr.my-information.bio-zk') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'hr.my-information.bio-zk' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-dna mr-3 text-xs text-gray-400 group-hover:text-blue-600"></i><span>Bio ZK</span></a>
            </x-dashboard.sidebar.flyout-nav>
        </x-dashboard.sidebar.flyout-nav>
        @endif

        {{-- ============ WORKFORCE (HR) ============ --}}
        @if($user->role === 'admin' || $user->role === 'hr')
        <div class="px-2 pt-4 pb-1">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-users text-[10px]"></i> Workforce
            </h3>
        </div>

        @php
            $employeeCount = $currentCompany ? \App\Models\Employee::forCompany($currentCompany->id)->count() : \App\Models\Employee::count();
            $employeeRoutes = ['employees.index', 'employees.info', 'employees.other-employee-info', 'employees.education-training-rating', 'employees.prev-emp-oth', 'employees.documents', 'employees.ytd-info', 'employees.bio-zk'];
        @endphp
        <x-dashboard.sidebar.flyout-nav
            label="Employee Directory"
            class="{{ in_array($activeRoute, $employeeRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}"
        >
            <x-slot:trigger>
                <div class="flex items-center">
                    <i class="fas fa-address-book mr-3 text-lg {{ in_array($activeRoute, $employeeRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Employee Directory</span>
                </div>
                <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
            </x-slot:trigger>
            <a href="{{ route('employees.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'employees.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Employee List</span>
                <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $employeeCount }}</span>
            </a>
            <a href="{{ route('employees.info') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'employees.info' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-id-card mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Employee Info</span>
            </a>
            <a href="{{ route('employees.other-employee-info') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'employees.other-employee-info' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-user-circle mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Other Employee Info</span>
            </a>
            <a href="{{ route('employees.education-training-rating') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'employees.education-training-rating' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-book mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Education/ Training/ Rating</span>
            </a>
            <a href="{{ route('employees.prev-emp-oth') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'employees.prev-emp-oth' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-briefcase mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Previous Employer & Other</span>
            </a>
            <a href="{{ route('employees.documents') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'employees.documents' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-file-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Documents</span>
            </a>
            <a href="{{ route('employees.ytd-info') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'employees.ytd-info' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-passport mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>YTD - INFO</span>
            </a>
            <a href="{{ route('employees.bio-zk') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'employees.bio-zk' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-dna mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Bio ZK</span>
            </a>
        </x-dashboard.sidebar.flyout-nav>

        @php $orgStructureRoutes = ['departments.index', 'positions.index', 'companies.index']; @endphp
        <x-dashboard.sidebar.flyout-nav
            label="Org Structure"
            class="{{ in_array($activeRoute, $orgStructureRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}"
        >
            <x-slot:trigger>
                <div class="flex items-center">
                    <i class="fas fa-sitemap mr-3 text-lg {{ in_array($activeRoute, $orgStructureRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Org Structure</span>
                </div>
                <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
            </x-slot:trigger>
            <a href="{{ route('departments.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'departments.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-building mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Departments</span>
                <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $currentCompany ? \App\Models\Department::forCompany($currentCompany->id)->count() : \App\Models\Department::count() }}</span>
            </a>
            <a href="{{ route('positions.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'positions.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-briefcase mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Positions</span>
                <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $currentCompany ? \App\Models\Position::forCompany($currentCompany->id)->count() : \App\Models\Position::count() }}</span>
            </a>
            <a href="{{ route('companies.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'companies.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-industry mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Companies</span>
                <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\Company::count() }}</span>
            </a>
        </x-dashboard.sidebar.flyout-nav>

        <a href="{{ route('documents.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('documents.*') ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-folder mr-3 text-lg {{ request()->routeIs('documents.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Documents & Files</span>
        </a>
        @endif

        {{-- ============ TIME & WORKFORCE MANAGEMENT ============ --}}
        <div class="px-2 pt-4 pb-1">
            <h3 class="flex items-center gap-1.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                <i class="fas fa-user-clock text-[10px] flex-shrink-0"></i>
                <span class="truncate">Time & Workforce Management</span>
                @if($pendingApprovalsCount > 0)
                    <span class="ml-auto flex-shrink-0 bg-amber-100 text-amber-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full normal-case tracking-normal">{{ $pendingApprovalsCount }} Pending</span>
                @endif
            </h3>
        </div>

        <x-dashboard.sidebar.flyout-nav
            label="Attendance Logs"
            class="{{ in_array($activeRoute, $timeRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}"
        >
            <x-slot:trigger>
                <div class="flex items-center">
                    <i class="fas fa-clock mr-3 text-lg {{ in_array($activeRoute, $timeRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Attendance Logs</span>
                </div>
                <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
            </x-slot:trigger>
            <a href="{{ route('attendance.daily') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'attendance.daily' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-calendar-day mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Attendance Record</span>
            </a>
            @if($user->role !== 'employee')
            <a href="{{ route('attendance.timekeeping') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'attendance.timekeeping' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-stopwatch mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Timekeeping</span>
            </a>
            <a href="{{ route('attendance.import-dtr') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'attendance.import-dtr' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-file-import mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Import DTR</span>
                <span class="ml-auto bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-full">New</span>
            </a>
            @endif
            @if(in_array($user->role, ['admin', 'hr']))
            <a href="{{ route('attendance.period-management.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'attendance.period-management.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-calendar-week mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Period Management</span>
                <span class="ml-auto bg-purple-100 text-purple-600 text-xs px-2 py-1 rounded-full">New</span>
            </a>
            @endif
        </x-dashboard.sidebar.flyout-nav>

        @if(in_array($user->role, ['admin', 'hr', 'manager']))
        @php $rosterRoutes = ['schedule-v2.index', 'schedule-v2.create', 'schedule-v2.show', 'schedule-v2.edit', 'schedule-templates.index', 'schedule-templates.create', 'schedule-templates.edit']; @endphp
        <x-dashboard.sidebar.flyout-nav
            label="Schedules & Roster"
            class="{{ in_array($activeRoute, $rosterRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}"
        >
            <x-slot:trigger>
                <div class="flex items-center">
                    <i class="fas fa-calendar-plus mr-3 text-lg {{ in_array($activeRoute, $rosterRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Schedules & Roster</span>
                </div>
                <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
            </x-slot:trigger>
            <a href="{{ route('schedule-v2.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'schedule-v2.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-calendar-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Schedule Calendar</span>
            </a>
            @if(in_array($user->role, ['admin', 'hr']))
            <a href="{{ route('schedule-templates.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ in_array($activeRoute, ['schedule-templates.index', 'schedule-templates.create', 'schedule-templates.edit']) ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-list-check mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Schedule Templates</span>
            </a>
            @endif
        </x-dashboard.sidebar.flyout-nav>
        @endif

        {{-- Leave Requests with pending count --}}
        <a href="{{ route('attendance.leave-management') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'attendance.leave-management' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-calendar-times mr-3 text-lg {{ $activeRoute === 'attendance.leave-management' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Leave Requests</span>
            @if($pendingLeaveCount > 0)
                <span class="ml-auto bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingLeaveCount }}</span>
            @endif
        </a>

        {{-- Overtime & Adjustments with pending count --}}
        @php $overtimeAdjustRoutes = ['attendance.overtime', 'attendance.official-business']; @endphp
        <x-dashboard.sidebar.flyout-nav
            label="Overtime & Adjustments"
            class="{{ in_array($activeRoute, $overtimeAdjustRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}"
        >
            <x-slot:trigger>
                <div class="flex items-center">
                    <i class="fas fa-business-time mr-3 text-lg {{ in_array($activeRoute, $overtimeAdjustRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Overtime & Adjustments</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($pendingOvertimeCount > 0)
                        <span class="bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingOvertimeCount }}</span>
                    @endif
                    <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
                </div>
            </x-slot:trigger>
            <a href="{{ route('attendance.overtime') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'attendance.overtime' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-clock mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Overtime</span>
                @if($pendingOvertimeCount > 0)
                    <span class="ml-auto bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingOvertimeCount }}</span>
                @endif
            </a>
            <a href="{{ route('attendance.official-business') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'attendance.official-business' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                <i class="fas fa-briefcase mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                <span>Official Business</span>
                @if($pendingOfficialBusinessCount > 0)
                    <span class="ml-auto bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingOfficialBusinessCount }}</span>
                @endif
            </a>
        </x-dashboard.sidebar.flyout-nav>

        @if($user->role === 'admin' || $user->role === 'hr')
        <x-dashboard.sidebar.flyout-nav
            label="Timekeeping & HRIS Reports"
            class="text-gray-700 hover:bg-gray-50 hover:text-blue-600"
        >
            <x-slot:trigger>
                <div class="flex items-center">
                    <i class="fas fa-chart-bar mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
                    <span>Timekeeping & HRIS Reports</span>
                </div>
                <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
            </x-slot:trigger>
            <a href="{{ route('attendance.reports', ['report_type' => 'monthly']) }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-chart-pie mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Time Summary</span></a>
            <a href="{{ route('attendance.timekeeping') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-id-card mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Time Card</span></a>
            <a href="{{ route('attendance.timekeeping') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Time Sheets</span></a>
            <a href="{{ route('attendance.reports', ['report_type' => 'absences']) }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-user-slash mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Absences</span></a>
            <a href="{{ route('attendance.timekeeping', ['exception' => 'incomplete']) }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-exclamation-triangle mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Undertime & Tardiness</span></a>
            <a href="{{ route('attendance.reports', ['report_type' => 'overtime']) }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-chart-line mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Overtime Report</span></a>
            <a href="{{ route('attendance.reports', ['report_type' => 'employee_list']) }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-users mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Employee Reports</span></a>
            <a href="{{ route('attendance.reports', ['report_type' => 'leave_balance']) }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-calendar-check mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Balance of Leaves</span></a>
            <a href="{{ route('attendance.reports', ['report_type' => 'filings']) }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-file-contract mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Employee Filings</span></a>
        </x-dashboard.sidebar.flyout-nav>

        <a href="{{ route('attendance.reports') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'attendance.reports' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-chart-line mr-3 text-lg {{ $activeRoute === 'attendance.reports' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Attendance Reports</span>
        </a>

        <a href="{{ route('attendance.settings') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'attendance.settings' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-cog mr-3 text-lg {{ $activeRoute === 'attendance.settings' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Attendance Settings</span>
        </a>
        @endif

        {{-- ============ PAYROLL & FINANCE ============ --}}
        @if($user->role === 'admin' || $user->role === 'hr' || $user->role === 'manager')
        <div class="px-2 pt-4 pb-1">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-sack-dollar text-[10px]"></i> Payroll & Finance
            </h3>
        </div>

        @php $payrollRunRoutes = ['payroll.index', 'payroll.team', 'payroll.runs', 'payroll-templates.index', 'payroll-templates.create', 'payroll-templates.edit', 'loans.index', 'loans.show', 'loan-types.index', 'loan-types.create', 'loan-types.edit', 'payroll-adjustments.index', 'payroll-adjustments.create', 'payroll-adjustments.edit']; @endphp
        <x-dashboard.sidebar.flyout-nav
            label="Run Payroll"
            class="{{ in_array($activeRoute, $payrollRunRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}"
        >
            <x-slot:trigger>
                <div class="flex items-center">
                    <i class="fas fa-money-bill-wave mr-3 text-lg {{ in_array($activeRoute, $payrollRunRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Run Payroll</span>
                </div>
                <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
            </x-slot:trigger>
            @if($user->role === 'admin' || $user->role === 'hr')
                <a href="{{ route('payroll.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'payroll.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Payroll Payments</span>
                </a>
                <a href="{{ route('payroll.runs') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'payroll.runs' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-layer-group mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Payroll Runs</span>
                </a>
                <a href="{{ route('payroll-templates.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ in_array($activeRoute, ['payroll-templates.index', 'payroll-templates.create', 'payroll-templates.edit'], true) ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-file-invoice mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Payroll Templates</span>
                </a>
                <a href="{{ route('payroll-adjustments.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ in_array($activeRoute, ['payroll-adjustments.index', 'payroll-adjustments.create', 'payroll-adjustments.edit'], true) ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-sliders-h mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Payroll Adjustments</span>
                </a>
                <a href="{{ route('loans.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ in_array($activeRoute, ['loans.index', 'loans.create', 'loans.show', 'loan-types.index', 'loan-types.create', 'loan-types.edit'], true) ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-hand-holding-dollar mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Loan Management</span>
                </a>
            @else
                <a href="{{ route('loans.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ in_array($activeRoute, ['loans.index', 'loans.show'], true) ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-hand-holding-dollar mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Loan Approvals</span>
                </a>
                <a href="{{ route('payroll.team') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'payroll.team' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-eye mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>My Team's Payroll</span>
                </a>
            @endif
        </x-dashboard.sidebar.flyout-nav>

        @if($user->role === 'admin' || $user->role === 'hr')
        <a href="{{ route('tax-brackets.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'tax-brackets.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-percentage mr-3 text-lg {{ $activeRoute === 'tax-brackets.index' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Tax Brackets & Gov't Tables</span>
            <span class="ml-auto bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">New</span>
        </a>

        @php $financeReportRoutes = ['reports.index', 'payrolls.summary', 'allowances.index']; @endphp
        <x-dashboard.sidebar.flyout-nav
            label="Financial Reports"
            panel-class="w-72 max-h-96"
            class="{{ in_array($activeRoute, $financeReportRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}"
        >
            <x-slot:trigger>
                <div class="flex items-center">
                    <i class="fas fa-chart-bar mr-3 text-lg {{ in_array($activeRoute, $financeReportRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Financial Reports</span>
                </div>
                <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
            </x-slot:trigger>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-book-open mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Account Entries</span></a>
            <a href="{{ route('allowances.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'allowances.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-gift mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Allowances</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-star mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Allowance Special Report</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-university mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Bank Remittance</span></a>
            <a href="{{ route('reports.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'reports.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-file-export mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Consolidated Reports</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-book mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Deduction Register</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-coins mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Denominations</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-piggy-bank mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Loan Balances</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-shield-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Pag Ibig Report</span></a>
            <a href="{{ route('payrolls.summary') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'payrolls.summary' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-calculator mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Payroll Reports</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-receipt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Payslips</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-heartbeat mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Philhealth Report & RF-1</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-print mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Print Undeducted Items</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Received List</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-file-pdf mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>SSS Report</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-percent mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Tax Report</span></a>
            <a href="#" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-file-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Text File Reports</span></a>
        </x-dashboard.sidebar.flyout-nav>
        @endif
        @endif

        {{-- ============ ACCESS & SECURITY ============ --}}
        @if($user->role === 'admin' || $user->role === 'hr')
        <div class="px-2 pt-4 pb-1">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-shield-halved text-[10px]"></i> Access & Security
                <span class="ml-auto bg-blue-100 text-blue-600 text-[10px] font-bold px-1.5 py-0.5 rounded-full normal-case tracking-normal">New</span>
            </h3>
        </div>

        @php $accessRoutes = ['developer.accounts.index', 'developer.accounts.edit', 'developer.permissions.index', 'developer.accounts.link', 'developer.activity-logs.index']; @endphp
        <x-dashboard.sidebar.flyout-nav
            label="User Accounts"
            class="{{ in_array($activeRoute, $accessRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}"
        >
            <x-slot:trigger>
                <div class="flex items-center">
                    <i class="fas fa-user-shield mr-3 text-lg {{ in_array($activeRoute, $accessRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>User Accounts</span>
                </div>
                <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
            </x-slot:trigger>
            <a href="{{ route('developer.accounts.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ in_array($activeRoute, ['developer.accounts.index', 'developer.accounts.edit']) ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-user-cog mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>User Account CRUD</span></a>
            <a href="{{ route('developer.accounts.link') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'developer.accounts.link' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-link mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Link Account to Employee</span></a>
            @if($user->role === 'admin')
            <a href="{{ route('developer.activity-logs.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'developer.activity-logs.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-clipboard-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Activity Logs</span></a>
            @endif
        </x-dashboard.sidebar.flyout-nav>

        @if($user->role === 'admin')
        <a href="{{ route('developer.permissions.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'developer.permissions.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-user-shield mr-3 text-lg {{ $activeRoute === 'developer.permissions.index' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Roles & Permissions</span>
        </a>
        @endif
        @endif

        {{-- ============ SYSTEM SETTINGS ============ --}}
        @if($user->role === 'admin')
        <div class="px-2 pt-4 pb-1">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-gear text-[10px]"></i> System Settings
            </h3>
        </div>

        @php $companyProfileRoutes = ['companies.index', 'companies.create', 'companies.edit', 'companies.show']; @endphp
        <a href="{{ route('companies.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $companyProfileRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-building-user mr-3 text-lg {{ in_array($activeRoute, $companyProfileRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Company Profile</span>
        </a>

        @php $devIntegrationRoutes = ['developer.recycle-bin.index', 'developer.database-backup.index']; @endphp
        <x-dashboard.sidebar.flyout-nav
            label="Developer & Integrations"
            class="{{ in_array($activeRoute, $devIntegrationRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}"
        >
            <x-slot:trigger>
                <div class="flex items-center">
                    <i class="fas fa-code mr-3 text-lg {{ in_array($activeRoute, $devIntegrationRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Developer & Integrations</span>
                </div>
                <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'translate-x-0.5 text-blue-600': open }"></i>
            </x-slot:trigger>
            <a href="{{ route('developer.accounts.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-key mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Password / Reset Handling</span></a>
            <a href="{{ route('developer.accounts.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group"><i class="fas fa-ban mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Restrict Admin Access by Role</span></a>
            <a href="{{ route('developer.sandbox.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'admin.sandbox.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-flask mr-3 text-sm text-yellow-500 group-hover:text-blue-600"></i><span>Payroll Simulation Demo</span></a>
            <a href="{{ route('developer.recycle-bin.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'developer.recycle-bin.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-trash-can mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Recycle Bin</span></a>
            <a href="{{ route('developer.database-backup.index') }}" class="mx-2 flex items-center rounded-md px-3 py-2 text-sm group {{ $activeRoute === 'developer.database-backup.index' ? 'border-l-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-database mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Database Backup</span></a>
        </x-dashboard.sidebar.flyout-nav>
        @endif

        {{-- ============ SUPPORT & HELP ============ --}}
        <div class="my-6 border-t border-gray-200"></div>
        <div class="px-4 mb-2">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Support & Help</h3>
        </div>

        @if($user->employee)
            <a href="{{ route('hr.contact.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
                <i class="fas fa-question-circle mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
                <span>Contact HR</span>
            </a>
        @endif

        <a href="{{ route('hr.help-support') }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
            <i class="fas fa-life-ring mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
            <span>Help & Support</span>
        </a>
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
    // Timekeeping and HRIS Reports Submenu Handler
    const timekeepingBtns = document.querySelectorAll('.timekeepingReportBtn');
    timekeepingBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const submenu = this.closest('.timekeeping-report-group')?.querySelector('.timekeepingReportSubMenu');
            if (submenu) {
                submenu.classList.toggle('hidden');
            }
        });
    });

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
