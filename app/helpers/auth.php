<?php 
    // app/helpers/auth.php 
    // Garante a inicialização da sessão caso ainda não tenha sido iniciada 
    if (session_status() === PHP_SESSION_NONE) { 
        session_start(); 
    } 
    /* Verifica se existe uma sessão válida de usuário ativo  @return bool */ 
    function is_logged_in(): bool { 
        return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']); 
    } 
    /* Bloqueia o acesso a scripts restritos redirecionando para login */ 
    function require_login(): void { 
        if (!is_logged_in()) { 
            header('Location: /portal-si/admin/login.php?erro=restrito');
            // MANDATÓRIO: Interrompe a execução do motor PHP imediatamente 
            exit; 
        } 
    } 
    /* Exemplo de guarda baseado em papel (RBAC) @param array $perfisPermitidos */
    function require_perfil(array $perfisPermitidos): void { 
        require_login(); 
        if (!in_array($_SESSION['usuario_perfil'], $perfisPermitidos, true)) { 
            header('Location: /portal-si/admin/dashboard.php?erro=permissao_negada'); 
            exit; 
        } 
    }
    
    /* admin/dashboard.php*/
    require_once __DIR__ . '/../../app/helpers/auth.php'; 
    /* Bloqueio mandatória: se não estiver autenticado, a execução para aqui*/ 
    require_login(); 
    
?> 
<!DOCTYPE html> 
<html lang="pt-BR"> 
    <head> 
        <meta charset="UTF-8"> 
        <title>Painel Administrativo — Portal SI</title> 
    </head> 
    <body> 
        <header> 
            <h1>Painel de Controle — Portal de Comunicação SI</h1> 
            <p>
                Usuário Conectado: <strong><?= htmlspecialchars($_SESSION['usuario_nome'], ENT_QUOTES, 'UTF-8'); ?></strong>
                (Perfil: <em><?= htmlspecialchars($_SESSION['usuario_perfil'], ENT_QUOTES, 'UTF-8'); ?></em>)
            </p> 
            <p>
                <a href="logout.php">Encerrar Sessão (Logout)</a>
            </p> 
        </header> 
        <hr> 
        <main> 
            <h3>Módulos de Gestão do Curso</h3> 
            <ul> 
            <li>
            <a href="conteudos/index.php">Gerenciar Publicações e Notícias</a>
            </li> 
            <li>
            <a href="eventos/index.php">Gerenciar Calendário de Eventos</a>
            </li> 
            <?php if ($_SESSION['usuario_perfil'] === 'admin'): ?> 
            <li>
            <a href="usuarios/index.php">Administração Geral de Usuários</a>
            </li> 
            <?php endif; ?> 
            </ul> 
        </main> 
    </body> 
</html>