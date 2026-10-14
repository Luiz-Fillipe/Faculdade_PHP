<?php
declare(strict_types=1);


class AuthController
{
    public function __construct(private PDO $pdo)
    {
    }

    
      @return bool 
     
    public function entrar(string $email, string $senha): bool
    {
        if ($email === '' || $senha === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "SELECT id, nome, email, senha_hash, perfil, status, curso
             FROM usuarios
             WHERE email = :email
             LIMIT 1"
        );
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if (!$usuario || $usuario['status'] !== 'ativo' || !password_verify($senha, $usuario['senha_hash'])) {
            return false;
        }

        if (password_needs_rehash($usuario['senha_hash'], PASSWORD_DEFAULT)) {
            $atualizar = $this->pdo->prepare('UPDATE usuarios SET senha_hash = :hash WHERE id = :id');
            $atualizar->execute([
                ':hash' => password_hash($senha, PASSWORD_DEFAULT),
                ':id'   => $usuario['id'],
            ]);
        }

        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];
        $_SESSION['usuario_curso'] = $usuario['curso'];
        $_SESSION['ultimo_acesso'] = time();

        return true;
    }

    
    public static function sair(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $params['path'],
                'domain'   => $params['domain'],
                'secure'   => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        session_destroy();
    }
}
