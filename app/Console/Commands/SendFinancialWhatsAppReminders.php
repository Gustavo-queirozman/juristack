<?php

namespace App\Console\Commands;

use App\Models\FinancialEntry;
use App\Services\WhatsAppReminderService;
use Illuminate\Console\Command;

class SendFinancialWhatsAppReminders extends Command
{
    protected $signature = 'financial:send-whatsapp-reminders
        {--dry-run : Apenas listar cobrancas elegiveis}
        {--limit=100 : Numero maximo de cobrancas por execucao}';

    protected $description = 'Envia ou lista cobrancas de contas a receber via WhatsApp';

    public function __construct(private readonly WhatsAppReminderService $whatsAppReminderService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limit = max(1, (int) $this->option('limit'));

        $entries = FinancialEntry::query()
            ->where('entry_type', FinancialEntry::TYPE_RECEIVABLE)
            ->where('whatsapp_reminder_enabled', true)
            ->whereDate('entry_date', '<=', today())
            ->where(function ($query) {
                $query->whereNull('last_whatsapp_reminder_at')
                    ->orWhereDate('last_whatsapp_reminder_at', '<', today());
            })
            ->whereHas('customer', function ($query) {
                $query->whereNotNull('mobile_phone')
                    ->orWhereNotNull('phone');
            })
            ->with(['customer'])
            ->withSum('payments', 'amount')
            ->orderBy('entry_date')
            ->limit($limit)
            ->get();

        if ($entries->isEmpty()) {
            $this->info('Nenhuma cobranca elegivel para envio.');

            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($entries as $entry) {
            if ($entry->remainingAmount() <= 0.0) {
                continue;
            }

            if ($dryRun) {
                $this->line(sprintf(
                    '[DRY RUN] #%d %s -> %s',
                    $entry->id,
                    $entry->customer?->name ?? 'Sem cliente',
                    $entry->whatsappReminderUrl() ?? 'sem link'
                ));

                continue;
            }

            if (! $this->whatsAppReminderService->canSend()) {
                $this->warn('Evolution API nao configurada em services.evolution.');

                return self::FAILURE;
            }

            $this->whatsAppReminderService->send($entry);
            $this->info(sprintf('Cobranca enviada para o lancamento #%d.', $entry->id));
            $sent++;
        }

        if ($dryRun) {
            $this->info(sprintf('%d cobranca(s) listada(s).', $entries->count()));

            return self::SUCCESS;
        }

        $this->info(sprintf('%d cobranca(s) enviada(s).', $sent));

        return self::SUCCESS;
    }
}
