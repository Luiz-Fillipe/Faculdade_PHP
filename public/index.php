<?php

require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/funcoes.php';
require_once __DIR__ . '/../app/Controller/HomeController.php';
require_once __DIR__ . '/../app/Controller/ConteudoController.php';

$busca = trim($_GET['busca'] ?? '');
$categoriasComTotal = (new ConteudoController($pdo))->categoriasComTotal();
$mensagemErro = mensagemDeErro($_GET['erro'] ?? '');
$dadosHome = (new HomeController($pdo))->carregar($busca);
$destaque = $dadosHome['destaque'];
$chamadas = $dadosHome['chamadas'];
$recentes = $dadosHome['recentes'];
$eventos = $dadosHome['eventos'];

$tituloPagina = $busca !== '' ? 'Busca' : 'Início';

require_once __DIR__ . '/includes/header.php';

?>

<div class="container pt-4">
    <?php if ($mensagemErro !== ''): ?>
        <p class="alerta-erro"><?php echo htmlspecialchars($mensagemErro); ?></p>
    <?php endif; ?>

    <?php if ($busca !== ''): ?>
        <p class="ed-busca-titulo">Resultados para <strong>“<?php echo htmlspecialchars($busca); ?>”</strong></p>
    <?php endif; ?>
</div>

<?php if ($destaque): ?>
    <section class="ed-abertura">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <article class="ed-manchete">
                        <p class="ed-meta">
                            <a class="ed-kicker" href="categoria.php?slug=<?php echo urlencode($destaque['categoria_slug']); ?>"><?php echo htmlspecialchars($destaque['categoria_nome']); ?></a>
                            <span aria-hidden="true">·</span>
                            <time datetime="<?php echo date('Y-m-d\TH:i', strtotime($destaque['publicado_em'])); ?>"><?php echo htmlspecialchars(tempoRelativo($destaque['publicado_em'])); ?></time>
                        </p>

                        <h1 class="ed-manchete-titulo">
                            <a href="conteudo.php?slug=<?php echo urlencode($destaque['slug']); ?>"><?php echo htmlspecialchars($destaque['titulo']); ?></a>
                        </h1>

                        <?php if (!empty($destaque['resumo'])): ?>
                            <p class="ed-manchete-resumo"><?php echo htmlspecialchars($destaque['resumo']); ?></p>
                        <?php endif; ?>

                        <a class="ed-manchete-imagem<?php echo empty($destaque['imagem_capa']) ? ' sem-imagem' : ''; ?>" href="conteudo.php?slug=<?php echo urlencode($destaque['slug']); ?>" tabindex="-1" aria-hidden="true">
                            <?php if (!empty($destaque['imagem_capa'])): ?>
                                <img src="<?php echo htmlspecialchars($destaque['imagem_capa']); ?>" alt="">
                            <?php else: ?>
                                <span>Portal SI</span>
                            <?php endif; ?>
                        </a>
                    </article>
                </div>

                <?php if (!empty($chamadas)): ?>
                    <div class="col-lg-4">
                        <div class="ed-tiles">
                            <?php foreach ($chamadas as $publicacao): ?>
                                <a class="ed-tile<?php echo empty($publicacao['imagem_capa']) ? ' sem-imagem' : ''; ?>" href="conteudo.php?slug=<?php echo urlencode($publicacao['slug']); ?>">
                                    <?php if (!empty($publicacao['imagem_capa'])): ?>
                                        <img src="<?php echo htmlspecialchars($publicacao['imagem_capa']); ?>" alt="" loading="lazy">
                                    <?php endif; ?>
                                    <span class="ed-tile-texto">
                                        <span class="ed-chip"><?php echo htmlspecialchars($publicacao['categoria_nome']); ?></span>
                                        <strong class="ed-tile-titulo"><?php echo htmlspecialchars($publicacao['titulo']); ?></strong>
                                        <span class="ed-tile-tempo"><?php echo htmlspecialchars(tempoRelativo($publicacao['publicado_em'])); ?></span>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php else: ?>
    <section class="container ed-vazio-home">
        <p class="estado-vazio">
            <?php echo $busca !== '' ? 'Nenhum resultado encontrado. Tente pesquisar usando outras palavras.' : 'Ainda não há publicações. Volte em breve.'; ?>
        </p>
    </section>
<?php endif; ?>

<section class="ed-corpo">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <?php if (!empty($recentes)): ?>
                    <div class="ed-titulo-secao">
                        <h2><?php echo $busca !== '' ? 'Mais resultados' : 'Últimas notícias'; ?></h2>
                    </div>

                    <?php foreach ($recentes as $item): ?>
                        <?php include __DIR__ . '/includes/item_lista.php'; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <aside class="col-lg-4 ps-lg-4" aria-label="Barra lateral">
                <?php if ($busca === '' && !empty($categoriasComTotal)): ?>
                    <div class="ed-lateral">
                        <div class="ed-titulo-secao"><h2>Editorias</h2></div>
                        <ul class="ed-lista-editorias">
                            <?php foreach ($categoriasComTotal as $cat): ?>
                                <li>
                                    <a href="categoria.php?slug=<?php echo urlencode($cat['slug']); ?>">
                                        <span><?php echo htmlspecialchars($cat['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <b><?php echo (int) $cat['total']; ?></b>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($eventos) && $busca === ''): ?>
                    <div class="ed-lateral">
                        <div class="ed-titulo-secao">
                            <h2>Agenda</h2>
                            <a href="evento.php">Ver tudo</a>
                        </div>

                        <ul class="ed-agenda">
                            <?php foreach ($eventos as $evento): ?>
                                <?php $inicio = strtotime($evento['data_inicio']); ?>
                                <li>
                                    <time class="ed-agenda-data" datetime="<?php echo date('Y-m-d', $inicio); ?>">
                                        <strong><?php echo date('d', $inicio); ?></strong>
                                        <span><?php echo htmlspecialchars(mb_substr(mesPorExtenso(date('n', $inicio)), 0, 3, 'UTF-8')); ?></span>
                                    </time>
                                    <div>
                                        <a class="ed-agenda-titulo" href="evento.php?id=<?php echo (int) $evento['id']; ?>"><?php echo htmlspecialchars($evento['titulo']); ?></a>
                                        <p class="ed-meta">
                                            <?php echo date('H:i', $inicio); ?>
                                            <span aria-hidden="true">·</span>
                                            <?php echo htmlspecialchars($evento['local'] ?: 'Local a definir'); ?>
                                        </p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
