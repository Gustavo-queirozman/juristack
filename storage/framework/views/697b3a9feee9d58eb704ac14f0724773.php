<?php $__env->startSection('pageTitle', 'Pesquisa de Processos'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <p class="text-gray-600 text-sm mb-6">
        Busque processos no DataJud por número. Selecione o tribunal, informe o número e clique em <strong>Pesquisar</strong>. Depois você pode salvar o processo para acompanhamento.
    </p>

    
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden mb-6">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900 m-0">Pesquisa</h3>
        </div>
        <div class="p-4">
            <form id="datajud-form" method="POST" action="#">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="tribunal" class="block text-sm font-medium text-gray-700 mb-1">Tribunal <span class="text-red-500">*</span></label>
                        <select name="tribunal" id="tribunal" required class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">Selecione o tribunal</option>
                            <option value="ALL">Todos os tribunais</option>
                            <optgroup label="Supremos / Superiores">
                                <option value="STF">STF - Supremo Tribunal Federal</option>
                                <option value="STJ">STJ - Superior Tribunal de Justiça</option>
                                <option value="TST">TST - Tribunal Superior do Trabalho</option>
                            </optgroup>
                            <optgroup label="TRFs">
                                <option value="TRF1">TRF1 - 1ª Região</option>
                                <option value="TRF2">TRF2 - 2ª Região</option>
                                <option value="TRF3">TRF3 - 3ª Região</option>
                                <option value="TRF4">TRF4 - 4ª Região</option>
                                <option value="TRF5">TRF5 - 5ª Região</option>
                                <option value="TRF6">TRF6 - 6ª Região</option>
                            </optgroup>
                            <optgroup label="Tribunais de Justiça (Estados)">
                                <option value="TJAC">TJAC - Acre</option>
                                <option value="TJAL">TJAL - Alagoas</option>
                                <option value="TJAP">TJAP - Amapá</option>
                                <option value="TJAM">TJAM - Amazonas</option>
                                <option value="TJBA">TJBA - Bahia</option>
                                <option value="TJCE">TJCE - Ceará</option>
                                <option value="TJDFT">TJDFT - Distrito Federal</option>
                                <option value="TJES">TJES - Espírito Santo</option>
                                <option value="TJGO">TJGO - Goiás</option>
                                <option value="TJMA">TJMA - Maranhão</option>
                                <option value="TJMT">TJMT - Mato Grosso</option>
                                <option value="TJMS">TJMS - Mato Grosso do Sul</option>
                                <option value="TJMG">TJMG - Minas Gerais</option>
                                <option value="TJPB">TJPB - Paraíba</option>
                                <option value="TJPA">TJPA - Pará</option>
                                <option value="TJPR">TJPR - Paraná</option>
                                <option value="TJPE">TJPE - Pernambuco</option>
                                <option value="TJPI">TJPI - Piauí</option>
                                <option value="TJRJ">TJRJ - Rio de Janeiro</option>
                                <option value="TJRN">TJRN - Rio Grande do Norte</option>
                                <option value="TJRS">TJRS - Rio Grande do Sul</option>
                                <option value="TJRO">TJRO - Rondônia</option>
                                <option value="TJRR">TJRR - Roraima</option>
                                <option value="TJSC">TJSC - Santa Catarina</option>
                                <option value="TJSP">TJSP - São Paulo</option>
                                <option value="TJSE">TJSE - Sergipe</option>
                                <option value="TJTO">TJTO - Tocantins</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label for="tipo_consulta" class="block text-sm font-medium text-gray-700 mb-1">Tipo de consulta</label>
                        <select id="tipo_consulta" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="numero" selected>Número do Processo</option>
                        </select>
                    </div>
                    <div>
                        <label for="numero_processo" class="block text-sm font-medium text-gray-700 mb-1">Número do processo</label>
                        <input type="text" name="numero_processo" id="numero_processo" value="<?php echo e(request('numero_processo')); ?>"
                               placeholder="Ex: 0001234-56.2023.8.26.0000"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="rounded-md bg-indigo-50 border border-indigo-200 px-4 py-3 text-sm text-indigo-800 mb-4">
                    Preencha o <strong>número do processo</strong> no formato do tribunal e clique em Pesquisar.
                </div>
                <button type="submit" id="btn-pesquisar" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Pesquisar
                </button>
            </form>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900 m-0">Resultados</h3>
                </div>
                <div class="p-4 min-h-[200px]">
                    <div id="resultados">
                        <p class="text-sm text-gray-500 m-0">Digite o número do processo (ex: 0001234-56.2023.8.26.0000), selecione o tribunal e clique em <strong>Pesquisar</strong>. Depois você pode salvar o processo para acompanhamento.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900 m-0">Processos salvos</h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-500 mb-3">Processos que você salvou ficam disponíveis para consulta e acompanhamento.</p>
                    <a href="<?php echo e(route('datajud.salvos')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Ver meus processos salvos
                    </a>
                    <h4 class="text-sm font-medium text-gray-900 mt-4 mb-2">Monitorados nesta sessão</h4>
                    <div id="monitored-list" class="divide-y divide-gray-100 rounded-md border border-gray-100"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="toast-container" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[11000] flex flex-col gap-2 pointer-events-none"></div>


<div id="jsonModal" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 bg-black/50" role="dialog" aria-labelledby="jsonModalTitle" aria-modal="true">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h2 id="jsonModalTitle" class="text-lg font-semibold text-gray-900 m-0">JSON do Processo</h2>
            <button type="button" id="jsonModalClose" aria-label="Fechar" class="p-1 text-gray-500 hover:text-gray-700 rounded">×</button>
        </div>
        <div class="p-4 overflow-auto flex-1">
            <pre id="jsonModalContent" class="bg-slate-900 text-sky-100 p-4 rounded-lg text-xs overflow-auto m-0 whitespace-pre-wrap break-words"></pre>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            <button type="button" id="jsonModalBtnFechar" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Fechar</button>
        </div>
    </div>
</div>

<style>
/* Pesquisa: botões dinâmicos + loading + toast (compatível com Tailwind) */
.btn-pesquisa-primary { display: inline-flex; align-items: center; padding: 0.5rem 1rem; background: #4f46e5; color: #fff; font-size: 0.875rem; font-weight: 500; border-radius: 6px; border: none; cursor: pointer; }
.btn-pesquisa-primary:hover { background: #4338ca; }
.btn-pesquisa-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-pesquisa-secondary { display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; background: #fff; color: #374151; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; }
.btn-pesquisa-secondary:hover { background: #f9fafb; }
.btn-pesquisa-secondary:disabled { opacity: 0.6; cursor: not-allowed; }
.loading-pesquisa { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2.5rem 1.5rem; gap: 1rem; }
.loading-spinner { width: 40px; height: 40px; border: 3px solid #e5e7eb; border-top-color: #4f46e5; border-radius: 50%; animation: loading-spin 0.8s linear infinite; }
@keyframes loading-spin { to { transform: rotate(360deg); } }
.save-loading-overlay { position: absolute; inset: 0; background: rgba(255,255,255,0.95); display: none; align-items: center; justify-content: center; flex-direction: column; gap: 0.75rem; z-index: 10; border-radius: 8px; }
.save-loading-overlay.is-visible { display: flex; }
.pesquisa-toast { min-width: 18rem; max-width: 24rem; padding: 1rem 1.25rem; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); display: flex; align-items: flex-start; gap: 0.75rem; pointer-events: auto; }
.pesquisa-toast-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
.pesquisa-toast-error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
.pesquisa-toast-info { background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1; }
#jsonModal.is-open { display: flex !important; }
</style>

<script>
(function () {
    const form = document.getElementById('datajud-form');
    const btn = document.getElementById('btn-pesquisar');
    const resultados = document.getElementById('resultados');
    const url = '<?php echo e(route('datajud.api.search')); ?>';
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '<?php echo e(csrf_token()); ?>';

    const monitoredKey = 'datajud_monitored_v1';
    const monitorIntervals = {};

    function loadMonitored() {
        try { return JSON.parse(localStorage.getItem(monitoredKey) || '{}'); } catch(e) { return {}; }
    }

    function saveMonitored(map) { localStorage.setItem(monitoredKey, JSON.stringify(map)); }

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

    function showToast(a, b, c) {
        var type = c !== undefined ? a : 'info';
        var title = c !== undefined ? b : a;
        var message = c !== undefined ? c : b;
        var container = document.getElementById('toast-container');
        if (!container) return;
        var toast = document.createElement('div');
        toast.className = 'pesquisa-toast pesquisa-toast-' + (type || 'info');
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'polite');
        toast.innerHTML = '<div><strong style="display:block; margin-bottom:0.25rem;">' + (title || '') + '</strong><span>' + (message || '') + '</span></div>';
        container.appendChild(toast);
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.2s';
            setTimeout(function() { toast.remove(); }, 200);
        }, type === 'success' ? 5000 : 4000);
    }

    function renderHits(hits) {
        resultados.innerHTML = '';
        if (!hits || hits.length === 0) {
            resultados.innerHTML = '<div class="rounded-md bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800">Nenhum resultado encontrado.</div>';
            return;
        }

        const container = document.createElement('div');
        container.className = 'space-y-4';

        hits.forEach((hit, idx) => {
            const source = hit._source || {};
            const numero = source.numeroProcesso || source.numero_processo || hit._id || '';
            const tribunal = hit._tribunal || source.tribunal || '';
            const classe = (source.classe && source.classe.nome) ? source.classe.nome : (source.classe || '');
            const assunto = (source.assuntos && source.assuntos.length) ? source.assuntos.map(a=>a.nome).join(', ') : (source.assunto || '');

            const lastMovement = (source.movimentos && source.movimentos.length) ? source.movimentos[source.movimentos.length - 1] : null;
            const lastMovementText = lastMovement ? (lastMovement.nome + (lastMovement.dataHora ? ' — ' + lastMovement.dataHora : '')) : '';
            const updatedAt = source.dataHoraUltimaAtualizacao || source.dataHora || '';

            const card = document.createElement('div');
            card.className = 'rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden relative';
            card.id = 'result-card-' + idx;

            const header = document.createElement('div');
            header.className = 'flex flex-wrap justify-between items-center gap-2 border-b border-gray-200 bg-gray-50 px-4 py-3';

            const title = document.createElement('div');
            title.className = 'font-semibold text-gray-900';
            title.innerHTML = numero + (assunto ? ' <span class="text-gray-600 font-normal">— ' + assunto + '</span>' : '');

            const badge = document.createElement('div');
            badge.className = 'flex flex-wrap items-center gap-2';
            badge.innerHTML = '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">' + tribunal + '</span>' +
                '<button type="button" class="btn-pesquisa-secondary" data-action="refresh">Atualizar</button>' +
                '<button type="button" class="btn-pesquisa-primary" data-action="save" data-role="save-process">Salvar processo</button>' +
                '<button type="button" class="btn-pesquisa-secondary" data-action="monitor">Monitorar</button>';

            header.appendChild(title);
            header.appendChild(badge);

            const body = document.createElement('div');
            body.className = 'p-4';

            const status = mapStatusFromMovement(lastMovement);
            const meta = document.createElement('p');
            meta.className = 'text-sm text-gray-700';
            meta.setAttribute('data-role', 'card-meta');
            meta.innerHTML = '<strong>Classe:</strong> ' + classe + ' &nbsp; <strong>Status:</strong> ' + status + ' &nbsp; <strong>Atualizado:</strong> ' + fmtDate(updatedAt);

            const infoTable = document.createElement('table');
            infoTable.className = 'w-full text-sm my-2 border-collapse';
            const orgao = (source.orgaoJulgador && source.orgaoJulgador.nome) ? source.orgaoJulgador.nome : (source.orgaoJulgador || '');
            const ajuizamento = source.dataAjuizamento || source.dataAjuizamentoFormat || '';
            const sistema = (source.sistema && source.sistema.nome) ? source.sistema.nome : (source.sistema || '');
            const formato = (source.formato && source.formato.nome) ? source.formato.nome : (source.formato || '');
            const sigilo = source.nivelSigilo ?? source.nivel_sigilo ?? (source.nivel ? source.nivel : '');
            const movCount = (source.movimentos || []).length;
            infoTable.innerHTML = '<tbody class="text-gray-700">' +
                '<tr><th class="text-left text-gray-500 font-medium w-1/3 py-1 pr-2">Juízo/Órgão</th><td class="py-1">' + orgao + '</td></tr>' +
                '<tr><th class="text-left text-gray-500 font-medium py-1 pr-2">Data de ajuizamento</th><td class="py-1">' + fmtDate(ajuizamento) + '</td></tr>' +
                '<tr><th class="text-left text-gray-500 font-medium py-1 pr-2">Sistema / Formato</th><td class="py-1">' + sistema + ' / ' + formato + '</td></tr>' +
                '<tr><th class="text-left text-gray-500 font-medium py-1 pr-2">Nível de sigilo</th><td class="py-1">' + sigilo + '</td></tr>' +
                '<tr><th class="text-left text-gray-500 font-medium py-1 pr-2">Movimentações</th><td class="py-1">' + movCount + '</td></tr>' +
                '</tbody>';

            const partiesDiv = document.createElement('div');
            partiesDiv.className = 'mb-2 text-sm text-gray-700';
            const partes = source.partes || [];
            const partiesHtml = partes.map(p => {
                const nomes = (p.advogados || []).map(a => (a.nome || '') + (a.oab ? ' (OAB: '+a.oab+')' : '')).filter(Boolean).join('; ');
                return '<div><strong>Parte:</strong> ' + (p.nome || p.nomeParte || '') + (p.tipoParte ? ' <span class="text-gray-500 text-xs">('+p.tipoParte+')</span>' : '') + (nomes ? ' <div class="text-xs text-gray-500">Advogados: ' + nomes + '</div>' : '') + '</div>';
            }).join('');
            partiesDiv.innerHTML = partiesHtml || '<em>Sem informações de partes</em>';

            const movementDiv = document.createElement('div');
            movementDiv.setAttribute('data-role', 'card-movements');
            movementDiv.className = 'text-sm text-gray-700';
            const recent = (source.movimentos || []).slice(-5).reverse();
            const recentHtml = recent.map(m => '<div><small class="text-gray-500">' + fmtDate(m.dataHora || m.data || '') + '</small> — ' + (m.nome || m.descricao || '') + '</div>').join('');
            movementDiv.innerHTML = '<strong>Últimos movimentos:</strong><div class="mt-1">' + (recentHtml || '<em>Nenhum</em>') + '</div>';

            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'mt-4 flex flex-wrap gap-2';
            const copyBtn = document.createElement('button');
            copyBtn.type = 'button';
            copyBtn.className = 'btn-pesquisa-secondary';
            copyBtn.textContent = 'Copiar número';
            copyBtn.addEventListener('click', () => { navigator.clipboard.writeText(numero); showToast('info', 'Copiado', 'Número do processo copiado para a área de transferência'); });

            const jsonBtn = document.createElement('button');
            jsonBtn.type = 'button';
            jsonBtn.className = 'btn-pesquisa-secondary';
            jsonBtn.textContent = 'Ver JSON';
            jsonBtn.addEventListener('click', () => {
                const pre = document.getElementById('jsonModalContent');
                pre.textContent = JSON.stringify(source, null, 2);
                document.getElementById('jsonModal').classList.add('is-open');
            });

            function runSave(btnEl) {
                if (!btnEl || btnEl.dataset.saved === '1') return;
                var cardEl = btnEl.closest('[id^="result-card-"]');
                var overlay = cardEl ? cardEl.querySelector('.save-loading-overlay') : null;
                var allSaveBtns = cardEl ? cardEl.querySelectorAll('[data-role="save-process"]') : [btnEl];
                var prevText = btnEl.textContent;
                if (overlay) overlay.classList.add('is-visible');
                allSaveBtns.forEach(function(b) { b.disabled = true; });
                fetch('<?php echo e(route('datajud.salvar')); ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ tribunal: tribunal, source: source })
                }).then(function(r) {
                    return r.text().then(function(text) {
                        try { return JSON.parse(text); } catch(e) { return { error: text || 'Resposta inválida' }; }
                    });
                }).then(function(json) {
                    if (json && json.ok) {
                        allSaveBtns.forEach(function(b) {
                            b.textContent = 'Salvo ✓';
                            b.dataset.saved = '1';
                            b.classList.add('opacity-90');
                        });
                        showToast('success', 'Processo salvo', 'O processo foi salvo com sucesso. <a href="<?php echo e(route('datajud.salvos')); ?>" class="underline font-medium">Ver Processos salvos</a>');
                    } else {
                        showToast('error', 'Erro ao salvar', (json && json.error) || 'Erro ao salvar processo.');
                        allSaveBtns.forEach(function(b) { b.textContent = prevText; b.disabled = false; });
                    }
                }).catch(function(err) {
                    console.error('Save error', err);
                    showToast('error', 'Erro ao salvar', 'Tente novamente. Verifique sua conexão.');
                    allSaveBtns.forEach(function(b) { b.textContent = prevText; b.disabled = false; });
                }).finally(function() {
                    if (overlay) overlay.classList.remove('is-visible');
                    if (allSaveBtns[0] && allSaveBtns[0].dataset.saved !== '1') {
                        allSaveBtns.forEach(function(b) { b.disabled = false; });
                    }
                });
            }

            const saveBtnBody = document.createElement('button');
            saveBtnBody.type = 'button';
            saveBtnBody.className = 'btn-pesquisa-primary';
            saveBtnBody.setAttribute('data-role', 'save-process');
            saveBtnBody.textContent = 'Salvar processo';
            saveBtnBody.addEventListener('click', function () { runSave(saveBtnBody); });

            actionsDiv.appendChild(copyBtn);
            actionsDiv.appendChild(jsonBtn);
            actionsDiv.appendChild(saveBtnBody);

            body.appendChild(meta);
            body.appendChild(infoTable);
            body.appendChild(partiesDiv);
            body.appendChild(movementDiv);
            body.appendChild(actionsDiv);

            card.appendChild(header);
            card.appendChild(body);

            var saveOverlay = document.createElement('div');
            saveOverlay.className = 'save-loading-overlay';
            saveOverlay.setAttribute('aria-hidden', 'true');
            saveOverlay.innerHTML = '<div class="loading-spinner" aria-hidden="true"></div><p class="m-0 text-sm text-gray-600">Salvando processo...</p>';
            card.appendChild(saveOverlay);

            container.appendChild(card);

            const refreshBtn = badge.querySelector('[data-action="refresh"]');
            const saveBtn = badge.querySelector('[data-action="save"]');
            const monitorBtn = badge.querySelector('[data-action="monitor"]');

            refreshBtn.addEventListener('click', function () {
                fetchSingleAndUpdate(idx, tribunal, numero, card, true);
            });

            saveBtn.addEventListener('click', function () { runSave(saveBtn); });

            const monitored = loadMonitored();
            const key = tribunal + '::' + numero;
            if (monitored[key]) {
                monitorBtn.textContent = 'Parar';
                card.classList.add('border-emerald-500');
            }

            monitorBtn.addEventListener('click', function () {
                const map = loadMonitored();
                if (map[key]) {
                    clearMonitorInterval(key);
                    delete map[key];
                    saveMonitored(map);
                    monitorBtn.textContent = 'Monitorar';
                    card.classList.remove('border-emerald-500');
                    renderMonitoredList();
                    return;
                }

                map[key] = { tribunal, numero, lastSignature: lastMovementText };
                saveMonitored(map);
                monitorBtn.textContent = 'Parar';
                card.classList.add('border-emerald-500');
                startMonitorFor(key, map[key], card);
                renderMonitoredList();
            });
        });

        resultados.appendChild(container);
    }

    function fetchSingleAndUpdate(idx, tribunal, numero, card, notifyOnChange = false) {
        const payload = { tribunal: tribunal, numero_processo: numero };
        return fetch('<?php echo e(route('datajud.api.search')); ?>', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        }).then(r => r.json())
          .then(json => {
              const hits = (json && json.hits && json.hits.hits) ? json.hits.hits : [];
              if (hits.length === 0) return null;
              const hit = hits[0];
              const src = hit._source || {};
              const lastMovement = (src.movimentos && src.movimentos.length) ? src.movimentos[src.movimentos.length - 1] : null;
              const movementText = lastMovement ? (lastMovement.nome + (lastMovement.dataHora ? ' — ' + lastMovement.dataHora : '')) : '';
              const updatedAt = src.dataHoraUltimaAtualizacao || src.dataHora || '';
              const meta = card.querySelector('[data-role="card-meta"]');
              if (meta) meta.innerHTML = '<strong>Classe:</strong> ' + ((src.classe && src.classe.nome) ? src.classe.nome : (src.classe||'')) + ' &nbsp; <strong>Status:</strong> ' + mapStatusFromMovement(lastMovement) + ' &nbsp; <strong>Atualizado:</strong> ' + fmtDate(updatedAt);
              const md = card.querySelector('[data-role="card-movements"]');
              if (md) md.innerHTML = '<strong>Últimos movimentos:</strong><div class="mt-1">' + ((src.movimentos||[]).slice(-5).reverse().map(m=>'<div><small class="text-gray-500">'+fmtDate(m.dataHora||m.data||'')+'</small> — '+(m.nome||m.descricao||'')+'</div>').join('') || '<em>Nenhum</em>') + '</div>';

              return { hit, src, movementText };
          })
          .catch(err => { console.error('Refresh error', err); return null; });
    }

    function startMonitorFor(key, item, card) {
        if (monitorIntervals[key]) return;
        fetchSingleAndUpdate(null, item.tribunal, item.numero, card, true).then(res => {
            if (res) {
                const map = loadMonitored();
                map[key] = map[key] || item;
                map[key].lastSignature = res.movementText || '';
                saveMonitored(map);
            }
        });

        monitorIntervals[key] = setInterval(() => {
            const map = loadMonitored();
            const current = map[key];
            if (!current) { clearMonitorInterval(key); return; }
            fetchSingleAndUpdate(null, current.tribunal, current.numero, card, true).then(res => {
                if (!res) return;
                const sig = res.movementText || '';
                if (sig !== (current.lastSignature || '')) {
                    current.lastSignature = sig;
                    saveMonitored(map);
                    showToast('info', 'Processo atualizado', current.numero + ' — ' + current.tribunal + ': ' + sig);
                    const meta = card.querySelector('[data-role="card-meta"]');
                    if (meta) meta.innerHTML = '<strong>Classe:</strong> ' + ((res.src.classe && res.src.classe.nome) ? res.src.classe.nome : (res.src.classe||'')) + ' &nbsp; <strong>Status:</strong> ' + mapStatusFromMovement(res.src.movimentos && res.src.movimentos.slice(-1)[0]) + ' &nbsp; <strong>Atualizado:</strong> ' + fmtDate(res.src.dataHoraUltimaAtualizacao || res.src.dataHora || '');
                    const md = card.querySelector('[data-role="card-movements"]');
                    if (md) md.innerHTML = '<strong>Últimos movimentos:</strong><div class="mt-1">' + ((res.src.movimentos||[]).slice(-5).reverse().map(m=>'<div><small class="text-gray-500">'+fmtDate(m.dataHora||m.data||'')+'</small> — '+(m.nome||m.descricao||'')+'</div>').join('') || '<em>Nenhum</em>') + '</div>';
                }
            });
        }, 15000);
    }

    function clearMonitorInterval(key) {
        if (monitorIntervals[key]) {
            clearInterval(monitorIntervals[key]);
            delete monitorIntervals[key];
        }
    }

    function renderMonitoredList() {
        const list = document.getElementById('monitored-list');
        const map = loadMonitored();
        list.innerHTML = '';
        Object.keys(map).forEach(k => {
            const it = map[k];
            const item = document.createElement('div');
            item.className = 'flex items-center justify-between gap-2 py-3 px-4';
            item.innerHTML = '<div><strong class="text-gray-900 text-sm">' + it.numero + '</strong><div class="text-xs text-gray-500">' + it.tribunal + '</div></div>';
            const stopBtn = document.createElement('button');
            stopBtn.className = 'inline-flex items-center px-2 py-1 text-xs font-medium text-red-700 border border-red-200 rounded-md hover:bg-red-50';
            stopBtn.textContent = 'Parar';
            stopBtn.addEventListener('click', () => {
                const m = loadMonitored(); delete m[k]; saveMonitored(m); clearMonitorInterval(k); renderMonitoredList();
            });
            item.appendChild(stopBtn);
            list.appendChild(item);
        });
    }

    function startAllMonitors() {
        const map = loadMonitored();
        Object.keys(map).forEach(k => {
            const it = map[k];
            const card = Array.from(document.querySelectorAll('[id^="result-card-"]')).find(c => c.querySelector('.font-semibold') && c.querySelector('.font-semibold').textContent.includes(it.numero));
            startMonitorFor(k, it, card || document.createElement('div'));
        });
        renderMonitoredList();
    }

    startAllMonitors();

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const tipoEl = document.getElementById('tipo_consulta');
        const tipo = tipoEl ? tipoEl.value : 'numero';
        const numEl = document.getElementById('numero_processo');
        const numVal = (numEl && numEl.value ? numEl.value : '').trim();
        const advEl = document.getElementById('nome_advogado');
        const advVal = (advEl && advEl.value ? advEl.value : '').trim();
        if (tipo === 'numero' && !numVal) {
            showToast('info', 'Campo obrigatório', 'Informe o número do processo.');
            return;
        }
        if (tipo === 'advogado' && !advVal) {
            showToast('info', 'Campo obrigatório', 'Informe o nome do advogado.');
            return;
        }

        btn.disabled = true;
        resultados.innerHTML = '<div class="loading-pesquisa" role="status" aria-live="polite">' +
            '<div class="loading-spinner" aria-hidden="true"></div>' +
            '<p class="m-0 text-sm text-gray-600">Buscando processo no DataJud...</p>' +
            '<p class="m-0 text-xs text-gray-500">Isso pode levar alguns segundos.</p>' +
            '</div>';

        const data = { tribunal: document.getElementById('tribunal').value };
        if (tipo === 'numero') {
            data.numero_processo = numVal;
        } else {
            data.nome_advogado = advVal;
        }

        fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        }).then(r => r.json())
          .then(json => {
              if (json && json.error) {
                  resultados.innerHTML = '<div class="rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">' + (json.error || 'Erro ao consultar o DataJud.') + '</div>';
                  return;
              }
              const hits = (json && json.hits && json.hits.hits) ? json.hits.hits : [];
              renderHits(hits);
          })
          .catch(err => {
              resultados.innerHTML = '<div class="rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">Erro ao consultar o DataJud. Verifique a conexão e tente novamente.</div>';
              console.error(err);
          })
          .finally(() => btn.disabled = false);
    });

    const tipoSelect = document.getElementById('tipo_consulta');
    function toggleFields() {
        if (!tipoSelect) return;
        const tipo = tipoSelect.value;
        const numEl = document.getElementById('numero_processo');
        const advEl = document.getElementById('nome_advogado');
        const numDiv = numEl ? numEl.closest('div') : null;
        const advDiv = advEl ? advEl.closest('div') : null;
        if (tipo === 'numero') {
            if (numDiv) numDiv.style.display = '';
            if (advDiv) advDiv.style.display = 'none';
        } else {
            if (numDiv) numDiv.style.display = 'none';
            if (advDiv) advDiv.style.display = '';
        }
    }

    if (tipoSelect) {
        tipoSelect.addEventListener('change', toggleFields);
        toggleFields();
    }

    (function() {
        var modal = document.getElementById('jsonModal');
        var btnFechar = document.getElementById('jsonModalBtnFechar');
        var btnClose = document.getElementById('jsonModalClose');
        function closeJsonModal() { if (modal) modal.classList.remove('is-open'); }
        if (btnFechar) btnFechar.addEventListener('click', closeJsonModal);
        if (btnClose) btnClose.addEventListener('click', closeJsonModal);
        if (modal) modal.addEventListener('click', function(e) { if (e.target === modal) closeJsonModal(); });
    })();
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/snews/projects/juristack/resources/views/datajud/pesquisa.blade.php ENDPATH**/ ?>