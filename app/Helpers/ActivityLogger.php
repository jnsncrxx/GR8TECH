<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Record an entry in the Activity Logs.
     *
     * @param  string  $action  One of ActivityLog::ACTIONS (login, logout, create, update, delete, restore, backup).
     * @param  string  $module  The area of the app the action happened in (e.g. "Account", "Employee").
     * @param  string|null  $description  Human-readable detail of what happened.
     * @param  Account|null  $account  The account performing the action. Defaults to the authenticated user.
     */
    public static function log(string $action, string $module, ?string $description = null, ?Account $account = null): void
    {
        $account = $account ?? auth()->user();

        try {
            ActivityLog::create([
                'account_id' => $account?->id,
                'actor_name' => $account?->email ?? 'System',
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to write activity log: ' . $e->getMessage());
        }
    }
}
