BEGIN;

DROP TABLE IF EXISTS auditoria CASCADE;
DROP TABLE IF EXISTS conteudo_tags CASCADE;
DROP TABLE IF EXISTS tags CASCADE;
DROP TABLE IF EXISTS midias CASCADE;
DROP TABLE IF EXISTS eventos CASCADE;
DROP TABLE IF EXISTS conteudos CASCADE;
DROP TABLE IF EXISTS categorias CASCADE;
DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(254) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    perfil VARCHAR(20) NOT NULL CHECK (perfil IN ('Admin', 'Professor', 'Aluno')),
    curso VARCHAR(150),
    status VARCHAR(20) NOT NULL DEFAULT 'ativo' CHECK (status IN ('ativo', 'inativo')),
    criado_em TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT,
    icone VARCHAR(100),
    ordem INTEGER NOT NULL DEFAULT 0,
    ativo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE conteudos (
    id BIGSERIAL PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    resumo TEXT NOT NULL,
    corpo TEXT NOT NULL,
    imagem_capa VARCHAR(255),
    link_youtube VARCHAR(255),
    categoria_id BIGINT NOT NULL REFERENCES categorias(id) ON DELETE RESTRICT,
    autor_id BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
    status VARCHAR(20) NOT NULL DEFAULT 'rascunho' CHECK (status IN ('rascunho', 'publicado', 'arquivado')),
    destaque BOOLEAN NOT NULL DEFAULT FALSE,
    publicado_em TIMESTAMPTZ
);

CREATE TABLE tags (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE conteudo_tags (
    conteudo_id BIGINT NOT NULL REFERENCES conteudos(id) ON DELETE CASCADE,
    tag_id BIGINT NOT NULL REFERENCES tags(id) ON DELETE CASCADE,
    PRIMARY KEY (conteudo_id, tag_id)
);

CREATE TABLE eventos (
    id BIGSERIAL PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descricao TEXT NOT NULL,
    imagem VARCHAR(255),
    data_inicio TIMESTAMPTZ NOT NULL,
    data_fim TIMESTAMPTZ,
    local VARCHAR(200),
    link_inscricao VARCHAR(255),
    curso VARCHAR(150),
    autor_id BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
    status VARCHAR(20) NOT NULL DEFAULT 'rascunho' CHECK (status IN ('rascunho', 'publicado', 'arquivado'))
);

CREATE TABLE midias (
    id BIGSERIAL PRIMARY KEY,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho VARCHAR(500) NOT NULL,
    tipo_mime VARCHAR(100),
    tamanho_bytes BIGINT,
    enviado_por BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
    criado_em TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE auditoria (
    id BIGSERIAL PRIMARY KEY,
    usuario_id BIGINT REFERENCES usuarios(id) ON DELETE SET NULL,
    acao VARCHAR(100) NOT NULL,
    tabela_afetada VARCHAR(100),
    registro_id BIGINT,
    detalhes JSONB,
    ip_origem VARCHAR(45),
    criado_em TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_conteudos_publicacao ON conteudos(status, publicado_em DESC);
CREATE INDEX idx_eventos_publicacao ON eventos(status, data_inicio);
CREATE INDEX idx_usuarios_status ON usuarios(status);

INSERT INTO categorias (nome, slug, descricao, icone, ordem) VALUES
('Notícias', 'noticias', 'Notícias da instituição e da comunidade acadêmica.', '📰', 1),
('Comunicados', 'comunicados', 'Avisos e comunicados institucionais.', '📢', 2),
('Cursos', 'cursos', 'Conteúdos relacionados aos cursos.', '📚', 3),
('Pesquisa', 'pesquisa', 'Pesquisas e projetos acadêmicos.', '🔬', 4),
('Extensão', 'extensao', 'Ações e projetos de extensão.', '🤝', 5);

COMMIT;
