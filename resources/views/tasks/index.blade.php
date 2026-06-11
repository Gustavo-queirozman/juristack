@extends('layouts.app')

@section('pageTitle', 'Kanban de tarefas')

@section('content')
@php
    $statuses = [
        'pending' => [
            'label' => 'Pendente',
            'dot' => 'bg-amber-500',
        ],
        'in_progress' => [
            'label' => 'Em progresso',
            'dot' => 'bg-sky-500',
        ],
        'completed' => [
            'label' => 'Concluida',
            'dot' => 'bg-emerald-500',
        ],
    ];

    $priorityStyles = [
        'low' => 'bg-slate-100 text-slate-700',
        'medium' => 'bg-sky-100 text-sky-700',
        'high' => 'bg-amber-100 text-amber-700',
        'urgent' => 'bg-rose-100 text-rose-700',
    ];

    $tasksByStatus = $tasks->groupBy('status');
@endphp

<div class="w-full max-w-full">
    @if(session('success'))
    <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="alert">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
        <p class="font-medium">Nao foi possivel salvar a tarefa.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <p class="mb-6 text-sm text-gray-600">
        Organize o trabalho arrastando as tarefas entre os status. Agora cada tarefa pode ser vinculada a um processo salvo, com responsavel, prazo, status e prioridade.
    </p>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="m-0 text-lg font-semibold text-gray-900">
                Kanban de tarefas
            </h2>
            <p class="mt-1 text-xs text-gray-500">
                Arraste as tarefas, vincule processos do escritorio e acompanhe os prazos.
            </p>
        </div>
        <button type="button"
            id="kanban-open-create"
            class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nova tarefa
        </button>
    </div>

    <div class="rounded-xl border border-gray-200 bg-slate-50/80">
        <div class="max-h-[60vh] overflow-auto p-3 md:max-h-[calc(100vh-260px)] md:p-4">
            <div id="kanban-board"
                class="grid grid-cols-1 gap-4 md:grid-cols-3"
                data-task-update-url-template="{{ route('tasks.update', ['task' => '__TASK_ID__']) }}"
                data-update-url-template="{{ route('tasks.update-status', ['task' => '__TASK_ID__']) }}"
                data-assign-url-template="{{ route('tasks.update-assignee', ['task' => '__TASK_ID__']) }}"
                data-delete-url-template="{{ route('tasks.destroy', ['task' => '__TASK_ID__']) }}">
                @foreach($statuses as $statusKey => $statusConfig)
                <div class="flex min-h-[340px] flex-col rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-2 border-b border-gray-100 bg-gray-50/80 px-3 py-2">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ $statusConfig['dot'] }}"></span>
                            <span class="text-xs font-medium uppercase tracking-wide text-gray-700">
                                {{ $statusConfig['label'] }}
                            </span>
                        </div>
                        <span class="kanban-count text-xs text-gray-400" data-status="{{ $statusKey }}">
                            {{ isset($tasksByStatus[$statusKey]) ? $tasksByStatus[$statusKey]->count() : 0 }}
                        </span>
                    </div>

                    <div class="kanban-column flex-1 space-y-3 overflow-y-auto p-3" data-status="{{ $statusKey }}">
                        @forelse(($tasksByStatus[$statusKey] ?? collect()) as $task)
                        <div class="kanban-card cursor-move rounded-md border border-gray-200 bg-white p-3 shadow-sm transition-all hover:border-indigo-300 hover:shadow-md"
                            draggable="true"
                            data-task-id="{{ $task->id }}"
                            data-status="{{ $task->status }}"
                            data-task-title="{{ $task->title }}"
                            data-task-description="{{ $task->description }}"
                            data-task-status-label="{{ $statuses[$task->status]['label'] ?? $statusConfig['label'] }}"
                            data-task-priority="{{ $task->priority }}"
                            data-task-priority-label="{{ $priorityOptions[$task->priority] ?? 'Media' }}"
                            data-task-due-date="{{ optional($task->due_date)->format('d/m/Y') }}"
                            data-task-due-date-raw="{{ optional($task->due_date)->format('Y-m-d') }}"
                            data-task-assignee-id="{{ optional($task->users->first())->id }}"
                            data-task-assignee="{{ ($task->users && $task->users->count()) ? $task->users->pluck('name')->join(', ') : 'Sem responsavel' }}"
                            data-task-process-id="{{ $task->processo?->id }}"
                            data-task-process-number="{{ $task->processo?->numero_processo }}"
                            data-task-process-label="{{ $task->processo ? trim($task->processo->numero_processo . ' - ' . ($task->processo->customer?->name ?? $task->processo->tribunal)) : 'Sem processo vinculado' }}"
                            data-task-created="{{ optional($task->created_at)->format('d/m/Y H:i') }}"
                            data-task-updated="{{ optional($task->updated_at)->format('d/m/Y H:i') }}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $task->title }}
                                    </p>
                                    @if($task->processo)
                                    <p class="mt-1 truncate text-[11px] text-slate-500">
                                        Processo: {{ $task->processo->numero_processo }}
                                    </p>
                                    @endif
                                    @if($task->description)
                                    <p class="mt-1 line-clamp-3 text-xs text-gray-500">
                                        {{ $task->description }}
                                    </p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-0.5 text-[10px] font-medium text-gray-500">
                                    #{{ $task->id }}
                                </span>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px]">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 font-medium {{ $priorityStyles[$task->priority] ?? $priorityStyles['medium'] }}">
                                    {{ $priorityOptions[$task->priority] ?? 'Media' }}
                                </span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-gray-600">
                                    Prazo: {{ $task->due_date ? $task->due_date->format('d/m/Y') : 'Nao definido' }}
                                </span>
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-2">
                                <span class="kanban-assignee-label inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] text-gray-600">
                                    @if($task->users && $task->users->count())
                                        {{ $task->users->pluck('name')->first() }}
                                    @else
                                        Sem responsavel
                                    @endif
                                </span>

                                @if($statusKey === 'pending')
                                <select
                                    class="kanban-assign-select rounded-md border-gray-200 bg-white text-[11px] text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    data-task-id="{{ $task->id }}">
                                    <option value="">Definir responsavel</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected($task->users && $task->users->contains($user))>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @endif
                            </div>

                            <div class="mt-2 flex justify-end">
                                <button type="button"
                                    class="kanban-view-details text-[11px] font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                                    Ver detalhes
                                </button>
                            </div>
                        </div>
                        @empty
                        <p class="text-xs italic text-gray-400">
                            Nenhuma tarefa neste status.
                        </p>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="task-create-modal" class="fixed inset-0 z-40 hidden items-center justify-center bg-slate-900/40">
        <div class="relative w-full max-w-xl rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                <h3 class="text-sm font-semibold text-gray-900">Nova tarefa</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" data-task-create-close>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="modal-title" class="block text-sm font-medium text-gray-700">Tarefa</label>
                            <input type="text" name="title" id="modal-title" required
                                value="{{ old('title') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ex.: Revisar peticao inicial, preparar audiencia, ligar para o cliente">
                        </div>
                        <div class="md:col-span-2">
                            <label for="modal-process" class="block text-sm font-medium text-gray-700">Processo vinculado</label>
                            <select name="datajud_processo_id" id="modal-process"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sem processo vinculado</option>
                                @foreach($processes as $process)
                                <option value="{{ $process->id }}" @selected((string) old('datajud_processo_id') === (string) $process->id)>
                                    {{ $process->numero_processo }}@if($process->customer) - {{ $process->customer->name }}@endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="modal-status" class="block text-sm font-medium text-gray-700">Status inicial</label>
                            <select name="status" id="modal-status"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="pending" @selected(old('status', 'pending') === 'pending')>Pendente</option>
                                <option value="in_progress" @selected(old('status') === 'in_progress')>Em progresso</option>
                                <option value="completed" @selected(old('status') === 'completed')>Concluida</option>
                            </select>
                        </div>
                        <div>
                            <label for="modal-assignee" class="block text-sm font-medium text-gray-700">Responsavel</label>
                            <select name="users[]" id="modal-assignee"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sem responsavel</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected((string) old('users.0') === (string) $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="modal-due-date" class="block text-sm font-medium text-gray-700">Prazo</label>
                            <input type="date" name="due_date" id="modal-due-date"
                                value="{{ old('due_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="modal-priority" class="block text-sm font-medium text-gray-700">Prioridade</label>
                            <select name="priority" id="modal-priority"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($priorityOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('priority', 'medium') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="modal-description" class="block text-sm font-medium text-gray-700">Descricao</label>
                        <textarea name="description" id="modal-description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Detalhes, proximos passos ou contexto da tarefa">{{ old('description') }}</textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <button type="button"
                            class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800"
                            data-task-create-close>
                            Cancelar
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Salvar tarefa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="task-edit-modal" class="fixed inset-0 z-40 hidden items-center justify-center bg-slate-900/40">
        <div class="relative w-full max-w-xl rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                <h3 class="text-sm font-semibold text-gray-900">Editar tarefa</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" data-task-edit-close>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <form id="task-edit-form" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="edit-title" class="block text-sm font-medium text-gray-700">Tarefa</label>
                            <input type="text" name="title" id="edit-title" required
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label for="edit-process" class="block text-sm font-medium text-gray-700">Processo vinculado</label>
                            <select name="datajud_processo_id" id="edit-process"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sem processo vinculado</option>
                                @foreach($processes as $process)
                                <option value="{{ $process->id }}">
                                    {{ $process->numero_processo }}@if($process->customer) - {{ $process->customer->name }}@endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="edit-status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="edit-status"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="pending">Pendente</option>
                                <option value="in_progress">Em progresso</option>
                                <option value="completed">Concluida</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit-assignee" class="block text-sm font-medium text-gray-700">Responsavel</label>
                            <select name="users[]" id="edit-assignee"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sem responsavel</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="edit-due-date" class="block text-sm font-medium text-gray-700">Prazo</label>
                            <input type="date" name="due_date" id="edit-due-date"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="edit-priority" class="block text-sm font-medium text-gray-700">Prioridade</label>
                            <select name="priority" id="edit-priority"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($priorityOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="edit-description" class="block text-sm font-medium text-gray-700">Descricao</label>
                        <textarea name="description" id="edit-description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Detalhes, proximos passos ou contexto da tarefa"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <button type="button"
                            class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800"
                            data-task-edit-close>
                            Cancelar
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Atualizar tarefa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="task-details-modal" class="fixed inset-0 z-40 hidden items-center justify-center bg-slate-900/40">
        <div class="relative w-full max-w-xl rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                <div>
                    <h3 id="task-details-title" class="text-sm font-semibold text-gray-900">Tarefa</h3>
                    <p id="task-details-status" class="mt-0.5 text-xs text-gray-500"></p>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-600" data-task-details-close>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="space-y-4 p-4">
                <p id="task-details-description" class="whitespace-pre-line text-sm text-gray-700"></p>

                <dl class="grid grid-cols-1 gap-4 text-xs text-gray-600 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <dt class="font-medium text-gray-700">Processo vinculado</dt>
                        <dd id="task-details-process" class="mt-0.5 text-gray-600"></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Responsavel</dt>
                        <dd id="task-details-assignee" class="mt-0.5 text-gray-600"></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Status</dt>
                        <dd id="task-details-status-label" class="mt-0.5 text-gray-600"></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Prazo</dt>
                        <dd id="task-details-due-date" class="mt-0.5 text-gray-600"></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Prioridade</dt>
                        <dd id="task-details-priority" class="mt-0.5 text-gray-600"></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Criada em</dt>
                        <dd id="task-details-created" class="mt-0.5 text-gray-600"></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Atualizada em</dt>
                        <dd id="task-details-updated" class="mt-0.5 text-gray-600"></dd>
                    </div>
                </dl>
            </div>
            <div class="flex items-center justify-between gap-2 border-t border-gray-100 px-4 py-3">
                <div class="flex items-center gap-2">
                    <button type="button"
                        id="task-details-delete"
                        class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 hover:text-red-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Excluir tarefa</span>
                    </button>
                    <button type="button"
                        id="task-details-edit"
                        class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-xs font-medium text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16.862 4.487a2.25 2.25 0 113.182 3.182L7.5 20.214 3 21l.786-4.5L16.862 4.487z" />
                        </svg>
                        <span>Editar tarefa</span>
                    </button>
                </div>
                <button type="button"
                    class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800"
                    data-task-details-close>
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <div id="task-delete-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50">
        <div class="w-full max-w-sm rounded-xl border border-red-100 bg-white shadow-2xl">
            <div class="flex items-center gap-2 border-b border-red-50 bg-red-50/80 px-4 py-3">
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3.75M12 15.75h.007v.008H12v-.008zM21 12A9 9 0 113 12a9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Excluir tarefa</h3>
            </div>
            <div class="space-y-2 px-4 py-3">
                <p class="text-sm text-gray-700">
                    Tem certeza que deseja excluir esta tarefa? Essa acao nao pode ser desfeita.
                </p>
                <p id="task-delete-confirm-title" class="line-clamp-2 text-xs text-gray-500"></p>
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-100 px-4 py-3">
                <button type="button"
                    class="rounded-md px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800"
                    data-task-delete-cancel>
                    Cancelar
                </button>
                <button type="button"
                    class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700"
                    data-task-delete-confirm>
                    Confirmar exclusao
                </button>
            </div>
        </div>
    </div>

    <form id="task-delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const board = document.getElementById('kanban-board');
    if (!board) return;

    const statusLabels = @json(collect($statuses)->mapWithKeys(fn ($config, $key) => [$key => $config['label']]));
    const updateTaskUrlTemplate = board.dataset.taskUpdateUrlTemplate || '';
    const updateStatusUrlTemplate = board.dataset.updateUrlTemplate || '';
    const assignUrlTemplate = board.dataset.assignUrlTemplate || '';
    const deleteUrlTemplate = board.dataset.deleteUrlTemplate || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const columns = Array.from(board.querySelectorAll('.kanban-column'));
    const cards = Array.from(board.querySelectorAll('.kanban-card'));

    const createButton = document.getElementById('kanban-open-create');
    const createModal = document.getElementById('task-create-modal');
    const createCloseButtons = createModal ? createModal.querySelectorAll('[data-task-create-close]') : [];

    const editModal = document.getElementById('task-edit-modal');
    const editForm = document.getElementById('task-edit-form');
    const editCloseButtons = editModal ? editModal.querySelectorAll('[data-task-edit-close]') : [];
    const editTitleInput = document.getElementById('edit-title');
    const editDescriptionInput = document.getElementById('edit-description');
    const editStatusInput = document.getElementById('edit-status');
    const editPriorityInput = document.getElementById('edit-priority');
    const editDueDateInput = document.getElementById('edit-due-date');
    const editAssigneeInput = document.getElementById('edit-assignee');
    const editProcessInput = document.getElementById('edit-process');

    const detailsModal = document.getElementById('task-details-modal');
    const detailsCloseButtons = detailsModal ? detailsModal.querySelectorAll('[data-task-details-close]') : [];
    const detailsTitle = document.getElementById('task-details-title');
    const detailsStatus = document.getElementById('task-details-status');
    const detailsDescription = document.getElementById('task-details-description');
    const detailsAssignee = document.getElementById('task-details-assignee');
    const detailsStatusLabel = document.getElementById('task-details-status-label');
    const detailsProcess = document.getElementById('task-details-process');
    const detailsDueDate = document.getElementById('task-details-due-date');
    const detailsPriority = document.getElementById('task-details-priority');
    const detailsCreated = document.getElementById('task-details-created');
    const detailsUpdated = document.getElementById('task-details-updated');
    const detailsDeleteButton = document.getElementById('task-details-delete');
    const detailsEditButton = document.getElementById('task-details-edit');

    const deleteConfirmModal = document.getElementById('task-delete-confirm-modal');
    const deleteConfirmTitle = document.getElementById('task-delete-confirm-title');
    const deleteConfirmButton = deleteConfirmModal ? deleteConfirmModal.querySelector('[data-task-delete-confirm]') : null;
    const deleteCancelButton = deleteConfirmModal ? deleteConfirmModal.querySelector('[data-task-delete-cancel]') : null;
    const deleteForm = document.getElementById('task-delete-form');

    let currentDetailsTaskId = null;
    let currentDetailsCard = null;
    let draggedCard = null;
    let originalColumn = null;

    function openModal(modal) {
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openDetailsModalFromCard(card) {
        if (!detailsModal || !card) return;

        const id = card.dataset.taskId || '';
        currentDetailsTaskId = id || null;
        currentDetailsCard = card;

        if (detailsTitle) detailsTitle.textContent = card.dataset.taskTitle || ('Tarefa #' + id);
        if (detailsStatus) detailsStatus.textContent = id ? ('Tarefa #' + id) : '';
        if (detailsDescription) detailsDescription.textContent = card.dataset.taskDescription || 'Sem descricao.';
        if (detailsAssignee) detailsAssignee.textContent = card.dataset.taskAssignee || 'Sem responsavel';
        if (detailsStatusLabel) detailsStatusLabel.textContent = card.dataset.taskStatusLabel || '-';
        if (detailsProcess) detailsProcess.textContent = card.dataset.taskProcessLabel || 'Sem processo vinculado';
        if (detailsDueDate) detailsDueDate.textContent = card.dataset.taskDueDate || 'Nao definido';
        if (detailsPriority) detailsPriority.textContent = card.dataset.taskPriorityLabel || '-';
        if (detailsCreated) detailsCreated.textContent = card.dataset.taskCreated || '-';
        if (detailsUpdated) detailsUpdated.textContent = card.dataset.taskUpdated || '-';

        openModal(detailsModal);
    }

    function openEditModalFromCard(card) {
        if (!editModal || !editForm || !card || !updateTaskUrlTemplate) return;

        const taskId = card.dataset.taskId || '';
        editForm.setAttribute('action', updateTaskUrlTemplate.replace('__TASK_ID__', taskId));

        if (editTitleInput) editTitleInput.value = card.dataset.taskTitle || '';
        if (editDescriptionInput) editDescriptionInput.value = card.dataset.taskDescription || '';
        if (editStatusInput) editStatusInput.value = card.dataset.status || 'pending';
        if (editPriorityInput) editPriorityInput.value = card.dataset.taskPriority || 'medium';
        if (editDueDateInput) editDueDateInput.value = card.dataset.taskDueDateRaw || '';
        if (editAssigneeInput) editAssigneeInput.value = card.dataset.taskAssigneeId || '';
        if (editProcessInput) editProcessInput.value = card.dataset.taskProcessId || '';

        closeModal(detailsModal);
        openModal(editModal);
    }

    function updateCounts() {
        board.querySelectorAll('.kanban-count').forEach((countEl) => {
            const status = countEl.dataset.status;
            const column = board.querySelector('.kanban-column[data-status="' + status + '"]');
            if (!column) return;
            countEl.textContent = column.querySelectorAll('.kanban-card').length;
        });
    }

    function attachCardEvents(card) {
        card.addEventListener('dragstart', (event) => {
            draggedCard = card;
            originalColumn = card.parentElement;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', card.dataset.taskId || '');
            card.classList.add('opacity-60', 'ring-2', 'ring-indigo-400');
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('opacity-60', 'ring-2', 'ring-indigo-400');
            draggedCard = null;
            originalColumn = null;
            columns.forEach((column) => {
                column.classList.remove('ring-2', 'ring-indigo-200', 'bg-indigo-50/40');
            });
        });

        const detailsButton = card.querySelector('.kanban-view-details');
        if (detailsButton) {
            detailsButton.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                openDetailsModalFromCard(card);
            });
        }
    }

    cards.forEach(attachCardEvents);

    function attachAssignEvents() {
        board.querySelectorAll('.kanban-assign-select').forEach((select) => {
            select.addEventListener('change', (event) => {
                const el = event.target;
                const taskId = el.dataset.taskId;
                if (!taskId || !assignUrlTemplate) return;

                const card = el.closest('.kanban-card');
                const label = card ? card.querySelector('.kanban-assignee-label') : null;
                const previousText = label ? label.textContent : '';
                const previousAssigneeId = card ? (card.dataset.taskAssigneeId || '') : '';
                const selectedOption = el.options[el.selectedIndex];
                const newText = selectedOption && selectedOption.value ? selectedOption.text : 'Sem responsavel';

                if (label) {
                    label.textContent = newText;
                }
                if (card) {
                    card.dataset.taskAssignee = newText;
                    card.dataset.taskAssigneeId = selectedOption?.value || '';
                }

                fetch(assignUrlTemplate.replace('__TASK_ID__', taskId), {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ user_id: selectedOption?.value || null }),
                })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Erro ao atualizar responsavel');
                    }
                    return response.json();
                })
                .then((data) => {
                    if (!data.success) {
                        throw new Error('Resposta invalida do servidor');
                    }
                })
                .catch(() => {
                    if (label) {
                        label.textContent = previousText;
                    }
                    if (card) {
                        card.dataset.taskAssignee = previousText;
                        card.dataset.taskAssigneeId = previousAssigneeId;
                    }
                    alert('Nao foi possivel atualizar o responsavel. Tente novamente.');
                });
            });
        });
    }

    attachAssignEvents();

    if (createButton && createModal) {
        createButton.addEventListener('click', (event) => {
            event.preventDefault();
            openModal(createModal);
        });
    }

    createCloseButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            closeModal(createModal);
        });
    });

    editCloseButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            closeModal(editModal);
        });
    });

    detailsCloseButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            closeModal(detailsModal);
        });
    });

    [createModal, editModal, detailsModal, deleteConfirmModal].forEach((modal) => {
        if (!modal) return;
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    if (detailsEditButton) {
        detailsEditButton.addEventListener('click', (event) => {
            event.preventDefault();
            if (!currentDetailsCard) return;
            openEditModalFromCard(currentDetailsCard);
        });
    }

    function openDeleteConfirmModal() {
        if (!deleteConfirmModal || !currentDetailsTaskId) return;
        if (deleteConfirmTitle && detailsTitle) {
            deleteConfirmTitle.textContent = detailsTitle.textContent;
        }
        openModal(deleteConfirmModal);
    }

    if (detailsDeleteButton && deleteUrlTemplate) {
        detailsDeleteButton.addEventListener('click', (event) => {
            event.preventDefault();
            if (!currentDetailsTaskId) return;
            openDeleteConfirmModal();
        });
    }

    if (deleteConfirmButton && deleteForm && deleteUrlTemplate) {
        deleteConfirmButton.addEventListener('click', (event) => {
            event.preventDefault();
            if (!currentDetailsTaskId) return;
            deleteForm.setAttribute('action', deleteUrlTemplate.replace('__TASK_ID__', currentDetailsTaskId));
            deleteForm.submit();
        });
    }

    if (deleteCancelButton) {
        deleteCancelButton.addEventListener('click', (event) => {
            event.preventDefault();
            closeModal(deleteConfirmModal);
        });
    }

    columns.forEach((column) => {
        column.addEventListener('dragover', (event) => {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            column.classList.add('ring-2', 'ring-indigo-200', 'bg-indigo-50/40');
        });

        column.addEventListener('dragleave', () => {
            column.classList.remove('ring-2', 'ring-indigo-200', 'bg-indigo-50/40');
        });

        column.addEventListener('drop', (event) => {
            event.preventDefault();
            column.classList.remove('ring-2', 'ring-indigo-200', 'bg-indigo-50/40');

            if (!draggedCard || !originalColumn) return;

            const taskId = draggedCard.dataset.taskId;
            const newStatus = column.dataset.status;
            const currentStatus = draggedCard.dataset.status;

            if (!taskId || !newStatus || newStatus === currentStatus) {
                return;
            }

            column.appendChild(draggedCard);
            draggedCard.dataset.status = newStatus;
            draggedCard.dataset.taskStatusLabel = statusLabels[newStatus] || newStatus;
            updateCounts();

            fetch(updateStatusUrlTemplate.replace('__TASK_ID__', taskId), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status: newStatus }),
            })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Erro ao atualizar status');
                }
                return response.json();
            })
            .then((data) => {
                if (!data.success) {
                    throw new Error('Resposta invalida do servidor');
                }
            })
            .catch(() => {
                if (originalColumn) {
                    originalColumn.appendChild(draggedCard);
                    draggedCard.dataset.status = currentStatus;
                    draggedCard.dataset.taskStatusLabel = statusLabels[currentStatus] || currentStatus;
                    updateCounts();
                }
                alert('Nao foi possivel atualizar o status da tarefa. Tente novamente.');
            });
        });
    });
});
</script>
@endpush
