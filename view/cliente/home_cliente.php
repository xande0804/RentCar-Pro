<?php
session_start();

// Redireciona se não for um cliente autenticado
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'cliente') {
    header("Location: ../login.php"); // Corrigido o caminho
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Área do Cliente</title>
    <!-- CSS específico para cliente -->
    <link rel="stylesheet" href="../../assets/css/cliente.css"> <!-- Corrigido o caminho -->
</head>
<body>
    <?php include_once __DIR__ . '/../header.php'; ?>
    <main class="container">
        <h1>Área do Cliente</h1>
        <p>Aqui você pode visualizar seu perfil, editar seus dados e acessar os serviços disponíveis.</p>
    </main>
    <?php include_once __DIR__ . '/../footer.php'; ?>
</body>
</html>
