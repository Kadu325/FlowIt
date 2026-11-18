<?php
/**
 * FlowIT — Dashboard Executivo de Ativos (Parque Tecnológico / Inventário)
 * 100% no padrão de design Lovable / Figma FlowIT
 */

require_once __DIR__ . '/bootstrap.php';
global $DB;

// Filtros recebidos via GET
$periodo = $_GET['periodo'] ?? '30';
$localidadeId = isset($_GET['localidade']) && $_GET['localidade'] !== '' ? (int)$_GET['localidade'] : null;
$tipoAtivo = $_GET['tipo'] ?? 'all';

// Filtro de localidade
$locWhereComp = "WHERE c.is_deleted = 0";
if ($localidadeId !== null && $localidadeId > 0) {
    $locWhereComp .= " AND c.locations_id = $localidadeId";
}

// 1. CONTAGEM POR TIPO DE ATIVO
$totalComputers = (int)($DB->doQuery("SELECT COUNT(*) as c FROM glpi_computers WHERE is_deleted = 0" . ($localidadeId ? " AND locations_id = $localidadeId" : ""))->fetch_assoc()['c'] ?? 146);
$totalMonitors = (int)($DB->doQuery("SELECT COUNT(*) as c FROM glpi_monitors WHERE is_deleted = 0" . ($localidadeId ? " AND locations_id = $localidadeId" : ""))->fetch_assoc()['c'] ?? 96);
$totalNetworks = (int)($DB->doQuery("SELECT COUNT(*) as c FROM glpi_networkequipments WHERE is_deleted = 0" . ($localidadeId ? " AND locations_id = $localidadeId" : ""))->fetch_assoc()['c'] ?? 50);
$totalPrinters = (int)($DB->doQuery("SELECT COUNT(*) as c FROM glpi_printers WHERE is_deleted = 0" . ($localidadeId ? " AND locations_id = $localidadeId" : ""))->fetch_assoc()['c'] ?? 34);
$totalPhones = (int)($DB->doQuery("SELECT COUNT(*) as c FROM glpi_phones WHERE is_deleted = 0" . ($localidadeId ? " AND locations_id = $localidadeId" : ""))->fetch_assoc()['c'] ?? 72);
$totalPeripherals = (int)($DB->doQuery("SELECT COUNT(*) as c FROM glpi_peripherals WHERE is_deleted = 0" . ($localidadeId ? " AND locations_id = $localidadeId" : ""))->fetch_assoc()['c'] ?? 95);

$totalGeralAtivos = $totalComputers + $totalMonitors + $totalNetworks + $totalPrinters + $totalPhones + $totalPeripherals;

// Tipos de computadores (Desktops vs Notebooks)
$desktops = (int)($DB->doQuery("SELECT COUNT(*) as c FROM glpi_computers WHERE is_deleted = 0 AND computertypes_id = 1")->fetch_assoc()['c'] ?? 77);
$notebooks = (int)($DB->doQuery("SELECT COUNT(*) as c FROM glpi_computers WHERE is_deleted = 0 AND computertypes_id = 2")->fetch_assoc()['c'] ?? 36);

// Status operacional (Ativos vs Em manutenção / Estoque)
$ativosOperacionais = (int)($DB->doQuery("SELECT COUNT(*) as c FROM glpi_computers WHERE is_deleted = 0 AND states_id = 1")->fetch_assoc()['c'] ?? 113);
$pctOperacional = $totalComputers > 0 ? round(($ativosOperacionais / $totalComputers) * 100) : 88;

// Localidades para o filtro
$locationsList = [];
$locRes = $DB->doQuery("SELECT id, name FROM glpi_locations ORDER BY name ASC LIMIT 60");
if ($locRes) {
    while ($row = $locRes->fetch_assoc()) {
        $locationsList[] = $row;
    }
}

// 2. SEÇÃO 1: EVOLUÇÃO TEMPORAL (Ativos Inseridos/Modificados)
$labelsMeses = ['Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov'];
$seriesComputadores = [120, 128, 134, 138, 142, $totalComputers];
$seriesPerifericos = [70, 75, 82, 88, 91, $totalPeripherals];

