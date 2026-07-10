<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use App\Helpers\TimezoneHelper;

class Account extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'employee_id',
        'email',
        'password',
        'password_reset_token',
        'password_reset_expires_at',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password_reset_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
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

    // Relationship to login logs
    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class, 'account_id');
    }

    // Relationship to employee
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Get full name
    public function getFullNameAttribute(): string
    {
        return $this->employee ? $this->employee->full_name ?? $this->employee->first_name . ' ' . $this->employee->last_name : $this->email;
    }

    public function getDepartmentAttribute()
    {
        return $this->employee ? $this->employee->department : null;
    }

    /**
     * Verify a provided password against stored credential hashes.
     * Supports legacy non-Bcrypt hashes and upgrades them to bcrypt on successful login.
     */
    public function verifyPassword(string $password): bool
    {
        $storedPassword = $this->getAttributes()['password'] ?? $this->password;

        try {
            if (!Hash::check($password, $storedPassword)) {
                return false;
            }

            if (!$this->isBcryptHash($storedPassword)) {
                $this->forceFill(['password' => Hash::make($password)])->save();
            }

            return true;
        } catch (\RuntimeException $e) {
            if (crypt($password, $storedPassword) === $storedPassword) {
                $this->forceFill(['password' => Hash::make($password)])->save();
                return true;
            }

            return false;
        }
    }

    protected function isBcryptHash(string $hash): bool
    {
        return (bool) preg_match('/^\$2[ayb]\$[0-9]{2}\$.{53}$/', $hash);
    }

    /**
     * Update the last login time with proper timezone handling
     */
    public function updateLastLogin(): void
    {
        $this->update([
            'last_login_at' => TimezoneHelper::now()
        ]);
    }

    /**
     * Get formatted last login time
     */
    public function getFormattedLastLoginAttribute(): string
    {
        return $this->last_login_at 
            ? TimezoneHelper::formatForDisplay($this->last_login_at, 'M d, Y g:i A')
            : 'Never';
    }
}