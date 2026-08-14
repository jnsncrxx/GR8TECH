<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public const DEFAULT_PASSWORD = 'Password123!';

    public function run(): void
    {
        $accounts = [
            ['jerson', 'admin@gr8tech.example', 'admin'],
            ['taylor', 'hr@gr8tech.example', 'hr'],
            ['charlie', 'manager@gr8tech.example', 'manager'],
            ['curt', 'employee@gr8tech.example', 'employee'],
            ['reece', 'reece@gr8tech.example', 'employee'],
            ['alexander', 'alexander@gr8tech.example', 'employee'],
            ['maria', 'maria@gr8tech.example', 'employee'],
            ['reyven', 'reyven@gr8tech.example', 'manager'],
            ['bulbasaur', 'bulbasaur@gr8tech.example', 'employee'],
            ['npi_employee', 'employee@northstar.example', 'employee'],
        ];

        foreach ($accounts as [$employeeKey, $email, $role]) {
            $employeeId = Employee::query()
                ->where('employee_id', EmployeeSeeder::NUMBERS[$employeeKey])
                ->value('id');

            $account = Account::withTrashed()->firstOrNew(['employee_id' => $employeeId]);
            $account->fill([
                'email' => $email,
                'role' => $role,
                'is_active' => true,
            ]);

            if (! $account->exists) {
                $account->password = self::DEFAULT_PASSWORD;
            }

            $account->save();

            if ($account->trashed()) {
                $account->restore();
            }
        }

        $this->command?->warn('Demo login password: '.self::DEFAULT_PASSWORD.' (development only; change before deployment).');
    }
}
