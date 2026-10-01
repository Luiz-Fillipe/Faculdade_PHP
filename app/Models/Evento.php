<?php
/**
 * EVENTOS — Portal SI
 * - evento.php        → agenda completa (próximos + encerrados)
 * - evento.php?id=X   → detalhe de um evento
 */

require_once __DIR__ . '/config/conexao.php';

// ---------- Funções de data (PHP puro) ----------
function mesAbreviado($n) {
    $meses = [1=>'Jan',2=>'Fev',3=>'Mar',4=>'Abr',5=>'Mai',6=>'Jun',
              7=>'Jul',8=>'Ago',9=>'Set',10=>'Out',11=>'Nov',12=>'Dez'];
    return $meses[(int)$n];
}

function dataCompleta($timestamp) {
    $meses = [1=>'janeiro',2=>'fevereiro',3=>'março',4=>'abril',5=>'maio',6=>'junho',
              7=>'julho',8=>'agosto',9=>'setembro',10=>'outubro',11=>'novembro',12=>'dezembro'];
    $t = strtotime($timestamp);
    return date('j', $t) . ' de ' . $meses[(int)date('n', $t)] . ' de ' . date('Y', $t)
         . ' às ' . date('H\hi', $t);
}

function resumoCurto($texto, $limite = 140) {
    $texto = trim($texto ?? '');
    if (mb_strlen($texto) <= $limite) {
        return $texto;
    }
    $corte  = mb_substr($texto, 0, $limite);
    $espaco = mb_strrpos($corte, ' ');
    return (($espaco !== false) ? mb_substr($corte, 0, $espaco) : $corte) . '…';
}

// ---------- ID da URL ----------
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* =====================================================
   MODO DETALHE — evento.php?id=X
   ===================================================== */
