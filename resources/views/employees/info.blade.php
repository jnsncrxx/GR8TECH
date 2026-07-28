@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employees.info'])

@section('title', 'Employee Info')

@section('content')
<div class="max-w-7xl mx-auto space-y-4" x-data="{ 
        allowChanges: false,
        activeTab: 'position_dept',
        showBasicPayModal: false,
        basicPay: '{{ data_get($selectedEmployee, 'info.basic_pay', 0.00) }}',
        newBasicPay: 0.00,
        
        searchQuery: '',
        searchResults: [],
        isSearching: false,
        showResults: false,
        
        async searchEmployees() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                this.showResults = false;
                return;
            }
            this.isSearching = true;
            try {
                const response = await fetch('{{ route('employees.info.search') }}?query=' + encodeURIComponent(this.searchQuery));
                this.searchResults = await response.json();
                this.showResults = true;
            } catch (error) {
                console.error('Search failed:', error);
            }
            this.isSearching = false;
        },
        
        selectEmployee(id) {
            window.location.href = '{{ route('employees.info') }}?employee_id=' + id;
        }
    }">
    
    <!-- Search Bar Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-4">
        <div class="relative w-full max-w-md">
            <div class="relative">
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    @input.debounce.300ms="searchEmployees" 
                    @focus="searchQuery.length >= 2 ? showResults = true : null" 
                    @click.outside="showResults = false"
                    placeholder="Search employee or employee no." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                >
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Search Results Dropdown -->
            <div x-show="showResults" x-transition style="display: none;" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                <template x-if="isSearching">
                    <div class="p-3 text-sm text-gray-500 text-center">Searching...</div>
                </template>
                <template x-if="!isSearching && searchResults.length === 0">
                    <div class="p-3 text-sm text-gray-500 text-center">No employees found.</div>
                </template>
                <template x-for="emp in searchResults" :key="emp.id">
                    <div @click="selectEmployee(emp.id)" class="p-3 border-b border-gray-100 hover:bg-blue-50 cursor-pointer flex flex-col">
                        <span class="font-medium text-sm text-gray-900" x-text="emp.last_name + ', ' + emp.first_name"></span>
                        <span class="text-xs text-gray-500" x-text="'Emp No: ' + emp.employee_id"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded-md border border-green-200 text-sm">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-3 rounded-md border border-red-200 text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($selectedEmployee)
    <form method="POST" action="{{ route('employees.info.save') }}">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $selectedEmployee->id }}">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
            
            <!-- Identification Block -->
            <div class="emp-card sec-personal lg:order-1">
                    <div class="emp-card-header">
                        <div class="section-icon"><i class="fas fa-id-card"></i></div>
                        <div>
                            <h3>Identification</h3>
                            <p>Basic employee identification</p>
                        </div>
                    </div>
                    <div class="emp-card-body">
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="form-label">Employee No.</label>
                                <input type="text" value="{{ $selectedEmployee->employee_id }}" class="form-control bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="form-label">ID Card No.</label>
                                <input type="text" name="id_card_no" value="{{ data_get($selectedEmployee, 'info.id_card_no') }}" class="form-control" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                            </div>
                        </div>
                    
                    <div class="mb-3">
                        <label class="block text-xs text-gray-600 mb-1">Control No.</label>
                        <input type="text" name="control_no" value="{{ data_get($selectedEmployee, 'info.control_no') }}" class="w-full text-sm border border-gray-300 rounded px-2 py-1.5 bg-yellow-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" :readonly="!allowChanges">
                    </div>
                    
                    <div class="space-y-2 mb-3">
                        <div class="flex gap-2 items-center">
                            <label class="w-24 text-xs text-gray-600">Last Name</label>
                            <input type="text" name="last_name" value="{{ $selectedEmployee->last_name }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100" readonly>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label class="w-24 text-xs text-gray-600">First Name</label>
                            <input type="text" name="first_name" value="{{ $selectedEmployee->first_name }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100" readonly>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label class="w-24 text-xs text-gray-600">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ $selectedEmployee->middle_name }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100" readonly>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div class="flex gap-2 items-center">
                            <label class="w-16 text-xs text-gray-600">Sex</label>
                            <select class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100" disabled>
                                <option>{{ $selectedEmployee->sex }}</option>
                            </select>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label class="w-20 text-xs text-gray-600">Civil Status</label>
                            <select class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100" disabled>
                                <option>{{ $selectedEmployee->civil_status }}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 items-center mb-3">
                        <label class="w-24 text-xs text-gray-600">Birthday</label>
                        <input type="text" value="{{ $selectedEmployee->date_of_birth ? $selectedEmployee->date_of_birth->format('m/d/Y') : '' }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100" readonly>
                        
                        <label class="ml-2 text-xs text-gray-600">Age</label>
                        <input type="text" value="{{ $selectedEmployee->date_of_birth ? $selectedEmployee->date_of_birth->age : '' }}" class="w-12 text-center text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100" readonly>
                    </div>

                    <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded text-center">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Active Status</label>
                        <select name="active_status" class="w-32 mx-auto text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" :disabled="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                            <option value="Active" {{ data_get($selectedEmployee, 'info.active_status') === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ data_get($selectedEmployee, 'info.active_status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                </div>

                <!-- Banking & IDs Block -->
                <div class="emp-card sec-account lg:order-3">
                    <div class="emp-card-header">
                        <div class="section-icon"><i class="fas fa-university"></i></div>
                        <div>
                            <h3>Banking & IDs</h3>
                            <p>Bank and statutory identification numbers</p>
                        </div>
                    </div>
                    <div class="emp-card-body space-y-3">
                    @php
                        $bankingFields = [
                            ['label' => 'Payment', 'name' => 'payment_method', 'type' => 'select', 'options' => ['Bank', 'Cash', 'Cheque']],
                            ['label' => 'Account No.', 'name' => 'account_no', 'type' => 'text'],
                            ['label' => 'Bank', 'name' => 'bank', 'type' => 'text'],
                            ['label' => 'Taxcode', 'name' => 'taxcode', 'type' => 'text'],
                            ['label' => 'TIN No.', 'name' => 'tin_no', 'type' => 'text'],
                            ['label' => 'SSS No.', 'name' => 'sss_no', 'type' => 'text'],
                            ['label' => 'HDMF', 'name' => 'hdmf_no', 'type' => 'text'],
                            ['label' => 'Philhealth', 'name' => 'philhealth_no', 'type' => 'text'],
                            ['label' => 'HMO No.', 'name' => 'hmo_no', 'type' => 'text'],
                            ['label' => 'Email Pers', 'name' => 'email_personal', 'type' => 'text'],
                            ['label' => 'Email Comp', 'name' => 'email_company', 'type' => 'text'],
                        ];
                    @endphp

                    @foreach($bankingFields as $field)
                    <div class="flex gap-2 items-center">
                        <label class="w-24 text-xs text-gray-600">{{ $field['label'] }}</label>
                        @if($field['type'] === 'select')
                            <select name="{{ $field['name'] }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" :disabled="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                <option value="">Select...</option>
                                @foreach($field['options'] as $opt)
                                    <option value="{{ $opt }}" {{ data_get($selectedEmployee, 'info.'.$field['name']) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ data_get($selectedEmployee, 'info.'.$field['name']) }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                        @endif
                    </div>
                    @endforeach
                </div>
                </div>
                
                <!-- Employment Block -->
                <div class="emp-card sec-work lg:order-2">
                    <div class="emp-card-header">
                        <div class="section-icon"><i class="fas fa-briefcase"></i></div>
                        <div>
                            <h3>Employment Details</h3>
                            <p>Status, dates, and payroll information</p>
                        </div>
                    </div>
                    <div class="emp-card-body">
                    <div class="flex gap-2 items-center mb-2">
                        <label class="w-28 text-xs text-gray-600">Employee Status</label>
                        <select name="employee_status" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500" :disabled="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                            <option value="{{ $selectedEmployee->employee_status }}">{{ $selectedEmployee->employee_status }}</option>
                            <option value="PROBATIONARY">PROBATIONARY</option>
                            <option value="REGULAR">REGULAR</option>
                            <option value="CONTRACTUAL">CONTRACTUAL</option>
                            <option value="RESIGNED">RESIGNED</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div class="flex gap-2 items-center">
                            <label class="w-24 text-xs text-gray-600">Date Employed</label>
                            <input type="text" value="{{ $selectedEmployee->hire_date ? \Carbon\Carbon::parse($selectedEmployee->hire_date)->format('m/d/Y') : '' }}" class="w-24 text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100" readonly>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label class="w-20 text-xs text-gray-600">Resigned</label>
                            <input type="date" name="resigned_date" value="{{ data_get($selectedEmployee, 'info.resigned_date') ? data_get($selectedEmployee, 'info.resigned_date')->format('Y-m-d') : '' }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-3 border-b border-gray-200 pb-3">
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2 items-center">
                                <label class="w-24 text-xs text-gray-600">Regular Date</label>
                                <input type="date" name="regular_date" value="{{ data_get($selectedEmployee, 'info.regular_date') ? data_get($selectedEmployee, 'info.regular_date')->format('Y-m-d') : '' }}" class="w-28 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2 items-center">
                                <label class="w-20 text-xs text-gray-600">Contract End</label>
                                <input type="text" value="{{ $selectedEmployee->contract_end_date ? \Carbon\Carbon::parse($selectedEmployee->contract_end_date)->format('m/d/Y') : '' }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100" readonly>
                            </div>
                            <div class="flex gap-2 items-center">
                                <label class="w-20 text-xs text-gray-600">Resign Process</label>
                                <input type="text" name="resign_process" value="{{ data_get($selectedEmployee, 'info.resign_process') }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 mb-3">
                        <div class="flex gap-2 items-center">
                            <label class="w-28 text-xs text-gray-600">Paycode</label>
                            <select name="paycode" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500" :disabled="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                <option value="">Select...</option>
                                <option value="MONTHLY SS RD" {{ data_get($selectedEmployee, 'info.paycode') === 'MONTHLY SS RD' ? 'selected' : '' }}>MONTHLY SS RD</option>
                            </select>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label class="w-28 text-xs text-gray-600">Period Type</label>
                            <select name="period_type" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500" :disabled="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                <option value="">Select...</option>
                                <option value="ANON-CONFI" {{ data_get($selectedEmployee, 'info.period_type') === 'ANON-CONFI' ? 'selected' : '' }}>ANON-CONFI</option>
                            </select>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label class="w-28 text-xs text-gray-600">Paylevel</label>
                            <select name="paylevel" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500" :disabled="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                <option value="">Select...</option>
                                <option value="Normal" {{ data_get($selectedEmployee, 'info.paylevel') === 'Normal' ? 'selected' : '' }}>Normal</option>
                            </select>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label class="w-28 text-xs text-gray-600">JobGrade</label>
                            <select name="job_grade" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500" :disabled="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                <option value="">Select...</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div class="flex gap-2 items-center relative">
                            <label class="w-20 text-xs text-gray-600">Basic Pay</label>
                            <input type="text" name="basic_pay" x-model="basicPay" @click="if(allowChanges) showBasicPayModal = true" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500 cursor-pointer bg-blue-50 hover:bg-blue-100" readonly>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label class="w-10 text-xs text-gray-600">Cola</label>
                            <input type="text" name="cola" value="{{ data_get($selectedEmployee, 'info.cola') }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                        </div>
                    </div>
                    
                    <div class="flex gap-4 items-center">
                        <label class="flex items-center gap-2 cursor-pointer bg-gray-50 border border-gray-200 px-3 py-1.5 rounded">
                            <span class="text-xs text-gray-700">Resign on Next Payroll</span>
                            <input type="checkbox" name="resign_on_next_payroll" value="1" {{ data_get($selectedEmployee, 'info.resign_on_next_payroll') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600" :disabled="!allowChanges">
                        </label>
                        <div class="flex gap-2 items-center flex-1">
                            <label class="w-20 text-xs text-gray-600 text-right">Basic Pay2</label>
                            <input type="text" name="basic_pay_2" value="{{ data_get($selectedEmployee, 'info.basic_pay_2') }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:border-blue-500" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                        </div>
                    </div>
                </div>
                </div>

                <!-- Tabs Block -->
                <div class="emp-card sec-details lg:order-4">
                    <div class="emp-card-header">
                        <div class="section-icon"><i class="fas fa-layer-group"></i></div>
                        <div>
                            <h3>Additional Information</h3>
                            <p>Position, groups, allowances and limits</p>
                        </div>
                    </div>
                    <div class="emp-card-body">
                    <!-- Tab Headers -->
                    <div class="flex gap-2 border-b border-gray-200 mb-4 overflow-x-auto text-xs pb-1">
                        <button type="button" @click="activeTab = 'position_dept'" class="px-2 py-1 font-medium transition-colors whitespace-nowrap" :class="activeTab === 'position_dept' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'">Position.Dept</button>
                        <button type="button" @click="activeTab = 'mfg_groups'" class="px-2 py-1 font-medium transition-colors whitespace-nowrap" :class="activeTab === 'mfg_groups' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'">MFG. Groups</button>
                        <button type="button" @click="activeTab = 'projects'" class="px-2 py-1 font-medium transition-colors whitespace-nowrap" :class="activeTab === 'projects' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'">Projects</button>
                        <button type="button" @click="activeTab = 'allowances'" class="px-2 py-1 font-medium transition-colors whitespace-nowrap" :class="activeTab === 'allowances' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'">Allowances</button>
                        <button type="button" @click="activeTab = 'history'" class="px-2 py-1 font-medium transition-colors whitespace-nowrap" :class="activeTab === 'history' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'">Position & Pay History</button>
                        <button type="button" @click="activeTab = 'others'" class="px-2 py-1 font-medium transition-colors whitespace-nowrap" :class="activeTab === 'others' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'">Others</button>
                    </div>

                    <!-- Tab Contents -->
                    
                    <!-- Position.Dept -->
                    <div x-show="activeTab === 'position_dept'" class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="flex gap-2 items-center">
                                <label class="w-24 text-xs text-gray-600">Position</label>
                                <input type="text" value="{{ $selectedEmployee->position->name ?? '' }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100" readonly>
                            </div>
                            <div class="flex gap-2 items-center">
                                <label class="w-24 text-xs text-gray-600">Department</label>
                                <input type="text" value="{{ $selectedEmployee->department->name ?? '' }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100" readonly>
                            </div>
                            <div class="flex gap-2 items-center">
                                <label class="w-24 text-xs text-gray-600">Branch</label>
                                <input type="text" name="branch" value="{{ data_get($selectedEmployee, 'info.branch') }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                            </div>
                            <div class="flex gap-2 items-center">
                                <label class="w-24 text-xs text-gray-600">Shuttle Location</label>
                                <input type="text" name="shuttle_location" value="{{ data_get($selectedEmployee, 'info.shuttle_location') }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                            </div>
                            <div class="flex gap-2 items-center">
                                <label class="w-24 text-xs text-gray-600">Cost Center</label>
                                <select name="cost_center" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :disabled="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="flex gap-2 items-center">
                                <label class="w-24 text-xs text-gray-600">Sub Cost Center</label>
                                <select name="sub_cost_center" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :disabled="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="flex gap-2 items-center">
                                <label class="w-24 text-xs text-gray-600">Schedule</label>
                                <select name="schedule" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :disabled="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                    <option value="8AM-5PM" {{ data_get($selectedEmployee, 'info.schedule') === '8AM-5PM' ? 'selected' : '' }}>8AM-5PM</option>
                                </select>
                            </div>
                            <div class="flex gap-2 items-center">
                                <label class="w-24 text-xs text-gray-600">Group Schedule</label>
                                <select name="group_schedule" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :disabled="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                    <option value=""></option>
                                </select>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer pt-2">
                                <input type="checkbox" name="allow_flexible_time" value="1" {{ data_get($selectedEmployee, 'info.allow_flexible_time') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600" :disabled="!allowChanges">
                                <span class="text-xs text-gray-700">Allow Flexible Time</span>
                            </label>
                        </div>
                        <div class="space-y-2 text-xs">
                            @php
                                $leaveFields = [
                                    ['Max Sick', 'max_sick', 'sick_days_total'],
                                    ['Max Vacation', 'max_vacation', 'vacation_days_total'],
                                    ['Max SL (SIL)', 'max_sl', 'sil_days_total'],
                                    ['Max SPL', 'max_spl', 'spl_days_total'],
                                    ['Max PL', 'max_pl', 'paternity_days_total'],
                                    ['Max VAWC', 'max_vawc', 'vawc_days_total'],
                                    ['Max ML', 'max_ml', 'maternity_days_total'],
                                    ['Max BL', 'max_bl', 'bl_days_total'],
                                    ['Max EL', 'max_el', 'emergency_days_total'],
                                ];
                            @endphp
                            @foreach($leaveFields as $m)
                            @php
                                $val = $employeeBalance ? $employeeBalance->{$m[2]} : null;
                                $displayVal = $val !== null ? $val : 'N/A';
                            @endphp
                            <div class="flex items-center">
                                <label class="w-32 text-gray-600 font-medium">{{ $m[0] }}</label>
                                <input type="text" name="{{ $m[1] }}" value="{{ $displayVal }}" class="w-20 text-right px-2 py-1 border border-gray-300 rounded focus:border-blue-500" :readonly="!allowChanges" :class="{'bg-gray-100 text-gray-500': !allowChanges}">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- MFG Groups -->
                    <div x-show="activeTab === 'mfg_groups'" class="space-y-2" style="display: none;">
                        @foreach(['Cluster'=>'cluster', 'Section'=>'section', 'Sub Section'=>'sub_section', 'Group'=>'group_name', 'Line'=>'line_name', 'Position'=>'mfg_position'] as $lbl => $fld)
                        <div class="flex gap-2 items-center max-w-sm">
                            <label class="w-24 text-xs text-gray-600">{{ $lbl }}</label>
                            <input type="text" name="{{ $fld }}" value="{{ data_get($selectedEmployee, 'info.'.$fld) }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                        </div>
                        @endforeach
                    </div>

                    <!-- Projects -->
                    <div x-show="activeTab === 'projects'" class="space-y-2" style="display: none;">
                        @foreach(['Contracts'=>'contract_ref', 'Project'=>'project', 'Category'=>'category'] as $lbl => $fld)
                        <div class="flex gap-2 items-center max-w-sm">
                            <label class="w-24 text-xs text-gray-600">{{ $lbl }}</label>
                            <input type="text" name="{{ $fld }}" value="{{ data_get($selectedEmployee, 'info.'.$fld) }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                        </div>
                        @endforeach
                    </div>

                    <!-- Allowances -->
                    <div x-show="activeTab === 'allowances'" class="grid grid-cols-2 gap-6" style="display: none;">
                        <div class="space-y-2">
                            @for($i=1; $i<=5; $i++)
                            <div class="flex gap-2 items-center">
                                <label class="w-24 text-xs text-gray-600">Allowance {{ $i }}</label>
                                <input type="text" name="allowance_{{ $i }}" value="{{ data_get($selectedEmployee, 'info.allowance_'.$i) }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                            </div>
                            @endfor
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-center mb-2">Non - Taxable</div>
                            <div class="space-y-2">
                                <div class="flex gap-2 items-center">
                                    <label class="w-28 text-xs text-gray-600">Transpo</label>
                                    <input type="text" name="allow_transpo" value="{{ data_get($selectedEmployee, 'info.allow_transpo') }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                </div>
                                <div class="flex gap-2 items-center">
                                    <label class="w-28 text-xs text-gray-600">Housing</label>
                                    <input type="text" name="allow_housing" value="{{ data_get($selectedEmployee, 'info.allow_housing') }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                </div>
                                <div class="flex gap-2 items-center">
                                    <label class="w-28 text-xs text-gray-600">Communication</label>
                                    <input type="text" name="allow_communication" value="{{ data_get($selectedEmployee, 'info.allow_communication') }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                </div>
                                <div class="flex gap-2 items-center">
                                    <label class="w-28 text-xs text-gray-600">Allowance 4</label>
                                    <input type="text" name="allow_4_nt" value="{{ data_get($selectedEmployee, 'info.allow_4_nt') }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                </div>
                                <div class="flex gap-2 items-center">
                                    <label class="w-28 text-xs text-gray-600">Allowance 5</label>
                                    <input type="text" name="allow_5_nt" value="{{ data_get($selectedEmployee, 'info.allow_5_nt') }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History -->
                    <div x-show="activeTab === 'history'" class="space-y-6" style="display: none;">
                        <div>
                            <table class="w-full text-xs text-center border-collapse border border-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="border border-gray-300 p-1 font-semibold text-gray-700">POSITION</th>
                                        <th class="border border-gray-300 p-1 font-semibold text-gray-700">FROM</th>
                                        <th class="border border-gray-300 p-1 font-semibold text-gray-700">TO</th>
                                        <th class="border border-gray-300 p-1 font-semibold text-gray-700">REMARKS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i=0; $i<3; $i++)
                                    <tr>
                                        <td class="border border-gray-300 p-0"><input type="text" class="w-full border-0 px-2 py-1 text-xs focus:ring-0" :readonly="!allowChanges" :class="{'bg-gray-50': !allowChanges}"></td>
                                        <td class="border border-gray-300 p-0"><input type="text" class="w-full border-0 px-2 py-1 text-xs focus:ring-0" :readonly="!allowChanges" :class="{'bg-gray-50': !allowChanges}"></td>
                                        <td class="border border-gray-300 p-0"><input type="text" class="w-full border-0 px-2 py-1 text-xs focus:ring-0" :readonly="!allowChanges" :class="{'bg-gray-50': !allowChanges}"></td>
                                        <td class="border border-gray-300 p-0"><input type="text" class="w-full border-0 px-2 py-1 text-xs focus:ring-0" :readonly="!allowChanges" :class="{'bg-gray-50': !allowChanges}"></td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <table class="w-full text-xs text-center border-collapse border border-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="border border-gray-300 p-1 font-semibold text-gray-700">BASIC</th>
                                        <th class="border border-gray-300 p-1 font-semibold text-gray-700">FROM</th>
                                        <th class="border border-gray-300 p-1 font-semibold text-gray-700">TO</th>
                                        <th class="border border-gray-300 p-1 font-semibold text-gray-700">REMARKS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i=0; $i<3; $i++)
                                    <tr>
                                        <td class="border border-gray-300 p-0"><input type="text" class="w-full border-0 px-2 py-1 text-xs focus:ring-0" :readonly="!allowChanges" :class="{'bg-gray-50': !allowChanges}"></td>
                                        <td class="border border-gray-300 p-0"><input type="text" class="w-full border-0 px-2 py-1 text-xs focus:ring-0" :readonly="!allowChanges" :class="{'bg-gray-50': !allowChanges}"></td>
                                        <td class="border border-gray-300 p-0"><input type="text" class="w-full border-0 px-2 py-1 text-xs focus:ring-0" :readonly="!allowChanges" :class="{'bg-gray-50': !allowChanges}"></td>
                                        <td class="border border-gray-300 p-0"><input type="text" class="w-full border-0 px-2 py-1 text-xs focus:ring-0" :readonly="!allowChanges" :class="{'bg-gray-50': !allowChanges}"></td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Others -->
                    <div x-show="activeTab === 'others'" style="display: none;">
                        <table class="w-full text-xs border border-gray-200 mb-4 text-center">
                            <thead class="bg-amber-100">
                                <tr>
                                    <th colspan="4" class="p-1 border-b border-gray-200 font-semibold text-gray-700">Override Table</th>
                                </tr>
                                <tr>
                                    <th class="p-1 border-r border-gray-200 w-1/4"></th>
                                    <th class="p-1 border-r border-gray-200 w-1/6">Exclude</th>
                                    <th class="p-1 border-r border-gray-200">Employee</th>
                                    <th class="p-1">Employer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach([
                                    ['lbl'=>'SSS', 'pfx'=>'override_sss'],
                                    ['lbl'=>'Philhealth', 'pfx'=>'override_philhealth'],
                                    ['lbl'=>'Pag-ibig', 'pfx'=>'override_pagibig'],
                                    ['lbl'=>'Tax', 'pfx'=>'override_tax']
                                ] as $r)
                                <tr class="border-t border-gray-200">
                                    <td class="p-1.5 border-r border-gray-200 text-left font-medium">{{ $r['lbl'] }}</td>
                                    <td class="p-1.5 border-r border-gray-200">
                                        <input type="checkbox" name="{{ $r['pfx'] }}_exclude" value="1" {{ data_get($selectedEmployee, 'info.'.$r['pfx'].'_exclude') ? 'checked' : '' }} class="rounded border-gray-300" :disabled="!allowChanges">
                                    </td>
                                    <td class="p-1.5 border-r border-gray-200">
                                        @if($r['lbl'] !== 'Tax')
                                        <input type="text" name="{{ $r['pfx'] }}_employee" value="{{ data_get($selectedEmployee, 'info.'.$r['pfx'].'_employee') }}" class="w-full h-6 text-center text-xs border border-gray-300 rounded" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                        @endif
                                    </td>
                                    <td class="p-1.5">
                                        @if($r['lbl'] !== 'Tax')
                                        <input type="text" name="{{ $r['pfx'] }}_employer" value="{{ data_get($selectedEmployee, 'info.'.$r['pfx'].'_employer') }}" class="w-full h-6 text-center text-xs border border-gray-300 rounded" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="flex items-start justify-between text-xs bg-gray-50 p-3 border border-gray-200 rounded">
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="pagibig_voluntary" value="1" {{ data_get($selectedEmployee, 'info.pagibig_voluntary') ? 'checked' : '' }} class="rounded border-blue-600" :disabled="!allowChanges">
                                    <span class="font-medium">Pagibig Voluntary</span>
                                </label>
                                <div>
                                    <label class="block mb-1">P-ibig vol, amt</label>
                                    <input type="text" name="pagibig_vol_amount" value="{{ data_get($selectedEmployee, 'info.pagibig_vol_amount') }}" class="w-24 px-2 py-1 border border-gray-300 rounded text-right" :readonly="!allowChanges" :class="{'bg-gray-100': !allowChanges}">
                                </div>
                            </div>
                            
                            <div class="space-y-2 border-l border-gray-300 pl-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="tax_computation_type" value="annualized" {{ (data_get($selectedEmployee, 'info.tax_computation_type') ?? 'annualized') === 'annualized' ? 'checked' : '' }} :disabled="!allowChanges">
                                    <span>Annualized Tax Computation</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="tax_computation_type" value="tax_table" {{ data_get($selectedEmployee, 'info.tax_computation_type') === 'tax_table' ? 'checked' : '' }} :disabled="!allowChanges">
                                    <span>Tax Table Computation</span>
                                </label>
                            </div>
                            
                            <div class="border-l border-gray-300 pl-4 self-center">
                                <button type="button" class="px-3 py-1 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50" :disabled="!allowChanges" :class="{'opacity-50 cursor-not-allowed': !allowChanges}">Reset Base Per</button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        <!-- Allow Changes Footer -->
        <div class="mt-6 flex justify-center">
            <label class="inline-flex items-center gap-2 px-6 py-2 bg-blue-100 text-blue-800 rounded-lg cursor-pointer hover:bg-blue-200 transition-colors border border-blue-300 shadow-sm">
                <span class="font-semibold text-sm">Allow Changes</span>
                <input type="checkbox" x-model="allowChanges" class="rounded text-blue-600 w-4 h-4 border-blue-300 focus:ring-blue-500">
            </label>
            
            <button type="submit" x-show="allowChanges" class="ml-4 px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                Save Changes
            </button>
        </div>

        <!-- Update Basic Pay Modal -->
        <div x-show="showBasicPayModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showBasicPayModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showBasicPayModal" x-transition.scale class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-t-4 border-blue-800">
                    <div class="bg-blue-50 px-4 py-3 flex justify-between items-center border-b border-gray-200">
                        <h3 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-blue-800"></i> Update Basicpay
                        </h3>
                        <div class="flex gap-2">
                            <button type="button" @click="showBasicPayModal = false" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" @click="showBasicPayModal = false" class="text-red-500 hover:text-red-700 font-bold px-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-gray-100 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-300">
                        <div class="mb-4 space-y-2 ml-4">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="update_type" value="update_only" checked class="text-blue-800 focus:ring-blue-800">
                                <span class="text-sm font-medium text-blue-900">Update Only</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="update_type" value="salary_increase" class="text-blue-800 focus:ring-blue-800">
                                <span class="text-sm font-medium text-gray-700">Salary Increase / Adjustment</span>
                            </label>
                        </div>
                        
                        <div class="flex gap-4 mb-4">
                            <div>
                                <label class="block text-xs text-gray-600 text-center mb-1">Previous Amount</label>
                                <input type="text" x-model="basicPay" class="w-32 text-right text-sm border border-gray-300 rounded px-2 py-1.5 bg-white" readonly>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 text-center mb-1">New Amount</label>
                                <input type="number" x-model="newBasicPay" step="0.01" class="w-32 text-right text-sm border border-gray-300 rounded px-2 py-1.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 text-center mb-1">Effectivity</label>
                                <input type="date" value="{{ date('Y-m-d') }}" class="w-32 text-sm border border-gray-300 rounded px-2 py-1.5 bg-white">
                            </div>
                        </div>
                        
                        <div class="flex justify-center mb-6">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" class="rounded border-gray-300">
                                <span class="text-xs text-gray-600">Allow Zero</span>
                            </label>
                        </div>
                        
                        <div class="flex items-center gap-2 px-8">
                            <label class="text-xs text-gray-600">Remarks</label>
                            <input type="text" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="bg-gray-200 px-4 py-3 sm:px-6 flex justify-center gap-12">
                        <button type="button" @click="basicPay = newBasicPay; showBasicPayModal = false" class="w-24 justify-center rounded border border-gray-400 shadow-sm px-4 py-1.5 bg-gray-50 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            OK
                        </button>
                        <button type="button" @click="showBasicPayModal = false" class="w-24 justify-center rounded border border-gray-400 shadow-sm px-4 py-1.5 bg-gray-50 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                    </div>
                    <div class="absolute top-14 right-4">
                        <button type="button" class="text-xs text-blue-600 underline">Default salary</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @else
        <!-- No employee selected placeholder -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center text-gray-500 h-96 flex flex-col justify-center items-center">
            <i class="fas fa-user-circle text-6xl mb-4 text-gray-300"></i>
            <p class="text-lg">Please search and select an employee above to view their information.</p>
        </div>
    @endif
</div>
@endsection
