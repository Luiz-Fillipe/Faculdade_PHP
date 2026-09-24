<?php 
// admin/cadastro.php 
require_once __DIR__ . '/../../../config/database.php'; 
$erros = []; 
$sucesso = false; 
$nome = ''; 
$email = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 

    $nome = trim($_POST['nome'] ?? ''); $email = trim($_POST['email'] ?? ''); 
    $senha = $_POST['senha'] ?? ''; 
    $senha_confirmacao = $_POST['senha_confirmacao'] ?? ''; 
    /*Validações server-side*/
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
    /*Persistência caso não haja erros de validação*/ 
    if (empty($erros)) { 
        try { /*Geração do hash seguro utilizando o algoritmo padrão (Bcrypt)*/ 
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT); 
            $sql = "INSERT INTO usuarios (nome, email, senha_hash, perfil, status) VALUES (:nome, :email, :senha_hash, 'Coordenador', 'ativo')"; 
            $stmt = $pdo->prepare($sql); 
            $stmt->bindValue(':nome', $nome, PDO::PARAM_STR); 
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':senha_hash', $senhaHash, PDO::PARAM_STR); 
            $stmt->execute(); 
            header('Location: login.php?sucesso=cadastrado'); exit; } 
            catch (PDOException $e) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro — Portal SI</title>
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
                        <h2 class="card-title mb-4">Cadastro de Novo Usuário</h2>

                        <?php if (!empty($erros)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($erros as $erro): ?>
                                <li><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <form action="cadastro.php" method="POST">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo:</label>
                                <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail Institucional:</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha (mínimo 8 caracteres):</label>
                                <input type="password" id="senha" name="senha" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="senha_confirmacao" class="form-label">Confirme a Senha:</label>
                                <input type="password" id="senha_confirmacao" name="senha_confirmacao" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-marca-azul w-100">Cadastrar Usuário</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>