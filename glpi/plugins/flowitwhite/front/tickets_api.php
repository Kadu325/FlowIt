<?php
/**
 * FlowIT - API de Chamados em Tempo Real (Live Tickets NOC)
 */

require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

global $DB;

$action = $_GET['action'] ?? 'live_board';

try {
    if ($action === 'live_board') {
        // Mapeamento enriquecido dos chamados operacionais da Fazenda Progresso
        $ticketsMetadata = [
            7225 => [
                'tecnico' => 'Ana Valéria Cardoso',
                'tecnico_avatar' => 'AV',
                'requerente' => 'Financeiro / Faturamento',
                'categoria' => 'ERP / Banco de Dados',
                'sla_total_min' => 60,
                'sla_restante_min' => 12,
                'tempo_decorrido' => 'há 10 min',
                'sla_status' => 'critical'
            ],
            7215 => [
                'tecnico' => 'Éder Ferreira Caires',
                'tecnico_avatar' => 'EF',
                'requerente' => 'RH / Folha de Pagamento',
                'categoria' => 'Fiscal / eSocial',
                'sla_total_min' => 120,
                'sla_restante_min' => 28,
                'tempo_decorrido' => 'há 26 min',
                'sla_status' => 'warning'
            ],
            7203 => [
                'tecnico' => 'Luis Felipe Santana',
                'tecnico_avatar' => 'LF',
                'requerente' => 'Mario Tuena Neto',
                'categoria' => 'Agronomia / Campo',
                'sla_total_min' => 180,
                'sla_restante_min' => 95,
                'tempo_decorrido' => 'há 48 min',
                'sla_status' => 'ok'
            ],
            7217 => [
                'tecnico' => 'Eduardo Souza',
                'tecnico_avatar' => 'ES',
                'requerente' => 'Almoxarifado Central',
                'categoria' => 'Segurança & Químicos',
                'sla_total_min' => 180,
                'sla_restante_min' => 110,
                'tempo_decorrido' => 'há 1h 02m',
                'sla_status' => 'ok'
            ],
            7216 => [
                'tecnico' => 'Eduardo Souza',
                'tecnico_avatar' => 'ES',
                'requerente' => 'Paulo (Comunicação)',
                'categoria' => 'E-mail / Microsoft 365',
                'sla_total_min' => 240,
                'sla_restante_min' => 170,
                'tempo_decorrido' => 'há 1h 15m',
                'sla_status' => 'ok'
            ],
            7218 => [
                'tecnico' => 'Ana Valéria Cardoso',
                'tecnico_avatar' => 'AV',
                'requerente' => 'Balança & Expedição',
                'categoria' => 'PDV / Balança Rodoviária',
                'sla_total_min' => 120,
                'sla_restante_min' => 65,
                'tempo_decorrido' => 'há 1h 30m',
                'sla_status' => 'ok'
            ],
            7213 => [
                'tecnico' => 'Luis Felipe Santana',
                'tecnico_avatar' => 'LF',
                'requerente' => 'Administração Central',
                'categoria' => 'Hardware / Desktop',
                'sla_total_min' => 240,
                'sla_restante_min' => 140,
                'tempo_decorrido' => 'há 1h 45m',
                'sla_status' => 'ok'
            ],
            7209 => [
                'tecnico' => 'Hassan Gustavo',
                'tecnico_avatar' => 'HG',
                'requerente' => 'Logística / Galpão 3',
                'categoria' => 'Infraestrutura / Rede',
                'sla_total_min' => 120,
                'sla_restante_min' => 35,
                'tempo_decorrido' => 'há 2h 10m',
                'sla_status' => 'warning'
            ],
            7086 => [
                'tecnico' => 'Luis Felipe Santana',
                'tecnico_avatar' => 'LF',
                'requerente' => 'Nicolas Trentin',
                'categoria' => 'Agronomia / Automação',
                'sla_total_min' => 60,
                'sla_restante_min' => 18,
                'tempo_decorrido' => 'há 2h 35m',
                'sla_status' => 'critical'
            ],
            7109 => [
                'tecnico' => 'Ana Valéria Cardoso',
                'tecnico_avatar' => 'AV',
                'requerente' => 'Refeitório / RH',
                'categoria' => 'Acesso / Catraca',
                'sla_total_min' => 300,
                'sla_restante_min' => 220,
                'tempo_decorrido' => 'há 3h 15m',
                'sla_status' => 'ok'
            ],
            7230 => [
                'tecnico' => 'Triagem NOC FlowIT',
                'tecnico_avatar' => 'NOC',
                'requerente' => 'Monitoramento NOC',
                'categoria' => 'Telecom / Links',
                'sla_total_min' => 30,
                'sla_restante_min' => 8,
                'tempo_decorrido' => 'há 3 min',
                'sla_status' => 'critical'
            ],
            7228 => [
                'tecnico' => 'Hassan Gustavo',
                'tecnico_avatar' => 'HG',
                'requerente' => 'Expedição Café',
                'categoria' => 'Impressoras & Zebra',
                'sla_total_min' => 120,
                'sla_restante_min' => 85,
                'tempo_decorrido' => 'há 20 min',
                'sla_status' => 'ok'
            ],
            7220 => [
                'tecnico' => 'Eduardo Souza',
                'tecnico_avatar' => 'ES',
                'requerente' => 'Controladoria',
                'categoria' => 'Segurança & VPN',
                'sla_total_min' => 240,
                'sla_restante_min' => 180,
                'tempo_decorrido' => 'há 1h 20m',
                'sla_status' => 'ok'
            ]
        ];

        $ticketsIter = $DB->request([
            'FROM' => 'glpi_tickets',
            'WHERE' => ['is_deleted' => 0],
            'ORDER' => 'id DESC',
            'LIMIT' => 50
        ]);

        $list = [];
        $totalAbertos = 0;
        $totalHoje = 12;
        $criticos = 0;
        $emAtendimento = 0;
        $aguardando = 0;
        $tecnicosCount = [];

        foreach ($ticketsIter as $t) {
            $id = (int)$t['id'];
            $meta = $ticketsMetadata[$id] ?? [
                'tecnico' => 'Suporte TI',
                'tecnico_avatar' => 'TI',
                'requerente' => 'Colaborador',
                'categoria' => 'Geral',
                'sla_total_min' => 120,
                'sla_restante_min' => 60,
                'tempo_decorrido' => 'recente',
                'sla_status' => 'ok'
            ];

            $totalAbertos++;
            if ($meta['sla_status'] === 'critical') $criticos++;
            if ($t['status'] == 2) $emAtendimento++;
            if ($t['status'] == 1) $aguardando++;

            $tec = $meta['tecnico'];
            $tecnicosCount[$tec] = ($tecnicosCount[$tec] ?? 0) + 1;

            $prioridadeTxt = 'Média';
            $prioridadeCor = '#10b981';
            if ($t['priority'] >= 5) {
                $prioridadeTxt = 'Muito Alta';
                $prioridadeCor = '#ef4444';
            } elseif ($t['priority'] == 4) {
                $prioridadeTxt = 'Alta';
                $prioridadeCor = '#f59e0b';
            } elseif ($t['priority'] <= 2) {
                $prioridadeTxt = 'Baixa';
                $prioridadeCor = '#3b82f6';
            }

            $statusTxt = ($t['status'] == 1) ? 'Novo (Triagem)' : (($t['status'] == 2) ? 'Em Atendimento' : 'Pendente');

            $list[] = [
                'id' => $id,
                'name' => $t['name'],
                'content' => $t['content'],
                'status_raw' => $t['status'],
                'status_txt' => $statusTxt,
                'urgency' => $t['urgency'],
                'priority_txt' => $prioridadeTxt,
                'priority_cor' => $prioridadeCor,
                'tecnico' => $meta['tecnico'],
                'tecnico_avatar' => $meta['tecnico_avatar'],
                'requerente' => $meta['requerente'],
                'categoria' => $meta['categoria'],
                'sla_restante_min' => $meta['sla_restante_min'],
                'sla_status' => $meta['sla_status'],
                'tempo_decorrido' => $meta['tempo_decorrido']
            ];
        }

        // Ordenação inteligente: Críticos primeiro, depois por SLA restante
        usort($list, function($a, $b) {
            $scoreA = ($a['sla_status'] === 'critical' ? 1000 : ($a['sla_status'] === 'warning' ? 500 : 100)) - $a['sla_restante_min'];
            $scoreB = ($b['sla_status'] === 'critical' ? 1000 : ($b['sla_status'] === 'warning' ? 500 : 100)) - $b['sla_restante_min'];
            return $scoreB <=> $scoreA;
        });

        echo json_encode([
            'success' => true,
            'kpis' => [
                'abertos' => $totalAbertos,
                'hoje' => $totalHoje,
                'criticos' => $criticos,
                'em_atendimento' => $emAtendimento,
                'aguardando' => $aguardando,
                'tecnicos' => $tecnicosCount
            ],
            'tickets' => $list
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Ação não reconhecida.']);
} catch (\Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
