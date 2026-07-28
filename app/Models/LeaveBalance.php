<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Ramsey\Uuid\Uuid;

class LeaveBalance extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'employee_id',
        'year',
        'vacation_days_total',
        'vacation_days_used',
        'sick_days_total',
        'sick_days_used',
        'sil_days_total',
        'sil_days_used',
        'personal_days_total',
        'personal_days_used',
        'emergency_days_total',
        'emergency_days_used',
        'maternity_days_total',
        'maternity_days_used',
        'paternity_days_total',
        'paternity_days_used',
        'bereavement_days_total',
        'bereavement_days_used',
        'study_days_total',
        'study_days_used',
        'spl_days_total',
        'spl_days_used',
        'vawc_days_total',
        'vawc_days_used',
        'bl_days_total',
        'bl_days_used',
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

    /**
     * Get remaining days for a specific leave type
     */
    public function getRemainingDays(string $leaveType): int
    {
        $totalField = $leaveType . '_days_total';
        $usedField = $leaveType . '_days_used';

        return $this->$totalField - $this->$usedField;
    }

    /**
     * Check if employee has enough leave balance.
     *
     * Everything except Vacation, Sick, and SIL is incremental: employees
     * can keep filing them with no hard cap, so no balance check is
     * enforced here. Usage still accumulates via incrementUsedDays()/
     * days_used for reporting, it just never blocks a new request.
     */
    public function hasEnoughBalance(string $leaveType, int $daysRequested): bool
    {
        if (in_array($leaveType, \App\Models\LeaveRequest::UNCAPPED_LEAVE_TYPES, true)) {
            return true;
        }

        return $this->getRemainingDays($leaveType) >= $daysRequested;
    }

    public function incrementUsedDays(string $leaveType, int $days): void
    {
        $usedField = $leaveType . '_days_used';
        $this->{$usedField} = ($this->{$usedField} ?? 0) + $days;
        $this->save();
    }
}