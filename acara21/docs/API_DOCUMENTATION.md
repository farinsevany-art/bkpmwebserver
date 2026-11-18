# Dokumentasi API - Sistem Informasi Akademik

Dokumen ini menjelaskan endpoint yang ditangani `AuthController` dan `MahasiswaController` pada Acara 21. Contoh mahasiswa mengikuti field yang digunakan pada Acara 17, contoh data tidak ditemukan mengikuti respons Acara 18, dan alur register/login mengikuti controller Acara 19-20.

## Base URL dan format umum

```text
http://localhost/minngu1/acara21/public
```

`public/.htaccess` meneruskan path ke front controller. Jika rewrite Apache tidak aktif, gunakan `http://localhost/minngu1/acara21/public/index.php?url=...`.

Request dengan body JSON menggunakan:

```http
Content-Type: application/json
```

Format response:

```json
{
  "success": true,
  "message": "Pesan hasil request",
  "data": null,
  "errors": [],
  "timestamp": "2026-11-18T20:00:00+07:00"
}
```

## 1. Peta endpoint

| Method | URL | Controller dan method | Akses |
|---|---|---|---|
| POST | `/register` | `AuthController::register()` | Publik |
| POST | `/login` | `AuthController::login()` | Publik |
| GET | `/mahasiswa` | `MahasiswaController::index()` | Publik |
| GET | `/mahasiswa/{nim}` atau `/mahasiswa?nim={nim}` | `MahasiswaController::index()` | Publik |
| POST | `/mahasiswa` | `MahasiswaController::store()` | Bearer Token |
| PUT | `/mahasiswa/{nim}` | `MahasiswaController::update()` | Bearer Token |
| DELETE | `/mahasiswa/{nim}` | `MahasiswaController::destroy()` | Bearer Token |

## 2. Autentikasi

### POST `/register`

**URL:** `http://localhost/minngu1/acara21/public/register`  
**Controller:** `AuthController::register()`  
**Headers:** `Content-Type: application/json`

Contoh body register (JSON sudah dilengkapi kurung kurawal pembuka dan penutup):

```json
{
  "username": "mahasiswa_uji",
  "email": "mahasiswa_uji@example.test",
  "password": "contoh-password"
}
```

**Response `201 Created`:**

```json
{
  "success": true,
  "message": "Registrasi berhasil",
  "data": { "id": "1" },
  "errors": [],
  "timestamp": "2026-11-18T20:00:00+07:00"
}
```

`400 Bad Request` dikembalikan jika field wajib tidak diisi; `422 Unprocessable Entity` jika format email tidak valid.

### POST `/login`

**URL:** `http://localhost/minngu1/acara21/public/login`  
**Controller:** `AuthController::login()`  
**Headers:** `Content-Type: application/json`

Login menggunakan username dan password akun yang sudah terdaftar:

```json
{
  "username": "mahasiswa_uji",
  "password": "contoh-password"
}
```

**Response `200 OK`:**

```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "<jwt-token>",
    "role": "user"
  },
  "errors": [],
  "timestamp": "2026-11-18T20:05:00+07:00"
}
```

Token JWT menggunakan HS256 dan berlaku selama lima menit. `400 Bad Request` berarti username/password wajib belum lengkap; `401 Unauthorized` berarti kredensial salah.

> Login harus dikirim ke `/login` dengan body `username` dan `password` saja. Request Acara 20 yang memakai `/mahasiswa` dan body `username`, `email`, `password` bukan contoh login.

## 3. Endpoint mahasiswa

### GET `/mahasiswa`

**URL:** `http://localhost/minngu1/acara21/public/mahasiswa`  
**Controller:** `MahasiswaController::index()`  
**Akses:** Publik.

Mengambil semua mahasiswa. Respons sukses `200 OK` berisi array pada `data`.

### GET `/mahasiswa/{nim}`

**URL:** `http://localhost/minngu1/acara21/public/mahasiswa/E412515000`  
**Controller:** `MahasiswaController::index()`  
**Akses:** Publik.

Jika NIM ditemukan, respons `200 OK` berisi satu objek mahasiswa. Jika tidak ditemukan, respons `404 Not Found`:

```json
{
  "success": false,
  "message": "Data mahasiswa tidak ditemukan",
  "data": null,
  "errors": {
    "nim": ["Data mahasiswa dengan NIM tersebut tidak ditemukan."]
  },
  "timestamp": "2026-11-18T20:10:00+07:00"
}
```

Untuk memakai query string, gunakan `/mahasiswa?nim=E412515000`.

### POST `/mahasiswa`

**URL:** `http://localhost/minngu1/acara21/public/mahasiswa`  
**Controller:** `MahasiswaController::store()`  
**Headers:** `Content-Type: application/json` dan `Authorization: Bearer <jwt-token>`

