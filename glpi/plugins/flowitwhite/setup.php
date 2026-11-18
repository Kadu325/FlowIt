<?php
/** FlowIT Branco e Módulo de Compras — extensão homologada. */
use Glpi\Plugin\Hooks;

function plugin_init_flowitwhite()
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['flowitwhite'] = ['favicon.js'];
    $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT_ANONYMOUS_PAGE]['flowitwhite'] = ['favicon.js'];
    $PLUGIN_HOOKS[Hooks::ADD_CSS]['flowitwhite'] = ['flowit-bundle.css'];
    $PLUGIN_HOOKS[Hooks::ADD_CSS_ANONYMOUS_PAGE]['flowitwhite'] = ['flowit-bundle.css'];

    if (is_file(__DIR__ . '/public/logo-flowit.png')) {
        $PLUGIN_HOOKS[Hooks::ADD_CSS]['flowitwhite'][] = 'brand.css';
        $PLUGIN_HOOKS[Hooks::ADD_CSS_ANONYMOUS_PAGE]['flowitwhite'][] = 'brand.css';
    }

    // Entrada no Menu Helpdesk e Redefine Menus
    $PLUGIN_HOOKS[Hooks::HELPDESK_MENU_ENTRY]['flowitwhite'] = '/front/compras.php';
    $PLUGIN_HOOKS[Hooks::HELPDESK_MENU_ENTRY_ICON]['flowitwhite'] = 'ti ti-shopping-cart';
    $PLUGIN_HOOKS[Hooks::REDEFINE_MENUS]['flowitwhite'] = 'plugin_flowitwhite_redefine_menus';
}

function plugin_version_flowitwhite()
{
    return [
        'name' => 'FlowIT — Tema e Módulo de Compras',
        'version' => '0.2.0',
        'author' => 'Projeto FlowIT',
        'license' => 'GPLv3+',
        'requirements' => ['glpi' => ['min' => '12.0.1', 'max' => '12.0.2']],
    ];
}

function plugin_flowitwhite_check_prerequisites()
{
    return defined('GLPI_VERSION') && version_compare(GLPI_VERSION, '12.0.1', '>=')
        && version_compare(GLPI_VERSION, '12.0.2', '<');
}

function plugin_flowitwhite_check_config($verbose = false)
{
    return true;
}
