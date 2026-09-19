CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    perfil VARCHAR(20) NOT NULL
        CHECK (perfil IN ('admin', 'editor', 'aluno')),
    status VARCHAR(20) NOT NULL DEFAULT 'ativo'
        CHECK (status IN ('ativo', 'inativo')),
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    descricao TEXT,
    icone VARCHAR(100),
    ordem INTEGER,
    ativo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE conteudos (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    resumo TEXT,
    corpo TEXT NOT NULL,
    imagem_capa VARCHAR(255),
    link_youtube VARCHAR(255),
    categoria_id INTEGER NOT NULL,
    autor_id INTEGER NOT NULL,
    status VARCHAR(20) NOT NULL,
    destaque BOOLEAN NOT NULL DEFAULT FALSE,
    publicado_em TIMESTAMP,
    CONSTRAINT fk_conteudo_categoria
        FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_conteudo_autor
        FOREIGN KEY (autor_id)
        REFERENCES usuarios(id)
        ON DELETE RESTRICT
);

CREATE TABLE tags (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE conteudo_tags (
    conteudo_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    PRIMARY KEY (conteudo_id, tag_id),
    CONSTRAINT fk_conteudo_tags_conteudo
        FOREIGN KEY (conteudo_id)
        REFERENCES conteudos(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_conteudo_tags_tag
        FOREIGN KEY (tag_id)
        REFERENCES tags(id)
        ON DELETE CASCADE
);

CREATE TABLE eventos (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descricao TEXT,
    data_inicio TIMESTAMP NOT NULL,
    data_fim TIMESTAMP,
    local VARCHAR(200),
    link_inscricao VARCHAR(255),
    autor_id INTEGER NOT NULL,
    status VARCHAR(20) NOT NULL,
    CONSTRAINT fk_evento_autor
        FOREIGN KEY (autor_id)
        REFERENCES usuarios(id)
        ON DELETE RESTRICT
);

CREATE TABLE midias (
    id SERIAL PRIMARY KEY,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho VARCHAR(500) NOT NULL,
    tipo_mime VARCHAR(100),
    tamanho_bytes BIGINT,
    enviado_por INTEGER NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_midia_usuario
        FOREIGN KEY (enviado_por)
        REFERENCES usuarios(id)
        ON DELETE RESTRICT
);

CREATE TABLE auditoria (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER NOT NULL,
    acao VARCHAR(100) NOT NULL,
    tabela_afetada VARCHAR(100),
    registro_id INTEGER,
    detalhes JSONB,
    ip_origem VARCHAR(45),
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_auditoria_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE RESTRICT
);

commit;
