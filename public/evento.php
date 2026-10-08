<?php

require_once __DIR__ . '/config/conexao.php';

function mesAbreviado($numero)
{
    $meses = [
        1 => 'Jan',
        2 => 'Fev',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'Mai',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Set',
        10 => 'Out',
        11 => 'Nov',
        12 => 'Dez'
    ];

    return $meses[(int) $numero] ?? '';
}

function dataCompleta($timestamp)
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

    $data = strtotime($timestamp);

    return date('j', $data)
        . ' de '
        . $meses[(int) date('n', $data)]
        . ' de '
        . date('Y', $data)
        . ' às '
        . date('H:i', $data);
}

function resumoCurto($texto, $limite = 170)
{
    $texto = trim($texto ?? '');

    if (mb_strlen($texto) <= $limite) {
        return $texto;
    }

    $corte = mb_substr($texto, 0, $limite);
    $ultimoEspaco = mb_strrpos($corte, ' ');

    if ($ultimoEspaco !== false) {
        $corte = mb_substr($corte, 0, $ultimoEspaco);
    }

    return $corte . '…';
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare(
        "SELECT e.*, u.nome AS autor_nome
         FROM eventos e
         INNER JOIN usuarios u ON u.id = e.autor_id
         WHERE e.id = :id
           AND e.status = 'publicado'"
    );

    $stmt->execute([':id' => $id]);
    $evento = $stmt->fetch();

    if (!$evento) {
        header('Location: evento.php');
        exit;
    }

    $tituloPagina = $evento['titulo'];

    require_once __DIR__ . '/includes/header.php';

    $dataInicio = strtotime($evento['data_inicio']);
    ?>

    <article class="evento-detalhe">

        <header class="evento-detalhe-cabecalho">
            <div class="container">

                <a href="evento.php" class="evento-voltar">
                    Agenda de eventos
                </a>

                <div class="evento-detalhe-apresentacao">

                    <time
                        class="evento-detalhe-data"
                        datetime="<?php echo date('Y-m-d\TH:i', $dataInicio); ?>"
                    >
                        <span><?php echo mesAbreviado(date('n', $dataInicio)); ?></span>
                        <strong><?php echo date('d', $dataInicio); ?></strong>
                        <small><?php echo date('Y', $dataInicio); ?></small>
                    </time>

                    <div class="evento-detalhe-titulo">
                        <span class="evento-detalhe-chapeu">
                            Agenda institucional
                        </span>

                        <h1>
                            <?php echo htmlspecialchars($evento['titulo']); ?>
                        </h1>

                        <p>
                            <?php echo dataCompleta($evento['data_inicio']); ?>
                        </p>
                    </div>

                </div>

            </div>
        </header>

        <div class="container evento-detalhe-grid">

            <section class="evento-detalhe-conteudo">
                <h2>Sobre o evento</h2>

                <?php if (!empty($evento['descricao'])): ?>
                    <div class="evento-detalhe-texto">
                        <?php echo nl2br(htmlspecialchars($evento['descricao'])); ?>
                    </div>
                <?php else: ?>
                    <p class="evento-sem-descricao">
                        Mais informações sobre este evento serão divulgadas em breve.
                    </p>
                <?php endif; ?>
            </section>

            <aside class="evento-detalhe-informacoes">
                <h2>Informações</h2>

                <dl>
                    <div>
                        <dt>Início</dt>
                        <dd><?php echo dataCompleta($evento['data_inicio']); ?></dd>
                    </div>

                    <?php if (!empty($evento['data_fim'])): ?>
                        <div>
                            <dt>Término</dt>
                            <dd><?php echo dataCompleta($evento['data_fim']); ?></dd>
                        </div>
                    <?php endif; ?>

                    <div>
                        <dt>Local</dt>
                        <dd>
                            <?php echo htmlspecialchars($evento['local'] ?: 'Local a definir'); ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Responsável</dt>
                        <dd>
                            <a href="usuario.php?id=<?php echo (int) $evento['autor_id']; ?>">
                                <?php echo htmlspecialchars($evento['autor_nome']); ?>
                            </a>
                        </dd>
                    </div>
                </dl>

                <?php if (!empty($evento['link_inscricao'])): ?>
                    <a
                        href="<?php echo htmlspecialchars($evento['link_inscricao']); ?>"
                        class="evento-inscricao"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Fazer inscrição
                    </a>
                <?php endif; ?>
            </aside>

        </div>

    </article>

    <?php

    require_once __DIR__ . '/includes/footer.php';
} else {
    $proximos = $pdo->query(
        "SELECT e.*, u.nome AS autor_nome
         FROM eventos e
         INNER JOIN usuarios u ON u.id = e.autor_id
         WHERE e.status = 'publicado'
           AND e.data_inicio >= NOW()
         ORDER BY e.data_inicio ASC"
    )->fetchAll();

    $encerrados = $pdo->query(
        "SELECT e.*, u.nome AS autor_nome
         FROM eventos e
         INNER JOIN usuarios u ON u.id = e.autor_id
         WHERE e.status = 'publicado'
           AND e.data_inicio < NOW()
         ORDER BY e.data_inicio DESC
         LIMIT 6"
    )->fetchAll();

    $tituloPagina = 'Agenda de Eventos';

    require_once __DIR__ . '/includes/header.php';

    ?>

    <header class="agenda-cabecalho">
        <div class="container">
            <span class="agenda-chapeu">Portal SI</span>
            <h1>Agenda de eventos</h1>
            <p>
                Palestras, encontros, oficinas e atividades abertas à comunidade.
            </p>
        </div>
    </header>

    <main class="agenda-pagina">

        <section class="agenda-secao">
            <div class="container">

                <div class="agenda-secao-cabecalho">
                    <div>
                        <span>Programação</span>
                        <h2>Próximos eventos</h2>
                    </div>

                    <p>
                        <?php echo count($proximos); ?>
                        <?php echo count($proximos) === 1 ? 'evento programado' : 'eventos programados'; ?>
                    </p>
                </div>

                <?php if (!empty($proximos)): ?>
                    <div class="agenda-lista">

                        <?php foreach ($proximos as $evento): ?>
                            <?php $dataEvento = strtotime($evento['data_inicio']); ?>

                            <article class="agenda-item">

                                <time
                                    class="agenda-item-data"
                                    datetime="<?php echo date('Y-m-d\TH:i', $dataEvento); ?>"
                                >
                                    <strong><?php echo date('d', $dataEvento); ?></strong>
                                    <span><?php echo mesAbreviado(date('n', $dataEvento)); ?></span>
                                    <small><?php echo date('Y', $dataEvento); ?></small>
                                </time>

                                <div class="agenda-item-conteudo">
                                    <div class="agenda-item-status">
                                        Próximo evento
                                    </div>

                                    <h3>
                                        <a href="evento.php?id=<?php echo (int) $evento['id']; ?>">
                                            <?php echo htmlspecialchars($evento['titulo']); ?>
                                        </a>
                                    </h3>

                                    <?php if (!empty($evento['descricao'])): ?>
                                        <p class="agenda-item-resumo">
                                            <?php echo htmlspecialchars(resumoCurto($evento['descricao'])); ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="agenda-item-meta">
                                        <span>
                                            <?php echo date('H:i', $dataEvento); ?>
                                        </span>

                                        <span>
                                            <?php echo htmlspecialchars($evento['local'] ?: 'Local a definir'); ?>
                                        </span>

                                        <a href="usuario.php?id=<?php echo (int) $evento['autor_id']; ?>">
                                            <?php echo htmlspecialchars($evento['autor_nome']); ?>
                                        </a>
                                    </div>
                                </div>

                                <a
                                    href="evento.php?id=<?php echo (int) $evento['id']; ?>"
                                    class="agenda-item-link"
                                >
                                    Ver detalhes
                                </a>

                            </article>
                        <?php endforeach; ?>

                    </div>
                <?php else: ?>
                    <div class="agenda-vazia">
                        <h3>Nenhum evento programado</h3>
                        <p>Novas atividades serão divulgadas em breve.</p>
                    </div>
                <?php endif; ?>

            </div>
        </section>

        <?php if (!empty($encerrados)): ?>
            <section class="agenda-secao agenda-secao-encerrados">
                <div class="container">

                    <div class="agenda-secao-cabecalho">
                        <div>
                            <span>Arquivo</span>
                            <h2>Eventos encerrados</h2>
                        </div>
                    </div>

                    <div class="agenda-lista">

                        <?php foreach ($encerrados as $evento): ?>
                            <?php $dataEvento = strtotime($evento['data_inicio']); ?>

                            <article class="agenda-item agenda-item-encerrado">

                                <time
                                    class="agenda-item-data"
                                    datetime="<?php echo date('Y-m-d\TH:i', $dataEvento); ?>"
                                >
                                    <strong><?php echo date('d', $dataEvento); ?></strong>
                                    <span><?php echo mesAbreviado(date('n', $dataEvento)); ?></span>
                                    <small><?php echo date('Y', $dataEvento); ?></small>
                                </time>

                                <div class="agenda-item-conteudo">
                                    <div class="agenda-item-status">
                                        Encerrado
                                    </div>

                                    <h3>
                                        <a href="evento.php?id=<?php echo (int) $evento['id']; ?>">
                                            <?php echo htmlspecialchars($evento['titulo']); ?>
                                        </a>
                                    </h3>

                                    <?php if (!empty($evento['descricao'])): ?>
                                        <p class="agenda-item-resumo">
                                            <?php echo htmlspecialchars(resumoCurto($evento['descricao'])); ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="agenda-item-meta">
                                        <span>
                                            <?php echo htmlspecialchars($evento['local'] ?: 'Local não informado'); ?>
                                        </span>

                                        <a href="usuario.php?id=<?php echo (int) $evento['autor_id']; ?>">
                                            <?php echo htmlspecialchars($evento['autor_nome']); ?>
                                        </a>
                                    </div>
                                </div>

                                <a
                                    href="evento.php?id=<?php echo (int) $evento['id']; ?>"
                                    class="agenda-item-link"
                                >
                                    Consultar
                                </a>

                            </article>
                        <?php endforeach; ?>

                    </div>

                </div>
            </section>
        <?php endif; ?>

    </main>

    <?php

    require_once __DIR__ . '/includes/footer.php';
}
?>