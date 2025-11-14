<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/dao/RelatorioDAO.php';

header('Content-Type: application/json; charset=utf-8');

// Segurança básica: só staff
$perfil = $_SESSION['usuario']['perfil'] ?? 'visitante';
if (!in_array($perfil, ['admin','gerente','funcionario'])) {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso negado']);
    exit;
}

$acao = $_GET['acao'] ?? $_POST['acao'] ?? '';
$dao  = new RelatorioDAO();

$filtros = [
    'inicio' => $_GET['inicio'] ?? $_POST['inicio'] ?? null,
    'fim'    => $_GET['fim']    ?? $_POST['fim']    ?? null,
];

try {
    switch ($acao) {
        case 'kpis':
            echo json_encode($dao->kpis($filtros));
            break;

        // ATUALIZADO: Renomeado para clareza
        case 'series_diarias': 
            echo json_encode($dao->seriesDiarias($filtros));
            break;
        
        // NOVO: Rota para o gráfico de faturamento mensal
        case 'series_mensais_fat':
            echo json_encode($dao->seriesMensaisFaturamento($filtros));
            break;

        case 'distribuicao_categoria':
            echo json_encode($dao->distribuicaoCategoria($filtros));
            break;

        case 'ocupacao':
            echo json_encode($dao->ocupacaoPorCarro($filtros));
            break;

        case 'top_clientes':
            echo json_encode($dao->topClientes($filtros));
            break;

        case 'top_carros':
            echo json_encode($dao->topCarros($filtros));
            break;

        case 'export_reservas':
            echo json_encode($dao->reservasTabela($filtros));
            break;

        default:
            http_response_code(400);
            echo json_encode(['erro' => 'Ação inválida']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha no relatório', 'msg' => $e->getMessage()]);
}