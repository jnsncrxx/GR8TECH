<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$emails = ['jerson.cerezo.100@gmail.com', 'curt@gmail.com', 'jersondev03@gmail.com'];
foreach ($emails as $email) {
    $acct = App\Models\Account::where('email', $email)->first();
    echo "Email: {$email}\n";
    if ($acct) {
        echo "  id={$acct->id}\n";
        echo "  email={$acct->email}\n";
        echo "  role=" . ($acct->role ?? 'null') . "\n";
        echo "  is_active=" . ($acct->is_active ? 'true' : 'false') . "\n";
        echo "  employee_id=" . ($acct->employee_id ?? 'null') . "\n";
        echo "  full_name=" . ($acct->employee ? ($acct->employee->full_name ?? ($acct->employee->first_name . ' ' . $acct->employee->last_name)) : 'null') . "\n";
        echo "  created_at=" . ($acct->created_at ? $acct->created_at->toDateTimeString() : 'null') . "\n";
        echo "  updated_at=" . ($acct->updated_at ? $acct->updated_at->toDateTimeString() : 'null') . "\n";
        echo "  password_hash=" . ($acct->password ? '[set]' : '[empty]') . "\n";
    } else {
        echo "  not found\n";
    }
    echo "---\n";
}

$matches = App\Models\Account::where('email', 'like', '%jerson%')
    ->orWhere('email', 'like', '%devo%')
    ->orWhere('email', 'like', '%jersondevo%')
    ->orWhere('email', 'like', '%jersond%')
    ->get();

echo "near matches for jerson*/devo*:\n";
foreach ($matches as $acct) {
    echo "  {$acct->email} (id={$acct->id})\n";
}
