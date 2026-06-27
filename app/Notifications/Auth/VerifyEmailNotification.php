<?php

namespace App\Notifications\Auth;

use App\Notifications\Channels\WhatsAppChannel;
use App\Notifications\Messages\WhatsAppMessage;
use Illuminate\Auth\Notifications\VerifyEmail;

class VerifyEmailNotification extends VerifyEmail
{
    public function via($notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toWhatsApp(object $notifiable): WhatsAppMessage
    {
        return new WhatsAppMessage(
            "Ola {$notifiable->name}, confirme seu e-mail no JuriStack acessando este link: "
            .$this->verificationUrl($notifiable)
        );
    }
}
