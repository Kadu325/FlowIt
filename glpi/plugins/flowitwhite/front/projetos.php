<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>FlowIT — Governança Integrada de Projetos, Contratos, SLAs e RACI</title>
<style>
:root {
  --bg: #f6f8fa;
  --surface: #ffffff;
  --surface-alt: #f0f4f6;
  --line: #dce3e8;
  --text: #17212b;
  --muted: #5b6875;
  --brand: #138a66;
  --brand-hover: #0f7053;
  --brand-soft: #eaf7f2;
  --info: #006aa6;
  --info-soft: #f0f7fc;
  --warn: #b45309;
  --warn-soft: #fef3c7;
  --danger: #b42318;
  --danger-soft: #fee2e2;
  --ok: #138a66;
  --ok-soft: #d1fae5;
  --shadow: 0 4px 18px rgb(18 30 42 / 6%);
  --radius: 12px;
  --radius-sm: 8px;
}
* { box-sizing: border-box; }
body { margin: 0; background: var(--bg); color: var(--text); font: 14px/1.5 "Segoe UI", system-ui, sans-serif; }
.num { font-variant-numeric: tabular-nums; text-align: right; }
<style>
:root {
  --bg: #f8fafc;
  --surface: #ffffff;
  --surface-alt: #f1f5f9;
  --line: #e2e8f0;
  --line-strong: #cbd5e1;
  --text: #0f172a;
  --muted: #64748b;
  --brand: #138a66;
  --brand-hover: #0f7053;
  --brand-soft: #eaf7f2;
  --info: #0284c7;
  --info-hover: #0369a1;
  --info-soft: #e0f2fe;
  --info-active: #0284c7;
  --warn: #b45309;
  --warn-soft: #fef3c7;
  --danger: #b42318;
  --danger-soft: #fee2e2;
  --ok: #138a66;
  --ok-soft: #d1fae5;
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.05);
  --radius: 10px;
  --radius-sm: 6px;
}
* { box-sizing: border-box; }
body { margin: 0; background: var(--bg); color: var(--text); font: 14px/1.5 "Segoe UI", -apple-system, BlinkMacSystemFont, Roboto, sans-serif; -webkit-font-smoothing: antialiased; }
.num { font-variant-numeric: tabular-nums; text-align: right; }

/* 1. Barra Principal Corporativa (Header: 68px de altura, fundo branco, limpo) */
header.app-header {
  height: 68px;
  min-height: 68px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  padding: 0 24px;
  background: var(--surface);
  border-bottom: 1px solid var(--line);
  position: relative;
  z-index: 100;
}
.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
  min-width: 0;
}
.header-left img.logo {
  height: 34px;
  width: auto;
  object-fit: contain;
  flex-shrink: 0;
}
.header-title-box {
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.header-title-box h1 {
  font-size: 16.5px;
  margin: 0;
  font-weight: 700;
  color: #0f172a;
  letter-spacing: -0.01em;
  white-space: nowrap;
}
.header-title-box .sub {
  font-size: 11.5px;
  color: var(--muted);
  font-weight: 500;
  margin-top: 1px;
  white-space: nowrap;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}
.select-proj {
  padding: 8px 14px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--line-strong);
  background: #ffffff;
  font-size: 13.5px;
  font-weight: 500;
  color: var(--text);
  min-width: 320px;
  height: 38px;
  outline: none;
  transition: all .15s ease;
}
.select-proj:focus {
  border-color: var(--info);
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}
.btn-refresh {
  padding: 0 16px;
  height: 38px;
  border-radius: var(--radius-sm);
  border: 1px solid #0284c7;
  background: #0284c7;
  color: #ffffff;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  transition: all .15s ease;
}
.btn-refresh:hover {
  background: #0369a1;
  border-color: #0369a1;
  box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);
}

/* 2. Barra de Navegação Corporativa de Abas (Segunda Barra) */
.subnav-container {
  background: var(--surface);
  border-bottom: 1px solid var(--line);
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 90;
}
.subnav-tabs {
  display: flex;
  align-items: center;
  gap: 4px;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
  margin: 0;
  padding: 0;
  list-style: none;
}
.subnav-tabs::-webkit-scrollbar {
  display: none;
}

.tab-group-divider {
  width: 1px;
  height: 24px;
  background: var(--line);
  margin: 0 6px;
  flex-shrink: 0;
}

.tab-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 14px;
  border: none;
  background: transparent;
  font-size: 13px;
  font-weight: 600;
  color: var(--muted);
  cursor: pointer;
  white-space: nowrap;
  position: relative;
  border-radius: var(--radius-sm) var(--radius-sm) 0 0;
  transition: all .15s ease-in-out;
  outline: none;
}
.tab-btn:hover {
  color: #0f172a;
  background: #f8fafc;
}
.tab-btn i.tab-icon, .tab-btn span.tab-icon {
  font-size: 15px;
  line-height: 1;
}

/* Estado Ativo: Linha inferior de 3px, fundo azul claro sutil, texto azul escuro */
.tab-btn.active {
  color: #0369a1;
  background: #f0f9ff;
}
.tab-btn.active::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: #0284c7;
  border-radius: 3px 3px 0 0;
}

/* Badges Corporativos (Pills / Círculos) */
.tab-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  height: 18px;
  min-width: 18px;
  padding: 0 5px;
  border-radius: 9999px;
  line-height: 1;
  background: #e2e8f0;
  color: #475569;
}
/* Cores semânticas para badges */
.tab-badge.badge-danger {
  background: #fee2e2;
  color: #b91c1c;
}
.tab-badge.badge-warn {
  background: #fef3c7;
  color: #b45309;
}
.tab-badge.badge-info {
  background: #e0f2fe;
  color: #0369a1;
}
/* Oculta automaticamente quando valor for 0 ou vazio */
.tab-badge:empty, .tab-badge[data-count="0"] {
  display: none !important;
}

main { padding: 24px; max-width: 1440px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }
.tab-pane { display: none; flex-direction: column; gap: 20px; }
.tab-pane.active { display: flex; }

/* Grid de KPIs */
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; }
.kpi-card {
  background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius);
  padding: 16px; box-shadow: var(--shadow); position: relative; overflow: hidden;
}
.kpi-card::before {
  content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--line);
}
.kpi-card.brand::before { background: var(--brand); }
.kpi-card.info::before { background: var(--info); }
.kpi-card.warn::before { background: var(--warn); }
.kpi-card.danger::before { background: var(--danger); }
.kpi-card.ok::before { background: var(--ok); }
.kpi-label { font-size: 11.5px; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); font-weight: 600; margin-bottom: 4px; }
.kpi-value { font-size: 22px; font-weight: 700; color: var(--text); font-variant-numeric: tabular-nums; }
.kpi-sub { font-size: 12px; color: var(--muted); margin-top: 4px; display: flex; align-items: center; gap: 4px; }

/* Painéis e Cards */
.panel {
  background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius);
  box-shadow: var(--shadow); padding: 20px; display: flex; flex-direction: column; gap: 16px;
}
.panel-head { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--line); padding-bottom: 12px; }
.panel-title { font-size: 15px; font-weight: 650; margin: 0; display: flex; align-items: center; gap: 8px; }
.panel-desc { font-size: 12px; color: var(--muted); margin-top: 2px; }

