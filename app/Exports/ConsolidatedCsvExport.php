<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ConsolidatedCsvExport implements FromCollection
{
    protected array $reportsData;

    public function __construct(array $reportsData)
    {
        $this->reportsData = $reportsData;
    }

    public function collection()
    {
        $rows = [];

        foreach ($this->reportsData as $type => $data) {
            $singleExport = new ReportExport($data, $type);
            
            // Section Header
            $rows[] = ['=== ' . strtoupper($singleExport->title()) . ' ==='];
            
            // Headings
            $headings = $singleExport->headings();
            if (!empty($headings)) {
                $rows[] = $headings;
            }

            // Rows
            if (count($data) > 0) {
                foreach ($data as $item) {
                    $rows[] = $singleExport->map($item);
                }
            } else {
                $rows[] = ['No records found matching criteria.'];
            }

            // Blank line between modules
            $rows[] = [''];
        }

        return collect($rows);
    }
}
