<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeaveRequestExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private readonly Collection $requests
    ) {}

    public function collection(): Collection
    {
        return $this->requests;
    }

    public function headings(): array
    {
        return [
            'Employee',
            'Department',
            'Leave Type',
            'Start Date',
            'End Date',
            'Days',
            'Status',
            'Reason',
            'Reviewed By',
            'Reviewed At',
        ];
    }

    public function map($leaveRequest): array
    {
        return [
            $leaveRequest->employee->full_name ?? 'N/A',
            $leaveRequest->employee->department->name ?? 'N/A',
            LeaveRequest::labelFor($leaveRequest->leave_type),
            $leaveRequest->start_date?->format('Y-m-d') ?? '',
            $leaveRequest->end_date?->format('Y-m-d') ?? '',
            $leaveRequest->days_requested,
            ucfirst($leaveRequest->status),
            $leaveRequest->reason,
            $leaveRequest->approver?->full_name ?? '',
            $leaveRequest->approved_at?->format('Y-m-d H:i:s') ?? '',
        ];
    }
}
