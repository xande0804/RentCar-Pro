<?php
session_start();
require_once __DIR__ . '/../config.php'; // Já inclui a função registrarLog()
require_once __DIR__ . "/../model/dto/UsuarioDTO.php";
require_once __DIR__ . "/../model/dao/UsuarioDAO.php";
require_once __DIR__ . "/../model/dto/EnderecoDTO.php";
require_once __DIR__ . "/../model/dao/EnderecoDAO.php";

$redirectURL = BASE_URL . "/view/admin/usuarios.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $usuarioDAO = new UsuarioDAO();
    $idUsuarioLogado = $_SESSION['usuario']['id'] ?? null;
    $nomeUsuarioLogado = $_SESSION['usuario']['nome'] ?? 'Sistema';

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

            $novoUsuarioId = $usuarioDAO->create($usuarioDTO);

            if ($novoUsuarioId) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Usuário criado com sucesso!'];
                // --- LOG SIMPLIFICADO ---
                $detalhes = "Admin '{$nomeUsuarioLogado}' criou o novo usuário '{$nome}' ({$email}) com o perfil '{$perfil}'.";
                registrarLog("CADASTRO_ADMIN", $detalhes);
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
                // --- LOG SIMPLIFICADO ---
                $detalhes = "Admin '{$nomeUsuarioLogado}' atualizou os dados do usuário '{$nome}' (ID: {$cod_usuario}).";
                registrarLog("ATUALIZACAO_USUARIO", $detalhes);
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao atualizar usuário.'];
            }
        }
    }
    // --- AÇÃO: DESATIVAR USUÁRIO ---
    else if ($acao === 'desativar') {
        $cod_usuario_alvo = filter_input(INPUT_POST, 'cod_usuario', FILTER_VALIDATE_INT);

        if ($cod_usuario_alvo == $idUsuarioLogado) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Você não pode desativar sua própria conta.'];
        } else {
            $usuarioAlvo = $usuarioDAO->findById($cod_usuario_alvo);
            if ($usuarioDAO->changeStatus($cod_usuario_alvo, 'inativo')) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Usuário desativado com sucesso!'];
                // --- LOG SIMPLIFICADO ---
                $nomeAlvo = $usuarioAlvo ? $usuarioAlvo['nome'] : "ID {$cod_usuario_alvo}";
                $detalhes = "Admin '{$nomeUsuarioLogado}' desativou a conta '{$nomeAlvo}'.";
                registrarLog("DESATIVACAO_USUARIO", $detalhes);
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao desativar usuário.'];
            }
        }
        header("Location: " . $redirectURL);
        exit;
    }
    // --- AÇÃO: REATIVAR USUÁRIO ---
    else if ($acao === 'reativar') {
        $cod_usuario_alvo = filter_input(INPUT_POST, 'cod_usuario', FILTER_VALIDATE_INT);
        $usuarioAlvo = $usuarioDAO->findById($cod_usuario_alvo);
        if ($usuarioDAO->changeStatus($cod_usuario_alvo, 'ativo')) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Usuário reativado com sucesso!'];
            // --- LOG SIMPLIFICADO ---
            $nomeAlvo = $usuarioAlvo ? $usuarioAlvo['nome'] : "ID {$cod_usuario_alvo}";
            $detalhes = "Admin '{$nomeUsuarioLogado}' reativou a conta '{$nomeAlvo}'.";
            registrarLog("REATIVACAO_USUARIO", $detalhes);
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao reativar usuário.'];
        }
        header("Location: " . $redirectURL . "?ver_inativos=true");
        exit;
    }
    // --- AÇÃO: COMPLETAR CADASTRO ---
    else if ($acao === 'completar_cadastro') {
        $idUsuarioParaCompletar = $_POST['cod_usuario'] ?? $_SESSION['usuario']['id'] ?? null;
        if (!$idUsuarioParaCompletar) {
            header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Sessão expirada."));
            exit; 
        }
        
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        function validaCPF($cpf) {
            $cpf = preg_replace('/[^0-9]/is', '', $cpf);
            if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) return false;
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) { $d += $cpf[$c] * (($t + 1) - $c); }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) return false;
            }
            return true;
        }

        if (!validaCPF($cpf)) {
            $carroIdPendente = $_POST['carroId'] ?? null;
            $redirectBack = $carroIdPendente ? "&carroId=$carroIdPendente" : "";
            header("Location: " . BASE_URL . "/view/profile/completarCadastro.php?clienteId=$idUsuarioParaCompletar$redirectBack&erro=" . urlencode("CPF inválido."));
            exit;
        }

        $usuarioAtualizado = $usuarioDAO->updateCadastroCompleto($idUsuarioParaCompletar, $cpf, $telefone);

        $enderecoDTO = new EnderecoDTO();
        $enderecoDTO->setCodUsuario($idUsuarioParaCompletar);
        $enderecoDTO->setCep(trim($_POST['cep'] ?? ''));
        $enderecoDTO->setLogradouro(trim($_POST['logradouro'] ?? ''));
        $enderecoDTO->setNumero(trim($_POST['numero'] ?? ''));
        $enderecoDTO->setComplemento(trim($_POST['complemento'] ?? ''));
        $enderecoDTO->setBairro(trim($_POST['bairro'] ?? ''));
        $enderecoDTO->setCidade(trim($_POST['cidade'] ?? ''));
        $enderecoDTO->setEstado(trim($_POST['estado'] ?? ''));
    
        $enderecoDAO = new EnderecoDAO();
        $enderecoSalvo = $enderecoDAO->createOrUpdate($enderecoDTO);
    
        if ($usuarioAtualizado && $enderecoSalvo) {
            if (isset($_SESSION['reserva_pendente'])) {
                header("Location: " . BASE_URL . "/view/reservas/confirmar.php");
                exit;
            }
            header("Location: " . BASE_URL . "/view/carros/index.php?sucesso=" . urlencode("Cadastro completo com sucesso!"));
        } else {
            $carroIdPendente = $_POST['carroId'] ?? null;
            $redirectBack = $carroIdPendente ? "&carroId=$carroIdPendente" : "";
            header("Location: " . BASE_URL . "/view/profile/completarCadastro.php?clienteId=$idUsuarioParaCompletar$redirectBack&erro=" . urlencode("Erro ao salvar os dados."));
        }
        exit;
    }
    // --- AÇÃO: ADMIN CRIAR CLIENTE COMPLETO (opcional) ---
    else if ($acao === 'admin_criar_cliente_completo') {
        $nome = trim($_POST['nome'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = $_POST["senha"] ?? "";
    
        $usuarioDTO = new UsuarioDTO();
        $usuarioDTO->setNome($nome);
        $usuarioDTO->setEmail($email);
        $usuarioDTO->setSenha($senha);
        $usuarioDTO->setPerfil('cliente');
    
        // Usa o create padrão (ajustado para retornar o ID)
        $novoUsuarioId = $usuarioDAO->createAndReturnId($usuarioDTO);
    
        if ($novoUsuarioId) {
            // Salva CPF e telefone
            $cpf = trim($_POST['cpf'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $usuarioDAO->updateCadastroCompleto($novoUsuarioId, $cpf, $telefone);
    
            // Cria ou atualiza endereço
            $enderecoDAO = new EnderecoDAO();
            $enderecoDTO = new EnderecoDTO();
            $enderecoDTO->setCodUsuario($novoUsuarioId);
            $enderecoDTO->setCep(trim($_POST['cep'] ?? ''));
            $enderecoDTO->setLogradouro(trim($_POST['logradouro'] ?? ''));
            $enderecoDTO->setNumero(trim($_POST['numero'] ?? ''));
            $enderecoDTO->setComplemento(trim($_POST['complemento'] ?? ''));
            $enderecoDTO->setBairro(trim($_POST['bairro'] ?? ''));
            $enderecoDTO->setCidade(trim($_POST['cidade'] ?? ''));
            $enderecoDTO->setEstado(trim($_POST['estado'] ?? ''));
            $enderecoDAO->createOrUpdate($enderecoDTO);
    
            // Mensagem de sucesso
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Novo cliente cadastrado com sucesso!'];
    
            // Log de ação
            registrarLog("CADASTRO_CLIENTE_COMPLETO", "Admin '{$nomeUsuarioLogado}' criou o cliente completo '{$nome}' (ID: {$novoUsuarioId}).");
    
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao criar o cliente. O e-mail pode já existir.'];
        }
    
        // Redireciona para a lista de usuários após o cadastro
        header("Location: " . $redirectURL);
        exit;
    }
    

    // Redireciona padrão pós-ação admin
    header("Location: " . $redirectURL);
    exit;
}

// GET → redireciona
header("Location: " . $redirectURL);
exit;
