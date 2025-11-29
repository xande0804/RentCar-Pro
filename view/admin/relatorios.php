<?php
$perfisPermitidos = ['admin','gerente','funcionario'];
$pageTitle = "Relatórios e Métricas";
require_once __DIR__ . '/../layout/header.php';

if (!in_array($usuarioPerfil, $perfisPermitidos)) {
    header("Location: " . BASE_URL . "/public/index.php?erro=" . urlencode("Acesso negado!"));
    exit;
}

// Datas padrão (últimos 30 dias)
$hoje = (new DateTime('today'))->format('Y-m-d');
$inicioDefault = (new DateTime('-30 days'))->format('Y-m-d');
?>

<link rel="preconnect" href="https://cdn.jsdelivr.net" />
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<div class="content-reports">
    <div class="table-header">
        <h2>Relatórios e Métricas</h2>
        <p>Visão geral de reservas, faturamento e uso da frota.</p>
    </div>

    <div class="d-flex flex-wrap gap-2 align-items-end mb-3">
        <div>
            <label class="form-label mb-1">Início</label>
            <input type="date" id="filtro-inicio" class="form-control" value="<?= htmlspecialchars($inicioDefault) ?>">
        </div>
        <div>
            <label class="form-label mb-1">Fim</label>
            <input type="date" id="filtro-fim" class="form-control" value="<?= htmlspecialchars($hoje) ?>">
        </div>
        <button id="btn-aplicar" class="btn btn-primary">Aplicar</button>
        <button id="btn-ultimos7" class="btn btn-outline-secondary">Últimos 7</button>
        <button id="btn-ultimos30" class="btn btn-outline-secondary">Últimos 30</button>
        <button id="btn-todo" class="btn btn-outline-secondary">Todo período</button>
    </div>


  <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
        <div class="kpi-card skeleton">
            <div class="kpi-title">Reservas</div>
            <div class="kpi-value" id="kpi-reservas">0</div>
        </div>
        </div>
        <div class="col-6 col-lg-3">
        <div class="kpi-card skeleton">
            <div class="kpi-title">Faturamento</div>
            <div class="kpi-value" id="kpi-faturamento">R$ 0,00</div>
        </div>
        </div>
        <div class="col-6 col-lg-3">
        <div class="kpi-card skeleton">
            <div class="kpi-title">Clientes ativos</div>
            <div class="kpi-value" id="kpi-clientes">0</div>
        </div>
        </div>
        <div class="col-6 col-lg-3">
        <div class="kpi-card skeleton">
            <div class="kpi-title">Carros alugados</div>
            <div class="kpi-value" id="kpi-carros">0</div>
        </div>
        </div>
    </div>

  <div class="row g-3">
        <div class="col-12 col-lg-8">
        <div class="chart-card skeleton">
            <div class="chart-title">Reservas por dia (Barras) & Faturamento por dia (Linha)</div>
            <canvas id="chart-reservas-faturamento" height="120"></canvas>
        </div>
        </div>
        <div class="col-12 col-lg-4">
        <div class="chart-card skeleton">
            <div class="chart-title">Distribuição por Categoria (Donut)</div>
            <canvas id="chart-categorias" height="120"></canvas>
        </div>
        </div>
        <div class="col-12 col-lg-6">
        <div class="chart-card skeleton">
            <div class="chart-title">Ocupação por Carro (Barras)</div>
            <canvas id="chart-ocupacao" height="120"></canvas>
        </div>
        </div>
        <div class="col-12 col-lg-6">
        <div class="chart-card skeleton">
            <div class="chart-title">Tendência de Faturamento por Mês (Área)</div>
            <canvas id="chart-area-fat" height="120"></canvas>
        </div>
        </div>
    </div>

  <div class="row g-3 mt-2">
        <div class="col-12 col-lg-6">
            <div class="chart-card skeleton">
                <div class="chart-title">Top 5 Clientes</div>
                <div id="top-clientes" class="rank-list"></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="chart-card skeleton">
                <div class="chart-title">Top 5 Carros</div>
                <div id="top-carros" class="rank-list"></div>
            </div>
        </div>
    </div>
</div>

<script>
  window.BASE_URL = <?= json_encode(BASE_URL) ?>;
  window.REL_DEFAULT_INICIO = <?= json_encode($inicioDefault) ?>;
  window.REL_DEFAULT_FIM    = <?= json_encode($hoje) ?>;
</script>
<script src="assets/js/relatorios.js"></script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>