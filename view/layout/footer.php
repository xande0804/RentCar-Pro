</main> 
<div class="footer">
    <p>© <?= date('Y') ?> RentCar. Todos os direitos reservados.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($jsFiles) && is_array($jsFiles)): ?>
    <?php foreach ($jsFiles as $jsFile): ?>
        <script src="assets/js/<?= htmlspecialchars($jsFile) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<script src="assets/js/management.js"></script>
<script src="assets/js/form-feedback.js"></script>
<script src="assets/js/theme-toggle.js"></script>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$perfil = $_SESSION['usuario']['perfil'] ?? 'visitante';
$clientes = $clientes ?? [];
?>

<?php if (in_array($perfil, ['admin','gerente','funcionario'])): ?>
<script>
  window.perfilUsuarioLogado = <?= json_encode($perfil) ?>;
  window.listaDeClientes = <?= json_encode($clientes, JSON_UNESCAPED_UNICODE) ?>;
</script>
<?php endif; ?>

</body>
</html>
