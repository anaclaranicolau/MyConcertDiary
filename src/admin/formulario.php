
<!-- ========================================== -->
<!-- FORMULÁRIO DE CADASTRO E EDIÇÃO DE SHOWS -->
<!-- ========================================== -->

<form
    method="post"
    enctype="multipart/form-data"
    class="painel"
>

    <!-- TOKEN DE SEGURANÇA CSRF -->
    <input
        type="hidden"
        name="csrf"
        value="<?= e(token()) ?>"
    >

    <!-- ========================================== -->
    <!-- INFORMAÇÕES DO SHOW -->
    <!-- ========================================== -->

    <h2>Sobre o show 🎤</h2>

    <div class="row g-3">

        <!-- ARTISTA OU BANDA -->
        <div class="col-md-6">

            <label class="form-label">
                Artista ou banda *
            </label>

            <input
                class="form-control"
                name="artista"
                maxlength="150"
                required
                value="<?= e($dados['artista'] ?? '') ?>"
            >

        </div>

        <!-- NOME DA TURNÊ -->
        <div class="col-md-6">

            <label class="form-label">
                Nome da turnê
            </label>

            <input
                class="form-control"
                name="turne"
                maxlength="150"
                value="<?= e($dados['turne'] ?? '') ?>"
            >

        </div>

        <!-- DATA DO SHOW -->
        <div class="col-md-4">

            <label class="form-label">
                Data *
            </label>

            <input
                class="form-control"
                type="date"
                name="data_show"
                required
                value="<?= e($dados['data_show'] ?? '') ?>"
            >

        </div>

        <!-- LOCAL DO SHOW -->
        <div class="col-md-4">

            <label class="form-label">
                Local *
            </label>

            <input
                class="form-control"
                name="local_show"
                maxlength="150"
                required
                value="<?= e($dados['local_show'] ?? '') ?>"
            >

        </div>

        <!-- CIDADE -->
        <div class="col-md-4">

            <label class="form-label">
                Cidade *
            </label>

            <input
                class="form-control"
                name="cidade"
                maxlength="100"
                required
                value="<?= e($dados['cidade'] ?? '') ?>"
            >

        </div>

        <!-- SETOR -->
        <div class="col-md-6">

            <label class="form-label">
                Setor
            </label>

            <input
                class="form-control"
                name="setor"
                maxlength="100"
                value="<?= e($dados['setor'] ?? '') ?>"
            >

        </div>

        <!-- ACOMPANHANTES -->
        <div class="col-md-6">

            <label class="form-label">
                Com quem fui
            </label>

            <input
                class="form-control"
                name="acompanhantes"
                value="<?= e($dados['acompanhantes'] ?? '') ?>"
            >

        </div>

        <!-- LOOK -->
        <div class="col-12">

            <label class="form-label">
                Meu look
            </label>

            <textarea
                class="form-control"
                rows="2"
                name="look_descricao"
            ><?= e($dados['look_descricao'] ?? '') ?></textarea>

        </div>

    </div>

    <hr class="my-4">


    <!-- ========================================== -->
    <!-- DIÁRIO PESSOAL -->
    <!-- ========================================== -->

    <h2>Querido diário 💌</h2>

    <!-- RELATO DA EXPERIÊNCIA -->
    <div class="mb-3">

        <label class="form-label">
            Relato da experiência
        </label>

        <textarea
            class="form-control"
            rows="6"
            name="relato"
        ><?= e($dados['relato'] ?? '') ?></textarea>

    </div>

    <div class="row g-3">

        <!-- AVALIAÇÃO DO SHOW -->
        <div class="col-md-4">

            <label class="form-label">
                Minha nota
            </label>

            <select
                class="form-select"
                name="nota"
            >

                <option value="">
                    Sem avaliação
                </option>

                <?php for ($i = 1; $i <= 5; $i++): ?>

                    <option
                        value="<?= $i ?>"
                        <?= (string) ($dados['nota'] ?? '') === (string) $i
                            ? 'selected'
                            : '' ?>
                    >
                        <?= str_repeat('★', $i) ?>
                    </option>

                <?php endfor; ?>

            </select>

        </div>

        <!-- MÚSICA FAVORITA -->
        <div class="col-md-8">

            <label class="form-label">
                Música favorita
            </label>

            <input
                class="form-control"
                name="musica_favorita"
                maxlength="150"
                value="<?= e($dados['musica_favorita'] ?? '') ?>"
            >

        </div>

        <!-- MOMENTO MARCANTE -->
        <div class="col-12">

            <label class="form-label">
                Momento mais marcante
            </label>

            <textarea
                class="form-control"
                rows="3"
                name="momento_marcante"
            ><?= e($dados['momento_marcante'] ?? '') ?></textarea>

        </div>

    </div>

    <hr class="my-4">


    <!-- ========================================== -->
    <!-- SETLIST DO SHOW -->
    <!-- ========================================== -->

    <h2>Setlist 🎵</h2>

    <label class="form-label">
        Uma música por linha
    </label>

    <textarea
        class="form-control mb-4"
        rows="7"
        name="setlist"
    ><?= e($dados['setlist'] ?? '') ?></textarea>


    <!-- ========================================== -->
    <!-- FOTOS E VÍDEOS -->
    <!-- ========================================== -->

    <h2>Fotos e vídeos 📸</h2>

    <div class="row g-3">

        <!-- FOTO DE CAPA -->
        <div class="col-md-6">

            <label class="form-label">
                Foto de capa
            </label>

            <input
                class="form-control"
                type="file"
                name="foto_capa"
                accept="image/jpeg,image/png,image/webp"
            >

        </div>

        <!-- FOTO DO INGRESSO -->
        <div class="col-md-6">

            <label class="form-label">
                Foto do ingresso
            </label>

            <input
                class="form-control"
                type="file"
                name="foto_ingresso"
                accept="image/jpeg,image/png,image/webp"
            >

        </div>

        <!-- GALERIA DE FOTOS -->
        <div class="col-12">

            <label class="form-label" for="fotos-galeria">
                Galeria de fotos (até 20 por envio)
            </label>

            <input
                class="form-control"
                type="file"
                id="fotos-galeria"
                name="fotos[]"
                multiple
                accept="image/jpeg,image/png,image/webp"
                data-limite-fotos="20"
                aria-describedby="status-fotos"
            >
            <p class="form-text mb-0" id="status-fotos" role="status"></p>
            <ul class="list-unstyled mb-0" id="lista-fotos"></ul>

        </div>

        <!-- LINKS DE VÍDEOS -->
        <div class="col-12">

            <label class="form-label">
                Links de vídeos (um por linha, URLs HTTPS)
            </label>

            <textarea
                class="form-control"
                rows="3"
                name="videos"
                placeholder="https://..."
            ></textarea>

        </div>

    </div>


    <!-- ========================================== -->
    <!-- OPÇÕES DISPONÍVEIS NA EDIÇÃO -->
    <!-- ========================================== -->

    <?php if (!empty($editando)): ?>

        <p class="detalhe mt-3">
            Novas fotos e vídeos serão acrescentados à galeria.
            Para remover imagens existentes, use as opções abaixo.
        </p>

        <!-- REMOVER FOTO DE CAPA -->
        <?php if (!empty($dados['foto_capa'])): ?>

            <label class="d-block">

                <input
                    type="checkbox"
                    name="remover_capa"
                    value="1"
                >

                Remover foto de capa atual

            </label>

        <?php endif; ?>

        <!-- REMOVER FOTO DO INGRESSO -->
        <?php if (!empty($dados['foto_ingresso'])): ?>

            <label class="d-block">

                <input
                    type="checkbox"
                    name="remover_ingresso"
                    value="1"
                >

                Remover foto do ingresso atual

            </label>

        <?php endif; ?>

        <!-- REMOVER FOTOS E VÍDEOS DA GALERIA -->
        <?php foreach ($midiasAtuais as $m): ?>

            <label class="d-block mt-2">

                <input
                    type="checkbox"
                    name="remover_midias[]"
                    value="<?= (int) $m['id_midia'] ?>"
                >

                Remover
                <?= $m['tipo'] === 'foto' ? 'foto' : 'vídeo' ?>
                #<?= (int) $m['id_midia'] ?>

            </label>

        <?php endforeach; ?>

    <?php endif; ?>


    <!-- ========================================== -->
    <!-- BOTÕES DO FORMULÁRIO -->
    <!-- ========================================== -->

    <!-- SALVAR -->
    <button
        class="botao mt-4"
        type="submit"
    >
        Salvar memória ♡
    </button>

    <!-- CANCELAR -->
    <a
        href="/admin/dashboard.php"
        class="ms-3"
    >
        Cancelar
    </a>

</form>
