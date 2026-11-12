# Portal de Comunicação — Sistemas de Informação ESUCRI

Projeto de Extensão IV · 6ª fase · 2026/2
Bacharelado em Sistemas de Informação — Faculdades ESUCRI (Criciúma/SC)
Equipe: Grupo Power — integrantes: *(preencher os nomes do grupo)*
Professor: Me. Jucemar Formigoni Candido

## Sobre o projeto

O Portal SI é o site de comunicação do curso de Sistemas de Informação. Ele reúne notícias, comunicados e a agenda de eventos em um só lugar, para quem acompanha o curso (alunos, professores e comunidade). A equipe entra em um painel restrito, cadastra o texto e ele aparece na página inicial e na lista da sua categoria, sem precisar alterar o código.

## Objetivo

Desenvolver um portal web de comunicação do curso de Sistemas de Informação, permitindo que a equipe publique notícias e eventos sem depender de alterações no código-fonte.

## Funcionalidades implementadas

- [x] Cadastro de usuários (feito pelo Admin) com senha protegida por hash
- [x] Login, logout e controle de acesso por perfil (Admin, Professor e Aluno)
- [x] Painel administrativo com cadastro, listagem, edição e exclusão de notícias, categorias, eventos e usuários
- [x] Home Page pública exibindo o conteúdo do banco, com manchete, destaques, lista de notícias, editorias e agenda
- [x] Filtro por categoria e paginação (6 conteúdos por página)
- [x] Página de detalhe do conteúdo, com resposta 404 amigável quando o conteúdo não existe
- [x] Layout responsivo (Bootstrap 5, somente CSS, mais CSS próprio) e sem JavaScript
- [x] Banco PostgreSQL hospedado no Aiven, com conexão criptografada (SSL)
- [ ] Upload de imagens — previsto para a próxima etapa
- [ ] Incorporação de vídeos — previsto para a próxima etapa

> Só está marcado o que foi testado e funciona. A lista completa dos casos executados está em [docs/matriz-de-testes.md](docs/matriz-de-testes.md).

## Escopo desta entrega (Etapa 2)

Esta entrega contempla o portal funcionando **até a Home Page pública, com conteúdo textual**. Não fazem parte desta fase o upload de imagens ou arquivos e a incorporação de vídeos. Os testes são textuais: verificam fluxos, mensagens, dados e navegação.

Transparência: o código já traz uma estrutura inicial para imagem de capa, vídeo e eventos com imagem (por exemplo, os campos `imagem_capa` e `link_youtube`). Esses recursos **não fazem parte do que foi avaliado nem foram testados** nesta etapa. Os eventos e as tags também existem no portal, mas ficaram fora da matriz de testes.

## Continuidade (próximo semestre)

- Upload de imagens com validação de tipo real (MIME) e limite de tamanho
- Incorporação responsiva de vídeos
- Manual do usuário com capturas de tela
- Recuperação de senha e limite de tentativas de login
- Testes dos eventos e das tags

## Tecnologias

| Camada | Tecnologia |
|---|---|
| Linguagem | PHP 8.x (testado com 8.0.30) |
| Banco de dados | PostgreSQL 18.6 hospedado no Aiven (compatível com PostgreSQL 14 ou superior) |
| Marcação | HTML5 semântico |
| Estilo | Bootstrap 5.3.3 (somente CSS, carregado por CDN) e CSS próprio, sem JavaScript |
| Servidor local | Apache (XAMPP ou Laragon) |
| Editor | VS Code |

> O Bootstrap é usado apenas pela folha de estilo (grade e formulários). O portal não carrega nenhum JavaScript.

## Requisitos

- Apache com PHP 8.x e a extensão `pdo_pgsql` habilitada
- Um banco PostgreSQL: local (porta 5432) ou o serviço gratuito do Aiven
- Acesso à internet (para o banco no Aiven e para a folha de estilo do Bootstrap)
- Navegador atualizado

## Instalação

