<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleTemplate extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'schedule_type',
        'time_in',
        'time_out',
        'required_hours',
        'created_by',
    ];

    protected $casts = [
        'required_hours' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // A flexible template has no fixed clock-in/out - only fixed
            // templates carry actual shift times.
            if ($model->schedule_type === 'flexible') {
                $model->time_in = null;
                $model->time_out = null;
            }

            // Codes are matched case-insensitively elsewhere (validation,
            // lookups), so store them consistently uppercase.
            if ($model->code) {
                $model->code = strtoupper($model->code);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'created_by');
    }

    public function employeeSchedules(): HasMany
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Human-readable summary of the working window, e.g. "8:00 AM - 5:00 PM"
     * for fixed templates, or "8 hrs flexible" for flexible ones.
     */
    public function getWindowLabelAttribute(): string
    {
        if ($this->schedule_type === 'flexible') {
            return number_format((float) $this->required_hours, 1) . ' hrs flexible';
        }

        if (!$this->time_in || !$this->time_out) {
            return 'Not set';
        }

        return \Carbon\Carbon::parse($this->time_in)->format('g:i A')
            . ' - ' . \Carbon\Carbon::parse($this->time_out)->format('g:i A');
    }
}