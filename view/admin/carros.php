<?php

$perfisPermitidos = ['admin', 'gerente', 'funcionario'];

$pageTitle = "Gerenciar Frota de Carros";

require_once __DIR__ . '/../layout/header.php';

if (!in_array($usuarioPerfil, ['admin', 'gerente', 'funcionario'])) {
    header("Location: " . BASE_URL . "/public/index.php?erro=" . urlencode("Acesso negado!"));
    exit;
}
require_once __DIR__ . '/../../model/dao/CarroDAO.php';
$carroDAO = new CarroDAO();

// --- LÓGICA DE FILTROS ---
$filtros = [
    'busca' => $_GET['busca'] ?? '',
    'status' => $_GET['status'] ?? ''
];
$carros = $carroDAO->getAll($filtros);
?>

<div class="content-management">
    <div class="table-header">
        <h2>Gerenciamento de Frota</h2>
        <p>Adicione, edite e filtre os veículos do sistema.</p>
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
        <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#createCarModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" 
            viewBox="0 0 24 24" fill="none" stroke="currentColor" 
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Novo Carro</span>
        </button>

        <form action="view/admin/carros.php" method="GET" class="d-flex align-items-center gap-2">
            <input type="search" name="busca" class="form-control" placeholder="Buscar por marca ou modelo..." value="<?= htmlspecialchars($filtros['busca']) ?>">
            
            <select name="status" class="form-select">
                <option value="">Todos os Status</option>
                <option value="disponivel" <?= $filtros['status'] == 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                <option value="reservado" <?= $filtros['status'] == 'reservado' ? 'selected' : '' ?>>Reservado</option>
                <option value="alugado" <?= $filtros['status'] == 'alugado' ? 'selected' : '' ?>>Alugado</option>
                <option value="manutencao" <?= $filtros['status'] == 'manutencao' ? 'selected' : '' ?>>Manutenção</option>
            </select>
            
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="view/admin/carros.php" class="btn btn-outline-secondary" title="Limpar Filtros">X</a>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr><th>Marca</th><th>Modelo</th><th>Ano</th><th>Preço/Dia</th><th>Status</th><th class="actions-header">Ações</th></tr>
            </thead>
            <tbody>
                <?php if (empty($carros)): ?>
                    <tr><td colspan="6" class="no-results">Nenhum carro encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($carros as $carro): ?>
                        <tr>
                            <td><?= htmlspecialchars($carro['marca']) ?></td>
                            <td><?= htmlspecialchars($carro['modelo']) ?></td>
                            <td><?= htmlspecialchars($carro['ano']) ?></td>
                            <td>R$ <?= htmlspecialchars(number_format($carro['preco_diaria'], 2, ',', '.')) ?></td>
                            <td><span class="badge badge-<?= strtolower($carro['status']) ?>"><?= ucfirst($carro['status']) ?></span></td>
                            <td class="actions">
                                <button type="button" class="btn-action edit" data-bs-toggle="modal" data-bs-target="#editCarModal"
                                        data-id="<?= $carro['cod_carro'] ?>" data-marca="<?= $carro['marca'] ?>" data-modelo="<?= $carro['modelo'] ?>"
                                        data-ano="<?= $carro['ano'] ?>" data-cor="<?= $carro['cor'] ?>" data-preco="<?= $carro['preco_diaria'] ?>"
                                        data-status="<?= $carro['status'] ?>" data-combustivel="<?= $carro['combustivel'] ?>" data-cambio="<?= $carro['cambio'] ?>">Editar</button>
                                
                                <form action="controller/CarroControl.php" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza?');">
                                    <input type="hidden" name="acao" value="excluir"><input type="hidden" name="cod_carro" value="<?= $carro['cod_carro'] ?>">
                                    <button type="submit" class="btn-action delete">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createCarModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Adicionar Novo Carro</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="controller/CarroControl.php" method="POST">
        <div class="modal-body">
            <input type="hidden" name="acao" value="cadastrar">
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Marca</label><input type="text" class="form-control" name="marca" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Modelo</label><input type="text" class="form-control" name="modelo" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Ano</label><input type="number" class="form-control" name="ano" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Cor</label><input type="text" class="form-control" name="cor" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Preço da Diária</label><input type="text" class="form-control" name="preco_diaria" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select" required><option value="disponivel">Disponível</option><option value="manutencao">Manutenção</option></select></div>
                <div class="col-md-6 mb-3"><label class="form-label">Combustível</label><select name="combustivel" class="form-select" required><option value="gasolina">Gasolina</option><option value="alcool">Álcool</option><option value="flex">Flex</option><option value="diesel">Diesel</option></select></div>
                <div class="col-md-6 mb-3"><label class="form-label">Câmbio</label><select name="cambio" class="form-select" required><option value="manual">Manual</option><option value="automatico">Automático</option></select></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Salvar</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="editCarModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Editar Carro</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form id="editCarForm" action="controller/CarroControl.php" method="POST">
        <div class="modal-body">
            <input type="hidden" name="acao" value="atualizar"><input type="hidden" id="edit-id" name="cod_carro">
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Marca</label><input type="text" class="form-control" id="edit-marca" name="marca" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Modelo</label><input type="text" class="form-control" id="edit-modelo" name="modelo" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Ano</label><input type="number" class="form-control" id="edit-ano" name="ano" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Cor</label><input type="text" class="form-control" id="edit-cor" name="cor" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Preço da Diária</label><input type="text" class="form-control" id="edit-preco" name="preco_diaria" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Status</label><select id="edit-status" name="status" class="form-select" required><option value="disponivel">Disponível</option><option value="manutencao">Manutenção</option><option value="reservado">Reservado</option><option value="alugado">Alugado</option></select></div>
                <div class="col-md-6 mb-3"><label class="form-label">Combustível</label><select id="edit-combustivel" name="combustivel" class="form-select" required><option value="gasolina">Gasolina</option><option value="alcool">Álcool</option><option value="flex">Flex</option><option value="diesel">Diesel</option></select></div>
                <div class="col-md-6 mb-3"><label class="form-label">Câmbio</label><select id="edit-cambio" name="cambio" class="form-select" required><option value="manual">Manual</option><option value="automatico">Automático</option></select></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Salvar Alterações</button></div>
    </form>
</div></div></div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
document.getElementById('editCarModal').addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var modal = this;
    modal.querySelector('#edit-id').value = button.getAttribute('data-id');
    modal.querySelector('#edit-marca').value = button.getAttribute('data-marca');
    modal.querySelector('#edit-modelo').value = button.getAttribute('data-modelo');
    modal.querySelector('#edit-ano').value = button.getAttribute('data-ano');
    modal.querySelector('#edit-cor').value = button.getAttribute('data-cor');
    modal.querySelector('#edit-preco').value = button.getAttribute('data-preco');
    modal.querySelector('#edit-status').value = button.getAttribute('data-status');
    modal.querySelector('#edit-combustivel').value = button.getAttribute('data-combustivel');
    modal.querySelector('#edit-cambio').value = button.getAttribute('data-cambio');
});
</script>