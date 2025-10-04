<?php
require_once __DIR__ . "/Conexao.php";

class PlanoDAO {
    private $pdo;
    public function __construct() {
        $this->pdo = Conexao::getInstance();
    }

    public function getAll() {
        $sql = "SELECT * FROM tbl_planos_aluguel ORDER BY dias_minimos DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}