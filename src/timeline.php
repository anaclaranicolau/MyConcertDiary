<?php
require __DIR__ . '/config/conexao.php';
require __DIR__ . '/config/funcoes.php';

exigirLogin();

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

// Define o título da página
$titulo = 'Linha do tempo | My Concert Diary';

// Inclui o cabeçalho
include __DIR__ . '/includes/header.php';
?>

<main class="container secao">

    <!-- CABEÇALHO DA PÁGINA -->
    <div class="text-center mb-5">

        <p class="sobretitulo">
            ♡ Viagem no tempo ♡
        </p>

        <h1>
            Linha do tempo
        </h1>

        <p>
            Cada data, uma lembrança.
        </p>

    </div>

    <!-- LINHA DO TEMPO DOS SHOWS -->
    <div class="linha-tempo">

        <?php foreach ($shows as $s): ?>

            <!-- CARD DE CADA SHOW -->
            <div class="painel">

                <!-- DATA DO SHOW -->
                <p class="etiqueta">
                    <?= dataBR($s['data_show']) ?>
                </p>

                <!-- NOME DO ARTISTA -->
                <h3>
                    <?= e($s['artista']) ?>
                </h3>

                <!-- TURNÊ E CIDADE -->
                <p>
                    <?= e($s['turne']) ?>
                    ·
                    <?= e($s['cidade']) ?>
                </p>

                <!-- LINK PARA A MEMÓRIA -->
                <a href="/show.php?id=<?= (int) $s['id_show'] ?>">
                    Revisitar memória ♡
                </a>

            </div>

        <?php endforeach; ?>

        <!-- MENSAGEM CASO NÃO EXISTAM SHOWS -->
        <?php if (!$shows): ?>

            <p>
                A linha do tempo ainda está vazia.
            </p>

        <?php endif; ?>

    </div>

</main>

<?php
// Inclui o rodapé
include __DIR__ . '/includes/footer.php';
?>
