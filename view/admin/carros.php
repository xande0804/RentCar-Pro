<?php
$perfisPermitidos = ['admin', 'gerente', 'funcionario'];
$pageTitle = "Gerenciar Frota de Carros";
require_once __DIR__ . '/../layout/header.php';

// Segurança adicional (além do header.php já trazer $_SESSION etc.)
if (!in_array($usuarioPerfil, ['admin', 'gerente', 'funcionario'])) {
    header("Location: " . BASE_URL . "/public/index.php?erro=" . urlencode("Acesso negado!"));
    exit;
}

require_once __DIR__ . '/../../model/dao/CarroDAO.php';
$carroDAO = new CarroDAO();

// --- LÓGICA DE FILTROS ---
$filtros = [
    'busca'  => $_GET['busca']  ?? '',
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
                <option value="reservado"  <?= $filtros['status'] == 'reservado'  ? 'selected' : '' ?>>Reservado</option>
                <option value="alugado"    <?= $filtros['status'] == 'alugado'    ? 'selected' : '' ?>>Alugado</option>
                <option value="manutencao" <?= $filtros['status'] == 'manutencao' ? 'selected' : '' ?>>Manutenção</option>
            </select>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="view/admin/carros.php" class="btn btn-outline-secondary" title="Limpar Filtros">X</a>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Imagem</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Ano</th>
                    <th>Categoria</th>
                    <th>Preço/Dia</th>
                    <th>KM Total</th>
                    <th>Status</th>
                    <th class="actions-header">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($carros)): ?>
                    <tr><td colspan="9" class="no-results">Nenhum carro encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($carros as $carro): ?>
                        <tr>
                            <td>
                                <?php
                                $img = $carro['imagem_url'] ?? '';
                                $src = $img
                                    ? (filter_var($img, FILTER_VALIDATE_URL) ? $img : BASE_URL . '/' . ltrim($img, '/'))
                                    : 'https://placehold.co/100x60/e2e8f0/cccccc?text=S/Foto';
                                ?>
                                <img src="<?= htmlspecialchars($src) ?>" alt="Carro" style="width:70px;height:40px;object-fit:cover;border-radius:4px;">
                            </td>
                            <td><?= htmlspecialchars($carro['marca']) ?></td>
                            <td><?= htmlspecialchars($carro['modelo']) ?></td>
                            <td><?= htmlspecialchars($carro['ano']) ?></td>
                            <td><?= htmlspecialchars($carro['categoria'] ?? '—') ?></td>
                            <td>R$ <?= htmlspecialchars(number_format($carro['preco_diaria'], 2, ',', '.')) ?></td>
                            <td><?= (int)($carro['km_total'] ?? 0) ?></td>
                            <td><span class="badge badge-<?= strtolower($carro['status']) ?>"><?= ucfirst($carro['status']) ?></span></td>
                            <td class="actions">
                                <button
                                    type="button"
                                    class="btn-action edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editCarModal"
                                    data-id="<?= $carro['cod_carro'] ?>"
                                    data-marca="<?= htmlspecialchars($carro['marca']) ?>"
                                    data-modelo="<?= htmlspecialchars($carro['modelo']) ?>"
                                    data-ano="<?= htmlspecialchars($carro['ano']) ?>"
                                    data-cor="<?= htmlspecialchars($carro['cor']) ?>"
                                    data-preco="<?= htmlspecialchars($carro['preco_diaria']) ?>"
                                    data-status="<?= htmlspecialchars($carro['status']) ?>"
                                    data-combustivel="<?= htmlspecialchars($carro['combustivel']) ?>"
                                    data-cambio="<?= htmlspecialchars($carro['cambio']) ?>"
                                    data-ar="<?= (int)($carro['ar_condicionado'] ?? 0) ?>"
                                    data-categoria="<?= htmlspecialchars($carro['categoria'] ?? '') ?>"
                                    data-km="<?= (int)($carro['km_total'] ?? 0) ?>"
                                    data-descricao="<?= htmlspecialchars($carro['descricao'] ?? '') ?>"
                                    data-imagem-url="<?= htmlspecialchars($carro['imagem_url'] ?? '') ?>"
                                >Editar</button>

                                <form action="controller/CarroControl.php" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza?');">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="cod_carro" value="<?= $carro['cod_carro'] ?>">
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

