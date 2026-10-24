
<?php
require BASE_PATH . '/app/Views/layouts/header.php';

$keyword = $keyword ?? '';
$rows = $rows ?? [];
$total = (int) ($total ?? 0);
$page = max(1, (int) ($page ?? 1));
$offset = max(0, (int) ($offset ?? 0));
$totalPages = max(1, (int) ($totalPages ?? 1));

// Membuat URL pagination dengan tetap membawa keyword pencarian
$pageUrl = function (int $p) use ($keyword): string {
    $query = [];

    if ($keyword !== '') {
        $query['keyword'] = $keyword;
    }

    if ($p > 1) {
        $query['page'] = $p;
    }

    return $this->url('mahasiswa')
        . ($query ? '?' . http_build_query($query) : '');
};
?>

<div class="card shadow-sm">
    <div class="card-body">

        <!-- Header halaman -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h1 class="h4 mb-0">Data Mahasiswa</h1>

            <a
                class="btn btn-primary"
                href="<?= $this->e($this->url('mahasiswa/create')) ?>"
            >
                + Tambah Mahasiswa
            </a>
        </div>

        <!-- Form pencarian -->
        <form
            method="get"
            action="<?= $this->e($this->url('mahasiswa')) ?>"
            class="row g-2 mb-3"
        >
            <div class="col-sm-6 col-md-5">
                <input
                    type="text"
                    name="keyword"
                    class="form-control"
                    placeholder="Cari nama atau NIM..."
                    value="<?= $this->e($keyword) ?>"
                >
            </div>

            <div class="col-auto">
                <button class="btn btn-primary" type="submit">
                    Cari
                </button>

                <a
                    class="btn btn-secondary"
                    href="<?= $this->e($this->url('mahasiswa')) ?>"
                >
                    Reset
                </a>
            </div>
        </form>

        <!-- Informasi pencarian -->
        <?php if ($keyword !== ''): ?>
            <p class="text-muted small">
                Hasil pencarian untuk
                <strong><?= $this->e($keyword) ?></strong>:
                <?= $total ?> data ditemukan.
            </p>
        <?php endif; ?>

        <!-- Tabel mahasiswa -->
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Email</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Data mahasiswa tidak ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $i => $row): ?>
                            <?php
                            $nim = (string) ($row['nim'] ?? '');
                            $nimUrl = rawurlencode($nim);
                            ?>

                            <tr>
                                <td><?= $offset + $i + 1 ?></td>

                                <td>
                                    <?= $this->e($nim) ?>
                                </td>

                                <td>
                                    <?= $this->e($row['nama'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= $this->e($row['email'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= $this->e($row['prodi_nama'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= $this->e((string) ($row['angkatan'] ?? '-')) ?>
                                </td>

                                <td>
                                    <?php
                                    $status = $row['status'] ?? '-';
                                    $badgeClass = match ($status) {
                                        'aktif' => 'bg-success',
                                        'cuti'  => 'bg-warning text-dark',
                                        'lulus' => 'bg-primary',
                                        default => 'bg-secondary',
                                    };
                                    ?>

                                    <span class="badge <?= $badgeClass ?>">
                                        <?= $this->e(ucfirst((string) $status)) ?>
                                    </span>
                                </td>

                                <td class="text-end text-nowrap">

                                    <!-- Tombol Detail -->
                                    <a
                                        class="btn btn-info btn-sm text-white"
                                        href="<?= $this->e(
                                            $this->url('mahasiswa/' . $nimUrl)
                                        ) ?>"
                                    >
                                        Detail
                                    </a>

                                    <!-- Tombol Edit -->
                                    <a
                                        class="btn btn-warning btn-sm"
                                        href="<?= $this->e(
                                            $this->url('mahasiswa/' . $nimUrl . '/edit')
                                        ) ?>"
                                    >
                                        Edit
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <form
                                        action="<?= $this->e(
                                            $this->url('mahasiswa/' . $nimUrl . '/delete')
                                        ) ?>"
                                        method="post"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus data mahasiswa ini?');"
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

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <small class="text-muted">
                Total <?= $total ?> data |
                Halaman <?= $page ?> dari <?= $totalPages ?>
            </small>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Navigasi halaman">
                    <ul class="pagination pagination-sm mb-0">

                        <!-- Halaman sebelumnya -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <?php if ($page <= 1): ?>
                                <span class="page-link">&laquo;</span>
                            <?php else: ?>
                                <a
                                    class="page-link"
                                    href="<?= $this->e($pageUrl($page - 1)) ?>"
                                >
                                    &laquo;
                                </a>
                            <?php endif; ?>
                        </li>

                        <!-- Nomor halaman -->
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a
                                    class="page-link"
                                    href="<?= $this->e($pageUrl($p)) ?>"
                                >
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Halaman berikutnya -->
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <?php if ($page >= $totalPages): ?>
                                <span class="page-link">&raquo;</span>
                            <?php else: ?>
                                <a
                                    class="page-link"
                                    href="<?= $this->e($pageUrl($page + 1)) ?>"
                                >
                                    &raquo;
                                </a>
                            <?php endif; ?>
                        </li>

                    </ul>
                </nav>
            <?php endif; ?>

        </div>

    </div>
</div>

<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
