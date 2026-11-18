<?php
/**
 * FlowIT - Gestão de Projetos com Curva S, EVM e Matriz de Riscos 4x4 (MOD-17 a 20 / Fase 15)
 */
require_once __DIR__ . '/bootstrap.php';

Session::checkLoginUser();

if (!Session::haveRight('project', READ) && !Session::haveRight('config', READ) && Session::getCurrentInterface() !== 'central') {
    Html::displayRightError();
}

Html::header('Projetos TI · Curva S e Riscos', $_SERVER['PHP_SELF'], 'management', 'flowit_projetos');

$iframeSrc = '/plugins/flowitwhite/front/projetos.php';
?>
<div class="flowit-projetos-container" style="padding: 6px; width: 100%; height: calc(100vh - 120px);">
    <iframe src="<?php echo $iframeSrc; ?>" 
            style="width: 100%; height: 100%; border: 1px solid #dce3e8; border-radius: 12px; background: #ffffff;" 
            title="Gestão de Projetos com Curva S, EVM e Riscos">
    </iframe>
</div>
<?php

Html::footer();
