<?php
session_start();
require_once __DIR__ . '/../../model/dao/FavoritoDAO.php';
require_once __DIR__ . '/../../model/dao/CarroDAO.php';
require_once __DIR__ . '/../../model/dao/UsuarioDAO.php';

$pageTitle = "Catálogo de Veículos - RentCar Pro";
$jsFiles   = ['reserva-admin.js'];

$favoritoDAO = new FavoritoDAO();
$carroDAO    = new CarroDAO();
$usuarioDAO  = new UsuarioDAO();

$usuarioLogado   = !empty($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true;

$filtros = [
    'categoria' => $_GET['categoria'] ?? '',
    'cambio' => $_GET['cambio'] ?? '',
    'combustivel' => $_GET['combustivel'] ?? '',
    'preco_min' => $_GET['preco_min'] ?? '',
    'preco_max' => $_GET['preco_max'] ?? '',
    'data_inicio' => $_GET['data_inicio'] ?? '',
    'data_fim' => $_GET['data_fim'] ?? '',
    'ordenar' => $_GET['ordenar'] ?? 'padrao',
    'somente_favoritos' => isset($_GET['somente_favoritos']),
    'cod_usuario' => $usuarioLogado ? $_SESSION['usuario']['id'] : null
];

$carros = $carroDAO->getAvailable($filtros);
$clientes = $usuarioDAO->getAllClientesEUsuarios();

$todasCategorias = array_unique(array_column($carroDAO->getAll(), 'categoria'));
$categorias = array_filter($todasCategorias);
$carrosFavoritados = $usuarioLogado ? $favoritoDAO->getFavoritosByUsuario($_SESSION['usuario']['id']) : [];
require_once __DIR__ . '/../layout/header.php';
?>

<div class="vitrine-container">
    <div class="vitrine-header">
        <h1>Catálogo de Veículos</h1>
        <p>Encontre o veículo perfeito para sua viagem</p>
    </div>
    
    <form action="view/carros/index.php" method="GET">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-3"><label for="categoria" class="form-label">Categoria</label><select name="categoria" id="categoria" class="form-select"><option value="">Todos os tipos</option><?php foreach ($categorias as $cat): ?><option value="<?= htmlspecialchars($cat) ?>" <?= $filtros['categoria'] == $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-2"><label for="cambio" class="form-label">Transmissão</label><select name="cambio" id="cambio" class="form-select"><option value="">Qualquer</option><option value="manual" <?= $filtros['cambio'] == 'manual' ? 'selected' : '' ?>>Manual</option><option value="automatico" <?= $filtros['cambio'] == 'automatico' ? 'selected' : '' ?>>Automático</option></select></div>
                    <div class="col-md-2"><label for="combustivel" class="form-label">Combustível</label><select name="combustivel" id="combustivel" class="form-select"><option value="">Qualquer</option><option value="flex" <?= $filtros['combustivel'] == 'flex' ? 'selected' : '' ?>>Flex</option><option value="gasolina" <?= $filtros['combustivel'] == 'gasolina' ? 'selected' : '' ?>>Gasolina</option><option value="diesel" <?= $filtros['combustivel'] == 'diesel' ? 'selected' : '' ?>>Diesel</option><option value="alcool" <?= $filtros['combustivel'] == 'alcool' ? 'selected' : '' ?>>Álcool</option></select></div>
                    <div class="col-md-5"><label class="form-label">Preço por dia</label><div class="input-group"><span class="input-group-text">R$</span><input type="number" name="preco_min" class="form-control" placeholder="Mín" value="<?= htmlspecialchars($filtros['preco_min']) ?>"><span class="input-group-text">a R$</span><input type="number" name="preco_max" class="form-control" placeholder="Máx" value="<?= htmlspecialchars($filtros['preco_max']) ?>"></div></div>
                </div>
                <div class="row g-3 align-items-end">
                     <div class="col-md-3"><label for="data_inicio" class="form-label">Data de Retirada</label><input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?= htmlspecialchars($filtros['data_inicio']) ?>"></div>
                     <div class="col-md-3"><label for="data_fim" class="form-label">Data de Devolução</label><input type="date" name="data_fim" id="data_fim" class="form-control" value="<?= htmlspecialchars($filtros['data_fim']) ?>"></div>
                     <div class="col-md-3"><label for="local" class="form-label">Local</label><select name="local" id="local" class="form-select"><option value="">Selecione o local</option><option value="aeroporto">Aeroporto</option><option value="centro">Centro da Cidade</option></select></div>
                     <div class="col-md-3 d-flex"><button type="submit" class="btn btn-primary w-100 me-2">Filtrar</button><a href="view/carros/index.php" class="btn btn-outline-secondary" title="Limpar Filtros"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/></svg></a></div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div><strong><?= count($carros) ?> veículo(s) encontrado(s)</strong></div>
            <div class="d-flex align-items-center gap-3">
                <?php if ($usuarioLogado):
                    $isFavoritosMode = $filtros['somente_favoritos'];
                    $paramsParaBotao = $_GET;
                    if ($isFavoritosMode) {
                        unset($paramsParaBotao['somente_favoritos']);
                        $classeBtnFavoritos = 'btn-danger';
                    } else {
                        $paramsParaBotao['somente_favoritos'] = '1';
                        $classeBtnFavoritos = 'btn-outline-secondary';
                    }
                    $linkFavoritos = 'view/carros/index.php?' . http_build_query($paramsParaBotao);
                ?>
                    <a href="<?= $linkFavoritos ?>" class="btn <?= $classeBtnFavoritos ?> d-flex align-items-center gap-2 btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/></svg>
                        Favoritos
                    </a>
                <?php endif; ?>

                <div class="d-flex align-items-center gap-2">
                    <label for="ordenar" class="form-label mb-0 text-nowrap">Ordenar por:</label>
                    <select name="ordenar" id="ordenar" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="padrao" <?= $filtros['ordenar'] == 'padrao' ? 'selected' : '' ?>>Padrão</option>
                        <option value="preco_asc" <?= $filtros['ordenar'] == 'preco_asc' ? 'selected' : '' ?>>Menor Preço</option>
                        <option value="preco_desc" <?= $filtros['ordenar'] == 'preco_desc' ? 'selected' : '' ?>>Maior Preço</option>
                    </select>
                </div>
            </div>
        </div>
        <?php if ($filtros['somente_favoritos']) { echo '<input type="hidden" name="somente_favoritos" value="1">'; } ?>
    </form>

    <div class="car-gallery">
        <?php if (empty($carros)): ?>
            <p class="no-cars-message">Nenhum carro disponível encontrado para os filtros selecionados.</p>
        <?php else: ?>
            <?php foreach ($carros as $carro): ?>
                <div class="car-card-new">
                    <div class="car-card-img-container">
                        <?php
                        $img = $carro['imagem_url'] ?? '';
                        $src = $img
                            ? (filter_var($img, FILTER_VALIDATE_URL) ? $img : BASE_URL . '/' . ltrim($img, '/'))
                            : 'https://placehold.co/600x400/e2e8f0/cccccc?text=' . urlencode($carro['marca'] . ' ' . $carro['modelo']);
                        ?>
                        <img src="<?= htmlspecialchars($src) ?>" class="card-img-top" alt="<?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']); ?>">

                        <?php
                            $isFavorito   = in_array($carro['cod_carro'], $carrosFavoritados);
                            $linkFavorito = $usuarioLogado ? '#' : 'view/auth/login.php';
                        ?>
                        <a href="<?= $linkFavorito ?>" class="btn-favorito <?= $isFavorito ? 'favorito-ativo' : '' ?>" data-car-id="<?= $carro['cod_carro'] ?>" title="Adicionar aos favoritos">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/></svg>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="car-title mb-0"><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']); ?></h5>
                            <div class="text-end">
                                <p class="car-price mb-0">R$ <?= htmlspecialchars(number_format($carro['preco_diaria'], 2, ',', '.')) ?></p>
                                <small class="text-muted">por dia</small>
                            </div>
                        </div>

                        <p class="car-specs-main mt-1 mb-2">
                            <?= htmlspecialchars($carro['categoria'] ?: 'N/A') ?> &bullet;
                            <?= htmlspecialchars(ucfirst($carro['cambio'])) ?> &bullet;
                            <?= htmlspecialchars(ucfirst($carro['combustivel'])) ?>
                        </p>
                        <hr class="my-2">
                        <div class="car-specs-details">
                            <span class="spec-item"><?= htmlspecialchars($carro['lugares'] ?? '5') ?> lugares</span>
                            <span class="spec-item"><?= htmlspecialchars($carro['portas'] ?? '4') ?> portas</span>
                            <?php if (!empty($carro['ar_condicionado']) && $carro['ar_condicionado']): ?>
                                <span class="spec-item">Ar cond.</span>
                            <?php else: ?>
                                <span class="spec-item text-muted text-decoration-line-through">Sem ar</span>
                            <?php endif; ?>
                        </div>

                        <div class="mt-auto pt-3">
                            <?php if ($carro['status'] == 'disponivel'): ?>
                                <a href="controller/ReservaControl.php?acao=iniciar&id=<?= $carro['cod_carro']; ?>" class="btn btn-primary btn-details">Ver Detalhes</a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-details" disabled>Indisponível</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
// só mostra o modal se for staff
$perfil = $_SESSION['usuario']['perfil'] ?? 'visitante';
if (in_array($perfil, ['admin','gerente','funcionario'])):
?>
<!-- Modal: selecionar cliente para reserva -->
<div class="modal fade" id="selecionarClienteModal" tabindex="-1" aria-labelledby="selecionarClienteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="selecionarClienteModalLabel">Selecionar cliente para a reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body">
        <p class="mb-3 text-muted" style="font-size: .875rem;">
          Você está criando uma reserva em nome de um cliente. Escolha abaixo o cliente que vai alugar este carro.
        </p>

        <!-- campo de busca -->
        <div class="mb-3">
          <label for="search-cliente-modal" class="form-label">Buscar cliente</label>
          <input type="text" id="search-cliente-modal" class="form-control" placeholder="Digite o nome ou e-mail do cliente...">
        </div>

        <!-- lista que o JS vai preencher -->
        <div id="lista-clientes-modal" class="list-group" style="max-height: 360px; overflow-y: auto;">
          <!-- o JS reserva-admin.js monta isso aqui -->
        </div>

        <p class="mt-3 mb-0 text-muted" style="font-size: .75rem;">
          Clientes com cadastro incompleto vão ser redirecionados primeiro para completar o cadastro.
        </p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>



<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-favorito').forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.getAttribute('href') !== '#') return;
            e.preventDefault();
            const carId = this.dataset.carId;
            const heartIcon = this;
            const formData = new FormData();
            formData.append('cod_carro', carId);
            fetch('controller/FavoritoControl.php', { method: 'POST', body: formData })
              .then(response => response.json())
              .then(data => {
                  if (data.status === 'success') heartIcon.classList.toggle('favorito-ativo');
                  else alert(data.message || 'Ocorreu um erro.');
              })
              .catch(error => console.error('Erro:', error));
        });
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>