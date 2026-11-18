<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>FlowIT — Gestão de Comodatos & Assinatura Digital</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
:root{
  --bg:#f8fafc;--surface:#ffffff;--surface2:#f1f5f9;--line:#e2e8f0;
  --text:#0f172a;--muted:#64748b;--brand:#006aa6;--brand-soft:#e0f2fe;
  --ok:#10b981;--warn:#f59e0b;--bad:#ef4444;--shadow:0 4px 20px -2px rgba(0,0,0,0.06);
}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font:14px/1.5 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif}
header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 28px;background:var(--surface);border-bottom:1px solid var(--line);flex-wrap:wrap}
.brand{display:flex;align-items:center;gap:12px}
.brand img{height:36px}
.brand h1{font-size:18px;margin:0;font-weight:700}
.brand .sub{font-size:12px;color:var(--muted)}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all .2s ease}
.btn-primary{background:var(--brand);color:#fff}
.btn-primary:hover{background:#004e7c}
.btn-outline{background:#fff;border-color:var(--line);color:var(--text)}
.btn-outline:hover{background:var(--surface2)}
.btn-sm{padding:5px 10px;font-size:12px}
.btn-success{background:#16a34a;color:#fff}
.btn-success:hover{background:#15803d}

main{padding:24px 28px;max-width:1440px;margin:auto;display:grid;gap:20px}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px}
.stat-card{background:var(--surface);border:1px solid var(--line);border-radius:12px;padding:18px;box-shadow:var(--shadow)}
.stat-card .label{font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.03em}
.stat-card .val{font-size:26px;font-weight:800;color:var(--brand);margin-top:4px}
.stat-card .sub{font-size:12px;color:var(--muted);margin-top:4px}

.card{background:var(--surface);border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow);overflow:hidden}
.card-header{padding:16px 20px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;background:#fafbfc}
.card-title{font-size:15px;font-weight:700;margin:0}

table{width:100%;border-collapse:collapse;font-size:13.5px}
th{text-align:left;padding:12px 18px;font-size:11.5px;font-weight:700;color:var(--muted);text-transform:uppercase;background:#f8fafc;border-bottom:1px solid var(--line)}
td{padding:14px 18px;border-bottom:1px solid var(--line);vertical-align:middle}
tr:hover td{background:#f8fafc}
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:99px;font-size:11px;font-weight:700;text-transform:uppercase}
.badge-active{background:#dcfce7;color:#15803d}
.badge-ended{background:#f1f5f9;color:#64748b}
.badge-signed{background:#dcfce7;color:#16a34a;border:1px solid #86efac}
.badge-pending{background:#fef3c7;color:#b45309;border:1px solid #fde68a}

/* Modal */
.modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);display:none;place-items:center;z-index:9999;padding:20px}
.modal-box{background:#fff;border-radius:16px;max-width:720px;width:100%;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);overflow:hidden;animation:slideDown .25s ease-out}
@keyframes slideDown{from{opacity:0;transform:translateY(-15px)}to{opacity:1;transform:translateY(0)}}
.modal-head{padding:18px 24px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;background:#fafbfc}
.modal-head h3{margin:0;font-size:16px;font-weight:700;color:#0f172a}
.modal-close{background:none;border:none;font-size:22px;cursor:pointer;color:var(--muted)}
.modal-body{padding:22px 24px;display:grid;gap:15px;max-height:calc(88vh - 120px);overflow-y:auto}
.form-group{display:grid;gap:6px}
.form-group label{font-size:11.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.03em}
.form-control{padding:10px 12px;border:1.5px solid var(--line);border-radius:8px;font-size:13.5px;background:#f8fafc;color:#0f172a;transition:all .2s ease}
.form-control:focus{border-color:var(--brand);background:#fff;outline:none;box-shadow:0 0 0 3px rgba(0,106,166,0.12)}
.form-control[readonly]{background:#f1f5f9;cursor:not-allowed;color:#475569}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.modal-foot{padding:16px 24px;border-top:1px solid var(--line);background:#fafbfc;display:flex;justify-content:flex-end;gap:10px}

/* Modal de Sucesso com Link de Assinatura */
.success-banner{
  background:#f0fdf4;
  border:1.5px solid #86efac;
  border-radius:12px;
  padding:20px;
  text-align:center;
}
.success-banner i{font-size:44px;color:#16a34a;}
.link-copy-box{
  display:flex;
  gap:8px;
  background:#fff;
  border:1.5px solid var(--line);
  border-radius:8px;
  padding:4px 6px;
  margin-top:12px;
}
.link-copy-input{
  flex:1;
  border:none;
  padding:8px 10px;
  font-size:13px;
  outline:none;
  background:transparent;
  color:#0f172a;
}
</style>
</head>
<body>

<header>
  <div class="brand">
    <img src="../public/logo-flowit.png" alt="FlowIT">
    <div>
      <h1>Gestão de Comodatos & Assinatura Digital</h1>
      <div class="sub">Emissão oficial Fazenda Progresso e acompanhamento em tempo real</div>
    </div>
  </div>
  <div style="display:flex;gap:10px;">
    <button class="btn btn-outline" onclick="loadComodatos()">
      <i class="ti ti-refresh"></i> Atualizar
    </button>
    <button class="btn btn-primary" onclick="openModal()">
      <i class="ti ti-file-plus"></i> Novo Termo de Comodato
    </button>
  </div>
</header>

<main>
  <div class="stats-grid">
    <div class="stat-card">
      <div class="label">Termos Ativos</div>
      <div class="val" id="stat-active">0</div>
      <div class="sub">Equipamentos sob custódia</div>
    </div>
    <div class="stat-card">
      <div class="label">Assinados Digitalmente</div>
      <div class="val" id="stat-signed" style="color:#16a34a;">0</div>
      <div class="sub">Validados pelo colaborador</div>
    </div>
    <div class="stat-card">
      <div class="label">Aguardando Assinatura</div>
      <div class="val" id="stat-pending" style="color:#b45309;">0</div>
      <div class="sub">Links de assinatura enviados</div>
    </div>
    <div class="stat-card">
      <div class="label">Total Formalizado</div>
      <div class="val" id="stat-total">0</div>
      <div class="sub">Histórico documental</div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Equipamentos Atribuídos & Status de Assinatura</h2>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Colaborador / CPF</th>
            <th>Equipamento & Modelo</th>
            <th>Serial / IMEI</th>
            <th>Data Atribuição</th>
            <th>Assinatura Digital</th>
            <th>Status</th>
            <th style="text-align:center;">Ações & Link</th>
          </tr>
        </thead>
        <tbody id="comodato-rows">
          <tr><td colspan="9" style="text-align:center;padding:30px;color:var(--muted);">Carregando dados...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- MODAL NOVO COMODATO -->
<div class="modal-overlay" id="newModal">
  <div class="modal-box">
    <div class="modal-head">
      <h3>Novo Termo de Comodato & Responsabilidade</h3>
      <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <form id="comodatoForm" onsubmit="saveComodato(event)">
      <input type="hidden" id="itemtype" name="itemtype" value="Computer">
      <input type="hidden" id="items_id" name="items_id" value="0">
      <input type="hidden" id="contract_type" name="contract_type" value="notebook">

      <div class="modal-body">
        <!-- SELEÇÃO DIRETA DO ATIVO DO INVENTÁRIO -->
        <div class="grid-2">
          <div class="form-group">
            <label>Equipamento do Inventário (Cadastro do Ativo)</label>
            <select class="form-control" id="asset_select" onchange="onAssetSelectChange()" required>
              <option value="">-- Selecione o Ativo Cadastrado --</option>
            </select>
          </div>
          <div class="form-group">
            <label>Data de Atribuição</label>
            <input type="date" class="form-control" id="assigned_date" name="assigned_date" required>
          </div>
        </div>

        <!-- DADOS AUTO-PREENCHIDOS DO ATIVO -->
        <div class="grid-2">
          <div class="form-group">
            <label>Tipo de Equipamento (Do Cadastro)</label>
            <input type="text" class="form-control" id="display_device_type" readonly value="Selecione um ativo acima...">
          </div>
          <div class="form-group">
            <label>Modelo do Fabricante (Do Cadastro)</label>
            <input type="text" class="form-control" id="asset_model" name="asset_model" placeholder="Ex: P112F ou SM-A266M/DS" required>
          </div>
        </div>

        <div class="grid-2">
          <div class="form-group">
            <label>Descrição / Nome do Ativo</label>
            <input type="text" class="form-control" id="asset_name" name="asset_name" placeholder="Nome do ativo no GLPI" required>
          </div>
          <div class="form-group">
            <label>Nº de Série / Patrimônio / IMEI (Do Cadastro)</label>
            <input type="text" class="form-control" id="asset_serial" name="asset_serial" placeholder="Serial ou IMEI" required>
          </div>
        </div>

        <!-- SELEÇÃO DO USUÁRIO / COLABORADOR -->
        <div class="grid-2">
          <div class="form-group">
            <label>Colaborador (Usuário GLPI/AD)</label>
            <select class="form-control" id="users_id" name="users_id" onchange="fillUserData()">
              <option value="0">-- Selecione o Colaborador --</option>
            </select>
          </div>
          <div class="form-group">
            <label>CPF do Colaborador</label>
            <input type="text" class="form-control" id="user_cpf" name="user_cpf" placeholder="000.000.000-00" required>
          </div>
        </div>

        <div class="form-group">
          <label>Nome Completo do Colaborador (Para o Termo)</label>
          <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Ex: Carlos Eduardo Silva" required>
        </div>

        <!-- ACESSÓRIOS DISCRIMINADOS (MANTIDO CONFORME MODELO OFICIAL) -->
        <div class="form-group">
          <label>Materiais Entregues / Acessórios (Discriminado)</label>
          <textarea class="form-control" id="accessories" name="accessories" rows="4"></textarea>
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn btn-outline" onclick="closeModal()">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnSubmitModal">
          <i class="ti ti-check"></i> Gerar Termo & Criar Link de Assinatura
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL DE SUCESSO / LINK DE ASSINATURA GERADO -->
<div class="modal-overlay" id="linkSuccessModal">
  <div class="modal-box" style="max-width:560px;">
    <div class="modal-head">
      <h3>Termo Criado & Link Disponível</h3>
      <button class="modal-close" onclick="closeLinkModal()">&times;</button>
    </div>
    <div class="modal-body">
      <div class="success-banner">
        <i class="ti ti-circle-check"></i>
        <h4 style="margin:8px 0 4px 0;font-size:16px;color:#15803d;">Contrato Vinculado com Sucesso!</h4>
        <p style="margin:0;font-size:13px;color:#166534;">
          Envie o link abaixo para o colaborador assinar diretamente pelo celular ou computador.
        </p>
      </div>

      <div class="form-group" style="margin-top:10px;">
        <label>Link de Assinatura Digital do Colaborador</label>
        <div class="link-copy-box">
          <input type="text" class="link-copy-input" id="generatedSignLink" readonly>
          <button class="btn btn-primary btn-sm" onclick="copySignLink()">
            <i class="ti ti-copy"></i> Copiar Link
          </button>
        </div>
      </div>

      <div style="display:flex;gap:10px;justify-content:center;margin-top:10px;">
        <a id="btnOpenSignDirect" href="#" target="_blank" class="btn btn-success" style="flex:1;justify-content:center;">
          <i class="ti ti-signature"></i> Abrir Tela de Assinatura
        </a>
        <a id="btnOpenDocDirect" href="#" target="_blank" class="btn btn-outline" style="flex:1;justify-content:center;">
          <i class="ti ti-file-text"></i> Ver Termo Oficial
        </a>
      </div>
    </div>
    <div class="modal-foot">
      <button type="button" class="btn btn-outline" onclick="closeLinkModal()">Fechar</button>
    </div>
  </div>
</div>

<script>
let glpiOptions = { assets: [], computers: [], phones: [], users: [] };

document.getElementById('assigned_date').value = new Date().toISOString().split('T')[0];

function openModal() {
  document.getElementById('newModal').style.display = 'grid';
  if (glpiOptions.assets.length > 0 && !document.getElementById('asset_select').value) {
    document.getElementById('asset_select').selectedIndex = 1;
    onAssetSelectChange();
  }
}

function closeModal() {
  document.getElementById('newModal').style.display = 'none';
}

function closeLinkModal() {
  document.getElementById('linkSuccessModal').style.display = 'none';
}

function onAssetSelectChange() {
  const sel = document.getElementById('asset_select');
  const selectedKey = sel.value;
  if (!selectedKey) return;

  const [itemtype, idStr] = selectedKey.split(':');
  const id = parseInt(idStr);

  const asset = glpiOptions.assets.find(a => a.itemtype === itemtype && a.id === id);
  if (!asset) return;

  document.getElementById('itemtype').value = asset.itemtype;
  document.getElementById('items_id').value = asset.id;
  document.getElementById('contract_type').value = asset.contract_type;

  document.getElementById('display_device_type').value = asset.contract_type === 'notebook' 
    ? `Notebook (${asset.type_name || 'Computador'})` 
    : `Smartphone (${asset.type_name || 'Telefone'})`;
  
  document.getElementById('asset_name').value = asset.description || asset.name;
  document.getElementById('asset_model').value = asset.model || (asset.contract_type === 'notebook' ? 'P112F' : 'SM-A266M/DS');
  document.getElementById('asset_serial').value = asset.serial || asset.patrimonio || 'NÃO INFORMADO';

  if (asset.users_id > 0) {
    const userSel = document.getElementById('users_id');
    userSel.value = asset.users_id;
    fillUserData();
  }

  const accField = document.getElementById('accessories');
  if (asset.contract_type === 'notebook') {
    accField.value = "01 Adaptador CA/CC\nMODELO: LA65NS2-01\nINPUT: 110V-240V ~ 1.6A 50-60 Hz\nOUTPUT: 19,5V==3.34A";
  } else {
    accField.value = "01 Carregador\nModelo: MC – 207L\nINPUT: 100V-240V ~ 0.3A 50-60 Hz\nOUTPUT: 5V==2.0A 15.0W\n\n01 Cabo de Sincronização USB – A / USB – C\n01 Carregador de Parede\n01 Chip TIM e chave removedora de chip";
  }
}

function fillUserData() {
  const sel = document.getElementById('users_id');
  const userId = parseInt(sel.value);
  const user = glpiOptions.users.find(u => u.id === userId);
  if (user) {
    document.getElementById('user_name').value = user.name;
  }
}

async function loadOptions() {
  try {
    const res = await fetch('comodato_api.php?action=get_options');
    const json = await res.json();
    if (json.success) {
      glpiOptions = json;
      
      const assetSel = document.getElementById('asset_select');
      assetSel.innerHTML = '<option value="">-- Selecione o Ativo Cadastrado --</option>';
      
      if (json.assets && json.assets.length) {
        json.assets.forEach(a => {
          const opt = document.createElement('option');
          opt.value = `${a.itemtype}:${a.id}`;
          const typeIcon = a.contract_type === 'notebook' ? '💻' : '📱';
          opt.textContent = `${typeIcon} ${a.name} [Serial: ${a.serial}] - Mod: ${a.model}`;
          assetSel.appendChild(opt);
        });
      }

      const userSel = document.getElementById('users_id');
      userSel.innerHTML = '<option value="0">-- Selecione o Colaborador --</option>';
      if (json.users && json.users.length) {
        json.users.forEach(u => {
          const opt = document.createElement('option');
          opt.value = u.id;
          opt.textContent = `${u.name} (${u.login})`;
          userSel.appendChild(opt);
        });
      }
    }
  } catch (e) {
    console.error('Erro ao carregar opções GLPI', e);
  }
}

async function loadComodatos() {
  try {
    const res = await fetch('comodato_api.php?action=list');
    const json = await res.json();
    const rowsEl = document.getElementById('comodato-rows');
    
    if (!json.success || !json.data.length) {
      rowsEl.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px;color:var(--muted);">Nenhum contrato de comodato emitido até o momento. Clique em "Novo Termo de Comodato" acima.</td></tr>';
      updateStats([]);
      return;
    }

    updateStats(json.data);

    let html = '';
    const currentBase = window.location.href.substring(0, window.location.href.lastIndexOf('/'));

    json.data.forEach(item => {
      const typeBadge = item.contract_type === 'notebook' 
        ? '<span style="color:#0284c7;font-weight:700;"><i class="ti ti-device-laptop"></i> Notebook</span>'
        : '<span style="color:#10b981;font-weight:700;"><i class="ti ti-device-mobile"></i> Smartphone</span>';
      
      const statusBadge = item.status === 'ativo'
        ? '<span class="badge badge-active">Ativo</span>'
        : '<span class="badge badge-ended">Encerrado</span>';

      const isSigned = (item.signature_status === 'assinado');
      const signBadge = isSigned
        ? `<span class="badge badge-signed" title="Assinado em ${formatDateTime(item.signed_at)}"><i class="ti ti-check"></i> Assinado</span>`
        : `<span class="badge badge-pending" title="Aguardando assinatura do usuário"><i class="ti ti-clock"></i> Pendente</span>`;

      const fullSignUrl = `${currentBase}/${item.sign_url}`;

      html += `<tr>
        <td><strong>#${item.id}</strong></td>
        <td>${typeBadge}</td>
        <td>
          <div style="font-weight:700;">${escapeHtml(item.user_name)}</div>
          <div style="font-size:12px;color:var(--muted);">CPF: ${escapeHtml(item.user_cpf || '-')}</div>
        </td>
        <td>
          <div style="font-weight:600;">${escapeHtml(item.asset_name)}</div>
          <div style="font-size:12px;color:var(--muted);">Mod: ${escapeHtml(item.asset_model || '-')}</div>
        </td>
        <td><code>${escapeHtml(item.asset_serial || '-')}</code></td>
        <td>${formatDate(item.assigned_date)}</td>
        <td>${signBadge}</td>
        <td>${statusBadge}</td>
        <td style="text-align:center;">
          <div style="display:inline-flex;gap:4px;flex-wrap:wrap;justify-content:center;">
            <button onclick="copyToClipboard('${fullSignUrl}')" class="btn btn-outline btn-sm" title="Copiar Link de Assinatura para enviar ao usuário">
              <i class="ti ti-link"></i> Copiar Link
            </button>
            <a href="${item.sign_url}" target="_blank" class="btn btn-outline btn-sm" title="Abrir Tela de Assinatura">
              <i class="ti ti-signature"></i> Assinar
            </a>
            <a href="comodato_doc.php?id=${item.id}" target="_blank" class="btn btn-outline btn-sm" title="Ver Termo Oficial">
              <i class="ti ti-file-text"></i> Termo
            </a>
            ${item.status === 'ativo' ? `
              <button onclick="terminateComodato(${item.id})" class="btn btn-outline btn-sm" style="color:var(--bad);border-color:#fecaca;" title="Encerrar Comodato (Devolução)">
                <i class="ti ti-arrow-back-up"></i>
              </button>
            ` : ''}
          </div>
        </td>
      </tr>`;
    });

    rowsEl.innerHTML = html;
  } catch (e) {
    console.error('Erro ao buscar comodatos', e);
  }
}

function updateStats(data) {
  const active = data.filter(d => d.status === 'ativo').length;
  const signed = data.filter(d => d.signature_status === 'assinado').length;
  const pending = data.filter(d => d.signature_status === 'pendente' && d.status === 'ativo').length;
  
  document.getElementById('stat-active').textContent = active;
  document.getElementById('stat-signed').textContent = signed;
  document.getElementById('stat-pending').textContent = pending;
  document.getElementById('stat-total').textContent = data.length;
}

async function saveComodato(e) {
  e.preventDefault();
  const form = document.getElementById('comodatoForm');
  const btn = document.getElementById('btnSubmitModal');
  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Gerando termo e link...';

  const formData = new FormData(form);
  formData.append('action', 'create');

  try {
    const res = await fetch('comodato_api.php', {
      method: 'POST',
      body: formData
    });
    const json = await res.json();
    if (json.success) {
      closeModal();
      form.reset();
      loadComodatos();

      // Exibir modal de link de assinatura imediato
      const currentBase = window.location.href.substring(0, window.location.href.lastIndexOf('/'));
      const fullUrl = `${currentBase}/${json.sign_url}`;
      
      document.getElementById('generatedSignLink').value = fullUrl;
      document.getElementById('btnOpenSignDirect').href = json.sign_url;
      document.getElementById('btnOpenDocDirect').href = json.doc_url;
      document.getElementById('linkSuccessModal').style.display = 'grid';
    } else {
      alert('Erro: ' + (json.error || 'Falha ao salvar'));
    }
  } catch (err) {
    alert('Erro de conexão ao salvar.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="ti ti-check"></i> Gerar Termo & Criar Link de Assinatura';
  }
}

function copySignLink() {
  const input = document.getElementById('generatedSignLink');
  input.select();
  navigator.clipboard.writeText(input.value);
  alert('Link de assinatura copiado para a área de transferência!');
}

function copyToClipboard(url) {
  navigator.clipboard.writeText(url);
  alert('Link de assinatura copiado com sucesso! Envie para o colaborador.');
}

async function terminateComodato(id) {
  if (!confirm('Deseja confirmar a devolução do equipamento e encerrar este comodato?')) return;
  
  const fd = new FormData();
  fd.append('action', 'terminate');
  fd.append('id', id);

  try {
    const res = await fetch('comodato_api.php', { method: 'POST', body: fd });
    const json = await res.json();
    if (json.success) {
      loadComodatos();
    } else {
      alert('Erro: ' + json.error);
    }
  } catch (e) {
    alert('Falha ao processar devolução.');
  }
}

function formatDate(d) {
  if (!d) return '-';
  const parts = d.split('-');
  if (parts.length === 3) return `${parts[2]}/${parts[1]}/${parts[0]}`;
  return d;
}

function formatDateTime(dt) {
  if (!dt) return '-';
  const parts = dt.split(' ');
  const d = formatDate(parts[0]);
  return `${d} às ${parts[1].substring(0, 5)}`;
}

function escapeHtml(text) {
  if (!text) return '';
  return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

loadOptions();
loadComodatos();
</script>

</body>
</html>
