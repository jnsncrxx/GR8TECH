<?php

namespace App\Models;

use App\Models\Concerns\HasExpiryWindow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Ramsey\Uuid\Uuid;

class LeaveRequest extends Model
{
    use HasUuids;
    use HasExpiryWindow;

    // Statuses used by HasExpiryWindow (PENDING/EXPIRED) and elsewhere.
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';
    public const CANCELLED = 'cancelled';
    public const EXPIRED = 'expired';

    // Leave types that are incremental (no hard balance cap enforced).
    public const UNCAPPED_LEAVE_TYPES = ['personal', 'emergency'];

    // Display labels for each stored leave_type value. Centralized here so
    // the request form, leave management table/filters, and balance widgets
    // all show the same wording without duplicating it in every view.
    public const LEAVE_TYPE_LABELS = [
        'vacation' => 'Vacation Leave',
        'sick' => 'Sick Leave',
        'personal' => 'Personal Leave/Leave Without Pay',
        'emergency' => 'Emergency Leave',
        'maternity' => 'Maternity Leave',
        'paternity' => 'Paternity Leave',
        'bereavement' => 'SIL (Service Incentive Leave)',
        'study' => 'Others',
    ];

    public static function labelFor(string $leaveType): string
    {
        return static::LEAVE_TYPE_LABELS[$leaveType] ?? ucfirst(str_replace('_', ' ', $leaveType));
    }

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_requested',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'expires_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Uuid::uuid4()->toString();
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'approved_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'approved_by');
    }
}