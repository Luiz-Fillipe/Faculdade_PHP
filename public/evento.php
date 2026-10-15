<?php

require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/funcoes.php';
require_once __DIR__ . '/../app/Controller/EventoController.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$eventoController = new EventoController($pdo);

if ($id > 0) {
    $evento = $eventoController->detalhe($id);

    if (!$evento) {
        header('Location: evento.php?erro=evento');
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
                        <span><?php echo mesPorExtenso(date('n', $dataInicio)); ?></span>
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

        <?php if (!empty($evento['imagem'])): ?>
            <div class="container evento-detalhe-imagem-container">
                <img class="evento-detalhe-imagem" src="<?php echo htmlspecialchars($evento['imagem'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        <?php endif; ?>

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

            <aside class="evento-detalhe-informacoes card">
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

                <?php $linkInscricao = linkValido($evento['link_inscricao'] ?? null); ?>
                <?php if ($linkInscricao): ?>
                    <a
                        href="<?php echo htmlspecialchars($linkInscricao); ?>"
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
    $proximos = $eventoController->proximos();
    $encerrados = $eventoController->encerrados();

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
            <?php $mensagemErro = mensagemDeErro($_GET['erro'] ?? ''); ?>
            <?php if ($mensagemErro !== ''): ?>
                <p class="alerta-erro"><?php echo htmlspecialchars($mensagemErro); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <div class="agenda-pagina">

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

                            <article class="agenda-item card">

                                <time
                                    class="agenda-item-data"
                                    datetime="<?php echo date('Y-m-d\TH:i', $dataEvento); ?>"
                                >
                                    <strong><?php echo date('d', $dataEvento); ?></strong>
                                    <span><?php echo mesPorExtenso(date('n', $dataEvento)); ?></span>
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

                            <article class="agenda-item agenda-item-encerrado card">

                                <time
                                    class="agenda-item-data"
                                    datetime="<?php echo date('Y-m-d\TH:i', $dataEvento); ?>"
                                >
                                    <strong><?php echo date('d', $dataEvento); ?></strong>
                                    <span><?php echo mesPorExtenso(date('n', $dataEvento)); ?></span>
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

    </div>

    <?php

    require_once __DIR__ . '/includes/footer.php';
}
?>
