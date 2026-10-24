
<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4 mb-3">Form Tambah Program Studi</h1>

        <?php
        $action = $this->url('prodi/store');

        require __DIR__ . '/_form.php';
        ?>
    </div>
</div>

<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
