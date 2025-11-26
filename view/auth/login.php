<?php
$pageTitle = "Login - RentCar Pro";
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
    <link rel="stylesheet" href="assets/css/theme-dark.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="auth-body">

<script>
(function () {
    try {
        const saved = localStorage.getItem('theme');
        if (saved === 'dark') {
            document.body.classList.add('theme-dark');
            document.documentElement.style.colorScheme = 'dark';
        } else {
            document.documentElement.style.colorScheme = 'light';
        }
    } catch (e) {}
})();
</script>

    <main class="auth-main">
        <form method="POST" action="controller/AuthControl.php">
            <h2>Acesso ao Sistema</h2>

            <input type="hidden" name="acao" value="login">

            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['erro']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['sucesso']) ?></div>
            <?php endif; ?>

            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit">Entrar</button>
            <p class="login-link">
                Não tem uma conta? <a href="view/auth/cadastroUsuario.php">Cadastre-se</a>
            </p>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>