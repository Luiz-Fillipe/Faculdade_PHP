<?php

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

$usuarioLogado = is_logged_in();
$nomeUsuario = (string) ($_SESSION['usuario_nome'] ?? '');
$iniciaisUsuario = '';
foreach (preg_split('/\s+/', trim($nomeUsuario), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $parteNome) {
    $iniciaisUsuario .= mb_substr($parteNome, 0, 1, 'UTF-8');
    if (mb_strlen($iniciaisUsuario, 'UTF-8') >= 2) {
        break;
    }
}
$iniciaisUsuario = $iniciaisUsuario !== '' ? mb_strtoupper($iniciaisUsuario, 'UTF-8') : 'U';

$basePublica = str_contains(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''), '/admin/') ? '../' : '';

$categorias = $pdo->query(
    "SELECT id, nome, slug
     FROM categorias
     WHERE ativo = TRUE
     ORDER BY ordem, nome"
)->fetchAll();

$tituloPagina = $tituloPagina ?? 'Início';
$slugAtual = $slugAtual ?? '';
$paginaAtual = basename($_SERVER['PHP_SELF']);
$buscaAtual = trim($_GET['busca'] ?? '');
$slugsCategorias = array_column($categorias, 'slug');

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal SI — Sistema de Informações Institucional">
    <title><?php echo htmlspecialchars($tituloPagina); ?> | Portal SI</title>
    <link rel="stylesheet" href="<?php echo $basePublica; ?>css/style.css?v=<?php echo filemtime(__DIR__ . '/../css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo $basePublica; ?>css/layout.css?v=<?php echo filemtime(__DIR__ . '/../css/layout.css'); ?>">
</head>
<body>

<input type="checkbox" id="navegacao-controle" class="navegacao-controle" aria-hidden="true">

<header class="cabecalho">
    <div class="cabecalho-principal">
        <div class="container cabecalho-linha">
            <label for="navegacao-controle" class="cabecalho-menu-botao" aria-label="Abrir menu">
                <span></span>
                <span></span>
                <span></span>
            </label>

            <a href="<?php echo $basePublica; ?>index.php" class="cabecalho-logo">
                <span class="cabecalho-logo-nome">
                    <small>Portal</small>
                    <strong>SI</strong>
                </span>
                <span class="cabecalho-logo-descricao">Sistema de Informações Institucional</span>
            </a>

            <div class="cabecalho-acoes">
                <div class="cabecalho-links">
                    <a href="<?php echo $basePublica; ?>index.php">Institucional</a>
                    <a href="<?php echo $basePublica; ?>evento.php">Agenda</a>
                    <a href="mailto:contato@portalsi.edu.br">Contato</a>
                    <?php if ($usuarioLogado): ?>
                        <a class="atalho-perfil" href="<?php echo $basePublica === '../' ? 'perfil.php' : 'admin/perfil.php'; ?>" aria-label="Abrir perfil de <?php echo htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8'); ?>" title="Meu perfil">
                            <span class="avatar-cabecalho"><?php echo htmlspecialchars($iniciaisUsuario, ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $basePublica; ?>login.php">Entrar</a>
                    <?php endif; ?>
                </div>

                <form action="<?php echo $basePublica; ?>index.php" method="get" class="cabecalho-busca" role="search">
                    <label for="busca" class="texto-acessivel">Buscar no portal</label>
                    <input type="search" id="busca" name="busca" value="<?php echo htmlspecialchars($buscaAtual); ?>" placeholder="Buscar no portal">
                    <button type="submit" aria-label="Buscar">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="6.5"></circle>
                            <path d="M16 16L21 21"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <nav class="editorias" aria-label="Navegação principal">
        <div class="container editorias-conteudo">
            <ul class="editorias-lista">
                <li>
                    <a href="<?php echo $basePublica; ?>index.php" class="<?php echo ($paginaAtual === 'index.php' && $slugAtual === '') ? 'ativo' : ''; ?>">Início</a>
                </li>
                <?php foreach ($categorias as $cat): ?>
                    <li>
                        <a href="<?php echo $basePublica; ?>categoria.php?slug=<?php echo urlencode($cat['slug']); ?>" class="<?php echo ($cat['slug'] === $slugAtual) ? 'ativo' : ''; ?>">
                            <?php echo htmlspecialchars($cat['nome']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <?php if (!in_array('eventos', $slugsCategorias, true)): ?>
                    <li>
                        <a href="<?php echo $basePublica; ?>evento.php" class="<?php echo ($paginaAtual === 'evento.php') ? 'ativo' : ''; ?>">Eventos</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>

<main>
