<?php

// ==========================================
// IMPORTAÇÃO DOS ARQUIVOS NECESSÁRIOS
// ==========================================

// Conexão com o banco de dados
require __DIR__ . '/../config/conexao.php';

// Funções auxiliares do sistema
require __DIR__ . '/../config/funcoes.php';

// Função responsável por salvar o show
require __DIR__ . '/salvar.php';


// ==========================================
// AUTENTICAÇÃO
// ==========================================

// Permite o acesso apenas ao administrador
exigirLogin();


// ==========================================
// INICIALIZAÇÃO DAS VARIÁVEIS
// ==========================================

// Mensagem de erro
$erro = '';

// Dados do formulário
$dados = [];

// Indica que estamos cadastrando um novo show
$editando = false;

// Lista de mídias já cadastradas
$midiasAtuais = [];


// ==========================================
// PROCESSAMENTO DO FORMULÁRIO
// ==========================================

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        // Salva o novo show no banco de dados
        $id = salvarShow($pdo, (int) $_SESSION['admin_id'], null);

        // Redireciona para a página do show cadastrado
        header('Location: /show.php?id=' . $id);

        exit;

    } catch (Throwable $ex) {

        // Registra o erro no log do servidor
        error_log($ex->getMessage());

        // Define a mensagem que será exibida
        $erro = $ex instanceof RuntimeException
            ? $ex->getMessage()
            : 'Não foi possível salvar o show.';

        // Preserva os dados preenchidos no formulário
        $dados = $_POST;

    }

}


// ==========================================
// CONFIGURAÇÃO DA PÁGINA
// ==========================================

// Define o título da página
$titulo = 'Cadastrar show';

// Inclui o cabeçalho
include __DIR__ . '/../includes/header.php';

?>

<main class="container secao">

    <!-- CABEÇALHO DA PÁGINA -->
    <p class="sobretitulo">
        ♡ Nova memória ♡
    </p>

    <h1>
        Cadastrar show
    </h1>

    <p>
        Guarde todos os detalhes de uma noite especial.
    </p>

    <!-- MENSAGEM DE ERRO -->
    <?php if ($erro): ?>

        <div class="alert alert-danger">
            <?= e($erro) ?>
        </div>

    <?php endif; ?>

    <!-- FORMULÁRIO DE CADASTRO -->
    <?php
    include __DIR__ . '/formulario.php';
    ?>

</main>

<?php

// Inclui o rodapé
include __DIR__ . '/../includes/footer.php';

?>
