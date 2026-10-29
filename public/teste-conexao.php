<?php
declare(strict_types=1);

// Teste de conexão com o PostgreSQL (atividade Aiven.io - Projeto de Extensão IV).
// Não depende do schema do portal e nunca exibe a senha do banco.
require_once __DIR__ . '/config/conexao.php';

const GRUPO_NOME = 'Grupo 01';
const SERVIDOR_NOME = 'Aiven';

$erro = false;
$banco = '';
$versao = '';
$registrosTeste = [];
$tabelaTesteOk = true;

try {
    $banco = (string) $pdo->query('SELECT current_database()')->fetchColumn();
    $versao = (string) $pdo->query('SELECT version()')->fetchColumn();
} catch (PDOException $e) {
    error_log('Erro no teste de conexão: ' . $e->getMessage());
    $erro = true;
}

if (!$erro) {
    try {
        $registrosTeste = $pdo->query('SELECT id, descricao FROM teste ORDER BY id')->fetchAll();
    } catch (PDOException $e) {
        error_log('Erro ao consultar a tabela teste: ' . $e->getMessage());
        $tabelaTesteOk = false;
    }
}

function esc($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Teste de conexão com PostgreSQL | Portal SI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f6f8; }
        h1 { color: #12294B; border-bottom: 4px solid #F5C518; padding-bottom: .5rem; }
        th { width: 11rem; }
    </style>
</head>
<body>
<main class="container py-5" style="max-width: 820px;">
    <h1 class="h3 mb-4">Teste de conexão com PostgreSQL</h1>

    <?php if ($erro): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Status:</strong> DESCONECTADO. Não foi possível executar a consulta; confira os logs do servidor.
        </div>
    <?php else: ?>
        <div class="alert alert-success" role="status"><strong>Status:</strong> CONECTADO</div>

        <table class="table table-bordered bg-white">
            <tbody>
                <tr><th scope="row">Grupo</th><td><?= esc(GRUPO_NOME) ?></td></tr>
                <tr><th scope="row">Servidor</th><td><?= esc(SERVIDOR_NOME) ?></td></tr>
                <tr><th scope="row">Banco</th><td><?= esc($banco) ?></td></tr>
                <tr><th scope="row">PostgreSQL</th><td><?= esc($versao) ?></td></tr>
            </tbody>
        </table>

        <h2 class="h5 mt-4">Tabela de teste</h2>
        <?php if (!$tabelaTesteOk): ?>
            <div class="alert alert-info">A tabela <code>teste</code> ainda não existe. Execute <code>database/teste_conexao.sql</code> no banco do grupo.</div>
        <?php elseif ($registrosTeste === []): ?>
            <div class="alert alert-info">Nenhum registro encontrado na tabela <code>teste</code>.</div>
        <?php else: ?>
            <table class="table table-striped table-bordered bg-white">
                <thead><tr><th scope="col">ID</th><th scope="col">Descrição</th></tr></thead>
                <tbody>
                    <?php foreach ($registrosTeste as $registro): ?>
                        <tr><td><?= esc($registro['id']) ?></td><td><?= esc($registro['descricao']) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <a class="btn btn-outline-primary" href="index.php">Voltar ao portal</a>
</main>
</body>
</html>
