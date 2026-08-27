@props(['title', 'user'])

@php
    // Check the employee-linked attendance state for every role.
    $isCurrentlyTimedIn = false;
    $todayAttendance = null;
    if ($user->employee) {
        $todayAttendance = $user->employee->getTodayAttendance();
        $isCurrentlyTimedIn = $todayAttendance && $todayAttendance->hasActiveTimeEntry();
    }

    $isDashboard = str_ends_with($title, 'Dashboard');
    $moduleDescription = match ($title) {
        'Official Business' => request()->query('scope') === 'mine'
            ? 'Submit and track your Official Business requests'
            : 'Review and manage employee OB requests.',
        'Overtime Management' => request()->query('scope') === 'mine'
            ? 'Apply for overtime and track your requests'
            : 'Review and manage employee overtime requests.',
        'Leave Management' => 'Review and manage employee leave requests.',
        'Employees' => 'Manage employee records and account information.',
        'Departments', 'Archived Departments' => 'Organize departments and employee assignments.',
        'Positions' => 'Manage company positions, levels, and salary ranges.',
        'Companies' => 'Manage company profiles and active organizations.',
        'Payroll Payments' => 'Process and monitor employee payroll payments.',
        'Payroll Runs' => 'Generate, review, finalize, and lock payroll runs.',
        'Payroll Period Management' => 'Manage payroll cutoff periods and workflow status.',
        'Payroll Templates' => 'Configure reusable payroll calculation templates.',
        'Salary & Payslips' => 'Your approved and paid payroll history',
        'My Loans' => 'View and manage your own loan requests and repayment status.',
        'Loan Management' => 'Review employee loan requests, approvals, and repayments.',
        'Payroll Summary Report', 'Monthly Payroll Report' => 'Review summarized payroll results and trends.',
        'Attendance Records Report', 'Attendance Reports', 'Daily Attendance Report' => 'Review and export employee attendance data.',
        'Daily Attendance', 'My Attendance', 'Timekeeping' => 'Monitor employee attendance and time records.',
        'Schedule Management', 'Schedule Management V2', 'Schedule Templates' => 'Manage employee schedules and work patterns.',
        'Import DTR', 'Review DTR Import' => 'Import and validate employee time records.',
        'Tax Brackets Management' => 'Manage tax brackets and deduction rates.',
        'Reports', 'Report Results' => 'Generate and review HR and payroll reports.',
        'HR Contact Management', 'Messages from Employees' => 'Review and respond to employee concerns.',
        'Help & Support' => 'Find guidance and support for system features.',
        'Attendance Settings' => 'Configure attendance rules and company policies.',
        'Login Logs' => 'Review account access and authentication activity.',
        default => 'View and manage '.str($title)->lower().'.',
    };
@endphp

