<?php $title = 'Tidak ditemukan'; require BASE_PATH . '/app/Views/layouts/header.php'; ?>
<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4">404 - Data tidak ditemukan</h1>
        <p class="text-muted">Data yang kamu cari tidak ada atau sudah dihapus.</p>
        <a class="btn btn-secondary" href="<?= $this->e($this->url('dashboard')) ?>">Kembali ke Dashboard</a>
    </div>
</div>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
