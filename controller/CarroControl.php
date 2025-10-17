<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . "/../model/dto/CarroDTO.php";
require_once __DIR__ . "/../model/dao/CarroDAO.php";

$redirectURL = BASE_URL . "/view/admin/carros.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $carroDAO = new CarroDAO();

    // --- AÇÃO: CADASTRAR CARRO ---
    if ($acao === 'cadastrar') {
        $carroDTO = new CarroDTO();
        $carroDTO->setMarca(trim($_POST['marca'] ?? ''));
        $carroDTO->setModelo(trim($_POST['modelo'] ?? ''));
        $carroDTO->setAno(filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT));
        $carroDTO->setCor(trim($_POST['cor'] ?? ''));
        $carroDTO->setPrecoDiaria(str_replace(',', '.', $_POST['preco_diaria'] ?? 0));
        $carroDTO->setStatus($_POST['status'] ?? 'disponivel');
        $carroDTO->setCombustivel($_POST['combustivel'] ?? 'flex');
        $carroDTO->setCambio($_POST['cambio'] ?? 'manual');

        if ($carroDAO->create($carroDTO)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro adicionado com sucesso!'];
            // --- REGISTRA O LOG ---
            $detalhes = "Usuário '{$nomeUsuarioLogado}' adicionou o novo carro: {$carroDTO->getMarca()} {$carroDTO->getModelo()}.";
            registrarLog("CADASTRO_CARRO", $detalhes);
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao adicionar carro.'];
        }
    }

    else if ($acao === 'atualizar') {
        $cod_carro = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
        if (!$cod_carro) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'ID do carro é inválido.'];
        } else {
            $carroDTO = new CarroDTO();
            $carroDTO->setCodCarro($cod_carro);
            $carroDTO->setMarca(trim($_POST['marca'] ?? ''));
            $carroDTO->setModelo(trim($_POST['modelo'] ?? ''));
            $carroDTO->setAno(filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT));
            $carroDTO->setCor(trim($_POST['cor'] ?? ''));
            $carroDTO->setPrecoDiaria(str_replace(',', '.', $_POST['preco_diaria'] ?? 0));
            $carroDTO->setStatus($_POST['status'] ?? 'disponivel');
            $carroDTO->setCombustivel($_POST['combustivel'] ?? 'flex');
            $carroDTO->setCambio($_POST['cambio'] ?? 'manual');

            if ($carroDAO->update($carroDTO)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro atualizado com sucesso!'];
                // --- REGISTRA O LOG ---
                $detalhes = "Usuário '{$nomeUsuarioLogado}' atualizou os dados do carro '{$carroDTO->getMarca()} {$carroDTO->getModelo()}' (ID: {$cod_carro}).";
                registrarLog("ATUALIZACAO_CARRO", $detalhes);
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao atualizar carro.'];
            }
        }
    }
    
    else if ($acao === 'excluir') {
        $cod_carro = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
        if (!$cod_carro) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'ID do carro é inválido.'];
        } else {
            // Pega os dados do carro ANTES de deletar, para ter no log
            $carroParaExcluir = $carroDAO->findById($cod_carro);
            
            if ($carroDAO->delete($cod_carro)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro excluído com sucesso!'];
                // --- REGISTRA O LOG ---
                $nomeCarro = $carroParaExcluir ? "{$carroParaExcluir['marca']} {$carroParaExcluir['modelo']}" : "ID {$cod_carro}";
                $detalhes = "Usuário '{$nomeUsuarioLogado}' excluiu o carro '{$nomeCarro}'.";
                registrarLog("EXCLUSAO_CARRO", $detalhes);
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao excluir carro. Verifique se ele não está associado a uma reserva ativa.'];
            }
        }
    }
}

header("Location: " . $redirectURL);
exit;