
<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>

<div class="card shadow-sm">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h1 class="h4 mb-0">Detail Mahasiswa</h1>

            <span class="badge bg-primary">
                <?= $this->e(ucfirst((string) ($row['status'] ?? '-'))) ?>
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-3">
                <tbody>
                    <tr>
                        <th style="width: 200px;">NIM</th>
                        <td><?= $this->e($row['nim'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <th>Nama Mahasiswa</th>
                        <td><?= $this->e($row['nama'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td><?= $this->e($row['email'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <th>Jenis Kelamin</th>
                        <td>
                            <?php
                            $jenisKelamin = $row['jenis_kelamin'] ?? '';
                            echo $this->e(
                                $jenisKelamin === 'L'
                                    ? 'Laki-laki'
                                    : ($jenisKelamin === 'P' ? 'Perempuan' : '-')
                            );
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Alamat</th>
                        <td><?= nl2br($this->e($row['alamat'] ?? '-')) ?></td>
                    </tr>

                    <tr>
                        <th>Program Studi</th>
                        <td>
                            <?= $this->e($row['prodi_kode'] ?? '-') ?>
                            -
                            <?= $this->e($row['prodi_nama'] ?? '-') ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Angkatan</th>
                        <td><?= $this->e((string) ($row['angkatan'] ?? '-')) ?></td>
                    </tr>

                    <tr>
                        <th>Status Mahasiswa</th>
                        <td><?= $this->e(ucfirst((string) ($row['status'] ?? '-'))) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a
                class="btn btn-secondary"
                href="<?= $this->e($this->url('mahasiswa')) ?>"
            >
                Kembali ke Daftar
            </a>

            <a
                class="btn btn-warning"
                href="<?= $this->e(
                    $this->url(
                        'mahasiswa/' . rawurlencode((string) $row['nim']) . '/edit'
                    )
                ) ?>"
            >
                Edit Mahasiswa
            </a>
        </div>

    </div>
</div>

<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
