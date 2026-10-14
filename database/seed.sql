INSERT INTO categorias (nome, slug, descricao, icone, ordem)
VALUES
('Notícias', 'noticias', 'Notícias da instituição e da comunidade acadêmica.', '📰', 1),
('Comunicados', 'comunicados', 'Avisos e comunicados institucionais.', '📢', 2),
('Cursos', 'cursos', 'Conteúdos relacionados aos cursos.', '📚', 3),
('Pesquisa', 'pesquisa', 'Pesquisas e projetos acadêmicos.', '🔬', 4),
('Extensão', 'extensao', 'Ações e projetos de extensão.', '🤝', 5)
ON CONFLICT (slug) DO NOTHING;

INSERT INTO conteudos (titulo, slug, resumo, corpo, categoria_id, autor_id, status, destaque, publicado_em, imagem_capa, link_youtube)
SELECT 'Matrícula aberta para o próximo semestre', 'matricula-aberta', 'Confira os prazos e documentos necessários.', 'Texto completo da notícia de matrícula.', cat.id, u.id, 'publicado', TRUE, now(), NULL, 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
FROM categorias cat, usuarios u
WHERE cat.slug = 'noticias'
  AND u.email = 'admin@portalsi.local'
  AND NOT EXISTS (SELECT 1 FROM conteudos WHERE slug = 'matricula-aberta');

INSERT INTO conteudos (titulo, slug, resumo, corpo, categoria_id, autor_id, status, destaque, publicado_em)
SELECT 'Resultado da pesquisa de satisfação', 'resultado-pesquisa', 'Veja os principais resultados.', 'Texto completo da pesquisa de satisfação.', cat.id, u.id, 'publicado', FALSE, now() - interval '1 day'
FROM categorias cat, usuarios u
WHERE cat.slug = 'pesquisa'
  AND u.email = 'admin@portalsi.local'
  AND NOT EXISTS (SELECT 1 FROM conteudos WHERE slug = 'resultado-pesquisa');

INSERT INTO eventos (titulo, descricao, data_inicio, data_fim, local, link_inscricao, autor_id, status)
SELECT 'Palestra de Inteligência Artificial', 'Palestra aberta ao público sobre IA.', now() + interval '3 day', now() + interval '3 day' + interval '2 hour', 'Auditório Central', 'https://forms.gle/exemplo', u.id, 'publicado'
FROM usuarios u
WHERE u.email = 'admin@portalsi.local'
  AND NOT EXISTS (SELECT 1 FROM eventos WHERE titulo = 'Palestra de Inteligência Artificial');

INSERT INTO eventos (titulo, descricao, data_inicio, local, autor_id, status)
SELECT 'Semana acadêmica', 'Evento institucional já encerrado, usado como exemplo de evento antigo.', now() - interval '10 day', 'Online', u.id, 'publicado'
FROM usuarios u
WHERE u.email = 'admin@portalsi.local'
  AND NOT EXISTS (SELECT 1 FROM eventos WHERE titulo = 'Semana acadêmica');
