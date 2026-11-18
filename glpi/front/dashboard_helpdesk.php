<?php
/**
 * ---------------------------------------------------------------------
 * GLPI - Helpdesk Dashboard (FlowIT Custom Executive Dashboard)
 * ---------------------------------------------------------------------
 */

require_once(__DIR__ . '/_check_webserver_config.php');

use Glpi\Dashboard\Dashboard;
use Glpi\Dashboard\Grid;

global $CFG_GLPI;

Session::checkCentralAccess();

Html::header(__('Helpdesk Dashboard'), $_SERVER['PHP_SELF'], "helpdesk", "dashboard");
?>
<div class="flowit-dashboard-full" style="width: 100%; height: calc(100vh - 100px); padding: 0; margin: 0; background: #f8fafc;">
    <iframe src="/plugins/flowitwhite/front/analytics_dashboard.php" 
            style="width: 100%; height: 100%; border: none; background: #f8fafc;" 
            title="Painel Executivo de Suporte">
    </iframe>
</div>
<?php
Html::footer();
