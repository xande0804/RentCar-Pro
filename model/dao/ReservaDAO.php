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
            // Inicia uma transação para garantir que ambas as operações funcionem
            $this->pdo->beginTransaction();

            // 1. Insere a nova reserva na tabela de reservas
            $sql_reserva = "INSERT INTO tbl_reservas (cod_usuario, cod_carro, data_inicio, data_fim, valor_total, status) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_reserva = $this->pdo->prepare($sql_reserva);
            $stmt_reserva->execute([
                $reserva->getCodUsuario(),
                $reserva->getCodCarro(),
                $reserva->getDataInicio(),
                $reserva->getDataFim(),
                $reserva->getValorTotal(),
                'pendente' // Todas as reservas começam como 'pendente'
            ]);

            // 2. Atualiza o status do carro na tabela de carros para 'reservado'
            $sql_carro = "UPDATE tbl_carros SET status = 'reservado' WHERE cod_carro = ?";
            $stmt_carro = $this->pdo->prepare($sql_carro);
            $stmt_carro->execute([$reserva->getCodCarro()]);

            // Se as duas operações acima deram certo, confirma as alterações no banco
            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            // Se qualquer uma das operações falhar, desfaz tudo
            $this->pdo->rollBack();
            error_log("Erro ao criar reserva: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retorna todas as reservas, juntando dados do carro e do usuário.
     * Usado no painel de gerenciamento do funcionário.
     */
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

    /**
     * Retorna todas as reservas de um usuário específico.
     * Usado na tela 'Minhas Reservas' do cliente.
     */
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

            // 1. Pega o ID do carro ANTES de deletar a reserva
            $stmt_get_car = $this->pdo->prepare("SELECT cod_carro FROM tbl_reservas WHERE cod_reserva = ?");
            $stmt_get_car->execute([$reservaId]);
            $carroId = $stmt_get_car->fetchColumn();

            // 2. Deleta a reserva
            $stmt_delete = $this->pdo->prepare("DELETE FROM tbl_reservas WHERE cod_reserva = ?");
            $stmt_delete->execute([$reservaId]);

            // 3. Atualiza o status do carro para 'disponivel'
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

            // 1. Atualiza o status da reserva
            $sql_reserva = "UPDATE tbl_reservas SET status = ? WHERE cod_reserva = ?";
            $stmt_reserva = $this->pdo->prepare($sql_reserva);
            $stmt_reserva->execute([$novoStatusReserva, $reservaId]);

            // 2. Pega o ID do carro a partir da reserva
            $stmt_get_car = $this->pdo->prepare("SELECT cod_carro FROM tbl_reservas WHERE cod_reserva = ?");
            $stmt_get_car->execute([$reservaId]);
            $carroId = $stmt_get_car->fetchColumn();

            // 3. Atualiza o status do carro
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
}