<?php
// Garante que a sessão seja iniciada em todas as páginas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

// O "Porteiro" entra em ação aqui.

// 1. A página que incluiu este header definiu a variável $perfisPermitidos?
if (isset($perfisPermitidos) && is_array($perfisPermitidos)) {
    // Se sim, chama a verificação de perfil (que já verifica o login antes).
    AuthMiddleware::checkProfile($perfisPermitidos);
}
// 2. Se não, a página definiu que precisa apenas de login (qualquer perfil)?
else if (isset($acessoApenasLogado) && $acessoApenasLogado === true) {
    // Se sim, chama a verificação básica de login.
    AuthMiddleware::checkAuth();
}
// 3. Se nenhuma das variáveis foi definida, a página é pública e nada é feito.


// Lógica normal para definir as variáveis de sessão para a view
$usuarioLogado = !empty($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true;
$usuarioNome = $_SESSION['usuario']['nome'] ?? '';
$usuarioPerfil = $_SESSION['usuario']['perfil'] ?? 'visitante';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="http://localhost/Projeto/">
    <title><?= $pageTitle ?? 'RentCar Pro' ?></title>

    
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/navigation.css">
    <link rel="stylesheet" href="assets/css/alerts.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="stylesheet" href="assets/css/tables.css">
    <link rel="stylesheet" href="assets/css/management.css">
    <link rel="stylesheet" href="assets/css/vitrine.css"> 
    <link rel="stylesheet" href="assets/css/responsivo.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header>
    <div class="top-bar">
        <div class="site-logo-text">
            <a href="public/index.php" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="2rem" height="2rem" viewBox="0 0 24 24" fill="none" stroke="rgb(37, 99, 235)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.5rem;"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path><circle cx="7" cy="17" r="2"></circle><path d="M9 17h6"></path><circle cx="17" cy="17" r="2"></circle>
                </svg>
                <span>RentCar Pro</span>
            </a>
        </div>

        <nav class="header-links">
            <a href="public/index.php">Início</a>
            <?php if (in_array($usuarioPerfil, ['funcionario', 'admin', 'gerente'])): ?>
                <a href="view/admin/hub.php">Painel de Gestão</a>
            <?php else: ?>
                 <a href="view/carros/index.php">Carros</a>
            <?php endif; ?>
            <?php if (in_array($usuarioPerfil, ['usuario', 'cliente'])): ?>
                 <a href="view/reservas/minhasReservas.php">Minhas Reservas</a>
            <?php endif; ?>
        </nav>
        
        <div class="user-info">
            <?php if ($usuarioLogado): ?>
                <span>Olá, <?= htmlspecialchars(ucfirst($usuarioNome)); ?></span>
                <a href="controller/AuthControl.php?acao=logout" class="btn-logout">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span>Sair</span>
                </a>
            <?php else: ?>
                <a href="view/auth/login.php" class="btn-header-action">Entrar</a>
                <a href="view/auth/cadastroUsuario.php" class="btn-header-action primary">Cadastrar</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main>