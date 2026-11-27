
/* ==========================================
   CONFIRMAÇÃO DE EXCLUSÃO DE MEMÓRIAS
========================================== */

// Seleciona todos os formulários que possuem
// o atributo data-confirmar
document.querySelectorAll('[data-confirmar]').forEach((form) => {

    // Adiciona um evento ao enviar o formulário
    form.addEventListener('submit', (e) => {

        // Exibe uma mensagem de confirmação
        const confirmado = confirm(
            'Tem certeza de que deseja excluir esta memória?'
        );

        // Cancela o envio caso o usuário não confirme
        if (!confirmado) {

            e.preventDefault();

        }

    });

});


const seletorFotos = document.querySelector('[data-limite-fotos]');

if (seletorFotos && typeof DataTransfer === 'function') {
    const limite = Number(seletorFotos.dataset.limiteFotos);
    const lista = document.querySelector('#lista-fotos');
    const status = document.querySelector('#status-fotos');
    const fotos = [];

    const atualizarSelecao = (excedeuLimite = false) => {
        const selecao = new DataTransfer();
        lista.replaceChildren();

        fotos.forEach((foto, indice) => {
            selecao.items.add(foto);

            const item = document.createElement('li');
            item.className = 'd-flex align-items-center gap-2 mt-1';
            const nome = document.createElement('span');
            nome.className = 'text-break';
            nome.textContent = foto.name;

            const remover = document.createElement('button');
            remover.type = 'button';
            remover.className = 'btn btn-sm btn-outline-secondary';
            remover.textContent = 'Remover';
            remover.setAttribute('aria-label', 'Remover ' + foto.name);
            remover.addEventListener('click', () => {
                fotos.splice(indice, 1);
                atualizarSelecao();
                (lista.children[indice]?.querySelector('button') || seletorFotos).focus();
            });

            item.append(nome, remover);
            lista.append(item);
        });

        seletorFotos.files = selecao.files;
        status.textContent = fotos.length + ' de ' + limite + ' fotos selecionadas.' +
            (excedeuLimite ? ' O limite de fotos por envio foi atingido.' : '');
    };

    seletorFotos.addEventListener('change', () => {
        const novas = [...seletorFotos.files];
        const vagas = limite - fotos.length;
        fotos.push(...novas.slice(0, vagas));
        atualizarSelecao(novas.length > vagas);
    });
}

const visualizador = document.querySelector('#visualizador-fotos');

if (visualizador && typeof visualizador.showModal === 'function') {
    const fotos = [...document.querySelectorAll('[data-foto-galeria]')];
    const imagem = visualizador.querySelector('[data-foto-ampliada]');
    const posicao = visualizador.querySelector('[data-foto-posicao]');
    const anterior = visualizador.querySelector('[data-foto-anterior]');
    const proxima = visualizador.querySelector('[data-foto-proxima]');
    let indiceAtual = 0;

    const mostrarFoto = (indice) => {
        indiceAtual = (indice + fotos.length) % fotos.length;
        const foto = fotos[indiceAtual];
        imagem.src = foto.href;
        imagem.alt = foto.querySelector('img').alt;
        posicao.textContent = 'Foto ' + (indiceAtual + 1) + ' de ' + fotos.length;
    };

    fotos.forEach((foto, indice) => {
        foto.addEventListener('click', (evento) => {
            if (evento.ctrlKey || evento.metaKey || evento.shiftKey || evento.altKey) {
                return;
            }
            evento.preventDefault();
            mostrarFoto(indice);
            visualizador.showModal();
        });
    });

    anterior.disabled = proxima.disabled = fotos.length < 2;
    anterior.addEventListener('click', () => mostrarFoto(indiceAtual - 1));
    proxima.addEventListener('click', () => mostrarFoto(indiceAtual + 1));
    visualizador.querySelector('[data-foto-fechar]').addEventListener('click', () => visualizador.close());
    visualizador.addEventListener('keydown', (evento) => {
        if (evento.key === 'ArrowLeft' || evento.key === 'ArrowRight') {
            evento.preventDefault();
            mostrarFoto(indiceAtual + (evento.key === 'ArrowRight' ? 1 : -1));
        }
    });
}
