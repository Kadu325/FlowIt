<?php
/**
 * Tela de Configuração do HashiCorp Vault (front/config.form.php)
 */

require_once __DIR__ . '/bootstrap.php';

Session::checkRight('config', UPDATE);

$vaultClient = new PluginVault360VaultClient();
$message = '';
$testResult = null;

if (isset($_POST['update_config'])) {
    Config::setConfigurationValues('plugin:vault360', [
        'vault_url'     => rtrim($_POST['vault_url'], '/'),
        'vault_token'   => trim($_POST['vault_token']),
        'vault_mount'   => trim($_POST['vault_mount']),
        'default_ttl'   => (int)$_POST['default_ttl'],
        'require_ticket'=> isset($_POST['require_ticket']) ? '1' : '0'
    ]);
    $message = '<div class="alert alert-success">Configurações do HashiCorp Vault atualizadas com sucesso!</div>';
    $vaultClient = new PluginVault360VaultClient();
}

if (isset($_POST['test_connection'])) {
    $testResult = $vaultClient->testConnection();
}

$config = Config::getConfigurationValues('plugin:vault360');

Html::header('Vault360 — Configuração do HashiCorp Vault', $_SERVER['PHP_SELF'], 'config', 'plugins');

?>
<div class="container my-4" style="max-width: 900px;">
    <div class="card shadow-sm border-0" style="border-radius: 14px;">
        <div class="card-header bg-white d-flex align-items-center justify-content-between p-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(19, 138, 102, 0.1); color: #138a66; display: grid; place-items: center; font-size: 22px;">
                    <i class="ti ti-shield-lock"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Vault360 — Conexão HashiCorp Vault</h4>
                    <span class="text-muted fs-6">Motor Criptográfico Externo e Gestão de Privilégios PAM</span>
                </div>
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                GLPI 12 & Vault v1.15+
            </span>
        </div>

        <div class="card-body p-4">
            <?php echo $message; ?>

            <?php if ($testResult !== null): ?>
                <?php if ($testResult['success']): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2">
                        <i class="ti ti-circle-check fs-4"></i>
                        <div>
                            <strong>Conexão bem-sucedida com o Vault!</strong><br>
                            HTTP Code: <?php echo $testResult['http_code']; ?> | Status: Ativo / Inicializado.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2">
                        <i class="ti ti-alert-triangle fs-4"></i>
                        <div>
                            <strong>Falha ao conectar no Vault:</strong><br>
                            HTTP Code: <?php echo $testResult['http_code']; ?> | 
                            <?php echo htmlspecialchars(json_encode($testResult['details'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-bold text-uppercase fs-7 text-muted">URL da API do HashiCorp Vault</label>
                    <input type="url" name="vault_url" class="form-control" 
                           value="<?php echo htmlspecialchars($config['vault_url'] ?? 'https://vault.fazendaprogresso.info:8200'); ?>" 
                           placeholder="https://vault.seuservidor.com:8200" required>
                    <div class="form-text">Endereço seguro (HTTPS) do cluster ou instância do Vault.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-uppercase fs-7 text-muted">Token de Acesso (Root / AppRole Token)</label>
                    <div class="input-group">
                        <input type="password" name="vault_token" id="vault_token" class="form-control" 
                               value="<?php echo htmlspecialchars($config['vault_token'] ?? ''); ?>" 
                               placeholder="hvs.CAES..." required>
                        <button class="btn btn-outline-secondary" type="button" onclick="toggleTokenVisibility()">
                            <i class="ti ti-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <div class="form-text">Token gerado no Vault com políticas de escrita em <code>secret/</code> e <code>ssh/</code>.</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-uppercase fs-7 text-muted">Mount Path KV v2</label>
                        <input type="text" name="vault_mount" class="form-control" 
                               value="<?php echo htmlspecialchars($config['vault_mount'] ?? 'secret'); ?>" required>
                        <div class="form-text">Caminho base dos segredos KV (ex: <code>secret</code>).</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-uppercase fs-7 text-muted">Tempo Padrão de Validade OTP (TTL)</label>
                        <select name="default_ttl" class="form-select">
                            <option value="900" <?php echo ($config['default_ttl'] ?? '') == '900' ? 'selected' : ''; ?>>15 Minutos</option>
                            <option value="1800" <?php echo ($config['default_ttl'] ?? '1800') == '1800' ? 'selected' : ''; ?>>30 Minutos (Recomendado)</option>
                            <option value="3600" <?php echo ($config['default_ttl'] ?? '') == '3600' ? 'selected' : ''; ?>>1 Hora</option>
                            <option value="7200" <?php echo ($config['default_ttl'] ?? '') == '7200' ? 'selected' : ''; ?>>2 Horas</option>
                        </select>
                        <div class="form-text">Tempo máximo em que a credencial temporária permanece válida.</div>
                    </div>
                </div>

                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="require_ticket" id="require_ticket" 
                           value="1" <?php echo ($config['require_ticket'] ?? '1') == '1' ? 'checked' : ''; ?>>
                    <label class="form-check-label fw-bold text-dark" for="require_ticket">
                        Exigir Chamado (Ticket) Aberto para gerar Acesso Temporário
                    </label>
                    <div class="text-muted small">Evita que técnicos gerem acessos sem um ticket de suporte vinculado para auditoria.</div>
                </div>

                <div class="d-flex justify-content-between gap-2 pt-3 border-top">
                    <button type="submit" name="test_connection" class="btn btn-outline-primary">
                        <i class="ti ti-wifi"></i> Testar Conectividade
                    </button>
                    <button type="submit" name="update_config" class="btn btn-primary" style="background:#138a66; border-color:#138a66;">
                        <i class="ti ti-device-floppy"></i> Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleTokenVisibility() {
    const input = document.getElementById('vault_token');
    const icon = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ti ti-eye-off';
    } else {
        input.type = 'password';
        icon.className = 'ti ti-eye';
    }
}
</script>
<?php

Html::footer();
