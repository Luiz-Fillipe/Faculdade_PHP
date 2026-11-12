<?php

require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/funcoes.php';

$tituloPagina = 'Contato';
require_once __DIR__ . '/includes/header.php';

$email = htmlspecialchars(CONTATO_EMAIL, ENT_QUOTES, 'UTF-8');
?>

<section class="ed-corpo">
    <div class="container">
        <header class="ed-cabecalho-secao">
            <p class="ed-kicker">Fale com a gente</p>
            <h1>Contato</h1>
            <p class="ed-cabecalho-descricao">
                O Portal SI é o canal de comunicação do curso de Sistemas de Informação.
                Escreva para a equipe do portal.
            </p>
        </header>

        <div class="ed-contato">
            <section>
                <h2>E-mail</h2>
                <p><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
                <p class="ed-contato-nota">Informe o assunto na mensagem: dúvida, sugestão de pauta ou correção.</p>
            </section>

            <section>
                <h2>Sugerir ou corrigir uma publicação</h2>
                <p>Envie o título da publicação e o que deve mudar. A equipe analisa e atualiza pelo painel.</p>
            </section>

            <section>
                <h2>Quem faz o portal</h2>
                <p>Grupo Power, do Projeto de Extensão IV do curso de Sistemas de Informação, com orientação do professor Jucemar Formigoni Cândido.</p>
            </section>

            <section>
                <h2>Continue navegando</h2>
                <ul>
                    <li><a href="index.php">Página inicial</a></li>
                    <li><a href="evento.php">Agenda de eventos</a></li>
                    <li><a href="login.php">Área restrita</a></li>
                </ul>
            </section>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
