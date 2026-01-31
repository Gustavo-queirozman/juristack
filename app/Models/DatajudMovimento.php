<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class DatajudMovimento extends Model
{
    protected $fillable = [
        'processo_id',
        'codigo',
        'nome',
        'data_hora',
        'orgao_codigo',
        'orgao_nome',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function processo()
    {
        return $this->belongsTo(DatajudProcesso::class, 'processo_id');
    }

    public function complementos()
    {
        return $this->hasMany(DatajudMovimentoComplemento::class, 'movimento_id');
    }
}

