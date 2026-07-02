<?php

namespace App\Notifications;

use App\Models\Enterprise;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionPaymentOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Enterprise $enterprise,
        private readonly int $daysOverdue,
        private readonly int $stage,
        private readonly string $actionUrl,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $recipientName = $notifiable->name ?? $this->enterprise->name ?? 'cliente';
        $planName = $this->enterprise->subscriptionPlan?->name ?? 'plano contratado';
        $statusLabel = match ($this->enterprise->subscription_status) {
            'past_due' => 'pagamento pendente',
            'unpaid' => 'assinatura em aberto',
            'incomplete' => 'assinatura aguardando pagamento',
            'incomplete_expired' => 'assinatura nao ativada',
            default => 'assinatura vencida',
        };

        $message = (new MailMessage)
            ->subject($this->subject())
            ->greeting("Ola {$recipientName}!")
            ->line("Identificamos {$statusLabel} do {$planName}.")
            ->line($this->overdueLine())
            ->line('Para evitar bloqueio do acesso, atualize a forma de pagamento ou quite a fatura pendente o quanto antes.')
            ->action('Regularizar assinatura', $this->actionUrl)
            ->line($this->stageLine());

        if ($this->enterprise->subscription_ends_at) {
            $message->line('Ciclo atual registrado ate: '.$this->enterprise->subscription_ends_at->format('d/m/Y'));
        }

        return $message->line('Se o pagamento ja foi realizado, desconsidere esta mensagem.');
    }

    private function subject(): string
    {
        return match ($this->stage) {
            14 => 'Aviso final: regularize o pagamento do seu plano JuriStack',
            7 => 'Urgente: seu plano JuriStack segue com pagamento pendente',
            3 => 'Lembrete: pagamento pendente do seu plano JuriStack',
            default => 'Pagamento pendente do seu plano JuriStack',
        };
    }

    private function overdueLine(): string
    {
        if ($this->daysOverdue <= 0) {
            return 'O vencimento ocorreu hoje e o sistema iniciou a regua automatica de cobranca.';
        }

        if ($this->daysOverdue === 1) {
            return 'O pagamento esta pendente ha 1 dia.';
        }

        return "O pagamento esta pendente ha {$this->daysOverdue} dias.";
    }

    private function stageLine(): string
    {
        return match ($this->stage) {
            14 => 'Este e o ultimo aviso da regua padrao de cobranca antes de medidas mais restritivas no ciclo de assinatura.',
            7 => 'Este aviso marca a etapa critica da regua de cobranca e exige regularizacao prioritaria.',
            3 => 'Este e um segundo lembrete automatico da regua de cobranca por inadimplencia.',
            default => 'Este e o primeiro lembrete automatico da regua de cobranca por inadimplencia.',
        };
    }
}
