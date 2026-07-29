<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class OvertimeReminder extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    public const PENDING = 'pending';
    public const SUBMITTED = 'submitted';
    public const DISMISSED = 'dismissed';

    protected $fillable = [
        'employee_id',
        'attendance_record_id',
        'date',
        'required_hours',
        'worked_hours',
        'extra_hours',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'required_hours' => 'decimal:2',
        'worked_hours' => 'decimal:2',
        'extra_hours' => 'decimal:2',
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class, 'attendance_record_id');
    }
}
