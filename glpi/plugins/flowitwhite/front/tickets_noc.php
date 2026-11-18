<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>FlowIT — Painel Operacional de Chamados (NOC & Service Desk)</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
:root{
  --bg:#f6f8fa;
  --surface:#ffffff;
  --surface2:#f1f5f8;
  --surface-hover:#f8fafc;
  --line:#dce3e8;
  --text:#17212b;
  --text-muted:#5b6875;
  --brand:#138a66;
  --brand-hover:#0f7053;
  --link:#006aa6;
  --critical:#dc2626;
  --critical-bg:#fef2f2;
  --critical-border:#fecaca;
  --warning:#d97706;
  --warning-bg:#fffbeb;
  --warning-border:#fde68a;
  --ok:#059669;
  --ok-bg:#ecfdf5;
  --ok-border:#a7f3d0;
  --low:#2563eb;
  --low-bg:#eff6ff;
  --low-border:#bfdbfe;
  --shadow:0 1px 3px rgba(0, 0, 0, 0.05);
}
*{box-sizing:border-box}
body{
  margin:0;
  background:var(--bg);
  color:var(--text);
  font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
  height:100vh;
  display:flex;
  flex-direction:column;
  overflow:hidden;
}

/* HEADER NOC */
header{
  background:var(--surface);
  border-bottom:1px solid var(--line);
  padding:12px 24px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:16px;
  flex-shrink:0;
}
.header-left{display:flex;align-items:center;gap:16px}
.header-logo{height:36px}
.live-clock{
  font-family:"Courier New",Courier,monospace;
  font-size:20px;
  font-weight:800;
  color:var(--brand);
  background:rgba(19, 138, 102, 0.08);
  border:1px solid rgba(19, 138, 102, 0.25);
  padding:4px 14px;
  border-radius:8px;
  letter-spacing:1px;
  display:flex;
  align-items:center;
  gap:8px;
}
.live-dot{
  width:10px;
  height:10px;
  background:var(--ok);
  border-radius:50%;
  box-shadow:0 0 8px rgba(5, 150, 105, 0.5);
  animation:pulse 1.8s infinite;
}
@keyframes pulse{
  0%,100%{opacity:1;transform:scale(1)}
  50%{opacity:0.4;transform:scale(0.85)}
}

.header-center{display:flex;align-items:center;gap:20px}
.main-kpi-pill{
  background:var(--surface2);
  border:1px solid var(--line);
  padding:6px 18px;
  border-radius:30px;
  display:flex;
  align-items:center;
  gap:10px;
  font-size:13.5px;
  color:var(--text);
}
.main-kpi-pill strong{font-size:18px;color:var(--brand);font-weight:800;}
.main-kpi-pill .sub-kpi{font-size:13px;color:var(--ok);font-weight:700;}

