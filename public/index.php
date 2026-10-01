<?php
/**
 * HOME — Portal SI
 * 1. Hero com conteúdo em DESTAQUE
 * 2. Grade de publicações recentes por categoria
 * 3. Próximos eventos
 */

require_once __DIR__ . '/config/conexao.php';

// Mês abreviado em português (para eventos)
function mesAbreviado($n) {
    $meses = [1=>'Jan',2=>'Fev',3=>'Mar',4=>'Abr',5=>'Mai',6=>'Jun',
              7=>'Jul',8=>'Ago',9=>'Set',10=>'Out',11=>'Nov',12=>'Dez'];
    return $meses[(int)$n];
}

// ---- 1) DESTAQUE (hero) ----
$destaque = $pdo->query(
    "SELECT c.titulo, c.slug, c.resumo, c.publicado_em,
            cat.nome AS categoria_nome, cat.slug AS categoria_slug, cat.icone AS categoria_icone,
            u.nome  AS autor_nome
     FROM conteudos c
     INNER JOIN categorias cat ON cat.id = c.categoria_id
     INNER JOIN usuarios u     ON u.id   = c.autor_id
     WHERE c.destaque = TRUE
       AND c.status = 'publicado'
       AND c.publicado_em IS NOT NULL
     ORDER BY c.publicado_em DESC
     LIMIT 1"
)->fetch();

// ---- 2) PUBLICAÇÕES RECENTES ----
$publicacoes = $pdo->query(
    "SELECT c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
            cat.nome AS categoria_nome, cat.slug AS categoria_slug, cat.icone AS categoria_icone,
            u.nome  AS autor_nome
     FROM conteudos c
     INNER JOIN categorias cat ON cat.id = c.categoria_id
     INNER JOIN usuarios u     ON u.id   = c.autor_id
     WHERE c.status = 'publicado'
       AND c.publicado_em IS NOT NULL
     ORDER BY c.publicado_em DESC
     LIMIT 6"
)->fetchAll();

// ---- 3) PRÓXIMOS EVENTOS ----
$eventos = $pdo->query(
    "SELECT titulo, data_inicio, local, link_inscricao
     FROM eventos
     WHERE status = 'publicado'
       AND data_inicio >= NOW()
     ORDER BY data_inicio ASC
     LIMIT 3"
)->fetchAll();

$tituloPagina = 'Início';
require_once __DIR__ . '/includes/header.php';
?>

    <!-- ========== HERO DE DESTAQUE ========== -->
    <section class="hero">
        <div class="container">

            <?php if ($destaque): ?>
                <span class="hero-tag">
                    <?php echo htmlspecialchars($destaque['categoria_icone']); ?>
                    Em destaque — <?php echo htmlspecialchars($destaque['categoria_nome']); ?>
                </span>

                <h1 class="hero-titulo"><?php echo htmlspecialchars($destaque['titulo']); ?></h1>
                <p class="hero-resumo"><?php echo htmlspecialchars($destaque['resumo']); ?></p>

                <p class="hero-data">
                    📅 <?php echo date('d/m/Y', strtotime($destaque['publicado_em'])); ?>
                    &nbsp;·&nbsp; ✍️ <?php echo htmlspecialchars($destaque['autor_nome']); ?>
                </p>

                <a href="conteudo.php?slug=<?php echo urlencode($destaque['slug']); ?>"
                   class="botao botao-claro">Ler publicação completa →</a>
            <?php else: ?>
                <h1 class="hero-titulo">Bem-vindo ao Portal SI</h1>
                <p class="hero-resumo">Notícias, eventos, comunicados e muito mais em um só lugar.</p>
            <?php endif; ?>

        </div>
    </section>

    <!-- ========== GRADE DE PUBLICAÇÕES RECENTES ========== -->
    <section class="secao-publicacoes">
        <div class="container">
            <h2 class="secao-titulo">Publicações Recentes</h2>
            <p class="secao-subtitulo">As últimas novidades organizadas por categoria</p>

            <div class="grade">
                <?php foreach ($publicacoes as $p): ?>
                    <article class="card">

                        <span class="card-categoria">
                            <?php echo htmlspecialchars($p['categoria_icone'] . ' ' . $p['categoria_nome']); ?>
                        </span>

                        <?php if (!empty($p['imagem_capa'])): ?>
                            <img class="card-imagem"
                                 src="<?php echo htmlspecialchars($p['imagem_capa']); ?>"
                                 alt="<?php echo htmlspecialchars($p['titulo']); ?>">
                        <?php endif; ?>

                        <h3 class="card-titulo"><?php echo htmlspecialchars($p['titulo']); ?></h3>
                        <p class="card-resumo"><?php echo htmlspecialchars($p['resumo']); ?></p>

                        <div class="card-rodape">
                            <span class="card-meta">
                                📅 <?php echo date('d/m/Y', strtotime($p['publicado_em'])); ?>
                                · ✍️ <?php echo htmlspecialchars($p['autor_nome']); ?>
                            </span>
                            <a class="card-link"
                               href="conteudo.php?slug=<?php echo urlencode($p['slug']); ?>">
                               Ler mais →
                            </a>
                        </div>

                    </article>
                <?php endforeach; ?>

                <?php if (empty($publicacoes)): ?>
                    <p class="grade-vazia">Nenhuma publicação encontrada.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ========== PRÓXIMOS EVENTOS ========== -->
    <?php if (!empty($eventos)): ?>
    <section class="secao-eventos">
        <div class="container">
            <h2 class="secao-titulo">Próximos Eventos</h2>
            <p class="secao-subtitulo">Fique por dentro da agenda institucional</p>

            <div class="evento-lista">
                <?php foreach ($eventos as $ev): ?>
                    <article class="evento-item">

                        <div class="evento-data">
                            <span class="evento-dia"><?php echo date('d', strtotime($ev['data_inicio'])); ?></span>
                            <span class="evento-mes"><?php echo mesAbreviado(date('n', strtotime($ev['data_inicio']))); ?></span>
                        </div>

                        <div class="evento-info">
                            <h3><?php echo htmlspecialchars($ev['titulo']); ?></h3>
                            <p>📍 <?php echo htmlspecialchars($ev['local'] ?? 'Local a definir'); ?></p>
                        </div>

                        <?php if (!empty($ev['link_inscricao'])): ?>
                            <a href="<?php echo htmlspecialchars($ev['link_inscricao']); ?>"
                               class="botao-pequeno" target="_blank">Inscrever-se</a>
                        <?php endif; ?>

                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
