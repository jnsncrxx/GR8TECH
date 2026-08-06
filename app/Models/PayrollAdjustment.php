<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollAdjustment extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'company_id', 'employee_id', 'name', 'category', 'direction',
        'frequency', 'amount', 'effective_from', 'effective_to',
        'is_taxable', 'is_active', 'reason', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function creator(): BelongsTo { return $this->belongsTo(Account::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(Account::class, 'updated_by'); }

    public function scopeForCompany($query, ?string $companyId)
    {
        return $companyId ? $query->where('company_id', $companyId) : $query->whereNull('company_id');
    }

    public function scopeApplicableToPeriod($query, string $startDate, string $endDate)
    {
        return $query->where('is_active', true)
            ->whereDate('effective_from', '<=', $endDate)
            ->where(function ($q) use ($startDate) {
                $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $startDate);
            })
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('frequency', 'recurring')
                    ->orWhere(function ($oneTime) use ($startDate, $endDate) {
                        $oneTime->where('frequency', 'one_time')
                            ->whereBetween('effective_from', [$startDate, $endDate]);
                    });
            });
    }
}
