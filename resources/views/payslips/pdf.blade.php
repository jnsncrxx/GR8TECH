<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Payslip #{{ $payroll->id ?? 'N/A' }}</title>
<style>
  @page { margin: 6px; size: 100mm 160mm; }
  * { box-sizing: border-box; }
  body {
    font-family: 'Courier New', DejaVu Sans Mono, monospace;
    font-size: 8px;
    line-height: 1.3;
    color: #1a1a1a;
    margin: 0;
    padding: 0;
  }
  .receipt {
    width: 100%;
    margin: 0 auto;
    background: #ffffff;
    padding: 8px 10px;
  }

  /* Header */
  .bank-header { text-align: center; margin-bottom: 4px; }
  .bank-name { font-size: 11px; font-weight: bold; letter-spacing: 1px; margin: 0; }
  .doc-title { font-size: 8px; letter-spacing: 2px; color: #444; margin-top: 1px; text-transform: uppercase; }

  .divider { border: none; border-top: 1px dashed #999; margin: 5px 0; }
  .divider-solid { border: none; border-top: 1.5px solid #1a1a1a; margin: 5px 0; }

  /* Meta / employee info */
  .meta-row { display: flex; justify-content: space-between; margin: 1.5px 0; }
  .meta-label { color: #555; }
  .meta-value { font-weight: bold; text-align: right; }

  .section-title {
    font-size: 8px;
    font-weight: bold;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #555;
    margin: 5px 0 2px 0;
  }

  /* Line items */
  table { width: 100%; border-collapse: collapse; }
  td { padding: 1px 0; vertical-align: top; }
  .desc { text-align: left; }
  .amt { text-align: right; white-space: nowrap; }

  .total-row td {
    padding-top: 3px;
    border-top: 1px dashed #999;
    font-weight: bold;
  }

  .net-box {
    margin-top: 5px;
    padding: 6px;
    background: #1a1a1a;
    color: #ffffff;
    text-align: center;
  }
  .net-label { font-size: 8px; letter-spacing: 1px; text-transform: uppercase; opacity: 0.8; }
  .net-amount { font-size: 14px; font-weight: bold; margin-top: 1px; }

  .footer { text-align: center; margin-top: 6px; color: #777; font-size: 7px; }
  .footer .stars { letter-spacing: 2px; margin-bottom: 2px; }
</style>
</head>
<body>
<div class="receipt">

  <div class="bank-header">
    <p class="bank-name">PAYSLIP</p>
    <div class="doc-title">Employee Earnings Statement</div>
  </div>

  <hr class="divider-solid">

  <div class="meta-row">
    <span class="meta-label">Employee</span>
    <span class="meta-value">{{ $employee->name ?? ($employee->first_name . ' ' . $employee->last_name ?? 'Unknown') }}</span>
  </div>
  <div class="meta-row">
    <span class="meta-label">Pay Period</span>
    <span class="meta-value">{{ $payroll->pay_period_start }} to {{ $payroll->pay_period_end }}</span>
  </div>
  <div class="meta-row">
    <span class="meta-label">Payslip ID</span>
    <span class="meta-value">{{ $payroll->id ?? 'N/A' }}</span>
  </div>

  <hr class="divider">

  <div class="section-title">Earnings</div>
  <table>
    <tr><td class="desc">Basic Salary</td><td class="amt">{{ number_format($payroll->basic_salary ?? 0, 2) }}</td></tr>
    <tr><td class="desc">Holiday Basic Pay</td><td class="amt">{{ number_format($payroll->holiday_basic_pay ?? 0, 2) }}</td></tr>
    <tr><td class="desc">Holiday Premium</td><td class="amt">{{ number_format($payroll->holiday_premium ?? 0, 2) }}</td></tr>
    <tr><td class="desc">Special Holiday Premium</td><td class="amt">{{ number_format($payroll->special_holiday_premium ?? 0, 2) }}</td></tr>
    <tr><td class="desc">Overtime Pay</td><td class="amt">{{ number_format($payroll->overtime_pay ?? 0, 2) }}</td></tr>
    <tr><td class="desc">Night Differential</td><td class="amt">{{ number_format($payroll->night_differential_pay ?? 0, 2) }}</td></tr>
    <tr><td class="desc">Rest Day Premium</td><td class="amt">{{ number_format($payroll->rest_day_premium_pay ?? 0, 2) }}</td></tr>
    <tr><td class="desc">Bonuses</td><td class="amt">{{ number_format($payroll->bonuses ?? 0, 2) }}</td></tr>
    <tr class="total-row"><td class="desc">Total Gross</td><td class="amt">{{ number_format($payroll->gross_pay ?? 0, 2) }}</td></tr>
  </table>

  <div class="section-title">Deductions</div>
  <table>
    <tr><td class="desc">Deductions</td><td class="amt">{{ number_format($payroll->deductions ?? 0, 2) }}</td></tr>
    <tr><td class="desc">Tax</td><td class="amt">{{ number_format($payroll->tax_amount ?? 0, 2) }}</td></tr>
  </table>

  <div class="net-box">
    <div class="net-label">Net Pay</div>
    <div class="net-amount">PHP {{ number_format($payroll->net_pay ?? 0, 2) }}</div>
  </div>

  <div class="footer">
    <div class="stars">* * * * * * * * * * * * *</div>
    Generated at {{ now()->toDateTimeString() }}<br>
    This is a system-generated document.
  </div>

</div>
</body>
</html>