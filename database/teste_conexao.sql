-- Tabela de teste da atividade Aiven.io (Parte 4 da apostila).
-- Execute no banco do grupo (pgAdmin ou PG Studio do Aiven).
-- Seguro para repetir: não apaga nada e só insere o registro se a tabela estiver vazia.

SELECT version();
SELECT current_database();
SELECT current_user;

CREATE TABLE IF NOT EXISTS teste (
    id SERIAL PRIMARY KEY,
    descricao VARCHAR(100) NOT NULL
);

INSERT INTO teste (descricao)
SELECT 'Primeiro registro do grupo'
WHERE NOT EXISTS (SELECT 1 FROM teste);

SELECT * FROM teste;
