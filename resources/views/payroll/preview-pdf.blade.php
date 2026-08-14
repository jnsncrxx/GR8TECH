<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payroll Preview</title>
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 8px; }
        .watermark { position: fixed; top: 42%; left: 18%; transform: rotate(-28deg); font-size: 54px; font-weight: bold; color: rgba(220, 38, 38, .12); z-index: -1; }
        h1 { margin: 0 0 3px; font-size: 18px; }
        .meta { color: #4b5563; margin-bottom: 10px; }
        .summary { width: 100%; margin: 9px 0; border-collapse: separate; border-spacing: 5px 0; }
        .summary td { border: 1px solid #d1d5db; padding: 6px; }
        .summary strong { display: block; font-size: 11px; }
        table.report { width: 100%; border-collapse: collapse; }
        .report th, .report td { border: 1px solid #d1d5db; padding: 4px 3px; vertical-align: top; }
        .report th { background: #f3f4f6; font-size: 6.5px; text-transform: uppercase; }
        .money { text-align: right; white-space: nowrap; }
        .total { font-weight: bold; background: #f9fafb; }
        .footer { margin-top: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="watermark">DRAFT – NOT FOR PAYMENT</div>
    <h1>Payroll Preview</h1>
    <div class="meta">
        {{ $period['name'] ?? 'Payroll period' }} ·
        {{ \Carbon\Carbon::parse($period['start_date'])->format('M d, Y') }}–{{ \Carbon\Carbon::parse($period['end_date'])->format('M d, Y') }} ·
        Generated {{ $generatedAt->format('M d, Y h:i A') }} PHT
    </div>

    <table class="summary"><tr>
        <td>Employees<strong>{{ count($previewPayrolls) }}</strong></td>
        <td>Gross Pay<strong>₱{{ number_format($summaryData['total_gross_pay'], 2) }}</strong></td>
        <td>Deductions<strong>₱{{ number_format($summaryData['total_deductions'], 2) }}</strong></td>
        <td>Tax<strong>₱{{ number_format($summaryData['total_tax'], 2) }}</strong></td>
        <td>Net Pay<strong>₱{{ number_format($summaryData['total_net_pay'], 2) }}</strong></td>
    </tr></table>

    <table class="report">
        <thead><tr>
            <th>Employee</th><th>Basic</th><th>Allowance</th><th>Paid Leave</th><th>Holiday</th>
            <th>OT</th><th>Night Diff.</th><th>Rest Day</th><th>Bonus / Other</th><th>Gross</th>
            <th>Late</th><th>Undertime</th><th>Absence</th><th>Unpaid Leave</th><th>SSS</th>
            <th>PhilHealth</th><th>Pag-IBIG</th><th>Loan / Other</th><th>Deductions</th><th>Tax</th><th>Net</th>
        </tr></thead>
        <tbody>
        @foreach($previewPayrolls as $payroll)
            @php($e = $payroll['earnings_details'] ?? [])
            @php($d = $payroll['deductions_details'] ?? [])
            <tr>
                <td><strong>{{ $payroll['employee_code'] ?? '' }}</strong><br>{{ $payroll['employee_name'] ?? '' }}</td>
                <td class="money">{{ number_format($e['basic_salary'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($e['allowances'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($e['paid_leave'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($e['holiday_pay'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($e['overtime'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($e['night_differential'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($e['rest_day_premium'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format(($e['bonuses'] ?? 0) + ($e['other'] ?? 0), 2) }}</td>
                <td class="money"><strong>{{ number_format($payroll['gross_pay'] ?? 0, 2) }}</strong></td>
                <td class="money">{{ number_format($d['total_late_deduction'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($d['undertime'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($d['absence'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($d['unpaid_leave'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($d['sss'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($d['philhealth'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format($d['pagibig'] ?? 0, 2) }}</td>
                <td class="money">{{ number_format(($d['loan'] ?? 0) + ($d['other'] ?? 0), 2) }}</td>
                <td class="money"><strong>{{ number_format($payroll['deductions'] ?? 0, 2) }}</strong></td>
                <td class="money">{{ number_format($payroll['tax_amount'] ?? 0, 2) }}</td>
                <td class="money"><strong>{{ number_format($payroll['net_pay'] ?? 0, 2) }}</strong></td>
            </tr>
        @endforeach
        </tbody>
        <tfoot><tr class="total">
            <td>TOTAL</td><td colspan="8"></td>
            <td class="money">{{ number_format($summaryData['total_gross_pay'], 2) }}</td>
            <td colspan="8"></td>
            <td class="money">{{ number_format($summaryData['total_deductions'], 2) }}</td>
            <td class="money">{{ number_format($summaryData['total_tax'], 2) }}</td>
            <td class="money">{{ number_format($summaryData['total_net_pay'], 2) }}</td>
        </tr></tfoot>
    </table>
    <div class="footer">Preview only. Finalize and lock the payroll run before using an official report for payment.</div>
</body>
</html>
