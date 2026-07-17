<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'code',
        'description',
        'level',
        'department_id',
        'company_id',
        'min_salary',
        'max_salary',
        'payroll_template_id',
        'is_active',
        'requirements',
        'responsibilities',
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'is_active' => 'boolean',
        'requirements' => 'array',
        'responsibilities' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department_id', $department);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function payrollTemplate(): BelongsTo
    {
        return $this->belongsTo(PayrollTemplate::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function getSalaryRangeAttribute(): string
    {
        if ($this->min_salary !== null && $this->max_salary !== null) {
            return '₱' . number_format((float) $this->min_salary, 0)
                . ' - ₱' . number_format((float) $this->max_salary, 0);
        }

        return 'Salary not specified';
    }
}