<?php

$pageTitle = "Finalizar Reserva";
require_once __DIR__ . '/../layout/header.php';

$cod_carro = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$cod_usuario = filter_input(INPUT_GET, 'cod_usuario', FILTER_VALIDATE_INT);

// Apenas identifica o usuário (não é usado para submissão final)
$usuarioParaReservaId = $cod_usuario ?: ($_SESSION['usuario']['id'] ?? null);

if (!$cod_carro) {
    header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Carro inválido."));
    exit;
}

require_once __DIR__ . '/../../model/dao/CarroDAO.php';
$carroDAO = new CarroDAO();
$carro = $carroDAO->findById($cod_carro);

if (!$carro || $carro['status'] !== 'disponivel') {
    header("Location: " . BASE_URL . "/view/carros/index.php?erro=" . urlencode("Este carro não está mais disponível."));
    exit;
}

$img = $carro['imagem_url'] ?? '';
$imagemSrc = filter_var($img, FILTER_VALIDATE_URL)
    ? $img
    : (!empty($img)
        ? BASE_URL . '/' . ltrim($img, '/')
        : "https://placehold.co/800x500/e2e8f0/cccccc?text=" . urlencode($carro['marca'])
    );

// Dados para os ícones (simulando)
$lugares = $carro['categoria'] === 'Hatch' ? 4 : 5;
$portas = 4;
?>

<div class="step-header-wrapper">
  <nav class="step-breadcrumb">
    <a href="<?= BASE_URL ?>/view/carros/index.php">Catálogo</a>
    <span class="sep">›</span>
    <span class="active"><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?></span>
  </nav>
</div>

