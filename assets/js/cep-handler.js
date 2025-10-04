document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');
    if (!cepInput) return;

    // Função para formatar CEP (adiciona o traço)
    function formatarCEP(cep) {
        cep = cep.replace(/\D/g, ''); // Remove não numéricos
        if (cep.length > 5) {
            cep = cep.substring(0, 5) + '-' + cep.substring(5, 8);
        }
        return cep;
    }

    // Função principal para consultar o CEP
    async function consultarCEP(cep) {
        const loadingDiv = document.getElementById('loading');
        const cepNumerico = cep.replace(/\D/g, '');
        
        if (cepNumerico.length !== 8) return;

        if(loadingDiv) loadingDiv.style.display = 'block';
        
        try {
            const response = await fetch(`https://viacep.com.br/ws/${cepNumerico}/json/`);
            if (!response.ok) throw new Error('Erro na requisição');
            const dados = await response.json();
            if (dados.erro) throw new Error('CEP não encontrado');

            preencherFormulario(dados);
        } catch (error) {
            console.error("Erro ao consultar CEP:", error.message);
            limparCamposEndereco(false); // Não limpa o CEP
        } finally {
            if(loadingDiv) loadingDiv.style.display = 'none';
        }
    }

    function preencherFormulario(dados) {
        document.getElementById('logradouro').value = dados.logradouro || '';
        document.getElementById('bairro').value = dados.bairro || '';
        document.getElementById('cidade').value = dados.localidade || '';
        document.getElementById('estado').value = dados.uf || '';
    }

    function limparCamposEndereco(limparCep = true) {
        if(limparCep) document.getElementById('cep').value = '';
        document.getElementById('logradouro').value = '';
        document.getElementById('bairro').value = '';
        document.getElementById('cidade').value = '';
        document.getElementById('estado').value = '';
    }

    // Formata o CEP enquanto o usuário digita
    cepInput.addEventListener('input', function(e) {
        e.target.value = formatarCEP(e.target.value);
    });
    
    // Consulta o CEP quando o campo perde o foco (blur)
    cepInput.addEventListener('blur', function(e) {
        consultarCEP(this.value);
    });
});