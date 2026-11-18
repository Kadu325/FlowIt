<?php
/**
 * FlowIT - Painel de Gestão de Comodatos e Termos de Responsabilidade
 */
require_once __DIR__ . '/bootstrap.php';

Session::checkLoginUser();

Html::header('Comodatos & Termos de Uso', $_SERVER['PHP_SELF'], 'management', 'comodato');

$iframeSrc = '/plugins/flowitwhite/front/comodato.php';
?>
<div class="flowit-comodato-container" style="padding: 6px; width: 100%; height: calc(100vh - 120px);">
    <iframe src="<?php echo $iframeSrc; ?>" 
            style="width: 100%; height: 100%; border: 1px solid #dce3e8; border-radius: 12px; background: #ffffff;" 
            title="Gestão de Comodatos & Termos de Uso">
    </iframe>
</div>
<?php

Html::footer();