Request body mengikuti contoh data mahasiswa Acara 17:

```json
{
  "nim": "E41230002",
  "nama": "Siti Aminah",
  "jenis_kelamin": "P",
  "alamat": "Jl. Contoh Baru",
  "email": "siti.updated@example.com",
  "id_prodi": 1,
  "angkatan": "2024",
  "status": "aktif",
  "foto": null
}
```

Gunakan NIM dan email yang belum terdaftar. NIM harus 9 karakter, `jenis_kelamin` harus `L` atau `P`, `id_prodi` harus tersedia, dan `status` harus `aktif`, `cuti`, atau `lulus`.

**Response `201 Created`:**

```json
{
  "success": true,
  "message": "Data berhasil ditambahkan",
  "data": {
    "nim": "E41230002",
    "nama": "Siti Aminah",
    "jenis_kelamin": "P",
    "alamat": "Jl. Contoh Baru",
    "email": "siti.updated@example.com",
    "id_prodi": 1,
    "angkatan": 2024,
    "status": "aktif",
    "foto": null
  },
  "errors": [],
  "timestamp": "2026-11-18T20:12:00+07:00"
}
```

`400 Bad Request` berarti JSON tidak valid, `401 Unauthorized` berarti token tidak ada/tidak valid/kedaluwarsa, dan `422 Unprocessable Entity` berarti data mahasiswa tidak memenuhi validasi.

### PUT `/mahasiswa/{nim}`

**URL:** `http://localhost/minngu1/acara21/public/mahasiswa/E41230002`  
**Controller:** `MahasiswaController::update()`  
**Headers:** `Content-Type: application/json` dan `Authorization: Bearer <jwt-token>`

Body dapat berisi field yang ingin diperbarui; contoh semua field:

```json
{
  "nim": "E41230002",
  "nama": "Siti Aminah Updated",
  "jenis_kelamin": "P",
  "alamat": "Jl. Contoh Baru",
  "email": "siti.updated@example.com",
  "id_prodi": 1,
  "angkatan": "2024",
  "status": "aktif",
  "foto": null
}
```

**Response `200 OK`:**

```json
{
  "success": true,
  "message": "Data berhasil diperbarui",
  "data": {
    "nim": "E41230002",
    "nama": "Siti Aminah Updated",
    "jenis_kelamin": "P",
    "alamat": "Jl. Contoh Baru",
    "email": "siti.updated@example.com",
    "id_prodi": 1,
    "angkatan": 2024,
    "status": "aktif",
    "foto": null
  },
  "errors": [],
  "timestamp": "2026-11-18T20:15:00+07:00"
}
```

Kemungkinan error: `400 Bad Request` jika NIM/body tidak valid, `401 Unauthorized` jika token tidak ada/tidak valid, `404 Not Found` jika NIM tidak ditemukan, dan `422 Unprocessable Entity` jika field tidak valid.

### DELETE `/mahasiswa/{nim}`

**URL:** `http://localhost/minngu1/acara21/public/mahasiswa/E41230002`  
**Controller:** `MahasiswaController::destroy()`  
**Headers:** `Authorization: Bearer <jwt-token>`

**Response `200 OK`:**

```json
{
  "success": true,
  "message": "Data berhasil dihapus",
  "data": null,
  "errors": [],
  "timestamp": "2026-11-18T20:17:00+07:00"
}
```

Jika NIM tidak ditemukan, endpoint mengembalikan `404 Not Found` dengan format error NIM seperti contoh GET. POST, PUT, dan DELETE harus memakai token yang diterima dari `/login`.

## 4. Ringkasan status HTTP

| Status | Arti | Contoh |
|---|---|---|
| 200 | Request berhasil | Login, GET, PUT, DELETE |
| 201 | Data berhasil dibuat | Register atau POST mahasiswa |
| 400 | Request/body/field wajib tidak valid | Body register kosong |
| 401 | Kredensial atau token tidak valid | Token tidak ada, diubah, atau kedaluwarsa |
| 404 | Endpoint atau data tidak ditemukan | NIM mahasiswa tidak ada |
| 405 | Method tidak diizinkan | GET pada `/login` |
| 422 | Nilai field tidak memenuhi validasi | Format email/NIM tidak valid |

## 5. Collection Postman

Import `postman/API-Sistem-Informasi-Akademik-Acara21.postman_collection.json` dan `postman/Local-Acara21.postman_environment.json`. Pilih environment **Local Acara 21**. Isi `login_username` dan `login_password` dengan akun uji yang terdaftar.

Collection memuat deskripsi endpoint serta contoh respons sukses dan gagal. Jalankan login sebelum request mahasiswa yang memerlukan Bearer Token. Gunakan NIM/email unik dan hapus hanya data uji setelah selesai.
