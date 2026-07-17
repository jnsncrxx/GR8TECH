<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$emp = App\Models\Employee::where('employee_id', 'EMP-0004')->first();
$period = ['start_date' => '2026-08-31', 'end_date' => '2026-09-29']; 
$service = app('App\Services\PayrollGenerationService');
$reflection = new \ReflectionClass($service);
$method = $reflection->getMethod('calculateApprovedLeaveData');
$method->setAccessible(true);
$leaveData = $method->invoke($service, $emp, $period);
print_r($leaveData);

$comprehensiveDataMethod = $reflection->getMethod('calculateAbsenceDeductions');
$comprehensiveDataMethod->setAccessible(true);
// I can't easily mock attendance records here, so just checking leave data for now.
