<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NOC Observability & Video Wall — FlowIT Zabbix 7.4</title>
<link rel="stylesheet" href="/css/lib/tabler-icons.min.css">
<!-- Chart.js para renderização precisa do Figma Chart Components -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
:root {
  --bg: #090d16;
  --surface: #111827;
  --surface-hover: #1f2937;
  --border: #1f2937;
  --text: #f9fafb;
  --text-muted: #94a3b8;
  --primary: #38bdf8;
  --primary-glow: rgba(56, 189, 248, 0.25);
  --disaster: #ef4444;
  --high: #f97316;
  --average: #f59e0b;
  --warning: #eab308;
  --ok: #10b981;
  --purple: #a855f7;
  --indigo: #6366f1;
  --radius: 12px;
  --card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.4), 0 2px 6px -1px rgba(0, 0, 0, 0.2);
}

body.theme-light {
  --bg: #f8fafc;
  --surface: #ffffff;
  --surface-hover: #f1f5f9;
  --border: #e2e8f0;
  --text: #0f172a;
  --text-muted: #64748b;
  --primary: #0284c7;
  --primary-glow: rgba(2, 132, 199, 0.15);
  --card-shadow: 0 4px 16px -1px rgba(0, 78, 124, 0.06), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
}

* { box-sizing: border-box; }
body {
  margin: 0;
  background: var(--bg);
  color: var(--text);
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  overflow-x: hidden;
}

header {
  height: 56px;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  position: sticky;
  top: 0;
  z-index: 100;
}

.brand-section { display: flex; align-items: center; gap: 12px; }
.brand-logo { font-weight: 800; font-size: 1.125rem; letter-spacing: -0.02em; color: var(--primary); display: flex; align-items: center; gap: 6px; }
.badge-env { font-size: 0.6875rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: rgba(56, 189, 248, 0.15); color: var(--primary); text-transform: uppercase; }

.global-status { display: flex; align-items: center; gap: 14px; }
.pulse-indicator { display: inline-flex; align-items: center; gap: 8px; font-size: 0.8125rem; font-weight: 600; padding: 4px 12px; border-radius: 9999px; background: rgba(16, 185, 129, 0.15); color: var(--ok); }
.pulse-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--ok); box-shadow: 0 0 8px var(--ok); animation: pulse 2s infinite; }
@keyframes pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.alert-counters { display: flex; gap: 6px; }
.counter-pill { font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; }
.cp-disaster { background: rgba(239, 68, 68, 0.2); color: var(--disaster); }
.cp-high { background: rgba(249, 115, 22, 0.2); color: var(--high); }
.cp-average { background: rgba(245, 158, 11, 0.2); color: var(--average); }

.header-controls { display: flex; align-items: center; gap: 12px; }
.clock-display { font-size: 0.8125rem; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--text-muted); }
.btn-control { background: var(--surface-hover); border: 1px solid var(--border); color: var(--text); padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-control:hover { background: rgba(56, 189, 248, 0.2); border-color: var(--primary); }

body.kiosk-active header { display: none !important; }
body.kiosk-active .kiosk-top-bar { display: flex !important; }
.kiosk-top-bar { display: none; height: 38px; background: #040711; border-bottom: 1px solid #1e293b; padding: 0 20px; align-items: center; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); }

main { padding: 20px; display: grid; gap: 20px; }

/* 1. Sparkline KPI Cards (Figma Metric Cards) */
.kpi-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; }
@media (max-width: 1400px) { .kpi-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px) { .kpi-grid { grid-template-columns: 1fr; } }

.kpi-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 16px 18px 12px 18px;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
  box-shadow: var(--card-shadow);
  transition: transform 0.2s, box-shadow 0.2s;
}
.kpi-card:hover { transform: translateY(-2px); border-color: rgba(56, 189, 248, 0.4); }
.kpi-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; }
.kpi-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); }
.kpi-badge { font-size: 0.6875rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px; }
.badge-up { background: rgba(16, 185, 129, 0.15); color: var(--ok); }
.badge-down { background: rgba(239, 68, 68, 0.15); color: var(--disaster); }

.kpi-body { display: flex; justify-content: space-between; align-items: flex-end; }
.kpi-value { font-size: 1.875rem; font-weight: 800; line-height: 1; font-variant-numeric: tabular-nums; }
.kpi-sparkline { width: 90px; height: 38px; }

/* 2. Grid Central dos Gráficos Figma */
.chart-grid-main {
  display: grid;
  grid-template-columns: 1.7fr 1fr;
  gap: 20px;
}
@media (max-width: 1280px) { .chart-grid-main { grid-template-columns: 1fr; } }

