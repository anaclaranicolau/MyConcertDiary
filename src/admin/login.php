<?php

// ==========================================
// IMPORTAÇÃO DOS ARQUIVOS NECESSÁRIOS
// ==========================================

// Conexão com o banco de dados
require __DIR__ . '/../config/conexao.php';

// Funções auxiliares do sistema
require __DIR__ . '/../config/funcoes.php';


// ==========================================
// INICIALIZAÇÃO DA SESSÃO
// ==========================================

// Inicia a sessão do usuário
iniciarSessao();


// ==========================================
// VERIFICAÇÃO DE LOGIN EXISTENTE
// ==========================================

// Redireciona caso o administrador já esteja logado
if (!empty($_SESSION['admin_id'])) {

    header('Location: /admin/dashboard.php');

    exit;

}


// ==========================================
// INICIALIZAÇÃO DAS VARIÁVEIS
// ==========================================

// Mensagem de erro
$erro = '';


// ==========================================
// PROCESSAMENTO DO FORMULÁRIO
// ==========================================

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Valida o token de segurança
    verificarToken();

    // Prepara a consulta para buscar o administrador
    $q = $pdo->prepare(
        'SELECT *
         FROM usuarios
         WHERE email = ?'
    );

    // Executa a consulta com o e-mail informado
    $q->execute([
        trim($_POST['email'] ?? '')
    ]);

    // Recupera os dados do administrador
    $u = $q->fetch();


    // ==========================================
    // VERIFICAÇÃO DAS CREDENCIAIS
    // ==========================================

    // Confere se o usuário existe e a senha está correta
    if (
        $u &&
        password_verify(
            $_POST['senha'] ?? '',
            $u['senha_hash']
        )
    ) {

        // Gera um novo identificador de sessão
        session_regenerate_id(true);

        // Armazena o ID do administrador na sessão
        $_SESSION['admin_id'] = $u['id_usuario'];

        // Redireciona para o painel administrativo
        header('Location: /admin/dashboard.php');

        exit;

    }

    // Mensagem exibida em caso de falha
    $erro = 'E-mail ou senha incorretos.';

}


// ==========================================
// CONFIGURAÇÃO DA PÁGINA
// ==========================================

// Define o título da página
$titulo = 'Entrar | My Concert Diary';

// Inclui o cabeçalho
include __DIR__ . '/../includes/header.php';

?>

<main class="container secao" style="max-width: 600px">

    <!-- PAINEL DE LOGIN -->
    <div class="painel">

        <!-- TÍTULO -->
        <h1>
            Área de login ♡
        </h1>

        <!-- MENSAGEM DE ERRO -->
        <?php if ($erro): ?>

            <div class="alert alert-danger">
                <?= e($erro) ?>
            </div>

        <?php endif; ?>

        <!-- FORMULÁRIO DE LOGIN -->
        <form method="post">

            <!-- TOKEN DE SEGURANÇA CSRF -->
            <input
                type="hidden"
                name="csrf"
                value="<?= e(token()) ?>"
            >

            <!-- E-MAIL -->
            <label class="form-label">
                E-mail
            </label>

            <input
                type="email"
                name="email"
                class="form-control mb-3"
                required
            >

            <!-- SENHA -->
            <label class="form-label">
                Senha
            </label>

            <input
                type="password"
                name="senha"
                class="form-control mb-4"
                required
            >

            <!-- BOTÃO ENTRAR -->
            <button
                class="botao"
                type="submit"
            >
                Entrar
            </button>

        </form>

        <p class="mt-3 mb-0"><a href="/admin/criar-conta.php">Criar conta</a></p>

    </div>

</main>

<?php

// Inclui o rodapé
include __DIR__ . '/../includes/footer.php';

?>
