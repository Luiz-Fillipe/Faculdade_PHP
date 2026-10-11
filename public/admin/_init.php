<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
$perfil = $_SESSION['usuario_perfil'] ?? '';
$admin = $perfil === 'Admin';
function admin_header(string $title): void { global $admin; ?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= h($title) ?> — Portal SI</title><link rel="stylesheet" href="../assets/css/admin.css"></head><body><header class="top"><a class="brand" href="perfil.php">Portal SI</a><nav><a href="perfil.php">Meu perfil</a><?php if ($admin): ?><a href="usuarios.php">Usuários</a><a href="conteudos.php">Notícias</a><a href="eventos.php">Eventos</a><?php endif; ?><a href="../index.php">Site público</a><form method="post" action="logout.php" class="inline"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><button class="linkbtn" type="submit">Sair</button></form></nav></header><main class="wrap"><h1><?= h($title) ?></h1><?php if (isset($_GET['ok'])): ?><div class="alert">Alterações salvas.</div><?php endif; ?><?php }
function admin_footer(): void { ?></main></body></html><?php }
function audit(PDO $pdo, string $acao, string $tabela='', ?int $id=null): void { $s=$pdo->prepare('INSERT INTO auditoria(usuario_id,acao,tabela_afetada,registro_id,ip_origem) VALUES(:u,:a,:t,:r,:ip)'); $s->execute([':u'=>$_SESSION['usuario_id'] ?? null,':a'=>$acao,':t'=>$tabela,':r'=>$id,':ip'=>$_SERVER['REMOTE_ADDR'] ?? null]); }
function upload_image(string $field): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f=$_FILES[$field]; if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 5*1024*1024) throw new RuntimeException('A imagem deve ter até 5 MB.');
    $fi=new finfo(FILEINFO_MIME_TYPE); $mime=$fi->file($f['tmp_name']); $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'][$mime] ?? null;
    if (!$ext || @getimagesize($f['tmp_name']) === false) throw new RuntimeException('Formato de imagem inválido. Use JPG, PNG, WEBP ou GIF.');
    $dir=__DIR__.'/../uploads'; if (!is_dir($dir) && !mkdir($dir,0755,true)) throw new RuntimeException('Não foi possível preparar a pasta de imagens.');
    $name=bin2hex(random_bytes(16)).'.'.$ext; if (!move_uploaded_file($f['tmp_name'],$dir.'/'.$name)) throw new RuntimeException('Não foi possível salvar a imagem.');
    return 'uploads/'.$name;
}
