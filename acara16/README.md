# Acara 16 — RESTful API Mahasiswa

Endpoint API memakai PHP, PDO, MySQL, dan JSON. Letakkan folder Acara 16 di web root Apache, aktifkan `mod_rewrite`, lalu sesuaikan koneksi database di `config/database.php`.

File `.htaccess` mengarahkan `/api/mahasiswa` dan `/api/mahasiswa/{id}` ke endpoint yang sama di `api/mahasiswa.php`. Jika `mod_rewrite` tidak tersedia, endpoint file langsung tetap dapat diuji dengan `api/mahasiswa.php`; detail mahasiswa bisa diakses dengan `?id=1`.

## Endpoint

| Method | URL | Keterangan |
| --- | --- | --- |
| GET | `/api/mahasiswa` | Mengambil seluruh data mahasiswa |
| GET | `/api/mahasiswa/1` | Mengambil mahasiswa berdasarkan primary key `id` |
| POST | `/api/mahasiswa` | Menambahkan mahasiswa |
| PUT | `/api/mahasiswa/1` | Mengubah mahasiswa berdasarkan primary key `id` |
| DELETE | `/api/mahasiswa/1` | Menghapus mahasiswa berdasarkan primary key `id` |

GET juga mendukung pencarian berdasarkan NIM dengan `/api/mahasiswa?nim=23001`.

## Body JSON untuk POST dan PUT

NIM, nama, dan email wajib dikirim. PUT mengganti nilai ketiganya; data opsional yang tidak dikirim tetap memakai nilai tersimpan (saat PUT) atau nilai default (saat POST).

```json
{
  "nim": "23005",
  "nama": "Rina",
  "email": "rina@gmail.com"
}
```

Tabel `mahasiswa` perlu mempunyai kolom `id`, `nim`, `nama`, `email`, `jenis_kelamin`, `alamat`, `id_prodi`, `angkatan`, dan `status`. Untuk kolom tambahan, POST memakai nilai default bila tidak diberikan.

## Status dan pengujian

- `200`: GET, PUT, atau DELETE berhasil.
- `201`: mahasiswa berhasil dibuat.
- `400`: JSON tidak valid/tidak lengkap, email tidak valid, ID tidak valid, atau data duplikat.
- `404`: mahasiswa tidak ditemukan.
- `405`: HTTP method tidak didukung.
- `500`: kesalahan server/database; detail teknis tidak ditampilkan dalam respons.

Impor `postman/Acara-16-RESTful-API.postman_collection.json` ke Postman. Ganti variabel collection `baseUrl` jika alamat project berbeda. Request PUT dan DELETE memakai variabel `mahasiswaId` (default `1`).
