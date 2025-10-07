<?php
session_start(); // Inicia a sessão para acessar variáveis de sessão
require_once __DIR__ . '/../config.php'; // Inclui configuração do sistema (BASE_URL, DB, etc.)
require_once __DIR__ . "/../model/dao/UsuarioDAO.php"; // DAO de usuários
require_once __DIR__ . "/../model/dao/CarroDAO.php"; // DAO de carros
require_once __DIR__ . "/../model/dao/ReservaDAO.php"; // DAO de reservas
require_once __DIR__ . "/../model/dao/PlanoDAO.php"; // DAO de planos de desconto
require_once __DIR__ . "/../model/dto/ReservaDTO.php"; // DTO de reserva (objeto)

$acao = $_GET['acao'] ?? $_POST['acao'] ?? ''; // Define qual ação será executada (via GET ou POST)

// --- AÇÃO 1: INICIA O PROCESSO DE RESERVA (O "PORTEIRO") ---
if ($acao === 'iniciar') {
    $idCarro = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Pega o ID do carro da URL
    $perfilUsuarioLogado = $_SESSION['usuario']['perfil'] ?? null; // Perfil do usuário logado

    // Se for um funcionário/admin/gerente, não pode iniciar reserva assim
    if (in_array($perfilUsuarioLogado, ['admin', 'gerente', 'funcionario'])) {
        header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Ação inválida. Selecione um cliente no modal."));
        exit;
    }
    
    // Para usuário comum ou visitante, redireciona para página de finalização da reserva
    header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $idCarro);
    exit;
}

// --- AÇÃO 2: FINALIZA E SALVA A RESERVA NO BANCO ---
if ($acao === 'finalizar_reserva') {

    $idUsuarioLogado = $_SESSION['usuario']['id'] ?? null;

    // --- BARREIRA DE LOGIN ---
    if (!$idUsuarioLogado) {
        $_SESSION['reserva_pendente_dados'] = $_POST; // Salva dados para continuar após login
        header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Você precisa fazer login para confirmar a reserva."));
        exit;
    }

    // --- BARREIRA DE CADASTRO COMPLETO ---
    $usuarioDAO = new UsuarioDAO();
    $usuario = $usuarioDAO->findById($idUsuarioLogado);
    if ($usuario && $usuario['cadastro_completo'] == 0) {
        $_SESSION['reserva_pendente_dados'] = $_POST; // Salva dados para completar cadastro
        header("Location: " . BASE_URL . "/view/profile/completarCadastro.php");
        exit;
    }

    // --- DADOS DA RESERVA ---
    $cod_carro = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
    $cod_usuario = filter_input(INPUT_POST, 'cod_usuario', FILTER_VALIDATE_INT);
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';

    // Validação de campos obrigatórios e datas
    if (!$cod_carro || !$cod_usuario || empty($data_inicio) || empty($data_fim) || $data_fim <= $data_inicio) {
        $clienteIdParam = isset($_POST['clienteId']) ? "&clienteId=" . $_POST['clienteId'] : "";
        header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $cod_carro . $clienteIdParam . "&erro=" . urlencode("Datas inválidas."));
        exit;
    }

    // --- VALIDAÇÃO DE MÁXIMO 30 DIAS ---
    $totalDias = (new DateTime($data_inicio))->diff(new DateTime($data_fim))->days; // Calcula diferença em dias
    if ($totalDias > 30) {
        $clienteIdParam = isset($_POST['clienteId']) ? "&clienteId=" . $_POST['clienteId'] : "";
        header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $cod_carro . $clienteIdParam . "&erro=" . urlencode("A reserva não pode ultrapassar 30 dias."));
        exit;
    }

    // --- VERIFICA DISPONIBILIDADE DO CARRO ---
    $reservaDAO = new ReservaDAO();
    if (!$reservaDAO->checkDisponibilidade($cod_carro, $data_inicio, $data_fim)) {
        $clienteIdParam = isset($_POST['clienteId']) ? "&clienteId=" . $_POST['clienteId'] : "";
        header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $cod_carro . $clienteIdParam . "&erro=" . urlencode("Este carro já está reservado neste período."));
        exit;
    }

    // --- CÁLCULO DO VALOR ---
    $carroDAO = new CarroDAO();
    $carro = $carroDAO->findById($cod_carro);
    $precoDiaria = $carro['preco_diaria'];

    $planoDAO = new PlanoDAO();
    $planos = $planoDAO->getAll();
    $multiplicador = 1.0; // Valor padrão sem desconto
    foreach ($planos as $plano) {
        if ($totalDias >= $plano['dias_minimos']) { // Aplica multiplicador se atingir mínimo de dias
            $multiplicador = $plano['multiplicador_valor'];
            break;
        }
    }
    $valorTotal = $totalDias * $precoDiaria * $multiplicador; // Calcula valor final

    // --- CRIA DTO E SALVA RESERVA ---
    $reservaDTO = new ReservaDTO();
    $reservaDTO->setCodUsuario($cod_usuario);
    $reservaDTO->setCodCarro($cod_carro);
    $reservaDTO->setDataInicio($data_inicio);
    $reservaDTO->setDataFim($data_fim);
    $reservaDTO->setValorTotal($valorTotal);

    if ($reservaDAO->create($reservaDTO)) {
        // Se o perfil do usuário era apenas 'usuario', atualiza para 'cliente'
        if ($usuario['perfil'] === 'usuario') {
            $usuarioDAO->updateProfile($cod_usuario, 'cliente');
            $_SESSION['usuario']['perfil'] = 'cliente';
        }
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Reserva realizada com sucesso!'];
        header("Location: " . BASE_URL . "/public/index.php");
    } else {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao processar a reserva. Tente novamente.'];
        header("Location: " . BASE_URL . "/view/carros/index.php");
    }
    exit;
}

