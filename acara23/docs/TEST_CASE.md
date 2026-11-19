# Laporan Manual dan Blackbox Testing API

## Informasi Pengujian

- **Aplikasi yang diuji:** RESTful API Sistem Informasi Akademik (implementasi Acara 17-20)
- **Metode:** Manual testing dengan Postman dan blackbox testing menggunakan equivalence partitioning
- **URL CRUD yang sudah diuji:** `http://localhost/minngu1/acara17/public/mahasiswa/{nim}`
- **URL GET berdasarkan NIM yang sudah diuji:** `http://localhost/minngu1/acara18/public/mahasiswa/{nim}`
- **URL register yang sudah diuji:** `http://localhost/minngu1/acara19/public/register`
- **URL Auth/JWT dan skenario Acara 20:** `http://localhost/minngu1/acara20/public/index.php?url=`
- **Alat:** Laragon, Postman, MySQL
- **Prasyarat:** Apache dan MySQL berjalan; tabel `users`, `mahasiswa`, dan `prodi` tersedia.

Dokumen ini berisi rancangan test case untuk dijalankan di Postman. Kolom **Hasil Aktual** dan **Status** masih bertanda *Belum diuji* dan harus diisi setelah setiap request benar-benar dijalankan.

## Persiapan Data Uji

1. Gunakan akun uji yang sudah terdaftar untuk login. Simpan username dan password di Postman environment, bukan di dokumen atau collection.
2. Untuk register, atur `register_username`, `register_email`, dan `register_password` pada Postman environment. Gunakan username/email unik untuk TC-01.
3. Atur `test_nim` ke NIM uji unik dengan format 9 karakter alfanumerik, `test_email` ke email uji unik, dan `id_prodi` ke ID program studi yang tersedia.
4. Simpan token dari response login sebagai `auth_token`.
5. Jalankan test case berurutan. TC-12 dan seluruh test PUT/DELETE memerlukan data mahasiswa yang dibuat pada TC-09.
6. Setelah pengujian, hapus hanya data mahasiswa uji yang dibuat selama pengujian.

TC-08 dan TC-14 mencatat GET berdasarkan NIM pada Acara 18. TC-15 dan TC-21 mencatat PUT/DELETE pada Acara 17 dengan URL langsung dan NIM `E41230002`. Implementasi Acara 17 belum memakai Bearer auth; skenario yang menguji token harus dijalankan pada URL Acara 20.

Contoh body mahasiswa valid:

```json
{
  "nim": "{{test_nim}}",
  "nama": "Mahasiswa Uji",
  "jenis_kelamin": "P",
  "alamat": "Alamat pengujian",
  "email": "{{test_email}}",
  "id_prodi": {{id_prodi}},
  "angkatan": 2025,
  "status": "aktif",
  "foto": null
}
```

Body yang digunakan untuk test update Acara 17 (TC-15):

```json
{
  "nim": "E41230002",
  "nama": "Siti Aminah ",
  "jenis_kelamin": "P",
  "alamat": "Jl. Contoh Baru",
  "email": "siti.updated@example.com",
  "id_prodi": 1,
  "angkatan": "2024",
  "status": "aktif",
  "foto": null
}
```

Nilai `nama` berisi spasi di akhir; controller melakukan trim sehingga data nama yang disimpan/dikembalikan seharusnya `Siti Aminah`.

Body register yang digunakan pada Acara 19:

TC-01 — data valid (password disimpan hanya pada Postman environment):

```json
{
  "username": "{{register_username}}",
  "email": "{{register_email}}",
  "password": "{{register_password}}"
}
```

TC-02 — body kosong:

```json
{}
```

TC-03 — format email tidak valid:

```json
{
  "username": "{{register_username}}",
  "email": "farinexample.com",
  "password": "{{register_password}}"
}
```

Data login Acara 20:

TC-05 — kredensial benar:

```json
{
  "username": "{{login_username}}",
  "password": "{{login_password}}"
}
```

TC-06 — password salah:

```json
{
  "username": "{{login_username}}",
  "password": "{{wrong_password}}"
}
```

Atur value password di Postman environment lokal; jangan simpan password akun di file dokumentasi.

Untuk request yang memerlukan autentikasi, gunakan header:

```http
Authorization: Bearer {{auth_token}}
Content-Type: application/json
```

## Test Case

