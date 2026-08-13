<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AttendanceRecord;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LoanType;
use App\Models\Loan;
use App\Models\Position;
use App\Models\TimeEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RolePortalPersonalActionsTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private Department $department;
    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');

        $this->company = Company::create([
            'name' => 'Role Portal Company',
            'code' => 'RPC',
            'is_active' => true,
        ]);
        $this->department = Department::create([
            'department_id' => 'DPT-RPC',
            'name' => 'Operations',
            'budget' => 100000,
            'company_id' => $this->company->id,
        ]);
        $position = Position::create([
            'name' => 'Supervisor',
            'code' => 'SUP-RPC',
            'department_id' => $this->department->id,
        ]);
        $this->employee = Employee::create([
            'employee_id' => 'EMP-RPC',
            'first_name' => 'Role',
            'last_name' => 'Tester',
            'company_id' => $this->company->id,
            'department_id' => $this->department->id,
            'position_id' => $position->id,
            'salary' => 30000,
            'hire_date' => now()->subYear(),
            'employee_status' => 'active',
        ]);

        LoanType::create([
            'company_id' => $this->company->id,
            'name' => 'Emergency Loan',
            'default_interest_rate' => 2,
            'interest_type' => 'flat',
            'is_active' => true,
        ]);
    }

    public function test_manager_gets_personal_loan_application_and_can_review_without_managing_types(): void
    {
        $manager = $this->account('manager');
        $this->clockInEmployee();

        $this->actingAs($manager)->withSession(['current_company_id' => $this->company->id])
            ->get(route('loans.index', ['scope' => 'mine']))
            ->assertOk()
            ->assertSee('My Loans')
            ->assertSee('New Loan Request')
            ->assertDontSee('Manage Loan Types');

        $this->get(route('loans.create'))
            ->assertOk()
            ->assertSee('New Loan Request');

        $this->get(route('loan-types.create'))->assertForbidden();

        $this->get(route('loans.index'))
            ->assertOk()
            ->assertSee('Loan Management')
            ->assertDontSee('Manage Loan Types');
    }

    public function test_hr_can_switch_between_my_loans_and_loan_management(): void
    {
        $hr = $this->account('hr');
        $this->clockInEmployee();
        $this->actingAs($hr)->withSession(['current_company_id' => $this->company->id]);

        $this->get(route('loans.index', ['scope' => 'mine']))
            ->assertOk()
            ->assertSee('My Loans')
            ->assertSee('New Loan Request')
            ->assertDontSee('Manage Loan Types');

        $this->get(route('loans.index'))
            ->assertOk()
            ->assertSee('Loan Management')
            ->assertSee('Manage Loan Types');
    }

    public function test_authorized_reviewer_cannot_approve_or_reject_own_loan(): void
    {
        $manager = $this->account('manager');
        $this->clockInEmployee();
        $loanType = LoanType::firstOrFail();
        $loan = Loan::create([
            'company_id' => $this->company->id,
            'employee_id' => $this->employee->id,
            'loan_type_id' => $loanType->id,
            'principal_amount' => 2000,
            'interest_rate' => 2,
            'interest_type' => 'flat',
            'term_months' => 2,
            'amortization_amount' => 510,
            'remaining_balance' => 2040,
            'status' => 'pending',
            'requested_by' => $manager->id,
        ]);

        $this->actingAs($manager)->withSession(['current_company_id' => $this->company->id]);

        $this->post(route('loans.approve', $loan))->assertForbidden();
        $this->post(route('loans.reject', $loan), ['rejection_reason' => 'Self review'])->assertForbidden();
        $this->assertSame('pending', $loan->fresh()->status);
    }

    public function test_employee_linked_manager_sees_logout_choices_and_direct_clock_in_action(): void
    {
        $header = file_get_contents(resource_path('views/components/dashboard/header.blade.php'));
        $navigation = file_get_contents(resource_path('views/components/dashboard/sidebar/navigation.blade.php'));

        $this->assertStringContainsString('if ($user->employee)', $header);
        $this->assertStringContainsString('Clock Out & Logout', $header);
        $this->assertStringContainsString('Logout & Keep Clocked In', $header);
        $this->assertStringContainsString('Cancel', $header);
        $this->assertStringContainsString('sidebarTimeIn();', $navigation);
        $this->assertStringNotContainsString('Please go to the Time In/Out page first', $navigation);
    }

    private function clockInEmployee(): void
    {
        $attendance = AttendanceRecord::create([
            'employee_id' => $this->employee->id,
            'date' => now()->toDateString(),
            'time_in' => now()->subHour(),
            'status' => AttendanceRecord::PRESENT,
        ]);
        TimeEntry::create([
            'attendance_record_id' => $attendance->id,
            'time_in' => now()->subHour(),
            'entry_type' => 'regular',
        ]);
    }

    private function account(string $role): Account
    {
        return Account::create([
            'employee_id' => $this->employee->id,
            'email' => $role.'-role-portal@test.local',
            'password' => bcrypt('password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
