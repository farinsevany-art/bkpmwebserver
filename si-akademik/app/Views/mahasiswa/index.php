<?php require_once __DIR__ . '/../partials/header.php'; ?>

<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<div class="container mt-4">

    <h1 class="mb-4">Daftar Mahasiswa</h1>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">

            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($mahasiswa as $index => $mhs): ?>

                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $mhs->getNim() ?></td>
                        <td><?= $mhs->getNama() ?></td>
                        <td><?= $mhs->getProdi() ?></td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>
    </div>

</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>