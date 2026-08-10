@extends('layouts.dashboard-base')

@section('title', 'Reports')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Generate and export attendance, leave, Official Business, and payroll reports.</p>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <form action="{{ route('reports.generate') }}" method="GET" id="reportForm" x-data="multiSelectDropdown()">
                
                <!-- Error Toast Notification -->
                <div x-show="showError" 
                     style="display: none;"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="fixed bottom-6 right-6 z-50 bg-red-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <span class="font-medium text-sm" x-text="errorMessage"></span>
                    <button type="button" @click="showError = false" class="text-white hover:text-red-200 ml-4 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Multi-Select Dropdown Component -->
                <div class="mb-6 relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Modules / Report Types <span class="text-red-500">*</span>
                    </label>

                    <!-- Dropdown Trigger Button -->
                    <div class="relative" @click.away="open = false">
                        <button type="button" 
                                @click="open = !open" 
                                class="w-full flex items-center justify-between min-h-[50px] px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-left">
                            
                            <div class="flex flex-wrap items-center gap-2 py-1">
                                <template x-if="selected.length === 0">
                                    <span class="text-gray-400 text-sm">Click to select modules...</span>
                                </template>
                                <template x-for="val in selected" :key="val">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold shadow-sm border transition"
                                          :class="badgeClass(val)">
                                        <i :class="iconClass(val)" class="text-xs"></i>
                                        <span x-text="labelName(val)"></span>
                                        <span @click.stop="remove(val)" class="ml-1 cursor-pointer opacity-60 hover:opacity-100 hover:text-red-600 transition-colors p-0.5">
                                            <i class="fas fa-times text-[10px]"></i>
                                        </span>
                                    </span>
                                </template>
                            </div>

                            <div class="flex items-center space-x-3 ml-2 flex-shrink-0">
                                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full border border-gray-200" x-text="selected.length + ' / 5 Selected'"></span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'transform rotate-180': open }"></i>
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             @click.stop
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
                             class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 overflow-hidden divide-y divide-gray-100"
                             style="display: none;">
                            
                            <!-- Quick Actions Header -->
                            <div class="px-4 py-3 bg-gray-50 flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Available Modules</span>
                                <div class="flex items-center space-x-3">
                                    <button type="button" @click.stop="selectAll()" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                                        <i class="fas fa-check-double mr-1"></i> Select All
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <button type="button" @click.stop="clearAll()" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition">
                                        <i class="fas fa-times mr-1"></i> Clear All
                                    </button>
                                </div>
                            </div>

                            <!-- Options List -->
                            <div class="p-2 space-y-1.5 max-h-80 overflow-y-auto">
                                <template x-for="item in options" :key="item.value">
                                    <div @click.stop="toggle(item.value)" 
                                         class="flex items-center px-4 py-3 rounded-lg cursor-pointer transition-all duration-150 select-none group"
                                         :class="isSelected(item.value) ? item.activeRowClass : 'border-l-4 border-transparent hover:bg-gray-50'">
                                        
                                        <input type="checkbox" 
                                               name="report_types[]" 
                                               :value="item.value" 
                                               :checked="isSelected(item.value)" 
                                               style="display: none !important;">

                                        <!-- Custom Checkbox -->
                                        <div class="w-5 h-5 rounded-md border-2 flex items-center justify-center mr-4 flex-shrink-0 transition-all duration-150 shadow-xs"
                                             :class="isSelected(item.value) ? item.activeCheckClass : 'bg-white border-gray-300 group-hover:border-gray-400'">
                                            <i class="fas fa-check text-[10px] font-extrabold text-white" x-show="isSelected(item.value)"></i>
                                        </div>

                                        <!-- Module Icon -->
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-4 flex-shrink-0 shadow-xs transition-transform duration-150 group-hover:scale-105" :class="item.bgColor">
                                            <i :class="[item.icon, item.iconColor]" class="text-base"></i>
                                        </div>

                                        <!-- Text Details -->
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-gray-900 leading-snug" x-text="item.label"></div>
                                            <div class="text-xs text-gray-500 truncate mt-0.5" x-text="item.desc"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <div class="flex items-center space-x-2">
                            <input type="date" name="start_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-gray-500">to</span>
                            <input type="date" name="end_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department (Optional)</label>
                        <select name="department_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee (Optional)</label>
                        <select name="employee_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" @click="validateForm($event)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-layer-group mr-2"></i> Generate Selected Reports
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function multiSelectDropdown() {
    return {
        open: false,
        showError: false,
        errorMessage: '',
        selected: ['attendance'],
        options: [
            { 
                value: 'attendance', 
                label: 'Attendance', 
                desc: 'Time-in, time-out, and hours worked.', 
                icon: 'fas fa-clock', 
                iconColor: 'text-green-600', 
                bgColor: 'bg-green-100',
                activeCheckClass: 'bg-green-600 border-green-600',
                activeRowClass: 'bg-green-50/70 border-l-4 border-green-500'
            },
            { 
                value: 'leave', 
                label: 'Leave', 
                desc: 'Leave requests and usage history.', 
                icon: 'fas fa-calendar-times', 
                iconColor: 'text-purple-600', 
                bgColor: 'bg-purple-100',
                activeCheckClass: 'bg-purple-600 border-purple-600',
                activeRowClass: 'bg-purple-50/70 border-l-4 border-purple-500'
            },
            { 
                value: 'overtime', 
                label: 'Overtime', 
                desc: 'OT requests, hours, and approval statuses.', 
                icon: 'fas fa-user-clock', 
                iconColor: 'text-blue-600', 
                bgColor: 'bg-blue-100',
                activeCheckClass: 'bg-blue-600 border-blue-600',
                activeRowClass: 'bg-blue-50/70 border-l-4 border-blue-500'
            },
            { 
                value: 'official_business', 
                label: 'Official Business', 
                desc: 'OB schedules, credited hours, and reasons.', 
                icon: 'fas fa-briefcase', 
                iconColor: 'text-violet-600', 
                bgColor: 'bg-violet-100',
                activeCheckClass: 'bg-violet-600 border-violet-600',
                activeRowClass: 'bg-violet-50/70 border-l-4 border-violet-500'
            },
            { 
                value: 'payroll', 
                label: 'Payroll', 
                desc: 'Gross pay, deductions, and net pay.', 
                icon: 'fas fa-money-bill-wave', 
                iconColor: 'text-emerald-600', 
                bgColor: 'bg-emerald-100',
                activeCheckClass: 'bg-emerald-600 border-emerald-600',
                activeRowClass: 'bg-emerald-50/70 border-l-4 border-emerald-500'
            }
        ],
        isSelected(val) {
            return this.selected.includes(val);
        },
        toggle(val) {
            if (this.isSelected(val)) {
                this.selected = this.selected.filter(i => i !== val);
            } else {
                this.selected.push(val);
            }
        },
        remove(val) {
            this.selected = this.selected.filter(i => i !== val);
        },
        selectAll() {
            this.selected = this.options.map(o => o.value);
        },
        clearAll() {
            this.selected = [];
        },
        labelName(val) {
            const opt = this.options.find(o => o.value === val);
            return opt ? opt.label : val;
        },
        iconClass(val) {
            const opt = this.options.find(o => o.value === val);
            return opt ? opt.icon : 'fas fa-file-alt';
        },
        badgeClass(val) {
            const map = {
                'attendance': 'bg-green-50 text-green-700 border-green-200',
                'leave': 'bg-purple-50 text-purple-700 border-purple-200',
                'overtime': 'bg-blue-50 text-blue-700 border-blue-200',
                'official_business': 'bg-violet-50 text-violet-700 border-violet-200',
                'payroll': 'bg-emerald-50 text-emerald-700 border-emerald-200'
            };
            return map[val] || 'bg-gray-50 text-gray-700 border-gray-200';
        },
        validateForm(e) {
            // Validate Module Selection
            if (this.selected.length === 0) {
                e.preventDefault();
                this.errorMessage = 'Please select at least one report type before generating.';
                this.showError = true;
                setTimeout(() => { this.showError = false; }, 3500);
                return;
            }

            // Validate Date Range
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;

            if (!startDate || !endDate) {
                e.preventDefault();
                this.errorMessage = 'Please provide a complete date range (start and end dates) before generating.';
                this.showError = true;
                setTimeout(() => { this.showError = false; }, 3500);
                return;
            }
        }
    };
}
</script>
@endsection
