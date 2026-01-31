@extends('layouts.app')

@section('content')
<div class="container">

    <style>
        .datajud-card {
            box-shadow: 0 6px 18px rgba(15,23,42,0.06);
            border-radius: 8px;
            overflow: hidden;
        }
        .datajud-card .card-header {
            background: linear-gradient(90deg,#f8fafc,#ffffff);
            font-weight: 600;
        }
        .badge-tribunal {
            background: #eef2ff;
            color: #2a2a72;
            font-weight: 600;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📁 Processos Salvos</h3>
        <a href="{{ route('datajud.index') }}" class="btn btn-outline-primary">
            🔍 Nova pesquisa
        </a>
    </div>

    @if($processos->isEmpty())
        <div class="alert alert-info">
            Nenhum processo salvo até o momento.
        </div>
    @endif

    @foreach($processos as $processo)
        <div class="card datajud-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $processo->numero_processo }}</strong>
                    @if($processo->classe_nome)
                        — {{ $processo->classe_nome }}
                    @endif
                </div>
                <span class="badge badge-tribunal">
                    {{ $processo->tribunal }}
                </span>
            </div>

            <div class="card-body">
                <p class="mb-2">
                    <strong>Status:</strong>
                    {{ $processo->status ?? '—' }}
                </p>

                <p class="mb-2">
                    <strong>Data de ajuizamento:</strong>
                    {{ optional($processo->data_ajuizamento)->format('d/m/Y') ?? '—' }}
                </p>

                <p class="mb-2">
                    <strong>Última atualização:</strong>
                    {{ optional($processo->datahora_ultima_atualizacao)->format('d/m/Y H:i') ?? '—' }}
                </p>

                @if($processo->assuntos->count())
                    <p class="mb-2">
                        <strong>Assuntos:</strong><br>
                        <small class="text-muted">
                            {{ $processo->assuntos->pluck('nome')->implode(', ') }}
                        </small>
                    </p>
                @endif

                <div class="d-flex gap-2 mt-3">
                    {{-- Detalhes --}}
                    <a href="{{ route('datajud.salvo.show', $processo->id) }}"
                       class="btn btn-sm btn-outline-primary">
                        📄 Detalhes
                    </a>

                    {{-- Ver JSON --}}
                    <button
                        class="btn btn-sm btn-outline-secondary"
                        data-bs-toggle="collapse"
                        data-bs-target="#json-{{ $processo->id }}">
                        🧾 JSON
                    </button>

                    {{-- Remover --}}
                    <form method="POST"
                          action="{{ route('datajud.salvo.delete', $processo->id) }}"
                          onsubmit="return confirm('Remover este processo salvo?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            🗑 Remover
                        </button>
                    </form>
                </div>

                {{-- JSON --}}
                <div class="collapse mt-3" id="json-{{ $processo->id }}">
                    <pre style="background:#0b1220;color:#dbeafe;padding:1rem;border-radius:6px;white-space:pre-wrap">
{{ json_encode($processo->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                    </pre>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Paginação --}}
    <div class="mt-4">
        {{ $processos->links() }}
    </div>

</div>
@endsection
