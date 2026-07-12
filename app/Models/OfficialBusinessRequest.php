<?php

namespace App\Models;

use App\Models\Concerns\HasExpiryWindow;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class OfficialBusinessRequest extends Model
{
    use HasUuids;
    use HasExpiryWindow;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Status constants
     */
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';
    public const EXPIRED = 'expired';

    public const STATUSES = [
        self::PENDING,
        self::APPROVED,
        self::REJECTED,
        self::EXPIRED,
    ];

    protected $fillable = [
        'employee_id',
        'date',
        'reason',
        'status',
        'is_full_day',
        'ob_start_time',
        'ob_end_time',
        'credited_hours',
        'cutoff_period_key',
        'expires_at',
        'reviewed_by',
        'reviewed_at',
        'approved_by_role',
        'rejection_reason',
        'attendance_record_id',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'reviewed_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_full_day' => 'boolean',
        'ob_start_time' => 'datetime:H:i',
        'ob_end_time' => 'datetime:H:i',
        'credited_hours' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Uuid::uuid4()->toString();
            }
            if (empty($model->status)) {
                $model->status = self::PENDING;
            }
        });
    }

    public function newUniqueId()
    {
        return (string) Uuid::uuid4();
    }

    public function uniqueIds()
    {
        return ['id'];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    // Reviewers/creators are tracked via the accounts table (App\Models\Account),
    // not the default Laravel users table, since this app doesn't use a `users` table.
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Account::class, 'reviewed_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Account::class, 'created_by');
    }

    /**
     * Hours this request credits toward attendance/payroll — always the
     * literal duration between ob_start_time and ob_end_time. Per the
     * finalized OB spec, Time In/Time Out are required on every request, so
     * duration is always computable directly (no full-day fallback).
     */
    public function computeCreditedHours(): float
    {
        if (!$this->ob_start_time || !$this->ob_end_time) {
            return 0.0;
        }

        $start = \Carbon\Carbon::parse($this->ob_start_time);
        $end = \Carbon\Carbon::parse($this->ob_end_time);

        return round(max(0, $start->diffInMinutes($end)) / 60, 2);
    }

    public function isPending(): bool
    {
        return $this->status === self::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::REJECTED;
    }

    public function isExpired(): bool
    {
        return $this->status === self::EXPIRED;
    }

    // isPastDeadline() and scopePastDeadline() now come from HasExpiryWindow.

    public function scopePending($query)
    {
        return $query->where('status', self::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::REJECTED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::EXPIRED);
    }
}