<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /** @var array<string, string> */
    public const IDS = [
        'jerson' => '20000000-0000-4000-8000-000000000001',
        'curt' => '20000000-0000-4000-8000-000000000002',
        'charlie' => '20000000-0000-4000-8000-000000000003',
        'reece' => '20000000-0000-4000-8000-000000000004',
        'alexander' => '20000000-0000-4000-8000-000000000005',
        'maria' => '20000000-0000-4000-8000-000000000006',
        'reyven' => '20000000-0000-4000-8000-000000000007',
        'bulbasaur' => '20000000-0000-4000-8000-000000000008',
        'taylor' => '20000000-0000-4000-8000-000000000009',
        'npi_employee' => '20000000-0000-4000-8000-000000000010',
    ];

    /** @var array<string, string> */
    public const NUMBERS = [
        'jerson' => 'EMP-0001',
        'curt' => 'EMP-0002',
        'charlie' => 'EMP-0003',
        'reece' => 'EMP-0004',
        'alexander' => 'EMP-0005',
        'maria' => 'EMP-0006',
        'reyven' => 'EMP-0007',
        'bulbasaur' => 'EMP-0008',
        'taylor' => 'EMP-0009',
        'npi_employee' => 'NPI-0001',
    ];

    public function run(): void
    {
        $employees = [
            ['jerson', 'EMP-0001', 'Jerson Marg', 'Cerezo', 'GR8TECH', 'DEPT-GR8-HR', 'GR8-HRM', 48000],
            ['curt', 'EMP-0002', 'Curt Vincent', 'Guiling', 'GR8TECH', 'DEPT-GR8-OPS', 'GR8-OPSA', 30000],
            ['charlie', 'EMP-0003', 'Charlie', 'Cawile', 'GR8TECH', 'DEPT-GR8-SM', 'GR8-SMM', 50000],
            ['reece', 'EMP-0004', 'Reece', 'Bibaro', 'GR8TECH', 'DEPT-GR8-HR', 'GR8-HRA', 28000],
            ['alexander', 'EMP-0005', 'Alexander', 'Estares', 'GR8TECH', 'DEPT-GR8-FIN', 'GR8-FINA', 32000],
            ['maria', 'EMP-0006', 'Maria', 'Sampalok', 'GR8TECH', 'DEPT-GR8-FIN', 'GR8-FINA', 28000],
            ['reyven', 'EMP-0007', 'Reyven', 'Plaza', 'GR8TECH', 'DEPT-GR8-IT', 'GR8-ITM', 55000],
            ['bulbasaur', 'EMP-0008', 'Bulbasaur', 'Poke', 'GR8TECH', 'DEPT-GR8-IT', 'GR8-SDEV', 36000],
            ['taylor', 'EMP-0009', 'Taylor', 'Swift', 'GR8TECH', 'DEPT-GR8-HR', 'GR8-HRA', 30000],
            ['npi_employee', 'NPI-0001', 'Nora', 'Santos', 'NPI', 'DEPT-NPI-IT', 'NPI-SDEV', 35000],
        ];

        foreach ($employees as [$key, $employeeNumber, $firstName, $lastName, $companyCode, $departmentCode, $positionCode, $salary]) {
            $companyId = Company::query()->where('code', $companyCode)->value('id');
            $department = Department::query()->where('department_id', $departmentCode)->firstOrFail();
            $position = Position::query()->where('code', $positionCode)->firstOrFail();

            $employee = Employee::query()->firstOrNew(['employee_id' => $employeeNumber]);
            if (! $employee->exists) {
                $employee->id = self::IDS[$key];
            }

            $employee->fill([
                'company_id' => $companyId,
                'department_id' => $department->id,
                'position_id' => $position->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => '09170000000',
                'mobile_number' => '09170000000',
                'salary' => $salary,
                'hire_date' => '2025-01-10',
                'employee_status' => 'active',
            ])->save();
        }

        $this->assignDepartmentManagers();
        $this->command?->info('Company-scoped employees and department managers seeded.');
    }

    private function assignDepartmentManagers(): void
    {
        $assignments = [
            'DEPT-GR8-HR' => self::NUMBERS['jerson'],
            'DEPT-GR8-IT' => self::NUMBERS['reyven'],
            'DEPT-GR8-SM' => self::NUMBERS['charlie'],
        ];

        foreach ($assignments as $departmentCode => $managerNumber) {
            $managerId = Employee::query()->where('employee_id', $managerNumber)->value('id');
            Department::query()
                ->where('department_id', $departmentCode)
                ->update(['manager_id' => $managerId]);
        }
    }
}
