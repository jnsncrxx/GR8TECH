<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class HRISReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $type;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        if ($this->type === 'absences') {
            return ['Employee Name', 'Department', 'Date'];
        } elseif ($this->type === 'overtime') {
            return ['Employee Name', 'Department', 'Total Requests', 'Total Hours', 'Average Hours'];
        } elseif ($this->type === 'employee_list') {
            return ['Employee Name', 'Department', 'Position', 'Status', 'Employee Code'];
        } elseif ($this->type === 'leave_balance') {
            return ['Employee Name', 'Leave Type', 'Earned', 'Used', 'Pending', 'Remaining'];
        } elseif ($this->type === 'filings') {
            return ['Employee Name', 'Filing Type', 'Date', 'Status'];
        }
        return [];
    }

    public function map($row): array
    {
        if ($this->type === 'absences') {
            return [
                optional($row->employee)->full_name ?? 'N/A',
                optional(optional($row->employee)->department)->name ?? 'N/A',
                Carbon::parse($row->date)->format('M d, Y'),
            ];
        } elseif ($this->type === 'overtime') {
            // $row here is an array from employee_overtime
            $employee = $row['employee'];
            return [
                $employee->full_name ?? 'N/A',
                optional($employee->department)->name ?? 'N/A',
                $row['total_requests'],
                $row['total_hours'],
                $row['total_requests'] > 0 ? number_format($row['total_hours'] / $row['total_requests'], 2) : 0,
            ];
        } elseif ($this->type === 'employee_list') {
            return [
                $row->full_name ?? 'N/A',
                optional($row->department)->name ?? 'N/A',
                optional($row->position)->title ?? 'N/A',
                ucfirst($row->employment_status ?? 'Active'),
                $row->employee_code ?? 'N/A',
            ];
        } elseif ($this->type === 'leave_balance') {
            return [
                optional($row->employee)->full_name ?? 'N/A',
                optional($row->leaveType)->name ?? 'N/A',
                $row->earned_credits ?? 0,
                $row->used_credits ?? 0,
                $row->pending_credits ?? 0,
                $row->remaining_credits ?? 0,
            ];
        } elseif ($this->type === 'filings') {
            return [
                optional($row->employee)->full_name ?? 'N/A',
                $row->type,
                Carbon::parse($row->date)->format('M d, Y'),
                ucfirst($row->status),
            ];
        }
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
