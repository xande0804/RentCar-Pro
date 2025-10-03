<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . "/../model/dto/UsuarioDTO.php";
require_once __DIR__ . "/../model/dao/UsuarioDAO.php";

$redirectURL = BASE_URL . "/view/admin/usuarios.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $usuarioDAO = new UsuarioDAO();
    $idUsuarioLogado = $_SESSION['usuario']['id'] ?? null;

    // --- AÇÃO: CADASTRO PELO PAINEL ADMIN (DO MODAL) ---
    if ($acao === 'admin_cadastrar') {
        $nome = trim($_POST['nome'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = $_POST["senha"] ?? "";
        $perfil = $_POST["perfil"] ?? "usuario";

        if (empty($nome) || !$email || empty($senha) || empty($perfil)) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Todos os campos são obrigatórios para o cadastro.'];
        } else if ($usuarioDAO->findByEmail($email)) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'O e-mail informado já está em uso.'];
        } else {
            $usuarioDTO = new UsuarioDTO();
            $usuarioDTO->setNome($nome);
            $usuarioDTO->setEmail($email);
            $usuarioDTO->setSenha($senha);
            $usuarioDTO->setPerfil($perfil);

            if ($usuarioDAO->create($usuarioDTO)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Usuário criado com sucesso!'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao criar usuário.'];
            }
        }
    }
    // --- AÇÃO: ATUALIZAR USUÁRIO (DO MODAL DE EDIÇÃO) ---
    else if ($acao === 'atualizar') {
        $cod_usuario = filter_input(INPUT_POST, 'cod_usuario', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = $_POST["senha"] ?? "";
        $perfil = $_POST["perfil"] ?? "";

        if (!$cod_usuario || empty($nome) || !$email || empty($perfil)) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Dados inválidos para atualização.'];
        } else {
            $usuarioDTO = new UsuarioDTO();
            $usuarioDTO->setCodUsuario($cod_usuario);
            $usuarioDTO->setNome($nome);
            $usuarioDTO->setEmail($email);
            $usuarioDTO->setPerfil($perfil);
            if (!empty($senha)) {
                $usuarioDTO->setSenha($senha);
            }

            if ($usuarioDAO->update($usuarioDTO)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Usuário atualizado com sucesso!'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao atualizar usuário.'];
            }
        }
    }
    // --- AÇÃO: EXCLUIR USUÁRIO ---
    else if ($acao === 'excluir') {
        $cod_usuario = filter_input(INPUT_POST, 'cod_usuario', FILTER_VALIDATE_INT);

        if ($cod_usuario == $idUsuarioLogado) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Você não pode excluir sua própria conta.'];
        } else if (!$cod_usuario) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'ID de usuário inválido.'];
        } else {
            if ($usuarioDAO->delete($cod_usuario)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Usuário excluído com sucesso!'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao excluir usuário.'];
            }
        }
    }
}

// Redireciona de volta para a lista de usuários após qualquer ação
header("Location: " . $redirectURL);
exit;