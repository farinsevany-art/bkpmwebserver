# Acara 14 — Security, Exception, Logging, PRG, dan CRUD Lengkap

Project ini merupakan pengembangan dari Acara 13 dengan database MySQL `si-akademik`.

## Fitur yang ditambahkan/diperkuat
- Prepared statement melalui PDO pada repository mahasiswa.
- Validasi input mahasiswa: NIM, nama, email, jenis kelamin, program studi, angkatan, alamat, dan status.
- CSRF token untuk seluruh form POST (login, logout, tambah, edit, hapus, dan modul lain).
- Penanganan exception terpusat agar detail teknis tidak ditampilkan kepada pengguna.
- Logging exception ke `storage/logs/app.log`.
- Flash message untuk notifikasi berhasil/gagal.
- Pola PRG setelah proses simpan, ubah, hapus, serta redirect setelah validasi gagal.
- CRUD mahasiswa memakai kolom pada database `si-akademik` yang tersedia.

## Cara menjalankan di XAMPP
1. Ekstrak folder `acara14` ke `C:\xampp\htdocs\`.
2. Jalankan Apache dan MySQL dari XAMPP.
3. Buka phpMyAdmin, buat/import database `si-akademik` dari file `si-akademik.sql`. Jika database sudah ada, jangan impor ulang tanpa backup.
4. Pastikan `config/database.php` berisi host `127.0.0.1`, port `3306`, database `si-akademik`, user `root`, dan password sesuai pengaturan MySQL lokal.
5. Buka `http://localhost/acara14/public/`.
6. Akun praktikum bawaan dari project: `admin` / `admin123`.

Jika menggunakan URL tanpa `/public`, konfigurasi Apache/VirtualHost harus diarahkan ke folder `public`. Jangan memindahkan `index.php` keluar dari `public` tanpa menyesuaikan konfigurasi.

## Log
Log error berada di `storage/logs/app.log`. Folder ini harus dapat ditulisi oleh PHP. Jangan menyimpan password database atau token rahasia di dalam log. Jangan publikasikan file log.

## Skenario uji Acara 14
1. Login dengan akun praktikum.
2. Tambah mahasiswa dengan semua data valid.
3. Coba NIM kosong; pastikan muncul validasi dan refresh tidak mengirim ulang POST.
4. Coba NIM yang sudah terdaftar.
5. Coba format email yang salah.
6. Edit data mahasiswa dan pastikan perubahan tampil di daftar.
7. Hapus data mahasiswa melalui tombol hapus.
8. Refresh setelah proses tambah/ubah/hapus dan pastikan operasi tidak dikirim ulang.
9. Uji token CSRF kosong/tidak valid; request POST harus ditolak.
10. Simulasikan database bermasalah hanya di lingkungan lokal, lalu periksa `storage/logs/app.log`; halaman pengguna seharusnya tidak menampilkan detail SQL/PDO.

## Catatan keamanan praktikum
Akun `admin` / `admin123` hanya untuk demonstrasi lokal. Ganti mekanisme autentikasi dan kata sandi sebelum aplikasi dipakai di lingkungan nyata.
