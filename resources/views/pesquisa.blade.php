@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Pesquisa Processual – DataJud</h2>

    <form method="POST" action="{{ route('pesquisa.datajud') }}">
        @csrf

        {{-- Tribunal --}}
        <div class="mb-3">
            <label for="tribunal" class="form-label">
                Tribunal <span class="text-danger">*</span>
            </label>
            <select name="tribunal" id="tribunal" class="form-select" required>
                <option value="">Selecione o Tribunal</option>

                {{-- Tribunais Estaduais --}}
                <optgroup label="Tribunais de Justiça (TJ)">
                    <option value="tjmg">TJMG - Minas Gerais</option>
                    <option value="tjsp">TJSP - São Paulo</option>
                    <option value="tjrj">TJRJ - Rio de Janeiro</option>
                    <option value="tjrs">TJRS - Rio Grande do Sul</option>
                    <option value="tjba">TJBA - Bahia</option>
                </optgroup>

                {{-- Tribunais Federais --}}
                <optgroup label="Tribunais Regionais Federais (TRF)">
                    <option value="trf1">TRF1</option>
                    <option value="trf2">TRF2</option>
                    <option value="trf3">TRF3</option>
                    <option value="trf4">TRF4</option>
                    <option value="trf5">TRF5</option>
                    <option value="trf6">TRF6</option>
                </optgroup>

                {{-- Tribunais do Trabalho --}}
                <optgroup label="Tribunais Regionais do Trabalho (TRT)">
                    <option value="trt1">TRT1</option>
                    <option value="trt2">TRT2</option>
                    <option value="trt3">TRT3</option>
                    <option value="trt4">TRT4</option>
                </optgroup>

                {{-- Tribunais Superiores --}}
                <optgroup label="Tribunais Superiores">
                    <option value="stj">STJ</option>
                    <option value="tst">TST</option>
                </optgroup>
            </select>
        </div>

        {{-- Tipo de pesquisa --}}
        <div class="mb-3">
            <label class="form-label">Pesquisar por</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_pesquisa"
                       id="por_processo" value="processo" required>
                <label class="form-check-label" for="por_processo">
                    Número do Processo
                </label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_pesquisa"
                       id="por_advogado" value="advogado">
                <label class="form-check-label" for="por_advogado">
                    Nome do Advogado
                </label>
            </div>
        </div>

        {{-- Número do processo --}}
        <div class="mb-3" id="campo_processo" style="display: none;">
            <label for="numero_processo" class="form-label">Número do Processo</label>
            <input type="text" name="numero_processo" id="numero_processo"
                   class="form-control"
                   placeholder="0000000-00.0000.8.00.0000">
        </div>

        {{-- Nome do advogado --}}
        <div class="mb-3" id="campo_advogado" style="display: none;">
            <label for="nome_advogado" class="form-label">Nome do Advogado</label>
            <input type="text" name="nome_advogado" id="nome_advogado"
                   class="form-control"
                   placeholder="Nome completo do advogado">
        </div>

        <button type="submit" class="btn btn-primary">
            Pesquisar
        </button>
    </form>
</div>

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radioProcesso = document.getElementById('por_processo');
        const radioAdvogado = document.getElementById('por_advogado');

        const campoProcesso = document.getElementById('campo_processo');
        const campoAdvogado = document.getElementById('campo_advogado');

        function toggleCampos() {
            if (radioProcesso.checked) {
                campoProcesso.style.display = 'block';
                campoAdvogado.style.display = 'none';
            } else if (radioAdvogado.checked) {
                campoAdvogado.style.display = 'block';
                campoProcesso.style.display = 'none';
            }
        }

        radioProcesso.addEventListener('change', toggleCampos);
        radioAdvogado.addEventListener('change', toggleCampos);
    });
</script>
@endsection
