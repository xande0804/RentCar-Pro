<?php
// 1. Define as variáveis para o layout
$pageTitle = "Bem-vindo à RentCar Pro";

// 2. Inclui o cabeçalho (que já define $usuarioLogado, $usuarioPerfil, etc.)
require_once __DIR__ . '/../view/layout/header.php';
?>

<div class="main-banner-image">
    <div class="banner-content">
        <h1>Alugue o Carro <br><span class="highlight">dos Seus Sonhos</span></h1>
        <p>Frota premium, preços justos e atendimento profissional.</p>
        
        <div class="banner-buttons">
            <?php if ($usuarioLogado): ?>
                <a href="view/carros/index.php" class="btn btn-primary">Ver Carros Disponíveis</a>
            <?php else: ?>
                <a href="view/carros/index.php" class="btn btn-primary">Ver Carros Disponíveis</a>
                <a href="view/auth/cadastroUsuario.php" class="btn2 btn-secondary2">Cadastre-se agora</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container" style="padding-top: 2rem; text-align: center;">

    <?php
    if (isset($_SESSION['flash_message'])) {
        $flashMessage = $_SESSION['flash_message'];
        $messageType = $flashMessage['type'] === 'success' ? 'alert-success' : 'alert-danger';
        echo "<div class='alert {$messageType}'>" . htmlspecialchars($flashMessage['message']) . "</div>";
        unset($_SESSION['flash_message']);
    }
    ?>

    <h2>Bem-vindo ao Sistema, <?php echo htmlspecialchars(ucfirst($usuarioPerfil)); ?>!</h2>
    <p>Use o menu no topo para navegar.</p>
</div>


<?php
// 4. Inclui o rodapé
require_once __DIR__ . '/../view/layout/footer.php';
?>