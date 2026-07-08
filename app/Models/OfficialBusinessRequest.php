<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class OfficialBusinessRequest extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Status constants
     */
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';

    public const STATUSES = [
        self::PENDING,
        self::APPROVED,
        self::REJECTED,
    ];

    protected $fillable = [
        'employee_id',
        'date',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'attendance_record_id',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'reviewed_at' => 'datetime',
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
}