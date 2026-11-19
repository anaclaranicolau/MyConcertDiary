
<?php
require __DIR__ . '/config/conexao.php';
require __DIR__ . '/config/funcoes.php';

// Recebe os filtros de pesquisa
$busca = trim($_GET['busca'] ?? '');
$ano = trim($_GET['ano'] ?? '');

// Consulta inicial
$sql = 'SELECT
            s.*,
            a.nome AS artista
        FROM shows s
        JOIN artistas a
            ON a.id_artista = s.id_artista
        WHERE 1=1';

$args = [];

// Filtro por artista ou turnê
if ($busca !== '') {

    $sql .= ' AND (
        a.nome LIKE ?
        OR s.turne LIKE ?
    )';

    $args[] = '%' . $busca . '%';
    $args[] = '%' . $busca . '%';
}

// Filtro por ano
if (preg_match('/^\d{4}$/', $ano)) {

    $sql .= ' AND YEAR(s.data_show) = ?';

    $args[] = $ano;
}

// Ordena os shows pela data mais recente
$sql .= ' ORDER BY s.data_show DESC';

// Executa a consulta
$q = $pdo->prepare($sql);
$q->execute($args);

// Recupera os shows encontrados
$shows = $q->fetchAll();

// Define o título da página
$titulo = 'Meus shows | My Concert Diary';

include __DIR__ . '/includes/header.php';
?>

<main class="container secao">

    <!-- CABEÇALHO DA PÁGINA -->
    <div class="text-center mb-5">

        <p class="sobretitulo">
            ♡ Minha coleção ♡
        </p>

        <h1 class="titulo-pagina">
            Meus shows
        </h1>

        <p>
            As noites que guardo no coração.
        </p>

    </div>

    <!-- FORMULÁRIO DE PESQUISA -->
    <form class="row g-2 mb-5" method="get">

        <!-- BUSCA POR ARTISTA OU TURNÊ -->
        <div class="col-md-7">

            <input
                class="form-control"
                type="text"
                name="busca"
                placeholder="Buscar artista ou turnê"
                value="<?= e($busca) ?>"
            >

        </div>

        <!-- FILTRO POR ANO -->
        <div class="col-md-3">

            <input
                class="form-control"
                type="text"
                name="ano"
                placeholder="Ano (ex.: 2024)"
                value="<?= e($ano) ?>"
                maxlength="4"
            >

        </div>

        <!-- BOTÃO DE BUSCA -->
        <div class="col-md-2">

            <button
                class="botao w-100"
                type="submit"
            >
                Buscar
            </button>

        </div>

    </form>

    <!-- LISTAGEM DOS SHOWS -->
    <div class="row g-4">

        <?php foreach ($shows as $s): ?>

            <div class="col-md-6 col-lg-4">

                <article class="cartao">

                    <!-- FOTO DE CAPA -->
                    <?php if ($s['foto_capa']): ?>

                        <img
                            class="capa"
                            src="<?= e($s['foto_capa']) ?>"
                            alt="Foto de <?= e($s['artista']) ?>"
                        >

                    <?php else: ?>

                        <div class="sem-foto">
                            ♡
                        </div>

                    <?php endif; ?>

                    <!-- INFORMAÇÕES DO SHOW -->
                    <div class="cartao-conteudo">

                        <!-- ARTISTA -->
                        <h3>
                            <?= e($s['artista']) ?>
                        </h3>

                        <!-- TURNÊ -->
                        <p class="etiqueta">
                            <?= e(
                                $s['turne']
                                ?: 'Uma noite especial'
                            ) ?>
                        </p>

                        <!-- DATA E CIDADE -->
                        <p>
                            <?= dataBR($s['data_show']) ?>
                            ·
                            <?= e($s['cidade']) ?>
                        </p>

                        <!-- LINK PARA A MEMÓRIA -->
                        <a
                            class="botao botao-rosa"
                            href="/show.php?id=<?= (int) $s['id_show'] ?>"
                        >
                            Ver memória ♡
                        </a>

                    </div>

                </article>

            </div>

        <?php endforeach; ?>

    </div>

    <!-- MENSAGEM CASO NENHUM SHOW SEJA ENCONTRADO -->
    <?php if (!$shows): ?>

        <div class="painel text-center">
            Nenhum show encontrado. ♡
        </div>

    <?php endif; ?>

</main>

<?php
include __DIR__ . '/includes/footer.php';
?>
