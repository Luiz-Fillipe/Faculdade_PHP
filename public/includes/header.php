<?php
/**
 * HEADER REUTILIZÁVEL — Portal SI
 * Menu dinâmico: categorias ATIVAS, na ORDEM definida no banco (PHP puro)
 */

require_once __DIR__ . '/../config/conexao.php';

// ---- MENU DINÂMICO ----
$categorias = $pdo->query(
    "SELECT id, nome, slug, icone
     FROM categorias
     WHERE ativo = TRUE
     ORDER BY ordem, nome"
)->fetchAll();

$tituloPagina = $tituloPagina ?? 'Início';
$slugAtual    = $slugAtual ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tituloPagina); ?> | Portal SI</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ===== TOPO ===== -->
<header class="topo">
    <div class="container topo-flex">
        <a href="index.php" class="logo">
            <span class="logo-sigla">SI</span>
            <span class="logo-texto">Portal SI</span>
        </a>
        <p class="topo-frase">Sistema de Informações Institucional</p>
    </div>
</header>

<!-- ===== MENU DINÂMICO (gerado do banco) ===== -->
<nav class="menu">
    <div class="container">
        <ul class="menu-lista">
            <li>
                <a href="index.php"
                   class="<?php echo ($tituloPagina === 'Início') ? 'ativo' : ''; ?>">
                   🏠 Início
                </a>
            </li>
            <?php foreach ($categorias as $cat): ?>
                <li>
                    <a href="categoria.php?slug=<?php echo urlencode($cat['slug']); ?>"
                       class="<?php echo ($cat['slug'] === $slugAtual) ? 'ativo' : ''; ?>">
                        <?php echo htmlspecialchars($cat['icone'] . ' ' . $cat['nome']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<!-- ===== CONTEÚDO DA PÁGINA ===== -->
<main>
