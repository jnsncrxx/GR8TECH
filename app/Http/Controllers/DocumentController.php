<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        return view('documents.index', ['user' => Auth::user()]);
    }

    public function switchCompany(Request $request)
    {
        return response()->json(['message' => 'Switch company not yet implemented'], 501);
    }

    public function export(Request $request)
    {
        $employees = \App\Models\Employee::all();
        $filename = "employees_export_" . date('Y-m-d_H-i-s') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Employee ID', 'First Name', 'Last Name', 'Department', 'Position', 'Salary', 'Hire Date'];

        $callback = function() use($employees, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($employees as $employee) {
                $row['ID']  = $employee->id;
                $row['Employee ID']    = $employee->employee_id;
                $row['First Name']    = $employee->first_name;
                $row['Last Name']  = $employee->last_name;
                $row['Department']  = $employee->department ? $employee->department->name : '';
                $row['Position']  = $employee->positionModel ? $employee->positionModel->name : '';
                $row['Salary']  = $employee->salary;
                $row['Hire Date']  = $employee->hire_date;

                fputcsv($file, array($row['ID'], $row['Employee ID'], $row['First Name'], $row['Last Name'], $row['Department'], $row['Position'], $row['Salary'], $row['Hire Date']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportEmployee(Request $request, $id)
    {
        return response()->json(['message' => 'Export employee not yet implemented'], 501);
    }

    public function getEmployeeDetails(Request $request, $id)
    {
        return response()->json(['employee' => null]);
    }
}
