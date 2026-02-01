<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessoMonitor extends Model
{
    protected $fillable = [
        'user_id',
        'processo_id',
        'tribunal',
        'numero_processo',
        'ultima_verificacao',
        'ultima_atualizacao_datajud',
        'verificacoes_consecutivas_sem_mudanca',
        'ativo',
        'observacoes',
    ];

    protected $casts = [
        'ultima_verificacao' => 'datetime',
        'ultima_atualizacao_datajud' => 'datetime',
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function processo()
    {
        return $this->belongsTo(DatajudProcesso::class, 'processo_id');
    }
}
