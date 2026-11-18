<?php
/**
 * FlowIT — Wrapper Nativo GLPI do Dashboard Executivo Analytics (Substituição ST-Dashboard)
 */
require_once __DIR__ . '/bootstrap.php';

Session::checkLoginUser();

Html::header('FlowIT Analytics — Business & Operations', $_SERVER['PHP_SELF'], 'management', 'analytics_dashboard');

$iframeSrc = '/plugins/flowitwhite/front/analytics_dashboard.php';
?>
<div class="flowit-analytics-wrapper" style="padding: 4px; width: 100%; height: calc(100vh - 110px);">
    <iframe src="<?php echo $iframeSrc; ?>" 
            style="width: 100%; height: 100%; border: none; border-radius: 16px; background: #f8fafc;" 
            title="FlowIT Business & Operations Analytics">
    </iframe>
</div>
<?php

Html::footer();
