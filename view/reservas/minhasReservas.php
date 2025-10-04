<?php
$pageTitle = "Minhas Reservas";
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../layout/header.php';

// --- SEGURANÇA E DADOS ---
if (!$usuarioLogado) {
    header("Location: " . BASE_URL . "/view/auth/login.php");
    exit;
}

require_once __DIR__ . '/../../model/dao/ReservaDAO.php';
$reservaDAO = new ReservaDAO();
$reservas = $reservaDAO->getByUserId($_SESSION['usuario']['id']);
?>

<div class="container mt-5">
    <div class="table-header">
        <h2>Minhas Reservas</h2>
        <p>Aqui está o histórico de todas as suas locações.</p>
    </div>

    <?php if (empty($reservas)): ?>
        <div class="text-center p-5 border rounded bg-light">
            <h4>Você ainda não tem nenhuma reserva.</h4>
            <p>Que tal encontrar o carro perfeito para sua próxima aventura?</p>
            <a href="view/carros/index.php" class="btn btn-primary mt-3">Ver Carros Disponíveis</a>
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr><th>Carro</th><th>Data de Retirada</th><th>Data de Devolução</th><th>Valor Total</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?= htmlspecialchars($reserva['marca'] . ' ' . $reserva['modelo']) ?></td>
                        <td><?= date('d/m/Y', strtotime($reserva['data_inicio'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($reserva['data_fim'])) ?></td>
                        <td>R$ <?= htmlspecialchars(number_format($reserva['valor_total'], 2, ',', '.')) ?></td>
                        <td><span class="badge bg-info text-dark"><?= ucfirst($reserva['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>