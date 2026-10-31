
<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Program Studi</h1>

            <a
                class="btn btn-primary"
                href="<?= $this->e($this->url('prodi/create')) ?>"
            >
                + Tambah Prodi
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Program Studi</th>
                        <th>Jenjang</th>
                        <th>Jml Mahasiswa</th>
                        <th>Jml Mata Kuliah</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Belum ada data prodi.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>

                                <td>
                                    <?= $this->e($row['kode_prodi']) ?>
                                </td>

                                <td>
                                    <?= $this->e($row['nama_prodi']) ?>
                                </td>

                                <td>
                                    <?= $this->e($row['jenjang'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= (int) $row['jumlah_mahasiswa'] ?>
                                </td>

                                <td>
                                    <?= (int) $row['jumlah_mk'] ?>
                                </td>

                                <td class="text-end text-nowrap">
                                    <a
                                        class="btn btn-warning btn-sm"
                                        href="<?= $this->e(
                                            $this->url(
                                                'prodi/' . $row['id_prodi'] . '/edit'
                                            )
                                        ) ?>"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="<?= $this->e(
                                            $this->url(
                                                'prodi/' . $row['id_prodi'] . '/delete'
                                            )
                                        ) ?>"
                                        method="post"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus program studi ini?');"
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
