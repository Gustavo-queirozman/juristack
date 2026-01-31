<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class DatajudAssunto extends Model
{
    protected $fillable = ['processo_id', 'codigo', 'nome'];

    public function processo()
    {
        return $this->belongsTo(DatajudProcesso::class, 'processo_id');
    }
}

