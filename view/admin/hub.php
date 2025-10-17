<?php
$pageTitle = "Painel de Gestão";

require_once __DIR__ . '/../layout/header.php';

// --- SEGURANÇA ---
// A variável $usuarioPerfil é definida no header.php
if (!in_array($usuarioPerfil, ['admin', 'gerente', 'funcionario'])) {
    header("Location: /public/index.php?erro=" . urlencode("Acesso negado!"));
    exit;
}
?>

<div class="content-management">
    <div class="table-header">
        <h2>Painel de Gestão</h2>
        <p>Selecione um módulo abaixo para começar a gerir.</p>
    </div>

    <div class="hub-container">
        
        <a href="view/admin/usuarios.php" class="hub-card">
            <div class="hub-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div class="hub-card-content">
                <h3>Gerenciar Usuários</h3>
                <p>Adicione, edite e gerencie as contas e permissões dos usuários do sistema.</p>
            </div>
        </a>

        <a href="view/admin/carros.php" class="hub-card">
            <div class="hub-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path><circle cx="7" cy="17" r="2"></circle><path d="M9 17h6"></path><circle cx="17" cy="17" r="2"></circle></svg>
            </div>
            <div class="hub-card-content">
                <h3>Gerenciar Carros</h3>
                <p>Adicione novos veículos à frota, edite informações e gerencie o status.</p>
            </div>
        </a>

        <a href="view/admin/reserva.php" class="hub-card">
            <div class="hub-card-icon">
                 <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <div class="hub-card-content">
                <h3>Gerenciar Reservas</h3>
                <p>Visualize e gerencie todas as reservas de veículos do sistema.</p>
            </div>
        </a>

        <?php if (in_array($usuarioPerfil, ['admin', 'gerente'])): ?>
        <a href="view/admin/logs.php" class="hub-card">
            <div class="hub-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
            </div>
            <div class="hub-card-content">
                <h3>Logs de Auditoria</h3>
                <p>Visualize o histórico de ações importantes realizadas pelos usuários no sistema.</p>
            </div>
        </a>
        <?php endif; ?>

    </div>
</div>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>