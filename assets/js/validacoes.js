document.addEventListener("DOMContentLoaded", function () {
    // --- LÓGICA DE VALIDAÇÃO DE SENHA ---
    const formCadastro = document.getElementById("formCadastro");
    const senha = document.getElementById("senha");
    const confirma = document.getElementById("confirma_senha");
    const msgSenha = document.getElementById("msg_senha");

    if (confirma) {
        confirma.addEventListener("blur", function () {
            if (senha.value !== confirma.value) {
                msgSenha.style.display = "block";
            } else {
                msgSenha.style.display = "none";
            }
        });
    }

    if (formCadastro && senha && confirma) {
        formCadastro.addEventListener("submit", function (e) {
            if (senha.value !== confirma.value) {
                e.preventDefault();
                alert("As senhas não coincidem. Corrija antes de prosseguir.");
            }
        });
    }

    // --- LÓGICA DE VALIDAÇÃO DE CPF ---
    const cpfInput = document.getElementById('cpf');
    const cpfError = document.getElementById('cpfError');

    function validaCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');
        if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

        let soma = 0, resto;
        for (let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
        resto = (soma * 10) % 11;
        if ((resto === 10) || (resto === 11)) resto = 0;
        if (resto !== parseInt(cpf.substring(9, 10))) return false;

        soma = 0;
        for (let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
        resto = (soma * 10) % 11;
        if ((resto === 10) || (resto === 11)) resto = 0;
        if (resto !== parseInt(cpf.substring(10, 11))) return false;

        return true;
    }

    // --- VALIDA CPF AO SAIR DO CAMPO (evento 'blur') ---
    if (cpfInput) {
        cpfInput.addEventListener('blur', function(e) {
            const cpfValue = e.target.value;

            if (cpfValue.length === 14 && !validaCPF(cpfValue)) {
                cpfError.style.display = 'block';
                cpfInput.classList.add('is-invalid');
            } else {
                cpfError.style.display = 'none';
                cpfInput.classList.remove('is-invalid');
            }
        });
    }

    // --- LÓGICA DE VALIDAÇÃO NO ENVIO DO FORMULÁRIO DE COMPLETAR CADASTRO ---
    // O formulário em completarCadastro.php não tem um ID, então pegamos pela action.
    const formCompletarCadastro = document.querySelector('form[action="controller/UsuarioControl.php"]');

    if (formCompletarCadastro && cpfInput) {
        formCompletarCadastro.addEventListener('submit', function(e) {
            const cpfValue = cpfInput.value;

            // Se o CPF não for válido...
            if (!validaCPF(cpfValue)) {
                e.preventDefault(); // Impede o envio
                cpfError.textContent = 'CPF inválido. Por favor, verifique.';
                cpfError.style.display = 'block';
                cpfInput.classList.add('is-invalid');
            }
        });
    }
});
