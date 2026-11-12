<?php
// Item de lista editorial: linha-fina, título, resumo e miniatura.
// Espera a variável $item com: titulo, slug, resumo, imagem_capa, publicado_em e,
// opcionalmente, categoria_nome / categoria_slug / autor_nome.
$linkItem = 'conteudo.php?slug=' . urlencode($item['slug']);
?>
<article class="ed-item<?php echo empty($item['imagem_capa']) ? ' ed-item-so-texto' : ''; ?>">
    <div class="ed-item-texto">
        <?php if (!empty($item['categoria_nome'])): ?>
            <a class="ed-kicker" href="categoria.php?slug=<?php echo urlencode($item['categoria_slug']); ?>"><?php echo htmlspecialchars($item['categoria_nome']); ?></a>
        <?php endif; ?>

        <h3 class="ed-item-titulo">
            <a href="<?php echo $linkItem; ?>"><?php echo htmlspecialchars($item['titulo']); ?></a>
        </h3>

        <?php if (!empty($item['resumo'])): ?>
            <p class="ed-item-resumo"><?php echo htmlspecialchars($item['resumo']); ?></p>
        <?php endif; ?>

        <p class="ed-meta">
            <time datetime="<?php echo date('Y-m-d\TH:i', strtotime($item['publicado_em'])); ?>"><?php echo htmlspecialchars(tempoRelativo($item['publicado_em'])); ?></time>
            <?php if (!empty($item['autor_nome'])): ?>
                <span aria-hidden="true">·</span> <?php echo htmlspecialchars($item['autor_nome']); ?>
            <?php endif; ?>
        </p>
    </div>

    <?php if (!empty($item['imagem_capa'])): ?>
        <a class="ed-item-imagem" href="<?php echo $linkItem; ?>" tabindex="-1" aria-hidden="true">
            <img src="<?php echo htmlspecialchars($item['imagem_capa']); ?>" alt="" loading="lazy">
        </a>
    <?php endif; ?>
</article>
