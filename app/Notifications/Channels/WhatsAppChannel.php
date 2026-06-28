<?php

namespace App\Notifications\Channels;

use App\Notifications\Messages\WhatsAppMessage;
use App\Services\EvolutionWhatsAppService;

class WhatsAppChannel
{
    public function __construct(
        private readonly EvolutionWhatsAppService $whatsAppService,
    ) {}

    public function send(object $notifiable, object $notification): void
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $route = $notifiable->routeNotificationFor('whatsapp', $notification);
        $message = $notification->toWhatsApp($notifiable);

        if (! $message instanceof WhatsAppMessage || blank($message->content) || blank($route)) {
            return;
        }

        $instance = $this->resolveInstance($notifiable);

        if (! $this->whatsAppService->isConfigured($instance)) {
            return;
        }

        $this->whatsAppService->sendTextSafely($route, $message->content, [
            'notification' => $notification::class,
            'notifiable' => $notifiable::class,
        ], $instance);
    }

    private function resolveInstance(object $notifiable): ?string
    {
        return data_get($notifiable, 'enterprise.evolution_instance')
            ?: data_get($notifiable, 'customerProfile.enterprise.evolution_instance')
            ?: data_get($notifiable, 'evolution_instance');
    }
}
