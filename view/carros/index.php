<?php
$pageTitle = "Nossa Frota - RentCar Pro";
// Informa ao layout para carregar o CSS da vitrine e o JS específico desta página.
$cssFiles = ['vitrine.css', 'management.css']; 
$jsFiles = ['reserva-admin.js'];

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../layout/header.php';

// --- DADOS ---
require_once __DIR__ . '/../../model/dao/CarroDAO.php';
$carroDAO = new CarroDAO();
$carros = $carroDAO->getAvailable();

require_once __DIR__ . '/../../model/dao/UsuarioDAO.php';
$usuarioDAO = new UsuarioDAO();
$clientes = $usuarioDAO->getAllClientesEUsuarios();
?>

<script>
    window.listaDeClientes = <?= json_encode($clientes) ?>;
    window.perfilUsuarioLogado = '<?= $usuarioPerfil ?>';
</script>

<div class="vitrine-container">
    <div class="vitrine-header">
        <h1>Nossa Frota</h1>
        <p>Escolha o carro ideal para a sua próxima viagem.</p>
    </div>
    
    <div class="car-gallery">
        <?php if (empty($carros)): ?>
            <p class="no-cars-message">Nenhum carro disponível no momento.</p>
        <?php else: ?>
            <?php foreach ($carros as $carro): ?>
                <div class="car-card">
                    <div class="car-card-image">
                        <img src="https://placehold.co/600x400/e2e8f0/cccccc?text=<?= urlencode($carro['marca']) ?>" alt="<?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']); ?>">
                    </div>
                    <div class="car-card-content">
                        <h2><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']); ?></h2>
                        <div class="car-specs">
                            <span><?= htmlspecialchars($carro['ano']); ?></span>
                            <span><?= htmlspecialchars(ucfirst($carro['cambio'])); ?></span>
                            <span><?= htmlspecialchars(ucfirst($carro['combustivel'])); ?></span>
                        </div>
                        <div class="car-price">
                            <p><span>R$ <?= htmlspecialchars(number_format($carro['preco_diaria'], 2, ',', '.')); ?></span>/dia</p>
                        </div>
                        <?php
                        // Define o link do botão com base no perfil do usuário
                        $linkReserva = "controller/ReservaControl.php?acao=iniciar&id=" . $carro['cod_carro'];
                        // Se for um funcionário, o JS cuidará da ação, então o link não deve levar a lugar nenhum.
                        if (in_array($usuarioPerfil, ['admin', 'gerente', 'funcionario'])) {
                            $linkReserva = "#"; // O JS vai interceptar o clique pelo 'class="btn-reservar"'
                        }
                        ?>
                        <a href="controller/ReservaControl.php?acao=iniciar&id=<?= $carro['cod_carro']; ?>" 
   data-car-id="<?= $carro['cod_carro']; ?>" 
   class="btn-reservar">Reservar Agora</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="selecionarClienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selecionar Cliente para a Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="search-cliente-modal" class="form-control" placeholder="Pesquisar cliente por nome ou e-mail...">
                </div>
                <div id="lista-clientes-modal" class="list-group">
                    </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>