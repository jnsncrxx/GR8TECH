<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\AttendanceLog;
use App\Models\EmployeeBreak;
use App\Models\TimeEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Ramsey\Uuid\Uuid;

class AttendanceRecord extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Attendance status constants
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
     * All valid attendance statuses supported by the module.
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function breaks(): HasMany
    {
        return $this->hasMany(EmployeeBreak::class);
    }

    /**
     * Multiple time entries per day relationship
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class)->orderBy('time_in');
    }

    /**
     * Get the currently active time entry (clocked in but not out)
     */
    public function getActiveTimeEntry()
    {
        $activeEntry = $this->timeEntries()->whereNull('time_out')->first();

        // Fallback: If no active TimeEntry exists but the main record has an active time_in
        if (!$activeEntry && $this->time_in && !$this->time_out) {
            $activeEntry = TimeEntry::create([
                'attendance_record_id' => $this->id,
                'time_in' => $this->time_in,
                'entry_type' => 'regular'
            ]);
        }

        return $activeEntry;
    }

    /**
     * Check if there's an active time entry
     */
    public function hasActiveTimeEntry(): bool
    {
        return $this->timeEntries()->whereNull('time_out')->exists() || ($this->time_in && !$this->time_out);
    }

    /**
     * Get the total hours from all completed time entries
     */
    public function getTotalHoursFromEntries(): float
    {
        return (float) $this->timeEntries()->whereNotNull('time_out')->sum('hours_worked');
    }

    /**
     * Get the first time entry of the day (earliest time_in)
     */
    public function getFirstTimeEntry()
    {
        return $this->timeEntries()->orderBy('time_in', 'asc')->first();
    }

    /**
     * Get the last time entry of the day (latest time_out or latest time_in if no time_out)
     */
    public function getLastTimeEntry()
    {
        return $this->timeEntries()->orderBy('time_in', 'desc')->first();
    }

    /**
     * Calculate total hours worked from all time entries
     */
    public function calculateTotalHours(): float
    {
        $totalMinutes = 0;
        $entries = $this->timeEntries()->whereNotNull('time_out')->get();

        if ($entries->isNotEmpty()) {
            foreach ($entries as $entry) {
                $timeIn = \Carbon\Carbon::parse($entry->time_in);
                $timeOut = \Carbon\Carbon::parse($entry->time_out);
                $totalMinutes += $timeIn->diffInMinutes($timeOut);
            }
        } elseif ($this->time_in && $this->time_out) {
            // Fallback for records created directly with time_in/time_out on the
            // attendance_records row itself (e.g. the manual "Create Record" form,
            // or an approved Official Business request) rather than through the
            // clock-in/clock-out flow that populates the time_entries table.
            // Without this, such records always summed to 0 minutes here even
            // though total_hours had already been computed correctly at creation.
            $timeIn = \Carbon\Carbon::parse($this->time_in);
            $timeOut = \Carbon\Carbon::parse($this->time_out);
            $totalMinutes = max(0, $timeIn->diffInMinutes($timeOut));
        }

        // Subtract break minutes
        $totalBreakMinutes = 0;
        $breaks = $this->breaks()->whereNotNull('break_end')->get();

        if ($breaks->isNotEmpty()) {
            foreach ($breaks as $break) {
                $totalBreakMinutes += \Carbon\Carbon::parse($break->break_start)->diffInMinutes(\Carbon\Carbon::parse($break->break_end));
            }
        } elseif ($this->break_start && $this->break_end) {
            // Same fallback for the legacy break_start/break_end columns.
            $totalBreakMinutes = \Carbon\Carbon::parse($this->break_start)->diffInMinutes(\Carbon\Carbon::parse($this->break_end));
        }

        $workingMinutes = max(0, $totalMinutes - $totalBreakMinutes);
        return round($workingMinutes / 60, 2);
    }

    /**
     * Calculate total hours from all time entries (new multi-entry system)
     */
    public function calculateTotalHoursFromEntries(): float
    {
        $totalHours = 0;

        foreach ($this->timeEntries as $entry) {
            if ($entry->time_out) {
                // Completed entry - use calculated hours
                $totalHours += $entry->hours_worked > 0 ? $entry->hours_worked : $entry->calculateHoursWorked();
            }
            // Active entries (no time_out) are not counted until clocked out
        }

        // Subtract break time
        $breakHours = $this->getTotalBreakMinutes() / 60;
        $totalHours = max(0, $totalHours - $breakHours);

        return round($totalHours, 2);
    }

    /**
     * Get total break minutes from all breaks
     */
    public function getTotalBreakMinutes(): int
    {
        // Use breaks relationship if available
        if ($this->relationLoaded('breaks')) {
            return $this->breaks->sum(function ($break) {
                if ($break->break_end) {
                    return $break->break_duration_minutes ?? $break->break_start->diffInMinutes($break->break_end);
                }
                // If break is still active, calculate up to now
                return $break->break_start->diffInMinutes(now());
            });
        }

        // Fallback to old break_start/break_end fields for backward compatibility
        if ($this->break_start && $this->break_end) {
            return $this->break_end->diffInMinutes($this->break_start);
        }

        // Check if there's an active break
        if ($this->break_start && !$this->break_end) {
            return $this->break_start->diffInMinutes(now());
        }

        return 0;
    }

    /**
     * Get total break hours
     */
    public function getTotalBreakHours(): float
    {
        return round($this->getTotalBreakMinutes() / 60, 2);
    }

    /**
     * Check if total break exceeds 1.5 hours (90 minutes)
     */
    public function isOverBreak(): bool
    {
        return $this->getTotalBreakMinutes() > 90; // 1.5 hours = 90 minutes
    }

    /**
     * Get over break minutes (how many minutes over 1.5 hours)
     */
    public function getOverBreakMinutes(): int
    {
        $totalMinutes = $this->getTotalBreakMinutes();
        return max(0, $totalMinutes - 90);
    }

    /**
     * Calculate regular and overtime hours
     */
    public function calculateRegularAndOvertimeHours(): array
    {
        $totalHours = $this->calculateTotalHours();
        $regularHours = min($totalHours, 8); // 8 hours regular
        $overtimeHours = max(0, $totalHours - 8);

        return [
            'regular_hours' => $regularHours,
            'overtime_hours' => $overtimeHours,
        ];
    }

    // Standard company schedule: 8am-5pm with 1hr lunch, 15 min grace period
    private const DEFAULT_SHIFT_START = '08:00';
    private const DEFAULT_SHIFT_END = '17:00';
    private const DEFAULT_BREAK_MINUTES = 60;
    private const GRACE_PERIOD_MINUTES = 15;

    // Get the schedule for this date, only if it's a working day
    private function getWorkingSchedule(): ?EmployeeSchedule
    {
        $schedule = $this->employee->getScheduleForDate($this->date);

        if (!$schedule || $schedule->status !== 'Working') {
            return null;
        }

        return $schedule;
    }

    // Expected hours for the day (shift span minus lunch break)
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

        $start = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . ($schedule->time_in ?? self::DEFAULT_SHIFT_START));
        $end = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . ($schedule->time_out ?? self::DEFAULT_SHIFT_END));

        $spanMinutes = abs($end->diffInMinutes($start));
        $workingMinutes = max(0, $spanMinutes - self::DEFAULT_BREAK_MINUTES);

        return round($workingMinutes / 60, 2);
    }

    // Check if employee is late (past grace period). Flexible schedules have no fixed
    // start time so this doesn't apply to them.
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

        $expectedStartTime = $schedule->time_in ?? self::DEFAULT_SHIFT_START;

        $expectedTime = Carbon::parse(
            $this->date->format('Y-m-d') . ' ' . $expectedStartTime
        )->addMinutes(self::GRACE_PERIOD_MINUTES);

        return $this->time_in->gt($expectedTime);
    }

    // How many minutes late, past the grace period
    public function getLateMinutes(): int
    {
        if (!$this->isLate()) {
            return 0;
        }

        $schedule = $this->getWorkingSchedule();
        $expectedStartTime = $schedule->time_in ?? self::DEFAULT_SHIFT_START;
        $expectedTime = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $expectedStartTime)
            ->addMinutes(self::GRACE_PERIOD_MINUTES);

        return max(0, abs($this->time_in->diffInMinutes($expectedTime)));
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

        $expectedTime = Carbon::parse(
            $this->date->format('Y-m-d') . ' ' . $expectedEndTime
        );

        return $this->time_out->lt($expectedTime);
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
     * Check if this record is marked as Official Business
     */
    public function isOfficialBusiness(): bool
    {
        return $this->status === self::OFFICIAL_BUSINESS;
    }

    /**
     * Get status based on attendance data
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
     * Check if this attendance record is a night shift (10pm-6am)
     */
    public function isNightShift(): bool
    {
        if (!$this->time_in || !$this->time_out) {
            return false;
        }

        $timeIn = \Carbon\Carbon::parse($this->time_in);
        $timeOut = \Carbon\Carbon::parse($this->time_out);

        // Night shift period: 10:00 PM (22:00) to 6:00 AM (06:00)
        $nightStart = 22; // 10 PM
        $nightEnd = 6;    // 6 AM

        // Convert times to minutes for easier calculation
        $timeInMinutes = $timeIn->hour * 60 + $timeIn->minute;
        $timeOutMinutes = $timeOut->hour * 60 + $timeOut->minute;

        // Determine if work spans across midnight
        $spansMidnight = $timeOutMinutes < $timeInMinutes;

        if ($spansMidnight) {
            // Work spans across midnight (e.g., 10 PM to 2 AM)
            $midnightMinutes = 24 * 60; // 1440 minutes

            // Check if time_in is in night period (10 PM to midnight)
            if ($timeInMinutes >= $nightStart * 60) {
                return true;
            }

            // Check if time_out is in night period (midnight to 6 AM)
            if ($timeOutMinutes <= $nightEnd * 60) {
                return true;
            }
        } else {
            // Work within the same day
            $nightStartMinutes = $nightStart * 60; // 10 PM = 1320 minutes
            $nightEndMinutes = $nightEnd * 60;     // 6 AM = 360 minutes
            $midnightMinutes = 24 * 60;            // 1440 minutes

            // Check if work overlaps with evening night period (10 PM to midnight)
            if ($timeInMinutes >= $nightStartMinutes && $timeInMinutes < $midnightMinutes) {
                return true;
            }

            // Check if work overlaps with early morning night period (midnight to 6 AM)
            if ($timeInMinutes < $nightEndMinutes && $timeOutMinutes > $timeInMinutes) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate night shift hours (10pm-6am)
     */
    public function calculateNightShiftHours(): float
    {
        if (!$this->time_in || !$this->time_out) {
            return 0;
        }

        $timeIn = \Carbon\Carbon::parse($this->time_in);
        $timeOut = \Carbon\Carbon::parse($this->time_out);

        // Night shift period: 10:00 PM (22:00) to 6:00 AM (06:00)
        $nightStart = 22; // 10 PM
        $nightEnd = 6;    // 6 AM

        $nightShiftHours = 0;

        // Convert times to minutes for easier calculation
        $timeInMinutes = $timeIn->hour * 60 + $timeIn->minute;
        $timeOutMinutes = $timeOut->hour * 60 + $timeOut->minute;

        // Determine if work spans across midnight
        $spansMidnight = $timeOutMinutes < $timeInMinutes;

        if ($spansMidnight) {
            // Work spans across midnight (e.g., 10 PM to 2 AM)
            $midnightMinutes = 24 * 60; // 1440 minutes

            // Check if time_in is in night period (10 PM to midnight)
            if ($timeInMinutes >= $nightStart * 60) {
                $nightShiftHours += ($midnightMinutes - $timeInMinutes) / 60;
            }

            // Check if time_out is in night period (midnight to 6 AM)
            if ($timeOutMinutes <= $nightEnd * 60) {
                $nightShiftHours += $timeOutMinutes / 60;
            }
        } else {
            // Work within the same day
            $nightStartMinutes = $nightStart * 60; // 10 PM = 1320 minutes
            $nightEndMinutes = $nightEnd * 60;     // 6 AM = 360 minutes
            $midnightMinutes = 24 * 60;            // 1440 minutes

            // Check if work overlaps with evening night period (10 PM to midnight)
            if ($timeInMinutes >= $nightStartMinutes && $timeInMinutes < $midnightMinutes) {
                $eveningEnd = min($timeOutMinutes, $midnightMinutes);
                $nightShiftHours += ($eveningEnd - $timeInMinutes) / 60;
            }

            // Check if work overlaps with early morning night period (midnight to 6 AM)
            if ($timeInMinutes <= $nightEndMinutes && $timeOutMinutes > 0) {
                $morningStart = max($timeInMinutes, 0);
                $morningEnd = min($timeOutMinutes, $nightEndMinutes);
                $nightShiftHours += ($morningEnd - $morningStart) / 60;
            }
        }

        return round($nightShiftHours, 2);
    }
}