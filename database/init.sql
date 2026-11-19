
-- ==========================================
-- BANCO DE DADOS: MY CONCERT DIARY
-- ==========================================

USE my_concert_diary;


-- ==========================================
-- TABELA: USUARIOS
-- ==========================================

-- Armazena os dados dos administradores
-- responsáveis pelo gerenciamento do site.

CREATE TABLE IF NOT EXISTS usuarios (

    id_usuario INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL,

    email VARCHAR(150) NOT NULL UNIQUE,

    senha_hash VARCHAR(255) NOT NULL

);


-- ==========================================
-- TABELA: ARTISTAS
-- ==========================================

-- Armazena os artistas e bandas cadastrados.

CREATE TABLE IF NOT EXISTS artistas (

    id_artista INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(150) NOT NULL UNIQUE

);


-- ==========================================
-- TABELA: SHOWS
-- ==========================================

-- Armazena as informações principais
-- de cada show registrado no diário.

CREATE TABLE IF NOT EXISTS shows (

    id_show INT AUTO_INCREMENT PRIMARY KEY,

    id_artista INT NOT NULL,

    turne VARCHAR(150),

    data_show DATE NOT NULL,

    local_show VARCHAR(150) NOT NULL,

    cidade VARCHAR(100) NOT NULL,

    setor VARCHAR(100),

    acompanhantes TEXT,

    look_descricao TEXT,

    foto_capa VARCHAR(255),

    foto_ingresso VARCHAR(255),

    FOREIGN KEY (id_artista)
        REFERENCES artistas(id_artista)

);


-- ==========================================
-- TABELA: MEMORIAS
-- ==========================================

-- Armazena os relatos e avaliações pessoais
-- relacionados a cada show.

CREATE TABLE IF NOT EXISTS memorias (

    id_memoria INT AUTO_INCREMENT PRIMARY KEY,

    id_show INT NOT NULL UNIQUE,

    relato TEXT,

    nota TINYINT CHECK (
        nota BETWEEN 1 AND 5
    ),

    musica_favorita VARCHAR(150),

    momento_marcante TEXT,

    FOREIGN KEY (id_show)
        REFERENCES shows(id_show)
        ON DELETE CASCADE

);


-- ==========================================
-- TABELA: MIDIAS
-- ==========================================

-- Armazena as fotos e os vídeos
-- associados aos shows.

CREATE TABLE IF NOT EXISTS midias (

    id_midia INT AUTO_INCREMENT PRIMARY KEY,

    id_show INT NOT NULL,

    tipo ENUM('foto', 'video') NOT NULL,

    caminho_arquivo VARCHAR(500) NOT NULL,

    legenda VARCHAR(255),

    FOREIGN KEY (id_show)
        REFERENCES shows(id_show)
        ON DELETE CASCADE

);


-- ==========================================
-- TABELA: SETLIST
-- ==========================================

-- Armazena as músicas apresentadas
-- em cada show, respeitando sua ordem.

CREATE TABLE IF NOT EXISTS setlist (

    id_musica INT AUTO_INCREMENT PRIMARY KEY,

    id_show INT NOT NULL,

    nome_musica VARCHAR(150) NOT NULL,

    ordem INT NOT NULL,

    FOREIGN KEY (id_show)
        REFERENCES shows(id_show)
        ON DELETE CASCADE,

    UNIQUE (id_show, ordem)

);
