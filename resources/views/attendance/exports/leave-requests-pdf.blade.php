<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Requests Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 10px; }
        h1 { margin-bottom: 4px; font-size: 18px; }
        .meta { margin-bottom: 14px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px; border: 1px solid #d1d5db; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; }
    </style>
</head>
<body>
    <h1>Leave Requests Report</h1>
    <div class="meta">Generated: {{ $generatedAt->format('M d, Y h:i A') }}</div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Leave Type</th>
                <th>Date Range</th>
                <th>Days</th>
                <th>Status</th>
                <th>Reason</th>
                <th>Reviewed By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $leaveRequest)
                <tr>
                    <td>{{ $leaveRequest->employee->full_name ?? 'N/A' }}</td>
                    <td>{{ $leaveRequest->employee->department->name ?? 'N/A' }}</td>
                    <td>{{ \App\Models\LeaveRequest::labelFor($leaveRequest->leave_type) }}</td>
                    <td>
                        {{ $leaveRequest->start_date?->format('M d, Y') }}
                        &ndash;
                        {{ $leaveRequest->end_date?->format('M d, Y') }}
                    </td>
                    <td>{{ $leaveRequest->days_requested }}</td>
                    <td>{{ ucfirst($leaveRequest->status) }}</td>
                    <td>{{ $leaveRequest->reason }}</td>
                    <td>{{ $leaveRequest->approver?->full_name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No leave records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
