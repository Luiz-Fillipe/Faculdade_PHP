<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$email = 'admin@portalsi.local';
$senha = 'TroqueEstaSenha!2026';
$hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO usuarios (nome, email, senha_hash, perfil, status)
     VALUES (:nome, :email, :hash, 'Admin', 'ativo')
     ON CONFLICT (email) DO NOTHING"
);
$stmt->execute([':nome' => 'Administrador do Portal', ':email' => $email, ':hash' => $hash]);

echo "Administrador de teste: {$email}" . PHP_EOL;
echo "Senha inicial: {$senha}" . PHP_EOL;
echo "Apague este arquivo do servidor após criar o usuário." . PHP_EOL;
