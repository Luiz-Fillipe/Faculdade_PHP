# Registro de defeitos

Portal de Comunicação SI · execução dos testes em 29/10/2026

**Quadro de acompanhamento:** aberto 0 · em correção 0 · em teste 0 · **fechado 4**.

| ID | Defeito | Caso | Severidade | Prioridade | Situação |
|---|---|---|---|---|---|
| BUG-001 | Redirecionamento do login aponta para página inexistente | CT-10 e CT-12 | Alta | Alta | Fechado |
| BUG-002 | Contraste insuficiente na hora da faixa AGORA | CT-44 | Baixa | Média | Fechado |
| BUG-003 | Título do destaque sem imagem sai cinza, maiúsculo e espaçado | CT-45 (caso adicional) | Média | Alta | Fechado |
| BUG-004 | Conteúdo só de texto exibe blocos de imagem vazios e deixa vazios na Home | CT-46 (caso adicional) | Baixa | Média | Fechado |

Observação: CT-26 e CT-33 foram reprovados na 1ª rodada por falha do roteiro de teste (não por defeito do portal): a faixa AGORA do cabeçalho repete o conteúdo mais recente em todas as páginas e a checagem olhava a página inteira. O roteiro foi corrigido e os casos reexecutados com aprovação.

## BUG-001 — Redirecionamento do login aponta para página inexistente

- **Data:** 29/10/2026
- **Caso de teste relacionado:** CT-10 e CT-12
- **Funcionalidade:** Controle de acesso (app/helpers/auth.php)
- **Severidade:** Alta · **Prioridade:** Alta
- **Ambiente:** PHP 8.0.30 no Windows, raiz do servidor na pasta public/
- **Pré-condição:** Sessão encerrada ou inexistente
- **Passos para reproduzir:**
  1. Sem fazer login, abrir /admin/conteudos.php
  2. Seguir o redirecionamento
- **Resultado esperado:** Redirecionar para /admin/login.php, que abre
- **Resultado obtido:** Redirecionava para "\/admin/login.php", endereço que não existe (erro 404)
- **Causa raiz:** public_base_path() usava dirname("/admin"), que no Windows devolve "\" e não "/"; o caminho do login saía com barra invertida.
- **Correção:** Os separadores passam a ser normalizados para "/" antes de montar o caminho. (`app/helpers/auth.php`)
- **Teste de regressão:** CT-10, CT-11, CT-12 e CT-13 reexecutados: aprovados.
- **Situação:** fechado

## BUG-002 — Contraste insuficiente na hora da faixa AGORA

- **Data:** 29/10/2026
- **Caso de teste relacionado:** CT-44
- **Funcionalidade:** Layout e acessibilidade (editorial.css)
- **Severidade:** Baixa · **Prioridade:** Média
- **Ambiente:** Qualquer navegador
- **Pré-condição:** Há conteúdo publicado
- **Passos para reproduzir:**
  1. Abrir qualquer página
  2. Medir o contraste do texto "há X min" na faixa AGORA
- **Resultado esperado:** Razão mínima de 4,5:1 (WCAG AA)
- **Resultado obtido:** 4,47:1 (cinza #6b7280 sobre #f5f6f8)
- **Causa raiz:** A cor de texto auxiliar (#6b7280) ficava um pouco abaixo do mínimo sobre o fundo cinza da faixa.
- **Correção:** Cor do texto auxiliar alterada para #5f6368 (6,05:1 sobre branco e 5,59:1 sobre o cinza da faixa). (`public/css/editorial.css`)
- **Teste de regressão:** CT-44 reexecutado: menor razão do portal 5,59:1.
- **Situação:** fechado

## BUG-003 — Título do destaque sem imagem sai cinza, maiúsculo e espaçado

- **Data:** 29/10/2026
- **Caso de teste relacionado:** CT-45 (caso adicional)
- **Funcionalidade:** Home pública (editorial.css)
- **Severidade:** Média · **Prioridade:** Alta
- **Ambiente:** Qualquer navegador
- **Pré-condição:** Conteúdos só de texto (escopo desta etapa)
- **Passos para reproduzir:**
  1. Publicar conteúdos sem imagem
  2. Abrir a Home e olhar os dois cards de destaque
- **Resultado esperado:** Título em branco, legível, com o mesmo estilo dos demais
- **Resultado obtido:** Título em cinza, em maiúsculas e com letras espaçadas, como o rótulo do bloco sem imagem
- **Causa raiz:** O seletor ".sem-imagem span" criado para o rótulo "PORTAL SI" também atingia o texto dentro do card de destaque.
- **Correção:** O seletor passou a valer só para os blocos de imagem da manchete e da lista, não para o card de destaque. (`public/css/editorial.css`)
- **Teste de regressão:** CT-45 aprovado; conferido no navegador (cor branca, sem maiúsculas, espaçamento normal).
- **Situação:** fechado

## BUG-004 — Conteúdo só de texto exibe blocos de imagem vazios e deixa vazios na Home

- **Data:** 29/10/2026
- **Caso de teste relacionado:** CT-46 (caso adicional)
- **Funcionalidade:** Home e listas (index.php, item_lista.php)
- **Severidade:** Baixa · **Prioridade:** Média
- **Ambiente:** Qualquer navegador
- **Pré-condição:** Conteúdos só de texto (escopo desta etapa)
- **Passos para reproduzir:**
  1. Publicar conteúdos sem imagem
  2. Abrir a Home e uma categoria
- **Resultado esperado:** Itens só de texto ocupam a largura toda, sem espaço reservado para foto
- **Resultado obtido:** Cada item mostrava um bloco azul "PORTAL SI" e a manchete deixava uma área vazia
- **Causa raiz:** Os layouts foram desenhados supondo uma imagem em todo conteúdo.
- **Correção:** Sem imagem, o item não desenha o bloco e usa a largura toda; os cards de destaque sem imagem ficam mais baixos. (`public/index.php, public/includes/item_lista.php, public/css/editorial.css`)
- **Teste de regressão:** CT-46 aprovado; 6 itens só de texto e 0 blocos vazios.
- **Situação:** fechado
