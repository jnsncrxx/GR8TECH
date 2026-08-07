<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Company extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
        'tax_id',
        'registration_number',
        'is_active',
        'cutoff_day_1',
        'cutoff_day_2',
        'payroll_frequency',
        'payroll_release_offset',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cutoff_day_1' => 'integer',
        'cutoff_day_2' => 'integer',
        'payroll_release_offset' => 'integer',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * This company's configured monthly cutoff days, sorted ascending.
     * Used by CutoffPeriodService in place of config('attendance_cutoff.cutoff_days').
     *
     * @return array<int>
     */
    public function cutoffDays(): array
    {
        return collect([$this->cutoff_day_1, $this->cutoff_day_2])
            ->filter()
            ->sort()
            ->values()
            ->all();
    }
}
