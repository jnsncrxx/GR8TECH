@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'admin.sandbox.index'])

@section('title', 'Payroll Simulation Demo')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 rounded-xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3">
                    <div>
                        <h2 class="text-2xl font-bold">Payroll Simulation Demo</h2>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2 bg-yellow-400/20 border border-yellow-300/30 px-3 py-1.5 rounded-lg text-xs font-semibold text-yellow-200">
                <i></i> Simulation Demo
            </div>
        </div>
    </div>

    <!-- Simulator Control Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Panel -->
        <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col max-h-[calc(100vh-8rem)]">
            <div class="p-5 border-b border-gray-100 shrink-0">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <i class="fas fa-sliders-h text-blue-600 mr-2"></i> Simulator
                </h3>
                <p class="text-xs text-gray-500 mt-1">Configure a demo scenario, then run the live payroll engine.</p>
            </div>

            <form id="sandboxForm" class="flex flex-col flex-1 min-h-0">
                @csrf

                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    <!-- Employee & Period -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Employee</label>
                            <select name="employee_id" id="employee_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->full_name }} — ₱{{ number_format($employee->salary, 0) }}/mo
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">From</label>
                                <input type="date" name="start_date" id="start_date" value="{{ date('Y-m-01') }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">To</label>
                                <input type="date" name="end_date" id="end_date" value="{{ date('Y-m-15') }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Presets -->
                    <div>
                        <span class="block text-xs font-semibold text-gray-600 mb-2">Quick presets</span>
                        <div class="grid grid-cols-3 gap-1.5">
                            <button type="button" data-preset="standard" class="preset-btn text-[11px] px-2 py-2 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-800 hover:bg-indigo-100 font-medium leading-tight shadow-sm">Standard<br>Demo</button>
                            <button type="button" data-preset="perfect" class="preset-btn text-[11px] px-2 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 font-medium leading-tight">Perfect<br>Attendance</button>
                            <button type="button" data-preset="absence" class="preset-btn text-[11px] px-2 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 font-medium leading-tight">With<br>Absences</button>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div>
                        <div class="flex rounded-lg border border-gray-200 p-0.5 bg-gray-50" role="tablist">
                            <button type="button" data-tab="setup" class="sandbox-tab flex-1 text-xs font-semibold py-1.5 rounded-md bg-white text-blue-700 shadow-sm">Setup</button>
                            <button type="button" data-tab="attendance" class="sandbox-tab flex-1 text-xs font-semibold py-1.5 rounded-md text-gray-600 hover:text-gray-900">Days &amp; Time</button>
                            <button type="button" data-tab="deductions" class="sandbox-tab flex-1 text-xs font-semibold py-1.5 rounded-md text-gray-600 hover:text-gray-900">Deductions</button>
                        </div>

                        <!-- Setup Tab -->
                        <div id="tab-setup" class="sandbox-tab-panel mt-3 space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Daily rate divisor</label>
                                <select name="daily_rate_divisor" id="daily_rate_divisor" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                    @foreach($dailyRateDivisors as $value => $label)
                                        <option value="{{ $value }}" {{ $value == 261 ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-gray-400 mt-1">Applied by the payroll engine when you run a simulation (Monthly × 12 ÷ divisor).</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Shift start</label>
                                    <input type="time" name="shift_start" id="shift_start" value="08:00" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Shift end</label>
                                    <input type="time" name="shift_end" id="shift_end" value="17:00" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Rest day hrs (Sat)</label>
                                    <input type="number" name="rest_day_hours" id="rest_day_hours" value="0" min="0" max="24" step="0.5" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                </div>
                                <div class="flex items-end">
                                    <label class="flex items-start gap-2 text-xs text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="simulate_night_diff" id="simulate_night_diff" value="1" class="rounded border-gray-300 text-purple-600 mt-0.5">
                                        <span>Night diff on last regular day (10 PM–6 AM, +10%)</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Tab -->
                        <div id="tab-attendance" class="sandbox-tab-panel mt-3 space-y-3 hidden">
                            <p class="text-[10px] text-gray-500">Weekdays only (Mon–Fri). OB &amp; VL paid at 100% when approved.</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Regular days</label>
                                    <input type="number" name="regular_days" id="regular_days" value="8" min="0" max="31" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Absent days</label>
                                    <input type="number" name="absent_days" id="absent_days" value="0" min="0" max="31" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">OB days</label>
                                    <input type="number" name="ob_days" id="ob_days" value="1" min="0" max="31" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">VL days</label>
                                    <input type="number" name="vl_days" id="vl_days" value="1" min="0" max="31" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Regular holiday days (worked)</label>
                                    <input type="number" name="regular_holiday_days" id="regular_holiday_days" value="0" min="0" max="31" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                </div>
                            </div>
                            <div class="border-t border-gray-100 pt-3">
                                <p class="text-[10px] text-gray-500 mb-2">Applied on the last regular workday in the scenario.</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Late (min)</label>
                                        <input type="number" name="late_minutes" id="late_minutes" value="30" min="0" max="480" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Undertime (min)</label>
                                        <input type="number" name="undertime_minutes" id="undertime_minutes" value="0" min="0" max="480" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">OT hours</label>
                                        <input type="number" name="overtime_hours" id="overtime_hours" value="4" min="0" max="24" step="0.25" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">OT multiplier</label>
                                        <input type="number" name="overtime_multiplier" id="overtime_multiplier" value="1.25" min="1" max="3" step="0.05" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deductions Tab -->
                        <div id="tab-deductions" class="sandbox-tab-panel mt-3 space-y-3 hidden">
                            <p class="text-[10px] text-gray-500">Toggle optional deductions. Attendance penalties (late, absent) always apply.</p>

                            <div class="space-y-2">
                                <label class="flex items-center justify-between p-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <span class="text-xs">
                                        <span class="font-semibold text-gray-800 block">Statutory (SSS / PhilHealth / Pag-IBIG)</span>
                                        <span class="text-gray-500">2026 tables, split by cutoff length</span>
                                    </span>
                                    <input type="checkbox" name="include_statutory" id="include_statutory" value="1" class="rounded border-gray-300 text-blue-600">
                                </label>
                                <label class="flex items-center justify-between p-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <span class="text-xs">
                                        <span class="font-semibold text-gray-800 block">Withholding tax (TRAIN)</span>
                                        <span class="text-gray-500">Based on taxable gross after attendance adjustments</span>
                                    </span>
                                    <input type="checkbox" name="include_withholding_tax" id="include_withholding_tax" value="1" class="rounded border-gray-300 text-blue-600">
                                </label>
                                <label class="flex items-center justify-between p-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <span class="text-xs">
                                        <span class="font-semibold text-gray-800 block">Employee loans</span>
                                        <span class="text-gray-500">From loan records or sandbox amount below</span>
                                    </span>
                                    <input type="checkbox" name="include_loans" id="include_loans" value="1" class="rounded border-gray-300 text-blue-600">
                                </label>
                                <label class="flex items-center justify-between p-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <span class="text-xs">
                                        <span class="font-semibold text-gray-800 block">Payroll adjustments</span>
                                        <span class="text-gray-500">Active records + demo amounts below</span>
                                    </span>
                                    <input type="checkbox" name="include_adjustments" id="include_adjustments" value="1" class="rounded border-gray-300 text-blue-600">
                                </label>
                            </div>

                            <div class="border-t border-gray-100 pt-3 space-y-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Loan (₱)</label>
                                    <input type="number" name="sandbox_loan_amount" id="sandbox_loan_amount" value="" min="0" step="0.01" class="w-full rounded-lg border-gray-300 shadow-sm text-sm" placeholder="Optional override">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Taxable bonus</label>
                                        <input type="number" name="demo_bonus_taxable" id="demo_bonus_taxable" value="0" min="0" step="0.01" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">De minimis</label>
                                        <input type="number" name="demo_allowance_de_minimis" id="demo_allowance_de_minimis" value="0" min="0" step="0.01" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Manual deduction</label>
                                    <input type="number" name="demo_deduction" id="demo_deduction" value="0" min="0" step="0.01" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="weekdayHint" class="text-[11px] text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 hidden"></div>
                </div>

                <!-- Actions (sticky footer) -->
                <div class="p-5 border-t border-gray-100 space-y-2 shrink-0 bg-white rounded-b-xl">
                    <button type="button" id="btnRunScenario" class="w-full py-2.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold text-sm shadow hover:from-blue-700 hover:to-indigo-700 transition flex items-center justify-center">
                        <i class="fas fa-play mr-2"></i> Run Simulation
                    </button>
                    <button type="button" id="btnResetData" class="w-full py-2 px-4 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg font-medium text-xs transition flex items-center justify-center">
                        <i class="fas fa-trash-alt mr-2"></i> Clear test DTRs in range
                    </button>
                </div>
            </form>
        </div>

        <!-- Calculation Live Output Panel -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Initial State Placeholder -->
            <div id="outputPlaceholder" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-calculator"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-900">Ready to Run a Payroll Demo</h4>
                <p class="text-sm text-gray-500 max-w-md mx-auto mt-1">
                    Pick an employee and a sample scenario, then click <strong>"Run Simulation"</strong> to show clients a live payroll breakdown with DTR, deductions, and net pay.
                </p>
            </div>

            <!-- Loading Spinner -->
            <div id="outputLoading" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-3"></i>
                <p class="text-sm font-semibold text-gray-700">Simulating DTR Records &amp; Executing Payroll Engine...</p>
            </div>

            <!-- Dynamic Result Card -->
            <div id="outputResults" class="hidden space-y-6">

                <!-- Header Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                        <div>
                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Payroll Scenario Result</span>
                            <h3 id="resEmpName" class="text-xl font-bold text-gray-900"></h3>
                            <p id="resDates" class="text-xs text-gray-500"></p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-500 block">Monthly Base Salary</span>
                            <span id="resMonthlySalary" class="text-lg font-bold text-gray-900"></span>
                        </div>
                    </div>

                    <!-- Rates Bar -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 p-3 rounded-lg text-xs">
                        <div>
                            <span id="resDailyRateLabel" class="text-gray-500 block">Daily Rate</span>
                            <span id="resDailyRate" class="font-bold text-gray-800"></span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Hourly Rate (8 Hours)</span>
                            <span id="resHourlyRate" class="font-bold text-gray-800"></span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Minute Rate (Late/UT)</span>
                            <span id="resMinuteRate" class="font-bold text-gray-800"></span>
                        </div>
                        <div>
                            <span id="resOtRateLabel" class="text-gray-500 block">OT Rate</span>
                            <span id="resOtRate" class="font-bold text-gray-800"></span>
                        </div>
                    </div>

                    <!-- Compliance / Table Version -->
                    <div id="resCompliance" class="mt-4 hidden bg-blue-50 border border-blue-100 rounded-lg p-3 text-[11px] text-blue-900 space-y-1"></div>
                </div>

                <!-- Breakdown Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Scenario Applied -->
                    <div id="resScenarioApplied" class="md:col-span-2 hidden bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h4 class="text-sm font-bold text-blue-700 uppercase tracking-wider flex items-center border-b border-gray-100 pb-2 mb-3">
                            <i class="fas fa-calendar-check mr-2"></i> Scenario Applied
                        </h4>
                        <div id="resScenarioGrid" class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs mb-3"></div>
                        <ul id="resScenarioDates" class="text-xs space-y-1 max-h-40 overflow-y-auto"></ul>
                    </div>

                    <!-- Step-by-Step Summary -->
                    <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h4 class="text-sm font-bold text-indigo-700 uppercase tracking-wider flex items-center border-b border-gray-100 pb-2 mb-3">
                            <i class="fas fa-list-ol mr-2"></i> Step-by-Step Payroll Summary
                        </h4>
                        <ol id="resSummarySteps" class="space-y-2 text-sm list-decimal list-inside"></ol>
                    </div>

                    <!-- Earnings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 space-y-3">
                        <h4 class="text-sm font-bold text-green-700 uppercase tracking-wider flex items-center border-b border-gray-100 pb-2">
                            <i class="fas fa-plus-circle mr-2"></i> Gross Salary &amp; Earnings
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">Cutoff Basic Salary</span>
                                <span id="resBasicPay" class="font-semibold text-gray-900"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">Overtime Earnings</span>
                                <span id="resOtPay" class="font-semibold text-green-600"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">Paid Leave / OB Pay</span>
                                <span id="resLeavePay" class="font-semibold text-green-600"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">Night Differential</span>
                                <span id="resNightDiff" class="font-semibold text-green-600"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">Holiday / Rest Day Premium</span>
                                <span id="resHolidayPremium" class="font-semibold text-green-600"></span>
                            </div>
                            <div class="flex justify-between py-2 font-bold text-base bg-green-50 px-3 rounded-lg text-green-800">
                                <span>Gross Income</span>
                                <span id="resGrossIncome"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 space-y-3">
                        <h4 class="text-sm font-bold text-red-700 uppercase tracking-wider flex items-center border-b border-gray-100 pb-2">
                            <i class="fas fa-minus-circle mr-2"></i> Penalties &amp; Statutory Deductions
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">Absence Deduction</span>
                                <span id="resAbsenceDeduction" class="font-semibold text-red-600"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">Loan Amortization</span>
                                <span id="resLoanDeduction" class="font-semibold text-red-600"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">Late &amp; Undertime Penalty</span>
                                <span id="resLatePenalty" class="font-semibold text-red-600"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">SSS Premium</span>
                                <span id="resSss" class="font-semibold text-gray-900"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">PhilHealth Contribution</span>
                                <span id="resPhilhealth" class="font-semibold text-gray-900"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">Pag-IBIG Contribution</span>
                                <span id="resPagibig" class="font-semibold text-gray-900"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-600">Withholding Tax</span>
                                <span id="resTax" class="font-semibold text-gray-900"></span>
                            </div>
                            <div class="flex justify-between py-2 font-bold text-base bg-red-50 px-3 rounded-lg text-red-800">
                                <span>Total Deductions</span>
                                <span id="resTotalDeductions"></span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Final Net Pay Footer -->
                <div class="bg-gradient-to-r from-green-700 to-emerald-800 text-white rounded-xl p-6 shadow-md flex items-center justify-between">
                    <div>
                        <span class="text-xs uppercase tracking-wider text-green-200 block font-semibold">Calculated Take-Home Pay</span>
                        <h2 class="text-3xl font-extrabold" id="resNetPay"></h2>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full">
                            <i class="fas fa-check-circle mr-1.5"></i> Computation Verified
                        </span>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnRunScenario = document.getElementById('btnRunScenario');
    const btnResetData = document.getElementById('btnResetData');
    const form = document.getElementById('sandboxForm');
    const weekdayHint = document.getElementById('weekdayHint');

    const outputPlaceholder = document.getElementById('outputPlaceholder');
    const outputLoading = document.getElementById('outputLoading');
    const outputResults = document.getElementById('outputResults');

    const presets = {
        standard: {
            regular_days: 8, ob_days: 1, vl_days: 1, absent_days: 0,
            regular_holiday_days: 0, rest_day_hours: 0,
            late_minutes: 30, undertime_minutes: 0, overtime_hours: 4,
            overtime_multiplier: 1.25, shift_start: '08:00', shift_end: '17:00',
            daily_rate_divisor: 261, simulate_night_diff: false,
            include_statutory: false, include_withholding_tax: false,
            include_loans: false, include_adjustments: false,
            sandbox_loan_amount: '', demo_bonus_taxable: 0,
            demo_allowance_de_minimis: 0, demo_deduction: 0,
        },
        perfect: {
            _fillAllWeekdays: true,
            regular_days: 0, ob_days: 0, vl_days: 0, absent_days: 0,
            regular_holiday_days: 0, rest_day_hours: 0,
            late_minutes: 0, undertime_minutes: 0, overtime_hours: 0,
            overtime_multiplier: 1.25, shift_start: '08:00', shift_end: '17:00',
            daily_rate_divisor: 261, simulate_night_diff: false,
            include_statutory: false, include_withholding_tax: false,
            include_loans: false, include_adjustments: false,
            sandbox_loan_amount: '', demo_bonus_taxable: 0,
            demo_allowance_de_minimis: 0, demo_deduction: 0,
        },
        absence: {
            regular_days: 7, ob_days: 1, vl_days: 1, absent_days: 1,
            regular_holiday_days: 0, rest_day_hours: 0,
            late_minutes: 30, undertime_minutes: 0, overtime_hours: 4,
            overtime_multiplier: 1.25, shift_start: '08:00', shift_end: '17:00',
            daily_rate_divisor: 261, simulate_night_diff: false,
            include_statutory: false, include_withholding_tax: false,
            include_loans: true, include_adjustments: false,
            sandbox_loan_amount: '500', demo_bonus_taxable: 0,
            demo_allowance_de_minimis: 0, demo_deduction: 0,
        },
    };

    document.querySelectorAll('.sandbox-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const tab = this.dataset.tab;
            document.querySelectorAll('.sandbox-tab').forEach(function (b) {
                b.classList.remove('bg-white', 'text-blue-700', 'shadow-sm');
                b.classList.add('text-gray-600');
            });
            this.classList.add('bg-white', 'text-blue-700', 'shadow-sm');
            this.classList.remove('text-gray-600');
            document.querySelectorAll('.sandbox-tab-panel').forEach(function (panel) {
                panel.classList.add('hidden');
            });
            document.getElementById('tab-' + tab).classList.remove('hidden');
        });
    });

    document.querySelectorAll('.preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.preset-btn').forEach(function (b) {
                b.classList.remove('border-indigo-200', 'bg-indigo-50', 'text-indigo-800', 'shadow-sm');
                b.classList.add('border-gray-200', 'bg-gray-50', 'text-gray-700');
            });
            this.classList.add('border-indigo-200', 'bg-indigo-50', 'text-indigo-800', 'shadow-sm');
            this.classList.remove('border-gray-200', 'bg-gray-50', 'text-gray-700');
            applyPreset(presets[this.dataset.preset] || presets.standard);
        });
    });

    ['start_date', 'end_date', 'regular_days', 'ob_days', 'vl_days', 'absent_days', 'regular_holiday_days'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', updateWeekdayHint);
        if (el) el.addEventListener('input', updateWeekdayHint);
    });

    updateWeekdayHint();

    function applyPreset(p) {
        const adjusted = Object.assign({}, p);

        if (adjusted._fillAllWeekdays) {
            const weekdays = countWeekdays(
                document.getElementById('start_date').value,
                document.getElementById('end_date').value
            );
            const reserved =
                (+adjusted.ob_days || 0) +
                (+adjusted.vl_days || 0) +
                (+adjusted.absent_days || 0) +
                (+adjusted.regular_holiday_days || 0);
            adjusted.regular_days = Math.max(0, weekdays - reserved);
        }

        Object.keys(adjusted).forEach(function (key) {
            if (key.charAt(0) === '_') {
                return;
            }
            const el = document.getElementById(key);
            if (!el) return;
            if (el.type === 'checkbox') {
                el.checked = !!adjusted[key];
            } else {
                el.value = adjusted[key];
            }
        });
        updateWeekdayHint();
    }

    function countWeekdays(startStr, endStr) {
        const start = new Date(startStr + 'T00:00:00');
        const end = new Date(endStr + 'T00:00:00');
        if (isNaN(start) || isNaN(end) || end < start) return 0;
        let count = 0;
        const d = new Date(start);
        while (d <= end) {
            const day = d.getDay();
            if (day !== 0 && day !== 6) count++;
            d.setDate(d.getDate() + 1);
        }
        return count;
    }

    function updateWeekdayHint() {
        const weekdays = countWeekdays(
            document.getElementById('start_date').value,
            document.getElementById('end_date').value
        );
        const needed =
            (+document.getElementById('regular_days').value || 0) +
            (+document.getElementById('ob_days').value || 0) +
            (+document.getElementById('vl_days').value || 0) +
            (+document.getElementById('absent_days').value || 0) +
            (+document.getElementById('regular_holiday_days').value || 0);

        if (!weekdays) {
            weekdayHint.classList.add('hidden');
            return;
        }

        weekdayHint.classList.remove('hidden');
        if (needed > weekdays) {
            weekdayHint.textContent = 'Warning: scenario needs ' + needed + ' weekday(s) but only ' + weekdays + ' exist in this date range.';
            weekdayHint.className = 'text-[11px] text-red-700 bg-red-50 border border-red-100 rounded-lg px-3 py-2';
        } else {
            weekdayHint.textContent = weekdays + ' weekday(s) available · scenario uses ' + needed + '.';
            weekdayHint.className = 'text-[11px] text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2';
        }
    }

    function buildFormData() {
        const formData = new FormData(form);
        [
            'include_statutory', 'include_withholding_tax',
            'include_loans', 'include_adjustments', 'simulate_night_diff',
        ].forEach(function (name) {
            formData.delete(name);
            if (document.getElementById(name) && document.getElementById(name).checked) {
                formData.append(name, '1');
            }
        });
        return formData;
    }

    btnRunScenario.addEventListener('click', async function () {
        outputPlaceholder.classList.add('hidden');
        outputResults.classList.add('hidden');
        outputLoading.classList.remove('hidden');

        try {
            const response = await fetch("{{ route('developer.sandbox.run') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: buildFormData()
            });

            const data = await response.json();
            outputLoading.classList.add('hidden');

            if (data.success) {
                renderResults(data);
                outputResults.classList.remove('hidden');
            } else {
                alert('Error: ' + (data.error || data.message || 'Failed to calculate scenario'));
                outputPlaceholder.classList.remove('hidden');
            }
        } catch (err) {
            console.error(err);
            outputLoading.classList.add('hidden');
            outputPlaceholder.classList.remove('hidden');
            alert('Failed to connect to Sandbox server.');
        }
    });

    btnResetData.addEventListener('click', async function () {
        if (!confirm('Clear all sandbox DTR, leave, OB, OT, schedule, and demo adjustment records for this employee in the date range?')) {
            return;
        }

        const formData = new FormData(form);
        formData.delete('include_loans');
        formData.delete('include_adjustments');

        try {
            const response = await fetch("{{ route('developer.sandbox.reset') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                alert(data.message);
                outputResults.classList.add('hidden');
                outputPlaceholder.classList.remove('hidden');
            }
        } catch (err) {
            alert('Failed to reset sandbox data.');
        }
    });

    function fmt(num) {
        return '₱' + (parseFloat(num) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function renderResults(data) {
        const emp = data.employee;
        const dates = data.dates;
        const calc = data.calculation;
        const scenario = data.scenario || {};
        const compliance = calc.compliance || {};

        document.getElementById('resEmpName').textContent = emp.name;
        document.getElementById('resDates').textContent = dates.start + ' — ' + dates.end;
        document.getElementById('resMonthlySalary').textContent = fmt(emp.monthly_salary);

        document.getElementById('resDailyRateLabel').textContent = compliance.divisor_applied_in_engine
            ? 'Daily Rate (Monthly × 12 ÷ ' + compliance.daily_rate_divisor + ')'
            : 'Daily Rate (employee / template)';
        document.getElementById('resDailyRate').textContent = fmt(emp.daily_rate);
        document.getElementById('resHourlyRate').textContent = fmt(emp.hourly_rate);
        document.getElementById('resMinuteRate').textContent = fmt(emp.hourly_rate / 60);

        const otMultiplier = parseFloat(calc.overtime_effective_multiplier)
            || parseFloat(scenario.overtime_multiplier)
            || 1.25;
        const otRate = parseFloat(calc.overtime_rate) || (emp.hourly_rate * otMultiplier);
        document.getElementById('resOtRateLabel').textContent =
            'OT Rate (' + otMultiplier.toFixed(2).replace(/\.?0+$/, '') + '×)';
        document.getElementById('resOtRate').textContent = fmt(otRate);

        document.getElementById('resBasicPay').textContent = fmt(calc.basic_pay || calc.basic_salary);
        document.getElementById('resOtPay').textContent = fmt(calc.overtime_pay || 0);
        document.getElementById('resLeavePay').textContent = calc.paid_leave_included_in_basic
            ? 'Included in basic'
            : fmt(calc.leave_pay || 0);
        document.getElementById('resNightDiff').textContent = fmt(calc.night_differential_pay || 0);
        document.getElementById('resHolidayPremium').textContent = fmt(
            (parseFloat(calc.holiday_pay || 0) + parseFloat(calc.rest_day_premium_pay || 0))
        );
        document.getElementById('resGrossIncome').textContent = fmt(
            calc.gross_after_attendance ?? calc.gross_salary ?? calc.gross_pay
        );

        document.getElementById('resAbsenceDeduction').textContent = fmt(calc.absence_deduction || 0);
        document.getElementById('resLoanDeduction').textContent = fmt(calc.loan_deduction || 0);
        document.getElementById('resLatePenalty').textContent = fmt((calc.tardiness_deduction || 0) + (calc.undertime_deduction || 0));
        document.getElementById('resSss').textContent = statutoryLabel(calc.sss_deduction, compliance.statutory_enabled);
        document.getElementById('resPhilhealth').textContent = statutoryLabel(calc.philhealth_deduction, compliance.statutory_enabled);
        document.getElementById('resPagibig').textContent = statutoryLabel(calc.pagibig_deduction, compliance.statutory_enabled);
        document.getElementById('resTax').textContent = compliance.withholding_tax_enabled === false
            ? 'Excluded'
            : fmt(calc.tax_deduction || calc.withholding_tax || 0);

        document.getElementById('resTotalDeductions').textContent = fmt(calc.total_deductions || 0);
        document.getElementById('resNetPay').textContent = fmt(calc.net_salary || calc.net_pay);

        renderScenarioApplied(scenario);
        renderCompliance(calc.compliance || {});
        renderSummarySteps(calc.summary_steps || []);
    }

    function statutoryLabel(amount, enabled) {
        if (enabled === false) {
            return 'Excluded';
        }
        return fmt(amount || 0);
    }

    function renderCompliance(compliance) {
        const panel = document.getElementById('resCompliance');
        if (!compliance || Object.keys(compliance).length === 0) {
            panel.classList.add('hidden');
            return;
        }

        panel.classList.remove('hidden');

        const excluded = [];
        if (compliance.statutory_enabled === false) {
            excluded.push('SSS / PhilHealth / Pag-IBIG');
        }
        if (compliance.withholding_tax_enabled === false) {
            excluded.push('Withholding tax');
        }

        let exclusionNote = '';
        if (excluded.length) {
            exclusionNote =
                '<p class="mt-2 pt-2 border-t border-blue-200 text-amber-900">' +
                '<strong>Excluded from this run:</strong> ' + excluded.join(', ') +
                ' — deduction lines show "Excluded", not ₱0.</p>';
        }

        const nonTaxable = parseFloat(compliance.non_taxable_earnings_excluded || 0);
        let nonTaxableNote = '';
        if (nonTaxable > 0) {
            nonTaxableNote =
                '<p><strong>De minimis / non-taxable earnings:</strong> ₱' +
                nonTaxable.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +
                ' excluded from taxable gross before withholding tax.</p>';
        }

        panel.innerHTML =
            '<p class="font-bold text-blue-800 mb-1"><i class="fas fa-balance-scale mr-1"></i> Formulas &amp; tables in effect</p>' +
            '<p><strong>Pay frequency:</strong> ' + (compliance.pay_frequency || '—') + '</p>' +
            (compliance.pay_frequency_tax_mode
                ? '<p><strong>Tax mode:</strong> ' + compliance.pay_frequency_tax_mode + '</p>'
                : '') +
            '<p><strong>Daily rate:</strong> ' + (compliance.daily_rate_formula || '—') + '</p>' +
            '<p><strong>SSS:</strong> ' + (compliance.sss_table || '—') + '</p>' +
            '<p><strong>PhilHealth:</strong> ' + (compliance.philhealth_table || '—') + '</p>' +
            '<p><strong>Pag-IBIG:</strong> ' + (compliance.pagibig_table || '—') + '</p>' +
            '<p><strong>Withholding tax:</strong> ' + (compliance.tax_table || '—') +
                (compliance.tax_bracket_detail ? ' — ' + compliance.tax_bracket_detail : '') + '</p>' +
            '<p><strong>Late grace:</strong> ' + (compliance.late_grace_period || '10 minutes') + '</p>' +
            nonTaxableNote +
            exclusionNote +
            (compliance.thirteenth_month_note
                ? '<p class="mt-2 pt-2 border-t border-blue-200 text-gray-600 italic">' + compliance.thirteenth_month_note + '</p>'
                : '');
    }

    function renderScenarioApplied(scenario) {
        const panel = document.getElementById('resScenarioApplied');
        const grid = document.getElementById('resScenarioGrid');
        const list = document.getElementById('resScenarioDates');

        if (!scenario.dates || !scenario.dates.length) {
            panel.classList.add('hidden');
            return;
        }

        panel.classList.remove('hidden');
        const chips = [
            ['Regular Days', scenario.regular_days || 0],
            ['OB Days', scenario.ob_days || 0],
            ['VL Days', scenario.vl_days || 0],
            ['Holiday Days', scenario.regular_holiday_days || 0],
            ['Absent Days', scenario.absent_days || 0],
            ['Rest Day Hrs', scenario.rest_day_hours || 0],
            ['Late (min)', scenario.late_minutes || 0],
            ['OT (hrs)', scenario.overtime_hours || 0],
            ['OT mult.', scenario.overtime_multiplier || 1.25],
        ];

        grid.innerHTML = chips.map(function (c) {
            return '<div class="bg-gray-50 rounded-lg p-2 text-center border border-gray-100">' +
                '<div class="text-gray-500">' + c[0] + '</div>' +
                '<div class="font-bold text-gray-900 text-sm">' + c[1] + '</div></div>';
        }).join('');

        list.innerHTML = scenario.dates.map(function (d) {
            return '<li class="flex justify-between py-1 border-b border-gray-50">' +
                '<span class="text-gray-700">' + d.date + '</span>' +
                '<span class="text-gray-500 font-medium">' + d.type + '</span></li>';
        }).join('');
    }

    function renderSummarySteps(steps) {
        const container = document.getElementById('resSummarySteps');
        container.innerHTML = '';

        if (!steps.length) {
            container.innerHTML = '<li class="text-gray-500">No detailed breakdown available.</li>';
            return;
        }

        steps.forEach(function (step) {
            const li = document.createElement('li');
            li.className = 'py-2 border-b border-gray-50 last:border-0';

            const amountClass = step.type === 'deduction'
                ? 'text-red-600'
                : (step.type === 'info'
                    ? 'text-gray-500 italic'
                    : (step.type === 'net' || step.type === 'total' ? 'text-indigo-700 font-bold' : 'text-green-700'));

            li.innerHTML =
                '<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">' +
                    '<div>' +
                        '<span class="font-semibold text-gray-900">' + step.label + ':</span> ' +
                        '<span class="text-gray-600">' + (step.detail || '') + '</span>' +
                    '</div>' +
                    '<span class="' + amountClass + ' font-semibold whitespace-nowrap">' + (step.formula || '') + '</span>' +
                '</div>';

            container.appendChild(li);
        });
    }
});
</script>
@endsection
