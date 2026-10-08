<?php

require_once __DIR__ . '/config/conexao.php';

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    "SELECT c.*,
            cat.nome AS categoria_nome,
            cat.slug AS categoria_slug,
            u.nome AS autor_nome
     FROM conteudos c
     INNER JOIN categorias cat ON cat.id = c.categoria_id
     INNER JOIN usuarios u ON u.id = c.autor_id
     WHERE c.slug = :slug
       AND c.status = 'publicado'"
);

$stmt->execute([':slug' => $slug]);
$conteudo = $stmt->fetch();

if (!$conteudo) {
    header('Location: index.php');
    exit;
}

$stmtTags = $pdo->prepare(
    "SELECT t.nome
     FROM tags t
     INNER JOIN conteudo_tags ct ON ct.tag_id = t.id
     WHERE ct.conteudo_id = :id
     ORDER BY t.nome"
);

$stmtTags->execute([':id' => $conteudo['id']]);
$tags = $stmtTags->fetchAll();

$videoId = null;

if (!empty($conteudo['link_youtube'])) {
    $partes = parse_url($conteudo['link_youtube']);
    $host = strtolower($partes['host'] ?? '');
    $caminho = trim($partes['path'] ?? '', '/');

    if (str_contains($host, 'youtu.be')) {
        $videoId = explode('/', $caminho)[0] ?? null;
    } elseif (str_contains($host, 'youtube.com')) {
        parse_str($partes['query'] ?? '', $parametros);

        if (!empty($parametros['v'])) {
            $videoId = $parametros['v'];
        } elseif (str_starts_with($caminho, 'embed/')) {
            $videoId = substr($caminho, 6);
        } elseif (str_starts_with($caminho, 'shorts/')) {
            $videoId = substr($caminho, 7);
        }
    }

    if (
        $videoId !== null
        && !preg_match('/^[a-zA-Z0-9_-]{6,20}$/', $videoId)
    ) {
        $videoId = null;
    }
}

$tituloPagina = $conteudo['titulo'];
$slugAtual = $conteudo['categoria_slug'];

require_once __DIR__ . '/includes/header.php';

?>

<article class="publicacao">

    <header class="publicacao-cabecalho">
        <div class="container publicacao-cabecalho-conteudo">

            <a
                href="categoria.php?slug=<?php echo urlencode($conteudo['categoria_slug']); ?>"
                class="publicacao-categoria"
            >
                <?php echo htmlspecialchars($conteudo['categoria_nome']); ?>
            </a>

            <h1>
                <?php echo htmlspecialchars($conteudo['titulo']); ?>
            </h1>

            <?php if (!empty($conteudo['resumo'])): ?>
                <p class="publicacao-resumo">
                    <?php echo htmlspecialchars($conteudo['resumo']); ?>
                </p>
            <?php endif; ?>

            <div class="publicacao-meta">
                <time datetime="<?php echo date('Y-m-d', strtotime($conteudo['publicado_em'])); ?>">
                    Publicado em
                    <?php echo date('d/m/Y', strtotime($conteudo['publicado_em'])); ?>
                </time>

                <span></span>

                <p>
                    Por
                    <a href="usuario.php?id=<?php echo (int) $conteudo['autor_id']; ?>">
                        <?php echo htmlspecialchars($conteudo['autor_nome']); ?>
                    </a>
                </p>
            </div>

        </div>
    </header>

    <?php if (!empty($conteudo['imagem_capa'])): ?>
        <div class="container publicacao-capa-container">
            <figure class="publicacao-capa">
                <img
                    src="<?php echo htmlspecialchars($conteudo['imagem_capa']); ?>"
                    alt="<?php echo htmlspecialchars($conteudo['titulo']); ?>"
                >
            </figure>
        </div>
    <?php endif; ?>

    <div class="container publicacao-layout">

        <div class="publicacao-principal">

            <div class="publicacao-texto">
                <?php echo nl2br(htmlspecialchars($conteudo['corpo'])); ?>
            </div>

            <?php if ($videoId): ?>
                <div class="publicacao-video">
                    <iframe
                        src="https://www.youtube.com/embed/<?php echo htmlspecialchars($videoId); ?>"
                        title="Vídeo relacionado à publicação"
                        loading="lazy"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                </div>
            <?php endif; ?>

            <?php if (!empty($tags)): ?>
                <footer class="publicacao-tags">
                    <span class="publicacao-tags-titulo">Assuntos</span>

                    <div class="publicacao-tags-lista">
                        <?php foreach ($tags as $tag): ?>
                            <span>
                                <?php echo htmlspecialchars($tag['nome']); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </footer>
            <?php endif; ?>

        </div>

        <aside class="publicacao-informacoes">
            <div>
                <span>Categoria</span>
                <a href="categoria.php?slug=<?php echo urlencode($conteudo['categoria_slug']); ?>">
                    <?php echo htmlspecialchars($conteudo['categoria_nome']); ?>
                </a>
            </div>

            <div>
                <span>Publicação</span>
                <time datetime="<?php echo date('Y-m-d', strtotime($conteudo['publicado_em'])); ?>">
                    <?php echo date('d/m/Y', strtotime($conteudo['publicado_em'])); ?>
                </time>
            </div>

            <div>
                <span>Autoria</span>
                <a href="usuario.php?id=<?php echo (int) $conteudo['autor_id']; ?>">
                    <?php echo htmlspecialchars($conteudo['autor_nome']); ?>
                </a>
            </div>

            <a
                href="categoria.php?slug=<?php echo urlencode($conteudo['categoria_slug']); ?>"
                class="publicacao-voltar"
            >
                Mais conteúdos desta categoria
            </a>
        </aside>

    </div>

</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>