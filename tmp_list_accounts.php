<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$accounts = App\Models\Account::take(20)->get();
foreach ($accounts as $acct) {
    echo $acct->email . ' | active=' . ($acct->is_active ? 'true' : 'false') . ' | role=' . $acct->role . ' | employee_id=' . ($acct->employee_id ?? 'null') . "\n";
}
