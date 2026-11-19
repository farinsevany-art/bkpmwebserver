# Pengujian Autentikasi JWT dan API Mahasiswa

## Informasi pengujian

- **Aplikasi:** RESTful API Sistem Informasi Akademik (Acara 20)
- **Alat:** Laragon, Postman, dan MySQL
- **Base URL:** `http://localhost/minngu1/acara20/public/index.php`
- **Routing:** parameter query `url`, misalnya `?url=login` atau `?url=mahasiswa/T12345678`
- **Prasyarat:** Apache dan MySQL berjalan; tabel `users`, `mahasiswa`, dan `prodi` tersedia.

Akun dari Acara 19 dapat digunakan untuk login karena Acara 19 dan Acara 20 menggunakan database `si-akademik` yang sama. Jangan menyimpan password akun pribadi di collection atau laporan.

## Menyiapkan Postman

1. Import `postman/API-Sistem-Informasi-Akademik-Acara20.postman_collection.json` dan `postman/Local-Acara20.postman_environment.json`.
2. Pilih environment **Local Acara 20**.
3. Isi `login_username` dan `login_password` dengan akun yang terdaftar dari Acara 19. `id_prodi` bernilai `1` secara default; sesuaikan jika ID tersebut tidak tersedia.
4. Jalankan request secara berurutan. Request login sukses menyimpan JWT sebagai `auth_token`; request POST mahasiswa membuat NIM uji untuk request PUT dan DELETE.
5. Request POST/PUT/DELETE yang sah mengirim header `Authorization: Bearer {{auth_token}}`. Request negatif sengaja tidak mengirim header atau menambahkan karakter pada token.

Contoh body mahasiswa:

```json
{
  "nim": "T12345678",
  "nama": "Mahasiswa Uji",
  "jenis_kelamin": "P",
  "alamat": "Alamat pengujian",
  "email": "uji@example.test",
  "id_prodi": 1,
  "angkatan": 2025,
  "status": "aktif",
  "foto": null
}
```

NIM dan email harus unik. Collection membuat nilai uji secara otomatis. Request DELETE menghapus data uji yang dibuat oleh request POST tersebut.

## Test case

| ID | Skenario | Hasil yang diharapkan | Hasil aktual | Status |
|---|---|---:|---:|---|
| TC-01 | Login dengan username dan password yang benar | 200, respons berisi token | 200, token diterima | Pass |
| TC-02 | Login dengan password salah | 401 | 401 | Pass |
| TC-03 | GET daftar mahasiswa tanpa token | 200 | 200 | Pass |
| TC-04 | POST mahasiswa dengan token valid | 201 | 201 | Pass |
| TC-05 | POST mahasiswa tanpa token | 401 | 401 | Pass |
| TC-06 | POST mahasiswa dengan token yang diubah | 401 | 401 | Pass |
| TC-07 | PUT mahasiswa dengan token valid | 200 | 200 | Pass |
| TC-08 | PUT mahasiswa tanpa token | 401 | 401 | Pass |
| TC-09 | PUT mahasiswa dengan token yang diubah | 401 | 401 | Pass |
| TC-10 | DELETE mahasiswa dengan token valid | 200 | 200 | Pass |
| TC-11 | DELETE mahasiswa tanpa token | 401 | 401 | Pass |
| TC-12 | DELETE mahasiswa dengan token yang diubah | 401 | 401 | Pass |
| TC-13 | Mengakses API dengan token bertanda tangan valid tetapi sudah kedaluwarsa | 401 | 401 | Pass |
| TC-14 | GET mahasiswa setelah data uji dihapus | 404 | 404 | Pass |

Pengujian lokal memakai akun sementara pada tabel `users` dan data mahasiswa sementara. Semua data sementara dibersihkan setelah pengujian. Masa berlaku token baru diverifikasi sebesar 1.200 detik (20 menit); token kedaluwarsa ditolak oleh API.

## Kesimpulan JWT stateless dan logout

JWT stateless menyimpan klaim dan waktu kedaluwarsa di dalam token yang ditandatangani server. Server tidak menyimpan sesi atau daftar token aktif, sehingga membuang token dari sisi client hanya menghapus salinan client; token yang sudah diterbitkan tetap dapat digunakan sampai kedaluwarsa.

Logout yang langsung membatalkan token memerlukan mekanisme tambahan di sisi server, misalnya blacklist berisi ID token (`jti`) sampai waktu kedaluwarsanya. Setiap request perlu memeriksa blacklist tersebut. Cara ini menambah penyimpanan dan pemeriksaan server, sehingga autentikasi tidak lagi sepenuhnya stateless. Untuk implementasi saat ini, token berlaku 20 menit dan setelah itu harus ditolak; penghapusan token pada client saja bukan pencabutan token di server.
