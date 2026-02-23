<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DatajudProcesso;

class DatajudPersistService
{
    public function salvarProcesso(array $source, string $tribunal, ?int $userId = null)
    {
        return DB::transaction(function () use ($source, $tribunal, $userId) {

            // normalizar datas principais
            $dataAjuiz = $this->normalizeDate(data_get($source, 'dataAjuizamento'));
            $dataHoraUlt = $this->normalizeDate($source['dataHoraUltimaAtualizacao'] ?? $source['dataHora'] ?? data_get($source, '@timestamp'));
            $indexedAt = $this->normalizeDate(data_get($source, '@timestamp'));

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

            // assuntos
            $processo->assuntos()->delete();
            foreach ($source['assuntos'] ?? [] as $assunto) {
                $processo->assuntos()->create([
                    'codigo' => $assunto['codigo'] ?? null,
                    'nome' => $assunto['nome'] ?? null,
                ]);
            }

            // movimentos
            foreach ($source['movimentos'] ?? [] as $mov) {
                $movDataHora = $this->normalizeDate($mov['dataHora'] ?? $mov['data'] ?? null);
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

                // complementos
                foreach ($mov['complementosTabelados'] ?? [] as $c) {
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
    }

    private function normalizeDate($value)
    {
        if (empty($value)) return null;

        // already Carbon instance
        if ($value instanceof Carbon) {
            return $value->toDateTimeString();
        }

        // numeric string like YmdHis -> 20251118164655
        $v = (string) $value;
        // remove non-digit except TZ and separators
        $clean = preg_replace('/[^0-9T:\-+.Z]/', '', $v);

        try {
            if (preg_match('/^[0-9]{14}$/', $v)) {
                $dt = Carbon::createFromFormat('YmdHis', $v);
                return $dt->toDateTimeString();
            }

            // ISO 8601 or RFC formats
            if (strpos($v, 'T') !== false || strpos($v, '-') !== false) {
                $dt = Carbon::parse($v);
                return $dt->toDateTimeString();
            }

            // pure numeric maybe timestamp seconds
            if (is_numeric($v)) {
                $num = (int) $v;
                // if looks like milliseconds
                if ($num > 1000000000000) {
                    $dt = Carbon::createFromTimestampMs($num);
                    return $dt->toDateTimeString();
                }
                // seconds
                $dt = Carbon::createFromTimestamp($num);
                return $dt->toDateTimeString();
            }

            // fallback parse
            $dt = Carbon::parse($v);
            return $dt->toDateTimeString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
