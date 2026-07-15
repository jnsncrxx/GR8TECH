<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

class EmployeeSchedule extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'employee_id',
        'department_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
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
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'created_by');
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        if (!$this->time_in || !$this->time_out) {
            return 'N/A';
        }

        return $this->time_in . ' - ' . $this->time_out;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Working' => 'green',
            'Day Off' => 'gray',
            'Leave' => 'yellow',
            'Holiday' => 'red',
            'Overtime' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Check if schedule is for today
     */
    public function isToday(): bool
    {
        return $this->date->isToday();
    }

    /**
     * Check if schedule is for a specific date
     */
    public function isForDate(Carbon $date): bool
    {
        return $this->date->isSameDay($date);
    }

    /**
     * Get working hours in decimal format
     */
    public function getWorkingHoursAttribute(): float
    {
        if (!$this->time_in || !$this->time_out || $this->status !== 'Working') {
            return 0;
        }

        // Normalize date and time separately, then construct once, so a
        // time value that already carries a date (e.g. a full datetime
        // string or Carbon) can never produce "Y-m-d Y-m-d H:i:s".
        $date = $this->date->format('Y-m-d');

        $start = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $date . ' ' . Carbon::parse($this->time_in)->format('H:i:s')
        );

        $end = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $date . ' ' . Carbon::parse($this->time_out)->format('H:i:s')
        );

        return round($start->diffInMinutes($end, true) / 60, 2);
    }

    /**
     * Scope for specific department
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope for specific date range
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for specific month
     */
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
    }
}