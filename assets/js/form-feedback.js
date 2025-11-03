document.addEventListener('DOMContentLoaded', function() {
    // Procura por TODOS os formulários com a classe específica
    const formsWithFeedback = document.querySelectorAll('.form-loading-feedback');

    // Para cada formulário encontrado, aplica a lógica
    formsWithFeedback.forEach(formElement => {
        formElement.addEventListener('submit', function() {
            // Encontra o botão de submit DENTRO do formulário que foi enviado
            const submitButton = formElement.querySelector('button[type="submit"]');
            
            if (submitButton) {
                // 1. Desabilita o botão para prevenir cliques duplos
                submitButton.disabled = true;

                // 2. Muda o conteúdo do botão para mostrar o spinner e o texto
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Salvando...
                `;
            }
        });
    });
});