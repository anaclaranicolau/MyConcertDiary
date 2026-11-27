-- Execute uma vez no banco existente, com uma conta autorizada a alterar tabelas.
-- Em instalacoes antigas, apenas a primeira conta podia cadastrar shows.
ALTER TABLE shows ADD COLUMN id_usuario INT NULL AFTER id_artista;

UPDATE shows
SET id_usuario = (SELECT MIN(id_usuario) FROM usuarios)
WHERE id_usuario IS NULL;

ALTER TABLE shows
    MODIFY id_usuario INT NOT NULL,
    ADD CONSTRAINT fk_shows_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario);
