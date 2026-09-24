<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /portal-si/admin/login.php?erro=restrito');
        exit;
    }
}

function require_perfil(array $perfisPermitidos): void
{
    require_login();

    if (!in_array($_SESSION['usuario_perfil'], $perfisPermitidos, true)) {
        header('Location: /portal-si/admin/dashboard.php?erro=permissao_negada');
        exit;
    }
}