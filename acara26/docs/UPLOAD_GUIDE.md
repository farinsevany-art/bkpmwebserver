# Panduan Upload Foto Mahasiswa

Sebelum upload, jalankan migrasi `database/alter_mahasiswa_foto.sql` sekali pada database yang dipakai Acara 26. Pastikan kolom `foto` belum ada agar migrasi tidak dijalankan dua kali.

## Aturan berkas

- Hanya gambar JPG/JPEG/PNG yang ekstensi dan signature gambarnya cocok.
- Ukuran maksimal 2MB.
- Nama berkas di server dibuat acak; database hanya menyimpan nama berkas.
- Berkas tersimpan di `public/uploads/`. Folder tersebut menonaktifkan akses eksekusi untuk berkas skrip.

## Request API

Untuk menambah mahasiswa, gunakan `POST /mahasiswa`. Pada Postman pilih **Body → form-data**, isi `nim`, `nama`, `jenis_kelamin` (`L`/`P`), `id_prodi`, `angkatan`, dan `status` (`aktif`/`cuti`/`lulus`) sebagai teks. `alamat` dan `email` bersifat opsional. Isi `foto` sebagai file dan tambahkan Bearer Token admin.

Kolom identitas tabel adalah `nim`, sehingga endpoint update dan delete menggunakan NIM sebagai penanda: `POST /mahasiswa/{nim}` untuk form-data upload, `PUT /mahasiswa/{nim}` untuk JSON, dan `DELETE /mahasiswa/{nim}` untuk menghapus. Router mengarahkan POST dengan NIM ke aksi update. Jika foto baru tidak dipilih, foto lama tetap dipakai. Jika foto baru berhasil disimpan, berkas lama dihapus setelah data berhasil diperbarui.
