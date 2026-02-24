@extends('layouts.app')

@section('pageTitle', 'Kanban de tarefas')

@section('content')
<div class="w-full max-w-full">
    @if(session('success'))
    <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800" role="alert">
        {{ session('success') }}
    </div>
    @endif

    <p class="text-gray-600 text-sm mb-6">
        Organize o trabalho arrastando as tarefas entre os status. Crie novas tarefas rapidamente e acompanhe o fluxo em um quadro kanban.
    </p>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 m-0">
                Kanban de tarefas
            </h2>
            <p class="mt-1 text-xs text-gray-500">
                Arraste as tarefas entre as colunas, defina responsáveis e acompanhe o fluxo.
            </p>
        </div>
        <div class="inline-flex items-center gap-2">
            <button type="button"
                id="kanban-open-create"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nova tarefa
            </button>
        </div>
    </div>

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
                'label' => 'Concluída',
                'dot' => 'bg-emerald-500',
            ],
        ];

        $tasksByStatus = $tasks->groupBy('status');
    @endphp

    <div class="rounded-xl border border-gray-200 bg-slate-50/80">
        <div class="max-h-[60vh] md:max-h-[calc(100vh-260px)] overflow-auto p-3 md:p-4">
            <div id="kanban-board"
                class="grid grid-cols-1 md:grid-cols-3 gap-4"
                data-update-url-template="{{ route('tasks.update-status', ['task' => '__TASK_ID__']) }}"
                data-assign-url-template="{{ route('tasks.update-assignee', ['task' => '__TASK_ID__']) }}"
                data-delete-url-template="{{ route('tasks.destroy', ['task' => '__TASK_ID__']) }}">
                @foreach($statuses as $statusKey => $statusConfig)
                <div class="flex flex-col rounded-lg border border-gray-200 bg-white shadow-sm min-h-[340px]">
            <div class="flex items-center justify-between gap-2 px-3 py-2 border-b border-gray-100 bg-gray-50/80">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full {{ $statusConfig['dot'] }}"></span>
                    <span class="text-xs font-medium text-gray-700 uppercase tracking-wide">
                        {{ $statusConfig['label'] }}
                    </span>
                </div>
                <span class="kanban-count text-xs text-gray-400"
                    data-status="{{ $statusKey }}">
                    {{ isset($tasksByStatus[$statusKey]) ? $tasksByStatus[$statusKey]->count() : 0 }}
                </span>
            </div>
            <div class="kanban-column flex-1 p-3 space-y-3 overflow-y-auto"
                data-status="{{ $statusKey }}">
                @forelse(($tasksByStatus[$statusKey] ?? collect()) as $task)
                <div class="kanban-card rounded-md border border-gray-200 bg-white p-3 shadow-sm cursor-move hover:border-indigo-300 hover:shadow-md transition-all"
                    draggable="true"
                    data-task-id="{{ $task->id }}"
                    data-status="{{ $task->status }}"
                    data-task-title="{{ $task->title }}"
                    data-task-description="{{ $task->description }}"
                    data-task-status-label="{{ $statusConfig['label'] }}"
                    data-task-assignee="{{ ($task->users && $task->users->count()) ? $task->users->pluck('name')->join(', ') : 'Sem responsável' }}"
                    data-task-created="{{ optional($task->created_at)->format('d/m/Y H:i') }}"
                    data-task-updated="{{ optional($task->updated_at)->format('d/m/Y H:i') }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $task->title }}
                            </p>
                            @if($task->description)
                            <p class="mt-1 text-xs text-gray-500 line-clamp-3">
                                {{ $task->description }}
                            </p>
                            @endif
                        </div>
                        <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-0.5 text-[10px] font-medium text-gray-500">
                            #{{ $task->id }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-2">
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] text-gray-600 kanban-assignee-label">
                            @if($task->users && $task->users->count())
                                {{ $task->users->pluck('name')->first() }}
                            @else
                                Sem responsável
                            @endif
                        </span>

                        @if($statusKey === 'pending')
                        <select
                            class="kanban-assign-select rounded-md border-gray-200 bg-white text-[11px] text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            data-task-id="{{ $task->id }}">
                            <option value="">Definir responsável</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                @if($task->users && $task->users->contains($user)) selected @endif>
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
                <p class="text-xs text-gray-400 italic">
                    Nenhuma tarefa neste status.
                </p>
                @endforelse
            </div>
        </div>
        @endforeach
            </div>
        </div>
    </div>

    {{-- Modal: criar tarefa --}}
    <div id="task-create-modal"
        class="fixed inset-0 z-40 hidden items-center justify-center bg-slate-900/40">
        <div class="relative w-full max-w-xl rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                <h3 class="text-sm font-semibold text-gray-900">Nova tarefa</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600"
                    data-task-create-close>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="modal-title" class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" name="title" id="modal-title" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Ex.: Revisar petição inicial, preparar audiência, ligar para o cliente">
                        </div>
                        <div>
                            <label for="modal-status" class="block text-sm font-medium text-gray-700">Status inicial</label>
                            <select name="status" id="modal-status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="pending">Pendente</option>
                                <option value="in_progress">Em progresso</option>
                                <option value="completed">Concluída</option>
                            </select>
                        </div>
                        <div>
                            <label for="modal-assignee" class="block text-sm font-medium text-gray-700">Responsável</label>
                            <select name="users[]" id="modal-assignee"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Sem responsável</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="modal-description" class="block text-sm font-medium text-gray-700">Descrição</label>
                        <textarea name="description" id="modal-description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Detalhes, próximos passos ou contexto da tarefa"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <button type="button"
                            class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-md"
                            data-task-create-close>
                            Cancelar
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Salvar tarefa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: detalhes da tarefa --}}
    <div id="task-details-modal"
        class="fixed inset-0 z-40 hidden items-center justify-center bg-slate-900/40">
        <div class="relative w-full max-w-xl rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                <div>
                    <h3 id="task-details-title" class="text-sm font-semibold text-gray-900">Tarefa</h3>
                    <p id="task-details-status" class="mt-0.5 text-xs text-gray-500"></p>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-600"
                    data-task-details-close>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4 space-y-4">
                <p id="task-details-description" class="text-sm text-gray-700 whitespace-pre-line"></p>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-600">
                    <div>
                        <dt class="font-medium text-gray-700">Responsável</dt>
                        <dd id="task-details-assignee" class="mt-0.5 text-gray-600"></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Status</dt>
                        <dd id="task-details-status-label" class="mt-0.5 text-gray-600"></dd>
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
            <div class="flex justify-between items-center gap-2 border-t border-gray-100 px-4 py-3">
                <button type="button"
                    id="task-details-delete"
                    class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Excluir tarefa</span>
                </button>
                <button type="button"
                    class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-md"
                    data-task-details-close>
                    Fechar
                </button>
            </div>
        </div>
    </div>
    {{-- Modal: confirmação de exclusão --}}
    <div id="task-delete-confirm-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50">
        <div class="w-full max-w-sm rounded-xl bg-white shadow-2xl border border-red-100">
            <div class="flex items-center gap-2 border-b border-red-50 px-4 py-3 bg-red-50/80">
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3.75M12 15.75h.007v.008H12v-.008zM21 12A9 9 0 113 12a9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Excluir tarefa</h3>
            </div>
            <div class="px-4 py-3 space-y-2">
                <p class="text-sm text-gray-700">
                    Tem certeza que deseja excluir esta tarefa? Essa ação não pode ser desfeita.
                </p>
                <p id="task-delete-confirm-title" class="text-xs text-gray-500 line-clamp-2"></p>
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-100 px-4 py-3">
                <button type="button"
                    class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-md"
                    data-task-delete-cancel>
                    Cancelar
                </button>
                <button type="button"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md"
                    data-task-delete-confirm>
                    Confirmar exclusão
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

    const updateUrlTemplate = board.dataset.updateUrlTemplate;
    const assignUrlTemplate = board.dataset.assignUrlTemplate;
    const deleteUrlTemplate = board.dataset.deleteUrlTemplate;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const columns = Array.from(board.querySelectorAll('.kanban-column'));
    const cards = Array.from(board.querySelectorAll('.kanban-card'));

    const createButton = document.getElementById('kanban-open-create');
    const createModal = document.getElementById('task-create-modal');
    const createCloseButtons = createModal ? createModal.querySelectorAll('[data-task-create-close]') : [];

    const detailsModal = document.getElementById('task-details-modal');
    const detailsCloseButtons = detailsModal ? detailsModal.querySelectorAll('[data-task-details-close]') : [];
    const detailsTitle = document.getElementById('task-details-title');
    const detailsStatus = document.getElementById('task-details-status');
    const detailsDescription = document.getElementById('task-details-description');
    const detailsAssignee = document.getElementById('task-details-assignee');
    const detailsStatusLabel = document.getElementById('task-details-status-label');
    const detailsCreated = document.getElementById('task-details-created');
    const detailsUpdated = document.getElementById('task-details-updated');
    const detailsDeleteButton = document.getElementById('task-details-delete');

    const deleteConfirmModal = document.getElementById('task-delete-confirm-modal');
    const deleteConfirmTitle = document.getElementById('task-delete-confirm-title');
    const deleteConfirmButton = deleteConfirmModal ? deleteConfirmModal.querySelector('[data-task-delete-confirm]') : null;
    const deleteCancelButton = deleteConfirmModal ? deleteConfirmModal.querySelector('[data-task-delete-cancel]') : null;

    const deleteForm = document.getElementById('task-delete-form');

    let currentDetailsTaskId = null;

    let draggedCard = null;
    let originalColumn = null;

    function openCreateModal() {
        if (!createModal) return;
        createModal.classList.remove('hidden');
        createModal.classList.add('flex');
    }

    function closeCreateModal() {
        if (!createModal) return;
        createModal.classList.add('hidden');
        createModal.classList.remove('flex');
    }

    function openDetailsModalFromCard(card) {
        if (!detailsModal || !card) return;

        const id = card.dataset.taskId || '';
        const title = card.dataset.taskTitle || ('Tarefa #' + id);
        const description = card.dataset.taskDescription || '';
        const assignee = card.dataset.taskAssignee || 'Sem responsável';
        const statusLabel = card.dataset.taskStatusLabel || '';
        const created = card.dataset.taskCreated || '';
        const updated = card.dataset.taskUpdated || '';

        currentDetailsTaskId = id || null;

        if (detailsTitle) detailsTitle.textContent = title;
        if (detailsStatus) detailsStatus.textContent = id ? ('Tarefa #' + id) : '';
        if (detailsDescription) detailsDescription.textContent = description || 'Sem descrição.';
        if (detailsAssignee) detailsAssignee.textContent = assignee;
        if (detailsStatusLabel) detailsStatusLabel.textContent = statusLabel || '-';
        if (detailsCreated) detailsCreated.textContent = created || '-';
        if (detailsUpdated) detailsUpdated.textContent = updated || '-';

        detailsModal.classList.remove('hidden');
        detailsModal.classList.add('flex');
    }

    function closeDetailsModal() {
        if (!detailsModal) return;
        detailsModal.classList.add('hidden');
        detailsModal.classList.remove('flex');
    }

    function updateCounts() {
        const counts = board.querySelectorAll('.kanban-count');
        counts.forEach((countEl) => {
            const status = countEl.dataset.status;
            const column = board.querySelector('.kanban-column[data-status="' + status + '"]');
            if (!column) return;
            const total = column.querySelectorAll('.kanban-card').length;
            countEl.textContent = total;
        });
    }

    function attachCardEvents(card) {
        card.addEventListener('dragstart', (event) => {
            draggedCard = card;
            originalColumn = card.parentElement;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', card.dataset.taskId);
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
    }

    cards.forEach(attachCardEvents);

    const detailButtons = board.querySelectorAll('.kanban-view-details');
    detailButtons.forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const card = btn.closest('.kanban-card');
            openDetailsModalFromCard(card);
        });
    });

    function attachAssignEvents() {
        const selects = board.querySelectorAll('.kanban-assign-select');

        selects.forEach((select) => {
            select.addEventListener('change', (event) => {
                const el = event.target;
                const taskId = el.dataset.taskId;
                if (!taskId || !assignUrlTemplate) return;

                const card = el.closest('.kanban-card');
                const label = card ? card.querySelector('.kanban-assignee-label') : null;
                const previousText = label ? label.textContent : '';
                const selectedOption = el.options[el.selectedIndex];
                const newText = selectedOption && selectedOption.value
                    ? selectedOption.text
                    : 'Sem responsável';

                if (label) {
                    label.textContent = newText;
                }

                const url = assignUrlTemplate.replace('__TASK_ID__', taskId);

                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ user_id: selectedOption.value || null }),
                })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Erro ao atualizar responsável');
                    }
                    return response.json();
                })
                .then((data) => {
                    if (!data.success) {
                        throw new Error('Resposta inválida do servidor');
                    }
                })
                .catch(() => {
                    if (label) {
                        label.textContent = previousText;
                    }
                    alert('Não foi possível atualizar o responsável. Tente novamente.');
                });
            });
        });
    }

    attachAssignEvents();

    if (createButton && createModal) {
        createButton.addEventListener('click', (event) => {
            event.preventDefault();
            openCreateModal();
        });
    }

    createCloseButtons.forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            closeCreateModal();
        });
    });

    if (createModal) {
        createModal.addEventListener('click', (event) => {
            if (event.target === createModal) {
                closeCreateModal();
            }
        });
    }

    detailsCloseButtons.forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            closeDetailsModal();
        });
    });

    if (detailsModal) {
        detailsModal.addEventListener('click', (event) => {
            if (event.target === detailsModal) {
                closeDetailsModal();
            }
        });
    }

    function openDeleteConfirmModal() {
        if (!deleteConfirmModal || !currentDetailsTaskId) return;

        if (deleteConfirmTitle && detailsTitle) {
            deleteConfirmTitle.textContent = detailsTitle.textContent;
        }

        deleteConfirmModal.classList.remove('hidden');
        deleteConfirmModal.classList.add('flex');
    }

    function closeDeleteConfirmModal() {
        if (!deleteConfirmModal) return;
        deleteConfirmModal.classList.add('hidden');
        deleteConfirmModal.classList.remove('flex');
    }

    if (detailsDeleteButton && deleteUrlTemplate) {
        detailsDeleteButton.addEventListener('click', (event) => {
            event.preventDefault();
            if (!currentDetailsTaskId) {
                return;
            }
            openDeleteConfirmModal();
        });
    }

    if (deleteConfirmButton && deleteForm && deleteUrlTemplate) {
        deleteConfirmButton.addEventListener('click', (event) => {
            event.preventDefault();
            if (!currentDetailsTaskId) return;

            const url = deleteUrlTemplate.replace('__TASK_ID__', currentDetailsTaskId);
            deleteForm.setAttribute('action', url);
            deleteForm.submit();
        });
    }

    if (deleteCancelButton) {
        deleteCancelButton.addEventListener('click', (event) => {
            event.preventDefault();
            closeDeleteConfirmModal();
        });
    }

    if (deleteConfirmModal) {
        deleteConfirmModal.addEventListener('click', (event) => {
            if (event.target === deleteConfirmModal) {
                closeDeleteConfirmModal();
            }
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

            // Move visual
            column.appendChild(draggedCard);
            draggedCard.dataset.status = newStatus;
            updateCounts();

            // Persist via API
            const url = updateUrlTemplate.replace('__TASK_ID__', taskId);

            fetch(url, {
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
                    throw new Error('Resposta inválida do servidor');
                }
            })
            .catch(() => {
                // Reverter visual em caso de erro
                if (originalColumn) {
                    originalColumn.appendChild(draggedCard);
                    draggedCard.dataset.status = currentStatus;
                    updateCounts();
                }
                alert('Não foi possível atualizar o status da tarefa. Tente novamente.');
            });
        });
    });
});
</script>
@endpush

