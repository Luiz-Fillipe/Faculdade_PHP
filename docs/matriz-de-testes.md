# Matriz de casos de teste

Portal de Comunicação SI · Projeto de Extensão IV · 2026/2 · execução em 29/10/2026

Escopo da Etapa 2 (Aula 13): portal funcionando até a Home pública, com conteúdo **textual**. Os casos que dependem de imagem, upload ou vídeo ficam **Suspensos** (ou **Não se aplica**) e voltam na próxima etapa.

**Ambiente:** PHP 8.0.30 (XAMPP), Apache/servidor embutido do PHP, PostgreSQL 18.6 hospedado no Aiven (SSL), navegador Microsoft Edge.
**Dados de teste (seção 3.1 da apostila):** 3 usuários (Admin, Professor e Aluno), 5 categorias e 12 conteúdos publicados, 8 deles em Notícias para exercitar a paginação. Todos só de texto. Os dados de teste foram removidos ao final.

## Resumo

| Bloco | Previstos | Executados | Aprovados | Suspensos / não se aplica |
|---|---:|---:|---:|---:|
| Cadastro de usuário | 6 | 6 | 6 | 0 |
| Login e sessão | 7 | 7 | 7 | 0 |
| CRUD de conteúdos | 10 | 7 | 7 | 3 |
| Exibição dinâmica | 9 | 8 | 8 | 1 |
| Segurança | 6 | 5 | 5 | 1 |
| Responsividade e usabilidade | 6 | 5 | 5 | 1 |
| Casos adicionais da equipe | 2 | 2 | 2 | 0 |
| **Total** | **46** | **40** | **40** | **6** |

Na 1ª rodada, 7 casos foram reprovados: 5 por causa de 4 defeitos do portal (BUG-001 a BUG-004) e 2 por falha do roteiro de teste. Todos foram corrigidos e reexecutados; a situação final abaixo é a da rodada de regressão.


## Cadastro de usuário

| ID | Caso de teste | Pré-condição | Passos | Entrada | Resultado esperado | Resultado obtido | Situação |
|---|---|---|---|---|---|---|---|
| CT-01 | Cadastro válido | Admin autenticado | Entrar como Admin; abrir Cadastrar usuário; preencher os campos; salvar | Nome, e-mail e senha de 17 caracteres (perfil Professor) | Usuário gravado e redirecionado à lista de usuários | HTTP 302, redirecionou para usuarios.php?ok=1, +1 registro (Obs.: No portal o cadastro é feito pelo Admin e termina na lista de usuários (não há autocadastro).) | Aprovado |
| CT-02 | E-mail duplicado | Admin autenticado | Cadastrar usuário com e-mail já existente | E-mail já cadastrado | Mensagem de erro e nenhum registro novo | Mensagem "Verifique se o e-mail já está em uso" e total de usuários inalterado | Aprovado |
| CT-03 | Senha curta | Admin autenticado | Cadastrar usuário com senha de 3 caracteres | Senha com 3 caracteres | Mensagem de erro de senha mínima, sem gravar | Mensagem "A senha deve conter pelo menos 12 caracteres" (regra mais rígida que os 8 do modelo) | Aprovado |
| CT-04 | Campos vazios | Admin autenticado | Enviar o formulário de cadastro sem preencher | Formulário sem preenchimento | Mensagem de erro para os campos obrigatórios | HTTP 200; erros exibidos: nome, e-mail e senha | Aprovado |
| CT-05 | E-mail em formato inválido | Admin autenticado | Cadastrar usuário com e-mail "teste@" | "teste@" | Mensagem de erro de formato | Mensagem "Informe um e-mail válido" e nada gravado | Aprovado |
| CT-06 | Senha armazenada com hash | Admin autenticado | Consultar a coluna senha_hash do usuário criado no CT-01 | Senha de teste cadastrada no CT-01 | Coluna senha_hash com hash, nunca texto puro | senha_hash começa com "$2y$10$" (bcrypt), 60 caracteres | Aprovado |

