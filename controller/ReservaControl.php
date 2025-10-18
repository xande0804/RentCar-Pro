<?php
session_start();
require_once __DIR__ . '/../config.php'; // Já inclui registrarLog()
require_once __DIR__ . "/../model/dao/UsuarioDAO.php";
require_once __DIR__ . "/../model/dao/CarroDAO.php";
require_once __DIR__ . "/../model/dao/ReservaDAO.php";
require_once __DIR__ . "/../model/dao/PlanoDAO.php";
require_once __DIR__ . "/../model/dto/ReservaDTO.php";

$acao = $_GET['acao'] ?? $_POST['acao'] ?? '';
$nomeUsuarioLogado = $_SESSION['usuario']['nome'] ?? 'Sistema';
$idUsuarioLogado   = $_SESSION['usuario']['id'] ?? null;

// --- INICIAR RESERVA (da vitrine de carros) ---
if ($acao === 'iniciar') {
    $idCarro = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $perfilUsuarioLogado = $_SESSION['usuario']['perfil'] ?? null;

    // Funcionários não iniciam reserva direto pela vitrine
    if (in_array($perfilUsuarioLogado, ['admin', 'gerente', 'funcionario'])) {
        header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Ação inválida. Selecione um cliente no modal."));
        exit;
    }

    header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $idCarro);
    exit;
}

// --- FINALIZAR RESERVA (criação real) ---
if ($acao === 'finalizar_reserva') {
    if (!$idUsuarioLogado) {
        $_SESSION['reserva_pendente'] = $_POST;
        header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Você precisa fazer login para confirmar a reserva."));
        exit;
    }

    $cod_carro   = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim    = $_POST['data_fim'] ?? '';
    $cod_usuario = $idUsuarioLogado;

    if (!$cod_carro || empty($data_inicio) || empty($data_fim) || $data_fim <= $data_inicio) {
        header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $cod_carro . "&erro=" . urlencode("Datas inválidas."));
        exit;
    }

    $usuarioDAO = new UsuarioDAO();
    $usuario = $usuarioDAO->findById($idUsuarioLogado);
    if ($usuario && (int)$usuario['cadastro_completo'] === 0) {
        $_SESSION['reserva_pendente'] = [
            'carroId'     => $cod_carro,
            'data_inicio' => $data_inicio,
            'data_fim'    => $data_fim,
        ];
        header("Location: " . BASE_URL . "/view/profile/completarCadastro.php?clienteId=$idUsuarioLogado&carroId=$cod_carro");
        exit;
    }

    $reservaDAO = new ReservaDAO();
    if (!$reservaDAO->checkDisponibilidade($cod_carro, $data_inicio, $data_fim)) {
        header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $cod_carro . "&erro=" . urlencode("Este carro já está reservado neste período."));
        exit;
    }

    $carroDAO = new CarroDAO();
    $carro = $carroDAO->findById($cod_carro);
    $precoDiaria = (float)$carro['preco_diaria'];

    $totalDias = (new DateTime($data_inicio))->diff(new DateTime($data_fim))->days;
    $planoDAO = new PlanoDAO();
    $planos = $planoDAO->getAll();

    usort($planos, fn($a,$b) => (int)$b['dias_minimos'] <=> (int)$a['dias_minimos']);
    $multiplicador = 1.0;
    foreach ($planos as $plano) {
        if ($totalDias >= (int)$plano['dias_minimos']) {
            $multiplicador = (float)$plano['multiplicador_valor'];
            break;
        }
    }
    $valorTotal = $totalDias * $precoDiaria * $multiplicador;

    $reservaDTO = new ReservaDTO();
    $reservaDTO->setCodUsuario($cod_usuario);
    $reservaDTO->setCodCarro($cod_carro);
    $reservaDTO->setDataInicio($data_inicio);
    $reservaDTO->setDataFim($data_fim);
    $reservaDTO->setValorTotal($valorTotal);

    $novaReservaId = $reservaDAO->create($reservaDTO);

    if ($novaReservaId) {
        if ($usuario['perfil'] === 'usuario') {
            $usuarioDAO->updateProfile($cod_usuario, 'cliente');
            $_SESSION['usuario']['perfil'] = 'cliente';
        }
        unset($_SESSION['reserva_pendente']);
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Sua reserva foi confirmada com sucesso!'];

        // --- LOG ---
        $detalhes = "Usuário '{$nomeUsuarioLogado}' criou a reserva #{$novaReservaId} para o carro ID {$cod_carro}.";
        registrarLog("CRIACAO_RESERVA", $detalhes, $cod_usuario);

        header("Location: " . BASE_URL . "/view/reservas/minhasReservas.php");
    } else {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao processar a reserva. Tente novamente.'];
        header("Location: " . BASE_URL . "/view/carros/index.php");
    }
    exit;
}

