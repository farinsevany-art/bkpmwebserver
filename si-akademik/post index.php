<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Acara 3 - POST</title>
</head>
<body>

    <h1>Form Login</h1>

    <form method="POST" action="">

        <label>Username:</label>
        <input type="text" name="username">

        <br><br>

        <label>Password:</label>
        <input type="password" name="password">

        <br><br>

        <button type="submit">Login</button>

    </form>

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $username = $_POST['username'];
        $password = $_POST['password'];

        echo "<p>Username: <strong>"
            . htmlspecialchars($username)
            . "</strong></p>";

        echo "<p>Password berhasil diterima melalui POST.</p>";
    }

    ?>

</body>
</html>