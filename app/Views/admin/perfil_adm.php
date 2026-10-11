<section class="panel">
    <div class="profile-summary">
        <div class="profile-avatar" aria-hidden="true"><?= h(mb_strtoupper(mb_substr($u['nome'], 0, 1))) ?></div>
        <div>
            <p class="profile-name"><?= h($u['nome']) ?></p>
            <p class="profile-email"><?= h($u['email']) ?></p>
            <span class="badge-role"><?= h($u['perfil']) ?></span>
        </div>
    </div>
    <div class="profile-meta">
        <div class="meta-box"><span>Curso</span><strong><?= h($u['curso'] ?: 'Não informado') ?></strong></div>
        <div class="meta-box"><span>Status da conta</span><strong><?= h(ucfirst($u['status'])) ?></strong></div>
        <div class="meta-box"><span>Conta criada em</span><strong><?= h(date('d/m/Y', strtotime($u['criado_em']))) ?></strong></div>
        <div class="meta-box"><span>Tipo de acesso</span><strong><?= $admin ? 'Administração do portal' : 'Acesso de consulta' ?></strong></div>
    </div>
</section>
<?php if ($admin): ?>
<section class="panel">
    <h2>Central de administração</h2>
    <p>Gerencie os cadastros e as publicações do Portal SI por estas áreas.</p>
    <div class="hub-grid">
        <a class="hub-card" href="usuarios.php"><strong>Usuários</strong><span>Cadastrar, editar, ativar e desativar contas.</span></a>
        <a class="hub-card" href="conteudos.php"><strong>Notícias e conteúdos</strong><span>Organizar publicações, categorias e imagens.</span></a>
        <a class="hub-card" href="eventos.php"><strong>Eventos</strong><span>Manter a agenda institucional atualizada.</span></a>
    </div>
</section>
<?php endif; ?>
