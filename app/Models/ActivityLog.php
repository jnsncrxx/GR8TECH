<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    /**
     * Action categories this system records.
     */
    public const ACTIONS = ['login', 'logout', 'create', 'update', 'delete', 'restore', 'backup'];

    protected $fillable = [
        'account_id',
        'actor_name',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function scopeForAccount($query, ?string $accountId)
    {
        return $accountId ? $query->where('account_id', $accountId) : $query;
    }

    public function scopeForAction($query, ?string $action)
    {
        return $action ? $query->where('action', $action) : $query;
    }

    public function scopeForModule($query, ?string $module)
    {
        return $module ? $query->where('module', $module) : $query;
    }

    public function scopeBetweenDates($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }
}
