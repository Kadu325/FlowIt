<?php
/**
 * Importador Multissheet de Planilha (XLSX/CSV) para o Inventário do GLPI & HashiCorp Vault
 * Lê TODAS as abas da pasta de trabalho (Servidores, Redes, Serviços, Rádios, etc.)
 */

use PhpOffice\PhpSpreadsheet\IOFactory;

class PluginVault360Importer
{
    private PluginVault360VaultClient $vaultClient;

    public function __construct()
    {
        $this->vaultClient = new PluginVault360VaultClient();
    }

    /**
     * Importa arquivo de planilha (.xlsx, .csv) percorrendo todas as abas
     */
    public function importFile(string $filePath): array
    {
        global $DB;

        if (!file_exists($filePath)) {
            return ['success' => false, 'error' => 'Arquivo não encontrado.'];
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheets = $spreadsheet->getAllSheets();
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Erro ao processar planilha: ' . $e->getMessage()];
        }

        if (empty($sheets)) {
            return ['success' => false, 'error' => 'Nenhuma aba encontrada na planilha.'];
        }

        $imported = 0;
        $vaultSaved = 0;
        $sheetDetails = [];
        $errors = [];

        foreach ($sheets as $sheet) {
            $sheetTitle = trim($sheet->getTitle());
            $rows = $sheet->toArray(null, true, true, true);

            if (count($rows) <= 1) {
                continue;
            }

            // Localiza a linha do cabeçalho
            $headerMap = [];
            $headerRowIndex = null;

            foreach ($rows as $rIdx => $row) {
                $hasNome = false;
                foreach ($row as $col => $val) {
                    $cleaned = trim(strtolower((string)$val));
                    // Reconhece variações comuns de cabeçalhos
                    if (in_array($cleaned, ['nome', 'ativo', 'servidor', 'host', 'equipamento', 'device'])) {
                        $hasNome = true;
                    }
                }

                if ($hasNome) {
                    $headerRowIndex = $rIdx;
                    foreach ($row as $col => $val) {
                        $normalized = trim(strtolower((string)$val));
                        // Mapeamentos flexíveis
                        if (in_array($normalized, ['nome', 'ativo', 'servidor', 'host', 'equipamento'])) {
                            $headerMap['nome'] = $col;
                        } elseif (in_array($normalized, ['ip', 'ip / host', 'endereco_ip', 'ip_address'])) {
                            $headerMap['ip'] = $col;
                        } elseif (in_array($normalized, ['tipo_ativo', 'tipo', 'so', 'sistema'])) {
                            $headerMap['tipo_ativo'] = $col;
                        } elseif (in_array($normalized, ['modo_acesso', 'acesso', 'protocolo'])) {
                            $headerMap['modo_acesso'] = $col;
                        } elseif (in_array($normalized, ['ambiente', 'env'])) {
                            $headerMap['ambiente'] = $col;
                        } elseif (in_array($normalized, ['usuario', 'user', 'login', 'username'])) {
                            $headerMap['usuario'] = $col;
                        } elseif (in_array($normalized, ['senha_inicial', 'senha', 'password', 'pass'])) {
                            $headerMap['senha_inicial'] = $col;
                        } elseif (in_array($normalized, ['localizacao', 'local', 'sala'])) {
                            $headerMap['localizacao'] = $col;
                        } elseif (in_array($normalized, ['observacao', 'obs', 'detalhes', 'descricao'])) {
                            $headerMap['observacao'] = $col;
                        }
                    }
                    break;
                }
            }

            // Se não encontrou linha com 'nome', assume Linha 1 como padrão
            if ($headerRowIndex === null) {
                $headerRowIndex = key($rows);
                $firstRow = $rows[$headerRowIndex];
                foreach ($firstRow as $col => $val) {
                    $normalized = trim(strtolower((string)$val));
                    $headerMap[$normalized] = $col;
                }
            }

            // Deduz tipo de ativo padrão baseado no nome da aba se a coluna não vier preenchida
            $defaultTipoBySheet = 'Servidor';
            $sheetLower = strtolower($sheetTitle);
            if (str_contains($sheetLower, 'rede') || str_contains($sheetLower, 'radio') || str_contains($sheetLower, 'mikrotik') || str_contains($sheetLower, 'switch') || str_contains($sheetLower, 'roteador')) {
                $defaultTipoBySheet = 'Rede';
            } elseif (str_contains($sheetLower, 'servi') || str_contains($sheetLower, 'web') || str_contains($sheetLower, 'aplic')) {
                $defaultTipoBySheet = 'Servico';
            }

            $sheetImported = 0;

            foreach ($rows as $rIdx => $row) {
                // Pula linhas de cabeçalho ou anteriores a ele
                if ($rIdx <= $headerRowIndex) {
                    continue;
                }

                $name        = trim((string)($row[$headerMap['nome'] ?? 'A'] ?? ''));
                $ip          = trim((string)($row[$headerMap['ip'] ?? 'B'] ?? ''));
                $tipoAtivo   = trim((string)($row[$headerMap['tipo_ativo'] ?? 'C'] ?? ''));
                $modoAcesso  = trim((string)($row[$headerMap['modo_acesso'] ?? 'D'] ?? ''));
                $ambiente    = trim((string)($row[$headerMap['ambiente'] ?? 'E'] ?? 'Produção'));
                $usuario     = trim((string)($row[$headerMap['usuario'] ?? 'F'] ?? 'admin'));
                $senha       = trim((string)($row[$headerMap['senha_inicial'] ?? 'G'] ?? ''));
                $localizacao = trim((string)($row[$headerMap['localizacao'] ?? 'J'] ?? ''));
                $observacao  = trim((string)($row[$headerMap['observacao'] ?? 'N'] ?? ''));

                // Pula linhas vazias ou de observações soltas da planilha
                if (empty($name) && empty($ip)) {
                    continue;
                }
                if (str_starts_with(strtolower($name), 'obs.') || str_starts_with(strtolower($name), 'observa')) {
                    continue;
                }

                // Ajusta tipo se vazio
                if (empty($tipoAtivo)) {
                    $tipoAtivo = $defaultTipoBySheet;
                }

                // Determina classe do GLPI
                $tipoLower = strtolower($tipoAtivo);
                if (str_contains($tipoLower, 'rede') || str_contains($tipoLower, 'switch') || str_contains($tipoLower, 'mikrotik') || str_contains($tipoLower, 'ubiquiti')) {
                    $itemtype = 'NetworkEquipment';
                } elseif (str_contains($tipoLower, 'servi') || str_contains($tipoLower, 'software') || str_contains($tipoLower, 'app')) {
                    $itemtype = 'Software';
                } else {
                    $itemtype = 'Computer';
                }

                // 1. Busca ou cria o Ativo no GLPI
                $items_id = $this->findOrCreateAsset($itemtype, $name, $ip, $ambiente, $localizacao);

                // 2. Grava a senha diretamente no HashiCorp Vault
                $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name ?: $ip);
                $vaultPath = "hosts/{$cleanName}";

                if (!empty($senha) && $this->vaultClient->isConfigured()) {
                    $vaultPayload = [
                        'username'    => $usuario,
                        'password'    => $senha,
                        'ip'          => $ip,
                        'modo_acesso' => $modoAcesso,
                        'ambiente'    => $ambiente,
                        'aba_origem'  => $sheetTitle,
                        'updated_at'  => date('Y-m-d H:i:s'),
                        'source'      => 'GLPI_Vault360_Import'
                    ];
                    $vaultRes = $this->vaultClient->saveSecret($vaultPath, $vaultPayload);
                    if ($vaultRes['success']) {
                        $vaultSaved++;
                    }
                }

                // 3. Salva metadados na tabela glpi_plugin_vault360_credentials
                $exists = $DB->request([
                    'FROM'  => 'glpi_plugin_vault360_credentials',
                    'WHERE' => [
                        'name'    => $name,
                        'usuario' => $usuario
                    ]
                ])->current();

                $data = [
                    'name'        => $name,
                    'itemtype'    => $itemtype,
                    'items_id'    => $items_id,
                    'ip'          => $ip,
                    'tipo_ativo'  => $tipoAtivo,
                    'modo_acesso' => $modoAcesso ?: 'SSH / Web',
                    'ambiente'    => $ambiente ?: 'Produção',
                    'usuario'     => $usuario,
                    'vault_path'  => $vaultPath,
                    'status'      => 'Ativo',
                    'localizacao' => $localizacao,
                    'observacao'  => ($observacao ? "{$observacao} | " : '') . "Aba: {$sheetTitle}"
                ];

                if ($exists) {
                    $DB->update('glpi_plugin_vault360_credentials', $data, ['id' => $exists['id']]);
                } else {
                    $DB->insert('glpi_plugin_vault360_credentials', $data);
                }

                $imported++;
                $sheetImported++;
            }

            if ($sheetImported > 0) {
                $sheetDetails[] = "{$sheetTitle}: {$sheetImported} ativos";
            }
        }

        return [
            'success'       => true,
            'imported'      => $imported,
            'vault_saved'   => $vaultSaved,
            'sheets_count'  => count($sheets),
            'sheet_details' => $sheetDetails,
            'errors'        => $errors
        ];
    }

    private function findOrCreateAsset(string $itemtype, string $name, string $ip, string $ambiente, string $loc): int
    {
        global $DB;
        $table = getTableForItemType($itemtype);

        // Busca pelo nome
        $item = $DB->request([
            'FROM'  => $table,
            'WHERE' => ['name' => $name]
        ])->current();

        if ($item) {
            return (int)$item['id'];
        }

        // Cria o ativo se não existir
        $asset = getItemForItemtype($itemtype);
        $newId = $asset->add([
            'name'        => $name,
            'comment'     => "Cadastrado pelo Vault360 em " . date('d/m/Y H:i') . ($ip ? " | IP: {$ip}" : ''),
            'entities_id' => $_SESSION['glpiactive_entity'] ?? 0,
            'is_recursive'=> 1
        ]);

        return (int)$newId;
    }
}