/* Curva S Barras */
.curve-bar-group { display: flex; flex-direction: column; gap: 14px; }
.curve-row { display: grid; grid-template-columns: 140px 1fr 140px; align-items: center; gap: 16px; font-size: 13px; }
.curve-track { height: 14px; background: var(--surface-alt); border-radius: 7px; overflow: hidden; position: relative; }
.curve-fill { height: 100%; border-radius: 7px; transition: width .4s cubic-bezier(.4,0,.2,1); }
.fill-pv { background: #3b82f6; }
.fill-ev { background: var(--ok); }
.fill-ac { background: #ef4444; }

/* Matriz 4x4 */
.matrix-container { display: flex; gap: 20px; flex-wrap: wrap; }
.matrix-grid {
  display: grid; grid-template-columns: 80px repeat(4, 1fr); gap: 6px;
  background: var(--surface-alt); padding: 12px; border-radius: var(--radius-sm); flex: 2; min-width: 480px;
}
.m-header { font-size: 11px; font-weight: 700; text-align: center; color: var(--muted); text-transform: uppercase; padding: 6px; }
.m-label { font-size: 11px; font-weight: 700; color: var(--muted); display: flex; align-items: center; justify-content: flex-end; padding-right: 8px; }
.m-cell {
  background: #fff; border-radius: 6px; min-height: 52px; padding: 4px;
  display: flex; flex-direction: column; gap: 3px; border: 1px solid #e2e8f0; justify-content: center; align-items: center;
}
.m-badge {
  font-size: 10.5px; padding: 2px 6px; border-radius: 4px; font-weight: 600; color: #fff;
  text-align: center; width: 92%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}

/* Tabelas */
.table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: var(--radius-sm); }
table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
th { background: var(--surface-alt); padding: 10px 14px; font-weight: 650; color: var(--muted); border-bottom: 1px solid var(--line); }
td { padding: 11px 14px; border-bottom: 1px solid var(--line); background: var(--surface); }
tr:last-child td { border-bottom: none; }
tr:hover td { background: var(--surface-alt); }
.tag { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 4px; font-size: 11.5px; font-weight: 600; }
.t-ok { background: var(--ok-soft); color: var(--ok); }
.t-warn { background: var(--warn-soft); color: var(--warn); }
.t-bad { background: var(--danger-soft); color: var(--danger); }
.t-info { background: var(--info-soft); color: var(--info); }

.raci-r { background: #e0f2fe; color: #0369a1; }
.raci-a { background: #d1fae5; color: #065f46; }
.raci-c { background: #fef3c7; color: #b45309; }
.raci-i { background: #f1f5f9; color: #475569; }

/* Checklist de Encerramento */
.checklist-grid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px;
}
.check-card {
  padding: 14px; border: 1px solid var(--line); border-radius: var(--radius-sm);
  background: var(--surface-alt); display: flex; gap: 12px; align-items: flex-start;
}
.check-icon { font-size: 20px; line-height: 1; }
.check-title { font-weight: 650; font-size: 13px; color: var(--text); }
.check-val { font-size: 12px; margin-top: 3px; }

/* Lições Aprendidas Repositório */
.lessons-box {
  background: var(--surface-alt); border-left: 4px solid var(--brand); padding: 16px; border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
  font-size: 13px; line-height: 1.6; white-space: pre-line;
}
</style>
</head>
<body>

<!-- 1. BARRA PRINCIPAL CORPORATIVA (Header 68px, fundo branco, borda suave) -->
<header class="app-header">
  <div class="header-left">
    <img src="/plugins/flowitwhite/public/logo-flowit.png" alt="FlowIT" class="logo" onerror="this.style.display='none'">
    <div class="header-title-box">
      <h1>Governança Integrada de Projetos</h1>
      <div class="sub">PMO &bull; Gestão Financeira, Curva S, EVM, Riscos e Contratos</div>
    </div>
  </div>

  <div class="header-right">
    <select id="selProj" class="select-proj" title="Selecione o Projeto Corporativo">
      <option value="">Carregando projetos...</option>
    </select>
    <button id="btnRecarregar" class="btn-refresh">
      <span style="font-size:14px">&#x21bb;</span> Atualizar
    </button>
  </div>
</header>

<!-- 2. SEGUNDA BARRA HORIZONTAL: ABAS CORPORATIVAS AGRUPADAS POR DOMÍNIO -->
<nav class="subnav-container">
  <ul class="subnav-tabs">
    <!-- Grupo 1: Financeiro -->
    <li>
      <button class="tab-btn active" data-tab="tab-financeiro" title="Painel Financeiro Consolidado">
        <span class="tab-icon">&#x1f4ca;</span>
        <span>Painel Financeiro</span>
      </button>
    </li>
    <li>
      <button class="tab-btn" data-tab="tab-custos-diarios" title="Custos Diários Operacionais">
        <span class="tab-icon">&#x1f4b5;</span>
        <span>Custos Diários</span>
        <span class="tab-badge badge-info" id="badgeCustosCount"></span>
      </button>
    </li>
    <li>
      <button class="tab-btn" data-tab="tab-rateio" title="Rateio &amp; Baseline Temporal">
        <span class="tab-icon">&#x1f4c8;</span>
        <span>Rateio &amp; Baseline</span>
      </button>
    </li>

    <li class="tab-group-divider" aria-hidden="true"></li>

    <!-- Grupo 2: Operação -->
    <li>
      <button class="tab-btn" data-tab="tab-timesheet" title="Timesheet e Apontamento de Mão de Obra">
        <span class="tab-icon">&#x23f1;&#xfe0f;</span>
        <span>Timesheet</span>
      </button>
    </li>
    <li>
      <button class="tab-btn" data-tab="tab-evm" title="Curva S e Análise de Valor Agregado (EVM)">
        <span class="tab-icon">&#x1f4c9;</span>
        <span>Curva S &amp; EVM</span>
      </button>
    </li>

    <li class="tab-group-divider" aria-hidden="true"></li>

    <!-- Grupo 3: Governança -->
    <li>
      <button class="tab-btn" data-tab="tab-risks" title="Matriz de Riscos 4x4 e Exposição">
        <span class="tab-icon">&#x26a0;&#xfe0f;</span>
        <span>Matriz de Riscos</span>
        <span class="tab-badge badge-danger" id="badgeRiscos"></span>
      </button>
    </li>
    <li>
      <button class="tab-btn" data-tab="tab-contracts" title="Gestão de Contratos, Fornecedores e Glosas">
        <span class="tab-icon">&#x1f4dd;</span>
        <span>Contratos &amp; Glosas</span>
        <span class="tab-badge badge-warn" id="badgeSlas"></span>
      </button>
    </li>

    <li class="tab-group-divider" aria-hidden="true"></li>

    <!-- Grupo 4: Encerramento -->
    <li>
      <button class="tab-btn" data-tab="tab-raci" title="Matriz RACI e Partes Interessadas">
        <span class="tab-icon">&#x1f465;</span>
        <span>Partes Interessadas</span>
        <span class="tab-badge" id="badgeStakeholders"></span>
      </button>
    </li>
    <li>
      <button class="tab-btn" data-tab="tab-closure" title="Checklist de Encerramento e Lições Aprendidas">
        <span class="tab-icon">&#x1f393;</span>
        <span>Lições Aprendidas</span>
      </button>
    </li>
  </ul>
</nav>

<main>

  <!-- ========================================== -->
  <!-- ABA 1: CURVA S & EVM                       -->
  <!-- ========================================== -->
  <div id="tab-evm" class="tab-pane">
    <div class="kpi-grid">
      <div class="kpi-card brand">
        <div class="kpi-label">BAC (Orçamento Total)</div>
        <div class="kpi-value" id="kpiBac">R$ 0,00</div>
        <div class="kpi-sub">Baseline Aprovada</div>
      </div>
      <div class="kpi-card info">
        <div class="kpi-label">PV (Valor Planejado)</div>
        <div class="kpi-value" id="kpiPv">R$ 0,00</div>
        <div class="kpi-sub">Cronograma Esperado</div>
      </div>
      <div class="kpi-card ok">
        <div class="kpi-label">EV (Valor Agregado)</div>
        <div class="kpi-value" id="kpiEv">R$ 0,00</div>
        <div class="kpi-sub" id="txtAvançoFisico">0% avanço físico</div>
      </div>
      <div class="kpi-card danger">
        <div class="kpi-label">AC (Custo Real)</div>
        <div class="kpi-value" id="kpiAc">R$ 0,00</div>
        <div class="kpi-sub">Despesas e Mão de Obra</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">CPI (Índice de Custo)</div>
        <div class="kpi-value" id="kpiCpi">—</div>
        <div class="kpi-sub" id="kpiCpiDesc">Eficiência Financeira</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">SPI (Índice de Prazo)</div>
        <div class="kpi-value" id="kpiSpi">—</div>
        <div class="kpi-sub" id="kpiSpiDesc">Aderência ao Cronograma</div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Curva S — Confronto de Execução Orçamentária e Física</h3>
          <div class="panel-desc">Planned Value (PV) vs Earned Value (EV) vs Actual Cost (AC)</div>
        </div>
      </div>
      <div class="curve-bar-group">
        <div class="curve-row">
          <span><b>Planejado (PV)</b></span>
          <div class="curve-track"><div id="barPv" class="curve-fill fill-pv" style="width: 0%"></div></div>
          <span class="num" id="txtPv">R$ 0,00</span>
        </div>
        <div class="curve-row">
          <span><b>Agregado (EV)</b></span>
          <div class="curve-track"><div id="barEv" class="curve-fill fill-ev" style="width: 0%"></div></div>
          <span class="num" id="txtEv">R$ 0,00</span>
        </div>
        <div class="curve-row">
          <span><b>Realizado (AC)</b></span>
          <div class="curve-track"><div id="barAc" class="curve-fill fill-ac" style="width: 0%"></div></div>
          <span class="num" id="txtAc">R$ 0,00</span>
        </div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Entregas e Marcos Físicos Ponderados</h3>
          <div class="panel-desc">Composição do avanço físico real somando 100% dos pesos</div>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Marco / Entrega</th>
              <th>Prazo Previsto</th>
              <th class="num">Peso (%)</th>
              <th>Situação</th>
            </tr>
          </thead>
          <tbody id="tbMarcos">
            <tr><td colspan="4" style="text-align:center;color:var(--muted)">Carregando entregas...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- ABA 2: MATRIZ DE RISCOS 4x4                -->
  <!-- ========================================== -->
  <div id="tab-risks" class="tab-pane">
    <div class="kpi-grid">
      <div class="kpi-card warn">
        <div class="kpi-label">Riscos Ativos</div>
        <div class="kpi-value" id="kpiRiscosAtivos">0</div>
        <div class="kpi-sub">Em monitoramento / materializados</div>
      </div>
      <div class="kpi-card danger">
        <div class="kpi-label">Exposição Financeira</div>
        <div class="kpi-value" id="kpiExpCusto">R$ 0,00</div>
        <div class="kpi-sub">Impacto estimado em custo</div>
      </div>
      <div class="kpi-card info">
        <div class="kpi-label">Exposição em Prazo</div>
        <div class="kpi-value" id="kpiExpDias">0 dias</div>
        <div class="kpi-sub">Impacto potencial no cronograma</div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Matriz de Probabilidade × Impacto (Grade 4x4)</h3>
          <div class="panel-desc">Distribuição de severidade: Verde (Baixo) · Amarelo (Médio) · Laranja (Alto) · Vermelho (Crítico)</div>
        </div>
      </div>
      <div class="matrix-grid">
        <div class="m-header"></div>
        <div class="m-header">Baixo (1)</div>
        <div class="m-header">Médio (2)</div>
        <div class="m-header">Alto (3)</div>
        <div class="m-header">Crítico (4)</div>

        <div class="m-label">Crítica (4)</div>
        <div class="m-cell" id="c-Critica-Baixo"></div>
        <div class="m-cell" id="c-Critica-Medio"></div>
        <div class="m-cell" id="c-Critica-Alto"></div>
        <div class="m-cell" id="c-Critica-Critico"></div>

        <div class="m-label">Alta (3)</div>
        <div class="m-cell" id="c-Alta-Baixo"></div>
        <div class="m-cell" id="c-Alta-Medio"></div>
        <div class="m-cell" id="c-Alta-Alto"></div>
        <div class="m-cell" id="c-Alta-Critico"></div>

        <div class="m-label">Média (2)</div>
        <div class="m-cell" id="c-Media-Baixo"></div>
        <div class="m-cell" id="c-Media-Medio"></div>
        <div class="m-cell" id="c-Media-Alto"></div>
        <div class="m-cell" id="c-Media-Critico"></div>

        <div class="m-label">Baixa (1)</div>
        <div class="m-cell" id="c-Baixa-Baixo"></div>
        <div class="m-cell" id="c-Baixa-Medio"></div>
        <div class="m-cell" id="c-Baixa-Alto"></div>
        <div class="m-cell" id="c-Baixa-Critico"></div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Registro Detalhado de Riscos e Planos de Resposta</h3>
          <div class="panel-desc">Ações preventivas de mitigação e planos de contingência</div>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Risco / Categoria</th>
              <th>Probabilidade / Impacto</th>
              <th>Plano de Mitigação</th>
              <th>Plano de Contingência</th>
              <th class="num">Impacto Custo</th>
              <th class="num">Prazo</th>
              <th>Situação</th>
            </tr>
          </thead>
          <tbody id="tbRiscos">
            <tr><td colspan="7" style="text-align:center;color:var(--muted)">Carregando riscos...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- ABA 3: CONTRATOS, SLAS E GLOSAS (4.2)      -->
  <!-- ========================================== -->
  <div id="tab-contracts" class="tab-pane">
    <div class="kpi-grid">
      <div class="kpi-card info">
        <div class="kpi-label">Contratos Monitorados</div>
        <div class="kpi-value" id="kpiSlasCount">0</div>
        <div class="kpi-sub">Níveis de Serviço vinculados</div>
      </div>
      <div class="kpi-card ok">
        <div class="kpi-label">Disponibilidade Média</div>
        <div class="kpi-value" id="kpiSlaDisp">0%</div>
        <div class="kpi-sub">Apurada nos terceiros</div>
      </div>
      <div class="kpi-card danger">
        <div class="kpi-label">Glosas Aplicadas</div>
        <div class="kpi-value" id="kpiSlaGlosas">R$ 0,00</div>
        <div class="kpi-sub">Penalidades por quebra de SLA</div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Acordos de Nível de Serviço (SLAs de Terceiros) e Apuração de Glosas</h3>
          <div class="panel-desc">Metas de disponibilidade contratual vs apuração real e cálculo financeiro de penalidades</div>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Contrato / Serviço</th>
              <th>Tipo Terceirizado</th>
              <th>Meta Disp. (%)</th>
              <th>Apurada (%)</th>
              <th class="num">Fatura Mensal</th>
              <th class="num">Glosa (R$)</th>
              <th>Conformidade</th>
              <th>Situação Auditoria</th>
            </tr>
          </thead>
          <tbody id="tbContratos">
            <tr><td colspan="8" style="text-align:center;color:var(--muted)">Carregando acordos de contratos...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- ABA 4: MATRIZ RACI & STAKEHOLDERS (3.9)    -->
  <!-- ========================================== -->
  <div id="tab-raci" class="tab-pane">
    <div class="kpi-grid">
      <div class="kpi-card info">
        <div class="kpi-label">Responsáveis (R)</div>
        <div class="kpi-value" id="kpiRaciR">0</div>
        <div class="kpi-sub">Executores diretos das entregas</div>
      </div>
      <div class="kpi-card ok">
        <div class="kpi-label">Aprovadores (A)</div>
        <div class="kpi-value" id="kpiRaciA">0</div>
        <div class="kpi-sub">Alçadas finais de aprovação</div>
      </div>
      <div class="kpi-card warn">
        <div class="kpi-label">Consultados (C)</div>
        <div class="kpi-value" id="kpiRaciC">0</div>
        <div class="kpi-sub">Especialistas e áreas de apoio</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Informados (I)</div>
        <div class="kpi-value" id="kpiRaciI">0</div>
        <div class="kpi-sub">Notificados de status e janelas</div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Mapeamento de Partes Interessadas e Matriz RACI</h3>
          <div class="panel-desc">Matriz de papéis, responsabilidades, poder de influência e clima de engajamento</div>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Nome da Parte Interessada</th>
              <th>Cargo / Papel no Projeto</th>
              <th>Área / Organização</th>
              <th>Papel RACI</th>
              <th>Poder Influência</th>
              <th>Engajamento</th>
              <th>E-mail</th>
              <th>Expectativas e Comunicação</th>
            </tr>
          </thead>
          <tbody id="tbRaci">
            <tr><td colspan="8" style="text-align:center;color:var(--muted)">Carregando partes interessadas...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- ABA 5: ENCERRAMENTO & LIÇÕES (4.1)         -->
  <!-- ========================================== -->
  <div id="tab-closure" class="tab-pane">
    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Checklist Formal de Encerramento e Transição para Service Desk</h3>
          <div class="panel-desc">Critérios de transição operacional, homologação pelo patrocinador e garantia</div>
        </div>
        <div id="badgeClosureStatus"></div>
      </div>

      <div class="checklist-grid">
        <div class="check-card">
          <div class="check-icon" id="chkHandoverIcon">⏳</div>
          <div>
            <div class="check-title">Transição Operacional (Handover)</div>
            <div class="check-val" id="chkHandoverVal">Pendente</div>
            <small style="color:var(--muted)">Transferência de suporte para o N1/N2</small>
          </div>
        </div>
        <div class="check-card">
          <div class="check-icon" id="chkDocIcon">⏳</div>
          <div>
            <div class="check-title">Documentação Técnica Entregue</div>
            <div class="check-val" id="chkDocVal">Pendente</div>
            <small style="color:var(--muted)">Topologias, manuais e procedimentos</small>
          </div>
        </div>
        <div class="check-card">
          <div class="check-icon" id="chkTrainingIcon">⏳</div>
          <div>
            <div class="check-title">Treinamento e Capacitação</div>
            <div class="check-val" id="chkTrainingVal">Pendente</div>
            <small style="color:var(--muted)">Repasse técnico para os operadores</small>
          </div>
        </div>
        <div class="check-card">
          <div class="check-icon">🛡️</div>
          <div>
            <div class="check-title">Garantia / Suporte Pós-Projeto</div>
            <div class="check-val" id="chkWarrantyVal">Não estipulada</div>
            <small style="color:var(--muted)">Cobertura e sustentação assistida</small>
          </div>
        </div>
      </div>

      <div style="background:var(--surface-alt); padding:16px; border-radius:var(--radius-sm); border:1px solid var(--line);">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
          <span><b>Patrocinador / Aprovador Formal:</b> <span id="txtSponsorApprover">—</span></span>
        </div>
        <div>
          <b>Parecer de Homologação:</b> <span id="txtClosureReason">—</span>
        </div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Resumo dos Resultados e Objetivos Alcançados</h3>
          <div class="panel-desc">Entrega de valor do projeto para a organização</div>
        </div>
      </div>
      <div class="lessons-box" id="boxSuccessSummary">
        Nenhum resumo de resultados cadastrado.
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Repositório de Lições Aprendidas</h3>
          <div class="panel-desc">Práticas que funcionaram bem, pontos de melhoria e recomendações para futuros projetos</div>
        </div>
      </div>
      <div class="lessons-box" id="boxLessonsLearned" style="border-left-color:var(--info);">
        Nenhuma lição aprendida registrada no encerramento deste projeto.
      </div>
    </div>
  </div>


  <!-- ========================================== -->
  <!-- PRIORIDADE 4: PAINEL FINANCEIRO EXECUTIVO  -->
  <!-- ========================================== -->
  <div id="tab-financeiro" class="tab-pane active">
    <div class="kpi-grid">
      <div class="kpi-card brand">
        <div class="kpi-label">Orçamento Aprovado (BAC)</div>
        <div class="kpi-value" id="finKpiBac">R$ 0,00</div>
        <div class="kpi-sub">Baseline Original Aprovada</div>
      </div>
      <div class="kpi-card danger">
        <div class="kpi-label">Custo Real Total (AC)</div>
        <div class="kpi-value" id="finKpiRealizado">R$ 0,00</div>
        <div class="kpi-sub" id="finKpiRealizadoSub">Despesas + Mão de Obra</div>
      </div>
      <div class="kpi-card warn">
        <div class="kpi-label">Comprometido (Pedidos ERP)</div>
        <div class="kpi-value" id="finKpiComprometido">R$ 0,00</div>
        <div class="kpi-sub">Reservas & Pedidos Abertos</div>
      </div>
      <div class="kpi-card ok">
        <div class="kpi-label">Saldo Disponível</div>
        <div class="kpi-value" id="finKpiSaldo">R$ 0,00</div>
        <div class="kpi-sub" id="finKpiConsumo">0% consumido</div>
      </div>
      <div class="kpi-card info">
        <div class="kpi-label">Estimativa no Término (EAC)</div>
        <div class="kpi-value" id="finKpiEac">R$ 0,00</div>
        <div class="kpi-sub" id="finKpiVac">Variação: R$ 0,00</div>
      </div>
    </div>

    <!-- Banner de Alertas Orçamentários 80%, 90% e 100% -->
    <div id="finAlertBanner" style="display:none; padding:12px 18px; border-radius:var(--radius-sm); font-size:13px; font-weight:600;"></div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
      <div class="panel">
        <div class="panel-head">
          <div>
            <h3 class="panel-title">Distribuição por Categoria de Custo</h3>
            <div class="panel-desc">Apropriação contábil por especialidade técnica</div>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>Categoria</th><th class="num">Valor Realizado</th><th class="num">% Total</th></tr>
            </thead>
            <tbody id="tbFinCategories"></tbody>
          </table>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head">
          <div>
            <h3 class="panel-title">Natureza Financeira: CAPEX vs OPEX</h3>
            <div class="panel-desc">Investimento patrimonial vs Custo operacional de sustentação</div>
          </div>
        </div>
        <div style="display:flex; flex-direction:column; gap:16px; padding:10px 0;">
          <div>
            <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:600; margin-bottom:4px;">
              <span>CAPEX (Ativo Imobilizado / Hardware)</span>
              <span id="txtCapexVal">R$ 0,00</span>
            </div>
            <div class="curve-track"><div id="barCapex" class="curve-fill fill-pv" style="width:0%"></div></div>
          </div>
          <div>
            <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:600; margin-bottom:4px;">
              <span>OPEX (Serviços / Manutenção / Nuvem)</span>
              <span id="txtOpexVal">R$ 0,00</span>
            </div>
            <div class="curve-track"><div id="barOpex" class="curve-fill fill-ac" style="width:0%"></div></div>
          </div>
          <div>
            <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:600; margin-bottom:4px;">
              <span>Mão de Obra Técnica (Timesheet Aprovado)</span>
              <span id="txtLaborVal">R$ 0,00</span>
            </div>
            <div class="curve-track"><div id="barLabor" class="curve-fill fill-ev" style="width:0%"></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- PRIORIDADE 1: CUSTOS DIÁRIOS OPERACIONAIS  -->
  <!-- ========================================== -->
  <div id="tab-custos-diarios" class="tab-pane">
    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Lançamento e Rastreamento Operacional de Custos Diários</h3>
          <div class="panel-desc">Substituição da experiência genérica: controle diário com impostos, alçadas e conciliação ERP Sankhya</div>
        </div>
        <div style="display:flex; gap:8px;">
          <button class="btn btn-primary" onclick="alert('Funcionalidade de inclusão em lote / cópia entre dias disponível via integração.')">➕ Novo Lançamento</button>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Competência</th>
              <th>Categoria / Subcategoria</th>
              <th>Natureza</th>
              <th>Fornecedor</th>
              <th>Qtd x Unitário</th>
              <th class="num">Total (c/ Impostos)</th>
              <th>Origem</th>
              <th>Documento / ERP</th>
              <th>Situação</th>
              <th>Responsável / Aprovador</th>
            </tr>
          </thead>
          <tbody id="tbDailyCosts">
            <tr><td colspan="10" style="text-align:center;color:var(--muted)">Carregando custos diários...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- PRIORIDADE 2: RATEIO AUTOMÁTICO E BASELINE -->
  <!-- ========================================== -->
  <div id="tab-rateio" class="tab-pane">
    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Distribuição Temporal e Planejamento do Valor Total do Projeto</h3>
          <div class="panel-desc">Rateio automático com ajuste de saldo residual no último período (Baseline Versionada)</div>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
          <span class="tag t-ok" style="font-size:12px;">Baseline v1.0 Aprovada</span>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Competência / Período</th>
              <th>Regra de Distribuição</th>
              <th class="num">Peso (%)</th>
              <th class="num">Valor Planejado (R$)</th>
              <th class="num">Valor Revisado (R$)</th>
              <th>Premissas & Entregas da Parcela</th>
            </tr>
          </thead>
          <tbody id="tbSchedules">
            <tr><td colspan="6" style="text-align:center;color:var(--muted)">Carregando cronograma temporal de rateio...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- PRIORIDADE 3: TIMESHEET E MÃO DE OBRA REAL -->
  <!-- ========================================== -->
  <div id="tab-timesheet" class="tab-pane">
    <div class="panel">
      <div class="panel-head">
        <div>
          <h3 class="panel-title">Timesheet e Apropriação Real de Mão de Obra</h3>
          <div class="panel-desc">Apontamento por colaborador, tarefa e dia com custo-hora vigente e segregação interno / terceiro</div>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Data</th>
              <th>Colaborador / Função</th>
              <th>Tipo</th>
              <th>Tarefa / Atividade</th>
              <th class="num">Horas Apontadas</th>
              <th class="num">Horas Aprovadas</th>
              <th class="num">Taxa Horária (R$)</th>
              <th class="num">Custo Real (R$)</th>
              <th>Situação Aprovação</th>
            </tr>
          </thead>
          <tbody id="tbTimesheet">
            <tr><td colspan="9" style="text-align:center;color:var(--muted)">Carregando apontamentos de mão de obra...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</main>

<script>
let currentProjects = [];
let currentReport = null;

const brl = v => (Number(v) || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
const pct = v => `${Math.round((Number(v) || 0) * 100)}%`;

// Gerenciamento de Abas
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.onclick = () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    const target = document.getElementById(btn.getAttribute('data-tab'));
    if (target) target.classList.add('active');
  };
});

let allProjectsData = null;

async function loadProjects() {
  const sel = document.getElementById('selProj');
  try {
    let res = await fetch('/plugins/flowitwhite/front/projects_data.json');
    if (!res.ok) {
      res = await fetch('/plugins/flowitwhite/front/projetos_api.php?action=all');
    }
    if (!res.ok) {
      res = await fetch('/api/itsm/projects');
    }
    if (!res.ok) throw new Error('Falha ao listar projetos.');

    const data = await res.json();
    if (data.projects && data.reports) {
      allProjectsData = data;
      currentProjects = data.projects;
    } else if (Array.isArray(data)) {
      currentProjects = data;
    }

    sel.innerHTML = currentProjects.map(p => `<option value="${p.id}">${p.title} (${p.status})</option>`).join('');
    if (currentProjects.length > 0) {
      loadProjectReport(currentProjects[0].id);
    }
  } catch (err) {
    console.error('Erro ao carregar projetos:', err);
    sel.innerHTML = '<option value="">Erro ao carregar projetos</option>';
  }
}

async function loadProjectReport(id) {
  try {
    if (allProjectsData && allProjectsData.reports && allProjectsData.reports[id]) {
      currentReport = allProjectsData.reports[id];
      render();
      loadFinancialData(id);
      return;
    }

    let res = await fetch(`/plugins/flowitwhite/front/projetos_api.php?action=report&id=${encodeURIComponent(id)}`);
    if (!res.ok) {
      res = await fetch(`/api/projects/${id}/report`);
    }
    if (!res.ok) throw new Error('Erro ao carregar dados do projeto.');
    currentReport = await res.json();
    render();
    loadFinancialData(id);
  } catch (err) {
    console.error('Erro ao carregar relatório do projeto:', err);
  }
}

function render() {
  if (!currentReport) return;
  const { project, metrics, milestones, risks, riskExposure, stakeholders, raciSummary, closure, contractSlas, supplierPerformance } = currentReport;

  // Atualizar Badges de Abas (Com ocultação automática quando zero)
  const setBadgeVal = (id, count) => {
    const el = document.getElementById(id);
    if (!el) return;
    const num = Number(count) || 0;
    el.setAttribute('data-count', num);
    el.textContent = num > 0 ? num : '';
  };

  const activeRisksCount = (risks || []).filter(r => !['Mitigado','Cancelado'].includes(r.status)).length;
  setBadgeVal('badgeRiscos', activeRisksCount);
  setBadgeVal('badgeSlas', (contractSlas || []).length);
  setBadgeVal('badgeStakeholders', (stakeholders || []).length);

  // ==========================================
  // ABA 1: CURVA S & EVM
  // ==========================================
  document.getElementById('kpiBac').textContent = brl(metrics.budget);
  document.getElementById('kpiPv').textContent = brl(metrics.pv);
  document.getElementById('kpiEv').textContent = brl(metrics.ev);
  document.getElementById('kpiAc').textContent = brl(metrics.ac);
  document.getElementById('txtAvançoFisico').textContent = `${metrics.progress || 0}% de avanço físico apurado`;

  const cpi = metrics.cpi;
  const spi = metrics.spi;
  document.getElementById('kpiCpi').textContent = cpi !== null ? cpi.toFixed(2) : '—';
  document.getElementById('kpiCpi').style.color = cpi >= 1 ? 'var(--ok)' : 'var(--danger)';
  document.getElementById('kpiCpiDesc').textContent = cpi >= 1 ? 'Favorável (Abaixo do orçamento)' : 'Desfavorável (Sobrecusto)';

  document.getElementById('kpiSpi').textContent = spi !== null ? spi.toFixed(2) : '—';
  document.getElementById('kpiSpi').style.color = spi >= 1 ? 'var(--ok)' : 'var(--warn)';
  document.getElementById('kpiSpiDesc').textContent = spi >= 1 ? 'No prazo ou adiantado' : 'Atraso de cronograma';

  const maxV = Math.max(metrics.budget, metrics.pv, metrics.ev, metrics.ac, 1);
  document.getElementById('txtPv').textContent = `${brl(metrics.pv)} (${pct(metrics.pv/maxV)})`;
  document.getElementById('barPv').style.width = `${Math.min((metrics.pv/maxV)*100, 100)}%`;
  
  document.getElementById('txtEv').textContent = `${brl(metrics.ev)} (${pct(metrics.ev/maxV)})`;
  document.getElementById('barEv').style.width = `${Math.min((metrics.ev/maxV)*100, 100)}%`;

  document.getElementById('txtAc').textContent = `${brl(metrics.ac)} (${pct(metrics.ac/maxV)})`;
  document.getElementById('barAc').style.width = `${Math.min((metrics.ac/maxV)*100, 100)}%`;

  const tbM = document.getElementById('tbMarcos');
  if (milestones && milestones.length) {
    tbM.innerHTML = milestones.map(m => `
      <tr>
        <td><b>${m.title}</b></td>
        <td>${m.dueDate ? m.dueDate.split('-').reverse().join('/') : '—'}</td>
        <td class="num">${m.weight}%</td>
        <td><span class="tag ${m.status === 'Concluído' ? 't-ok' : 't-warn'}">${m.status}</span></td>
      </tr>
    `).join('');
  } else {
    tbM.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--muted)">Nenhum marco cadastrado.</td></tr>';
  }

  // ==========================================
  // ABA 2: RISCOS 4x4
  // ==========================================
  const activeRisks = (risks || []).filter(r => !['Mitigado', 'Cancelado'].includes(r.status));
  document.getElementById('kpiRiscosAtivos').textContent = activeRisks.length;
  document.getElementById('kpiExpCusto').textContent = brl(riskExposure?.costExposure || 0);
  document.getElementById('kpiExpDias').textContent = `${riskExposure?.daysExposure || 0} dias`;

  const probs = ['Critica', 'Alta', 'Media', 'Baixa'];
  const imps = ['Baixo', 'Medio', 'Alto', 'Critico'];
  const mapProb = { 'Crítica': 'Critica', 'Alta': 'Alta', 'Média': 'Media', 'Baixa': 'Baixa' };
  const mapImp = { 'Baixo': 'Baixo', 'Médio': 'Medio', 'Alto': 'Alto', 'Crítico': 'Critico' };

  probs.forEach(p => {
    imps.forEach(i => {
      const cell = document.getElementById(`c-${p}-${i}`);
      if (cell) {
        cell.innerHTML = '<span style="color:#cbd5e1;font-size:11px">—</span>';
        cell.style.background = '#fff';
        cell.style.borderColor = '#e2e8f0';
      }
    });
  });

  activeRisks.forEach(r => {
    const pKey = mapProb[r.probability] || 'Baixa';
    const iKey = mapImp[r.impact] || 'Baixo';
    const cell = document.getElementById(`c-${pKey}-${iKey}`);
    if (cell) {
      const pScore = { Baixa: 1, Media: 2, Alta: 3, Critica: 4 }[pKey] || 1;
      const iScore = { Baixo: 1, Medio: 2, Alto: 3, Critico: 4 }[iKey] || 1;
      const prod = pScore * iScore;
      
      let bg = '#fee2e2', border = '#fca5a5', badgeBg = '#ef4444';
      if (prod < 3) { bg = '#f0fdf4'; border = '#bbf7d0'; badgeBg = '#138a66'; }
      else if (prod < 6) { bg = '#fefce8'; border = '#fde047'; badgeBg = '#b45309'; }
      else if (prod < 12) { bg = '#ffedd5'; border = '#fdba74'; badgeBg = '#ea580c'; }

      cell.style.background = bg;
      cell.style.borderColor = border;
      cell.innerHTML = `<span class="m-badge" style="background:${badgeBg}">${r.title.slice(0, 18)}...</span>`;
    }
  });

  const tbR = document.getElementById('tbRiscos');
  if (risks && risks.length) {
    tbR.innerHTML = risks.map(r => `
      <tr>
        <td><b>${r.title}</b><br><small style="color:var(--muted)">${r.category || 'Geral'}</small></td>
        <td>${r.probability} / ${r.impact}</td>
        <td>${r.mitigationPlan || '—'}</td>
        <td>${r.contingencyPlan || '—'}</td>
        <td class="num">${brl(r.costImpact)}</td>
        <td class="num">${r.scheduleImpactDays || 0}d</td>
        <td><span class="tag ${r.status === 'Mitigado' ? 't-ok' : r.status === 'Materializado' ? 't-bad' : 't-warn'}">${r.status}</span></td>
      </tr>
    `).join('');
  } else {
    tbR.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--muted)">Nenhum risco cadastrado.</td></tr>';
  }

  // ==========================================
  // ABA 3: CONTRATOS, SLAS & GLOSAS (4.2)
  // ==========================================
  const slas = contractSlas || [];
  document.getElementById('kpiSlasCount').textContent = supplierPerformance?.totalMonitored || slas.length;
  document.getElementById('kpiSlaDisp').textContent = supplierPerformance?.avgAvailability !== null && supplierPerformance?.avgAvailability !== undefined ? `${supplierPerformance.avgAvailability}%` : '—';
  document.getElementById('kpiSlaGlosas').textContent = brl(supplierPerformance?.totalPenaltyAmount || 0);

  const tbC = document.getElementById('tbContratos');
  if (slas && slas.length) {
    tbC.innerHTML = slas.map(s => `
      <tr>
        <td><b>${s.title}</b><br><small style="color:var(--muted)">${s.supplierName || 'Fornecedor'}</small></td>
        <td>${s.serviceType || 'Geral'}</td>
        <td>${s.targetAvailabilityPercent ? s.targetAvailabilityPercent + '%' : '—'}</td>
        <td><b>${s.measuredAvailabilityPercent ? s.measuredAvailabilityPercent + '%' : '—'}</b></td>
        <td class="num">${brl(s.monthlyContractValue)}</td>
        <td class="num" style="color:${Number(s.penaltyAmount) > 0 ? 'var(--danger)' : 'inherit'};font-weight:${Number(s.penaltyAmount) > 0 ? '700' : 'normal'}">${brl(s.penaltyAmount)}</td>
        <td><span class="tag ${s.complianceStatus === 'Conforme' ? 't-ok' : s.complianceStatus === 'Gravemente violado' ? 't-bad' : 't-warn'}">${s.complianceStatus || 'Em apuração'}</span></td>
        <td><span class="tag ${s.status === 'Glosa aplicada' ? 't-bad' : s.status === 'Auditado' ? 't-ok' : 't-info'}">${s.status}</span></td>
      </tr>
    `).join('');
  } else {
    tbC.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--muted)">Nenhum contrato ou SLA cadastrado.</td></tr>';
  }

  // ==========================================
  // ABA 4: MATRIZ RACI & STAKEHOLDERS (3.9)
  // ==========================================
  document.getElementById('kpiRaciR').textContent = raciSummary?.responsible || 0;
  document.getElementById('kpiRaciA').textContent = raciSummary?.accountable || 0;
  document.getElementById('kpiRaciC').textContent = raciSummary?.consulted || 0;
  document.getElementById('kpiRaciI').textContent = raciSummary?.informed || 0;

  const tbStk = document.getElementById('tbRaci');
  if (stakeholders && stakeholders.length) {
    tbStk.innerHTML = stakeholders.map(s => {
      let rClass = 'raci-c';
      if (s.raci?.startsWith('Responsável')) rClass = 'raci-r';
      else if (s.raci?.startsWith('Aprovador')) rClass = 'raci-a';
      else if (s.raci?.startsWith('Informado')) rClass = 'raci-i';

      return `
        <tr>
          <td><b>${s.stakeholderName || s.title}</b></td>
          <td>${s.roleTitle || '—'}</td>
          <td>${s.organization || 'Interna'}</td>
          <td><span class="tag ${rClass}">${s.raci || 'Consultado (C)'}</span></td>
          <td>${s.influencePower || 'Médio'}</td>
          <td><span class="tag ${s.engagementLevel?.includes('Alto') ? 't-ok' : s.engagementLevel?.includes('Crítico') ? 't-bad' : 't-info'}">${s.engagementLevel || 'Neutro'}</span></td>
          <td><a href="mailto:${s.contactEmail}" style="color:var(--info);text-decoration:none">${s.contactEmail || '—'}</a></td>
          <td><small>${s.notes || '—'}</small></td>
        </tr>
      `;
    }).join('');
  } else {
    tbStk.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--muted)">Nenhuma parte interessada cadastrada.</td></tr>';
  }

  // ==========================================
  // ABA 5: ENCERRAMENTO & LIÇÕES (4.1)
  // ==========================================
  const bdgClo = document.getElementById('badgeClosureStatus');
  if (closure) {
    bdgClo.innerHTML = `<span class="tag ${closure.status === 'Homologado' ? 't-ok' : 't-warn'}" style="font-size:13px;padding:4px 10px">${closure.status}</span>`;
    
    // Handover
    const isHandover = closure.operationalHandover === 'Concluída';
    document.getElementById('chkHandoverIcon').textContent = isHandover ? '✅' : '⏳';
    document.getElementById('chkHandoverVal').textContent = closure.operationalHandover || 'Pendente';
    document.getElementById('chkHandoverVal').style.color = isHandover ? 'var(--ok)' : 'var(--warn)';

    // Doc
    const isDoc = closure.documentationDelivered === 'Sim';
    document.getElementById('chkDocIcon').textContent = isDoc ? '✅' : '❌';
    document.getElementById('chkDocVal').textContent = isDoc ? 'Entregue e Validada' : 'Pendente';
    document.getElementById('chkDocVal').style.color = isDoc ? 'var(--ok)' : 'var(--danger)';

    // Treinamento
    const isTrain = closure.trainingCompleted === 'Sim';
    document.getElementById('chkTrainingIcon').textContent = isTrain ? '✅' : '⏳';
    document.getElementById('chkTrainingVal').textContent = isTrain ? 'Capacitação Concluída' : 'Pendente';
    document.getElementById('chkTrainingVal').style.color = isTrain ? 'var(--ok)' : 'var(--warn)';

    // Garantia
    document.getElementById('chkWarrantyVal').textContent = closure.warrantyUntil ? closure.warrantyUntil.split('-').reverse().join('/') : 'Não estipulada';

    // Textos
    document.getElementById('txtSponsorApprover').textContent = closure.sponsorApprover || 'Não designado';
    document.getElementById('txtClosureReason').textContent = closure.decisionReason || 'Sem parecer formal registrado.';
    document.getElementById('boxSuccessSummary').textContent = closure.successSummary || 'Sem resumo cadastrado.';
    document.getElementById('boxLessonsLearned').textContent = closure.lessonsLearned || 'Sem lições aprendidas registradas.';
  } else {
    bdgClo.innerHTML = `<span class="tag t-warn" style="font-size:13px;padding:4px 10px">Encerramento Não Iniciado</span>`;
    document.getElementById('chkHandoverIcon').textContent = '⏳';
    document.getElementById('chkHandoverVal').textContent = 'Pendente';
    document.getElementById('chkDocIcon').textContent = '⏳';
    document.getElementById('chkDocVal').textContent = 'Pendente';
    document.getElementById('chkTrainingIcon').textContent = '⏳';
    document.getElementById('chkTrainingVal').textContent = 'Pendente';
    document.getElementById('chkWarrantyVal').textContent = 'Não estipulada';
    document.getElementById('txtSponsorApprover').textContent = 'Aguardando encerramento';
    document.getElementById('txtClosureReason').textContent = 'O projeto está ativo na fase de execução.';
    document.getElementById('boxSuccessSummary').textContent = 'Resumo de encerramento será registrado após conclusão dos marcos físicos.';
    document.getElementById('boxLessonsLearned').textContent = 'Lições aprendidas serão homologadas na transição para operação.';
  }
}

