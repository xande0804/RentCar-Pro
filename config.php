<?php
// Define a URL base completa do projeto.
define('BASE_URL', 'http://localhost/Projeto');

/**
 * Função centralizada para registrar logs de auditoria.
 * Automatiza a criação do LogDAO e LogDTO.
 *
 * @param string $acao      O tipo de ação (ex: "LOGIN_FALHA", "CADASTRO_CARRO").
 * @param string $detalhes  A descrição completa do que aconteceu.
 * @param int|null $cod_usuario O ID do usuário que realizou a ação (opcional, pega da sessão se não for fornecido).
 */
function registrarLog(string $acao, string $detalhes, ?int $cod_usuario = null) {
    // Se o ID do usuário não for informado, a função tenta pegar da sessão logada.
    // Isso automatiza o processo na maioria dos casos.
    if ($cod_usuario === null && isset($_SESSION['usuario']['id'])) {
        $cod_usuario = $_SESSION['usuario']['id'];
    }

    // Inclui os arquivos necessários somente quando a função é chamada.
    require_once __DIR__ . '/model/dao/LogDAO.php';
    require_once __DIR__ . '/model/dto/LogDTO.php';

    try {
        $logDAO = new LogDAO();
        $logDTO = new LogDTO();
        $logDTO->setCodUsuario($cod_usuario);
        $logDTO->setAcaoRealizada($acao);
        $logDTO->setDetalhes($detalhes);
        $logDAO->create($logDTO);
    } catch (Exception $e) {
        // Se algo der errado ao registrar o log, isso não quebra o sistema.
        // Apenas registra o erro no log de erros do servidor.
        error_log("ERRO AO REGISTRAR LOG DE AUDITORIA: " . $e->getMessage());
    }
}