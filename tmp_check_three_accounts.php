<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$emails = [
    'jersondevo3@gmail.com',
    'jerson.cerezo.100@gmail.com',
    'curt@gmail.com',
];

foreach ($emails as $email) {
    $acct = App\Models\Account::where('email', $email)->first();
    if (! $acct) {
        echo "$email => MISSING\n";
        continue;
    }
    echo "$email => FOUND | active=" . ($acct->is_active ? 'true' : 'false') . " | role=" . ($acct->role ?? 'null') . " | employee_id=" . ($acct->employee_id ?? 'null') . "\n";
}
