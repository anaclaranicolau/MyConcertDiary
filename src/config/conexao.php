<?php

// Cria a conexão com o banco de dados MySQL
$pdo = new PDO(

    // Configurações do banco de dados
    'mysql:host=' . (getenv('DB_HOST') ?: 'db') .
    ';dbname=' . (getenv('DB_NAME') ?: 'my_concert_diary') .
    ';charset=utf8mb4',

    // Usuário do banco de dados
    getenv('DB_USER') ?: 'concert_user',

    // Senha do banco de dados
    getenv('DB_PASSWORD') ?: 'concert123',

    // Configurações do PDO
    [
        // Exibe erros através de exceções
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

        // Retorna os resultados como arrays associativos
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

        // Utiliza consultas preparadas nativas
        PDO::ATTR_EMULATE_PREPARES => false
    ]
);
