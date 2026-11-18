<?php
/**
 * Plugin Vault360 - Importação e Gerenciamento de Ativos PAM
 * Visual 100% responsivo, sem scroll horizontal, adaptativo a qualquer resolução de tela
 */

require_once __DIR__ . '/bootstrap.php';

Session::checkRight('computer', READ);

global $DB, $CFG_GLPI;

$client = new PluginVault360VaultClient();

// Tratamento de Importação de Planilha (.xlsx, .csv)
$importResult = null;
if (isset($_POST['upload_file']) && isset($_FILES['spreadsheet'])) {
    Session::checkRight('computer', CREATE);
    $file = $_FILES['spreadsheet'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $importer = new PluginVault360Importer();
        $importResult = $importer->importFile($file['tmp_name'], $file['name']);
    } else {
        $importResult = ['success' => false, 'error' => 'Falha no envio do arquivo. Código: ' . $file['error']];
    }
}

// Filtros e Consulta de Ativos
$searchType = $_GET['search_type'] ?? '';
$where = [];
if (!empty($searchType)) {
    $where['tipo_ativo'] = $searchType;
}

$items = [];
$totalCreds = $DB->request(['COUNT' => 'c', 'FROM' => 'glpi_plugin_vault360_credentials'])->current()['c'] ?? 0;
$servidores = $DB->request(['COUNT' => 'c', 'FROM' => 'glpi_plugin_vault360_credentials', 'WHERE' => ['tipo_ativo' => ['Linux', 'Windows']]])->current()['c'] ?? 0;
$redes      = $DB->request(['COUNT' => 'c', 'FROM' => 'glpi_plugin_vault360_credentials', 'WHERE' => ['tipo_ativo' => 'Rede']])->current()['c'] ?? 0;
$servicos   = $DB->request(['COUNT' => 'c', 'FROM' => 'glpi_plugin_vault360_credentials', 'WHERE' => ['tipo_ativo' => 'Servico']])->current()['c'] ?? 0;

$iterator = $DB->request([
    'FROM'  => 'glpi_plugin_vault360_credentials',
    'WHERE' => $where,
    'ORDER' => 'name ASC'
]);
foreach ($iterator as $row) {
    $items[] = $row;
}
$filteredCount = count($items);

Html::header('Vault360 — Gestão de Credenciais Privilegiadas (PAM)', $_SERVER['PHP_SELF'], 'tools', 'vault360_import');

?>
<style>
/* Reset e contenção global para viewport */
*, *::before, *::after {
    box-sizing: border-box !important;
}

.v360-main-wrapper {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
    margin: 0 auto !important;
    padding: 12px 16px !important;
}

/* Container de cards e tabelas */
.v360-table-card {
    background: #ffffff !important;
    border-radius: 12px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
}

/* Tabela Responsiva com largura total e quebra automática */
table.v360-table-responsive {
    width: 100% !important;
    max-width: 100% !important;
    table-layout: fixed !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    margin-bottom: 0 !important;
}

table.v360-table-responsive thead th {
    background-color: #fafbfc !important;
    color: #64748b !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    padding: 12px 8px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    border-top: none !important;
    white-space: nowrap !important;
}

table.v360-table-responsive tbody td {
    padding: 12px 8px !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #f1f5f9 !important;
    color: #1e293b !important;
    background-color: #ffffff !important;
    font-size: 13px !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    word-break: break-word !important;
}

table.v360-table-responsive tbody tr:hover td {
    background-color: #f8fafc !important;
}

/* Definição de proporção das colunas (Soma = 100%) */
.col-asset-name   { width: 17% !important; }
.col-asset-ip     { width: 13% !important; }
.col-asset-type   { width: 7% !important; }
.col-asset-mode   { width: 19% !important; }
.col-asset-env    { width: 9% !important; }
.col-asset-user   { width: 14% !important; }
.col-asset-vault  { width: 13% !important; }
.col-asset-action { width: 8% !important; text-align: center !important; }

/* 1. Nome do Ativo */
.v360-asset-name {
    font-size: 13.5px !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    line-height: 1.3 !important;
    display: block !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
}

/* 2. Badge de IP / Host */
.v360-badge-ip {
    display: inline-block !important;
    background-color: #f1f5f9 !important;
    color: #334155 !important;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    padding: 3px 6px !important;
    border-radius: 4px !important;
    border: 1px solid #e2e8f0 !important;
    max-width: 100% !important;
    word-break: break-all !important;
}

/* 3. Badge Tipo */
.v360-badge-tipo {
    display: inline-block !important;
    background-color: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #bae6fd !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    padding: 3px 8px !important;
    border-radius: 9999px !important;
    white-space: nowrap !important;
}

/* 4. Modo de Acesso */
.v360-access-mode {
    font-size: 12px !important;
    color: #334155 !important;
    line-height: 1.35 !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
}

