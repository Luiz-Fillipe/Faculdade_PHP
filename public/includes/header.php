<?php

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../funcoes.php';

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

// Faixa "AGORA": a publicação mais recente.
$ultimaPublicacao = $pdo->query(
    "SELECT titulo, slug, publicado_em
     FROM conteudos
     WHERE status = 'publicado'
       AND publicado_em IS NOT NULL
     ORDER BY publicado_em DESC
     LIMIT 1"
)->fetch();

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo $basePublica; ?>css/style.css?v=<?php echo filemtime(__DIR__ . '/../css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo $basePublica; ?>css/layout.css?v=<?php echo filemtime(__DIR__ . '/../css/layout.css'); ?>">
    <link rel="stylesheet" href="<?php echo $basePublica; ?>css/editorial.css?v=<?php echo filemtime(__DIR__ . '/../css/editorial.css'); ?>">
</head>
<body class="d-flex flex-column min-vh-100">

<header class="topo">
    <div class="topo-util">
        <div class="container">
            <span class="topo-data"><?php echo htmlspecialchars(dataDeHoje(), ENT_QUOTES, 'UTF-8'); ?></span>
            <nav class="topo-util-links" aria-label="Links institucionais">
                <a href="<?php echo $basePublica; ?>index.php">Institucional</a>
                <a href="<?php echo $basePublica; ?>evento.php">Agenda</a>
                <a href="mailto:contato@portalsi.edu.br">Contato</a>
                <?php if ($usuarioLogado): ?>
                    <a href="<?php echo $basePublica === '../' ? 'perfil.php' : 'admin/perfil.php'; ?>" title="Meu perfil">
                        <?php echo htmlspecialchars($nomeUsuario !== '' ? $nomeUsuario : 'Meu perfil', ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $basePublica; ?>login.php">Entrar</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>

    <div class="topo-principal">
        <div class="container">
            <a href="<?php echo $basePublica; ?>index.php" class="marca" aria-label="Portal SI — página inicial">
                <span class="marca-nome">Portal</span>
                <span class="marca-sigla">SI</span>
                <span class="marca-legenda">Sistema de Informações<br>Institucional</span>
            </a>

            <form action="<?php echo $basePublica; ?>index.php" method="get" class="busca" role="search">
                <label for="busca" class="texto-acessivel">Buscar no portal</label>
                <input type="search" id="busca" name="busca" value="<?php echo htmlspecialchars($buscaAtual); ?>" placeholder="Buscar no portal">
                <button type="submit" aria-label="Buscar">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="11" cy="11" r="6.5"></circle>
                        <path d="M16 16L21 21"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <nav class="topo-nav" aria-label="Navegação principal">
        <div class="container">
            <ul>
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

    <?php if ($ultimaPublicacao): ?>
        <div class="agora">
            <div class="container">
                <span class="agora-selo">Agora</span>
                <a class="agora-titulo" href="<?php echo $basePublica; ?>conteudo.php?slug=<?php echo urlencode($ultimaPublicacao['slug']); ?>">
                    <?php echo htmlspecialchars($ultimaPublicacao['titulo']); ?>
                </a>
                <span class="agora-tempo"><?php echo htmlspecialchars(tempoRelativo($ultimaPublicacao['publicado_em'])); ?></span>
            </div>
        </div>
    <?php endif; ?>
</header>

<main>
