@extends('layouts.app')

@section('pageTitle', 'Editar modelo')

@section('content')
<div class="w-full max-w-3xl">
    <div class="mb-4">
        <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Voltar para Documentos
        </a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h2 class="text-base font-semibold text-gray-900">Editar modelo: {{ $template->title }}</h2>
        </div>
        <form action="{{ route('document-templates.update', $template->id) }}" method="POST" class="p-4 space-y-4">
            @csrf
            @method('PUT')
            @if($errors->any())
            <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título do modelo *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $template->title) }}" required
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="type" id="type" required
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ old('type', $template->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição (opcional)</label>
                <input type="text" name="description" id="description" value="{{ old('description', $template->description) }}"
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Data do modelo *</label>
                <input type="date" name="date" id="date" value="{{ old('date', $template->date?->format('Y-m-d')) }}" required
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="content-editor" class="block text-sm font-medium text-gray-700 mb-1">Conteúdo (formate o texto; use placeholders entre chaves duplas: nome, data, cidade)</label>
                <div id="content-editor" class="min-h-[320px] rounded-md border border-gray-300 bg-white text-gray-900" style="height: 360px;"></div>
                <input type="hidden" name="content" id="content-input" value="">
                <p class="mt-1 text-xs text-gray-500">Use a barra de ferramentas para negrito, itálico, listas e espaços. Placeholders: nome_outorgante, cpf, data, cidade, etc.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Atualizar modelo
                </button>
                <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js"></script>
<script>
(function() {
    var initialContent = {!! json_encode(old('content', $template->content ?? '')) !!};
    var editorEl = document.getElementById('content-editor');
    var inputEl = document.getElementById('content-input');
    if (!editorEl || !inputEl) return;
    var quill = new Quill(editorEl, {
        theme: 'snow',
        placeholder: 'Digite o texto do modelo. Use duas chaves + nome do campo para placeholders.',
        modules: {
            toolbar: [
                [{ 'header': [2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['blockquote'],
                ['clean']
            ]
        }
    });
    if (initialContent) {
        if (initialContent.indexOf('<') === -1) quill.setText(initialContent);
        else quill.root.innerHTML = initialContent;
    }
    var form = editorEl.closest('form');
    if (form) {
        form.addEventListener('submit', function() {
            inputEl.value = quill.root.innerHTML;
        });
    }
})();
</script>
@endpush
@endsection
