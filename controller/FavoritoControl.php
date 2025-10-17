<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/dao/FavoritoDAO.php';

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// --- Validações de Segurança ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido.']);
    exit;
}

$idUsuarioLogado = $_SESSION['usuario']['id'] ?? null;
if (!$idUsuarioLogado) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não está logado.']);
    exit;
}

$cod_carro = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
if (!$cod_carro) {
    echo json_encode(['status' => 'error', 'message' => 'ID do carro inválido.']);
    exit;
}
// --- Fim das Validações ---

$favoritoDAO = new FavoritoDAO();

try {
    // Verifica se já é favorito para decidir se adiciona ou remove
    if ($favoritoDAO->isFavorito($idUsuarioLogado, $cod_carro)) {
        // Se já é, remove
        $favoritoDAO->remove($idUsuarioLogado, $cod_carro);
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        // Se não é, adiciona
        $favoritoDAO->add($idUsuarioLogado, $cod_carro);
        echo json_encode(['status' => 'success', 'action' => 'added']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro no servidor.']);
}