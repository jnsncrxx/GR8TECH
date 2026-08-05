<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceCorrection extends Model
{
    use HasUuids;

    protected $fillable = [
        'attendance_record_id',
        'corrected_by',
        'reason',
        'original_values',
        'corrected_values',
    ];

    protected $casts = [
        'original_values' => 'array',
        'corrected_values' => 'array',
    ];

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function correctedBy(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'corrected_by');
    }
}
