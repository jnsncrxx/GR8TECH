<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleReportsExport implements WithMultipleSheets
{
    protected array $reportsData;

    public function __construct(array $reportsData)
    {
        $this->reportsData = $reportsData;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->reportsData as $type => $data) {
            $sheets[] = new ReportExport($data, $type);
        }

        return $sheets;
    }
}