/* 5. Badge Ambiente */
.v360-badge-ambiente {
    display: inline-block !important;
    background-color: #fee2e2 !important;
    color: #b91c1c !important;
    border: 1px solid #fecaca !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    padding: 3px 8px !important;
    border-radius: 9999px !important;
    white-space: nowrap !important;
}

/* 6. Usuário */
.v360-user-name {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    word-break: break-all !important;
    line-height: 1.3 !important;
    display: block !important;
}

/* 7. Caminho Vault */
.v360-vault-path {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    font-size: 11px !important;
    color: #64748b !important;
    word-break: break-all !important;
    line-height: 1.25 !important;
}

/* 8. Botão Ações */
a.v360-btn-testar {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    background-color: #ffffff !important;
    color: #0284c7 !important;
    border: 1px solid #0284c7 !important;
    border-radius: 9999px !important;
    padding: 4px 10px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    transition: all 0.15s ease-in-out !important;
}

a.v360-btn-testar:hover {
    background-color: #0284c7 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2) !important;
}

/* Em resoluções menores, adapta para permitir rolagem suave sem quebrar a janela */
@media (max-width: 1200px) {
    .v360-table-wrapper {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    table.v360-table-responsive {
        min-width: 1050px !important;
        table-layout: auto !important;
    }
}
</style>

<!-- Container Principal com largura 100% estrita -->
<div class="v360-main-wrapper">

    <!-- CARDS DE ESTATÍSTICAS -->
    <div class="row g-3 mb-3">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius:12px;">
                <span class="text-muted small fw-bold text-uppercase">Total de Ativos PAM</span>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $totalCreds; ?></h3>
                <span class="text-success small fw-semibold"><i class="ti ti-shield-check"></i> Gravados no Vault</span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius:12px;">
                <span class="text-muted small fw-bold text-uppercase">Servidores (Linux/Win)</span>
                <h3 class="fw-bold mb-0 text-primary"><?php echo $servidores; ?></h3>
                <span class="text-muted small">SSH / RDP / Proxmox</span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius:12px;">
                <span class="text-muted small fw-bold text-uppercase">Equipamentos de Rede</span>
                <h3 class="fw-bold mb-0 text-warning"><?php echo $redes; ?></h3>
                <span class="text-muted small">MikroTik / Ubiquiti / Rádios</span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius:12px;">
                <span class="text-muted small fw-bold text-uppercase">Serviços & Sistemas Web</span>
                <h3 class="fw-bold mb-0 text-info"><?php echo $servicos; ?></h3>
                <span class="text-muted small">Bancos / SaaS / Contas / E-mails</span>
            </div>
        </div>
    </div>

    <!-- ÁREA DE UPLOAD (ACORDEÃO / RECOLHÍVEL) -->
    <div class="card shadow-sm border-0 mb-3" style="border-radius:12px;">
        <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-file-upload fs-4 text-success"></i>
                <h6 class="mb-0 fw-bold">Importar / Atualizar Planilha de Ativos</h6>
            </div>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#uploadCollapse">
                <i class="ti ti-chevron-down"></i> Upload de Arquivo
            </button>
        </div>

        <div class="collapse <?php echo ($importResult !== null) ? 'show' : ''; ?>" id="uploadCollapse">
            <div class="card-body p-3">
                <?php if ($importResult !== null): ?>
                    <?php if ($importResult['success']): ?>
                        <div class="alert alert-success d-flex align-items-start gap-3">
                            <i class="ti ti-circle-check fs-3 flex-shrink-0 mt-1"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Planilha processada com sucesso!</h6>
                                <div class="small"><strong><?php echo $importResult['imported']; ?></strong> ativos importados/atualizados em <strong><?php echo $importResult['sheets_count']; ?></strong> abas.</div>
                                <div class="small"><strong><?php echo $importResult['vault_saved']; ?></strong> senhas gravadas no HashiCorp Vault.</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger d-flex align-items-center gap-3">
                            <i class="ti ti-alert-triangle fs-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Erro durante a importação</h6>
                                <div class="small"><?php echo htmlspecialchars($importResult['error']); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="p-3 border-2 border-dashed rounded-3 text-center mb-3" style="background:#f8fafc; border-color:#cbd5e1;">
                        <i class="ti ti-file-spreadsheet text-success" style="font-size:36px;"></i>
                        <h6 class="mt-2 mb-1 fw-bold">Selecione o arquivo de inventário (.xlsx)</h6>
                        <input type="file" name="spreadsheet" class="form-control form-control-sm w-50 mx-auto" accept=".xlsx,.csv,.xls" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="text-muted small">
                            <i class="ti ti-shield-check text-success"></i> Senhas salvas criptografadas no Vault.
                        </span>
                        <button type="submit" name="upload_file" class="btn btn-success btn-sm px-3 py-2 fw-semibold" style="background:#138a66;border-color:#138a66;">
                            <i class="ti ti-upload"></i> Processar Planilha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- BARRA DE FILTRO POR TIPO -->
    <div class="card shadow-sm border-0 mb-3" style="border-radius:12px; background:#ffffff;">
        <div class="card-body p-2 px-3">
            <form method="GET" action="" class="row g-2 align-items-center">
                <div class="col-md-5 col-lg-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="ti ti-category"></i>
                        </span>
                        <select name="search_type" id="v360_filter_type" class="form-select border-start-0" onchange="this.form.submit()">
                            <option value="">Todos os Tipos de Ativo (Todos)</option>
                            <option value="Linux" <?php echo ($searchType === 'Linux') ? 'selected' : ''; ?>>Servidor Linux</option>
                            <option value="Windows" <?php echo ($searchType === 'Windows') ? 'selected' : ''; ?>>Servidor Windows</option>
                            <option value="Rede" <?php echo ($searchType === 'Rede') ? 'selected' : ''; ?>>Equipamento de Rede / Rádio</option>
                            <option value="Servico" <?php echo ($searchType === 'Servico') ? 'selected' : ''; ?>>Serviço / Aplicação Web</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-7 col-lg-8 d-flex gap-2 justify-content-md-end align-items-center">
                    <?php if (!empty($searchType)): ?>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-x"></i> Ver Todos
                        </a>
                    <?php endif; ?>
                    <a href="/front/computer.php" class="btn btn-sm btn-outline-secondary" title="Ver no CMDB GLPI">
                        <i class="ti ti-device-desktop"></i> Inventário GLPI
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- TABELA COMPLETA ENQUADRADA NA LARGURA VISÍVEL -->
    <div class="v360-table-card">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background:#ffffff;">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-lock-access fs-4 text-success"></i>
                <h6 class="mb-0 fw-bold" style="color:#0f172a;">Ativos Cadastrados no Cofre PAM</h6>
                <span class="badge bg-light text-dark border px-2 py-1 ms-1" style="font-size:11px;">
                    <?php echo $filteredCount; ?> ativo(s)
                </span>
            </div>
            <div>
                <a href="/front/computer.php" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-external-link"></i> Ver no Inventário GLPI
                </a>
            </div>
        </div>

        <div class="v360-table-wrapper w-100">
            <table class="table v360-table-responsive align-middle">
                <thead>
                    <tr>
                        <th class="col-asset-name">NOME DO ATIVO</th>
                        <th class="col-asset-ip">IP / HOST</th>
                        <th class="col-asset-type">TIPO</th>
                        <th class="col-asset-mode">MODO DE ACESSO</th>
                        <th class="col-asset-env">AMBIENTE</th>
                        <th class="col-asset-user">USUÁRIO</th>
                        <th class="col-asset-vault">CAMINHO VAULT</th>
                        <th class="col-asset-action">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($items) === 0): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="ti ti-database-off fs-1 d-block mb-2 text-secondary"></i>
                                Nenhum ativo encontrado para o filtro selecionado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $row): 
                            $tipoLabel = htmlspecialchars($row['tipo_ativo']);
                            if ($tipoLabel === 'Servico') {
                                $tipoLabel = 'Serviço';
                            }
                        ?>
                        <tr>
                            <!-- 1. NOME DO ATIVO -->
                            <td>
                                <span class="v360-asset-name"><?php echo htmlspecialchars($row['name']); ?></span>
                                <?php if (!empty($row['localizacao'])): ?>
                                    <div class="text-muted" style="font-size: 11px; margin-top: 2px;">
                                        <i class="ti ti-map-pin"></i> <?php echo htmlspecialchars($row['localizacao']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- 2. IP / HOST -->
                            <td>
                                <?php if (!empty($row['ip'])): ?>
                                    <span class="v360-badge-ip"><?php echo htmlspecialchars($row['ip']); ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- 3. TIPO -->
                            <td>
                                <span class="v360-badge-tipo">
                                    <?php echo $tipoLabel; ?>
                                </span>
                            </td>

                            <!-- 4. MODO DE ACESSO -->
                            <td>
                                <div class="v360-access-mode">
                                    <?php echo nl2br(htmlspecialchars($row['modo_acesso'])); ?>
                                </div>
                            </td>

                            <!-- 5. AMBIENTE -->
                            <td>
                                <span class="v360-badge-ambiente">
                                    <?php echo htmlspecialchars($row['ambiente']); ?>
                                </span>
                            </td>

                            <!-- 6. USUÁRIO -->
                            <td>
                                <span class="v360-user-name">
                                    <?php echo htmlspecialchars($row['usuario']); ?>
                                </span>
                            </td>

                            <!-- 7. CAMINHO VAULT -->
                            <td>
                                <div class="v360-vault-path">
                                    <?php echo htmlspecialchars($row['vault_path']); ?>
                                </div>
                            </td>

                            <!-- 8. AÇÕES -->
                            <td class="text-center">
                                <a href="/plugins/vault360/front/access.php?action=view&id=<?php echo $row['id']; ?>" 
                                   class="v360-btn-testar" target="_blank" title="Abrir cofre e resgatar senha">
                                    <i class="ti ti-key"></i> Testar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php

Html::footer();
