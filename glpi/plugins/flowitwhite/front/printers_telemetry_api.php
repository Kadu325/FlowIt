<?php
/**
 * FlowIT - Endpoint de Telemetria de Impressoras (Toners & Páginas por Período)
 */
define('GLPI_ROOT', '/var/www/glpi');
require_once __DIR__ . '/bootstrap.php';
require_once (__DIR__ . '/FlowitZabbixClient.php');

header('Content-Type: application/json; charset=UTF-8');

if (!Session::haveRight('config', READ) && !Session::haveRight('networking', READ) && Session::getCurrentInterface() !== 'central') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado.']);
    exit;
}

$client = new FlowitZabbixClient();
$periodDays = intval($_GET['days'] ?? 30);
if ($periodDays <= 0) $periodDays = 30;

$timeFrom = time() - ($periodDays * 86400);

// 1. Obter todas as 9 impressoras
$hostsRes = $client->call('host.get', [
    'output'           => ['hostid', 'host', 'name'],
    'search'           => ['name' => 'Impressora'],
    'selectInterfaces' => ['ip', 'port']
]);

$printers = $hostsRes['result'] ?? [];
$data = [];

$totalLifetimePages = 0;
$totalPeriodPages = 0;
$printersWarning = 0;

foreach ($printers as $p) {
    $hostId = $p['hostid'];
    $ip = $p['interfaces'][0]['ip'] ?? 'N/A';

    // Buscar itens da impressora
    $itemsRes = $client->call('item.get', [
        'hostids' => [$hostId],
        'output'  => ['itemid', 'name', 'key_', 'lastvalue', 'units', 'value_type']
    ]);

    $items = $itemsRes['result'] ?? [];

    $tonerLevel = null;
    $tonerName = 'Toner Padrão';
    $lifeCount = 0;
    $lifeItemId = null;
    $drumLevel = null;

    foreach ($items as $it) {
        $key = $it['key_'];
        $name = $it['name'];
        $val = floatval($it['lastvalue']);

        // Item de contador total de páginas
        if ($key === 'prtMarkerLifeCount' || strpos($key, 'prtMarkerLifeCount') !== false) {
            $lifeCount = intval($it['lastvalue']);
            $lifeItemId = $it['itemid'];
        }

        // Itens de Capacidade / Nível de Toner
        if (strpos($key, 'prtMarkerSuppliesCapacity') !== false && strpos(strtolower($name), 'drum') === false) {
            $tonerLevel = $val;
            $tonerName = $name;
        }

        // Se tiver Drum (Cilindro)
        if (strpos(strtolower($name), 'drum') !== false && strpos($key, 'prtMarkerSuppliesCapacity') !== false) {
            $drumLevel = $val;
        }
    }

    // Calcular páginas no período usando a API de Trends ou History
    $pagesInPeriod = 0;
    if ($lifeItemId && $lifeCount > 0) {
        $trendRes = $client->call('trend.get', [
            'itemids'   => [$lifeItemId],
            'time_from' => $timeFrom,
            'limit'     => 1,
            'sortfield' => 'clock',
            'sortorder' => 'ASC'
        ]);

        $initialTrend = $trendRes['result'][0] ?? null;
        if ($initialTrend && isset($initialTrend['value_min'])) {
            $initialCount = intval($initialTrend['value_min']);
            $pagesInPeriod = max(0, $lifeCount - $initialCount);
        } else {
            // Se for dentro do período recente ou sem histórico antigo
            $histRes = $client->call('history.get', [
                'itemids'   => [$lifeItemId],
                'history'   => 3,
                'time_from' => $timeFrom,
                'limit'     => 1,
                'sortfield' => 'clock',
                'sortorder' => 'ASC'
            ]);
            $initialHist = $histRes['result'][0] ?? null;
            if ($initialHist) {
                $initialCount = intval($initialHist['value']);
                $pagesInPeriod = max(0, $lifeCount - $initialCount);
            }
        }
    }

    if ($tonerLevel !== null && $tonerLevel <= 15) {
        $printersWarning++;
    }

    $totalLifetimePages += $lifeCount;
    $totalPeriodPages += $pagesInPeriod;

    $data[] = [
        'hostid'          => $hostId,
        'name'            => $p['name'],
        'ip'              => $ip,
        'toner_level'     => $tonerLevel,
        'toner_name'      => $tonerName,
        'drum_level'      => $drumLevel,
        'lifetime_pages'  => $lifeCount,
        'period_pages'    => $pagesInPeriod
    ];
}

echo json_encode([
    'success'              => true,
    'period_days'          => $periodDays,
    'total_printers'       => count($printers),
    'total_lifetime_pages' => $totalLifetimePages,
    'total_period_pages'   => $totalPeriodPages,
    'warning_printers'     => $printersWarning,
    'printers'             => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
