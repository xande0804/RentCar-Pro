<?php
require_once __DIR__ . "/Conexao.php";
require_once __DIR__ . "/../dto/CarroDTO.php";

class CarroDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getInstance();
    }

    public function getAll($filtros = []) {
        try {
            $sql = "SELECT * FROM tbl_carros";
            $params = [];
            $whereClauses = [];

            // Filtro por termo de busca (marca ou modelo)
            if (!empty($filtros['busca'])) {
                $whereClauses[] = "(marca LIKE :busca OR modelo LIKE :busca)";
                $params[':busca'] = '%' . $filtros['busca'] . '%';
            }

            // Filtro por status
            if (!empty($filtros['status'])) {
                $whereClauses[] = "status = :status";
                $params[':status'] = $filtros['status'];
            }

            if (!empty($whereClauses)) {
                $sql .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $sql .= " ORDER BY marca, modelo ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Erro ao listar carros: " . $e->getMessage());
            return [];
        }
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

    public function getAvailable($filtros = []) {
    try {
        // Base + alias
        $sql = "SELECT c.* FROM tbl_carros c";
        $params = [];

        // Favoritos (join opcional)
        if (!empty($filtros['somente_favoritos']) && !empty($filtros['cod_usuario'])) {
            $sql .= " JOIN tbl_favoritos f ON c.cod_carro = f.cod_carro";
            $params[':cod_usuario'] = $filtros['cod_usuario'];
        }

        // WHERE base
        $sql .= " WHERE c.status = 'disponivel'";

        // Se filtrar favoritos, restringe ao usuário
        if (isset($params[':cod_usuario'])) {
            $sql .= " AND f.cod_usuario = :cod_usuario";
        }

        // Disponibilidade por período
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $sql .= " AND c.cod_carro NOT IN (
                        SELECT r.cod_carro FROM tbl_reservas r
                        WHERE r.status IN ('pendente', 'ativa')
                          AND NOT (r.data_fim < :data_inicio OR r.data_inicio > :data_fim)
                     )";
            $params[':data_inicio'] = $filtros['data_inicio'];
            $params[':data_fim']    = $filtros['data_fim'];
        }

        // Demais filtros
        if (!empty($filtros['categoria']))   { $sql .= " AND c.categoria = :categoria";         $params[':categoria'] = $filtros['categoria']; }
        if (!empty($filtros['cambio']))      { $sql .= " AND c.cambio = :cambio";               $params[':cambio'] = $filtros['cambio']; }
        if (!empty($filtros['combustivel'])) { $sql .= " AND c.combustivel = :combustivel";     $params[':combustivel'] = $filtros['combustivel']; }
        if (!empty($filtros['preco_min']))   { $sql .= " AND c.preco_diaria >= :preco_min";     $params[':preco_min'] = $filtros['preco_min']; }
        if (!empty($filtros['preco_max']))   { $sql .= " AND c.preco_diaria <= :preco_max";     $params[':preco_max'] = $filtros['preco_max']; }

        // Ordenação
        if (!empty($filtros['ordenar'])) {
            if ($filtros['ordenar'] === 'preco_asc')  $sql .= " ORDER BY c.preco_diaria ASC, c.marca, c.modelo";
            elseif ($filtros['ordenar'] === 'preco_desc') $sql .= " ORDER BY c.preco_diaria DESC, c.marca, c.modelo";
            else $sql .= " ORDER BY c.marca, c.modelo ASC";
        } else {
            $sql .= " ORDER BY c.marca, c.modelo ASC";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
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
                        marca = :marca, modelo = :modelo, ano = :ano, cor = :cor, 
                        combustivel = :combustivel, cambio = :cambio, 
                        preco_diaria = :preco_diaria, status = :status
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