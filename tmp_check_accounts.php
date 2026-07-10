<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$config = $app['config']->get('database.connections.' . $app['config']->get('database.default'));
echo 'connection=' . $app['config']->get('database.default') . PHP_EOL;
echo 'driver=' . $config['driver'] . PHP_EOL;
echo 'database=' . $config['database'] . PHP_EOL;
echo 'host=' . $config['host'] . PHP_EOL;
echo 'username=' . $config['username'] . PHP_EOL;
echo 'password=' . ($config['password'] === '' ? '(empty)' : '(set)') . PHP_EOL;
echo 'accounts_total=' . App\Models\Account::count() . PHP_EOL;
$emails = ['jersondevo3@gmail.com', 'jerson.cerezo.100@gmail.com', 'curt@gmail.com'];
foreach ($emails as $email) {
    $account = App\Models\Account::where('email', $email)->first();
    echo $email . ' => ' . ($account ? 'FOUND (id='.$account->id.')' : 'MISSING') . PHP_EOL;
}
