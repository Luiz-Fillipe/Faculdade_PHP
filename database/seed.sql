-- ============================================================
-- SEED — Portal SI (execute DEPOIS das suas CREATE TABLE)
-- ============================================================

-- Gera hash de senha compatível com password_verify() do PHP
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- ---------- USUÁRIOS (autores) ----------
INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES
    ('Administrador do Portal', 'admin@portalsi.edu.br',
     crypt('admin123', gen_salt('bf')), 'Admin'),
    ('Maria Souza', 'maria@portalsi.edu.br',
     crypt('maria123', gen_salt('bf')), 'Editor');

-- ---------- 6 CATEGORIAS (ordenadas pelo campo ordem) ----------
INSERT INTO categorias (nome, slug, descricao, icone, ordem, ativo) VALUES
    ('Notícias',    'noticias',    'Últimas notícias e acontecimentos da instituição.', '📰', 1, TRUE),
    ('Eventos',     'eventos',     'Agenda de eventos, palestras e workshops.',         '📅', 2, TRUE),
    ('Comunicados', 'comunicados', 'Comunicados oficiais e avisos importantes.',        '📢', 3, TRUE),
    ('Cursos',      'cursos',      'Cursos, disciplinas e materiais de ensino.',        '📚', 4, TRUE),
    ('Pesquisa',    'pesquisa',    'Pesquisas, projetos e publicações científicas.',    '🔬', 5, TRUE),
    ('Extensão',    'extensao',    'Ações e projetos de extensão comunitária.',         '🤝', 6, TRUE);

-- ---------- CONTEÚDOS ----------
INSERT INTO conteudos (titulo, slug, resumo, corpo, categoria_id, autor_id, status, destaque, publicado_em) VALUES
    ('Portal SI é lançado com nova plataforma digital',
     'portal-si-plataforma-digital',
     'A nova plataforma centraliza informações institucionais e oferece acesso rápido a publicações, eventos e comunicados oficiais.',
     E'A partir de agora, toda a comunidade acadêmica conta com um espaço único para acompanhar notícias, eventos e comunicados.\n\nA plataforma foi desenvolvida com PHP e PostgreSQL, seguindo padrões de acessibilidade e responsividade.',
     1, 1, 'publicado', TRUE, NOW() - INTERVAL '1 day'),

    ('Semana de Tecnologia: inscrições abertas',
     'semana-tecnologia-inscricoes',
     'Evento reunirá palestras, minicursos e feira de projetos. Inscrições gratuitas até o final do mês.',
     'A Semana de Tecnologia chega à 5ª edição com mais de 20 atividades gratuitas para a comunidade.',
     2, 2, 'publicado', FALSE, NOW() - INTERVAL '2 days'),

    ('Comunicado: novo horário de atendimento',
     'comunicado-horario-atendimento',
     'A partir da próxima segunda-feira, o atendimento ao público será das 8h às 17h, sem intervalo.',
     'A mudança busca ampliar o acesso da comunidade aos serviços da secretaria.',
     3, 1, 'publicado', FALSE, NOW() - INTERVAL '3 days'),

    ('Matrículas abertas para cursos de qualificação',
     'matriculas-cursos-qualificacao',
     'Programação, banco de dados e redes de computadores entre as opções disponíveis.',
     'As matrículas podem ser feitas presencialmente ou pelo e-mail da coordenação.',
     4, 2, 'publicado', FALSE, NOW() - INTERVAL '4 days'),

    ('Pesquisa aponta crescimento do uso de dados no setor público',
     'pesquisa-dados-setor-publico',
     'Estudo da instituição analisa a adoção de ferramentas de ciência de dados em órgãos públicos.',
     'A pesquisa ouviu 120 servidores de 15 órgãos diferentes ao longo de oito meses.',
     5, 1, 'publicado', FALSE, NOW() - INTERVAL '5 days'),

    ('Projeto de extensão leva tecnologia à comunidade',
     'extensao-tecnologia-comunidade',
     'Iniciativa oferece aulas de informática básica e suporte digital para moradores do bairro.',
     'O projeto atende cerca de 80 moradores por mês, com turmas aos sábados.',
     6, 2, 'publicado', FALSE, NOW() - INTERVAL '6 days'),

    ('Estudantes vencem prêmio nacional de inovação',
     'estudantes-premio-inovacao',
     'Equipe da instituição conquistou o primeiro lugar com aplicativo voltado à acessibilidade digital.',
     'O aplicativo facilita a navegação de pessoas com deficiência visual em ambientes internos.',
     1, 1, 'publicado', FALSE, NOW() - INTERVAL '7 days'),

    ('Manutenção programada do sistema neste fim de semana',
     'manutencao-programada-sistema',
     'O portal ficará indisponível no sábado, das 22h às 6h, para atualizações.',
     'Pedimos desculpas pelo inconveniente. O retorno está previsto para às 6h de domingo.',
     3, 2, 'publicado', FALSE, NOW() - INTERVAL '8 days');

-- ---------- TAGS ----------
INSERT INTO tags (nome, slug) VALUES
    ('Tecnologia', 'tecnologia'),
    ('Inovação',   'inovacao'),
    ('Ensino',     'ensino'),
    ('Pesquisa',   'pesquisa');

INSERT INTO conteudo_tags (conteudo_id, tag_id) VALUES
    (1, 1), (1, 2), (4, 3), (5, 4);

-- ---------- EVENTOS ----------
INSERT INTO eventos (titulo, descricao, data_inicio, data_fim, local, link_inscricao, autor_id, status) VALUES
    ('Semana de Tecnologia 2025',
     'Cinco dias de palestras, minicursos e feira de projetos.',
     NOW() + INTERVAL '10 days', NOW() + INTERVAL '14 days',
     'Auditório Central', 'https://exemplo.com/inscricao', 1, 'publicado'),

    ('Ciclo de Palestras: Carreiras em TI',
     'Profissionais do mercado compartilham experiências sobre as principais carreiras da área.',
     NOW() + INTERVAL '20 days', NULL,
     'Sala de Conferências', NULL, 2, 'publicado'),

    ('Feira de Profissões',
     'Alunos do ensino médio conhecem os cursos da instituição.',
     NOW() + INTERVAL '30 days', NOW() + INTERVAL '31 days',
     'Pátio Principal', NULL, 1, 'publicado');