document.getElementById('selProj').onchange = e => loadProjectReport(e.target.value);
document.getElementById('btnRecarregar').onclick = () => {
  const sel = document.getElementById('selProj');
  if (sel.value) loadProjectReport(sel.value);
};


async function loadFinancialData(projectId) {
  try {
    const res = await fetch(`/plugins/flowitwhite/front/financial_api.php?action=summary&project_id=${projectId}`);
    if (!res.ok) return;
    const data = await res.json();
    
    // KPIs Financeiros
    document.getElementById('finKpiBac').textContent = brl(data.plannedTotal);
    document.getElementById('finKpiRealizado').textContent = brl(data.totalRealizado);
    document.getElementById('finKpiRealizadoSub').textContent = `Despesas: ${brl(data.realizadoDespesas)} + Mão de Obra: ${brl(data.realizadoMaoDeObra)}`;
    document.getElementById('finKpiComprometido').textContent = brl(data.comprometido);
    document.getElementById('finKpiSaldo').textContent = brl(data.saldoDisponivel);
    document.getElementById('finKpiConsumo').textContent = `${data.consumoPercent}% do orçamento consumido`;
    document.getElementById('finKpiEac').textContent = brl(data.eac);
    document.getElementById('finKpiVac').textContent = `Variação no término: ${brl(data.vac)}`;

    // Alertas Orçamentários
    const b = document.getElementById('finAlertBanner');
    if (data.consumoPercent >= 100) {
      b.style.display = 'block';
      b.style.background = 'var(--danger-soft)';
      b.style.color = 'var(--danger)';
      b.style.border = '1px solid #fca5a5';
      b.textContent = '🚨 Alerta Crítico (100%): O projeto atingiu o limite total do orçamento aprovado!';
    } else if (data.consumoPercent >= 90) {
      b.style.display = 'block';
      b.style.background = '#ffedd5';
      b.style.color = '#c2410c';
      b.style.border = '1px solid #fdba74';
      b.textContent = '⚠️ Alerta de Consumo Elevado (90%): Mais de 90% do orçamento do projeto já foi comprometido/realizado.';
    } else if (data.consumoPercent >= 80) {
      b.style.display = 'block';
      b.style.background = 'var(--warn-soft)';
      b.style.color = 'var(--warn)';
      b.style.border = '1px solid #fde047';
      b.textContent = '⚡ Atenção (80%): Consumo orçamentário superior a 80%. Monitorar novas autorizações.';
    } else {
      b.style.display = 'none';
    }

    // Categorias
    const tbCat = document.getElementById('tbFinCategories');
    const cats = Object.entries(data.costByCategory || {});
    if (cats.length) {
      const tot = data.realizadoDespesas || 1;
      tbCat.innerHTML = cats.map(([k, v]) => `
        <tr>
          <td><b>${k}</b></td>
          <td class="num"><b>${brl(v)}</b></td>
          <td class="num">${Math.round((v / tot) * 100)}%</td>
        </tr>
      `).join('');
    }

    // Barras Natureza
    const totGeral = Math.max(data.totalRealizado, 1);
    const ca = data.costByNature?.CAPEX || 0;
    const op = data.costByNature?.OPEX || 0;
    const lab = data.realizadoMaoDeObra || 0;
    document.getElementById('txtCapexVal').textContent = `${brl(ca)} (${Math.round((ca/totGeral)*100)}%)`;
    document.getElementById('barCapex').style.width = `${Math.min((ca/totGeral)*100, 100)}%`;
    document.getElementById('txtOpexVal').textContent = `${brl(op)} (${Math.round((op/totGeral)*100)}%)`;
    document.getElementById('barOpex').style.width = `${Math.min((op/totGeral)*100, 100)}%`;
    document.getElementById('txtLaborVal').textContent = `${brl(lab)} (${Math.round((lab/totGeral)*100)}%)`;
    document.getElementById('barLabor').style.width = `${Math.min((lab/totGeral)*100, 100)}%`;

    // Carregar Tabelas Detalhadas
    loadDailyCostsTable(projectId);
    loadSchedulesTable(projectId);
    loadTimesheetTable(projectId);
  } catch(e) {
    console.error('Erro financeiro:', e);
  }
}

