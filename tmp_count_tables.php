<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = [
    'departments' => App\Models\Department::count(),
    'positions' => App\Models\Position::count(),
    'companies' => App\Models\Company::count(),
];

foreach ($tables as $name => $count) {
    echo "$name=$count\n";
}
