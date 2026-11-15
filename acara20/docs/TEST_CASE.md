# Laporan Manual Testing dan Blackbox Testing

## Informasi pengujian

- **Aplikasi:** RESTful API Sistem Informasi Akademik
- **Metode:** Manual testing dengan teknik Equivalence Partitioning
- **Alat:** Laragon, Postman, dan MySQL
- **Base URL:** `http://localhost/minngu1/acara20/public/index.php`
- **Tanggal pengujian:** 14 November 2026
- **Pelaksana:** Pengguna
- **Prasyarat:** Apache dan MySQL berjalan; tabel `users` dan `mahasiswa` tersedia; siapkan akun untuk login dan token yang belum kedaluwarsa.

> Hasil aktual pada tabel dicatat berdasarkan konfirmasi pengguna bahwa seluruh skenario telah dicoba melalui Postman dan berhasil.

## Header dan contoh data

Request dengan body memakai:

```http
Content-Type: application/json
```

Request terlindungi juga memakai:

```http
Authorization: Bearer <token-dari-login>
```

Contoh body mahasiswa yang valid:

```json
{
  "nim": "220100123",
  "nama": "Mahasiswa Uji",
  "prodi": "Teknik Informatika"
}
```

NIM harus berisi 9 karakter. Untuk pengujian duplikasi, pastikan kolom `mahasiswa.nim` memiliki indeks `UNIQUE` dan gunakan NIM yang sudah ada.

## Test case

| ID | Skenario | Data uji / request | Hasil yang diharapkan | Hasil aktual | Status |
|---|---|---|---|---|---|
| TC-01 | Menambahkan mahasiswa dengan data valid | `POST ?url=mahasiswa`, body valid, Bearer Token valid | `201 Created`, data berhasil ditambahkan | `201 Created`, sesuai harapan | Pass |
| TC-02 | Menambahkan mahasiswa dengan NIM kosong | `POST ?url=mahasiswa`, `"nim": ""`, Bearer Token valid | `400 Bad Request`, field NIM wajib diisi | `400 Bad Request`, sesuai harapan | Pass |
| TC-03 | Menambahkan NIM duplikat | `POST ?url=mahasiswa`, NIM yang sudah tersimpan, Bearer Token valid | `422 Unprocessable Entity`, NIM sudah terdaftar | `422 Unprocessable Entity`, sesuai harapan | Pass |
| TC-04 | Login dengan kredensial benar | `POST ?url=login`, username dan password terdaftar | `200 OK`, respons berisi token | `200 OK`, token diterima | Pass |
| TC-05 | Login dengan password salah | `POST ?url=login`, password yang salah | `401 Unauthorized` | `401 Unauthorized`, sesuai harapan | Pass |
| TC-06 | Menambahkan mahasiswa tanpa token | `POST ?url=mahasiswa`, body valid, tanpa `Authorization` | `401 Unauthorized` | `401 Unauthorized`, sesuai harapan | Pass |
| TC-07 | Memperbarui mahasiswa dengan ID dan data valid | `PUT ?url=mahasiswa/{id}`, ID yang ada, body valid, Bearer Token valid | `200 OK`, data berhasil diperbarui | `200 OK`, sesuai harapan | Pass |
| TC-08 | Memperbarui mahasiswa tanpa ID | `PUT ?url=mahasiswa`, body valid, Bearer Token valid | `400 Bad Request`, ID wajib diisi | `400 Bad Request`, sesuai harapan | Pass |
| TC-09 | Memperbarui mahasiswa dengan ID yang tidak ditemukan | `PUT ?url=mahasiswa/999999`, body valid, Bearer Token valid | `404 Not Found` | `404 Not Found`, sesuai harapan | Pass |
| TC-10 | Memperbarui mahasiswa dengan NIM duplikat | `PUT ?url=mahasiswa/{id}`, NIM milik data lain, Bearer Token valid | `422 Unprocessable Entity`, NIM sudah terdaftar | `422 Unprocessable Entity`, sesuai harapan | Pass |
| TC-11 | Memperbarui mahasiswa dengan NIM terlalu pendek | `PUT ?url=mahasiswa/{id}`, NIM `123`, Bearer Token valid | `422 Unprocessable Entity`, panjang NIM tidak valid | `422 Unprocessable Entity`, sesuai harapan | Pass |
| TC-12 | Memperbarui mahasiswa tanpa token | `PUT ?url=mahasiswa/{id}`, body valid, tanpa `Authorization` | `401 Unauthorized` | `401 Unauthorized`, sesuai harapan | Pass |
| TC-13 | Menghapus mahasiswa yang ada | `DELETE ?url=mahasiswa/{id}`, ID yang ada, Bearer Token valid | `200 OK`, data berhasil dihapus | `200 OK`, sesuai harapan | Pass |
| TC-14 | Menghapus mahasiswa dengan ID yang tidak ditemukan | `DELETE ?url=mahasiswa/999999`, Bearer Token valid | `404 Not Found` | `404 Not Found`, sesuai harapan | Pass |
| TC-15 | Menghapus mahasiswa tanpa token | `DELETE ?url=mahasiswa/{id}`, tanpa `Authorization` | `401 Unauthorized` | `401 Unauthorized`, sesuai harapan | Pass |
| TC-16 | Menghapus mahasiswa tanpa ID | `DELETE ?url=mahasiswa`, Bearer Token valid | `400 Bad Request`, ID wajib diisi | `400 Bad Request`, sesuai harapan | Pass |

## Catatan eksekusi

1. Login untuk memperoleh token. Token berlaku selama 5 menit; login kembali jika token kedaluwarsa.
2. Pada Postman, masukkan token di **Authorization → Bearer Token** untuk request terlindungi.
3. Ganti `{id}` dengan ID mahasiswa yang benar-benar ada untuk skenario sukses.
4. Gunakan NIM pengujian yang aman dan tidak dipakai data penting. Pengujian DELETE akan menghapus data secara permanen.
5. Untuk TC-03 dan TC-10, pastikan indeks unik pada `mahasiswa.nim` aktif. Simpan respons/status HTTP aktual sebagai bukti pengujian.
