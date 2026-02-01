@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Detalhes do Processo</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('datajud.salvos') }}" class="btn btn-outline-secondary">← Voltar</a>
            <form method="POST" action="{{ route('datajud.salvo.delete', $processo->id) }}" onsubmit="return confirm('Remover este processo salvo?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">Remover</button>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $processo->numero_processo }}</strong>
                @if($processo->classe_nome) — {{ $processo->classe_nome }} @endif
            </div>
            <span class="badge bg-secondary">{{ $processo->tribunal }}</span>
        </div>
        <div class="card-body">
            <p><strong>Data de ajuizamento:</strong> {{ optional($processo->data_ajuizamento)->format('d/m/Y') ?? '—' }}</p>
            <p><strong>Última atualização:</strong> {{ optional($processo->datahora_ultima_atualizacao)->format('d/m/Y H:i') ?? '—' }}</p>

            @if($processo->assuntos->count())
                <p><strong>Assuntos:</strong> {{ $processo->assuntos->pluck('nome')->implode(', ') }}</p>
            @endif

            <h5 class="mt-3">Últimos Movimentos</h5>
            @if($processo->movimentos->count())
                <ul class="list-group">
                    @foreach($processo->movimentos as $mov)
                        <li class="list-group-item">
                            <div><small class="text-muted">{{ optional($mov->data_hora)->format('d/m/Y H:i') }}</small></div>
                            <div><strong>{{ $mov->nome }}</strong></div>
                            @if($mov->complementos->count())
                                <div class="mt-2 small text-muted">
                                    Complementos: {{ $mov->complementos->pluck('descricao')->filter()->implode('; ') }}
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="alert alert-info">Nenhum movimento registrado.</div>
            @endif

            <hr>
            <h5>JSON completo</h5>
            <pre style="background:#0b1220;color:#dbeafe;padding:1rem;border-radius:6px;white-space:pre-wrap">{{ json_encode($processo->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
</div>
@endsection
