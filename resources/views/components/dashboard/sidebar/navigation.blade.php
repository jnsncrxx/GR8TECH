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
    $myPortalRoutes = [
        'attendance.time-in-out', 'attendance.my', 'employee.schedule',
        'attendance.overtime', 'attendance.leave-management', 'attendance.official-business',
        'employee.payroll.history', 'loans.index', 'loans.create', 'loans.show',
        'hr.my-information.info', 'hr.my-information.other-info', 'hr.my-information.education-training-rating',
        'hr.my-information.prev-emp-oth', 'hr.my-information.documents', 'hr.my-information.ytd-info', 'hr.my-information.bio-zk',
    ];
    $myPortalActive = request()->query('scope') === 'mine' || in_array($activeRoute, $myPortalRoutes, true);

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
        'attendance.overtime', 'attendance.leave-management', 'attendance.leave-management.create',
        'attendance.official-business', 'attendance.period-management.index',
        'attendance.period-management.create', 'attendance.period-management.show',
        'attendance.reports', 'settings',
    ];

    $payrollFinanceRoutes = [
        'payroll.index', 'payroll.team', 'payroll.runs',
        'payroll-templates.index', 'payroll-templates.create', 'payroll-templates.edit',
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
        <div class="relative" x-data="{ open: {{ $myPortalActive ? 'true' : 'false' }} }">
            <button @click="open = !open" class="group flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 {{ $myPortalActive ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                <span class="flex items-center">
                    <i class="fas fa-user-circle mr-3 text-lg {{ $myPortalActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>My Portal</span>
                </span>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 rounded-lg border border-gray-200 bg-gray-50 p-2">
                
                {{-- ===== TIME IN / OUT - SHOW FOR ALL USERS ===== --}}
                <a href="{{ route('attendance.time-in-out') }}" class="flex items-center rounded-md px-3 py-2 text-sm transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'attendance.time-in-out' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                    <i class="fas fa-sign-in-alt mr-3 text-sm {{ $activeRoute === 'attendance.time-in-out' ? 'text-blue-600' : 'text-gray-400' }}"></i>
                    <span>Time In / Out</span>
                    <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">Live</span>
                </a>

                {{-- ===== MY ATTENDANCE ===== --}}
                <a href="{{ route('attendance.my') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'attendance.my' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-calendar-check mr-3 text-sm {{ $activeRoute === 'attendance.my' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Attendance</span>
                </a>

                {{-- ===== MY SCHEDULE ===== --}}
                <a href="{{ route('employee.schedule') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'employee.schedule' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-calendar-alt mr-3 text-sm {{ $activeRoute === 'employee.schedule' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Schedule</span>
                </a>

                {{-- ===== MY OVERTIME ===== --}}
                <a href="{{ route('attendance.overtime', ['scope' => 'mine']) }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'attendance.overtime' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-clock mr-3 text-sm {{ $activeRoute === 'attendance.overtime' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Overtime</span>
                </a>

                {{-- ===== MY LEAVE ===== --}}
                <a href="{{ route('attendance.leave-management', ['scope' => 'mine']) }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'attendance.leave-management' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-calendar-times mr-3 text-sm {{ $activeRoute === 'attendance.leave-management' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Leave</span>
                </a>

                {{-- ===== MY OFFICIAL BUSINESS ===== --}}
                <a href="{{ route('attendance.official-business', ['scope' => 'mine']) }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'attendance.official-business' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-briefcase mr-3 text-sm {{ $activeRoute === 'attendance.official-business' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Official Business</span>
                </a>

                {{-- ===== MY PAYSLIPS ===== --}}
                <a href="{{ route('employee.payroll.history') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'employee.payroll.history' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-receipt mr-3 text-sm {{ $activeRoute === 'employee.payroll.history' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Payslips</span>
                </a>

                {{-- ===== MY LOANS ===== --}}
                <a href="{{ route('loans.index') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'loans.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-hand-holding-dollar mr-3 text-sm {{ $activeRoute === 'loans.index' ? 'text-blue-600' : 'text-gray-400' }}"></i><span>My Loans</span>
                </a>

                {{-- ===== MY INFORMATION (with submenu) ===== --}}
                @php
                    $myInfoRoutes = [
                        'hr.my-information.info', 'hr.my-information.other-info', 'hr.my-information.education-training-rating',
                        'hr.my-information.prev-emp-oth', 'hr.my-information.documents', 'hr.my-information.ytd-info', 'hr.my-information.bio-zk',
                    ];
                    $isMyInfoActive = in_array($activeRoute, $myInfoRoutes);
                @endphp
                <div class="relative" x-data="{ infoOpen: {{ $isMyInfoActive ? 'true' : 'false' }} }">
                    <button @click="infoOpen = !infoOpen" type="button" class="w-full flex items-center justify-between rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-blue-600">
                        <span class="flex items-center"><i class="fas fa-id-badge mr-3 text-sm text-gray-400"></i><span>My Information</span></span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': infoOpen }"></i>
                    </button>
                    <div x-show="infoOpen" x-transition class="ml-4 mt-1 space-y-1 rounded-md border border-gray-200 bg-white p-2">
                        <a href="{{ route('hr.my-information.info') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'hr.my-information.info' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-id-card mr-3 text-xs text-gray-400"></i><span>Personal Information</span></a>
                        <a href="{{ route('hr.my-information.other-info') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'hr.my-information.other-info' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-user-circle mr-3 text-xs text-gray-400"></i><span>Other Info</span></a>
                        <a href="{{ route('hr.my-information.education-training-rating') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'hr.my-information.education-training-rating' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-book mr-3 text-xs text-gray-400"></i><span>Educational/ Training/ Rating</span></a>
                        <a href="{{ route('hr.my-information.prev-emp-oth') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'hr.my-information.prev-emp-oth' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-briefcase mr-3 text-xs text-gray-400"></i><span>Previous Employer & Other</span></a>
                        <a href="{{ route('hr.my-information.documents') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'hr.my-information.documents' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-file-alt mr-3 text-xs text-gray-400"></i><span>Documents</span></a>
                        <a href="{{ route('hr.my-information.ytd-info') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'hr.my-information.ytd-info' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-passport mr-3 text-xs text-gray-400"></i><span>YTD - INFO</span></a>
                        <a href="{{ route('hr.my-information.bio-zk') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 {{ $activeRoute === 'hr.my-information.bio-zk' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-dna mr-3 text-xs text-gray-400"></i><span>Bio ZK</span></a>
                    </div>
                </div>
            </div>
        </div>
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
        <div class="relative" x-data="{ open: {{ in_array($activeRoute, $employeeRoutes) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $employeeRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-address-book mr-3 text-lg {{ in_array($activeRoute, $employeeRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Employee Directory</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                {{-- Employee List with count badge --}}
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

        @php $orgStructureRoutes = ['departments.index', 'positions.index', 'companies.index']; @endphp
        <div class="relative" x-data="{ open: {{ in_array($activeRoute, $orgStructureRoutes) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $orgStructureRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-sitemap mr-3 text-lg {{ in_array($activeRoute, $orgStructureRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Org Structure</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                <a href="{{ route('departments.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'departments.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-building mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Departments</span>
                    <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $currentCompany ? \App\Models\Department::forCompany($currentCompany->id)->count() : \App\Models\Department::count() }}</span>
                </a>
                <a href="{{ route('positions.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'positions.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-briefcase mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Positions</span>
                    <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $currentCompany ? \App\Models\Position::forCompany($currentCompany->id)->count() : \App\Models\Position::count() }}</span>
                </a>
                <a href="{{ route('companies.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'companies.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-industry mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Companies</span>
                    <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\Company::count() }}</span>
                </a>
            </div>
        </div>

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

        <div class="relative" x-data="{ open: {{ in_array($activeRoute, $timeRoutes) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $timeRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-clock mr-3 text-lg {{ in_array($activeRoute, $timeRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Attendance Logs</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                <a href="{{ route('attendance.daily') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.daily' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-calendar-day mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Attendance Record</span>
                </a>
                @if($user->role !== 'employee')
                <a href="{{ route('attendance.timekeeping') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.timekeeping' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-stopwatch mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Timekeeping</span>
                </a>
                <a href="{{ route('attendance.import-dtr') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.import-dtr' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-file-import mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Import DTR</span>
                    <span class="ml-auto bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-full">New</span>
                </a>
                @endif
                @if(in_array($user->role, ['admin', 'hr']))
                <a href="{{ route('attendance.period-management.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.period-management.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-calendar-week mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Period Management</span>
                    <span class="ml-auto bg-purple-100 text-purple-600 text-xs px-2 py-1 rounded-full">New</span>
                </a>
                @endif
            </div>
        </div>

        @if(in_array($user->role, ['admin', 'hr', 'manager']))
        @php $rosterRoutes = ['schedule-v2.index', 'schedule-v2.create', 'schedule-v2.show', 'schedule-v2.edit', 'schedule-templates.index', 'schedule-templates.create', 'schedule-templates.edit']; @endphp
        <div class="relative" x-data="{ open: {{ in_array($activeRoute, $rosterRoutes) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $rosterRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-calendar-plus mr-3 text-lg {{ in_array($activeRoute, $rosterRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Schedules & Roster</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                <a href="{{ route('schedule-v2.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'schedule-v2.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-calendar-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Schedule Calendar</span>
                </a>
                @if(in_array($user->role, ['admin', 'hr']))
                <a href="{{ route('schedule-templates.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ in_array($activeRoute, ['schedule-templates.index', 'schedule-templates.create', 'schedule-templates.edit']) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-list-check mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Schedule Templates</span>
                </a>
                @endif
            </div>
        </div>
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
        <div class="relative" x-data="{ open: {{ in_array($activeRoute, $overtimeAdjustRoutes) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $overtimeAdjustRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-business-time mr-3 text-lg {{ in_array($activeRoute, $overtimeAdjustRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Overtime & Adjustments</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($pendingOvertimeCount > 0)
                        <span class="bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingOvertimeCount }}</span>
                    @endif
                    <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                {{-- Overtime with pending count --}}
                <a href="{{ route('attendance.overtime') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.overtime' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-clock mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Overtime</span>
                    @if($pendingOvertimeCount > 0)
                        <span class="ml-auto bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingOvertimeCount }}</span>
                    @endif
                </a>
                {{-- Official Business with pending count --}}
                <a href="{{ route('attendance.official-business') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'attendance.official-business' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-briefcase mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Official Business</span>
                    @if($pendingOfficialBusinessCount > 0)
                        <span class="ml-auto bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingOfficialBusinessCount }}</span>
                    @endif
                </a>
            </div>
        </div>

        @if($user->role === 'admin' || $user->role === 'hr')
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-file-chart mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
                    <span>Timekeeping & HRIS Reports</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                <a href="{{ route('attendance.timekeeping') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-hourglass-half mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Time Summary</span></a>
                <a href="{{ route('attendance.timekeeping') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-id-card mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Time Card</span></a>
                <a href="{{ route('attendance.timekeeping') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Time Sheets</span></a>
                <a href="{{ route('attendance.timekeeping', ['exception' => 'missing_schedule']) }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-user-slash mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Absences</span></a>
                <a href="{{ route('attendance.timekeeping', ['exception' => 'possible_wrong_schedule']) }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-exclamation-triangle mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Undertime & Tardiness</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-chart-line mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Overtime Report</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-users mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Employee Reports</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-calendar-check mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Balance of Leaves</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-file-contract mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Employee Filings</span></a>
            </div>
        </div>

        <a href="{{ route('attendance.reports') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'attendance.reports' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-chart-line mr-3 text-lg {{ $activeRoute === 'attendance.reports' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Attendance Reports</span>
        </a>

        <a href="{{ route('settings') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'settings' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-cog mr-3 text-lg {{ $activeRoute === 'settings' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
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

        @php $payrollRunRoutes = ['payroll.index', 'payroll.team', 'payroll.runs', 'payroll-templates.index', 'payroll-templates.create', 'payroll-templates.edit', 'loans.index', 'loans.show', 'loan-types.index', 'loan-types.create', 'loan-types.edit']; @endphp
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
                <a href="{{ route('payroll-templates.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ in_array($activeRoute, ['payroll-templates.index', 'payroll-templates.create', 'payroll-templates.edit']) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-file-invoice mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Payroll Templates</span></a>
                <a href="{{ route('loans.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ in_array($activeRoute, ['loans.index', 'loans.show', 'loan-types.index', 'loan-types.create', 'loan-types.edit']) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-hand-holding-dollar mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Loan Management</span></a>
                @else
                <a href="{{ route('payroll.team') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'payroll.team' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-eye mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>My Team's Payroll</span></a>
                @endif
            </div>
        </div>

        @if($user->role === 'admin' || $user->role === 'hr')
        <a href="{{ route('tax-brackets.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'tax-brackets.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-percentage mr-3 text-lg {{ $activeRoute === 'tax-brackets.index' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Tax Brackets & Gov't Tables</span>
            <span class="ml-auto bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">New</span>
        </a>

        @php $financeReportRoutes = ['reports.index', 'payrolls.summary']; @endphp
        <div class="relative" x-data="{ open: {{ in_array($activeRoute, $financeReportRoutes) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $financeReportRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-chart-bar mr-3 text-lg {{ in_array($activeRoute, $financeReportRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Financial Reports</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200 max-h-96 overflow-y-auto">
                <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'reports.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-file-export mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Consolidated Reports</span></a>
                <a href="{{ route('payrolls.summary') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'payrolls.summary' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-calculator mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Payroll Reports</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-receipt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Payslips</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-university mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Bank Remittance</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-coins mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Denominations</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Received List</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-gift mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Allowances</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-star mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Allowance Special Report</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-piggy-bank mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Loan Balances</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-book mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Deduction Register</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-file-pdf mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>SSS Report</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-heartbeat mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Philhealth Report & RF-1</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-percent mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Tax Report</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-shield-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Pag Ibig Report</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-ledger mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Account Entries</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-file-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Text File Reports</span></a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-print mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Print Undeducted Items</span></a>
            </div>
        </div>
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
        <div class="relative" x-data="{ open: {{ in_array($activeRoute, $accessRoutes) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $accessRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-user-shield mr-3 text-lg {{ in_array($activeRoute, $accessRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>User Accounts</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                <a href="{{ route('developer.accounts.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ in_array($activeRoute, ['developer.accounts.index', 'developer.accounts.edit']) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-user-cog mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>User Account CRUD</span></a>
                <a href="{{ route('developer.accounts.link') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'developer.accounts.link' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-link mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Link Account to Employee</span></a>
                @if($user->role === 'admin')
                <a href="{{ route('developer.activity-logs.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'developer.activity-logs.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-clipboard-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Activity Logs</span></a>
                @endif
            </div>
        </div>

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

        <a href="{{ route('hr.settings') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'hr.settings' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-building-user mr-3 text-lg {{ $activeRoute === 'hr.settings' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Company Profile</span>
        </a>

        @php $devIntegrationRoutes = ['developer.recycle-bin.index', 'developer.database-backup.index']; @endphp
        <div class="relative" x-data="{ open: {{ in_array($activeRoute, $devIntegrationRoutes) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $devIntegrationRoutes) ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-code mr-3 text-lg {{ in_array($activeRoute, $devIntegrationRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Developer & Integrations</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                <a href="{{ route('developer.accounts.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-key mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Password / Reset Handling</span></a>
                <a href="{{ route('developer.accounts.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group"><i class="fas fa-ban mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Restrict Admin Access by Role</span></a>
                <a href="{{ route('developer.recycle-bin.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'developer.recycle-bin.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-trash-can mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Recycle Bin</span></a>
                <a href="{{ route('developer.database-backup.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md group {{ $activeRoute === 'developer.database-backup.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : '' }}"><i class="fas fa-database mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i><span>Database Backup</span></a>
            </div>
        </div>
        @endif

        {{-- ============ QUICK ACTIONS ============ --}}
        <div class="my-6 border-t border-gray-200"></div>
        <div class="px-4 mb-2">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Quick Actions</h3>
        </div>

        @if($user->role === 'admin' || $user->role === 'hr')
        <a href="{{ route('employees.create') }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
            <i class="fas fa-user-plus mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
            <span>Add Employee</span>
        </a>
        <a href="{{ route('documents.export') }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
            <i class="fas fa-file-export mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
            <span>Export Data</span>
        </a>
        <a href="{{ route('notifications.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'notifications.index' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-bell mr-3 text-lg {{ $activeRoute === 'notifications.index' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Notifications</span>
        </a>
        <a href="{{ route('hr.contacts.admin') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'hr.contacts.admin' ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-inbox mr-3 text-lg {{ $activeRoute === 'hr.contacts.admin' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Inbox</span>
        </a>
        @endif

        {{-- Clock In/Out - Show for ALL users with employee record --}}
        @if($user->employee)
            @if($todayAttendance && $todayAttendance->time_in && !$todayAttendance->time_out)
                <div class="flex items-center px-4 py-3 text-sm font-medium text-green-700 bg-green-50 rounded-lg">
                    <span class="w-2 h-2 rounded-full bg-green-500 mr-3 animate-pulse"></span>
                    <span>You're Clocked In</span>
                </div>
                
                <button onclick="sidebarConfirmTimeOut()" class="w-full flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-red-600 rounded-lg transition-all duration-200 group">
                    <i class="fas fa-sign-out-alt mr-3 text-lg text-gray-400 group-hover:text-red-600"></i>
                    <span>Time Out</span>
                </button>
            @elseif($todayAttendance && $todayAttendance->time_out)
                <div class="flex items-center px-4 py-3 text-sm font-medium text-gray-400 rounded-lg cursor-not-allowed">
                    <i class="fas fa-check mr-3 text-lg text-gray-400"></i>
                    <span>Already Clocked Out</span>
                </div>
            @else
                <button onclick="sidebarConfirmTimeIn()" class="w-full flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-green-600 rounded-lg transition-all duration-200 group">
                    <i class="fas fa-sign-in-alt mr-3 text-lg text-gray-400 group-hover:text-green-600"></i>
                    <span>Time In</span>
                </button>
                
                <div class="flex items-center px-4 py-3 text-sm font-medium text-gray-400 rounded-lg cursor-not-allowed">
                    <i class="fas fa-sign-out-alt mr-3 text-lg text-gray-400"></i>
                    <span>Time Out (Clock In First)</span>
                </div>
            @endif

            @php
                $latestPayroll = null;
                try {
                    if (isset($user->employee) && $user->employee) {
                        $latestPayroll = \App\Models\Payroll::where('employee_id', $user->employee->id)
                            ->whereIn('status', ['approved', 'paid'])
                            ->latest()
                            ->first();
                    }
                } catch (\Exception $e) {
                    $latestPayroll = null;
                }
            @endphp
            @if($latestPayroll)
                <button onclick="downloadEmployeePayslip('{{ $latestPayroll->id }}')" class="w-full flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
                    <i class="fas fa-download mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
                    <span>Download Payslip</span>
                </button>
            @else
                <div class="flex items-center px-4 py-3 text-sm font-medium text-gray-400 rounded-lg cursor-not-allowed">
                    <i class="fas fa-download mr-3 text-lg text-gray-400"></i>
                    <span>No Payslip Available</span>
                </div>
            @endif

            <a href="{{ route('attendance.leave-management', ['scope' => 'mine']) }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
                <i class="fas fa-calendar-times mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
                <span>Apply Leave</span>
            </a>
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

// Show confirmation modal (uses global functions if available)
function showConfirmationModal(title, message, confirmAction, options = {}) {
    // Check if the global modal function exists
    if (typeof window.showConfirmationModal === 'function') {
        // Use the dashboard's modal function
        window.showConfirmationModal(title, message, confirmAction, options);
        return;
    }

    // Fallback: Use browser's native confirm dialog
    if (confirm(`${title}\n\n${message}`)) {
        confirmAction();
    }
}

// Hide confirmation modal
function hideConfirmationModal() {
    if (typeof window.hideConfirmationModal === 'function') {
        window.hideConfirmationModal();
    }
}

// ============================================================
// SIDEBAR TIME IN/OUT CONFIRMATION FUNCTIONS
// ============================================================

// Confirm Time In from sidebar
function sidebarConfirmTimeIn() {
    // Check if timeIn function exists globally
    if (typeof window.timeIn !== 'function') {
        alert('Please go to the Time In/Out page first to initialize the time tracking system.');
        return;
    }
    
    // Get current time for the confirmation message
    const currentTime = window.getPhilippineTime ? window.getPhilippineTime() : getPhilippineTime();
    const formattedTime = window.format12HourTime ? window.format12HourTime(currentTime) : format12HourTime(currentTime);

    showConfirmationModal(
        'Confirm Time In',
        `Are you sure you want to clock in at ${formattedTime}?`,
        function() {
            // Call the global timeIn function
            window.timeIn();
        },
        {
            color: 'green',
            icon: 'fa-sign-in-alt'
        }
    );
}

// Confirm Time Out from sidebar
function sidebarConfirmTimeOut() {
    // Check if timeOut function exists globally
    if (typeof window.timeOut !== 'function') {
        alert('Please go to the Time In/Out page first to initialize the time tracking system.');
        return;
    }
    
    // Get current time for the confirmation message
    const currentTime = window.getPhilippineTime ? window.getPhilippineTime() : getPhilippineTime();
    const formattedTime = window.format12HourTime ? window.format12HourTime(currentTime) : format12HourTime(currentTime);

    showConfirmationModal(
        'Confirm Time Out',
        `Are you sure you want to clock out at ${formattedTime}?`,
        function() {
            // Call the global timeOut function
            window.timeOut();
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
});
</script>