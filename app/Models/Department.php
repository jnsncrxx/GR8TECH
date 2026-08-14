<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Ramsey\Uuid\Uuid;

class Department extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'department_id',
        'name',
        'description',
        'location',
        'budget',
        'company_id',
        'archived_at',
        'manager_id',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'archived_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Uuid::uuid4()->toString();
            }
            if (empty($model->department_id)) {
                $model->department_id = 'DEPT-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
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

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Scope to filter by company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to only include departments that are not archived
     */
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope to only include archived departments
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Determine if the department is archived
     */
    public function isArchived(): bool
    {
        return !is_null($this->archived_at);
    }

    /**
     * Determine if the department has active employees assigned to it
     */
    public function hasActiveEmployees(): bool
    {
        return $this->employees()->exists();
    }

    /**
     * Determine if the department currently has a manager assigned
     */
    public function hasManager(): bool
    {
        return !is_null($this->manager_id);
    }


}