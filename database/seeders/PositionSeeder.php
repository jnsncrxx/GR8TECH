<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['GR8TECH', 'DEPT-GR8-HR', 'GR8TECH Human Resources Manager', 'GR8-HRM', 'Manager', 45000, 70000],
            ['GR8TECH', 'DEPT-GR8-HR', 'GR8TECH Human Resources Associate', 'GR8-HRA', 'Associate', 24000, 38000],
            ['GR8TECH', 'DEPT-GR8-IT', 'GR8TECH Software Developer', 'GR8-SDEV', 'Associate', 30000, 60000],
            ['GR8TECH', 'DEPT-GR8-IT', 'GR8TECH Information Technology Manager', 'GR8-ITM', 'Manager', 50000, 80000],
            ['GR8TECH', 'DEPT-GR8-FIN', 'GR8TECH Finance Associate', 'GR8-FINA', 'Associate', 26000, 42000],
            ['GR8TECH', 'DEPT-GR8-SM', 'GR8TECH Sales and Marketing Manager', 'GR8-SMM', 'Manager', 45000, 70000],
            ['GR8TECH', 'DEPT-GR8-OPS', 'GR8TECH Operations Associate', 'GR8-OPSA', 'Associate', 26000, 42000],
            ['NPI', 'DEPT-NPI-HR', 'NPI Human Resources Manager', 'NPI-HRM', 'Manager', 45000, 70000],
            ['NPI', 'DEPT-NPI-IT', 'NPI Software Developer', 'NPI-SDEV', 'Associate', 30000, 60000],
        ];

        foreach ($positions as [$companyCode, $departmentCode, $name, $code, $level, $minimum, $maximum]) {
            $companyId = Company::query()->where('code', $companyCode)->value('id');
            $department = Department::query()->where('department_id', $departmentCode)->firstOrFail();

            Position::query()->updateOrCreate(
                ['company_id' => $companyId, 'name' => $name],
                [
                    'code' => $code,
                    'department_id' => $department->id,
                    'name' => $name,
                    'description' => "Demo {$name} position",
                    'level' => $level,
                    'min_salary' => $minimum,
                    'max_salary' => $maximum,
                    'is_active' => true,
                    'requirements' => ['Relevant education or equivalent experience'],
                    'responsibilities' => ['Perform assigned role responsibilities'],
                ],
            );
        }

        $this->command?->info('Company-scoped positions seeded.');
    }
}
