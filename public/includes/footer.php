
</main>

<footer class="rodape">
    <div class="rodape-superior">
        <div class="container rodape-superior-grid">
            <div class="rodape-item">
                <a href="index.php" class="rodape-item-titulo">Portal SI</a>
                <p>Informação institucional, notícias e serviços em um só lugar.</p>
            </div>
            <div class="rodape-item">
                <a href="index.php" class="rodape-item-titulo">Publicações</a>
                <p>Acompanhe notícias, comunicados e conteúdos da instituição.</p>
            </div>
            <div class="rodape-item">
                <a href="evento.php" class="rodape-item-titulo">Agenda</a>
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

            <nav class="rodape-categorias" aria-label="Categorias">
                <h2>Categorias</h2>
                <ul>
                <?php foreach ($categorias as $cat): ?>
                    <li>
                        <a href="categoria.php?slug=<?php echo urlencode($cat['slug']); ?>">
                            <?php echo htmlspecialchars($cat['nome']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </nav>

            <div class="rodape-marca">
                <a href="index.php" class="rodape-logo">
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
