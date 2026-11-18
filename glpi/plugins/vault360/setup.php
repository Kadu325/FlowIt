<?php
/**
 * Plugin Vault360 - Integração GLPI & HashiCorp Vault
 * Gestão de Credenciais Privilegiadas (PAM) e Acesso Temporário OTP
 */

use Glpi\Plugin\Hooks;

function plugin_init_vault360()
{
    global $PLUGIN_HOOKS;

    // CSRF Compliance
    $PLUGIN_HOOKS['csrf_compliant']['vault360'] = true;

    // Config page
    $PLUGIN_HOOKS[Hooks::CONFIG_PAGE]['vault360'] = 'front/config.form.php';

    // Menu entries: Ferramentas > Vault360
    $PLUGIN_HOOKS[Hooks::REDEFINE_MENUS]['vault360'] = 'plugin_vault360_redefine_menus';

    // Registrar abas nos Ativos e Chamados
    Plugin::registerClass('PluginVault360Credential', [
        'addtabon' => [
            'Ticket',
            'Computer',
            'NetworkEquipment',
            'Software',
            'Database'
        ]
    ]);
}

function plugin_version_vault360()
{
    return [
        'name'           => 'Vault360 — Cofre PAM & Acesso Temporário',
        'version'        => '1.0.0',
        'author'         => 'Equipe FlowIT / Fazenda Progresso',
        'license'        => 'GPLv3+',
        'homepage'       => 'https://flowit.fazendaprogresso.info',
        'requirements'   => [
            'glpi' => [
                'min' => '12.0.0',
                'max' => '12.1.0'
            ]
        ]
    ];
}

function plugin_vault360_check_prerequisites()
{
    return defined('GLPI_VERSION') && version_compare(GLPI_VERSION, '12.0.0', '>=');
}

function plugin_vault360_check_config($verbose = false)
{
    return true;
}

function plugin_vault360_redefine_menus($menu)
{
    // Adiciona menu em Ferramentas (Tools)
    if (isset($menu['tools']['content'])) {
        $menu['tools']['content']['vault360_import'] = [
            'title' => 'Vault360 — Importar Planilha',
            'page'  => '/plugins/vault360/front/import.php',
            'icon'  => 'ti ti-file-spreadsheet',
        ];
        $menu['tools']['content']['vault360_config'] = [
            'title' => 'Vault360 — Configurações',
            'page'  => '/plugins/vault360/front/config.form.php',
            'icon'  => 'ti ti-shield-lock',
        ];
    }

    // Adiciona menu em Gestão (Management)
    if (isset($menu['management']['content'])) {
        $menu['management']['content']['vault360_import'] = [
            'title' => 'Vault360 (Cofre PAM)',
            'page'  => '/plugins/vault360/front/import.php',
            'icon'  => 'ti ti-shield-lock',
        ];
    }

    return $menu;
}
