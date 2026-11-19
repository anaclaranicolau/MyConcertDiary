
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
