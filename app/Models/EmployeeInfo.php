<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeInfo extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'resigned_date' => 'date',
        'regular_date' => 'date',
        'resign_on_next_payroll' => 'boolean',
        'allow_flexible_time' => 'boolean',
        'override_sss_exclude' => 'boolean',
        'override_philhealth_exclude' => 'boolean',
        'override_pagibig_exclude' => 'boolean',
        'override_tax_exclude' => 'boolean',
        'pagibig_voluntary' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
