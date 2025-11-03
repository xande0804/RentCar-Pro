<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/dao/UsuarioDAO.php';
require_once __DIR__ . '/../model/dao/CarroDAO.php';
require_once __DIR__ . '/../model/dao/ReservaDAO.php';
require_once __DIR__ . '/../model/dao/PlanoDAO.php';
require_once __DIR__ . '/../model/dto/ReservaDTO.php';

$acao = $_GET['acao'] ?? $_POST['acao'] ?? '';
$nomeUsuarioLogado = $_SESSION['usuario']['nome'] ?? 'Sistema';
$idUsuarioLogado   = $_SESSION['usuario']['id'] ?? null;
$perfilUsuarioLogado = $_SESSION['usuario']['perfil'] ?? null;

if ($acao === 'iniciar') {
    $idCarro = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $clienteSelecionado = filter_input(INPUT_GET, 'cod_usuario', FILTER_VALIDATE_INT);

    if (!$idCarro) {
        header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Carro inválido."));
        exit;
    }

    // Se for funcionário/gerente/admin
    if (in_array($perfilUsuarioLogado, ['admin', 'gerente', 'funcionario'])) {
        if (!$clienteSelecionado) {
            header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Selecione um cliente antes de iniciar a reserva."));
            exit;
        }
        // Permite seguir, passando o cliente selecionado
        header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id={$idCarro}&cod_usuario={$clienteSelecionado}");
        exit;
    }

    // Caso comum (usuário/cliente)
    header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id={$idCarro}");
    exit;
}

// ----------------------------------------------------------------------
// AÇÃO: SALVAR_PENDENTE (Tela finalizar.php -> confirmar.php)
// ----------------------------------------------------------------------
if ($acao === 'salvar_pendente') {
    $cod_carro    = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
    $data_inicio  = $_POST['data_inicio'] ?? '';
    $data_fim     = $_POST['data_fim'] ?? '';
    $cod_usuario  = filter_input(INPUT_POST, 'cod_usuario', FILTER_VALIDATE_INT); // ID do cliente (se staff)

    // Opções do plano / extras vindas do form
    $plano_tipo   = $_POST['plano_tipo'] ?? 'ilimitado';        // 'ilimitado' | 'limitado'
    $km_limite    = isset($_POST['km_limite']) ? (int)$_POST['km_limite'] : null;
    $extras_input = isset($_POST['extras']) && is_array($_POST['extras']) ? $_POST['extras'] : [];

    // Validação básica
    if (!$cod_carro || empty($data_inicio) || empty($data_fim) || $data_fim <= $data_inicio) {
        header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id={$cod_carro}&erro=" . urlencode("Datas inválidas."));
        exit;
    }

    // Normaliza plano
    $plano_tipo = ($plano_tipo === 'limitado') ? 'limitado' : 'ilimitado';
    if ($plano_tipo === 'limitado') {
        $km_permitidos = [100, 200, 300];
        if (!in_array($km_limite, $km_permitidos, true)) {
            $km_limite = 100;
        }
    } else {
        $km_limite = null;
    }

    // Saneia extras
    $extras_permitidos = ['cadeirinha','seguro_completo','danos','roubo'];
    $extras = array_values(array_intersect($extras_input, $extras_permitidos));

    // --- Calcula um total estimado no servidor (mesma regra do JS) ---
    $carroDAO   = new CarroDAO();
    $carro      = $carroDAO->findById($cod_carro);
    $precoDia   = (float)($carro['preco_diaria'] ?? 0);

    // dias
    $dtIni      = new DateTime($data_inicio);
    $dtFim      = new DateTime($data_fim);
    $totalDias  = max(1, (int)$dtIni->diff($dtFim)->days);

    // multiplicadores do plano
    $multiplicador = 1.0;
    if ($plano_tipo === 'ilimitado') {
        $multiplicador = 1.25;
    } else {
        // limitado
        $map = [100 => 1.00, 200 => 1.10, 300 => 1.15];
        $multiplicador = $map[$km_limite] ?? 1.00;
    }

    // preços dos extras (por dia)
    $precosExtrasDia = [
        'cadeirinha'      => 12,
        'seguro_completo' => 35,
        'danos'           => 18,
        'roubo'           => 15,
    ];
    $extrasDiaTotal = 0.0;
    foreach ($extras as $ex) {
        $extrasDiaTotal += (float)($precosExtrasDia[$ex] ?? 0);
    }

    $valorEstimado = $totalDias * ($precoDia * $multiplicador + $extrasDiaTotal);

    // Padroniza reserva_pendente na sessão
    $_SESSION['reserva_pendente'] = [
        'cod_carro'     => $cod_carro,
        'data_inicio'   => $data_inicio,
        'data_fim'      => $data_fim,
        'cod_usuario'   => $cod_usuario ?: ($idUsuarioLogado ?? null), // ID do cliente (se staff) ou do logado

        // guarda escolhas para a etapa de pagamento/confirmar
        'plano' => [
            'tipo'      => $plano_tipo,      // 'ilimitado' | 'limitado'
            'km_limite' => $km_limite,       // null quando ilimitado
        ],
        'extras' => $extras,                 // array de strings
        'total_estimado' => $valorEstimado,  // número (float)
        'total_dias'     => $totalDias,      // inteiro
        'preco_diaria'   => $precoDia,       // referência
        'multiplicador'  => $multiplicador,  // referência
    ];

    // Decide próximo passo
    if (!$idUsuarioLogado) {
        header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Você precisa estar logado para continuar."));
        exit;
    }

    $usuarioDAO = new UsuarioDAO();
    $usuario    = $usuarioDAO->findById($idUsuarioLogado);
    if ($usuario && (int)$usuario['cadastro_completo'] === 0) {
        header("Location: " . BASE_URL . "/view/profile/completarCadastro.php?cod_usuario={$idUsuarioLogado}&cod_carro={$cod_carro}");
        exit;
    }

    header("Location: " . BASE_URL . "/view/reservas/confirmar.php");
    exit;
}


