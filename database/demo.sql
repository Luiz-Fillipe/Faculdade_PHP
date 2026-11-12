-- database/demo.sql
-- Conteúdo FICTÍCIO para a demonstração (Aula 14): evita Home vazia.
-- As capas usam as imagens que já estão em public/assets/img/noticias/ (nada é enviado pelo painel).
-- Execute depois de schema.sql, criar_admin.php e seed.sql:
--   psql -U postgres -d portal_si -f database/demo.sql
-- Repetível: não duplica itens (usa o slug "demo-...").
-- Para remover: DELETE FROM conteudos WHERE slug LIKE 'demo-%';

INSERT INTO conteudos (titulo, slug, resumo, corpo, imagem_capa, categoria_id, autor_id, status, destaque, publicado_em)
SELECT d.titulo, d.slug, d.resumo, d.corpo, d.imagem, cat.id, u.id, 'publicado', d.destaque, now() - d.atraso
FROM (VALUES
    ('Oficina de PHP abre inscrições', 'demo-oficina-php', 'Encontro prático sobre PHP e PostgreSQL para iniciantes.', 'A oficina apresenta, passo a passo, como ligar uma página PHP a um banco PostgreSQL. Conteúdo fictício de demonstração.', 'assets/img/noticias/semana-tecnologia-inscricoes.webp', 'noticias', TRUE, interval '2 hours'),
    ('Grupo apresenta o Portal SI à turma', 'demo-apresentacao-portal', 'A equipe mostra o portal funcionando, do cadastro à Home.', 'A apresentação percorre o login, o painel administrativo e a exibição pública do conteúdo. Conteúdo fictício de demonstração.', 'assets/img/noticias/portal-si-plataforma-digital.webp', 'noticias', FALSE, interval '5 hours'),
    ('Biblioteca recebe novos títulos de programação', 'demo-biblioteca-programacao', 'Acervo ganha livros sobre web, bancos de dados e testes.', 'Os novos títulos já podem ser consultados no acervo. Conteúdo fictício de demonstração.', NULL, 'noticias', FALSE, interval '1 day'),
    ('Monitoria de banco de dados continua nesta semana', 'demo-monitoria-bd', 'Atendimento em grupo para dúvidas de SQL e modelagem.', 'A monitoria revisa consultas com JOIN e a criação de tabelas. Conteúdo fictício de demonstração.', 'assets/img/noticias/pesquisa-dados-setor-publico.webp', 'noticias', FALSE, interval '2 days'),
    ('Clube de leitura técnica reúne estudantes', 'demo-clube-leitura', 'Encontro quinzenal para discutir artigos de engenharia de software.', 'Cada encontro tem um texto curto e uma conversa guiada. Conteúdo fictício de demonstração.', 'assets/img/noticias/estudantes-premio-inovacao.webp', 'noticias', FALSE, interval '3 days'),
    ('Laboratório de informática ganha novos computadores', 'demo-laboratorio', 'Máquinas atualizadas para as aulas de programação.', 'O laboratório passa a ter equipamentos mais rápidos para compilar e testar projetos. Conteúdo fictício de demonstração.', 'assets/img/noticias/extensao-tecnologia-comunidade.webp', 'noticias', FALSE, interval '4 days'),
    ('Dicas para estudar modelagem de dados', 'demo-dicas-modelagem', 'Três hábitos simples para entender tabelas e relacionamentos.', 'Desenhar o diagrama antes do código ajuda a encontrar erros cedo. Conteúdo fictício de demonstração.', NULL, 'noticias', FALSE, interval '5 days'),
    ('Atendimento da secretaria muda de horário', 'demo-horario-secretaria', 'Novo horário vale a partir da próxima segunda-feira.', 'Consulte o horário atualizado antes de ir à secretaria. Conteúdo fictício de demonstração.', 'assets/img/noticias/comunicado-horario-atendimento.webp', 'comunicados', FALSE, interval '6 hours'),
    ('Sistema acadêmico em manutenção no sábado', 'demo-manutencao-sistema', 'O serviço fica indisponível durante a manhã.', 'Evite agendar atividades que dependam do sistema nesse período. Conteúdo fictício de demonstração.', 'assets/img/noticias/manutencao-programada-sistema.webp', 'comunicados', FALSE, interval '1 day 3 hours'),
    ('Prazo para entrega de trabalhos é prorrogado', 'demo-prazo-trabalhos', 'A nova data foi divulgada pela coordenação.', 'Confira o calendário atualizado no portal. Conteúdo fictício de demonstração.', NULL, 'comunicados', FALSE, interval '3 days 2 hours'),
    ('Curso de Git e GitHub para iniciantes', 'demo-curso-git', 'Versionamento de código na prática, em quatro encontros.', 'O curso ensina commits, branches e envio de alterações ao repositório. Conteúdo fictício de demonstração.', 'assets/img/noticias/matriculas-cursos-qualificacao.webp', 'cursos', FALSE, interval '8 hours'),
    ('Curso de SQL com PostgreSQL', 'demo-curso-sql', 'Do SELECT ao JOIN, com exercícios guiados.', 'Os exercícios usam um banco de exemplo criado para a turma. Conteúdo fictício de demonstração.', NULL, 'cursos', FALSE, interval '2 days 4 hours')
) AS d(titulo, slug, resumo, corpo, imagem, categoria, destaque, atraso)
JOIN categorias cat ON cat.slug = d.categoria
JOIN usuarios u ON u.email = 'admin@portalsi.local'
WHERE NOT EXISTS (SELECT 1 FROM conteudos c WHERE c.slug = d.slug);

-- Um único destaque na Home: o primeiro item de demonstração.
UPDATE conteudos SET destaque = FALSE WHERE slug IN ('matricula-aberta', 'resultado-pesquisa');

-- Os itens do seed ficam mais antigos que o conteúdo de demonstração (a faixa AGORA mostra o mais recente).
UPDATE conteudos SET publicado_em = LEAST(publicado_em, now() - interval '8 days') WHERE slug IN ('matricula-aberta', 'resultado-pesquisa');
