<?php

namespace Tests\Unit;

use App\Models\Employee;
use App\Services\PayrollGenerationService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class PayrollGenerationServiceAttendanceHoursTest extends TestCase
{
    public function test_absent_days_are_not_assumed_to_be_worked(): void
    {
        $records = collect([
            [
                'schedule_status' => 'Working',
                'attendance_status' => 'Absent',
                'scheduled_hours' => '8 hrs',
                'worked_hours' => '—',
            ],
        ]);

        $this->assertSame(0.0, $this->invoke('calculateDaysWorkedFromRecords', $records));
        $this->assertSame(8.0, $this->invoke('sumScheduledHours', $records));
        $this->assertSame(0.0, $this->invoke('sumWorkedHours', $records));
    }

    public function test_actual_and_official_business_hours_are_counted_from_authoritative_records(): void
    {
        $records = collect([
            [
                'schedule_status' => 'Working',
                'attendance_status' => 'Present',
                'scheduled_hours' => '8 hrs',
                'worked_hours' => 7.5,
            ],
            [
                'schedule_status' => 'Working',
                'attendance_status' => 'Official Business',
                'scheduled_hours' => '8 hrs',
                'worked_hours' => '—',
            ],
            [
                'schedule_status' => 'Day Off',
                'attendance_status' => 'Day Off',
                'scheduled_hours' => '—',
                'worked_hours' => '—',
            ],
        ]);

        $this->assertSame(1.9375, $this->invoke('calculateDaysWorkedFromRecords', $records));
        $this->assertSame(16.0, $this->invoke('sumScheduledHours', $records));
        $this->assertSame(7.5, $this->invoke('sumWorkedHours', $records));
    }

    public function test_allowances_do_not_grant_five_unrequested_incentive_leave_days(): void
    {
        $allowances = $this->invoke(
            'calculateAllowances',
            new Employee(),
            1000.0,
            ['paid_leave_days' => 0, 'paid_leave_pay' => 0]
        );

        $this->assertSame(0, $allowances['incentive_leave_days']);
        $this->assertSame(0.0, $allowances['incentive_leave_pay']);
        $this->assertSame(0.0, $allowances['total']);
    }

    public function test_allowances_include_only_approved_paid_leave_compensation(): void
    {
        $allowances = $this->invoke(
            'calculateAllowances',
            new Employee(),
            1000.0,
            ['paid_leave_days' => 2, 'paid_leave_pay' => 2000.0]
        );

        $this->assertSame(2, $allowances['paid_leave_days']);
        $this->assertSame(2000.0, $allowances['paid_leave_pay']);
        $this->assertSame(2000.0, $allowances['total']);
    }

    public function test_regular_overtime_requires_completed_scheduled_hours(): void
    {
        $records = collect([[
            'schedule_status' => 'Working',
            'attendance_status' => 'Present',
            'scheduled_hours' => 8,
            'worked_hours' => 4,
            'overtime_entries' => [['hours' => 2, 'rate_multiplier' => 1.25]],
        ]]);

        $overtime = $this->invoke('calculateOvertimeWithExcelRates', $records, 100.0);

        $this->assertSame(0.0, $overtime['total_hours']);
        $this->assertSame(0.0, $overtime['total_pay']);
    }

    public function test_rest_day_duty_uses_actual_hours_at_one_hundred_thirty_percent(): void
    {
        $records = collect([[
            'schedule_status' => 'Day Off',
            'attendance_status' => 'Rest Day Duty',
            'worked_hours' => 4,
        ]]);

        $premium = $this->invoke(
            'calculateRestDayPremiumWithExcelRates',
            new Employee(),
            $records,
            800.0
        );

        $this->assertSame(520.0, $premium['total_pay']);
    }

    public function test_active_loan_uses_half_monthly_amortization_per_standard_cutoff(): void
    {
        $employee = new Employee();
        $employee->setRawAttributes([
            'loan_start_date' => '2026-01-01',
            'loan_end_date' => '2026-12-31',
            'loan_monthly_amortization' => 2000,
        ], true);

        $deduction = $this->invoke('calculateLoanDeduction', $employee, [
            'start_date' => '2026-06-26',
            'end_date' => '2026-07-10',
        ]);

        $this->assertSame(1000.0, $deduction);
    }

    public function test_statutory_deductions_are_split_across_two_cutoffs(): void
    {
        $deductions = $this->invoke(
            'calculateStatutoryDeductions',
            30000.0,
            2
        );

        $this->assertSame(750.0, $deductions['sss']);
        $this->assertSame(375.0, $deductions['phic']);
        $this->assertSame(100.0, $deductions['hdmf']);
    }

    public function test_monthly_template_deductions_are_also_split_across_two_cutoffs(): void
    {
        $deductions = $this->invoke(
            'calculateStatutoryDeductions',
            30000.0,
            2,
            [
                'sss' => 1800.0,
                'phic' => 900.0,
                'hdmf' => 200.0,
            ]
        );

        $this->assertSame(900.0, $deductions['sss']);
        $this->assertSame(450.0, $deductions['phic']);
        $this->assertSame(100.0, $deductions['hdmf']);
    }

    private function invoke(string $method, mixed ...$arguments): mixed
    {
        $reflection = new ReflectionMethod(PayrollGenerationService::class, $method);
        $reflection->setAccessible(true);

        return $reflection->invoke(new PayrollGenerationService(), ...$arguments);
    }
}
