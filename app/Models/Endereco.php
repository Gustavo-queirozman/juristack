<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Endereco extends Model
{
    protected $fillable = [
        'cliente_id',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Linha única de endereço para exibição.
     */
    public function getLinhaCompletaAttribute(): string
    {
        $partes = array_filter([
            trim($this->logradouro . ($this->numero ? ', ' . $this->numero : '')),
            $this->complemento,
            $this->bairro,
            $this->cidade ? $this->cidade . ($this->estado ? ' - ' . $this->estado : '') : $this->estado,
            $this->cep ? 'CEP ' . $this->cep : null,
        ]);
        return implode(', ', $partes);
    }
}
