<?php

namespace App\Notifications\Auth;

use App\Notifications\Channels\WhatsAppChannel;
use App\Notifications\Messages\WhatsAppMessage;
use Illuminate\Auth\Notifications\ResetPassword;

class ResetPasswordNotification extends ResetPassword
{
    public function via($notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toWhatsApp(object $notifiable): WhatsAppMessage
    {
        $url = $this->resetUrl($notifiable);

        return new WhatsAppMessage(
            "Ola {$notifiable->name}, recebemos uma solicitacao para redefinir sua senha no JuriStack. "
            ."Use este link para cadastrar uma nova senha: {$url}"
        );
    }
}