1. Clone o repositório na pasta do servidor web:
   `git clone https://github.com/Luiz-Fillipe/Faculdade_PHP.git portal-si`
2. Crie a base: `CREATE DATABASE portal_si;` (ou crie o banco no Aiven)
3. Execute o script de estrutura: `psql -U postgres -d portal_si -f database/schema.sql`
   (ele **apaga e recria** as tabelas do banco selecionado; confira o banco antes)
4. Crie o administrador de teste: `php database/criar_admin.php`
5. Carregue os dados iniciais: `psql -U postgres -d portal_si -f database/seed.sql`
   (opcional, para a demonstração: `psql -U postgres -d portal_si -f database/demo.sql` carrega 12 conteúdos fictícios, com capas das imagens que já estão em `public/assets/img/noticias/`)
6. Copie `.env.example` para `.env` e ajuste a conexão (veja a seção seguinte)
7. Aponte o servidor web para a pasta `public/` e acesse, por exemplo, `http://localhost/portal-si/public/`

## Configuração

Arquivo `.env` (na raiz do projeto, fora do Git):

- `PORTAL_DB_HOST` — servidor do banco (padrão `localhost`)
- `PORTAL_DB_PORT` — porta (padrão `5432`)
- `PORTAL_DB_NAME` — nome da base (`portal_si`)
- `PORTAL_DB_USER` e `PORTAL_DB_PASSWORD` — credenciais do banco
- `PORTAL_DB_SSLMODE` — use `require` para o Aiven e deixe vazio para o banco local

> Nunca versione senhas reais. O `.env` está no `.gitignore`; o arquivo `.env.example` traz só valores de exemplo.

## Banco de dados

Tabelas principais: `usuarios`, `categorias` e `conteudos`. Também existem `tags`, `conteudo_tags`, `eventos`, `midias` e `auditoria`.

    conteudos
    ├── id             BIGSERIAL, chave primária
    ├── titulo         VARCHAR(200), obrigatório
    ├── slug           VARCHAR(200), obrigatório e único (endereço amigável)
    ├── resumo         TEXT, obrigatório
    ├── corpo          TEXT, obrigatório
    ├── categoria_id   BIGINT, chave estrangeira → categorias(id)
    ├── autor_id       BIGINT, chave estrangeira → usuarios(id)
    ├── status         rascunho, publicado ou arquivado
    ├── destaque       BOOLEAN
    └── publicado_em   TIMESTAMPTZ

> Os campos `imagem_capa` e `link_youtube` existem em `conteudos`, mas não são usados nesta etapa (previstos para a próxima). O diagrama de entidades e o dicionário completo estão em [docs/modelo-de-dados.md](docs/modelo-de-dados.md).

## Estrutura de pastas

    portal-si/
    ├── app/            consultas e regras (Controller), views auxiliares e funções de sessão (helpers)
    ├── config/         conexão com o banco (database.php)
    ├── database/       schema.sql, seed.sql, demo.sql (conteúdo fictício), criar_admin.php e teste_conexao.sql
    ├── docs/           documentação, matriz de testes, defeitos e modelo de dados
    └── public/         raiz do site (única pasta exposta ao navegador)
        ├── admin/      área restrita: notícias, eventos, usuários, categorias e perfil
        ├── css/        folhas de estilo
        ├── includes/   cabeçalho, rodapé e item de lista
        ├── index.php   Home pública
        ├── categoria.php  listagem por categoria, com paginação
        └── conteudo.php   detalhe do conteúdo

## Como usar

1. Acesse `/admin/login.php`
2. Informe as credenciais de teste
3. No painel, abra "Notícias" e cadastre um conteúdo (título, resumo, texto, categoria e estado "Publicado")
4. Volte à Home e confirme a exibição

## Acesso de teste

| Perfil | E-mail | Senha |
|---|---|---|
| Administrador | admin@portalsi.local | informada ao professor avaliador |

> A senha inicial é a definida em `database/criar_admin.php`. Troque-a no primeiro acesso.

## Testes realizados

