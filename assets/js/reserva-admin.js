document.addEventListener('DOMContentLoaded', function () {
    const clientes = window.listaDeClientes || [];
    const perfilLogado = window.perfilUsuarioLogado || 'visitante';
    
    const modalElement = document.getElementById('selecionarClienteModal');
    if (!modalElement) return;

    const modal = new bootstrap.Modal(modalElement);
    const listaClientesDiv = document.getElementById('lista-clientes-modal');
    const searchInput = document.getElementById('search-cliente-modal');

    function popularListaClientes(filtro = '') {
        listaClientesDiv.innerHTML = '';
        const filtroLowerCase = filtro.toLowerCase();

        const clientesFiltrados = clientes.filter(c =>
            c.nome.toLowerCase().includes(filtroLowerCase) ||
            c.email.toLowerCase().includes(filtroLowerCase)
        );

        if (clientesFiltrados.length === 0) {
            listaClientesDiv.innerHTML = '<p class="text-center text-muted">Nenhum cliente encontrado.</p>';
            return;
        }

        clientesFiltrados.forEach(cliente => {
            const link = document.createElement('a');
            if (cliente.cadastro_completo == 0) {
                // ADICIONADO: Passa o ID do carro junto com o ID do cliente
                link.href = `view/profile/completarCadastro.php?clienteId=${cliente.cod_usuario}&carroId=${window.carroIdSelecionado}`;
            } else {
                link.href = `view/reservas/finalizar.php?id=${window.carroIdSelecionado}&clienteId=${cliente.cod_usuario}`;
            }
            link.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';

            // --- Definir o tipo de usuário e estilo da badge ---
            let badgeText = '';
            let badgeClass = '';

            if (cliente.perfil === 'cliente') {
                badgeText = 'Cliente';
                badgeClass = 'badge bg-success'; // verde
            } 
            else if (cliente.perfil === 'usuario' && cliente.cadastro_completo == 1) {
                badgeText = 'Usuário Completo';
                badgeClass = 'badge bg-primary'; // azul
            } 
            else if (cliente.perfil === 'usuario' && cliente.cadastro_completo == 0) {
                badgeText = 'Usuário Incompleto';
                badgeClass = 'badge bg-warning text-dark'; // amarelo
            } 
            else {
                badgeText = 'Outro';
                badgeClass = 'badge bg-secondary'; // cinza padrão
            }

            link.innerHTML = `
                <div>
                    <strong>${cliente.nome}</strong><br>
                    <small class="text-muted">${cliente.email}</small>
                </div>
                <span class="${badgeClass}">${badgeText}</span>
            `;
            listaClientesDiv.appendChild(link);
        });
    }

    // Quando clicar no botão "Reservar"
    document.querySelectorAll('.btn-reservar').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); 
            window.carroIdSelecionado = this.getAttribute('data-car-id');

            if (['admin', 'gerente', 'funcionario'].includes(perfilLogado)) {
                popularListaClientes(); 
                modal.show();
            } else {
                window.location.href = this.href;
            }
        });
    });

    // Filtro de busca no modal
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            popularListaClientes(this.value);
        });
    }
});
