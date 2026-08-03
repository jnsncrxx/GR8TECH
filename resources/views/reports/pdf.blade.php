<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst($type) }} Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($type) }} Report</h1>
        <p>
            @if($startDate && $endDate)
                From: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - To: {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            @else
                All Time
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                @if($type === 'attendance')
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Status</th>
                @elseif($type === 'timekeeping')
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Assigned Schedule</th>
                    <th>Actual Log</th>
                    <th>Hours</th>
                    <th>Exception</th>
                    <th>Severity</th>
                @elseif($type === 'leave')
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                @elseif($type === 'payroll')
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Pay Period</th>
                    <th>Gross Pay</th>
                    <th>Deductions</th>
                    <th>Net Pay</th>
                    <th>Status</th>
                @elseif($type === 'official_business')
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>OB Schedule</th>
                    <th>Credited Hours</th>
                    <th>Reason</th>
                    <th>Status</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    @if($type === 'attendance')
                        <td>{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                        <td>{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                        <td>{{ $row->time_in ? \Carbon\Carbon::parse($row->time_in)->format('h:i A') : '--' }}</td>
                        <td>{{ $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('h:i A') : '--' }}</td>
                        <td>{{ ucfirst($row->status) }}</td>
                    @elseif($type === 'timekeeping')
                        <td>{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                        <td>{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                        <td>
                            @if($row->assignedSchedule?->time_in && $row->assignedSchedule?->time_out)
                                {{ \Carbon\Carbon::parse($row->assignedSchedule->time_in)->format('h:i A') }} - {{ \Carbon\Carbon::parse($row->assignedSchedule->time_out)->format('h:i A') }}
                            @else
                                {{ $row->assignedSchedule?->status ?? '--' }}
                            @endif
                        </td>
                        <td>{{ $row->time_in ? \Carbon\Carbon::parse($row->time_in)->format('h:i A') : '--' }} - {{ $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('h:i A') : '--' }}</td>
                        <td>{{ number_format((float) $row->display_worked_hours, 2) }}</td>
                        <td>{{ $row->exception_label }}</td>
                        <td>{{ ucfirst($row->exception_severity) }}</td>
                    @elseif($type === 'leave')
                        <td>{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                        <td>{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                        <td>{{ $row->leave_type ? \App\Models\LeaveRequest::labelFor($row->leave_type) : 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->start_date)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->end_date)->format('M d, Y') }}</td>
                        <td>{{ ucfirst($row->status) }}</td>
                    @elseif($type === 'payroll')
                        <td>{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                        <td>{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->pay_period_start)->format('M d') }} - {{ \Carbon\Carbon::parse($row->pay_period_end)->format('M d, Y') }}</td>
                        <td>{{ number_format($row->gross_pay, 2) }}</td>
                        <td>{{ number_format($row->deductions, 2) }}</td>
                        <td>{{ number_format($row->net_pay, 2) }}</td>
                        <td>{{ ucfirst($row->status) }}</td>
                    @elseif($type === 'official_business')
                        <td>{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                        <td>{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                        <td>{{ $row->ob_start_time?->format('h:i A') ?? '--' }} – {{ $row->ob_end_time?->format('h:i A') ?? '--' }}</td>
                        <td>{{ number_format((float) ($row->credited_hours ?? $row->computeCreditedHours()), 2) }}</td>
                        <td>{{ $row->reason }}</td>
                        <td>{{ ucfirst($row->status) }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
