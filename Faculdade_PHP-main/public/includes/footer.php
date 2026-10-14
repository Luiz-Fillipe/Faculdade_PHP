
</main>
<?php $basePublica = str_contains(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''), '/admin/') ? '../' : ''; ?>

<footer class="rodape">
    <div class="rodape-superior">
        <div class="container rodape-superior-grid">
            <div class="rodape-item">
                <a href="<?php echo $basePublica; ?>index.php" class="rodape-item-titulo">Portal SI</a>
                <p>Informação institucional, notícias e serviços em um só lugar.</p>
            </div>
            <div class="rodape-item">
                <a href="<?php echo $basePublica; ?>index.php" class="rodape-item-titulo">Publicações</a>
                <p>Acompanhe notícias, comunicados e conteúdos da instituição.</p>
            </div>
            <div class="rodape-item">
                <a href="<?php echo $basePublica; ?>evento.php" class="rodape-item-titulo">Agenda</a>
                <p>Confira os próximos eventos, atividades e encontros.</p>
            </div>
            <div class="rodape-item">
                <a href="mailto:contato@portalsi.edu.br" class="rodape-item-titulo">Contato</a>
                <p>Envie dúvidas, sugestões ou solicite informações.</p>
            </div>
        </div>
    </div>

    <div class="rodape-inferior">
        <div class="container rodape-inferior-grid">
            <div class="rodape-endereco">
                <strong>Portal SI</strong>
                <p>Av. Principal, 1000</p>
                <p>(00) 0000-0000</p>
                <a href="mailto:contato@portalsi.edu.br">contato@portalsi.edu.br</a>
            </div>

            <div class="rodape-marca">
                <a href="<?php echo $basePublica; ?>index.php" class="rodape-logo">
                    <span class="rodape-logo-sigla">SI</span>
                    <span class="rodape-logo-texto">
                        <strong>Portal</strong>
                        <b>SI</b>
                    </span>
                </a>
                <p>© <?php echo date('Y'); ?> Portal SI</p>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
