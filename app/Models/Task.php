<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    protected $fillable = [
        'enterprise_id',
        'datajud_processo_id',
        'title',
        'description',
        'status',
        'due_date',
        'priority',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(DatajudProcesso::class, 'datajud_processo_id');
    }

    // Relacionamento muitos-para-muitos com User via tabela pivot task_user
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user')
            ->withTimestamps();
    }

    public static function priorityLabels(): array
    {
        return [
            self::PRIORITY_LOW => 'Baixa',
            self::PRIORITY_MEDIUM => 'Média',
            self::PRIORITY_HIGH => 'Alta',
            self::PRIORITY_URGENT => 'Urgente',
        ];
    }
}
