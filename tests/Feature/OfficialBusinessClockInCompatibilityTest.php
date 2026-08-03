<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\OfficialBusinessController;
use App\Models\Account;
use App\Models\AttendanceRecord;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class OfficialBusinessClockInCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_employee_can_file_ob_after_clocking_in_for_biometric_correction(): void
    {
        Carbon::setTestNow('2026-08-01 17:30:00');

        $company = Company::create([
            'name' => 'OB Clock-in Test Company',
            'code' => 'OBT-'.uniqid(),
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
            'first_name' => 'Biometric',
            'last_name' => 'Correction',
            'department_id' => $department->id,
            'salary' => 30000,
            'hire_date' => now()->subYear(),
            'employee_status' => 'active',
            'company_id' => $company->id,
        ]);
        $account = Account::create([
            'employee_id' => $employee->id,
            'email' => 'ob-clock-in-'.uniqid().'@test.local',
            'password' => 'password',
            'role' => 'employee',
            'is_active' => true,
        ]);

        AttendanceRecord::create([
            'employee_id' => $employee->id,
            'date' => '2026-08-01',
            'status' => AttendanceRecord::PRESENT,
            'time_in' => '2026-08-01 08:00:00',
        ]);

        $this->actingAs($account);

        app(OfficialBusinessController::class)->store(Request::create(
            '/official-business',
            'POST',
            [
                'date' => '2026-08-01',
                'reason' => 'Biometric device failed to capture the employee clock-out.',
                'ob_start_time' => '17:00',
                'ob_end_time' => '17:30',
            ]
        ));

        $this->assertDatabaseHas('official_business_requests', [
            'employee_id' => $employee->id,
            'date' => '2026-08-01 00:00:00',
            'status' => 'pending',
            'ob_start_time' => '17:00:00',
            'ob_end_time' => '17:30:00',
        ]);
    }
}
