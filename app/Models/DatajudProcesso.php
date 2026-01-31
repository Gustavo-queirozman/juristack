<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// app/Models/DatajudProcesso.php
class DatajudProcesso extends Model
{
    protected $fillable = [
        'user_id',
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

    public function assuntos()
    {
        return $this->hasMany(DatajudAssunto::class, 'processo_id');
    }

    public function movimentos()
    {
        return $this->hasMany(DatajudMovimento::class, 'processo_id')
                    ->orderBy('data_hora', 'desc');
    }
}

