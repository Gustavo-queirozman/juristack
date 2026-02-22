<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'title' => 'Procuração (geral)',
                'type' => 'power_of_attorney',
                'description' => 'Modelo de procuração pública/particular para outorga de poderes a advogado(a).',
                'date' => now(),
                'content' => '<p style="text-align: center; font-weight: bold; margin-bottom: 1.5em; font-size: 1.1em;">PROCURAÇÃO</p>
<p><strong>OUTORGANTE:</strong> {{nome_outorgante}}, {{nacionalidade}}, {{estado_civil}}, {{profissao}}, portador(a) da Cédula de Identidade sob o nº {{rg}} e inscrito(a) no Cadastro de Pessoas Físicas do Ministério da Fazenda sob o nº {{cpf}}, residente e domiciliado(a) à {{endereco_outorgante}}.</p>
<p><strong>OUTORGADO:</strong> {{nome_advogado}}, advogado(a) inscrito(a) na Ordem dos Advogados do Brasil, Seccional {{uf_oab}}, sob o nº {{numero_oab}}, com escritório profissional na {{endereco_escritorio}}.</p>
<p>O <strong>OUTORGANTE</strong> nomeia e constitui seu bastante procurador o <strong>OUTORGADO</strong>, para que em seu nome e representação possa:</p>
<ul>
<li>Representar o outorgante perante quaisquer instâncias, juízos, tribunais, autoridades administrativas e órgãos públicos, em âmbito federal, estadual ou municipal;</li>
<li>Praticar todos os atos processuais e extraprocessuais necessários ao fiel cumprimento do mandato, incluindo transações, acordos e renúncias, quando conveniente;</li>
<li>Receber citações, intimações, notificações e demais comunicações judiciais e extrajudiciais;</li>
<li>Substabelecer esta procuração com ou sem reserva de poderes, em caráter gratuito ou oneroso.</li>
</ul>
<p>O outorgante ratifica e aprova tudo o que o outorgado fizer em conformidade com o presente mandato.</p>
<p style="margin-top: 2em;">{{cidade}}, {{data}}.</p>
<p style="margin-top: 2.5em; text-align: center;">_________________________<br>{{nome_outorgante}}<br><em>Outorgante</em></p>',
            ],
            [
                'title' => 'Procuração (especial para processo)',
                'type' => 'power_of_attorney',
                'description' => 'Procuração ad judicia para atuação em processo judicial específico.',
                'date' => now(),
                'content' => '<p style="text-align: center; font-weight: bold; margin-bottom: 1.5em; font-size: 1.1em;">PROCURAÇÃO AD JUDICIA</p>
<p><strong>OUTORGANTE:</strong> {{nome_outorgante}}, portador(a) do RG nº {{rg}} e inscrito(a) no CPF sob o nº {{cpf}}, residente e domiciliado(a) à {{endereco_outorgante}}.</p>
<p><strong>OUTORGADO:</strong> {{nome_advogado}}, advogado(a) inscrito(a) na OAB/{{uf_oab}} sob o nº {{numero_oab}}.</p>
<p>O <strong>OUTORGANTE</strong> constitui o <strong>OUTORGADO</strong> como seu procurador para representá-lo na condição de parte em processo judicial, conferindo-lhe os poderes necessários para:</p>
<ul>
<li>Praticar todos os atos processuais em nome do outorgante, em qualquer grau de jurisdição;</li>
<li>Receber citações, intimações e notificações, constituindo-se em morada eletrônica e física quando cabível;</li>
<li>Propor e contestar ações, interpor recursos, requerer medidas de urgência e praticar os demais atos inerentes ao mandato judicial.</li>
</ul>
<p>O outorgante ratifica e aprova os atos que o outorgado praticar no exercício deste mandato.</p>
<p style="margin-top: 2em;">{{cidade}}, {{data}}.</p>
<p style="margin-top: 2.5em; text-align: center;">_________________________<br>{{nome_outorgante}}<br><em>Outorgante</em></p>',
            ],
            [
                'title' => 'Contrato de prestação de serviços advocatícios (honorários)',
                'type' => 'contract',
                'description' => 'Contrato de honorários entre advogado e cliente.',
                'date' => now(),
                'content' => "CONTRATO DE PRESTAÇÃO DE SERVIÇOS ADVOCATÍCIOS\n\nCONTRATANTE: {{nome_cliente}}\nCPF {{cpf}}, residente em {{endereco_cliente}}.\n\nCONTRATADO: {{nome_advogado}}\nOAB {{uf_oab}} nº {{numero_oab}}, com escritório em {{endereco_escritorio}}.\n\nO CONTRATANTE contrata os serviços advocatícios do CONTRATADO para {{objeto_contrato}}.\n\nHONORÁRIOS: {{valor_honorarios}} ({{valor_por_extenso}}), conforme forma de pagamento acordada.\n\nO CONTRATANTE declara estar ciente das condições e aceita os termos deste contrato.\n\n{{cidade}}, {{data}}.\n\n_________________________     _________________________\nCONTRATANTE                    CONTRATADO",
            ],
            [
                'title' => 'Petição inicial (modelo)',
                'type' => 'petition',
                'description' => 'Modelo de petição inicial para ajuizamento de ação.',
                'date' => now(),
                'content' => "EXCELENTÍSSIMO(A) SENHOR(A) DOUTOR(A) JUIZ(A) DE DIREITO DA ___ VARA CÍVEL DA COMARCA DE {{comarca}}\n\n{{nome_autor}}, já devidamente qualificado nos autos da ação de {{tipo_acao}}, vem, por seu advogado que esta subscreve (procuração em anexo), com escritório profissional na {{endereco_escritorio}}, OAB {{uf_oab}} nº {{numero_oab}}, respeitosamente à presença de Vossa Excelência, propor a presente\n\nAÇÃO DE {{tipo_acao}}\n\nem face de {{nome_reu}}, pelos fatos e fundamentos a seguir expostos:\n\nI – DOS FATOS\n\n{{narrativa_fatos}}\n\nII – DO DIREITO\n\n{{fundamentacao_juridica}}\n\nIII – DOS PEDIDOS\n\nAnte o exposto, requer:\n\n{{pedidos}}\n\nDá-se à causa o valor de R$ {{valor_causa}}.\n\n{{cidade}}, {{data}}.\n\n{{nome_advogado}}\nOAB {{uf_oab}} nº {{numero_oab}}",
            ],
            [
                'title' => 'Contestação (modelo)',
                'type' => 'contestation',
                'description' => 'Modelo de contestação para defesa em ação judicial.',
                'date' => now(),
                'content' => "EXCELENTÍSSIMO(A) SENHOR(A) DOUTOR(A) JUIZ(A) DE DIREITO DA ___ VARA CÍVEL DA COMARCA DE {{comarca}}\n\n{{nome_reu}}, já qualificado nos autos do processo em epígrafe, vem, por seu advogado (procuração em anexo), OAB {{uf_oab}} nº {{numero_oab}}, apresentar\n\nCONTESTAÇÃO\n\nà ação de {{tipo_acao}} proposta por {{nome_autor}}, pelos fatos e fundamentos a seguir:\n\nI – PRELIMINARMENTE\n\n{{preliminares}}\n\nII – DO MÉRITO\n\n{{defesa_merito}}\n\nIII – DOS PEDIDOS\n\nAnte o exposto, requer:\n\n{{pedidos_defesa}}\n\n{{cidade}}, {{data}}.\n\n{{nome_advogado}}\nOAB {{uf_oab}} nº {{numero_oab}}",
            ],
            [
                'title' => 'Declaração de comparecimento',
                'type' => 'declaration',
                'description' => 'Declaração de comparecimento para fins diversos.',
                'date' => now(),
                'content' => "DECLARAÇÃO DE COMPARECIMENTO\n\nDeclaro, para os devidos fins, que {{nome_declarante}}, portador(a) do RG nº {{rg}} e CPF nº {{cpf}}, compareceu a este escritório em {{data}}, às {{hora}}, para {{finalidade}}.\n\n{{cidade}}, {{data}}.\n\n_________________________\n{{nome_advogado}}\nOAB {{uf_oab}} nº {{numero_oab}}",
            ],
            [
                'title' => 'Declaração de inexistência de débitos',
                'type' => 'declaration',
                'description' => 'Declaração de que não existem débitos ou pendências.',
                'date' => now(),
                'content' => "DECLARAÇÃO\n\n{{nome_declarante}}, brasileiro(a), {{estado_civil}}, {{profissao}}, portador(a) do RG nº {{rg}} e do CPF nº {{cpf}}, residente em {{endereco}}, declara sob as penas da lei que:\n\nNão possui débitos ou pendências junto a {{orgao_instituicao}}, relativamente a {{objeto_declaracao}}.\n\nEsta declaração é válida para fins de {{finalidade}}.\n\n{{cidade}}, {{data}}.\n\n_________________________\n{{nome_declarante}}",
            ],
            [
                'title' => 'Declaração de residência',
                'type' => 'declaration',
                'description' => 'Declaração de endereço de residência.',
                'date' => now(),
                'content' => "DECLARAÇÃO DE RESIDÊNCIA\n\n{{nome_declarante}}, portador(a) do CPF nº {{cpf}} e do RG nº {{rg}}, declara, para os fins que se fizerem necessários, que reside no endereço: {{endereco_completo}}, desde {{data_inicio_residencia}}.\n\n{{cidade}}, {{data}}.\n\n_________________________\n{{nome_declarante}}",
            ],
        ];

        foreach ($templates as $data) {
            DocumentTemplate::updateOrCreate(
                ['title' => $data['title'], 'type' => $data['type']],
                $data
            );
        }
    }
}
