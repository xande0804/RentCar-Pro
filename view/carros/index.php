<?php
$pageTitle = "Catálogo de Veículos - RentCar Pro";
$jsFiles = ['reserva-admin.js'];
require_once __DIR__ . '/../layout/header.php';

require_once __DIR__ . '/../../model/dao/FavoritoDAO.php';
$favoritoDAO = new FavoritoDAO();

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

require_once __DIR__ . '/../../model/dao/CarroDAO.php';
$carroDAO = new CarroDAO();
$carros = $carroDAO->getAvailable($filtros);

require_once __DIR__ . '/../../model/dao/UsuarioDAO.php';
$usuarioDAO = new UsuarioDAO();
$clientes = $usuarioDAO->getAllClientesEUsuarios();

$todasCategorias = array_unique(array_column($carroDAO->getAll(), 'categoria'));
$categorias = array_filter($todasCategorias); 
$carrosFavoritados = $usuarioLogado ? $favoritoDAO->getFavoritosByUsuario($_SESSION['usuario']['id']) : [];
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
                        <img src="https://placehold.co/600x400/e2e8f0/cccccc?text=<?= urlencode($carro['marca']) ?>" class="card-img-top" alt="<?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']); ?>">
                        
                        <?php
                            $isFavorito = in_array($carro['cod_carro'], $carrosFavoritados);
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
                            <span class="spec-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M15 14s1 0 1-1-1-4-6-4-6 3-6 4 1 1 1 1h10zm-9.995-.944v-.002.002zM3.022 13h9.956a.274.274 0 0 0 .014-.002l.008-.002c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664a1.05 1.05 0 0 0 .022.004zM8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10c-1.668.02-2.615.64-3.16 1.256C1.107 11.96 1 12.73 1 13h3c0-1.045.322-2.086.92-3zM3.5 7.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg> <?= htmlspecialchars($carro['lugares'] ?? '5') ?> lugares</span>
                            <span class="spec-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1z"/><path d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2.5A1.5 1.5 0 0 1 4.5 1H5V.5a.5.5 0 0 1 .172-.378l.894-.894A.5.5 0 0 1 6.5 0h3a.5.5 0 0 1 .354.146l.894.894zM7.146 12.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 14.207l-.646.647a.5.5 0 0 1-.708-.708l2-2zM11 1.5H5v13h6V1.5z"/></svg> <?= htmlspecialchars($carro['portas'] ?? '4') ?> portas</span>
                            <?php if (!empty($carro['ar_condicionado']) && $carro['ar_condicionado']): ?>
                                <span class="spec-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16a.5.5 0 0 1-.5-.5v-1.293l-.646.647a.5.5 0 0 1-.707-.708l1.5-1.5a.5.5 0 0 1 .708 0l1.5 1.5a.5.5 0 1 1-.708.708L8.5 14.207V15.5A.5.5 0 0 1 8 16zM2.071 11.071a.5.5 0 0 1 .707 0l.646.646.293-.293a.5.5 0 0 1 .707.708l-1.5 1.5a.5.5 0 0 1-.708-.708l1.5-1.5zM12.929 11.071a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708-.708l1.5-1.5.293-.293.646.646a.5.5 0 0 1 .708 0zM8 0a.5.5 0 0 1 .5.5v1.293l.646-.647a.5.5 0 1 1 .708.708l-1.5 1.5a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 1.793V.5A.5.5 0 0 1 8 0zM12.929 4.929a.5.5 0 0 1 .707 0l.646.646.293-.293a.5.5 0 0 1 .708.707l-1.5 1.5a.5.5 0 0 1-.707 0l-1.5-1.5a.5.5 0 0 1 .707-.707l.293.293zM2.071 4.929a.5.5 0 0 1 0-.707l1.5-1.5a.5.5 0 0 1 .708.708l-1.5 1.5-.293.293-.646-.647a.5.5 0 0 1 0-.707zM4.646 8.354a.5.5 0 0 1 0-.708l1.5-1.5a.5.5 0 1 1 .708.708l-1.5 1.5a.5.5 0 0 1-.708 0zm6.708 0a.5.5 0 0 1 0-.708l1.5-1.5a.5.5 0 1 1 .708.708l-1.5 1.5a.5.5 0 0 1-.708 0z"/></svg> Ar cond.</span>
                            <?php else: ?>
                                <span class="spec-item text-muted text-decoration-line-through"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">...</svg> Sem ar</span>
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
