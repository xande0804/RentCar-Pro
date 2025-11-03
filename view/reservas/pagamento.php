<?php
$acessoApenasLogado = true;
$pageTitle = "Pagamento da Reserva";
require_once __DIR__ . '/../layout/header.php';

require_once __DIR__ . '/../../model/dao/ReservaDAO.php';
require_once __DIR__ . '/../../model/dao/CarroDAO.php';
require_once __DIR__ . '/../../model/dao/UsuarioDAO.php';

$cod_reserva = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$idUsuarioLogado = $_SESSION['usuario']['id'] ?? null;

if (!$cod_reserva) {
    header("Location: " . BASE_URL . "/view/reservas/minhasReservas.php?erro=" . urlencode("Reserva inválida."));
    exit;
}

$reservaDAO = new ReservaDAO();
$reserva = $reservaDAO->findById($cod_reserva);

// Validação de segurança
if (!$reserva || $reserva['cod_usuario'] != $idUsuarioLogado) {
    header("Location: " . BASE_URL . "/view/reservas/minhasReservas.php?erro=" . urlencode("Reserva não encontrada."));
    exit;
}
// Se a reserva não estiver pendente (já foi paga, cancelada, etc.), não permite pagar de novo
if ($reserva['status'] !== 'pendente') {
     header("Location: " . BASE_URL . "/view/reservas/minhasReservas.php?erro=" . urlencode("Esta reserva não está mais aguardando pagamento."));
    exit;
}

$carroDAO = new CarroDAO();
$carro = $carroDAO->findById($reserva['cod_carro']);

$usuarioDAO = new UsuarioDAO();
$usuario = $usuarioDAO->findById($idUsuarioLogado);

// Cálculo dos dias (apenas para exibição)
$totalDias = (new DateTime($reserva['data_inicio']))->diff(new DateTime($reserva['data_fim']))->days;
?>

<div class="container-xl mt-4 mb-5">
    
    <?php if (isset($_GET['erro'])): ?>
        <div class='alert alert-danger col-lg-8 mx-auto'><?= htmlspecialchars($_GET['erro']) ?></div>
    <?php endif; ?>

    <form action="controller/ReservaControl.php" method="POST" id="form-pagamento">
        <input type="hidden" name="acao" value="processar_pagamento">
        <input type="hidden" name="cod_reserva" value="<?= $reserva['cod_reserva'] ?>">

        <div class="row g-5">

            <div class="col-lg-8">

                <div class="payment-header">
                    <h1>Processamento de Pagamento</h1>
                    <p>Finalize sua reserva com segurança.</p>
                </div>
                
                <div class="badge-transacao-segura">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/></svg>
                    <span>Transação Segura - SSL Criptografado</span>
                </div>

                <div class="payment-card">
                    <div class="card-header">
                        <h3>Resumo da Locação</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="flex-shrink-0">
                                <img src="https://placehold.co/150x100/e2e8f0/cccccc?text=<?= urlencode($carro['marca']) ?>" class="img-fluid rounded" style="width: 150px;" alt="carro">
                            </div>
                            <div class="flex-grow-1 ms-4">
                                <h5 class="mb-1"><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?></h5>
                                <small class="text-muted">
                                    <?= htmlspecialchars($carro['categoria'] ?? 'N/A') ?> &bull; 
                                    <?= htmlspecialchars(ucfirst($carro['cambio'])) ?>
                                </small>
                            </div>
                        </div>

                        <div class="summary-item">
                            <span class="label">Período:</span>
                            <span class="value"><?= date('d/m/Y', strtotime($reserva['data_inicio'])) ?> a <?= date('d/m/Y', strtotime($reserva['data_fim'])) ?> (<?= $totalDias ?> dias)</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Diária (<?= $totalDias ?> dias):</span>
                            <span class="value">R$ <?= htmlspecialchars(number_format($reserva['valor_total'], 2, ',', '.')) ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Taxas e Impostos:</span>
                            <span class="value">R$ 0,00</span>
                        </div>
                    </div>
                </div>

                <div class="payment-card">
                    <div class="card-header">
                        <h3>Método de Pagamento</h3>
                    </div>
                    <div class="card-body">
                        
                        <div class="payment-method-box selected" data-method="cartao">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" id="metodo_cartao" value="cartao" checked>
                                <label class="form-check-label" for="metodo_cartao">
                                    Cartão de Crédito
                                    <small>Visa, Mastercard, Elo</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="payment-method-box" data-method="pix">
                             <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" id="metodo_pix" value="pix">
                                <label class="form-check-label" for="metodo_pix">
                                    PIX
                                    <small>Pagamento instantâneo</small>
                                </label>
                            </div>
                        </div>

                        <div id="campos_cartao" class="mt-4">
                            <div class="mb-3">
                                <label for="num_cartao" class="form-label">Número do Cartão</label>
                                <input type="text" class="form-control" id="num_cartao" placeholder="0000 0000 0000 0000">
                            </div>
                            <div class="row">
                                <div class="col-md-7 mb-3">
                                    <label for="validade_cartao" class="form-label">Validade</label>
                                    <input type="text" class="form-control" id="validade_cartao" placeholder="MM/AA">
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label for="cvv_cartao" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv_cartao" placeholder="000">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="nome_cartao" class="form-label">Nome no Cartão</label>
                                <input type="text" class="form-control" id="nome_cartao" placeholder="Nome como impresso no cartão">
                            </div>
                        </div>
                        
                        <div id="campos_pix" class="mt-4 text-center" style="display: none;">
                            <p>Escaneie o QR Code abaixo para pagar via PIX:</p>
                            <img src="https://placehold.co/250x250/e2e8f0/cccccc?text=QR+Code+PIX" alt="QR Code PIX" class="img-fluid rounded border">
                            <p class="mt-3 text-muted">Após o pagamento, clique no botão "Processar Pagamento" no resumo ao lado.</p>
                        </div>

                    </div>
                </div>

            </div> <div class="col-lg-4">

                <div class="reservation-card">
                    <div class="card-header">
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0;">
                            Resumo do Pagamento
                        </h3>
                    </div>
                    <div class="card-body">
                        
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>R$ <?= htmlspecialchars(number_format($reserva['valor_total'], 2, ',', '.')) ?></span>
                        </div>
                         <div class="summary-row">
                            <span>Taxas:</span>
                            <span>R$ 0,00</span>
                        </div>

                        <div class="summary-total">
                            <span>Total Final:</span>
                            <span>R$ <?= htmlspecialchars(number_format($reserva['valor_total'], 2, ',', '.')) ?></span>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-reservar" id="btn-processar-pagamento">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                <span class="text-btn">Processar Pagamento</span>
                            </button>
                        </div>
                        
                        <p class="payment-terms">
                            Ao confirmar, você aceita nossos 
                            <a href="#">Termos de Uso</a> e 
                            <a href="#">Política de Privacidade</a>.
                        </p>

                        <div class="security-section">
                            <h4>Segurança</h4>
                            <div class="security-item">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12 12 0 003 20.944a11.955 11.955 0 019-4.611V14a1 1 0 002 0v-1.001a11.955 11.955 0 013.618 4.016 12.01 12.01 0 00-4-10.873z" /></svg>
                                <span>SSL 256-bit</span>
                            </div>
                            <div class="security-item">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12 12 0 003 20.944a11.955 11.955 0 019-4.611V14a1 1 0 002 0v-1.001a11.955 11.955 0 013.618 4.016 12.01 12.01 0 00-4-10.873z" /></svg>
                                <span>Proteção Antifraude</span>
                            </div>
                            <p class="security-footer">
                                Seus dados são protegidos conforme a LGPD.
                            </p>
                        </div>

                    </div>
                </div> </div> </div> </form>
