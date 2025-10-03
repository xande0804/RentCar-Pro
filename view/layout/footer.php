</main> 
<div class="footer">
    <p>© <?= date('Y') ?> RentCar. Todos os direitos reservados.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($jsFiles) && is_array($jsFiles)): ?>
    <?php foreach ($jsFiles as $jsFile): ?>
        <script src="/assets/js/<?= htmlspecialchars($jsFile) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>