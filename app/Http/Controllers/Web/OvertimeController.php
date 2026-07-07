<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $summary = ["total" => 0, "approved" => 0, "pending" => 0, "total_hours" => 0];
        $overtimeRequests = \App\Models\AttendanceRecord::where("id", -1)->paginate(10);
        $departments = \App\Models\Department::all();
        $employees = \App\Models\Employee::all();

        return view("attendance.overtime", [
            "user" => Auth::user(),
            "summary" => $summary,
            "overtimeRequests" => $overtimeRequests,
            "departments" => $departments,
            "employees" => $employees
        ]);
    }

    public function exportOvertime(Request $request, $format) { return back(); }
    public function store(Request $request) { return back(); }
    public function updateStatus(Request $request, $id) { return back(); }
    public function cancel(Request $request, $id) { return back(); }
    public function getStatistics(Request $request) { return response()->json([]); }
}
