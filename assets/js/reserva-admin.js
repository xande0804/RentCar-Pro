// assets/js/reserva-admin.js
document.addEventListener('DOMContentLoaded', function () {
    const clientes = window.listaDeClientes || [];
    const perfilLogado = window.perfilUsuarioLogado || 'visitante';

    const modalElement = document.getElementById('selecionarClienteModal');
    if (!modalElement) return;

    const modal = new bootstrap.Modal(modalElement);
    const listaClientesDiv = document.getElementById('lista-clientes-modal');
    const searchInput = document.getElementById('search-cliente-modal');

    let codCarroSelecionado = null;

    function getCodCarroFromHref(href) {
        try {
            const url = new URL(href, window.location.origin);
            // preferir cod_carro; manter compat com id (href atual do botão usa id)
            const codFromParam = url.searchParams.get('cod_carro');
            const idFromParam = url.searchParams.get('id');
            const cod = parseInt(codFromParam ?? idFromParam, 10);
            return Number.isFinite(cod) ? cod : null;
        } catch {
            return null;
        }
    }

    function popularListaClientes(filtro = '') {
        listaClientesDiv.innerHTML = '';
        const filtroLower = (filtro || '').toLowerCase();

        const clientesFiltrados = clientes.filter(c =>
            (c.nome || '').toLowerCase().includes(filtroLower) ||
            (c.email || '').toLowerCase().includes(filtroLower)
        );

        if (clientesFiltrados.length === 0) {
            listaClientesDiv.innerHTML = '<p class="text-center text-muted">Nenhum cliente encontrado.</p>';
            return;
        }

        clientesFiltrados.forEach(cliente => {
            const link = document.createElement('a');
            link.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';

            const cadastroCompleto = parseInt(cliente.cadastro_completo, 10) === 1;

            if (!cadastroCompleto) {
                // precisa completar cadastro antes de finalizar
                link.href = `view/profile/completarCadastro.php?cod_usuario=${cliente.cod_usuario}&cod_carro=${codCarroSelecionado}`;
            } else {
                // fluxo normal: iniciar reserva em nome do cliente selecionado
                // Enviamos cod_carro **e** id (backcompat com controller atual)
                link.href =
                  `controller/ReservaControl.php?acao=iniciar&cod_carro=${codCarroSelecionado}&id=${codCarroSelecionado}&cod_usuario=${cliente.cod_usuario}`;
            }

            // badge por perfil/estado
            let badgeText = 'Outro';
            let badgeClass = 'badge bg-secondary';
            if (cliente.perfil === 'cliente') {
                badgeText = 'Cliente';
                badgeClass = 'badge bg-success';
            } else if (cliente.perfil === 'usuario' && cadastroCompleto) {
                badgeText = 'Usuário Completo';
                badgeClass = 'badge bg-primary';
            } else if (cliente.perfil === 'usuario' && !cadastroCompleto) {
                badgeText = 'Usuário Incompleto';
                badgeClass = 'badge bg-warning text-dark';
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

    // Interceptar o botão da vitrine: .btn-details
    document.querySelectorAll('.btn-details').forEach(button => {
        button.addEventListener('click', function (event) {
            // Só intercepta para staff
            if (!['admin', 'gerente', 'funcionario'].includes(perfilLogado)) return;

            event.preventDefault();

            codCarroSelecionado = getCodCarroFromHref(this.getAttribute('href'));
            if (!codCarroSelecionado) {
                alert('Não foi possível identificar o cod_carro selecionado.');
                return;
            }

            popularListaClientes(); // sem filtro inicial
            modal.show();
        });
    });

    // Filtro de busca no modal
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            popularListaClientes(this.value);
        });
    }
});
