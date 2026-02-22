<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        Document::insert([
            [
                'title' => 'Procuração Geral',
                'type' => 'power_of_attorney',
                'date' => now(),
            ],
            [
                'title' => 'Contrato de Prestação de Serviços',
                'type' => 'contract',
                'date' => now(),
            ],
            [
                'title' => 'Petição Inicial',
                'type' => 'petition',
                'date' => now(),
            ],
        ]);
    }
}

