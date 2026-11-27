<?php

// ==========================================
// IMPORTAÇÃO DOS ARQUIVOS NECESSÁRIOS
// ==========================================

// Funções auxiliares do sistema
require __DIR__ . '/../config/funcoes.php';


// ==========================================
// INICIALIZAÇÃO DA SESSÃO
// ==========================================

// Inicia ou recupera a sessão atual
iniciarSessao();


// ==========================================
// VALIDAÇÃO DO MÉTODO HTTP
// ==========================================

// Permite apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    exit('Método inválido.');

}


// ==========================================
// PROTEÇÃO CONTRA CSRF
// ==========================================

// Verifica o token de segurança
verificarToken();


// ==========================================
// ENCERRAMENTO DA SESSÃO
// ==========================================

// Remove todas as variáveis da sessão
$_SESSION = [];

// Destrói os dados da sessão no servidor
session_destroy();


// ==========================================
// REDIRECIONAMENTO
// ==========================================

// Redireciona para a página inicial do site
header('Location: /');

exit;
