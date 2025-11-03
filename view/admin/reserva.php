<?php

$perfisPermitidos = ['admin', 'gerente', 'funcionario'];

$pageTitle = "Gerenciar Reservas";

require_once __DIR__ . '/../layout/header.php';


require_once __DIR__ . '/../../model/dao/ReservaDAO.php';
$reservaDAO = new ReservaDAO();

// --- LÓGICA DE FILTROS ---
$filtros = [
    'busca' => $_GET['busca'] ?? '',
    'status' => $_GET['status'] ?? '',
    'data_inicio' => $_GET['data_inicio'] ?? '',
    'data_fim' => $_GET['data_fim'] ?? ''
];
$reservas = $reservaDAO->getAll($filtros);


?>

<div class="content-management">
    <div class="table-header">
        <h2>Gerenciamento de Reservas</h2>
        <p>Visualize e gerencie todas as reservas do sistema.</p>
    </div>
    
    <?php 
    if (isset($_SESSION['flash_message'])) {
        $flashMessage = $_SESSION['flash_message'];
        $messageType = $flashMessage['type'] === 'success' ? 'alert-success' : 'alert-danger';
        echo "<div class='alert {$messageType}'>" . htmlspecialchars($flashMessage['message']) . "</div>";
        unset($_SESSION['flash_message']);
    }
    ?>

    <div class="table-controls d-flex justify-content-between align-items-center mb-3">
        <a href="view/carros/index.php" class="btn-add">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Nova Reserva</span>
        </a>

        <form action="view/admin/reserva.php" method="GET" class="d-flex align-items-center gap-2">
            <input type="search" name="busca" class="form-control" placeholder="Buscar por cliente ou carro..." value="<?= htmlspecialchars($filtros['busca']) ?>">
            
            <select name="status" class="form-select">
                <option value="">Todos os Status</option>
                <option value="pendente" <?= $filtros['status'] == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                <option value="ativa" <?= $filtros['status'] == 'ativa' ? 'selected' : '' ?>>Ativa</option>
                <option value="aguardando_pagamento" <?= $filtros['status'] == 'aguardando_pagamento' ? 'selected' : '' ?>>Aguardando pagamento</option>
                <option value="aguardando_retirada" <?= $filtros['status'] == 'aguardando_retirada' ? 'selected' : '' ?>>Aguardando Retirada</option>
                <option value="concluida" <?= $filtros['status'] == 'concluida' ? 'selected' : '' ?>>Concluída</option>
                <option value="cancelada" <?= $filtros['status'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
            </select>
            
            <input type="date" name="data_inicio" class="form-control" title="Data de início do período" value="<?= htmlspecialchars($filtros['data_inicio']) ?>">
            
            <input type="date" name="data_fim" class="form-control" title="Data de fim do período" value="<?= htmlspecialchars($filtros['data_fim']) ?>">
            
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="view/admin/reserva.php" class="btn btn-outline-secondary" title="Limpar Filtros">X</a>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Carro</th>
                    <th>Período</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th class="actions-header">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservas)): ?>
                    <tr><td colspan="7" class="no-results">Nenhuma reserva encontrada para os filtros aplicados.</td></tr>
                <?php else: ?>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($reserva['cod_reserva']) ?></td>
                            <td><?= htmlspecialchars($reserva['nome_usuario']) ?></td>
                            <td><?= htmlspecialchars($reserva['marca'] . ' ' . $reserva['modelo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($reserva['data_inicio'])) ?> a <?= date('d/m/Y', strtotime($reserva['data_fim'])) ?></td>
                            <td>R$ <?= htmlspecialchars(number_format($reserva['valor_total'], 2, ',', '.')) ?></td>
                            <td>
                                <?php
                                $status = $reserva['status'];
                                $label = $status;

                                if ($status === 'aguardando_retirada') {
                                    $label = 'Aguardando Retirada';
                                } else {
                                    // deixa só a primeira letra maiúscula
                                    $label = ucfirst($status);
                                }
                                ?>
                                <span class="badge badge-<?= strtolower($status) ?>">
                                    <?= $label ?>
                                </span>
                            </td>

                            <td class="actions">
                                <?php 
                                // 🔹 Botão "Entregar / Iniciar" aparece quando a reserva está aguardando retirada
                                if ($reserva['status'] === 'aguardando_retirada'): ?>
                                    <form action="controller/ReservaControl.php" method="POST" class="d-inline">
                                        <input type="hidden" name="acao" value="mudar_status_ativa">
                                        <input type="hidden" name="cod_reserva" value="<?= $reserva['cod_reserva'] ?>">
                                        <button type="submit" class="btn-action" style="background-color: #28a745; color: #fff;">
                                            Entregar
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($reserva['status'] === 'concluida'): ?>
                                    <a href="view/admin/multas.php?cod_reserva=<?= $reserva['cod_reserva'] ?>" 
                                    class="btn-action" 
                                    style="background-color: #ffc107; color: white;">
                                    Multa
                                    </a>
                                <?php endif; ?>

                                <button type="button" 
                                        class="btn-action edit" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editReservaModal"
                                        data-id="<?= $reserva['cod_reserva'] ?>"
                                        data-cod-carro="<?= $reserva['cod_carro'] ?>"
                                        data-inicio="<?= date('Y-m-d', strtotime($reserva['data_inicio'])) ?>"
                                        data-fim="<?= date('Y-m-d', strtotime($reserva['data_fim'])) ?>"
                                        data-valor="<?= number_format($reserva['valor_total'], 2, ',', '.') ?>"
                                        data-status="<?= $reserva['status'] ?>">
                                    Editar
                                </button>

                                <?php if (in_array($usuarioPerfil, ['admin', 'gerente'])): ?>
                                    <form action="controller/ReservaControl.php" 
                                        method="POST" 
                                        class="d-inline" 
                                        onsubmit="return confirm('Tem certeza que deseja excluir esta reserva?');">
                                        <input type="hidden" name="acao" value="excluir_reserva">
                                        <input type="hidden" name="cod_reserva" value="<?= $reserva['cod_reserva'] ?>">
                                        <button type="submit" class="btn-action delete">Excluir</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="editReservaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editReservaForm" action="controller/ReservaControl.php" method="POST" class="form-loading-feedback">
                <div class="modal-body">
                    <p id="aviso-pago" class="alert alert-info py-2 px-3" style="display:none; font-size:.85rem;">
                        Esta reserva já está paga e aguardando retirada.
                    </p>
                    <input type="hidden" name="acao" value="atualizar_reserva">
                    <input type="hidden" id="edit-id-reserva" name="cod_reserva">
                    <input type="hidden" id="edit-cod-carro" name="cod_carro">
                    <div class="mb-3"><label for="edit-data-inicio" class="form-label">Data de Retirada</label><input type="date" class="form-control" id="edit-data-inicio" name="data_inicio" required></div>
                    <div class="mb-3"><label for="edit-data-fim" class="form-label">Data de Devolução</label><input type="date" class="form-control" id="edit-data-fim" name="data_fim" required></div>
                    
                    <div class="mb-3">
                        <label for="edit-valor-total" class="form-label">Valor Total (R$)</label>
                        <input type="text" class="form-control" id="edit-valor-total" name="valor_total" required placeholder="Ex: 250,50">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-status-reserva" class="form-label">Status</label>
                        <select id="edit-status-reserva" name="status" class="form-select" required>
                            <option value="pendente">Pendente</option>
                            <option value="aguardando_retirada">Aguardando Retirada</option>
                            <option value="ativa">Ativa</option>
                            <option value="concluida">Concluída</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModalEl = document.getElementById('editReservaModal');
    if (editModalEl) {
        editModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id = button.getAttribute('data-id');
            const codCarro = button.getAttribute('data-cod-carro');
            const inicio = button.getAttribute('data-inicio');
            const fim = button.getAttribute('data-fim');
            const valor = button.getAttribute('data-valor');
            const status = button.getAttribute('data-status');

            const form = this.querySelector('form');
            form.querySelector('#edit-id-reserva').value = id;
            form.querySelector('#edit-cod-carro').value = codCarro;
            form.querySelector('#edit-data-inicio').value = inicio;
            form.querySelector('#edit-data-fim').value = fim;
            form.querySelector('#edit-valor-total').value = valor;
            form.querySelector('#edit-status-reserva').value = status;
        });
    }
    const statusSelect = form.querySelector('#edit-status-reserva');
    statusSelect.value = status;

    const avisoPago = form.querySelector('#aviso-pago');
    if (avisoPago) {
    if (status === 'aguardando_retirada') {
        avisoPago.style.display = 'block';
    } else {
        avisoPago.style.display = 'none';
    }
    }
});
</script>