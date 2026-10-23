<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? htmlspecialchars($title) : 'SI Akademik' ?></title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f4f6fb;color:#202938;font:15px Arial,sans-serif}
        .nav{background:#173b70;color:#fff;padding:16px 6%;display:flex;align-items:center;justify-content:space-between;gap:16px}
        .nav a{color:#fff;text-decoration:none;margin-right:18px}.nav form{display:inline}
        .wrap{max-width:1000px;margin:32px auto;padding:0 18px}.card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 4px 18px #152c4b12;margin-bottom:20px}
        h1{margin-top:0;font-size:26px}.muted{color:#64748b}.btn{display:inline-block;background:#2563eb;color:white;border:0;border-radius:7px;padding:10px 15px;text-decoration:none;cursor:pointer;font-size:14px}
        .btn.secondary{background:#64748b}.btn.danger{background:#dc2626}.alert{padding:12px 15px;border-radius:7px;margin-bottom:16px}.success{background:#dcfce7;color:#166534}.error{background:#fee2e2;color:#991b1b}
        label{display:block;font-weight:bold;margin:14px 0 6px}input,select{width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:7px;font-size:15px}
        table{width:100%;border-collapse:collapse;margin-top:18px}th,td{text-align:left;border-bottom:1px solid #e2e8f0;padding:12px}th{background:#f8fafc}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px}.stat{background:#eff6ff;border-radius:10px;padding:18px}.stat strong{display:block;font-size:28px;color:#1d4ed8;margin-bottom:5px}
        .login{max-width:420px;margin:70px auto}.footer{text-align:center;color:#64748b;padding:20px}
    </style>
</head>
<body>
