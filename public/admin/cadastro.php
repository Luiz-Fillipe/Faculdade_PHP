<?php
declare(strict_types=1);
require_once __DIR__ . '/_init.php';
require_perfil(['Admin']);
$erros = [];
$nome = '';
$email = '';
$perfilNovo = 'Aluno';
$curso = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nome = trim((string)($_POST['nome'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $senha = (string)($_POST['senha'] ?? '');
    $confirmacao = (string)($_POST['senha_confirmacao'] ?? '');
    $perfilNovo = (string)($_POST['perfil'] ?? '');
    $curso = trim((string)($_POST['curso'] ?? ''));
    if (mb_strlen($nome) < 3 || mb_strlen($nome) > 150) $erros[] = 'Informe um nome válido.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) $erros[] = 'Informe um e-mail válido.';
    if (strlen($senha) < 12) $erros[] = 'A senha deve conter pelo menos 12 caracteres.';
    if ($senha !== $confirmacao) $erros[] = 'A confirmação de senha não confere.';
    if (!in_array($perfilNovo, ['Admin', 'Professor', 'Aluno'], true)) $erros[] = 'Selecione um perfil válido.';
    if (mb_strlen($curso) > 150) $erros[] = 'O nome do curso é muito longo.';
    if (!$erros) {
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios(nome,email,senha_hash,perfil,curso,status) VALUES(:nome,:email,:hash,:perfil,:curso,'ativo')");
            $stmt->execute([':nome'=>$nome, ':email'=>$email, ':hash'=>password_hash($senha, PASSWORD_DEFAULT), ':perfil'=>$perfilNovo, ':curso'=>$curso ?: null]);
            audit($pdo, 'criou usuário', 'usuarios', (int)$pdo->lastInsertId());
            header('Location: usuarios.php?ok=1');
            exit;
        } catch (PDOException $e) {
            $erros[] = 'Não foi possível cadastrar. Verifique se o e-mail já está em uso.';
        }
    }
}
admin_header('Cadastrar usuário');
require __DIR__ . '/../../app/Views/admin/adm_cad.php';
admin_footer();
