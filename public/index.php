<?php

require_once __DIR__ . '/config/conexao.php';

function mesAbreviado($numero)
{
    $meses = [
        1 => 'Jan',
        2 => 'Fev',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'Mai',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Set',
        10 => 'Out',
        11 => 'Nov',
        12 => 'Dez'
    ];

    return $meses[(int) $numero] ?? '';
}

$busca = trim($_GET['busca'] ?? '');
$destaque = false;

if ($busca === '') {
    $destaque = $pdo->query(
        "SELECT c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
                cat.nome AS categoria_nome,
                cat.slug AS categoria_slug,
                u.nome AS autor_nome
         FROM conteudos c
         INNER JOIN categorias cat ON cat.id = c.categoria_id
         INNER JOIN usuarios u ON u.id = c.autor_id
         WHERE c.destaque = TRUE
           AND c.status = 'publicado'
           AND c.publicado_em IS NOT NULL
         ORDER BY c.publicado_em DESC
         LIMIT 1"
    )->fetch();
}

if ($busca !== '') {
    $stmtPublicacoes = $pdo->prepare(
        "SELECT c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
                cat.nome AS categoria_nome,
                cat.slug AS categoria_slug,
                u.nome AS autor_nome
         FROM conteudos c
         INNER JOIN categorias cat ON cat.id = c.categoria_id
         INNER JOIN usuarios u ON u.id = c.autor_id
         WHERE c.status = 'publicado'
           AND c.publicado_em IS NOT NULL
           AND (c.titulo LIKE :busca OR c.resumo LIKE :busca)
         ORDER BY c.publicado_em DESC
         LIMIT 9"
    );
    $stmtPublicacoes->execute([':busca' => '%' . $busca . '%']);
    $publicacoes = $stmtPublicacoes->fetchAll();
} else {
    $publicacoes = $pdo->query(
        "SELECT c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
                cat.nome AS categoria_nome,
                cat.slug AS categoria_slug,
                u.nome AS autor_nome
         FROM conteudos c
         INNER JOIN categorias cat ON cat.id = c.categoria_id
         INNER JOIN usuarios u ON u.id = c.autor_id
         WHERE c.status = 'publicado'
           AND c.publicado_em IS NOT NULL
         ORDER BY c.publicado_em DESC
         LIMIT 9"
    )->fetchAll();
}

if ($destaque) {
    $publicacoes = array_values(
        array_filter(
            $publicacoes,
            function ($publicacao) use ($destaque) {
                return $publicacao['slug'] !== $destaque['slug'];
            }
        )
    );
} elseif (!empty($publicacoes)) {
    $destaque = array_shift($publicacoes);
}

$chamadas = array_slice($publicacoes, 0, 2);
$recentes = array_slice($publicacoes, 2, 6);

$eventos = $pdo->query(
    "SELECT id, titulo, data_inicio, local, link_inscricao
     FROM eventos
     WHERE status = 'publicado'
       AND data_inicio >= NOW()
     ORDER BY data_inicio ASC
     LIMIT 3"
)->fetchAll();

$tituloPagina = $busca !== '' ? 'Busca' : 'Início';

require_once __DIR__ . '/includes/header.php';

?>

