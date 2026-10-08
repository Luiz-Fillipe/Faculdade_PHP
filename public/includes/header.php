<?php

require_once __DIR__ . '/../config/conexao.php';

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
    <link rel="stylesheet" href="css/style.css?v=<?php echo filemtime(__DIR__ . '/../css/style.css'); ?>">
    <link rel="stylesheet" href="css/layout.css?v=<?php echo filemtime(__DIR__ . '/../css/layout.css'); ?>">
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

            <a href="index.php" class="cabecalho-logo">
                <span class="cabecalho-logo-nome">
                    <small>Portal</small>
                    <strong>SI</strong>
                </span>
                <span class="cabecalho-logo-descricao">Sistema de Informações Institucional</span>
            </a>

            <div class="cabecalho-acoes">
                <div class="cabecalho-links">
                    <a href="index.php">Institucional</a>
                    <a href="evento.php">Agenda</a>
                    <a href="mailto:contato@portalsi.edu.br">Contato</a>
                </div>

                <form action="index.php" method="get" class="cabecalho-busca" role="search">
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
                    <a href="index.php" class="<?php echo ($paginaAtual === 'index.php' && $slugAtual === '') ? 'ativo' : ''; ?>">Início</a>
                </li>
                <?php foreach ($categorias as $cat): ?>
                    <li>
                        <a href="categoria.php?slug=<?php echo urlencode($cat['slug']); ?>" class="<?php echo ($cat['slug'] === $slugAtual) ? 'ativo' : ''; ?>">
                            <?php echo htmlspecialchars($cat['nome']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <?php if (!in_array('eventos', $slugsCategorias, true)): ?>
                    <li>
                        <a href="evento.php" class="<?php echo ($paginaAtual === 'evento.php') ? 'ativo' : ''; ?>">Eventos</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>

<main>
