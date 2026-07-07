<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\HrContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = HrContact::with(['employee', 'user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('hr.contact', [
            'user' => Auth::user(),
            'contacts' => $contacts,
        ]);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Store not yet implemented'], 501);
    }

    public function show(HrContact $hrContact)
    {
        return view('hr.contact-show', [
            'user' => Auth::user(),
            'hrContact' => $hrContact->load(['employee', 'user', 'responder']),
        ]);
    }

    public function respond(Request $request, HrContact $hrContact)
    {
        return response()->json(['message' => 'Respond not yet implemented'], 501);
    }

    public function admin(Request $request)
    {
        $query = HrContact::with(['employee', 'user'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where('subject', 'like', "%{$search}%");
        }

        $contacts = $query->paginate(15)->withQueryString();

        $baseQuery = HrContact::query();

        return view('hr.contacts-admin', [
            'user' => Auth::user(),
            'contacts' => $contacts,
            'totalContacts' => (clone $baseQuery)->count(),
            'pendingCount' => (clone $baseQuery)->where('status', 'pending')->count(),
            'inProgressCount' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'resolvedCount' => (clone $baseQuery)->where('status', 'resolved')->count(),
        ]);
    }

    public function messages(Request $request)
    {
        $messages = HrContact::with(['employee', 'user'])
            ->latest()
            ->paginate(15);

        $baseQuery = HrContact::query();

        return view('hr.messages', [
            'user' => Auth::user(),
            'messages' => $messages,
            'totalMessages' => (clone $baseQuery)->count(),
            'unreadCount' => (clone $baseQuery)->where('status', 'pending')->count(),
            'respondedCount' => (clone $baseQuery)->whereNotNull('response')->count(),
        ]);
    }

    public function quickInbox(Request $request)
    {
        return response()->json(['messages' => []]);
    }
}
