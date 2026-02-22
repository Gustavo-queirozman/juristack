<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTemplate;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DocumentTemplate::upsert(
            [
                [
                    'type' => 'power_of_attorney',
                    'title' => 'Modelo de Procuração Geral',
                    'date' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'contract',
                    'title' => 'Modelo de Contrato de Prestação de Serviços',
                    'date' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'petition',
                    'title' => 'Modelo de Petição Inicial',
                    'date' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            ['type'],                 // chave única para “não duplicar”
            ['title', 'date', 'updated_at'] // campos que atualiza se já existir
        );
    }
}
