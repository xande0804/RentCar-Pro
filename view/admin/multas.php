<?php

$perfisPermitidos = ['admin', 'gerente', 'funcionario'];

$pageTitle = "Gerenciar Multas";

require_once __DIR__ . '/../layout/header.php';

// Segurança: Acesso restrito
if (!in_array($usuarioPerfil, ['admin', 'gerente', 'funcionario'])) {
    header("Location: " . BASE_URL . "/public/index.php?erro=" . urlencode("Acesso negado!"));
    exit;
}

require_once __DIR__ . '/../../model/dao/MultaDAO.php';
$multaDAO = new MultaDAO();
$multas = $multaDAO->getAll();
?>

<div class="content-management">
    <div class="table-header">
        <h2>Gerenciamento de Multas</h2>
        <p>Registre e controle as multas associadas às reservas.</p>
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
        <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#createMultaModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" 
            viewBox="0 0 24 24" fill="none" stroke="currentColor" 
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Nova Multa</span>
        </button>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reserva</th>
                    <th>Cliente</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th class="actions-header">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($multas)): ?>
                    <tr><td colspan="7" class="no-results">Nenhuma multa registrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($multas as $multa): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($multa['cod_reserva']) ?></td>
                            <td><?= htmlspecialchars($multa['nome_cliente']) ?></td>
                            <td><?= htmlspecialchars($multa['descricao']) ?></td>
                            <td>R$ <?= htmlspecialchars(number_format($multa['valor'], 2, ',', '.')) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($multa['data_vencimento']))) ?></td>
                            <td>
                                <span class="badge text-bg-<?= $multa['status'] == 'paga' ? 'success' : ($multa['status'] == 'cancelada' ? 'secondary' : 'warning') ?>">
                                    <?= ucfirst($multa['status']) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <button type="button" class="btn-action edit" data-bs-toggle="modal" data-bs-target="#editMultaModal"
                                        data-cod-multa="<?= $multa['cod_multa'] ?>"
                                        data-descricao="<?= htmlspecialchars($multa['descricao']) ?>"
                                        data-valor="<?= number_format($multa['valor'], 2, ',', '.') ?>"
                                        data-vencimento="<?= date('Y-m-d', strtotime($multa['data_vencimento'])) ?>"
                                        data-status="<?= $multa['status'] ?>"
                                        data-observacoes="<?= htmlspecialchars($multa['observacoes']) ?>">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createMultaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nova Multa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="controller/MultaControl.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="cadastrar">
                    <div class="mb-3">
                        <label for="cod_reserva" class="form-label">ID da Reserva</label>
                        <input type="number" class="form-control" name="cod_reserva" id="cod_reserva" required>
                        <small class="form-text text-muted">A multa deve ser associada a uma reserva existente.</small>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição (Motivo)</label>
                        <input type="text" class="form-control" name="descricao" id="descricao" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="valor" class="form-label">Valor</label>
                            <input type="text" class="form-control" name="valor" id="valor" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="data_vencimento" class="form-label">Data de Vencimento</label>
                            <input type="date" class="form-control" name="data_vencimento" id="data_vencimento" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações (Opcional)</label>
                        <textarea class="form-control" name="observacoes" id="observacoes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Multa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editMultaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Multa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="controller/MultaControl.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="acao" value="atualizar">
                    <input type="hidden" name="cod_multa" id="edit-cod-multa">
                    
                    <div class="mb-3">
                        <label for="edit-descricao" class="form-label">Descrição (Motivo)</label>
                        <input type="text" class="form-control" name="descricao" id="edit-descricao" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit-valor" class="form-label">Valor (R$)</label>
                            <input type="text" class="form-control" name="valor" id="edit-valor" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit-data-vencimento" class="form-label">Data de Vencimento</label>
                            <input type="date" class="form-control" name="data_vencimento" id="edit-data-vencimento" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Status</label>
                        <select name="status" id="edit-status" class="form-select">
                            <option value="pendente">Pendente</option>
                            <option value="paga">Paga</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-observacoes" class="form-label">Observações (Opcional)</label>
                        <textarea class="form-control" name="observacoes" id="edit-observacoes" rows="3"></textarea>
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
    // CORREÇÃO: Pega os elementos do modal no escopo principal do script
    const createModalEl = document.getElementById('createMultaModal');
    const editModalEl = document.getElementById('editMultaModal');
    const urlParams = new URLSearchParams(window.location.search);
    const codReserva = urlParams.get('cod_reserva');

    // Lida com o modal de CRIAÇÃO
    if (createModalEl) {
        // Abre o modal se veio da tela de reservas
        if (codReserva) {
            const codReservaInput = createModalEl.querySelector('#cod_reserva');
            if (codReservaInput) {
                codReservaInput.value = codReserva;
                const createModal = new bootstrap.Modal(createModalEl);
                createModal.show();
            }
        }
        // Limpa o formulário ao fechar
        createModalEl.addEventListener('hide.bs.modal', function () {
            this.querySelector('form')?.reset();
        });
    }

    // Lida com o modal de EDIÇÃO
    if (editModalEl) {
        editModalEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const form = this.querySelector('form');

            form.querySelector('#edit-cod-multa').value = button.dataset.codMulta;
            form.querySelector('#edit-descricao').value = button.dataset.descricao;
            form.querySelector('#edit-valor').value = button.dataset.valor;
            form.querySelector('#edit-data-vencimento').value = button.dataset.vencimento;
            form.querySelector('#edit-status').value = button.dataset.status;
            form.querySelector('#edit-observacoes').value = button.dataset.observacoes;
        });
    }
});
</script>