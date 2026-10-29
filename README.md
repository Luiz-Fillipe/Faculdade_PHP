# Portal SI

Portal institucional em PHP e PostgreSQL. Os arquivos existentes foram mantidos em seus diretórios; a área administrativa foi acrescentada em `public/admin/`.

## Requisitos

- PHP 8.1 ou superior com extensões `pdo_pgsql`, `fileinfo` e `mbstring` habilitadas.
- PostgreSQL 14 ou superior.
- Apache/XAMPP configurado para servir a pasta `public/` como raiz web. Na organização atual do XAMPP, use `http://localhost/PE4/portal-si/public/`.

## Instalação de teste

1. Faça uma cópia de segurança do banco antigo antes de qualquer exclusão.
2. No pgAdmin, crie um banco chamado `portal_si` (ou escolha outro e ajuste `PORTAL_DB_NAME`).
3. Abra `database/schema.sql` no Query Tool do banco novo e execute-o. **Esse script apaga e recria as tabelas existentes do banco selecionado. Confira o banco selecionado antes de executar.**
4. Execute `database/seed.sql` para inserir as categorias de exemplo.
5. Configure a conexão em `config/database.php` por variáveis de ambiente `PORTAL_DB_HOST`, `PORTAL_DB_NAME`, `PORTAL_DB_USER`, `PORTAL_DB_PASSWORD` e `PORTAL_DB_PORT`, ou ajuste os valores locais.
6. Execute uma única vez `database/criar_admin.php` pelo terminal: `php database/criar_admin.php`. O usuário de teste é `admin@portalsi.local`, senha inicial `TroqueEstaSenha!2026`. Troque a senha ao entrar e remova `database/criar_admin.php` da pasta pública do projeto/servidor após criar a conta. Não use essa senha em produção.
7. Salve o projeto no XAMPP com o nome de pasta `portal-si`.
8. Abra `http://localhost/PE4/portal-si/public/` para a página inicial e `http://localhost/PE4/portal-si/public/login.php` para entrar. Se a pasta `portal-si` estiver diretamente em `htdocs`, retire `/PE4` dessas URLs.

## Banco PostgreSQL no Aiven.io

O PHP roda localmente (XAMPP) e o PostgreSQL fica hospedado no Aiven (plano Free), conforme a apostila da Aula 11 de Projeto de Extensão IV.

1. No Aiven, crie o serviço PostgreSQL Free e o banco do grupo (ex.: `portal_grupo01`).
2. Copie `.env.example` para `.env` na raiz do projeto e preencha host, porta, banco, usuário e senha do serviço. O `.env` está no `.gitignore`: **nunca** publique a senha nem o Service URI.
3. Mantenha `PORTAL_DB_SSLMODE=require`: o DSN passa a usar `sslmode=require` e a conexão é criptografada. Para banco local, deixe a variável vazia.
4. Execute no banco do Aiven, nesta ordem: `database/schema.sql`; `php database/criar_admin.php` (o seed usa esse usuário como autor); `database/seed.sql`; e `database/teste_conexao.sql` (tabela `teste` da atividade).
5. Abra `public/teste-conexao.php` no navegador: ela mostra o status, o banco, a versão do PostgreSQL e os registros da tabela `teste`. A senha nunca é exibida. Ajuste `GRUPO_NOME` no topo do arquivo.
6. O CRUD de categorias (cadastrar, listar, alterar e excluir) fica em `public/admin/categorias.php`, acessível pelo menu do painel administrativo.

O plano Free do Aiven desliga o serviço sem uso e permite no máximo 20 conexões simultâneas. Se a conexão falhar, ligue o serviço novamente no console.

## Exibição dinâmica (Etapa 2)

Tudo que o visitante vê vem do PostgreSQL a cada requisição; publicar uma notícia no painel não exige mexer no código.

