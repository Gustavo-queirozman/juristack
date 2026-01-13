@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Resultados – {{ $tribunal }}</h4>

    @if(empty($resultados))
        <div class="alert alert-warning">
            Nenhum processo encontrado.
        </div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Número do Processo</th>
                    <th>Classe</th>
                    <th>Data de Ajuizamento</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultados as $item)
                    <tr>
                        <td>{{ $item['_source']['numeroProcesso'] ?? '-' }}</td>
                        <td>{{ $item['_source']['classe'] ?? '-' }}</td>
                        <td>{{ $item['_source']['dataAjuizamento'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
