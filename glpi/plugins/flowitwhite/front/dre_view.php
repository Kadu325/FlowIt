<?php
/**
 * FlowIT - Painel DRE Executivo e Orçamento Gerencial de TI (MOD-16 / Seção 13)
 */
require_once __DIR__ . '/bootstrap.php';

Session::checkLoginUser();

// Apenas perfis com permissão gerencial ou administrativa acessam o DRE
if (!Session::haveRight('budget', READ) && !Session::haveRight('config', READ) && Session::getCurrentInterface() !== 'central') {
    Html::displayRightError();
}

Html::header('DRE Executivo de TI', $_SERVER['PHP_SELF'], 'management', 'dre_ti');

$iframeSrc = '/plugins/flowitwhite/front/dre.php';
?>
<div class="flowit-dre-container" style="padding: 6px; width: 100%; height: calc(100vh - 120px);">
    <iframe src="<?php echo $iframeSrc; ?>" 
            style="width: 100%; height: 100%; border: 1px solid #dce3e8; border-radius: 12px; background: #ffffff;" 
            title="DRE Executivo e Orçamento Gerencial de TI">
    </iframe>
</div>
<?php

Html::footer();
