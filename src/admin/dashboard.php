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
// CONSULTA DOS SHOWS CADASTRADOS
// ==========================================

// Busca os shows da conta conectada e seus respectivos artistas
$consulta = $pdo->prepare(
    'SELECT
        s.*,
        a.nome AS artista
     FROM shows s
     JOIN artistas a
        ON a.id_artista = s.id_artista
     WHERE s.id_usuario = ?
     ORDER BY s.data_show DESC'
);
$consulta->execute([$_SESSION['admin_id']]);
$shows = $consulta->fetchAll();


// ==========================================
// CONFIGURAÇÃO DA PÁGINA
// ==========================================

// Define o título da página
$titulo = 'Meu diário';

// Inclui o cabeçalho
include __DIR__ . '/../includes/header.php';

?>

<main class="container secao">

    <!-- CABEÇALHO DO PAINEL -->
    <div class="d-flex justify-content-between flex-wrap gap-3 align-items-center mb-4">

        <!-- TÍTULO -->
        <div>

            <p class="sobretitulo">
                ♡ Minha área
            </p>

            <h1>
                Meu diário
            </h1>

        </div>

        <!-- AÇÕES DO ADMINISTRADOR -->
        <div>

            <!-- BOTÃO CADASTRAR SHOW -->
            <a
                class="botao"
                href="/admin/cadastrar.php"
            >
                + Cadastrar show
            </a>

            <!-- FORMULÁRIO DE LOGOUT -->
            <form
                class="d-inline"
                method="post"
                action="/admin/logout.php"
            >

                <!-- TOKEN CSRF -->
                <input
                    type="hidden"
                    name="csrf"
                    value="<?= e(token()) ?>"
                >

                <!-- BOTÃO SAIR -->
                <button
                    class="btn btn-outline-secondary ms-2"
                    type="submit"
                >
                    Sair
                </button>

            </form>

        </div>

    </div>

    <!-- TABELA DE SHOWS CADASTRADOS -->
    <div class="painel table-responsive">

        <table class="table align-middle">

            <!-- CABEÇALHO DA TABELA -->
            <thead>

                <tr>

                    <th>Artista</th>

                    <th>Data</th>

                    <th>Cidade</th>

                    <th>Ações</th>

                </tr>

            </thead>

            <!-- CORPO DA TABELA -->
            <tbody>

                <?php foreach ($shows as $s): ?>

                    <tr>

                        <!-- NOME DO ARTISTA -->
                        <td>
                            <?= e($s['artista']) ?>
                        </td>

                        <!-- DATA DO SHOW -->
                        <td>
                            <?= dataBR($s['data_show']) ?>
                        </td>

                        <!-- CIDADE -->
                        <td>
                            <?= e($s['cidade']) ?>
                        </td>

                        <!-- AÇÕES -->
                        <td>

                            <!-- VISUALIZAR SHOW -->
                            <a
                                href="/show.php?id=<?= (int) $s['id_show'] ?>"
                            >
                                Ver
                            </a>

                            ·

                            <!-- EDITAR SHOW -->
                            <a
                                href="/admin/editar.php?id=<?= (int) $s['id_show'] ?>"
                            >
                                Editar
                            </a>

                            ·

                            <!-- EXCLUIR SHOW -->
                            <form
                                method="post"
                                action="/admin/excluir.php"
                                class="d-inline"
                                data-confirmar
                            >

                                <!-- TOKEN CSRF -->
                                <input
                                    type="hidden"
                                    name="csrf"
                                    value="<?= e(token()) ?>"
                                >

                                <!-- ID DO SHOW -->
                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $s['id_show'] ?>"
                                >

                                <!-- BOTÃO EXCLUIR -->
                                <button
                                    class="btn btn-link text-danger p-0"
                                    type="submit"
                                >
                                    Excluir
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

        <!-- MENSAGEM CASO NÃO EXISTAM SHOWS -->
        <?php if (!$shows): ?>

            Nenhum show cadastrado.

        <?php endif; ?>

    </div>

</main>

<?php

// Inclui o rodapé
include __DIR__ . '/../includes/footer.php';

?>
