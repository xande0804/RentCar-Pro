<?php
// A sessão precisa ser iniciada para verificarmos se o usuário está logado
session_start();
require_once __DIR__ . '/../../config.php';

// Segurança: Apenas usuários logados podem acessar
if (empty($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Você precisa estar logado."));
    exit;
}


$pageTitle = "Complete seu Cadastro";
// Captura os IDs da URL
$clienteId = filter_input(INPUT_GET, 'clienteId', FILTER_VALIDATE_INT);
$carroId = filter_input(INPUT_GET, 'carroId', FILTER_VALIDATE_INT);

$isNovoCliente = isset($_GET['novo_cliente']) && $_GET['novo_cliente'] === 'true';
if ($isNovoCliente) {
    // Pega os dados da URL
    $nome = $_GET['nome'] ?? '';
    $email = $_GET['email'] ?? '';
    $senha = $_GET['senha'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <base href="http://localhost/Projeto/">
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="stylesheet" href="assets/css/profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="card shadow-sm" style="max-width: 800px; margin: auto;">
        <div class="card-header bg-light py-3">
            <h3>Complete seu Cadastro para Reservar</h3>
            <p class="mb-0">Precisamos de mais algumas informações para validar sua reserva.</p>
        </div>
        <div class="card-body p-4">
            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-danger mb-3"><?= htmlspecialchars($_GET['erro']) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alert alert-success mb-3"><?= htmlspecialchars($_GET['sucesso']) ?></div>
            <?php endif; ?>

            <form action="controller/UsuarioControl.php" method="POST">
                <input type="hidden" name="acao" value="completar_cadastro">
                <?php if ($clienteId): // Se um funcionário está completando para um cliente ?>
                    <input type="hidden" name="cod_usuario" value="<?= $clienteId ?>">
                <?php endif; ?>
                <?php if ($carroId): // Se a ação veio de uma tentativa de reserva ?>
                    <input type="hidden" name="carroId" value="<?= $carroId ?>">
                <?php endif; ?>
                
                <h4 class="mb-3">Dados Pessoais</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cpf" class="form-label">CPF/CNPJ</label><input type="text" name="cpf" id="cpf" class="form-control" required>
                        <small id="cpfError" class="form-text text-danger" style="display: none;">CPF/CNPJ inválido.</small>
                    </div>
                    <div class="col-md-6 mb-3"><label for="telefone" class="form-label">Telefone</label><input type="text" name="telefone" id="telefone" class="form-control" required></div>
                </div>

                <hr class="my-4">
                <h4 class="mb-3">Endereço</h4>
                <div class="row">
                    <div class="col-md-4 mb-3"><label for="cep" class="form-label">CEP</label><input type="text" name="cep" id="cep" class="form-control" required maxlength="9"><div id="loading" class="loading">Buscando...</div></div>
                    <div class="col-md-8 mb-3"><label for="logradouro" class="form-label">Logradouro</label><input type="text" name="logradouro" id="logradouro" class="form-control" readonly required></div>
                    <div class="col-md-4 mb-3"><label for="numero" class="form-label">Número</label><input type="text" name="numero" id="numero" class="form-control" required></div>
                    <div class="col-md-8 mb-3"><label for="complemento" class="form-label">Complemento</label><input type="text" name="complemento" id="complemento" class="form-control"></div>
                    <div class="col-md-4 mb-3"><label for="bairro" class="form-label">Bairro</label><input type="text" name="bairro" id="bairro" class="form-control" readonly required></div>
                    <div class="col-md-5 mb-3"><label for="cidade" class="form-label">Cidade</label><input type="text" name="cidade" id="cidade" class="form-control" readonly required></div>
                    <div class="col-md-3 mb-3"><label for="estado" class="form-label">Estado</label><input type="text" name="estado" id="estado" class="form-control" readonly required></div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Salvar e Continuar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/cep-handler.js"></script>
<script src="assets/js/input-mask.js"></script>
<script src="assets/js/validacoes.js"></script>
</body>
</html>