## Login e sessão

| ID | Caso de teste | Pré-condição | Passos | Entrada | Resultado esperado | Resultado obtido | Situação |
|---|---|---|---|---|---|---|---|
| CT-07 | Login válido | Usuários de teste criados no CT-01 | Abrir o login; informar e-mail e senha corretos; abrir o painel | E-mail e senha corretos (usuário Professor) | Acesso autenticado ao painel | Login HTTP 302; /admin/perfil.php abriu (HTTP 200) | Aprovado |
| CT-08 | Senha incorreta | Usuários de teste criados no CT-01 | Informar e-mail válido e senha errada | E-mail válido, senha errada | Mensagem genérica, sem revelar qual campo errou | Mensagem "E-mail ou senha inválidos." (HTTP 200, sem sessão) | Aprovado |
| CT-09 | E-mail inexistente | Usuários de teste criados no CT-01 | Informar um e-mail que não existe | E-mail não cadastrado | Mesma mensagem genérica do CT-08 | Mesma mensagem "E-mail ou senha inválidos." | Aprovado |
| CT-10 | Acesso direto sem login | Usuários de teste criados no CT-01 | Sem sessão, abrir /admin/conteudos.php e seguir o redirecionamento | Abrir /admin/conteudos.php sem sessão | Redirecionamento para o login (página existente) | HTTP 302, Location: /admin/login.php?erro=restrito; destino abre: True (Obs.: BUG-001: na 1ª rodada o redirecionamento ia para "\/admin/login.php" (página inexistente). Corrigido e reexecutado.) | Aprovado (1ª rodada: Reprovado) |
| CT-11 | Logout | Usuários de teste criados no CT-01 | Com sessão ativa, clicar em Sair; abrir página restrita | Clicar em Sair (POST com token) | Sessão destruída; área restrita bloqueada | Logout HTTP 302; perfil.php depois: HTTP 302 | Aprovado |
| CT-12 | Acesso após logout | Usuários de teste criados no CT-01 | Após o logout, abrir uma página restrita | Abrir página restrita | Redirecionamento para o login (página existente) | HTTP 302, Location: /admin/login.php?erro=restrito (Obs.: BUG-001 (mesma causa do CT-10). Corrigido e reexecutado.) | Aprovado (1ª rodada: Reprovado) |
| CT-13 | Persistência da sessão | Usuários de teste criados no CT-01 | Navegar por 5 páginas do painel na mesma sessão | Navegar entre 5 páginas do painel (Admin) | Usuário permanece autenticado | Todas HTTP 200: 200, 200, 200, 200, 200 | Aprovado |

## CRUD de conteúdos

