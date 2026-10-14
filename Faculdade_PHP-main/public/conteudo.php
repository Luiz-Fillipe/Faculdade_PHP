<?php

require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/../app/Controller/ConteudoController.php';

$slug = trim((string) ($_GET['slug'] ?? ''));

if ($slug === '') {
    header('Location: index.php?erro=conteudo');
    exit;
}

$conteudoControlador = new ConteudoController($pdo);
$conteudo = $conteudoControlador->porSlug($slug);

if (!$conteudo) {
    header('Location: index.php?erro=conteudo');
    exit;
}

$tags = $conteudoControlador->tagsDoConteudo((int) $conteudo['id']);
$videoId = ConteudoController::extrairVideoId($conteudo['link_youtube'] ?? null);

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

        <aside class="publicacao-informacoes card">
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
