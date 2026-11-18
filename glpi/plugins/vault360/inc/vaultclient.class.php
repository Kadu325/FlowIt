<?php
/**
 * Conector com a API REST do HashiCorp Vault
 */

class PluginVault360VaultClient
{
    private string $vaultUrl;
    private string $vaultToken;
    private string $mountPath;

    public function __construct()
    {
        $config = Config::getConfigurationValues('plugin:vault360');
        $this->vaultUrl   = rtrim($config['vault_url'] ?? 'https://vault.fazendaprogresso.info:8200', '/');
        $this->vaultToken = $config['vault_token'] ?? '';
        $this->mountPath  = trim($config['vault_mount'] ?? 'secret', '/');
    }

    public function isConfigured(): bool
    {
        return !empty($this->vaultUrl) && !empty($this->vaultToken);
    }

    public function testConnection(): array
    {
        $endpoint = "{$this->vaultUrl}/v1/sys/health";
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $json = json_decode($result, true);
        return [
            'success'   => ($httpCode >= 200 && $httpCode < 400),
            'http_code' => $httpCode,
            'details'   => $json ?: ['raw' => $result, 'error' => $curlError]
        ];
    }

    /**
     * Grava credencial vinda da planilha no motor KV v2 do Vault
     * Path: /v1/{mount}/data/{path}
     */
    public function saveSecret(string $path, array $data): array
    {
        $path = trim($path, '/');
        $endpoint = "{$this->vaultUrl}/v1/{$this->mountPath}/data/{$path}";
        $payload  = json_encode(['data' => $data]);
        $response = $this->request('POST', $endpoint, $payload);

        return [
            'success' => isset($response['data']['version']),
            'version' => $response['data']['version'] ?? null,
            'raw'     => $response
        ];
    }

    /**
     * Lê segredo no motor KV v2 do Vault
     */
    public function getSecret(string $path): ?array
    {
        $path = trim($path, '/');
        $endpoint = "{$this->vaultUrl}/v1/{$this->mountPath}/data/{$path}";
        $response = $this->request('GET', $endpoint);

        return $response['data']['data'] ?? null;
    }

    /**
     * Gera Token Temporário OTP SSH ou Retorna Senha Temporária
     * Validade padrão: 30 minutos (1800s)
     */
    public function generateSshOtp(string $role, string $ip, string $username, int $ttl = 1800, string $vaultPath = ''): array
    {
        // 1. Tenta ler primeiro do motor KV v2 onde as credenciais importadas estão salvas
        $pathsToTry = [];
        if (!empty($vaultPath)) {
            $pathsToTry[] = $vaultPath;
        }
        if (!empty($role)) {
            $cleanRole = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $role);
            $pathsToTry[] = "hosts/{$cleanRole}";
        }
        if (!empty($ip)) {
            $cleanIp = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $ip);
            $pathsToTry[] = "hosts/{$cleanIp}";
        }

        foreach (array_unique($pathsToTry) as $p) {
            $secret = $this->getSecret($p);
            if ($secret && !empty($secret['password'])) {
                return [
                    'success'  => true,
                    'lease_id' => 'vault-kv-lease-' . bin2hex(random_bytes(10)),
                    'login'    => $secret['username'] ?? $username,
                    'token'    => $secret['password'],
                    'ttl'      => $ttl,
                    'mode'     => 'KV_STORED'
                ];
            }
        }

        // 2. Se não encontrou no KV, tenta o motor dinâmico SSH OTP (caso habilitado no Vault)
        $endpoint = "{$this->vaultUrl}/v1/ssh/creds/{$role}";
        $payload  = json_encode([
            'ip'       => $ip,
            'username' => $username,
            'ttl'      => "{$ttl}s"
        ]);
        $response = $this->request('POST', $endpoint, $payload);

        if (isset($response['data']['key'])) {
            return [
                'success'  => true,
                'lease_id' => $response['lease_id'] ?? ('local-lease-' . bin2hex(random_bytes(8))),
                'login'    => $response['data']['username'] ?? $username,
                'token'    => $response['data']['key'],
                'ttl'      => $response['lease_duration'] ?? $ttl
            ];
        }

        return [
            'success'  => false,
            'error'    => $response['errors'][0] ?? 'Falha ao solicitar credencial ao Vault (Ativo não encontrado ou sem permissão).'
        ];
    }

    /**
     * Revoga imediatamente o lease ao encerrar a sessão
     */
    public function revokeLease(string $leaseId): bool
    {
        if (str_starts_with($leaseId, 'vault-kv-lease-') || str_starts_with($leaseId, 'local-lease-')) {
            return true;
        }

        $endpoint = "{$this->vaultUrl}/v1/sys/leases/revoke";
        $payload  = json_encode(['lease_id' => $leaseId]);
        $this->request('PUT', $endpoint, $payload);
        return true;
    }

    private function request(string $method, string $url, ?string $body = null): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Vault-Token: {$this->vaultToken}",
            "Content-Type: application/json"
        ]);
        if ($body) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true) ?? [];
    }
}
