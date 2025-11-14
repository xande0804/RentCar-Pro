<?php
$acessoApenasLogado = true;
$pageTitle = "Confirmar Reserva";
require_once __DIR__ . '/../layout/header.php';

if (empty($_SESSION['reserva_pendente'])) {
    header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Nenhuma reserva para confirmar."));
    exit;
}

$dadosSessao = $_SESSION['reserva_pendente'];
$cod_carro   = isset($dadosSessao['cod_carro']) ? (int)$dadosSessao['cod_carro'] : null;
$data_inicio = $dadosSessao['data_inicio'] ?? '';
$data_fim    = $dadosSessao['data_fim'] ?? '';

$cod_usuario = isset($dadosSessao['cod_usuario'])
    ? (int)$dadosSessao['cod_usuario']
    : (int)$_SESSION['usuario']['id'];

if (!$cod_carro || empty($data_inicio) || empty($data_fim) || $data_fim <= $data_inicio) {
    header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Dados da reserva inválidos."));
    exit;
}

require_once __DIR__ . '/../../model/dao/UsuarioDAO.php';
$usuarioDAO = new UsuarioDAO();
$usuarioDaReserva = $usuarioDAO->findById($cod_usuario);
if (!$usuarioDaReserva) {
    header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Usuário da reserva não encontrado."));
    exit;
}

if ((int)$usuarioDaReserva['cadastro_completo'] === 0) {
    header("Location: " . BASE_URL . "/view/profile/completarCadastro.php?cod_usuario=$cod_usuario&cod_carro=$cod_carro");
    exit;
}

require_once __DIR__ . '/../../model/dao/CarroDAO.php';
$carroDAO = new CarroDAO();
$carro = $carroDAO->findById($cod_carro);
if (!$carro) {
    header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Carro não encontrado."));
    exit;
}

// imagem dinâmica
$img = $carro['imagem_url'] ?? '';
$imagemSrc = filter_var($img, FILTER_VALIDATE_URL)
    ? $img
    : (!empty($img)
        ? BASE_URL . '/' . ltrim($img, '/')
        : "https://placehold.co/150x100/e2e8f0/cccccc?text=" . urlencode($carro['marca'])
    );

$totalDias = (int)$dadosSessao['total_dias'];
$valorTotal = (float)$dadosSessao['total_estimado'];
$planoAplicadoNome = $dadosSessao['plano']['tipo'] === 'limitado'
    ? "KM Limitado ({$dadosSessao['plano']['km_limite']} km/dia)"
    : "KM Ilimitado";
?>

<div class="step-header-wrapper">
  <nav class="step-breadcrumb">
    <a href="<?= BASE_URL ?>/view/carros/index.php">Catálogo</a>
    <span class="sep">›</span>
    <a href="<?= BASE_URL ?>/view/reservas/finalizar.php?id=<?= (int)$carro['cod_carro'] ?>">
      <?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?>
    </a>
    <span class="sep">›</span>
    <span class="active">Confirmar</span>
  </nav>
</div>

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
                            <img src="<?= htmlspecialchars($imagemSrc) ?>" class="img-fluid rounded" style="width: 150px;" alt="Carro">
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h5 class="mb-1"><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?></h5>
                            <small class="text-muted">Ano: <?= htmlspecialchars($carro['ano']) ?></small>
                        </div>
                    </div>

                    <hr>

                    <h4 class="mb-3 mt-4">Resumo da Locação</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            Data de Retirada: <strong><?= date('d/m/Y', strtotime($data_inicio)) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            Data de Devolução: <strong><?= date('d/m/Y', strtotime($data_fim)) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            Total de Dias: <strong><?= $totalDias ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            Plano aplicado: <strong><?= htmlspecialchars($planoAplicadoNome) ?></strong>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center bg-light p-3 mt-3 rounded">
                        <h5 class="mb-0">Valor Total:</h5>
                        <h5 class="mb-0 text-success">R$ <?= number_format($valorTotal, 2, ',', '.') ?></h5>
                    </div>

                    <hr>

                    <h4 class="mb-3 mt-4">Dados do Cliente</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            Nome: <strong><?= htmlspecialchars($usuarioDaReserva['nome']) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            E-mail: <strong><?= htmlspecialchars($usuarioDaReserva['email']) ?></strong>
                        </li>
                    </ul>

                    <form action="controller/ReservaControl.php" method="POST" class="mt-5">
                        <input type="hidden" name="acao" value="finalizar_reserva">
                        <input type="hidden" name="cod_carro" value="<?= $cod_carro ?>">
                        <input type="hidden" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>">
                        <input type="hidden" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>">
                        <?php if (!empty($_SESSION['reserva_pendente']['cod_usuario'])): ?>
                            <input type="hidden" name="cod_usuario" value="<?= (int)$_SESSION['reserva_pendente']['cod_usuario'] ?>">
                        <?php endif; ?>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="view/reservas/finalizar.php?id=<?= $cod_carro ?>" class="btn btn-secondary btn-lg">Voltar</a>
                            <button type="submit" class="btn btn-primary btn-lg">Confirmar Reserva</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
