<?php
require_once __DIR__ . "/Conexao.php";
require_once __DIR__ . "/../dto/MultaDTO.php";

class MultaDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getInstance();
    }

    // Cria uma nova multa no banco de dados
    public function create(MultaDTO $multa) {
        try {
            $sql = "INSERT INTO tbl_multas (cod_reserva, descricao, valor, data_vencimento, cod_usuario_registro, observacoes) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $multa->getCodReserva(),
                $multa->getDescricao(),
                $multa->getValor(),
                $multa->getDataVencimento(),
                $multa->getCodUsuarioRegistro(),
                $multa->getObservacoes()
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao criar multa: " . $e->getMessage());
            return false;
        }
    }

    // Atualiza uma multa existente
    public function update(MultaDTO $multa) {
        try {
            $sql = "UPDATE tbl_multas SET 
                        descricao = ?, valor = ?, status = ?, 
                        data_vencimento = ?, data_resolucao = ?, observacoes = ?
                    WHERE cod_multa = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $multa->getDescricao(),
                $multa->getValor(),
                $multa->getStatus(),
                $multa->getDataVencimento(),
                $multa->getDataResolucao(),
                $multa->getObservacoes(),
                $multa->getCodMulta()
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar multa: " . $e->getMessage());
            return false;
        }
    }

    // Busca uma multa específica pelo seu ID
    public function findById($cod_multa) {
        try {
            $sql = "SELECT * FROM tbl_multas WHERE cod_multa = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cod_multa]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar multa por ID: " . $e->getMessage());
            return null;
        }
    }

    // Lista todas as multas, com filtros
    public function getAll($filtros = []) {
        try {
            // A consulta principal já une todas as tabelas necessárias
            $sql = "SELECT m.*, r.cod_reserva, u_cliente.nome as nome_cliente, u_registro.nome as nome_registro
                    FROM tbl_multas m
                    JOIN tbl_reservas r ON m.cod_reserva = r.cod_reserva
                    JOIN tbl_usuarios u_cliente ON r.cod_usuario = u_cliente.cod_usuario
                    LEFT JOIN tbl_usuarios u_registro ON m.cod_usuario_registro = u_registro.cod_usuario";
            
            // Adicione a lógica de filtros aqui no futuro...

            $sql .= " ORDER BY m.data_registro DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar multas: " . $e->getMessage());
            return [];
        }
    }
}