// ----------------------------------------------------------------------
// AÇÃO: FINALIZAR_RESERVA (Tela confirmar.php -> cria no banco)
// ----------------------------------------------------------------------
if ($acao === 'finalizar_reserva') {
    if (!$idUsuarioLogado) {
        // $_SESSION['reserva_pendente'] = $_POST; // A sessão já deve estar preenchida
        header("Location: " . BASE_URL . "/view/auth/login.php?erro=" . urlencode("Você precisa fazer login para confirmar a reserva."));
        exit;
    }

    // Pega dados da SESSÃO (mais seguro que o POST de confirmar.php)
    if (empty($_SESSION['reserva_pendente'])) {
        header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Sua sessão expirou. Comece novamente."));
        exit;
    }
    
    $dadosSessao = $_SESSION['reserva_pendente'];
    
    $cod_carro   = (int)$dadosSessao['cod_carro'];
    $data_inicio = $dadosSessao['data_inicio'];
    $data_fim    = $dadosSessao['data_fim'];
    $valorTotal  = (float)$dadosSessao['total_estimado'];
    $cod_usuario = !empty($dadosSessao['cod_usuario'])
        ? (int)$dadosSessao['cod_usuario']
        : (int)$idUsuarioLogado;

    // Validação
    if (!$cod_carro || empty($data_inicio) || empty($data_fim) || $data_fim <= $data_inicio) {
        header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $cod_carro . "&erro=" . urlencode("Datas inválidas."));
        exit;
    }

    // (Validação de cadastro completo já foi feita em confirmar.php)
    
    $reservaDAO = new ReservaDAO();
    if (!$reservaDAO->checkDisponibilidade($cod_carro, $data_inicio, $data_fim)) {
        header("Location: " . BASE_URL . "/view/reservas/finalizar.php?id=" . $cod_carro . "&erro=" . urlencode("Este carro já está reservado neste período."));
        exit;
    }

    $reservaDTO = new ReservaDTO();
    $reservaDTO->setCodUsuario($cod_usuario);
    $reservaDTO->setCodCarro($cod_carro);
    $reservaDTO->setDataInicio($data_inicio);
    $reservaDTO->setDataFim($data_fim);
    $reservaDTO->setValorTotal($valorTotal);
    // Status 'pendente' já é o default no DAO

    // O DAO precisa retornar o ID
    $novaReservaId = $reservaDAO->create($reservaDTO); 

    if ($novaReservaId) {
        $usuarioDAO = new UsuarioDAO();
        $usuario = $usuarioDAO->findById($cod_usuario); // Pega o dono da reserva
        
        if ($usuario && $usuario['perfil'] === 'usuario') {
            $usuarioDAO->updateProfile($cod_usuario, 'cliente');
            if ($idUsuarioLogado == $cod_usuario) { // Só muda a sessão se for o próprio
                $_SESSION['usuario']['perfil'] = 'cliente';
            }
        }
        
        unset($_SESSION['reserva_pendente']); // Limpa a sessão
        
        // MENSAGEM alterada para refletir o próximo passo
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Reserva confirmada! Efetue o pagamento para garantir seu carro.'];

        // --- LOG ---
        $detalhes = "Usuário '{$nomeUsuarioLogado}' criou a reserva #{$novaReservaId} (valor R$ {$valorTotal}) para o carro ID {$cod_carro}. Status: Pendente.";
        registrarLog("CRIACAO_RESERVA", $detalhes, $idUsuarioLogado);

        // Redireciona para Minhas Reservas (onde o botão Pagar vai aparecer)
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
        // (Mantido seu código original)
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

            // (Lógica de plano/multiplicador - mantida)
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
            $reservaDTO->setValorTotal($valorTotal); // Usa o recalculado

            if ($reservaDAO->update($reservaDTO)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Reserva atualizada com sucesso!'];
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
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Esta reserva não pode mais ser cancelada (já foi paga ou está ativa).'];
        } else {
            if ($reservaDAO->cancel($cod_reserva)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Reserva cancelada com sucesso!'];
                $detalhes = "Usuário '{$nomeUsuarioLogado}' cancelou a reserva #{$cod_reserva}.";
                registrarLog("CANCELAMENTO_RESERVA", $detalhes);
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao cancelar a reserva.'];
            }
        }
        header("Location: " . BASE_URL . "/view/reservas/minhasReservas.php");
        exit;

    case 'processar_pagamento':
        $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
        $metodo_pagamento = $_POST['metodo_pagamento'] ?? 'cartao'; // 'cartao' ou 'pix'

        if (!$cod_reserva || !$idUsuarioLogado) {
            header("Location: " . BASE_URL . "/view/reservas/minhasReservas.php?erro=" . urlencode("Pagamento inválido."));
            exit;
        }

        $reserva = $reservaDAO->findById($cod_reserva);
        
        // Validação tripla: reserva existe, pertence ao usuário E está pendente
        if (!$reserva || $reserva['cod_usuario'] != $idUsuarioLogado || $reserva['status'] !== 'pendente') {
            header("Location: " . BASE_URL . "/view/reservas/minhasReservas.php?erro=" . urlencode("Não foi possível processar o pagamento para esta reserva."));
            exit;
        }
        
        // Simulação aprovada!
        // Atualiza o status da reserva de 'pendente' para 'aguardando_retirada'
        // O status do carro NÃO muda, ele continua 'reservado'
        $sucesso = $reservaDAO->updateStatus($cod_reserva, 'aguardando_retirada', 'reservado');
        
        if ($sucesso) {
             $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Pagamento aprovado! Sua reserva está confirmada.'];
             
             // --- LOG ---
             $detalhes = "Usuário '{$nomeUsuarioLogado}' (ID: {$idUsuarioLogado}) pagou a reserva #{$cod_reserva} via {$metodo_pagamento}. Status alterado para 'aguardando_retirada'.";
             registrarLog("PAGAMENTO_RESERVA", $detalhes, $idUsuarioLogado);

        } else {
             $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Pagamento simulado falhou ao atualizar o banco.'];
        }
        
        header("Location: " . BASE_URL . "/view/reservas/minhasReservas.php");
        exit;


    default:
        header("Location: " . BASE_URL . "/view/carros/index.php");
        exit;
}