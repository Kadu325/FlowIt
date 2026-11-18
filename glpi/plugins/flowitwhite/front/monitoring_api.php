<?php
/**
 * FlowIT - API de Monitoramento e Comunicação Zabbix 7.4
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

global $DB;
$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');
$zbx = new FlowitZabbixClient();

// 1. Teste de Comunicação em Tempo Real (Ping + Portas)
if ($action === 'test_comm') {
    $ip = trim($_POST['ip'] ?? '');
    $type = $_POST['interface_type'] ?? 'agent'; // agent, snmp
    $port = intval($_POST['port'] ?? ($type === 'snmp' ? 161 : 10050));

    if (empty($ip)) {
        echo json_encode(['success' => false, 'error' => 'Endereço IP não informado.']);
        exit;
    }

    $pingResult = FlowitZabbixClient::pingIcmp($ip);
    $portResult = FlowitZabbixClient::checkPort($ip, $port, 2.0, $type === 'snmp' ? 'udp' : 'tcp');

    $isOnline = $pingResult['success'] || $portResult['success'];
    $statusText = $isOnline ? 'Online' : 'Offline';
    if ($pingResult['success'] && !$portResult['success']) {
        $statusText = 'Inconsistente (Ping OK, Porta fechada)';
    }

    echo json_encode([
        'success'      => true,
        'ip'           => $ip,
        'status'       => $statusText,
        'ping'         => $pingResult,
        'port_check'   => $portResult,
        'timestamp'    => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. Listagem de Templates e Grupos do Zabbix
if ($action === 'get_templates_and_groups') {
    $tplRows = [];
    $tplIt = $DB->request([
        'FROM'  => 'glpi_plugin_flowit_monitoring_templates',
        'WHERE' => ['is_active' => 1],
        'ORDER' => 'category ASC, name ASC'
    ]);
    foreach ($tplIt as $t) {
        $tplRows[] = $t;
    }

    $groupsRes = $zbx->call('hostgroup.get', [
        'output' => ['groupid', 'name'],
        'sortfield' => 'name',
        'limit' => 100
    ]);
    $groups = $groupsRes['result'] ?? [];

    echo json_encode([
        'success'   => true,
        'templates' => $tplRows,
        'groups'    => $groups
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// 3. Listagem de Dispositivos Cadastrados
if ($action === 'list') {
    $rows = [];
    $iterator = $DB->request([
        'FROM'  => 'glpi_plugin_flowit_monitoring_hosts',
        'ORDER' => 'id DESC',
        'LIMIT' => 100
    ]);
    foreach ($iterator as $r) {
        $rows[] = $r;
    }
    echo json_encode(['success' => true, 'data' => $rows], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// 4. Salvar e Provisionar Dispositivo no Zabbix Server Real (host.create / host.update)
if ($action === 'save_device') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;

    $id = isset($data['id']) && is_numeric($data['id']) ? intval($data['id']) : 0;
    $name = trim($data['name'] ?? '');
    $host = trim($data['host'] ?? '');
    $ip = trim($data['ip'] ?? '');
    $dns = trim($data['dns'] ?? '');
    $deviceType = $data['device_type'] ?? 'server';
    $snmpVersion = intval($data['snmp_version'] ?? 2);
    $snmpCommunity = trim($data['snmp_community'] ?? '');
    $agentMode = $data['agent_mode'] ?? 'passive';
    $port = intval($data['port'] ?? 10050);
    $interfaceType = intval($data['interface_type'] ?? 1); // 1 = Agent, 2 = SNMP
    $status = intval($data['zabbix_status'] ?? 0);
    $templateId = trim($data['template_id'] ?? '');
    $groupId = trim($data['group_id'] ?? '');

    if (empty($name) || empty($host) || empty($ip)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nome, Hostname e IP são campos obrigatórios.']);
        exit;
    }

    $now = date('Y-m-d H:i:s');
    $record = [
        'name'                  => $name,
        'host'                  => $host,
        'ip'                    => $ip,
        'dns'                   => $dns,
        'device_type'           => $deviceType,
        'snmp_version'          => $snmpVersion,
        'snmp_community'        => $snmpCommunity,
        'agent_mode'            => $agentMode,
        'zabbix_status'         => $status,
        'sync_status'           => 'pending',
        'date_mod'              => $now
    ];

    if ($id > 0) {
        $DB->update('glpi_plugin_flowit_monitoring_hosts', $record, ['id' => $id]);
        $hostId = $id;
    } else {
        $record['date_creation'] = $now;
        $DB->insert('glpi_plugin_flowit_monitoring_hosts', $record);
        $hostId = $DB->insertId();
    }

    // Gravar Interface no GLPI
    $DB->delete('glpi_plugin_flowit_monitoring_interfaces', ['host_id' => $hostId, 'is_main' => 1]);
    $DB->insert('glpi_plugin_flowit_monitoring_interfaces', [
        'host_id'  => $hostId,
        'type'     => $interfaceType,
        'useip'    => 1,
        'ip'       => $ip,
        'dns'      => $dns,
        'port'     => (string)$port,
        'is_main'  => 1
    ]);

    // Provisionar no Zabbix 7.4 via API
    $zbxHostId = null;
    $syncMsg = '';
    $syncStatus = 'synced';

    // Montar Interface Zabbix
    $zbxInterface = [
        'type'   => $interfaceType,
        'main'   => 1,
        'useip'  => 1,
        'ip'     => $ip,
        'dns'    => $dns,
        'port'   => (string)$port
    ];
    if ($interfaceType === 2) { // SNMP
        $zbxInterface['details'] = [
            'version'   => $snmpVersion,
            'community' => !empty($snmpCommunity) ? $snmpCommunity : 'public'
        ];
    }

    // Default Group se não selecionado
    $targetGroupId = !empty($groupId) ? $groupId : '2'; // Linux servers ou Discovered
    $targetTemplates = !empty($templateId) ? [['templateid' => $templateId]] : [];

    // Verificar se o host já existe no Zabbix
    $findHost = $zbx->call('host.get', ['filter' => ['host' => $host]]);
    $existingZbx = $findHost['result'][0] ?? null;

    if ($existingZbx) {
        $zbxHostId = $existingZbx['hostid'];
        $updateParams = [
            'hostid'     => $zbxHostId,
            'name'       => $name,
            'status'     => $status,
            'interfaces' => [$zbxInterface]
        ];
        if (!empty($targetTemplates)) {
            $updateParams['templates'] = $targetTemplates;
        }
        $zbxRes = $zbx->call('host.update', $updateParams);
        if (isset($zbxRes['error'])) {
            $syncStatus = 'error';
            $syncMsg = 'Erro Zabbix host.update: ' . $zbxRes['error']['data'];
        } else {
            $syncMsg = "Host atualizado com sucesso no Zabbix (HostID: $zbxHostId)";
        }
    } else {
        $createParams = [
            'host'       => $host,
            'name'       => $name,
            'status'     => $status,
            'interfaces' => [$zbxInterface],
            'groups'     => [['groupid' => $targetGroupId]],
            'templates'  => $targetTemplates
        ];
        $zbxRes = $zbx->call('host.create', $createParams);
        if (isset($zbxRes['error'])) {
            $syncStatus = 'error';
            $syncMsg = 'Erro Zabbix host.create: ' . ($zbxRes['error']['data'] ?? $zbxRes['error']['message']);
        } else {
            $zbxHostId = $zbxRes['result']['hostids'][0] ?? null;
            $syncMsg = "Host criado com sucesso no Zabbix (HostID: $zbxHostId)";
        }
    }

    // Atualizar no banco do GLPI com o HostID e Status de Sincronização
    $DB->update('glpi_plugin_flowit_monitoring_hosts', [
        'zabbix_hostid' => $zbxHostId,
        'sync_status'   => $syncStatus,
        'sync_message'  => $syncMsg
    ], ['id' => $hostId]);

    // Registrar Log de Auditoria
    $DB->insert('glpi_plugin_flowit_monitoring_sync_logs', [
        'host_id'           => $hostId,
        'action'            => $id > 0 ? 'update_host' : 'create_host',
        'status'            => $syncStatus === 'synced' ? 'success' : 'error',
        'request_payload'   => json_encode($record, JSON_UNESCAPED_UNICODE),
        'response_payload'  => $syncMsg,
        'error_details'     => $syncStatus === 'error' ? $syncMsg : null,
        'user_id'           => Session::getLoginUserID(),
        'date_creation'     => $now
    ]);

    echo json_encode([
        'success'       => $syncStatus === 'synced',
        'id'            => $hostId,
        'zabbix_hostid' => $zbxHostId,
        'message'       => $syncMsg
    ]);
    exit;
}

// 5. Excluir Dispositivo no GLPI e no Zabbix
if ($action === 'delete_device') {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        $hostRow = false;
        $it = $DB->request(['FROM' => 'glpi_plugin_flowit_monitoring_hosts', 'WHERE' => ['id' => $id]]);
        foreach ($it as $r) {
            $hostRow = $r;
            break;
        }

        if ($hostRow && !empty($hostRow['zabbix_hostid'])) {
            $zbx->call('host.delete', [$hostRow['zabbix_hostid']]);
        }

        $DB->delete('glpi_plugin_flowit_monitoring_interfaces', ['host_id' => $id]);
        $DB->delete('glpi_plugin_flowit_monitoring_host_templates', ['host_id' => $id]);
        $DB->delete('glpi_plugin_flowit_monitoring_hosts', ['id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Dispositivo removido do GLPI e Zabbix.']);
        exit;
    }
    echo json_encode(['success' => false, 'error' => 'ID inválido.']);
    exit;
}

// 6. Alertas e Problemas Reais do Zabbix 7.4 (problem.get)
if ($action === 'get_active_problems') {
    $res = $zbx->call('problem.get', [
        'output'        => ['eventid', 'name', 'severity', 'clock', 'acknowledged'],
        'selectHosts'   => ['hostid', 'host', 'name'],
        'sortfield'     => ['eventid'],
        'sortorder'     => 'DESC',
        'limit'         => 20
    ]);
    echo json_encode(['success' => true, 'problems' => $res['result'] ?? []]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Ação não reconhecida.']);
