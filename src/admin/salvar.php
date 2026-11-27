<?php

// ==========================================
// IMPORTAÇÃO DOS ARQUIVOS NECESSÁRIOS
// ==========================================

// Funções responsáveis pelo upload de imagens
require_once __DIR__ . '/../config/upload.php';


// ==========================================
// FUNÇÃO PRINCIPAL DE SALVAMENTO
// ==========================================

// Processamento compartilhado entre criação e edição
function salvarShow(PDO $pdo, int $usuarioId, ?int $id): int
{

    // ==========================================
    // PROTEÇÃO CONTRA CSRF
    // ==========================================

    verificarToken();


    // ==========================================
    // RECUPERAÇÃO DOS DADOS DO FORMULÁRIO
    // ==========================================

    // Informações do show
    $artista = trim($_POST['artista'] ?? '');
    $turne = trim($_POST['turne'] ?? '');
    $data = $_POST['data_show'] ?? '';
    $local = trim($_POST['local_show'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $setor = trim($_POST['setor'] ?? '');
    $acompanhantes = trim($_POST['acompanhantes'] ?? '');
    $look = trim($_POST['look_descricao'] ?? '');

    // Informações do diário
    $relato = trim($_POST['relato'] ?? '');
    $nota = $_POST['nota'] ?? '';
    $favorita = trim($_POST['musica_favorita'] ?? '');
    $momento = trim($_POST['momento_marcante'] ?? '');


    // ==========================================
    // VALIDAÇÃO DOS CAMPOS OBRIGATÓRIOS
    // ==========================================

    // Converte e valida a data
    $dt = DateTime::createFromFormat('!Y-m-d', $data);

    // Verifica os campos obrigatórios
    if (
        !$artista ||
        !$local ||
        !$cidade ||
        !$dt ||
        $dt->format('Y-m-d') !== $data
    ) {
        throw new RuntimeException(
            'Preencha os campos obrigatórios.'
        );
    }


    // ==========================================
    // VALIDAÇÃO DO TAMANHO DOS CAMPOS
    // ==========================================

    foreach (
        [
            [$artista, 150],
            [$turne, 150],
            [$local, 150],
            [$cidade, 100],
            [$setor, 100],
            [$favorita, 150]
        ] as [$valor, $limite]
    ) {

        if (mb_strlen($valor) > $limite) {

            throw new RuntimeException(
                'Um campo ultrapassou o tamanho permitido.'
            );

        }

    }


    // ==========================================
    // VALIDAÇÃO DA NOTA
    // ==========================================

    // A nota deve estar entre 1 e 5
    if (
        $nota !== '' &&
        (
            !ctype_digit((string) $nota) ||
            (int) $nota < 1 ||
            (int) $nota > 5
        )
    ) {

        throw new RuntimeException(
            'Nota inválida.'
        );

    }


    // ==========================================
    // PROCESSAMENTO DA SETLIST
    // ==========================================

    // Separa as músicas por quebra de linha
    $musicas = array_values(
        array_filter(
            array_map(
                'trim',
                preg_split(
                    '/\r\n|\r|\n/',
                    $_POST['setlist'] ?? ''
                )
            ),
            fn($v) => $v !== ''
        )
    );

    // Limita a quantidade de músicas
    if (count($musicas) > 100) {

        throw new RuntimeException(
            'Máximo de 100 músicas.'
        );

    }

    // Verifica o tamanho do nome das músicas
    foreach ($musicas as $m) {

        if (mb_strlen($m) > 150) {

            throw new RuntimeException(
                'Nome de música muito longo.'
            );

        }

    }


    // ==========================================
    // PROCESSAMENTO DOS LINKS DE VÍDEOS
    // ==========================================

    // Separa os links por quebra de linha
    $videos = array_values(
        array_filter(
            array_map(
                'trim',
                preg_split(
                    '/\r\n|\r|\n/',
                    $_POST['videos'] ?? ''
                )
            )
        )
    );

    // Limita a quantidade de vídeos
    if (count($videos) > 20) {

        throw new RuntimeException(
            'Máximo de 20 links de vídeos.'
        );

    }

    // Valida os links
    foreach ($videos as $v) {

        if (
            strlen($v) > 500 ||
            !filter_var($v, FILTER_VALIDATE_URL) ||
            parse_url($v, PHP_URL_SCHEME) !== 'https'
        ) {

            throw new RuntimeException(
                'Use links de vídeos HTTPS válidos.'
            );

        }

    }


    // ==========================================
    // CONTROLE DOS ARQUIVOS
    // ==========================================

    // Arquivos adicionados durante o salvamento
    $novos = [];

    // Arquivos que serão removidos após salvar
    $removerDepois = [];


    // ==========================================
    // INÍCIO DA TRANSAÇÃO
    // ==========================================

    try {

        $pdo->beginTransaction();


        // ==========================================
        // CADASTRO OU BUSCA DO ARTISTA
        // ==========================================

        // Procura o artista no banco de dados
        $q = $pdo->prepare(
            'SELECT id_artista
             FROM artistas
             WHERE nome = ?'
        );

        $q->execute([$artista]);

        $artistaId = $q->fetchColumn();

        // Cadastra o artista caso não exista
        if (!$artistaId) {

            $q = $pdo->prepare(
                'INSERT INTO artistas (nome)
                 VALUES (?)'
            );

            $q->execute([$artista]);

            $artistaId = $pdo->lastInsertId();

        }


        // ==========================================
        // EDIÇÃO DE UM SHOW EXISTENTE
        // ==========================================

        if ($id) {

            // Recupera o show anterior
            $anterior = buscarShow($pdo, $id, $usuarioId);

            // Verifica se o show existe
            if (!$anterior) {

                throw new RuntimeException(
                    'Show não encontrado.'
                );

            }

            // Atualiza as informações do show
            $q = $pdo->prepare(
                'UPDATE shows
                 SET
                    id_artista = ?,
                    turne = ?,
                    data_show = ?,
                    local_show = ?,
                    cidade = ?,
                    setor = ?,
                    acompanhantes = ?,
                    look_descricao = ?
                 WHERE id_show = ? AND id_usuario = ?'
            );

            $q->execute([
                $artistaId,
                $turne ?: null,
                $data,
                $local,
                $cidade,
                $setor ?: null,
                $acompanhantes ?: null,
                $look ?: null,
                $id,
                $usuarioId
            ]);

        } else {

            // ==========================================
            // CADASTRO DE UM NOVO SHOW
            // ==========================================

            $q = $pdo->prepare(
                'INSERT INTO shows (
                    id_usuario,
                    id_artista,
                    turne,
                    data_show,
                    local_show,
                    cidade,
                    setor,
                    acompanhantes,
                    look_descricao
                 )
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $q->execute([
                $usuarioId,
                $artistaId,
                $turne ?: null,
                $data,
                $local,
                $cidade,
                $setor ?: null,
                $acompanhantes ?: null,
                $look ?: null
            ]);

            // Recupera o ID do show cadastrado
            $id = (int) $pdo->lastInsertId();

        }


        // ==========================================
        // SALVAMENTO DAS MEMÓRIAS
        // ==========================================

        // Insere ou atualiza as informações do diário
        $q = $pdo->prepare(
            'INSERT INTO memorias (
                id_show,
                relato,
                nota,
                musica_favorita,
                momento_marcante
             )
             VALUES (?, ?, ?, ?, ?)

             ON DUPLICATE KEY UPDATE
                relato = VALUES(relato),
                nota = VALUES(nota),
                musica_favorita = VALUES(musica_favorita),
                momento_marcante = VALUES(momento_marcante)'
        );

        $q->execute([
            $id,
            $relato ?: null,
            $nota === '' ? null : (int) $nota,
            $favorita ?: null,
            $momento ?: null
        ]);


        // ==========================================
        // ATUALIZAÇÃO DA SETLIST
        // ==========================================

        // Remove a setlist anterior
        $pdo->prepare(
            'DELETE FROM setlist WHERE id_show = ?'
        )->execute([$id]);

        // Prepara a inserção das músicas
        $q = $pdo->prepare(
            'INSERT INTO setlist (
                id_show,
                nome_musica,
                ordem
             )
             VALUES (?, ?, ?)'
        );

        // Insere as músicas na ordem informada
        foreach ($musicas as $i => $m) {

            $q->execute([
                $id,
                $m,
                $i + 1
            ]);

        }


        // ==========================================
        // FOTO DE CAPA E FOTO DO INGRESSO
        // ==========================================

        foreach (
            [
                'foto_capa' => 'capas',
                'foto_ingresso' => 'ingressos'
            ] as $campo => $pasta
        ) {

            // Tenta salvar a nova imagem
            $novo = salvarImagem(
                $_FILES[$campo] ?? [],
                $pasta
            );

            if ($novo) {

                // Registra o novo arquivo
                $novos[] = $novo;

                // Recupera o arquivo anterior
                $q = $pdo->prepare(
                    "SELECT $campo
                     FROM shows
                     WHERE id_show = ?"
                );

                $q->execute([$id]);

                $ant = $q->fetchColumn();

                // Agenda a remoção da imagem anterior
                if ($ant) {
                    $removerDepois[] = $ant;
                }

                // Atualiza a imagem no banco
                $pdo->prepare(
                    "UPDATE shows
                     SET $campo = ?
                     WHERE id_show = ?"
                )->execute([
                    $novo,
                    $id
                ]);

            } elseif (
                !empty(
                    $_POST[
                        $campo === 'foto_capa'
                            ? 'remover_capa'
                            : 'remover_ingresso'
                    ]
                )
            ) {

                // Recupera a imagem que será removida
                $q = $pdo->prepare(
                    "SELECT $campo
                     FROM shows
                     WHERE id_show = ?"
                );

                $q->execute([$id]);

                $ant = $q->fetchColumn();

                if ($ant) {
                    $removerDepois[] = $ant;
                }

                // Remove a referência da imagem no banco
                $pdo->prepare(
                    "UPDATE shows
                     SET $campo = NULL
                     WHERE id_show = ?"
                )->execute([$id]);

            }

        }


        // ==========================================
        // GALERIA DE FOTOS E VÍDEOS
        // ==========================================

        // Prepara a inserção de mídias
        $q = $pdo->prepare(
            'INSERT INTO midias (
                id_show,
                tipo,
                caminho_arquivo
             )
             VALUES (?, ?, ?)'
        );

        // Processa as fotos enviadas
        foreach (
            arquivosMultiplos($_FILES['fotos'] ?? []) as $f
        ) {

            // Salva a imagem na pasta de fotos
            $novo = salvarImagem($f, 'fotos');

            if ($novo) {

                // Registra o arquivo criado
                $novos[] = $novo;

                // Cadastra a foto no banco
                $q->execute([
                    $id,
                    'foto',
                    $novo
                ]);

            }

        }

        // Cadastra os links de vídeos
        foreach ($videos as $v) {

            $q->execute([
                $id,
                'video',
                $v
            ]);

        }


        // ==========================================
        // REMOÇÃO DE MÍDIAS EXISTENTES
        // ==========================================

        foreach (
            (array) ($_POST['remover_midias'] ?? []) as $mid
        ) {

            // Ignora identificadores inválidos
            if (!ctype_digit((string) $mid)) {
                continue;
            }

            // Busca a mídia vinculada ao show
            $s = $pdo->prepare(
                'SELECT
                    tipo,
                    caminho_arquivo
                 FROM midias
                 WHERE id_midia = ?
                   AND id_show = ?'
            );

            $s->execute([
                (int) $mid,
                $id
            ]);

            $m = $s->fetch();

            if ($m) {

                // Exclui o registro da mídia
                $pdo->prepare(
                    'DELETE FROM midias
                     WHERE id_midia = ?
                       AND id_show = ?'
                )->execute([
                    (int) $mid,
                    $id
                ]);

                // Agenda a remoção do arquivo físico
                if ($m['tipo'] === 'foto') {

                    $removerDepois[] = $m['caminho_arquivo'];

                }

            }

        }


        // ==========================================
        // CONFIRMAÇÃO DA TRANSAÇÃO
        // ==========================================

        // Confirma todas as alterações no banco
        $pdo->commit();


        // ==========================================
        // LIMPEZA DOS ARQUIVOS ANTIGOS
        // ==========================================

        // Remove os arquivos substituídos ou excluídos
        foreach ($removerDepois as $c) {

            removerArquivo($c);

        }

        // Retorna o ID do show salvo
        return $id;


    } catch (Throwable $e) {

        // ==========================================
        // TRATAMENTO DE ERROS
        // ==========================================

        // Desfaz as alterações no banco
        if ($pdo->inTransaction()) {

            $pdo->rollBack();

        }

        // Remove os arquivos criados durante a operação
        foreach ($novos as $c) {

            removerArquivo($c);

        }

        // Repassa o erro para a página responsável
        throw $e;

    }

}
