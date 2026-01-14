<?php $__env->startSection('content'); ?>
<div class="container">
    <style>
        /* DataJud UI enhancements */
        .datajud-hero { margin-bottom: 1rem; display:flex; align-items:center; gap:1rem }
        .datajud-hero h3 { margin:0; font-weight:700; color:#0b5ed7 }
        .card.datajud-card { box-shadow: 0 6px 18px rgba(15,23,42,0.06); border-radius:8px; overflow:hidden }
        .card.datajud-card .card-header { background: linear-gradient(90deg,#f8fafc,#ffffff); border-bottom:1px solid rgba(0,0,0,0.05); font-weight:600 }
        .card.datajud-card .card-body { background:#fff }
        .badge-tribunal { background:#eef2ff; color:#2a2a72; font-weight:600 }
        #resultados .card { transition: transform .12s ease, box-shadow .12s ease }
        #resultados .card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(15,23,42,0.08) }
        #monitored-list .list-group-item { display:flex; align-items:center; justify-content:space-between }
        #monitored-list .small { display:block }
        .toast { border-radius:8px }
        .table-sm th { width:30% }
        .btn-monitor-active { background:#198754; color:#fff; border-color:#198754 }
        .card .card-text strong { color:#444 }
        pre#jsonModalContent { background:#0b1220; color:#dbeafe; padding:1rem; border-radius:6px }
        @media (max-width:767px) {
            #monitored-list { margin-top:1rem }
        }
    </style>
    <div class="datajud-hero">
        <h3>Pesquisa de Processos - DataJud</h3>
        <div class="text-muted">Busque por número, advogado ou monitore processos em tempo real</div>
    </div>

    <form id="datajud-form" method="POST" action="#">
        <?php echo csrf_field(); ?>
        
        
        <div class="mb-3">
            <label for="tribunal" class="form-label">
                Tribunal <span class="text-danger">*</span>
            </label>
            <select name="tribunal" id="tribunal" class="form-select" required>
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
                    <option value="TJG" disabled>-- (use TJGO abaixo) --</option>
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
                    <option value="TJToc">TJTO - Tocantins</option>
                </optgroup>

            </select>
        </div>

        
        <div class="mb-3">
            <label for="tipo_consulta" class="form-label">Tipo de Consulta</label>
            <select id="tipo_consulta" class="form-select">
                <option value="numero">Número do Processo</option>
                <option value="advogado">Nome do Advogado</option>
            </select>
        </div>

        
        <div class="mb-3">
            <label for="numero_processo" class="form-label">
                Número do Processo
            </label>
            <input
                type="text"
                name="numero_processo"
                id="numero_processo"
                class="form-control"
                placeholder="Ex: 0001234-56.2023.8.26.0000"
                value="<?php echo e(request('numero_processo')); ?>"
            >
        </div>

        
        <div class="mb-3">
            <label for="nome_advogado" class="form-label">
                Nome do Advogado
            </label>
            <input
                type="text"
                name="nome_advogado"
                id="nome_advogado"
                class="form-control"
                placeholder="Ex: João da Silva"
                value="<?php echo e(request('nome_advogado')); ?>"
            >
        </div>

        <div class="alert alert-info">
            Preencha <strong>o número do processo</strong> ou <strong>o nome do advogado</strong>.
        </div>

        <button type="submit" id="btn-pesquisar" class="btn btn-primary">
            Pesquisar
        </button>
    </form>

    <div class="row">
        <div class="col-md-8">
            <div id="resultados" class="mt-4"></div>
        </div>
        <div class="col-md-4">
            <h5 class="mt-4">Monitorados</h5>
            <div id="monitored-list" class="list-group"></div>
        </div>
    </div>

    <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:11000"></div>
        <!-- JSON modal -->
        <div class="modal fade" id="jsonModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">JSON do Processo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"><pre id="jsonModalContent" style="white-space:pre-wrap;word-break:break-word"></pre></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

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
                if (name.includes('petição') || name.includes('peticao') || name.includes('petição')) return 'Petição';
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
                const bsToast = new bootstrap.Toast(toast, { delay: 8000 });
                bsToast.show();
            }

            function renderHits(hits) {
                resultados.innerHTML = '';
                if (!hits || hits.length === 0) {
                    resultados.innerHTML = '<div class="alert alert-warning">Nenhum resultado encontrado.</div>';
                    return;
                }

                const container = document.createElement('div');

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
                    card.className = 'card mb-3';
                    card.id = 'result-card-' + idx;

                    const header = document.createElement('div');
                    header.className = 'card-header d-flex justify-content-between align-items-center';

                    const title = document.createElement('div');
                    title.innerHTML = '<strong>' + numero + '</strong>' + (assunto ? ' &mdash; ' + assunto : '');

                    const badge = document.createElement('div');
                    badge.innerHTML = '<span class="badge bg-secondary me-2">' + tribunal + '</span>' +
                        '<button class="btn btn-sm btn-outline-primary me-1" data-action="refresh">Atualizar</button>' +
                        '<button class="btn btn-sm btn-success" data-action="monitor">Monitorar</button>';

                    header.appendChild(title);
                    header.appendChild(badge);

                    const body = document.createElement('div');
                    body.className = 'card-body';

                    const status = mapStatusFromMovement(lastMovement);
                    const meta = document.createElement('p');
                    meta.className = 'card-text';
                    meta.innerHTML = '<strong>Classe:</strong> ' + classe + ' &nbsp; <strong>Status:</strong> ' + status + ' &nbsp; <strong>Atualizado:</strong> ' + fmtDate(updatedAt);

                    // additional info table
                    const infoTable = document.createElement('table');
                    infoTable.className = 'table table-sm mt-2 mb-2';
                    const orgao = (source.orgaoJulgador && source.orgaoJulgador.nome) ? source.orgaoJulgador.nome : (source.orgaoJulgador || '');
                    const ajuizamento = source.dataAjuizamento || source.dataAjuizamentoFormat || '';
                    const sistema = (source.sistema && source.sistema.nome) ? source.sistema.nome : (source.sistema || '');
                    const formato = (source.formato && source.formato.nome) ? source.formato.nome : (source.formato || '');
                    const sigilo = source.nivelSigilo ?? source.nivel_sigilo ?? (source.nivel ? source.nivel : '');
                    const movCount = (source.movimentos || []).length;
                    infoTable.innerHTML = `<tbody>
                        <tr><th class="w-25">Juízo/Órgão</th><td>${orgao}</td></tr>
                        <tr><th>Data de ajuizamento</th><td>${fmtDate(ajuizamento)}</td></tr>
                        <tr><th>Sistema / Formato</th><td>${sistema} / ${formato}</td></tr>
                        <tr><th>Nível de sigilo</th><td>${sigilo}</td></tr>
                        <tr><th>Movimentações</th><td>${movCount}</td></tr>
                    </tbody>`;

                    const partiesDiv = document.createElement('div');
                    partiesDiv.className = 'mb-2';
                    const partes = source.partes || [];
                    const partiesHtml = partes.map(p => {
                        const nomes = (p.advogados || []).map(a => (a.nome || '') + (a.oab ? ' (OAB: '+a.oab+')' : '')).filter(Boolean).join('; ');
                        return '<div><strong>Parte:</strong> ' + (p.nome || p.nomeParte || '') + (p.tipoParte ? ' <small class="text-muted">('+p.tipoParte+')</small>' : '') + (nomes ? ' <div class="small text-muted">Advogados: ' + nomes + '</div>' : '') + '</div>';
                    }).join('');
                    partiesDiv.innerHTML = partiesHtml || '<em>Sem informações de partes</em>';

                    const movementDiv = document.createElement('div');
                    const recent = (source.movimentos || []).slice(-5).reverse();
                    const recentHtml = recent.map(m => '<div><small class="text-muted">' + fmtDate(m.dataHora || m.data || '') + '</small> — ' + (m.nome || m.descricao || '') + '</div>').join('');
                    movementDiv.innerHTML = '<strong>Últimos movimentos:</strong><div>' + (recentHtml || '<em>Nenhum</em>') + '</div>';

                    const actionsDiv = document.createElement('div');
                    actionsDiv.className = 'mt-2';
                    const copyBtn = document.createElement('button');
                    copyBtn.className = 'btn btn-sm btn-outline-secondary me-2';
                    copyBtn.textContent = 'Copiar número';
                    copyBtn.addEventListener('click', () => { navigator.clipboard.writeText(numero); showToast('Copiado', 'Número do processo copiado para a área de transferência'); });

                    const jsonBtn = document.createElement('button');
                    jsonBtn.className = 'btn btn-sm btn-outline-info me-2';
                    jsonBtn.textContent = 'Ver JSON';
                    jsonBtn.addEventListener('click', () => {
                        const pre = document.getElementById('jsonModalContent');
                        pre.textContent = JSON.stringify(source, null, 2);
                        const modal = new bootstrap.Modal(document.getElementById('jsonModal'));
                        modal.show();
                    });

                    actionsDiv.appendChild(copyBtn);
                    actionsDiv.appendChild(jsonBtn);

                    body.appendChild(meta);
                    body.appendChild(infoTable);
                    body.appendChild(partiesDiv);
                    body.appendChild(movementDiv);
                    body.appendChild(actionsDiv);

                    card.appendChild(header);
                    card.appendChild(body);

                    container.appendChild(card);

                    // attach behavior
                    const refreshBtn = badge.querySelector('[data-action="refresh"]');
                    const monitorBtn = badge.querySelector('[data-action="monitor"]');

                    refreshBtn.addEventListener('click', function () {
                        fetchSingleAndUpdate(idx, tribunal, numero, card, true);
                    });

                    // monitor button toggles persistent monitoring
                    const monitored = loadMonitored();
                    const key = tribunal + '::' + numero;
                    if (monitored[key]) {
                        monitorBtn.textContent = 'Parar';
                        card.classList.add('border-success');
                    }

                    monitorBtn.addEventListener('click', function () {
                        const map = loadMonitored();
                        if (map[key]) {
                            // stop
                            clearMonitorInterval(key);
                            delete map[key];
                            saveMonitored(map);
                            monitorBtn.textContent = 'Monitorar';
                            card.classList.remove('border-success');
                            renderMonitoredList();
                            return;
                        }

                        map[key] = { tribunal, numero, lastSignature: lastMovementText };
                        saveMonitored(map);
                        monitorBtn.textContent = 'Parar';
                        card.classList.add('border-success');
                        startMonitorFor(key, map[key], card);
                        renderMonitoredList();
                    });
                });

                resultados.appendChild(container);
            }

            function fetchSingleAndUpdate(idx, tribunal, numero, card, notifyOnChange = false) {
                const payload = { tribunal: tribunal, numero_processo: numero };
                return fetch('<?php echo e(url('/api/datajud/search')); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
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
                      const meta = card.querySelector('.card-text');
                      if (meta) meta.innerHTML = '<strong>Classe:</strong> ' + ((src.classe && src.classe.nome) ? src.classe.nome : (src.classe||'')) + ' &nbsp; <strong>Status:</strong> ' + mapStatusFromMovement(lastMovement) + ' &nbsp; <strong>Atualizado:</strong> ' + fmtDate(updatedAt);
                      const md = card.querySelectorAll('.card-body > div')[1];
                      if (md) md.innerHTML = '<strong>Últimos movimentos:</strong><div>' + ((src.movimentos||[]).slice(-5).reverse().map(m=>'<div><small class="text-muted">'+fmtDate(m.dataHora||m.data||'')+'</small> — '+(m.nome||m.descricao||'')+'</div>').join('') || '<em>Nenhum</em>') + '</div>';

                      return { hit, src, movementText };
                  })
                  .catch(err => { console.error('Refresh error', err); return null; });
            }

            function startMonitorFor(key, item, card) {
                // avoid duplicate intervals
                if (monitorIntervals[key]) return;
                // immediate run
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
                            // changed
                            current.lastSignature = sig;
                            saveMonitored(map);
                            showToast('Processo atualizado', `${current.numero} — ${current.tribunal}: ${sig}`);
                            // update card UI
                            const meta = card.querySelector('.card-text');
                            if (meta) meta.innerHTML = '<strong>Classe:</strong> ' + ((res.src.classe && res.src.classe.nome) ? res.src.classe.nome : (res.src.classe||'')) + ' &nbsp; <strong>Status:</strong> ' + mapStatusFromMovement(res.src.movimentos && res.src.movimentos.slice(-1)[0]) + ' &nbsp; <strong>Atualizado:</strong> ' + fmtDate(res.src.dataHoraUltimaAtualizacao || res.src.dataHora || '');
                            const md = card.querySelectorAll('.card-body > div')[1];
                            if (md) md.innerHTML = '<strong>Últimos movimentos:</strong><div>' + ((res.src.movimentos||[]).slice(-5).reverse().map(m=>'<div><small class="text-muted">'+fmtDate(m.dataHora||m.data||'')+'</small> — '+(m.nome||m.descricao||'')+'</div>').join('') || '<em>Nenhum</em>') + '</div>';
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
                    item.className = 'list-group-item d-flex justify-content-between align-items-center';
                    item.innerHTML = `<div><strong>${it.numero}</strong><div class="small text-muted">${it.tribunal}</div></div>`;
                    const stopBtn = document.createElement('button');
                    stopBtn.className = 'btn btn-sm btn-outline-danger';
                    stopBtn.textContent = 'Parar';
                    stopBtn.addEventListener('click', () => {
                        const m = loadMonitored(); delete m[k]; saveMonitored(m); clearMonitorInterval(k); renderMonitoredList();
                        // also update buttons in cards
                        document.querySelectorAll('[data-action="monitor"]').forEach(b => {
                            const parent = b.closest('.card');
                            if (!parent) return;
                        });
                    });
                    item.appendChild(stopBtn);
                    list.appendChild(item);
                });
            }

            function startAllMonitors() {
                const map = loadMonitored();
                // find cards for each monitored item; if not present, create hidden polls
                Object.keys(map).forEach(k => {
                    const it = map[k];
                    // try to find existing card by matching number
                    const card = Array.from(document.querySelectorAll('.card')).find(c => c.querySelector('strong') && c.querySelector('strong').textContent.includes(it.numero));
                    startMonitorFor(k, it, card || document.createElement('div'));
                });
                renderMonitoredList();
            }

            // initialize monitors from storage
            startAllMonitors();

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                btn.disabled = true;
                resultados.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>';

                    const tipo = document.getElementById('tipo_consulta') ? document.getElementById('tipo_consulta').value : 'numero';
                    const data = { tribunal: document.getElementById('tribunal').value };

                    if (tipo === 'numero') {
                        data.numero_processo = document.getElementById('numero_processo').value;
                    } else if (tipo === 'advogado') {
                        data.nome_advogado = document.getElementById('nome_advogado').value;
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
                      const hits = (json && json.hits && json.hits.hits) ? json.hits.hits : [];
                      renderHits(hits);
                  })
                  .catch(err => {
                      resultados.innerHTML = '<div class="alert alert-danger">Erro ao consultar o DataJud.</div>';
                      console.error(err);
                  })
                  .finally(() => btn.disabled = false);
            });

            // Toggle visibility based on tipo_consulta
            const tipoSelect = document.getElementById('tipo_consulta');
            function toggleFields() {
                const tipo = tipoSelect.value;
                const numDiv = document.getElementById('numero_processo').closest('.mb-3');
                const advDiv = document.getElementById('nome_advogado').closest('.mb-3');
                if (tipo === 'numero') {
                    numDiv.style.display = '';
                    advDiv.style.display = 'none';
                } else {
                    numDiv.style.display = 'none';
                    advDiv.style.display = '';
                }
            }

            if (tipoSelect) {
                tipoSelect.addEventListener('change', toggleFields);
                toggleFields();
            }
        })();
    </script>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/juristack/resources/views/datajud/pesquisa.blade.php ENDPATH**/ ?>