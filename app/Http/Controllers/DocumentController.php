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
        return response()->json(['message' => 'Export not yet implemented'], 501);
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
