<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'description',
        'date',
        'content',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Tipos permitidos
    public const TYPES = [
        'power_of_attorney' => 'Procuração',
        'contract' => 'Contrato',
        'petition' => 'Petição',
        'contestation' => 'Contestação',
        'declaration' => 'Declaração',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Extrai os nomes dos placeholders do conteúdo (ex: {{nome}} → nome).
     * Retorna array único e ordenado.
     */
    public function getPlaceholders(): array
    {
        if (empty($this->content)) {
            return [];
        }
        if (preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $this->content, $matches)) {
            $placeholders = array_unique($matches[1]);
            sort($placeholders);
            return array_values($placeholders);
        }
        return [];
    }

    /**
     * Label amigável para o placeholder (ex: nome_outorgante → Nome do outorgante).
     */
    public static function placeholderLabel(string $key): string
    {
        $labels = [
            'nome_outorgante' => 'Nome do outorgante',
            'nome_advogado' => 'Nome do advogado',
            'nome_cliente' => 'Nome do cliente',
            'nome_declarante' => 'Nome do declarante',
            'nome_autor' => 'Nome do autor',
            'nome_reu' => 'Nome do réu',
            'nacionalidade' => 'Nacionalidade',
            'estado_civil' => 'Estado civil',
            'profissao' => 'Profissão',
            'rg' => 'RG',
            'cpf' => 'CPF',
            'endereco_outorgante' => 'Endereço do outorgante',
            'endereco_escritorio' => 'Endereço do escritório',
            'endereco_cliente' => 'Endereço do cliente',
            'endereco' => 'Endereço',
            'endereco_completo' => 'Endereço completo',
            'uf_oab' => 'UF OAB',
            'numero_oab' => 'Número OAB',
            'cidade' => 'Cidade',
            'data' => 'Data',
            'hora' => 'Hora',
            'comarca' => 'Comarca',
            'tipo_acao' => 'Tipo da ação',
            'narrativa_fatos' => 'Narrativa dos fatos',
            'fundamentacao_juridica' => 'Fundamentação jurídica',
            'pedidos' => 'Pedidos',
            'pedidos_defesa' => 'Pedidos da defesa',
            'preliminares' => 'Preliminares',
            'defesa_merito' => 'Defesa do mérito',
            'valor_causa' => 'Valor da causa',
            'objeto_contrato' => 'Objeto do contrato',
            'valor_honorarios' => 'Valor dos honorários',
            'valor_por_extenso' => 'Valor por extenso',
            'finalidade' => 'Finalidade',
            'orgao_instituicao' => 'Órgão/Instituição',
            'objeto_declaracao' => 'Objeto da declaração',
            'data_inicio_residencia' => 'Data de início da residência',
        ];
        return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }
}

