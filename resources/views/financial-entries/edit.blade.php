@extends('layouts.app')

@section('pageTitle', 'Editar lancamento financeiro')

@section('content')
@php
    $paymentStatus = $financialEntry->paymentStatus();
    $paymentStatusLabel = \App\Models\FinancialEntry::paymentStatusLabels()[$paymentStatus] ?? $paymentStatus;
@endphp
<div class="max-w-5xl space-y-6">
    <p class="text-gray-600 text-sm">
        Atualize os dados do lancamento e registre baixas parciais ou totais.
    </p>

    @if(session('success'))
        <div class="rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-600">Status</p>
            <p class="mt-2 text-xl font-semibold text-slate-900">{{ $paymentStatusLabel }}</p>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <p class="text-sm font-medium text-emerald-700">Valor pago</p>
            <p class="mt-2 text-xl font-semibold text-emerald-900">R$ {{ number_format($financialEntry->paidAmount(), 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <p class="text-sm font-medium text-amber-700">Saldo pendente</p>
            <p class="mt-2 text-xl font-semibold text-amber-900">R$ {{ number_format($financialEntry->remainingAmount(), 2, ',', '.') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('financial-entries.update', $financialEntry->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('financial-entries._form')

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Atualizar lancamento
            </button>
            <a href="{{ route('financial-entries.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Voltar
            </a>
        </div>
    </form>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <div class="xl:col-span-3 rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between gap-3">
                <h3 class="text-sm font-semibold text-gray-900">Historico de pagamentos</h3>
                @if($financialEntry->entry_type === \App\Models\FinancialEntry::TYPE_RECEIVABLE && $financialEntry->remainingAmount() > 0 && $financialEntry->whatsappReminderUrl())
                    <form method="POST" action="{{ route('financial-entries.whatsapp-reminder', $financialEntry->id) }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-md hover:bg-emerald-700">
                            Cobrar no WhatsApp
                        </button>
                    </form>
                @endif
            </div>
            @if($financialEntry->payments->isEmpty())
                <div class="p-6 text-sm text-gray-500">
                    Nenhum pagamento registrado ainda.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Data</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Valor</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Origem</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Referencia</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Observacoes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($financialEntry->payments as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $payment->payment_date?->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $paymentSourceOptions[$payment->source] ?? $payment->source }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $payment->reference ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $payment->notes ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="xl:col-span-2 rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Registrar pagamento</h3>
            </div>
            <form method="POST" action="{{ route('financial-entries.payments.store', $financialEntry->id) }}" class="p-4 space-y-4">
                @csrf
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Data do pagamento</label>
                    <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('payment_date') border-red-500 @enderror">
                    @error('payment_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-1">Valor pago</label>
                    <input type="number" name="payment_amount" id="payment_amount" value="{{ old('payment_amount', number_format($financialEntry->remainingAmount(), 2, '.', '')) }}" min="0.01" step="0.01"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('payment_amount') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Saldo atual: R$ {{ number_format($financialEntry->remainingAmount(), 2, ',', '.') }}</p>
                    @error('payment_amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="payment_reference" class="block text-sm font-medium text-gray-700 mb-1">Referencia</label>
                    <input type="text" name="payment_reference" id="payment_reference" value="{{ old('payment_reference') }}"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('payment_reference') border-red-500 @enderror">
                    @error('payment_reference')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-1">Observacoes</label>
                    <textarea name="payment_notes" id="payment_notes" rows="4"
                              class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('payment_notes') border-red-500 @enderror">{{ old('payment_notes') }}</textarea>
                    @error('payment_notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-md hover:bg-slate-800">
                    Registrar baixa
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
