<?php
/**
 * Endpoints AJAX e Tela de Visualização/Teste de Acesso Temporário (front/access.php)
 * Integrado com HashiCorp Vault (Gestão PAM)
 */

require_once __DIR__ . '/bootstrap.php';

Session::checkLoginUser();

global $DB, $CFG_GLPI;

$action = $_REQUEST['action'] ?? '';
$client = new PluginVault360VaultClient();

// =========================================================================
// 1. TELA DE VISUALIZAÇÃO DIRETA / TESTE (action=view)
// =========================================================================
if ($action === 'view') {
    $id = (int)($_GET['id'] ?? 0);
    $cred = $DB->request([
        'FROM'  => 'glpi_plugin_vault360_credentials',
        'WHERE' => ['id' => $id]
    ])->current();

    if (!$cred) {
        Html::header('Vault360 — Ativo Não Encontrado', $_SERVER['PHP_SELF'], 'tools', 'vault360_import');
        echo '<div class="container my-5"><div class="alert alert-danger">Ativo PAM não encontrado no banco de dados.</div></div>';
        Html::footer();
        exit;
    }

    Html::header("Vault360 — {$cred['name']}", $_SERVER['PHP_SELF'], 'tools', 'vault360_import');
    ?>
    <div class="container my-4" style="max-width: 980px;">
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <a href="/plugins/vault360/front/import.php" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left"></i> Voltar à Lista de Ativos
            </a>
            <span class="badge bg-light text-muted border px-3 py-2">
                ID Credencial: #<?php echo $cred['id']; ?>
            </span>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 52px; height: 52px; border-radius: 12px; background: linear-gradient(135deg, rgba(19, 138, 102, 0.12) 0%, rgba(19, 138, 102, 0.22) 100%); color: #138a66; display: grid; place-items: center; font-size: 26px; border: 1px solid rgba(19, 138, 102, 0.3);">
                            <i class="ti ti-shield-lock"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                                <?php echo htmlspecialchars($cred['name']); ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle fs-7">
                                    <?php echo htmlspecialchars($cred['status']); ?>
                                </span>
                            </h4>
                            <div class="text-muted small">
                                <?php echo htmlspecialchars($cred['modo_acesso']); ?> · Caminho no Vault: <code><?php echo htmlspecialchars($cred['vault_path']); ?></code>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-muted border px-3 py-2 rounded-pill">
                            <i class="ti ti-clock"></i> Validade: 30 minutos
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Dados do Ativo -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
                    <div class="card-header bg-white p-3 border-bottom">
                        <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="ti ti-server text-primary"></i> Detalhes do Ativo
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="p-3 rounded-3 border mb-3" style="background: #f8fafc; border-color: #e2e8f0;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Tipo:</span>
                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($cred['tipo_ativo']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Ambiente:</span>
                                <span class="badge <?php echo $cred['ambiente'] === 'Produção' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning'; ?> fw-bold">
                                    <?php echo htmlspecialchars($cred['ambiente']); ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Modo de Acesso:</span>
                                <span class="fw-bold text-dark small"><?php echo htmlspecialchars($cred['modo_acesso']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">IP / Host:</span>
                                <span class="font-monospace fw-bold text-dark small"><?php echo htmlspecialchars($cred['ip'] ?: '-'); ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Usuário Base:</span>
                                <span class="fw-bold text-dark small"><?php echo htmlspecialchars($cred['usuario'] ?: 'root/admin'); ?></span>
                            </div>
                        </div>

                        <div class="alert alert-light border small text-muted mb-4">
                            <i class="ti ti-info-circle text-primary me-1"></i>
                            Ao gerar o acesso, a credencial será lida do HashiCorp Vault e uma sessão RDP no Guacamole estará pronta para acesso direto no navegador.
                        </div>

                        <button id="v360_btn_generate" class="btn btn-primary w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2" style="background:#138a66;border-color:#138a66;" onclick="v360GenerateToken(<?php echo $cred['id']; ?>)">
                            <i class="ti ti-key"></i> Solicitar Acesso & Iniciar Sessão
                        </button>
                    </div>
                </div>
            </div>

            <!-- Painel de Credencial / Cronômetro -->
            <div class="col-lg-7">
                <div id="v360_state_waiting" class="card border-0 shadow-sm h-100 text-center p-5 d-flex flex-column align-items-center justify-content-center" style="border-radius: 14px; background: #ffffff;">
                    <div style="width: 72px; height: 72px; border-radius: 50%; background: #f1f5f9; display: grid; place-items: center; font-size: 32px; color: #94a3b8; margin-bottom: 16px;">
                        <i class="ti ti-lock-access"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Cofre Fechado</h5>
                    <p class="text-muted small mb-0" style="max-width: 380px;">
                        Clique em <strong>"Solicitar Acesso"</strong> para buscar a credencial diretamente no HashiCorp Vault e liberar a conexão remota.
                    </p>
                </div>

                <div id="v360_state_active" class="card border-0 shadow-sm h-100 p-4" style="display: none; border-radius: 14px; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                        <span class="badge bg-success text-white px-3 py-2 rounded-pill d-flex align-items-center gap-1">
                            <i class="ti ti-bolt"></i> Acesso Temporário Liberado
                        </span>

                        <div class="d-flex align-items-center gap-2 px-3 py-1 rounded-3" style="background: rgba(220, 38, 38, 0.08); border: 1px solid rgba(220, 38, 38, 0.25);">
                            <i class="ti ti-alarm text-danger fs-5"></i>
                            <span class="fw-bold text-danger fs-5 font-monospace" id="v360_timer">30:00</span>
                        </div>
                    </div>

                    <!-- Botão de Acesso Remoto Guacamole (Destaque Principal) -->
                    <div id="v360_guac_box" class="p-3 rounded-3 mb-3 text-center border" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff;">
                        <div class="fw-bold fs-5 mb-1 d-flex align-items-center justify-content-center gap-2">
                            <i class="ti ti-device-desktop"></i> Acesso Remoto Web (Apache Guacamole)
                        </div>
                        <div class="small text-white-50 mb-3">
                            Túnel RDP HTML5 criptografado com suporte a WebSockets de alta velocidade.
                        </div>
                        <a id="v360_guac_link" href="#" target="_blank" class="btn btn-light fw-bold px-4 py-2 text-primary shadow-sm">
                            <i class="ti ti-external-link me-1"></i> Abrir Estação Windows no Navegador
                        </a>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Usuário</label>
                            <div class="input-group">
                                <input type="text" id="v360_res_login" class="form-control fw-bold" readonly value="">
                                <button class="btn btn-outline-secondary" onclick="v360Copy('v360_res_login')">
                                    <i class="ti ti-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Host / Destino</label>
                            <div class="input-group">
                                <input type="text" id="v360_res_host" class="form-control fw-bold text-primary font-monospace" readonly value="">
                                <button class="btn btn-outline-secondary" onclick="v360Copy('v360_res_host')">
                                    <i class="ti ti-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Senha / Token Resgatado do Vault</label>
                        <div class="input-group input-group-lg">
                            <input type="password" id="v360_res_token" class="form-control font-monospace fw-bold text-success fs-4" readonly value="">
                            <button class="btn btn-outline-secondary" type="button" onclick="v360TogglePassword()">
                                <i class="ti ti-eye" id="v360_eye_icon"></i>
                            </button>
                            <button class="btn btn-success px-4" type="button" style="background:#138a66;border-color:#138a66;" onclick="v360Copy('v360_res_token')">
                                <i class="ti ti-copy"></i> Copiar
                            </button>
                        </div>
                        <div class="form-text text-danger small mt-2">
                            <i class="ti ti-alert-triangle"></i> Esta credencial foi recuperada via API do Vault e auditada na sessão do técnico.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                        <span class="text-muted small">
                            Lease ID: <code id="v360_res_lease" class="text-muted">--</code>
                        </span>
                        <button class="btn btn-outline-danger fw-bold d-flex align-items-center gap-1" onclick="v360Revoke()">
                            <i class="ti ti-door-exit"></i> Encerrar Sessão & Bloquear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let v360CurrentLease = '';
    let v360CountdownInterval = null;
    let v360RemainingSeconds = 1800;

    async function v360GenerateToken(credId) {
        const btn = document.getElementById('v360_btn_generate');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Consultando Vault & Guacamole...';

        try {
            const formData = new FormData();
            formData.append('action', 'generate_token');
            formData.append('credential_id', credId);
            formData.append('ticket_id', '0'); // Teste direto do admin

            const res = await fetch('/plugins/vault360/front/access.php', {
                method: 'POST',
                body: formData
            });
            const json = await res.json();

            if (json.success) {
                v360CurrentLease = json.lease_id;
                v360RemainingSeconds = json.ttl || 1800;

                document.getElementById('v360_res_login').value = json.login;
                document.getElementById('v360_res_host').value  = json.host;
                document.getElementById('v360_res_token').value = json.token;
                document.getElementById('v360_res_lease').textContent = json.lease_id;

                // Configura link do Apache Guacamole
                if (json.guacamole_url) {
                    const guacBox = document.getElementById('v360_guac_box');
                    const guacLink = document.getElementById('v360_guac_link');
                    guacBox.style.display = 'block';
                    guacLink.href = json.guacamole_url;
                }

                document.getElementById('v360_state_waiting').style.display = 'none';
                document.getElementById('v360_state_active').style.display  = 'flex';

                v360StartTimer();
            } else {
                alert('Falha na solicitação: ' + (json.error || 'Erro desconhecido.'));
            }
        } catch(e) {
            alert('Erro ao conectar ao endpoint do Vault360.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    function v360StartTimer() {
        clearInterval(v360CountdownInterval);
        const timerEl = document.getElementById('v360_timer');

        v360CountdownInterval = setInterval(() => {
            v360RemainingSeconds--;
            if (v360RemainingSeconds <= 0) {
                clearInterval(v360CountdownInterval);
                timerEl.textContent = '00:00 EXPIRADO';
                alert('O tempo de validade da credencial expirou. O lease foi revogado no Vault.');
                v360ResetState();
                return;
            }

            const m = String(Math.floor(v360RemainingSeconds / 60)).padStart(2, '0');
            const s = String(v360RemainingSeconds % 60).padStart(2, '0');
            timerEl.textContent = `${m}:${s}`;
        }, 1000);
    }

    async function v360Revoke() {
        if (!confirm('Deseja realmente encerrar a sessão? O token será revogado no Vault e a sessão encerrada.')) return;

        try {
            const formData = new FormData();
            formData.append('action', 'revoke_token');
            formData.append('lease_id', v360CurrentLease);
            formData.append('ticket_id', '0');

            await fetch('/plugins/vault360/front/access.php', {
                method: 'POST',
                body: formData
            });
        } catch(e) {}

        v360ResetState();
    }

    function v360ResetState() {
        clearInterval(v360CountdownInterval);
        v360CurrentLease = '';
        document.getElementById('v360_res_token').value = '';
        document.getElementById('v360_state_active').style.display = 'none';
        document.getElementById('v360_state_waiting').style.display = 'flex';
    }

    function v360TogglePassword() {
        const input = document.getElementById('v360_res_token');
        const icon  = document.getElementById('v360_eye_icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ti ti-eye-off';
        } else {
            input.type = 'password';
            icon.className = 'ti ti-eye';
        }
    }

    function v360Copy(elementId) {
        const input = document.getElementById(elementId);
        if (!input) return;

        const originalType = input.type;
        input.type = 'text';
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value);
        input.type = originalType;

        const originalBorder = input.style.borderColor;
        input.style.borderColor = '#138a66';
        setTimeout(() => { input.style.borderColor = originalBorder; }, 1200);
    }
    </script>
    <?php
    Html::footer();
    exit;
}

// =========================================================================
// 2. ENDPOINTS AJAX: JSON (generate_token & revoke_token)
// =========================================================================
header('Content-Type: application/json; charset=utf-8');

// GERAR TOKEN TEMPORÁRIO E VINCULAR GUACAMOLE
if ($action === 'generate_token') {
    $credentialId = (int)($_POST['credential_id'] ?? 0);
    $ticketId     = (int)($_POST['ticket_id'] ?? 0);
    $config       = Config::getConfigurationValues('plugin:vault360');

    // Validação de chamado obrigatório (exceto quando testando como admin no painel com ticket_id = 0)
    $isAdmin = Session::haveRight('config', UPDATE);
    if (!$isAdmin && ($config['require_ticket'] ?? '1') === '1' && $ticketId <= 0) {
        echo json_encode([
            'success' => false,
            'error'   => 'É obrigatório vincular esta solicitação a um Chamado aberto para fins de auditoria.'
        ]);
        exit;
    }

    // Busca credencial
    $cred = $DB->request([
        'FROM'  => 'glpi_plugin_vault360_credentials',
        'WHERE' => ['id' => $credentialId]
    ])->current();

    if (!$cred) {
        echo json_encode(['success' => false, 'error' => 'Ativo ou credencial não encontrada.']);
        exit;
    }

    $ttl = (int)($config['default_ttl'] ?? 1800);
    $userIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $userId = Session::getLoginUserID();

    // Solicita Token / Senha Temporária ao Vault
    $vaultPath = $cred['vault_path'] ?? '';
    $tokenRes = $client->generateSshOtp($cred['name'], $cred['ip'], $cred['usuario'], $ttl, $vaultPath);

    if (!$tokenRes['success']) {
        echo json_encode(['success' => false, 'error' => $tokenRes['error']]);
        exit;
    }

    // Registra Lease e Auditoria no Banco do GLPI
    $leaseDbId = $DB->insert('glpi_plugin_vault360_leases', [
        'credential_id' => $credentialId,
        'tickets_id'    => $ticketId,
        'users_id'      => $userId,
        'lease_id'      => $tokenRes['lease_id'],
        'login'         => $tokenRes['login'],
        'tipo_acesso'   => $cred['modo_acesso'],
        'ttl'           => $ttl,
        'status'        => 'ativo',
        'user_ip'       => $userIp,
        'date_creation' => date('Y-m-d H:i:s')
    ]);

    // Insere Acompanhamento Privado no Chamado (Auditoria Imutável)
    if ($ticketId > 0) {
        $userName = getUserName($userId);
        $fup = new ITILFollowup();
        $fup->add([
            'itemtype'   => 'Ticket',
            'items_id'   => $ticketId,
            'is_private' => 1,
            'content'    => "🔐 <strong>[Vault360 — PAM] Acesso Temporário Concedido</strong><br>"
                          . "Técnico: <strong>{$userName}</strong> (IP: {$userIp})<br>"
                          . "Ativo: <strong>{$cred['name']}</strong> ({$cred['ip']})<br>"
                          . "Modo: {$cred['modo_acesso']} | Usuário: <code>{$tokenRes['login']}</code><br>"
                          . "Validade: 30 minutos (Lease ID: <code>{$tokenRes['lease_id']}</code>)"
        ]);
    }

    echo json_encode([
        'success'       => true,
        'lease_id'      => $tokenRes['lease_id'],
        'login'         => $tokenRes['login'],
        'token'         => $tokenRes['token'],
        'ttl'           => $ttl,
        'host'          => $cred['ip'] ?: $cred['name'],
        'modo'          => $cred['modo_acesso']
    ]);
    exit;
}

// REVOGAR / ENCERRAR SESSÃO IMEDIATAMENTE
if ($action === 'revoke_token') {
    $leaseId  = trim((string)($_POST['lease_id'] ?? ''));
    $ticketId = (int)($_POST['ticket_id'] ?? 0);

    if (!empty($leaseId)) {
        $client->revokeLease($leaseId);

        // Atualiza status do lease
        $DB->update(
            'glpi_plugin_vault360_leases',
            ['status' => 'encerrado', 'date_revoked' => date('Y-m-d H:i:s')],
            ['lease_id' => $leaseId]
        );

        // Notifica no chamado
        if ($ticketId > 0) {
            $userId   = Session::getLoginUserID();
            $userName = getUserName($userId);
            $fup = new ITILFollowup();
            $fup->add([
                'itemtype'   => 'Ticket',
                'items_id'   => $ticketId,
                'is_private' => 1,
                'content'    => "🚪 <strong>[Vault360 — PAM] Sessão Temporária Encerrada</strong><br>"
                              . "Técnico: <strong>{$userName}</strong> encerrou o acesso manualmente.<br>"
                              . "O lease foi revogado no HashiCorp Vault."
            ]);
        }
    }

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Ação não reconhecida.']);
