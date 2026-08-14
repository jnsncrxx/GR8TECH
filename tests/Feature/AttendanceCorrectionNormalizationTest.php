<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\AttendanceController;
use App\Models\Account;
use App\Models\AttendanceRecord;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AttendanceCorrectionNormalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_absent_correction_clears_stale_punch_and_break_values(): void
    {
        $company = Company::create([
            'name' => 'Attendance Test Company',
            'code' => 'ATC-'.uniqid(),
            'is_active' => true,
        ]);

        $department = Department::create([
            'department_id' => 'DPT-'.uniqid(),
            'name' => 'Operations',
            'budget' => 100000,
            'company_id' => $company->id,
        ]);

        $employee = Employee::create([
            'employee_id' => 'EMP-'.uniqid(),
            'first_name' => 'Jerson',
            'last_name' => 'Correction Test',
            'department_id' => $department->id,
            'salary' => 30000,
            'hire_date' => now()->subYear(),
            'employee_status' => 'active',
            'company_id' => $company->id,
        ]);

        $account = Account::create([
            'employee_id' => $employee->id,
            'email' => 'attendance-admin-'.uniqid().'@test.local',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $attendance = AttendanceRecord::create([
            'employee_id' => $employee->id,
            'date' => '2026-08-01',
            'status' => 'present',
            'time_in' => '2026-08-01 20:50:00',
            'break_start' => '2026-08-01 21:00:00',
        ]);

        $this->actingAs($account);

        app(AttendanceController::class)->updateRecord(Request::create(
            '/attendance/update-record/'.$attendance->id,
            'PUT',
            [
                'employee_id' => $employee->id,
                'date' => '2026-08-01',
                'status' => 'absent',
                'time_in' => '20:50',
                'time_out' => '22:00',
                'break_start' => '21:00',
                'break_end' => '21:30',
                'correction_reason' => 'Confirmed absence; stale test punches removed.',
            ]
        ), $attendance->id);

        $attendance->refresh();

        $this->assertSame('absent', $attendance->status);
        $this->assertNull($attendance->time_in);
        $this->assertNull($attendance->time_out);
        $this->assertNull($attendance->break_start);
        $this->assertNull($attendance->break_end);
        $this->assertEquals(0, (float) $attendance->total_hours);
        $this->assertEquals(0, (float) $attendance->regular_hours);
        $this->assertEquals(0, (float) $attendance->overtime_hours);
    }
}
