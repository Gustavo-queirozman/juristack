<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DatajudProcesso extends Model
{
    protected $fillable = [
        'user_id',
        'enterprise_id',
        'customer_id',
        'datajud_id',
        'tribunal',
        'numero_processo',
        'data_ajuizamento',
        'grau',
        'nivel_sigilo',
        'formato_codigo',
        'formato_nome',
        'sistema_codigo',
        'sistema_nome',
        'classe_codigo',
        'classe_nome',
        'orgao_julgador_codigo',
        'orgao_julgador_nome',
        'orgao_julgador_codigo_municipio_ibge',
        'datahora_ultima_atualizacao',
        'indexed_at',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'data_ajuizamento' => 'datetime',
        'datahora_ultima_atualizacao' => 'datetime',
        'indexed_at' => 'datetime',
    ];

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assuntos(): HasMany
    {
        return $this->hasMany(DatajudAssunto::class, 'processo_id');
    }

    public function movimentos(): HasMany
    {
        return $this->hasMany(DatajudMovimento::class, 'processo_id')
                    ->orderBy('data_hora', 'desc');
    }

    public function latestMovement(): HasOne
    {
        return $this->hasOne(DatajudMovimento::class, 'processo_id')->latestOfMany('data_hora');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'datajud_processo_id');
    }
}
