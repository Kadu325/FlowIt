<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro Centralizado de Dispositivos — FlowIT Observabilidade</title>
  <link rel="stylesheet" href="/css/lib/tabler-icons.min.css">
  <link rel="stylesheet" href="/plugins/flowitwhite/public/tokens.css">
  <style>
    :root {
      --bg-page: #f8fafc;
      --card-bg: #ffffff;
      --card-border: #e2e8f0;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --primary: #006aa6;
      --primary-hover: #004e7c;
      --success: #10b981;
      --danger: #ef4444;
      --warning: #f59e0b;
      --radius: 12px;
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
      --shadow-md: 0 4px 6px -1px rgba(0, 78, 124, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
    }
    body {
      margin: 0;
      padding: 24px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background: var(--bg-page);
      color: var(--text-main);
      box-sizing: border-box;
    }
    .header-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--card-border);
    }
    .header-bar h1 {
      font-size: 1.5rem;
      font-weight: 700;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--text-main);
    }
    .badge-env {
      font-size: 0.75rem;
      padding: 4px 8px;
      border-radius: 6px;
      background: #e0f2fe;
      color: #0369a1;
      font-weight: 600;
    }
    .top-actions {
      display: flex;
      gap: 10px;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 9px 18px;
      font-size: 0.875rem;
      font-weight: 600;
      border-radius: 8px;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.2s ease;
      text-decoration: none;
    }
    .btn-primary {
      background: var(--primary);
      color: #ffffff;
    }
    .btn-primary:hover {
      background: var(--primary-hover);
    }
    .btn-outline {
      background: #ffffff;
      border-color: var(--card-border);
      color: var(--text-main);
    }
    .btn-outline:hover {
      background: #f1f5f9;
    }
    .grid-layout {
      display: grid;
      grid-template-columns: 1.15fr 0.85fr;
      gap: 24px;
    }
    @media (max-width: 1024px) {
      .grid-layout {
        grid-template-columns: 1fr;
      }
    }
    .card {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
      padding: 24px;
      margin-bottom: 24px;
    }
    .card-title {
      font-size: 1rem;
      font-weight: 700;
      margin-top: 0;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
      color: #1e293b;
      border-bottom: 1px solid #f1f5f9;
      padding-bottom: 10px;
    }
    .form-group {
      margin-bottom: 16px;
    }
    .form-group label {
      display: block;
      font-size: 0.8125rem;
      font-weight: 600;
      margin-bottom: 6px;
      color: var(--text-main);
    }
    .form-control, .form-select {
      width: 100%;
      padding: 9px 12px;
      font-size: 0.875rem;
      border-radius: 6px;
      border: 1px solid var(--card-border);
      background: #ffffff;
      color: var(--text-main);
      box-sizing: border-box;
      transition: border-color 0.2s;
    }
    .form-control:focus, .form-select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 106, 166, 0.12);
    }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    .diag-box {
      border: 1px dashed #cbd5e1;
      background: #f8fafc;
      border-radius: 8px;
      padding: 16px;
      margin-top: 16px;
    }
    .diag-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      border-bottom: 1px solid #e2e8f0;
      font-size: 0.875rem;
    }
    .diag-item:last-child {
      border-bottom: none;
    }
    .pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 10px;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .pill-success { background: #dcfce7; color: #15803d; }
    .pill-danger { background: #fee2e2; color: #b91c1c; }
    .pill-neutral { background: #e2e8f0; color: #475569; }
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem; }
    th { background: #f8fafc; color: var(--text-muted); font-weight: 600; padding: 12px 14px; border-bottom: 2px solid var(--card-border); }
    td { padding: 12px 14px; border-bottom: 1px solid var(--card-border); }
    tr:hover td { background: #f8fafc; }
  </style>
</head>
<body>

  <div class="header-bar">
    <div>
      <h1>
        <i class="ti ti-server-cog" style="color: var(--primary);"></i>
        Cadastro Centralizado de Dispositivos (GLPI ➔ Zabbix)
        <span class="badge-env">Zabbix 7.4.5 Conectado</span>
      </h1>
      <span style="font-size: 0.875rem; color: var(--text-muted);">
        Provisione hosts físicos, VMs, switches e nobreaks diretamente no Zabbix 7.4 sem sair do GLPI.
      </span>
    </div>
    <div class="top-actions">
      <button class="btn btn-outline" onclick="loadDevices()"><i class="ti ti-refresh"></i> Atualizar Lista</button>
      <button class="btn btn-primary" onclick="resetForm()"><i class="ti ti-plus"></i> Novo Dispositivo</button>
    </div>
  </div>

  <div class="grid-layout">
    
    <!-- Formulário de Cadastro / Edição -->
    <div>
      <div class="card">
        <h2 class="card-title"><i class="ti ti-edit"></i> 1. Identificação do Dispositivo</h2>
        <input type="hidden" id="dev_id" value="">
        
        <div class="form-row">
          <div class="form-group">
            <label for="dev_name">Nome Amigável / Descrição *</label>
            <input type="text" id="dev_name" class="form-control" placeholder="Ex: Switch Core Fazenda BA-142">
          </div>
          <div class="form-group">
            <label for="dev_host">Hostname no Zabbix (Sem espaços) *</label>
            <input type="text" id="dev_host" class="form-control" placeholder="Ex: sw-core-ba142">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="dev_ip">Endereço IP Primário *</label>
            <input type="text" id="dev_ip" class="form-control" placeholder="Ex: 192.168.77.254">
          </div>
          <div class="form-group">
            <label for="dev_dns">FQDN / Nome DNS</label>
            <input type="text" id="dev_dns" class="form-control" placeholder="Ex: sw-core.fazendaprogresso.com.local">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="dev_type">Tipo de Equipamento *</label>
            <select id="dev_type" class="form-select">
              <option value="server">Servidor (Físico / Virtual)</option>
              <option value="switch">Switch de Rede</option>
              <option value="router">Roteador / BGP</option>
              <option value="firewall">Firewall / Gateway</option>
              <option value="ap">Access Point / Rádio</option>
              <option value="printer">Impressora de Rede</option>
              <option value="ups">Nobreak / UPS</option>
              <option value="storage">Storage / NAS</option>
              <option value="internet_link">Link de Internet</option>
            </select>
          </div>
          <div class="form-group">
            <label for="dev_iface_type">Interface Principal *</label>
            <select id="dev_iface_type" class="form-select" onchange="toggleIfaceFields()">
              <option value="1">Zabbix Agent (Porta 10050)</option>
              <option value="2" selected>SNMP (Porta 161)</option>
            </select>
          </div>
        </div>
      </div>

      <div class="card">
        <h2 class="card-title"><i class="ti ti-template"></i> 2. Template & Host Group no Zabbix</h2>

        <div class="form-row">
          <div class="form-group">
            <label for="dev_group">Host Group no Zabbix *</label>
            <select id="dev_group" class="form-select">
              <option value="">Carregando grupos...</option>
            </select>
          </div>
          <div class="form-group">
            <label for="dev_template">Template Zabbix Associado *</label>
            <select id="dev_template" class="form-select">
              <option value="">Carregando templates...</option>
            </select>
          </div>
        </div>

        <div id="snmp_fields">
          <div class="form-row">
            <div class="form-group">
              <label for="dev_snmp_ver">Versão SNMP</label>
              <select id="dev_snmp_ver" class="form-select">
                <option value="2" selected>SNMP v2c (Padrão Corporativo)</option>
                <option value="1">SNMP v1</option>
                <option value="3">SNMP v3 (Autenticado & Criptografado)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="dev_snmp_comm">Comunidade SNMP</label>
              <input type="text" id="dev_snmp_comm" class="form-control" value="public" placeholder="Comunidade de Leitura">
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="dev_port">Porta de Comunicação</label>
            <input type="number" id="dev_port" class="form-control" value="161">
          </div>
          <div class="form-group">
            <label for="dev_status">Estado Inicial do Monitoramento</label>
            <select id="dev_status" class="form-select">
              <option value="0" selected>Monitorado (Ativo no Zabbix)</option>
              <option value="1">Não Monitorado (Em Manutenção / Pausado)</option>
            </select>
          </div>
        </div>

        <!-- Bloco de Diagnóstico e Teste de Comunicação -->
        <div class="diag-box">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <strong style="font-size: 0.875rem;"><i class="ti ti-activity"></i> Diagnóstico Pré-Sincronização</strong>
            <button type="button" class="btn btn-outline" style="padding: 5px 12px;" onclick="testCommunication()">
              <i class="ti ti-bolt"></i> Testar Comunicação Agora
            </button>
          </div>

          <div id="diag_results">
            <div class="diag-item">
              <span>Ping ICMP:</span>
              <span id="res_ping" class="pill pill-neutral">Não testado</span>
            </div>
            <div class="diag-item">
              <span>Porta de Serviço:</span>
              <span id="res_port" class="pill pill-neutral">Não testado</span>
            </div>
            <div class="diag-item">
              <span>Status Geral:</span>
              <span id="res_status" class="pill pill-neutral">Aguardando teste</span>
            </div>
          </div>
        </div>

        <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
          <button type="button" class="btn btn-outline" onclick="resetForm()">Cancelar</button>
          <button type="button" class="btn btn-primary" onclick="saveDevice()">
            <i class="ti ti-cloud-upload"></i> Provisionar no Zabbix
          </button>
        </div>
      </div>
    </div>

    <!-- Lista de Dispositivos Cadastrados -->
    <div>
      <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 20px; border-bottom: 1px solid var(--card-border); display: flex; justify-content: space-between; align-items: center;">
          <h2 class="card-title" style="margin: 0; border: none; padding: 0;">
            <i class="ti ti-list-details"></i> Dispositivos no Monitoramento (<span id="dev_count">0</span>)
          </h2>
          <span style="font-size: 0.75rem; color: var(--text-muted);">Zabbix: 192.168.77.208</span>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Dispositivo</th>
                <th>IP / Host</th>
                <th>Sincronização</th>
                <th style="text-align: right;">Ações</th>
              </tr>
            </thead>
            <tbody id="device_table_body">
              <tr>
                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 32px;">
                  Carregando dispositivos...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  <script>
    function toggleIfaceFields() {
      const type = document.getElementById('dev_iface_type').value;
      const portField = document.getElementById('dev_port');
      const snmpFields = document.getElementById('snmp_fields');
      if (type === '1') { // Agent
        portField.value = '10050';
        snmpFields.style.display = 'none';
      } else { // SNMP
        portField.value = '161';
        snmpFields.style.display = 'block';
      }
    }

    async function loadTemplatesAndGroups() {
      try {
        const res = await fetch('/plugins/flowitwhite/front/monitoring_api.php?action=get_templates_and_groups');
        const json = await res.json();
        
        // Preencher Grupos
        const groupSel = document.getElementById('dev_group');
        groupSel.innerHTML = '<option value="">-- Selecione o Grupo no Zabbix --</option>';
        (json.groups || []).forEach(g => {
          groupSel.innerHTML += `<option value="${g.groupid}">${g.name}</option>`;
        });

        // Preencher Templates
        const tplSel = document.getElementById('dev_template');
        tplSel.innerHTML = '<option value="">-- Selecione o Template --</option>';
        
        let currentCat = '';
        let optgroup = null;
        (json.templates || []).forEach(t => {
          if (t.category !== currentCat) {
            currentCat = t.category;
            optgroup = document.createElement('optgroup');
            optgroup.label = currentCat;
            tplSel.appendChild(optgroup);
          }
          const opt = document.createElement('option');
          opt.value = t.templateid;
          opt.textContent = t.name;
          if (optgroup) optgroup.appendChild(opt);
          else tplSel.appendChild(opt);
        });
      } catch (err) {
        console.error('Erro ao carregar templates/grupos:', err);
      }
    }

    async function testCommunication() {
      const ip = document.getElementById('dev_ip').value.trim();
      const type = document.getElementById('dev_iface_type').value === '1' ? 'agent' : 'snmp';
      const port = document.getElementById('dev_port').value;

      if (!ip) {
        alert('Informe o endereço IP antes de testar a comunicação.');
        return;
      }

      document.getElementById('res_ping').className = 'pill pill-neutral';
      document.getElementById('res_ping').innerText = 'Testando...';
      document.getElementById('res_port').className = 'pill pill-neutral';
      document.getElementById('res_port').innerText = 'Verificando porta...';
      document.getElementById('res_status').className = 'pill pill-neutral';
      document.getElementById('res_status').innerText = 'Avaliando...';

      try {
        const formData = new FormData();
        formData.append('action', 'test_comm');
        formData.append('ip', ip);
        formData.append('interface_type', type);
        formData.append('port', port);

        const res = await fetch('/plugins/flowitwhite/front/monitoring_api.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();

        if (data.success) {
          if (data.ping.success) {
            document.getElementById('res_ping').className = 'pill pill-success';
            document.getElementById('res_ping').innerText = `${data.ping.latency_ms} ms (OK)`;
          } else {
            document.getElementById('res_ping').className = 'pill pill-danger';
            document.getElementById('res_ping').innerText = 'Falha / Perda 100%';
          }

          if (data.port_check.success) {
            document.getElementById('res_port').className = 'pill pill-success';
            document.getElementById('res_port').innerText = data.port_check.message;
          } else {
            document.getElementById('res_port').className = 'pill pill-danger';
            document.getElementById('res_port').innerText = data.port_check.message;
          }

          if (data.status === 'Online') {
            document.getElementById('res_status').className = 'pill pill-success';
            document.getElementById('res_status').innerText = 'Online & Comunicável';
          } else if (data.status.includes('Inconsistente')) {
            document.getElementById('res_status').className = 'pill pill-warning';
            document.getElementById('res_status').innerText = data.status;
          } else {
            document.getElementById('res_status').className = 'pill pill-danger';
            document.getElementById('res_status').innerText = 'Offline';
          }
        } else {
          alert('Erro no teste: ' + (data.error || 'Falha desconhecida'));
        }
      } catch (err) {
        alert('Erro ao chamar API de diagnóstico: ' + err.message);
      }
    }

    async function saveDevice() {
      const id = document.getElementById('dev_id').value;
      const name = document.getElementById('dev_name').value.trim();
      const host = document.getElementById('dev_host').value.trim();
      const ip = document.getElementById('dev_ip').value.trim();
      const dns = document.getElementById('dev_dns').value.trim();
      const device_type = document.getElementById('dev_type').value;
      const interface_type = document.getElementById('dev_iface_type').value;
      const port = document.getElementById('dev_port').value;
      const snmp_version = document.getElementById('dev_snmp_ver').value;
      const snmp_community = document.getElementById('dev_snmp_comm').value.trim();
      const zabbix_status = document.getElementById('dev_status').value;
      const group_id = document.getElementById('dev_group').value;
      const template_id = document.getElementById('dev_template').value;

      if (!name || !host || !ip) {
        alert('Por favor, preencha os campos obrigatórios (Nome, Hostname e IP).');
        return;
      }

      const payload = {
        action: 'save_device',
        id: id || 0,
        name,
        host,
        ip,
        dns,
        device_type,
        interface_type,
        port,
        snmp_version,
        snmp_community,
        zabbix_status,
        group_id,
        template_id
      };

      try {
        const res = await fetch('/plugins/flowitwhite/front/monitoring_api.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const result = await res.json();
        if (result.success) {
          alert(result.message || 'Host sincronizado com sucesso no Zabbix!');
          resetForm();
          loadDevices();
        } else {
          alert('Aviso Zabbix: ' + (result.message || result.error));
          loadDevices();
        }
      } catch (err) {
        alert('Falha na requisição: ' + err.message);
      }
    }

    async function loadDevices() {
      try {
        const res = await fetch('/plugins/flowitwhite/front/monitoring_api.php?action=list');
        const json = await res.json();
        const tbody = document.getElementById('device_table_body');
        tbody.innerHTML = '';

        if (!json.data || json.data.length === 0) {
          tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 32px;">Nenhum dispositivo cadastrado no momento.</td></tr>';
          document.getElementById('dev_count').innerText = '0';
          return;
        }

        document.getElementById('dev_count').innerText = json.data.length;

        json.data.forEach(d => {
          const tr = document.createElement('tr');
          const isSynced = d.sync_status === 'synced';
          const syncPill = isSynced 
            ? `<span class="pill pill-success" title="HostID: ${d.zabbix_hostid}"><i class="ti ti-check"></i> Sincronizado</span>` 
            : `<span class="pill pill-danger" title="${d.sync_message || 'Pendente'}"><i class="ti ti-alert-circle"></i> ${d.sync_status}</span>`;
          
          tr.innerHTML = `
            <td>
              <strong>${d.name}</strong><br>
              <small style="color: var(--text-muted);">${d.host} &bull; ${d.device_type}</small>
            </td>
            <td>
              <span>${d.ip}</span><br>
              <small style="color: var(--text-muted);">${d.dns || 'Sem FQDN'}</small>
            </td>
            <td>${syncPill}</td>
            <td style="text-align: right;">
              <button class="btn btn-outline" style="padding: 4px 8px; font-size: 0.75rem;" onclick='editDevice(${JSON.stringify(d)})'>
                <i class="ti ti-edit"></i>
              </button>
              <button class="btn btn-outline" style="padding: 4px 8px; font-size: 0.75rem; color: var(--danger);" onclick="deleteDevice(${d.id})">
                <i class="ti ti-trash"></i>
              </button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      } catch (err) {
        console.error('Erro ao carregar dispositivos:', err);
      }
    }

    function editDevice(d) {
      document.getElementById('dev_id').value = d.id;
      document.getElementById('dev_name').value = d.name;
      document.getElementById('dev_host').value = d.host;
      document.getElementById('dev_ip').value = d.ip;
      document.getElementById('dev_dns').value = d.dns || '';
      document.getElementById('dev_type').value = d.device_type;
      document.getElementById('dev_snmp_ver').value = d.snmp_version || 2;
      document.getElementById('dev_snmp_comm').value = d.snmp_community || 'public';
      document.getElementById('dev_status').value = d.zabbix_status || 0;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    async function deleteDevice(id) {
      if (!confirm('Deseja realmente remover este dispositivo do GLPI e do Zabbix?')) return;
      const formData = new FormData();
      formData.append('action', 'delete_device');
      formData.append('id', id);

      const res = await fetch('/plugins/flowitwhite/front/monitoring_api.php', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();
      if (data.success) {
        loadDevices();
      } else {
        alert('Erro ao excluir: ' + (data.error || 'Erro desconhecido'));
      }
    }

    function resetForm() {
      document.getElementById('dev_id').value = '';
      document.getElementById('dev_name').value = '';
      document.getElementById('dev_host').value = '';
      document.getElementById('dev_ip').value = '';
      document.getElementById('dev_dns').value = '';
      document.getElementById('dev_type').value = 'server';
      document.getElementById('dev_iface_type').value = '2';
      document.getElementById('dev_port').value = '161';
      document.getElementById('dev_snmp_comm').value = 'public';
      document.getElementById('dev_status').value = '0';
      toggleIfaceFields();
      document.getElementById('res_ping').className = 'pill pill-neutral';
      document.getElementById('res_ping').innerText = 'Não testado';
      document.getElementById('res_port').className = 'pill pill-neutral';
      document.getElementById('res_port').innerText = 'Não testado';
      document.getElementById('res_status').className = 'pill pill-neutral';
      document.getElementById('res_status').innerText = 'Aguardando teste';
    }

    // Inicialização
    loadTemplatesAndGroups();
    loadDevices();
    toggleIfaceFields();
  </script>
</body>
</html>
