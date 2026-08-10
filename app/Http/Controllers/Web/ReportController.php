<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Department;
use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\OfficialBusinessRequest;
use App\Models\OvertimeRequest;
use App\Helpers\CompanyHelper;
use App\Exports\ReportExport;
use App\Exports\MultipleReportsExport;
use App\Exports\ConsolidatedCsvExport;

class ReportController extends Controller
{
    public function index()
    {
        $currentCompany = CompanyHelper::getCurrentCompany();
        
        $departments = Department::forCompany($currentCompany?->id)
            ->orderBy('name')
            ->get();
            
        $employees = Employee::forCompany($currentCompany?->id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $user = auth()->user();

        return view('reports.index', compact('departments', 'employees', 'user'));
    }

    private function scopeEmployeeCompany($query)
    {
        $companyId = CompanyHelper::getCurrentCompanyId();

        return $query->whereHas(
            'employee',
            fn ($employeeQuery) => $employeeQuery->forCompany($companyId)
        );
    }

    private function fetchReportData(string $type, ?string $startDate, ?string $endDate, ?string $departmentId, ?string $employeeId)
    {
        if ($type === 'attendance') {
            $query = $this->scopeEmployeeCompany(AttendanceRecord::with('employee.department'));
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
            return $query->orderBy('date', 'desc')->get();
        } elseif ($type === 'leave') {
            $query = $this->scopeEmployeeCompany(LeaveRequest::with(['employee.department']));
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
            return $query->orderBy('start_date', 'desc')->get();
        } elseif ($type === 'overtime') {
            $query = $this->scopeEmployeeCompany(OvertimeRequest::with(['employee.department']));
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
            return $query->orderBy('date', 'desc')->get();
        } elseif ($type === 'payroll') {
            $query = Payroll::with('employee.department')
                ->where('company_id', CompanyHelper::getCurrentCompanyId());
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
            return $query->orderBy('pay_period_start', 'desc')->get();
        } elseif ($type === 'official_business') {
            $query = $this->scopeEmployeeCompany(
                OfficialBusinessRequest::with(['employee.department', 'reviewer.employee'])
            );
            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            if ($departmentId) {
                $query->whereHas('employee', fn ($employee) => $employee->where('department_id', $departmentId));
            }
            return $query->orderBy('date', 'desc')->get();
        }

        return collect();
    }

    public function generate(Request $request)
    {
        $rawTypes = $request->input('report_types') ?: $request->input('report_type');
        $types = array_filter((array) $rawTypes);

        if (empty($types)) {
            return redirect()->back()->with('error', 'Please select at least one report type to generate.');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $departmentId = $request->input('department_id');
        $employeeId = $request->input('employee_id');

        $reportsData = [];
        foreach ($types as $t) {
            $reportsData[$t] = $this->fetchReportData($t, $startDate, $endDate, $departmentId, $employeeId);
        }

        $user = auth()->user();
        $type = count($types) === 1 ? $types[0] : 'consolidated';

        return view('reports.results', compact('types', 'type', 'reportsData', 'startDate', 'endDate', 'departmentId', 'employeeId', 'user'));
    }
    
    public function export(Request $request)
    {
        $rawTypes = $request->input('report_types') ?: $request->input('report_type');
        $types = array_filter((array) $rawTypes);

        if (empty($types)) {
            return redirect()->back()->with('error', 'Please select at least one report type to export.');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $departmentId = $request->input('department_id');
        $employeeId = $request->input('employee_id');
        $format = $request->input('format', 'csv');

        $reportsData = [];
        foreach ($types as $t) {
            $reportsData[$t] = $this->fetchReportData($t, $startDate, $endDate, $departmentId, $employeeId);
        }

        $isMultiple = count($types) > 1;
        $primaryType = $isMultiple ? 'consolidated' : $types[0];
        $fileName = "{$primaryType}_report_" . date('Y_m_d_His');
        
        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
                'types' => $types,
                'reportsData' => $reportsData,
                'startDate' => $startDate,
                'endDate' => $endDate,
                // Keep backward compatibility props
                'type' => $primaryType,
                'data' => $isMultiple ? collect() : ($reportsData[$primaryType] ?? collect()),
            ]);
            return $pdf->download($fileName . '.pdf');
        } elseif ($format === 'excel') {
            $exportObj = $isMultiple 
                ? new MultipleReportsExport($reportsData) 
                : new ReportExport($reportsData[$primaryType] ?? collect(), $primaryType);

            return \Maatwebsite\Excel\Facades\Excel::download($exportObj, $fileName . '.xlsx');
        } else {
            $exportObj = $isMultiple 
                ? new ConsolidatedCsvExport($reportsData) 
                : new ReportExport($reportsData[$primaryType] ?? collect(), $primaryType);

            return \Maatwebsite\Excel\Facades\Excel::download($exportObj, $fileName . '.csv');
        }
    }
}
