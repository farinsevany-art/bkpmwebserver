<?php
$title = $title ?? 'SI Akademik';
$current = $current ?? '';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$loggedIn = !empty($_SESSION['logged_in']);
$navItems = [
    'dashboard'   => ['Dashboard', 'dashboard'],
    'mahasiswa'   => ['Mahasiswa', 'mahasiswa'],
    'prodi'       => ['Program Studi', 'prodi'],
    'mata-kuliah' => ['Mata Kuliah', 'mata-kuliah'],
];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->e($title) ?> | SI Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php if ($loggedIn): ?>
<nav class="navbar navbar-expand-md navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $this->e($this->url('dashboard')) ?>">SI AKADEMIK</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <?php foreach ($navItems as $key => [$label, $path]): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current === $key ? 'active fw-semibold' : '' ?>" href="<?= $this->e($this->url($path)) ?>"><?= $this->e($label) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form action="<?= $this->e($this->url('logout')) ?>" method="post" class="d-flex">
                <button class="btn btn-danger btn-sm" type="submit">Logout</button>
            </form>
        </div>
    </div>
</nav>
<?php endif; ?>
<main class="container py-4">
<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $this->e($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= $this->e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
