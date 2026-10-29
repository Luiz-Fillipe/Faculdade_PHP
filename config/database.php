<?php
declare(strict_types=1);

// Carrega o arquivo .env da raiz do projeto (ignorado pelo Git), quando existir.
// Variáveis já definidas no ambiente do sistema têm prioridade sobre o .env.
$arquivoEnv = __DIR__ . '/../.env';
if (is_readable($arquivoEnv)) {
    foreach (file($arquivoEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $linhaEnv) {
        $linhaEnv = trim($linhaEnv);
        if ($linhaEnv === '' || $linhaEnv[0] === '#' || !str_contains($linhaEnv, '=')) {
            continue;
        }
        [$chaveEnv, $valorEnv] = explode('=', $linhaEnv, 2);
        $chaveEnv = trim($chaveEnv);
        if ($chaveEnv !== '' && getenv($chaveEnv) === false) {
            putenv($chaveEnv . '=' . trim($valorEnv, " \t\"'"));
        }
    }
}
unset($arquivoEnv, $linhaEnv, $chaveEnv, $valorEnv);

$host = getenv('PORTAL_DB_HOST') ?: 'localhost';
$dbname = getenv('PORTAL_DB_NAME') ?: 'portal_si';
$username = getenv('PORTAL_DB_USER') ?: 'postgres';
$password = getenv('PORTAL_DB_PASSWORD') ?: 'postgres';
$port = getenv('PORTAL_DB_PORT') ?: '5432';
// Banco remoto (Aiven): use PORTAL_DB_SSLMODE=require. Em banco local, deixe vazio.
$sslmode = getenv('PORTAL_DB_SSLMODE') ?: '';

try {
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
    if ($sslmode !== '') {
        $dsn .= ";sslmode={$sslmode}";
    }

    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Não foi possível conectar ao banco de dados. Confira a configuração do ambiente.');
}
