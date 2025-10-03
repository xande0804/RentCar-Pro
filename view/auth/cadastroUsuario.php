<?php
$pageTitle = "Cadastro - RentCar Pro";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <base href="http://localhost/Projeto/">
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/stylelogin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="auth-body">

    <main class="auth-main">
        <form id="formCadastro" method="post" action="controller/AuthControl.php">
            <h2>Crie sua Conta</h2>

            <?php if (isset($_GET['erro'])): ?>
                <p class="erro"><?= htmlspecialchars($_GET['erro']) ?></p>
            <?php endif; ?>

            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
                <small>A senha deve ter no mínimo 6 caracteres.</small>
            </div>
            <div>
                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                <small id="msg_senha" style="color:red; display:none;">As senhas não coincidem.</small>
            </div>
            
            <input type="hidden" name="acao" value="cadastrar">
            <button type="submit">Cadastrar</button>

            <p class="login-link">
                Já possui uma conta? <a href="view/auth/login.php">Faça login</a>
            </p>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/validacoes.js"></script>
</body>
</html>