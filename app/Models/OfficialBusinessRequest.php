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
        'is_full_day',
        'ob_start_time',
        'ob_end_time',
        'credited_hours',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'attendance_record_id',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'reviewed_at' => 'datetime',
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
     * Compute the hours this request should credit toward attendance/payroll.
     *
     * - Partial day: exact duration between ob_start_time and ob_end_time.
     * - Full day: falls back to the employee's scheduled shift length for
     *   $this->date.
     *
     * NOTE: I don't have your WorkSchedule/Employee schedule model, so the
     * full-day branch below is a best-effort guess based on the
     * `$schedule->{$dayOfWeek . '_start'}` pattern referenced elsewhere in
     * this codebase (e.g. isLate()). Replace getScheduledHoursFor() with
     * whatever your actual schedule lookup looks like before relying on this
     * for payroll. Until then this falls back to 8.0 if no schedule is found,
     * which may NOT be correct for part-time/varied-shift employees.
     */
    public function computeCreditedHours(): float
    {
        if (!$this->is_full_day && $this->ob_start_time && $this->ob_end_time) {
            $start = \Carbon\Carbon::parse($this->ob_start_time);
            $end = \Carbon\Carbon::parse($this->ob_end_time);
            return round(max(0, $start->diffInMinutes($end)) / 60, 2);
        }

        return $this->getScheduledHoursFor($this->date);
    }

    /**
     * NOTE: placeholder — adjust to match your actual Employee/WorkSchedule
     * relation and column names. Assumes something like:
     *   $employee->schedule->{$dayOfWeek . '_start'} / '_end'
     * per the pattern used in AttendanceRecord::isLate() elsewhere in this app.
     */
    protected function getScheduledHoursFor($date): float
    {
        $employee = $this->employee;
        $schedule = $employee->schedule ?? null;

        if ($schedule) {
            $dayOfWeek = strtolower(\Carbon\Carbon::parse($date)->format('l')); // e.g. 'monday'
            $startField = $dayOfWeek . '_start';
            $endField = $dayOfWeek . '_end';

            if (!empty($schedule->{$startField}) && !empty($schedule->{$endField})) {
                $start = \Carbon\Carbon::parse($schedule->{$startField});
                $end = \Carbon\Carbon::parse($schedule->{$endField});
                return round(max(0, $start->diffInMinutes($end)) / 60, 2);
            }
        }

        // Fallback only — flag this so it's easy to find and fix once the
        // real schedule model is wired in.
        return 8.0;
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