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

<section class="ed-corpo">
    <div class="container">
        <header class="ed-cabecalho-secao">
            <p class="ed-kicker">Editoria</p>
            <h1><?php echo htmlspecialchars($categoria['nome']); ?></h1>
            <?php if (!empty($categoria['descricao'])): ?>
                <p class="ed-cabecalho-descricao"><?php echo htmlspecialchars($categoria['descricao']); ?></p>
            <?php endif; ?>
            <p class="contador">
                <?php echo (int) $total; ?>
                <?php echo $total === 1 ? 'publicação encontrada' : 'publicações encontradas'; ?>
            </p>
        </header>

        <div class="row">
            <div class="col-lg-9">
                <?php if (empty($conteudos)): ?>
                    <p class="estado-vazio">Nenhuma publicação nesta categoria ainda.</p>
                <?php endif; ?>

                <?php foreach ($conteudos as $item): ?>
                    <?php include __DIR__ . '/includes/item_lista.php'; ?>
                <?php endforeach; ?>

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
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