| ID | Caso de teste | Pré-condição | Passos | Entrada | Resultado esperado | Resultado obtido | Situação |
|---|---|---|---|---|---|---|---|
| CT-14 | Cadastro completo (textual) | Admin autenticado; 5 categorias | Painel > Notícias; criar 12 conteúdos (título, resumo, texto, categoria, estado Publicado) | Título, categoria, resumo e texto; 12 conteúdos criados | Registro gravado e redirecionamento com sucesso | 12 registros gravados, todos com HTTP 302 (Obs.: Versão textual: sem imagem e sem vídeo (escopo da Aula 13).) | Aprovado |
| CT-15 | Cadastro sem imagem | Admin autenticado; 5 categorias | Conferir no banco os campos de imagem e vídeo do conteúdo criado | Todos os campos textuais, sem arquivo | Registro gravado com campo imagem nulo | imagem_capa = NULL e link_youtube = NULL no registro criado | Aprovado |
| CT-16 | Cadastro sem título | Admin autenticado; 5 categorias | Salvar um conteúdo com o título vazio | Categoria e texto preenchidos, título vazio | Mensagem de erro e nenhum registro gravado | Mensagem "Preencha título, resumo, texto e categoria." e total inalterado | Aprovado |
| CT-17 | Imagem acima do limite | Admin autenticado; 5 categorias | Suspenso nesta etapa | Arquivo de 3 MB | Bloqueio por tamanho | Suspenso nesta etapa (escopo reduzido da Aula 13) | Suspenso |
| CT-18 | Arquivo de tipo inválido | Admin autenticado; 5 categorias | Suspenso nesta etapa | .php renomeado para .jpg | Bloqueio por MIME real | Suspenso nesta etapa (escopo reduzido da Aula 13) | Suspenso |
| CT-19 | Listagem | Admin autenticado; 5 categorias | Abrir a listagem de notícias do painel | Abrir /admin/conteudos.php | Todos os registros exibidos, com categoria resolvida pelo JOIN | 12 dos 12 conteúdos de teste listados, com a categoria ao lado | Aprovado |
| CT-20 | Edição de conteúdo | Admin autenticado; 5 categorias | Abrir um conteúdo, alterar só o título e salvar | Alterar apenas o título | Título atualizado, demais campos preservados | Título agora: "[TESTE] Cursos 01 (editado)" | Aprovado |
| CT-21 | Edição com troca de imagem | Admin autenticado; 5 categorias | Suspenso nesta etapa | Enviar nova imagem | Imagem trocada e arquivo antigo removido | Suspenso nesta etapa (escopo reduzido da Aula 13) | Suspenso |
| CT-22 | Exclusão de conteúdo | Admin autenticado; 5 categorias | Excluir um conteúdo pelo painel; conferir o banco | Excluir um conteúdo (somente o registro) | Registro removido do banco (arquivo físico: suspenso) | HTTP 302; registro 44 não existe mais no banco (Obs.: Exclusão do registro; a parte do arquivo físico está suspensa nesta etapa.) | Aprovado |
| CT-23 | Exclusão de categoria com conteúdo | Admin autenticado; 5 categorias | Painel > Categorias; excluir a categoria Notícias (com conteúdos) | Tentar excluir a categoria Notícias (em uso) | Bloqueio por integridade referencial, com mensagem clara | Mensagem "Não é possível excluir: existem publicações nesta categoria." e categoria preservada | Aprovado |

## Exibição dinâmica

