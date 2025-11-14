<?php
require_once __DIR__ . "/Conexao.php";

class RelatorioDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getInstance();
    }

    private function rangeParams($filtros) {
        // Período padrão: últimos 30 dias
        $hoje = (new DateTime('today'))->format('Y-m-d');
        $inicioDefault = (new DateTime('-30 days'))->format('Y-m-d');

        $inicio = !empty($filtros['inicio']) ? $filtros['inicio'] : $inicioDefault;
        $fim    = !empty($filtros['fim'])    ? $filtros['fim']    : $hoje;
        return [$inicio, $fim];
    }

    // KPIs principais
    public function kpis($filtros = []) {
        [$inicio, $fim] = $this->rangeParams($filtros);

        // total reservas no período
        // MUDANÇA: Usei BETWEEN para pegar o dia final
        $sqlTotal = "SELECT COUNT(*) AS total
                     FROM tbl_reservas
                     WHERE DATE(data_inicio) BETWEEN :ini AND :fim";
        $st1 = $this->pdo->prepare($sqlTotal);
        $st1->execute([':ini'=>$inicio, ':fim'=>$fim]);
        $totalReservas = (int)$st1->fetchColumn();

        // faturamento: soma de valor_total para reservas efetivadas
        // (Usei os status do seu banco)
        $sqlFat = "SELECT COALESCE(SUM(valor_total),0) AS fat
                   FROM tbl_reservas
                   WHERE DATE(data_inicio) BETWEEN :ini AND :fim
                     AND status IN ('ativa','concluida','aguardando_retirada')";
        $st2 = $this->pdo->prepare($sqlFat);
        $st2->execute([':ini'=>$inicio, ':fim'=>$fim]);
        $faturamento = (float)$st2->fetchColumn();

        // clientes ativos: distintos que reservaram no período
        $sqlCli = "SELECT COUNT(DISTINCT cod_usuario) AS ativos
                   FROM tbl_reservas
                   WHERE DATE(data_inicio) BETWEEN :ini AND :fim";
        $st3 = $this->pdo->prepare($sqlCli);
        $st3->execute([':ini'=>$inicio, ':fim'=>$fim]);
        $clientesAtivos = (int)$st3->fetchColumn();

        // carros alugados (reservas ativas que tocam o período)
        $sqlCar = "SELECT COUNT(DISTINCT cod_carro) AS alugados
                   FROM tbl_reservas
                   WHERE NOT (data_fim < :ini OR data_inicio > :fim)
                     AND status IN ('pendente','ativa','aguardando_retirada')";
        $st4 = $this->pdo->prepare($sqlCar);
        $st4->execute([':ini'=>$inicio, ':fim'=>$fim]);
        $carrosAlugados = (int)$st4->fetchColumn();

        return [
            'total_reservas'  => $totalReservas,
            'faturamento'     => $faturamento,
            'clientes_ativos' => $clientesAtivos,
            'carros_alugados' => $carrosAlugados
        ];
    }

    // Séries temporais DIÁRIAS (Para o gráfico combo)
    public function seriesDiarias($filtros = []) {
        [$inicio, $fim] = $this->rangeParams($filtros);

        $sqlR = "SELECT DATE(data_inicio) AS dia, COUNT(*) AS qtd
                 FROM tbl_reservas
                 WHERE DATE(data_inicio) BETWEEN :ini AND :fim
                 GROUP BY DATE(data_inicio)
                 ORDER BY DATE(data_inicio)";
        $stR = $this->pdo->prepare($sqlR);
        $stR->execute([':ini'=>$inicio, ':fim'=>$fim]);
        $reservasPorDia = $stR->fetchAll(PDO::FETCH_ASSOC);

        $sqlF = "SELECT DATE(data_inicio) AS dia, COALESCE(SUM(valor_total),0) AS total
                 FROM tbl_reservas
                 WHERE DATE(data_inicio) BETWEEN :ini AND :fim
                   AND status IN ('ativa','concluida','aguardando_retirada')
                 GROUP BY DATE(data_inicio)
                 ORDER BY DATE(data_inicio)";
        $stF = $this->pdo->prepare($sqlF);
        $stF->execute([':ini'=>$inicio, ':fim'=>$fim]);
        $fatPorDia = $stF->fetchAll(PDO::FETCH_ASSOC);

        return ['reservas' => $reservasPorDia, 'faturamento' => $fatPorDia];
    }

    // NOVO: Séries temporais MENSAIS (Para o gráfico de área)
    public function seriesMensaisFaturamento($filtros = []) {
        [$inicio, $fim] = $this->rangeParams($filtros);
        
        $sql = "SELECT DATE_FORMAT(data_inicio, '%Y-%m') AS mes, COALESCE(SUM(valor_total),0) AS total
                FROM tbl_reservas
                WHERE DATE(data_inicio) BETWEEN :ini AND :fim
                  AND status IN ('ativa','concluida','aguardando_retirada')
                GROUP BY DATE_FORMAT(data_inicio, '%Y-%m')
                ORDER BY mes ASC";
        $st = $this->pdo->prepare($sql);
        $st->execute([':ini'=>$inicio, ':fim'=>$fim]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }


    // Distribuição por categoria (pizza/donut)
    public function distribuicaoCategoria($filtros = []) {
        [$inicio, $fim] = $this->rangeParams($filtros);

        $sql = "SELECT c.categoria, COUNT(*) AS qtd
                FROM tbl_reservas r
                JOIN tbl_carros c ON c.cod_carro = r.cod_carro
                WHERE DATE(r.data_inicio) BETWEEN :ini AND :fim
                GROUP BY c.categoria
                ORDER BY qtd DESC";
        $st = $this->pdo->prepare($sql);
        $st->execute([':ini'=>$inicio, ':fim'=>$fim]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    // Taxa de ocupação por carro (radar)
    public function ocupacaoPorCarro($filtros = []) {
        [$inicio, $fim] = $this->rangeParams($filtros);
        $diasPeriodo = (new DateTime($inicio))->diff(new DateTime($fim))->days + 1;

        $sql = "SELECT c.cod_carro, CONCAT(c.marca, ' ', c.modelo) AS nome,
                       SUM(DATEDIFF(
                           LEAST(DATE(r.data_fim), :fim), 
                           GREATEST(DATE(r.data_inicio), :ini)
                       ) + 1) AS dias_ocupados
                FROM tbl_carros c
                LEFT JOIN tbl_reservas r
                  ON r.cod_carro = c.cod_carro
                 AND NOT (DATE(r.data_fim) < :ini OR DATE(r.data_inicio) > :fim)
                 AND r.status IN ('pendente','ativa','concluida','aguardando_retirada')
                GROUP BY c.cod_carro, nome
                ORDER BY dias_ocupados DESC
                LIMIT 12";
        $st = $this->pdo->prepare($sql);
        $st->execute([':ini'=>$inicio, ':fim'=>$fim]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $dias = (int)($row['dias_ocupados'] ?? 0);
            $row['ocupacao_pct'] = $diasPeriodo > 0 ? round($dias * 100 / $diasPeriodo, 1) : 0;
        }
        return $rows;
    }

    // Top 5 (clientes e carros)
    public function topClientes($filtros = []) {
        [$inicio, $fim] = $this->rangeParams($filtros);

        $sql = "SELECT u.nome, u.email, COUNT(*) AS qtd, COALESCE(SUM(r.valor_total),0) AS fat
                FROM tbl_reservas r
                JOIN tbl_usuarios u ON u.cod_usuario = r.cod_usuario
                WHERE DATE(r.data_inicio) BETWEEN :ini AND :fim
                GROUP BY u.cod_usuario, u.nome, u.email
                ORDER BY qtd DESC, fat DESC
                LIMIT 5";
        $st = $this->pdo->prepare($sql);
        $st->execute([':ini'=>$inicio, ':fim'=>$fim]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function topCarros($filtros = []) {
        [$inicio, $fim] = $this->rangeParams($filtros);

        $sql = "SELECT CONCAT(c.marca,' ',c.modelo) AS nome, c.cod_carro,
                       COUNT(*) AS qtd, COALESCE(SUM(r.valor_total),0) AS fat
                FROM tbl_reservas r
                JOIN tbl_carros c ON c.cod_carro = r.cod_carro
                WHERE DATE(r.data_inicio) BETWEEN :ini AND :fim
                GROUP BY c.cod_carro, nome
                ORDER BY qtd DESC, fat DESC
                LIMIT 5";
        $st = $this->pdo->prepare($sql);
        $st->execute([':ini'=>$inicio, ':fim'=>$fim]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    // Exportação simples (tabela plana de reservas no período)
    public function reservasTabela($filtros = []) {
        [$inicio, $fim] = $this->rangeParams($filtros);

        $sql = "SELECT r.cod_reserva, r.cod_usuario, u.nome AS cliente, r.cod_carro,
                       CONCAT(c.marca,' ',c.modelo) AS carro, r.data_inicio, r.data_fim,
                       r.status, r.valor_total
                FROM tbl_reservas r
                JOIN tbl_usuarios u ON u.cod_usuario = r.cod_usuario
                JOIN tbl_carros c   ON c.cod_carro   = r.cod_carro
                WHERE DATE(r.data_inicio) BETWEEN :ini AND :fim
                ORDER BY r.data_inicio DESC, r.cod_reserva DESC
                LIMIT 2000";
        $st = $this->pdo->prepare($sql);
        $st->execute([':ini'=>$inicio, ':fim'=>$fim]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}