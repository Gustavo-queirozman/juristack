<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use App\Rules\CnpjValido;
use App\Rules\CpfValido;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $cpf = $this->input('cpf');
        $cnpj = $this->input('cnpj');
        $this->merge([
            'cpf' => $cpf ? preg_replace('/\D/', '', $cpf) : null,
            'cnpj' => $cnpj ? preg_replace('/\D/', '', $cnpj) : null,
        ]);
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        $base = [
            'type' => ['required', 'string', Rule::in([Cliente::TYPE_PF, Cliente::TYPE_PJ])],
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:20'],

            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'max:10'],
        ];

        if ($this->input('type') === Cliente::TYPE_PF) {
            $base['cpf'] = [
                'required',
                'string',
                'size:11',
                new CpfValido,
                Rule::unique('clientes', 'cpf')->where('user_id', $userId)->whereNull('deleted_at'),
            ];
            $base['cnpj'] = ['nullable', 'prohibited'];
        } elseif ($this->input('type') === Cliente::TYPE_PJ) {
            $base['cpf'] = ['nullable', 'prohibited'];
            $base['cnpj'] = [
                'required',
                'string',
                'size:14',
                new CnpjValido,
                Rule::unique('clientes', 'cnpj')->where('user_id', $userId)->whereNull('deleted_at'),
            ];
        } else {
            $base['cpf'] = ['nullable', 'string', 'size:11', new CpfValido];
            $base['cnpj'] = ['nullable', 'string', 'size:14', new CnpjValido];
        }

        if ($this->filled('logradouro') || $this->filled('cidade') || $this->filled('cep')) {
            $base['logradouro'] = ['required', 'string', 'max:255'];
            $base['cidade'] = ['required', 'string', 'max:100'];
            $base['estado'] = ['required', 'string', 'size:2'];
            $base['cep'] = ['required', 'string', 'max:10'];
        }

        return $base;
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Selecione o tipo de pessoa (PF ou PJ).',
            'type.in' => 'O tipo deve ser Pessoa Física (PF) ou Pessoa Jurídica (PJ).',
            'nome.required' => 'O nome ou razão social é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório para Pessoa Física.',
            'cnpj.required' => 'O CNPJ é obrigatório para Pessoa Jurídica.',
        ];
    }
}
