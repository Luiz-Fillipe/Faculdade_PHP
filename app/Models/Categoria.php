<?php
/**
 * PÁGINA DE CATEGORIA — recebe ?slug=noticias (do menu dinâmico)
 */

require_once __DIR__ . '/config/conexao.php';

$slug = $_GET['slug'] ?? '';

$stmtCategoria = $pdo->prepare(
    "SELECT * FROM categorias WHERE slug = :slug AND ativo = TRUE"
);
$stmtCategoria->execute([':slug' => $slug]);
$categoria = $stmtCategoria->fetch();

if (!$categoria) {
    header('Location: index.php');
    exit;
}

$stmtConteudos = $pdo->prepare(
    "SELECT c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
            u.nome AS autor_nome
     FROM conteudos c
     INNER JOIN usuarios u ON u.id = c.autor_id
     WHERE c.categoria_id = :categoria_id
       AND c.status = 'publicado'
       AND c.publicado_em IS NOT NULL
     ORDER BY c.publicado_em DESC"
);
$stmtConteudos->execute([':categoria_id' => $categoria['id']]);
$conteudos = $stmtConteudos->fetchAll();

$tituloPagina = $categoria['nome'];
$slugAtual    = $categoria['slug'];   // marca o link ativo no menu
require_once __DIR__ . '/includes/header.php';
?>

    <section class="hero hero-categoria">
        <div class="container">
            <span class="hero-tag"><?php echo htmlspecialchars($categoria['icone']); ?> Categoria</span>
            <h1 class="hero-titulo"><?php echo htmlspecialchars($categoria['nome']); ?></h1>
            <p class="hero-resumo">
                <?php echo htmlspecialchars($categoria['descricao'] ?? ''); ?>
            </p>
        </div>
    </section>

    <section class="secao-publicacoes">
        <div class="container">
            <div class="grade">
                <?php foreach ($conteudos as $c): ?>
                    <article class="card">

                        <?php if (!empty($c['imagem_capa'])): ?>
                            <img class="card-imagem"
                                 src="<?php echo htmlspecialchars($c['imagem_capa']); ?>"
                                 alt="<?php echo htmlspecialchars($c['titulo']); ?>">
                        <?php endif; ?>

                        <h3 class="card-titulo"><?php echo htmlspecialchars($c['titulo']); ?></h3>
                        <p class="card-resumo"><?php echo htmlspecialchars($c['resumo']); ?></p>

                        <div class="card-rodape">
                            <span class="card-meta">
                                📅 <?php echo date('d/m/Y', strtotime($c['publicado_em'])); ?>
                                · ✍️ <?php echo htmlspecialchars($c['autor_nome']); ?>
                            </span>
                            <a class="card-link"
                               href="conteudo.php?slug=<?php echo urlencode($c['slug']); ?>">
                               Ler mais →
                            </a>
                        </div>

                    </article>
                <?php endforeach; ?>

                <?php if (empty($conteudos)): ?>
                    <p class="grade-vazia">Nenhuma publicação nesta categoria ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
