<?php
/**
 * FlowIT - Painel NOC Video Wall de Chamados em Tempo Real
 */
require_once __DIR__ . '/bootstrap.php';

Session::checkLoginUser();

Html::header('Painel NOC — Fila de Chamados', $_SERVER['PHP_SELF'], 'helpdesk', 'tickets_noc');

$iframeSrc = '/plugins/flowitwhite/front/tickets_noc.php';
?>
<div class="flowit-tickets-container" style="padding: 6px; width: 100%; height: calc(100vh - 120px);">
    <iframe src="<?php echo $iframeSrc; ?>" 
            style="width: 100%; height: 100%; border: 1px solid var(--flowit-border, #dce3e8); border-radius: 12px; background: #ffffff;" 
            title="Painel NOC de Chamados FlowIT">
    </iframe>
</div>
<?php

Html::footer();
