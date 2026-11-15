# Backup dan Restore Basis Data

Acara 28 menggunakan database `si-akademik` secara lokal, sesuai konfigurasi pada `app/config/Database.php`.

## Prosedur backup

1. Buka phpMyAdmin dan pilih database `si-akademik`.
2. Pilih tab **Export**, gunakan format **SQL**, lalu klik **Export**.
3. Simpan hasil backup dengan pola nama `si-akademik-YYYY-MM-DD.sql` di lokasi backup yang tidak berada di folder publik aplikasi.
4. Catat waktu dan lokasi backup pada tabel berikut.

| Tanggal dan waktu | Database | Nama file backup | Status |
|---|---|---|---|
| Isi setelah backup dilakukan | `si-akademik` | Isi nama file hasil export | Belum dicatat |

**Jadwal:** lakukan backup pada akhir setiap sesi praktikum.

## Prosedur restore

1. Buat atau pilih database tujuan di phpMyAdmin.
2. Pilih database tujuan, buka tab **Import**, dan pilih file backup SQL.
3. Jalankan import dan pastikan tabel `users` serta `mahasiswa` berhasil dipulihkan.
4. Verifikasi data melalui aplikasi sebelum menggunakan database hasil restore.

## Migrasi kolom foto

Jalankan `database/alter_mahasiswa_foto.sql` sekali pada database `si-akademik` sebelum memakai fitur upload. Jika kolom `foto` sudah ada, jangan menjalankan migrasi yang sama lagi.
