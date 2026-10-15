<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/Controller/AuthController.php';
if (is_logged_in()) { header('Location: index.php'); exit; }
$erro = '';
$authController = new AuthController($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $senha = (string)($_POST['senha'] ?? '');
    if ($authController->entrar($email, $senha)) {
        header('Location: index.php');
        exit;
    }
    $erro = 'E-mail ou senha inválidos.';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Acesso ao Portal SI">
    <title>Entrar — Portal SI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/admin.css?v=<?= filemtime(__DIR__ . '/assets/css/admin.css') ?>">
</head>
<body class="d-flex align-items-center min-vh-100">
    <main class="login card shadow-sm">
        <div class="card-body">
            <a href="index.php" class="brand d-inline-block mb-3">Portal SI</a>
            <h1 class="card-title">Acessar conta</h1>
            <p>Entre para consultar seu perfil e os conteúdos relacionados.</p>
            <?php if ($erro): ?><div class="alert alert-danger" role="alert"><?= h($erro) ?></div><?php endif; ?>
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logout'): ?><div class="alert alert-success" role="status">Você saiu da sua conta.</div><?php endif; ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <label class="form-label" for="email">E-mail</label>
                <input class="form-control" id="email" type="email" name="email" autocomplete="username" required maxlength="254">
                <label class="form-label mt-3" for="senha">Senha</label>
                <input class="form-control" id="senha" type="password" name="senha" autocomplete="current-password" required>
                <button class="btn btn-marca-azul w-100 mt-4" type="submit">Entrar</button>
            </form>
            <a class="d-inline-block mt-3" href="index.php">Voltar à página inicial</a>
        </div>
    </main>
</body>
</html>
