<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Position;
use App\Models\Department;

$positions = Position::all();
$dept = Department::where('name', 'Human Resources')->first();
foreach ($positions as $p) {
    echo "position: {$p->id} name={$p->name} department_id={$p->department_id}\n";
}
if ($dept) {
    echo "department: {$dept->id}\n";
} else {
    echo "department: none\n";
}