<header class="brand-top-header bg-white backdrop-blur-sm shadow-sm border-b border-gray-200 sticky top-0 z-40 dark:bg-slate-900 dark:border-slate-700">
    <div class="flex items-center justify-between gap-2 sm:gap-3 lg:gap-4 h-16 sm:h-20 px-3 sm:px-4 lg:px-8">
        <div class="flex items-center flex-1 min-w-0">
            <button class="lg:hidden text-gray-500 hover:text-gray-700 p-1.5 sm:p-2 rounded-lg hover:bg-gray-100 transition-colors flex-shrink-0 dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-slate-700" onclick="toggleSidebar()">
                <i class="fas fa-bars text-lg sm:text-xl"></i>
            </button>
            <div class="ml-2 sm:ml-4 lg:ml-0 min-w-0 flex-1">
                <h1 id="page-header-title" class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 truncate dark:text-gray-100">{{ $title }}</h1>
                @if($isDashboard)
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-4">
                        <p class="text-xs sm:text-sm text-gray-500 truncate dark:text-gray-400">Welcome back, {{ $user->full_name }}</p>
                        <div class="hidden lg:flex items-center text-xs text-gray-400 whitespace-nowrap dark:text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            <span id="current-time-desktop" class="hidden sm:inline">{{ \App\Helpers\TimezoneHelper::now()->format('M d, Y g:i A') }}</span>
                            <span id="current-time-mobile" class="sm:hidden">{{ \App\Helpers\TimezoneHelper::now()->format('g:i A') }}</span>
                            <span class="ml-1 text-blue-600 dark:text-blue-400">PHT</span>
                        </div>
                    </div>
                @else
                    <p id="page-header-description" class="text-xs sm:text-sm text-gray-500 truncate dark:text-gray-400">
                        {{ $moduleDescription }}
                    </p>
                @endif
            </div>
        </div>

        <x-dashboard.global-search :user="$user" />

        <div class="flex items-center space-x-1 sm:space-x-2 lg:space-x-3 flex-shrink-0">
            @php
                $currentCompany = \App\Helpers\CompanyHelper::getCurrentCompany();
                $allCompanies = \App\Models\Company::where('is_active', true)->orderBy('name')->get();
            @endphp
            
            <!-- Company Selector - Hidden on mobile, shown on tablet+ -->
            <div class="hidden md:block relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-800 border border-green-700 hover:border-green-900 hover:from-green-700 hover:to-green-900 rounded-lg shadow-md transition-all group">
                    <i class="fas fa-building text-white mr-2"></i>
                    <span class="text-sm font-medium text-white">
                        {{ $currentCompany ? $currentCompany->name : 'No Company' }}
                    </span>
                    <span class="ml-2 px-2 py-0.5 bg-white/20 border border-white/30 text-white text-xs font-medium rounded-full">Active</span>
                    <i class="fas fa-chevron-down ml-2 text-white text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open"
                     x-cloak
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50 max-h-96 overflow-y-auto dark:bg-slate-900 dark:border-slate-700">

                    <!-- Dropdown Header -->
                    <div class="px-4 py-2 border-b border-gray-100 dark:border-slate-700">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide dark:text-gray-400">Select Company</p>
                    </div>

                    <!-- Company List -->
                    @foreach($allCompanies as $company)
                        <form method="POST" action="{{ route('companies.switch') }}" class="block"
                              onsubmit="handleCompanySwitch(event, '{{ $company->name }}')">
                            @csrf
                            <input type="hidden" name="company_id" value="{{ $company->id }}">
                            <button type="submit" class="w-full flex items-center px-4 py-3 text-left hover:bg-green-50 transition-colors {{ $currentCompany && $company->id === $currentCompany->id ? 'bg-green-50 cursor-not-allowed' : 'cursor-pointer' }}">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-900">{{ $company->name }}</span>
                                        @if($currentCompany && $company->id === $currentCompany->id)
                                            <span class="inline-flex items-center flex-shrink-0 px-2 py-0.5 bg-green-700 text-white text-xs font-medium rounded-full whitespace-nowrap">
                                                <i class="fas fa-check-circle mr-1"></i>Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center flex-shrink-0 px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full dark:bg-slate-700 dark:text-gray-300 whitespace-nowrap">Switch</span>
                                        @endif
                                    </div>
                                    @if($company->code)
                                        <p class="text-xs text-gray-500 mt-0.5 dark:text-gray-400">{{ $company->code }}</p>
                                    @endif
                                </div>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
            <button type="button" onclick="toggleTheme()" title="Toggle dark/light theme" class="p-1.5 sm:p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors dark:text-gray-200 dark:hover:bg-slate-700">
                <i id="themeToggleIcon" class="fas fa-moon text-lg"></i>
            </button>
            
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50" 
                     x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 3000)">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50" 
                     x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 3000)">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            
            <!-- Notifications - Only show for HR and Admin -->
            @if(in_array($user->role, ['admin', 'hr']))
            <div class="relative" x-data="{
                open: false,
                notifications: [],
                unreadCount: 0,
                loading: false,
                userRole: '{{ $user->role }}',
                
                init() {
                    if (this.userRole === 'admin' || this.userRole === 'hr') {
                        this.loadNotifications();
                        
                        // Refresh every 60 seconds
                        setInterval(() => {
                            if (!this.open) {
                                this.loadNotifications();
                            }
                        }, 60000);
                    }
                },
                
                toggleNotifications() {
                    if (this.userRole !== 'admin' && this.userRole !== 'hr') {
                        return; // Don't open for non-HR/Admin
                    }
                    
                    this.open = !this.open;
                    if (this.open) {
                        this.loadNotifications();
                    }
                },
                
                async loadNotifications() {
                    if (this.userRole !== 'admin' && this.userRole !== 'hr') {
                        return; // Don't load for non-HR/Admin
                    }
                    
                    this.loading = true;
                    try {
                        const response = await fetch('/notifications/login-logs', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                            }
                        });
                        
                        if (!response.ok) {
                            if (response.status === 403) {
                                // Unauthorized - user is not HR/Admin
                                this.notifications = [];
                                this.unreadCount = 0;
                                return;
                            }
                            throw new Error('Failed to load notifications');
                        }
                        
                        const data = await response.json();
                        this.notifications = data.logs || [];
                        this.unreadCount = data.unread_count || 0;
                        this.userRole = data.user_role || this.userRole;
                    } catch (error) {
                        console.error('Error loading notifications:', error);
                        this.notifications = [{
                            id: 'error',
                            employee_name: 'Unable to load login logs',
                            employee_email: 'Please try again later',
                            ip_address: 'Error',
                            user_agent: 'Connection failed',
                            time_ago: 'Just now'
                        }];
                        this.unreadCount = 0;
                    } finally {
                        this.loading = false;
                    }
                },
                
                markAllAsRead() {
                    this.unreadCount = 0;
                    // In a real implementation, you would mark notifications as read in the database
                    // For now, we just clear the badge
                }
            }">
                <button @click="toggleNotifications()"
                        :class="{'bg-gray-100 dark:bg-slate-700': open}"
                        class="relative p-1.5 sm:p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-slate-700">
                    <i class="fas fa-bell text-lg sm:text-xl"></i>
                    <template x-if="unreadCount > 0">
                        <span x-text="unreadCount > 99 ? '99+' : unreadCount"
                              class="absolute -top-0.5 -right-0.5 sm:-top-1 sm:-right-1 block h-4 w-4 sm:h-5 sm:w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center animate-pulse"></span>
                    </template>
                </button>

                <!-- Notification Dropdown -->
                <div x-show="open"
                     x-cloak
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50 max-h-[80vh] overflow-hidden flex flex-col dark:bg-slate-900 dark:border-slate-700">

                    <!-- Header -->
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-50 to-white dark:from-slate-800 dark:to-slate-900 dark:border-slate-700">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications</h3>
                            <p class="text-xs text-gray-500 mt-0.5 dark:text-gray-400" x-text="userRole === 'admin' ? 'Admin View' : 'HR View'"></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span x-show="loading" class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-spinner fa-spin mr-1"></i>
                            </span>
                            <button @click="loadNotifications()" class="text-xs text-blue-600 hover:text-blue-800 p-1 dark:text-blue-400 dark:hover:text-blue-300">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Notifications List -->
                    <div class="flex-1 overflow-y-auto">
                        <template x-if="notifications.length === 0 && !loading">
                            <div class="px-4 py-8 text-center">
                                <i class="fas fa-bell-slash text-gray-300 text-3xl mb-2 dark:text-slate-600"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400">No notifications</p>
                                <p class="text-xs text-gray-400 mt-1 dark:text-gray-500">Payroll reminders and login activity will appear here</p>
                            </div>
                        </template>

                        <template x-if="loading">
                            <div class="px-4 py-8 text-center">
                                <div class="inline-block">
                                    <i class="fas fa-spinner fa-spin text-blue-600 text-2xl dark:text-blue-400"></i>
                                </div>
                                <p class="text-sm text-gray-500 mt-2 dark:text-gray-400">Loading login logs...</p>
                            </div>
                        </template>

                        <template x-for="notification in notifications" :key="notification.id">
                            <div @click="notification.url && (window.location.href = notification.url)"
                                 :class="notification.url ? 'cursor-pointer' : ''"
                                 class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors dark:border-slate-800 dark:hover:bg-slate-800">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm"
                                             :class="notification.color === 'orange' ? 'bg-orange-100 dark:bg-orange-900/40' : 'bg-blue-100 dark:bg-slate-700'">
                                            <i class="fas text-sm"
                                               :class="[notification.icon || 'fa-bell', notification.color === 'orange' ? 'text-orange-600 dark:text-orange-400' : 'text-blue-600 dark:text-blue-400']"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-100" x-text="notification.employee_name"></p>
                                            <span class="text-xs text-gray-400 ml-2 flex-shrink-0 dark:text-gray-500" x-text="notification.time_ago"></span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5 truncate dark:text-gray-400" x-text="notification.employee_email"></p>
                                        <div class="flex items-center mt-1 space-x-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-gray-300">
                                                <i class="fas fa-globe mr-1 text-xs"></i>
                                                <span x-text="notification.ip_address"></span>
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 dark:bg-slate-800 dark:text-blue-300">
                                                <i class="fas fa-desktop mr-1 text-xs"></i>
                                                <span x-text="notification.user_agent"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Footer -->
                    <div class="px-4 py-2 border-t border-gray-100 bg-gray-50 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="notifications.length"></span> notification(s)
                            </p>
                            <button @click="loadNotifications()" class="text-xs text-blue-600 hover:text-blue-800 font-medium dark:text-blue-400 dark:hover:text-blue-300">
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- My Requests Notifications - Leave / Overtime / Official Business status changes.
                 Admin and HR use the primary notification control above. -->
            @unless(in_array($user->role, ['admin', 'hr']))
            <div class="relative" x-data="{
                open: false,
                notifications: [],
                unreadCount: 0,
                loading: false,

                init() {
                    this.loadNotifications();
                    setInterval(() => {
                        if (!this.open) {
                            this.loadNotifications();
                        }
                    }, 60000);
                },

                toggleNotifications() {
                    this.open = !this.open;
                    if (this.open) {
                        this.loadNotifications();
                    }
                },

                async loadNotifications() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('notifications.mine') }}', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to load notifications');
                        }

                        const data = await response.json();
                        this.notifications = data.notifications || [];
                        this.unreadCount = data.unread_count || 0;
                    } catch (error) {
                        console.error('Error loading request notifications:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async openNotification(notification) {
                    if (!notification.read && !notification.persistent) {
                        try {
                            await fetch(`/notifications/${notification.id}/read`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                                }
                            });
                            notification.read = true;
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                        } catch (error) {
                            console.error('Error marking notification as read:', error);
                        }
                    }
                    if (notification.url) {
                        window.location.href = notification.url;
                    }
                },

                async markAllAsRead() {
                    try {
                        await fetch('{{ route('notifications.read-all') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                            }
                        });
                        this.notifications.forEach(n => {
                            if (!n.persistent) n.read = true;
                        });
                        this.unreadCount = this.notifications.filter(n => n.persistent && !n.read).length;
                    } catch (error) {
                        console.error('Error marking all as read:', error);
                    }
                }
            }">
                <button @click="toggleNotifications()"
                        :class="{'bg-gray-100 dark:bg-slate-700': open}"
                        title="My request updates"
                        class="relative p-1.5 sm:p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-slate-700">
                    <i class="fas fa-bell text-lg sm:text-xl"></i>
                    <template x-if="unreadCount > 0">
                        <span x-text="unreadCount > 99 ? '99+' : unreadCount"
                              class="absolute -top-0.5 -right-0.5 sm:-top-1 sm:-right-1 block h-4 w-4 sm:h-5 sm:w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center animate-pulse"></span>
                    </template>
                </button>

                <div x-show="open"
                     x-cloak
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50 max-h-[80vh] overflow-hidden flex flex-col dark:bg-slate-900 dark:border-slate-700">

                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-50 to-white dark:from-slate-800 dark:to-slate-900 dark:border-slate-700">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">My Request Updates</h3>
                            <p class="text-xs text-gray-500 mt-0.5 dark:text-gray-400">Leave, Overtime &amp; Official Business</p>
                        </div>
                        <span x-show="loading" class="text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        <template x-if="notifications.length === 0 && !loading">
                            <div class="px-4 py-8 text-center">
                                <i class="fas fa-bell-slash text-gray-300 text-3xl mb-2 dark:text-slate-600"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400">No updates yet</p>
                                <p class="text-xs text-gray-400 mt-1 dark:text-gray-500">You'll see status changes for your requests here</p>
                            </div>
                        </template>

                        <template x-for="notification in notifications" :key="notification.id">
                            <button @click="openNotification(notification)"
                                    class="w-full text-left px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors dark:border-slate-800 dark:hover:bg-slate-800"
                                    :class="{ 'bg-blue-50/50 dark:bg-slate-800/60': !notification.read }">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm"
                                             :class="{
                                                'bg-green-100 dark:bg-green-900/40': notification.color === 'green',
                                                'bg-red-100 dark:bg-red-900/40': notification.color === 'red',
                                                'bg-orange-100 dark:bg-orange-900/40': notification.color === 'orange',
                                                'bg-gray-200 dark:bg-slate-700': notification.color === 'gray',
                                                'bg-blue-100 dark:bg-blue-900/40': !['green','red','orange','gray'].includes(notification.color)
                                             }">
                                            <i class="fas text-sm"
                                               :class="[notification.icon || 'fa-bell', {
                                                    'text-green-600 dark:text-green-400': notification.color === 'green',
                                                    'text-red-600 dark:text-red-400': notification.color === 'red',
                                                    'text-orange-600 dark:text-orange-400': notification.color === 'orange',
                                                    'text-gray-600 dark:text-gray-300': notification.color === 'gray',
                                                    'text-blue-600 dark:text-blue-400': !['green','red','orange','gray'].includes(notification.color)
                                               }]"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="notification.title"></p>
                                            <span class="text-xs text-gray-400 ml-2 flex-shrink-0 dark:text-gray-500" x-text="notification.time_ago"></span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-0.5 dark:text-gray-400" x-text="notification.message"></p>
                                    </div>
                                    <template x-if="!notification.read">
                                        <span class="ml-2 mt-1 h-2 w-2 rounded-full bg-blue-600 flex-shrink-0 dark:bg-blue-400"></span>
                                    </template>
                                </div>
                            </button>
                        </template>
                    </div>

                    <div class="px-4 py-2 border-t border-gray-100 bg-gray-50 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="notifications.length"></span> updates
                            </p>
                            <button @click="markAllAsRead()" class="text-xs text-blue-600 hover:text-blue-800 font-medium dark:text-blue-400 dark:hover:text-blue-300">
                                Mark all as read
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @endunless

            <!-- Inbox Popup - Only show for HR and Admin -->
            @if(in_array($user->role, ['admin', 'hr']))
            <div class="relative" x-data="{
                open: false,
                messages: [],
                pendingCount: 0,
                loading: false,
                
                init() {
                    this.loadMessages();
                    // Refresh every 30 seconds
                    setInterval(() => {
                        if (!this.open) {
                            this.loadMessages();
                        }
                    }, 30000);
                },
                
                toggleMessages() {
                    this.open = !this.open;
                    if (this.open) {
                        this.loadMessages();
                    }
                },
                
                async loadMessages() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('hr.inbox.quick') }}', {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to load messages');
                        }
                        
                        const data = await response.json();
                        this.messages = data.messages || [];
                        this.pendingCount = data.pending_count || 0;
                    } catch (error) {
                        console.error('Error loading messages:', error);
                        this.messages = [];
                        this.pendingCount = 0;
                    } finally {
                        this.loading = false;
                    }
                }
            }">
                <button @click="toggleMessages()"
                        :class="{'bg-gray-100 dark:bg-slate-700': open}"
                        class="relative p-1.5 sm:p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-slate-700">
                    <i class="fas fa-inbox text-lg sm:text-xl"></i>
                    <template x-if="pendingCount > 0">
                        <span x-text="pendingCount > 99 ? '99+' : pendingCount"
                              class="absolute -top-0.5 -right-0.5 sm:-top-1 sm:-right-1 block h-4 w-4 sm:h-5 sm:w-5 rounded-full bg-indigo-500 text-white text-xs flex items-center justify-center animate-pulse"></span>
                    </template>
                </button>

                <!-- Inbox Dropdown -->
                <div x-show="open"
                     x-cloak
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50 max-h-[80vh] overflow-hidden flex flex-col dark:bg-slate-900 dark:border-slate-700">

                    <!-- Header -->
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-indigo-50 to-white dark:from-slate-800 dark:to-slate-900 dark:border-slate-700">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Employee Messages</h3>
                            <p class="text-xs text-gray-500 mt-0.5 dark:text-gray-400" x-text="pendingCount + ' Pending'"></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span x-show="loading" class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-spinner fa-spin mr-1"></i>
                            </span>
                            <button @click="loadMessages()" class="text-xs text-indigo-600 hover:text-indigo-800 p-1 dark:text-indigo-400 dark:hover:text-indigo-300">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Messages List -->
                    <div class="flex-1 overflow-y-auto">
                        <template x-if="messages.length === 0 && !loading">
                            <div class="px-4 py-8 text-center">
                                <i class="fas fa-inbox text-gray-300 text-3xl mb-2 dark:text-slate-600"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400">No messages</p>
                                <p class="text-xs text-gray-400 mt-1 dark:text-gray-500">Employee messages will appear here</p>
                            </div>
                        </template>

                        <template x-if="loading">
                            <div class="px-4 py-8 text-center">
                                <div class="inline-block">
                                    <i class="fas fa-spinner fa-spin text-indigo-600 text-2xl dark:text-indigo-400"></i>
                                </div>
                                <p class="text-sm text-gray-500 mt-2 dark:text-gray-400">Loading messages...</p>
                            </div>
                        </template>

                        <template x-for="message in messages" :key="message.id">
                            <a :href="'/hr/contact/' + message.id"
                               class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors dark:border-slate-800 dark:hover:bg-slate-800">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-100 to-indigo-50 flex items-center justify-center shadow-sm dark:from-slate-700 dark:to-slate-800">
                                            <i class="fas fa-envelope text-indigo-600 text-sm dark:text-indigo-400"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-100" x-text="message.sender_name"></p>
                                            <span class="text-xs text-gray-400 ml-2 flex-shrink-0 dark:text-gray-500" x-text="message.time_ago"></span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-0.5 truncate dark:text-gray-400" x-text="message.subject"></p>
                                        <div class="flex items-center mt-1 space-x-2">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium" :class="{
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300': message.status === 'pending',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': message.status === 'in_progress',
                                                'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300': message.status === 'resolved',
                                                'bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-gray-300': message.status === 'closed'
                                            }" x-text="message.status_label"></span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-gray-300" x-text="message.category_label"></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>

                    <!-- Footer -->
                    <div class="px-4 py-2 border-t border-gray-100 bg-gray-50 dark:border-slate-700 dark:bg-slate-800/50">
                        <a href="{{ route('hr.contacts.admin') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center gap-1 dark:text-indigo-400 dark:hover:text-indigo-300">
                            View All Messages
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @else
            <!-- Empty placeholder for non-HR/Admin users -->
            <div class="p-1.5 sm:p-2 text-gray-300 dark:text-slate-600">
                <i class="fas fa-inbox text-lg sm:text-xl"></i>
            </div>
            @endif
            
            <!-- Quick Action Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        :class="{'bg-gray-100 dark:bg-slate-700': open}"
                        title="Quick Actions"
                        class="relative p-1.5 sm:p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-slate-700 flex items-center gap-1">
                    <i class="fas fa-bolt text-lg sm:text-xl text-amber-500"></i>
                    <i class="fas fa-chevron-down text-xs text-gray-400 dark:text-gray-500" :class="{ 'rotate-180': open }"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open"
                     x-cloak
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50 dark:bg-slate-900 dark:border-slate-700">
                    
                    <div class="px-4 py-2 border-b border-gray-100 dark:border-slate-700">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide dark:text-gray-400">Quick Actions</p>
                    </div>

                    <div class="py-1">
                        {{-- 1. Time In --}}
                        @if($user->employee && !$isCurrentlyTimedIn)
                            <button onclick="sidebarConfirmTimeIn()" class="w-full text-left flex items-center px-4 py-2.5 text-sm text-green-700 hover:bg-green-50 transition-colors dark:text-green-400 dark:hover:bg-slate-800">
                                <i class="fas fa-sign-in-alt w-5 text-green-600 mr-2"></i>
                                Time In
                            </button>
                        @elseif($user->employee && $isCurrentlyTimedIn)
                            <div class="px-4 py-2 text-xs font-medium text-green-600 bg-green-50 dark:bg-green-950/30 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                                You're Clocked In
                            </div>
                        @endif

                        {{-- 2. Time Out --}}
                        @if($user->employee && $isCurrentlyTimedIn)
                            <button onclick="sidebarConfirmTimeOut()" class="w-full text-left flex items-center px-4 py-2.5 text-sm text-red-700 hover:bg-red-50 transition-colors dark:text-red-400 dark:hover:bg-slate-800">
                                <i class="fas fa-sign-out-alt w-5 text-red-600 mr-2"></i>
                                Time Out
                            </button>
                        @endif

                        {{-- 3. Add Employees (HR & Admin only) --}}
                        @if(in_array($user->role, ['admin', 'hr']))
                            <a href="{{ route('employees.create') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800">
                                <i class="fas fa-user-plus w-5 text-blue-600 mr-2"></i>
                                Add Employee
                            </a>
                        @endif

                        {{-- 4. Apply Leave --}}
                        @if($user->employee)
                            <a href="{{ route('attendance.leave-management', ['scope' => 'mine']) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800">
                                <i class="fas fa-calendar-times w-5 text-purple-600 mr-2"></i>
                                Apply Leave
                            </a>
                        @endif

                        {{-- 5. Download Payslip --}}
                        @php
                            $hdrLatestPayroll = null;
                            try {
                                if ($user->employee) {
                                    $hdrLatestPayroll = \App\Models\Payroll::where('employee_id', $user->employee->id)
                                        ->whereIn('status', ['approved', 'paid'])
                                        ->latest()
                                        ->first();
                                }
                            } catch (\Exception $e) {
                                $hdrLatestPayroll = null;
                            }
                        @endphp
                        @if($hdrLatestPayroll)
                            <button onclick="downloadEmployeePayslip('{{ $hdrLatestPayroll->id }}')" class="w-full text-left flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800">
                                <i class="fas fa-download w-5 text-blue-600 mr-2"></i>
                                Download Payslip
                            </button>
                        @else
                            <a href="{{ route('employee.payroll.history') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800">
                                <i class="fas fa-receipt w-5 text-blue-600 mr-2"></i>
                                Payslips
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- User Menu Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-1 sm:space-x-2 p-1.5 sm:p-2 rounded-lg hover:bg-gray-100 transition-colors dark:hover:bg-slate-700">
                    @php
                        $userProfilePhoto = null;
                        if ($user->employee && $user->employee->profile_photo) {
                            $userProfilePhoto = asset('storage/' . $user->employee->profile_photo);
                        } elseif ($user->employee && $user->employee->otherInfo && $user->employee->otherInfo->photo_path) {
                            $userProfilePhoto = asset('storage/' . $user->employee->otherInfo->photo_path);
                        }
                    @endphp
                    @if($userProfilePhoto)
                        <img src="{{ $userProfilePhoto }}" alt="{{ $user->full_name }}" class="w-6 h-6 sm:w-8 sm:h-8 rounded-full object-cover border border-gray-200 shadow-sm">
                    @else
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                            <span class="text-white text-xs sm:text-sm font-semibold">{{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <span class="hidden lg:block text-sm font-medium text-gray-700 dark:text-gray-200">{{ $user->full_name }}</span>
                    <i class="fas fa-chevron-down text-gray-400 text-xs hidden sm:block dark:text-gray-500" :class="{ 'rotate-180': open }"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open"
                     x-cloak
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50 dark:bg-slate-900 dark:border-slate-700">

                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-slate-700">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->full_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    </div>

                    <!-- Profile -->
                    <a href="{{ route('hr.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800">
                        <i class="fas fa-user-circle w-4 h-4 mr-3 text-gray-400 dark:text-gray-500"></i>
                        Profile
                    </a>

                    <!-- My Information -->
                    <div x-data="{ infoOpen: {{ request()->routeIs('hr.my-information.*') ? 'true' : 'false' }} }">
                        <button type="button" @click="infoOpen = !infoOpen" class="flex items-center justify-between w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800">
                            <span class="flex items-center">
                                <i class="fas fa-id-badge w-4 h-4 mr-3 text-gray-400 dark:text-gray-500"></i>
                                My Information
                            </span>
                            <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': infoOpen }"></i>
                        </button>
                        <div x-show="infoOpen" x-transition class="ml-4 border-l border-gray-200 dark:border-slate-700">
                            <a href="{{ route('hr.my-information.info') }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800 {{ request()->routeIs('hr.my-information.info') ? 'bg-blue-50 text-blue-600 dark:bg-slate-800 dark:text-blue-400' : '' }}">Personal Information</a>
                            <a href="{{ route('hr.my-information.other-info') }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800 {{ request()->routeIs('hr.my-information.other-info') ? 'bg-blue-50 text-blue-600 dark:bg-slate-800 dark:text-blue-400' : '' }}">Other Info</a>
                            <a href="{{ route('hr.my-information.education-training-rating') }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800 {{ request()->routeIs('hr.my-information.education-training-rating') ? 'bg-blue-50 text-blue-600 dark:bg-slate-800 dark:text-blue-400' : '' }}">Educational / Training / Rating</a>
                            <a href="{{ route('hr.my-information.prev-emp-oth') }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800 {{ request()->routeIs('hr.my-information.prev-emp-oth') ? 'bg-blue-50 text-blue-600 dark:bg-slate-800 dark:text-blue-400' : '' }}">Previous Employer &amp; Other</a>
                            <a href="{{ route('hr.my-information.documents') }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800 {{ request()->routeIs('hr.my-information.documents') ? 'bg-blue-50 text-blue-600 dark:bg-slate-800 dark:text-blue-400' : '' }}">Documents</a>
                            <a href="{{ route('hr.my-information.ytd-info') }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800 {{ request()->routeIs('hr.my-information.ytd-info') ? 'bg-blue-50 text-blue-600 dark:bg-slate-800 dark:text-blue-400' : '' }}">YTD - INFO</a>
                            <a href="{{ route('hr.my-information.bio-zk') }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800 {{ request()->routeIs('hr.my-information.bio-zk') ? 'bg-blue-50 text-blue-600 dark:bg-slate-800 dark:text-blue-400' : '' }}">Bio ZK</a>
                        </div>
                    </div>

                    <!-- Settings -->
                    <a href="{{ route('hr.settings') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors dark:text-gray-200 dark:hover:bg-slate-800">
                        <i class="fas fa-cog w-4 h-4 mr-3 text-gray-400 dark:text-gray-500"></i>
                        {{ match ($user->role) {
                            'admin' => 'Admin Settings',
                            'hr' => 'HR Settings',
                            'employee' => 'Employee Settings',
                            default => 'Account Settings',
                        } }}
                    </a>

                    <!-- Divider -->
                    <div class="border-t border-gray-100 my-1 dark:border-slate-700"></div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" id="logout-form" class="block">
                        @csrf
                        <button type="submit" onclick="handleLogout(event, {{ $isCurrentlyTimedIn ? 'true' : 'false' }})" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors dark:text-red-400 dark:hover:bg-red-900/20">
                            <i class="fas fa-sign-out-alt w-4 h-4 mr-3"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
[x-cloak] {
    display: none !important;
}
</style>

<script>
// Real-time clock functionality
document.addEventListener('DOMContentLoaded', function() {
    function updateTime() {
        const timeElementDesktop = document.getElementById('current-time-desktop');
        const timeElementMobile = document.getElementById('current-time-mobile');
        
        if (timeElementDesktop || timeElementMobile) {
            // Get current time in Philippines timezone
            const now = new Date();
            const philippinesTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Manila"}));
            
            // Format for desktop (full date and time)
            const desktopFormat = philippinesTime.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            
            // Format for mobile (time only)
            const mobileFormat = philippinesTime.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            
            // Update elements
            if (timeElementDesktop) {
                timeElementDesktop.textContent = desktopFormat;
            }
            if (timeElementMobile) {
                timeElementMobile.textContent = mobileFormat;
            }
        }
    }
    
    // Update time immediately
    updateTime();
    
    // Update time every second
    setInterval(updateTime, 1000);

    // Company switch handler
    window.handleCompanySwitch = function(event, companyName) {
        // Show loading state
        const submitButton = event.target.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Switching...';
        }
        
        // The form will submit normally and page will reload
        return true;
    };

    // Logout handler - check if employee is timed in
    window.handleLogout = function(event, isTimedIn) {
        if (isTimedIn) {
            event.preventDefault();
            event.stopPropagation();
            
            // Show warning modal
            showLogoutWarningModal();
            return false;
        }
        // If not timed in, allow normal logout
        return true;
    };

    // Show logout warning modal
    function showLogoutWarningModal() {
        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.id = 'logout-warning-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-[9999] flex items-center justify-center p-4 animate-fadeIn';
        overlay.style.animation = 'fadeIn 0.2s ease-out';
        overlay.onclick = function(e) {
            if (e.target === overlay) {
                closeLogoutWarningModal();
            }
        };

        // Create modal content
        const modal = document.createElement('div');
        modal.className = 'bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all';
        modal.style.animation = 'slideUp 0.3s ease-out';
        modal.innerHTML = `
            <div class="p-6 sm:p-8">
                <!-- Icon -->
                <div class="flex items-center justify-center w-20 h-20 mx-auto mb-5 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-full shadow-lg">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl"></i>
                </div>
                
                <!-- Title -->
                <h3 class="text-2xl font-bold text-gray-900 text-center mb-3">Clock Out Required</h3>
                
                <!-- Message -->
                <p class="text-gray-600 text-center mb-8 leading-relaxed text-sm sm:text-base">
                        You are currently clocked in. Choose whether to clock out first, remain clocked in, or cancel logout.
                </p>
                
                <!-- Buttons -->
                <div class="space-y-3">
                    <button onclick="clockOutAndLogout()" class="w-full flex items-center justify-center px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Clock Out & Logout
                    </button>
                    <button onclick="closeLogoutWarningModal()" class="w-full px-5 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium border border-gray-200">
                        Cancel
                    </button>
                    <button onclick="logoutAnyway()" class="w-full flex items-center justify-center px-5 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout & Keep Clocked In
                    </button>
                </div>
            </div>
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        // Add CSS animations if not already added
        if (!document.getElementById('logout-modal-styles')) {
            const style = document.createElement('style');
            style.id = 'logout-modal-styles';
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes slideUp {
                    from { 
                        opacity: 0;
                        transform: translateY(20px) scale(0.95);
                    }
                    to { 
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    // Close logout warning modal - make it global
    window.closeLogoutWarningModal = function() {
        const overlay = document.getElementById('logout-warning-overlay');
        if (overlay) {
            overlay.remove();
        }
    };

    // Clock out and then logout - make it global
    window.clockOutAndLogout = async function() {
        const overlay = document.getElementById('logout-warning-overlay');
        if (overlay) {
            const modal = overlay.querySelector('div');
            if (modal) {
                modal.innerHTML = `
                    <div class="p-8">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Clocking Out...</h3>
                            <p class="text-gray-600 text-center text-sm">Please wait while we process your clock out.</p>
                        </div>
                    </div>
                `;
            }
        }

        try {
            // Clock out first
            const response = await fetch('{{ route("attendance.time-out") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok) {
                // Successfully clocked out, now logout
                setTimeout(() => {
                    document.getElementById('logout-form').submit();
                }, 500);
            } else {
                // Clock out failed, show error but allow logout
                alert('Failed to clock out: ' + (data.error || 'Unknown error') + '\n\nYou can still logout, but please contact HR to update your attendance record.');
                document.getElementById('logout-form').submit();
            }
        } catch (error) {
            console.error('Error clocking out:', error);
            alert('Error clocking out. You can still logout, but please contact HR to update your attendance record.');
            document.getElementById('logout-form').submit();
        }
    }

    // Logout anyway (without clocking out) - make it global
    window.logoutAnyway = function() {
        window.closeLogoutWarningModal();
        document.getElementById('logout-form').submit();
    };
});
</script>
