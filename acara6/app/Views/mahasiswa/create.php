<?php $title = 'Tambah Mahasiswa'; require BASE_PATH . '/app/Views/layouts/header.php'; ?>
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
        <h1>Form Tambah Mahasiswa</h1>
        <?php if (!empty($flash)): ?><div class="alert <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <form action="<?= htmlspecialchars($this->url('mahasiswa/tambah')) ?>" method="post">
            <label for="nim">NIM</label><input id="nim" name="nim" required maxlength="30" value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>">
            <label for="nama">Nama Mahasiswa</label><input id="nama" name="nama" required maxlength="100" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            <label for="prodi">Program Studi</label>
            <select id="prodi" name="prodi" required>
                <option value="">-- Pilih Program Studi --</option>
                <?php foreach (['Manajemen Informatika','Teknik Informatika','Teknik Komputer'] as $program): ?>
                    <option value="<?= htmlspecialchars($program) ?>" <?= (($_POST['prodi'] ?? '') === $program) ? 'selected' : '' ?>><?= htmlspecialchars($program) ?></option>
                <?php endforeach; ?>
            </select>
            <p><button class="btn" type="submit">Simpan</button> <a class="btn secondary" href="<?= htmlspecialchars($this->url('mahasiswa')) ?>">Kembali</a></p>
        </form>
    </div>
</div>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
