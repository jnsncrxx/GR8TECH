<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class AttendanceRecord extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Attendance status constants.
     */
    public const PRESENT = 'present';
    public const ABSENT = 'absent';
    public const LATE = 'late';
    public const HALF_DAY = 'half_day';
    public const ON_LEAVE = 'on_leave';
    public const DAY_OFF = 'day_off';
    public const HOLIDAY = 'holiday';
    public const OFFICIAL_BUSINESS = 'official_business';
    public const ERROR = 'error';

    /**
     * All valid attendance statuses.
     */
    public const STATUSES = [
        self::PRESENT,
        self::ABSENT,
        self::LATE,
        self::HALF_DAY,
        self::ON_LEAVE,
        self::DAY_OFF,
        self::HOLIDAY,
        self::OFFICIAL_BUSINESS,
        self::ERROR,
    ];

    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'time_out',
        'break_start',
        'break_end',
        'total_hours',
        'regular_hours',
        'overtime_hours',
        'night_shift',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'total_hours' => 'decimal:2',
        'regular_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'night_shift' => 'boolean',
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

    /**
     * Employee relationship.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Attendance logs relationship.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    /**
     * Employee breaks relationship.
     */
    public function breaks(): HasMany
    {
        return $this->hasMany(EmployeeBreak::class);
    }

    /**
     * Multiple time entries per attendance day.
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class)
            ->orderBy('time_in');
    }

    /**
     * Get currently active time entry.
     */
    public function getActiveTimeEntry()
    {
        $activeEntry = $this->timeEntries()
            ->whereNull('time_out')
            ->first();

        /*
         * Legacy attendance fallback.
         *
         * If the main attendance record contains time_in
         * without time_out, create a matching TimeEntry.
         */
        if (
            !$activeEntry
            && $this->time_in
            && !$this->time_out
        ) {
            $activeEntry = TimeEntry::create([
                'attendance_record_id' => $this->id,
                'time_in' => $this->time_in,
                'entry_type' => 'regular',
            ]);
        }

        return $activeEntry;
    }

    /**
     * Determine whether an active time entry exists.
     */
    public function hasActiveTimeEntry(): bool
    {
        return $this->timeEntries()
            ->whereNull('time_out')
            ->exists()
            || ($this->time_in && !$this->time_out);
    }

    /**
     * Get completed TimeEntry hours.
     */
    public function getTotalHoursFromEntries(): float
    {
        return round(
            (float) $this->timeEntries()
                ->whereNotNull('time_out')
                ->sum('hours_worked'),
            2
        );
    }

    /**
     * Get first time entry.
     */
    public function getFirstTimeEntry()
    {
        return $this->timeEntries()
            ->orderBy('time_in', 'asc')
            ->first();
    }

    /**
     * Get last time entry.
     */
    public function getLastTimeEntry()
    {
        return $this->timeEntries()
            ->orderBy('time_in', 'desc')
            ->first();
    }

    /**
     * Calculate total worked hours.
     *
     * Priority:
     * 1. Completed TimeEntry records.
     * 2. attendance_records.time_in/time_out fallback.
     *
     * This fallback fixes Official Business and manually
     * created attendance records returning 0.00 hours.
     */
    public function calculateTotalHours(): float
    {
        $totalMinutes = 0;

        $entries = $this->timeEntries()
            ->whereNotNull('time_out')
            ->get();

        /*
         * MULTI-TIME ENTRY ATTENDANCE
         */
        if ($entries->isNotEmpty()) {
            foreach ($entries as $entry) {
                $timeIn = Carbon::parse($entry->time_in);
                $timeOut = Carbon::parse($entry->time_out);

                if ($timeOut->gt($timeIn)) {
                    $totalMinutes += $timeIn->diffInMinutes($timeOut);
                }
            }
        }

        /*
         * DIRECT ATTENDANCE / OFFICIAL BUSINESS FALLBACK
         *
         * Approved OB records may not contain TimeEntry
         * records. Use the attendance row time fields.
         */
        elseif ($this->time_in && $this->time_out) {
            $timeIn = Carbon::parse($this->time_in);
            $timeOut = Carbon::parse($this->time_out);

            if ($timeOut->gt($timeIn)) {
                $totalMinutes = $timeIn->diffInMinutes($timeOut);
            }
        }

        /*
         * Subtract completed breaks.
         */
        $totalBreakMinutes = $this->getCompletedBreakMinutes();

        $workingMinutes = max(
            0,
            $totalMinutes - $totalBreakMinutes
        );

        return round($workingMinutes / 60, 2);
    }

    /**
     * Calculate total hours from TimeEntry records.
     */
    public function calculateTotalHoursFromEntries(): float
    {
        $totalMinutes = 0;

        foreach ($this->timeEntries as $entry) {
            if (!$entry->time_out) {
                continue;
            }

            $timeIn = Carbon::parse($entry->time_in);
            $timeOut = Carbon::parse($entry->time_out);

            if ($timeOut->gt($timeIn)) {
                $totalMinutes += $timeIn->diffInMinutes($timeOut);
            }
        }

        $workingMinutes = max(
            0,
            $totalMinutes - $this->getTotalBreakMinutes()
        );

        return round($workingMinutes / 60, 2);
    }

    /**
     * Get completed break minutes.
     *
     * Active breaks are intentionally excluded from final
     * worked-hour calculations.
     */
    private function getCompletedBreakMinutes(): int
    {
        $totalMinutes = 0;

        $breaks = $this->breaks()
            ->whereNotNull('break_end')
            ->get();

        if ($breaks->isNotEmpty()) {
            foreach ($breaks as $break) {
                $breakStart = Carbon::parse($break->break_start);
                $breakEnd = Carbon::parse($break->break_end);

                if ($breakEnd->gt($breakStart)) {
                    $totalMinutes += $breakStart
                        ->diffInMinutes($breakEnd);
                }
            }

            return $totalMinutes;
        }

        /*
         * Legacy break column fallback.
         */
        if ($this->break_start && $this->break_end) {
            $breakStart = Carbon::parse($this->break_start);
            $breakEnd = Carbon::parse($this->break_end);

            if ($breakEnd->gt($breakStart)) {
                return $breakStart->diffInMinutes($breakEnd);
            }
        }

        return 0;
    }

    /**
     * Get total break minutes.
     *
     * Includes active breaks.
     */
    public function getTotalBreakMinutes(): int
    {
        $totalMinutes = 0;

        $breaks = $this->breaks()->get();

        if ($breaks->isNotEmpty()) {
            foreach ($breaks as $break) {
                $breakStart = Carbon::parse($break->break_start);

                $breakEnd = $break->break_end
                    ? Carbon::parse($break->break_end)
                    : now();

                if ($breakEnd->gt($breakStart)) {
                    $totalMinutes += $breakStart
                        ->diffInMinutes($breakEnd);
                }
            }

            return $totalMinutes;
        }

        /*
         * Legacy break columns.
         */
        if ($this->break_start) {
            $breakStart = Carbon::parse($this->break_start);

            $breakEnd = $this->break_end
                ? Carbon::parse($this->break_end)
                : now();

            if ($breakEnd->gt($breakStart)) {
                return $breakStart->diffInMinutes($breakEnd);
            }
        }

        return 0;
    }

    /**
     * Get total break hours.
     */
    public function getTotalBreakHours(): float
    {
        return round(
            $this->getTotalBreakMinutes() / 60,
            2
        );
    }

    /**
     * Check whether break exceeds 90 minutes.
     */
    public function isOverBreak(): bool
    {
        return $this->getTotalBreakMinutes() > 90;
    }

    /**
     * Get break minutes exceeding 90 minutes.
     */
    public function getOverBreakMinutes(): int
    {
        return max(
            0,
            $this->getTotalBreakMinutes() - 90
        );
    }

    /**
     * Calculate regular and overtime hours.
     */
    public function calculateRegularAndOvertimeHours(): array
    {
        $totalHours = $this->calculateTotalHours();

        return [
            'regular_hours' => min($totalHours, 8),
            'overtime_hours' => max(0, $totalHours - 8),
        ];
    }

    /**
     * Determine whether employee is late.
     */
    public function isLate(): bool
    {
        /*
         * Official Business must not be considered late.
         */
        if ($this->isOfficialBusiness()) {
            return false;
        }

        if (!$this->time_in) {
            return false;
        }

        $schedule = $this->employee
            ?->getWorkScheduleForDate($this->date);

        if (!$schedule) {
            return false;
        }

        $dayOfWeek = strtolower(
            $this->date->format('l')
        );

        $expectedStartTime =
            $schedule->{$dayOfWeek . '_start'};

        if (!$expectedStartTime) {
            return false;
        }

        $expectedTime = Carbon::parse(
            $this->date->format('Y-m-d')
            . ' '
            . $expectedStartTime
        );

        /*
         * 15-minute grace period.
         */
        $expectedTime->addMinutes(15);

        return Carbon::parse($this->time_in)
            ->gt($expectedTime);
    }

    /**
     * Check whether this is an Official Business record.
     */
    public function isOfficialBusiness(): bool
    {
        return $this->status === self::OFFICIAL_BUSINESS;
    }

    /**
     * Calculate attendance status.
     */
    public function getCalculatedStatus(): string
    {
        /*
         * IMPORTANT:
         * Preserve approved Official Business status.
         *
         * OB must not become present, late, or half-day.
         */
        if ($this->isOfficialBusiness()) {
            return self::OFFICIAL_BUSINESS;
        }

        if (!$this->time_in && !$this->time_out) {
            return self::ABSENT;
        }

        if ($this->time_in && !$this->time_out) {
            return self::PRESENT;
        }

        if ($this->time_in && $this->time_out) {
            $totalHours = $this->calculateTotalHours();

            if ($totalHours < 4) {
                return self::HALF_DAY;
            }

            return $this->isLate()
                ? self::LATE
                : self::PRESENT;
        }

        return self::ABSENT;
    }

    /**
     * Determine whether attendance overlaps night shift.
     *
     * Night period:
     * 10:00 PM to 6:00 AM.
     */
    public function isNightShift(): bool
    {
        return $this->calculateNightShiftHours() > 0;
    }

    /**
     * Calculate night shift hours.
     *
     * Night period:
     * 10:00 PM to 6:00 AM.
     */
    public function calculateNightShiftHours(): float
    {
        if (!$this->time_in || !$this->time_out) {
            return 0;
        }

        $timeIn = Carbon::parse($this->time_in);
        $timeOut = Carbon::parse($this->time_out);

        /*
         * Protect against invalid attendance periods.
         */
        if ($timeOut->lte($timeIn)) {
            return 0;
        }

        $totalNightMinutes = 0;

        /*
         * Check each calendar date touched by attendance.
         */
        $currentDate = $timeIn
            ->copy()
            ->startOfDay();

        $lastDate = $timeOut
            ->copy()
            ->startOfDay();

        while ($currentDate->lte($lastDate)) {
            /*
             * Evening night period:
             * 10 PM until midnight.
             */
            $eveningStart = $currentDate
                ->copy()
                ->setTime(22, 0);

            $eveningEnd = $currentDate
                ->copy()
                ->addDay()
                ->startOfDay();

            $totalNightMinutes += $this->calculateOverlapMinutes(
                $timeIn,
                $timeOut,
                $eveningStart,
                $eveningEnd
            );

            /*
             * Morning night period:
             * Midnight until 6 AM.
             */
            $morningStart = $currentDate
                ->copy()
                ->startOfDay();

            $morningEnd = $currentDate
                ->copy()
                ->setTime(6, 0);

            $totalNightMinutes += $this->calculateOverlapMinutes(
                $timeIn,
                $timeOut,
                $morningStart,
                $morningEnd
            );

            $currentDate->addDay();
        }

        return round(
            $totalNightMinutes / 60,
            2
        );
    }

    /**
     * Calculate overlap between two date/time ranges.
     */
    private function calculateOverlapMinutes(
        Carbon $rangeStart,
        Carbon $rangeEnd,
        Carbon $periodStart,
        Carbon $periodEnd
    ): int {
        $start = $rangeStart->gt($periodStart)
            ? $rangeStart->copy()
            : $periodStart->copy();

        $end = $rangeEnd->lt($periodEnd)
            ? $rangeEnd->copy()
            : $periodEnd->copy();

        if ($end->lte($start)) {
            return 0;
        }

        return $start->diffInMinutes($end);
    }
}