<?php
session_start();

// Verifica se é um gerente autenticado
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'gerente') {
    header("Location: ../login.php"); // Corrigido o caminho
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Gerente</title>
    <!-- CSS específico para gerente -->
    <link rel="stylesheet" href="../../assets/css/gerente.css"> <!-- Corrigido -->
</head>
<body>
    <?php include_once __DIR__ . '/../header.php'; ?>
    <main class="container">
        <h1>Painel do Gerente</h1>
        <p>Aqui você pode acompanhar operações e gerar relatórios de desempenho.</p>
    </main>
    <?php include_once __DIR__ . '/../footer.php'; ?>
</body>
</html>
