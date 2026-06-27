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

        $this->whatsAppService->sendTextSafely($route, $message->content, [
            'notification' => $notification::class,
            'notifiable' => $notifiable::class,
        ]);
    }
}
