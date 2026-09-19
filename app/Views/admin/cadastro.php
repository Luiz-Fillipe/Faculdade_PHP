<?php 
// admin/cadastro.php 
require_once __DIR__ . '/../../../config/database.php'; 
$erros = []; 
$sucesso = false; 
$nome = ''; 
$email = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    // 1. Sanitização básica dos campos textuais 
    $nome = trim($_POST['nome'] ?? ''); $email = trim($_POST['email'] ?? ''); 
    $senha = $_POST['senha'] ?? ''; 
    $senha_confirmacao = $_POST['senha_confirmacao'] ?? ''; 
    // 2. Validações server-side 
    if (mb_strlen($nome) < 3) { 
        $erros[] = "O nome deve conter ao menos 3 caracteres."; 
    } 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $erros[] = "Informe um endereço de e-mail válido."; 
    } 
    if (mb_strlen($senha) < 8) { 
        $erros[] = "A senha deve conter no mínimo 8 caracteres."; 
    } 
    if ($senha !== $senha_confirmacao) { 
        $erros[] = "A confirmação de senha não confere com a senha informada."; 
    } 
    // 3. Persistência caso não haja erros de validação 
    if (empty($erros)) { 
        try { // Geração do hash seguro utilizando o algoritmo padrão (Bcrypt) 
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT); 
            $sql = "INSERT INTO usuarios (nome, email, senha_hash, perfil, status) VALUES (:nome, :email, :senha_hash, 'aluno', 'ativo')"; 
            $stmt = $pdo->prepare($sql); 
            $stmt->bindValue(':nome', $nome, PDO::PARAM_STR); 
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':senha_hash', $senhaHash, PDO::PARAM_STR); 
            $stmt->execute(); 
            // Redirecionamento com flag de sucesso 
            header('Location: login.php?sucesso=cadastrado'); exit; } 
            catch (PDOException $e) { // Código 23505 indica violação de constraint UNIQUE no PostgreSQL 
                if ($e->getCode() === '23505') { 
                    $erros[] = "O e-mail informado já está cadastrado no sistema."; 
                } else { 
                    $erros[] = "Erro interno ao processar cadastro. Tente novamente mais tarde."; 
                } 
            } 
        } 
    } 
?> 
<!DOCTYPE html> 
<html lang="pt-BR"> 
    <head> 
        <meta charset="UTF-8"> 
        <title>Cadastro — Portal SI</title> 
    </head> 
    <body> 
        <h2>Cadastro de Novo Usuário</h2> 
        <?php if (!empty($erros)): ?> 
        <div style="color: #b71c1c;"> 
            <ul> 
                <?php foreach ($erros as $erro): ?> 
            <li>
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
            </li> 
                <?php endforeach; ?> 
            </ul> 
        </div> 
        <?php endif; ?> 
        <form action="cadastro.php" method="POST"> 
            <div> 
                <label for="nome">Nome Completo:</label><br> 
                <input type="text" id="nome" name="nome" value="
                <?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>" required> 
            </div> 
            <div> 
                <label for="email">E-mail Institucional:</label><br> 
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required> </div> 
            <div> 
                <label for="senha">Senha (mínimo 8 caracteres):</label>
                <br> 
                <input type="password" id="senha" name="senha" required> 
            </div> 
            <div> 
                <label for="senha_confirmacao">Confirme a Senha:</label><br> 
                <input type="password" id="senha_confirmacao" name="senha_confirmacao" required> 
            </div> 
            <br> 
            <button type="submit">Cadastrar Usuário</button> 
        </form> 
    </body> 
</html>