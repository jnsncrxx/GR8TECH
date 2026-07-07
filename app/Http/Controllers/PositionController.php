<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $positions = Position::with('department')
            ->orderBy('name')
            ->paginate(15);

        return view('positions.index', [
            'user' => Auth::user(),
            'positions' => $positions,
        ]);
    }

    public function create(Request $request)
    {
        return view('positions.create', ['user' => Auth::user()]);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Store not yet implemented'], 501);
    }

    public function show($position)
    {
        return view('positions.show', ['position' => $position, 'user' => Auth::user()]);
    }

    public function edit($position)
    {
        return view('positions.edit', ['position' => $position, 'user' => Auth::user()]);
    }

    public function update(Request $request, $position)
    {
        return response()->json(['message' => 'Update not yet implemented'], 501);
    }

    public function destroy($position)
    {
        return response()->json(['message' => 'Delete not yet implemented'], 501);
    }
}
