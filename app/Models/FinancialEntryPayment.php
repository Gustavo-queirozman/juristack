<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialEntryPayment extends Model
{
    use HasFactory;

    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_BANK_IMPORT = 'bank_import';

    protected $fillable = [
        'financial_entry_id',
        'amount',
        'payment_date',
        'source',
        'reference',
        'notes',
        'imported_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'imported_payload' => 'array',
    ];

    public function financialEntry(): BelongsTo
    {
        return $this->belongsTo(FinancialEntry::class);
    }

    public static function sourceLabels(): array
    {
        return [
            self::SOURCE_MANUAL => 'Manual',
            self::SOURCE_BANK_IMPORT => 'Importacao bancaria',
        ];
    }
}
