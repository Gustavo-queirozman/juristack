<?php $__env->startSection('content'); ?>
<div class="container">
    <style>
        .datajud-hero { margin-bottom: 1rem; display:flex; align-items:center; justify-content:space-between; gap:1rem }
        .datajud-hero h3 { margin:0; font-weight:700; color:#0b5ed7 }
        .card.datajud-card { box-shadow: 0 6px 18px rgba(15,23,42,0.06); border-radius:8px; overflow:hidden }
        .card.datajud-card .card-header { background: linear-gradient(90deg,#f8fafc,#ffffff); border-bottom:1px solid rgba(0,0,0,0.05); font-weight:600 }
        .muted { color:#6b7280 }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
        pre#jsonModalContent { background:#0b1220; color:#dbeafe; padding:1rem; border-radius:6px; white-space:pre-wrap; word-break:break-word; }
        #toast-container { z-index: 11000 }
    </style>

    <div class="datajud-hero">
        <div>
            <h3>Processos Monitorados</h3>
            <div class="muted">Lista persistida no seu navegador (localStorage).</div>
        </div>

        <div class="d-flex gap-2">
            <button id="btn-refresh-all" class="btn btn-outline-primary">Atualizar todos</button>
            <button id="btn-clear-all" class="btn btn-outline-danger">Limpar lista</button>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card datajud-card">
                <div class="card-header">Filtro</div>
                <div class="card-body">
                    <label class="form-label">Buscar</label>
                    <input id="filter" type="text" class="form-control" placeholder="número ou tribunal...">

                    <hr class="my-3">

                    <div class="small muted">
                        <div><strong>Chave do storage:</strong> <span class="mono">datajud_monitored_v1</span></div>
                        <div class="mt-2">Dica: essa tela refaz a consulta no DataJud sob demanda.</div>
                    </div>
                </div>
            </div>

            <div class="card datajud-card mt-3">
                <div class="card-header">Resumo</div>
                <div class="card-body">
                    <div><strong>Total:</strong> <span id="count">0</span></div>
                    <div class="muted small mt-1" id="last-update"></div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div id="list"></div>
            <div id="empty" class="alert alert-warning d-none">Você não tem processos monitorados ainda.</div>
        </div>
    </div>

    <div id="toast-container" class="position-fixed top-0 end-0 p-3"></div>

    <!-- JSON modal -->
    <div class="modal fade" id="jsonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">JSON do Processo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre id="jsonModalContent"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const monitoredKey = 'datajud_monitored_v1';
            const listEl = document.getElementById('list');
            const emptyEl = document.getElementById('empty');
            const countEl = document.getElementById('count');
            const lastUpdateEl = document.getElementById('last-update');
            const filterEl = document.getElementById('filter');

            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '<?php echo e(csrf_token()); ?>';

            function loadMonitored() {
                try { return JSON.parse(localStorage.getItem(monitoredKey) || '{}'); }
                catch(e) { return {}; }
            }
            function saveMonitored(map) {
                localStorage.setItem(monitoredKey, JSON.stringify(map));
            }

            function fmtDate(d) {
                if (!d) return '';
                try {
                    const dt = new Date(d);
                    return new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeStyle: 'short' }).format(dt);
                } catch(e) { return d; }
            }

            function mapStatusFromMovement(mov) {
                if (!mov || !mov.nome) return 'Desconhecido';
                const name = mov.nome.toLowerCase();
                if (name.includes('sentença') || name.includes('julgado')) return 'Julgado';
                if (name.includes('decisão') || name.includes('decis')) return 'Decisão';
                if (name.includes('audiencia') || name.includes('audiência')) return 'Audiência';
                if (name.includes('conclus') || name.includes('conclusão')) return 'Concluso';
                if (name.includes('distribui') || name.includes('sorteio')) return 'Distribuído';
                if (name.includes('arquivado') || name.includes('arquiv')) return 'Arquivado';
                if (name.includes('petição') || name.includes('peticao')) return 'Petição';
                return mov.nome;
            }

            function showToast(title, message) {
                const id = 'toast-' + Date.now();
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = 'toast align-items-center text-bg-light border';
                toast.id = id;
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.setAttribute('aria-atomic', 'true');
                toast.innerHTML = `<div class="d-flex">
                    <div class="toast-body"><strong>${title}</strong><div>${message}</div></div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>`;
                container.appendChild(toast);
                const bsToast = new bootstrap.Toast(toast, { delay: 6000 });
                bsToast.show();
            }

            async function fetchProcesso(tribunal, numero) {
                const payload = { tribunal, numero_processo: numero };

                const r = await fetch('<?php echo e(url('/api/datajud/search')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const json = await r.json();
                const hits = (json && json.hits && json.hits.hits) ? json.hits.hits : [];
                return hits.length ? hits[0] : null;
            }

            function cardSkeleton(tribunal, numero) {
                const div = document.createElement('div');
                div.className = 'card datajud-card mb-3';
                div.innerHTML = `
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div><strong class="mono">${numero}</strong> <span class="badge bg-secondary ms-2">${tribunal}</span></div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" data-action="refresh">Atualizar</button>
                            <button class="btn btn-sm btn-outline-info" data-action="json">Ver JSON</button>
                            <button class="btn btn-sm btn-outline-danger" data-action="stop">Parar</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="muted">Carregando detalhes...</div>
                    </div>
                `;
                return div;
            }

            function renderCardBody(card, hit) {
                const body = card.querySelector('.card-body');
                const src = hit?._source || {};

                const classe = (src.classe && src.classe.nome) ? src.classe.nome : (src.classe || '');
                const assuntos = (src.assuntos && src.assuntos.length) ? src.assuntos.map(a => a.nome).join(', ') : (src.assunto || '');
                const orgao = (src.orgaoJulgador && src.orgaoJulgador.nome) ? src.orgaoJulgador.nome : (src.orgaoJulgador || '');
                const ajuizamento = src.dataAjuizamento || '';
                const updatedAt = src.dataHoraUltimaAtualizacao || src.dataHora || '';

                const lastMovement = (src.movimentos && src.movimentos.length) ? src.movimentos[src.movimentos.length - 1] : null;
                const status = mapStatusFromMovement(lastMovement);

                const recent = (src.movimentos || []).slice(-5).reverse();
                const recentHtml = recent.map(m => `
                    <div><small class="text-muted">${fmtDate(m.dataHora || m.data || '')}</small> — ${m.nome || m.descricao || ''}</div>
                `).join('');

                body.innerHTML = `
                    ${assuntos ? `<div class="mb-2"><strong>Assuntos:</strong> ${assuntos}</div>` : ``}
                    <div class="d-flex flex-wrap gap-3">
                        <div><strong>Classe:</strong> ${classe || '-'}</div>
                        <div><strong>Status:</strong> ${status}</div>
                        <div><strong>Atualizado:</strong> ${fmtDate(updatedAt) || '-'}</div>
                    </div>

                    <hr class="my-3">

                    <table class="table table-sm mb-2">
                        <tbody>
                            <tr><th style="width:30%">Juízo/Órgão</th><td>${orgao || '-'}</td></tr>
                            <tr><th>Data ajuizamento</th><td>${fmtDate(ajuizamento) || '-'}</td></tr>
                            <tr><th>Movimentações</th><td>${(src.movimentos || []).length}</td></tr>
                        </tbody>
                    </table>

                    <div class="mt-2">
                        <strong>Últimos movimentos:</strong>
                        <div>${recentHtml || '<em>Nenhum</em>'}</div>
                    </div>
                `;
            }

            function renderList() {
                const map = loadMonitored();
                const query = (filterEl.value || '').trim().toLowerCase();

                const keys = Object.keys(map)
                    .filter(k => {
                        if (!query) return true;
                        const it = map[k];
                        return (it.numero || '').toLowerCase().includes(query) || (it.tribunal || '').toLowerCase().includes(query);
                    });

                countEl.textContent = keys.length;
                listEl.innerHTML = '';

                if (keys.length === 0) {
                    emptyEl.classList.remove('d-none');
                    return;
                }
                emptyEl.classList.add('d-none');

                keys.forEach(k => {
                    const it = map[k]; // {tribunal, numero, lastSignature}
                    const card = cardSkeleton(it.tribunal, it.numero);
                    listEl.appendChild(card);

                    // carregamento real
                    fetchProcesso(it.tribunal, it.numero)
                        .then(hit => {
                            if (!hit) {
                                card.querySelector('.card-body').innerHTML = `<div class="alert alert-warning mb-0">Não encontrei detalhes no DataJud agora.</div>`;
                                return;
                            }
                            renderCardBody(card, hit);

                            // atualiza lastSignature com base no último movimento atual
                            const src = hit._source || {};
                            const lastMov = (src.movimentos && src.movimentos.length) ? src.movimentos[src.movimentos.length - 1] : null;
                            const signature = lastMov ? (lastMov.nome + (lastMov.dataHora ? ' — ' + lastMov.dataHora : '')) : '';
                            const m = loadMonitored();
                            if (m[k]) {
                                m[k].lastSignature = signature;
                                saveMonitored(m);
                            }
                        })
                        .catch(() => {
                            card.querySelector('.card-body').innerHTML = `<div class="alert alert-danger mb-0">Erro ao consultar o DataJud.</div>`;
                        });

                    // ações
                    card.querySelector('[data-action="stop"]').addEventListener('click', () => {
                        const m = loadMonitored();
                        delete m[k];
                        saveMonitored(m);
                        showToast('Monitoramento', 'Removido da lista de monitorados.');
                        renderList();
                    });

                    card.querySelector('[data-action="refresh"]').addEventListener('click', async () => {
                        try {
                            const hit = await fetchProcesso(it.tribunal, it.numero);
                            if (!hit) {
                                showToast('Atualização', 'Sem resultados no DataJud.');
                                return;
                            }
                            renderCardBody(card, hit);
                            lastUpdateEl.textContent = 'Última atualização: ' + fmtDate(new Date().toISOString());
                            showToast('Atualização', 'Processo atualizado.');
                        } catch (e) {
                            showToast('Erro', 'Falha ao atualizar este processo.');
                        }
                    });

                    card.querySelector('[data-action="json"]').addEventListener('click', async () => {
                        try {
                            const hit = await fetchProcesso(it.tribunal, it.numero);
                            const src = hit?._source || {};
                            document.getElementById('jsonModalContent').textContent = JSON.stringify(src, null, 2);
                            const modal = new bootstrap.Modal(document.getElementById('jsonModal'));
                            modal.show();
                        } catch(e) {
                            showToast('Erro', 'Falha ao carregar JSON.');
                        }
                    });
                });
            }

            // Refresh all + clear all
            document.getElementById('btn-clear-all').addEventListener('click', () => {
                localStorage.removeItem(monitoredKey);
                showToast('Monitoramento', 'Lista limpa.');
                renderList();
            });

            document.getElementById('btn-refresh-all').addEventListener('click', async () => {
                const map = loadMonitored();
                const keys = Object.keys(map);
                if (!keys.length) return;

                showToast('Atualização', 'Atualizando todos...');
                // estratégia simples: re-render e cada card puxa de novo
                renderList();
                lastUpdateEl.textContent = 'Última atualização: ' + fmtDate(new Date().toISOString());
            });

            filterEl.addEventListener('input', renderList);

            // init
            renderList();
        })();
    </script>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/gustavo/Desktop/juristack/resources/views/datajud/monitorados.blade.php ENDPATH**/ ?>