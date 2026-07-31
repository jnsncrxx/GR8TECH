<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the Access Rules from the Universal Search spec:
 *   - Results are scoped to the active company.
 *   - Employees can only search their own records.
 *   - Managers can only search their own + assigned team records.
 *   - HR/Admin follow existing (company-wide) permissions.
 *   - Sensitive modules (Payroll, Accounts) are never exposed to roles
 *     that shouldn't see them, even when the query would otherwise match.
 */
class SearchAccessScopeTest extends TestCase
{
    use RefreshDatabase;

    private Company $companyA;
    private Company $companyB;

    private Department $deptA;

    private Employee $managerEmployee;   // manages $deptA
    private Employee $teamEmployee;      // in $deptA, reports to $managerEmployee
    private Employee $outsiderEmployee;  // same company, different department
    private Employee $crossCompanyEmployee; // in $companyB entirely

    private Account $adminAccount;
    private Account $managerAccount;
    private Account $employeeAccount; // logs in as $teamEmployee

    private LeaveRequest $teamLeaveRequest;
    private LeaveRequest $outsiderLeaveRequest;

    private Payroll $teamPayroll;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyA = Company::create([
            'name' => 'Alpha Testing Co',
            'code' => 'ALPHA-'.uniqid(),
            'is_active' => true,
        ]);

        $this->companyB = Company::create([
            'name' => 'Beta Testing Co',
            'code' => 'BETA-'.uniqid(),
            'is_active' => true,
        ]);

        $this->deptA = Department::create([
            'department_id' => 'DPT-A-'.uniqid(),
            'name' => 'Engineering',
            'budget' => 100000,
            'company_id' => $this->companyA->id,
        ]);

        $deptOutsider = Department::create([
            'department_id' => 'DPT-B-'.uniqid(),
            'name' => 'Marketing',
            'budget' => 50000,
            'company_id' => $this->companyA->id,
        ]);

        $deptCrossCompany = Department::create([
            'department_id' => 'DPT-C-'.uniqid(),
            'name' => 'Support',
            'budget' => 50000,
            'company_id' => $this->companyB->id,
        ]);

        $position = Position::create([
            'name' => 'Test Engineer '.uniqid(),
            'code' => 'ENG-'.uniqid(),
            'department_id' => $this->deptA->id,
            'is_active' => true,
            'company_id' => $this->companyA->id,
        ]);

        $this->managerEmployee = Employee::create([
            'employee_id' => 'EMP-MGR-'.uniqid(),
            'first_name' => 'Morgan',
            'last_name' => 'Managerson',
            'department_id' => $this->deptA->id,
            'position_id' => $position->id,
            'salary' => 50000,
            'hire_date' => now()->subYear(),
            'employee_status' => 'active',
            'company_id' => $this->companyA->id,
        ]);

        // Now that the manager's employee record exists, point the
        // department at them so the manager-scope query resolves.
        $this->deptA->update(['manager_id' => $this->managerEmployee->id]);

        $this->teamEmployee = Employee::create([
            'employee_id' => 'EMP-TEAM-'.uniqid(),
            'first_name' => 'TerryUniqueMarker',
            'last_name' => 'Teammate',
            'department_id' => $this->deptA->id,
            'position_id' => $position->id,
            'salary' => 40000,
            'hire_date' => now()->subMonths(6),
            'employee_status' => 'active',
            'company_id' => $this->companyA->id,
        ]);

        $this->outsiderEmployee = Employee::create([
            'employee_id' => 'EMP-OUT-'.uniqid(),
            'first_name' => 'OliviaUniqueMarker',
            'last_name' => 'Outsider',
            'department_id' => $deptOutsider->id,
            'position_id' => null,
            'salary' => 40000,
            'hire_date' => now()->subMonths(6),
            'employee_status' => 'active',
            'company_id' => $this->companyA->id,
        ]);

        $this->crossCompanyEmployee = Employee::create([
            'employee_id' => 'EMP-XCO-'.uniqid(),
            'first_name' => 'CaseyUniqueMarker',
            'last_name' => 'CrossCompany',
            'department_id' => $deptCrossCompany->id,
            'position_id' => null,
            'salary' => 40000,
            'hire_date' => now()->subMonths(6),
            'employee_status' => 'active',
            'company_id' => $this->companyB->id,
        ]);

