<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão de Impressoras, Toners e Volume de Páginas — FlowIT</title>
  <link rel="stylesheet" href="/css/lib/tabler-icons.min.css">
  <link rel="stylesheet" href="/plugins/flowitwhite/public/tokens.css">
  <style>
    :root {
      --bg: #f8fafc;
      --card-bg: #ffffff;
      --border: #e2e8f0;
      --text: #0f172a;
      --text-muted: #64748b;
      --primary: #006aa6;
      --primary-hover: #004e7c;
      --success: #10b981;
      --danger: #ef4444;
      --warning: #f59e0b;
      --radius: 12px;
      --shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
    }
    body {
      margin: 0;
      padding: 20px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background: var(--bg);
      color: var(--text);
    }
    .header-box {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 18px 24px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: var(--shadow);
    }
    .header-box h1 {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .kpi-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 20px;
    }
    @media (max-width: 1024px) {
      .kpi-row { grid-template-columns: repeat(2, 1fr); }
    }
    .kpi-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 16px 20px;
      box-shadow: var(--shadow);
      position: relative;
    }
    .kpi-title {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      color: var(--text-muted);
      margin-bottom: 6px;
    }
    .kpi-val {
      font-size: 1.75rem;
      font-weight: 800;
      font-variant-numeric: tabular-nums;
    }
    .kpi-sub {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-top: 4px;
    }
    .kpi-icon {
      position: absolute;
      right: 16px;
      bottom: 14px;
      font-size: 2.25rem;
      opacity: 0.12;
    }
    .card-table {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }
    .table-head {
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
    }
    .table-head h2 {
      margin: 0;
      font-size: 0.9375rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.875rem;
      text-align: left;
    }
    th {
      background: #f8fafc;
      color: var(--text-muted);
      font-weight: 600;
      padding: 12px 16px;
      border-bottom: 2px solid var(--border);
    }
    td {
      padding: 14px 16px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
    }
    tr:hover td { background: #f8fafc; }
    
    /* Barra de Progresso do Toner */
    .toner-wrapper {
      min-width: 140px;
    }
    .toner-header {
      display: flex;
      justify-content: space-between;
      font-size: 0.75rem;
      font-weight: 700;
      margin-bottom: 4px;
    }
    .toner-bar {
      height: 8px;
      background: #e2e8f0;
      border-radius: 4px;
      overflow: hidden;
    }
    .toner-fill {
      height: 100%;
      border-radius: 4px;
      transition: width 0.4s ease;
    }
    .toner-high { background: var(--success); }
    .toner-medium { background: var(--warning); }
    .toner-low { background: var(--danger); animation: pulse-toner 1.5s infinite; }

    @keyframes pulse-toner {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 8px;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 700;
    }
    .pill-success { background: #dcfce7; color: #15803d; }
    .pill-warning { background: #fef3c7; color: #b45309; }
    .pill-danger { background: #fee2e2; color: #b91c1c; }

    .btn {
      padding: 6px 14px;
      border-radius: 6px;
      font-size: 0.8125rem;
      font-weight: 600;
      border: 1px solid var(--border);
      background: #ffffff;
      color: var(--text);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .btn:hover { background: #f1f5f9; }
    .btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
    .btn-primary:hover { background: var(--primary-hover); }
    .form-select {
      padding: 6px 12px;
      border-radius: 6px;
      border: 1px solid var(--border);
      background: #ffffff;
      font-size: 0.8125rem;
      font-weight: 600;
      color: var(--text);
      cursor: pointer;
    }
  </style>
</head>
<body>

  <div class="header-box">
    <div>
      <h1><i class="ti ti-printer" style="color: var(--primary);"></i> Gestão de Impressoras, Toners & Contadores</h1>
      <span style="font-size: 0.8125rem; color: var(--text-muted);">
        Telemetria SNMP em tempo real: nível residual de cartuchos e contadores de páginas por período.
      </span>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
      <label for="period_select" style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted);">Período:</label>
      <select id="period_select" class="form-select" onchange="loadPrintersData()">
        <option value="1">Hoje (Últimas 24h)</option>
        <option value="7">Últimos 7 dias</option>
        <option value="30" selected>Últimos 30 dias (Mensal)</option>
        <option value="90">Últimos 90 dias (Trimestral)</option>
      </select>
      <button class="btn btn-primary" onclick="loadPrintersData()"><i class="ti ti-refresh"></i> Atualizar Telemetria</button>
    </div>
  </div>

  <div class="kpi-row">
    <div class="kpi-card">
      <div class="kpi-title">Impressoras de Rede</div>
      <div class="kpi-val" id="kpi_printers_count">9</div>
      <div class="kpi-sub" style="color: var(--success);">● 100% Online via SNMP</div>
      <i class="ti ti-printer kpi-icon"></i>
    </div>
    <div class="kpi-card">
      <div class="kpi-title">Volume de Páginas no Período</div>
      <div class="kpi-val" style="color: var(--primary);" id="kpi_period_pages">--</div>
      <div class="kpi-sub" id="kpi_period_label">Consumo nos últimos 30 dias</div>
      <i class="ti ti-file-analytics kpi-icon"></i>
    </div>
    <div class="kpi-card">
      <div class="kpi-title">Total Geral Histórico (Lifetime)</div>
      <div class="kpi-val" id="kpi_lifetime_pages">--</div>
      <div class="kpi-sub">Total acumulado de todas as impressoras</div>
      <i class="ti ti-database kpi-icon"></i>
    </div>
    <div class="kpi-card">
      <div class="kpi-title">Toners em Atenção (&le; 15%)</div>
      <div class="kpi-val" id="kpi_warning_count" style="color: var(--success);">0</div>
      <div class="kpi-sub" id="kpi_warning_sub">Nenhum toner esgotando</div>
      <i class="ti ti-alert-triangle kpi-icon"></i>
    </div>
  </div>

  <div class="card-table">
    <div class="table-head">
      <h2><i class="ti ti-list"></i> Parque de Impressoras & Suprimentos</h2>
      <input type="text" id="printer_search" placeholder="Buscar por setor ou IP..." 
             style="padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border); font-size: 0.8125rem;" 
             onkeyup="filterPrinters()">
    </div>
    <table>
      <thead>
        <tr>
          <th>Impressora / Setor</th>
          <th>Endereço IP</th>
          <th style="min-width: 180px;">Nível de Toner Black</th>
          <th>Cilindro (Drum)</th>
          <th>Páginas no Período</th>
          <th>Total Acumulado (Lifetime)</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="printers_table_body">
        <tr>
          <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 32px;">
            Consultando telemetria SNMP do Zabbix...
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <script>
    let allPrinters = [];

    async function loadPrintersData() {
      const days = document.getElementById('period_select').value;
      const periodLabel = document.getElementById('period_select').options[document.getElementById('period_select').selectedIndex].text;
      document.getElementById('kpi_period_label').innerText = 'Consumo em: ' + periodLabel;

      try {
        const res = await fetch(`/plugins/flowitwhite/front/printers_telemetry_api.php?days=${days}`);
        const json = await res.json();

        if (json.success) {
          allPrinters = json.printers || [];
          document.getElementById('kpi_printers_count').innerText = json.total_printers;
          document.getElementById('kpi_period_pages').innerText = Number(json.total_period_pages).toLocaleString('pt-BR');
          document.getElementById('kpi_lifetime_pages').innerText = Number(json.total_lifetime_pages).toLocaleString('pt-BR');
          
          const warnCount = json.warning_printers;
          document.getElementById('kpi_warning_count').innerText = warnCount;
          if (warnCount > 0) {
            document.getElementById('kpi_warning_count').style.color = 'var(--danger)';
            document.getElementById('kpi_warning_sub').innerText = `${warnCount} impressora(s) requer troca`;
          } else {
            document.getElementById('kpi_warning_count').style.color = 'var(--success)';
            document.getElementById('kpi_warning_sub').innerText = 'Nenhum toner em nível crítico';
          }

          renderTable(allPrinters);
        }
      } catch (err) {
        console.error('Falha ao carregar telemetria de impressoras:', err);
      }
    }

    function renderTable(printers) {
      const tbody = document.getElementById('printers_table_body');
      tbody.innerHTML = '';

      if (printers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 32px;">Nenhuma impressora localizada.</td></tr>';
        return;
      }

      printers.forEach(p => {
        const tr = document.createElement('tr');
        
        // Formatar barra de Toner
        let tonerHtml = '<span style="color: var(--text-muted); font-size: 0.75rem;">Sem dados de OID</span>';
        if (p.toner_level !== null) {
          const lvl = Math.round(p.toner_level);
          let colorClass = 'toner-high';
          if (lvl <= 15) colorClass = 'toner-low';
          else if (lvl <= 35) colorClass = 'toner-medium';

          tonerHtml = `
            <div class="toner-wrapper">
              <div class="toner-header">
                <span>Preto (K)</span>
                <span>${lvl}%</span>
              </div>
              <div class="toner-bar">
                <div class="toner-fill ${colorClass}" style="width: ${lvl}%;"></div>
              </div>
            </div>
          `;
        }

        // Cilindro / Drum
        let drumHtml = '<span style="color: var(--text-muted); font-size: 0.75rem;">--</span>';
        if (p.drum_level !== null && p.drum_level > 0) {
          drumHtml = `<strong style="color: var(--primary);">${Math.round(p.drum_level)}%</strong>`;
        }

        tr.innerHTML = `
          <td>
            <strong>${p.name}</strong><br>
            <small style="color: var(--text-muted);">SNMP Agent v2c</small>
          </td>
          <td><code>${p.ip}</code></td>
          <td>${tonerHtml}</td>
          <td>${drumHtml}</td>
          <td>
            <strong style="color: var(--primary); font-size: 1rem;">${Number(p.period_pages).toLocaleString('pt-BR')}</strong>
            <small style="color: var(--text-muted); display: block;">páginas</small>
          </td>
          <td>
            <strong style="font-size: 0.9375rem;">${Number(p.lifetime_pages).toLocaleString('pt-BR')}</strong>
            <small style="color: var(--text-muted); display: block;">total impressas</small>
          </td>
          <td><span class="pill pill-success"><i class="ti ti-check"></i> Online</span></td>
        `;
        tbody.appendChild(tr);
      });
    }

    function filterPrinters() {
      const q = document.getElementById('printer_search').value.toLowerCase();
      const filtered = allPrinters.filter(p => 
        (p.name || '').toLowerCase().includes(q) || 
        (p.ip || '').toLowerCase().includes(q)
      );
      renderTable(filtered);
    }

    loadPrintersData();
  </script>
</body>
</html>
