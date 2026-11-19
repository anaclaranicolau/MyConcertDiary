
<?php
require __DIR__ . '/config/conexao.php';
require __DIR__ . '/config/funcoes.php';

// Recebe o ID do show pela URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Busca as informações do show
$s = $id ? buscarShow($pdo, $id) : false;

// Verifica se o show existe
if (!$s) {
    http_response_code(404);
    exit('Show não encontrado.');
}

// Busca as músicas e mídias do show
$musicas = musicasDoShow($pdo, $id);
$midias = imagensDoShow($pdo, $id);

// Define o título da página
$titulo = $s['artista'] . ' | My Concert Diary';

include __DIR__ . '/includes/header.php';
?>

<main class="container secao">

    <!-- VOLTAR PARA OS SHOWS -->
    <a href="/shows.php">
        ← Voltar aos meus shows
    </a>

    <!-- CABEÇALHO DO SHOW -->
    <header class="text-center my-5">

        <p class="sobretitulo">
            ♡ Uma noite para lembrar ♡
        </p>

        <h1 class="titulo-pagina">
            <?= e($s['artista']) ?>
        </h1>

        <p class="subtitulo">
            <?= e($s['turne']) ?>
        </p>

        <p>
            <?= dataBR($s['data_show']) ?>
            ·
            <?= e($s['cidade']) ?>
        </p>

    </header>

    <!-- FOTO DE CAPA -->
    <?php if ($s['foto_capa']): ?>

        <img
            class="foto-grande mb-5"
            src="<?= e($s['foto_capa']) ?>"
            alt="Foto do show"
        >

    <?php else: ?>

        <div class="sem-foto mb-5">
            ♡
        </div>

    <?php endif; ?>

    <!-- INFORMAÇÕES DO SHOW -->
    <div class="row g-3 mb-5">

        <!-- LOCAL -->
        <div class="col-md-4">
            <div class="painel h-100">

                <strong>📍 Local</strong>

                <p>
                    <?= e($s['local_show']) ?>
                </p>

            </div>
        </div>

        <!-- SETOR -->
        <div class="col-md-4">
            <div class="painel h-100">

                <strong>🎟 Setor</strong>

                <p>
                    <?= e($s['setor'] ?: 'Não informado') ?>
                </p>

            </div>
        </div>

        <!-- AVALIAÇÃO -->
        <div class="col-md-4">
            <div class="painel h-100">

                <strong>⭐ Minha nota</strong>

                <p class="estrela">
                    <?=
                        $s['nota']
                            ? str_repeat('★', (int) $s['nota'])
                              . str_repeat('☆', 5 - (int) $s['nota'])
                            : 'Ainda não avaliado'
                    ?>
                </p>

            </div>
        </div>

    </div>

    <!-- RELATO PESSOAL -->
    <section class="mb-5">

        <p class="sobretitulo">
            ♡ Querido diário ♡
        </p>

        <h2>Minha experiência</h2>

        <div class="painel texto-diario">
            <?= e(
                $s['relato']
                ?: 'Esta memória ainda está sendo escrita... ♡'
            ) ?>
        </div>

    </section>

    <!-- MOMENTOS FAVORITOS -->
    <div class="row g-4 mb-5">

        <!-- MÚSICA FAVORITA -->
        <div class="col-md-6">
            <div class="painel h-100">

                <h3>🎵 Música favorita</h3>

                <p>
                    <?= e(
                        $s['musica_favorita']
                        ?: 'Ainda não escolhida'
                    ) ?>
                </p>

            </div>
        </div>

        <!-- MOMENTO MARCANTE -->
        <div class="col-md-6">
            <div class="painel h-100">

                <h3>💗 Momento marcante</h3>

                <p class="texto-diario">
                    <?= e(
                        $s['momento_marcante']
                        ?: 'Ainda não registrado'
                    ) ?>
                </p>

            </div>
        </div>

    </div>

    <!-- SETLIST -->
    <section class="mb-5">

        <h2>Setlist da noite</h2>

        <div class="painel">

            <?php if ($musicas): ?>

                <ol>

                    <?php foreach ($musicas as $m): ?>

                        <li class="mb-2">
                            <?= e($m['nome_musica']) ?>
                        </li>

                    <?php endforeach; ?>

                </ol>

            <?php else: ?>

                A setlist ainda não foi adicionada.

            <?php endif; ?>

        </div>

    </section>

    <!-- GALERIA DE FOTOS E VÍDEOS -->
    <section class="mb-5">

        <h2>Galeria de fotos e vídeos</h2>

        <div class="row g-3">

            <?php foreach ($midias as $m): ?>

                <div class="col-md-4">

                    <?php if ($m['tipo'] === 'foto'): ?>

                        <!-- FOTO -->
                        <img
                            class="mini-foto"
                            src="<?= e($m['caminho_arquivo']) ?>"
                            alt="<?= e(
                                $m['legenda'] ?: 'Foto do show'
                            ) ?>"
                        >

                    <?php else: ?>

                        <!-- VÍDEO -->
                        <div class="painel">

                            <a
                                href="<?= e($m['caminho_arquivo']) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                ▶ Assistir ao vídeo
                            </a>

                        </div>

                    <?php endif; ?>

                    <!-- LEGENDA -->
                    <p class="detalhe mt-2">
                        <?= e($m['legenda']) ?>
                    </p>

                </div>

            <?php endforeach; ?>

        </div>

        <!-- GALERIA VAZIA -->
        <?php if (!$midias): ?>

            <p>
                As fotos e vídeos aparecerão aqui em breve. ♡
            </p>

        <?php endif; ?>

    </section>

    <!-- PEQUENOS DETALHES -->
    <section class="mb-5">

        <h2>Pequenos detalhes</h2>

        <div class="row g-3">

            <!-- ACOMPANHANTES -->
            <div class="col-md-6">
                <div class="painel h-100">

                    <h3>Com quem fui</h3>

                    <p class="texto-diario">
                        <?= e(
                            $s['acompanhantes']
                            ?: 'Não informado'
                        ) ?>
                    </p>

                </div>
            </div>

            <!-- LOOK -->
            <div class="col-md-6">
                <div class="painel h-100">

                    <h3>Meu look</h3>

                    <p class="texto-diario">
                        <?= e(
                            $s['look_descricao']
                            ?: 'Não registrado'
                        ) ?>
                    </p>

                </div>
            </div>

        </div>

        <!-- FOTO DO INGRESSO -->
        <?php if ($s['foto_ingresso']): ?>

            <div class="text-center mt-5">

                <h3>Meu ingresso 🎟</h3>

                <img
                    class="img-fluid foto-grande"
                    style="max-width: 500px"
                    src="<?= e($s['foto_ingresso']) ?>"
                    alt="Ingresso do show"
                >

            </div>

        <?php endif; ?>

    </section>

</main>

<?php
include __DIR__ . '/includes/footer.php';
?>
