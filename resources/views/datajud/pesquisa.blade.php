@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Pesquisa de Processos - DataJud</h3>

    <form method="GET" action="{{ route('datajud.pesquisar') }}">
        
        {{-- Tribunal --}}
        <div class="mb-3">
            <label for="tribunal" class="form-label">
                Tribunal <span class="text-danger">*</span>
            </label>
            <select name="tribunal" id="tribunal" class="form-select" required>
                <option value="">Selecione o tribunal</option>

                {{-- Exemplo de tribunais --}}
                <option value="STF">STF - Supremo Tribunal Federal</option>
                <option value="STJ">STJ - Superior Tribunal de Justiça</option>
                <option value="TST">TST - Tribunal Superior do Trabalho</option>
                <option value="TRF1">TRF 1ª Região</option>
                <option value="TRF2">TRF 2ª Região</option>
                <option value="TRF3">TRF 3ª Região</option>
                <option value="TRF4">TRF 4ª Região</option>
                <option value="TRF5">TRF 5ª Região</option>
                <option value="TJSP">TJSP - Tribunal de Justiça de SP</option>
                <option value="TJRJ">TJRJ - Tribunal de Justiça do RJ</option>
            </select>
        </div>

        {{-- Número do Processo --}}
        <div class="mb-3">
            <label for="numero_processo" class="form-label">
                Número do Processo
            </label>
            <input
                type="text"
                name="numero_processo"
                id="numero_processo"
                class="form-control"
                placeholder="Ex: 0001234-56.2023.8.26.0000"
                value="{{ request('numero_processo') }}"
            >
        </div>

        {{-- Nome do Advogado --}}
        <div class="mb-3">
            <label for="nome_advogado" class="form-label">
                Nome do Advogado
            </label>
            <input
                type="text"
                name="nome_advogado"
                id="nome_advogado"
                class="form-control"
                placeholder="Ex: João da Silva"
                value="{{ request('nome_advogado') }}"
            >
        </div>

        <div class="alert alert-info">
            Preencha <strong>o número do processo</strong> ou <strong>o nome do advogado</strong>.
        </div>

        <button type="submit" class="btn btn-primary">
            Pesquisar
        </button>
    </form>
</div>
@endsection
