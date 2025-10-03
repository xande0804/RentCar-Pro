<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . "/../model/dto/CarroDTO.php";
require_once __DIR__ . "/../model/dao/CarroDAO.php";

$redirectURL = BASE_URL . "/view/admin/carros.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $carroDAO = new CarroDAO();

    $carroDTO = new CarroDTO();
    // Pega os dados comuns a create e update
    $carroDTO->setMarca(trim($_POST['marca'] ?? ''));
    $carroDTO->setModelo(trim($_POST['modelo'] ?? ''));
    $carroDTO->setAno(filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT));
    $carroDTO->setCor(trim($_POST['cor'] ?? ''));
    $carroDTO->setPrecoDiaria(str_replace(',', '.', $_POST['preco_diaria'] ?? 0));
    $carroDTO->setStatus($_POST['status'] ?? 'disponivel');
    $carroDTO->setCombustivel($_POST['combustivel'] ?? 'flex');
    $carroDTO->setCambio($_POST['cambio'] ?? 'manual');

    if ($acao === 'cadastrar') {
        if ($carroDAO->create($carroDTO)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro adicionado com sucesso!'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao adicionar carro.'];
        }
    } 
    else if ($acao === 'atualizar') {
        $cod_carro = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
        if (!$cod_carro) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'ID do carro é inválido.'];
        } else {
            $carroDTO->setCodCarro($cod_carro);
            if ($carroDAO->update($carroDTO)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro atualizado com sucesso!'];
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
            if ($carroDAO->delete($cod_carro)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro excluído com sucesso!'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao excluir carro.'];
            }
        }
    }
}

header("Location: " . $redirectURL);
exit;