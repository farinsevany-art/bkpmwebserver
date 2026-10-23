# SI Akademik - Acara 6

Contoh proyek PHP native untuk praktikum Middleware, Auth Sederhana, dan Struktur Folder.

## Persyaratan
- XAMPP (Apache + PHP)
- Browser
- Visual Studio Code

## Cara menjalankan
1. Ekstrak folder `si-akademik` ke `D:\xampp\htdocs\si-akademik`.
2. Jalankan Apache melalui XAMPP Control Panel.
3. Buka `http://localhost/si-akademik/public/`.
4. Login menggunakan:
   - Username: `admin`
   - Password: `admin123`
5. Coba buka `http://localhost/si-akademik/public/mahasiswa` sebelum login. Seharusnya diarahkan ke halaman login.
6. Setelah login, buka menu Mahasiswa dan coba tambah data.
7. Klik Logout. Pesan “Anda telah logout.” akan tampil di halaman login.

## Struktur
```text
si-akademik/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   └── MahasiswaController.php
│   ├── Core/
│   │   ├── Controller.php
│   │   ├── Router.php
│   │   └── Middleware/
│   │       └── AuthMiddleware.php
│   └── Views/
│       ├── auth/login.php
│       ├── dashboard/index.php
│       ├── mahasiswa/create.php
│       ├── mahasiswa/index.php
│       └── layouts/
├── public/
│   └── index.php
├── routes/
│   └── web.php
└── README.md
```

## Catatan
- Data mahasiswa disimpan dalam session agar contoh bisa berjalan tanpa database.
- Username/password masih hardcode untuk simulasi praktikum, bukan untuk produksi.
- Pada server produksi, gunakan database, password hashing, CSRF token, dan konfigurasi keamanan yang sesuai.

- File `public/.htaccess` mengarahkan URL seperti `/login` dan `/dashboard` ke router PHP. Pastikan modul `mod_rewrite` aktif dan `AllowOverride All` di konfigurasi Apache.
