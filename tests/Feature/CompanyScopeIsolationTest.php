<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\HrController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\AttendanceController;
use App\Http\Controllers\Web\LeaveController;
use App\Http\Controllers\Web\LoanController;
use App\Http\Controllers\Web\OfficialBusinessController;
use App\Http\Controllers\Web\OvertimeController;
use App\Http\Controllers\Web\PayrollController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\ScheduleV2Controller;
use App\Models\Account;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CompanyScopeIsolationTest extends TestCase
{
    use RefreshDatabase;

    private Company $activeCompany;
    private Company $otherCompany;
    private Department $activeDepartment;
    private Department $otherDepartment;
    private Employee $activeEmployee;
    private Employee $otherEmployee;
    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activeCompany = Company::create([
            'name' => 'Active Company',
            'code' => 'ACTIVE-'.uniqid(),
            'is_active' => true,
        ]);
        $this->otherCompany = Company::create([
            'name' => 'Other Company',
            'code' => 'OTHER-'.uniqid(),
            'is_active' => true,
        ]);

        $this->activeDepartment = $this->department($this->activeCompany, 'Information Technology');
        $this->otherDepartment = $this->department($this->otherCompany, 'Information Technology');
        $this->activeEmployee = $this->employee($this->activeCompany, $this->activeDepartment, 'Active');
        $this->otherEmployee = $this->employee($this->otherCompany, $this->otherDepartment, 'Other');

        $this->account = Account::create([
            'email' => 'scope-admin-'.uniqid().'@test.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($this->account);
        session(['current_company_id' => $this->activeCompany->id]);
    }

    public function test_department_and_employee_dropdowns_only_use_the_active_company(): void
    {
        $employeeIndex = app(EmployeeController::class)->index();
        $employeeIndexData = $employeeIndex->getData();
        $this->assertSame([$this->activeDepartment->id], $employeeIndexData['departments']->pluck('id')->all());
        $this->assertSame(1, $employeeIndexData['employeeStats']['departments']);
        $this->assertSame(1, $employeeIndexData['employeeStats']['total']);
        $this->assertSame([$this->activeEmployee->id], $employeeIndexData['employees']->pluck('id')->all());

        $reports = app(ReportController::class)->index();
        $this->assertSame([$this->activeDepartment->id], $reports->getData()['departments']->pluck('id')->all());
        $this->assertSame([$this->activeEmployee->id], $reports->getData()['employees']->pluck('id')->all());

        $settings = app(HrController::class)->settings(Request::create('/hr/settings'));
        $this->assertSame([$this->activeDepartment->id], $settings->getData()['departments']->pluck('id')->all());

        $leave = app(LeaveController::class)->create(Request::create('/leave-management/create'));
        $this->assertSame([$this->activeEmployee->id], $leave->getData()['employees']->pluck('id')->all());
    }

    public function test_direct_employee_access_from_another_company_is_not_found(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        app(EmployeeController::class)->show($this->otherEmployee);
    }

    public function test_payroll_generation_options_only_use_the_active_company(): void
    {
        $activePeriod = $this->period($this->activeCompany, $this->activeDepartment, 'Active Period');
        $this->period($this->otherCompany, $this->otherDepartment, 'Other Period');

        $view = app(PayrollController::class)->generateFromPeriod();
        $data = $view->getData();

        $this->assertSame([$activePeriod->id], $data['periods']->pluck('id')->all());
        $this->assertSame([$this->activeDepartment->id], $data['departments']->pluck('id')->all());
        $this->assertSame([$this->activeEmployee->id], $data['employees']->pluck('id')->all());
    }

    public function test_module_filter_options_never_include_another_company(): void
    {
        $modules = [
            [AttendanceController::class, 'timekeeping', '/attendance/timekeeping'],
            [OfficialBusinessController::class, 'index', '/official-business'],
            [OvertimeController::class, 'index', '/overtime'],
            [LoanController::class, 'index', '/loans'],
            [ScheduleV2Controller::class, 'index', '/schedule-management'],
        ];

        foreach ($modules as [$controller, $method, $uri]) {
            $view = app($controller)->{$method}(Request::create($uri));
            $data = $view->getData();

            if (isset($data['departments'])) {
                $this->assertSame(
                    [$this->activeDepartment->id],
                    $data['departments']->pluck('id')->all(),
                    "{$controller} leaked another company's department."
                );
            }

            if (isset($data['employees'])) {
                $this->assertSame(
                    [$this->activeEmployee->id],
                    $data['employees']->pluck('id')->all(),
                    "{$controller} leaked another company's employee."
                );
            }
        }
    }

    private function department(Company $company, string $name): Department
    {
        return Department::create([
            'department_id' => 'DPT-'.uniqid(),
            'name' => $name,
            'budget' => 100000,
            'company_id' => $company->id,
        ]);
    }

    private function employee(Company $company, Department $department, string $firstName): Employee
    {
        return Employee::create([
            'employee_id' => 'EMP-'.uniqid(),
            'first_name' => $firstName,
            'last_name' => 'Employee',
            'department_id' => $department->id,
            'salary' => 30000,
            'hire_date' => now()->subYear(),
            'employee_status' => 'active',
            'company_id' => $company->id,
        ]);
    }

    private function period(Company $company, Department $department, string $name): Period
    {
        return Period::create([
            'company_id' => $company->id,
            'name' => $name,
            'period_month' => 7,
            'period_year' => 2026,
            'period_no' => 1,
            'period_type' => 'semi_monthly',
            'processing_type' => 'regular',
            'payroll_date' => '2026-07-15',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-15',
            'working_days' => 11,
            'status' => Period::STATUS_OPEN,
            'department_id' => $department->id,
            'created_by' => $this->account->id,
        ]);
    }
}
