<?php
/**
 * Hook de Instalação e Desinstalação do Plugin Vault360
 */

function plugin_vault360_install()
{
    global $DB;

    // 1. Tabela de Metadados de Credenciais (NENHUMA SENHA É SALVA NO MYSQL)
    if (!$DB->tableExists('glpi_plugin_vault360_credentials')) {
        $query = "CREATE TABLE `glpi_plugin_vault360_credentials` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `itemtype` VARCHAR(100) NOT NULL DEFAULT 'Computer',
            `items_id` INT UNSIGNED NOT NULL DEFAULT 0,
            `ip` VARCHAR(255) DEFAULT NULL,
            `tipo_ativo` VARCHAR(50) DEFAULT 'Servidor',
            `modo_acesso` VARCHAR(255) DEFAULT 'SSH',
            `ambiente` VARCHAR(50) DEFAULT 'Produção',
            `usuario` VARCHAR(100) NOT NULL,
            `vault_path` VARCHAR(255) NOT NULL,
            `status` VARCHAR(50) DEFAULT 'Ativo',
            `localizacao` VARCHAR(255) DEFAULT NULL,
            `observacao` TEXT DEFAULT NULL,
            `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `date_mod` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `item` (`itemtype`, `items_id`),
            INDEX `vault_path` (`vault_path`),
            INDEX `ip` (`ip`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $DB->doQuery($query);
    }

    // 2. Tabela de Auditoria e Leases Ativos do Vault
    if (!$DB->tableExists('glpi_plugin_vault360_leases')) {
        $query = "CREATE TABLE `glpi_plugin_vault360_leases` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `credential_id` INT UNSIGNED NOT NULL DEFAULT 0,
            `tickets_id` INT UNSIGNED NOT NULL DEFAULT 0,
            `users_id` INT UNSIGNED NOT NULL DEFAULT 0,
            `lease_id` VARCHAR(255) NOT NULL,
            `login` VARCHAR(100) NOT NULL,
            `tipo_acesso` VARCHAR(50) DEFAULT 'OTP_SSH',
            `ttl` INT UNSIGNED NOT NULL DEFAULT 1800,
            `status` ENUM('ativo', 'encerrado', 'expirado') NOT NULL DEFAULT 'ativo',
            `user_ip` VARCHAR(45) DEFAULT NULL,
            `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `date_revoked` TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            INDEX `ticket` (`tickets_id`),
            INDEX `user` (`users_id`),
            INDEX `lease` (`lease_id`),
            INDEX `status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $DB->doQuery($query);
    }

    // 3. Inicializa configurações padrão caso não existam
    $config = Config::getConfigurationValues('plugin:vault360');
    if (!isset($config['vault_url'])) {
        Config::setConfigurationValues('plugin:vault360', [
            'vault_url'     => 'https://vault.fazendaprogresso.info:8200',
            'vault_token'   => '',
            'vault_mount'   => 'secret',
            'default_ttl'   => '1800', // 30 minutos
            'require_ticket'=> '1'
        ]);
    }

    return true;
}

function plugin_vault360_uninstall()
{
    // Tabelas preservadas na desinstalação para evitar perda de vínculos de inventário.
    return true;
}
