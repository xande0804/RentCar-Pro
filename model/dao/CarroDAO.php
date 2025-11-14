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

            if (!empty($filtros['busca'])) {
                $whereClauses[] = "(marca LIKE :busca OR modelo LIKE :busca)";
                $params[':busca'] = '%' . $filtros['busca'] . '%';
            }

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
            $sql = "INSERT INTO tbl_carros 
                (marca, modelo, categoria, ano, cor, combustivel, cambio, ar_condicionado, preco_diaria, status, km_total, descricao, imagem_url) 
            VALUES 
                (:marca, :modelo, :categoria, :ano, :cor, :combustivel, :cambio, :ar_condicionado, :preco_diaria, :status, :km_total, :descricao, :imagem_url)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":marca", $carro->getMarca());
            $stmt->bindValue(":modelo", $carro->getModelo());
            $stmt->bindValue(":categoria", $carro->getCategoria());
            $stmt->bindValue(":ano", $carro->getAno(), PDO::PARAM_INT);
            $stmt->bindValue(":cor", $carro->getCor());
            $stmt->bindValue(":combustivel", $carro->getCombustivel());
            $stmt->bindValue(":cambio", $carro->getCambio());
            $stmt->bindValue(":ar_condicionado", $carro->getArCondicionado(), PDO::PARAM_INT);
            $stmt->bindValue(":preco_diaria", $carro->getPrecoDiaria());
            $stmt->bindValue(":status", $carro->getStatus());
            $stmt->bindValue(":km_total", $carro->getKmTotal(), PDO::PARAM_INT);
            $stmt->bindValue(":descricao", $carro->getDescricao());
            $stmt->bindValue(":imagem_url", $carro->getImagemUrl());
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Erro ao criar carro: " . $e->getMessage());
            return false;
        }
    }

    public function getAvailable($filtros = []) {
        try {
            $sql = "SELECT c.* FROM tbl_carros c";
            $params = [];

            if (!empty($filtros['somente_favoritos']) && !empty($filtros['cod_usuario'])) {
                $sql .= " JOIN tbl_favoritos f ON c.cod_carro = f.cod_carro";
                $params[':cod_usuario'] = $filtros['cod_usuario'];
            }

            $sql .= " WHERE c.status = 'disponivel'";

            if (isset($params[':cod_usuario'])) {
                $sql .= " AND f.cod_usuario = :cod_usuario";
            }

            if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
                $sql .= " AND c.cod_carro NOT IN (
                            SELECT r.cod_carro FROM tbl_reservas r
                            WHERE r.status IN ('pendente', 'ativa')
                              AND NOT (r.data_fim < :data_inicio OR r.data_inicio > :data_fim)
                         )";
                $params[':data_inicio'] = $filtros['data_inicio'];
                $params[':data_fim']    = $filtros['data_fim'];
            }

            if (!empty($filtros['categoria']))   { $sql .= " AND c.categoria = :categoria";         $params[':categoria']   = $filtros['categoria']; }
            if (!empty($filtros['cambio']))      { $sql .= " AND c.cambio = :cambio";               $params[':cambio']      = $filtros['cambio']; }
            if (!empty($filtros['combustivel'])) { $sql .= " AND c.combustivel = :combustivel";     $params[':combustivel'] = $filtros['combustivel']; }
            if (!empty($filtros['preco_min']))   { $sql .= " AND c.preco_diaria >= :preco_min";     $params[':preco_min']   = $filtros['preco_min']; }
            if (!empty($filtros['preco_max']))   { $sql .= " AND c.preco_diaria <= :preco_max";     $params[':preco_max']   = $filtros['preco_max']; }

            if (!empty($filtros['ordenar'])) {
                if ($filtros['ordenar'] === 'preco_asc')       $sql .= " ORDER BY c.preco_diaria ASC, c.marca, c.modelo";
                elseif ($filtros['ordenar'] === 'preco_desc')  $sql .= " ORDER BY c.preco_diaria DESC, c.marca, c.modelo";
                else                                           $sql .= " ORDER BY c.marca, c.modelo ASC";
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
                marca = :marca,
                modelo = :modelo,
                categoria = :categoria,
                ano = :ano,
                cor = :cor,
                combustivel = :combustivel,
                cambio = :cambio,
                ar_condicionado = :ar_condicionado,
                preco_diaria = :preco_diaria,
                status = :status,
                km_total = :km_total,
                descricao = :descricao,
                imagem_url = :imagem_url
            WHERE cod_carro = :cod_carro";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":cod_carro", $carro->getCodCarro(), PDO::PARAM_INT);
            $stmt->bindValue(":marca", $carro->getMarca());
            $stmt->bindValue(":modelo", $carro->getModelo());
            $stmt->bindValue(":categoria", $carro->getCategoria());
            $stmt->bindValue(":ano", $carro->getAno(), PDO::PARAM_INT);
            $stmt->bindValue(":cor", $carro->getCor());
            $stmt->bindValue(":combustivel", $carro->getCombustivel());
            $stmt->bindValue(":cambio", $carro->getCambio());
            $stmt->bindValue(":ar_condicionado", $carro->getArCondicionado(), PDO::PARAM_INT);
            $stmt->bindValue(":preco_diaria", $carro->getPrecoDiaria());
            $stmt->bindValue(":status", $carro->getStatus());
            $stmt->bindValue(":km_total", $carro->getKmTotal(), PDO::PARAM_INT);
            $stmt->bindValue(":descricao", $carro->getDescricao());
            $stmt->bindValue(":imagem_url", $carro->getImagemUrl());
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