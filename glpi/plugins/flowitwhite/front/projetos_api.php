<?php
define('GLPI_ROOT', '/var/www/glpi');
require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

// Permissões
if (!Session::haveRight('project', READ) && !Session::haveRight('config', READ) && Session::getCurrentInterface() !== 'central') {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso não autorizado.']);
    exit;
}

$action = $_GET['action'] ?? 'list';

// 1. Carregar payload de projetos consolidados do FlowIT (Curva S, EVM, Riscos 4x4, RACI, Contratos e Encerramento)
$flowitPayloadFile = __DIR__ . '/projects_data.json';
$flowitData = [
    'projects' => [],
    'reports'  => []
];

if (file_exists($flowitPayloadFile)) {
    $raw = file_get_contents($flowitPayloadFile);
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $flowitData = $decoded;
    }
}

// 2. Carregar projetos cadastrados nativamente no GLPI (glpi_projects) para integrá-los e sincronizá-los
global $DB;
$glpiProjects = [];

if (isset($DB) && is_object($DB)) {
    $iterator = $DB->request([
        'SELECT'    => [
            'p.id',
            'p.name',
            'p.code',
            'p.priority',
            'p.percent_done',
            'p.plan_start_date',
            'p.plan_end_date',
            'p.date',
            'ps.name AS state_name'
        ],
        'FROM'      => 'glpi_projects AS p',
        'LEFT JOIN' => [
            'glpi_projectstates AS ps' => [
                'FKEY' => [
                    'p'  => 'projectstates_id',
                    'ps' => 'id'
                ]
            ]
        ],
        'WHERE'     => [
            'p.is_deleted' => 0
        ]
    ]);

    foreach ($iterator as $data) {
        $projId = 'glpi-' . $data['id'];
        $projTitle = $data['name'];
        $projStatus = !empty($data['state_name']) ? $data['state_name'] : 'Em andamento';
        $projProgress = intval($data['percent_done'] ?? 0);

        // Se o projeto do GLPI ainda não estiver nos relatórios, criar estrutura executiva sintética
        if (!isset($flowitData['reports'][$projId])) {
            $flowitData['projects'][] = [
                'id'       => $projId,
                'title'    => $projTitle,
                'status'   => $projStatus,
                'category' => 'ITSM GLPI'
            ];

            $flowitData['reports'][$projId] = [
                'project' => [
                    'id'             => $projId,
                    'title'          => $projTitle,
                    'category'       => 'ITSM GLPI',
                    'type'           => 'Operacional',
                    'status'         => $projStatus,
                    'owner'          => 'Gestor de TI',
                    'sponsor'        => 'TI Corporativa',
                    'erpProjectCode' => $data['code'] ?? '',
                    'startDate'      => !empty($data['plan_start_date']) ? substr($data['plan_start_date'], 0, 10) : substr($data['date'], 0, 10),
                    'endDate'        => !empty($data['plan_end_date']) ? substr($data['plan_end_date'], 0, 10) : null,
                    'totalValue'     => 100000,
                    'hourlyRate'     => 120
                ],
                'metrics' => [
                    'budget'                => 100000,
                    'pv'                    => 100000,
                    'ev'                    => ($projProgress / 100) * 100000,
                    'ac'                    => ($projProgress / 100) * 95000,
                    'extraCosts'            => 0,
                    'laborCost'             => 0,
                    'totalActual'           => ($projProgress / 100) * 95000,
                    'totalEffortHours'      => 0,
                    'totalEffortMinutes'    => 0,
                    'progress'              => $projProgress,
                    'cpi'                   => $projProgress > 0 ? 1.05 : 1.00,
                    'spi'                   => $projProgress > 0 ? 1.00 : 0.80,
                    'activeRisksCount'      => 0,
                    'totalRiskCostExposure' => 0,
                    'totalRiskDaysExposure' => 0
                ],
                'milestones' => [
                    [
                        'id'      => 'm1',
                        'title'   => 'Iniciação e Planejamento',
                        'dueDate' => $data['plan_start_date'] ?? date('Y-m-d'),
                        'weight'  => 30,
                        'status'  => $projProgress >= 30 ? 'Concluído' : 'Em andamento'
                    ],
                    [
                        'id'      => 'm2',
                        'title'   => 'Execução e Homologação Técnica',
                        'dueDate' => $data['plan_end_date'] ?? date('Y-m-d', strtotime('+30 days')),
                        'weight'  => 70,
                        'status'  => $projProgress >= 100 ? 'Concluído' : 'Planejado'
                    ]
                ],
                'budgetLines' => [],
                'costBreakdown' => [],
                'effortTasks' => [],
                'risks' => [],
                'riskExposure' => [
                    'activeCount'  => 0,
                    'costExposure' => 0,
                    'daysExposure' => 0
                ],
                'stakeholders' => [],
                'raciSummary' => [
                    'responsible' => 1,
                    'accountable' => 1,
                    'consulted'   => 0,
                    'informed'    => 1
                ],
                'closure' => null,
                'contractSlas' => [],
                'supplierPerformance' => [
                    'totalMonitored'     => 0,
                    'totalPenaltyAmount' => 0,
                    'avgAvailability'    => null
                ]
            ];
        }
    }
}

// 3. Responder de acordo com a ação solicitada
if ($action === 'list') {
    echo json_encode($flowitData['projects'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'report') {
    $id = $_GET['id'] ?? '';
    if (isset($flowitData['reports'][$id])) {
        echo json_encode($flowitData['reports'][$id], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else if (!empty($flowitData['projects'])) {
        // Fallback para o primeiro projeto caso o ID não seja encontrado diretamente
        $firstId = $flowitData['projects'][0]['id'];
        echo json_encode($flowitData['reports'][$firstId], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Projeto não encontrado.'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// Retorna tudo se nenhuma ação específica foi pedida
echo json_encode($flowitData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
