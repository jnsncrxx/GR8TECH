<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Account;

$account = Account::where('email','hasong500@gmail.com')->first();
if (! $account) {
    echo "Account not found\n";
    exit(1);
}
echo "account: {$account->id}\n";
echo "employee_id: " . ($account->employee_id ?: 'null') . "\n";
if ($account->employee) {
    echo "employee exists: {$account->employee->id} {$account->employee->first_name} {$account->employee->last_name}\n";
} else {
    echo "employee relationship is null\n";
}
