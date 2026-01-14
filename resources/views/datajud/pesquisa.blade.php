@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Pesquisa de Processos - DataJud</h3>

    <form id="datajud-form" method="POST" action="#">
        @csrf
        
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

        <button type="submit" id="btn-pesquisar" class="btn btn-primary">
            Pesquisar
        </button>
    </form>

    <div id="resultados" class="mt-4"></div>

    <script>
        (function () {
            const form = document.getElementById('datajud-form');
            const btn = document.getElementById('btn-pesquisar');
            const resultados = document.getElementById('resultados');
            const url = '{{ route('datajud.api.search') }}';
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '{{ csrf_token() }}';

            function renderHits(hits) {
                resultados.innerHTML = '';
                if (!hits || hits.length === 0) {
                    resultados.innerHTML = '<div class="alert alert-warning">Nenhum resultado encontrado.</div>';
                    return;
                }

                const list = document.createElement('div');

                hits.forEach(hit => {
                    const card = document.createElement('div');
                    card.className = 'card mb-2';
                    const body = document.createElement('div');
                    body.className = 'card-body';

                    const source = hit._source || hit._source || {};
                    const numero = source.numeroProcesso || source.numero_processo || hit._id || '';
                    const title = source.titulo || source.assunto || '';

                    const h5 = document.createElement('h5');
                    h5.className = 'card-title';
                    h5.textContent = numero + (title ? ' — ' + title : '');

                    const pre = document.createElement('pre');
                    pre.style.whiteSpace = 'pre-wrap';
                    pre.textContent = JSON.stringify(source, null, 2);

                    body.appendChild(h5);
                    body.appendChild(pre);
                    card.appendChild(body);
                    list.appendChild(card);
                });

                resultados.appendChild(list);
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                btn.disabled = true;
                resultados.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>';

                const data = {
                    tribunal: document.getElementById('tribunal').value,
                    numero_processo: document.getElementById('numero_processo').value,
                    nome_advogado: document.getElementById('nome_advogado').value,
                };

                fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                }).then(r => r.json())
                  .then(json => {
                      const hits = (json && json.hits && json.hits.hits) ? json.hits.hits : [];
                      renderHits(hits);
                  })
                  .catch(err => {
                      resultados.innerHTML = '<div class="alert alert-danger">Erro ao consultar o DataJud.</div>';
                      console.error(err);
                  })
                  .finally(() => btn.disabled = false);
            });
        })();
    </script>
    </form>
</div>
@endsection
