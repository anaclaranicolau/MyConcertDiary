<?php

require __DIR__ . '/../config/conexao.php';
require __DIR__ . '/../config/funcoes.php';

iniciarSessao();

if (!empty($_SESSION['admin_id'])) {
    header('Location: /admin/dashboard.php');
    exit;
}

$erro = '';
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarToken();

    $nome = trim((string) ($_POST['nome'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($nome === '' || mb_strlen($nome) > 100 || strlen($email) > 150 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 12 ||
        strlen($senha) > 72 || str_contains($senha, chr(0))) {
        $erro = 'Informe um nome, um e-mail válido e uma senha de 12 a 72 bytes.';
    } else {
        try {
            $consulta = $pdo->prepare(
                'INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)'
            );
            $consulta->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT)]);

            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $pdo->lastInsertId();
            header('Location: /admin/dashboard.php', true, 303);
            exit;
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                $erro = 'Já existe uma conta com esse e-mail.';
            } else {
                error_log($ex->getMessage());
                $erro = 'Não foi possível criar a conta.';
            }
        }
    }
}

$titulo = 'Criar conta';
include __DIR__ . '/../includes/header.php';
?>

<main class="container secao" style="max-width: 650px">
    <div class="painel">
        <h1>Criar conta</h1>
        <p>Crie sua conta para cadastrar e gerenciar os shows do seu diário.</p>

        <?php if ($erro): ?>
            <div class="alert alert-danger" role="alert"><?= e($erro) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(token()) ?>">

            <label class="form-label" for="nome">Nome</label>
            <input class="form-control mb-3" id="nome" name="nome"
                   value="<?= e($nome) ?>" autocomplete="name" required maxlength="100">

            <label class="form-label" for="email">E-mail</label>
            <input class="form-control mb-3" type="email" id="email" name="email"
                   value="<?= e($email) ?>" autocomplete="email" required maxlength="150">

            <label class="form-label" for="senha">Senha (mínimo de 12 caracteres)</label>
            <input class="form-control mb-4" type="password" id="senha" name="senha"
                   autocomplete="new-password" required minlength="12" maxlength="72">

            <button class="botao" type="submit">Criar conta</button>
        </form>

        <p class="mt-3 mb-0"><a href="/admin/login.php">Já tenho conta</a></p>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
