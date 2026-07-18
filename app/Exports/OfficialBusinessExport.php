<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OfficialBusinessExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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
            'Date',
            'Reason',
            'OB Hours',
            'Time In',
            'Time Out',
            'Status',
            'Reviewed By',
            'Admin Reason',
        ];
    }

    public function map($obRequest): array
    {
        $reviewerName = trim(
            ($obRequest->reviewer->employee->first_name ?? '')
            . ' '
            . ($obRequest->reviewer->employee->last_name ?? '')
        );

        return [
            $obRequest->employee->full_name ?? 'N/A',
            $obRequest->employee->department->name ?? 'N/A',
            $obRequest->date?->format('Y-m-d') ?? '',
            $obRequest->reason,
            number_format(
                (float) (
                    $obRequest->credited_hours
                    ?? $obRequest->computeCreditedHours()
                ),
                2,
                '.',
                ''
            ),
            $obRequest->ob_start_time?->format('h:i A') ?? '',
            $obRequest->ob_end_time?->format('h:i A') ?? '',
            ucfirst($obRequest->status),
            $reviewerName !== '' ? $reviewerName : '—',
            $obRequest->rejection_reason ?? '',
        ];
    }
}
