<?php

namespace App\Notifications;

use App\Models\ProcessoMonitor;
use App\Notifications\Channels\WhatsAppChannel;
use App\Notifications\Messages\WhatsAppMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProcessoAtualizadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $monitor;

    protected $ultimoMovimento;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProcessoMonitor $monitor, ?array $ultimoMovimento = null)
    {
        $this->monitor = $monitor;
        $this->ultimoMovimento = $ultimoMovimento;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', WhatsAppChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Processo #{$this->monitor->numero_processo} foi atualizado")
            ->greeting("Olá {$notifiable->name}!")
            ->line('O processo que você está monitorando foi atualizado.')
            ->line('**Tribunal:** '.$this->monitor->tribunal)
            ->line('**Número:** '.$this->monitor->numero_processo);

        if ($this->ultimoMovimento) {
            $mail->line('**Último movimento:** '.($this->ultimoMovimento['nome'] ?? 'N/A'))
                ->line('**Data:** '.($this->ultimoMovimento['dataHora'] ?? 'N/A'));
        }

        $mail->action('Ver Processo', url('/datajud/salvos'))
            ->line('Obrigado por usar o JuriStack!');

        return $mail;
    }

    public function toWhatsApp(object $notifiable): WhatsAppMessage
    {
        $message = [
            "Ola {$notifiable->name}, o processo {$this->monitor->numero_processo} foi atualizado.",
            'Tribunal: '.$this->monitor->tribunal,
        ];

        if ($this->ultimoMovimento) {
            $message[] = 'Ultimo movimento: '.($this->ultimoMovimento['nome'] ?? 'N/A');
            $message[] = 'Data: '.($this->ultimoMovimento['dataHora'] ?? 'N/A');
        }

        $message[] = 'Acompanhe em: '.url('/datajud/salvos');

        return new WhatsAppMessage(implode("\n", $message));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'monitor_id' => $this->monitor->id,
            'processo_id' => $this->monitor->processo_id,
            'tribunal' => $this->monitor->tribunal,
            'numero_processo' => $this->monitor->numero_processo,
            'movimento' => $this->ultimoMovimento,
        ];
    }
}
