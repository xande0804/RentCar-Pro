document.addEventListener('DOMContentLoaded', function() {

    const cpfInput = document.getElementById('cpf');
    const telefoneInput = document.getElementById('telefone');

    // --- MÁSCARA PARA CPF (formato 000.000.000-00) ---
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
            
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            
            e.target.value = value.substring(0, 14); // Limita o tamanho
        });
    }

    // --- MÁSCARA PARA TELEFONE (formato (00) 00000-0000) ---
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
            
            if (value.length > 10) {
                 value = value.replace(/^(\d\d)(\d{5})(\d{4}).*/, '($1) $2-$3');
            } else if (value.length > 5) {
                value = value.replace(/^(\d\d)(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d\d)(\d*)/, '($1) $2');
            } else {
                value = value.replace(/^(\d*)/, '($1');
            }
            
            e.target.value = value.substring(0, 15); // Limita o tamanho
        });
    }
});