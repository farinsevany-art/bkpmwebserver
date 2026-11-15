# Panduan Upload Foto Mahasiswa

Sebelum upload, pastikan kolom `foto` sudah ada pada database yang dipakai Acara 28. Folder Acara 28 memakai database lokal yang sama; jangan jalankan migrasi lagi jika kolom tersebut sudah dibuat.

## Aturan berkas

- Hanya gambar JPG/JPEG/PNG yang ekstensi dan signature gambarnya cocok.
- Ukuran maksimal 2MB.
- Nama berkas di server dibuat acak; database hanya menyimpan nama berkas.
- Berkas tersimpan di `public/uploads/`. Folder tersebut menonaktifkan akses eksekusi untuk berkas skrip.

## Request API

Untuk menambah mahasiswa, gunakan `POST /mahasiswa`. Pada Postman pilih **Body → form-data**, isi `nim`, `nama`, `jenis_kelamin` (`L`/`P`), `id_prodi`, `angkatan`, dan `status` (`aktif`/`cuti`/`lulus`) sebagai teks. `alamat` dan `email` bersifat opsional. Isi `foto` sebagai file dan tambahkan Bearer Token admin.

Kolom identitas tabel adalah `nim`, sehingga endpoint update dan delete menggunakan NIM sebagai penanda: `PUT /mahasiswa/{nim}` untuk mengubah data tanpa foto baru, `POST /mahasiswa/{nim}` untuk mengubah data beserta foto melalui form-data, dan `DELETE /mahasiswa/{nim}` untuk menghapus. Router mengarahkan POST dengan NIM ke aksi update. Jika foto baru tidak dipilih, foto lama tetap dipakai. Jika foto baru berhasil disimpan, berkas lama dihapus setelah data berhasil diperbarui.
