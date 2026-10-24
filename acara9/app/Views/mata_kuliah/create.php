
<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4 mb-3">Form Tambah Mata Kuliah</h1>

        <?php
        $action = $this->url('mata-kuliah/store');

        require __DIR__ . '/_form.php';
        ?>
    </div>
</div>

<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
