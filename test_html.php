<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
Auth::login($user);
$request = Illuminate\Http\Request::create('/payroll', 'GET');
$controller = app(\App\Http\Controllers\Web\PayrollController::class);
$response = $controller->index($request);
$html = $response->render();

preg_match('/Curt Vincent Guiling.*?openPayrollModal\([^,]+,\s*\'([^\']+)\'/s', $html, $matches);
if (isset($matches[1])) {
    $json = base64_decode($matches[1]);
    echo $json;
} else {
    echo "Not found";
}
