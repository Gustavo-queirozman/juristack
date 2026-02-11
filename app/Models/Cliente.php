<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    public const TYPE_PF = 'PF';
    public const TYPE_PJ = 'PJ';

    protected $fillable = [
        'user_id',
        'type',
        'nome',
        'cpf',
        'cnpj',
        'email',
        'telefone',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enderecos(): HasMany
    {
        return $this->hasMany(Endereco::class);
    }

    public function isPf(): bool
    {
        return $this->type === self::TYPE_PF;
    }

    public function isPj(): bool
    {
        return $this->type === self::TYPE_PJ;
    }

    /**
     * Documento formatado (CPF ou CNPJ) para exibição.
     */
    public function getDocumentoFormatadoAttribute(): ?string
    {
        if ($this->cpf) {
            return $this->formatarCpf($this->cpf);
        }
        if ($this->cnpj) {
            return $this->formatarCnpj($this->cnpj);
        }
        return null;
    }

    public static function formatarCpf(string $cpf): string
    {
        $digits = preg_replace('/\D/', '', $cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
    }

    public static function formatarCnpj(string $cnpj): string
    {
        $digits = preg_replace('/\D/', '', $cnpj);
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $digits);
    }
}
