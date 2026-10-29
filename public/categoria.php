<?php

require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/funcoes.php';
require_once __DIR__ . '/../app/Controller/ConteudoController.php';

const PUBLICACOES_POR_PAGINA = 6;

$slug = trim((string) ($_GET['slug'] ?? ''));
$pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);

$dadosCategoria = $slug === ''
    ? null
    : (new ConteudoController($pdo))->porCategoriaSlug($slug, $pagina, PUBLICACOES_POR_PAGINA);

if ($dadosCategoria === null) {
    paginaNaoEncontrada('Essa categoria não foi encontrada.');
}

$categoria = $dadosCategoria['categoria'];
$conteudos = $dadosCategoria['conteudos'];
$total = $dadosCategoria['total'];
$pagina = $dadosCategoria['pagina'];
$totalPaginas = $dadosCategoria['totalPaginas'];

$tituloPagina = $categoria['nome'];
$slugAtual    = $categoria['slug'];   
require_once __DIR__ . '/includes/header.php';
?>

    <section class="hero hero-categoria">
        <div class="container">
            <span class="hero-tag">Categoria</span>
            <h1 class="hero-titulo"><?php echo htmlspecialchars($categoria['nome']); ?></h1>
            <p class="hero-resumo">
                <?php echo htmlspecialchars($categoria['descricao'] ?? ''); ?>
            </p>
        </div>
    </section>

    <section class="secao-publicacoes">
        <div class="container">
            <p class="contador">
                <?php echo (int) $total; ?>
                <?php echo $total === 1 ? 'publicação encontrada' : 'publicações encontradas'; ?>
            </p>

            <div class="row g-4">
                <?php foreach ($conteudos as $c): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                    <article class="card portal-card h-100">

                        <?php if (!empty($c['imagem_capa'])): ?>
                            <img class="card-imagem"
                                 src="<?php echo htmlspecialchars($c['imagem_capa']); ?>"
                                 alt="<?php echo htmlspecialchars($c['titulo']); ?>">
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h3 class="card-titulo"><?php echo htmlspecialchars($c['titulo']); ?></h3>
                            <p class="card-resumo"><?php echo htmlspecialchars($c['resumo']); ?></p>

                            <div class="card-rodape mt-auto">
                                <span class="card-meta d-block">
                                    <?php echo date('d/m/Y', strtotime($c['publicado_em'])); ?>
                                    · <?php echo htmlspecialchars($c['autor_nome']); ?>
                                </span>
                                <a class="card-link"
                                   href="conteudo.php?slug=<?php echo urlencode($c['slug']); ?>">
                                   Ler mais →
                                </a>
                            </div>
                        </div>

                    </article>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($conteudos)): ?>
                    <div class="col-12">
                        <p class="grade-vazia mb-0">Nenhuma publicação nesta categoria ainda.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($totalPaginas > 1): ?>
                <?php $urlBase = 'categoria.php?slug=' . urlencode($categoria['slug']) . '&pagina='; ?>
                <nav class="paginacao" aria-label="Paginação das publicações">
                    <?php if ($pagina > 1): ?>
                        <a href="<?php echo htmlspecialchars($urlBase . ($pagina - 1), ENT_QUOTES, 'UTF-8'); ?>" rel="prev">&laquo; Anterior</a>
                    <?php else: ?>
                        <span class="desativado" aria-hidden="true">&laquo; Anterior</span>
                    <?php endif; ?>

                    <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                        <a href="<?php echo htmlspecialchars($urlBase . $p, ENT_QUOTES, 'UTF-8'); ?>"
                           class="<?php echo $p === $pagina ? 'ativo' : ''; ?>"
                           <?php echo $p === $pagina ? 'aria-current="page"' : ''; ?>>
                            <?php echo $p; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="<?php echo htmlspecialchars($urlBase . ($pagina + 1), ENT_QUOTES, 'UTF-8'); ?>" rel="next">Próxima &raquo;</a>
                    <?php else: ?>
                        <span class="desativado" aria-hidden="true">Próxima &raquo;</span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
