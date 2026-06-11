<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerFile extends Model
{
    public const DOCUMENT_TYPES = [
        'identification' => 'Documento de identificacao',
        'cpf' => 'CPF',
        'address_proof' => 'Comprovante de residencia',
        'income_proof' => 'Comprovante de renda',
        'power_of_attorney' => 'Procuracao',
        'medical_report' => 'Laudo ou documento complementar',
        'other' => 'Outro documento',
    ];

    protected $fillable = [
        'customer_id',
        'document_type',
        'description',
        'path',
        'original_name',
        'mime',
        'size',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return self::DOCUMENT_TYPES[$this->document_type] ?? self::DOCUMENT_TYPES['other'];
    }
}
