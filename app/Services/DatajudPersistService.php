<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DatajudProcesso;

class DatajudPersistService
{
    public function salvarProcesso(array $source, string $tribunal, ?int $userId = null)
    {
        $numeroProcesso = $source['numeroProcesso'] ?? null;

        $ctxBase = [
            'tribunal' => $tribunal,
            'user_id' => $userId,
            'numero_processo' => $numeroProcesso,
            'grau' => $source['grau'] ?? null,
            'datajud_id' => $source['id'] ?? null,
        ];

        $t0 = microtime(true);

        Log::info('DatajudPersist: iniciar persistência do processo', $ctxBase);

        try {
            $processo = DB::transaction(function () use ($source, $tribunal, $userId, $ctxBase) {

                // normalizar datas principais
                $dataAjuiz = $this->normalizeDate(data_get($source, 'dataAjuizamento'));
                $dataHoraUlt = $this->normalizeDate($source['dataHoraUltimaAtualizacao'] ?? $source['dataHora'] ?? data_get($source, '@timestamp'));
                $indexedAt = $this->normalizeDate(data_get($source, '@timestamp'));

                Log::debug('DatajudPersist: datas normalizadas', $ctxBase + [
                    'data_ajuizamento' => $dataAjuiz,
                    'datahora_ultima_atualizacao' => $dataHoraUlt,
                    'indexed_at' => $indexedAt,
                ]);

                $processo = DatajudProcesso::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'tribunal' => $tribunal,
                        'numero_processo' => $source['numeroProcesso'],
                        'grau' => $source['grau'] ?? null,
                    ],
                    [
                        'datajud_id' => $source['id'] ?? null,
                        'data_ajuizamento' => $dataAjuiz,
                        'nivel_sigilo' => $source['nivelSigilo'] ?? null,
                        'classe_codigo' => data_get($source, 'classe.codigo'),
                        'classe_nome' => data_get($source, 'classe.nome'),
                        'formato_codigo' => data_get($source, 'formato.codigo'),
                        'formato_nome' => data_get($source, 'formato.nome'),
                        'sistema_codigo' => data_get($source, 'sistema.codigo'),
                        'sistema_nome' => data_get($source, 'sistema.nome'),
                        'orgao_julgador_codigo' => data_get($source, 'orgaoJulgador.codigo'),
                        'orgao_julgador_nome' => data_get($source, 'orgaoJulgador.nome'),
                        'orgao_julgador_codigo_municipio_ibge' => data_get($source, 'orgaoJulgador.codigoMunicipioIBGE'),
                        'datahora_ultima_atualizacao' => $dataHoraUlt,
                        'indexed_at' => $indexedAt,
                        'payload' => $source,
                    ]
                );

                Log::info('DatajudPersist: processo upsert OK', $ctxBase + [
                    'processo_id' => $processo->id,
                    'was_recently_created' => $processo->wasRecentlyCreated,
                ]);

                // assuntos
                $assuntosCount = count($source['assuntos'] ?? []);
                Log::debug('DatajudPersist: atualizando assuntos', $ctxBase + [
                    'processo_id' => $processo->id,
                    'assuntos_count' => $assuntosCount,
                ]);

                $processo->assuntos()->delete();
                foreach ($source['assuntos'] ?? [] as $assunto) {
                    $processo->assuntos()->create([
                        'codigo' => $assunto['codigo'] ?? null,
                        'nome' => $assunto['nome'] ?? null,
                    ]);
                }

                // movimentos
                $movimentos = $source['movimentos'] ?? [];
                Log::debug('DatajudPersist: atualizando movimentos', $ctxBase + [
                    'processo_id' => $processo->id,
                    'movimentos_count' => count($movimentos),
                ]);

                foreach ($movimentos as $i => $mov) {
                    $movDataHora = $this->normalizeDate($mov['dataHora'] ?? $mov['data'] ?? null);

                    $movCtx = $ctxBase + [
                        'processo_id' => $processo->id,
                        'mov_index' => $i,
                        'mov_codigo' => $mov['codigo'] ?? null,
                        'mov_data_hora' => $movDataHora,
                    ];

                    $movimento = $processo->movimentos()->updateOrCreate(
                        [
                            'codigo' => $mov['codigo'] ?? null,
                            'data_hora' => $movDataHora,
                        ],
                        [
                            'nome' => $mov['nome'] ?? $mov['descricao'] ?? null,
                            'orgao_codigo' => data_get($mov, 'orgaoJulgador.codigoOrgao'),
                            'orgao_nome' => data_get($mov, 'orgaoJulgador.nomeOrgao'),
                        ]
                    );

                    Log::debug('DatajudPersist: movimento upsert OK', $movCtx + [
                        'movimento_id' => $movimento->id,
                    ]);

                    // complementos
                    $comps = $mov['complementosTabelados'] ?? [];
                    if (!empty($comps)) {
                        Log::debug('DatajudPersist: atualizando complementos do movimento', $movCtx + [
                            'complementos_count' => count($comps),
                        ]);
                    }

                    foreach ($comps as $c) {
                        $movimento->complementos()->updateOrCreate(
                            [
                                'codigo' => $c['codigo'] ?? null,
                            ],
                            [
                                'descricao' => $c['descricao'] ?? null,
                                'valor' => $c['valor'] ?? null,
                                'nome' => $c['nome'] ?? null,
                            ]
                        );
                    }
                }

                return $processo;
            });

            $ms = (int) round((microtime(true) - $t0) * 1000);

            Log::info('DatajudPersist: finalizado com sucesso', [
                'processo_id' => $processo->id,
                'tribunal' => $tribunal,
                'user_id' => $userId,
                'numero_processo' => $numeroProcesso,
                'duration_ms' => $ms,
            ]);

            return $processo;
        } catch (\Throwable $e) {
            $ms = (int) round((microtime(true) - $t0) * 1000);

            Log::error('DatajudPersist: falha ao persistir processo', $ctxBase + [
                'duration_ms' => $ms,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            throw $e;
        }
    }

    private function normalizeDate($value)
    {
        if (empty($value)) return null;

        if ($value instanceof Carbon) {
            return $value->toDateTimeString();
        }

        $v = (string) $value;

        try {
            if (preg_match('/^[0-9]{14}$/', $v)) {
                return Carbon::createFromFormat('YmdHis', $v)->toDateTimeString();
            }

            if (strpos($v, 'T') !== false || strpos($v, '-') !== false) {
                return Carbon::parse($v)->toDateTimeString();
            }

            if (is_numeric($v)) {
                $num = (int) $v;
                if ($num > 1000000000000) {
                    return Carbon::createFromTimestampMs($num)->toDateTimeString();
                }
                return Carbon::createFromTimestamp($num)->toDateTimeString();
            }

            return Carbon::parse($v)->toDateTimeString();
        } catch (\Throwable $e) {
            Log::warning('DatajudPersist: normalizeDate falhou', [
                'value' => $value,
                'value_type' => is_object($value) ? get_class($value) : gettype($value),
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