// --- AÇÕES DE GERENCIAMENTO (ADMIN) ---
else if ($acao === 'mudar_status_ativa') {
    $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
    if ($cod_reserva) {
        $reservaDAO = new ReservaDAO();
        $reservaDAO->updateStatus($cod_reserva, 'ativa', 'alugado'); // Ativa a reserva e marca carro como alugado
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Retirada confirmada. Reserva está ativa!'];
    }
    header("Location: " . BASE_URL . "/view/admin/reserva.php");
    exit;
}

else if ($acao === 'mudar_status_concluida') {
    $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
    if ($cod_reserva) {
        $reservaDAO = new ReservaDAO();
        $reservaDAO->updateStatus($cod_reserva, 'concluida', 'disponivel'); // Conclui reserva e libera carro
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
        // Recalcula valor da reserva com base no plano e dias
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

        // Atualiza DTO
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
    // Só admins ou gerentes podem excluir reservas
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

else if ($acao === 'cancelar_reserva') {
    $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
    $idUsuarioLogado = $_SESSION['usuario']['id'] ?? null;

    $reservaDAO = new ReservaDAO();
    $reserva = $reservaDAO->findById($cod_reserva);

    // Verificações de segurança:
    if (!$reserva) {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Reserva não encontrada.'];
    } else if ($reserva['cod_usuario'] != $idUsuarioLogado) {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Você não tem permissão para cancelar esta reserva.'];
    } else if ($reserva['status'] !== 'pendente') {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Esta reserva não pode mais ser cancelada.'];
    } else {
        // Se tudo estiver certo, cancela a reserva
        if ($reservaDAO->cancel($cod_reserva)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Reserva cancelada com sucesso!'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao cancelar a reserva.'];
        }
    }
    // Redireciona de volta para a lista de reservas do cliente
    header("Location: " . BASE_URL . "/view/reservas/minhasReservas.php");
    exit;
}
