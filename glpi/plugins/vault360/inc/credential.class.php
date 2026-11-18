<?php
/**
 * Aba "Cofre / Acesso Temporário" integrada nos Ativos e Chamados do GLPI
 */

class PluginVault360Credential extends CommonGLPI
{
    public static function getTypeName($nb = 0)
    {
        return 'Vault360 — Acesso Temporário';
    }

    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if ($_SESSION['glpiactiveprofile']['id'] ?? false) {
            return [
                1 => '🔒 Vault360 — Acesso Temporário'
            ];
        }
        return '';
    }

    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        self::showAccessTab($item);
        return true;
    }

    public static function showAccessTab(CommonGLPI $item)
    {
        global $DB, $CFG_GLPI;

        $itemtype = get_class($item);
        $items_id = $item->getID();

        // 1. Localiza credenciais vinculadas a este item ou disponíveis
        $credentials = [];
        if ($itemtype === 'Ticket') {
            // Em chamados, busca credenciais associadas aos computadores vinculados ao chamado
            $iterator = $DB->request([
                'FROM'  => 'glpi_items_tickets',
                'WHERE' => ['tickets_id' => $items_id]
            ]);
            $item_pairs = [];
            foreach ($iterator as $row) {
                $item_pairs[] = "(`itemtype` = '{$row['itemtype']}' AND `items_id` = {$row['items_id']})";
            }

            if (!empty($item_pairs)) {
                $whereClause = implode(' OR ', $item_pairs);
                $cIterator = $DB->request([
                    'FROM'  => 'glpi_plugin_vault360_credentials',
                    'WHERE' => [$DB->raw($whereClause)]
                ]);
                foreach ($cIterator as $c) {
                    $credentials[] = $c;
                }
            }

            // Se ainda não houver vínculo específico, lista todas as credenciais ativas para seleção
            if (empty($credentials)) {
                $cIterator = $DB->request([
                    'FROM'  => 'glpi_plugin_vault360_credentials',
                    'WHERE' => ['status' => 'Ativo'],
                    'ORDER' => 'name ASC',
                    'LIMIT' => 50
                ]);
                foreach ($cIterator as $c) {
                    $credentials[] = $c;
                }
            }
        } else {
            // No próprio ativo (Computer, NetworkEquipment, etc.)
            $cIterator = $DB->request([
                'FROM'  => 'glpi_plugin_vault360_credentials',
                'WHERE' => [
                    'itemtype' => $itemtype,
                    'items_id' => $items_id
                ]
            ]);
            foreach ($cIterator as $c) {
                $credentials[] = $c;
            }
        }

        // Renderiza o template Twig
        echo TemplateRenderer::getInstance()->render('@vault360/temporary_access.html.twig', [
            'itemtype'    => $itemtype,
            'items_id'    => $items_id,
            'is_ticket'   => ($itemtype === 'Ticket'),
            'credentials' => $credentials,
            'root_doc'    => $CFG_GLPI['root_doc'],
        ]);
    }
}
