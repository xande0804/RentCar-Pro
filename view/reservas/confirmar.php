<?php
$acessoApenasLogado = true;

require_once __DIR__ . '/../layout/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cod_carro   = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim    = $_POST['data_fim'] ?? '';

    if (!$cod_carro || empty($data_inicio) || empty($data_fim) || $data_fim <= $data_inicio) {
        header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=$cod_carro&erro=" . urlencode("Datas inválidas."));
        exit;
    }

    // Se não logado → guarda intenção e manda logar
    if (empty($_SESSION['usuario']['id'])) {
        $_SESSION['reserva_pendente'] = [
            'carroId'     => $cod_carro,
            'data_inicio' => $data_inicio,
            'data_fim'    => $data_fim,
        ];
        header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Você precisa fazer login para continuar."));
        exit;
    }

    $cod_usuario = $_SESSION['usuario']['id'];
}
elseif (isset($_SESSION['reserva_pendente'])) {
    // Voltando do login/completar cadastro
    $dados = $_SESSION['reserva_pendente'];
    $cod_carro   = $dados['carroId'] ?? null;
    $data_inicio = $dados['data_inicio'] ?? '';
    $data_fim    = $dados['data_fim'] ?? '';

    if (empty($_SESSION['usuario']['id'])) {
        header("Location: " . BASE_URL . "/view/auth/login.php");
        exit;
    }
    $cod_usuario = $_SESSION['usuario']['id'];
} else {
    header("Location: " . BASE_URL . "/view/carros/index.php");
    exit;
}

// Carrega usuário e verifica cadastro completo
require_once __DIR__ . '/../../model/dao/UsuarioDAO.php';
$usuarioDAO = new UsuarioDAO();
$usuarioDaReserva = $usuarioDAO->findById($cod_usuario);

if ($usuarioDaReserva && (int)$usuarioDaReserva['cadastro_completo'] === 0) {
    $_SESSION['reserva_pendente'] = [
        'carroId'     => $cod_carro,
        'data_inicio' => $data_inicio,
        'data_fim'    => $data_fim,
    ];
    header("Location: " . BASE_URL . "/view/profile/completarCadastro.php?clienteId=$cod_usuario&carroId=$cod_carro");
    exit;
}

// Carro e cálculo
require_once __DIR__ . '/../../model/dao/CarroDAO.php';
$carroDAO = new CarroDAO();
$carro = $carroDAO->findById($cod_carro);

$totalDias = (new DateTime($data_inicio))->diff(new DateTime($data_fim))->days;

require_once __DIR__ . '/../../model/dao/PlanoDAO.php';
$planoDAO = new PlanoDAO();
$planos = $planoDAO->getAll();
usort($planos, fn($a,$b) => (int)$b['dias_minimos'] <=> (int)$a['dias_minimos']);

$multiplicador = 1.0;
foreach ($planos as $plano) {
    if ($totalDias >= (int)$plano['dias_minimos']) {
        $multiplicador = (float)$plano['multiplicador_valor'];
        break;
    }
}
$valorTotal = $totalDias * (float)$carro['preco_diaria'] * $multiplicador;
?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h3>Confirme os Dados da Sua Reserva</h3>
                    <p class="mb-0 text-muted">Revise as informações antes de confirmar a sua locação.</p>
                </div>
                <div class="card-body p-4">

                    <h4 class="mb-3">Detalhes do Veículo</h4>
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-shrink-0">
                            <img src="https://placehold.co/150x100/e2e8f0/cccccc?text=<?= urlencode($carro['marca']) ?>" class="img-fluid rounded" style="width: 150px;" alt="carro">
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h5 class="mb-1"><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?></h5>
                            <small class="text-muted">Ano: <?= htmlspecialchars($carro['ano']) ?></small>
                        </div>
                    </div>

                    <hr>

                    <h4 class="mb-3 mt-4">Resumo da Locação</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Data de Retirada:
                            <strong><?= htmlspecialchars(date('d/m/Y', strtotime($data_inicio))) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Data de Devolução:
                            <strong><?= htmlspecialchars(date('d/m/Y', strtotime($data_fim))) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Total de Dias:
                            <strong><?= $totalDias ?></strong>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center bg-light p-3 mt-3 rounded">
                        <h5 class="mb-0">Valor Total:</h5>
                        <h5 class="mb-0 text-success">R$ <?= htmlspecialchars(number_format($valorTotal, 2, ',', '.')) ?></h5>
                    </div>

                    <hr>

                    <h4 class="mb-3 mt-4">Seus Dados</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Nome: <strong><?= htmlspecialchars($usuarioDaReserva['nome']) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            E-mail: <strong><?= htmlspecialchars($usuarioDaReserva['email']) ?></strong>
                        </li>
                    </ul>

                    <!-- PASSO FINAL: manda para o controller criar a reserva -->
                    <form action="controller/reservaControl.php" method="POST" class="mt-5">
                        <input type="hidden" name="acao" value="finalizar_reserva">
                        <input type="hidden" name="cod_carro" value="<?= $cod_carro ?>">
                        <input type="hidden" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>">
                        <input type="hidden" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="view/reservas/finalizar.php?id=<?= $cod_carro ?>" class="btn btn-secondary btn-lg">Voltar e alterar datas</a>
                            <button type="submit" class="btn btn-primary btn-lg">Confirmar Reserva</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
