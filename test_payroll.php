<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$period = App\Models\Period::where('start_date', '>=', '2026-10-01')->first();
if (!$period) { echo "No period found\n"; exit; }
$emp = App\Models\Employee::where('employee_id', 'EMP-0004')->first();
if (!$emp) { echo "No employee found\n"; exit; }

$req = new Illuminate\Http\Request();
$req->merge(['period_id' => $period->id, 'employee_ids' => [$emp->id]]);
app('App\Http\Controllers\Web\PayrollController')->generateFromPeriodData($req);

$payroll = App\Models\Payroll::where('employee_id', $emp->id)->where('pay_period_start', $period->start_date->format('Y-m-d'))->first();
print_r($payroll->toArray());
