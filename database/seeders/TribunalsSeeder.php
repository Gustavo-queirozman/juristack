<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TribunalsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $tribunals = [
            // CNJ code => state court
            ['cnj_code' => 1,  'acronym' => 'TJAC',  'name_en' => 'Court of Justice of Acre State',                       'state_code' => 'AC', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjac.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 2,  'acronym' => 'TJAL',  'name_en' => 'Court of Justice of Alagoas State',                    'state_code' => 'AL', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjal.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 3,  'acronym' => 'TJAP',  'name_en' => 'Court of Justice of Amapá State',                      'state_code' => 'AP', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjap.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 4,  'acronym' => 'TJAM',  'name_en' => 'Court of Justice of Amazonas State',                   'state_code' => 'AM', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjam.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 5,  'acronym' => 'TJBA',  'name_en' => 'Court of Justice of Bahia State',                      'state_code' => 'BA', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjba.jus.br',  'public_search_url' => null, 'system' => 'e-SAJ'],
            ['cnj_code' => 6,  'acronym' => 'TJCE',  'name_en' => 'Court of Justice of Ceará State',                      'state_code' => 'CE', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjce.jus.br',  'public_search_url' => null, 'system' => 'e-SAJ'],
            ['cnj_code' => 7,  'acronym' => 'TJDFT', 'name_en' => 'Court of Justice of the Federal District and Territories','state_code' => 'DF', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjdft.jus.br', 'public_search_url' => null, 'system' => 'PJe'],
            ['cnj_code' => 8,  'acronym' => 'TJES',  'name_en' => 'Court of Justice of Espírito Santo State',             'state_code' => 'ES', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjes.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 9,  'acronym' => 'TJGO',  'name_en' => 'Court of Justice of Goiás State',                      'state_code' => 'GO', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjgo.jus.br',  'public_search_url' => null, 'system' => 'Projudi'],
            ['cnj_code' => 10, 'acronym' => 'TJMA',  'name_en' => 'Court of Justice of Maranhão State',                   'state_code' => 'MA', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjma.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 11, 'acronym' => 'TJMT',  'name_en' => 'Court of Justice of Mato Grosso State',               'state_code' => 'MT', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjmt.jus.br',  'public_search_url' => null, 'system' => 'PJe'],
            ['cnj_code' => 12, 'acronym' => 'TJMS',  'name_en' => 'Court of Justice of Mato Grosso do Sul State',        'state_code' => 'MS', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjms.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 13, 'acronym' => 'TJMG',  'name_en' => 'Court of Justice of Minas Gerais State',              'state_code' => 'MG', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjmg.jus.br',  'public_search_url' => 'https://pje-consulta-publica.tjmg.jus.br', 'system' => 'PJe'],
            ['cnj_code' => 14, 'acronym' => 'TJPA',  'name_en' => 'Court of Justice of Pará State',                      'state_code' => 'PA', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjpa.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 15, 'acronym' => 'TJPB',  'name_en' => 'Court of Justice of Paraíba State',                   'state_code' => 'PB', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjpb.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 16, 'acronym' => 'TJPR',  'name_en' => 'Court of Justice of Paraná State',                    'state_code' => 'PR', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjpr.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 17, 'acronym' => 'TJPE',  'name_en' => 'Court of Justice of Pernambuco State',                'state_code' => 'PE', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjpe.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 18, 'acronym' => 'TJPI',  'name_en' => 'Court of Justice of Piauí State',                     'state_code' => 'PI', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjpi.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 19, 'acronym' => 'TJRJ',  'name_en' => 'Court of Justice of Rio de Janeiro State',            'state_code' => 'RJ', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjrj.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 20, 'acronym' => 'TJRN',  'name_en' => 'Court of Justice of Rio Grande do Norte State',       'state_code' => 'RN', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjrn.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 21, 'acronym' => 'TJRS',  'name_en' => 'Court of Justice of Rio Grande do Sul State',         'state_code' => 'RS', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjrs.jus.br',  'public_search_url' => null, 'system' => 'eproc'],
            ['cnj_code' => 22, 'acronym' => 'TJRO',  'name_en' => 'Court of Justice of Rondônia State',                  'state_code' => 'RO', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjro.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 23, 'acronym' => 'TJRR',  'name_en' => 'Court of Justice of Roraima State',                   'state_code' => 'RR', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjrr.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 24, 'acronym' => 'TJSC',  'name_en' => 'Court of Justice of Santa Catarina State',            'state_code' => 'SC', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjsc.jus.br',  'public_search_url' => null, 'system' => 'e-SAJ'],
            ['cnj_code' => 25, 'acronym' => 'TJSE',  'name_en' => 'Court of Justice of Sergipe State',                   'state_code' => 'SE', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjse.jus.br',  'public_search_url' => null, 'system' => null],
            ['cnj_code' => 26, 'acronym' => 'TJSP',  'name_en' => 'Court of Justice of São Paulo State',                 'state_code' => 'SP', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjsp.jus.br',  'public_search_url' => 'https://esaj.tjsp.jus.br/cpopg/open.do', 'system' => 'e-SAJ'],
            ['cnj_code' => 27, 'acronym' => 'TJTO',  'name_en' => 'Court of Justice of Tocantins State',                 'state_code' => 'TO', 'country_code' => 'BR', 'homepage_url' => 'https://www.tjto.jus.br',  'public_search_url' => null, 'system' => null],
        ];

        DB::table('tribunals')->upsert(
            array_map(fn ($t) => array_merge($t, ['created_at' => $now, 'updated_at' => $now]), $tribunals),
            ['cnj_code'],
            ['acronym', 'name_en', 'state_code', 'country_code', 'homepage_url', 'public_search_url', 'system', 'updated_at']
        );
    }
}

