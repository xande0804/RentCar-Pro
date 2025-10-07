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

    else if ($acao === 'completar_cadastro') {
        // Inclui os arquivos necessários para o Endereço
        require_once __DIR__ . "/../model/dto/EnderecoDTO.php";
        require_once __DIR__ . "/../model/dao/EnderecoDAO.php";

        // Determina para qual usuário a ação se aplica:
        // 1. O ID vindo do formulário (quando um funcionário edita para um cliente)
        // 2. O ID do usuário logado (quando o próprio usuário completa seu cadastro)
        $idUsuarioParaCompletar = $_POST['cod_usuario'] ?? $_SESSION['usuario']['id'] ?? null;
        $carroIdPendente = $_POST['carroId'] ?? null;

        if (!$idUsuarioParaCompletar) {
            // Redireciona para o login se a sessão expirar no meio do processo
            header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Sessão expirada."));
            exit; 
        }
        // Pega os dados do usuário (CPF e Telefone)
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        // --- VALIDAÇÃO DO LADO DO SERVIDOR ---
        // Função de validação de CPF (pode ser movida para um arquivo de helpers no futuro)
        function validaCPF($cpf) {
            $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
            if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) return false;
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) return false;
            }
            return true;
        }

        if (!validaCPF($cpf)) {
            $redirectBack = $carroIdPendente ? "&carroId=$carroIdPendente" : "";
            header("Location: " . BASE_URL . "/view/profile/completarCadastro.php?clienteId=$idUsuarioParaCompletar$redirectBack&erro=" . urlencode("CPF inválido."));
            exit;
        }
        // Atualiza a tabela de usuários
        $usuarioDAO = new UsuarioDAO();
        // Chamando o novo método do DAO
        $usuarioAtualizado = $usuarioDAO->updateCadastroCompleto($idUsuarioParaCompletar, $cpf, $telefone);

        // Pega os dados de endereço
        $enderecoDTO = new EnderecoDTO();
        $enderecoDTO->setCodUsuario($idUsuarioParaCompletar);
        $enderecoDTO->setCep(trim($_POST['cep'] ?? ''));
        $enderecoDTO->setLogradouro(trim($_POST['logradouro'] ?? ''));
        $enderecoDTO->setNumero(trim($_POST['numero'] ?? ''));
        $enderecoDTO->setComplemento(trim($_POST['complemento'] ?? ''));
        $enderecoDTO->setBairro(trim($_POST['bairro'] ?? ''));
        $enderecoDTO->setCidade(trim($_POST['cidade'] ?? ''));
        $enderecoDTO->setEstado(trim($_POST['estado'] ?? ''));
    
        // Salva ou atualiza o endereço
        $enderecoDAO = new EnderecoDAO();
        $enderecoSalvo = $enderecoDAO->createOrUpdate($enderecoDTO);
    
        if ($usuarioAtualizado && $enderecoSalvo) {
            // Verifica a sessão correta
            if (isset($_SESSION['reserva_pendente_dados'])) {
                $dadosReserva = $_SESSION['reserva_pendente_dados'];
                unset($_SESSION['reserva_pendente_dados']);
    
                // Remonta a URL de finalização com os dados guardados
                $idCarro = $dadosReserva['cod_carro'];
                $clienteId = $dadosReserva['cod_usuario'] ?? null;
    
                $redirectUrl = BASE_URL . "/view/reservas/finalizar.php?id=" . $idCarro;
                // Se um funcionário estava fazendo a reserva para um cliente
                if ($clienteId && $clienteId != ($_SESSION['usuario']['id'] ?? null)) {
                    $redirectUrl .= "&clienteId=" . $clienteId;
                }
                header("Location: " . $redirectUrl);
                exit;
            }

            // Se não havia reserva, manda para a vitrine com sucesso
            header("Location: " . BASE_URL . "/view/carros/index.php?sucesso=" . urlencode("Cadastro completo com sucesso!"));
        } else {
            // Se deu erro, manda de volta para a tela de completar cadastro
            $redirectBack = $carroIdPendente ? "&carroId=$carroIdPendente" : "";
            header("Location: " . BASE_URL . "/view/profile/completarCadastro.php?clienteId=$idUsuarioParaCompletar$redirectBack&erro=" . urlencode("Erro ao salvar os dados."));
        }
        exit;
    }
}

else if ($acao === 'admin_criar_cliente_completo') {
    require_once __DIR__ . "/../model/dto/EnderecoDTO.php";
    require_once __DIR__ . "/../model/dao/EnderecoDAO.php";

    // 1. Pega os dados básicos que vieram dos campos ocultos
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST["senha"] ?? "";

    // 2. Cria o usuário básico com perfil 'cliente'
    $usuarioDTO = new UsuarioDTO();
    $usuarioDTO->setNome($nome);
    $usuarioDTO->setEmail($email);
    $usuarioDTO->setSenha($senha);
    $usuarioDTO->setPerfil('cliente');
    
    // O método create() do DAO precisa retornar o ID do usuário criado
    $novoUsuarioId = $usuarioDAO->createAndReturnId($usuarioDTO); // PRECISAREMOS CRIAR ESTE MÉTODO

    if ($novoUsuarioId) {
        // 3. Pega os dados completos do formulário
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        
        // 4. Atualiza o cadastro para completo
        $usuarioDAO->updateCadastroCompleto($novoUsuarioId, $cpf, $telefone);

        // 5. Salva o endereço
        $enderecoDAO = new EnderecoDAO();
        $enderecoDTO = new EnderecoDTO();
        $enderecoDTO->setCodUsuario($novoUsuarioId);
        // ... (faça os sets para todos os campos do endereço do $_POST) ...
        $enderecoDAO->createOrUpdate($enderecoDTO);
        
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Novo cliente cadastrado com sucesso!'];
    } else {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao criar o cliente. O e-mail pode já existir.'];
    }
    header("Location: " . $redirectURL); // Volta para a lista de usuários
    exit;
}

// Redireciona de volta para a lista de usuários após qualquer ação
header("Location: " . $redirectURL);
exit;