<div class="container-xl mt-5 mb-5">
    
    <?php if (isset($_GET['erro'])): ?>
        <div class='alert alert-danger col-lg-8 mx-auto'><?= htmlspecialchars($_GET['erro']) ?></div>
    <?php endif; ?>

    <div class="row g-5">

        <div class="col-lg-7">

            <div class="detail-gallery">
            <div class="gallery-main-image mb-4">
                <img src="<?= htmlspecialchars($imagemSrc) ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($carro['modelo']) ?>">
            </div>

                <?php if (isset($carro['fotos']) && count($carro['fotos']) > 1): ?>
                    <div class="gallery-thumbnails">
                        </div>
                <?php endif; ?>
            </div>

            <div class="detail-content">
                <div class="detail-header">
                    <h1><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?></h1>
                    <div class="specs-quick">
                        <?= htmlspecialchars($carro['categoria'] ?? 'N/A') ?> &bull;
                        <?= htmlspecialchars(ucfirst($carro['cambio'])) ?> &bull;
                        <?= htmlspecialchars(ucfirst($carro['combustivel'])) ?>
                    </div>

                    <div class="detail-price-main">
                        R$ <?= number_format($carro['preco_diaria'], 2, ',', '.') ?> 
                        <small>/ dia</small>
                    </div>
                </div>


                <hr class="my-4">

                <h3 class="section-title">Visão Geral</h3>
                <div class="spec-icons-grid">
                    
                    <div class="spec-icon-item">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <div class="spec-name"><?= $lugares ?> Lugares</div>
                    </div>

                    <div class="spec-icon-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <rect x="6" y="3" width="12" height="18" rx="1" ry="1"></rect>
                          <circle cx="14" cy="12" r="1.25"></circle>
                        </svg>
                        <div class="spec-name"><?= $portas ?> Portas</div>
                    </div>

                    <div class="spec-icon-item">
                        <?php if ($carro['ar_condicionado']): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        <?php else: ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        <?php endif; ?>
                        <div class="spec-name"><?= $carro['ar_condicionado'] ? 'Ar Condicionado' : 'Sem Ar' ?></div>
                    </div>

                    <div class="spec-icon-item">
                    <svg class="w-8 h-8 mx-auto mb-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" data-component-id="2-0-0-1-1-3-0" data-component-path="/pages/vehicle_details.html" data-component-name="svg" data-component-file="vehicle_details.html" data-component-content="%7B%22elementName%22%3A%22svg%22%2C%22className%22%3A%22w-8%20h-8%20mx-auto%20mb-2%20text-primary%22%7D">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        <div class="spec-name"><?= htmlspecialchars(ucfirst($carro['cambio'])) ?></div>
                    </div>
                </div>

                <h3 class="section-title">Especificações Técnicas</h3>
                <div class="tech-specs">
                    <div class="tech-spec-row">
                        <span class="spec-label">Modelo:</span>
                        <span class="spec-value"><?= htmlspecialchars($carro['modelo']) ?></span>
                    </div>
                    <div class="tech-spec-row">
                        <span class="spec-label">Ano:</span>
                        <span class="spec-value"><?= htmlspecialchars($carro['ano']) ?></span>
                    </div>
                     <div class="tech-spec-row">
                        <span class="spec-label">Cor:</span>
                        <span class="spec-value"><?= htmlspecialchars($carro['cor'] ?? 'N/A') ?></span>
                    </div>
                    <div class="tech-spec-row">
                        <span class="spec-label">Transmissão:</span>
                        <span class="spec-value"><?= htmlspecialchars(ucfirst($carro['cambio'])) ?></span>
                    </div>
                    <div class="tech-spec-row">
                        <span class="spec-label">Combustível:</span>
                        <span class="spec-value"><?= htmlspecialchars(ucfirst($carro['combustivel'])) ?></span>
                    </div>
                    <div class="tech-spec-row">
                        <span class="spec-label">Categoria:</span>
                        <span class="spec-value"><?= htmlspecialchars($carro['categoria'] ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>

        </div> 
        <div class="col-lg-4">

            <div class="reservation-card">

                <div class="card-header">
                    <span class="price">R$ <?= number_format($carro['preco_diaria'], 2, ',', '.') ?></span>
                    <span class="per-day">/ dia</span>
                </div>

                <div class="card-body">
                    
                    <form action="<?= BASE_URL ?>/controller/ReservaControl.php" method="POST" id="form-reserva">
                        <input type="hidden" name="acao" value="salvar_pendente">
                        <input type="hidden" name="cod_carro" value="<?= $carro['cod_carro'] ?>">
                        <input type="hidden" id="preco_diaria" value="<?= $carro['preco_diaria'] ?>">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="data_inicio" class="form-label">Data de Retirada</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="data_fim" class="form-label">Data de Devolução</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim" required min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <h4 class="form-section-title">Escolha o Plano de KM</h4>
                        <div class="mb-3 planos-km">
                          <label class="form-check w-100 mb-2 align-items-center d-flex gap-2">
                            <input class="form-check-input mt-0" type="radio" name="plano_tipo" id="plano_ilimitado" value="ilimitado" checked>
                            <span class="form-check-label">KM Ilimitado <small class="text-muted">(preço +25%)</small></span>
                          </label>

                          <label class="form-check w-100 d-flex align-items-center gap-2">
                            <input class="form-check-input mt-0" type="radio" name="plano_tipo" id="plano_limitado" value="limitado">
                            <span class="form-check-label">KM Limitado</span>

                            <!-- “chip” do select; fica cinza quando desabilitado -->
                            <div id="km_wrap" class="km-select-wrap ms-auto">
                              <select class="form-select form-select-sm km-select" name="km_limite" id="km_limite" disabled>
                                <option value="100">100 km/dia</option>
                                <option value="200">200 km/dia</option>
                                <option value="300">300 km/dia</option>
                              </select>
                            </div>
                          </label>
                        </div>

                        <h4 class="form-section-title">Extras Opcionais</h4>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="extras[]" value="cadeirinha" id="ex_cadeirinha">
                            <label class="form-check-label" for="ex_cadeirinha">Cadeirinha Infantil (+R$ 12/dia)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="extras[]" value="danos" id="ex_danos">
                            <label class="form-check-label" for="ex_danos">Cobertura de Danos (+R$ 18/dia)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="extras[]" value="roubo" id="ex_roubo">
                            <label class="form-check-label" for="ex_roubo">Cobertura contra Roubo (+R$ 15/dia)</label>
                        </div>

                        <div id="resumo-reserva" style="display:none;">
                            <h5>Resumo da Reserva</h5>
                            <p><strong>Plano aplicado:</strong> <span id="plano-aplicado">Nenhum</span></p>
                            <p><strong>Extras:</strong> <span id="extras-aplicados">Nenhum</span></p>
                            <p><strong>Total de dias:</strong> <span id="total-dias">0</span></p>
                            <p class="h4"><strong>Total Estimado:</strong> R$ <span id="valor-total">0,00</span></p>
                        </div>

                        <div class="mt-4 text-center">
                          <button type="submit" class="btn btn-primary btn-lg btn-reservar">Reservar Agora</button>
                        </div>
                        <div class="text-center mt-2">
                          <a href="<?= BASE_URL ?>/view/carros/index.php" class="btn btn-link btn-sm text-secondary">Cancelar</a>
                        </div>
                    </form>

                </div> 
            </div> 
        </div> 
    </div> 
</div> 

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
// ----- parâmetros de cálculo -----
const planoMultiplicador = {
    ilimitado: 1.25,
    limitado: {
        100: 1.00,
        200: 1.10,
        300: 1.15
    }
};
const extrasValores = {
    cadeirinha: 12,
    seguro_completo: 35,
    danos: 18,
    roubo: 15
};

// ----- elementos -----
const dataInicio = document.getElementById('data_inicio');
const dataFim = document.getElementById('data_fim');
const precoBase = parseFloat(document.getElementById('preco_diaria').value);
const planoIlimitado = document.getElementById('plano_ilimitado');
const planoLimitado = document.getElementById('plano_limitado');
const kmSelect = document.getElementById('km_limite');
const resumoBox = document.getElementById('resumo-reserva');
const spanDias = document.getElementById('total-dias');
const spanPlano = document.getElementById('plano-aplicado');
const spanExtras = document.getElementById('extras-aplicados');
const spanTotal = document.getElementById('valor-total');
const kmWrap = document.getElementById('km_wrap');

// habilita o select de km quando escolher plano limitado
function syncKm() {
    const on = planoLimitado.checked;
    kmSelect.disabled = !on;
    kmWrap.classList.toggle('disabled', !on);
}

planoIlimitado.addEventListener('change', syncKm);
planoLimitado.addEventListener('change', syncKm);

syncKm();

// cálculo
function calcularTotal() {
    const ini = new Date(dataInicio.value);
    const fim = new Date(dataFim.value);
    if (!dataInicio.value || !dataFim.value || fim <= ini) {
        resumoBox.style.display = 'none';
        return;
    }
    const diffDias = Math.ceil((fim - ini) / (1000 * 60 * 60 * 24));
    let multiplicador = 1;

    if (planoIlimitado.checked) {
        multiplicador = planoMultiplicador.ilimitado;
        spanPlano.textContent = 'KM Ilimitado';
    } else {
        const km = kmSelect.value;
        multiplicador = planoMultiplicador.limitado[km] || 1;
        spanPlano.textContent = `${km} km/dia`;
    }

    // extras
    const extrasSelecionados = Array.from(document.querySelectorAll('input[name="extras[]"]:checked')).map(e => e.value);
    let extrasTotalDia = 0;
    extrasSelecionados.forEach(ex => extrasTotalDia += extrasValores[ex] || 0);
    spanExtras.textContent = extrasSelecionados.length ? extrasSelecionados.join(', ') : 'Nenhum';

    const total = (diffDias * (precoBase * multiplicador + extrasTotalDia));
    resumoBox.style.display = 'block';
    spanDias.textContent = diffDias;
    spanTotal.textContent = total.toLocaleString('pt-BR', {minimumFractionDigits: 2});
}

// eventos
[dataInicio, dataFim, kmSelect, planoIlimitado, planoLimitado, ...document.querySelectorAll('input[name="extras[]"]')]
    .forEach(el => el.addEventListener('input', calcularTotal));
</script>