<?php

require_once __DIR__ . '/config/conexao.php';

function mesPorExtenso($numero)
{
    $meses = [
        1 => 'janeiro',
        2 => 'fevereiro',
        3 => 'março',
        4 => 'abril',
        5 => 'maio',
        6 => 'junho',
        7 => 'julho',
        8 => 'agosto',
        9 => 'setembro',
        10 => 'outubro',
        11 => 'novembro',
        12 => 'dezembro'
    ];

    return $meses[(int) $numero] ?? '';
}

function iniciaisDoNome($nome)
{
    $partes = preg_split('/\s+/', trim($nome));
    $iniciais = mb_substr($partes[0], 0, 1);

    if (count($partes) > 1) {
        $iniciais .= mb_substr($partes[count($partes) - 1], 0, 1);
    }

    return mb_strtoupper($iniciais);
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmtUsuario = $pdo->prepare(
    "SELECT id, nome, perfil, criado_em
     FROM usuarios
     WHERE id = :id
       AND status = 'ativo'"
);

$stmtUsuario->execute([':id' => $id]);
$usuario = $stmtUsuario->fetch();

if (!$usuario) {
    header('Location: index.php');
    exit;
}

$stmtTotConteudos = $pdo->prepare(
    "SELECT COUNT(*) AS total
     FROM conteudos
     WHERE autor_id = :id
       AND status = 'publicado'
       AND publicado_em IS NOT NULL"
);

$stmtTotConteudos->execute([':id' => $id]);
$totConteudos = (int) $stmtTotConteudos->fetch()['total'];

$stmtTotEventos = $pdo->prepare(
    "SELECT COUNT(*) AS total
     FROM eventos
     WHERE autor_id = :id
       AND status = 'publicado'"
);

$stmtTotEventos->execute([':id' => $id]);
$totEventos = (int) $stmtTotEventos->fetch()['total'];

$stmtPubs = $pdo->prepare(
    "SELECT c.titulo,
            c.slug,
            c.resumo,
            c.imagem_capa,
            c.publicado_em,
            cat.nome AS categoria_nome,
            cat.slug AS categoria_slug
     FROM conteudos c
     INNER JOIN categorias cat ON cat.id = c.categoria_id
     WHERE c.autor_id = :id
       AND c.status = 'publicado'
       AND c.publicado_em IS NOT NULL
     ORDER BY c.publicado_em DESC
     LIMIT 6"
);

$stmtPubs->execute([':id' => $id]);
$publicacoes = $stmtPubs->fetchAll();

$membroDesde = mesPorExtenso(
    date('n', strtotime($usuario['criado_em']))
) . ' de ' . date('Y', strtotime($usuario['criado_em']));

$primeiroNome = explode(' ', trim($usuario['nome']))[0];
$tituloPagina = $usuario['nome'];

require_once __DIR__ . '/includes/header.php';

?>

<section class="autor-cabecalho">
    <div class="container autor-cabecalho-grid">

        <div class="autor-identidade">
            <div class="autor-avatar" aria-hidden="true">
                <?php echo htmlspecialchars(iniciaisDoNome($usuario['nome'])); ?>
            </div>

            <div class="autor-identidade-texto">
                <span class="autor-chapeu">Perfil público</span>

                <h1>
                    <?php echo htmlspecialchars($usuario['nome']); ?>
                </h1>

                <p>
                    <?php echo htmlspecialchars($usuario['perfil']); ?>
                    do Portal SI
                </p>
            </div>
        </div>

        <div class="autor-informacoes">
            <div class="autor-numeros">

                <div class="autor-numero">
                    <strong><?php echo $totConteudos; ?></strong>
                    <span>Publicações</span>
                </div>

                <div class="autor-numero">
                    <strong><?php echo $totEventos; ?></strong>
                    <span>Eventos</span>
                </div>

            </div>

            <p class="autor-desde">
                Colabora com o portal desde
                <?php echo htmlspecialchars($membroDesde); ?>.
            </p>
        </div>

    </div>
</section>

<section class="autor-publicacoes">
    <div class="container">

        <div class="autor-secao-titulo">
            <h2>
                Publicações de <?php echo htmlspecialchars($primeiroNome); ?>
            </h2>
            <span></span>
        </div>

        <?php if (!empty($publicacoes)): ?>
            <div class="autor-lista">

                <?php foreach ($publicacoes as $publicacao): ?>
                    <article class="autor-publicacao">

                        <a
                            href="conteudo.php?slug=<?php echo urlencode($publicacao['slug']); ?>"
                            class="autor-publicacao-imagem<?php echo empty($publicacao['imagem_capa']) ? ' autor-publicacao-imagem-vazia' : ''; ?>"
                        >
                            <?php if (!empty($publicacao['imagem_capa'])): ?>
                                <img
                                    src="<?php echo htmlspecialchars($publicacao['imagem_capa']); ?>"
                                    alt="<?php echo htmlspecialchars($publicacao['titulo']); ?>"
                                    loading="lazy"
                                >
                            <?php else: ?>
                                <span>Portal SI</span>
                            <?php endif; ?>
                        </a>

                        <div class="autor-publicacao-conteudo">

                            <div class="autor-publicacao-meta">
                                <a href="categoria.php?slug=<?php echo urlencode($publicacao['categoria_slug']); ?>">
                                    <?php echo htmlspecialchars($publicacao['categoria_nome']); ?>
                                </a>

                                <span></span>

                                <time datetime="<?php echo date('Y-m-d', strtotime($publicacao['publicado_em'])); ?>">
                                    <?php echo date('d/m/Y', strtotime($publicacao['publicado_em'])); ?>
                                </time>
                            </div>

                            <h3>
                                <a href="conteudo.php?slug=<?php echo urlencode($publicacao['slug']); ?>">
                                    <?php echo htmlspecialchars($publicacao['titulo']); ?>
                                </a>
                            </h3>

                            <?php if (!empty($publicacao['resumo'])): ?>
                                <p>
                                    <?php echo htmlspecialchars($publicacao['resumo']); ?>
                                </p>
                            <?php endif; ?>

                            <a
                                href="conteudo.php?slug=<?php echo urlencode($publicacao['slug']); ?>"
                                class="autor-publicacao-link"
                            >
                                Acessar publicação
                            </a>

                        </div>

                    </article>
                <?php endforeach; ?>

            </div>
        <?php else: ?>
            <div class="autor-sem-publicacoes">
                <h3>Nenhuma publicação disponível</h3>
                <p>Este autor ainda não publicou conteúdos no portal.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>