
<form action="<?= $this->e($action) ?>" method="post" novalidate>
    <div class="mb-3">
        <label for="kode_prodi" class="form-label">Kode Prodi</label>
        <input
            id="kode_prodi"
            name="kode_prodi"
            maxlength="10"
            class="form-control <?= isset($errors['kode_prodi']) ? 'is-invalid' : '' ?>"
            value="<?= $this->e($old['kode_prodi'] ?? '') ?>"
            required
        >
        <div class="invalid-feedback">
            <?= $this->e($errors['kode_prodi'] ?? '') ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="nama_prodi" class="form-label">Nama Program Studi</label>
        <input
            id="nama_prodi"
            name="nama_prodi"
            maxlength="100"
            class="form-control <?= isset($errors['nama_prodi']) ? 'is-invalid' : '' ?>"
            value="<?= $this->e($old['nama_prodi'] ?? '') ?>"
            required
        >
        <div class="invalid-feedback">
            <?= $this->e($errors['nama_prodi'] ?? '') ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="jenjang" class="form-label">Jenjang Pendidikan</label>
        <select
            id="jenjang"
            name="jenjang"
            class="form-select <?= isset($errors['jenjang']) ? 'is-invalid' : '' ?>"
            required
        >
            <option value="">-- Pilih Jenjang --</option>

            <?php foreach (['D3', 'D4', 'S1', 'S2', 'S3'] as $jenjang): ?>
                <option
                    value="<?= $this->e($jenjang) ?>"
                    <?= ($old['jenjang'] ?? '') === $jenjang ? 'selected' : '' ?>
                >
                    <?= $this->e($jenjang) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="invalid-feedback">
            <?= $this->e($errors['jenjang'] ?? '') ?>
        </div>
    </div>

    <button class="btn btn-primary" type="submit">Simpan</button>

    <a
        class="btn btn-secondary"
        href="<?= $this->e($this->url('prodi')) ?>"
    >
        Batal
    </a>
</form>