| ID | Caso de teste | Pré-condição | Passos | Entrada | Resultado esperado | Resultado obtido | Situação |
|---|---|---|---|---|---|---|---|
| CT-24 | Home Page reflete o banco | 12 conteúdos de teste publicados (8 em Notícias) | Cadastrar um conteúdo publicado; abrir a Home | Conteúdo novo cadastrado pelo painel | Conteúdo aparece na Home sem alterar código | "[TESTE] Cursos 02" apareceu na Home (HTTP 200) logo após o cadastro | Aprovado |
| CT-25 | Ordem por data | 12 conteúdos de teste publicados (8 em Notícias) | Abrir a categoria Notícias; comparar a ordem dos conteúdos criados | Vários conteúdos na mesma categoria | Mais recente aparece primeiro | Na página 1, "Noticias 07" vem antes de "06" e "05" | Aprovado |
| CT-26 | Filtro por categoria | 12 conteúdos de teste publicados (8 em Notícias) | Abrir a categoria Comunicados | Abrir a categoria Comunicados | Apenas conteúdos daquela categoria | Na área de conteúdo só aparecem conteúdos de Comunicados (Obs.: Reprovado na 1ª rodada por falha do roteiro: a faixa AGORA do cabeçalho repete o conteúdo mais recente em todas as páginas. A checagem passou a olhar só a área de conteúdo; reexecutado.) | Aprovado (1ª rodada: Reprovado) |
| CT-27 | Paginação | 12 conteúdos de teste publicados (8 em Notícias) | Abrir Notícias (8 conteúdos); ir para a página 2 | Categoria com mais de 6 conteúdos (8 no total) | Navegação entre páginas com registros distintos | Contador: 8; página 1 e página 2 sem itens repetidos; "Próxima" e números presentes: True | Aprovado |
| CT-28 | Detalhe do conteúdo | 12 conteúdos de teste publicados (8 em Notícias) | Clicar em um conteúdo da lista | Abrir um conteúdo pelo endereço (slug) | Página exibe título, autor, data e texto | Página com título, autor "Administrador do Portal", data dd/mm/aaaa e texto | Aprovado |
| CT-29 | Embed de vídeo | 12 conteúdos de teste publicados (8 em Notícias) | Suspenso nesta etapa | Conteúdo com URL do YouTube | Vídeo em 16:9 | Suspenso nesta etapa (escopo reduzido da Aula 13) | Suspenso |
| CT-30 | Conteúdo inexistente | 12 conteúdos de teste publicados (8 em Notícias) | Abrir /conteudo.php?id=99999 | /conteudo.php?id=99999 | Resposta 404 com mensagem amigável | HTTP 404 com a mensagem "Essa publicação não foi encontrada." | Aprovado |
| CT-31 | Estado vazio | 12 conteúdos de teste publicados (8 em Notícias) | Abrir a categoria Extensão (sem conteúdo) | Categoria Extensão, sem conteúdo | Mensagem de estado vazio, sem área em branco | Mensagem "Nenhuma publicação nesta categoria ainda." | Aprovado |
| CT-32 | Formatação de data | 12 conteúdos de teste publicados (8 em Notícias) | Abrir um conteúdo e olhar a data | Qualquer conteúdo | Data exibida em dd/mm/aaaa | Detalhe mostra "Publicado em dd/mm/aaaa" | Aprovado |

## Segurança

| ID | Caso de teste | Pré-condição | Passos | Entrada | Resultado esperado | Resultado obtido | Situação |
|---|---|---|---|---|---|---|---|
| CT-33 | SQL Injection no identificador | Servidor local em execução | Abrir /conteudo.php?id=1 OR 1=1 e ?slug=x' OR '1'='1 | conteudo.php?id=1 OR 1=1  e  slug=x' OR '1'='1 | Tratado como literal, sem vazamento de dados | Ambos HTTP 404 com a mensagem amigável; nenhum conteúdo listado ou vazado (Obs.: Reprovado na 1ª rodada pelo mesmo motivo do CT-26 (falha do roteiro). Reexecutado.) | Aprovado (1ª rodada: Reprovado) |
| CT-34 | XSS armazenado | Servidor local em execução | Cadastrar conteúdo com título <script>; abrir a Home e o detalhe | Título com tag <script> | Texto exibido literalmente, sem execução | Na página do conteúdo e na Home o título saiu como &lt;script&gt; | Aprovado |
| CT-35 | XSS em campo de texto | Servidor local em execução | Cadastrar conteúdo com <img onerror> no texto; abrir o detalhe | Texto com <img onerror> | Exibido como texto escapado | O corpo foi exibido escapado (&lt;img ...) | Aprovado |
| CT-36 | Acesso a arquivo de configuração | Servidor local em execução | Abrir /config/conexao.php, /config/database.php e /.env pela URL | Abrir /config/conexao.php, /config/database.php e /.env pela URL | Nenhuma credencial exibida | conexao.php: HTTP 200, corpo sem credenciais; database.php: HTTP 404; .env: HTTP 404 | Aprovado |
| CT-37 | Erro de PDO exposto | Servidor local em execução | Iniciar o servidor com banco inacessível; abrir a Home | Forçar falha de conexão (banco inacessível) | Mensagem genérica ao usuário; detalhe só no log | HTTP 500 com "Não foi possível conectar ao banco de dados. Confira a configuração do"; detalhe técnico apenas no log do servidor | Aprovado |
| CT-38 | Upload de script disfarçado | Servidor local em execução | Suspenso nesta etapa | shell.php renomeado como imagem | Bloqueio por MIME | Suspenso nesta etapa (escopo reduzido da Aula 13) | Suspenso |

