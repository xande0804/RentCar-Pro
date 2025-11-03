<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . "/../model/dto/CarroDTO.php";
require_once __DIR__ . "/../model/dao/CarroDAO.php";

$redirectURL = BASE_URL . "/view/admin/carros.php";

// para logs
$nomeUsuarioLogado = $_SESSION['usuario']['nome'] ?? 'Sistema';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $carroDAO = new CarroDAO();

    

    if ($acao === 'cadastrar') {
        $carroDTO = new CarroDTO();
        $carroDTO->setMarca(trim($_POST['marca'] ?? ''));
        $carroDTO->setModelo(trim($_POST['modelo'] ?? ''));
        $carroDTO->setCategoria($_POST['categoria'] ?? null);             
        $carroDTO->setAno(filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT));
        $carroDTO->setCor(trim($_POST['cor'] ?? ''));
        $carroDTO->setCombustivel($_POST['combustivel'] ?? 'flex');
        $carroDTO->setCambio($_POST['cambio'] ?? 'manual');
        $carroDTO->setArCondicionado(isset($_POST['ar_condicionado']) ? 1 : 0);
        $carroDTO->setPrecoDiaria($_POST['preco_diaria'] ?? 0);
        $carroDTO->setStatus($_POST['status'] ?? 'disponivel');
        $carroDTO->setKmTotal((int)($_POST['km_total'] ?? 0));            
        $carroDTO->setDescricao(trim($_POST['descricao'] ?? null));        

        if ($carroDAO->create($carroDTO)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro adicionado com sucesso!'];
            registrarLog("CADASTRO_CARRO", "Usuário '{$nomeUsuarioLogado}' adicionou o novo carro: {$carroDTO->getMarca()} {$carroDTO->getModelo()}.");
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
            $carroDTO->setCategoria($_POST['categoria'] ?? null);           
            $carroDTO->setAno(filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT));
            $carroDTO->setCor(trim($_POST['cor'] ?? ''));
            $carroDTO->setCombustivel($_POST['combustivel'] ?? 'flex');
            $carroDTO->setCambio($_POST['cambio'] ?? 'manual');
            $carroDTO->setArCondicionado(isset($_POST['ar_condicionado']) ? 1 : 0);
            $carroDTO->setPrecoDiaria($_POST['preco_diaria'] ?? 0);
            $carroDTO->setStatus($_POST['status'] ?? 'disponivel');
            $carroDTO->setKmTotal((int)($_POST['km_total'] ?? 0));          
            $carroDTO->setDescricao(trim($_POST['descricao'] ?? null));     

            if ($carroDAO->update($carroDTO)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro atualizado com sucesso!'];
                registrarLog("ATUALIZACAO_CARRO", "Usuário '{$nomeUsuarioLogado}' atualizou os dados do carro '{$carroDTO->getMarca()} {$carroDTO->getModelo()}' (ID: {$cod_carro}).");
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
            $carroParaExcluir = $carroDAO->findById($cod_carro);
            if ($carroDAO->delete($cod_carro)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro excluído com sucesso!'];
                $nomeCarro = $carroParaExcluir ? "{$carroParaExcluir['marca']} {$carroParaExcluir['modelo']}" : "ID {$cod_carro}";
                registrarLog("EXCLUSAO_CARRO", "Usuário '{$nomeUsuarioLogado}' excluiu o carro '{$nomeCarro}'.");
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao excluir carro. Verifique se ele não está associado a uma reserva ativa.'];
            }
        }
    }
}

header("Location: " . $redirectURL);
exit;
