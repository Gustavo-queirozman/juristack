<?php

namespace App\Models;

use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\Auth\VerifyEmailNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_ENTERPRISE_ADMIN = 'enterprise_admin';

    public const ROLE_LAWYER = 'lawyer';

    public const ROLE_CLIENT = 'client';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_ENTERPRISE_ADMIN,
        self::ROLE_LAWYER,
        self::ROLE_CLIENT,
    ];

    public const INTERNAL_ROLES = [
        self::ROLE_ENTERPRISE_ADMIN,
        self::ROLE_LAWYER,
    ];

    protected $fillable = [
        'enterprise_id',
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'oab_state',
        'oab_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'oab_state' => 'string',
            'oab_number' => 'string',
        ];
    }

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function customerProfile(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function processosMonitorados(): HasMany
    {
        return $this->hasMany(ProcessoMonitor::class, 'user_id');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isClient(): bool
    {
        return $this->hasRole(self::ROLE_CLIENT);
    }

    public static function roleLabels(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrador global',
            self::ROLE_ENTERPRISE_ADMIN => 'Administrador do escritório',
            self::ROLE_LAWYER => 'Advogado',
            self::ROLE_CLIENT => 'Cliente',
        ];
    }

    public static function internalRoleLabels(): array
    {
        return array_intersect_key(self::roleLabels(), array_flip(self::INTERNAL_ROLES));
    }

    public function routeNotificationForWhatsApp(?Notification $notification = null): ?string
    {
        $customerPhone = $this->customerProfile?->mobile_phone
            ?: $this->customerProfile?->phone;

        if ($this->isClient()) {
            return $customerPhone;
        }

        return $customerPhone ?: $this->enterprise?->phone;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }
}