| Item da Aula 12 | Onde está |
| --- | --- |
| Consultas com `INNER JOIN` (conteúdos, categorias, usuários) | `app/Controller/ConteudoController.php`, `HomeController.php` |
| `LEFT JOIN` com contagem por categoria (inclui categorias vazias) | `ConteudoController::categoriasComTotal()`, chips da Home |
| Filtro por categoria por query string | `public/categoria.php?slug=...` |
| Paginação (`COUNT(*)`, `LIMIT`/`OFFSET` com `PDO::PARAM_INT`, 6 por página) | `ConteudoController::porCategoriaSlug()` e `calcularPaginacao()` |
| Detalhe pelo identificador | `public/conteudo.php?slug=...` ou `?id=...` |
| Vídeo do YouTube em 16:9 | `.publicacao-video` (`aspect-ratio`) em `public/css/style.css` |
| Identificadores validados com `filter_input` e `404` amigável | `paginaNaoEncontrada()` em `public/funcoes.php` |
| Estado vazio, datas `dd/mm/aaaa`, `alt` nas imagens | páginas públicas |
| Erro do PDO nunca na tela (vai para `error_log`) | `catch` das páginas públicas e do painel |

## Interface e Bootstrap

- O portal utiliza Bootstrap 5.3.3 pelo CDN oficial.
- Somente o CSS do Bootstrap é carregado; o projeto não utiliza JavaScript.
- A grade responsiva, formulários, botões, navegação e cards devem priorizar classes Bootstrap.
- O CSS próprio aplica a identidade visual: azul `#12294B`, amarelo `#F5C518`, preto e branco.
- A fonte padrão do portal e do painel administrativo é Arial.
- `public/css/style.css` reúne tokens, base e componentes; `public/css/layout.css` contém apenas os layouts específicos das páginas.
- `public/assets/css/admin.css` contém apenas os ajustes visuais do painel administrativo.

## Estrutura principal

```text
portal-si/
├── app/                 Regras, models, controllers e views auxiliares
├── config/              Configuração central do banco
├── database/            Estrutura, dados iniciais e criação do administrador
├── docs/                Identidade visual do projeto
└── public/              Arquivos acessíveis pelo navegador
    ├── admin/           Área administrativa
    ├── assets/css/      Estilos do painel
    ├── css/             Estilos do portal público
    ├── includes/        Cabeçalho e rodapé
    └── uploads/         Imagens enviadas pelo painel
```

## Verificação antes da entrega

1. Execute `php -l` em todos os arquivos PHP.
2. Crie um banco vazio e execute `schema.sql` e `seed.sql` nessa ordem.
3. Teste início, busca, categorias, publicação, agenda e perfil público.
4. Teste login, perfil, usuários, notícias, eventos e logout.
5. Verifique o portal em larguras de 360 px, 768 px e 1280 px.
6. Confirme que não existem arquivos `.js` ou tags `<script>`.
7. Confirme que `git status` está limpo antes da entrega.

## Perfis

- **Admin:** edita seu próprio perfil e gerencia contas, notícias e eventos. É o único com permissões administrativas.
- **Professor:** consulta e edita os próprios dados de perfil; não cria, altera ou exclui publicações/eventos.
- **Aluno:** consulta seu perfil e conteúdos/eventos associados ao curso informado no cadastro.
- Visitantes veem o site público sem autenticação.

## Segurança incluída

Senhas são armazenadas com `password_hash()` e verificadas com `password_verify()`; sessões usam cookies `HttpOnly`, `SameSite`, modo estrito e regeneração do identificador após autenticação; ações de formulário autenticadas usam token CSRF; consultas usam parâmetros PDO; autorização é conferida no servidor; mensagens de erro de banco não são exibidas ao visitante; uploads aceitam somente formatos de imagem reconhecidos e limitam tamanho.

Nenhum sistema pode ser garantido como livre de todas as falhas apenas por uma revisão do código. Antes de publicar na internet, configure HTTPS, credenciais próprias e fora do código, permissões mínimas no PostgreSQL, backups, atualização de PHP/Apache/PostgreSQL, limite de tentativas de login, política de recuperação de senha e revisão de segurança independente.
