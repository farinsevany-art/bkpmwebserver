# Pengujian CRUD Acara 28

Dashboard: `http://localhost/minngu1/acara28/public/views/login.html`

1. Login sebagai admin dan gunakan satu data uji, `id_prodi` yang sudah ada, serta JPG/PNG berukuran maksimal 2MB.
2. Tambahkan data. Pastikan baris dan foto tampil tanpa memuat ulang seluruh halaman, lalu periksa notifikasi sukses.
3. Ubah data tanpa mengganti foto. Pastikan perubahan tampil dan foto lama tetap ada.
4. Ubah data dengan foto baru. Pastikan foto baru tampil dan notifikasi sukses muncul.
5. Uji validasi: kosongkan field wajib, masukkan email tidak valid, atau pilih foto lebih dari 2MB. Form harus menolak pengiriman dan menampilkan validasi.
6. Hapus hanya data uji. Pastikan baris hilang dan notifikasi sukses muncul.
7. Login sebagai user dan pastikan tombol CRUD tidak ditampilkan. Backend tetap membatasi operasi mutasi untuk admin.

Jika sebuah operasi gagal, dashboard menampilkan notifikasi error dari API. Hapus data uji setelah pengujian selesai.
