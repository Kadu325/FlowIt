<?php
/**
 * FlowIT - Bootstrap Seguro para Endpoints e APIs do Plugin
 */

$glpi_root = dirname(__DIR__, 3);

if (file_exists($glpi_root . '/inc/downstream.php')) {
    require_once $glpi_root . '/inc/downstream.php';
}

require_once $glpi_root . '/src/Glpi/Application/ResourcesChecker.php';
(new \Glpi\Application\ResourcesChecker($glpi_root))->checkResources();
require_once $glpi_root . '/vendor/autoload.php';

global $kernel, $DB;
if (!$kernel) {
    $kernel = new \Glpi\Kernel\Kernel('production');
    $kernel->boot();
}
