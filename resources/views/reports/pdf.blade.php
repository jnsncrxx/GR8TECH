<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Consolidated Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            color: #1e3a8a;
        }
        .header p {
            margin: 4px 0 0;
            color: #666;
            font-size: 11px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 20px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @php
        $activeTypes = isset($types) && is_array($types) ? $types : [$type];
        $allData = isset($reportsData) && is_array($reportsData) ? $reportsData : [$type => $data ?? collect()];
    @endphp

    <div class="header">
        <h1>Consolidated Reports</h1>
        <p>
            @if($startDate && $endDate)
                Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            @else
                Period: All Time
            @endif
            | Generated: {{ date('M d, Y h:i A') }}
        </p>
    </div>

    @foreach($activeTypes as $index => $currentType)
        @php
            $currentData = $allData[$currentType] ?? collect();
            $titles = [
                'attendance' => 'Attendance Report',
                'leave' => 'Leave Report',
                'overtime' => 'Overtime Report',
                'official_business' => 'Official Business Report',
                'payroll' => 'Payroll Report',
            ];
            $titleName = $titles[$currentType] ?? ucfirst(str_replace('_', ' ', $currentType));
        @endphp

        @if($index > 0)
            <div class="page-break"></div>
        @endif

        <div class="section-title">
            {{ $titleName }} ({{ count($currentData) }} Records)
        </div>

        <table>
            <thead>
                <tr>
                    @if($currentType === 'attendance')
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                    @elseif($currentType === 'leave')
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                    @elseif($currentType === 'overtime')
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Hours</th>
                        <th>Reason</th>
                        <th>Status</th>
                    @elseif($currentType === 'payroll')
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Pay Period</th>
                        <th>Gross Pay</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                    @elseif($currentType === 'official_business')
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
                @forelse($currentData as $row)
                    <tr>
                        @if($currentType === 'attendance')
                            <td>{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                            <td>{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                            <td>{{ $row->time_in ? \Carbon\Carbon::parse($row->time_in)->format('h:i A') : '--' }}</td>
                            <td>{{ $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('h:i A') : '--' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $row->status ?? 'pending')) }}</td>
                        @elseif($currentType === 'leave')
                            <td>{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                            <td>{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                            <td>{{ $row->leave_type ? \App\Models\LeaveRequest::labelFor($row->leave_type) : 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->start_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->end_date)->format('M d, Y') }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $row->status ?? 'pending')) }}</td>
                        @elseif($currentType === 'overtime')
                            <td>{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                            <td>{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                            <td>{{ $row->start_time ? \Carbon\Carbon::parse($row->start_time)->format('h:i A') : '--' }}</td>
                            <td>{{ $row->end_time ? \Carbon\Carbon::parse($row->end_time)->format('h:i A') : '--' }}</td>
                            <td>{{ number_format((float)($row->hours ?? 0), 2) }}h</td>
                            <td>{{ $row->reason ?? 'N/A' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $row->status ?? 'pending')) }}</td>
                        @elseif($currentType === 'payroll')
                            <td>{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                            <td>{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->pay_period_start)->format('M d') }} - {{ \Carbon\Carbon::parse($row->pay_period_end)->format('M d, Y') }}</td>
                            <td>₱{{ number_format($row->gross_pay, 2) }}</td>
                            <td>₱{{ number_format($row->deductions, 2) }}</td>
                            <td>₱{{ number_format($row->net_pay, 2) }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $row->status ?? 'pending')) }}</td>
                        @elseif($currentType === 'official_business')
                            <td>{{ optional($row->employee)->full_name ?? 'N/A' }}</td>
                            <td>{{ optional(optional($row->employee)->department)->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                            <td>{{ $row->ob_start_time?->format('h:i A') ?? '--' }} – {{ $row->ob_end_time?->format('h:i A') ?? '--' }}</td>
                            <td>{{ number_format((float) ($row->credited_hours ?? $row->computeCreditedHours()), 2) }}</td>
                            <td>{{ $row->reason }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $row->status ?? 'pending')) }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #6b7280; padding: 12px;">No records found matching criteria for {{ strtolower($titleName) }}.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</body>
</html>
