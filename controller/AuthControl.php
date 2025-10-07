<?php
// Garante que a sessão seja iniciada para podermos manipular as variáveis
session_start();

// Inclui o arquivo de configuração para termos acesso à BASE_URL
require_once __DIR__ . '/../config.php'; 

// Inclui os arquivos necessários do Model
require_once __DIR__ . "/../model/dto/UsuarioDTO.php";
require_once __DIR__ . "/../model/dao/UsuarioDAO.php";

// Pega a ação do formulário (seja por POST ou GET para o logout)
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

// --- LÓGICA DE LOGIN ---
if ($acao === 'login') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$email || empty($senha)) {
        header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Email ou senha inválidos."));
        exit;
    }

    try {
        $usuarioDAO = new UsuarioDAO();
        $usuario = $usuarioDAO->findByEmail($email);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Popula a sessão com os dados do usuário
            $_SESSION['usuario_logado'] = true;
            $_SESSION['usuario'] = [
                'id'     => $usuario['cod_usuario'],
                'nome'   => $usuario['nome'],
                'email'  => $usuario['email'],
                'perfil' => $usuario['perfil']
            ];

    
            // --- LÓGICA DE REDIRECIONAMENTO INTELIGENTE ---
            if (isset($_SESSION['reserva_pendente_dados'])) {
                $dadosReserva = $_SESSION['reserva_pendente_dados'];
                unset($_SESSION['reserva_pendente_dados']); // Limpa a sessão
                
                // Remonta a URL da tela de finalização
                $idCarro = $dadosReserva['cod_carro'];
                header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $idCarro);
                exit;
            }

            // Se não havia reserva pendente, redireciona para a página inicial padrão.
            header("Location: " . BASE_URL . "/public/index.php");
            exit;

        } else {
            // Se a senha ou o email estiverem errados
            header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Usuário ou senha inválidos!"));
            exit;
        }
    } catch (PDOException $e) {
        error_log("Erro no login: " . $e->getMessage());
        header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Ocorreu um erro no sistema."));
        exit;
    }
}

// --- LÓGICA DE CADASTRO PÚBLICO ---
else if ($acao === 'cadastrar') {
    // A lógica de cadastro continua a mesma, mas usando a BASE_URL
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST["senha"] ?? "";
    $confirmar_senha = $_POST["confirmar_senha"] ?? "";

    // Validações...
    if (empty($nome) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 6 || $senha !== $confirmar_senha) {
        header("Location: " . BASE_URL . "/view/auth/cadastroUsuario.php?erro=" . urlencode("Dados inválidos ou incompletos."));
        exit;
    }

    $usuarioDAO = new UsuarioDAO();
    if ($usuarioDAO->findByEmail($email)) {
        header("Location: " . BASE_URL . "/view/auth/cadastroUsuario.php?erro=" . urlencode("Este e-mail já está em uso."));
        exit;
    }
    
    $usuarioDTO = new UsuarioDTO();
    $usuarioDTO->setNome($nome);
    $usuarioDTO->setEmail($email);
    $usuarioDTO->setSenha($senha);
    $usuarioDTO->setPerfil('usuario');

    if ($usuarioDAO->create($usuarioDTO)) {
        header("Location: " . BASE_URL . "/view/auth/login.php?sucesso=" . urlencode("Cadastro realizado! Faça o login."));
    } else {
        header("Location: " . BASE_URL . "/view/auth/cadastroUsuario.php?erro=" . urlencode("Erro ao cadastrar."));
    }
    exit;
}

// --- LÓGICA DE LOGOUT ---
else if ($acao === 'logout') {
    $_SESSION = [];
    session_destroy();
    
    // Usando a BASE_URL para garantir o caminho absoluto
    header("Location: " . BASE_URL . "/view/auth/login.php");
    exit;
}

// Se nenhuma ação válida for encontrada, redireciona para a home
header("Location: " . BASE_URL . "/public/index.php");
exit;