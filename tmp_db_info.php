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
echo 'accounts=' . App\Models\Account::count() . PHP_EOL;
