<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\EmployeeSchedule;
use App\Models\Period;
use App\Models\OfficialBusinessRequest;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\PayrollAdjustment;
use App\Services\PayrollGenerationService;
use App\Helpers\CompanyHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PayrollSandboxController extends Controller
{
    private const SANDBOX_SCHEDULE_NOTE = 'Sandbox test schedule';

    private const SANDBOX_ADJUSTMENT_REASON = 'Sandbox simulation adjustment';

    protected PayrollGenerationService $payrollService;

    public function __construct(PayrollGenerationService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * Show the Sandbox page.
     */
    public function index()
    {
        $companyId = CompanyHelper::getCurrentCompanyId();

        $employees = Employee::active()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->orderBy('last_name')
            ->get();

        $periods = Period::when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->latest('start_date')
            ->take(10)
            ->get();

        $dailyRateDivisors = PayrollGenerationService::dailyRateDivisors();

        return view('admin.sandbox.index', compact('employees', 'periods', 'dailyRateDivisors'));
    }

    /**
     * Seed DTR from custom scenario inputs and calculate payroll via the live engine.
     */
    public function runScenario(Request $request)
    {
        Log::info('[PayrollSandbox] runScenario started', ['request' => $request->all()]);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'regular_days' => 'required|integer|min:0|max:31',
            'ob_days' => 'required|integer|min:0|max:31',
            'vl_days' => 'required|integer|min:0|max:31',
            'absent_days' => 'required|integer|min:0|max:31',
            'late_minutes' => 'required|integer|min:0|max:480',
            'undertime_minutes' => 'nullable|integer|min:0|max:480',
            'overtime_hours' => 'required|numeric|min:0|max:24',
            'overtime_multiplier' => 'nullable|numeric|min:1|max:3',
            'shift_start' => 'nullable|date_format:H:i',
            'shift_end' => 'nullable|date_format:H:i',
            'daily_rate_divisor' => 'nullable|integer|in:261,313,314,365',
            'regular_holiday_days' => 'nullable|integer|min:0|max:31',
            'rest_day_hours' => 'nullable|numeric|min:0|max:24',
            'simulate_night_diff' => 'nullable|boolean',
            'include_loans' => 'nullable|boolean',
            'include_adjustments' => 'nullable|boolean',
            'include_statutory' => 'nullable|boolean',
            'include_withholding_tax' => 'nullable|boolean',
            'sandbox_loan_amount' => 'nullable|numeric|min:0',
            'demo_bonus_taxable' => 'nullable|numeric|min:0',
            'demo_allowance_de_minimis' => 'nullable|numeric|min:0',
            'demo_deduction' => 'nullable|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $companyId = CompanyHelper::getCurrentCompanyId() ?? $employee->company_id;

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $scenario = [
            'regular_days' => (int) $validated['regular_days'],
            'ob_days' => (int) $validated['ob_days'],
            'vl_days' => (int) $validated['vl_days'],
            'absent_days' => (int) $validated['absent_days'],
            'late_minutes' => (int) $validated['late_minutes'],
            'undertime_minutes' => (int) ($validated['undertime_minutes'] ?? 0),
            'overtime_hours' => (float) $validated['overtime_hours'],
            'overtime_multiplier' => (float) ($validated['overtime_multiplier'] ?? 1.25),
            'shift_start' => $validated['shift_start'] ?? '08:00',
            'shift_end' => $validated['shift_end'] ?? '17:00',
            'regular_holiday_days' => (int) ($validated['regular_holiday_days'] ?? 0),
            'rest_day_hours' => (float) ($validated['rest_day_hours'] ?? 0),
            'simulate_night_diff' => $request->boolean('simulate_night_diff'),
            'daily_rate_divisor' => (int) ($validated['daily_rate_divisor'] ?? 261),
            'include_loans' => $request->boolean('include_loans'),
            'include_adjustments' => $request->boolean('include_adjustments'),
            'include_statutory' => $request->boolean('include_statutory'),
            'include_withholding_tax' => $request->boolean('include_withholding_tax'),
            'sandbox_loan_amount' => (float) ($validated['sandbox_loan_amount'] ?? 0),
            'demo_bonus_taxable' => (float) ($validated['demo_bonus_taxable'] ?? 0),
            'demo_allowance_de_minimis' => (float) ($validated['demo_allowance_de_minimis'] ?? 0),
            'demo_deduction' => (float) ($validated['demo_deduction'] ?? 0),
        ];

        try {
            $plan = $this->buildScenarioPlan($scenario, $startDate, $endDate);
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages(['regular_days' => $e->getMessage()]);
        }

        DB::beginTransaction();
        try {
            $this->cleanSandboxData($employee, $startDate, $endDate);

            $this->seedSandboxSchedules(
                $employee,
                $startDate,
                $endDate,
                $plan,
                $scenario['shift_start'],
                $scenario['shift_end']
            );

            $scenarioSummary = $this->executeScenarioPlan($employee, $plan, array_merge($scenario, [
                '_start_date' => $startDate->format('Y-m-d'),
                '_end_date' => $endDate->format('Y-m-d'),
            ]));

            if ($request->boolean('include_adjustments', false)) {
                $this->seedDemoAdjustments(
                    $employee,
                    $companyId,
                    $startDate,
                    (float) ($validated['demo_bonus_taxable'] ?? 0),
                    (float) ($validated['demo_allowance_de_minimis'] ?? 0),
                    (float) ($validated['demo_deduction'] ?? 0)
                );
            }

            // Sandbox loan demos use the amount field only — never silent live loan records.
            $includeLoans = $request->boolean('include_loans', false);
            $loanOverride = $includeLoans
                ? ($request->filled('sandbox_loan_amount')
                    ? (float) $validated['sandbox_loan_amount']
                    : 0.0)
                : null;

            $calculation = $this->payrollService->calculateEmployeePayroll(
                $employee,
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
                [
                    'include_loans' => $includeLoans,
                    'include_adjustments' => $request->boolean('include_adjustments', false),
                    'include_statutory' => $request->boolean('include_statutory', false),
                    'include_withholding_tax' => $request->boolean('include_withholding_tax', false),
                    'daily_rate_divisor' => $scenario['daily_rate_divisor'],
                    'loan_override_amount' => $loanOverride,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payroll simulation completed using the live payroll engine.',
                'employee' => [
                    'name' => $employee->full_name,
                    'monthly_salary' => $employee->salary,
                    'daily_rate' => $calculation['daily_rate'] ?? ($employee->daily_rate ?? 0),
                    'hourly_rate' => $calculation['hourly_rate'] ?? ($employee->hourly_rate ?? 0),
                ],
                'dates' => [
                    'start' => $startDate->format('M d, Y'),
                    'end' => $endDate->format('M d, Y'),
                ],
                'scenario' => $scenarioSummary,
                'calculation' => $calculation,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('[PayrollSandbox] Exception during runScenario', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Sandbox Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear sandbox-generated records for an employee in a date range.
     */
    public function resetData(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        $this->cleanSandboxData(
            $employee,
            Carbon::parse($validated['start_date']),
            Carbon::parse($validated['end_date'])
        );

        return response()->json([
            'success' => true,
            'message' => 'Sandbox records cleared for the selected date range.',
        ]);
    }

    /**
     * Build an ordered list of weekday assignments for the simulation.
     */
    private function buildScenarioPlan(array $scenario, Carbon $startDate, Carbon $endDate): array
    {
        $weekdays = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            if (! $current->isWeekend()) {
                $weekdays[] = $current->copy();
            }
            $current->addDay();
        }

        $needed = $scenario['ob_days']
            + $scenario['vl_days']
            + $scenario['absent_days']
            + $scenario['regular_days']
            + ($scenario['regular_holiday_days'] ?? 0);

        if ($needed > count($weekdays)) {
            throw new \InvalidArgumentException(sprintf(
                'This scenario needs %d weekday(s) but only %d weekday(s) exist between %s and %s.',
                $needed,
                count($weekdays),
                $startDate->format('M d, Y'),
                $endDate->format('M d, Y')
            ));
        }

        if ($needed === 0 && ($scenario['rest_day_hours'] ?? 0) <= 0) {
            throw new \InvalidArgumentException('Set at least one attendance day, holiday, or rest-day hours.');
        }

        $plan = [];
        $idx = 0;

        for ($i = 0; $i < $scenario['ob_days']; $i++) {
            $plan[] = ['date' => $weekdays[$idx++], 'type' => 'ob'];
        }

        for ($i = 0; $i < $scenario['vl_days']; $i++) {
            $plan[] = ['date' => $weekdays[$idx++], 'type' => 'vl'];
        }

        for ($i = 0; $i < ($scenario['regular_holiday_days'] ?? 0); $i++) {
            $plan[] = ['date' => $weekdays[$idx++], 'type' => 'regular_holiday'];
        }

        for ($i = 0; $i < $scenario['absent_days']; $i++) {
            $plan[] = ['date' => $weekdays[$idx++], 'type' => 'absent'];
        }

        $hasLateOrOt = $scenario['late_minutes'] > 0
            || $scenario['undertime_minutes'] > 0
            || $scenario['overtime_hours'] > 0;

        for ($i = 0; $i < $scenario['regular_days']; $i++) {
            $isLateOtDay = $hasLateOrOt && ($i === $scenario['regular_days'] - 1);
            $plan[] = [
                'date' => $weekdays[$idx++],
                'type' => $isLateOtDay ? 'regular_late_ot' : 'regular',
            ];
        }

        return $plan;
    }

    /**
     * Create attendance / request records from the scenario plan.
     */
    private function executeScenarioPlan(Employee $employee, array $plan, array $scenario): array
    {
        $summary = [
            'regular_days' => 0,
            'ob_days' => 0,
            'vl_days' => 0,
            'absent_days' => 0,
            'regular_holiday_days' => 0,
            'rest_day_hours' => $scenario['rest_day_hours'] ?? 0,
            'daily_rate_divisor' => $scenario['daily_rate_divisor'] ?? 261,
            'late_minutes' => $scenario['late_minutes'],
            'undertime_minutes' => $scenario['undertime_minutes'],
            'overtime_hours' => $scenario['overtime_hours'],
            'overtime_multiplier' => $scenario['overtime_multiplier'] ?? 1.25,
            'simulate_night_diff' => $scenario['simulate_night_diff'] ?? false,
            'dates' => [],
        ];

        foreach ($plan as $entry) {
            $dateStr = $entry['date']->format('Y-m-d');
            $label = match ($entry['type']) {
                'ob' => 'Official Business (Paid 100%)',
                'vl' => 'Vacation Leave (Paid 100%)',
                'absent' => 'Absent (No punch)',
                'regular' => 'Regular Biometric Attendance',
                'regular_holiday' => 'Regular Holiday (Worked)',
                'regular_late_ot' => 'Regular Day + Late/UT/OT/NSD',
                default => ucfirst($entry['type']),
            };

            switch ($entry['type']) {
                case 'ob':
                    OfficialBusinessRequest::create([
                        'employee_id' => $employee->id,
                        'date' => $dateStr,
                        'reason' => 'Sandbox OB simulation',
                        'status' => 'approved',
                        'is_full_day' => true,
                        'ob_start_time' => $scenario['shift_start'] . ':00',
                        'ob_end_time' => $scenario['shift_end'] . ':00',
                    ]);
                    $summary['ob_days']++;
                    break;

                case 'vl':
                    LeaveRequest::create([
                        'employee_id' => $employee->id,
                        'leave_type' => 'vacation',
                        'start_date' => $dateStr,
                        'end_date' => $dateStr,
                        'days_requested' => 1,
                        'reason' => 'Sandbox VL simulation',
                        'status' => 'approved',
                        'is_paid' => true,
                    ]);
                    $summary['vl_days']++;
                    break;

                case 'absent':
                    $summary['absent_days']++;
                    break;

                case 'regular_holiday':
                    $this->upsertSandboxSchedule($employee, $dateStr, 'Regular Holiday', $scenario['shift_start'], $scenario['shift_end']);
                    $this->createBiometricAttendance($employee, $dateStr, $scenario['shift_start'], $scenario['shift_end']);
                    $summary['regular_holiday_days']++;
                    break;

                case 'regular':
                    $this->createBiometricAttendance(
                        $employee,
                        $dateStr,
                        $scenario['shift_start'],
                        $scenario['shift_end']
                    );
                    $summary['regular_days']++;
                    break;

                case 'regular_late_ot':
                    if (! empty($scenario['simulate_night_diff'])) {
                        $this->createNightShiftAttendance($employee, $dateStr);
                        $label = 'Night Shift (10 PM–6 AM) + NSD';
                    } else {
                        $times = $this->resolveLateOtTimes($dateStr, $scenario);
                        AttendanceRecord::create([
                            'employee_id' => $employee->id,
                            'date' => $dateStr,
                            'time_in' => $times['time_in'],
                            'time_out' => $times['time_out'],
                            'status' => 'present',
                            'regular_hours' => 8.00,
                            'overtime_hours' => $scenario['overtime_hours'],
                        ]);

                        if ($scenario['overtime_hours'] > 0) {
                            OvertimeRequest::create([
                                'employee_id' => $employee->id,
                                'date' => $dateStr,
                                'start_time' => $this->sandboxDateTime($dateStr, $scenario['shift_end']),
                                'end_time' => $times['time_out'],
                                'hours' => $scenario['overtime_hours'],
                                'rate_multiplier' => $scenario['overtime_multiplier'],
                                'reason' => 'Sandbox OT simulation',
                                'status' => 'approved',
                            ]);
                        }
                    }

                    $summary['regular_days']++;
                    break;
            }

            $summary['dates'][] = [
                'date' => $entry['date']->format('M d, Y (D)'),
                'type' => $label,
            ];
        }

        if (($scenario['rest_day_hours'] ?? 0) > 0) {
            $this->seedRestDayDuty($employee, $scenario, $summary);
        }

        return $summary;
    }

    /**
     * Simulate rest-day duty on the first Saturday in the cutoff range.
     */
    private function seedRestDayDuty(Employee $employee, array $scenario, array &$summary): void
    {
        $start = Carbon::parse($scenario['_start_date'] ?? now());
        $end = Carbon::parse($scenario['_end_date'] ?? now());
        $hours = (float) $scenario['rest_day_hours'];

        $current = $start->copy();
        while ($current->lte($end)) {
            if ($current->isSaturday()) {
                $dateStr = $current->format('Y-m-d');
                $this->upsertSandboxSchedule($employee, $dateStr, 'Day Off', null, null);

                $shiftEnd = Carbon::createFromFormat('H:i', $scenario['shift_start'])->addHours($hours);
                AttendanceRecord::create([
                    'employee_id' => $employee->id,
                    'date' => $dateStr,
                    'time_in' => $this->sandboxDateTime($dateStr, $scenario['shift_start']),
                    'time_out' => $this->sandboxDateTime($dateStr, $shiftEnd->format('H:i:s')),
                    'status' => 'present',
                    'regular_hours' => 0,
                    'overtime_hours' => $hours,
                ]);

                $summary['dates'][] = [
                    'date' => $current->format('M d, Y (D)'),
                    'type' => sprintf('Rest Day Duty (%.1f hrs @ 130%% premium)', $hours),
                ];
                break;
            }
            $current->addDay();
        }
    }

    /**
     * Night shift punch pattern for night differential demo (10 PM – 6 AM).
     */
    private function createNightShiftAttendance(Employee $employee, string $dateStr): void
    {
        $date = Carbon::parse($dateStr);
        AttendanceRecord::create([
            'employee_id' => $employee->id,
            'date' => $dateStr,
            'time_in' => $date->copy()->setTime(21, 0),
            'time_out' => $date->copy()->addDay()->setTime(7, 0),
            'status' => 'present',
            'regular_hours' => 8.00,
            'overtime_hours' => 0,
            'night_shift' => true,
        ]);
    }

    private function upsertSandboxSchedule(
        Employee $employee,
        string $dateStr,
        string $status,
        ?string $shiftStart,
        ?string $shiftEnd
    ): void {
        EmployeeSchedule::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $dateStr,
            ],
            [
                'department_id' => $employee->department_id,
                'time_in' => $shiftStart ? $shiftStart . ':00' : null,
                'time_out' => $shiftEnd ? $shiftEnd . ':00' : null,
                'status' => $status,
                'schedule_type' => 'fixed',
                'required_hours' => in_array($status, ['Working', 'Regular Holiday', 'Special Holiday'], true) ? 8 : 0,
                'notes' => self::SANDBOX_SCHEDULE_NOTE,
                'created_by' => auth()->id(),
            ]
        );
    }

    /**
     * Perfect on-time biometric attendance for a regular workday.
     */
    private function createBiometricAttendance(
        Employee $employee,
        string $dateStr,
        string $shiftStart,
        string $shiftEnd
    ): void {
        AttendanceRecord::create([
            'employee_id' => $employee->id,
            'date' => $dateStr,
            'time_in' => $this->sandboxDateTime($dateStr, $shiftStart),
            'time_out' => $this->sandboxDateTime($dateStr, $shiftEnd),
            'status' => 'present',
            'regular_hours' => 8.00,
            'overtime_hours' => 0,
        ]);
    }

    /**
     * AttendanceRecord casts time_in/time_out as datetime. Bare "08:00:00" values
     * anchor to today's date and inflate late/undertime penalties for past DTR days.
     */
    private function sandboxDateTime(string $dateStr, string $time): Carbon
    {
        $normalized = strlen($time) === 5 ? $time . ':00' : $time;

        return Carbon::parse($dateStr . ' ' . $normalized);
    }

    /**
     * Derive punch times from late, undertime, and OT inputs.
     * Late uses the same grace-period rules as AttendanceRecord (10 min grace).
     */
    private function resolveLateOtTimes(string $dateStr, array $scenario): array
    {
        $shiftStart = Carbon::createFromFormat('H:i', $scenario['shift_start']);
        $shiftEnd = Carbon::createFromFormat('H:i', $scenario['shift_end']);

        $timeIn = $shiftStart->copy();
        if ($scenario['late_minutes'] > 0) {
            // Must exceed 10-minute grace to register as late.
            $timeIn->addMinutes(max($scenario['late_minutes'], 11));
        }

        if ($scenario['overtime_hours'] > 0) {
            $timeOut = $shiftEnd->copy()->addHours($scenario['overtime_hours']);
        } elseif ($scenario['undertime_minutes'] > 0) {
            $timeOut = $shiftEnd->copy()->subMinutes($scenario['undertime_minutes']);
        } else {
            $timeOut = $shiftEnd->copy();
        }

        return [
            'time_in' => $this->sandboxDateTime($dateStr, $timeIn->format('H:i:s')),
            'time_out' => $this->sandboxDateTime($dateStr, $timeOut->format('H:i:s')),
        ];
    }

    /**
     * Optional one-time demo bonus/deduction via PayrollAdjustment (same model as live payroll).
     */
    private function seedDemoAdjustments(
        Employee $employee,
        ?string $companyId,
        Carbon $startDate,
        float $bonusTaxable,
        float $allowanceDeMinimis,
        float $deduction
    ): void {
        PayrollAdjustment::where('employee_id', $employee->id)
            ->where('reason', self::SANDBOX_ADJUSTMENT_REASON)
            ->delete();

        $effectiveDate = $startDate->format('Y-m-d');

        if ($bonusTaxable > 0) {
            PayrollAdjustment::create([
                'company_id' => $companyId,
                'employee_id' => $employee->id,
                'name' => 'Sandbox Taxable Bonus',
                'category' => 'bonus',
                'direction' => 'earning',
                'frequency' => 'one_time',
                'amount' => $bonusTaxable,
                'effective_from' => $effectiveDate,
                'is_taxable' => true,
                'is_active' => true,
                'reason' => self::SANDBOX_ADJUSTMENT_REASON,
                'created_by' => auth()->id(),
            ]);
        }

        if ($allowanceDeMinimis > 0) {
            PayrollAdjustment::create([
                'company_id' => $companyId,
                'employee_id' => $employee->id,
                'name' => 'Sandbox De Minimis Allowance',
                'category' => 'allowance',
                'direction' => 'earning',
                'frequency' => 'one_time',
                'amount' => $allowanceDeMinimis,
                'effective_from' => $effectiveDate,
                'is_taxable' => false,
                'is_active' => true,
                'reason' => self::SANDBOX_ADJUSTMENT_REASON,
                'created_by' => auth()->id(),
            ]);
        }

        if ($deduction > 0) {
            PayrollAdjustment::create([
                'company_id' => $companyId,
                'employee_id' => $employee->id,
                'name' => 'Sandbox Demo Deduction',
                'category' => 'manual',
                'direction' => 'deduction',
                'frequency' => 'one_time',
                'amount' => $deduction,
                'effective_from' => $effectiveDate,
                'is_taxable' => false,
                'is_active' => true,
                'reason' => self::SANDBOX_ADJUSTMENT_REASON,
                'created_by' => auth()->id(),
            ]);
        }
    }

    private function cleanSandboxData(Employee $employee, Carbon $startDate, Carbon $endDate): void
    {
        $from = $startDate->format('Y-m-d');
        $to = $endDate->format('Y-m-d');

        AttendanceRecord::where('employee_id', $employee->id)
            ->whereBetween('date', [$from, $to])
            ->delete();

        LeaveRequest::where('employee_id', $employee->id)
            ->whereBetween('start_date', [$from, $to])
            ->delete();

        OfficialBusinessRequest::where('employee_id', $employee->id)
            ->whereBetween('date', [$from, $to])
            ->delete();

        OvertimeRequest::where('employee_id', $employee->id)
            ->whereBetween('date', [$from, $to])
            ->delete();

        EmployeeSchedule::where('employee_id', $employee->id)
            ->whereBetween('date', [$from, $to])
            ->where('notes', self::SANDBOX_SCHEDULE_NOTE)
            ->delete();

        PayrollAdjustment::where('employee_id', $employee->id)
            ->where('reason', self::SANDBOX_ADJUSTMENT_REASON)
            ->delete();
    }

    /**
     * Seed schedules only for scenario plan dates (plus weekends for rest-day demos).
     *
     * Weekdays outside the plan intentionally receive no schedule so payroll does
     * not treat them as unexcused absences during a partial-period demo.
     */
    private function seedSandboxSchedules(
        Employee $employee,
        Carbon $startDate,
        Carbon $endDate,
        array $plan,
        string $shiftStart = '08:00',
        string $shiftEnd = '17:00'
    ): void {
        $planByDate = collect($plan)->keyBy(fn (array $entry) => $entry['date']->format('Y-m-d'));

        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');
            $isWeekend = $current->isWeekend();
            $planEntry = $planByDate->get($dateStr);

            if ($isWeekend) {
                if ($current->isSaturday()) {
                    EmployeeSchedule::create([
                        'employee_id' => $employee->id,
                        'department_id' => $employee->department_id,
                        'date' => $dateStr,
                        'time_in' => null,
                        'time_out' => null,
                        'status' => 'Day Off',
                        'schedule_type' => 'fixed',
                        'required_hours' => 0,
                        'notes' => self::SANDBOX_SCHEDULE_NOTE,
                        'created_by' => auth()->id(),
                    ]);
                }

                $current->addDay();
                continue;
            }

            if (! $planEntry) {
                $current->addDay();
                continue;
            }

            $scheduleStatus = $planEntry['type'] === 'regular_holiday'
                ? 'Regular Holiday'
                : 'Working';

            EmployeeSchedule::create([
                'employee_id' => $employee->id,
                'department_id' => $employee->department_id,
                'date' => $dateStr,
                'time_in' => $shiftStart . ':00',
                'time_out' => $shiftEnd . ':00',
                'status' => $scheduleStatus,
                'schedule_type' => 'fixed',
                'required_hours' => 8,
                'notes' => self::SANDBOX_SCHEDULE_NOTE,
                'created_by' => auth()->id(),
            ]);

            $current->addDay();
        }
    }
}