if ($id > 0) {

    $stmt = $pdo->prepare(
        "SELECT e.*, u.nome AS autor_nome
         FROM eventos e
         INNER JOIN usuarios u ON u.id = e.autor_id
         WHERE e.id = :id AND e.status = 'publicado'"
    );
    $stmt->execute([':id' => $id]);
    $evento = $stmt->fetch();

    // Evento não existe → volta para a agenda
    if (!$evento) {
        header('Location: evento.php');
        exit;
    }

    $tituloPagina = $evento['titulo'];
    require_once __DIR__ . '/includes/header.php';
?>

    <article class="detalhe">

        <span class="card-categoria">📅 Evento</span>

        <h1 class="detalhe-titulo"><?php echo htmlspecialchars($evento['titulo']); ?></h1>

        <div class="evento-meta">
            <span class="evento-meta-item">
                🚀 Início: <?php echo dataCompleta($evento['data_inicio']); ?>
            </span>

            <?php if (!empty($evento['data_fim'])): ?>
                <span class="evento-meta-item">
                    🏁 Término: <?php echo dataCompleta($evento['data_fim']); ?>
                </span>
            <?php endif; ?>

            <span class="evento-meta-item">
                📍 <?php echo htmlspecialchars($evento['local'] ?? 'Local a definir'); ?>
            </span>

            <span class="evento-meta-item">
                ✍️ <a class="autor-link"
                      href="usuario.php?id=<?php echo (int)$evento['autor_id']; ?>">
                    <?php echo htmlspecialchars($evento['autor_nome']); ?>
                </a>
            </span>
        </div>

        <?php if (!empty($evento['descricao'])): ?>
            <div class="detalhe-corpo">
                <?php echo nl2br(htmlspecialchars($evento['descricao'])); ?>
            </div>
        <?php endif; ?>

        <div class="evento-acoes">
            <?php if (!empty($evento['link_inscricao'])): ?>
                <a class="botao botao-inscricao"
                   href="<?php echo htmlspecialchars($evento['link_inscricao']); ?>"
                   target="_blank" rel="noopener">
                   Quero me inscrever →
                </a>
            <?php endif; ?>

            <a href="evento.php" class="voltar">← Voltar para a agenda</a>
        </div>

    </article>

<?php
    require_once __DIR__ . '/includes/footer.php';

/* =====================================================
   MODO LISTAGEM — evento.php (agenda completa)
   ===================================================== */
} else {

    // Próximos eventos (data futura, mais próximos primeiro)
    $proximos = $pdo->query(
        "SELECT e.*, u.nome AS autor_nome
         FROM eventos e
         INNER JOIN usuarios u ON u.id = e.autor_id
         WHERE e.status = 'publicado' AND e.data_inicio >= NOW()
         ORDER BY e.data_inicio ASC"
    )->fetchAll();

    // Últimos encerrados (mais recentes primeiro)
    $encerrados = $pdo->query(
        "SELECT e.*, u.nome AS autor_nome
         FROM eventos e
         INNER JOIN usuarios u ON u.id = e.autor_id
         WHERE e.status = 'publicado' AND e.data_inicio < NOW()
         ORDER BY e.data_inicio DESC
         LIMIT 6"
    )->fetchAll();

    $tituloPagina = 'Agenda de Eventos';
    require_once __DIR__ . '/includes/header.php';
?>

    <section class="hero hero-categoria">
        <div class="container">
            <span class="hero-tag">📅 Agenda</span>
            <h1 class="hero-titulo">Agenda de Eventos</h1>
            <p class="hero-resumo">Palestras, workshops e encontros da instituição</p>
        </div>
    </section>

    <section class="secao-publicacoes">
        <div class="container">
            <h2 class="secao-titulo">Próximos Eventos</h2>
            <p class="secao-subtitulo">Garanta sua participação</p>

            <div class="agenda">
                <?php foreach ($proximos as $ev): ?>
                    <article class="evento-card">

                        <div class="evento-data">
                            <span class="evento-dia"><?php echo date('d', strtotime($ev['data_inicio'])); ?></span>
                            <span class="evento-mes"><?php echo mesAbreviado(date('n', strtotime($ev['data_inicio']))); ?></span>
                        </div>

                        <div class="evento-card-info">
                            <h3><?php echo htmlspecialchars($ev['titulo']); ?></h3>
                            <p class="evento-card-desc">
                                <?php echo htmlspecialchars(resumoCurto($ev['descricao'])); ?>
                            </p>
                            <p class="card-meta">
                                📍 <?php echo htmlspecialchars($ev['local'] ?? 'Local a definir'); ?>
                                &nbsp;·&nbsp; 🕒 <?php echo date('H\hi', strtotime($ev['data_inicio'])); ?>
                            </p>
                        </div>

                        <a href="evento.php?id=<?php echo (int)$ev['id']; ?>"
                           class="botao-pequeno">Ver detalhes →</a>

                    </article>
                <?php endforeach; ?>

                <?php if (empty($proximos)): ?>
                    <p class="grade-vazia">Nenhum evento programado por enquanto.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if (!empty($encerrados)): ?>
    <section class="secao-eventos">
        <div class="container">
            <h2 class="secao-titulo">Eventos Encerrados</h2>
            <p class="secao-subtitulo">O que já aconteceu</p>

            <div class="agenda">
                <?php foreach ($encerrados as $ev): ?>
                    <article class="evento-card evento-card--encerrado">

                        <div class="evento-data">
                            <span class="evento-dia"><?php echo date('d', strtotime($ev['data_inicio'])); ?></span>
                            <span class="evento-mes"><?php echo mesAbreviado(date('n', strtotime($ev['data_inicio']))); ?></span>
                        </div>

                        <div class="evento-card-info">
                            <h3><?php echo htmlspecialchars($ev['titulo']); ?></h3>
                            <p class="evento-card-desc">
                                <?php echo htmlspecialchars(resumoCurto($ev['descricao'])); ?>
                            </p>
                            <p class="card-meta">
                                📍 <?php echo htmlspecialchars($ev['local'] ?? 'Local a definir'); ?>
                            </p>
                        </div>

                        <a href="evento.php?id=<?php echo (int)$ev['id']; ?>"
                           class="botao-pequeno">Ver detalhes →</a>

                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

<?php
    require_once __DIR__ . '/includes/footer.php';
}
?>
