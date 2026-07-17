<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Helpers\CompanyHelper;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $currentCompany = CompanyHelper::getCurrentCompany();

        $query = Department::withCount('employees')->with('supervisor')->active();

        // Filter by current company if set
        if ($currentCompany) {
            $query->forCompany($currentCompany->id);
        }

        $departments = $query->when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%');
            })
            ->paginate(15);

        $user = auth()->user();
        return view('departments.index', compact('departments', 'user'));
    }

    public function create()
    {
        $user = auth()->user();
        return view('departments.form', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'description' => 'nullable|string|max:1000',
            'budget' => 'nullable|numeric|min:0',
        ]);

        $currentCompany = CompanyHelper::getCurrentCompany();

        $departmentData = $request->all();
        if ($currentCompany) {
            $departmentData['company_id'] = $currentCompany->id;
        }

        Department::create($departmentData);

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['employees.position', 'supervisor']);
        $user = auth()->user();
        return view('departments.show', compact('department', 'user'));
    }

    public function edit(Department $department)
    {
        $user = auth()->user();
        return view('departments.form', compact('department', 'user'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string|max:1000',
            'budget' => 'nullable|numeric|min:0',
        ]);

        $department->update($request->validated());

        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->archived_at = \Illuminate\Support\Carbon::now();
        $department->save();

        return redirect()->route('departments.index')
            ->with('success', 'Department removed and archived successfully.');
    }

    public function employees(Department $department)
    {
        $currentCompany = CompanyHelper::getCurrentCompany();

        $query = $department->employees()->with('account');

        // Filter by current company if set
        if ($currentCompany) {
            $query->forCompany($currentCompany->id);
        }

        $employees = $query->when(request('search'), function ($query) {
                $query->where('first_name', 'like', '%' . request('search') . '%')
                      ->orWhere('last_name', 'like', '%' . request('search') . '%');
            })
            ->paginate(15);

        $user = auth()->user();
        return view('departments.employees', compact('department', 'employees', 'user'));
    }

    public function updateSupervisor(Request $request, Department $department)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|uuid|exists:employees,id',
        ]);

        $employeeId = $validated['employee_id'] ?? null;

        if ($employeeId && !$department->employees()->where('id', $employeeId)->exists()) {
            return redirect()->back()
                ->with('error', 'The selected employee does not belong to this department.');
        }

        $department->supervisor_id = $employeeId;
        $department->save();

        return redirect()->back()
            ->with('success', $employeeId ? 'Department supervisor updated successfully.' : 'Department supervisor removed successfully.');
    }
}
