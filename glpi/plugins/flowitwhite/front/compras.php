<?php
/**
 * FlowIT - Módulo de Solicitação de Compras integrado ao GLPI
 */
require_once __DIR__ . '/bootstrap.php';

Session::checkLoginUser();

Html::header('Solicitação de Compra', $_SERVER['PHP_SELF'], 'helpdesk', 'compras');

$iframeSrc = '/plugins/flowitwhite/front/form.php';
?>
<div class="flowit-purchase-container" style="padding: 10px; width: 100%; height: calc(100vh - 120px);">
    <iframe src="<?php echo $iframeSrc; ?>" 
            style="width: 100%; height: 100%; border: 1px solid #dce3e8; border-radius: 12px; background: #ffffff;" 
            title="Solicitação de Compra">
    </iframe>
</div>
<?php

Html::footer();
