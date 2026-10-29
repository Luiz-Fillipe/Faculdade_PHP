<?php
declare(strict_types=1);

// CRUD de categorias (Desafio 2 da atividade Aiven.io):
// cadastrar, listar, alterar e excluir. Fluxo: HTML -> PHP -> PDO -> PostgreSQL.
require_once __DIR__ . '/_init.php';
require_perfil(['Admin']);

function gerar_slug(string $texto): string
{
    $slug = (string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    $slug = strtolower(trim((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $slug), '-'));

    return $slug !== '' ? $slug : bin2hex(random_bytes(6));
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $acao = (string) ($_POST['acao'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    try {
        if ($acao === 'excluir') {
            $stmt = $pdo->prepare('DELETE FROM categorias WHERE id = :id');
            $stmt->execute([':id' => $id]);
            audit($pdo, 'excluiu categoria', 'categorias', $id);
            header('Location: categorias.php?ok=1');
            exit;
        }

        if ($acao === 'salvar') {
            $nome = trim((string) ($_POST['nome'] ?? ''));
            $slug = trim((string) ($_POST['slug'] ?? ''));
            $descricao = trim((string) ($_POST['descricao'] ?? ''));
            $icone = trim((string) ($_POST['icone'] ?? ''));
            $ordem = (int) ($_POST['ordem'] ?? 0);
            $ativo = isset($_POST['ativo']) ? 'TRUE' : 'FALSE';

            if ($nome === '' || mb_strlen($nome) > 100) {
                throw new RuntimeException('Informe o nome da categoria (até 100 caracteres).');
            }

            $slug = gerar_slug($slug !== '' ? $slug : $nome);
            $valores = [
                ':nome' => $nome,
                ':slug' => $slug,
                ':descricao' => $descricao !== '' ? $descricao : null,
                ':icone' => $icone !== '' ? $icone : null,
                ':ordem' => $ordem,
            ];

            if ($id > 0) {
                $stmt = $pdo->prepare(
                    "UPDATE categorias
                     SET nome = :nome, slug = :slug, descricao = :descricao,
                         icone = :icone, ordem = :ordem, ativo = {$ativo}
                     WHERE id = :id"
                );
                $stmt->execute($valores + [':id' => $id]);
                audit($pdo, 'editou categoria', 'categorias', $id);
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO categorias (nome, slug, descricao, icone, ordem, ativo)
                     VALUES (:nome, :slug, :descricao, :icone, :ordem, {$ativo})
                     RETURNING id"
                );
                $stmt->execute($valores);
                audit($pdo, 'criou categoria', 'categorias', (int) $stmt->fetchColumn());
            }

            header('Location: categorias.php?ok=1');
            exit;
        }
    } catch (PDOException $e) {
        // PDOException herda de RuntimeException: precisa vir primeiro para não expor a mensagem do banco.
        error_log('Erro no CRUD de categorias: ' . $e->getMessage());
        $erro = match ($e->getCode()) {
            '23505' => 'Já existe uma categoria com esse endereço (slug).',
            '23001', '23503' => 'Não é possível excluir: existem publicações nesta categoria.',
            default => 'Não foi possível concluir a operação. Confira os dados e tente novamente.',
        };
    } catch (RuntimeException $e) {
        $erro = $e->getMessage();
    }
}

$edit = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM categorias WHERE id = :id');
    $stmt->execute([':id' => (int) $_GET['editar']]);
    $edit = $stmt->fetch() ?: null;
}

$items = $pdo->query('SELECT id, nome, slug, ordem, ativo FROM categorias ORDER BY ordem, nome')->fetchAll();

admin_header('Gerenciar categorias');
if ($erro !== ''): ?>
    <div class="alert error" role="alert"><?= h($erro) ?></div>
<?php endif; ?>

<section class="panel">
    <h2><?= $edit ? 'Editar categoria' : 'Nova categoria' ?></h2>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="acao" value="salvar">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">

        <label>Nome
            <input name="nome" maxlength="100" required value="<?= h($edit['nome'] ?? '') ?>">
        </label>
        <label>Slug (endereço; se vazio, é gerado a partir do nome)
            <input name="slug" maxlength="100" value="<?= h($edit['slug'] ?? '') ?>">
        </label>
        <label>Descrição
            <textarea name="descricao" rows="3"><?= h($edit['descricao'] ?? '') ?></textarea>
        </label>
        <label>Ícone (emoji)
            <input name="icone" maxlength="100" value="<?= h($edit['icone'] ?? '') ?>">
        </label>
        <label>Ordem no menu
            <input type="number" name="ordem" value="<?= (int) ($edit['ordem'] ?? 0) ?>">
        </label>
        <label>
            <input type="checkbox" name="ativo" value="1" style="display:inline;width:auto"
                <?= ($edit === null || !empty($edit['ativo'])) ? 'checked' : '' ?>>
            Categoria ativa
        </label>

        <button type="submit">Salvar categoria</button>
        <?php if ($edit): ?>
            <a class="button secondary" href="categorias.php">Cancelar</a>
        <?php endif; ?>
    </form>
</section>

<section class="panel">
    <h2>Categorias cadastradas</h2>
    <?php foreach ($items as $it): ?>
        <article class="item">
            <div>
                <h3><?= h($it['nome']) ?></h3>
                <p><?= h($it['slug']) ?> · ordem <?= (int) $it['ordem'] ?> · <?= $it['ativo'] ? 'ativa' : 'inativa' ?></p>
            </div>
            <div class="actions">
                <a class="button secondary" href="categorias.php?editar=<?= (int) $it['id'] ?>">Editar</a>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="id" value="<?= (int) $it['id'] ?>">
                    <button class="danger" type="submit">Excluir</button>
                </form>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if ($items === []): ?>
        <p>Nenhuma categoria cadastrada.</p>
    <?php endif; ?>
</section>
<?php admin_footer();