async function loadDailyCostsTable(projectId) {
  try {
    const res = await fetch(`/plugins/flowitwhite/front/financial_api.php?action=daily_costs&project_id=${projectId}`);
    if (!res.ok) return;
    const rows = await res.json();
    const count = (rows && rows.length) ? rows.length : 0;
    const badgeCustos = document.getElementById('badgeCustosCount');
    if (badgeCustos) {
      badgeCustos.setAttribute('data-count', count);
      badgeCustos.textContent = count > 0 ? count : '';
    }
    const tb = document.getElementById('tbDailyCosts');
    if (rows && rows.length) {
      tb.innerHTML = rows.map(r => `
        <tr>
          <td>${r.competence_date.split('-').reverse().join('/')}</td>
          <td><b>${r.category}</b><br><small style="color:var(--muted)">${r.subcategory || 'Geral'}</small></td>
          <td><span class="tag ${r.cost_nature === 'CAPEX' ? 't-info' : 't-ok'}">${r.cost_nature}</span></td>
          <td><b>${r.supplier}</b></td>
          <td>${Number(r.quantity).toFixed(0)} x ${brl(r.unit_value)}</td>
          <td class="num"><b>${brl(r.total_value)}</b></td>
          <td><span class="tag ${r.origin === 'sankhya' ? 't-info' : 't-warn'}">${r.origin === 'sankhya' ? 'ERP Sankhya' : r.origin}</span></td>
          <td>${r.erp_doc_num ? r.erp_doc_num + (r.nunota ? ' (NUNOTA ' + r.nunota + ')' : '') : '—'}</td>
          <td><span class="tag ${r.status === 'realizado' ? 't-ok' : r.status === 'comprometido' ? 't-warn' : 't-info'}">${r.status}</span></td>
          <td>${r.responsible} ${r.approver ? '<br><small style="color:var(--muted)">Aprovado: ' + r.approver + '</small>' : ''}</td>
        </tr>
      `).join('');
    } else {
      tb.innerHTML = '<tr><td colspan="10" style="text-align:center;color:var(--muted)">Nenhum custo lançado.</td></tr>';
    }
  } catch(e) {}
}

