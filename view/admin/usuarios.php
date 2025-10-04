<?php
require_once __DIR__ . '/../../config.php';
$pageTitle = "Gerenciar Usuários";
$jsFiles = ['management.js'];

require_once __DIR__ . '/../layout/header.php';

// --- SEGURANÇA E DADOS ---
if (!in_array($usuarioPerfil, ['admin', 'gerente', 'funcionario'])) {
    header("Location: " . BASE_URL . "/public/index.php?erro=" . urlencode("Acesso negado!"));
    exit;
}

require_once __DIR__ . '/../../model/dao/UsuarioDAO.php';
$usuarioDAO = new UsuarioDAO();
$usuarios = $usuarioDAO->getAll();
?>

<div class="content-management">
    <div class="table-header">
        <h2>Gerenciamento de Usuários</h2>
        <p>Adicione, edite e remova usuários do sistema.</p>
    </div>

    <?php
    if (isset($_SESSION['flash_message'])) {
        $flashMessage = $_SESSION['flash_message'];
        $messageType = $flashMessage['type'] === 'success' ? 'alert-success' : 'alert-danger';
        echo "<div class='alert {$messageType}'>" . htmlspecialchars($flashMessage['message']) . "</div>";
        unset($_SESSION['flash_message']);
    }
    ?>

    <div class="table-controls">
        <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#createModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Novo Usuário</span>
        </button>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Perfil</th><th class="actions-header">Ações</th></tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr><td colspan="5" class="no-results">Nenhum usuário encontrado.</td></tr>
                <?php else: ?>
                    <?php 
                        $idUsuarioLogado = $_SESSION['usuario']['id'];
                        $perfilUsuarioLogado = $_SESSION['usuario']['perfil'];
                    ?>
                    <?php foreach ($usuarios as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['cod_usuario']) ?></td>
                            <td><?= htmlspecialchars($user['nome']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php
                                $badgeClass = 'badge-' . strtolower($user['perfil']);
                                $perfilTexto = $user['perfil'];
                                if ($user['perfil'] === 'usuario' && $user['cadastro_completo'] == 1) {
                                    $badgeClass = 'badge-usuario-completo';
                                    $perfilTexto = 'Usuário Completo';
                                }
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($perfilTexto) ?></span>
                            </td>
                            <td class="actions">
                                <?php
                                $podeEditar = true; $podeExcluir = true; 
                                $editClass = ''; $deleteClass = '';
                                $editMessage = ''; $deleteMessage = '';

                                if ($perfilUsuarioLogado == 'funcionario') {
                                    if (in_array($user['perfil'], ['admin', 'gerente']) || ($user['perfil'] == 'funcionario' && $user['cod_usuario'] != $idUsuarioLogado)) {
                                        $podeEditar = false; $editClass = 'disabled'; $editMessage = 'Permissão negada.';
                                    }
                                    if (in_array($user['perfil'], ['admin', 'gerente', 'funcionario'])) {
                                        $podeExcluir = false; $deleteClass = 'disabled'; $deleteMessage = 'Permissão negada.';
                                    }
                                }
                                
                                if ($user['cod_usuario'] == $idUsuarioLogado) {
                                    $podeExcluir = false; $deleteClass = 'disabled';
                                    $deleteMessage = 'Você não pode excluir sua própria conta.';
                                }
                                ?>
                                <button type="button" class="btn-action edit <?= $editClass ?>" data-bs-toggle="modal" data-bs-target="#editModal" 
                                        data-id="<?= $user['cod_usuario'] ?>" data-nome="<?= $user['nome'] ?>" data-email="<?= $user['email'] ?>" data-perfil="<?= $user['perfil'] ?>"
                                        data-message="<?= $editMessage ?>">Editar</button>
                                
                                <form action="controller/UsuarioControl.php" method="POST" style="display: inline;" onsubmit="if(this.querySelector('button').classList.contains('disabled')){ alert('<?= $deleteMessage ?>'); return false; } return confirm('Tem certeza?');">
                                    <input type="hidden" name="acao" value="excluir"><input type="hidden" name="cod_usuario" value="<?= $user['cod_usuario'] ?>">
                                    <button type="submit" class="btn-action delete <?= $deleteClass ?>" <?= !$podeExcluir ? 'disabled' : '' ?>>Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Criar Novo Usuário</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="controller/UsuarioControl.php" method="POST">
        <div class="modal-body">
            <div id="createError" class="alert alert-danger d-none"></div>
            <input type="hidden" name="acao" value="admin_cadastrar">
            <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" name="nome" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
            <div class="mb-3"><label class="form-label">Senha</label><input type="password" class="form-control" name="senha" required></div>
            <div class="mb-3">
                <label class="form-label">Perfil</label>
                <select name="perfil" class="form-select" required>
                    <option value="usuario">Usuário</option><option value="cliente">Cliente</option>
                    <?php if (in_array($perfilUsuarioLogado, ['admin', 'gerente'])): ?><option value="funcionario">Funcionário</option><?php endif; ?>
                    <?php if ($perfilUsuarioLogado == 'admin'): ?><option value="gerente">Gerente</option><?php endif; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Salvar</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Editar Usuário</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form id="editForm" action="controller/UsuarioControl.php" method="POST">
        <div class="modal-body">
            <div id="editError" class="alert alert-danger d-none"></div>
            <input type="hidden" name="acao" value="atualizar"><input type="hidden" id="edit-id" name="cod_usuario">
            <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" id="edit-nome" name="nome" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" id="edit-email" name="email" required></div>
            <div class="mb-3">
                <label class="form-label">Perfil</label>
                <select id="edit-perfil" name="perfil" class="form-select" required>
                     <option value="usuario">Usuário</option><option value="cliente">Cliente</option>
                    <?php if (in_array($perfilUsuarioLogado, ['admin', 'gerente'])): ?><option value="funcionario">Funcionário</option><?php endif; ?>
                    <?php if ($perfilUsuarioLogado == 'admin'): ?><option value="gerente">Gerente</option><?php endif; ?>
                </select>
            </div>
            <hr><p class="text-muted"><small>Deixe a senha em branco para não alterar.</small></p>
            <div class="mb-3"><label class="form-label">Nova Senha</label><input type="password" class="form-control" name="senha"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Salvar Alterações</button></div>
    </form>
</div></div></div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editModal');
    if(editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nome = button.getAttribute('data-nome');
            const email = button.getAttribute('data-email');
            const perfil = button.getAttribute('data-perfil');
            
            const modal = this;
            modal.querySelector('#edit-id').value = id;
            modal.querySelector('#edit-nome').value = nome;
            modal.querySelector('#edit-email').value = email;
            modal.querySelector('#edit-perfil').value = perfil;
        });
    }

    <?php
    if (isset($_SESSION['flash_message']) && isset($_SESSION['flash_message']['modal'])) {
        $validationData = $_SESSION['flash_message'];
        echo "const serverValidationData = " . json_encode($validationData) . ";";
        unset($_SESSION['flash_message']); // Limpa após usar
    }
    ?>
});
</script>