<?php

include "../config/database.php";

$query = "SELECT * FROM mahasiswa ORDER BY nim ASC";
$result = mysqli_query($conn, $query);

$mahasiswa = [];

while ($row = mysqli_fetch_assoc($result)) {
    $mahasiswa[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Mahasiswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body>

    <div class="container mt-4">

        <h1 class="mb-4">Daftar Mahasiswa</h1>

        <a href="../create.php" class="btn btn-primary mb-3">
            Tambah Mahasiswa
        </a>

        <table class="table table-bordered table-striped">

            <thead class="table-dark">
                <tr>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($mahasiswa as $mhs): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($mhs['nim']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($mhs['nama']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($mhs['prodi']) ?>
                        </td>

                        <td>

                            <a
                                href="../edit.php?nim=<?= urlencode($mhs['nim']) ?>"
                                class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <a
                                href="../delete.php?nim=<?= urlencode($mhs['nim']) ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin mau hapus data ini?')">
                                Hapus
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>