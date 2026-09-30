<!DOCTYPE html>
<html>
<head>
    <title>Informasi Server</title>
</head>

<body>

    <h1>Informasi Server</h1>

    <table border="1">
        <tr>
            <th>Informasi</th>
            <th>Keterangan</th>
        </tr>

        <tr>
            <td>Nama</td>
            <td>Farin Sevany Kasih </td>
        </tr>

        <tr>
            <td>NIM</td>
            <td>E41251508</td>
        </tr>

        <tr>
            <td>Waktu Server</td>
            <td><?php echo date("Y-m-d H:i:s"); ?></td>
        </tr>

        <tr>
            <td>Versi PHP</td>
            <td><?php echo phpversion(); ?></td>
        </tr>

        <tr>
            <td>Sistem Operasi Server</td>
            <td><?php echo PHP_OS; ?></td>
        </tr>
    </table>

</body>
</html>