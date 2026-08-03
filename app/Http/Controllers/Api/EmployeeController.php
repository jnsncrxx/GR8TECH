<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Helpers\CompanyHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('department')
            ->forCompany(CompanyHelper::getCurrentCompanyId())
            ->paginate(15);
        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'phone' => 'required|string|max:20',
            'department_id' => [
                'required',
                Rule::exists('departments', 'id')->where(
                    fn ($query) => $query->where('company_id', CompanyHelper::getCurrentCompanyId())
                ),
            ],
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
        ]);

        $employee = Employee::create(array_merge($request->validated(), [
            'company_id' => CompanyHelper::getCurrentCompanyId(),
        ]));

        return response()->json($employee, 201);
    }

    public function show(Employee $employee)
    {
        $this->authorizeCompany($employee);
        $employee->load('department', 'payrolls');
        return response()->json($employee);
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorizeCompany($employee);
        $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:employees,email,' . $employee->id,
            'phone' => 'sometimes|required|string|max:20',
            'department_id' => [
                'sometimes',
                'required',
                Rule::exists('departments', 'id')->where(
                    fn ($query) => $query->where('company_id', CompanyHelper::getCurrentCompanyId())
                ),
            ],
            'position' => 'sometimes|required|string|max:255',
            'salary' => 'sometimes|required|numeric|min:0',
            'hire_date' => 'sometimes|required|date',
        ]);

        $employee->update($request->validated());

        return response()->json($employee);
    }

    public function destroy(Employee $employee)
    {
        $this->authorizeCompany($employee);
        $employee->delete();

        return response()->json(null, 204);
    }

    public function payroll(Employee $employee)
    {
        $this->authorizeCompany($employee);
        $payrolls = $employee->payrolls()->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($payrolls);
    }

    private function authorizeCompany(Employee $employee): void
    {
        abort_unless($employee->company_id === CompanyHelper::getCurrentCompanyId(), 404);
    }
}
