<?php $title = 'Dashboard'; require BASE_PATH . '/app/Views/layouts/header.php'; ?>
<div class="nav">
    <div><strong>SI AKADEMIK</strong></div>
    <div>
        <a href="<?= htmlspecialchars($this->url('dashboard')) ?>">Dashboard</a>
        <a href="<?= htmlspecialchars($this->url('mahasiswa')) ?>">Mahasiswa</a>
        <form action="<?= htmlspecialchars($this->url('logout')) ?>" method="post"><button class="btn danger" type="submit">Logout</button></form>
    </div>
</div>
<div class="wrap">
    <?php if (!empty($flash)): ?><div class="alert <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
    <div class="card">
        <h1>Halaman Dashboard</h1>
        <p>Selamat datang, <b><?= htmlspecialchars($namaAdmin) ?></b>.</p>
        <p class="muted">Selamat datang di panel utama sistem akademik.</p>
        <div class="grid">
            <div class="stat"><strong><?= (int)$jumlahMahasiswa ?></strong>Data mahasiswa</div>
            <div class="stat"><strong>1</strong>Administrator aktif</div>
        </div>
        <p style="margin-top:22px"><a class="btn" href="<?= htmlspecialchars($this->url('mahasiswa')) ?>">Kelola Mahasiswa</a></p>
    </div>
</div>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