.chart-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 20px;
  box-shadow: var(--card-shadow);
  display: flex;
  flex-direction: column;
}
.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  border-bottom: 1px solid var(--border);
  padding-bottom: 12px;
}
.chart-title {
  font-size: 0.9375rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0;
}
.chart-legend {
  display: flex;
  gap: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}
.legend-item { display: flex; align-items: center; gap: 6px; }
.legend-color { width: 10px; height: 10px; border-radius: 2px; }

/* 3. Grid Inferior: Donut, Barras e Tabela de Alarmes */
.chart-grid-sub {
  display: grid;
  grid-template-columns: 1fr 1fr 1.3fr;
  gap: 20px;
}
@media (max-width: 1400px) { .chart-grid-sub { grid-template-columns: 1fr 1fr; } }
@media (max-width: 900px) { .chart-grid-sub { grid-template-columns: 1fr; } }

/* Tabela de Eventos e Problemas Ativos */
.event-table { width: 100%; border-collapse: collapse; font-size: 0.8125rem; }
.event-table th { text-align: left; padding: 10px 12px; background: rgba(255, 255, 255, 0.02); color: var(--text-muted); border-bottom: 1px solid var(--border); }
.event-table td { padding: 12px; border-bottom: 1px solid var(--border); }
.event-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 4px; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; }
.sev-disaster { background: var(--disaster); color: #fff; }
.sev-high { background: var(--high); color: #fff; }
.sev-average { background: var(--average); color: #000; }
.sev-warning { background: var(--warning); color: #000; }
.sev-ok { background: var(--ok); color: #fff; }
</style>
</head>
<body>

  <!-- Top Bar discreta do Modo Kiosk -->
  <div class="kiosk-top-bar">
    <div>
      <strong style="color: var(--primary);"><i class="ti ti-brand-visual-studio"></i> FlowIT Video Wall</strong> &bull;
      <span>Ambiente: Produção</span> &bull;
      <span id="kiosk_current_view">Aba 1: Telemetria & Gráficos NOC</span>
    </div>
    <div style="display: flex; align-items: center; gap: 14px;">
      <span>Auto-rotação: <strong id="kiosk_interval_txt">30s</strong></span>
      <span id="kiosk_clock" style="font-weight: 700;">14:40:00</span>
      <button class="btn-control" onclick="toggleKiosk(false)"><i class="ti ti-arrows-minimize"></i> Sair Kiosk</button>
    </div>
  </div>

  <!-- Header Global de Observabilidade -->
  <header>
    <div class="brand-section">
      <div class="brand-logo"><i class="ti ti-activity-heartbeat"></i> FlowIT NOC</div>
      <span class="badge-env">PRODUÇÃO (Zabbix 7.4.5)</span>
    </div>

    <div class="global-status">
      <div class="pulse-indicator">
        <span class="pulse-dot"></span>
        <span id="global_sla_txt">OPERAÇÃO NORMAL &bull; SLA 100%</span>
      </div>
      <div class="alert-counters">
        <span class="counter-pill cp-disaster" id="hdr_disaster"><i class="ti ti-alert-triangle"></i> 0</span>
        <span class="counter-pill cp-high" id="hdr_high"><i class="ti ti-alert-circle"></i> 0</span>
        <span class="counter-pill cp-average" id="hdr_average"><i class="ti ti-info-circle"></i> 0</span>
      </div>
    </div>

    <div class="header-controls">
      <span class="clock-display" id="global_clock">--:--:--</span>
      <button class="btn-control" onclick="toggleTheme()"><i class="ti ti-sun-moon"></i> Tema</button>
      <button class="btn-control" onclick="toggleKiosk(true)"><i class="ti ti-device-tv"></i> Video Wall</button>
    </div>
  </header>

  <main>
    <!-- 1. CARDS COM SPARKLINE CHARTS (Figma Components) -->
    <div class="kpi-grid">
      
      <!-- Card 1: Hosts -->
      <div class="kpi-card">
        <div class="kpi-header">
          <span class="kpi-title">Hosts Monitorados</span>
          <span class="kpi-badge badge-up"><i class="ti ti-trending-up"></i> +4 novos</span>
        </div>
        <div class="kpi-body">
          <div>
            <div class="kpi-value" id="kpi_hosts_val">49</div>
            <span style="font-size: 0.6875rem; color: var(--ok);">● 49 Ativos no Zabbix</span>
          </div>
          <div class="kpi-sparkline"><canvas id="spark_hosts"></canvas></div>
        </div>
      </div>

      <!-- Card 2: Tráfego de Rede / Bandwidth -->
      <div class="kpi-card">
        <div class="kpi-header">
          <span class="kpi-title">Banda WAN Consumida</span>
          <span class="kpi-badge badge-up"><i class="ti ti-activity"></i> 76% Pico</span>
        </div>
        <div class="kpi-body">
          <div>
            <div class="kpi-value" style="color: var(--primary);">380 <small style="font-size: 1rem;">Mbps</small></div>
            <span style="font-size: 0.6875rem; color: var(--text-muted);">Link Fibra BA-142</span>
          </div>
          <div class="kpi-sparkline"><canvas id="spark_bandwidth"></canvas></div>
        </div>
      </div>

      <!-- Card 3: SLA 24h -->
      <div class="kpi-card">
        <div class="kpi-header">
          <span class="kpi-title">Disponibilidade Geral</span>
          <span class="kpi-badge badge-up"><i class="ti ti-shield-check"></i> Meta OK</span>
        </div>
        <div class="kpi-body">
          <div>
            <div class="kpi-value" style="color: var(--ok);">99.96%</div>
            <span style="font-size: 0.6875rem; color: var(--text-muted);">Uptime mensal</span>
          </div>
          <div class="kpi-sparkline"><canvas id="spark_sla"></canvas></div>
        </div>
      </div>

      <!-- Card 4: Incidentes Ativos -->
      <div class="kpi-card">
        <div class="kpi-header">
          <span class="kpi-title">Incidentes Ativos</span>
          <span class="kpi-badge badge-up" style="background: rgba(16, 185, 129, 0.15); color: var(--ok);"><i class="ti ti-check"></i> 0 Críticos</span>
        </div>
        <div class="kpi-body">
          <div>
            <div class="kpi-value" style="color: var(--ok);" id="kpi_incidents_val">0</div>
            <span style="font-size: 0.6875rem; color: var(--ok);" id="kpi_incidents_sub">Nenhum alarme aberto</span>
          </div>
          <div class="kpi-sparkline"><canvas id="spark_incidents"></canvas></div>
        </div>
      </div>

      <!-- Card 5: Nobreaks / Autonomia -->
      <div class="kpi-card">
        <div class="kpi-header">
          <span class="kpi-title">Autonomia Nobreaks</span>
          <span class="kpi-badge badge-up"><i class="ti ti-bolt"></i> Rede AC OK</span>
        </div>
        <div class="kpi-body">
          <div>
            <div class="kpi-value" style="color: var(--ok);">58 <small style="font-size: 1rem;">min</small></div>
            <span style="font-size: 0.6875rem; color: var(--text-muted);">Baterias em 100%</span>
          </div>
          <div class="kpi-sparkline"><canvas id="spark_ups"></canvas></div>
        </div>
      </div>

    </div>

    <!-- 2. GRID PRINCIPAL: GRÁFICO DE ÁREA SUAVE (SMOOTH AREA/LINE) & BARRAS DUPLAS -->
    <div class="chart-grid-main">
      
      <!-- Gráfico 1: Área Suave com Gradiente (Tráfego de Dados e Latência) -->
      <div class="chart-card">
        <div class="chart-header">
          <h2 class="chart-title"><i class="ti ti-chart-line" style="color: var(--primary);"></i> Tráfego de Rede em Tempo Real & Throughput (Mbps)</h2>
          <div class="chart-legend">
            <div class="legend-item"><span class="legend-color" style="background: var(--primary);"></span> Link Primário (In)</div>
            <div class="legend-item"><span class="legend-color" style="background: var(--purple);"></span> Link Backup (Out)</div>
            <div class="legend-item"><span class="legend-color" style="background: var(--ok);"></span> Latência (ms)</div>
          </div>
        </div>
        <div style="height: 270px; width: 100%;">
          <canvas id="chart_traffic_area"></canvas>
        </div>
      </div>

      <!-- Gráfico 2: Donut Semi-Circular (Distribuição de Ativos por Tipo & Protocolo) -->
      <div class="chart-card">
        <div class="chart-header">
          <h2 class="chart-title"><i class="ti ti-chart-donut-2" style="color: var(--purple);"></i> Inventário & Protocolos</h2>
          <span style="font-size: 0.75rem; color: var(--text-muted);">49 Ativos Cadastrados</span>
        </div>
        <div style="height: 220px; width: 100%; display: flex; justify-content: center; align-items: center; position: relative;">
          <canvas id="chart_inventory_donut"></canvas>
          <div style="position: absolute; text-align: center; pointer-events: none; margin-top: 10px;">
            <strong style="font-size: 1.5rem; display: block; line-height: 1;">49</strong>
            <small style="color: var(--text-muted); font-size: 0.75rem;">Dispositivos</small>
          </div>
        </div>
        <div style="display: flex; justify-content: space-around; margin-top: 10px; font-size: 0.75rem; border-top: 1px solid var(--border); padding-top: 10px;">
          <span><strong style="color: #38bdf8;">■</strong> Servidores (24)</span>
          <span><strong style="color: #a855f7;">■</strong> Impressoras (9)</span>
          <span><strong style="color: #10b981;">■</strong> Rede & APs (10)</span>
          <span><strong style="color: #f59e0b;">■</strong> UPS (6)</span>
        </div>
      </div>

    </div>

    <!-- 3. GRID SECUNDÁRIO: BARRAS HORIZONTAIS DE CAPACIDADE, GAUGE & ALARMES AO VIVO -->
    <div class="chart-grid-sub">

      <!-- Subgráfico 1: Barras Horizontais com Metas (Capacidade de Storage & Recursos) -->
      <div class="chart-card">
        <div class="chart-header">
          <h2 class="chart-title"><i class="ti ti-chart-bar" style="color: var(--ok);"></i> Capacidade de Storage & Carga</h2>
          <span style="font-size: 0.75rem; color: var(--ok);"><i class="ti ti-shield-check"></i> Sem saturação</span>
        </div>
        <div style="height: 220px;">
          <canvas id="chart_capacity_bars"></canvas>
        </div>
      </div>

      <!-- Subgráfico 2: Gauge / Carga de Processamento dos Clusters Proxmox -->
      <div class="chart-card">
        <div class="chart-header">
          <h2 class="chart-title"><i class="ti ti-cpu" style="color: var(--primary);"></i> Consumo de CPU Proxmox VE</h2>
          <span style="font-size: 0.75rem; color: var(--primary);">Cluster HA</span>
        </div>
        <div style="height: 220px;">
          <canvas id="chart_cluster_cpu"></canvas>
        </div>
      </div>

      <!-- Tabela de Alertas e Problemas Reais do Zabbix 7.4 -->
      <div class="chart-card">
        <div class="chart-header">
          <h2 class="chart-title"><i class="ti ti-bell-ringing" style="color: var(--primary);"></i> Alertas Ativos (Zabbix 7.4)</h2>
          <span style="font-size: 0.75rem; color: var(--text-muted);"><i class="ti ti-refresh"></i> Ao vivo 15s</span>
        </div>
        <div style="overflow-x: auto; max-height: 220px;">
          <table class="event-table">
            <thead>
              <tr>
                <th>Severidade</th>
                <th>Dispositivo</th>
                <th>Problema / Gatilho</th>
                <th>Aberto em</th>
              </tr>
            </thead>
            <tbody id="problems_table_body">
              <tr>
                <td colspan="4" style="text-align: center; color: var(--ok); padding: 36px 12px;">
                  <i class="ti ti-circle-check" style="font-size: 1.5rem; display: block; margin-bottom: 6px;"></i>
                  Nenhum problema ativo no Zabbix Server. Operação 100% Estável.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>

  <script>
    // 1. Relógio NTP
    function updateClock() {
      const now = new Date();
      const str = now.toLocaleTimeString('pt-BR');
      document.getElementById('global_clock').innerText = str + ' (NTP)';
      document.getElementById('kiosk_clock').innerText = str;
    }
    setInterval(updateClock, 1000);
    updateClock();

    function toggleTheme() {
      document.body.classList.toggle('theme-light');
      // Recriar gráficos ou atualizar cores se necessário
    }

    // 2. Video Wall / Modo Kiosk
    let kioskTimer = null;
    let currentSlide = 0;
    const slides = [
      'Visão Geral de Alertas & Telemetria Zabbix',
      'Tráfego de Links & Ativos de Rede',
      'Saúde de Servidores & Proxmox VE',
      'Status de Nobreaks & Impressoras'
    ];

    function toggleKiosk(active) {
      if (active) {
        document.body.classList.add('kiosk-active');
        if (document.documentElement.requestFullscreen) {
          document.documentElement.requestFullscreen().catch(() => {});
        }
        startRotation(30);
      } else {
        document.body.classList.remove('kiosk-active');
        if (document.exitFullscreen) {
          document.exitFullscreen().catch(() => {});
        }
        if (kioskTimer) clearInterval(kioskTimer);
      }
    }

    function startRotation(seconds) {
      if (kioskTimer) clearInterval(kioskTimer);
      document.getElementById('kiosk_interval_txt').innerText = seconds + 's';
      kioskTimer = setInterval(() => {
        currentSlide = (currentSlide + 1) % slides.length;
        document.getElementById('kiosk_current_view').innerText = `Aba ${currentSlide + 1}: ${slides[currentSlide]}`;
      }, seconds * 1000);
    }

    // 3. Renderização dos Sparklines nos KPI Cards (Figma Component)
    function createSparkline(canvasId, data, color) {
      const ctx = document.getElementById(canvasId).getContext('2d');
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

    createSparkline('spark_hosts', [45, 45, 46, 46, 47, 48, 49], 'rgb(56, 189, 248)');
    createSparkline('spark_bandwidth', [210, 260, 310, 290, 340, 380, 365], 'rgb(168, 85, 247)');
    createSparkline('spark_sla', [99.92, 99.95, 99.94, 99.97, 99.96, 99.98, 99.96], 'rgb(16, 185, 129)');
    createSparkline('spark_incidents', [2, 1, 3, 1, 0, 0, 0], 'rgb(16, 185, 129)');
    createSparkline('spark_ups', [100, 100, 100, 100, 99, 100, 100], 'rgb(245, 158, 11)');

    // 4. Renderização do Gráfico Principal: Área com Gradiente Suave (Throughput de Rede)
    const ctxTraffic = document.getElementById('chart_traffic_area').getContext('2d');
    const gradPrimary = ctxTraffic.createLinearGradient(0, 0, 0, 260);
    gradPrimary.addColorStop(0, 'rgba(56, 189, 248, 0.45)');
    gradPrimary.addColorStop(1, 'rgba(56, 189, 248, 0.0)');

    const gradPurple = ctxTraffic.createLinearGradient(0, 0, 0, 260);
    gradPurple.addColorStop(0, 'rgba(168, 85, 247, 0.35)');
    gradPurple.addColorStop(1, 'rgba(168, 85, 247, 0.0)');

    new Chart(ctxTraffic, {
      type: 'line',
      data: {
        labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '14:30'],
        datasets: [
          {
            label: 'Link Primário (In)',
            data: [180, 240, 320, 370, 290, 340, 385, 360],
            borderColor: '#38bdf8',
            backgroundColor: gradPrimary,
            fill: true,
            tension: 0.4,
            borderWidth: 2.5,
            pointRadius: 3,
            pointHoverRadius: 6
          },
          {
            label: 'Link Backup (Out)',
            data: [40, 55, 75, 90, 65, 80, 95, 85],
            borderColor: '#a855f7',
            backgroundColor: gradPurple,
            fill: true,
            tension: 0.4,
            borderWidth: 2,
            pointRadius: 3,
            pointHoverRadius: 6
          },
          {
            label: 'Latência (ms)',
            data: [1.8, 1.9, 2.1, 2.4, 1.8, 2.0, 2.2, 1.9],
            borderColor: '#10b981',
            borderWidth: 2,
            borderDash: [4, 4],
            pointRadius: 0,
            fill: false,
            yAxisID: 'yLatency'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#111827',
            titleColor: '#f9fafb',
            bodyColor: '#94a3b8',
            borderColor: '#1f2937',
            borderWidth: 1,
            padding: 10
          }
        },
        scales: {
          x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
          y: { 
            grid: { color: 'rgba(255, 255, 255, 0.05)' }, 
            ticks: { color: '#94a3b8', callback: v => v + ' M' } 
          },
          yLatency: {
            position: 'right',
            grid: { display: false },
            ticks: { color: '#10b981', callback: v => v + ' ms' }
          }
        }
      }
    });

    // 5. Renderização do Donut de Inventário (Figma Component)
    const ctxDonut = document.getElementById('chart_inventory_donut').getContext('2d');
    new Chart(ctxDonut, {
      type: 'doughnut',
      data: {
        labels: ['Servidores', 'Impressoras', 'Rede & APs', 'UPS Nobreaks'],
        datasets: [{
          data: [24, 9, 10, 6],
          backgroundColor: ['#38bdf8', '#a855f7', '#10b981', '#f59e0b'],
          borderColor: '#111827',
          borderWidth: 3,
          cutout: '76%'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#111827',
            borderColor: '#1f2937',
            borderWidth: 1
          }
        }
      }
    });

    // 6. Barras Horizontais de Storage (Figma Component)
    const ctxCapacity = document.getElementById('chart_capacity_bars').getContext('2d');
    new Chart(ctxCapacity, {
      type: 'bar',
      data: {
        labels: ['Pool TrueNAS', 'Backup PBS', 'Sankhya DB', 'PVE Storage'],
        datasets: [{
          data: [72, 58, 81, 44],
          backgroundColor: ['#38bdf8', '#10b981', '#f59e0b', '#38bdf8'],
          borderRadius: 6,
          barThickness: 16
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { max: 100, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { callback: v => v + '%' } },
          y: { grid: { display: false }, ticks: { color: '#f9fafb' } }
        }
      }
    });

    // 7. Gráfico de Colunas: CPU Cluster Proxmox VE (pve01, pve02)
    const ctxCpu = document.getElementById('chart_cluster_cpu').getContext('2d');
    new Chart(ctxCpu, {
      type: 'bar',
      data: {
        labels: ['pve01 Core', 'pve02 Core', 'PBS Storage', 'S01 App'],
        datasets: [{
          label: 'Uso de CPU %',
          data: [32, 28, 14, 45],
          backgroundColor: '#6366f1',
          borderRadius: 6,
          barThickness: 22
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
          y: { max: 100, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { callback: v => v + '%' } }
        }
      }
    });

    // 8. Polling de Telemetria Real do Zabbix (Incidentes)
    async function fetchLiveTelemetry() {
      try {
        const res = await fetch('/plugins/flowitwhite/front/monitoring_api.php?action=get_active_problems');
        const json = await res.json();
        const problems = json.problems || [];

        const totalIncidents = problems.length;
        document.getElementById('kpi_incidents_val').innerText = totalIncidents;

        let disCount = 0, highCount = 0, avgCount = 0;
        problems.forEach(p => {
          if (p.severity == 5) disCount++;
          else if (p.severity == 4) highCount++;
          else if (p.severity == 3) avgCount++;
        });

        document.getElementById('hdr_disaster').innerHTML = `<i class="ti ti-alert-triangle"></i> ${disCount}`;
        document.getElementById('hdr_high').innerHTML = `<i class="ti ti-alert-circle"></i> ${highCount}`;
        document.getElementById('hdr_average').innerHTML = `<i class="ti ti-info-circle"></i> ${avgCount}`;

        const tbody = document.getElementById('problems_table_body');
        if (totalIncidents === 0) {
          tbody.innerHTML = `
            <tr>
              <td colspan="4" style="text-align: center; color: var(--ok); padding: 36px 12px;">
                <i class="ti ti-circle-check" style="font-size: 1.5rem; display: block; margin-bottom: 6px;"></i>
                Nenhum problema ativo no Zabbix Server. Operação 100% Estável.
              </td>
            </tr>
          `;
          document.getElementById('kpi_incidents_val').style.color = 'var(--ok)';
          document.getElementById('kpi_incidents_sub').innerText = 'Nenhum alarme aberto';
        } else {
          tbody.innerHTML = '';
          document.getElementById('kpi_incidents_val').style.color = 'var(--high)';
          document.getElementById('kpi_incidents_sub').innerText = `${totalIncidents} incidentes ativos`;

          problems.forEach(p => {
            const tr = document.createElement('tr');
            let sevClass = 'sev-warning';
            let sevLabel = 'Atenção';
            if (p.severity == 5) { sevClass = 'sev-disaster'; sevLabel = 'Desastre'; }
            else if (p.severity == 4) { sevClass = 'sev-high'; sevLabel = 'Alto'; }
            else if (p.severity == 3) { sevClass = 'sev-average'; sevLabel = 'Médio'; }

            const hostName = (p.hosts && p.hosts[0]) ? p.hosts[0].name : 'Infraestrutura';
            const dateStr = new Date(p.clock * 1000).toLocaleTimeString('pt-BR');

            tr.innerHTML = `
              <td><span class="event-badge ${sevClass}">${sevLabel}</span></td>
              <td><strong>${hostName}</strong></td>
              <td>${p.name}</td>
              <td>${dateStr}</td>
            `;
            tbody.appendChild(tr);
          });
        }
      } catch (err) {
        console.error('Falha no polling de telemetria Zabbix:', err);
      }
    }

    fetchLiveTelemetry();
    setInterval(fetchLiveTelemetry, 15000);
  </script>
</body>
</html>