async function loadSchedulesTable(projectId) {
  try {
    const res = await fetch(`/plugins/flowitwhite/front/financial_api.php?action=schedules&project_id=${projectId}`);
    if (!res.ok) return;
    const rows = await res.json();
    const tb = document.getElementById('tbSchedules');
    if (rows && rows.length) {
      tb.innerHTML = rows.map(r => `
        <tr>
          <td><b>${r.period_label}</b></td>
          <td>${r.distribution_rule === 'uniform_monthly' ? 'Distribuição Mensal Uniforme' : r.distribution_rule}</td>
          <td class="num">${Number(r.weight_percent).toFixed(1)}%</td>
          <td class="num"><b>${brl(r.planned_value)}</b></td>
          <td class="num">${brl(r.revised_value)}</td>
          <td>${r.notes || '—'}</td>
        </tr>
      `).join('');
    } else {
      tb.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--muted)">Nenhuma parcela planejada.</td></tr>';
    }
  } catch(e) {}
}

async function loadTimesheetTable(projectId) {
  try {
    const res = await fetch(`/plugins/flowitwhite/front/financial_api.php?action=timesheets&project_id=${projectId}`);
    if (!res.ok) return;
    const rows = await res.json();
    const tb = document.getElementById('tbTimesheet');
    if (rows && rows.length) {
      tb.innerHTML = rows.map(r => `
        <tr>
          <td>${r.work_date.split('-').reverse().join('/')}</td>
          <td><b>${r.collaborator_name}</b><br><small style="color:var(--muted)">${r.collaborator_role || 'Técnico'}</small></td>
          <td><span class="tag ${r.collaborator_type === 'interno' ? 't-info' : 't-warn'}">${r.collaborator_type}</span></td>
          <td>${r.task_name}</td>
          <td class="num">${Number(r.logged_hours).toFixed(1)}h</td>
          <td class="num"><b>${Number(r.approved_hours).toFixed(1)}h</b></td>
          <td class="num">${brl(r.hourly_rate)}/h</td>
          <td class="num" style="color:var(--ok);font-weight:700;">${brl(r.effective_cost)}</td>
          <td><span class="tag ${r.approval_status === 'aprovado' ? 't-ok' : 't-warn'}">${r.approval_status}</span></td>
        </tr>
      `).join('');
    } else {
      tb.innerHTML = '<tr><td colspan="9" style="text-align:center;color:var(--muted)">Nenhum apontamento registrado.</td></tr>';
    }
  } catch(e) {}
}

loadProjects();
</script>
</body>
</html>
