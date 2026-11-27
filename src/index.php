<?php
require __DIR__ . '/config/conexao.php';
require __DIR__ . '/config/funcoes.php';

iniciarSessao();
$usuarioId = (int) ($_SESSION['admin_id'] ?? 0);

if ($usuarioId) {
    $consulta = $pdo->prepare('SELECT COUNT(*) FROM shows WHERE id_usuario = ?');
    $consulta->execute([$usuarioId]);
    $total = (int) $consulta->fetchColumn();

    $consulta = $pdo->prepare(
        'SELECT s.*, a.nome AS artista
         FROM shows s
         JOIN artistas a ON a.id_artista = s.id_artista
         WHERE s.id_usuario = ?
         ORDER BY s.data_show DESC
         LIMIT 3'
    );
    $consulta->execute([$usuarioId]);
    $shows = $consulta->fetchAll();
}

$titulo = 'Início | My Concert Diary';

include __DIR__ . '/includes/header.php';
?>

<!-- SEÇÃO INICIAL -->
<section class="hero text-center">
    <div class="container">

        <p class="sobretitulo">
            ♡ Bem-vindo ao meu universo musical ♡
        </p>

        <h1>My Concert Diary</h1>

        <p class="subtitulo">
            Cada show tem uma história que merece ser lembrada.
        </p>

        <p>
            Um lugar para guardar músicas, fotografias
            e noites inesquecíveis.
        </p>

        <?php if ($usuarioId): ?>
            <a class="botao mt-3" href="/shows.php">Explorar minhas memórias ♡</a>
        <?php else: ?>
            <a class="botao mt-3" href="/admin/criar-conta.php">Criar conta</a>
            <a class="botao botao-rosa mt-3" href="/admin/login.php">Entrar</a>
        <?php endif; ?>

    </div>
</section>

<?php if ($usuarioId): ?>
<!-- MINHA COLEÇÃO -->
<section class="secao">
    <div class="container text-center">

        <p class="sobretitulo">
            Querido diário
        </p>

        <h2>
            Minha coleção de momentos especiais
        </h2>

        <p class="subtitulo">
            <?= match ($total) {
                0 => 'Ainda não há shows registrados neste diário.',
                1 => 'Já tem 1 show registrado neste diário.',
                default => "Já são {$total} shows registrados neste diário."
            } ?>
        </p>

    </div>
</section>

<!-- ÚLTIMAS MEMÓRIAS -->
<section class="secao secao-azul">
    <div class="container">

        <h2 class="text-center mb-5">
            Últimas memórias
        </h2>

        <div class="row g-4">

            <?php foreach ($shows as $s): ?>

                <div class="col-md-4">
                    <div class="cartao">

                        <!-- FOTO DE CAPA -->
                        <?php if ($s['foto_capa']): ?>

                            <img
                                class="capa"
                                src="<?= e(urlFoto($s['id_show'], $s['foto_capa'])) ?>"
                                alt="Show de <?= e($s['artista']) ?>"
                            >

                        <?php else: ?>

                            <div class="sem-foto">
                                ♡
                            </div>

                        <?php endif; ?>

                        <!-- INFORMAÇÕES DO SHOW -->
                        <div class="cartao-conteudo">

                            <h3>
                                <?= e($s['artista']) ?>
                            </h3>

                            <p class="etiqueta">
                                <?= e($s['turne']) ?>
                            </p>

                            <p>
                                <?= dataBR($s['data_show']) ?>
                                ·
                                <?= e($s['cidade']) ?>
                            </p>

                            <a
                                class="botao botao-rosa"
                                href="/show.php?id=<?= (int) $s['id_show'] ?>"
                            >
                                Ver memória
                            </a>

                        </div>

                    </div>
                </div>

            <?php endforeach; ?>

        </div>

        <!-- MENSAGEM CASO NÃO EXISTAM SHOWS -->
        <?php if (!$shows): ?>

            <p class="text-center">
                As primeiras memórias aparecerão aqui em breve. ♡
            </p>

        <?php endif; ?>

    </div>
</section>
<?php endif; ?>

<?php
include __DIR__ . '/includes/footer.php';
?>
