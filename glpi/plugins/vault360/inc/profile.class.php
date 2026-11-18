<?php
/**
 * Perfil e Permissões do Plugin Vault360
 */

class PluginVault360Profile extends CommonGLPI
{
    public static function canCreate(): bool
    {
        return Session::haveRight('config', UPDATE);
    }

    public static function canView(): bool
    {
        return Session::haveRight('ticket', READ);
    }

    public static function canGenerateToken(): bool
    {
        // Permitido para técnicos, administradores ou perfis autorizados
        return Session::haveRight('ticket', UPDATE) || Session::haveRight('computer', READ);
    }
}
