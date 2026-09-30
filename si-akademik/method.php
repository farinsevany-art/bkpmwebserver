<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Deteksi Method Request</title>
</head>
<body>

    <h1>Deteksi Method Request</h1>

    <form method="GET">
        <button type="submit">Kirim GET</button>
    </form>

    <br>

    <form method="POST">
        <button type="submit">Kirim POST</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo "<p>Request menggunakan method GET.</p>";
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<p>Request menggunakan method POST.</p>";
    }
    ?>

</body>
</html>