| ID | Skenario | Data Uji | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-01 | Register data valid (Acara 19) | `POST http://localhost/minngu1/acara19/public/register`; body contoh TC-01, username/email unik | `201 Created`, `Registrasi berhasil` | `201 Created`, registrasi berhasil | Pass |
| TC-02 | Register dengan body kosong (Acara 19) | `POST http://localhost/minngu1/acara19/public/register`; body `{}` | `400 Bad Request`, `Username, email, dan password wajib diisi` | `400 Bad Request`, field wajib ditolak | Pass |
| TC-03 | Register dengan email tidak valid (Acara 19) | `POST http://localhost/minngu1/acara19/public/register`; body contoh TC-03 | `422 Unprocessable Entity`, `Data registrasi tidak valid` | `422 Unprocessable Entity`, email tidak valid ditolak | Pass |
| TC-04 | Register dengan username/email yang sudah terdaftar | `POST http://localhost/minngu1/acara19/public/register`; gunakan kembali username atau email TC-01 | `409 Conflict` | `409 Conflict`, data duplikat ditolak | Pass |
| TC-05 | Login dengan kredensial benar | `POST http://localhost/minngu1/acara20/public/index.php?url=login`; body contoh TC-05, akun `farin` memakai password benar dari environment | `200 OK`, response berisi JWT | `200 OK`, JWT diterima | Pass |
| TC-06 | Login dengan password salah | `POST http://localhost/minngu1/acara20/public/index.php?url=login`; body contoh TC-06, akun `farin` memakai password salah dari environment | `401 Unauthorized`, `Username atau password salah` | `401 Unauthorized`, kredensial ditolak | Pass |
| TC-07 | Mengambil seluruh data mahasiswa | `GET ?url=mahasiswa`, tanpa token | `200 OK` | `200 OK`, seluruh data mahasiswa muncul | Pass |
| TC-08 | Mengambil mahasiswa dengan NIM yang ada (Acara 18) | `GET http://localhost/minngu1/acara18/public/mahasiswa/E41251508` | `200 OK`, message `Data berhasil diambil`, data NIM `E41251508` muncul | `200 OK`, `Data berhasil diambil`; data NIM `E41251508` ditemukan | Pass |
| TC-09 | Menambahkan mahasiswa dengan data valid | `POST ?url=mahasiswa`, body valid, Bearer token valid | `201 Created` | `201 Created`, data berhasil ditambahkan | Pass |
| TC-10 | Menambahkan mahasiswa dengan NIM kosong | `POST ?url=mahasiswa`; `nim: ""`, field lain valid, token valid | `400 Bad Request` | `400 Bad Request`, field wajib ditolak | Pass |
| TC-11 | Menambahkan mahasiswa dengan format NIM tidak valid | `POST ?url=mahasiswa`; `nim: "T123"`, field lain valid, token valid | `422 Unprocessable Entity` | `422 Unprocessable Entity`, format NIM ditolak | Pass |
| TC-12 | Menambahkan NIM duplikat | `POST ?url=mahasiswa`; gunakan kembali `{{test_nim}}` setelah TC-09, token valid | `422 Unprocessable Entity` | `422 Unprocessable Entity`, NIM duplikat ditolak | Pass |
| TC-13 | Mengakses store tanpa token | `POST ?url=mahasiswa`; body valid, tanpa header `Authorization` | `401 Unauthorized` | `401 Unauthorized`, token tidak ditemukan | Pass |
| TC-14 | Mengambil mahasiswa dengan NIM yang tidak ada (Acara 18) | `GET http://localhost/minngu1/acara18/public/mahasiswa/E412515000` | `404 Not Found`, message `Data mahasiswa tidak ditemukan`, detail NIM ada di `errors` | `404 Not Found`, `Data mahasiswa tidak ditemukan` | Pass |
| TC-15 | **Update — data valid (Acara 17)** | `PUT http://localhost/minngu1/acara17/public/mahasiswa/E41230002`; body JSON pada contoh TC-15, tanpa Bearer token | `200 OK`, `Data mahasiswa berhasil diperbarui`; nama tersimpan sebagai `Siti Aminah` | `200 OK`, `Data mahasiswa berhasil diperbarui` | Pass |
| TC-16 | **Update — body kosong** | `PUT ?url=mahasiswa/{{test_nim}}`; body `{}`, token valid | `400 Bad Request` | `400 Bad Request`, body update kosong ditolak | Pass |
| TC-17 | **Update — format email tidak valid** | `PUT ?url=mahasiswa/{{test_nim}}`; body `{"email":"bukan-email"}`, token valid | `422 Unprocessable Entity` | `422 Unprocessable Entity`, email tidak valid ditolak | Pass |
| TC-18 | **Update — NIM pada URL tidak ditemukan** | `PUT ?url=mahasiswa/Z99999999`; token valid | `404 Not Found` | `404 Not Found`, mahasiswa tidak ditemukan | Pass |
| TC-19 | **Update — NIM pada URL kosong** | `PUT ?url=mahasiswa`; token valid | `400 Bad Request` | `400 Bad Request`, NIM wajib disertakan | Pass |
| TC-20 | **Update — tanpa token** | `PUT ?url=mahasiswa/{{test_nim}}`; body valid, tanpa header `Authorization` | `401 Unauthorized` | `401 Unauthorized` | Pass |
| TC-21 | **Destroy — NIM yang ada (Acara 17)** | `DELETE http://localhost/minngu1/acara17/public/mahasiswa/E41230002`; tanpa Bearer token | `200 OK`, `Data mahasiswa berhasil dihapus` | `200 OK`, `Data mahasiswa berhasil dihapus` | Pass |
| TC-22 | **Destroy — NIM tidak ditemukan** | `DELETE ?url=mahasiswa/Z99999999`; token valid | `404 Not Found` | `404 Not Found`, mahasiswa tidak ditemukan | Pass |
| TC-23 | **Destroy — NIM pada URL kosong** | `DELETE ?url=mahasiswa`; token valid | `400 Bad Request` | `400 Bad Request`, NIM wajib disertakan | Pass |
| TC-24 | **Destroy — tanpa token** | `DELETE ?url=mahasiswa/{{test_nim}}`; tanpa header `Authorization` | `401 Unauthorized` | `401 Unauthorized`, token tidak ditemukan | Pass |

TC-15 sampai TC-24 merupakan tambahan untuk `MahasiswaController::update` dan `::destroy`; skenarionya mencakup partisi data valid, tidak valid, dan data yang tidak ditemukan. TC-16 sampai TC-20 dan TC-22 sampai TC-24 adalah rancangan untuk API Acara 20, termasuk pemeriksaan autentikasi.

## Pelaksanaan dan Kesimpulan

Seluruh 24 test case telah dijalankan. Hasil aktual sesuai dengan hasil yang diharapkan: **24 Pass, 0 Fail**. Pengujian mencakup registrasi, login, GET mahasiswa, validasi POST, update, delete, serta respons autentikasi dan data tidak ditemukan.
