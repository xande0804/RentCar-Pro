<?php
require_once __DIR__ . "/Conexao.php";
require_once __DIR__ . "/../dto/ReservaDTO.php";

class ReservaDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getInstance();
    }

    public function create(ReservaDTO $reserva) {
        try {
            $this->pdo->beginTransaction();

            $sql_reserva = "INSERT INTO tbl_reservas (cod_usuario, cod_carro, data_inicio, data_fim, valor_total, status) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_reserva = $this->pdo->prepare($sql_reserva);
            $stmt_reserva->execute([
                $reserva->getCodUsuario(),
                $reserva->getCodCarro(),
                $reserva->getDataInicio(),
                $reserva->getDataFim(),
                $reserva->getValorTotal(),
                'pendente'
            ]);

            $sql_carro = "UPDATE tbl_carros SET status = 'reservado' WHERE cod_carro = ?";
            $stmt_carro = $this->pdo->prepare($sql_carro);
            $stmt_carro->execute([$reserva->getCodCarro()]);

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erro ao criar reserva: " . $e->getMessage());
            return false;
        }
    }

    public function getAll() {
        try {
            $sql = "SELECT r.*, c.marca, c.modelo, u.nome as nome_usuario 
                    FROM tbl_reservas r
                    JOIN tbl_carros c ON r.cod_carro = c.cod_carro
                    JOIN tbl_usuarios u ON r.cod_usuario = u.cod_usuario
                    ORDER BY r.data_inicio DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar todas as reservas: " . $e->getMessage());
            return [];
        }
    }

    public function getByUserId($userId) {
        try {
            $sql = "SELECT r.*, c.marca, c.modelo 
                    FROM tbl_reservas r
                    JOIN tbl_carros c ON r.cod_carro = c.cod_carro
                    WHERE r.cod_usuario = ?
                    ORDER BY r.data_inicio DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar reservas do usuário: " . $e->getMessage());
            return [];
        }
    }

    public function update(ReservaDTO $reserva) {
        try {
            $sql = "UPDATE tbl_reservas SET data_inicio = ?, data_fim = ?, valor_total = ?, status = ? WHERE cod_reserva = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $reserva->getDataInicio(),
                $reserva->getDataFim(),
                $reserva->getValorTotal(),
                $reserva->getStatus(),
                $reserva->getCodReserva()
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar reserva: " . $e->getMessage());
            return false;
        }
    }

    public function delete($reservaId) {
        try {
            $this->pdo->beginTransaction();

            $stmt_get_car = $this->pdo->prepare("SELECT cod_carro FROM tbl_reservas WHERE cod_reserva = ?");
            $stmt_get_car->execute([$reservaId]);
            $carroId = $stmt_get_car->fetchColumn();

            $stmt_delete = $this->pdo->prepare("DELETE FROM tbl_reservas WHERE cod_reserva = ?");
            $stmt_delete->execute([$reservaId]);

            if ($carroId) {
                $stmt_carro = $this->pdo->prepare("UPDATE tbl_carros SET status = 'disponivel' WHERE cod_carro = ?");
                $stmt_carro->execute([$carroId]);
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erro ao deletar reserva: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatus($reservaId, $novoStatusReserva, $novoStatusCarro) {
        try {
            $this->pdo->beginTransaction();

            $sql_reserva = "UPDATE tbl_reservas SET status = ? WHERE cod_reserva = ?";
            $stmt_reserva = $this->pdo->prepare($sql_reserva);
            $stmt_reserva->execute([$novoStatusReserva, $reservaId]);

            $stmt_get_car = $this->pdo->prepare("SELECT cod_carro FROM tbl_reservas WHERE cod_reserva = ?");
            $stmt_get_car->execute([$reservaId]);
            $carroId = $stmt_get_car->fetchColumn();

            if ($carroId) {
                $sql_carro = "UPDATE tbl_carros SET status = ? WHERE cod_carro = ?";
                $stmt_carro = $this->pdo->prepare($sql_carro);
                $stmt_carro->execute([$novoStatusCarro, $carroId]);
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erro ao atualizar status da reserva: " . $e->getMessage());
            return false;
        }
    }

    public function findById($reservaId) {
        try {
            $sql = "SELECT * FROM tbl_reservas WHERE cod_reserva = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$reservaId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar reserva por ID: " . $e->getMessage());
            return null;
        }
    }

    public function cancel($reservaId) {
        try {
            $this->pdo->beginTransaction();

            $reserva = $this->findById($reservaId);
            if (!$reserva) throw new Exception("Reserva não encontrada.");

            $sql_reserva = "UPDATE tbl_reservas SET status = 'cancelada' WHERE cod_reserva = ?";
            $stmt_reserva = $this->pdo->prepare($sql_reserva);
            $stmt_reserva->execute([$reservaId]);

            $sql_carro = "UPDATE tbl_carros SET status = 'disponivel' WHERE cod_carro = ?";
            $stmt_carro = $this->pdo->prepare($sql_carro);
            $stmt_carro->execute([$reserva['cod_carro']]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Erro ao cancelar reserva: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica se o carro está disponível em um período específico.
     * Retorna true se disponível, false se houver conflito.
     */
    public function checkDisponibilidade($cod_carro, $data_inicio, $data_fim) {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM tbl_reservas 
                    WHERE cod_carro = :cod_carro 
                      AND status IN ('pendente', 'ativa') 
                      AND NOT (data_fim < :data_inicio OR data_inicio > :data_fim)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':cod_carro', $cod_carro, PDO::PARAM_INT);
            $stmt->bindValue(':data_inicio', $data_inicio);
            $stmt->bindValue(':data_fim', $data_fim);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] == 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar disponibilidade do carro: " . $e->getMessage());
            return false;
        }
    }
}
