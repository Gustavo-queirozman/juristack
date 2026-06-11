<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Event extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'user_id',
        'title',
        'status',
        'category',
        'description',
        'starts_at',
        'ends_at',
        'location',
        'is_public',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_CONFIRMED => 'Confirmado',
            self::STATUS_COMPLETED => 'Concluido',
            self::STATUS_CANCELLED => 'Cancelado',
        ];
    }
}
