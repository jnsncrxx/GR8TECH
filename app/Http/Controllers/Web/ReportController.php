<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Department;
use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Helpers\CompanyHelper;

class ReportController extends Controller
{
    public function index()
    {
        $currentCompany = CompanyHelper::getCurrentCompany();
        
        $departments = $currentCompany 
            ? Department::forCompany($currentCompany->id)->get() 
            : Department::all();
            
        $employees = $currentCompany 
            ? Employee::forCompany($currentCompany->id)->get() 
            : Employee::all();

        $user = auth()->user();

        return view('reports.index', compact('departments', 'employees', 'user'));
    }

    public function generate(Request $request)
    {
        $type = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $departmentId = $request->input('department_id');
        $employeeId = $request->input('employee_id');

        $data = [];
        
        if ($type === 'attendance') {
            $query = AttendanceRecord::with('employee.department');
            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            if ($departmentId) {
                $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }
            $data = $query->orderBy('date', 'desc')->get();
        } elseif ($type === 'leave') {
            $query = LeaveRequest::with(['employee.department']);
            if ($startDate && $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate]);
            }
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            if ($departmentId) {
                $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }
            $data = $query->orderBy('start_date', 'desc')->get();
        } elseif ($type === 'payroll') {
            $query = Payroll::with('employee.department');
            if ($startDate && $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->where('pay_period_start', '>=', $startDate)
                      ->where('pay_period_end', '<=', $endDate);
                });
            }
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            if ($departmentId) {
                $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }
            $data = $query->orderBy('pay_period_start', 'desc')->get();
        }

        $user = auth()->user();

        return view('reports.results', compact('type', 'data', 'startDate', 'endDate', 'departmentId', 'employeeId', 'user'));
    }
    
    public function export(Request $request)
    {
        $type = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $departmentId = $request->input('department_id');
        $employeeId = $request->input('employee_id');
        $format = $request->input('format', 'csv');

        // Fetch data exactly as in generate()
        $data = [];
        
        if ($type === 'attendance') {
            $query = AttendanceRecord::with('employee.department');
            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            if ($departmentId) {
                $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }
            $data = $query->orderBy('date', 'desc')->get();
        } elseif ($type === 'leave') {
            $query = LeaveRequest::with(['employee.department']);
            if ($startDate && $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate]);
            }
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            if ($departmentId) {
                $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }
            $data = $query->orderBy('start_date', 'desc')->get();
        } elseif ($type === 'payroll') {
            $query = Payroll::with('employee.department');
            if ($startDate && $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->where('pay_period_start', '>=', $startDate)
                      ->where('pay_period_end', '<=', $endDate);
                });
            }
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            if ($departmentId) {
                $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }
            $data = $query->orderBy('pay_period_start', 'desc')->get();
        }

        $fileName = "{$type}_report_" . date('Y_m_d_His');
        
        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact('type', 'data', 'startDate', 'endDate'));
            return $pdf->download($fileName . '.pdf');
        } elseif ($format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportExport($data, $type), $fileName . '.xlsx');
        } else {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportExport($data, $type), $fileName . '.csv');
        }
    }
}
