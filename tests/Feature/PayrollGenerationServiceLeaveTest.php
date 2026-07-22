<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\PayrollTemplate;
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
            collect(['2026-07-01', '2026-07-02'])->map(fn ($date) => [
                'employee_id' => $employee->id,
                'schedule_status' => 'Working',
                'attendance_status' => 'Paid Leave',
                'approved_leave_type' => 'sick',
                'scheduled_hours' => '8 hrs',
                'worked_hours' => 0,
                'late_minutes' => 0,
                'overtime' => 0,
                'night_differential_hours' => 0,
                'date_formatted' => $date,
                'date' => $date,
            ])->all(),
            [$employee->id]
        );

        $this->assertNotEmpty($preview);
        $payroll = $preview[0];

        $this->assertEquals(2, $payroll['sick_leave_days']);
        $this->assertEqualsWithDelta($employee->daily_rate * 2, $payroll['sick_leave_pay'], 0.01);
        $this->assertGreaterThan($payroll['basic_salary'], $payroll['gross_pay']);
    }

    public function test_assigned_template_overrides_rates_while_no_template_uses_employee_salary(): void
    {
        $department = Department::create([
            'department_id' => 'DEPT-TEST-2',
            'name' => 'Template Test',
            'budget' => 0,
        ]);

        $template = PayrollTemplate::create([
            'name' => 'Template A',
            'monthly_rate' => 40000,
            'daily_rate' => 800,
            'hourly_rate' => 100,
            'allowances' => 20000,
            'deductions' => 0,
            'is_active' => true,
        ]);

        $templatedEmployee = Employee::create([
            'first_name' => 'Taylor',
            'last_name' => 'Template',
            'salary' => 26000,
            'department_id' => $department->id,
            'hire_date' => '2024-01-01',
        ]);
        $templatedEmployee->forceFill(['payroll_template_id' => $template->id])->save();

        $defaultEmployee = Employee::create([
            'first_name' => 'Taylor',
            'last_name' => 'Default',
            'salary' => 26000,
            'department_id' => $department->id,
            'hire_date' => '2024-01-01',
        ]);

        $records = collect([$templatedEmployee, $defaultEmployee])->map(fn ($employee) => [
            'employee_id' => $employee->id,
            'schedule_status' => 'Day Off',
            'attendance_status' => 'Day Off',
            'scheduled_hours' => 0,
            'worked_hours' => 0,
            'late_minutes' => 0,
            'undertime_minutes' => 0,
            'overtime' => 0,
            'night_differential_hours' => 0,
            'date' => '2026-07-05',
        ])->all();

        $preview = app(PayrollGenerationService::class)->generatePayrollPreview(
            ['start_date' => '2026-07-01', 'end_date' => '2026-07-15'],
            $records,
            [$templatedEmployee->id, $defaultEmployee->id]
        );

        $byEmployee = collect($preview)->keyBy('employee_id');

        $this->assertSame(20000.0, $byEmployee[$templatedEmployee->id]['basic_salary']);
        $this->assertSame(20000.0, $byEmployee[$templatedEmployee->id]['allowances']);
        $this->assertSame(13000.0, $byEmployee[$defaultEmployee->id]['basic_salary']);
        $this->assertSame(0.0, $byEmployee[$defaultEmployee->id]['allowances']);
    }

    public function test_employee_template_assignment_is_mass_assignable(): void
    {
        $employee = new Employee();
        $employee->fill(['payroll_template_id' => 'template-id']);

        $this->assertSame('template-id', $employee->payroll_template_id);
    }
}
