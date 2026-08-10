<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\OfficialBusinessController;
use App\Models\Account;
use App\Models\AttendanceRecord;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\OfficialBusinessRequest;
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

    private function makeEmployeeAccount(): array
    {
        config(['app.url' => 'http://localhost']);
        \Illuminate\Support\Facades\URL::forceRootUrl('http://localhost');

        $company = Company::create([
            'name' => 'OB Request Test Company',
            'code' => 'OBR-'.uniqid(),
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
            'first_name' => 'Pending',
            'last_name' => 'Owner',
            'department_id' => $department->id,
            'salary' => 30000,
            'hire_date' => '2026-01-01',
            'employee_status' => 'active',
            'company_id' => $company->id,
        ]);
        $account = Account::create([
            'employee_id' => $employee->id,
            'email' => 'pending-owner-'.uniqid().'@test.local',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'is_active' => true,
        ]);

        return compact('company', 'employee', 'account');
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

    public function test_pending_ob_overlap_can_be_replaced_without_losing_new_values(): void
    {
        Carbon::setTestNow('2026-08-11 09:00:00');
        ['company' => $company, 'employee' => $employee, 'account' => $account] = $this->makeEmployeeAccount();
        $existing = OfficialBusinessRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-08-17',
            'ob_start_time' => '08:00',
            'ob_end_time' => '12:00',
            'reason' => 'Original filing',
            'status' => OfficialBusinessRequest::PENDING,
            'is_full_day' => false,
        ]);
        $payload = [
            'date' => '2026-08-17',
            'ob_start_time' => '10:00',
            'ob_end_time' => '15:00',
            'reason' => 'Corrected client visit',
        ];

        $this->actingAs($account)->withSession(['current_company_id' => $company->id])
            ->postJson('/official-business', $payload)
            ->assertStatus(409)
            ->assertJsonPath('overlap', true)
            ->assertJsonPath('replaceable_requests.0.id', $existing->id);

        $this->actingAs($account)->withSession(['current_company_id' => $company->id])
            ->postJson('/official-business', $payload + ['replace_request_id' => $existing->id])
            ->assertOk();

        $this->assertSame(OfficialBusinessRequest::CANCELLED, $existing->fresh()->status);
        $this->assertDatabaseHas('official_business_requests', [
            'employee_id' => $employee->id,
            'reason' => 'Corrected client visit',
            'status' => OfficialBusinessRequest::PENDING,
        ]);
    }

    public function test_only_owner_pending_ob_can_be_edited(): void
    {
        Carbon::setTestNow('2026-08-11 09:00:00');
        ['company' => $company, 'employee' => $employee, 'account' => $account] = $this->makeEmployeeAccount();
        $request = OfficialBusinessRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-08-17',
            'ob_start_time' => '08:00',
            'ob_end_time' => '12:00',
            'reason' => 'Needs correction',
            'status' => OfficialBusinessRequest::PENDING,
            'is_full_day' => false,
        ]);
        $payload = [
            'date' => '2026-08-18',
            'ob_start_time' => '09:00',
            'ob_end_time' => '13:00',
            'reason' => 'Corrected OB',
        ];

        $this->actingAs($account)->withSession(['current_company_id' => $company->id])
            ->putJson('/official-business/'.$request->id, $payload)
            ->assertOk();
        $this->assertSame('Corrected OB', $request->fresh()->reason);

        $request->update(['status' => OfficialBusinessRequest::APPROVED]);
        $this->actingAs($account)->withSession(['current_company_id' => $company->id])
            ->putJson('/official-business/'.$request->id, $payload)
            ->assertStatus(422);
    }
}
