<?php
$pageTitle = "Logs de Auditoria";
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../layout/header.php';

// SEGURANÇA: Apenas Admin e Gerente podem ver esta página
if (!in_array($usuarioPerfil, ['admin', 'gerente'])) {
    header("Location: " . BASE_URL . "/public/index.php?erro=" . urlencode("Acesso negado!"));
    exit;
}

require_once __DIR__ . '/../../model/dao/LogDAO.php';
$logDAO = new LogDAO();
$logs = $logDAO->getAll();
?>

<div class="content-management">
    <div class="table-header">
        <h2>Logs de Auditoria do Sistema</h2>
        <p>Registro de todas as ações importantes realizadas no sistema.</p>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data e Hora</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="4" class="no-results">Nenhum registro de log encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($log['data_hora']))) ?></td>
                            <td><?= htmlspecialchars($log['nome_usuario'] ?: 'Usuário Deletado (ID: '.$log['cod_usuario'].')') ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($log['acao_realizada']) ?></span></td>
                            <td><?= htmlspecialchars($log['detalhes']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>