Foram previstos 46 casos (44 do modelo da apostila e 2 adicionais da equipe), todos textuais:

| Bloco | Previstos | Executados | Aprovados |
|---|---:|---:|---:|
| Cadastro e login | 13 | 13 | 13 |
| CRUD de conteúdos | 10 | 7 | 7 |
| Exibição dinâmica | 9 | 8 | 8 |
| Segurança | 6 | 5 | 5 |
| Responsividade e usabilidade | 6 | 5 | 5 |
| Casos adicionais | 2 | 2 | 2 |
| **Total** | **46** | **40** | **40** |

Os 6 casos restantes dependem de imagem, upload ou vídeo e estão suspensos nesta etapa. A 1ª rodada encontrou **4 defeitos** do portal (redirecionamento do login quebrado, contraste e dois problemas de layout para conteúdo só de texto); todos foram corrigidos e reexecutados. Detalhes em [docs/matriz-de-testes.md](docs/matriz-de-testes.md) e [docs/registro-de-defeitos.md](docs/registro-de-defeitos.md).

## Segurança adotada

- Senhas com `password_hash()` e `password_verify()`
- Consultas parametrizadas com PDO em todo o sistema, inclusive `LIMIT` e `OFFSET`
- `htmlspecialchars()` em toda saída dinâmica
- Páginas restritas com verificação de sessão e de perfil; formulários do painel com token CSRF
- Identificadores recebidos por URL validados com `filter_input`, com resposta 404 para registro inexistente
- Erros registrados em log, nunca exibidos ao visitante
- Conexão com o Aiven criptografada (`sslmode=require`) e credenciais fora do repositório (`.env`)

## Problemas conhecidos

- O portal usa o Bootstrap (somente CSS) em vez de CSS totalmente próprio; foi uma decisão de projeto para ganhar tempo com a grade e os formulários.
- Não há recuperação de senha nem limite de tentativas de login.
- O plano gratuito do Aiven desliga o serviço sem uso; se a conexão falhar, ligue o serviço no console.
- Imagens e vídeos existem no código, mas estão fora do escopo avaliado e não foram testados.
- Os testes foram manuais e por roteiro; não há testes automatizados no repositório.

## Equipe e responsabilidades

| Integrante | Responsabilidade |
|---|---|
| *(preencher)* | *(ex.: modelagem do banco)* |
| *(preencher)* | *(ex.: painel administrativo)* |
| *(preencher)* | *(ex.: exibição pública)* |
| *(preencher)* | *(ex.: testes e documentação)* |

## Cronograma

| Data | Entrega |
|---|---|
| 24/09/2026 | Etapa 1 — autenticação e estrutura |
| 29/10/2026 | Testes integrados e documentação |
| 05/11/2026 | Ensaio da apresentação |
| 12/11/2026 | Etapa 2 — portal e apresentação final |

## Documentação

- [Documentação técnica](docs/Documentacao-Tecnica.docx)
- [Matriz de casos de teste](docs/matriz-de-testes.md)
- [Registro de defeitos](docs/registro-de-defeitos.md)
- [Modelo de dados e dicionário](docs/modelo-de-dados.md)
- [Apêndice B — Script do banco](docs/Apendice-B-Script-do-Banco.docx)
- [Apêndice C — Dicionário de dados](docs/Apendice-C-Dicionario-de-Dados.docx)

## Referências

SOMMERVILLE, Ian. **Engenharia de software**. 10. ed. São Paulo: Pearson, 2019.
PHP DOCUMENTATION GROUP. **PHP Manual**. Disponível em: https://www.php.net/manual/pt_BR/. Acesso em: 29 out. 2026.
POSTGRESQL GLOBAL DEVELOPMENT GROUP. **PostgreSQL Documentation**. Disponível em: https://www.postgresql.org/docs/. Acesso em: 29 out. 2026.

## Licença

Projeto desenvolvido para fins acadêmicos na disciplina Projeto de Extensão IV — Faculdades ESUCRI, 2026/2.
