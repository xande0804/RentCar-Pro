<?php
// Garante que a sessão seja iniciada
session_start();

// Inclui a configuração (que agora contém nossa função registrarLog)
require_once __DIR__ . '/../config.php'; 

// Inclui apenas os DAOs/DTOs que este controller usa diretamente
require_once __DIR__ . "/../model/dto/UsuarioDTO.php";
require_once __DIR__ . "/../model/dao/UsuarioDAO.php";

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
            // SUCESSO: Popula a sessão
            $_SESSION['usuario_logado'] = true;
            $_SESSION['usuario'] = [
                'id'     => (int)$usuario['cod_usuario'],
                'nome'   => $usuario['nome'],
                'email'  => $usuario['email'],
                'perfil' => $usuario['perfil'] ?? 'usuario'
            ];

            /**
             * PRIORIDADE 1: se houver reserva_pendente, continuar o fluxo da reserva
             */
            if (!empty($_SESSION['reserva_pendente'])) {
                $pend = $_SESSION['reserva_pendente'];

                // Quem precisa estar com cadastro completo para prosseguir?
                // No fluxo do cliente comum, é o usuário logado.
                // (No fluxo de staff, o login normalmente não é exigido, então não entra aqui.)
                $usuarioLogadoDB = $usuarioDAO->findById((int)$_SESSION['usuario']['id']);

                if ($usuarioLogadoDB && (int)$usuarioLogadoDB['cadastro_completo'] === 0) {
                    // Envia para completar cadastro, preservando o carro relacionado à reserva
                    $cod_carro = isset($pend['cod_carro']) ? (int)$pend['cod_carro'] : 0;
                    header("Location: " . BASE_URL . "/view/profile/completarCadastro.php?cod_usuario={$_SESSION['usuario']['id']}&cod_carro={$cod_carro}");
                    exit;
                }

                // Cadastro já completo → seguir para confirmar
                header("Location: " . BASE_URL . "/view/reservas/confirmar.php");
                exit;
            }

            /**
             * PRIORIDADE 2: se o middleware tiver salvo uma URL alvo, redireciona pra lá
             * (ex.: página restrita que o usuário tentou acessar)
             */
            if (!empty($_SESSION['redirect_url'])) {
                $url = $_SESSION['redirect_url'];
                unset($_SESSION['redirect_url']);
                header("Location: " . $url);
                exit;
            }

            // Padrão: sem reserva pendente e sem redirect específico → home
            header("Location: " . BASE_URL . "/public/index.php");
            exit;

        } else {
            registrarLog("LOGIN_FALHA", "Tentativa de login falhou para o e-mail '{$email}'.", null);
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
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST["senha"] ?? "";
    $confirmar_senha = $_POST["confirmar_senha"] ?? "";

    if (empty($nome) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 6 || $senha !== $confirmar_senha) {
        header("Location: " . BASE_URL . "/view/auth/cadastroUsuario.php?erro=" . urlencode("Dados inválidos ou incompletos."));
        exit;
    }

    try {
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

        $novoUsuarioId = $usuarioDAO->create($usuarioDTO);

        if ($novoUsuarioId) {
            header("Location: " . BASE_URL . "/view/auth/login.php?sucesso=" . urlencode("Cadastro realizado! Faça o login."));
            exit;
        } else {
            header("Location: " . BASE_URL . "/view/auth/cadastroUsuario.php?erro=" . urlencode("Erro ao cadastrar."));
            exit;
        }
    } catch (PDOException $e) {
        error_log("Erro no cadastro: " . $e->getMessage());
        header("Location: " . BASE_URL . "/view/auth/cadastroUsuario.php?erro=" . urlencode("Ocorreu um erro no sistema."));
        exit;
    }
}

// --- LÓGICA DE LOGOUT ---
else if ($acao === 'logout') {
    $_SESSION = [];
    session_destroy();
    
    header("Location: " . BASE_URL . "/public/index.php");
    exit;
}

// Se nenhuma ação válida for encontrada, redireciona para a home
header("Location: " . BASE_URL . "/public/index.php");
exit;