<section class="home-destaques">
    <div class="container">
        <?php if ($destaque): ?>
            <div class="home-destaques-grid">
                <article class="home-principal">
                    <a href="conteudo.php?slug=<?php echo urlencode($destaque['slug']); ?>" class="home-principal-imagem<?php echo empty($destaque['imagem_capa']) ? ' home-imagem-vazia' : ''; ?>">
                        <?php if (!empty($destaque['imagem_capa'])): ?>
                            <img src="<?php echo htmlspecialchars($destaque['imagem_capa']); ?>" alt="<?php echo htmlspecialchars($destaque['titulo']); ?>">
                        <?php else: ?>
                            <span>Portal SI</span>
                        <?php endif; ?>
                    </a>

                    <div class="home-principal-conteudo">
                        <a href="categoria.php?slug=<?php echo urlencode($destaque['categoria_slug']); ?>" class="home-categoria">
                            <?php echo htmlspecialchars($destaque['categoria_nome']); ?>
                        </a>

                        <h1>
                            <a href="conteudo.php?slug=<?php echo urlencode($destaque['slug']); ?>">
                                <?php echo htmlspecialchars($destaque['titulo']); ?>
                            </a>
                        </h1>

                        <?php if (!empty($destaque['resumo'])): ?>
                            <p class="home-principal-resumo"><?php echo htmlspecialchars($destaque['resumo']); ?></p>
                        <?php endif; ?>

                        <p class="home-meta">
                            <?php echo date('d/m/Y', strtotime($destaque['publicado_em'])); ?>
                            <span></span>
                            Por <?php echo htmlspecialchars($destaque['autor_nome']); ?>
                        </p>
                    </div>
                </article>

                <?php if (!empty($chamadas)): ?>
                    <div class="home-chamadas">
                        <?php foreach ($chamadas as $publicacao): ?>
                            <article class="home-chamada">
                                <a href="conteudo.php?slug=<?php echo urlencode($publicacao['slug']); ?>" class="home-chamada-imagem<?php echo empty($publicacao['imagem_capa']) ? ' home-imagem-vazia' : ''; ?>">
                                    <?php if (!empty($publicacao['imagem_capa'])): ?>
                                        <img src="<?php echo htmlspecialchars($publicacao['imagem_capa']); ?>" alt="<?php echo htmlspecialchars($publicacao['titulo']); ?>" loading="lazy">
                                    <?php else: ?>
                                        <span>Portal SI</span>
                                    <?php endif; ?>
                                </a>

                                <div class="home-chamada-conteudo">
                                    <a href="categoria.php?slug=<?php echo urlencode($publicacao['categoria_slug']); ?>" class="home-categoria">
                                        <?php echo htmlspecialchars($publicacao['categoria_nome']); ?>
                                    </a>
                                    <h2>
                                        <a href="conteudo.php?slug=<?php echo urlencode($publicacao['slug']); ?>">
                                            <?php echo htmlspecialchars($publicacao['titulo']); ?>
                                        </a>
                                    </h2>
                                    <p class="home-meta"><?php echo date('d/m/Y', strtotime($publicacao['publicado_em'])); ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="home-sem-destaque">
                <span>Portal SI</span>
                <h1><?php echo $busca !== '' ? 'Nenhum resultado encontrado' : 'Informação institucional em um só lugar'; ?></h1>
                <p><?php echo $busca !== '' ? 'Tente pesquisar usando outras palavras.' : 'Acompanhe notícias, comunicados, publicações e eventos.'; ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($recentes)): ?>
    <section class="home-noticias">
        <div class="container">
            <div class="home-secao-titulo">
                <h2><?php echo $busca !== '' ? 'Mais resultados' : 'Últimas publicações'; ?></h2>
                <span></span>
            </div>

            <div class="home-noticias-grid">
                <?php foreach ($recentes as $publicacao): ?>
                    <article class="home-noticia">
                        <a href="conteudo.php?slug=<?php echo urlencode($publicacao['slug']); ?>" class="home-noticia-imagem<?php echo empty($publicacao['imagem_capa']) ? ' home-imagem-vazia' : ''; ?>">
                            <?php if (!empty($publicacao['imagem_capa'])): ?>
                                <img src="<?php echo htmlspecialchars($publicacao['imagem_capa']); ?>" alt="<?php echo htmlspecialchars($publicacao['titulo']); ?>" loading="lazy">
                            <?php else: ?>
                                <span>Portal SI</span>
                            <?php endif; ?>
                        </a>

                        <div class="home-noticia-conteudo">
                            <a href="categoria.php?slug=<?php echo urlencode($publicacao['categoria_slug']); ?>" class="home-categoria">
                                <?php echo htmlspecialchars($publicacao['categoria_nome']); ?>
                            </a>
                            <h3>
                                <a href="conteudo.php?slug=<?php echo urlencode($publicacao['slug']); ?>">
                                    <?php echo htmlspecialchars($publicacao['titulo']); ?>
                                </a>
                            </h3>
                            <?php if (!empty($publicacao['resumo'])): ?>
                                <p class="home-noticia-resumo"><?php echo htmlspecialchars($publicacao['resumo']); ?></p>
                            <?php endif; ?>
                            <p class="home-meta">
                                <?php echo date('d/m/Y', strtotime($publicacao['publicado_em'])); ?>
                                <span></span>
                                <?php echo htmlspecialchars($publicacao['autor_nome']); ?>
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($eventos) && $busca === ''): ?>
    <section class="home-agenda">
        <div class="container">
            <div class="home-secao-titulo home-secao-titulo-claro">
                <h2>Agenda institucional</h2>
                <span></span>
            </div>

            <div class="home-eventos-grid">
                <?php foreach ($eventos as $evento): ?>
                    <article class="home-evento">
                        <time class="home-evento-data" datetime="<?php echo date('Y-m-d', strtotime($evento['data_inicio'])); ?>">
                            <strong><?php echo date('d', strtotime($evento['data_inicio'])); ?></strong>
                            <span><?php echo mesAbreviado(date('n', strtotime($evento['data_inicio']))); ?></span>
                            <small><?php echo date('H:i', strtotime($evento['data_inicio'])); ?></small>
                        </time>

                        <div class="home-evento-conteudo">
                            <h3><?php echo htmlspecialchars($evento['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($evento['local'] ?: 'Local a definir'); ?></p>
                        </div>

                        <?php if (!empty($evento['link_inscricao'])): ?>
                            <a href="<?php echo htmlspecialchars($evento['link_inscricao']); ?>" class="home-evento-link" target="_blank" rel="noopener noreferrer">Inscreva-se</a>
                        <?php else: ?>
                            <a href="evento.php?id=<?php echo (int) $evento['id']; ?>" class="home-evento-link">Ver evento</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="home-agenda-rodape">
                <a href="evento.php">Ver agenda completa</a>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
