
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

iniciarSessao();


// ==========================================
// VERIFICAÇÃO DE ADMINISTRADOR EXISTENTE
// ==========================================

// Conta quantos administradores estão cadastrados
$quantidade = (int) $pdo->query(
    'SELECT COUNT(*) FROM usuarios'
)->fetchColumn();

// Impede a criação de outro administrador
if ($quantidade) {

    http_response_code(403);

    exit(
        'Administrador já configurado. Acesse /admin/login.php.'
    );

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

    // Verifica o token de segurança
    verificarToken();

    // Recupera os dados do formulário
    $nome = trim($_POST['nome'] ?? '');

    $email = trim($_POST['email'] ?? '');

    $senha = $_POST['senha'] ?? '';

    // Valida os dados informados
    if (
        !$nome ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($senha) < 12
    ) {

        $erro = 'Informe nome, e-mail válido e senha de pelo menos 12 caracteres.';

    } else {

        try {

            // Prepara a consulta para cadastrar o administrador
            $q = $pdo->prepare(
                'INSERT INTO usuarios (
                    nome,
                    email,
                    senha_hash
                ) VALUES (?, ?, ?)'
            );

            // Salva os dados com a senha protegida por hash
            $q->execute([
                $nome,
                $email,
                password_hash($senha, PASSWORD_DEFAULT)
            ]);

            // Redireciona para a página de login
            header('Location: /admin/login.php');

            exit;

        } catch (PDOException $ex) {

            // Exibe uma mensagem em caso de erro
            $erro = 'Não foi possível criar o administrador.';

        }

    }

}


// ==========================================
// CONFIGURAÇÃO DA PÁGINA
// ==========================================

// Define o título da página
$titulo = 'Criar administrador';

// Inclui o cabeçalho
include __DIR__ . '/../includes/header.php';

?>

<main class="container secao" style="max-width: 650px">

    <!-- PAINEL DE CADASTRO -->
    <div class="painel">

        <!-- TÍTULO -->
        <h1>
            Criar acesso administrativo
        </h1>

        <!-- DESCRIÇÃO -->
        <p>
            Essa página só funciona enquanto não existir
            nenhum administrador.
        </p>

        <!-- MENSAGEM DE ERRO -->
        <?php if ($erro): ?>

            <div class="alert alert-danger">
                <?= e($erro) ?>
            </div>

        <?php endif; ?>

        <!-- FORMULÁRIO DE CADASTRO -->
        <form method="post">

            <!-- TOKEN CSRF -->
            <input
                type="hidden"
                name="csrf"
                value="<?= e(token()) ?>"
            >

            <!-- NOME -->
            <label class="form-label">
                Nome
            </label>

            <input
                class="form-control mb-3"
                name="nome"
                required
                maxlength="100"
            >

            <!-- E-MAIL -->
            <label class="form-label">
                E-mail
            </label>

            <input
                class="form-control mb-3"
                type="email"
                name="email"
                required
            >

            <!-- SENHA -->
            <label class="form-label">
                Senha (mínimo 12 caracteres)
            </label>

            <input
                class="form-control mb-4"
                type="password"
                name="senha"
                minlength="12"
                required
            >

            <!-- BOTÃO DE CADASTRO -->
            <button class="botao" type="submit">
                Criar administrador
            </button>

        </form>

    </div>

</main>

<?php

// Inclui o rodapé
include __DIR__ . '/../includes/footer.php';

?>
