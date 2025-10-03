<?php
$pageTitle = "Nossa Frota - RentCar Pro";

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../layout/header.php';

// --- DADOS ---
// Inclui o DAO e busca apenas os carros disponíveis
require_once __DIR__ . '/../../model/dao/CarroDAO.php';
$carroDAO = new CarroDAO();
$carros = $carroDAO->getAvailable();
?>

<div class="vitrine-container">
    <div class="vitrine-header">
        <h1>Nossa Frota</h1>
        <p>Escolha o carro ideal para a sua próxima viagem.</p>
    </div>

    <div class="car-gallery">
        <?php if (empty($carros)): ?>
            <p class="no-cars-message">Nenhum carro disponível no momento. Por favor, volte mais tarde.</p>
        <?php else: ?>
            <?php foreach ($carros as $carro): ?>
                <div class="car-card">
                    <div class="car-card-image">
                        <img src="https://placehold.co/600x400/e2e8f0/cccccc?text=<?= urlencode($carro['marca']) ?>" alt="<?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']); ?>">
                    </div>
                    <div class="car-card-content">
                        <h2><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']); ?></h2>
                        <div class="car-specs">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <?= htmlspecialchars($carro['ano']); ?>
                            </span>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6l-6 6-6-6"></path><path d="M12 6V4m0 8v8"></path><path d="M12 12h8a2 2 0 012 2v0a2 2 0 01-2 2h-8"></path><path d="M12 12H4a2 2 0 00-2 2v0a2 2 0 002 2h8"></path></svg>
                                <?= htmlspecialchars(ucfirst($carro['cambio'])); ?>
                            </span>
                             <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 8h.01"></path><path d="M10 8h.01"></path><path d="M12 4.222a2 2 0 00-1.785-1.085 2.002 2.002 0 00-1.788 1.085"></path><path d="M12 21.778a2 2 0 011.785-1.085 2.002 2.002 0 011.788 1.085"></path><path d="M8.222 17.556a2 2 0 01-1.085-1.785A2 2 0 018.222 14"></path><path d="M15.778 9.556a2 2 0 001.085-1.785A2 2 0 0015.778 6"></path><path d="M12 12l3.556-3.556"></path><path d="M12 12l-3.556 3.556"></path></svg>
                                <?= htmlspecialchars(ucfirst($carro['combustivel'])); ?>
                            </span>
                        </div>
                        <div class="car-price">
                            <p><span>R$ <?= htmlspecialchars(number_format($carro['preco_diaria'], 2, ',', '.')); ?></span>/dia</p>
                        </div>
                        <a href="controller/ReservaControl.php?acao=iniciar&id=<?= $carro['cod_carro']; ?>" class="btn-reservar">Reservar Agora</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>