<?php
require_once __DIR__ . "/Conexao.php";
require_once __DIR__ . "/../dto/CarroDTO.php";

class CarroDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getInstance();
    }

    public function create(CarroDTO $carro) {
        try {
            $sql = "INSERT INTO tbl_carros (marca, modelo, ano, cor, combustivel, cambio, preco_diaria, status) 
                    VALUES (:marca, :modelo, :ano, :cor, :combustivel, :cambio, :preco_diaria, :status)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":marca", $carro->getMarca());
            $stmt->bindValue(":modelo", $carro->getModelo());
            $stmt->bindValue(":ano", $carro->getAno());
            $stmt->bindValue(":cor", $carro->getCor());
            $stmt->bindValue(":combustivel", $carro->getCombustivel());
            $stmt->bindValue(":cambio", $carro->getCambio());
            $stmt->bindValue(":preco_diaria", $carro->getPrecoDiaria());
            $stmt->bindValue(":status", $carro->getStatus());
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao criar carro: " . $e->getMessage());
            return false;
        }
    }

    public function getAll() {
        try {
            $sql = "SELECT * FROM tbl_carros ORDER BY marca, modelo ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar carros: " . $e->getMessage());
            return [];
        }
    }

    public function getAvailable() {
        try {
            $sql = "SELECT * FROM tbl_carros WHERE status = 'disponivel' ORDER BY marca, modelo ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar carros disponíveis: " . $e->getMessage());
            return [];
        }
    }

    public function findById($id) {
        try {
            $sql = "SELECT * FROM tbl_carros WHERE cod_carro = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar carro por ID: " . $e->getMessage());
            return null;
        }
    }

    public function update(CarroDTO $carro) {
        try {
            $sql = "UPDATE tbl_carros SET 
                        marca = :marca, 
                        modelo = :modelo, 
                        ano = :ano, 
                        cor = :cor, 
                        combustivel = :combustivel, 
                        cambio = :cambio, 
                        preco_diaria = :preco_diaria, 
                        status = :status
                    WHERE cod_carro = :cod_carro";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":marca", $carro->getMarca());
            $stmt->bindValue(":modelo", $carro->getModelo());
            $stmt->bindValue(":ano", $carro->getAno());
            $stmt->bindValue(":cor", $carro->getCor());
            $stmt->bindValue(":combustivel", $carro->getCombustivel());
            $stmt->bindValue(":cambio", $carro->getCambio());
            $stmt->bindValue(":preco_diaria", $carro->getPrecoDiaria());
            $stmt->bindValue(":status", $carro->getStatus());
            $stmt->bindValue(":cod_carro", $carro->getCodCarro(), PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar carro: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $sql = "DELETE FROM tbl_carros WHERE cod_carro = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar carro: " . $e->getMessage());
            return false;
        }
    }
}