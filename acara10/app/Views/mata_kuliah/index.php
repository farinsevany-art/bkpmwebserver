
<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Mata Kuliah</h1>

            <a
                class="btn btn-primary"
                href="<?= $this->e($this->url('mata-kuliah/create')) ?>"
            >
                + Tambah Mata Kuliah
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Semester</th>
                        <th>Prodi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Belum ada data mata kuliah.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>

                                <td>
                                    <?= $this->e($row['kode_mk']) ?>
                                </td>

                                <td>
                                    <?= $this->e($row['nama_mk']) ?>
                                </td>

                                <td>
                                    <?= $this->e($row['sks']) ?>
                                </td>

                                <td>
                                    <?= $this->e($row['semester']) ?>
                                </td>

                                <td>
                                    <?= $this->e($row['prodi_nama'] ?? '-') ?>
                                </td>

                                <td class="text-end text-nowrap">
                                    <a
                                        class="btn btn-warning btn-sm"
                                        href="<?= $this->e(
                                            $this->url(
                                                'mata-kuliah/' . $row['id_mk'] . '/edit'
                                            )
                                        ) ?>"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="<?= $this->e(
                                            $this->url(
                                                'mata-kuliah/' . $row['id_mk'] . '/delete'
                                            )
                                        ) ?>"
                                        method="post"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus mata kuliah ini?');"
                                    >
                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
