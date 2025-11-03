<?php
$perfisPermitidos = ['cliente', 'usuario'];
$pageTitle = "Minhas Reservas";
require_once __DIR__ . '/../layout/header.php';

require_once __DIR__ . '/../../model/dao/ReservaDAO.php';
$reservaDAO = new ReservaDAO();
$reservas = $reservaDAO->getByUserId($_SESSION['usuario']['id']);
?>

<div class="container mt-5">
    <div class="table-header">
        <h2>Minhas Reservas</h2>
        <p>Aqui está o histórico de todas as suas locações.</p>
    </div>

    <?php
    // Bloco para exibir as Flash Messages (sucesso, erro)
    if (isset($_SESSION['flash_message'])) {
        $flashMessage = $_SESSION['flash_message'];
        $messageType = $flashMessage['type'] === 'success' ? 'alert-success' : 'alert-danger';
        echo "<div class='alert {$messageType}'>" . htmlspecialchars($flashMessage['message']) . "</div>";
        unset($_SESSION['flash_message']);
    }
    ?>

    <?php if (empty($reservas)): ?>
        <div class="text-center p-5 border rounded bg-light">
            <h4>Você ainda não tem nenhuma reserva.</h4>
            <p>Que tal encontrar o carro perfeito para sua próxima aventura?</p>
            <a href="view/carros/index.php" class="btn btn-primary mt-3">Ver Carros Disponíveis</a>
        </div>
    <?php else: ?>
        <table class="data-table table table-striped table-hover">
            <thead>
                <tr><th>Carro</th><th>Período</th><th>Valor Total</th><th>Status</th><th class="actions-header">Ação</th></tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?= htmlspecialchars($reserva['marca'] . ' ' . $reserva['modelo']) ?></td>
                        <td><?= date('d/m/Y', strtotime($reserva['data_inicio'])) ?> a <?= date('d/m/Y', strtotime($reserva['data_fim'])) ?></td>
                        <td>R$ <?= htmlspecialchars(number_format($reserva['valor_total'], 2, ',', '.')) ?></td>
                        <td>
                            <?php
                            $status = $reserva['status'];
                            $badgeClass = strtolower($status);
                            $statusTexto = ucfirst($status);

                            if ($status === 'pendente') {
                                $badgeClass = 'pendente'; 
                                $statusTexto = 'Aguardando Pagamento';
                            } elseif ($status === 'aguardando_retirada') {
                                $badgeClass = 'ativa'; 
                                $statusTexto = 'Aguardando Retirada';
                            } elseif ($status === 'ativa') {
                                $badgeClass = 'alugado'; 
                                $statusTexto = 'Em Andamento';
                            } elseif ($status === 'concluida') {
                                $badgeClass = 'concluida';
                                $statusTexto = 'Concluída';
                            } elseif ($status === 'cancelada') {
                                $badgeClass = 'cancelada';
                                $statusTexto = 'Cancelada';
                            }
                            ?>
                            <span class="badge badge-<?= $badgeClass ?>"><?= $statusTexto ?></span>
                        </td>
                        <td class="actions">
                            <?php if ($reserva['status'] == 'pendente'): ?>
                                
                                <a href="view/reservas/pagamento.php?id=<?= $reserva['cod_reserva'] ?>" class="btn-action edit">
                                    Pagar
                                </a>

                                <form action="controller/ReservaControl.php" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar esta reserva?');" style="display: inline-block;">
                                    <input type="hidden" name="acao" value="cancelar_reserva">
                                    <input type="hidden" name="cod_reserva" value="<?= $reserva['cod_reserva'] ?>">
                                    <button type="submit" class="btn-action delete">Cancelar</button>
                                </form>

                            <?php else: ?>
                                <span>--</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>