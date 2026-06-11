<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialEntry extends Model
{
    use HasFactory;

    public const TYPE_PAYABLE = 'payable';
    public const TYPE_RECEIVABLE = 'receivable';

    public const PAYMENT_METHOD_PIX = 'pix';
    public const PAYMENT_METHOD_CARD = 'card';

    protected $fillable = [
        'enterprise_id',
        'title',
        'amount',
        'entry_date',
        'entry_type',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'date',
    ];

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }

    public static function entryTypeLabels(): array
    {
        return [
            self::TYPE_PAYABLE => 'Conta a pagar',
            self::TYPE_RECEIVABLE => 'Conta a receber',
        ];
    }

    public static function paymentMethodLabels(): array
    {
        return [
            self::PAYMENT_METHOD_PIX => 'Pix',
            self::PAYMENT_METHOD_CARD => 'Cartao',
        ];
    }
}
