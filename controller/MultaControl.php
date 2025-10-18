<?php
session_start();
require_once __DIR__ . '/../config.php'; // Inclui a função registrarLog()
require_once __DIR__ . '/../model/dto/MultaDTO.php';
require_once __DIR__ . '/../model/dao/MultaDAO.php';

$redirectURL = BASE_URL . "/view/admin/multas.php";
$nomeUsuarioLogado = $_SESSION['usuario']['nome'] ?? 'Sistema';
$idUsuarioLogado = $_SESSION['usuario']['id'] ?? null;

// Segurança: Apenas funcionários e superiores podem gerenciar multas
if (!in_array($_SESSION['usuario']['perfil'] ?? '', ['admin', 'gerente', 'funcionario'])) {
    header("Location: " . BASE_URL);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $multaDAO = new MultaDAO();

    if ($acao === 'cadastrar') {
        $cod_reserva = filter_input(INPUT_POST, 'cod_reserva', FILTER_VALIDATE_INT);
        $descricao = trim($_POST['descricao'] ?? '');
        $valor = str_replace(',', '.', $_POST['valor'] ?? 0);
        $data_vencimento = $_POST['data_vencimento'] ?? '';

        if (!$cod_reserva || empty($descricao) || empty($valor) || empty($data_vencimento)) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Todos os campos são obrigatórios.'];
        } else {
            $multaDTO = new MultaDTO();
            $multaDTO->setCodReserva($cod_reserva);
            $multaDTO->setDescricao($descricao);
            $multaDTO->setValor($valor);
            $multaDTO->setDataVencimento($data_vencimento);
            $multaDTO->setCodUsuarioRegistro($idUsuarioLogado);
            $multaDTO->setObservacoes(trim($_POST['observacoes'] ?? ''));

            if ($multaDAO->create($multaDTO)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Multa registrada com sucesso!'];
                // --- REGISTRA O LOG ---
                $detalhes = "Usuário '{$nomeUsuarioLogado}' registrou uma multa de R$ {$valor} para a reserva #{$cod_reserva}.";
                registrarLog("CADASTRO_MULTA", $detalhes);
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao registrar multa. Verifique se o ID da reserva é válido.'];
            }
        }
    } else if ($acao === 'atualizar') {
        $cod_multa = filter_input(INPUT_POST, 'cod_multa', FILTER_VALIDATE_INT);
        $descricao = trim($_POST['descricao'] ?? '');
        $valor = str_replace(',', '.', $_POST['valor'] ?? 0);
        $status = $_POST['status'] ?? 'pendente';
        $data_vencimento = $_POST['data_vencimento'] ?? '';

        if (!$cod_multa || empty($descricao) || !is_numeric($valor) || empty($data_vencimento)) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Dados inválidos para atualização.'];
        } else {
            $multaDTO = new MultaDTO();
            $multaDTO->setCodMulta($cod_multa);
            $multaDTO->setDescricao($descricao);
            $multaDTO->setValor($valor);
            $multaDTO->setStatus($status);
            $multaDTO->setDataVencimento($data_vencimento);
            $multaDTO->setObservacoes(trim($_POST['observacoes'] ?? ''));

            // Regra de negócio: se a multa foi marcada como 'paga', registra a data de resolução
            if ($status === 'paga') {
                $multaDTO->setDataResolucao(date('Y-m-d H:i:s'));
            } else {
                $multaDTO->setDataResolucao(null); // Garante que a data seja nula para outros status
            }

            if ($multaDAO->update($multaDTO)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Multa atualizada com sucesso!'];
                $detalhes = "Usuário '{$nomeUsuarioLogado}' atualizou a multa #{$cod_multa}. Novo status: {$status}.";
                registrarLog("ATUALIZACAO_MULTA", $detalhes);
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao atualizar multa.'];
            }
        }
    }
}

header("Location: " . $redirectURL);
exit;