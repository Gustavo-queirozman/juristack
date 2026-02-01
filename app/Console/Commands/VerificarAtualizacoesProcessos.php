<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProcessoMonitor;
use App\Models\DatajudProcesso;
use App\Services\DataJudService;
use App\Services\DatajudPersistService;
use App\Notifications\ProcessoAtualizadoNotification;

class VerificarAtualizacoesProcessos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datajud:monitor-updates {--limit=50 : Número máximo de processos a verificar por execução}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica atualizações de processos monitorados via DataJud API e notifica usuários';

    protected $datajudService;
    protected $persistService;

    public function __construct(DataJudService $datajudService, DatajudPersistService $persistService)
    {
        parent::__construct();
        $this->datajudService = $datajudService;
        $this->persistService = $persistService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        // Buscar processos ativos não verificados recentemente
        $monitores = ProcessoMonitor::where('ativo', true)
            ->where(function ($q) {
                $q->whereNull('ultima_verificacao')
                  ->orWhere('ultima_verificacao', '<', now()->subHours(6));
            })
            ->with(['processo', 'usuario'])
            ->limit($limit)
            ->get();

        if ($monitores->isEmpty()) {
            $this->info('Nenhum processo para verificar.');
            return Command::SUCCESS;
        }

        $this->info("Verificando {$monitores->count()} processos...");
        $atualizados = 0;

        foreach ($monitores as $monitor) {
            try {
                $tribunal = $monitor->tribunal;
                $numero = $monitor->numero_processo;

                $this->line("Verificando {$tribunal}::{$numero}");

                // Buscar dados atualizados via API
                $resp = $this->datajudService->searchByProcess($tribunal, $numero, 0, 1);
                
                if (empty($resp) || empty($resp['hits']['hits'])) {
                    $this->warn("  Sem resultados");
                    $monitor->update(['ultima_verificacao' => now()]);
                    continue;
                }

                $hit = $resp['hits']['hits'][0];
                $source = $hit['_source'] ?? [];

                // Comparar última atualização no DataJud
                $novaData = $source['dataHoraUltimaAtualizacao'] ?? null;
                
                if ($novaData && $monitor->ultima_atualizacao_datajud) {
                    $anterior = strtotime($monitor->ultima_atualizacao_datajud);
                    $nova = strtotime($novaData);

                    if ($nova > $anterior) {
                        // Houve atualização!
                        $this->info("  ✓ Processo atualizado!");
                        
                        // Atualizar no banco
                        $processo = $this->persistService->salvarProcesso(
                            $source,
                            $tribunal,
                            $monitor->user_id
                        );

                        // Atualizar monitor
                        $monitor->update([
                            'ultima_verificacao' => now(),
                            'ultima_atualizacao_datajud' => $novaData,
                            'verificacoes_consecutivas_sem_mudanca' => 0,
                        ]);

                        // Notificar usuário
                        if ($monitor->usuario) {
                            $ultimoMovimento = $source['movimentos'][0] ?? null;
                            $monitor->usuario->notify(
                                new ProcessoAtualizadoNotification($monitor, $ultimoMovimento)
                            );
                        }

                        $atualizados++;
                    } else {
                        // Sem alteração
                        $monitor->increment('verificacoes_consecutivas_sem_mudanca');
                        $monitor->update(['ultima_verificacao' => now()]);
                    }
                } else {
                    // Primeira verificação ou sem data
                    $monitor->update([
                        'ultima_verificacao' => now(),
                        'ultima_atualizacao_datajud' => $novaData,
                    ]);
                }

            } catch (\Exception $e) {
                $this->error("  Erro ao verificar: " . $e->getMessage());
                $monitor->update(['ultima_verificacao' => now()]);
            }
        }

        $this->info("✓ Verificação concluída. {$atualizados} processo(s) atualizado(s).");
        return Command::SUCCESS;
    }
}

