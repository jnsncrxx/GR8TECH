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
        'is_balance_set',
        'sil_deferred',
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

    protected $casts = [
        'is_balance_set' => 'boolean',
        'sil_deferred'   => 'boolean',
    ];

    /**
     * Get remaining days for a specific leave type.
     *
     * Returns null for capped types (vacation/sick/sil) when no explicit
     * balance has been set — meaning the leave is uncapped/unpaid and only
     * usage is tracked. Returns an integer when a balance is set.
     */
    public function getRemainingDays(string $leaveType): ?int
    {
        // SIL that is still deferred (pending 1-year anniversary) behaves
        // like an uncapped/unpaid type — no remaining balance shown.
        if ($leaveType === 'sil' && ($this->sil_deferred ?? false)) {
            return null;
        }

        // For capped leave types, only return a remaining balance if an admin
        // has explicitly set the balance. Otherwise it's uncapped/unpaid.
        $cappedTypes = ['vacation', 'sick', 'sil'];
        if (in_array($leaveType, $cappedTypes, true) && !($this->is_balance_set ?? false)) {
            return null;
        }

        $totalField = $leaveType . '_days_total';
        $usedField  = $leaveType . '_days_used';

        return ($this->$totalField ?? 0) - ($this->$usedField ?? 0);
    }

    /**
     * Check if employee has enough leave balance.
     *
     * When no balance has been configured (is_balance_set = false), every
     * leave type — including vacation, sick, and SIL — is allowed without
     * restriction. Usage is still tracked, but no cap is enforced and all
     * leave will be marked unpaid.
     *
     * Once a balance is set, vacation/sick/SIL are capped at their totals.
     * SIL that is still deferred counts as uncapped (no remaining shown).
     */
    public function hasEnoughBalance(string $leaveType, int $daysRequested): bool
    {
        // Without an explicit balance, every type is uncapped (no block).
        if (!($this->is_balance_set ?? false)) {
            return true;
        }

        // SIL deferred (pending anniversary) — treat as uncapped.
        if ($leaveType === 'sil' && ($this->sil_deferred ?? false)) {
            return true;
        }

        // All non-VL/SL/SIL types are always uncapped (incremental).
        if (in_array($leaveType, \App\Models\LeaveRequest::UNCAPPED_LEAVE_TYPES, true)) {
            return true;
        }

        // Uncapped types beyond UNCAPPED_LEAVE_TYPES list (personal, emergency, etc.)
        $alwaysUncapped = ['personal', 'emergency', 'maternity', 'paternity', 'bereavement', 'study', 'spl', 'vawc'];
        if (in_array($leaveType, $alwaysUncapped, true)) {
            return true;
        }

        $remaining = $this->getRemainingDays($leaveType);
        if ($remaining === null) {
            return true; // null = uncapped
        }

        return $remaining >= $daysRequested;
    }

    public function incrementUsedDays(string $leaveType, int $days): void
    {
        $usedField = $leaveType . '_days_used';
        $this->{$usedField} = ($this->{$usedField} ?? 0) + $days;
        $this->save();
    }
}