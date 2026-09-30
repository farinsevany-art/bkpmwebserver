<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Acara 2 - GET</title>
</head>
<body>

    <h1>Form Pencarian</h1>

    <form method="GET" action="">
        <label>Nama Mahasiswa:</label>
        <input type="text" name="nama">

        <button type="submit">Cari</button>
    </form>

    <?php
    if (isset($_GET['nama'])) {
        $nama = $_GET['nama'];

        echo "<p>Nama yang dicari: <strong>"
            . htmlspecialchars($nama)
            . "</strong></p>";
    }
    ?>

</body>
</html>