<!-- Modal: Novo Carro -->
<div class="modal fade" id="createCarModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Adicionar Novo Carro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <form action="controller/CarroControl.php" method="POST" class="form-loading-feedback" enctype="multipart/form-data">
      <div class="modal-body">
        <input type="hidden" name="acao" value="cadastrar">
        <div class="row">
          <div class="col-md-6 mb-3"><label class="form-label">Marca</label><input type="text" class="form-control" name="marca" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Modelo</label><input type="text" class="form-control" name="modelo" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Ano</label><input type="number" class="form-control" name="ano" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Cor</label><input type="text" class="form-control" name="cor" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Preço da Diária</label><input type="number" class="form-control" name="preco_diaria" step="0.01" inputmode="decimal" autocomplete="off" onwheel="this.blur()" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select" required><option value="disponivel">Disponível</option><option value="manutencao">Manutenção</option></select></div>
          <div class="col-md-6 mb-3"><label class="form-label">Combustível</label><select name="combustivel" class="form-select" required><option value="gasolina">Gasolina</option><option value="alcool">Álcool</option><option value="flex">Flex</option><option value="diesel">Diesel</option></select></div>
          <div class="col-md-6 mb-3"><label class="form-label">Câmbio</label><select name="cambio" class="form-select" required><option value="manual">Manual</option><option value="automatico">Automático</option></select></div>
          <div class="col-md-6 mb-3">
            <label for="novo-categoria" class="form-label">Categoria</label>
            <select name="categoria" id="novo-categoria" class="form-select" required>
              <option value="">Selecione...</option>
              <?php foreach (CATEGORIAS_CARRO as $valor => $label): if ($valor === 'NaoClassificado') continue; ?>
                <option value="<?= htmlspecialchars($valor) ?>"><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6 mb-3"><label class="form-label">KM Total</label><input type="number" class="form-control" name="km_total" min="0" step="1" value="0"></div>
          <div class="col-md-6 mb-3"><label class="form-label d-block">Ar-condicionado</label><div class="form-check"><input class="form-check-input" type="checkbox" id="novo-ar" name="ar_condicionado" checked><label class="form-check-label" for="novo-ar">Possui ar-condicionado</label></div></div>
          <div class="col-12 mb-3"><label class="form-label">Descrição</label><textarea class="form-control" name="descricao" rows="2" placeholder="Opcional"></textarea></div>

          <div class="col-12"><hr></div>
          <div class="col-12 mb-2"><strong>Imagem do Veículo (principal)</strong></div>
          <div class="col-12 mb-2">
            <div class="form-check"><input class="form-check-input" type="radio" name="metodo_imagem" id="novo-metodo-url" value="url" checked data-bs-target="#novo-campos-imagem"><label class="form-check-label" for="novo-metodo-url">Inserir URL</label></div>
            <div class="form-check"><input class="form-check-input" type="radio" name="metodo_imagem" id="novo-metodo-upload" value="upload" data-bs-target="#novo-campos-imagem"><label class="form-check-label" for="novo-metodo-upload">Fazer Upload (Max 2MB: jpg, png, webp)</label></div>
          </div>
          <div id="novo-campos-imagem">
            <div class="col-12 mb-3" data-metodo-container="url">
              <label class="form-label" for="novo-imagem-url">URL da Imagem</label>
              <input type="text" class="form-control" name="imagem_url" id="novo-imagem-url" placeholder="https://exemplo.com/imagem.jpg">
            </div>
            <div class="col-12 mb-3" data-metodo-container="upload" style="display:none;">
              <label class="form-label" for="novo-imagem-upload">Arquivo de Imagem</label>
              <input type="file" class="form-control" name="imagem_upload" id="novo-imagem-upload" accept="image/jpeg, image/png, image/webp">
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Modal: Editar Carro -->
<div class="modal fade" id="editCarModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Editar Carro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <form id="editCarForm" action="controller/CarroControl.php" method="POST" class="form-loading-feedback" enctype="multipart/form-data">
      <div class="modal-body">
        <input type="hidden" name="acao" value="atualizar">
        <input type="hidden" id="edit-id" name="cod_carro">

        <div class="row">
          <div class="col-md-6 mb-3"><label class="form-label">Marca</label><input type="text" class="form-control" id="edit-marca" name="marca" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Modelo</label><input type="text" class="form-control" id="edit-modelo" name="modelo" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Ano</label><input type="number" class="form-control" id="edit-ano" name="ano" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Cor</label><input type="text" class="form-control" id="edit-cor" name="cor" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Preço da Diária</label><input type="number" class="form-control" id="edit-preco" name="preco_diaria" step="0.01" inputmode="decimal" onwheel="this.blur()" autocomplete="off" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Status</label><select id="edit-status" name="status" class="form-select" required><option value="disponivel">Disponível</option><option value="manutencao">Manutenção</option><option value="reservado">Reservado</option><option value="alugado">Alugado</option></select></div>
          <div class="col-md-6 mb-3"><label class="form-label">Combustível</label><select id="edit-combustivel" name="combustivel" class="form-select" required><option value="gasolina">Gasolina</option><option value="alcool">Álcool</option><option value="flex">Flex</option><option value="diesel">Diesel</option></select></div>
          <div class="col-md-6 mb-3"><label class="form-label">Câmbio</label><select id="edit-cambio" name="cambio" class="form-select" required><option value="manual">Manual</option><option value="automatico">Automático</option></select></div>
          <div class="col-md-6 mb-3">
            <label for="edit-categoria" class="form-label">Categoria</label>
            <select name="categoria" id="edit-categoria" class="form-select" required>
              <option value="">Selecione...</option>
              <?php foreach (CATEGORIAS_CARRO as $valor => $label): if ($valor === 'NaoClassificado') continue; ?>
                <option value="<?= htmlspecialchars($valor) ?>"><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6 mb-3"><label class="form-label">KM Total</label><input type="number" class="form-control" id="edit-km" name="km_total" min="0" step="1" value="0"></div>
          <div class="col-md-6 mb-3"><label class="form-label d-block">Ar-condicionado</label><div class="form-check"><input class="form-check-input" type="checkbox" id="edit-ar" name="ar_condicionado"><label class="form-check-label" for="edit-ar">Possui ar-condicionado</label></div></div>
          <div class="col-12 mb-3"><label class="form-label">Descrição</label><textarea class="form-control" id="edit-descricao" name="descricao" rows="2" placeholder="Opcional"></textarea></div>

          <div class="col-12"><hr></div>

          <div class="col-12 mb-3">
            <label class="form-label">Imagem Atual</label>
            <div>
                <img id="edit-imagem-preview" src="https://placehold.co/150x90/e2e8f0/cccccc?text=S/Foto" style="height:90px;width:150px;object-fit:cover;border-radius:4px;border:1px solid #ddd;">
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="remover_imagem" id="edit-remover-imagem" value="1">
                <label class="form-check-label" for="edit-remover-imagem">Remover imagem atual (deixar em branco)</label>
            </div>
          </div>

          <div class="col-12 mb-3">
            <label class="form-label">Alterar Imagem</label>
            <div class="form-check"><input class="form-check-input" type="radio" name="metodo_imagem" id="edit-metodo-url" value="url" checked data-bs-target="#edit-campos-imagem"><label class="form-check-label" for="edit-metodo-url">Inserir nova URL</label></div>
            <div class="form-check"><input class="form-check-input" type="radio" name="metodo_imagem" id="edit-metodo-upload" value="upload" data-bs-target="#edit-campos-imagem"><label class="form-check-label" for="edit-metodo-upload">Fazer novo Upload (Max 2MB: jpg, png, webp)</label></div>
          </div>

          <div id="edit-campos-imagem">
            <div class="col-12 mb-3" data-metodo-container="url">
              <label class="form-label" for="edit-imagem-url">URL da Imagem</label>
              <input type="text" class="form-control" name="imagem_url" id="edit-imagem-url" placeholder="Deixe em branco para manter a imagem atual">
            </div>
            <div class="col-12 mb-3" data-metodo-container="upload" style="display:none;">
              <label class="form-label" for="edit-imagem-upload">Arquivo de Imagem</label>
              <input type="file" class="form-control" name="imagem_upload" id="edit-imagem-upload" accept="image/jpeg, image/png, image/webp">
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
      </div>
    </form>
  </div></div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
