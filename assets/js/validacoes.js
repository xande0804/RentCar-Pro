document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formCadastro");
    const nome = document.getElementById("nome");
    const email = document.getElementById("email");
    const senha = document.getElementById("senha");
    const confirma = document.getElementById("confirma_senha");
    const msgSenha = document.getElementById("msg_senha");

    // --- VALIDAÇÃO DE SENHAS EM TEMPO REAL ---
    if (confirma) {
        confirma.addEventListener("blur", function () {
            if (senha.value !== confirma.value) {
                msgSenha.style.display = "block"; // Mostra a mensagem de erro
            } else {
                msgSenha.style.display = "none"; // Esconde a mensagem de erro
            }
        });
    }

    // --- VALIDAÇÃO COMPLETA AO ENVIAR O FORMULÁRIO ---
    if (form) {
        form.addEventListener("submit", function (e) {
            let isValid = true;
            let errorMessage = "";

            // 1. Verifica se os campos estão preenchidos
            if (nome.value.trim() === "" || email.value.trim() === "" || senha.value === "") {
                isValid = false;
                errorMessage = "Por favor, preencha todos os campos obrigatórios.";
            }
            
            // 2. Verifica se o email é válido (formato simples)
            else if (!/^\S+@\S+\.\S+$/.test(email.value)) {
                isValid = false;
                errorMessage = "Por favor, insira um endereço de e-mail válido.";
            }

            // 3. Verifica se as senhas coincidem
            else if (senha.value !== confirma.value) {
                isValid = false;
                errorMessage = "As senhas não coincidem. Corrija antes de prosseguir.";
            }

            // Se algo estiver inválido, previne o envio e mostra um alerta
            if (!isValid) {
                e.preventDefault(); // Bloqueia o envio do formulário
                alert(errorMessage);
            }
        });
    }
});