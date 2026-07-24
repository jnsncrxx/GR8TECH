<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class Period extends Model
{
    use HasUuids;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_OPEN = 'open';
    public const STATUS_FOR_VALIDATION = 'for_validation';
    public const STATUS_READY = 'ready';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_FOR_REVIEW = 'for_review';
    public const STATUS_FINALIZED = 'finalized';
    public const STATUS_LOCKED = 'locked';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_OPEN,
        self::STATUS_FOR_VALIDATION,
        self::STATUS_READY,
        self::STATUS_PROCESSING,
        self::STATUS_FOR_REVIEW,
        self::STATUS_FINALIZED,
        self::STATUS_LOCKED,
    ];

    public const VALIDATION_COMPONENTS = [
        'attendance',
        'leave',
        'ob',
        'overtime',
    ];

    /**
     * Statuses reached only after payroll generation has run for a period
     * (Ready -> Processing is the generation step itself). Any request
     * touching attendance/leave/OB/overtime data for a date inside a period
     * in one of these statuses would desync already-computed payroll.
     */
    public const GENERATED_STATUSES = [
        self::STATUS_PROCESSING,
        self::STATUS_FOR_REVIEW,
        self::STATUS_FINALIZED,
        self::STATUS_LOCKED,
    ];

    /**
     * Whether payroll has already been generated for any period (in this
     * company) that overlaps the given date range. Used to block edits to
     * approved Leave/OB/Overtime requests once the payroll built from that
     * data exists, so a cancellation or edit can't silently desync a
     * period that's already been processed, reviewed, finalized, or locked.
     */
    public static function hasGeneratedPayrollOverlapping(string $companyId, string $startDate, string $endDate): bool
    {
        return static::query()
            ->where('company_id', $companyId)
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->whereIn('status', self::GENERATED_STATUSES)
            ->exists();
    }

    /**
     * Single-date convenience wrapper around hasGeneratedPayrollOverlapping,
     * for request types (OB, Overtime) that carry one date rather than a range.
     */
    public static function hasGeneratedPayrollForDate(string $companyId, string $date): bool
    {
        return self::hasGeneratedPayrollOverlapping($companyId, $date, $date);
    }

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'company_id',
        'previous_period_id',
        'name',
        'description',
        'period_month',
        'period_year',
        'period_no',
        'period_type',
        'processing_type',
        'payroll_date',
        'start_date',
        'end_date',
        'working_days',
        'status',
        'department_id',
        'employee_ids',
        'created_by',

        'attendance_validated_at',
        'attendance_validated_by',
        'leave_validated_at',
        'leave_validated_by',
        'ob_validated_at',
        'ob_validated_by',
        'overtime_validated_at',
        'overtime_validated_by',
        'validation_notes',
        'ready_at',
        'ready_by',

        'reviewed_at',
        'reviewed_by',
        'finalized_at',
        'finalized_by',

        'locked_at',
        'locked_by',

        'unlocked_at',
        'unlocked_by',
        'reopened_at',
        'reopened_by',
        'reopen_reason',
    ];

    protected $casts = [
        'payroll_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'employee_ids' => 'array',
        'period_month' => 'integer',
        'period_year' => 'integer',
        'period_no' => 'integer',
        'working_days' => 'integer',

        'attendance_validated_at' => 'datetime',
        'leave_validated_at' => 'datetime',
        'ob_validated_at' => 'datetime',
        'overtime_validated_at' => 'datetime',
        'ready_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'finalized_at' => 'datetime',

        'locked_at' => 'datetime',
        'unlocked_at' => 'datetime',
        'reopened_at' => 'datetime',
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function previousPeriod(): BelongsTo
    {
        return $this->belongsTo(self::class, 'previous_period_id');
    }

    public function nextPeriods(): HasMany
    {
        return $this->hasMany(self::class, 'previous_period_id');
    }

    public function employees()
    {
        if (empty($this->employee_ids)) {
            return collect();
        }

        return Employee::whereIn('id', $this->employee_ids)->get();
    }

    public function getDurationAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::labelForStatus($this->status);
    }

    public static function labelForStatus(?string $status): string
    {
        return match ($status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_OPEN => 'Open',
            self::STATUS_FOR_VALIDATION => 'For Validation',
            self::STATUS_READY => 'Ready for Payroll',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_FOR_REVIEW => 'For Review',
            self::STATUS_FINALIZED => 'Finalized',
            self::STATUS_LOCKED => 'Locked',
            default => 'Unknown',
        };
    }

    public function canTransitionTo(string $status): bool
    {
        $allowed = [
            self::STATUS_DRAFT => [self::STATUS_OPEN],
            self::STATUS_OPEN => [self::STATUS_FOR_VALIDATION],
            self::STATUS_FOR_VALIDATION => [self::STATUS_READY, self::STATUS_OPEN],
            self::STATUS_READY => [self::STATUS_PROCESSING, self::STATUS_FOR_VALIDATION],
            self::STATUS_PROCESSING => [self::STATUS_FOR_REVIEW],
            self::STATUS_FOR_REVIEW => [self::STATUS_FINALIZED, self::STATUS_PROCESSING],
            self::STATUS_FINALIZED => [self::STATUS_LOCKED],
            self::STATUS_LOCKED => [],
        ];

        return in_array($status, $allowed[$this->status] ?? [], true);
    }

    public function nextStatus(): ?string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => self::STATUS_OPEN,
            self::STATUS_OPEN => self::STATUS_FOR_VALIDATION,
            self::STATUS_FOR_VALIDATION => self::STATUS_READY,
            self::STATUS_READY => self::STATUS_PROCESSING,
            self::STATUS_PROCESSING => self::STATUS_FOR_REVIEW,
            self::STATUS_FOR_REVIEW => self::STATUS_FINALIZED,
            self::STATUS_FINALIZED => self::STATUS_LOCKED,
            default => null,
        };
    }


    public function isComponentValidated(string $component): bool
    {
        return match ($component) {
            'attendance' => !is_null($this->attendance_validated_at),
            'leave' => !is_null($this->leave_validated_at),
            'ob' => !is_null($this->ob_validated_at),
            'overtime' => !is_null($this->overtime_validated_at),
            default => false,
        };
    }

    public function hasCompletedValidation(): bool
    {
        foreach (self::VALIDATION_COMPONENTS as $component) {
            if (!$this->isComponentValidated($component)) {
                return false;
            }
        }

        return true;
    }

    public function getValidationProgressAttribute(): int
    {
        $completed = 0;

        foreach (self::VALIDATION_COMPONENTS as $component) {
            if ($this->isComponentValidated($component)) {
                $completed++;
            }
        }

        return (int) round(
            ($completed / count(self::VALIDATION_COMPONENTS)) * 100
        );
    }

    public static function validationFieldsFor(string $component): array
    {
        return match ($component) {
            'attendance' => [
                'date' => 'attendance_validated_at',
                'user' => 'attendance_validated_by',
            ],
            'leave' => [
                'date' => 'leave_validated_at',
                'user' => 'leave_validated_by',
            ],
            'ob' => [
                'date' => 'ob_validated_at',
                'user' => 'ob_validated_by',
            ],
            'overtime' => [
                'date' => 'overtime_validated_at',
                'user' => 'overtime_validated_by',
            ],
            default => throw new \InvalidArgumentException(
                'Invalid validation component.'
            ),
        };
    }

    public function hasGeneratedPayrolls(): bool
    {
        return Payroll::query()
            ->whereDate('pay_period_start', $this->start_date)
            ->whereDate('pay_period_end', $this->end_date)
            ->exists();
    }

    public function canBeDeleted(): bool
    {
        return $this->status === self::STATUS_DRAFT
            && !$this->hasGeneratedPayrolls();
    }

    public function isLocked(): bool
    {
        return $this->status === self::STATUS_LOCKED;
    }

    public function isActive(): bool
    {
        $today = now()->toDateString();

        return $this->start_date->toDateString() <= $today
            && $this->end_date->toDateString() >= $today;
    }

    public function isPast(): bool
    {
        return $this->end_date->lt(now()->startOfDay());
    }

    public function isFuture(): bool
    {
        return $this->start_date->gt(now()->startOfDay());
    }
}