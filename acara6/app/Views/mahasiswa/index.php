<?php $title = 'Data Mahasiswa'; require BASE_PATH . '/app/Views/layouts/header.php'; ?>
<div class="nav">
    <div><strong>SI AKADEMIK</strong></div>
    <div>
        <a href="<?= htmlspecialchars($this->url('dashboard')) ?>">Dashboard</a>
        <a href="<?= htmlspecialchars($this->url('mahasiswa')) ?>">Mahasiswa</a>
        <form action="<?= htmlspecialchars($this->url('logout')) ?>" method="post"><button class="btn danger" type="submit">Logout</button></form>
    </div>
</div>
<div class="wrap">
    <div class="card">
        <h1>Data Mahasiswa</h1>
        <p class="muted">Contoh data disimpan sementara di session, belum menggunakan database.</p>
        <?php if (!empty($flash)): ?><div class="alert <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <a class="btn" href="<?= htmlspecialchars($this->url('mahasiswa/tambah')) ?>">+ Tambah Mahasiswa</a>
        <table>
            <thead><tr><th>No.</th><th>NIM</th><th>Nama</th><th>Program Studi</th></tr></thead>
            <tbody>
            <?php if (empty($mahasiswa)): ?>
                <tr><td colspan="4">Belum ada data mahasiswa.</td></tr>
            <?php else: foreach ($mahasiswa as $i => $mhs): ?>
                <tr><td><?= $i + 1 ?></td><td><?= htmlspecialchars($mhs['nim']) ?></td><td><?= htmlspecialchars($mhs['nama']) ?></td><td><?= htmlspecialchars($mhs['prodi']) ?></td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
