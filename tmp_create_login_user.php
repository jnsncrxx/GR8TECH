<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;

$company = App\Models\Company::first();
$department = App\Models\Department::first();
if (! $company || ! $department) {
    echo "Missing required company or department.\n";
    exit(1);
}

$position = App\Models\Position::create([
    'name' => 'Default Position',
    'code' => 'POS-DEFAULT',
    'description' => 'Default position for initial login',
    'level' => 'Entry',
    'department_id' => $department->id,
    'company_id' => $company->id,
    'min_salary' => 0,
    'max_salary' => 0,
]);

$employee = App\Models\Employee::create([
    'employee_id' => 'EMP-DEFAULT',
    'first_name' => 'Admin',
    'last_name' => 'User',
    'department_id' => $department->id,
    'position_id' => $position->id,
    'salary' => 30000,
    'hire_date' => now()->toDateString(),
]);

$account = App\Models\Account::create([
    'employee_id' => $employee->id,
    'email' => 'admin@gr8tech.local',
    'password' => Hash::make('Password123!'),
    'role' => 'admin',
    'is_active' => true,
]);

echo "Created login user:\n";
echo " email: admin@gr8tech.local\n";
echo " password: Password123!\n";
echo " role: admin\n";
