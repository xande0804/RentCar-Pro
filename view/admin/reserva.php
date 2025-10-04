<?php
$pageTitle = "Gerenciar Reservas";
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../layout/header.php';

if (!in_array($usuarioPerfil, ['admin', 'gerente', 'funcionario'])) { exit; }
require_once __DIR__ . '/../../model/dao/ReservaDAO.php';
$reservaDAO = new ReservaDAO();
$reservas = $reservaDAO->getAll();
?>

<div class="content-management">
    <div class="table-header">
        <h2>Gerenciamento de Reservas</h2>
        <p>Visualize e gerencie todas as reservas do sistema.</p>
    </div>
    
    <?php /* Bloco para exibir Flash Messages */ ?>

    <div class="table-controls">
        <a href="view/carros/index.php" class="btn-add">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Nova Reserva</span>
        </a>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr><th>Cliente</th><th>Carro</th><th>Período</th><th>Valor</th><th>Status</th><th class="actions-header">Ações</th></tr>
            </thead>
            <tbody>
                <?php if (empty($reservas)): ?>
                    <tr><td colspan="6" class="no-results">Nenhuma reserva encontrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['nome_usuario']) ?></td>
                            <td><?= htmlspecialchars($reserva['marca'] . ' ' . $reserva['modelo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($reserva['data_inicio'])) ?> a <?= date('d/m/Y', strtotime($reserva['data_fim'])) ?></td>
                            <td>R$ <?= htmlspecialchars(number_format($reserva['valor_total'], 2, ',', '.')) ?></td>
                            <td><span class="badge badge-<?= strtolower($reserva['status']) ?>"><?= ucfirst($reserva['status']) ?></span></td>
                            <td class="actions">
                                <button type="button" class="btn-action edit" data-bs-toggle="modal" data-bs-target="#editReservaModal"
                                        data-id="<?= $reserva['cod_reserva'] ?>"
                                        data-cod-carro="<?= $reserva['cod_carro'] ?>"
                                        data-inicio="<?= date('Y-m-d', strtotime($reserva['data_inicio'])) ?>"
                                        data-fim="<?= date('Y-m-d', strtotime($reserva['data_fim'])) ?>"
                                        data-status="<?= $reserva['status'] ?>">Editar</button>
                                
                                <?php
                                if (in_array($usuarioPerfil, ['admin', 'gerente'])): 
                                ?>
                                    <form action="controller/ReservaControl.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta reserva?');">
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

<div class="modal fade" id="editReservaModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Editar Reserva</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form id="editReservaForm" action="controller/ReservaControl.php" method="POST">
        <div class="modal-body">
            <input type="hidden" name="acao" value="atualizar_reserva">
            <input type="hidden" id="edit-id-reserva" name="cod_reserva">
            <input type="hidden" id="edit-cod-carro" name="cod_carro">
            <div class="mb-3"><label for="edit-data-inicio" class="form-label">Data de Retirada</label><input type="date" class="form-control" id="edit-data-inicio" name="data_inicio" required></div>
            <div class="mb-3"><label for="edit-data-fim" class="form-label">Data de Devolução</label><input type="date" class="form-control" id="edit-data-fim" name="data_fim" required></div>
            <div class="mb-3">
                <label for="edit-status-reserva" class="form-label">Status</label>
                <select id="edit-status-reserva" name="status" class="form-select" required>
                    <option value="pendente">Pendente</option><option value="ativa">Ativa</option>
                    <option value="concluida">Concluída</option><option value="cancelada">Cancelada</option>
                </select>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Salvar Alterações</button></div>
    </form>
</div></div></div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
<script>
document.getElementById('editReservaModal').addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var modal = this;
    modal.querySelector('#edit-id-reserva').value = button.getAttribute('data-id');
    modal.querySelector('#edit-cod-carro').value = button.getAttribute('data-cod-carro');
    modal.querySelector('#edit-data-inicio').value = button.getAttribute('data-inicio');
    modal.querySelector('#edit-data-fim').value = button.getAttribute('data-fim');
    modal.querySelector('#edit-status-reserva').value = button.getAttribute('data-status');
});
</script>