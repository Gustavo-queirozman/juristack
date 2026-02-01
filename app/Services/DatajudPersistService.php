<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\DatajudProcesso;

class DatajudPersistService
{
    public function salvarProcesso(array $source, string $tribunal, ?int $userId = null)
    {
        return DB::transaction(function () use ($source, $tribunal, $userId) {

            $processo = DatajudProcesso::updateOrCreate(
                [
                    'user_id' => $userId,
                    'tribunal' => $tribunal,
                    'numero_processo' => $source['numeroProcesso'],
                    'grau' => $source['grau'] ?? null,
                ],
                [
                    'datajud_id' => $source['id'] ?? null,
                    'data_ajuizamento' => $source['dataAjuizamento'] ?? null,
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
                    'datahora_ultima_atualizacao' => $source['dataHoraUltimaAtualizacao'] ?? null,
                    'indexed_at' => $source['@timestamp'] ?? null,
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
                $movimento = $processo->movimentos()->updateOrCreate(
                    [
                        'codigo' => $mov['codigo'] ?? null,
                        'data_hora' => $mov['dataHora'] ?? null,
                    ],
                    [
                        'nome' => $mov['nome'] ?? null,
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
}
