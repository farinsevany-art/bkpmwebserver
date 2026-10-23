<?php $title = 'Login Administrator'; require BASE_PATH . '/app/Views/layouts/header.php'; ?>
<div class="wrap login">
    <div class="card">
        <h1>Form Login Administrator</h1>
        <p class="muted">Silakan masuk untuk mengakses Sistem Informasi Akademik.</p>
        <?php if (!empty($flash)): ?>
            <div class="alert <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>
        <form action="<?= htmlspecialchars($this->url('login')) ?>" method="post">
            <label for="username">Username</label>
            <input id="username" name="username" required autocomplete="username">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">
            <p><button class="btn" type="submit">Login</button></p>
        </form>
        <p class="muted">Akun praktikum: <b>admin</b> / <b>admin123</b></p>
    </div>
</div>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
