<?php


require_once __DIR__ . '/config/conexao.php';


function mesPorExtenso($n) {
    $meses = [1=>'janeiro',2=>'fevereiro',3=>'março',4=>'abril',5=>'maio',6=>'junho',
              7=>'julho',8=>'agosto',9=>'setembro',10=>'outubro',11=>'novembro',12=>'dezembro'];
    return $meses[(int)$n];
}

function iniciaisDoNome($nome) {
    $partes   = preg_split('/\s+/', trim($nome));
    $iniciais = mb_substr($partes[0], 0, 1);
    if (count($partes) > 1) {
        $iniciais .= mb_substr($partes[count($partes) - 1], 0, 1);
    }
    return mb_strtoupper($iniciais);
}


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}


$stmtUsuario = $pdo->prepare(
    "SELECT id, nome, perfil, criado_em
     FROM usuarios
     WHERE id = :id AND status = 'ativo'"
);
$stmtUsuario->execute([':id' => $id]);
$usuario = $stmtUsuario->fetch();

if (!$usuario) {
    header('Location: index.php');
    exit;
}


$emojisPerfil = [
    'Admin'       
    'Editor'      
    'Coordenador' 
    'Aluno'       
];
$emojiPerfil = $emojisPerfil[$usuario['perfil']] ?? '👤';


$stmtTotConteudos = $pdo->prepare(
    "SELECT COUNT(*) AS total
     FROM conteudos
     WHERE autor_id = :id
       AND status = 'publicado'
       AND publicado_em IS NOT NULL"
);
$stmtTotConteudos->execute([':id' => $id]);
$totConteudos = (int)$stmtTotConteudos->fetch()['total'];

$stmtTotEventos = $pdo->prepare(
    "SELECT COUNT(*) AS total
     FROM eventos
     WHERE autor_id = :id AND status = 'publicado'"
);
$stmtTotEventos->execute([':id' => $id]);
$totEventos = (int)$stmtTotEventos->fetch()['total'];


$stmtPubs = $pdo->prepare(
    "SELECT c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
            cat.nome AS categoria_nome, cat.icone AS categoria_icone
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

$membroDesde = mesPorExtenso(date('n', strtotime($usuario['criado_em'])))
             . ' de ' . date('Y', strtotime($usuario['criado_em']));

$tituloPagina = $usuario['nome'];
require_once __DIR__ . '/includes/header.php';
?>

    <section class="hero hero-categoria">
        <div class="container">

            <div class="perfil-flex">
                <div class="avatar">
                    <?php echo htmlspecialchars(iniciaisDoNome($usuario['nome'])); ?>
                </div>

                <div class="perfil-texto">
                    <h1 class="perfil-nome"><?php echo htmlspecialchars($usuario['nome']); ?></h1>
                    <p class="perfil-cargo">
                        <?php echo $emojiPerfil; ?> <?php echo htmlspecialchars($usuario['perfil']); ?>
                        &nbsp;·&nbsp; Membro desde <?php echo $membroDesde; ?>
                    </p>
                </div>
            </div>

            <div class="perfil-stats">
                <div class="stat-box">
                    <span class="stat-numero"><?php echo $totConteudos; ?></span>
                    <span class="stat-rotulo">Publicações</span>
                </div>
                <div class="stat-box">
                    <span class="stat-numero"><?php echo $totEventos; ?></span>
                    <span class="stat-rotulo">Eventos organizados</span>
                </div>
            </div>

        </div>
    </section>

    
    <section class="secao-publicacoes">
        <div class="container">
            <h2 class="secao-titulo">Publicações de <?php echo htmlspecialchars(explode(' ', trim($usuario['nome']))[0]); ?></h2>
            <p class="secao-subtitulo">Conteúdos publicados por este autor</p>

            <div class="grade">
                <?php foreach ($publicacoes as $p): ?>
                    <article class="card">

                        <span class="card-categoria">
                            <?php echo htmlspecialchars($p['categoria_icone'] . ' ' . $p['categoria_nome']); ?>
                        </span>

                        <?php if (!empty($p['imagem_capa'])): ?>
                            <img class="card-imagem"
                                 src="<?php echo htmlspecialchars($p['imagem_capa']); ?>"
                                 alt="<?php echo htmlspecialchars($p['titulo']); ?>">
                        <?php endif; ?>

                        <h3 class="card-titulo"><?php echo htmlspecialchars($p['titulo']); ?></h3>
                        <p class="card-resumo"><?php echo htmlspecialchars($p['resumo']); ?></p>

                        <div class="card-rodape">
                            <span class="card-meta">
                                 <?php echo date('d/m/Y', strtotime($p['publicado_em'])); ?>
                            </span>
                            <a class="card-link"
                               href="conteudo.php?slug=<?php echo urlencode($p['slug']); ?>">
                               Ler mais →
                            </a>
                        </div>

                    </article>
                <?php endforeach; ?>

                <?php if (empty($publicacoes)): ?>
                    <p class="grade-vazia">Este autor ainda não possui publicações.</p>
                <?php endif; ?>
            </div>

        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
