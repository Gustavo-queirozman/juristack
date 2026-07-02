<?php

namespace App\Console\Commands;

use App\Models\Enterprise;
use App\Services\SubscriptionBillingReminderService;
use Illuminate\Console\Command;

class SendOverdueSubscriptionPaymentReminders extends Command
{
    protected $signature = 'billing:send-overdue-payment-reminders
        {--dry-run : Apenas listar assinaturas elegiveis}
        {--limit=100 : Numero maximo de assinaturas por execucao}';

    protected $description = 'Envia lembretes por email para assinaturas SaaS com pagamento vencido';

    public function __construct(
        private readonly SubscriptionBillingReminderService $reminderService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limit = max(1, (int) $this->option('limit'));

        $enterprises = Enterprise::query()
            ->where(function ($query): void {
                $query->whereIn('subscription_status', $this->reminderService->overdueStatuses())
                    ->orWhere(function ($fallbackQuery): void {
                        $fallbackQuery->whereNotNull('subscription_plan_id')
                            ->whereNotNull('subscription_ends_at')
                            ->where('subscription_ends_at', '<', now())
                            ->where(function ($statusQuery): void {
                                $statusQuery->whereNull('subscription_status')
                                    ->orWhereNotIn('subscription_status', ['active', 'trialing', 'canceled']);
                            });
                    });
            })
            ->with(['subscriptionPlan', 'users'])
            ->orderByRaw('COALESCE(payment_overdue_since, subscription_ends_at) asc')
            ->limit($limit)
            ->get();

        if ($enterprises->isEmpty()) {
            $this->info('Nenhuma assinatura vencida elegivel para cobranca.');

            return self::SUCCESS;
        }

        $processed = 0;

        foreach ($enterprises as $enterprise) {
            $stage = $this->reminderService->resolveStage($enterprise);
            $daysOverdue = $this->reminderService->daysOverdue($enterprise);

            if ($stage === null || $daysOverdue === null || ! $this->reminderService->shouldSendReminder($enterprise, $stage)) {
                continue;
            }

            if ($dryRun) {
                $this->line(sprintf(
                    '[DRY RUN] #%d %s -> D+%d (etapa %d)',
                    $enterprise->id,
                    $enterprise->name,
                    $daysOverdue,
                    $stage,
                ));

                $processed++;

                continue;
            }

            if (! $this->reminderService->sendReminder($enterprise)) {
                continue;
            }

            $this->info(sprintf(
                'Cobranca enviada para a assinatura #%d (%s) na etapa %d.',
                $enterprise->id,
                $enterprise->name,
                $stage,
            ));
            $processed++;
        }

        if ($processed === 0) {
            $this->info('Nenhuma assinatura exigia novo lembrete nesta execucao.');

            return self::SUCCESS;
        }

        $this->info(sprintf('%d assinatura(s) processada(s).', $processed));

        return self::SUCCESS;
    }
}
