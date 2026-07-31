@props(['user', 'activeRoute' => 'dashboard'])

@php
    $currentCompany = \App\Helpers\CompanyHelper::getCurrentCompany();

    // Check if employee is currently timed in (for employee role users)
    $isCurrentlyTimedIn = false;
    $todayAttendance = null;
    if ($user->role === 'employee' && $user->employee) {
        $todayAttendance = $user->employee->getTodayAttendance();
        $isCurrentlyTimedIn = $todayAttendance && $todayAttendance->hasActiveTimeEntry();
    }

    $myWorkspaceActive = request()->query('scope') === 'mine'
        || in_array($activeRoute, [
            'attendance.time-in-out',
            'attendance.my',
            'employee.schedule',
            'employee.payroll.history',
        ], true);
@endphp

<nav class="brand-sidebar-nav mt-8 px-4 pb-4">
    <div class="space-y-2">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'dashboard' ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-tachometer-alt mr-3 text-lg {{ $activeRoute === 'dashboard' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Dashboard</span>
        </a>

        @if($user->employee && $user->role !== 'employee')
        <div class="my-4 border-t border-gray-200"></div>
        <div class="relative" x-data="{ open: {{ $myWorkspaceActive ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="group flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 {{ $myWorkspaceActive ? 'border-r-4 border-blue-600 bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                <span class="flex items-center">
                    <i class="fas fa-user-circle mr-3 text-lg {{ $myWorkspaceActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>My Workspace</span>
                </span>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                   :class="{ 'rotate-180': open }"></i>
            </button>
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="ml-8 mt-2 space-y-1 rounded-lg border border-gray-200 bg-gray-50 p-2">
            <a href="{{ route('attendance.time-in-out') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-white hover:text-blue-600">
                <i class="fas fa-sign-in-alt mr-3 text-sm text-gray-400"></i><span>Clock In / Out</span>
            </a>
            <a href="{{ route('attendance.my') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-white hover:text-blue-600">
                <i class="fas fa-calendar-check mr-3 text-sm text-gray-400"></i><span>My Attendance</span>
            </a>
            <a href="{{ route('employee.schedule') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-white hover:text-blue-600">
                <i class="fas fa-calendar-alt mr-3 text-sm text-gray-400"></i><span>My Schedule</span>
            </a>
            <a href="{{ route('attendance.overtime', ['scope' => 'mine']) }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-white hover:text-blue-600">
                <i class="fas fa-clock mr-3 text-sm text-gray-400"></i><span>My Overtime</span>
            </a>
            <a href="{{ route('attendance.leave-management', ['scope' => 'mine']) }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-white hover:text-blue-600">
                <i class="fas fa-calendar-times mr-3 text-sm text-gray-400"></i><span>My Leave</span>
            </a>
            <a href="{{ route('attendance.official-business', ['scope' => 'mine']) }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-white hover:text-blue-600">
                <i class="fas fa-briefcase mr-3 text-sm text-gray-400"></i><span>My Official Business</span>
            </a>
            <a href="{{ route('employee.payroll.history') }}" class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-white hover:text-blue-600">
                <i class="fas fa-receipt mr-3 text-sm text-gray-400"></i><span>My Payslips</span>
            </a>
        </div>
        </div>
        <div class="my-4 border-t border-gray-200"></div>
        @endif
        
        @if($user->role === 'admin' || $user->role === 'hr')
        <!-- Employees Dropdown -->
        @php
            $employeeCount = $currentCompany
                ? \App\Models\Employee::forCompany($currentCompany->id)->count()
                : \App\Models\Employee::count();
        @endphp
        <div class="relative" x-data="{ 
            open: {{ in_array($activeRoute, ['employees.index', 'employees.info', 'employees.other-employee-info', 'employees.education-training-rating', 'employees.prev-emp-oth', 'employees.documents', 'employees.ytd-info', 'employees.bio-zk']) ? 'true' : 'false' }}
        }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, ['employees.index', 'employees.info', 'employees.other-employee-info', 'employees.education-training-rating', 'employees.prev-emp-oth', 'employees.documents', 'employees.ytd-info', 'employees.bio-zk']) ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-users mr-3 text-lg {{ in_array($activeRoute, ['employees.index', 'employees.info', 'employees.other-employee-info', 'employees.education-training-rating', 'employees.prev-emp-oth', 'employees.documents', 'employees.ytd-info', 'employees.bio-zk']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Employees</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $employeeCount }}</span>
                    <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>

            <!-- Employees Dropdown Menu -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">
                <a href="{{ route('employees.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'employees.index' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-list mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'employees.index' ? 'text-blue-600' : '' }}"></i>
                    <span>Employee List</span>
                </a>
                <a href="{{ route('employees.info') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'employees.info' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-id-card mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'employees.info' ? 'text-blue-600' : '' }}"></i>
                    <span>Employee Info</span>
                </a>
                <a href="{{ route('employees.other-employee-info') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'employees.other-employee-info' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-user-circle mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'employees.other-employee-info' ? 'text-blue-600' : '' }}"></i>
                    <span>Other Employee Info</span>
                </a>
                <a href="{{ route('employees.education-training-rating') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'employees.education-training-rating' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-book mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'employees.education-training-rating' ? 'text-blue-600' : '' }}"></i>
                    <span>Education/ Training/ Rating</span>
                </a>
                <a href="{{ route('employees.prev-emp-oth') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'employees.prev-emp-oth' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-briefcase mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'employees.prev-emp-oth' ? 'text-blue-600' : '' }}"></i>
                    <span>Previous Employer & Other</span>
                </a>
                <a href="{{ route('employees.documents') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'employees.documents' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-file-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'employees.documents' ? 'text-blue-600' : '' }}"></i>
                    <span>Documents</span>
                </a>
                <a href="{{ route('employees.ytd-info') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'employees.ytd-info' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-passport mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'employees.ytd-info' ? 'text-blue-600' : '' }}"></i>
                    <span>YTD - INFO</span>
                </a>
                <a href="{{ route('employees.bio-zk') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'employees.bio-zk' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-dna mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'employees.bio-zk' ? 'text-blue-600' : '' }}"></i>
                    <span>Bio ZK</span>
                </a>
            </div>
        </div>

        <!-- Departments -->
        @php
            $departmentCount = $currentCompany
                ? \App\Models\Department::forCompany($currentCompany->id)->count()
                : \App\Models\Department::count();
        @endphp
        <a href="{{ route('departments.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'departments.index' ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-building mr-3 text-lg {{ $activeRoute === 'departments.index' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Departments</span>
            <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $departmentCount }}</span>
        </a>

        <!-- Positions -->
        @php
            $positionCount = $currentCompany
                ? \App\Models\Position::forCompany($currentCompany->id)->count()
                : \App\Models\Position::count();
        @endphp
        <a href="{{ route('positions.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'positions.index' ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-briefcase mr-3 text-lg {{ $activeRoute === 'positions.index' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Positions</span>
            <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $positionCount }}</span>
        </a>

        <!-- Companies -->
        <a href="{{ route('companies.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'companies.index' ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-industry mr-3 text-lg {{ $activeRoute === 'companies.index' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Companies</span>
            <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ \App\Models\Company::count() }}</span>
        </a>
        @endif

        @if($user->role === 'admin' || $user->role === 'hr' || $user->role === 'manager')
        <!-- Payroll Dropdown -->
        @php
            $payrollRoutes = ['payroll.index', 'payroll.team', 'payroll.runs', 'payroll-templates.index', 'payroll-templates.create', 'payroll-templates.edit', 'loans.index', 'loans.show', 'loan-types.index', 'loan-types.create', 'loan-types.edit'];
        @endphp
        <div class="relative" x-data="{ 
            open: {{ in_array($activeRoute, $payrollRoutes) ? 'true' : 'false' }}
        }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, $payrollRoutes) ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-money-bill-wave mr-3 text-lg {{ in_array($activeRoute, $payrollRoutes) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Payroll</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">

                @if($user->role === 'admin' || $user->role === 'hr')
                <!-- Payroll Index -->
                <a href="{{ route('payroll.index') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'payroll.index' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-list mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'payroll.index' ? 'text-blue-600' : '' }}"></i>
                    <span>Payroll Payments</span>
                </a>

                <a href="{{ route('payroll.runs') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'payroll.runs' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-layer-group mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'payroll.runs' ? 'text-blue-600' : '' }}"></i>
                    <span>Payroll Runs</span>
                </a>

                <!-- Payroll Templates -->
                <a href="{{ route('payroll-templates.index') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ in_array($activeRoute, ['payroll-templates.index', 'payroll-templates.create', 'payroll-templates.edit']) ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-file-invoice mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ in_array($activeRoute, ['payroll-templates.index', 'payroll-templates.create', 'payroll-templates.edit']) ? 'text-blue-600' : '' }}"></i>
                    <span>Payroll Templates</span>
                </a>

                <a href="{{ route('loans.index') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ in_array($activeRoute, ['loans.index', 'loans.show', 'loan-types.index', 'loan-types.create', 'loan-types.edit']) ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-hand-holding-dollar mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ in_array($activeRoute, ['loans.index', 'loans.show', 'loan-types.index', 'loan-types.create', 'loan-types.edit']) ? 'text-blue-600' : '' }}"></i>
                    <span>Loan Management</span>
                </a>
                @else
                <!-- Manager: view-only, own department only -->
                <a href="{{ route('payroll.team') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'payroll.team' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-eye mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'payroll.team' ? 'text-blue-600' : '' }}"></i>
                    <span>My Team's Payroll</span>
                </a>
                @endif

            </div>
        </div>
        @endif

        <!-- Attendance Dropdown -->
        <div class="relative" x-data="{ 
            open: {{ in_array($activeRoute, ['attendance.time-in-out', 'attendance.daily', 'attendance.my', 'employee.schedule', 'attendance.timekeeping', 'attendance.import-dtr', 'schedule-v2.index', 'schedule-v2.create', 'schedule-v2.show', 'schedule-v2.edit', 'attendance.schedule.reports', 'schedule-templates.index', 'schedule-templates.create', 'schedule-templates.edit', 'attendance.overtime', 'attendance.leave-management', 'attendance.official-business', 'attendance.reports', 'attendance.settings', 'attendance.period-management.index', 'attendance.period-management.create', 'attendance.period-management.show']) ? 'true' : 'false' }}
        }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, ['attendance.time-in-out', 'attendance.daily', 'attendance.my', 'employee.schedule', 'attendance.timekeeping', 'attendance.import-dtr', 'schedule-v2.index', 'schedule-v2.create', 'schedule-v2.show', 'schedule-v2.edit', 'attendance.schedule.reports', 'schedule-templates.index', 'schedule-templates.create', 'schedule-templates.edit', 'attendance.overtime', 'attendance.leave-management', 'attendance.official-business', 'attendance.reports', 'attendance.settings', 'attendance.period-management.index', 'attendance.period-management.create', 'attendance.period-management.show']) ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-clock mr-3 text-lg {{ in_array($activeRoute, ['attendance.time-in-out', 'attendance.daily', 'attendance.my', 'employee.schedule', 'attendance.timekeeping', 'attendance.import-dtr', 'schedule-v2.index', 'schedule-v2.create', 'schedule-v2.show', 'schedule-v2.edit', 'attendance.schedule.reports', 'schedule-templates.index', 'schedule-templates.create', 'schedule-templates.edit', 'attendance.overtime', 'attendance.leave-management', 'attendance.official-business', 'attendance.reports', 'attendance.settings', 'attendance.period-management.index', 'attendance.period-management.create', 'attendance.period-management.show']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Attendance</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">

                <!-- Time In/Out - Only show for employees -->
                @if($user->role === 'employee')
                <a href="{{ route('attendance.time-in-out') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'attendance.time-in-out' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-sign-in-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'attendance.time-in-out' ? 'text-blue-600' : '' }}"></i>
                    <span>Time In/Out</span>
                    <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">Live</span>
                </a>
                @endif

                <!-- Attendance Record -->
                <a href="{{ $user->role === 'employee' ? route('attendance.my') : route('attendance.daily') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ in_array($activeRoute, ['attendance.daily', 'attendance.my']) ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-calendar-day mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ in_array($activeRoute, ['attendance.daily', 'attendance.my']) ? 'text-blue-600' : '' }}"></i>
                    <span>{{ $user->role === 'employee' ? 'My Attendance' : 'Attendance Record' }}</span>
                </a>

                <!-- Timekeeping -->
                @if($user->role !== 'employee')
                <a href="{{ route('attendance.timekeeping') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'attendance.timekeeping' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-stopwatch mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'attendance.timekeeping' ? 'text-blue-600' : '' }}"></i>
                    <span>Timekeeping</span>
                </a>
                @endif

                <!-- Import DTR -->
                @if($user->role !== 'employee')
                <a href="{{ route('attendance.import-dtr') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'attendance.import-dtr' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-file-import mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'attendance.import-dtr' ? 'text-blue-600' : '' }}"></i>
                    <span>Import DTR</span>
                    <span class="ml-auto bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-full">New</span>
                </a>
                @endif

                <!-- Schedule -->
                @if($user->role === 'employee')
                <a href="{{ route('employee.schedule') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'employee.schedule' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-calendar-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'employee.schedule' ? 'text-blue-600' : '' }}"></i>
                    <span>My Schedule</span>
                </a>
                @else
                @if(in_array($user->role, ['admin', 'hr', 'manager']))
                @php
                    $scheduleRoutes = ['schedule-v2.index', 'schedule-v2.create', 'schedule-v2.show', 'schedule-v2.edit', 'schedule-templates.index', 'schedule-templates.create', 'schedule-templates.edit'];
                @endphp
                <div class="relative" x-data="{ scheduleOpen: {{ in_array($activeRoute, $scheduleRoutes) ? 'true' : 'false' }} }">
                    <button @click="scheduleOpen = !scheduleOpen" type="button"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-md transition-all duration-200 group">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-plus mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                            <span>Schedule Management</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': scheduleOpen }"></i>
                    </button>

                    <div x-show="scheduleOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="ml-4 mt-1 space-y-1 bg-white rounded-lg p-2 border border-gray-200">
                        <a href="{{ route('schedule-v2.index') }}"
                           class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'schedule-v2.index' ? 'bg-white text-blue-600' : '' }}">
                            <i class="fas fa-calendar-alt mr-3 text-xs text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'schedule-v2.index' ? 'text-blue-600' : '' }}"></i>
                            <span>Schedule Calendar</span>
                        </a>
                        @if(in_array($user->role, ['admin', 'hr']))
                        <a href="{{ route('schedule-templates.index') }}"
                           class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ in_array($activeRoute, ['schedule-templates.index', 'schedule-templates.create', 'schedule-templates.edit']) ? 'bg-white text-blue-600' : '' }}">
                            <i class="fas fa-list-check mr-3 text-xs text-gray-400 group-hover:text-blue-600 {{ in_array($activeRoute, ['schedule-templates.index', 'schedule-templates.create', 'schedule-templates.edit']) ? 'text-blue-600' : '' }}"></i>
                            <span>Schedule Templates</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif
                @endif

                <!-- Overtime -->
                <a href="{{ route('attendance.overtime') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'attendance.overtime' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-clock mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'attendance.overtime' ? 'text-blue-600' : '' }}"></i>
                    <span>Overtime</span>
                </a>

                <!-- Leave Management -->
                <a href="{{ route('attendance.leave-management') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'attendance.leave-management' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-calendar-times mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'attendance.leave-management' ? 'text-blue-600' : '' }}"></i>
                    <span>Leave Management</span>
                </a>

                <!-- Official Business -->
                <a href="{{ route('attendance.official-business') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'attendance.official-business' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-briefcase mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'attendance.official-business' ? 'text-blue-600' : '' }}"></i>
                    <span>Official Business</span>
                </a>

                <!-- Period Management -->
                @if(in_array($user->role, ['admin', 'hr']))
                <a href="{{ route('attendance.period-management.index') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'attendance.period-management.index' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-calendar-week mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'attendance.period-management.index' ? 'text-blue-600' : '' }}"></i>
                    <span>Period Management</span>
                    <span class="ml-auto bg-purple-100 text-purple-600 text-xs px-2 py-1 rounded-full">New</span>
                </a>
                @endif

                @if($user->role === 'admin' || $user->role === 'hr')
                <!-- Timekeeping and HRIS Reports -->
                <div class="border-t border-gray-200 my-2"></div>
                <div class="relative group timekeeping-report-group">
                    <button class="timekeepingReportBtn w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                        <i class="fas fa-file-chart mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                        <span>Timekeeping & HRIS Reports</span>
                        <i class="fas fa-chevron-down ml-auto text-xs text-gray-400 group-hover:text-blue-600"></i>
                    </button>

                    <!-- Timekeeping Reports Submenu -->
                    <div class="timekeepingReportSubMenu hidden flex-col pl-4 mt-1 space-y-1 py-2">
                        <a href="{{ route('attendance.timekeeping') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg group hover:text-blue-600 transition-colors">
                            <i class="fas fa-hourglass-half mr-3 text-gray-400"></i>
                            <span>Time Summary</span>
                        </a>
                        <a href="{{ route('attendance.timekeeping') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg group hover:text-blue-600 transition-colors">
                            <i class="fas fa-id-card mr-3 text-gray-400"></i>
                            <span>Time Card</span>
                        </a>
                        <a href="{{ route('attendance.timekeeping') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg group hover:text-blue-600 transition-colors">
                            <i class="fas fa-list mr-3 text-gray-400"></i>
                            <span>Time Sheets</span>
                        </a>
                        <a href="{{ route('attendance.timekeeping', ['exception' => 'missing_schedule']) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg group hover:text-blue-600 transition-colors">
                            <i class="fas fa-user-slash mr-3 text-gray-400"></i>
                            <span>Absences</span>
                        </a>
                        <a href="{{ route('attendance.timekeeping', ['exception' => 'possible_wrong_schedule']) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg group hover:text-blue-600 transition-colors">
                            <i class="fas fa-exclamation-triangle mr-3 text-gray-400"></i>
                            <span>Undertime & Tardiness</span>
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg group hover:text-blue-600 transition-colors">
                            <i class="fas fa-chart-line mr-3 text-gray-400"></i>
                            <span>Overtime Report</span>
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg group hover:text-blue-600 transition-colors">
                            <i class="fas fa-users mr-3 text-gray-400"></i>
                            <span>Employee Reports</span>
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg group hover:text-blue-600 transition-colors">
                            <i class="fas fa-calendar-check mr-3 text-gray-400"></i>
                            <span>Balance of Leaves</span>
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg group hover:text-blue-600 transition-colors">
                            <i class="fas fa-file-contract mr-3 text-gray-400"></i>
                            <span>Employee Filings</span>
                        </a>
                    </div>
                </div>

                <!-- Attendance Reports -->
                <div class="border-t border-gray-200 my-2"></div>
                <a href="{{ route('attendance.reports') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'attendance.reports' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-chart-line mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'attendance.reports' ? 'text-blue-600' : '' }}"></i>
                    <span>Attendance Reports</span>
                </a>

                <!-- Attendance Settings -->
                <a href="{{ route('settings') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'settings' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-cog mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'settings' ? 'text-blue-600' : '' }}"></i>
                    <span>Attendance Settings</span>
                </a>
                @endif
            </div>
        </div>

        @if($user->role === 'admin' || $user->role === 'hr')
        <!-- Tax Brackets -->
        <a href="{{ route('tax-brackets.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'tax-brackets.index' ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-percentage mr-3 text-lg {{ $activeRoute === 'tax-brackets.index' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Tax Brackets</span>
            <span class="ml-auto bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">New</span>
        </a>

        <!-- Reports Dropdown -->
        <div class="relative" x-data="{
            open: {{ in_array($activeRoute, ['attendance.reports', 'payrolls.summary']) ? 'true' : 'false' }}
        }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, ['attendance.reports', 'payrolls.summary']) ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-chart-bar mr-3 text-lg {{ in_array($activeRoute, ['attendance.reports', 'payrolls.summary']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Reports</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200 max-h-96 overflow-y-auto">
                <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'reports.index' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-file-export mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'reports.index' ? 'text-blue-600' : '' }}"></i>
                    <span>Consolidated Reports</span>
                </a>
                <a href="{{ route('attendance.reports') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'attendance.reports' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-hourglass-half mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'attendance.reports' ? 'text-blue-600' : '' }}"></i>
                    <span>Time Summary</span>
                </a>
                <a href="{{ route('payrolls.summary') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'payrolls.summary' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-calculator mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'payrolls.summary' ? 'text-blue-600' : '' }}"></i>
                    <span>Payroll Reports</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-receipt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Payslips</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-university mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Bank Remittance</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-coins mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Denominations</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-list mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Received List</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-gift mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Allowances</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-star mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Allowance Special Report</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-piggy-bank mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Loan Balances</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-book mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Deduction Register</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-file-pdf mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>SSS Report</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-heartbeat mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Philhealth Report & RF-1</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-percent mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Tax Report</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-shield-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Pag Ibig Report</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-ledger mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Account Entries</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-file-alt mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Text File Reports</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-print mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Print Undeducted Items</span>
                </a>
            </div>
        </div>
        @endif

        @if($user->role === 'admin')
        <!-- Settings -->
        <a href="{{ route('hr.settings') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'hr.settings' ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-cog mr-3 text-lg {{ $activeRoute === 'hr.settings' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Settings</span>
        </a>
        @endif

        <!-- Divider -->
        <div class="my-6 border-t border-gray-200"></div>

        <!-- Quick Actions -->
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
        @endif

        @if($user->role === 'employee')
            <!-- Time In Button -->
            @if(!$todayAttendance || !$todayAttendance->time_in || $todayAttendance->time_out)
                <button onclick="sidebarConfirmTimeIn()" class="w-full flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-green-600 rounded-lg transition-all duration-200 group">
                    <i class="fas fa-sign-in-alt mr-3 text-lg text-gray-400 group-hover:text-green-600"></i>
                    <span>Time In</span>
                </button>
            @else
                <div class="flex items-center px-4 py-3 text-sm font-medium text-gray-400 rounded-lg cursor-not-allowed">
                    <i class="fas fa-check mr-3 text-lg text-gray-400"></i>
                    <span>Already Clocked In</span>
                </div>
            @endif

            <!-- Time Out Button -->
            @if($todayAttendance && $todayAttendance->time_in && !$todayAttendance->time_out)
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
                <div class="flex items-center px-4 py-3 text-sm font-medium text-gray-400 rounded-lg cursor-not-allowed">
                    <i class="fas fa-sign-out-alt mr-3 text-lg text-gray-400"></i>
                    <span>Time Out (Clock In First)</span>
                </div>
            @endif

            <!-- Payslip Download Button for Employees -->
            @php
                // Simple check - if we can't query database, just show disabled button
                $latestPayroll = null;
                try {
                    if (isset($user->employee) && $user->employee) {
                        $latestPayroll = \App\Models\Payroll::where('employee_id', $user->employee->id)
                            ->whereIn('status', ['approved', 'paid'])
                            ->latest()
                            ->first();
                    }
                } catch (\Exception $e) {
                    // If there's an error, just show disabled button
                    $latestPayroll = null;
                }
            @endphp

            @if($latestPayroll)
                <button id="nav-download-payslip-btn"
                        onclick="downloadEmployeePayslip('{{ $latestPayroll->id }}')"
                        class="w-full flex items-center justify-center px-4 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-all duration-200 group">
                    <i class="fas fa-download mr-3 text-lg"></i>
                    <span>Download Payslip</span>
                </button>
            @else
                <button disabled class="w-full flex items-center justify-center px-4 py-3 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg cursor-not-allowed group">
                    <i class="fas fa-download mr-3 text-lg text-gray-500"></i>
                    <span>No Payslip Available</span>
                </button>
            @endif

            <a href="{{ route('employee.payroll.history') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'employee.payroll.history' ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <i class="fas fa-receipt mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
                <span>Salary & Payslips</span>
            </a>

            <a href="{{ route('loans.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ in_array($activeRoute, ['loans.index', 'loans.create', 'loans.show']) ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <i class="fas fa-hand-holding-dollar mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
                <span>My Loans</span>
            </a>

            <!-- Update Profile -->
            <a href="{{ route('hr.profile') }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
                <i class="fas fa-edit mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
                <span>Update Profile</span>
            </a>

            <!-- Contact HR -->
            <a href="{{ route('hr.contact.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
                <i class="fas fa-question-circle mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
                <span>Contact HR</span>
            </a>

            <!-- Help & Support -->
            <a href="{{ route('hr.help-support') }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
                <i class="fas fa-life-ring mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
                <span>Help & Support</span>
            </a>
        @endif

        <!-- Additional test items to ensure scrolling -->
        @if($user->role === 'admin' || $user->role === 'hr')
        <!-- Documents -->
        <a href="{{ route('documents.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('documents.*') ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-folder mr-3 text-lg {{ request()->routeIs('documents.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Documents</span>
        </a>

        <a href="{{ route('attendance.leave-management.create') }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
            <i class="fas fa-calendar-check mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
            <span>Leave Requests</span>
        </a>

        <a href="{{ route('employees.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
            <i class="fas fa-user-friends mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
            <span>Team Directory</span>
        </a>

        <a href="{{ route('notifications.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'notifications.index' ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-bell mr-3 text-lg {{ $activeRoute === 'notifications.index' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Notifications</span>
        </a>

        <a href="{{ route('hr.contacts.admin') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ $activeRoute === 'hr.contacts.admin' ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
            <i class="fas fa-inbox mr-3 text-lg {{ $activeRoute === 'hr.contacts.admin' ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
            <span>Inbox</span>
            <span class="ml-auto text-xs font-semibold px-2 py-0.5 bg-green-500 text-white rounded-full">NEW</span>
        </a>

        <a href="{{ route('hr.help-support') }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200 group">
            <i class="fas fa-question-circle mr-3 text-lg text-gray-400 group-hover:text-blue-600"></i>
            <span>Help & Support</span>
            <span class="ml-auto text-xs font-semibold px-2 py-0.5 bg-green-500 text-white rounded-full">NEW</span>
        </a>

        <!-- Developer -->
        <div class="relative" x-data="{
            open: {{ in_array($activeRoute, ['developer.accounts.index', 'developer.accounts.edit']) ? 'true' : 'false' }}
        }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium {{ in_array($activeRoute, ['developer.accounts.index', 'developer.accounts.edit']) ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }} rounded-lg transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-code mr-3 text-lg {{ in_array($activeRoute, ['developer.accounts.index', 'developer.accounts.edit']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span>Developer</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="ml-8 mt-2 space-y-1 bg-gray-50 rounded-lg p-2 border border-gray-200">

                <a href="{{ route('developer.accounts.index') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ in_array($activeRoute, ['developer.accounts.index', 'developer.accounts.edit']) ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-user-cog mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ in_array($activeRoute, ['developer.accounts.index', 'developer.accounts.edit']) ? 'text-blue-600' : '' }}"></i>
                    <span>User Account CRUD</span>
                </a>

                @if($user->role === 'admin')
                <a href="{{ route('developer.permissions.index') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'developer.permissions.index' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-user-shield mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'developer.permissions.index' ? 'text-blue-600' : '' }}"></i>
                    <span>Role & Permission Mapping</span>
                </a>
                @endif
                <a href="{{ route('developer.accounts.link') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'developer.accounts.link' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-link mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'developer.accounts.link' ? 'text-blue-600' : '' }}"></i>
                    <span>Link Account to Employee</span>
                </a>
                <a href="{{ route('developer.accounts.index') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-key mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Password / Reset Handling</span>
                </a>
                <a href="{{ route('developer.accounts.index') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group">
                    <i class="fas fa-ban mr-3 text-sm text-gray-400 group-hover:text-blue-600"></i>
                    <span>Restrict Admin Access by Role</span>
                </a>
                @if($user->role === 'admin')
                <a href="{{ route('developer.activity-logs.index') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'developer.activity-logs.index' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-clipboard-list mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'developer.activity-logs.index' ? 'text-blue-600' : '' }}"></i>
                    <span>Activity Logs</span>
                </a>
                <a href="{{ route('developer.recycle-bin.index') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'developer.recycle-bin.index' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-trash-can mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'developer.recycle-bin.index' ? 'text-blue-600' : '' }}"></i>
                    <span>Recycle Bin</span>
                </a>
                <a href="{{ route('developer.database-backup.index') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white hover:text-blue-600 rounded-md transition-all duration-200 group {{ $activeRoute === 'developer.database-backup.index' ? 'bg-white text-blue-600' : '' }}">
                    <i class="fas fa-database mr-3 text-sm text-gray-400 group-hover:text-blue-600 {{ $activeRoute === 'developer.database-backup.index' ? 'text-blue-600' : '' }}"></i>
                    <span>Database Backup</span>
                </a>
                @endif
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

// Show confirmation modal (similar to dashboard)
function showConfirmationModal(title, message, confirmAction, options = {}) {
    // Check if the modal exists in the main dashboard
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
    // Get current time for the confirmation message
    const currentTime = getPhilippineTime();
    const formattedTime = format12HourTime(currentTime);

    showConfirmationModal(
        'Confirm Time In',
        `Are you sure you want to clock in at ${formattedTime}?`,
        sidebarTimeIn, // This will be called after confirmation
        {
            color: 'green',
            icon: 'fa-sign-in-alt'
        }
    );
}

// Confirm Time Out from sidebar
function sidebarConfirmTimeOut() {
    // Get current time for the confirmation message
    const currentTime = getPhilippineTime();
    const formattedTime = format12HourTime(currentTime);

    showConfirmationModal(
        'Confirm Time Out',
        `Are you sure you want to clock out at ${formattedTime}?`,
        sidebarTimeOut, // This will be called after confirmation
        {
            color: 'red',
            icon: 'fa-sign-out-alt'
        }
    );
}

// Time In function for sidebar (called after confirmation)
async function sidebarTimeIn() {
    try {
        const response = await fetch('{{ route("attendance.time-in") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (response.ok) {
            showSuccess(data.message);
            // Refresh the page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showError(data.error || 'Failed to clock in');
        }
    } catch (error) {
        console.error('Error clocking in:', error);
        showError('Failed to clock in');
    }
}

// Time Out function for sidebar (called after confirmation)
async function sidebarTimeOut() {
    try {
        const response = await fetch('{{ route("attendance.time-out") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (response.ok) {
            showSuccess(data.message);
            // Refresh the page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showError(data.error || 'Failed to clock out');
        }
    } catch (error) {
        console.error('Error clocking out:', error);
        showError('Failed to clock out');
    }
}


// Show success message
function showSuccess(message) {
    // Check if the function exists in the main dashboard
    if (typeof window.showSuccess === 'function') {
        window.showSuccess(message);
        return;
    }

    // Fallback: alert
    alert('Success: ' + message);
}

// Show error message
function showError(message) {
    // Check if the function exists in the main dashboard
    if (typeof window.showError === 'function') {
        window.showError(message);
        return;
    }

    // Fallback: alert
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

        // Hover removed for accordion style
    });
});
</script>