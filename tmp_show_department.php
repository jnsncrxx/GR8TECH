<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$department = App\Models\Department::first();
if (! $department) {
    echo "No department found.\n";
    exit(1);
}

echo "department_id=" . $department->id . "\n";
echo "department_code=" . ($department->department_id ?? 'NULL') . "\n";
echo "department_name=" . $department->name . "\n";