// -----------------------------
// Demais ações administrativas (com logs)
// -----------------------------
$reservaDAO = new ReservaDAO();
switch ($acao) {
    case 'mudar_status_ativa':
        $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
        if ($cod_reserva) {
            $reservaDAO->updateStatus($cod_reserva, 'ativa', 'alugado');
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Retirada confirmada. Reserva está ativa!'];

            // --- LOG ---
            $detalhes = "Usuário '{$nomeUsuarioLogado}' marcou a reserva #{$cod_reserva} como ativa.";
            registrarLog("MUDAR_STATUS_ATIVA", $detalhes);
        }
        header("Location: " . BASE_URL . "/view/admin/reserva.php");
        exit;

    case 'mudar_status_concluida':
        $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
        if ($cod_reserva) {
            $reservaDAO->updateStatus($cod_reserva, 'concluida', 'disponivel');
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Locação finalizada com sucesso!'];

            // --- LOG ---
            $detalhes = "Usuário '{$nomeUsuarioLogado}' marcou a reserva #{$cod_reserva} como concluída.";
            registrarLog("MUDAR_STATUS_CONCLUIDA", $detalhes);
        }
        header("Location: " . BASE_URL . "/view/admin/reserva.php");
        exit;

    case 'atualizar_reserva':
        $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
        $cod_carro   = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
        $data_inicio = $_POST['data_inicio'] ?? '';
        $data_fim    = $_POST['data_fim'] ?? '';
        $status      = $_POST['status'] ?? '';
        $valor_total = str_replace(',', '.', $_POST['valor_total'] ?? 0);

        if ($cod_reserva && $cod_carro && $data_inicio && $data_fim && $status) {
            $carro = (new CarroDAO())->findById($cod_carro);
            $precoDiaria = (float)$carro['preco_diaria'];
            $totalDias = (new DateTime($data_inicio))->diff(new DateTime($data_fim))->days;

            $planos = (new PlanoDAO())->getAll();
            usort($planos, fn($a,$b) => (int)$b['dias_minimos'] <=> (int)$a['dias_minimos']);
            $multiplicador = 1.0;
            foreach ($planos as $plano) {
                if ($totalDias >= (int)$plano['dias_minimos']) {
                    $multiplicador = (float)$plano['multiplicador_valor'];
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

            if ($reservaDAO->update($reservaDTO)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Reserva atualizada com sucesso!'];

                // --- LOG ---
                $detalhes = "Usuário '{$nomeUsuarioLogado}' atualizou a reserva #{$cod_reserva}. Novo status: {$status}.";
                registrarLog("ATUALIZACAO_RESERVA", $detalhes);
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao atualizar reserva.'];
            }
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Dados inválidos para atualização.'];
        }
        header("Location: " . BASE_URL . "/view/admin/reserva.php");
        exit;

    case 'excluir_reserva':
        if (!in_array($_SESSION['usuario']['perfil'], ['admin', 'gerente'])) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Você não tem permissão para excluir reservas.'];
        } else {
            $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
            if ($cod_reserva) {
                if ($reservaDAO->delete($cod_reserva)) {
                    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Reserva excluída com sucesso!'];

                    // --- LOG ---
                    $detalhes = "Usuário '{$nomeUsuarioLogado}' excluiu permanentemente a reserva #{$cod_reserva}.";
                    registrarLog("EXCLUSAO_RESERVA", $detalhes);
                } else {
                    $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao excluir reserva.'];
                }
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'ID de reserva inválido.'];
            }
        }
        header("Location: " . BASE_URL . "/view/admin/reserva.php");
        exit;

    case 'cancelar_reserva':
        $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
        $reserva = $reservaDAO->findById($cod_reserva);

        if (!$reserva) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Reserva não encontrada.'];
        } elseif ($reserva['cod_usuario'] != $idUsuarioLogado) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Você não tem permissão para cancelar esta reserva.'];
        } elseif ($reserva['status'] !== 'pendente') {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Esta reserva não pode mais ser cancelada.'];
        } else {
            if ($reservaDAO->cancel($cod_reserva)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Reserva cancelada com sucesso!'];

                // --- LOG ---
                $detalhes = "Usuário '{$nomeUsuarioLogado}' cancelou a reserva #{$cod_reserva}.";
                registrarLog("CANCELAMENTO_RESERVA", $detalhes);
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao cancelar a reserva.'];
            }
        }
        header("Location: " . BASE_URL . "/view/reservas/minhasReservas.php");
        exit;

    default:
        header("Location: " . BASE_URL . "/view/carros/index.php");
        exit;
}