</div> <?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-pagamento');
    const submitButton = document.getElementById('btn-processar-pagamento');
    const btnSpinner = submitButton.querySelector('.spinner-border');
    const btnText = submitButton.querySelector('.text-btn');
    
    const metodoRadios = document.querySelectorAll('input[name="metodo_pagamento"]');
    const methodBoxes = document.querySelectorAll('.payment-method-box');
    
    const camposCartao = document.getElementById('campos_cartao');
    const camposPix = document.getElementById('campos_pix');

    // --- CAMPOS DE CARTÃO ---
    const inputNumCartao = document.getElementById('num_cartao');
    const inputValidade = document.getElementById('validade_cartao');
    const inputCVV = document.getElementById('cvv_cartao');

    // MÁSCARA NÚMERO DO CARTÃO (0000 0000 0000 0000)
    inputNumCartao.addEventListener('input', function(e) {
        let valor = e.target.value.replace(/\D/g, '');
        valor = valor.slice(0, 16);
        let formatado = valor.replace(/(\d{4})(?=\d)/g, '$1 ');
        e.target.value = formatado;
    });

    // MÁSCARA + VALIDAÇÃO DE MÊS (MM/AA)
    inputValidade.addEventListener('input', function(e) {
        let valor = e.target.value.replace(/\D/g, '');
        valor = valor.slice(0, 4); // máximo MM + AA

        if (valor.length >= 3) {
            let mes = parseInt(valor.slice(0, 2));
            let ano = valor.slice(2);

            // Corrige mês inválido
            if (mes < 1) mes = 1;
            if (mes > 12) mes = 12;

            // Formata com 2 dígitos e adiciona "/"
            const mesFormatado = mes.toString().padStart(2, '0');
            e.target.value = mesFormatado + '/' + ano;
        } else {
            e.target.value = valor;
        }
    });

    // MÁSCARA CVV (3 ou 4 dígitos)
    inputCVV.addEventListener('input', function(e) {
        let valor = e.target.value.replace(/\D/g, '');
        e.target.value = valor.slice(0, 4);
    });

    // 1. Troca método de pagamento (Cartão/PIX)
    metodoRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const selectedMethod = this.value;

            methodBoxes.forEach(box => {
                if (box.dataset.method === selectedMethod) {
                    box.classList.add('selected');
                } else {
                    box.classList.remove('selected');
                }
            });

            if (selectedMethod === 'cartao') {
                camposCartao.style.display = 'block';
                camposPix.style.display = 'none';
            } else if (selectedMethod === 'pix') {
                camposCartao.style.display = 'none';
                camposPix.style.display = 'block';
            }
        });
    });

    // 2. Simulação de Pagamento
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const metodo = document.querySelector('input[name="metodo_pagamento"]:checked').value;
        let tempoSimulacao = 2500;
        let msgProcessamento = "Processando...";

        if (metodo === 'pix') {
            tempoSimulacao = 1500;
            msgProcessamento = "Validando pagamento PIX...";
        }

        submitButton.disabled = true;
        btnSpinner.style.display = 'inline-block';
        btnText.textContent = msgProcessamento;

        setTimeout(function() {
            btnSpinner.style.display = 'none';
            btnText.textContent = 'Pagamento Aprovado!';
            submitButton.classList.remove('btn-primary');
            submitButton.classList.add('btn-success');

            setTimeout(function() {
                form.submit();
            }, 1000);

        }, tempoSimulacao);
    });
});
</script>
