<h1 class="mb-4">Daftar Mahasiswa</h1>
<a href="#" class="btn btn-primary mb-3">Tambah Mahasiswa</a>

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Prodi</th>
                <th>Angkatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($daftarMahasiswa as $index => $mhs) : ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($mhs->getNim(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($mhs->getNama(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($mhs->getProdi(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($mhs->getAngkatan(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm">Hapus</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
