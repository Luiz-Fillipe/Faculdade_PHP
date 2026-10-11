<?php if ($erros): ?>
    <div class="alert error" role="alert">
        <ul><?php foreach ($erros as $erro): ?><li><?= h($erro) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>
<section class="panel">
    <p>Crie uma conta para um administrador, professor ou aluno. Somente administradores podem cadastrar usuários.</p>
    <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <label for="nome">Nome completo<input id="nome" name="nome" value="<?= h($nome) ?>" maxlength="150" autocomplete="name" required></label>
        <label for="email">E-mail<input id="email" type="email" name="email" value="<?= h($email) ?>" maxlength="254" autocomplete="email" required></label>
        <label for="perfil">Perfil<select id="perfil" name="perfil" required><option value="Aluno" <?= $perfilNovo === 'Aluno' ? 'selected' : '' ?>>Aluno</option><option value="Professor" <?= $perfilNovo === 'Professor' ? 'selected' : '' ?>>Professor</option><option value="Admin" <?= $perfilNovo === 'Admin' ? 'selected' : '' ?>>Administrador</option></select></label>
        <label for="curso">Curso<input id="curso" name="curso" value="<?= h($curso) ?>" maxlength="150"></label>
        <label for="senha">Senha inicial (mínimo de 12 caracteres)<input id="senha" type="password" name="senha" minlength="12" autocomplete="new-password" required></label>
        <label for="senha_confirmacao">Confirmar senha<input id="senha_confirmacao" type="password" name="senha_confirmacao" minlength="12" autocomplete="new-password" required></label>
        <div class="actions"><button type="submit">Cadastrar usuário</button><a class="button secondary" href="usuarios.php">Cancelar</a></div>
    </form>
</section>
