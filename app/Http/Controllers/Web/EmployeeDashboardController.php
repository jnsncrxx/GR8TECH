<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('dashboard');
    }

    public function downloadPayslip(string $payrollId)
    {
        $user = Auth::user();
        $payroll = Payroll::findOrFail($payrollId);

        abort_unless(
            $user?->employee
                && $payroll->employee_id === $user->employee->id
                && in_array($payroll->status, ['approved', 'paid'], true),
            403
        );

        return redirect()->route('payroll.download-payslip', $payroll->id);
    }

    public function payrollHistory()
    {
        $user = Auth::user();
        abort_unless($user?->employee, 403);

        $payrolls = Payroll::where('employee_id', $user->employee->id)
            ->whereIn('status', ['approved', 'paid'])
            ->orderByDesc('pay_period_end')
            ->paginate(15);

        return view('employee.payroll-history', compact('user', 'payrolls'));
    }

    public function getDashboardData()
    {
        return response()->json(['redirect' => route('dashboard')]);
    }
}
