<?php

require __DIR__ . '/config/conexao.php';
require __DIR__ . '/config/funcoes.php';

exigirLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$referencia = filter_input(INPUT_GET, 'arquivo') ?? '';
$arquivo = caminhoFoto($referencia);
$show = $id && $arquivo ? buscarShow($pdo, $id, (int) $_SESSION['admin_id']) : false;

if (!$show) {
    http_response_code(404);
    exit;
}

if ($referencia !== $show['foto_capa'] && $referencia !== $show['foto_ingresso']) {
    $consulta = $pdo->prepare(
        "SELECT 1 FROM midias WHERE id_show = ? AND tipo = 'foto' AND caminho_arquivo = ?"
    );
    $consulta->execute([$id, $referencia]);
    if (!$consulta->fetchColumn()) {
        http_response_code(404);
        exit;
    }
}

$imagem = @fopen($arquivo, 'rb');
if ($imagem === false) {
    http_response_code(404);
    exit;
}

session_write_close();
header('Cache-Control: private, no-store');
header('X-Content-Type-Options: nosniff');
header('Content-Type: ' . match (pathinfo($arquivo, PATHINFO_EXTENSION)) {
    'jpg' => 'image/jpeg',
    'png' => 'image/png',
    'webp' => 'image/webp'
});
fpassthru($imagem);
fclose($imagem);
