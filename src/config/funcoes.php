
<?php

// ==========================================
// FUNÇÕES DE SEGURANÇA
// ==========================================

// Protege a exibição de textos no HTML
function e($s): string
{
    return htmlspecialchars(
        (string) ($s ?? ''),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


// ==========================================
// GERENCIAMENTO DE SESSÃO
// ==========================================

// Inicia uma sessão caso ainda não exista
function iniciarSessao(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {

        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'use_strict_mode' => true
        ]);

    }
}


// ==========================================
// PROTEÇÃO CONTRA CSRF
// ==========================================

// Gera ou recupera o token CSRF da sessão
function token(): string
{
    iniciarSessao();

    return $_SESSION['csrf'] ??= bin2hex(
        random_bytes(32)
    );
}

// Verifica se o token enviado é válido
function verificarToken(): void
{
    iniciarSessao();

    if (!hash_equals(
        token(),
        (string) ($_POST['csrf'] ?? '')
    )) {

        http_response_code(403);

        exit('Solicitação inválida.');
    }
}


// ==========================================
// AUTENTICAÇÃO DO ADMINISTRADOR
// ==========================================

// Impede o acesso de usuários não autenticados
function exigirLogin(): void
{
    iniciarSessao();

    if (empty($_SESSION['admin_id'])) {

        header('Location: /admin/login.php');

        exit;
    }
}


// ==========================================
// FORMATAÇÃO DE DATAS
// ==========================================

// Converte uma data para o formato brasileiro
function dataBR($d): string
{
    return $d
        ? date('d/m/Y', strtotime($d))
        : '';
}


// ==========================================
// CONSULTAS AO BANCO DE DADOS
// ==========================================

// Busca as informações completas de um show
function buscarShow(PDO $pdo, int $id): array|false
{
    $q = $pdo->prepare(
        'SELECT
            s.*,
            a.nome AS artista,
            m.relato,
            m.nota,
            m.musica_favorita,
            m.momento_marcante
         FROM shows s
         JOIN artistas a
            ON a.id_artista = s.id_artista
         LEFT JOIN memorias m
            ON m.id_show = s.id_show
         WHERE s.id_show = ?'
    );

    $q->execute([$id]);

    return $q->fetch();
}


// ==========================================
// GALERIA DE FOTOS E VÍDEOS
// ==========================================

// Busca todas as mídias de um show
function imagensDoShow(PDO $pdo, int $id): array
{
    $q = $pdo->prepare(
        'SELECT *
         FROM midias
         WHERE id_show = ?
         ORDER BY id_midia'
    );

    $q->execute([$id]);

    return $q->fetchAll();
}


// ==========================================
// SETLIST DOS SHOWS
// ==========================================

// Busca as músicas na ordem cadastrada
function musicasDoShow(PDO $pdo, int $id): array
{
    $q = $pdo->prepare(
        'SELECT *
         FROM setlist
         WHERE id_show = ?
         ORDER BY ordem'
    );

    $q->execute([$id]);

    return $q->fetchAll();
}


// ==========================================
// GERENCIAMENTO DE ARQUIVOS
// ==========================================

// Remove um arquivo de imagem armazenado no servidor
function removerArquivo(?string $caminho): void
{
    // Verifica se o caminho é válido e permitido
    if (
        !$caminho ||
        !preg_match(
            '~^/uploads/(capas|ingressos|fotos)/[a-f0-9]{32}\.(jpg|png|webp)$~',
            $caminho
        )
    ) {
        return;
    }

    // Monta o caminho completo do arquivo
    $arquivo = dirname(__DIR__) . $caminho;

    // Remove o arquivo caso ele exista
    if (is_file($arquivo)) {
        unlink($arquivo);
    }
}
