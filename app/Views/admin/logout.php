<?php 
    // admin/logout.php 
    // 1. Inicializa o contexto para manipulação da sessão ativa 
    if (session_status() === PHP_SESSION_NONE) { 
        session_start(); 
    } 
    // 2.   Limpa   todas   as   variáveis   da   sessão   em   memória   
    $_SESSION   =   [];   
    //   3.   Invalida   o   cookie   de   sessão   no   navegador   do   cliente   
    if (ini_get("session.use_cookies")) { 
    $params = session_get_cookie_params(); 
        setcookie( session_name(), 
        // Nome do cookie (ex: PHPSESSID) 
        '', // Valor vazio 
        time() - 42000, // Timestamp no passado (força expiração) 
        $params["path"], // Escopo de caminho original 
        $params["domain"], // Domínio configurado 
        $params["secure"], // Flag de transmissão HTTPS 
        $params["httponly"] // Restrição a scripts client-side 
        ); 
    } 
    // 4. Destrói o registro físico da sessão no servidor de hospedagem 
    session_destroy(); 
    // 5. Redireciona o usuário para o login com notificação 
    header('Location: login.php?msg=logout_sucesso'); 
    exit;
?>