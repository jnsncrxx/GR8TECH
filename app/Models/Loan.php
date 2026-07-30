<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'employee_id',
        'loan_type_id',
        'company_id',
        'principal_amount',
        'interest_rate',
        'interest_type',
        'term_months',
        'amortization_amount',
        'total_repayable',
        'remaining_balance',
        'start_date',
        'status',
        'notes',
        'rejection_reason',
        'requested_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'amortization_amount' => 'decimal:2',
        'total_repayable' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'start_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function loanType(): BelongsTo
    {
        return $this->belongsTo(LoanType::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'approved_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Loans that should be actively deducted during payroll computation.
     */
    public function scopeDeductible($query)
    {
        return $query->where('status', 'approved')->where('remaining_balance', '>', 0);
    }

    /**
     * Computes total repayable and per-cutoff amortization from the loan's
     * principal, rate, and term. Flat interest only - diminishing balance
     * is not implemented (interest_type is stored for future use but has
     * no distinct calculation path yet).
     */
    public function computeAmortization(): void
    {
        $totalRepayable = (float) $this->principal_amount * (1 + ((float) $this->interest_rate / 100));
        $amortization = $this->term_months > 0 ? $totalRepayable / $this->term_months : $totalRepayable;

        $this->total_repayable = round($totalRepayable, 2);
        $this->amortization_amount = round($amortization, 2);
        $this->remaining_balance = round($totalRepayable, 2);
    }

    public function approve(string $approvedByAccountId): void
    {
        $this->computeAmortization();
        $this->status = 'approved';
        $this->approved_by = $approvedByAccountId;
        $this->approved_at = now();
        $this->start_date = $this->start_date ?? now()->toDateString();
        $this->save();
    }

    public function reject(string $reason): void
    {
        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        $this->save();
    }

    /**
     * Records a payment against this loan (typically a payroll deduction),
     * decrementing the remaining balance and marking the loan completed
     * once fully paid.
     */
    public function recordPayment(float $amount, ?string $payrollId = null, ?string $createdByAccountId = null): LoanPayment
    {
        $newBalance = max(0, round((float) $this->remaining_balance - $amount, 2));

        $payment = $this->payments()->create([
            'payroll_id' => $payrollId,
            'amount' => $amount,
            'payment_date' => now()->toDateString(),
            'balance_after' => $newBalance,
            'created_by' => $createdByAccountId,
        ]);

        $this->remaining_balance = $newBalance;
        if ($newBalance <= 0) {
            $this->status = 'completed';
        }
        $this->save();

        return $payment;
    }
}