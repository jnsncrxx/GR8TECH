<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\AttendanceLog;
use App\Models\EmployeeBreak;
use App\Models\TimeEntry;
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
        'corrected_by',
        'correction_reason',
        'corrected_at',
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
        'corrected_at' => 'datetime',
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
     * 1. Completed TimeEntry records with valid (positive) duration.
     * 2. attendance_records.time_in/time_out fallback — used when
     *    there are no TimeEntry records, OR when the only entries
     *    present have zero/invalid duration (e.g. duplicate clicks
     *    that created zero-length entries).
     *
     * This fallback fixes Official Business, manually created
     * attendance records, and zero-duration TimeEntry records all
     * returning 0.00 hours.
     */
    public function calculateTotalHours(): float
    {
        $totalMinutes = 0;

        // An audited HR correction intentionally supersedes imported/raw time
        // entries while preserving those original entries for traceability.
        if ($this->corrected_at && $this->time_in && $this->time_out) {
            $timeIn = Carbon::parse($this->time_in);
            $timeOut = Carbon::parse($this->time_out);

            if ($this->isPlausibleWorkSpan($timeIn, $timeOut)) {
                $totalMinutes = $timeIn->diffInMinutes($timeOut);
            }
        }

        $entries = $this->corrected_at || $totalMinutes > 0
            ? collect()
            : $this->timeEntries()->whereNotNull('time_out')->get();

        foreach ($entries as $entry) {
            $timeIn = Carbon::parse($entry->time_in);
            $timeOut = Carbon::parse($entry->time_out);

            if ($this->isPlausibleWorkSpan($timeIn, $timeOut)) {
                $totalMinutes += $timeIn->diffInMinutes($timeOut);
            }
        }

        /*
         * Fallback: no TimeEntry produced a valid duration
         * (empty, or all zero-length). Use the attendance
         * record's own time_in/time_out span instead of 0.
         */
        if (!$this->corrected_at && $totalMinutes === 0 && $this->time_in && $this->time_out) {
            $timeIn = Carbon::parse($this->time_in);
            $timeOut = Carbon::parse($this->time_out);

            if ($this->isPlausibleWorkSpan($timeIn, $timeOut)) {
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
     * Reject corrupted/cross-date pairs before they reach reports or payroll.
     * A legitimate overnight shift may end on the following date, but a single
     * attendance span can never exceed 24 hours or begin on another work date.
     */
    private function isPlausibleWorkSpan(Carbon $timeIn, Carbon $timeOut): bool
    {
        if (!$timeOut->gt($timeIn)) {
            return false;
        }

        $recordDate = Carbon::parse($this->date)->startOfDay();

        return $timeIn->isSameDay($recordDate)
            && $timeOut->lte($recordDate->copy()->addDay()->endOfDay())
            && $timeIn->diffInMinutes($timeOut) <= 24 * 60;
    }

    public function hasInvalidTimeSpan(): bool
    {
        $pairs = collect();

        if ($this->time_in && $this->time_out) {
            $pairs->push([$this->time_in, $this->time_out]);
        }

        ($this->relationLoaded('timeEntries')
            ? $this->timeEntries->whereNotNull('time_out')
            : $this->timeEntries()->whereNotNull('time_out')->get())
            ->each(fn ($entry) => $pairs->push([$entry->time_in, $entry->time_out]));

        return $pairs->contains(function (array $pair) {
            return !$this->isPlausibleWorkSpan(
                Carbon::parse($pair[0]),
                Carbon::parse($pair[1])
            );
        });
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

        if ($this->corrected_at && $this->break_start && $this->break_end) {
            $breakStart = Carbon::parse($this->break_start);
            $breakEnd = Carbon::parse($this->break_end);

            return $breakEnd->gt($breakStart)
                ? $breakStart->diffInMinutes($breakEnd)
                : 0;
        }

        $breaks = $this->relationLoaded('breaks')
            ? $this->breaks->whereNotNull('break_end')
            : $this->breaks()->whereNotNull('break_end')->get();

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
     *
     * Uses the employee's expected hours for the day (from their
     * schedule — flexible or fixed) rather than a hardcoded 8,
     * so this stays consistent with getExpectedHours() and with
     * CalculatesAttendanceWithOfficialBusiness::recalculateAttendanceWithOfficialBusiness().
     */
    public function calculateRegularAndOvertimeHours(): array
    {
        $totalHours = $this->calculateTotalHours();
        $expectedHours = $this->getExpectedHours() ?? 8.0;

        return [
            'regular_hours' => round(min($totalHours, $expectedHours), 2),
            'overtime_hours' => round(max(0, $totalHours - $expectedHours), 2),
        ];
    }

    // Standard company schedule: 8am-5pm with 1hr lunch, 10 min grace period
    private const DEFAULT_SHIFT_START = '08:00';
    private const DEFAULT_SHIFT_END = '17:00';
    private const DEFAULT_BREAK_MINUTES = 60;
    private const GRACE_PERIOD_MINUTES = 10;

    // Get the schedule for this date, only if it's a working day
    private function getWorkingSchedule(): ?EmployeeSchedule
    {
        $schedule = $this->employee->getScheduleForDate($this->date);

        if (!$schedule || $schedule->status !== 'Working') {
            return null;
        }

        return $schedule;
    }

    // Expected hours for the day - required_hours if flexible, shift span minus break if fixed
    public function getExpectedHours(): ?float
    {
        $schedule = $this->getWorkingSchedule();
        if (!$schedule) {
            return null;
        }

        if ($schedule->isFlexible()) {
            return (float) $schedule->required_hours;
        }

        $start = Carbon::parse($this->date->format('Y-m-d') . ' ' . ($schedule->time_in ?? self::DEFAULT_SHIFT_START));
        $end = Carbon::parse($this->date->format('Y-m-d') . ' ' . ($schedule->time_out ?? self::DEFAULT_SHIFT_END));

        $spanMinutes = abs($end->diffInMinutes($start));
        $workingMinutes = max(0, $spanMinutes - self::DEFAULT_BREAK_MINUTES);

        return round($workingMinutes / 60, 2);
    }

    /**
     * Determine whether employee is late.
     *
     * Official Business is exempt. Flexible schedules have no
     * fixed start time so this doesn't apply to them.
     */
    public function isLate(): bool
    {
        if ($this->hasNonWorkingStatus()) {
            return false;
        }

        if (!$this->time_in) {
            return false;
        }

        $schedule = $this->getWorkingSchedule();

        if (!$schedule || $schedule->isFlexible()) {
            return false;
        }

        $expectedStartTime = $schedule->time_in
            ?? self::DEFAULT_SHIFT_START;

        $expectedTime = Carbon::parse(
            $this->date->format('Y-m-d')
            . ' '
            . $expectedStartTime
        );

        return self::lateMinutesAfterGrace($expectedTime, Carbon::parse($this->time_in)) > 0;
    }

    // Once the employee exceeds the 10-minute grace window, the complete
    // lateness from scheduled start is deductible (11 minutes late = 11).
    public function getLateMinutes(): int
    {
        if (!$this->isLate()) {
            return 0;
        }

        $schedule = $this->getWorkingSchedule();
        $expectedStartTime = $schedule->time_in ?? self::DEFAULT_SHIFT_START;
        $expectedTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $expectedStartTime);

        return self::lateMinutesAfterGrace($expectedTime, Carbon::parse($this->time_in));
    }

    private static function lateMinutesAfterGrace($scheduledStart, $actualStart): int
    {
        $scheduled = Carbon::parse($scheduledStart);
        $actual = Carbon::parse($actualStart);

        if ($actual->lte($scheduled)) {
            return 0;
        }

        $minutes = $scheduled->diffInMinutes($actual);

        return $minutes > self::GRACE_PERIOD_MINUTES ? $minutes : 0;
    }

    // Check if employee left before their scheduled end time. Doesn't apply to flexible schedules.
    public function isUndertime(): bool
    {
        if ($this->hasNonWorkingStatus()) {
            return false;
        }

        if (!$this->time_out) {
            return false;
        }

        $schedule = $this->getWorkingSchedule();

        if (!$schedule || $schedule->isFlexible()) {
            return false;
        }

        $expectedEndTime = $schedule->time_out ?? self::DEFAULT_SHIFT_END;
        $expectedTime = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $expectedEndTime);

        return Carbon::parse($this->time_out)->lt($expectedTime);
    }

    // Fixed schedule: late or undertime = incomplete day.
    // Flexible schedule: incomplete if actual hours worked is less than required_hours.
    public function isIncompleteDay(): bool
    {
        if ($this->hasNonWorkingStatus()) {
            return false;
        }

        if (!$this->time_in) {
            return false;
        }

        $schedule = $this->getWorkingSchedule();

        if (!$schedule) {
            return false;
        }

        if ($schedule->isFlexible()) {
            $expectedHours = $this->getExpectedHours();

            if ($expectedHours === null || $expectedHours <= 0) {
                return false;
            }

            return $this->calculateTotalHours() < $expectedHours;
        }

        return $this->isLate() || $this->isUndertime();
    }

    /**
     * Determine whether this record should be excluded from
     * late, undertime, and incomplete-day calculations.
     */
    private function hasNonWorkingStatus(): bool
    {
        return in_array($this->status, [
            self::OFFICIAL_BUSINESS,
            self::ON_LEAVE,
            self::HOLIDAY,
            self::DAY_OFF,
            self::ERROR,
        ], true);
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
        if ($this->hasNonWorkingStatus()) {
            return $this->status;
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
