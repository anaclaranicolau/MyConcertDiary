<?php

// ==========================================
// IMPORTAÇÃO DOS ARQUIVOS NECESSÁRIOS
// ==========================================

// Conexão com o banco de dados
require __DIR__ . '/../config/conexao.php';

// Funções auxiliares do sistema
require __DIR__ . '/../config/funcoes.php';


// ==========================================
// AUTENTICAÇÃO DO ADMINISTRADOR
// ==========================================

// Permite o acesso apenas ao administrador
exigirLogin();


// ==========================================
// VALIDAÇÃO DO MÉTODO HTTP
// ==========================================

// Permite apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    exit('Método não permitido.');

}


// ==========================================
// PROTEÇÃO CONTRA CSRF
// ==========================================

// Verifica o token de segurança
verificarToken();


// ==========================================
// VALIDAÇÃO DO ID DO SHOW
// ==========================================

// Recupera o ID enviado pelo formulário
$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

// Verifica se o ID é válido
if (!$id) {

    http_response_code(400);

    exit('ID inválido.');

}


// ==========================================
// BUSCA DO SHOW
// ==========================================

// Recupera as informações do show
$usuarioId = (int) $_SESSION['admin_id'];
$s = buscarShow($pdo, $id, $usuarioId);

// Verifica se o show existe
if (!$s) {

    http_response_code(404);

    exit('Show não encontrado.');

}


// ==========================================
// IDENTIFICAÇÃO DOS ARQUIVOS
// ==========================================

// Recupera a foto de capa e a foto do ingresso
$arquivos = array_filter([
    $s['foto_capa'],
    $s['foto_ingresso']
]);

// Recupera as fotos da galeria
foreach (imagensDoShow($pdo, $id) as $m) {

    if ($m['tipo'] === 'foto') {

        $arquivos[] = $m['caminho_arquivo'];

    }

}


// ==========================================
// EXCLUSÃO DO SHOW NO BANCO DE DADOS
// ==========================================

// Prepara a consulta de exclusão
$q = $pdo->prepare(
    'DELETE FROM shows WHERE id_show = ? AND id_usuario = ?'
);

// Executa a exclusão
$q->execute([$id, $usuarioId]);


// ==========================================
// REMOÇÃO DOS ARQUIVOS DE IMAGEM
// ==========================================

// Remove as imagens armazenadas no servidor
foreach ($arquivos as $c) {

    removerArquivo($c);

}


// ==========================================
// REDIRECIONAMENTO
// ==========================================

// Retorna ao painel administrativo
header('Location: /admin/dashboard.php');

exit;
