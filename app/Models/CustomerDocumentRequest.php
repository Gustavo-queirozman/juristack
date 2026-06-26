<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CustomerDocumentRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_FULFILLED = 'fulfilled';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pendente',
        self::STATUS_FULFILLED => 'Atendido',
        self::STATUS_CANCELLED => 'Cancelado',
    ];

    protected $fillable = [
        'enterprise_id',
        'customer_id',
        'datajud_processo_id',
        'requested_by_user_id',
        'document_type',
        'description',
        'status',
        'notified_at',
        'fulfilled_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(DatajudProcesso::class, 'datajud_processo_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return CustomerFile::DOCUMENT_TYPES[$this->document_type] ?? CustomerFile::DOCUMENT_TYPES['other'];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? self::STATUS_LABELS[self::STATUS_PENDING];
    }
}
