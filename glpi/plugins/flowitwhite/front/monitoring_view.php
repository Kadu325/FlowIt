<?php
/**
 * FlowIT - Painel Central de Observabilidade, NOC & Submódulos Especializados
 */
require_once __DIR__ . '/bootstrap.php';

Session::checkLoginUser();

if (!Session::haveRight('config', READ) && !Session::haveRight('networking', READ) && Session::getCurrentInterface() !== 'central') {
    Html::displayRightError();
}

$tab = $_GET['tab'] ?? 'dashboard';

Html::header('Observabilidade & NOC', $_SERVER['PHP_SELF'], 'tools', 'monitoring_dashboard');

// Roteamento inteligente de cada aba
if ($tab === 'dashboard') {
    $iframeSrc = '/plugins/flowitwhite/front/monitoring_kiosk.php';
} elseif ($tab === 'devices') {
    $iframeSrc = '/plugins/flowitwhite/front/monitoring_devices.php';
} elseif ($tab === 'printers') {
    $iframeSrc = '/plugins/flowitwhite/front/monitoring_printers.php';
} else {
    $iframeSrc = '/plugins/flowitwhite/front/monitoring_category.php?category=' . urlencode($tab);
}

$navTabs = [
    'dashboard'   => ['title' => 'Dashboard Geral & NOC', 'icon' => 'ti ti-layout-dashboard'],
    'devices'     => ['title' => 'Dispositivos (GLPI ➔ Zabbix)', 'icon' => 'ti ti-server-cog'],
    'servers'     => ['title' => 'Servidores', 'icon' => 'ti ti-cpu'],
    'services'    => ['title' => 'Serviços & SLA', 'icon' => 'ti ti-server-2'],
    'printers'    => ['title' => 'Impressoras & Toners', 'icon' => 'ti ti-printer'],
    'network'     => ['title' => 'Ativos de Rede', 'icon' => 'ti ti-network'],
    'internet'    => ['title' => 'Internet & Links', 'icon' => 'ti ti-world-upload'],
    'ups'         => ['title' => 'UPS / Nobreaks', 'icon' => 'ti ti-bolt'],
    'predictive'  => ['title' => 'Pré-Falha (AIOps)', 'icon' => 'ti ti-shield-half'],
    'admin'       => ['title' => 'Administração & Fila', 'icon' => 'ti ti-settings-cog']
];
?>

<div style="padding: 10px 14px 0 14px; background: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; overflow-x: auto;">
    <div style="display: flex; gap: 4px; flex-wrap: nowrap;">
        <?php foreach ($navTabs as $key => $t): ?>
            <?php 
                $isActive = ($tab === $key);
                $bg = $isActive ? '#006aa6' : '#f1f5f9';
                $color = $isActive ? '#ffffff' : '#475569';
            ?>
            <a href="?tab=<?php echo $key; ?>" 
               style="padding: 7px 12px; border-radius: 6px 6px 0 0; font-weight: 600; font-size: 0.8125rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; background: <?php echo $bg; ?>; color: <?php echo $color; ?>;">
                <i class="<?php echo $t['icon']; ?>"></i> <?php echo $t['title']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div style="padding-left: 12px; flex-shrink: 0;">
        <a href="/plugins/flowitwhite/front/monitoring_kiosk.php" target="_blank" 
           style="padding: 6px 12px; background: #0f172a; color: #38bdf8; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
            <i class="ti ti-device-tv"></i> Video Wall NOC
        </a>
    </div>
</div>

<div class="flowit-monitoring-container" style="padding: 6px; width: 100%; height: calc(100vh - 150px);">
    <iframe src="<?php echo $iframeSrc; ?>" 
            style="width: 100%; height: 100%; border: 1px solid #dce3e8; border-radius: 0 0 12px 12px; background: #f8fafc;" 
            title="Observabilidade FlowIT">
    </iframe>
</div>
<?php

Html::footer();
