<?php
/**
 * FlowIT — Hook de Integração de Menus no GLPI
 */

function plugin_flowitwhite_redefine_menus($menu)
{
    // 1. Menu Simplificado (Helpdesk / Solicitante)
    if (isset($menu['tickets'])) {
        $menu['analytics_dashboard'] = [
            'title'   => 'Dashboard Analytics 360',
            'default' => '/plugins/flowitwhite/front/analytics_view.php',
            'icon'    => 'ti ti-chart-dots',
            'display' => true,
        ];
        $menu['compras'] = [
            'title'   => 'Solicitação de Compra',
            'default' => '/plugins/flowitwhite/front/compras.php',
            'icon'    => 'ti ti-shopping-cart',
            'display' => true,
        ];
        $menu['tickets_noc'] = [
            'title'   => 'Painel NOC Chamados',
            'default' => '/plugins/flowitwhite/front/tickets_noc_view.php',
            'icon'    => 'ti ti-layout-dashboard',
            'display' => true,
        ];
    }

    // 2. Menu Central / Assistência / Helpdesk
    if (isset($menu['helpdesk']['content'])) {
        $menu['helpdesk']['content']['analytics_dashboard'] = [
            'title' => 'Dashboard Analytics (Novo ST-Dashboard)',
            'page'  => '/plugins/flowitwhite/front/analytics_view.php',
            'icon'  => 'ti ti-chart-dots',
        ];
        $menu['helpdesk']['content']['tickets_noc'] = [
            'title' => 'Painel NOC Chamados (Video Wall)',
            'page'  => '/plugins/flowitwhite/front/tickets_noc_view.php',
            'icon'  => 'ti ti-device-tv',
        ];
        $menu['helpdesk']['content']['compras'] = [
            'title' => 'Solicitação de Compra',
            'page'  => '/plugins/flowitwhite/front/compras.php',
            'icon'  => 'ti ti-shopping-cart',
        ];
    }

    // 3. Menu Gestão (Management): Analytics, Compras, DRE, Projetos e Comodatos
    if (isset($menu['management']['content'])) {
        $menu['management']['content']['analytics_dashboard'] = [
            'title' => 'FlowIT Operations & Analytics 360',
            'page'  => '/plugins/flowitwhite/front/analytics_view.php',
            'icon'  => 'ti ti-chart-dots',
        ];
        $menu['management']['content']['tickets_noc'] = [
            'title' => 'Painel NOC Chamados (Ao Vivo)',
            'page'  => '/plugins/flowitwhite/front/tickets_noc_view.php',
            'icon'  => 'ti ti-device-tv',
        ];
        $menu['management']['content']['flowit_comodato'] = [
            'title' => 'Comodatos & Termos de Uso',
            'page'  => '/plugins/flowitwhite/front/comodato_view.php',
            'icon'  => 'ti ti-file-certificate',
        ];
        $menu['management']['content']['compras'] = [
            'title' => 'Solicitação de Compra',
            'page'  => '/plugins/flowitwhite/front/compras.php',
            'icon'  => 'ti ti-shopping-cart',
        ];
        $menu['management']['content']['dre_ti'] = [
            'title' => 'DRE Executivo de TI',
            'page'  => '/plugins/flowitwhite/front/dre_view.php',
            'icon'  => 'ti ti-chart-bar',
        ];
        $menu['management']['content']['flowit_projetos'] = [
            'title' => 'Projetos TI (Curva S & Riscos)',
            'page'  => '/plugins/flowitwhite/front/projetos_view.php',
            'icon'  => 'ti ti-chart-dots',
        ];
    }

    // 4. Menu Ativos (Assets): Acesso direto a Comodatos
    if (isset($menu['assets']['content'])) {
        $menu['assets']['content']['flowit_comodato'] = [
            'title' => 'Comodatos & Termos de Uso',
            'page'  => '/plugins/flowitwhite/front/comodato_view.php',
            'icon'  => 'ti ti-file-certificate',
        ];
    }

    // 5. Menu Ferramentas (Tools)
    if (isset($menu['tools']['content'])) {
        $menu['tools']['content']['analytics_dashboard'] = [
            'title' => 'Dashboard Analytics & SLA 360',
            'page'  => '/plugins/flowitwhite/front/analytics_view.php',
            'icon'  => 'ti ti-chart-dots',
        ];
        $menu['tools']['content']['tickets_noc'] = [
            'title' => 'Painel NOC Chamados',
            'page'  => '/plugins/flowitwhite/front/tickets_noc_view.php',
            'icon'  => 'ti ti-device-tv',
        ];
        $menu['tools']['content']['monitoring_devices'] = [
            'title' => 'Monitoramento & Zabbix',
            'page'  => '/plugins/flowitwhite/front/monitoring_view.php',
            'icon'  => 'ti ti-activity-heartbeat',
        ];
        $menu['tools']['content']['flowit_comodato'] = [
            'title' => 'Comodatos & Termos de Uso',
            'page'  => '/plugins/flowitwhite/front/comodato_view.php',
            'icon'  => 'ti ti-file-certificate',
        ];
        $menu['tools']['content']['dre_ti'] = [
            'title' => 'DRE Executivo de TI',
            'page'  => '/plugins/flowitwhite/front/dre_view.php',
            'icon'  => 'ti ti-chart-bar',
        ];
        $menu['tools']['content']['flowit_projetos'] = [
            'title' => 'Projetos TI (Curva S & Riscos)',
            'page'  => '/plugins/flowitwhite/front/projetos_view.php',
            'icon'  => 'ti ti-chart-dots',
        ];
    }

    return $menu;
}

function plugin_flowitwhite_install()
{
    return true;
}

function plugin_flowitwhite_uninstall()
{
    return true;
}

