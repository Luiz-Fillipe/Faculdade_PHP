INSERT INTO categorias (nome, slug, descricao, icone, ordem)
VALUES
('Notícias', 'noticias', 'Notícias da instituição e da comunidade acadêmica.', '📰', 1),
('Comunicados', 'comunicados', 'Avisos e comunicados institucionais.', '📢', 2),
('Cursos', 'cursos', 'Conteúdos relacionados aos cursos.', '📚', 3),
('Pesquisa', 'pesquisa', 'Pesquisas e projetos acadêmicos.', '🔬', 4),
('Extensão', 'extensao', 'Ações e projetos de extensão.', '🤝', 5)
ON CONFLICT (slug) DO NOTHING;
