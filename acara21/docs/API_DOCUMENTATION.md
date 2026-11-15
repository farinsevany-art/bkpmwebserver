# Dokumentasi API - Sistem Informasi Akademik

Dokumentasi ini mengikuti implementasi API Native PHP pada Acara 21.

## Base URL dan format request

```text
http://localhost/minngu1/acara21/public/index.php
```

Router menerima endpoint melalui parameter `url`. Untuk request dengan body JSON, gunakan header:

```http
Content-Type: application/json
```

Format response JSON:

```json
{
  "success": true,
  "message": "Pesan hasil request",
  "data": null,
  "errors": [],
  "timestamp": "2026-11-14T20:00:00+07:00"
}
```

## 1. Peta endpoint

| Method | URL | Controller dan method | Akses |
|---|---|---|---|
| POST | `?url=register` | `AuthController::register()` | Publik |
| POST | `?url=login` | `AuthController::login()` | Publik |
| GET | `?url=mahasiswa` | `MahasiswaController::index()` | Publik |
| GET | `?url=mahasiswa&id={id}` | `MahasiswaController::index()` | Publik |
| POST | `?url=mahasiswa` | `MahasiswaController::store()` | Bearer Token |
| PUT | `?url=mahasiswa/{id}` | `MahasiswaController::update()` | Bearer Token |
| DELETE | `?url=mahasiswa/{id}` | `MahasiswaController::destroy()` | Bearer Token |

## 2. Autentikasi

### POST `/register`

URL lengkap: `http://localhost/minngu1/acara21/public/index.php?url=register`

**Headers:** `Content-Type: application/json`

**Request body:**

```json
{
  "username": "mahasiswa1",
  "email": "mahasiswa1@example.com",
  "password": "rahasia123"
}
```

**Response `201 Created`:**

```json
{
  "success": true,
  "message": "Registrasi berhasil",
  "data": { "id": "1" },
  "errors": [],
  "timestamp": "2026-11-14T20:00:00+07:00"
}
```

Kemungkinan error: `400 Bad Request` jika field wajib kosong atau format JSON salah, `422 Unprocessable Entity` jika email tidak valid.

### POST `/login`

URL lengkap: `http://localhost/minngu1/acara21/public/index.php?url=login`

**Headers:** `Content-Type: application/json`

**Request body:**

```json
{
  "username": "mahasiswa1",
  "password": "rahasia123"
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
  "timestamp": "2026-11-14T20:05:00+07:00"
}
```

Token menggunakan JWT HS256 dan berlaku selama 5 menit. Kemungkinan error: `400 Bad Request` jika username/password tidak lengkap, `401 Unauthorized` jika kredensial salah.

## 3. Endpoint mahasiswa

### GET `/mahasiswa`

URL lengkap: `http://localhost/minngu1/acara21/public/index.php?url=mahasiswa`

Mengambil seluruh data mahasiswa. Tidak memerlukan token.

**Response `200 OK`:**

```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [
    {
      "id": "1",
      "nim": "E41230001",
      "nama": "Ahmad Fauzi",
      "prodi": "Teknik Informatika"
    }
  ],
  "errors": [],
  "timestamp": "2026-11-14T20:10:00+07:00"
}
```

### GET `/mahasiswa` berdasarkan ID

URL lengkap: `http://localhost/minngu1/acara21/public/index.php?url=mahasiswa&id=1`

Mengambil satu data. Jika ID tidak ditemukan, API mengembalikan `404 Not Found`.

### POST `/mahasiswa`

URL lengkap: `http://localhost/minngu1/acara21/public/index.php?url=mahasiswa`

**Headers:**

```http
Content-Type: application/json
Authorization: Bearer <jwt-token>
```

**Request body:**

```json
{
  "nim": "E41230002",
  "nama": "Siti Aminah",
  "prodi": "Teknik Informatika"
}
```

NIM harus berjumlah 9 karakter.

**Response `201 Created`:**

```json
{
  "success": true,
  "message": "Data berhasil ditambahkan",
  "data": { "id": "2" },
  "errors": [],
  "timestamp": "2026-11-14T20:12:00+07:00"
}
```

Kemungkinan error: `400 Bad Request` jika field wajib tidak diisi, `401 Unauthorized` jika token tidak ada/tidak valid, `422 Unprocessable Entity` jika panjang NIM bukan 9 karakter.

### PUT `/mahasiswa/{id}`

URL lengkap: `http://localhost/minngu1/acara21/public/index.php?url=mahasiswa/1`

**Headers:** `Content-Type: application/json` dan `Authorization: Bearer <jwt-token>`

**Request body:**

```json
{
  "nim": "E41230001",
  "nama": "Ahmad Fauzi Updated",
  "prodi": "Teknik Informatika"
}
```

**Response `200 OK`:**

```json
{
  "success": true,
  "message": "Data berhasil diperbarui",
  "data": null,
  "errors": [],
  "timestamp": "2026-11-14T20:15:00+07:00"
}
```

Kemungkinan error: `400 Bad Request` jika ID tidak diisi, `401 Unauthorized` jika token tidak ada/tidak valid, `404 Not Found` jika mahasiswa tidak ditemukan.

### DELETE `/mahasiswa/{id}`

URL lengkap: `http://localhost/minngu1/acara21/public/index.php?url=mahasiswa/1`

**Headers:** `Authorization: Bearer <jwt-token>`

**Response `200 OK`:**

```json
{
  "success": true,
  "message": "Data berhasil dihapus",
  "data": null,
  "errors": [],
  "timestamp": "2026-11-14T20:17:00+07:00"
}
```

Kemungkinan error: `400 Bad Request` jika ID tidak diisi, `401 Unauthorized` jika token tidak ada/tidak valid, `404 Not Found` jika mahasiswa tidak ditemukan.

## 4. Error umum

| HTTP Status | Arti | Contoh |
|---|---|---|
| 400 | Request atau field wajib tidak valid | Body register tidak lengkap |
| 401 | Token atau kredensial tidak valid | Token tidak ada/kedaluwarsa |
| 404 | Endpoint atau data tidak ditemukan | ID mahasiswa tidak ada |
| 405 | Method HTTP tidak diizinkan | `GET` pada endpoint login |
| 422 | Nilai field tidak memenuhi validasi | Format email atau panjang NIM tidak valid |

Contoh response error:

```json
{
  "success": false,
  "message": "Token tidak ditemukan",
  "data": null,
  "errors": [],
  "timestamp": "2026-11-14T20:20:00+07:00"
}
```

## 5. Dokumentasi Postman

Buat Collection **Acara 21 - Sistem Informasi Akademik**, lalu tambahkan setiap request pada tabel endpoint. Isi kolom Description dengan controller/method dan keterangan akses. Simpan contoh response sukses dan gagal melalui fitur **Examples**, kemudian gunakan **View Documentation** untuk menerbitkan dokumentasi Collection.
