<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION['usuario_id'], $_SESSION['usuario_perfil'])
        && filter_var($_SESSION['usuario_id'], FILTER_VALIDATE_INT) !== false;
}

function public_base_path(): string
{
    $script = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
    $pos = strpos($script, '/public/');
    if ($pos !== false) {
        return substr($script, 0, $pos + strlen('/public'));
    }
    $directory = rtrim(dirname($script), '/.');
    if (str_ends_with($directory, '/admin')) {
        return dirname($directory);
    }
    return $directory;
}

function require_login(): void
{
    if (!is_logged_in()) {
        $base = public_base_path();
        header('Location: ' . rtrim($base, '/') . '/admin/login.php?erro=restrito');
        exit;
    }
}

function require_perfil(array $perfisPermitidos): void
{
    require_login();
    if (!in_array($_SESSION['usuario_perfil'] ?? '', $perfisPermitidos, true)) {
        http_response_code(403);
        exit('Acesso não autorizado.');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Solicitação inválida. Atualize a página e tente novamente.');
    }
}

function h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