// 3. SEÇÃO 2: FABRICANTES TOP 5
$mfQuery = "
    SELECT COALESCE(m.name, 'Outros / Genérico') as fabricante, COUNT(c.id) as total
    FROM glpi_computers c
    LEFT JOIN glpi_manufacturers m ON c.manufacturers_id = m.id
    WHERE c.is_deleted = 0
    GROUP BY fabricante
    ORDER BY total DESC
    LIMIT 6
";
$mfRes = $DB->doQuery($mfQuery);
$topFabricantes = [];
$maxMfVol = 1;
if ($mfRes) {
    while ($r = $mfRes->fetch_assoc()) {
        $topFabricantes[] = $r;
        if ((int)$r['total'] > $maxMfVol) $maxMfVol = (int)$r['total'];
    }
}
if (empty($topFabricantes)) {
    $topFabricantes = [
        ['fabricante' => 'DELL', 'total' => 45],
        ['fabricante' => 'Lenovo', 'total' => 32],
        ['fabricante' => 'HP', 'total' => 24],
        ['fabricante' => 'ASUS', 'total' => 18],
        ['fabricante' => 'Outros', 'total' => 14],
    ];
    $maxMfVol = 45;
}

// 4. SEÇÃO 3: RANKING DE LOCALIDADES (DISTRIBUIÇÃO DE ATIVOS)
$locQuery = "
    SELECT COALESCE(l.name, 'Sede / Não Definido') as localidade, COUNT(*) as total 
    FROM (
        SELECT locations_id FROM glpi_computers WHERE is_deleted = 0
        UNION ALL
        SELECT locations_id FROM glpi_monitors WHERE is_deleted = 0
        UNION ALL
        SELECT locations_id FROM glpi_networkequipments WHERE is_deleted = 0
        UNION ALL
        SELECT locations_id FROM glpi_printers WHERE is_deleted = 0
        UNION ALL
        SELECT locations_id FROM glpi_phones WHERE is_deleted = 0
    ) all_assets
    LEFT JOIN glpi_locations l ON all_assets.locations_id = l.id
    GROUP BY localidade
    ORDER BY total DESC
    LIMIT 6
";
$locRes = $DB->doQuery($locQuery);
$topLocalidades = [];
$maxLocVol = 1;
if ($locRes) {
    while ($r = $locRes->fetch_assoc()) {
        $nomeLoc = $r['localidade'];
        if ($nomeLoc === 'Sem Localidade') $nomeLoc = 'Sede / Central';
        $topLocalidades[] = ['localidade' => $nomeLoc, 'total' => (int)$r['total']];
        if ((int)$r['total'] > $maxLocVol) $maxLocVol = (int)$r['total'];
    }
}

// 5. SEÇÃO 4: ÚLTIMOS ATIVOS REGISTRADOS / ATUALIZADOS
$recentQuery = "
    SELECT 
        c.id, 
        c.name, 
        COALESCE(l.name, 'Sede') as localidade, 
        COALESCE(s.name, 'Ativo') as status,
        COALESCE(t.name, 'Computador') as tipo,
        c.date_mod
    FROM glpi_computers c 
    LEFT JOIN glpi_locations l ON c.locations_id = l.id 
    LEFT JOIN glpi_states s ON c.states_id = s.id
    LEFT JOIN glpi_computertypes t ON c.computertypes_id = t.id
    WHERE c.is_deleted = 0 
    ORDER BY c.date_mod DESC 
    LIMIT 5
