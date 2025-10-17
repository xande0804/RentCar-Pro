<?php
require_once __DIR__ . "/Conexao.php";
require_once __DIR__ . "/../dto/LogDTO.php";

class LogDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getInstance();
    }

    public function create(LogDTO $log) {
        try {
            $sql = "INSERT INTO tbl_logs (cod_usuario, acao_realizada, detalhes) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $log->getCodUsuario(),
                $log->getAcaoRealizada(),
                $log->getDetalhes()
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao registrar log: " . $e->getMessage());
            return false;
        }
    }

    public function getAll() {
        try {
            // JOIN para buscar o nome do usuário junto com o log
            $sql = "SELECT l.*, u.nome as nome_usuario 
                    FROM tbl_logs l
                    LEFT JOIN tbl_usuarios u ON l.cod_usuario = u.cod_usuario
                    ORDER BY l.data_hora DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar logs: " . $e->getMessage());
            return [];
        }
    }
}