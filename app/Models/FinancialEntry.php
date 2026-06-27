<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class FinancialEntry extends Model
{
    use HasFactory;

    public const TYPE_PAYABLE = 'payable';
    public const TYPE_RECEIVABLE = 'receivable';

    public const PAYMENT_METHOD_PIX = 'pix';
    public const PAYMENT_METHOD_CARD = 'card';

    protected $fillable = [
        'enterprise_id',
        'customer_id',
        'title',
        'amount',
        'entry_date',
        'entry_type',
        'payment_method',
        'notes',
        'whatsapp_reminder_enabled',
        'last_whatsapp_reminder_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'date',
        'whatsapp_reminder_enabled' => 'boolean',
        'last_whatsapp_reminder_at' => 'datetime',
    ];

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FinancialEntryPayment::class)->orderByDesc('payment_date')->orderByDesc('id');
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

    public static function paymentStatusLabels(): array
    {
        return [
            'pending' => 'Pendente',
            'partial' => 'Parcial',
            'paid' => 'Pago',
            'overdue' => 'Em atraso',
        ];
    }

    public function paidAmount(): float
    {
        $sum = $this->payments_sum_amount;

        if ($sum === null && $this->relationLoaded('payments')) {
            $sum = $this->payments->sum('amount');
        }

        return round((float) ($sum ?? 0), 2);
    }

    public function remainingAmount(): float
    {
        return max(0, round((float) $this->amount - $this->paidAmount(), 2));
    }

    public function paymentStatus(): string
    {
        if ($this->remainingAmount() <= 0.0) {
            return 'paid';
        }

        if ($this->paidAmount() > 0.0) {
            return 'partial';
        }

        if ($this->entry_date instanceof Carbon && $this->entry_date->isPast() && ! $this->entry_date->isToday()) {
            return 'overdue';
        }

        return 'pending';
    }

    public function whatsappReminderMessage(): string
    {
        $customerName = $this->customer?->name ?: 'cliente';
        $dueDate = $this->entry_date?->format('d/m/Y') ?: '-';

        return sprintf(
            'Ola, %s. Esta e uma cobranca referente a "%s". Vencimento: %s. Valor total: R$ %s. Valor pago: R$ %s. Valor pendente: R$ %s.',
            $customerName,
            $this->title,
            $dueDate,
            number_format((float) $this->amount, 2, ',', '.'),
            number_format($this->paidAmount(), 2, ',', '.'),
            number_format($this->remainingAmount(), 2, ',', '.')
        );
    }

    public function whatsappReminderUrl(): ?string
    {
        $phone = preg_replace('/\D/', '', (string) ($this->customer?->mobile_phone ?: $this->customer?->phone));

        if ($phone === '') {
            return null;
        }

        if (! str_starts_with($phone, '55')) {
            $phone = '55' . $phone;
        }

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($this->whatsappReminderMessage());
    }
}