## Responsividade e usabilidade

| ID | Caso de teste | Pré-condição | Passos | Entrada | Resultado esperado | Resultado obtido | Situação |
|---|---|---|---|---|---|---|---|
| CT-39 | Layout em celular | 12 conteúdos de teste publicados | Abrir Início, Editoria e Notícia em 375 px; medir a largura da página | Largura de 375 px (Início, Editoria e Notícia) | Cards em coluna única, sem rolagem horizontal | Itens em 1 coluna e destaques empilhados; sem rolagem horizontal nas 3 páginas; paginação dentro da tela | Aprovado |
| CT-40 | Layout em tablet | 12 conteúdos de teste publicados | Abrir Início e Editoria em 768 px; verificar sobreposição de blocos | Largura de 768 px (Início e Editoria) | Grid reorganizado sem sobreposição | 11 blocos analisados na Início e 6 itens na Editoria: 0 pares sobrepostos; sem rolagem horizontal | Aprovado |
| CT-41 | Menu em tela pequena | 12 conteúdos de teste publicados | Em 375 px, rolar o menu e alcançar o último item | Largura de 375 px | Menu acessível e utilizável | Menu rola na horizontal (351 de 680 px visíveis); o último item fica visível ao rolar; links com 52 px de altura | Aprovado |
| CT-42 | Imagem com alt | 12 conteúdos de teste publicados | Não se aplica | Imagem sem carregar | Texto alternativo exibido | Não se aplica sem imagens nesta etapa; manter o atributo alt em imagens futuras | Não se aplica |
| CT-43 | Navegação por teclado | 12 conteúdos de teste publicados | Pressionar Tab cinco vezes e observar o foco | Tecla Tab entre os elementos (5 toques) | Foco visível e ordem lógica | Ordem: links do topo, marca, busca e menu; o foco fica visível (anel padrão do navegador) | Aprovado |
| CT-44 | Contraste | 12 conteúdos de teste publicados | Calcular a razão de contraste de 13 pares de cores (fórmula da WCAG) | Texto sobre fundo (13 pares de cores do portal) | Legibilidade preservada (mínimo 4,5:1) | Menor razão após a correção do BUG-002: 5,59:1 (todas atendem ao nível AA) (Obs.: BUG-002: a hora da faixa AGORA tinha 4,47:1. Cor ajustada para #5f6368 e reexecutado.) | Aprovado (1ª rodada: Reprovado) |

## Casos adicionais da equipe

| ID | Caso de teste | Pré-condição | Passos | Entrada | Resultado esperado | Resultado obtido | Situação |
|---|---|---|---|---|---|---|---|
| CT-45 | Destaque sem imagem mantém a legibilidade | 12 conteúdos de teste publicados (sem imagem) | Abrir a Home com conteúdos só de texto; ver o título do destaque | Home com conteúdos só de texto | Título do destaque em branco, sem maiúsculas forçadas | Título em branco, espaçamento normal e sem maiúsculas (verificado no navegador) (Obs.: BUG-003: na 1ª rodada o título saía cinza, maiúsculo e espaçado. Corrigido.) | Aprovado (1ª rodada: Reprovado) |
| CT-46 | Lista só de texto sem bloco de imagem vazio | 12 conteúdos de teste publicados (sem imagem) | Abrir uma categoria com conteúdos só de texto | Listas com conteúdos só de texto | Itens ocupam a largura toda, sem espaço reservado para imagem | 6 itens só de texto e 0 blocos de imagem vazios (Obs.: BUG-004: na 1ª rodada cada item só de texto mostrava um bloco azul "PORTAL SI". Corrigido.) | Aprovado (1ª rodada: Reprovado) |
