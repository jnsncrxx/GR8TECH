<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('employee-dashboard', ['user' => Auth::user()]);
    }
}
