<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatajudMovimentoComplemento extends Model
{
    protected $fillable = [
        'movimento_id',
        'codigo',
        'descricao',
        'valor',
        'nome',
    ];

    public function movimento()
    {
        return $this->belongsTo(DatajudMovimento::class, 'movimento_id');
    }
}

