<?php 
// admin/login.php 
session_start(); 
require_once __DIR__ . '/../../../config/database.php'; 
$erro = ''; 
// Se já estiver logado, redireciona diretamente ao dashboard 
if (isset($_SESSION['usuario_id'])) { header('Location: dashboard.php'); exit; } 
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $email = trim($_POST['email'] ?? ''); 
    $senha = $_POST['senha'] ?? ''; 
    if (empty($email) || empty($senha)) { $erro = "Preencha todos os campos para prosseguir."; } else { // Consulta buscando apenas contas ativas 
        $sql = "SELECT id, nome, email, senha_hash, perfil FROM usuarios WHERE email = :email AND status = 'ativo' LIMIT 1"; 
        $stmt = $pdo->prepare($sql); 
        $stmt->bindValue(':email', $email, PDO::PARAM_STR); 
        $stmt->execute(); 
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Verificação segura: password_verify valida contra o hash armazenado 
        if ($usuario && password_verify($senha, $usuario['senha_hash'])) { // Prevenção de Fixação de Sessão session_regenerate_id(true);
        // Definição dos dados da sessão autenticada 
            $_SESSION['usuario_id'] = (int)$usuario['id']; 
            $_SESSION['usuario_nome'] = $usuario['nome']; 
            $_SESSION['usuario_email'] = $usuario['email']; 
            $_SESSION['usuario_perfil'] = $usuario['perfil']; 
            $_SESSION['ultimo_acesso'] = time(); 
            header('Location: dashboard.php'); exit; } else { // Mensagem intencionalmente genérica para evitar enumeração de usuários 
            $erro = "E-mail ou senha inválidos."; 
            } 
        } 
    } 
?> 
<!DOCTYPE html> 
<html lang="pt-BR"> 
    <head> 
        <meta charset="UTF-8"> 
        <title>Autenticação — Portal SI</title> 
    </head> 
    <body> 
        <h2>Acesso ao Painel Administrativo</h2> 
        <?php if (!empty($erro)): ?> 
        <p style="color: #b71c1c; font-weight: bold;"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></p> 
        <?php endif; ?> 
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'cadastrado'): ?> 
        <p style="color: #2e7d32;">Cadastro realizado com sucesso! Efetue seu login.</p> 
        <?php endif; ?> 
        <form action="login.php" method="POST"> 
            <div> 
                <label for="email">E-mail:</label>
                <br> 
                <input type="email" id="email" name="email" required> 
            </div> 
                <br> 
                <div> 
                <label for="senha">Senha:</label>
                <br> 
                <input type="password" id="senha" name="senha" required> 
            </div> 
            <br> 
            <button type="submit">Entrar no Sistema</button> 
        </form> 
    </body> 
</html>