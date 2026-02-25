<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relacionamento com processos monitorados
     */
    public function processosMonitorados()
    {
        return $this->hasMany(ProcessoMonitor::class, 'user_id');
    }


public function advocate()
{
    return $this->hasOne(Advocate::class);
}

public function enterprise()
{
    return $this->hasOne(Enterprise::class);
}

public function customer()
{
    return $this->hasOne(Customer::class);
}

protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (!$model->id) {
            $model->id = (string) Str::uuid();
        }
    });
}
}
