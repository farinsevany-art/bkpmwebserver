<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4">Form Login Administrator</h1>
                <p class="text-muted">Silakan masuk untuk mengakses Sistem Informasi Akademik.</p>
                <form action="<?= $this->e($this->url('login')) ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input id="username" name="username" class="form-control" required autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" name="password" type="password" class="form-control" required autocomplete="current-password">
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Login</button>
                </form>
                <p class="text-muted small mt-3 mb-0">Akun praktikum: <b>admin</b> / <b>admin123</b></p>
            </div>
        </div>
    </div>
</div>
<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
