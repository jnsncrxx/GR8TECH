<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\OfficialBusinessController;
use App\Http\Controllers\Web\ReportController;
use App\Models\Account;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\OfficialBusinessRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class OfficialBusinessReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_consolidated_ob_report_and_personal_statistics_are_scoped(): void
    {
        $activeCompany = $this->company('ACTIVE');
        $otherCompany = $this->company('OTHER');
        $activeDepartment = $this->department($activeCompany, 'Operations');
        $otherDepartment = $this->department($otherCompany, 'Operations');

        $ownEmployee = $this->employee($activeCompany, $activeDepartment, 'Own');
        $colleague = $this->employee($activeCompany, $activeDepartment, 'Colleague');
        $outsider = $this->employee($otherCompany, $otherDepartment, 'Outsider');

        $account = Account::create([
            'employee_id' => $ownEmployee->id,
            'email' => 'ob-admin-'.uniqid().'@test.local',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($account);
        session(['current_company_id' => $activeCompany->id]);

        $own = $this->ob($ownEmployee, 'Own field visit');
        $sameCompany = $this->ob($colleague, 'Colleague field visit');
        $this->ob($outsider, 'Other-company field visit');

        $report = app(ReportController::class)->generate(Request::create(
            '/reports/generate',
            'GET',
            ['report_type' => 'official_business']
        ));

        $this->assertSame(
            collect([$own->id, $sameCompany->id])->sort()->values()->all(),
            $report->getData()['data']->pluck('id')->sort()->values()->all()
        );

        $statistics = app(OfficialBusinessController::class)->getStatistics(Request::create(
            '/official-business/statistics',
            'GET',
            ['scope' => 'mine']
        ));

        $this->assertSame(1, $statistics->getData(true)['total']);
        $this->assertSame(1, $statistics->getData(true)['approved']);
    }

    private function company(string $code): Company
    {
        return Company::create([
            'name' => $code.' Company',
            'code' => $code.'-'.uniqid(),
            'is_active' => true,
        ]);
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

    private function employee(Company $company, Department $department, string $name): Employee
    {
        return Employee::create([
            'employee_id' => 'EMP-'.uniqid(),
            'first_name' => $name,
            'last_name' => 'Employee',
            'department_id' => $department->id,
            'salary' => 30000,
            'hire_date' => now()->subYear(),
            'employee_status' => 'active',
            'company_id' => $company->id,
        ]);
    }

    private function ob(Employee $employee, string $reason): OfficialBusinessRequest
    {
        return OfficialBusinessRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-08-01',
            'reason' => $reason,
            'status' => OfficialBusinessRequest::APPROVED,
            'is_full_day' => false,
            'ob_start_time' => '13:00',
            'ob_end_time' => '17:00',
            'credited_hours' => 4,
        ]);
    }
}
