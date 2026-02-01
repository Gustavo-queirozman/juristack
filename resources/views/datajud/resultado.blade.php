@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Resultados</h3>
        <a href="{{ route('datajud.index') }}" class="btn btn-outline-secondary"> ← Voltar</a>
    </div>

    @if(empty($resultados) || count($resultados) === 0)
        <div class="alert alert-warning">Nenhum resultado encontrado.</div>
    @endif

    @foreach($resultados as $hit)
        @php $src = $hit['_source'] ?? []; $numero = $src['numeroProcesso'] ?? ($hit['_id'] ?? ''); @endphp
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><strong>{{ $numero }}</strong> @if(!empty($src['classe']['nome'])) — {{ $src['classe']['nome'] }} @endif</div>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('datajud.salvar') }}">
                        @csrf
                        <input type="hidden" name="tribunal" value="{{ $hit['_tribunal'] ?? ($src['tribunal'] ?? '') }}">
                        <input type="hidden" name="source" value='{{ json_encode($src, JSON_UNESCAPED_UNICODE) }}'>
                        <button class="btn btn-sm btn-outline-success">Salvar</button>
                    </form>

                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="event.preventDefault(); document.getElementById('json-{{ $numero }}').classList.toggle('d-none')">Ver JSON</a>
                </div>
            </div>
            <div class="card-body">
                <p><strong>Tribunal:</strong> {{ $hit['_tribunal'] ?? ($src['tribunal'] ?? '—') }}</p>
                <p><strong>Assuntos:</strong> {{ implode(', ', array_map(function($a){return $a['nome'] ?? '';}, $src['assuntos'] ?? [])) }}</p>
                <div id="json-{{ $numero }}" class="d-none">
                    <pre style="background:#0b1220;color:#dbeafe;padding:1rem;border-radius:6px;white-space:pre-wrap">{{ json_encode($src, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>
    @endforeach

    <div class="mt-4">
        <a href="{{ route('datajud.index') }}" class="btn btn-secondary">Nova pesquisa</a>
    </div>
</div>
@endsection
