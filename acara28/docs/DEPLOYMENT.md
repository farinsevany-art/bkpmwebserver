# Checklist Deployment Acara 28

Acara 28 mempertahankan struktur aplikasi dan skema mahasiswa yang dipakai Acara 27. Konfigurasi lokal memakai database `si-akademik`; untuk hosting, atur nama database, user, password, dan secret melalui environment server. Jangan commit kredensial, token, file backup, atau foto mahasiswa.

## 1. Persiapan lokal

- Jalankan test case Acara 23 dan checklist CRUD Acara 27.
- Pastikan kolom `foto` sudah ada. Folder Acara 28 memakai database lokal yang sama; jangan jalankan migrasi foto lagi jika kolom tersebut sudah dibuat.
- Ekspor database yang benar-benar dipakai aplikasi (`si-akademik` pada konfigurasi lokal ini) melalui phpMyAdmin. Jangan menyimpan dump berisi data pribadi di dalam folder proyek atau repository.
- Siapkan akun database khusus aplikasi, bukan akun `root`.

## 2. Buat database hosting

1. Di cPanel, buat database dan user MySQL khusus aplikasi, lalu berikan hak akses yang dibutuhkan pada database tersebut.
2. Impor dump SQL melalui phpMyAdmin cPanel.
3. Jika nama database di cPanel memiliki prefix akun, catat nama lengkapnya untuk `DB_NAME`.

## 3. Unggah dengan document root yang aman

Utamakan struktur berikut dan arahkan document root domain ke `public/`:

```text
acara28/
├── app/       # di luar document root
├── database/  # di luar document root
├── docs/      # di luar document root
└── public/    # document root
```

Jika hosting mengharuskan `public_html`, letakkan `app/` sebagai folder saudara `public_html/`, lalu unggah **isi** folder `public/` ke `public_html/`. Dengan begitu, path `../app` yang dipakai front controller tetap mengarah ke folder privat.

Pastikan folder `public/uploads/` dapat ditulis oleh PHP dan jangan mengunggah isi upload lokal atau dump database.

## 4. Atur environment produksi

Atur variabel berikut melalui pengaturan environment hosting/PHP-FPM. Jangan isi nilai rahasia di repository atau file yang dapat diunduh publik.

| Variabel | Nilai |
|---|---|
| `APP_ENV` | `production` |
| `APP_JWT_SECRET` | Secret acak minimal 32 karakter; buat nilai baru khusus hosting |
| `DB_HOST` | Host MySQL dari cPanel, biasanya `localhost` |
| `DB_NAME` | Nama database lengkap dari cPanel |
| `DB_USER` | User database dari cPanel |
| `DB_PASS` | Password user database |
| `CORS_ALLOWED_ORIGIN` | Origin frontend yang diizinkan, jika frontend berbeda origin |

Di produksi, aplikasi menolak koneksi jika konfigurasi database atau secret JWT belum lengkap. Jangan menggunakan secret development lokal pada hosting.

## 5. Uji deployment

1. Akses `https://domain-kamu/` atau `/views/login.html`, sesuai document root.
2. Login, cek pemuatan dashboard, lalu uji tambah/ubah/hapus menggunakan data uji.
3. Pastikan upload JPG/PNG dapat ditampilkan dan file skrip tidak dapat dieksekusi dari `uploads/`.
4. Uji endpoint yang dilindungi memakai token baru pada Postman. Jangan menguji operasi hapus dengan data nyata.
5. Coba akses `/app/config/Database.php`, `/database/`, dan `/docs/`; semuanya harus 403/404.
6. Pastikan HTTPS aktif dan backup produksi disimpan di luar document root.

Jangan mempublikasikan endpoint atau kredensial server pada laporan. Catat kendala dan solusinya tanpa mencantumkan password, token, atau data pribadi.
