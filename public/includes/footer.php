
<?php
/**
 * FOOTER REUTILIZÁVEL — Portal SI
 */
?>
</main>

<footer class="rodape">
    <div class="container rodape-flex">

        <div class="rodape-bloco">
            <h3 class="rodape-titulo">Portal SI</h3>
            <p>Sistema de Informações Institucional.<br>
               Transparência e acesso à informação.</p>
        </div>

        <div class="rodape-bloco">
            <h4 class="rodape-subtitulo">Categorias</h4>
            <ul class="rodape-lista">
                <?php foreach ($categorias as $cat): ?>
                    <li>
                        <a href="categoria.php?slug=<?php echo urlencode($cat['slug']); ?>">
                            <?php echo htmlspecialchars($cat['icone'] . ' ' . $cat['nome']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="rodape-bloco">
            <h4 class="rodape-subtitulo">Contato</h4>
            <ul class="rodape-lista">
                <li>📧 contato@portalsi.edu.br</li>
                <li>📞 (00) 0000-0000</li>
                <li>📍 Av. Principal, 1000</li>
            </ul>
        </div>

    </div>

    <div class="rodape-copy">
        <p>&copy; <?php echo date('Y'); ?> Portal SI — Todos os direitos reservados</p>
    </div>
</footer>

</body>
</html>
