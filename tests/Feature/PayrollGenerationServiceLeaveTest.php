<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Services\PayrollGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollGenerationServiceLeaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_sick_leave_is_included_in_payroll_preview(): void
    {
        $department = Department::create([
            'department_id' => 'DEPT-TEST-1',
            'name' => 'Operations',
            'budget' => 0,
        ]);

        $employee = Employee::create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'salary' => 26000,
            'department_id' => $department->id,
            'hire_date' => '2024-01-01',
        ]);

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type' => 'sick',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-02',
            'days_requested' => 2,
            'reason' => 'Fever',
            'status' => 'approved',
        ]);

        $service = app(PayrollGenerationService::class);

        $preview = $service->generatePayrollPreview(
            [
                'start_date' => '2026-07-01',
                'end_date' => '2026-07-15',
            ],
            [[
                'employee_id' => $employee->id,
                'schedule_status' => 'Working',
                'attendance_status' => 'Present',
                'scheduled_hours' => '8 hrs',
                'late_minutes' => 0,
                'overtime' => 0,
                'night_differential_hours' => 0,
                'date_formatted' => '2026-07-01',
                'date' => '2026-07-01',
            ]],
            [$employee->id]
        );

        $this->assertNotEmpty($preview);
        $payroll = $preview[0];

        $this->assertEquals(2, $payroll['sick_leave_days']);
        $this->assertEqualsWithDelta($employee->daily_rate * 2, $payroll['sick_leave_pay'], 0.01);
        $this->assertGreaterThan($payroll['basic_salary'], $payroll['gross_pay']);
    }
}
