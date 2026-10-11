<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
if (is_logged_in()) {
    header('Location: ../index.php');
    exit;
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $senha = (string)($_POST['senha'] ?? '');
    $stmt = $pdo->prepare('SELECT id,nome,email,senha_hash,perfil,status,curso FROM usuarios WHERE email=:email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && $senha !== '' && $usuario && $usuario['status'] === 'ativo' && password_verify($senha, $usuario['senha_hash'])) {
        if (password_needs_rehash($usuario['senha_hash'], PASSWORD_DEFAULT)) {
            $up = $pdo->prepare('UPDATE usuarios SET senha_hash=:hash WHERE id=:id');
            $up->execute([':hash' => password_hash($senha, PASSWORD_DEFAULT), ':id' => $usuario['id']]);
        }
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int)$usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];
        $_SESSION['usuario_curso'] = $usuario['curso'];
        $_SESSION['ultimo_acesso'] = time();
        header('Location: ../index.php');
        exit;
    }
    $erro = 'E-mail ou senha inválidos.';
}
$tituloPagina = 'Entrar';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="container" style="max-width:560px;padding:40px 16px"><h1>Acessar conta</h1><p>Entre para consultar seu perfil e os conteúdos relacionados.</p><?php if ($erro !== ''): ?><p role="alert"><?= h($erro) ?></p><?php endif; ?><?php if (($_GET['msg'] ?? '') === 'logout'): ?><p role="status">Você saiu da sua conta.</p><?php endif; ?><?php if (($_GET['erro'] ?? '') === 'restrito'): ?><p role="alert">Entre na sua conta para continuar.</p><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><p><label for="email">E-mail</label><br><input id="email" type="email" name="email" autocomplete="username" maxlength="254" required></p><p><label for="senha">Senha</label><br><input id="senha" type="password" name="senha" autocomplete="current-password" required></p><button type="submit">Entrar</button></form></section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