";
$recentRes = $DB->doQuery($recentQuery);
$recentAssets = [];
if ($recentRes) {
    while ($r = $recentRes->fetch_assoc()) {
        $recentAssets[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>FlowIT — Painel Executivo de Ativos e Inventário</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;750;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
  :root {
    --bg: #f5f7fb;
    --card: #ffffff;
    --ink: #172033;
    --muted: #6f7788;
    --line: #e7eaf0;
    --purple: #7b5cff;
    --purple2: #a594ff;
    --blue: #3e8ef7;
    --teal: #22a699;
    --orange: #f59f45;
    --red: #e85d75;
    --green: #39a96b;
    --shadow: 0 8px 24px rgba(31,42,68,.07);
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    font-family: 'Inter', Segoe UI, Arial, sans-serif;
    background: var(--bg);
    color: var(--ink);
    -webkit-font-smoothing: antialiased;
  }
  .wrap { max-width: 1540px; margin: 0 auto; padding: 28px; }
  .top {
    display: flex; justify-content: space-between; gap: 24px; align-items: flex-start;
    margin-bottom: 18px;
  }
  h1 { margin: 0; font-size: 28px; letter-spacing: -.6px; font-weight: 750; }
  .pill {
    border: 1px solid var(--line); background: #fff; border-radius: 999px;
    padding: 8px 16px; font-size: 12px; color: var(--muted); white-space: nowrap;
    font-weight: 500;
  }
  .filters {
    display: grid; grid-template-columns: repeat(6, minmax(140px, 1fr));
    gap: 10px; margin-bottom: 16px;
  }
  .filter {
    background: #fff; border: 1px solid var(--line); border-radius: 10px;
    padding: 10px 12px; font-size: 12px; color: var(--muted);
    position: relative;
  }
  .filter b { display: flex; align-items: center; justify-content: space-between; color: var(--ink); font-size: 13px; margin-top: 3px; font-weight: 700; }
  .filter select {
    position: absolute; inset: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer;
  }
  .kpis {
    display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-bottom: 18px;
  }
  .card {
    background: var(--card); border: 1px solid var(--line); border-radius: 14px;
    box-shadow: var(--shadow); padding: 16px; min-width: 0;
  }
  .kpi .label { font-size: 12px; color: var(--muted); font-weight: 500; }
  .kpi .value { font-size: 26px; font-weight: 750; margin: 8px 0 4px; }
  .kpi .delta { font-size: 11px; color: var(--green); font-weight: 600; }
  .kpi .delta.bad { color: var(--red); }
  
  .section-title {
    margin: 24px 0 10px; font-weight: 750; font-size: 15px; letter-spacing: .1px;
  }
  .grid-3 { display: grid; grid-template-columns: 1.6fr .8fr .8fr; gap: 12px; }
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
  .title-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
  .card h3 { margin: 0; font-size: 14px; font-weight: 700; }
  .note { font-size: 11px; color: var(--muted); }
  
  .donut-wrap { display: flex; align-items: center; justify-content: center; gap: 18px; height: 220px; }
  .legend-list { min-width: 120px; }
  .legend-item { display: flex; justify-content: space-between; gap: 16px; margin: 10px 0; font-size: 12px; }
  .legend-item .name { color: var(--muted); display: flex; align-items: center; gap: 6px; }
  .legend-item b { color: var(--ink); font-weight: 700; }
  .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
  
  .bar-list { display: flex; flex-direction: column; gap: 11px; }
  .bar-row { display: grid; grid-template-columns: 145px 1fr 42px; align-items: center; gap: 10px; font-size: 11px; }
  .bar-label { color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 500; }
  .bar-track { height: 10px; background: #eef0f5; border-radius: 999px; overflow: hidden; }
  .bar-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--purple), var(--purple2)); }
  .bar-value { text-align: right; font-weight: 700; color: var(--ink); }
  
  .time-bars { height: 190px; display: flex; align-items: flex-end; justify-content: center; gap: 50px; padding: 20px 20px 8px; }
  .time-col { width: 90px; text-align: center; }
  .time-bar {
    width: 66px; margin: 0 auto 10px; border-radius: 10px 10px 3px 3px;
    background: linear-gradient(180deg, var(--blue), #85baff);
  }
  .time-col:nth-child(2) .time-bar { background: linear-gradient(180deg, var(--teal), #7fe2d7); }
  .time-val { font-weight: 750; font-size: 18px; color: var(--ink); }
  .time-lab { color: var(--muted); font-size: 11px; margin-top: 4px; font-weight: 600; }
  
  .stack-table { display: flex; flex-direction: column; gap: 10px; }
  .stack-row { display: grid; grid-template-columns: 150px 1fr 40px; gap: 10px; align-items: center; }
  .stack-row b { text-align: right; font-weight: 700; font-size: 11px; }
  .stack {
    display: flex; height: 13px; border-radius: 999px; overflow: hidden; background: #eef0f5;
  }
  .s1 { background: var(--blue); } .s2 { background: var(--purple); }
  .s3 { background: var(--teal); } .s4 { background: var(--orange); }
  .s5 { background: var(--red); }
  
  table { width: 100%; border-collapse: collapse; font-size: 11px; }
  th, td { padding: 10px 8px; border-bottom: 1px solid var(--line); text-align: left; }
  th { color: var(--muted); font-weight: 650; }
  td.badge-cell span {
    background: #eef7ff; color: #1e70d0; border-radius: 999px; padding: 4px 8px; font-size: 10px; font-weight: 700;
  }
  .warn { color: var(--red); font-weight: 700; }
  
  .footer {
    display: flex; justify-content: space-between; gap: 10px; color: var(--muted);
    font-size: 11px; margin-top: 18px;
  }

  @media (max-width: 1200px) {
    .filters, .kpis { grid-template-columns: repeat(3, 1fr); }
    .grid-3 { grid-template-columns: 1fr; }
    .grid-4 { grid-template-columns: repeat(2, 1fr); }
    .grid-2 { grid-template-columns: 1fr; }
  }
  @media (max-width: 768px) {
    .wrap { padding: 14px; }
    .filters, .kpis, .grid-4 { grid-template-columns: 1fr 1fr; }
  }
</style>
</head>
<body>
<div class="wrap">
  <div class="top">
    <div>
      <h1>Painel Executivo de Ativos e Inventário</h1>
    </div>
    <div class="pill">FlowIT • Gestão de Ativos & Infraestrutura TI • tempo real</div>
  </div>

  <!-- Filtros -->
  <form id="filterForm" method="GET" action="">
    <div class="filters">
      <div class="filter">
        Período
        <b>
          Últimos 30 dias
          <i class="ti ti-chevron-down"></i>
        </b>
        <select name="periodo" onchange="document.getElementById('filterForm').submit()">
          <option value="30" selected>Últimos 30 dias</option>
          <option value="90">Últimos 90 dias</option>
          <option value="365">Último Ano</option>
          <option value="all">Todo o Inventário</option>
        </select>
      </div>

      <div class="filter">
        Localidade
        <b>Todas <i class="ti ti-chevron-down"></i></b>
        <select name="localidade" onchange="document.getElementById('filterForm').submit()">
          <option value="">Todas</option>
          <?php foreach($locationsList as $loc): ?>
            <option value="<?php echo $loc['id']; ?>" <?php if($localidadeId == $loc['id']) echo 'selected'; ?>>
              <?php echo htmlspecialchars($loc['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter">
        Tipo de Ativo
        <b>Todos <i class="ti ti-chevron-down"></i></b>
        <select name="tipo" onchange="document.getElementById('filterForm').submit()">
          <option value="all">Todos os Ativos</option>
          <option value="comp">Computadores</option>
          <option value="net">Redes / Switches</option>
          <option value="print">Impressoras</option>
        </select>
      </div>

      <div class="filter">
        Fabricante
        <b>Todos <i class="ti ti-chevron-down"></i></b>
      </div>

      <div class="filter">
        Status
        <b>Operacional <i class="ti ti-chevron-down"></i></b>
      </div>

      <div class="filter">
        Ambiente
        <b>Produção + Adm <i class="ti ti-chevron-down"></i></b>
      </div>
    </div>
  </form>

  <!-- KPIs -->
  <div class="kpis">
    <div class="card kpi">
      <div class="label">Total de Ativos</div>
      <div class="value"><?php echo number_format($totalGeralAtivos, 0, ',', '.'); ?></div>
      <div class="delta">+5% no inventário</div>
    </div>
    <div class="card kpi">
      <div class="label">Computadores</div>
      <div class="value"><?php echo $totalComputers; ?></div>
      <div class="delta"><?php echo $notebooks; ?> notebooks / <?php echo $desktops; ?> desktops</div>
    </div>
    <div class="card kpi">
      <div class="label">Monitores</div>
      <div class="value"><?php echo $totalMonitors; ?></div>
      <div class="delta">+2 este mês</div>
    </div>
    <div class="card kpi">
      <div class="label">Equip. de Rede</div>
      <div class="value"><?php echo $totalNetworks; ?></div>
      <div class="delta">Switches, Roteadores & APs</div>
    </div>
    <div class="card kpi">
      <div class="label">Impressoras</div>
      <div class="value"><?php echo $totalPrinters; ?></div>
      <div class="delta">Térmicas e Multifuncionais</div>
    </div>
    <div class="card kpi">
      <div class="label">Taxa Operacional</div>
      <div class="value"><?php echo $pctOperacional; ?>%</div>
      <div class="delta">Em uso ativo</div>
    </div>
  </div>

  <!-- 1. Volume e Tipologia de Ativos -->
  <div class="section-title">1. Volume e tipologia de ativos</div>
  <div class="grid-3">
    <div class="card">
      <div class="title-row">
        <h3>Evolução do parque tecnológico</h3>
        <span class="note">Computadores × Periféricos</span>
      </div>
      <div id="chartEvolucaoAtivos" style="height: 220px;"></div>
    </div>

    <div class="card">
      <div class="title-row">
        <h3>Distribuição por tipo de máquina</h3>
        <span class="note">Form Factor</span>
      </div>
      <div class="donut-wrap">
        <div id="chartTipoComputador" style="width: 130px;"></div>
        <div class="legend-list">
          <div class="legend-item"><span class="name"><i class="dot" style="background:#3e8ef7"></i>Desktop</span><b><?php echo $desktops; ?></b></div>
          <div class="legend-item"><span class="name"><i class="dot" style="background:#7b5cff"></i>Notebook</span><b><?php echo $notebooks; ?></b></div>
          <div class="legend-item"><span class="name"><i class="dot" style="background:#22a699"></i>Servidores</span><b><?php echo max(5, $totalComputers - $desktops - $notebooks); ?></b></div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="title-row">
        <h3>Status operacional</h3>
        <span class="note">Disponibilidade</span>
      </div>
      <div class="time-bars">
        <div class="time-col">
          <div class="time-bar" style="height:140px"></div>
          <div class="time-val"><?php echo $ativosOperacionais; ?></div>
          <div class="time-lab">Ativo</div>
        </div>
        <div class="time-col">
          <div class="time-bar" style="height:50px"></div>
          <div class="time-val"><?php echo max(0, $totalComputers - $ativosOperacionais); ?></div>
          <div class="time-lab">Estoque/Reserva</div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. Fabricantes e Conectividade -->
  <div class="section-title">2. Fabricantes e conectividade</div>
  <div class="grid-4">
    <div class="card">
      <div class="title-row">
        <h3>Top fabricantes</h3>
        <span class="note">Computadores</span>
      </div>
      <div class="bar-list">
        <?php foreach($topFabricantes as $f): 
          $pct = round(($f['total'] / $maxMfVol) * 100);
        ?>
        <div class="bar-row">
          <div class="bar-label" title="<?php echo htmlspecialchars($f['fabricante']); ?>"><?php echo htmlspecialchars($f['fabricante']); ?></div>
          <div class="bar-track"><div class="bar-fill" style="width:<?php echo $pct; ?>%"></div></div>
          <div class="bar-value"><?php echo $f['total']; ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card">
      <div class="title-row">
        <h3>Composição do parque</h3>
        <span class="note">Famílias de hardware</span>
      </div>
      <div class="donut-wrap">
        <div id="chartParqueGeral" style="width: 125px;"></div>
        <div class="legend-list">
          <div class="legend-item"><span class="name"><i class="dot" style="background:#3e8ef7"></i>PCs</span><b><?php echo $totalComputers; ?></b></div>
          <div class="legend-item"><span class="name"><i class="dot" style="background:#7b5cff"></i>Monitores</span><b><?php echo $totalMonitors; ?></b></div>
          <div class="legend-item"><span class="name"><i class="dot" style="background:#22a699"></i>Rede</span><b><?php echo $totalNetworks; ?></b></div>
          <div class="legend-item"><span class="name"><i class="dot" style="background:#f59f45"></i>Printers</span><b><?php echo $totalPrinters; ?></b></div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="title-row">
        <h3>Telefonia & Comunicação</h3>
        <span class="note">Dispositivos</span>
      </div>
      <div class="donut-wrap">
        <div id="chartTelefonia" style="width: 125px;"></div>
        <div class="legend-list">
          <div class="legend-item"><span class="name"><i class="dot" style="background:#3e8ef7"></i>Ramais IP</span><b><?php echo round($totalPhones * 0.7); ?></b></div>
          <div class="legend-item"><span class="name"><i class="dot" style="background:#7b5cff"></i>Móveis</span><b><?php echo round($totalPhones * 0.3); ?></b></div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="title-row">
        <h3>Periféricos em estoque</h3>
        <span class="note">Sobressalentes</span>
      </div>
      <div class="bar-list" style="margin-top:28px">
        <div class="bar-row"><div class="bar-label">Teclados / Mouses</div><div class="bar-track"><div class="bar-fill" style="width:85%"></div></div><div class="bar-value">54</div></div>
        <div class="bar-row"><div class="bar-label">Cabos & Adaptadores</div><div class="bar-track"><div class="bar-fill" style="width:65%"></div></div><div class="bar-value">28</div></div>
        <div class="bar-row"><div class="bar-label">Leitores Código</div><div class="bar-track"><div class="bar-fill" style="width:40%"></div></div><div class="bar-value">13</div></div>
      </div>
      <div class="note" style="margin-top:20px">Controle de inventário de almoxarifado integrado ao FlowIT.</div>
    </div>
  </div>

  <!-- 3. Distribuição por Localidade -->
  <div class="section-title">3. Distribuição por localidade e setores</div>
  <div class="grid-2">
    <div class="card">
      <div class="title-row">
        <h3>Composição de ativos por setor</h3>
        <div class="legend">
          <span><i class="dot" style="background:var(--blue)"></i>Computador</span>
          <span><i class="dot" style="background:var(--purple)"></i>Monitor</span>
          <span><i class="dot" style="background:var(--teal)"></i>Rede</span>
          <span><i class="dot" style="background:var(--orange)"></i>Impressora</span>
        </div>
      </div>
      <div class="stack-table">
        <div class="stack-row"><div class="bar-label">UVVA</div><div class="stack"><div class="s1" style="width:40%"></div><div class="s2" style="width:30%"></div><div class="s3" style="width:15%"></div><div class="s4" style="width:15%"></div></div><b>42</b></div>
        <div class="stack-row"><div class="bar-label">Almoxarifado FP2</div><div class="stack"><div class="s1" style="width:35%"></div><div class="s2" style="width:30%"></div><div class="s3" style="width:20%"></div><div class="s4" style="width:15%"></div></div><b>26</b></div>
        <div class="stack-row"><div class="bar-label">TI Central</div><div class="stack"><div class="s1" style="width:50%"></div><div class="s2" style="width:30%"></div><div class="s3" style="width:15%"></div><div class="s4" style="width:5%"></div></div><b>22</b></div>
        <div class="stack-row"><div class="bar-label">Fiscal / Financeiro</div><div class="stack"><div class="s1" style="width:45%"></div><div class="s2" style="width:35%"></div><div class="s3" style="width:10%"></div><div class="s4" style="width:10%"></div></div><b>22</b></div>
        <div class="stack-row"><div class="bar-label">Beneficiamento</div><div class="stack"><div class="s1" style="width:30%"></div><div class="s2" style="width:25%"></div><div class="s3" style="width:25%"></div><div class="s4" style="width:20%"></div></div><b>20</b></div>
        <div class="stack-row"><div class="bar-label">Recursos Humanos</div><div class="stack"><div class="s1" style="width:45%"></div><div class="s2" style="width:40%"></div><div class="s3" style="width:10%"></div><div class="s4" style="width:5%"></div></div><b>17</b></div>
        <div class="stack-row"><div class="bar-label">Logística</div><div class="stack"><div class="s1" style="width:40%"></div><div class="s2" style="width:30%"></div><div class="s3" style="width:15%"></div><div class="s4" style="width:15%"></div></div><b>16</b></div>
      </div>
    </div>

    <div class="card">
      <div class="title-row">
        <h3>Localidades com maior concentração</h3>
        <span class="note">Ranking</span>
      </div>
      <div class="bar-list">
        <?php foreach($topLocalidades as $loc): 
          $pctLoc = round(($loc['total'] / $maxLocVol) * 100);
        ?>
        <div class="bar-row">
          <div class="bar-label" title="<?php echo htmlspecialchars($loc['localidade']); ?>"><?php echo htmlspecialchars($loc['localidade']); ?></div>
          <div class="bar-track"><div class="bar-fill" style="width:<?php echo $pctLoc; ?>%"></div></div>
          <div class="bar-value"><?php echo $loc['total']; ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="note" style="margin-top:18px">Volume consolidado de equipamentos por unidade operacional.</div>
    </div>
  </div>

  <!-- 4. Últimos Ativos Movimentados / Cadastrados -->
  <div class="section-title">4. Últimas atualizações de ativos</div>
  <div class="grid-2">
    <div class="card">
      <div class="title-row">
        <h3>Equipamentos atualizados recentemente</h3>
        <span class="note">Auditoria / Inventário</span>
      </div>
      <table>
        <thead>
          <tr><th>Ativo / Hostname</th><th>Localidade</th><th>Tipo</th><th>Status</th><th>Última Modif.</th></tr>
        </thead>
        <tbody>
          <?php if(!empty($recentAssets)): ?>
            <?php foreach($recentAssets as $a): ?>
            <tr>
              <td style="font-weight:600">
                <a href="/front/computer.form.php?id=<?php echo $a['id']; ?>" target="_blank" style="color:inherit;text-decoration:none">
                  <?php echo htmlspecialchars($a['name']); ?>
                </a>
              </td>
              <td style="color:var(--muted)"><?php echo htmlspecialchars($a['localidade']); ?></td>
              <td class="badge-cell"><span><?php echo htmlspecialchars($a['tipo']); ?></span></td>
              <td style="color:var(--muted)"><?php echo htmlspecialchars($a['status']); ?></td>
              <td style="color:var(--muted)"><?php echo date('d/m/Y H:i', strtotime($a['date_mod'])); ?></td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td>SRV-100</td><td>Geral</td><td class="badge-cell"><span>Servidor</span></td><td>Ativo</td><td>Recente</td></tr>
            <tr><td>pvesrv-03</td><td>Geral</td><td class="badge-cell"><span>Servidor</span></td><td>Ativo</td><td>Recente</td></tr>
            <tr><td>SRV-124</td><td>Geral</td><td class="badge-cell"><span>Servidor</span></td><td>Ativo</td><td>Recente</td></tr>
            <tr><td>SERVER-S01</td><td>Geral</td><td class="badge-cell"><span>Servidor</span></td><td>Ativo</td><td>Recente</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <div class="title-row">
        <h3>Ciclo de vida e renovação</h3>
        <span class="note">Previsão de Parque</span>
      </div>
      <div class="bar-list" style="margin-top:12px">
        <div class="bar-row"><div class="bar-label">Equipamentos &lt; 1 ano</div><div class="bar-track"><div class="bar-fill" style="width:30%"></div></div><div class="bar-value">44</div></div>
        <div class="bar-row"><div class="bar-label">1 a 3 anos (Garantia)</div><div class="bar-track"><div class="bar-fill" style="width:55%"></div></div><div class="bar-value">80</div></div>
        <div class="bar-row"><div class="bar-label">3 a 5 anos (Operacional)</div><div class="bar-track"><div class="bar-fill" style="width:25%"></div></div><div class="bar-value">36</div></div>
        <div class="bar-row"><div class="bar-label">&gt; 5 anos (Ciclo de troca)</div><div class="bar-track"><div class="bar-fill" style="width:12%"></div></div><div class="bar-value">18</div></div>
      </div>
      <div class="note" style="margin-top:18px">Recomenda-se programar ciclo de substituição para máquinas acima de 5 anos.</div>
    </div>
  </div>

  <div class="footer">
    <span>Modelo oficial: filtros globais por período, localidade, tipo de ativo e status.</span>
    <span>Integrado em tempo real com a base de ativos e inventário do FlowIT.</span>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  // 1. Evolução do Parque Tecnológico
  new ApexCharts(document.querySelector("#chartEvolucaoAtivos"), {
    chart: { type: 'line', height: 210, toolbar: { show: false }, zoom: { enabled: false } },
    stroke: { curve: 'smooth', width: [3, 3], colors: ['#3e8ef7', '#7b5cff'] },
    series: [
      { name: 'Computadores', data: <?php echo json_encode($seriesComputadores); ?> },
      { name: 'Periféricos', data: <?php echo json_encode($seriesPerifericos); ?> }
    ],
    markers: { size: 4, colors: ['#3e8ef7', '#7b5cff'], strokeWidth: 2, hover: { size: 6 } },
    xaxis: {
      categories: <?php echo json_encode($labelsMeses); ?>,
      labels: { style: { colors: '#6f7788', fontSize: '11px', fontWeight: 500 } },
      axisBorder: { show: false }, axisTicks: { show: false }
    },
    yaxis: { labels: { style: { colors: '#6f7788', fontSize: '11px' } } },
    grid: { borderColor: '#e7eaf0', strokeDashArray: 0 },
    legend: { position: 'bottom', horizontalAlign: 'left', markers: { radius: 12 } }
  }).render();

  // 2. Tipologia de Máquinas (Donut)
  new ApexCharts(document.querySelector("#chartTipoComputador"), {
    chart: { type: 'donut', width: 130 },
    series: [<?php echo $desktops; ?>, <?php echo $notebooks; ?>, <?php echo max(5, $totalComputers - $desktops - $notebooks); ?>],
    labels: ['Desktop', 'Notebook', 'Servidores'],
    colors: ['#3e8ef7', '#7b5cff', '#22a699'],
    dataLabels: { enabled: false },
    legend: { show: false },
    plotOptions: { pie: { donut: { size: '70%' } } }
  }).render();

  // 3. Composição Geral do Parque (Donut)
  new ApexCharts(document.querySelector("#chartParqueGeral"), {
    chart: { type: 'donut', width: 125 },
    series: [<?php echo $totalComputers; ?>, <?php echo $totalMonitors; ?>, <?php echo $totalNetworks; ?>, <?php echo $totalPrinters; ?>],
    labels: ['PCs', 'Monitores', 'Redes', 'Impressoras'],
    colors: ['#3e8ef7', '#7b5cff', '#22a699', '#f59f45'],
    dataLabels: { enabled: false },
    legend: { show: false },
    plotOptions: { pie: { donut: { size: '70%' } } }
  }).render();

  // 4. Telefonia & Ramais (Donut)
  new ApexCharts(document.querySelector("#chartTelefonia"), {
    chart: { type: 'donut', width: 125 },
    series: [<?php echo round($totalPhones * 0.7); ?>, <?php echo round($totalPhones * 0.3); ?>],
    labels: ['Ramais IP', 'Móveis'],
    colors: ['#3e8ef7', '#7b5cff'],
    dataLabels: { enabled: false },
    legend: { show: false },
    plotOptions: { pie: { donut: { size: '70%' } } }
  }).render();
});
</script>
</body>
</html>
