# Portal SI

Portal institucional em PHP e PostgreSQL. Os arquivos existentes foram mantidos em seus diretórios; a área administrativa foi acrescentada em `public/admin/`.

## Requisitos

- PHP 8.1 ou superior com extensões `pdo_pgsql`, `fileinfo` e `mbstring` habilitadas.
- PostgreSQL 14 ou superior.
- Apache/XAMPP configurado para servir a pasta `public/` como raiz web, ou acesso local por `http://localhost/portal_si/public/`.

## Instalação de teste

1. Faça uma cópia de segurança do banco antigo antes de qualquer exclusão.
2. No pgAdmin, crie um banco chamado `portal_si` (ou escolha outro e ajuste `PORTAL_DB_NAME`).
3. Abra `database/schema.sql` no Query Tool do banco novo e execute-o. **Esse script apaga e recria as tabelas existentes do banco selecionado. Confira o banco selecionado antes de executar.**
4. Execute `database/seed.sql` para inserir as categorias de exemplo.
5. Configure a conexão em `config/database.php` por variáveis de ambiente `PORTAL_DB_HOST`, `PORTAL_DB_NAME`, `PORTAL_DB_USER`, `PORTAL_DB_PASSWORD` e `PORTAL_DB_PORT`, ou ajuste os valores locais.
6. Execute uma única vez `database/criar_admin.php` pelo terminal: `php database/criar_admin.php`. O usuário de teste é `admin@portalsi.local`, senha inicial `TroqueEstaSenha!2026`. Troque a senha ao entrar e remova `database/criar_admin.php` da pasta pública do projeto/servidor após criar a conta. Não use essa senha em produção.
7. Abra `http://localhost/portal_si/public/` para a página inicial e `http://localhost/portal_si/public/login.php` para entrar.

## Perfis

- **Admin:** edita seu próprio perfil e gerencia contas, notícias e eventos. É o único com permissões administrativas.
- **Professor:** consulta e edita os próprios dados de perfil; não cria, altera ou exclui publicações/eventos.
- **Aluno:** consulta seu perfil e conteúdos/eventos associados ao curso informado no cadastro.
- Visitantes veem o site público sem autenticação.

## Segurança incluída

Senhas são armazenadas com `password_hash()` e verificadas com `password_verify()`; sessões usam cookies `HttpOnly`, `SameSite`, modo estrito e regeneração do identificador após autenticação; ações de formulário autenticadas usam token CSRF; consultas usam parâmetros PDO; autorização é conferida no servidor; mensagens de erro de banco não são exibidas ao visitante; uploads aceitam somente formatos de imagem reconhecidos e limitam tamanho.

Nenhum sistema pode ser garantido como livre de todas as falhas apenas por uma revisão do código. Antes de publicar na internet, configure HTTPS, credenciais próprias e fora do código, permissões mínimas no PostgreSQL, backups, atualização de PHP/Apache/PostgreSQL, limite de tentativas de login, política de recuperação de senha e revisão de segurança independente.
