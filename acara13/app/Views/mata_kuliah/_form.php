
<form action="<?= $this->e($action) ?>" method="post" novalidate>
    <div class="mb-3">
        <label for="kode_mk" class="form-label">Kode Mata Kuliah</label>
        <input
            id="kode_mk"
            name="kode_mk"
            maxlength="20"
            class="form-control <?= isset($errors['kode_mk']) ? 'is-invalid' : '' ?>"
            value="<?= $this->e($old['kode_mk'] ?? '') ?>"
            required
        >
        <div class="invalid-feedback">
            <?= $this->e($errors['kode_mk'] ?? '') ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="nama_mk" class="form-label">Nama Mata Kuliah</label>
        <input
            id="nama_mk"
            name="nama_mk"
            maxlength="100"
            class="form-control <?= isset($errors['nama_mk']) ? 'is-invalid' : '' ?>"
            value="<?= $this->e($old['nama_mk'] ?? '') ?>"
            required
        >
        <div class="invalid-feedback">
            <?= $this->e($errors['nama_mk'] ?? '') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="sks" class="form-label">SKS</label>
            <input
                id="sks"
                name="sks"
                type="number"
                min="1"
                max="6"
                class="form-control <?= isset($errors['sks']) ? 'is-invalid' : '' ?>"
                value="<?= $this->e($old['sks'] ?? '') ?>"
                required
            >
            <div class="invalid-feedback">
                <?= $this->e($errors['sks'] ?? '') ?>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="semester" class="form-label">Semester</label>
            <input
                id="semester"
                name="semester"
                type="number"
                min="1"
                max="8"
                class="form-control <?= isset($errors['semester']) ? 'is-invalid' : '' ?>"
                value="<?= $this->e($old['semester'] ?? '') ?>"
                required
            >
            <div class="invalid-feedback">
                <?= $this->e($errors['semester'] ?? '') ?>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="id_prodi" class="form-label">Program Studi</label>
        <select
            id="id_prodi"
            name="id_prodi"
            class="form-select <?= isset($errors['id_prodi']) ? 'is-invalid' : '' ?>"
            required
        >
            <option value="">-- Pilih Program Studi --</option>

            <?php foreach ($prodiList as $p): ?>
                <option
                    value="<?= (int) $p['id_prodi'] ?>"
                    <?= (string) ($old['id_prodi'] ?? '') === (string) $p['id_prodi'] ? 'selected' : '' ?>
                >
                    <?= $this->e($p['kode_prodi']) ?> -
                    <?= $this->e($p['nama_prodi']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="invalid-feedback">
            <?= $this->e($errors['id_prodi'] ?? '') ?>
        </div>
    </div>

    <button class="btn btn-primary" type="submit">Simpan</button>

    <a
        class="btn btn-secondary"
        href="<?= $this->e($this->url('mata-kuliah')) ?>"
    >
        Batal
    </a>
</form>