// Preenche o modal de Edição
document.getElementById('editCarModal').addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var modal  = this;

    modal.querySelector('#edit-id').value       = button.getAttribute('data-id');
    modal.querySelector('#edit-marca').value    = button.getAttribute('data-marca');
    modal.querySelector('#edit-modelo').value   = button.getAttribute('data-modelo');
    modal.querySelector('#edit-ano').value      = button.getAttribute('data-ano');
    modal.querySelector('#edit-cor').value      = button.getAttribute('data-cor');
    modal.querySelector('#edit-preco').value    = button.getAttribute('data-preco');
    modal.querySelector('#edit-status').value   = button.getAttribute('data-status');
    modal.querySelector('#edit-combustivel').value = button.getAttribute('data-combustivel');
    modal.querySelector('#edit-cambio').value   = button.getAttribute('data-cambio');

    var temAr = parseInt(button.getAttribute('data-ar') || '0', 10) === 1;
    modal.querySelector('#edit-ar').checked = temAr;

    var categoria = button.getAttribute('data-categoria') || '';
    var km        = button.getAttribute('data-km') || '0';
    modal.querySelector('#edit-categoria').value = categoria;
    modal.querySelector('#edit-km').value        = parseInt(km, 10);
    modal.querySelector('#edit-descricao').value = button.getAttribute('data-descricao') || '';

    var imagemUrl  = button.getAttribute('data-imagem-url') || '';
    var previewImg = modal.querySelector('#edit-imagem-preview');
    var urlInput   = modal.querySelector('#edit-imagem-url');

    if (imagemUrl) {
        if (imagemUrl.startsWith('http')) previewImg.src = imagemUrl;
        else previewImg.src = '<?= BASE_URL ?>/' + imagemUrl.replace(/^\/+/, '');
        urlInput.value = imagemUrl;
    } else {
        previewImg.src = 'https://placehold.co/150x90/e2e8f0/cccccc?text=S/Foto';
        urlInput.value = '';
    }

    modal.querySelector('#edit-remover-imagem').checked = false;
    modal.querySelector('#edit-imagem-upload').value = '';

    modal.querySelector('#edit-metodo-url').checked = true;
    modal.querySelector('#edit-campos-imagem [data-metodo-container="url"]').style.display = 'block';
    modal.querySelector('#edit-campos-imagem [data-metodo-container="upload"]').style.display = 'none';
});

function setupImageToggle(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    const radios = modal.querySelectorAll('input[name="metodo_imagem"]');
    const container = modal.querySelector(radios[0].getAttribute('data-bs-target'));
    if (!container) return;

    const urlField    = container.querySelector('[data-metodo-container="url"]');
    const uploadField = container.querySelector('[data-metodo-container="upload"]');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'upload') {
                urlField.style.display = 'none';
                uploadField.style.display = 'block';
            } else {
                urlField.style.display = 'block';
                uploadField.style.display = 'none';
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    setupImageToggle('createCarModal');
    setupImageToggle('editCarModal');
});
</script>
