<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>
<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h3">Halaman Dashboard</h1>
        <p>Selamat datang, <b><?= $this->e($namaAdmin) ?></b>. Data di bawah ini diambil langsung dari database.</p>
        <div class="row g-3 mt-1">
            <div class="col-md-4">
                <div class="p-3 bg-primary-subtle rounded">
                    <div class="fs-2 fw-bold text-primary"><?= (int) $jumlahMahasiswa ?></div>Mahasiswa
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-success-subtle rounded">
                    <div class="fs-2 fw-bold text-success"><?= (int) $jumlahProdi ?></div>Program Studi
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-warning-subtle rounded">
                    <div class="fs-2 fw-bold text-warning-emphasis"><?= (int) $jumlahMk ?></div>Mata Kuliah
                </div>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2 flex-wrap">
            <a class="btn btn-primary" href="<?= $this->e($this->url('mahasiswa')) ?>">Kelola Mahasiswa</a>
            <a class="btn btn-outline-primary" href="<?= $this->e($this->url('prodi')) ?>">Kelola Prodi</a>
            <a class="btn btn-outline-primary" href="<?= $this->e($this->url('mata-kuliah')) ?>">Kelola Mata Kuliah</a>
        </div>
    </div>
</div>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
