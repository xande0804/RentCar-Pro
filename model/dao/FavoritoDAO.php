<?php
require_once __DIR__ . "/Conexao.php";

class FavoritoDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getInstance();
    }

    // Adiciona um carro aos favoritos de um usuário
    public function add($cod_usuario, $cod_carro) {
        try {
            $sql = "INSERT INTO tbl_favoritos (cod_usuario, cod_carro) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$cod_usuario, $cod_carro]);
        } catch (PDOException $e) {
            error_log("Erro ao adicionar favorito: " . $e->getMessage());
            return false;
        }
    }

    // Remove um carro dos favoritos de um usuário
    public function remove($cod_usuario, $cod_carro) {
        try {
            $sql = "DELETE FROM tbl_favoritos WHERE cod_usuario = ? AND cod_carro = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$cod_usuario, $cod_carro]);
        } catch (PDOException $e) {
            error_log("Erro ao remover favorito: " . $e->getMessage());
            return false;
        }
    }

    // Verifica se um carro específico já é favorito de um usuário
    public function isFavorito($cod_usuario, $cod_carro) {
        $sql = "SELECT COUNT(*) FROM tbl_favoritos WHERE cod_usuario = ? AND cod_carro = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cod_usuario, $cod_carro]);
        return $stmt->fetchColumn() > 0;
    }

    // Retorna um array com os IDs de todos os carros favoritados por um usuário
    public function getFavoritosByUsuario($cod_usuario) {
        $sql = "SELECT cod_carro FROM tbl_favoritos WHERE cod_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cod_usuario]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Retorna apenas a coluna 'cod_carro'
    }
}