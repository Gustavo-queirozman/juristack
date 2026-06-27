<?php

namespace App\Notifications;

use App\Models\CustomerDocumentRequest;
use App\Notifications\Channels\WhatsAppChannel;
use App\Notifications\Messages\WhatsAppMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerDocumentRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected CustomerDocumentRequest $documentRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $customerName = $this->documentRequest->customer?->name ?: ($notifiable->name ?? 'cliente');
        $processo = $this->documentRequest->processo;

        $mail = (new MailMessage)
            ->subject('Documentos solicitados pelo escritorio')
            ->greeting("Ola {$customerName}!")
            ->line('O escritorio solicitou o envio de um documento pelo portal do cliente.')
            ->line('Documento solicitado: '.$this->documentRequest->document_type_label);

        if ($processo) {
            $mail->line('Processo vinculado: '.$processo->numero_processo.($processo->tribunal ? ' - '.$processo->tribunal : ''));
        }

        if ($this->documentRequest->description) {
            $mail->line('Orientacoes: '.$this->documentRequest->description);
        }

        return $mail
            ->action('Acessar portal do cliente', url('/dashboard'))
            ->line('Assim que o arquivo for enviado, a solicitacao sera marcada como atendida.');
    }

    public function toWhatsApp(object $notifiable): WhatsAppMessage
    {
        $customerName = $this->documentRequest->customer?->name ?: ($notifiable->name ?? 'cliente');
        $processo = $this->documentRequest->processo;

        $lines = [
            "Ola {$customerName}, o escritorio solicitou um documento no portal do cliente.",
            'Documento solicitado: '.$this->documentRequest->document_type_label,
        ];

        if ($processo) {
            $lines[] = 'Processo vinculado: '.$processo->numero_processo.($processo->tribunal ? ' - '.$processo->tribunal : '');
        }

        if ($this->documentRequest->description) {
            $lines[] = 'Orientacoes: '.$this->documentRequest->description;
        }

        $lines[] = 'Acesse: '.url('/dashboard');

        return new WhatsAppMessage(implode("\n", $lines));
    }
}
