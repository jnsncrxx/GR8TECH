<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        if ($this->type === 'attendance') {
            return [
                'Employee Name',
                'Department',
                'Date',
                'Time In',
                'Time Out',
                'Status',
            ];
        } elseif ($this->type === 'leave') {
            return [
                'Employee Name',
                'Department',
                'Leave Type',
                'Start Date',
                'End Date',
                'Status',
            ];
        } elseif ($this->type === 'payroll') {
            return [
                'Employee Name',
                'Department',
                'Pay Period Start',
                'Pay Period End',
                'Gross Pay',
                'Deductions',
                'Net Pay',
                'Status',
            ];
        }
        return [];
    }

    public function map($row): array
    {
        if ($this->type === 'attendance') {
            return [
                optional($row->employee)->full_name ?? 'N/A',
                optional(optional($row->employee)->department)->name ?? 'N/A',
                Carbon::parse($row->date)->format('M d, Y'),
                $row->time_in ? Carbon::parse($row->time_in)->format('h:i A') : '--',
                $row->time_out ? Carbon::parse($row->time_out)->format('h:i A') : '--',
                ucfirst($row->status),
            ];
        } elseif ($this->type === 'leave') {
            return [
                optional($row->employee)->full_name ?? 'N/A',
                optional(optional($row->employee)->department)->name ?? 'N/A',
                $row->leave_type ? \App\Models\LeaveRequest::labelFor($row->leave_type) : 'N/A',
                Carbon::parse($row->start_date)->format('M d, Y'),
                Carbon::parse($row->end_date)->format('M d, Y'),
                ucfirst($row->status),
            ];
        } elseif ($this->type === 'payroll') {
            return [
                optional($row->employee)->full_name ?? 'N/A',
                optional(optional($row->employee)->department)->name ?? 'N/A',
                Carbon::parse($row->pay_period_start)->format('M d, Y'),
                Carbon::parse($row->pay_period_end)->format('M d, Y'),
                number_format($row->gross_pay, 2),
                number_format($row->deductions, 2),
                number_format($row->net_pay, 2),
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
