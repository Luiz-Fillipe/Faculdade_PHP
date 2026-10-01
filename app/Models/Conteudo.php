<?php
/**
 * PÁGINA DE DETALHE — recebe ?slug=titulo-da-publicacao
 */

require_once __DIR__ . '/config/conexao.php';

$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare(
    "SELECT c.*, cat.nome AS categoria_nome, cat.slug AS categoria_slug,
            cat.icone AS categoria_icone, u.nome AS autor_nome
     FROM conteudos c
     INNER JOIN categorias cat ON cat.id = c.categoria_id
     INNER JOIN usuarios u     ON u.id   = c.autor_id
     WHERE c.slug = :slug AND c.status = 'publicado'"
);
$stmt->execute([':slug' => $slug]);
$conteudo = $stmt->fetch();

if (!$conteudo) {
    header('Location: index.php');
    exit;
}

// Tags do conteúdo
$stmtTags = $pdo->prepare(
    "SELECT t.nome FROM tags t
     INNER JOIN conteudo_tags ct ON ct.tag_id = t.id
     WHERE ct.conteudo_id = :id"
);
$stmtTags->execute([':id' => $conteudo['id']]);
$tags = $stmtTags->fetchAll();

// Extrai o ID do vídeo do YouTube (sem JavaScript)
$videoId = null;
if (!empty($conteudo['link_youtube'])) {
    $partes = parse_url($conteudo['link_youtube']);
    if (strpos($partes['host'] ?? '', 'youtu.be') !== false) {
        $videoId = ltrim($partes['path'] ?? '', '/');
    } else {
        parse_str($partes['query'] ?? '', $q);
        $videoId = $q['v'] ?? null;
    }
}

$tituloPagina = $conteudo['titulo'];
$slugAtual    = $conteudo['categoria_slug'];
require_once __DIR__ . '/includes/header.php';
?>

    <article class="detalhe">

        <span class="card-categoria">
            <?php echo htmlspecialchars($conteudo['categoria_icone'] . ' ' . $conteudo['categoria_nome']); ?>
        </span>

        <h1 class="detalhe-titulo"><?php echo htmlspecialchars($conteudo['titulo']); ?></h1>

        <p class="detalhe-meta">
            📅 <?php echo date('d/m/Y', strtotime($conteudo['publicado_em'])); ?>
            &nbsp;·&nbsp; ✍️ <?php echo htmlspecialchars($conteudo['autor_nome']); ?>
        </p>

        <?php if (!empty($conteudo['imagem_capa'])): ?>
            <img class="detalhe-imagem"
                 src="<?php echo htmlspecialchars($conteudo['imagem_capa']); ?>"
                 alt="<?php echo htmlspecialchars($conteudo['titulo']); ?>">
        <?php endif; ?>

        <?php if ($videoId): ?>
            <div class="video-wrapper">
                <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($videoId); ?>"
                        title="Vídeo da publicação"
                        allowfullscreen></iframe>
            </div>
        <?php endif; ?>

        <div class="detalhe-corpo">
            <?php echo nl2br(htmlspecialchars($conteudo['corpo'])); ?>
        </div>

        <?php if (!empty($tags)): ?>
            <div class="tag-lista">
                <?php foreach ($tags as $t): ?>
                    <span class="tag"># <?php echo htmlspecialchars($t['nome']); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
