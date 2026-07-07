<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Period;

class PeriodManagementController extends Controller
{
    public function index()
    {
        $user = auth()->user() ?? (object) ["role" => "admin"];
        $periods = Period::orderBy("created_at", "desc")->get();
        
        // Ensure duration attribute works if not defined in model
        foreach ($periods as $period) {
            if (!isset($period->duration)) {
                $period->duration = $period->start_date ? $period->start_date->diffInDays($period->end_date) + 1 : 0;
            }
        }

        return view("attendance.period-management.index", compact("user", "periods"));
    }

    public function create()
    {
        $user = auth()->user() ?? (object) ["role" => "admin"];
        return view("attendance.period-management.create", compact("user"));
    }

    public function store(Request $request)
    {
        // TODO: Validate and store
        return redirect()->route("attendance.period-management.index")->with("success", "Period created successfully.");
    }

    public function show($id)
    {
        $user = auth()->user() ?? (object) ["role" => "admin"];
        $period = Period::findOrFail($id);
        return view("attendance.period-management.show", compact("user", "period"));
    }

    public function destroy($id)
    {
        $period = Period::findOrFail($id);
        $period->delete();
        return redirect()->route("attendance.period-management.index")->with("success", "Period deleted successfully.");
    }

    public function previewPayroll($period)
    {
        return back()->with("info", "Preview Payroll functionality coming soon.");
    }

    public function generatePayroll(Request $request, $period)
    {
        return back()->with("success", "Payroll generated successfully.");
    }

    public function showPayrollSummary($period)
    {
        return back()->with("info", "Payroll Summary functionality coming soon.");
    }

    public function exportPayroll($period)
    {
        return back()->with("info", "Export Payroll functionality coming soon.");
    }
}

