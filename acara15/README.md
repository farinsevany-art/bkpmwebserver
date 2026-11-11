# Acara 15 — API dan JSON

API mahasiswa mengikuti struktur MVC yang sudah ada di folder Acara 15:

- `api/mahasiswa.php` melayani akses file endpoint langsung.
- `/api/mahasiswa` diarahkan oleh `.htaccess` ke router dan controller yang sama.
- Controller memanggil model `Mahasiswa`, yang mengakses MySQL melalui PDO.

## Menjalankan

1. Jalankan Apache dan MySQL.
2. Pastikan database `si-akademik` tersedia dan pengaturan koneksinya sesuai di `config/database.php`.
3. Tabel `mahasiswa` perlu mempunyai kolom `id`, `nim`, `nama`, dan `email`. Proses POST juga memakai kolom `jenis_kelamin`, `alamat`, `id_prodi`, `angkatan`, dan `status`; nilai default disediakan untuk kolom-kolom tersebut.
4. Buka `http://localhost/minngu1/acara15/api/mahasiswa.php` atau `http://localhost/minngu1/acara15/api/mahasiswa`.

## Endpoint

| Method | URL | Keterangan |
| --- | --- | --- |
| GET | `/api/mahasiswa` | Mengambil seluruh data mahasiswa |
| GET | `/api/mahasiswa?id=1` | Mengambil data berdasarkan primary key `id` |
| GET | `/api/mahasiswa?nim=23001` | Mengambil data berdasarkan NIM |
| POST | `/api/mahasiswa` | Menambahkan mahasiswa dari JSON |

Contoh body POST:

```json
{
  "nim": "23004",
  "nama": "Dewi",
  "email": "dewi@gmail.com"
}
```

Response memakai bentuk `success`, `message`, dan `data`. POST berhasil mengembalikan HTTP 201; data yang tidak ditemukan mengembalikan HTTP 404.
