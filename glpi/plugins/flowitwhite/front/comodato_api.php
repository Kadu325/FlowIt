<?php
/**
 * FlowIT - API de Integração e Automação de Comodato e Assinatura Digital
 */

require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

global $DB;

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $status = $_GET['status'] ?? '';
        $where = [];
        if (!empty($status)) {
            $where['status'] = $status;
        }

        $iterator = $DB->request([
            'FROM' => 'glpi_plugin_flowit_comodato',
            'WHERE' => $where,
            'ORDER' => 'id DESC',
            'LIMIT' => 100
        ]);

        $data = [];
        foreach ($iterator as $row) {
            $row['sign_url'] = "assinar.php?token=" . $row['signature_token'];
            $data[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'get_options') {
        // Obter computadores com modelo e tipo
        $compQuery = "
            SELECT c.id, c.name, c.serial, c.otherserial, c.contact_num,
                   m.name AS model_name, m.product_number AS model_number,
                   t.name AS type_name,
                   c.users_id
            FROM glpi_computers c
            LEFT JOIN glpi_computermodels m ON c.computermodels_id = m.id
            LEFT JOIN glpi_computertypes t ON c.computertypes_id = t.id
            WHERE c.is_deleted = 0
            ORDER BY c.name ASC
        ";
        $compRes = $DB->doQuery($compQuery);
        $computers = [];
        if ($compRes) {
            while ($c = $compRes->fetch_assoc()) {
                $isNotebook = (stripos($c['type_name'] ?? '', 'note') !== false || stripos($c['name'] ?? '', 'note') !== false || stripos($c['model_name'] ?? '', 'inspiron') !== false);
                $computers[] = [
                    'id' => (int)$c['id'],
                    'itemtype' => 'Computer',
                    'contract_type' => $isNotebook ? 'notebook' : 'notebook',
                    'name' => $c['name'],
                    'type_name' => $c['type_name'] ?: 'Computador',
                    'serial' => $c['serial'] ?: ($c['contact_num'] ?: 'S/N'),
                    'patrimonio' => $c['contact_num'] ?: ($c['otherserial'] ?: ''),
                    'model' => $c['model_number'] ?: ($c['model_name'] ?: 'P112F'),
                    'description' => $c['name'] . ($c['model_name'] ? " ({$c['model_name']})" : ''),
                    'users_id' => (int)$c['users_id']
                ];
            }
        }

        // Obter telefones com modelo e tipo
        $phoneQuery = "
            SELECT p.id, p.name, p.serial, p.otherserial, p.contact_num,
                   m.name AS model_name, m.product_number AS model_number,
                   t.name AS type_name,
                   p.users_id
            FROM glpi_phones p
            LEFT JOIN glpi_phonemodels m ON p.phonemodels_id = m.id
            LEFT JOIN glpi_phonetypes t ON p.phonetypes_id = t.id
            WHERE p.is_deleted = 0
            ORDER BY p.name ASC
        ";
        $phoneRes = $DB->doQuery($phoneQuery);
        $phones = [];
        if ($phoneRes) {
            while ($p = $phoneRes->fetch_assoc()) {
                $phones[] = [
                    'id' => (int)$p['id'],
                    'itemtype' => 'Phone',
                    'contract_type' => 'smartphone',
                    'name' => $p['name'],
                    'type_name' => $p['type_name'] ?: 'Smartphone',
                    'serial' => $p['serial'] ?: ($p['contact_num'] ?: 'IMEI'),
                    'patrimonio' => $p['contact_num'] ?: ($p['otherserial'] ?: ''),
                    'model' => $p['model_number'] ?: ($p['model_name'] ?: 'SM-A266M/DS'),
                    'description' => $p['name'] . ($p['model_name'] ? " ({$p['model_name']})" : ''),
                    'users_id' => (int)$p['users_id']
                ];
            }
        }

        // Obter Usuários
        $userIter = $DB->request([
            'SELECT' => ['id', 'name', 'realname', 'firstname', 'phone'],
            'FROM' => 'glpi_users',
            'WHERE' => ['is_deleted' => 0, 'is_active' => 1],
            'ORDER' => 'name ASC',
            'LIMIT' => 200
        ]);
        $users = [];
        foreach ($userIter as $u) {
            $fullName = trim(($u['firstname'] ?? '') . ' ' . ($u['realname'] ?? ''));
            if (empty($fullName)) {
                $fullName = $u['name'];
            }
            $users[] = [
                'id' => (int)$u['id'],
                'login' => $u['name'],
                'name' => $fullName
            ];
        }

        echo json_encode([
            'success' => true,
            'assets' => array_merge($computers, $phones),
            'computers' => $computers,
            'phones' => $phones,
            'users' => $users
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'create') {
        $itemtype = $_POST['itemtype'] ?? 'Computer';
        $items_id = (int)($_POST['items_id'] ?? 0);
        $users_id = (int)($_POST['users_id'] ?? 0);
        $contract_type = $_POST['contract_type'] ?? ($itemtype === 'Computer' ? 'notebook' : 'smartphone');
        $user_name = trim($_POST['user_name'] ?? '');
        $user_cpf = trim($_POST['user_cpf'] ?? '');
        $asset_name = trim($_POST['asset_name'] ?? '');
        $asset_serial = trim($_POST['asset_serial'] ?? '');
        $asset_model = trim($_POST['asset_model'] ?? '');
        $accessories = trim($_POST['accessories'] ?? '');
        $assigned_date = !empty($_POST['assigned_date']) ? $_POST['assigned_date'] : date('Y-m-d');
        $notes = trim($_POST['notes'] ?? '');

        if (empty($user_name)) {
            echo json_encode(['success' => false, 'error' => 'Nome do colaborador obrigatório.']);
            exit;
        }

        // Atualizar custódia no GLPI
        if ($items_id > 0 && $users_id > 0) {
            if ($itemtype === 'Computer') {
                $DB->update('glpi_computers', ['users_id' => $users_id], ['id' => $items_id]);
            } elseif ($itemtype === 'Phone') {
                $DB->update('glpi_phones', ['users_id' => $users_id], ['id' => $items_id]);
            }
        }

        // Gerar token seguro único para assinatura eletrônica
        $token = bin2hex(random_bytes(24));

        $insertData = [
            'itemtype' => $itemtype,
            'items_id' => $items_id,
            'users_id' => $users_id,
            'contract_type' => $contract_type,
            'status' => 'ativo',
            'assigned_date' => $assigned_date,
            'user_name' => $user_name,
            'user_cpf' => $user_cpf,
            'asset_name' => $asset_name,
            'asset_serial' => $asset_serial,
            'asset_model' => $asset_model,
            'accessories' => $accessories,
            'notes' => $notes,
            'signature_token' => $token,
            'signature_status' => 'pendente'
        ];

        $DB->insert('glpi_plugin_flowit_comodato', $insertData);
        $newId = $DB->insertId();

        $signUrl = "assinar.php?token={$token}";

        echo json_encode([
            'success' => true,
            'id' => $newId,
            'message' => 'Contrato de comodato gerado com link de assinatura!',
            'token' => $token,
            'sign_url' => $signUrl,
            'doc_url' => "comodato_doc.php?id={$newId}"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // REGISTRAR ASSINATURA ELETRÔNICA DO USUÁRIO
    if ($action === 'sign') {
        $token = trim($_POST['token'] ?? '');
        $signature_image = trim($_POST['signature_image'] ?? '');

        if (empty($token)) {
            echo json_encode(['success' => false, 'error' => 'Token ausente.']);
            exit;
        }

        $iterator = $DB->request([
            'FROM' => 'glpi_plugin_flowit_comodato',
            'WHERE' => ['signature_token' => $token]
        ]);

        $contract = null;
        foreach ($iterator as $c) {
            $contract = $c;
            break;
        }

        if (!$contract) {
            echo json_encode(['success' => false, 'error' => 'Contrato não encontrado.']);
            exit;
        }

        if ($contract['signature_status'] === 'assinado') {
            echo json_encode(['success' => true, 'message' => 'Contrato já assinado anteriormente.']);
            exit;
        }

        $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido';
        if (strpos($clientIp, ',') !== false) {
            $clientIp = trim(explode(',', $clientIp)[0]);
        }

        $DB->update('glpi_plugin_flowit_comodato', [
            'signature_status' => 'assinado',
            'signed_at' => date('Y-m-d H:i:s'),
            'signer_ip' => $clientIp,
            'signature_image' => $signature_image
        ], ['id' => $contract['id']]);

        echo json_encode([
            'success' => true,
            'message' => 'Assinatura registrada e sincronizada com sucesso no FlowIT!'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'terminate') {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID inválido.']);
            exit;
        }

        $returned_date = date('Y-m-d');
        $DB->update('glpi_plugin_flowit_comodato', [
            'status' => 'encerrado',
            'returned_date' => $returned_date
        ], ['id' => $id]);

        echo json_encode(['success' => true, 'message' => 'Contrato de comodato encerrado com sucesso.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Ação não reconhecida.']);
} catch (\Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