.header-right{display:flex;align-items:center;gap:12px}
.btn{
  background:var(--surface);
  color:var(--text);
  border:1px solid var(--line);
  padding:7px 14px;
  border-radius:8px;
  font-size:12.5px;
  font-weight:600;
  display:inline-flex;
  align-items:center;
  gap:6px;
  cursor:pointer;
  transition:all .15s ease;
}
.btn:hover{background:var(--surface2);border-color:#cbd5e1;}
.btn-primary{background:var(--brand);border-color:var(--brand);color:#ffffff;}
.btn-primary:hover{background:var(--brand-hover);border-color:var(--brand-hover);}

/* BARRA DE KPIS RÁPIDOS */
.kpi-strip{
  background:var(--surface);
  border-bottom:1px solid var(--line);
  padding:10px 24px;
  display:flex;
  gap:14px;
  overflow-x:auto;
  flex-shrink:0;
}
.kpi-item{
  background:var(--surface);
  border:1px solid var(--line);
  border-radius:10px;
  padding:8px 16px;
  display:flex;
  align-items:center;
  gap:12px;
  min-width:170px;
  box-shadow:var(--shadow);
}
.kpi-icon{
  width:36px;
  height:36px;
  border-radius:8px;
  display:grid;
  place-items:center;
  font-size:18px;
}
.kpi-info .k{font-size:11px;color:var(--text-muted);text-transform:uppercase;font-weight:700;}
.kpi-info .v{font-size:18px;font-weight:800;color:var(--text);line-height:1.2;}

/* LAYOUT PRINCIPAL (TABELA + PAINEL DE TÉCNICOS) */
.main-layout{
  display:grid;
  grid-template-columns:1fr 340px;
  flex:1;
  overflow:hidden;
  background:var(--bg);
}
@media (max-width: 1200px){
  .main-layout{grid-template-columns:1fr;}
  .side-team-panel{display:none;}
}

.table-container{
  overflow-y:auto;
  padding:16px 24px;
  display:flex;
  flex-direction:column;
  gap:8px;
}

/* CABEÇALHO DAS COLUNAS ALINHADAS */
.grid-header{
  display:grid;
  grid-template-columns:75px 1fr 200px 180px 140px 110px;
  align-items:center;
  gap:16px;
  padding:8px 18px 8px 24px;
  font-size:11.5px;
  font-weight:700;
  color:var(--text-muted);
  text-transform:uppercase;
  letter-spacing:0.04em;
  border-bottom:1px solid var(--line);
  margin-bottom:4px;
}

/* CARDS DE CHAMADOS EM LINHA COM GRID FIXA */
.ticket-row{
  background:var(--surface);
  border:1px solid var(--line);
  border-radius:10px;
  padding:12px 18px 12px 24px;
  display:grid;
  grid-template-columns:75px 1fr 200px 180px 140px 110px;
  align-items:center;
  gap:16px;
  transition:all .18s ease;
  position:relative;
  overflow:hidden;
  box-shadow:var(--shadow);
}
.ticket-row:hover{
  background:#fafcff;
  border-color:#b5c3ce;
  transform:translateX(3px);
  box-shadow:0 4px 12px rgba(18, 30, 42, 0.08);
}

/* Borda indicadora de prioridade/SLA na esquerda */
.ticket-row::before{
  content:'';
  position:absolute;
  left:0;
  top:0;
  bottom:0;
  width:5px;
}
.ticket-row.critical::before{background:var(--critical);}
.ticket-row.warning::before{background:var(--warning);}
.ticket-row.ok::before{background:var(--ok);}

/* Coluna 1: ID */
.col-id{
  font-weight:800;
  font-size:15px;
  color:var(--link);
  display:flex;
  flex-direction:column;
  gap:2px;
}
.col-id span{
  font-size:10.5px;
  font-weight:600;
  color:var(--text-muted);
}

/* Coluna 2: TÍTULO & CATEGORIA */
.col-title{
  display:flex;
  flex-direction:column;
  gap:3px;
  min-width:0;
}
.ticket-text{
  font-size:14px;
  font-weight:700;
  color:var(--text);
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
  cursor:pointer;
}
.ticket-text:hover{color:var(--link);}
.ticket-meta{
  display:flex;
  align-items:center;
  gap:8px;
  font-size:11.5px;
  color:var(--text-muted);
}
.cat-tag{
  background:var(--surface2);
  border:1px solid var(--line);
  padding:2px 7px;
  border-radius:4px;
  font-size:11px;
  color:var(--text);
  font-weight:600;
}

/* Coluna 3: TÉCNICO */
.col-tech{
  display:flex;
  align-items:center;
  gap:10px;
  min-width:0;
}
.tech-avatar{
  width:32px;
  height:32px;
  border-radius:8px;
  background:linear-gradient(135deg, var(--brand) 0%, #0f7053 100%);
  color:#ffffff;
  display:grid;
  place-items:center;
  font-weight:700;
  font-size:11px;
  flex-shrink:0;
}
.tech-info{
  min-width:0;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}
.tech-name{
  font-size:13px;
  font-weight:600;
  color:var(--text);
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}
.tech-role{font-size:11px;color:var(--text-muted);}

/* Coluna 4: REQUERENTE */
.col-req{
  display:flex;
  align-items:center;
  gap:8px;
  font-size:12.5px;
  color:var(--text);
  min-width:0;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}
.col-req span{
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}

/* Coluna 5: SLA */
.col-sla{
  text-align:center;
}
.sla-badge{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:5px;
  padding:5px 10px;
  border-radius:6px;
  font-size:12px;
  font-weight:700;
  width:100%;
}
.sla-critical{background:var(--critical-bg);color:var(--critical);border:1px solid var(--critical-border);}
.sla-warning{background:var(--warning-bg);color:var(--warning);border:1px solid var(--warning-border);}
.sla-ok{background:var(--ok-bg);color:var(--ok);border:1px solid var(--ok-border);}

/* Coluna 6: PRIORIDADE */
.col-priority{
  text-align:center;
}
.priority-pill{
  display:inline-block;
  padding:5px 10px;
  border-radius:6px;
  font-size:11.5px;
  font-weight:800;
  letter-spacing:0.04em;
  text-transform:uppercase;
  width:100%;
}
.priority-muito-alta{background:var(--critical-bg);color:var(--critical);border:1px solid var(--critical-border);}
.priority-alta{background:var(--warning-bg);color:var(--warning);border:1px solid var(--warning-border);}
.priority-media{background:var(--ok-bg);color:var(--ok);border:1px solid var(--ok-border);}
.priority-baixa{background:var(--low-bg);color:var(--low);border:1px solid var(--low-border);}

/* PAINEL LATERAL: CARGA DOS TÉCNICOS */
.side-team-panel{
  background:var(--surface);
  border-left:1px solid var(--line);
  padding:18px 20px;
  display:flex;
  flex-direction:column;
  gap:14px;
  overflow-y:auto;
}
.panel-title{
  font-size:13.5px;
  font-weight:700;
  color:var(--text);
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin:0;
  text-transform:uppercase;
  letter-spacing:.04em;
}
.team-member-card{
  background:var(--surface);
  border:1px solid var(--line);
  border-radius:10px;
  padding:10px 14px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:10px;
  box-shadow:var(--shadow);
}
.team-member-info{
  display:flex;
  align-items:center;
  gap:10px;
  min-width:0;
}
.member-tickets-count{
  background:rgba(19, 138, 102, 0.1);
  border:1px solid rgba(19, 138, 102, 0.25);
  color:var(--brand);
  padding:3px 10px;
  border-radius:99px;
  font-size:11.5px;
  font-weight:700;
  flex-shrink:0;
}
</style>
</head>
<body>

<!-- CABEÇALHO SUPERIOR -->
<header>
  <div class="header-left">
    <img src="../public/logo-flowit.png" alt="FlowIT" class="header-logo">
    <div class="live-clock" id="clock">
      <div class="live-dot"></div>
      <span id="time-display">--:--:--</span>
    </div>
  </div>

  <div class="header-center">
    <div class="main-kpi-pill">
      <span>Chamados em Atendimento:</span>
      <strong id="kpi-abertos">--</strong>
      <span style="color:var(--line);">|</span>
      <span>Abertos Hoje:</span>
      <span class="sub-kpi" id="kpi-hoje">-- ↑</span>
    </div>
  </div>

  <div class="header-right">
    <button class="btn" onclick="toggleSound()" id="sound-btn">
      <i class="ti ti-volume"></i> Som: Ativo
    </button>
    <button class="btn" onclick="toggleFullscreen()">
      <i class="ti ti-maximize"></i> Tela Cheia
    </button>
    <button class="btn btn-primary" onclick="loadBoard()">
      <i class="ti ti-refresh"></i> Atualizar
    </button>
  </div>
</header>

<!-- BARRA DE KPIS RÁPIDOS -->
<div class="kpi-strip">
  <div class="kpi-item">
    <div class="kpi-icon" style="background:var(--critical-bg);color:var(--critical);border:1px solid var(--critical-border);">
      <i class="ti ti-alert-triangle"></i>
    </div>
    <div class="kpi-info">
      <div class="k">SLA Crítico (&lt;30m)</div>
      <div class="v" id="kpi-criticos" style="color:var(--critical);">0</div>
    </div>
  </div>

  <div class="kpi-item">
    <div class="kpi-icon" style="background:var(--warning-bg);color:var(--warning);border:1px solid var(--warning-border);">
      <i class="ti ti-clock-pause"></i>
    </div>
    <div class="kpi-info">
      <div class="k">SLA Atenção</div>
      <div class="v" id="kpi-atencao" style="color:var(--warning);">0</div>
    </div>
  </div>

  <div class="kpi-item">
    <div class="kpi-icon" style="background:var(--ok-bg);color:var(--ok);border:1px solid var(--ok-border);">
      <i class="ti ti-progress"></i>
    </div>
    <div class="kpi-info">
      <div class="k">Em Andamento</div>
      <div class="v" id="kpi-em-atendimento" style="color:var(--ok);">0</div>
    </div>
  </div>

  <div class="kpi-item">
    <div class="kpi-icon" style="background:rgba(19, 138, 102, 0.1);color:var(--brand);border:1px solid rgba(19, 138, 102, 0.25);">
      <i class="ti ti-users"></i>
    </div>
    <div class="kpi-info">
      <div class="k">Técnicos em Ação</div>
      <div class="v" id="kpi-tecnicos-count">0</div>
    </div>
  </div>

  <div class="kpi-item" style="flex:1;justify-content:flex-end;">
    <div style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:8px;">
      <i class="ti ti-wifi" style="color:var(--ok);"></i>
      Auto-refresh ativo: <strong>a cada 10 segundos</strong>
    </div>
  </div>
</div>

<!-- CORPO DO PAINEL COM GRID ALINHADA -->
<div class="main-layout">
  <div class="table-container">
    <!-- CABEÇALHO FIXO DAS COLUNAS -->
    <div class="grid-header">
      <div>ID</div>
      <div>Título do Chamado & Categoria</div>
      <div>Técnico Responsável</div>
      <div>Requerente / Setor</div>
      <div style="text-align:center;">SLA Restante</div>
      <div style="text-align:center;">Prioridade</div>
    </div>

    <!-- LISTA DE CHAMADOS PERFEITAMENTE ALINHADA -->
    <div id="tickets-list" style="display:flex;flex-direction:column;gap:8px;">
      <div style="text-align:center;padding:40px;color:var(--text-muted);">
        <i class="ti ti-loader ti-spin" style="font-size:28px;"></i>
        <p>Carregando fila de chamados em tempo real...</p>
      </div>
    </div>
  </div>

  <!-- PAINEL LATERAL: CARGA DA EQUIPE -->
  <aside class="side-team-panel">
    <h3 class="panel-title">
      <span>Carga dos Técnicos</span>
      <i class="ti ti-users-group" style="color:var(--brand);"></i>
    </h3>
    <div id="team-list" style="display:flex;flex-direction:column;gap:10px;">
      <!-- Preenchido via JS -->
    </div>

    <div style="margin-top:auto;padding-top:16px;border-top:1px solid var(--line);font-size:11.5px;color:var(--text-muted);line-height:1.5;">
      <strong style="color:var(--text);">NOC Service Desk · Fazenda Progresso</strong><br>
      Integração GLPI 12 & Zabbix 7.4 ativa.<br>
      Grid alinhada com priorização de SLA.
    </div>
  </aside>
</div>

<script>
let soundActive = true;
let lastTicketCount = 0;

function updateClock() {
  const now = new Date();
  const h = String(now.getHours()).padStart(2, '0');
  const m = String(now.getMinutes()).padStart(2, '0');
  const s = String(now.getSeconds()).padStart(2, '0');
  document.getElementById('time-display').textContent = `${h}:${m}:${s}`;
}
setInterval(updateClock, 1000);
updateClock();

function toggleSound() {
  soundActive = !soundActive;
  const btn = document.getElementById('sound-btn');
  btn.innerHTML = soundActive ? '<i class="ti ti-volume"></i> Som: Ativo' : '<i class="ti ti-volume-off"></i> Som: Mudo';
  btn.style.color = soundActive ? 'var(--text)' : 'var(--text-muted)';
}

function playBeep() {
  if (!soundActive) return;
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.connect(gain);
    gain.connect(ctx.destination);
    osc.type = 'sine';
    osc.frequency.value = 880;
    gain.gain.setValueAtTime(0.1, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.35);
    osc.start();
    osc.stop(ctx.currentTime + 0.35);
  } catch(e) {}
}

function toggleFullscreen() {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen();
  } else if (document.exitFullscreen) {
    document.exitFullscreen();
  }
}

async function loadBoard() {
  try {
    const res = await fetch('tickets_api.php?action=live_board');
    const json = await res.json();
    if (!json.success) return;

    // Atualizar KPIs
    document.getElementById('kpi-abertos').textContent = json.kpis.abertos;
    document.getElementById('kpi-hoje').textContent = `${json.kpis.hoje} ↑`;
    document.getElementById('kpi-criticos').textContent = json.kpis.criticos;
    document.getElementById('kpi-em-atendimento').textContent = json.kpis.em_atendimento;
    
    const tecKeys = Object.keys(json.kpis.tecnicos);
    document.getElementById('kpi-tecnicos-count').textContent = tecKeys.length;

    // Renderizar Técnicos
    const teamEl = document.getElementById('team-list');
    let teamHtml = '';
    tecKeys.forEach(name => {
      const count = json.kpis.tecnicos[name];
      const initials = name.split(' ').map(n=>n[0]).join('').substring(0, 2).toUpperCase();
      teamHtml += `
        <div class="team-member-card">
          <div class="team-member-info">
            <div class="tech-avatar">${initials}</div>
            <div style="min-width:0;">
              <div style="font-size:12.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(name)}</div>
              <div style="font-size:11px;color:var(--text-muted);">Suporte N2 / TI</div>
            </div>
          </div>
          <div class="member-tickets-count">${count} em fila</div>
        </div>
      `;
    });
    teamEl.innerHTML = teamHtml;

    // Renderizar Chamados na Grid Alinhada
    const container = document.getElementById('tickets-list');
    let html = '';

    json.tickets.forEach(t => {
      const slaClass = t.sla_status === 'critical' ? 'sla-critical' : (t.sla_status === 'warning' ? 'sla-warning' : 'sla-ok');
      const rowClass = t.sla_status === 'critical' ? 'critical' : (t.sla_status === 'warning' ? 'warning' : 'ok');

      let priorityClass = 'priority-media';
      if (t.priority_txt === 'Muito Alta') {
        priorityClass = 'priority-muito-alta';
      } else if (t.priority_txt === 'Alta') {
        priorityClass = 'priority-alta';
      } else if (t.priority_txt === 'Baixa') {
        priorityClass = 'priority-baixa';
      }

      html += `
        <div class="ticket-row ${rowClass}">
          <!-- Coluna 1: ID -->
          <div class="col-id">
            #${t.id}
            <span>${t.tempo_decorrido}</span>
          </div>

          <!-- Coluna 2: TÍTULO & CATEGORIA -->
          <div class="col-title">
            <div class="ticket-text" title="${escapeHtml(t.name)}">
              ${escapeHtml(t.name)}
            </div>
            <div class="ticket-meta">
              <span class="cat-tag"><i class="ti ti-tag"></i> ${escapeHtml(t.categoria)}</span>
              <span><i class="ti ti-activity"></i> ${t.status_txt}</span>
            </div>
          </div>

          <!-- Coluna 3: TÉCNICO -->
          <div class="col-tech" title="${escapeHtml(t.tecnico)}">
            <div class="tech-avatar">${t.tecnico_avatar}</div>
            <div class="tech-info">
              <div class="tech-name">${escapeHtml(t.tecnico)}</div>
              <div class="tech-role">Atribuído</div>
            </div>
          </div>

          <!-- Coluna 4: REQUERENTE -->
          <div class="col-req" title="Requerente: ${escapeHtml(t.requerente || 'Não informado')}">
            <i class="ti ti-user" style="color:var(--text-muted);flex-shrink:0;"></i>
            <span>${escapeHtml(t.requerente || 'Não informado')}</span>
          </div>

          <!-- Coluna 5: SLA -->
          <div class="col-sla">
            <div class="sla-badge ${slaClass}">
              <i class="ti ti-clock"></i> ${t.sla_restante_min}m restantes
            </div>
          </div>

          <!-- Coluna 6: PRIORIDADE -->
          <div class="col-priority">
            <div class="priority-pill ${priorityClass}">
              ${t.priority_txt}
            </div>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;

    // Alerta sonoro se entrar novo chamado
    if (lastTicketCount > 0 && json.tickets.length > lastTicketCount) {
      playBeep();
    }
    lastTicketCount = json.tickets.length;

  } catch(e) {
    console.error('Erro ao atualizar live board', e);
  }
}

function escapeHtml(text) {
  if (!text) return '';
  return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

loadBoard();
setInterval(loadBoard, 10000); // 10 segundos
</script>

</body>
</html>
