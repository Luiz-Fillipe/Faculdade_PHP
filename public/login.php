<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/auth.php';
if (is_logged_in()) { header('Location: index.php'); exit; }
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $senha = (string)($_POST['senha'] ?? '');
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && $senha !== '') {
        $stmt = $pdo->prepare("SELECT id,nome,email,senha_hash,perfil,status,curso FROM usuarios WHERE email=:email LIMIT 1");
        $stmt->execute([':email'=>$email]);
        $usuario = $stmt->fetch();
        if ($usuario && $usuario['status'] === 'ativo' && password_verify($senha, $usuario['senha_hash'])) {
            if (password_needs_rehash($usuario['senha_hash'], PASSWORD_DEFAULT)) {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $up = $pdo->prepare('UPDATE usuarios SET senha_hash=:hash WHERE id=:id');
                $up->execute([':hash'=>$hash, ':id'=>$usuario['id']]);
            }
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = (int)$usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_perfil'] = $usuario['perfil'];
            $_SESSION['usuario_curso'] = $usuario['curso'];
            $_SESSION['ultimo_acesso'] = time();
            header('Location: index.php'); exit;
        }
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
