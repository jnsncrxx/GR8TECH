<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Account;
use App\Models\Department;
use App\Models\Employee;

$accountEmail = 'hasong500@gmail.com';
$account = Account::where('email', $accountEmail)->first();
if (! $account) {
    echo "Account not found: {$accountEmail}\n";
    exit(1);
}

$dept = Department::where('name', 'Human Resources')->first();
if (! $dept) {
    echo "Human Resources department not found\n";
    exit(1);
}

if ($account->employee_id) {
    echo "Account already linked to employee: {$account->employee_id}\n";
    exit(0);
}

$employee = Employee::create([
    'employee_id' => 'EMP-0011',
    'first_name' => 'Steve',
    'last_name' => 'Hasong',
    'phone' => '09000000000',
    'department_id' => $dept->id,
    'position_id' => null,
    'created_by' => $account->id,
    'salary' => 25000.00,
    'hire_date' => now()->toDateString(),
]);

$account->employee_id = $employee->id;
$account->save();

echo "Created employee {$employee->id} and linked to account {$account->id}\n";
