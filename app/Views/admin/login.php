<?php  
session_start(); 
require_once __DIR__ . '/../../../config/database.php'; 
$erro = ''; 
if (isset($_SESSION['usuario_id'])) { header('Location: dashboard.php'); exit; } 
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $email = trim($_POST['email'] ?? ''); 
    $senha = $_POST['senha'] ?? ''; 
    if (empty($email) || empty($senha)) { $erro = "Preencha todos os campos para prosseguir."; } else {
        $sql = "SELECT id, nome, email, senha_hash, perfil FROM usuarios WHERE email = :email AND status = 'ativo' LIMIT 1"; 
        $stmt = $pdo->prepare($sql); 
        $stmt->bindValue(':email', $email, PDO::PARAM_STR); 
        $stmt->execute(); 
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC); 
        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            $_SESSION['usuario_id'] = (int)$usuario['id']; 
            $_SESSION['usuario_nome'] = $usuario['nome']; 
            $_SESSION['usuario_email'] = $usuario['email']; 
            $_SESSION['usuario_perfil'] = $usuario['perfil']; 
            $_SESSION['ultimo_acesso'] = time(); 
            header('Location: dashboard.php'); exit; } else { 
            $erro = "E-mail ou senha inválidos."; 
            } 
        } 
    } 
?> 
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Autenticação — Portal SI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
        }
        .btn-marca-azul {
            background-color: #12294B;
            color: #ffffff;
            border: none;
        }
        .btn-marca-azul:hover,
        .btn-marca-azul:focus {
            background-color: #0d1f3a;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4" style="background-color: #12294B;">
        <div class="container">
            <span class="navbar-brand">Portal SI</span>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Acesso ao Painel Administrativo</h2>

                        <?php if (!empty($erro)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>

                        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'cadastrado'): ?>
                        <div class="alert alert-success">Cadastro realizado com sucesso! Efetue seu login.</div>
                        <?php endif; ?>

                        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logout'): ?>
                            <div class="alert alert-success">
                                Sessão encerrada com sucesso!
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail:</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha:</label>
                                <input type="password" id="senha" name="senha" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-marca-azul w-100">Entrar no Sistema</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>