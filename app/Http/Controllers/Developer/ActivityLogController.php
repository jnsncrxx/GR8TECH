<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Account;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
    protected function filtered(Request $request)
    {
        return ActivityLog::with('account.employee')
            ->forAccount($request->query('account_id'))
            ->forAction($request->query('action'))
            ->forModule($request->query('module'))
            ->betweenDates($request->query('date_from'), $request->query('date_to'))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('actor_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc');
    }

    public function index(Request $request)
    {
        $logs = $this->filtered($request)->paginate(25)->withQueryString();

        $accounts = Account::withTrashed()->with('employee')->orderBy('email')->get(['id', 'email', 'employee_id', 'role']);
        $actions = ActivityLog::ACTIONS;
        $modules = ActivityLog::query()->distinct()->orderBy('module')->pluck('module');

        $user = auth()->user();
        $activeRoute = 'developer.activity-logs.index';

        return view('developer.activity-logs.index', compact(
            'logs', 'accounts', 'actions', 'modules', 'user', 'activeRoute'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $logs = $this->filtered($request)->get();

        $filename = 'activity-logs-' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['User', 'Action', 'Module', 'Description', 'Date/Time', 'IP Address']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->actor_name ?? $log->account?->email ?? 'System',
                    ucfirst($log->action),
                    $log->module,
                    $log->description,
                    $log->created_at?->format('Y-m-d H:i:s'),
                    $log->ip_address,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
