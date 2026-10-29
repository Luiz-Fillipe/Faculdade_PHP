
</main>
<?php $basePublica = str_contains(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''), '/admin/') ? '../' : ''; ?>

<footer class="rodape">
    <div class="container rodape-colunas">
        <div class="rodape-coluna rodape-sobre">
            <a href="<?php echo $basePublica; ?>index.php" class="marca" aria-label="Portal SI — página inicial">
                <span class="marca-nome">Portal</span>
                <span class="marca-sigla">SI</span>
            </a>
            <p>Informação institucional, notícias, comunicados e agenda do curso de Sistemas de Informação em um só lugar.</p>
        </div>

        <div class="rodape-coluna">
            <h2>Editorias</h2>
            <ul>
                <?php foreach (($categorias ?? []) as $cat): ?>
                    <li><a href="<?php echo $basePublica; ?>categoria.php?slug=<?php echo urlencode($cat['slug']); ?>"><?php echo htmlspecialchars($cat['nome']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="rodape-coluna">
            <h2>Portal</h2>
            <ul>
                <li><a href="<?php echo $basePublica; ?>index.php">Início</a></li>
                <li><a href="<?php echo $basePublica; ?>evento.php">Agenda de eventos</a></li>
                <li><a href="<?php echo $basePublica; ?>login.php">Área restrita</a></li>
            </ul>
        </div>

        <div class="rodape-coluna">
            <h2>Contato</h2>
            <address>
                Av. Principal, 1000<br>
                (00) 0000-0000<br>
                <a href="mailto:contato@portalsi.edu.br">contato@portalsi.edu.br</a>
            </address>
        </div>
    </div>

    <div class="rodape-base">
        <div class="container">
            <span>© <?php echo date('Y'); ?> Portal SI</span>
            <span>Projeto de Extensão IV · Sistemas de Informação</span>
        </div>
    </div>
</footer>

</body>
</html>
