<?php
$acessoApenasLogado = true;
$pageTitle = "Finalizar Reserva";
require_once __DIR__ . '/../layout/header.php';

$carroId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$clienteId = filter_input(INPUT_GET, 'clienteId', FILTER_VALIDATE_INT);

// Apenas identifica o usuário (não é usado para submissão final)
$usuarioParaReservaId = $clienteId ?: ($_SESSION['usuario']['id'] ?? null);

if (!$carroId) {
    header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Carro inválido."));
    exit;
}

require_once __DIR__ . '/../../model/dao/CarroDAO.php';
$carroDAO = new CarroDAO();
$carro = $carroDAO->findById($carroId);

if (!$carro || $carro['status'] !== 'disponivel') {
    header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Este carro não está mais disponível."));
    exit;
}

require_once __DIR__ . '/../../model/dao/PlanoDAO.php';
$planoDAO = new PlanoDAO();
$planos = $planoDAO->getAll();
?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h3>Realizar Reserva</h3>
                    <p class="mb-0">Escolha o período da sua locação e avance para confirmar.</p>
                </div>
                <div class="card-body p-4">

                    <?php if (isset($_GET['erro'])): ?>
                        <div class='alert alert-danger'><?= htmlspecialchars($_GET['erro']) ?></div>
                    <?php endif; ?>

                    <h4>Veículo Selecionado</h4>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <img src="https://placehold.co/600x400/e2e8f0/cccccc?text=<?= urlencode($carro['marca']) ?>" class="img-fluid rounded" alt="carro">
                        </div>
                        <div class="col-md-8">
                            <h5><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?></h5>
                            <ul class="list-unstyled text-muted">
                                <li><strong>Ano:</strong> <?= htmlspecialchars($carro['ano']) ?></li>
                                <li><strong>Cor:</strong> <?= htmlspecialchars($carro['cor']) ?></li>
                                <li><strong>Preço da Diária:</strong> R$ <?= htmlspecialchars(number_format($carro['preco_diaria'], 2, ',', '.')) ?></li>
                            </ul>
                        </div>
                    </div>
                    <hr class="my-4">

                    <!-- PASSO: escolher datas → enviar para confirmar.php -->
                    <form action="view/reservas/confirmar.php" method="POST">
                        <input type="hidden" name="cod_carro" value="<?= $carro['cod_carro'] ?>">
                        <!-- não envie cod_usuario; o servidor usa a sessão -->
                        <input type="hidden" id="preco_diaria" value="<?= $carro['preco_diaria'] ?>">

                        <h4 class="mb-3">Selecione o Período</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_inicio" class="form-label">Data de Retirada</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="data_fim" class="form-label">Data de Devolução</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim" required min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div id="resumo-reserva" class="mt-4 p-3 bg-light rounded" style="display: none;">
                            <h5>Resumo</h5>
                            <p><strong>Plano aplicado:</strong> <span id="plano-aplicado">Nenhum</span></p>
                            <p><strong>Total de dias:</strong> <span id="total-dias">0</span></p>
                            <p class="h4"><strong>Valor Total Estimado:</strong> R$ <span id="valor-total">0,00</span></p>
                        </div>

                        <div class="text-end mt-4">
                            <a href="view/carros/index.php" class="btn btn-secondary btn-lg">Cancelar</a>
                            <button type="submit" class="btn btn-primary btn-lg">Confirmar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
const planos = <?= json_encode($planos) ?>;
document.addEventListener('DOMContentLoaded', function () {
    const dataInicioInput = document.getElementById('data_inicio');
    const dataFimInput = document.getElementById('data_fim');
    const precoDiaria = parseFloat(document.getElementById('preco_diaria').value);
    const resumoDiv = document.getElementById('resumo-reserva');
    const totalDiasSpan = document.getElementById('total-dias');
    const valorTotalSpan = document.getElementById('valor-total');
    const planoAplicadoSpan = document.getElementById('plano-aplicado');

    function calcularReserva() {
        const dataInicio = new Date(dataInicioInput.value);
        const dataFim = new Date(dataFimInput.value);
        if (dataInicioInput.value && dataFimInput.value && dataFim > dataInicio) {
            const diffTime = Math.abs(dataFim - dataInicio);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            // garante melhor plano (assume lista não ordenada)
            const planosOrdenados = [...planos].sort((a,b) => parseInt(b.dias_minimos) - parseInt(a.dias_minimos));
            let multiplicador = 1.0;
            let planoNome = "Nenhum";
            for (const plano of planosOrdenados) {
                if (diffDays >= parseInt(plano.dias_minimos)) {
                    multiplicador = parseFloat(plano.multiplicador_valor);
                    planoNome = plano.nome;
                    break;
                }
            }
            const valorTotal = diffDays * precoDiaria * multiplicador;
            totalDiasSpan.textContent = diffDays;
            valorTotalSpan.textContent = valorTotal.toFixed(2).replace('.', ',');
            planoAplicadoSpan.textContent = planoNome;
            resumoDiv.style.display = 'block';
        } else {
            resumoDiv.style.display = 'none';
        }
    }
    dataInicioInput.addEventListener('change', calcularReserva);
    dataFimInput.addEventListener('change', calcularReserva);
});
</script>
