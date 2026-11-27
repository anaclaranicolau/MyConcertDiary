
<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <!-- CONFIGURAÇÕES DA PÁGINA -->
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <!-- TÍTULO DA PÁGINA -->
    <title>
        <?= e($titulo ?? 'My Concert Diary') ?>
    </title>

    <!-- CSS DO BOOTSTRAP -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- CSS PERSONALIZADO -->
    <link
        rel="stylesheet"
        href="/assets/css/style.css"
    >

</head>

<body>

    <!-- MENU DE NAVEGAÇÃO -->
    <nav class="navbar navbar-expand-lg">

        <div class="container">

            <!-- LOGO DO SITE -->
            <a class="navbar-brand" href="/">
                ✦ My Concert Diary
            </a>

            <!-- BOTÃO DO MENU RESPONSIVO -->
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menu"
            >

                <span class="navbar-toggler-icon"></span>

            </button>

            <!-- LINKS DE NAVEGAÇÃO -->
            <div
                class="collapse navbar-collapse"
                id="menu"
            >

                <div class="navbar-nav ms-auto">

                    <!-- INÍCIO -->
                    <a class="nav-link" href="/">
                        Início
                    </a>

                    <!-- MEUS SHOWS -->
                    <a class="nav-link" href="/shows.php">
                        Meus shows
                    </a>

                    <!-- LINHA DO TEMPO -->
                    <a class="nav-link" href="/timeline.php">
                        Linha do tempo
                    </a>

                    <!-- SOBRE MIM -->
                    <a class="nav-link" href="/sobre.php">
                        Sobre mim
                    </a>

                    <!-- DIÁRIO -->
                    <a
                        class="nav-link"
                        href="/admin/dashboard.php"
                    >
                        Meu diário
                    </a>

                </div>

            </div>

        </div>

    </nav>
