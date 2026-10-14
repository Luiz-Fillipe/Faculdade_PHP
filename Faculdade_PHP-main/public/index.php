<?php

require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/funcoes.php';
require_once __DIR__ . '/../app/Controller/HomeController.php';

$busca = trim((string) ($_GET['busca'] ?? ''));
$mensagemErro = mensagemDeErro($_GET['erro'] ?? '');

$home = new HomeController($pdo);
$dados = $home->carregar($busca);

$destaque = $dados['destaque'];
$chamadas = $dados['chamadas'];
$recentes = $dados['recentes'];
$eventos  = $dados['eventos'];

$tituloPagina = $busca !== '' ? 'Busca' : 'Início';

require_once __DIR__ . '/includes/header.php';

?>

<?php if ($mensagemErro !== ''): ?>
    <div class="container">
        <p class="alerta-erro"><?php echo htmlspecialchars($mensagemErro); ?></p>
    </div>
<?php endif; ?>

<section class="home-destaques">
    <div class="container">
        <?php if ($destaque): ?>
            <div class="home-destaques-grid">
                <article class="home-principal card">
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
                            <article class="home-chamada card">
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
                    <article class="home-noticia card border-0">
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
                    <article class="home-evento card">
                        <time class="home-evento-data" datetime="<?php echo date('Y-m-d', strtotime($evento['data_inicio'])); ?>">
                            <strong><?php echo date('d', strtotime($evento['data_inicio'])); ?></strong>
                            <span><?php echo mesPorExtenso(date('n', strtotime($evento['data_inicio']))); ?></span>
                            <small><?php echo date('H:i', strtotime($evento['data_inicio'])); ?></small>
                        </time>

                        <div class="home-evento-conteudo">
                            <h3><?php echo htmlspecialchars($evento['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($evento['local'] ?: 'Local a definir'); ?></p>
                        </div>

                        <?php $linkInscricao = linkValido($evento['link_inscricao'] ?? null); ?>
                        <?php if ($linkInscricao): ?>
                            <a href="<?php echo htmlspecialchars($linkInscricao); ?>" class="home-evento-link" target="_blank" rel="noopener noreferrer">Inscreva-se</a>
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