        $this->adminAccount = Account::create([
            'email' => 'admin-'.uniqid().'@test.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->managerAccount = Account::create([
            'employee_id' => $this->managerEmployee->id,
            'email' => 'manager-'.uniqid().'@test.local',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->employeeAccount = Account::create([
            'employee_id' => $this->teamEmployee->id,
            'email' => 'employee-'.uniqid().'@test.local',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'is_active' => true,
        ]);

        $this->teamLeaveRequest = LeaveRequest::create([
            'employee_id' => $this->teamEmployee->id,
            'leave_type' => 'vacation',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(7),
            'days_requested' => 2,
            'reason' => 'TeamLeaveUniqueMarker family trip',
            'status' => 'pending',
        ]);

        $this->outsiderLeaveRequest = LeaveRequest::create([
            'employee_id' => $this->outsiderEmployee->id,
            'leave_type' => 'personal',
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(4),
            'days_requested' => 1,
            'reason' => 'OutsiderLeaveUniqueMarker errand',
            'status' => 'pending',
        ]);

        $this->teamPayroll = Payroll::create([
            'employee_id' => $this->teamEmployee->id,
            'company_id' => $this->companyA->id,
            'pay_period_start' => now()->startOfMonth(),
            'pay_period_end' => now()->endOfMonth(),
            'basic_salary' => 40000,
            'gross_pay' => 40000,
            'net_pay' => 38000,
            'status' => 'pending',
        ]);
    }

    private function searchAs(Account $account, string $query, ?Company $company = null)
    {
        $company = $company ?? $this->companyA;

        return $this->withSession(['current_company_id' => $company->id])
            ->actingAs($account)
            ->getJson('/search?q='.urlencode($query));
    }

    private function moduleResults(array $json, string $moduleKey): array
    {
        foreach ($json['modules'] as $module) {
            if ($module['key'] === $moduleKey) {
                return $module['results'];
            }
        }

        return [];
    }

    /** Admin/HR: company-wide access across every module. */
    public function test_admin_sees_results_company_wide(): void
    {
        $response = $this->searchAs($this->adminAccount, 'TerryUniqueMarker');
        $response->assertOk();

        $employees = $this->moduleResults($response->json(), 'employees');
        $this->assertNotEmpty($employees, 'Admin should find the team employee.');

        $leaveResponse = $this->searchAs($this->adminAccount, 'TeamLeaveUniqueMarker');
        $leave = $this->moduleResults($leaveResponse->json(), 'leave');
        $this->assertNotEmpty($leave, 'Admin should see leave requests across the company.');
    }

    /** Employees may only ever find their own records, never a colleague's. */
    public function test_employee_cannot_find_a_colleague(): void
    {
        $response = $this->searchAs($this->employeeAccount, 'OliviaUniqueMarker');
        $response->assertOk();

        $employees = $this->moduleResults($response->json(), 'employees');
        $this->assertEmpty($employees, 'Employee must not be able to find another employee by name.');
    }

    /** Employees may search and find their own record. */
    public function test_employee_can_find_self(): void
    {
        $response = $this->searchAs($this->employeeAccount, 'TerryUniqueMarker');
        $response->assertOk();

        $employees = $this->moduleResults($response->json(), 'employees');
        $this->assertNotEmpty($employees, 'Employee should be able to find their own record.');
        $this->assertEquals($this->teamEmployee->id, $employees[0]['id']);
    }

    /** Employees may never see a colleague's leave request, even a teammate's. */
    public function test_employee_cannot_see_colleagues_leave_request(): void
    {
        $response = $this->searchAs($this->employeeAccount, 'OutsiderLeaveUniqueMarker');
        $response->assertOk();

        $leave = $this->moduleResults($response->json(), 'leave');
        $this->assertEmpty($leave, 'Employee must not see another employee\'s leave request.');
    }

    /** Managers may find employees within their managed department. */
    public function test_manager_can_find_team_member(): void
    {
        $response = $this->searchAs($this->managerAccount, 'TerryUniqueMarker');
        $response->assertOk();

        $employees = $this->moduleResults($response->json(), 'employees');
        $this->assertNotEmpty($employees, 'Manager should find a member of their managed department.');
    }

    /** Managers may not find employees outside their managed department. */
    public function test_manager_cannot_find_outsider(): void
    {
        $response = $this->searchAs($this->managerAccount, 'OliviaUniqueMarker');
        $response->assertOk();

        $employees = $this->moduleResults($response->json(), 'employees');
        $this->assertEmpty($employees, 'Manager must not find an employee outside their department.');
    }

    /**
     * Managers get attendance/leave visibility into their team, but payroll
     * is deliberately excluded from manager search results even for their
     * own team members.
     */
    public function test_manager_cannot_see_teams_payroll(): void
    {
        $response = $this->searchAs($this->managerAccount, 'TerryUniqueMarker');
        $response->assertOk();

        $payroll = $this->moduleResults($response->json(), 'payroll');
        $this->assertEmpty($payroll, 'Manager must not see payroll results, even for their own team.');
    }

    /** Accounts module is restricted to admin/hr — never manager or employee. */
    public function test_accounts_module_hidden_from_non_admin_roles(): void
    {
        $managerResponse = $this->searchAs($this->managerAccount, 'test.local');
        $managerAccounts = $this->moduleResults($managerResponse->json(), 'accounts');
        $this->assertEmpty($managerAccounts, 'Manager must never see Accounts search results.');

        $employeeResponse = $this->searchAs($this->employeeAccount, 'test.local');
        $employeeAccounts = $this->moduleResults($employeeResponse->json(), 'accounts');
        $this->assertEmpty($employeeAccounts, 'Employee must never see Accounts search results.');
    }

    /**
     * The same admin account, searching from a different active company,
     * must not see records that belong to another company.
     */
    public function test_results_are_scoped_to_the_active_company(): void
    {
        $inCompanyA = $this->searchAs($this->adminAccount, 'CaseyUniqueMarker', $this->companyA);
        $this->assertEmpty(
            $this->moduleResults($inCompanyA->json(), 'employees'),
            'Company A search must not surface an employee who belongs to Company B.'
        );

        $inCompanyB = $this->searchAs($this->adminAccount, 'CaseyUniqueMarker', $this->companyB);
        $this->assertNotEmpty(
            $this->moduleResults($inCompanyB->json(), 'employees'),
            'Switching the active company to Company B should surface that company\'s employee.'
        );
    }

    /** A query under the minimum length returns no modules and doesn't error. */
    public function test_short_query_returns_no_results(): void
    {
        $response = $this->searchAs($this->adminAccount, 'a');
        $response->assertOk();
        $response->assertJson(['modules' => []]);
    }
}
