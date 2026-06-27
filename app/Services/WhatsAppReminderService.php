<?php

namespace App\Services;

use App\Models\FinancialEntry;
use Illuminate\Support\Facades\Http;

class WhatsAppReminderService
{
    public function canSend(): bool
    {
        return (string) config('services.whatsapp.webhook_url', '') !== '';
    }

    public function send(FinancialEntry $entry): bool
    {
        $url = $entry->whatsappReminderUrl();
        $webhookUrl = (string) config('services.whatsapp.webhook_url', '');

        if ($url === null || $webhookUrl === '') {
            return false;
        }

        $request = Http::timeout(15);
        $token = (string) config('services.whatsapp.token', '');

        if ($token !== '') {
            $request = $request->withToken($token);
        }

        $request->post($webhookUrl, [
            'phone' => preg_replace('/\D/', '', (string) ($entry->customer?->mobile_phone ?: $entry->customer?->phone)),
            'message' => $entry->whatsappReminderMessage(),
            'whatsapp_url' => $url,
            'financial_entry_id' => $entry->id,
            'customer_name' => $entry->customer?->name,
            'due_date' => $entry->entry_date?->toDateString(),
            'total_amount' => (float) $entry->amount,
            'paid_amount' => $entry->paidAmount(),
            'remaining_amount' => $entry->remainingAmount(),
        ])->throw();

        $entry->forceFill([
            'last_whatsapp_reminder_at' => now(),
        ])->save();

        return true;
    }
}
