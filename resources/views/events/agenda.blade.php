@extends('layouts.app')

@section('pageTitle', 'Agenda')

@section('content')
@php($eventStatusLabels = \App\Models\Event::statusLabels())
<div class="w-full max-w-full">
    <p class="mb-6 text-sm text-gray-600">
        Crie e organize seus compromissos. Navegue pelos meses, clique em um dia para adicionar um evento e clique em um evento para ver detalhes.
    </p>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <button
                type="button"
                id="agenda-prev"
                class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                title="Mes anterior"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button
                type="button"
                id="agenda-today"
                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                Hoje
            </button>
            <button
                type="button"
                id="agenda-next"
                class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                title="Proximo mes"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <div class="min-w-0">
            <h2 id="agenda-title" class="truncate text-lg font-semibold text-gray-900"></h2>
            <p id="agenda-subtitle" class="mt-0.5 text-xs text-gray-500"></p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                id="agenda-new"
                class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Novo evento
            </button>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50 text-xs font-semibold text-gray-600">
            <div class="px-3 py-2">Dom</div>
            <div class="px-3 py-2">Seg</div>
            <div class="px-3 py-2">Ter</div>
            <div class="px-3 py-2">Qua</div>
            <div class="px-3 py-2">Qui</div>
            <div class="px-3 py-2">Sex</div>
            <div class="px-3 py-2">Sab</div>
        </div>
        <div id="agenda-grid" class="grid grid-cols-7">
            {{-- render via JS --}}
        </div>
    </div>

    <div id="agenda-toast-container" class="pointer-events-none fixed bottom-6 left-1/2 z-[11000] flex -translate-x-1/2 flex-col gap-2"></div>

    <div id="event-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-4 py-3">
                <h3 id="event-modal-title" class="text-sm font-semibold text-gray-900">Novo evento</h3>
                <button type="button" id="event-modal-close" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <form id="event-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Titulo</label>
                        <input
                            id="event-title"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Ex.: Audiencia, reuniao com cliente, prazo de peticao"
                        >
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select
                                id="event-status"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                @foreach ($eventStatusLabels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categoria</label>
                            <input
                                id="event-category"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ex.: Audiencia, reuniao, prazo"
                            >
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data</label>
                            <input
                                id="event-date"
                                type="date"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Inicio</label>
                                <input
                                    id="event-start-time"
                                    type="time"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fim</label>
                                <input
                                    id="event-end-time"
                                    type="time"
                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Local</label>
                        <input
                            id="event-location"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Ex.: Forum, escritorio, online"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descricao</label>
                        <textarea
                            id="event-description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Observacoes, documentos necessarios, pessoas envolvidas..."
                        ></textarea>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <input id="event-is-public" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                            Publico
                        </label>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                id="event-form-cancel"
                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                id="event-form-submit"
                                class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Salvar
                            </button>
                        </div>
                    </div>
                    <p id="event-form-error" class="hidden text-sm text-red-600"></p>
                </form>
            </div>
        </div>
    </div>

    <div id="event-details-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-gray-100 bg-gray-50 px-4 py-3">
                <div class="min-w-0">
                    <h3 id="event-details-title" class="truncate text-sm font-semibold text-gray-900">Evento</h3>
                    <p id="event-details-when" class="mt-0.5 text-xs text-gray-500"></p>
                </div>
                <button type="button" id="event-details-close" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="space-y-4 p-4">
                <p id="event-details-description" class="whitespace-pre-line text-sm text-gray-700"></p>
                <dl class="grid grid-cols-1 gap-4 text-xs text-gray-600 md:grid-cols-2">
                    <div>
                        <dt class="font-medium text-gray-700">Status</dt>
                        <dd id="event-details-status" class="mt-0.5"></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Categoria</dt>
                        <dd id="event-details-category" class="mt-0.5"></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Local</dt>
                        <dd id="event-details-location" class="mt-0.5"></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Visibilidade</dt>
                        <dd id="event-details-visibility" class="mt-0.5"></dd>
                    </div>
                </dl>
            </div>
            <div class="flex items-center justify-between border-t border-gray-100 px-4 py-3">
                <button
                    type="button"
                    id="event-details-delete"
                    class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 hover:text-red-700"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        />
                    </svg>
                    Excluir
                </button>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        id="event-details-edit"
                        class="rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Editar
                    </button>
                    <button
                        type="button"
                        id="event-details-ok"
                        class="rounded-md px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const apiBase = "{{ url('/events') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const statusLabels = @json($eventStatusLabels);

    const titleEl = document.getElementById('agenda-title');
    const subtitleEl = document.getElementById('agenda-subtitle');
    const gridEl = document.getElementById('agenda-grid');

    const btnPrev = document.getElementById('agenda-prev');
    const btnNext = document.getElementById('agenda-next');
    const btnToday = document.getElementById('agenda-today');
    const btnNew = document.getElementById('agenda-new');

    const toastContainer = document.getElementById('agenda-toast-container');

    const modal = document.getElementById('event-modal');
    const modalTitle = document.getElementById('event-modal-title');
    const modalClose = document.getElementById('event-modal-close');
    const form = document.getElementById('event-form');
    const formError = document.getElementById('event-form-error');

    const inputTitle = document.getElementById('event-title');
    const inputStatus = document.getElementById('event-status');
    const inputCategory = document.getElementById('event-category');
    const inputDate = document.getElementById('event-date');
    const inputStartTime = document.getElementById('event-start-time');
    const inputEndTime = document.getElementById('event-end-time');
    const inputLocation = document.getElementById('event-location');
    const inputDescription = document.getElementById('event-description');
    const inputIsPublic = document.getElementById('event-is-public');
    const btnCancel = document.getElementById('event-form-cancel');

    const detailsModal = document.getElementById('event-details-modal');
    const detailsClose = document.getElementById('event-details-close');
    const detailsOk = document.getElementById('event-details-ok');
    const detailsDelete = document.getElementById('event-details-delete');
    const detailsEdit = document.getElementById('event-details-edit');
    const detailsTitle = document.getElementById('event-details-title');
    const detailsWhen = document.getElementById('event-details-when');
    const detailsDescription = document.getElementById('event-details-description');
    const detailsStatus = document.getElementById('event-details-status');
    const detailsCategory = document.getElementById('event-details-category');
    const detailsLocation = document.getElementById('event-details-location');
    const detailsVisibility = document.getElementById('event-details-visibility');

    const monthNames = ['janeiro', 'fevereiro', 'marco', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];

    let current = new Date();
    let events = [];
    let editingId = null;
    let detailsEvent = null;

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function ymd(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    function hm(d) {
        return pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    function chipClassForStatus(status) {
        if (status === 'confirmed') {
            return 'border-emerald-100 bg-emerald-50 text-emerald-800 hover:bg-emerald-100';
        }

        if (status === 'completed') {
            return 'border-slate-200 bg-slate-100 text-slate-700 hover:bg-slate-200';
        }

        if (status === 'cancelled') {
            return 'border-red-100 bg-red-50 text-red-800 hover:bg-red-100';
        }

        return 'border-amber-100 bg-amber-50 text-amber-800 hover:bg-amber-100';
    }

    function showToast(type, title, message) {
        if (!toastContainer) {
            return;
        }

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto min-w-[18rem] max-w-[26rem] rounded-lg border px-4 py-3 text-sm shadow-lg ' +
            (type === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
            : type === 'error' ? 'border-red-200 bg-red-50 text-red-800'
            : 'border-sky-200 bg-sky-50 text-sky-800');
        toast.innerHTML = '<strong class="mb-0.5 block">' + (title || '') + '</strong><span>' + (message || '') + '</span>';
        toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.2s';
            setTimeout(() => toast.remove(), 200);
        }, type === 'success' ? 4500 : 4000);
    }

    function openModal(mode, datePrefill) {
        editingId = mode === 'edit' ? editingId : null;
        formError.classList.add('hidden');
        formError.textContent = '';
        modalTitle.textContent = mode === 'edit' ? 'Editar evento' : 'Novo evento';

        if (mode !== 'edit') {
            inputTitle.value = '';
            inputStatus.value = 'pending';
            inputCategory.value = '';
            inputLocation.value = '';
            inputDescription.value = '';
            inputIsPublic.checked = true;
            inputEndTime.value = '';

            const d = datePrefill || new Date();
            inputDate.value = ymd(d);
            inputStartTime.value = '09:00';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => inputTitle.focus(), 50);
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        editingId = null;
    }

    function openDetails(ev) {
        detailsEvent = ev;
        detailsTitle.textContent = ev.title || 'Evento';

        const start = new Date(ev.starts_at);
        const end = ev.ends_at ? new Date(ev.ends_at) : null;

        detailsWhen.textContent = ymd(start) + ' - ' + hm(start) + (end ? (' ate ' + hm(end)) : '');
        detailsDescription.textContent = ev.description || 'Sem descricao.';
        detailsStatus.textContent = statusLabels[ev.status] || ev.status || '-';
        detailsCategory.textContent = ev.category || '-';
        detailsLocation.textContent = ev.location || '-';
        detailsVisibility.textContent = ev.is_public ? 'Publico' : 'Privado';

        detailsModal.classList.remove('hidden');
        detailsModal.classList.add('flex');
    }

    function closeDetails() {
        detailsModal.classList.add('hidden');
        detailsModal.classList.remove('flex');
        detailsEvent = null;
    }

    function setMonthTitle() {
        const m = current.getMonth();
        const y = current.getFullYear();
        titleEl.textContent = monthNames[m] + ' ' + y;
        subtitleEl.textContent = 'Clique em um dia para adicionar um evento.';
    }

    function buildGridDates() {
        const y = current.getFullYear();
        const m = current.getMonth();
        const first = new Date(y, m, 1);
        const start = new Date(first);
        start.setDate(first.getDate() - first.getDay());

        const last = new Date(y, m + 1, 0);
        const end = new Date(last);
        end.setDate(last.getDate() + (6 - last.getDay()));

        const days = [];
        const cursor = new Date(start);

        while (cursor <= end) {
            days.push(new Date(cursor));
            cursor.setDate(cursor.getDate() + 1);
        }

        return { start, end, days };
    }

    function eventsByDayMap() {
        const map = {};

        events.forEach(ev => {
            const key = ymd(new Date(ev.starts_at));
            if (!map[key]) {
                map[key] = [];
            }

            map[key].push(ev);
        });

        Object.keys(map).forEach(key => {
            map[key].sort((a, b) => new Date(a.starts_at) - new Date(b.starts_at));
        });

        return map;
    }

    function render() {
        setMonthTitle();
        const { days } = buildGridDates();
        const currentMonth = current.getMonth();
        const todayKey = ymd(new Date());
        const map = eventsByDayMap();

        gridEl.innerHTML = '';

        days.forEach(d => {
            const key = ymd(d);
            const inMonth = d.getMonth() === currentMonth;
            const isToday = key === todayKey;
            const dayEvents = map[key] || [];

            const cell = document.createElement('div');
            cell.className = 'relative min-h-[110px] border-b border-r border-gray-100 p-2 ' +
                (inMonth ? 'bg-white' : 'bg-gray-50') +
                (isToday ? ' ring-1 ring-indigo-300' : '');
            cell.dataset.date = key;

            const header = document.createElement('div');
            header.className = 'flex items-center justify-between';
            header.innerHTML =
                '<span class="text-xs font-semibold ' + (inMonth ? 'text-gray-900' : 'text-gray-400') + '">' + d.getDate() + '</span>' +
                (isToday ? '<span class="rounded border border-indigo-100 bg-indigo-50 px-1.5 py-0.5 text-[10px] font-medium text-indigo-700">Hoje</span>' : '');
            cell.appendChild(header);

            const list = document.createElement('div');
            list.className = 'mt-2 space-y-1';

            dayEvents.slice(0, 2).forEach(ev => {
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'w-full truncate rounded border px-2 py-1 text-left text-[11px] transition-colors ' + chipClassForStatus(ev.status);

                const start = new Date(ev.starts_at);
                const prefix = ev.category ? '[' + ev.category + '] ' : '';
                chip.textContent = hm(start) + ' - ' + prefix + (ev.title || 'Evento');
                chip.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openDetails(ev);
                });
                list.appendChild(chip);
            });

            if (dayEvents.length > 2) {
                const more = document.createElement('div');
                more.className = 'px-1 text-[11px] text-gray-500';
                more.textContent = '+' + (dayEvents.length - 2) + ' mais';
                list.appendChild(more);
            }

            cell.appendChild(list);
            cell.addEventListener('click', () => openModal('create', d));
            gridEl.appendChild(cell);
        });
    }

    async function loadEventsForCurrentView() {
        const { start, end } = buildGridDates();
        const url = apiBase + '?start=' + encodeURIComponent(ymd(start)) + '&end=' + encodeURIComponent(ymd(end));
        const response = await fetch(url, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('Falha ao carregar eventos');
        }

        events = await response.json();
        render();
    }

    function toIso(dateStr, timeStr) {
        return dateStr + ' ' + (timeStr || '00:00') + ':00';
    }

    async function createEvent(payload) {
        const response = await fetch(apiBase, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        });

        if (response.status === 422) {
            throw { type: 'validation', json: await response.json() };
        }

        if (!response.ok) {
            throw new Error('Erro ao salvar evento');
        }

        return await response.json();
    }

    async function updateEvent(id, payload) {
        const response = await fetch(apiBase + '/' + id, {
            method: 'PUT',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        });

        if (response.status === 422) {
            throw { type: 'validation', json: await response.json() };
        }

        if (!response.ok) {
            throw new Error('Erro ao atualizar evento');
        }

        return await response.json();
    }

    async function deleteEvent(id) {
        const response = await fetch(apiBase + '/' + id, {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        if (!response.ok) {
            throw new Error('Erro ao excluir evento');
        }
    }

    function validationMessage(json) {
        const errors = json && json.errors ? json.errors : null;
        if (!errors) {
            return 'Verifique os campos e tente novamente.';
        }

        const firstKey = Object.keys(errors)[0];
        return firstKey ? (errors[firstKey][0] || 'Verifique os campos.') : 'Verifique os campos.';
    }

    btnPrev.addEventListener('click', async () => {
        current = new Date(current.getFullYear(), current.getMonth() - 1, 1);
        await loadEventsForCurrentView();
    });

    btnNext.addEventListener('click', async () => {
        current = new Date(current.getFullYear(), current.getMonth() + 1, 1);
        await loadEventsForCurrentView();
    });

    btnToday.addEventListener('click', async () => {
        current = new Date();
        current = new Date(current.getFullYear(), current.getMonth(), 1);
        await loadEventsForCurrentView();
    });

    btnNew.addEventListener('click', () => openModal('create', new Date()));

    modalClose.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    detailsClose.addEventListener('click', closeDetails);
    detailsOk.addEventListener('click', closeDetails);
    detailsModal.addEventListener('click', (e) => {
        if (e.target === detailsModal) {
            closeDetails();
        }
    });

    detailsEdit.addEventListener('click', () => {
        if (!detailsEvent) {
            return;
        }

        editingId = detailsEvent.id;
        const start = new Date(detailsEvent.starts_at);
        inputTitle.value = detailsEvent.title || '';
        inputStatus.value = detailsEvent.status || 'pending';
        inputCategory.value = detailsEvent.category || '';
        inputDate.value = ymd(start);
        inputStartTime.value = hm(start);
        inputEndTime.value = detailsEvent.ends_at ? hm(new Date(detailsEvent.ends_at)) : '';
        inputLocation.value = detailsEvent.location || '';
        inputDescription.value = detailsEvent.description || '';
        inputIsPublic.checked = !!detailsEvent.is_public;

        closeDetails();
        modalTitle.textContent = 'Editar evento';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => inputTitle.focus(), 50);
    });

    detailsDelete.addEventListener('click', async () => {
        if (!detailsEvent) {
            return;
        }

        if (!confirm('Excluir este evento?')) {
            return;
        }

        try {
            await deleteEvent(detailsEvent.id);
            showToast('success', 'Evento excluido', 'O evento foi removido da sua agenda.');
            closeDetails();
            await loadEventsForCurrentView();
        } catch (error) {
            showToast('error', 'Erro', 'Nao foi possivel excluir o evento.');
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        formError.classList.add('hidden');
        formError.textContent = '';

        const payload = {
            title: inputTitle.value.trim(),
            status: inputStatus.value,
            category: inputCategory.value.trim() || null,
            description: inputDescription.value.trim() || null,
            starts_at: toIso(inputDate.value, inputStartTime.value),
            ends_at: inputEndTime.value ? toIso(inputDate.value, inputEndTime.value) : null,
            location: inputLocation.value.trim() || null,
            is_public: !!inputIsPublic.checked,
        };

        try {
            if (editingId) {
                await updateEvent(editingId, payload);
                showToast('success', 'Evento atualizado', 'As alteracoes foram salvas.');
            } else {
                await createEvent(payload);
                showToast('success', 'Evento criado', 'O evento foi adicionado na sua agenda.');
            }

            closeModal();
            await loadEventsForCurrentView();
        } catch (error) {
            if (error && error.type === 'validation') {
                formError.textContent = validationMessage(error.json);
                formError.classList.remove('hidden');
                return;
            }

            formError.textContent = 'Nao foi possivel salvar o evento. Tente novamente.';
            formError.classList.remove('hidden');
        }
    });

    current = new Date(current.getFullYear(), current.getMonth(), 1);
    loadEventsForCurrentView().catch(() => {
        showToast('error', 'Erro', 'Nao foi possivel carregar os eventos.');
    });
})();
</script>
@endpush
