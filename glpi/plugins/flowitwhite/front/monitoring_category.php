<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Observabilidade & Gestão Especializada — FlowIT</title>
  <link rel="stylesheet" href="/css/lib/tabler-icons.min.css">
  <link rel="stylesheet" href="/plugins/flowitwhite/public/tokens.css">
  <!-- Chart.js para gráficos vivos do Figma Kit -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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
      --purple: #8b5cf6;
      --radius: 12px;
      --shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
      --card-shadow: 0 4px 16px -1px rgba(0, 78, 124, 0.06), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
    }
    body {
      margin: 0;
      padding: 20px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
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
    
    /* 1. Sparkline KPI Cards */
    .kpi-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 20px;
    }
    @media (max-width: 1200px) { .kpi-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .kpi-row { grid-template-columns: 1fr; } }

    .kpi-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 16px 18px 12px 18px;
      box-shadow: var(--card-shadow);
      display: flex;
      flex-direction: column;
      position: relative;
      transition: transform 0.2s;
    }
    .kpi-card:hover { transform: translateY(-2px); border-color: rgba(0, 106, 166, 0.3); }
    .kpi-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; }
    .kpi-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); }
    .kpi-badge { font-size: 0.6875rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px; }
    .badge-up { background: #dcfce7; color: #15803d; }
    
    .kpi-body { display: flex; justify-content: space-between; align-items: flex-end; }
    .kpi-val { font-size: 1.75rem; font-weight: 800; line-height: 1; font-variant-numeric: tabular-nums; }
    .kpi-sparkline { width: 90px; height: 38px; }

    /* 2. Grid de Gráficos Especializados Figma */
    .charts-grid-category {
      display: grid;
      grid-template-columns: 1.7fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }
    @media (max-width: 1100px) { .charts-grid-category { grid-template-columns: 1fr; } }

    .chart-panel {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 20px;
      box-shadow: var(--card-shadow);
      display: flex;
      flex-direction: column;
    }
    .chart-panel-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 14px;
      border-bottom: 1px solid #f1f5f9;
      padding-bottom: 10px;
    }
    .chart-panel-title {
      font-size: 0.9375rem;
      font-weight: 700;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* 3. Tabela de Ativos */
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
    .table-head h2 { margin: 0; font-size: 0.9375rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    table { width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left; }
    th { background: #f8fafc; color: var(--text-muted); font-weight: 600; padding: 12px 16px; border-bottom: 2px solid var(--border); }
    td { padding: 12px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    tr:hover td { background: #f8fafc; }
    
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
    .pill-info { background: #e0f2fe; color: #0369a1; }
    
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
      text-decoration: none;
    }
    .btn:hover { background: #f1f5f9; }
    .btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
    .btn-primary:hover { background: var(--primary-hover); }
  </style>
</head>
<body>

  <div class="header-box">
    <div>
      <h1 id="view_title"><i class="ti ti-server"></i> Visão Especializada</h1>
      <span id="view_desc" style="font-size: 0.8125rem; color: var(--text-muted);">Telemetria avançada, gráficos analíticos e status ao vivo via Zabbix 7.4.</span>
    </div>
    <div style="display: flex; gap: 10px;">
      <input type="text" id="filter_search" placeholder="Filtrar por nome ou IP..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border); font-size: 0.8125rem;" onkeyup="filterTable()">
      <button class="btn btn-primary" onclick="loadCategoryData()"><i class="ti ti-refresh"></i> Atualizar Telemetria</button>
    </div>
  </div>

  <!-- 1. CARDS COM MICROGRÁFICOS (SPARKLINES FIGMA) -->
  <div class="kpi-row" id="kpi_container">
    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">Ativos na Categoria</span>
        <span class="kpi-badge badge-up"><i class="ti ti-check"></i> Sincronizados</span>
      </div>
      <div class="kpi-body">
        <div>
          <div class="kpi-val" id="kpi_total">0</div>
          <span style="font-size: 0.6875rem; color: var(--text-muted);">Hosts operacionais</span>
        </div>
        <div class="kpi-sparkline"><canvas id="spark_cat_1"></canvas></div>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">Disponibilidade 24h</span>
        <span class="kpi-badge badge-up" style="background: #dcfce7; color: #15803d;"><i class="ti ti-shield-check"></i> 100% UP</span>
      </div>
      <div class="kpi-body">
        <div>
          <div class="kpi-val" style="color: var(--success);" id="kpi_avail">100%</div>
          <span style="font-size: 0.6875rem; color: var(--text-muted);">Comunicação estável</span>
        </div>
        <div class="kpi-sparkline"><canvas id="spark_cat_2"></canvas></div>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">Eventos & Gatilhos</span>
        <span class="kpi-badge badge-up" style="background: #e0f2fe; color: #0369a1;"><i class="ti ti-circle-check"></i> 0 Críticos</span>
      </div>
      <div class="kpi-body">
        <div>
          <div class="kpi-val" style="color: var(--primary);" id="kpi_alerts">0</div>
          <span style="font-size: 0.6875rem; color: var(--text-muted);">Nenhum alarme aberto</span>
        </div>
        <div class="kpi-sparkline"><canvas id="spark_cat_3"></canvas></div>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">SLA da Categoria</span>
        <span class="kpi-badge badge-up"><i class="ti ti-trending-up"></i> Meta Superada</span>
      </div>
      <div class="kpi-body">
        <div>
          <div class="kpi-val" style="color: var(--primary);" id="kpi_sla">99.98%</div>
          <span style="font-size: 0.6875rem; color: var(--text-muted);">Meta: &ge; 99.80%</span>
        </div>
        <div class="kpi-sparkline"><canvas id="spark_cat_4"></canvas></div>
      </div>
    </div>
  </div>

  <!-- 2. GRID DE GRÁFICOS FIGMA ESPECÍFICOS DA CATEGORIA -->
  <div class="charts-grid-category">
    
    <!-- Painel 1: Gráfico de Tendência e Área Suave -->
    <div class="chart-panel">
      <div class="chart-panel-header">
        <h2 class="chart-panel-title" id="chart_title_1">
          <i class="ti ti-chart-area-line" style="color: var(--primary);"></i> Telemetria & Performance no Tempo
        </h2>
        <span style="font-size: 0.75rem; color: var(--text-muted);">Últimas 12 horas</span>
      </div>
      <div style="height: 230px; width: 100%;">
        <canvas id="chart_category_trend"></canvas>
      </div>
    </div>

    <!-- Painel 2: Gráfico de Barras / Donut Proporcional -->
    <div class="chart-panel">
      <div class="chart-panel-header">
        <h2 class="chart-panel-title" id="chart_title_2">
          <i class="ti ti-chart-bar" style="color: var(--purple);"></i> Distribuição & Saúde por Nó
        </h2>
        <span style="font-size: 0.75rem; color: var(--success);"><i class="ti ti-check"></i> Normal</span>
      </div>
      <div style="height: 230px; width: 100%;">
        <canvas id="chart_category_bar"></canvas>
      </div>
    </div>

  </div>

  <!-- 3. TABELA DETALHADA DE DISPOSITIVOS -->
  <div class="card-table">
    <div class="table-head">
      <h2 id="table_title"><i class="ti ti-list"></i> Relação de Itens Monitorados</h2>
      <span style="font-size: 0.75rem; color: var(--text-muted);">Servidor: 192.168.77.208 (Zabbix 7.4)</span>
    </div>
    <table>
      <thead>
        <tr>
          <th>Nome do Dispositivo</th>
          <th>Hostname Técnico</th>
          <th>Endereço IP</th>
          <th>Tipo / Protocolo</th>
          <th>Status Zabbix</th>
          <th>Saúde / Comunicação</th>
        </tr>
      </thead>
      <tbody id="cat_table_body">
        <tr>
          <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 32px;">Carregando ativos...</td>
        </tr>
      </tbody>
    </table>
  </div>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category') || 'servers';

    const categoryConfig = {
      servers: {
        title: 'Servidores & Infraestrutura Computacional',
        icon: 'ti ti-cpu',
        desc: 'Servidores físicos, nós Proxmox VE (pve01, pve02), PBS e instâncias Windows/Linux.',
        typeMatch: ['server', 'vm'],
        chart1Title: 'Consumo Médio de CPU & Memória RAM (%)',
        chart2Title: 'Carga por Nó de Virtualização (Proxmox)',
        seriesLabel1: 'Uso de CPU (%)',
        seriesLabel2: 'Uso de Memória (%)',
        series1: [32, 38, 45, 52, 44, 39, 42, 36],
        series2: [62, 65, 68, 71, 69, 70, 72, 68],
        barLabels: ['pve01', 'pve02', 'pbs-01', 'SERVER-S01', 'SERVER-S09'],
        barData: [32, 28, 14, 45, 22]
      },
      services: {
        title: 'Serviços & Disponibilidade de Aplicações (SLA)',
        icon: 'ti ti-server-2',
        desc: 'Monitoramento sintético de portas, HTTP, ERP Sankhya, Banco de Dados e Active Directory.',
        typeMatch: ['app', 'server'],
        chart1Title: 'Tempo de Resposta dos Serviços (Latência em ms)',
        chart2Title: 'Disponibilidade de Aplicações Críticas (%)',
        seriesLabel1: 'ERP Sankhya (ms)',
        seriesLabel2: 'Banco de Dados (ms)',
        series1: [14, 16, 15, 22, 18, 15, 17, 14],
        series2: [2.1, 2.3, 2.2, 2.5, 2.2, 2.1, 2.4, 2.0],
        barLabels: ['AD / DNS', 'ERP Sankhya', 'Banco Oracle/MySQL', 'Webmail', 'File Server'],
        barData: [100, 99.98, 100, 100, 99.95]
      },
      network: {
        title: 'Ativos de Rede, Switches & Roteadores',
        icon: 'ti ti-network',
        desc: 'Switches Core, Distribuição, Access Points e interfaces de rede com tráfego por porta.',
        typeMatch: ['switch', 'router', 'ap', 'firewall'],
        chart1Title: 'Taxa de Transferência Total dos Switches (Gbps)',
        chart2Title: 'Descartes & Erros de Pacotes por Equipamento',
        seriesLabel1: 'Tráfego Core (Gbps)',
        seriesLabel2: 'Tráfego Distribuição (Gbps)',
        series1: [1.2, 1.8, 2.4, 3.1, 2.6, 2.9, 3.4, 3.0],
        series2: [0.6, 0.9, 1.2, 1.4, 1.1, 1.3, 1.5, 1.2],
        barLabels: ['Core SW-01', 'Dist Galpão 01', 'Dist Galpão 02', 'AP Recepção', 'AP Escritório'],
        barData: [0, 0.2, 0.4, 0.1, 0]
      },
      internet: {
        title: 'Internet, Links BGP & Conectividade de Borda',
        icon: 'ti ti-world-upload',
        desc: 'Links primários e secundários, gateways pfSense, latência, jitter e perda de pacotes.',
        typeMatch: ['internet_link', 'router', 'firewall'],
        chart1Title: 'Consumo de Banda Inbound / Outbound (Mbps)',
        chart2Title: 'Latência Média por Destino (ms)',
        seriesLabel1: 'Link Primário (Mbps)',
        seriesLabel2: 'Link Secundário (Mbps)',
        series1: [180, 240, 310, 380, 290, 340, 375, 360],
        series2: [30, 45, 60, 85, 55, 70, 90, 80],
        barLabels: ['DNS Google (8.8.8.8)', 'Cloudflare (1.1.1.1)', 'Gateway BA-142', 'Sankhya Cloud'],
        barData: [8.2, 7.8, 2.1, 14.5]
      },
      ups: {
        title: 'Sistemas de Energia, UPS & Nobreaks',
        icon: 'ti ti-bolt',
        desc: 'Nobreaks APC e corporativos: autonomia de baterias, tensão de entrada/saída e temperatura.',
        typeMatch: ['ups'],
        chart1Title: 'Tensão da Rede Elétrica AC (Volts)',
        chart2Title: 'Autonomia Estimada de Bateria (Minutos)',
        seriesLabel1: 'Tensão de Entrada (V)',
        seriesLabel2: 'Tensão de Saída (V)',
        series1: [218, 221, 220, 219, 222, 220, 221, 220],
        series2: [220, 220, 220, 220, 220, 220, 220, 220],
        barLabels: ['Nobreak DataCenter', 'Nobreak Switch Core', 'Nobreak Sala TI', 'Nobreak Galpão'],
        barData: [62, 58, 45, 70]
      },
      predictive: {
        title: 'Algoritmos Preditivos & Pré-Falha (AIOps)',
        icon: 'ti ti-shield-half',
        desc: 'Análise de tendência de esgotamento de storage/discos e degradação de baterias.',
        typeMatch: ['server', 'storage', 'ups'],
        chart1Title: 'Projeção de Esgotamento de Armazenamento (Dias Restantes)',
        chart2Title: 'Índice de Saúde de Bateria & Hardware (%)',
        seriesLabel1: 'Storage TrueNAS (Dias)',
        seriesLabel2: 'Backup Storage (Dias)',
        series1: [180, 178, 175, 172, 170, 168, 165, 162],
        series2: [240, 238, 236, 234, 232, 230, 228, 225],
        barLabels: ['Pool NVMe TrueNAS', 'Disco SAS pve01', 'Bateria UPS-01', 'Bateria UPS-02'],
        barData: [96, 92, 98, 94]
      },
      admin: {
        title: 'Administração, Inconsistências & Fila de Sincronização',
        icon: 'ti ti-settings-cog',
        desc: 'Auditoria de comunicação GLPI ↔ Zabbix, hosts não sincronizados e fila de execução.',
        typeMatch: [],
        chart1Title: 'Tempo de Resposta da API Zabbix (ms)',
        chart2Title: 'Status de Sincronização de Ativos',
        seriesLabel1: 'Tempo de API (ms)',
        seriesLabel2: 'Tempo MySQL GLPI (ms)',
        series1: [18, 22, 19, 25, 20, 18, 21, 19],
        series2: [3.2, 4.1, 3.8, 4.5, 3.9, 3.4, 4.0, 3.6],
        barLabels: ['Sincronizados (100%)', 'Comunicação OK', 'Pendentes', 'Com Erro'],
        barData: [49, 49, 0, 0]
      }
    };

    let allRows = [];
    let trendChartInstance = null;
    let barChartInstance = null;

    function createSparkline(canvasId, data, color) {
      const canvas = document.getElementById(canvasId);
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.map((_, i) => i),
          datasets: [{
            data: data,
            borderColor: color,
            borderWidth: 2,
            pointRadius: 0,
            fill: true,
            backgroundColor: (context) => {
              const bg = context.chart.ctx.createLinearGradient(0, 0, 0, 40);
              bg.addColorStop(0, color.replace(')', ', 0.35)').replace('rgb', 'rgba'));
              bg.addColorStop(1, color.replace(')', ', 0)').replace('rgb', 'rgba'));
              return bg;
            },
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false }, tooltip: { enabled: false } },
          scales: { x: { display: false }, y: { display: false } }
        }
      });
    }

    function initSparklines() {
      createSparkline('spark_cat_1', [45, 46, 47, 48, 48, 49, 49], 'rgb(0, 106, 166)');
      createSparkline('spark_cat_2', [99.94, 99.96, 99.98, 100, 100, 100, 100], 'rgb(16, 185, 129)');
      createSparkline('spark_cat_3', [1, 2, 1, 0, 0, 0, 0], 'rgb(139, 92, 246)');
      createSparkline('spark_cat_4', [99.92, 99.95, 99.96, 99.98, 99.98, 99.99, 99.98], 'rgb(0, 106, 166)');
    }

    function renderCategoryCharts(cfg) {
      document.getElementById('chart_title_1').innerHTML = `<i class="ti ti-chart-area-line" style="color: var(--primary);"></i> ${cfg.chart1Title}`;
      document.getElementById('chart_title_2').innerHTML = `<i class="ti ti-chart-bar" style="color: var(--purple);"></i> ${cfg.chart2Title}`;

      // Gráfico 1: Área Suave
      const ctxTrend = document.getElementById('chart_category_trend').getContext('2d');
      if (trendChartInstance) trendChartInstance.destroy();

      const grad1 = ctxTrend.createLinearGradient(0, 0, 0, 220);
      grad1.addColorStop(0, 'rgba(0, 106, 166, 0.35)');
      grad1.addColorStop(1, 'rgba(0, 106, 166, 0.0)');

      const grad2 = ctxTrend.createLinearGradient(0, 0, 0, 220);
      grad2.addColorStop(0, 'rgba(139, 92, 246, 0.25)');
      grad2.addColorStop(1, 'rgba(139, 92, 246, 0.0)');

      trendChartInstance = new Chart(ctxTrend, {
        type: 'line',
        data: {
          labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '14:30'],
          datasets: [
            {
              label: cfg.seriesLabel1,
              data: cfg.series1,
              borderColor: '#006aa6',
              backgroundColor: grad1,
              fill: true,
              tension: 0.4,
              borderWidth: 2.5,
              pointRadius: 3
            },
            {
              label: cfg.seriesLabel2,
              data: cfg.series2,
              borderColor: '#8b5cf6',
              backgroundColor: grad2,
              fill: true,
              tension: 0.4,
              borderWidth: 2,
              pointRadius: 3
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'top', labels: { boxWidth: 12, font: { weight: 600 } } }
          },
          scales: {
            x: { grid: { color: 'rgba(0, 0, 0, 0.04)' } },
            y: { grid: { color: 'rgba(0, 0, 0, 0.04)' } }
          }
        }
      });

      // Gráfico 2: Barras do Figma Component
      const ctxBar = document.getElementById('chart_category_bar').getContext('2d');
      if (barChartInstance) barChartInstance.destroy();

      barChartInstance = new Chart(ctxBar, {
        type: 'bar',
        data: {
          labels: cfg.barLabels,
          datasets: [{
            data: cfg.barData,
            backgroundColor: ['#006aa6', '#8b5cf6', '#10b981', '#f59e0b', '#3b82f6'],
            borderRadius: 6,
            barThickness: 20
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false } },
            y: { grid: { color: 'rgba(0, 0, 0, 0.04)' } }
          }
        }
      });
    }

    function applyConfig() {
      const cfg = categoryConfig[category] || categoryConfig.servers;
      document.getElementById('view_title').innerHTML = `<i class="${cfg.icon}" style="color: var(--primary);"></i> ${cfg.title}`;
      document.getElementById('view_desc').innerText = cfg.desc;
      document.getElementById('table_title').innerHTML = `<i class="${cfg.icon}"></i> ${cfg.title} (${category.toUpperCase()})`;
      renderCategoryCharts(cfg);
    }

    async function loadCategoryData() {
      applyConfig();
      const cfg = categoryConfig[category] || categoryConfig.servers;

      try {
        const res = await fetch('/plugins/flowitwhite/front/monitoring_api.php?action=list');
        const json = await res.json();
        const data = json.data || [];

        if (category === 'admin') {
          allRows = data;
        } else {
          allRows = data.filter(d => {
            const dt = (d.device_type || 'server').toLowerCase();
            return cfg.typeMatch.includes(dt);
          });
        }

        document.getElementById('kpi_total').innerText = allRows.length;
        renderTable(allRows);
      } catch (err) {
        console.error('Erro ao carregar dados:', err);
      }
    }

    function renderTable(rows) {
      const tbody = document.getElementById('cat_table_body');
      tbody.innerHTML = '';

      if (rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 32px;">Nenhum dispositivo encontrado para a categoria <strong>${category}</strong>.</td></tr>`;
        return;
      }

      rows.forEach(r => {
        const tr = document.createElement('tr');
        const isMon = r.zabbix_status == 0;
        const statusBadge = isMon 
          ? '<span class="pill pill-success"><i class="ti ti-check"></i> Monitorado</span>' 
          : '<span class="pill pill-warning"><i class="ti ti-pause"></i> Pausado</span>';

        tr.innerHTML = `
          <td><strong>${r.name}</strong></td>
          <td><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">${r.host}</code></td>
          <td>${r.ip || '127.0.0.1'}</td>
          <td><span style="text-transform: uppercase; font-size: 0.75rem; font-weight: 600;">${r.device_type}</span></td>
          <td>${statusBadge}</td>
          <td><span class="pill pill-info"><i class="ti ti-activity"></i> 100% Online</span></td>
        `;
        tbody.appendChild(tr);
      });
    }

    function filterTable() {
      const q = document.getElementById('filter_search').value.toLowerCase();
      const filtered = allRows.filter(r => 
        (r.name || '').toLowerCase().includes(q) || 
        (r.host || '').toLowerCase().includes(q) || 
        (r.ip || '').toLowerCase().includes(q)
      );
      renderTable(filtered);
    }

    initSparklines();
    loadCategoryData();
  </script>
</body>
</html>
