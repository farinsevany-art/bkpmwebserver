
<form action="<?= $this->e($action) ?>" method="post" novalidate>
    <div class="mb-3">
        <label for="nim" class="form-label">NIM</label>
        <input
            id="nim"
            name="nim"
            maxlength="20"
            class="form-control <?= isset($errors['nim']) ? 'is-invalid' : '' ?>"
            value="<?= $this->e($old['nim'] ?? '') ?>"
        >
        <div class="invalid-feedback">
            <?= $this->e($errors['nim'] ?? '') ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="nama" class="form-label">Nama Mahasiswa</label>
        <input
            id="nama"
            name="nama"
            maxlength="100"
            class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>"
            value="<?= $this->e($old['nama'] ?? '') ?>"
        >
        <div class="invalid-feedback">
            <?= $this->e($errors['nama'] ?? '') ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input
            id="email"
            name="email"
            type="email"
            maxlength="100"
            class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
            value="<?= $this->e($old['email'] ?? '') ?>"
        >
        <div class="invalid-feedback">
            <?= $this->e($errors['email'] ?? '') ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="id_prodi" class="form-label">Program Studi</label>
        <select
            id="id_prodi"
            name="id_prodi"
            class="form-select <?= isset($errors['id_prodi']) ? 'is-invalid' : '' ?>"
        >
            <option value="">-- Pilih Program Studi --</option>

            <?php foreach ($prodiList as $p): ?>
                <option
                    value="<?= (int) $p['id_prodi'] ?>"
                    <?= (string) ($old['id_prodi'] ?? '') === (string) $p['id_prodi'] ? 'selected' : '' ?>
                >
                    <?= $this->e($p['kode_prodi']) ?>
                    - <?= $this->e($p['nama_prodi']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">
            <?= $this->e($errors['id_prodi'] ?? '') ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
        <select
            id="jenis_kelamin"
            name="jenis_kelamin"
            class="form-select <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>"
        >
            <option value="">-- Pilih Jenis Kelamin --</option>
            <option value="L" <?= ($old['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="P" <?= ($old['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
        </select>
        <div class="invalid-feedback">
            <?= $this->e($errors['jenis_kelamin'] ?? '') ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="alamat" class="form-label">Alamat</label>
        <textarea
            id="alamat"
            name="alamat"
            class="form-control <?= isset($errors['alamat']) ? 'is-invalid' : '' ?>"
            rows="3"
        ><?= $this->e($old['alamat'] ?? '') ?></textarea>
        <div class="invalid-feedback">
            <?= $this->e($errors['alamat'] ?? '') ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="angkatan" class="form-label">Angkatan</label>
        <input
            id="angkatan"
            name="angkatan"
            type="number"
            min="1901"
            max="2155"
            class="form-control <?= isset($errors['angkatan']) ? 'is-invalid' : '' ?>"
            value="<?= $this->e($old['angkatan'] ?? '') ?>"
        >
        <div class="invalid-feedback">
            <?= $this->e($errors['angkatan'] ?? '') ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status Mahasiswa</label>
        <select
            id="status"
            name="status"
            class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>"
        >
            <option value="">-- Pilih Status --</option>
            <option value="aktif" <?= ($old['status'] ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
            <option value="cuti" <?= ($old['status'] ?? '') === 'cuti' ? 'selected' : '' ?>>Cuti</option>
            <option value="lulus" <?= ($old['status'] ?? '') === 'lulus' ? 'selected' : '' ?>>Lulus</option>
        </select>
        <div class="invalid-feedback">
            <?= $this->e($errors['status'] ?? '') ?>
        </div>
    </div>

    <button class="btn btn-primary" type="submit">Simpan</button>
    <a class="btn btn-secondary" href="<?= $this->e($this->url('mahasiswa')) ?>">Batal</a>
</form>
