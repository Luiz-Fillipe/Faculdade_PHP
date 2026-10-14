<?php
declare(strict_types=1);

$host = getenv('PORTAL_DB_HOST') ?: 'localhost';
$dbname = getenv('PORTAL_DB_NAME') ?: 'portal_si';
$username = getenv('PORTAL_DB_USER') ?: 'postgres';
$password = getenv('PORTAL_DB_PASSWORD') ?: 'postgres';
$port = getenv('PORTAL_DB_PORT') ?: '5432';

try {
    $pdo = new PDO(
        "pgsql:host={$host};port={$port};dbname={$dbname}",
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
