<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payroll = App\Models\Payroll::where('id', '45f99028-5dbd-42ba-9d52-ffb27e916c88')->first(); // This is the September record with 3660.48 deductions
if (!$payroll) {
    echo "Payroll not found";
    exit;
}

$modalData = [
    'employee_name' => $payroll->employee->full_name ?? 'N/A',
    'employee_code' => $payroll->employee->employee_id ?? 'N/A',
    'department' => $payroll->employee->department->name ?? 'N/A',
    'position' => $payroll->employee->position?->name ?? 'N/A',
    'basic_salary' => $payroll->basic_salary,
    'overtime_pay' => $payroll->overtime_pay,
    'allowances' => $payroll->allowances,
    'total_earnings' => $payroll->gross_pay ?? ($payroll->basic_salary + $payroll->overtime_pay + $payroll->allowances),
    'sss' => $payroll->sss,
    'phic' => $payroll->phic,
    'pagibig' => $payroll->hdmf,
    'tax' => $payroll->tax_amount,
    'unpaid_leave_deduction' => $payroll->unpaid_leave_deduction,
    'total_deductions' => $payroll->deductions,
    'net_pay' => $payroll->net_pay
];

echo json_encode($modalData, JSON_PRETTY_PRINT);
