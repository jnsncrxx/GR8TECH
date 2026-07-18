<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Business Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 10px;
        }

        h1 {
            margin-bottom: 4px;
            font-size: 18px;
        }

        .meta {
            margin-bottom: 14px;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 6px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Official Business Report</h1>

    <div class="meta">
        Generated: {{ $generatedAt->format('M d, Y h:i A') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Date</th>
                <th>Reason</th>
                <th>OB Hours</th>
                <th>Time</th>
                <th>Status</th>
                <th>Reviewed By</th>
                <th>Admin Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $obRequest)
                @php
                    $reviewerName = trim(
                        ($obRequest->reviewer->employee->first_name ?? '')
                        . ' '
                        . ($obRequest->reviewer->employee->last_name ?? '')
                    );
                @endphp
                <tr>
                    <td>{{ $obRequest->employee->full_name ?? 'N/A' }}</td>
                    <td>{{ $obRequest->employee->department->name ?? 'N/A' }}</td>
                    <td>{{ $obRequest->date?->format('M d, Y') }}</td>
                    <td>{{ $obRequest->reason }}</td>
                    <td>
                        {{ number_format(
                            (float) (
                                $obRequest->credited_hours
                                ?? $obRequest->computeCreditedHours()
                            ),
                            2
                        ) }}
                    </td>
                    <td>
                        {{ $obRequest->ob_start_time?->format('h:i A') }}
                        -
                        {{ $obRequest->ob_end_time?->format('h:i A') }}
                    </td>
                    <td>{{ ucfirst($obRequest->status) }}</td>
                    <td>{{ $reviewerName !== '' ? $reviewerName : '—' }}</td>
                    <td>{{ $obRequest->rejection_reason ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No Official Business records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
