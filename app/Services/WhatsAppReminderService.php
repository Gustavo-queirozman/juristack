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
        $entry->loadMissing('customer.enterprise');

        $phone = $entry->customer?->mobile_phone ?: $entry->customer?->phone;
        $instance = $entry->customer?->enterprise?->evolution_instance;

        if (! $this->evolutionWhatsAppService->canSendTo($phone)
            || ! $this->evolutionWhatsAppService->isConfigured($instance)) {
            return false;
        }

        $this->evolutionWhatsAppService->sendText($phone, $entry->whatsappReminderMessage(), $instance);

        $entry->forceFill([
            'last_whatsapp_reminder_at' => now(),
        ])->save();

        return true;
    }
}
