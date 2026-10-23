# SI Akademik - Acara 8
PDO, Prepared Statement, Relasi, dan CRUD Lengkap (Mahasiswa, Prodi, Mata Kuliah).
Dikembangkan dari struktur Acara 6 (Router + Controller + Middleware + Views).

## Cara menjalankan
1. Start **Apache** dan **MySQL** di XAMPP.
2. Di phpMyAdmin, pastikan database `si_akademik` sudah ada (import `si_akademik.sql` bila belum).
3. Import `database/acara8_mata_kuliah.sql` ke database `si_akademik` (membuat tabel `mata_kuliah`).
4. Ekstrak folder ini ke `htdocs/si-akademik` (isinya: `app`, `config`, `public`, `routes`, `database`).
5. Cek `config/database.php` (default: host 127.0.0.1, user `root`, password kosong, port 3306).
6. Buka `http://localhost/si-akademik/public/` (atau `localhost:8080/...` sesuai port Apache kamu).
7. Login: `admin` / `admin123`.

> Halaman memakai Bootstrap dari CDN, jadi perlu koneksi internet saat membuka di browser.

## Struktur
```text
si-akademik/
├── config/database.php
├── database/acara8_mata_kuliah.sql
├── app/
│   ├── Core/        Router, Controller, Database (singleton PDO), Model, Middleware/
│   ├── Models/      Mahasiswa, Prodi, MataKuliah (semua query prepared statement)
│   ├── Controllers/ Auth, Home, Mahasiswa, Prodi, MataKuliah
│   └── Views/       layouts, auth, dashboard, mahasiswa, prodi, mata_kuliah, errors
├── public/index.php (+ .htaccess)
└── routes/web.php
```

## Route CRUD
| Method | URL | Fungsi |
|---|---|---|
| GET | /mahasiswa?keyword=...&page=... | index + search + pagination |
| GET | /mahasiswa/create | form tambah |
| POST | /mahasiswa/store | simpan |
| GET | /mahasiswa/{id} | detail |
| GET | /mahasiswa/{id}/edit | form edit |
| POST | /mahasiswa/{id}/update | update |
| POST | /mahasiswa/{id}/delete | hapus (ada konfirmasi JavaScript) |

Pola yang sama berlaku untuk `/prodi` dan `/mata-kuliah` (tanpa halaman detail).

## Poin penting
- Semua query memakai **PDO prepared statement** (named parameter), `ERRMODE_EXCEPTION`, `EMULATE_PREPARES = false`.
- **Search**: `WHERE nama LIKE :kw_nama OR nim LIKE :kw_nim`. Dua placeholder berbeda dipakai karena named parameter tidak boleh diulang saat `EMULATE_PREPARES = false`.
- **JOIN**: daftar mahasiswa menampilkan nama prodi (`JOIN prodi`), daftar prodi menampilkan jumlah mahasiswa/mata kuliah (`LEFT JOIN`).
- Tambah/ubah/hapus memakai method **POST**; hapus diberi `confirm()` JavaScript.
- Menghapus prodi yang masih dipakai ditolak oleh foreign key dan ditampilkan sebagai pesan error.
- Uji SQL Injection: cari `' OR '1'='1` di halaman mahasiswa. Hasilnya 0 data, karena input diperlakukan sebagai teks biasa.
