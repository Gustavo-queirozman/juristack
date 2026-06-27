<?php

namespace App\Services;

use App\Models\FinancialEntry;

class WhatsAppReminderService
{
    public function __construct(
        private readonly EvolutionWhatsAppService $evolutionWhatsAppService,
    ) {}

    public function canSend(): bool
    {
        return $this->evolutionWhatsAppService->isConfigured();
    }

    public function send(FinancialEntry $entry): bool
    {
        $phone = $entry->customer?->mobile_phone ?: $entry->customer?->phone;

        if (! $this->evolutionWhatsAppService->canSendTo($phone) || ! $this->canSend()) {
            return false;
        }

        $this->evolutionWhatsAppService->sendText($phone, $entry->whatsappReminderMessage());

        $entry->forceFill([
            'last_whatsapp_reminder_at' => now(),
        ])->save();

        return true;
    }
}
