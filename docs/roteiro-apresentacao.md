# Roteiro da apresentação final

Portal de Comunicação SI · Grupo Power · Projeto de Extensão IV · apresentação em 12/11/2026

Arquivo dos slides: [Apresentacao-Portal-SI.pptx](Apresentacao-Portal-SI.pptx) (17 slides da narrativa + 7 de reserva; o texto da fala de cada slide está nas notas do PowerPoint).

> Antes de apresentar: troque `[Nome 1]` a `[Nome 4]` no slide 1 e combine quem é cada apresentador.
> O tempo máximo por grupo não está legível na apostila ("0 minutos"). Confirme com o professor. Abaixo, o plano usa os blocos da apostila (2 + 2 + 3 + 3 = 10 min) e 1 min de folga.

## Quem fala e quando

| Bloco | Apresentador | Slides | Tempo |
|---|---|---|---|
| 1 | Aluno 1 | 1 Capa · 2 Problema e público · 3 Objetivos · 4 Metodologia | 2 min |
| 2 | Aluno 2 | 5 Tecnologias · 6 Sem JavaScript · 7 Arquitetura · 8 Pastas | 2 min |
| 3 | Aluno 3 | 9 Banco · 10 Segurança · 11 O que funciona · 12 Painel | 3 min |
| 4 | Aluno 4 | 13 Demonstração · 14 Testes · 15 Defeitos · 16 Limitações · 17 Conclusão | 3 min |

Tempo dentro do bloco 4: demonstração cerca de 1 min 40 s, testes 30 s, defeitos 20 s, limitações 20 s e conclusão 20 s. Se a banca der mais tempo, a demonstração pode ir até 3 minutos.

Frases de passagem (dizer em voz alta):
- 1 para 2: "Agora o [nome] explica com o que construímos."
- 2 para 3: "Agora a [nome] mostra o banco, a segurança e o painel."
- 3 para 4: "Agora o [nome] mostra o portal funcionando."

Regras de fala (da apostila): frases curtas, ritmo pausado, não ler o slide, cortar conteúdo em vez de acelerar. Explique cada termo técnico em uma frase.

## Demonstração (fluxo fixo)

Quem apresenta não digita. Um colega fica no teclado e segue exatamente esta ordem:

1. **Home** (`/index.php`): mostrar a manchete, a faixa AGORA e as editorias.
2. **Editoria**: clicar em "Notícias" e mostrar a lista com paginação.
3. **Login**: clicar em "Entrar" e entrar como administrador.
4. **Painel**: abrir "Notícias" e mostrar o formulário e a lista.
5. **Cadastrar**: título `Demonstração ao vivo do Portal SI`, resumo e texto curtos, categoria Notícias, estado **Publicado**, "Salvar publicação".
6. **Home atualizada**: voltar ao site; a notícia nova aparece na faixa AGORA e na lista.

Depois da apresentação, apague o item de teste pelo painel (botão Excluir) para a Home voltar ao estado anterior.

Todos os dados são fictícios. Não use nomes, e-mails ou textos reais da ESUCRI.

## Antes de entrar na sala (checklist)

- [ ] Serviço do **PostgreSQL ligado** (no Windows: serviço `postgresql-x64-17`)
- [ ] **Apache** do XAMPP ligado (ou `php -S 127.0.0.1:8099 -t public` na raiz do projeto)
- [ ] `.env` apontando para o banco da demonstração (veja a seção seguinte)
- [ ] Banco com conteúdo suficiente: `database/demo.sql` executado (a Home não pode abrir vazia)
- [ ] Login do administrador testado na máquina da apresentação
- [ ] Navegador com as abas abertas na ordem do fluxo; zoom em 125% para a projeção
- [ ] Apresentação do PowerPoint aberta e as capturas de reserva conferidas (slides 18 a 24)
- [ ] Não depender de internet: o Bootstrap vem por CDN; sem internet a página perde parte do estilo. Teste antes e, se a rede for ruim, vá direto ao plano B
- [ ] Notificações do computador desligadas

## Banco da demonstração

Os testes da Aula 13 rodaram no banco do Aiven. Para a demonstração a equipe pode usar:

- **Banco local** (mais seguro, sem internet): crie a base, rode `database/schema.sql`, `php database/criar_admin.php`, `database/seed.sql` e `database/demo.sql`, e aponte o `.env` para `localhost`.
- **Aiven**: só se o serviço estiver ligado e o endereço atual estiver no `.env` (`PORTAL_DB_SSLMODE=require`). Ligue o serviço antes de sair de casa e teste o login.

## Plano de contingência

| Plano | Quando | O que fazer |
|---|---|---|
| A | Tudo funciona | Demonstração ao vivo |
| B | Banco, Apache ou rede falham | Ir aos slides de reserva 19 a 21 (Home e editoria, login e painel, painel após cadastro e Home atualizada) e narrar o mesmo fluxo pelas capturas |
| C | Nada funciona | Seguir com os slides, os testes (slide 14) e a conclusão; mostrar a matriz de testes em `docs/` |

Não peça desculpas por muito tempo. Diga "vamos pelas capturas" e continue.

## Perguntas prováveis e respostas

- **Por que PHP sem framework?** A disciplina pede PHP, HTML, CSS e PostgreSQL puros. Isso também mostrou o que um framework faz por baixo.
- **Por que sem JavaScript?** Limitação do servidor. Paginação por links, formulários por POST e Bootstrap só com o CSS.
- **Como a senha é protegida?** Com `password_hash` ao gravar e `password_verify` ao entrar. A senha nunca é guardada em texto.
- **O que é consulta parametrizada?** O comando SQL e os dados vão separados para o banco, o que evita SQL Injection.
- **O que é CSRF?** Um ataque que faz o navegador do usuário enviar um formulário sem ele querer. O token no formulário garante que ele veio do nosso site.
- **E as imagens e os vídeos?** A estrutura existe no código e no banco (`imagem_capa`, `link_youtube`), mas não foi avaliada nem testada. Ficam para o próximo semestre. Se alguém notar o campo "Imagem de capa" no formulário, é isso.
- **Por que 6 testes ficaram suspensos?** Dependem de imagem, upload ou vídeo, que não fazem parte desta etapa.
- **Os testes são automáticos?** Não. São manuais, por roteiro, e estão registrados em `docs/matriz-de-testes.md`.
- **Quantas pessoas pediram o portal / como era antes?** "Ainda não levantamos esse dado." Não invente números.
- **Quem pode publicar?** Quem tem perfil Admin e entra no painel. Os perfis Professor e Aluno existem, mas a demonstração usa o Admin.
- **O que vocês fariam diferente?** Planejar os testes antes de terminar o layout (dois defeitos vieram de conteúdo só de texto) e testar no Windows desde o início (defeito do login).

Se não souber responder: "ainda não levantamos esse dado" ou "vamos registrar e responder depois". Nunca invente.

## Ensaio

A apostila pede para cronometrar. Faça pelo menos um ensaio completo com o cronômetro, com o colega digitando a demonstração, e anote o tempo de cada bloco.
