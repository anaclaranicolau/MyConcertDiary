
<?php
require __DIR__ . '/config/funcoes.php';

// Define o título da página
$titulo = 'Sobre mim | My Concert Diary';

// Inclui o cabeçalho do site
include __DIR__ . '/includes/header.php';
?>

<main class="container secao">

    <!-- CABEÇALHO DA PÁGINA -->
    <div class="text-center">

        <p class="sobretitulo">
            ♡ Por trás do diário ♡
        </p>

        <h1>
            Sobre mim
        </h1>

    </div>

    <!-- APRESENTAÇÃO PESSOAL -->
    <div
        class="painel mx-auto mt-5"
        style="max-width: 780px"
    >

        <h2>
            Olá, seja bem-vindo! 🩵
        </h2>

        <p>
            Criei o My Concert Diary para guardar minhas
            experiências em shows: as músicas, as pessoas,
            os ingressos, as fotos e todos os detalhes que
            tornam cada apresentação inesquecível.
        </p>

        <p>
            Entre meus artistas favoritos estão
            <strong>One Direction</strong> e
            <strong>Sabrina Carpenter</strong>.
            Este espaço reúne minhas memórias e minha
            paixão pela música.
        </p>

        <p>
            Espero que você goste de conhecer um pouquinho
            desse universo! ♡
        </p>

    </div>

</main>

<?php
// Inclui o rodapé do site
include __DIR__ . '/includes/footer.php';
?>
