<?php
require __DIR__ . '/vendor/autoload.php';
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$password = 'Password123!';
$emails = ['curt@gmail.com', 'jerson.cerezo.100@gmail.com'];

foreach ($emails as $email) {
    $account = Account::where('email', $email)->first();
    if (!$account) {
        echo "{$email} not found\n";
        continue;
    }
    $account->password = $password;
    $account->save();
    echo "Updated {$email} (id={$account->id}) to bcrypt password.\n";
}

echo "Done. Login password for the two accounts is: {$password}\n";
