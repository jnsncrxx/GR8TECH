<?php
require __DIR__ . '/vendor/autoload.php';
use Illuminate\Support\Facades\Hash;

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$emails = ['curt@gmail.com', 'jerson.cerezo.100@gmail.com', 'jersondev03@gmail.com'];
foreach ($emails as $email) {
    $acct = App\Models\Account::where('email', $email)->first();
    echo "Email: {$email}\n";
    if (!$acct) {
        echo "  not found\n";
        continue;
    }
    $stored = $acct->getAttributes()['password'];
    echo "  id={$acct->id}\n";
    echo "  password_raw={$stored}\n";
    echo "  password_prefix=" . substr($stored, 0, min(4, strlen($stored))) . "\n";
    echo "  password_length=" . strlen($stored) . "\n";
    echo "  bcrypt_valid=" . (preg_match('/^\$2[ayb]\$[0-9]{2}\$.{53}$/', $stored) ? 'yes' : 'no') . "\n";
    try {
        $bcrypt = Hash::check('Password123!', $stored) ? 'yes' : 'no';
    } catch (Exception $e) {
        $bcrypt = 'error: ' . $e->getMessage();
    }
    echo "  can_check_bcrypt={$bcrypt}\n";
    echo "  can_check_password_verify=" . (password_verify('Password123!', $stored) ? 'yes' : 'no') . "\n";
    echo "  can_check_crypt=" . (crypt('Password123!', $stored) === $stored ? 'yes' : 'no') . "\n";
    echo "---\n";
}
