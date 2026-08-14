<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['GR8TECH', 'DEPT-GR8-HR', 'Human Resources', 'Main Office - Floor 2', 2500000],
            ['GR8TECH', 'DEPT-GR8-IT', 'Information Technology', 'Main Office - Floor 3', 3750000],
            ['GR8TECH', 'DEPT-GR8-FIN', 'Finance', 'Main Office - Floor 1', 3000000],
            ['GR8TECH', 'DEPT-GR8-SM', 'Sales and Marketing', 'Main Office - Floor 2', 2000000],
            ['GR8TECH', 'DEPT-GR8-OPS', 'Operations', 'Main Office - Floor 1', 4000000],
            ['NPI', 'DEPT-NPI-HR', 'NPI Human Resources', 'Northstar Office', 1500000],
            ['NPI', 'DEPT-NPI-IT', 'NPI Information Technology', 'Northstar Office', 2000000],
        ];

        foreach ($departments as [$companyCode, $code, $name, $location, $budget]) {
            $companyId = Company::query()->where('code', $companyCode)->value('id');
            Department::query()->updateOrCreate(
                ['company_id' => $companyId, 'name' => $name],
                [
                    'department_id' => $code,
                    'name' => $name,
                    'description' => "Demo {$name} department",
                    'location' => $location,
                    'budget' => $budget,
                    'archived_at' => null,
                ],
            );
        }

        $this->command?->info('Company-scoped departments seeded.');
    }
}
