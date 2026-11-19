
<?php

// ==========================================
// IMPORTAÇÃO DOS ARQUIVOS NECESSÁRIOS
// ==========================================

// Conexão com o banco de dados
require __DIR__ . '/../config/conexao.php';

// Funções auxiliares do sistema
require __DIR__ . '/../config/funcoes.php';

// Função responsável por salvar as alterações
require __DIR__ . '/salvar.php';


// ==========================================
// AUTENTICAÇÃO DO ADMINISTRADOR
// ==========================================

// Permite o acesso apenas ao administrador
exigirLogin();


// ==========================================
// IDENTIFICAÇÃO DO SHOW
// ==========================================

// Recupera o ID do show pela URL
$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

// Busca as informações do show
$s = $id
    ? buscarShow($pdo, $id)
    : false;

// Verifica se o show existe
if (!$s) {

    http_response_code(404);

    exit('Show não encontrado.');

}


// ==========================================
// INICIALIZAÇÃO DAS VARIÁVEIS
// ==========================================

// Mensagem de erro
$erro = '';

// Indica que o formulário está no modo de edição
$editando = true;

// Recupera as fotos e os vídeos cadastrados
$midiasAtuais = imagensDoShow($pdo, $id);

// Preenche o formulário com os dados existentes
$dados = $s;


// ==========================================
// CARREGAMENTO DA SETLIST
// ==========================================

// Recupera as músicas e separa cada uma por linha
$dados['setlist'] = implode(
    "\n",
    array_column(
        musicasDoShow($pdo, $id),
        'nome_musica'
    )
);


// ==========================================
// PROCESSAMENTO DO FORMULÁRIO
// ==========================================

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        // Salva as alterações do show
        salvarShow($pdo, $id);

        // Redireciona para a página do show atualizado
        header('Location: /show.php?id=' . $id);

        exit;

    } catch (Throwable $ex) {

        // Registra o erro no log do servidor
        error_log($ex->getMessage());

        // Define a mensagem de erro
        $erro = $ex instanceof RuntimeException
            ? $ex->getMessage()
            : 'Não foi possível salvar as alterações.';

        // Mantém os dados preenchidos pelo usuário
        $dados = array_merge(
            $dados,
            $_POST
        );

    }

}


// ==========================================
// CONFIGURAÇÃO DA PÁGINA
// ==========================================

// Define o título da página
$titulo = 'Editar show';

// Inclui o cabeçalho
include __DIR__ . '/../includes/header.php';

?>

<main class="container secao">

    <!-- CABEÇALHO DA PÁGINA -->
    <p class="sobretitulo">
        ♡ Atualizar lembrança ♡
    </p>

    <h1>
        Editar show
    </h1>

    <!-- MENSAGEM DE ERRO -->
    <?php if ($erro): ?>

        <div class="alert alert-danger">
            <?= e($erro) ?>
        </div>

    <?php endif; ?>

    <!-- FORMULÁRIO DE EDIÇÃO -->
    <?php
    include __DIR__ . '/formulario.php';
    ?>

</main>

<?php

// Inclui o rodapé
include __DIR__ . '/../includes/footer.php';

?>
