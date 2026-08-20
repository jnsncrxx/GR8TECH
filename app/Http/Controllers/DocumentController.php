<?php

namespace App\Http\Controllers;

use App\Helpers\CompanyHelper;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $currentCompany = CompanyHelper::getCurrentCompany();
        $currentCompanyId = $currentCompany?->id;

        $departments = Department::query()
            ->when($currentCompanyId, fn ($query) => $query->forCompany($currentCompanyId))
            ->active()
            ->orderBy('name')
            ->get();

        $employees = Employee::query()
            ->with(['department', 'position'])
            ->withCount('documents')
            ->when($currentCompanyId, fn ($query) => $query->forCompany($currentCompanyId))
            ->when($request->filled('department_id'), function ($query) use ($request, $currentCompanyId) {
                $query->whereHas('department', function ($departmentQuery) use ($request, $currentCompanyId) {
                    $departmentQuery->whereKey($request->input('department_id'))
                        ->when($currentCompanyId, fn ($scopedQuery) => $scopedQuery->forCompany($currentCompanyId));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where(
                'employee_status',
                str_replace('-', '_', $request->string('status')->toString())
            ))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search')->toString());
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('employee_id', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view('documents.index', [
            'user' => Auth::user(),
            'employees' => $employees,
            'departments' => $departments,
            'companies' => CompanyHelper::getAvailableCompanies(),
            'currentCompany' => $currentCompany,
            'currentCompanyId' => $currentCompanyId,
        ]);
    }

    public function switchCompany(Request $request)
    {
        $validated = $request->validate([
            'company_id' => [
                'required',
                'uuid',
                Rule::exists('companies', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
        ]);

        CompanyHelper::setCurrentCompany($validated['company_id']);

        return redirect()->route('documents.index')->with('success', 'Company switched successfully.');
    }

    public function export(Request $request)
    {
        $type = strtolower($request->input('type', $request->input('format', 'csv')));
        $scope = $request->input('scope', 'filtered');
        $currentCompany = CompanyHelper::getCurrentCompany();
        $currentCompanyId = $currentCompany?->id;

        $query = Employee::query()
            ->with(['department', 'position'])
            ->when($currentCompanyId, fn ($q) => $q->forCompany($currentCompanyId));

        if ($scope === 'filtered') {
            if ($request->filled('department_id')) {
                $query->whereHas('department', function ($departmentQuery) use ($request, $currentCompanyId) {
                    $departmentQuery->whereKey($request->input('department_id'))
                        ->when($currentCompanyId, fn ($scopedQuery) => $scopedQuery->forCompany($currentCompanyId));
                });
            }
            if ($request->filled('status')) {
                $query->where('employee_status', str_replace('-', '_', $request->string('status')->toString()));
            }
            if ($request->filled('search')) {
                $search = trim($request->string('search')->toString());
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('employee_id', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            }
        }

        $employees = $query->orderBy('last_name')->orderBy('first_name')->get();

        if ($type === 'pdf') {
            $departmentName = 'All Departments';
            if ($request->filled('department_id')) {
                $dept = Department::find($request->input('department_id'));
                if ($dept) {
                    $departmentName = $dept->name;
                }
            }

            $filters = [
                'export_date' => date('F d, Y H:i'),
                'total_employees' => $employees->count(),
                'department' => $scope === 'filtered' ? $departmentName : 'All Departments',
                'status' => $scope === 'filtered' && $request->filled('status') ? ucfirst($request->input('status')) : 'All Statuses',
                'search' => $scope === 'filtered' && $request->filled('search') ? $request->input('search') : 'None',
            ];

            $pdfData = $employees->map(function ($emp) {
                return [
                    'employee_id' => $emp->employee_id,
                    'full_name' => $emp->first_name . ' ' . $emp->last_name,
                    'department' => $emp->department ? $emp->department->name : 'N/A',
                    'position' => $emp->position ? $emp->position->name : 'N/A',
                    'status' => ucfirst(str_replace('_', ' ', $emp->employee_status ?? 'Active')),
                    'email' => $emp->account ? $emp->account->email : ($emp->email ?? 'N/A'),
                    'phone' => $emp->mobile_number ?? $emp->phone ?? 'N/A',
                    'salary' => number_format($emp->salary, 2),
                    'hire_date' => $emp->hire_date ? date('M d, Y', strtotime($emp->hire_date)) : 'N/A',
                ];
            });

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('documents.exports.pdf', [
                'employees' => $pdfData,
                'filters' => $filters,
            ]);

            $scopeSuffix = $scope === 'all' ? 'all' : 'filtered';
            $pdfFilename = "employee_documents_{$scopeSuffix}_" . date('Y-m-d_H-i-s') . ".pdf";

            return $pdf->download($pdfFilename);
        }

        $scopeSuffix = $scope === 'all' ? 'all' : 'filtered';
        $filename = "employees_export_{$scopeSuffix}_" . date('Y-m-d_H-i-s') . ".csv";

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
                $row['Position']  = $employee->position?->name ?? '';
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
        $employee = Employee::query()
            ->with(['department', 'position', 'company', 'account'])
            ->withCount('documents')
            ->forCompany(CompanyHelper::getCurrentCompanyId())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'html' => view('documents.partials.employee-details', compact('employee'))->render(),
        ]);
    }
}
