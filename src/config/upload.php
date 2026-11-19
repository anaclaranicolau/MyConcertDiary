
<?php

// ==========================================
// UPLOAD DE IMAGENS
// ==========================================

// Valida e salva uma imagem no servidor
function salvarImagem(array $arq, string $pasta): ?string
{
    // Verifica se nenhum arquivo foi enviado
    if (
        ($arq['error'] ?? UPLOAD_ERR_NO_FILE)
        === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    // Verifica se ocorreu algum erro no upload
    if (
        ($arq['error'] ?? -1)
        !== UPLOAD_ERR_OK
    ) {
        throw new RuntimeException(
            'Erro ao enviar imagem.'
        );
    }

    // Verifica se a pasta de destino é permitida
    if (
        !in_array(
            $pasta,
            ['capas', 'ingressos', 'fotos'],
            true
        )
    ) {
        throw new RuntimeException(
            'Pasta inválida.'
        );
    }

    // Limita o tamanho da imagem a 5 MB
    if (
        ($arq['size'] ?? 0)
        > 5 * 1024 * 1024
    ) {
        throw new RuntimeException(
            'Cada imagem deve ter até 5 MB.'
        );
    }

    // Identifica o tipo MIME do arquivo
    $mime = (new finfo(FILEINFO_MIME_TYPE))
        ->file($arq['tmp_name']);

    // Formatos de imagem permitidos
    $tipos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    // Verifica se o arquivo é uma imagem válida
    if (
        !isset($tipos[$mime]) ||
        @getimagesize($arq['tmp_name']) === false
    ) {
        throw new RuntimeException(
            'Imagem inválida (use JPG, PNG ou WebP).'
        );
    }

    // Gera um nome aleatório para a imagem
    $nome = bin2hex(
        random_bytes(16)
    ) . '.' . $tipos[$mime];

    // Define o caminho de destino
    $destino = dirname(__DIR__)
        . '/uploads/'
        . $pasta
        . '/'
        . $nome;

    // Move a imagem para a pasta de destino
    if (
        !move_uploaded_file(
            $arq['tmp_name'],
            $destino
        )
    ) {
        throw new RuntimeException(
            'Não foi possível salvar a imagem.'
        );
    }

    // Retorna o caminho da imagem salva
    return '/uploads/'
        . $pasta
        . '/'
        . $nome;
}


// ==========================================
// UPLOAD DE MÚLTIPLAS IMAGENS
// ==========================================

// Organiza os arquivos enviados em um array
function arquivosMultiplos(array $f): array
{
    // Verifica se existem arquivos enviados
    if (
        !isset($f['name']) ||
        !is_array($f['name'])
    ) {
        return [];
    }

    // Limita o envio a 20 fotos
    if (count($f['name']) > 20) {
        throw new RuntimeException(
            'Máximo de 20 fotos por envio.'
        );
    }

    // Array que armazenará os arquivos
    $resultado = [];

    // Percorre todos os arquivos enviados
    foreach ($f['name'] as $i => $nome) {

        $resultado[] = [
            'name'     => $nome,
            'tmp_name' => $f['tmp_name'][$i],
            'size'     => $f['size'][$i],
            'error'    => $f['error'][$i]
        ];

    }

    // Retorna a lista organizada de arquivos
    return $resultado;
}
