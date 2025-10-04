<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . "/../model/dao/UsuarioDAO.php";
require_once __DIR__ . "/../model/dao/CarroDAO.php";
require_once __DIR__ . "/../model/dao/ReservaDAO.php";
require_once __DIR__ . "/../model/dao/PlanoDAO.php";
require_once __DIR__ . "/../model/dto/ReservaDTO.php";

$acao = $_GET['acao'] ?? $_POST['acao'] ?? '';

// --- AÇÃO 1: INICIA O PROCESSO DE RESERVA (O "PORTEIRO") ---
if ($acao === 'iniciar') {
    $idCarro = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $idUsuarioLogado = $_SESSION['usuario']['id'] ?? null;
    $perfilUsuarioLogado = $_SESSION['usuario']['perfil'] ?? null;

    if (!$idUsuarioLogado) {
        header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Faça login para reservar."));
        exit;
    }

    // Se for um funcionário/admin, o JS deve abrir o modal.
    // Este redirecionamento é um fallback de segurança, que não deveria acontecer no fluxo normal.
    if (in_array($perfilUsuarioLogado, ['admin', 'gerente', 'funcionario'])) {
        // Como o fluxo correto é via modal, este clique direto é inválido.
        header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Ação inválida. Selecione um cliente."));
        exit;
    }
    
    // Se não for funcionário, verifica o cadastro do usuário/cliente
    $usuarioDAO = new UsuarioDAO();
    $usuario = $usuarioDAO->findById($idUsuarioLogado);

    if ($usuario && $usuario['cadastro_completo'] == 0) {
        $_SESSION['reserva_pendente_carro_id'] = $idCarro;
        header("Location: " . BASE_URL . "/view/profile/completarCadastro.php");
        exit;
    }

    // Se o cadastro do usuário/cliente está completo, vai para a finalização
    header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $idCarro);
    exit;
}

// --- AÇÃO 2: FINALIZA E SALVA A RESERVA NO BANCO ---
else if ($acao === 'finalizar_reserva') {
    $cod_carro = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
    $cod_usuario = filter_input(INPUT_POST, 'cod_usuario', FILTER_VALIDATE_INT);
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';

    if (!$cod_carro || !$cod_usuario || empty($data_inicio) || empty($data_fim) || $data_fim <= $data_inicio) {
        header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $cod_carro . "&erro=" . urlencode("Datas inválidas."));
        exit;
    }

    $carroDAO = new CarroDAO();
    $carro = $carroDAO->findById($cod_carro);
    $precoDiaria = $carro['preco_diaria'];
    $totalDias = (new DateTime($data_inicio))->diff(new DateTime($data_fim))->days;
    
    $planoDAO = new PlanoDAO();
    $planos = $planoDAO->getAll();
    $multiplicador = 1.0;
    foreach ($planos as $plano) {
        if ($totalDias >= $plano['dias_minimos']) {
            $multiplicador = $plano['multiplicador_valor'];
            break;
        }
    }
    $valorTotal = $totalDias * $precoDiaria * $multiplicador;

    $reservaDTO = new ReservaDTO();
    $reservaDTO->setCodUsuario($cod_usuario); $reservaDTO->setCodCarro($cod_carro);
    $reservaDTO->setDataInicio($data_inicio); $reservaDTO->setDataFim($data_fim);
    $reservaDTO->setValorTotal($valorTotal);

    $reservaDAO = new ReservaDAO();
    if ($reservaDAO->create($reservaDTO)) {
        $usuarioDAO = new UsuarioDAO();
        $usuarioDaReserva = $usuarioDAO->findById($cod_usuario);
        
        if ($usuarioDaReserva && $usuarioDaReserva['perfil'] === 'usuario') {
            $usuarioDAO->updateProfile($cod_usuario, 'cliente');
            if ($cod_usuario == ($_SESSION['usuario']['id'] ?? null)) {
                $_SESSION['usuario']['perfil'] = 'cliente';
            }
        }
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Reserva realizada com sucesso!'];
        header("Location: " . BASE_URL . "/public/index.php");
    } else {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao processar a reserva.'];
        header("Location: " . BASE_URL . "/view/carros/index.php");
    }
    exit;
}

// --- AÇÕES DE GERENCIAMENTO (ADMIN) ---
else if ($acao === 'mudar_status_ativa') {
    $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
    if ($cod_reserva) {
        $reservaDAO = new ReservaDAO();
        $reservaDAO->updateStatus($cod_reserva, 'ativa', 'alugado');
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Retirada confirmada. Reserva está ativa!'];
    }
    header("Location: " . BASE_URL . "/view/admin/reserva.php");
    exit;
}
else if ($acao === 'mudar_status_concluida') {
    $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
    if ($cod_reserva) {
        $reservaDAO = new ReservaDAO();
        $reservaDAO->updateStatus($cod_reserva, 'concluida', 'disponivel');
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Locação finalizada com sucesso!'];
    }
    header("Location: " . BASE_URL . "/view/admin/reserva.php");
    exit;
}
else if ($acao === 'atualizar_reserva') {
    $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
    $cod_carro = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (!$cod_reserva || !$cod_carro || empty($data_inicio) || empty($data_fim) || empty($status)) {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Dados inválidos para atualização.'];
    } else {
        $carroDAO = new CarroDAO();
        $carro = $carroDAO->findById($cod_carro);
        $precoDiaria = $carro['preco_diaria'];
        $totalDias = (new DateTime($data_inicio))->diff(new DateTime($data_fim))->days;
        
        $planoDAO = new PlanoDAO();
        $planos = $planoDAO->getAll();
        $multiplicador = 1.0;
        foreach ($planos as $plano) {
            if ($totalDias >= $plano['dias_minimos']) {
                $multiplicador = $plano['multiplicador_valor'];
                break;
            }
        }
        $valorTotal = $totalDias * $precoDiaria * $multiplicador;

        $reservaDTO = new ReservaDTO();
        $reservaDTO->setCodReserva($cod_reserva);
        $reservaDTO->setDataInicio($data_inicio);
        $reservaDTO->setDataFim($data_fim);
        $reservaDTO->setStatus($status);
        $reservaDTO->setValorTotal($valorTotal);

        $reservaDAO = new ReservaDAO();
        if ($reservaDAO->update($reservaDTO)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Reserva atualizada com sucesso!'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao atualizar reserva.'];
        }
    }
    header("Location: " . BASE_URL . "/view/admin/reserva.php");
    exit;
}
else if ($acao === 'excluir_reserva') {
    if (!in_array($_SESSION['usuario']['perfil'], ['admin', 'gerente'])) {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Você não tem permissão para excluir reservas.'];
    } else {
        $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
        if (!$cod_reserva) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'ID de reserva inválido.'];
        } else {
            $reservaDAO = new ReservaDAO();
            if ($reservaDAO->delete($cod_reserva)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Reserva excluída com sucesso!'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao excluir reserva.'];
            }
        }
    }
    header("Location: " . BASE_URL . "/view/admin/reserva.php");
    exit;
}