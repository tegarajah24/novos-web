# Business Rules

## User

1. User wajib login sebelum melakukan pemesanan.
2. Email user harus unik.
3. Password minimal 8 karakter.

---

## Produk

1. Produk harus memiliki kategori.
2. Produk harus memiliki harga.
3. Produk dapat dinonaktifkan tanpa dihapus.

---

## Pemesanan

1. Customer wajib mengisi data pemesanan.
2. Customer dapat mengunggah desain sendiri.
3. Customer dapat menambahkan catatan pesanan.
4. Pesanan tidak dapat diubah setelah pembayaran berhasil.

---

## Pembayaran

1. Pembayaran dilakukan melalui Midtrans.
2. Status pembayaran diperbarui otomatis melalui callback.
3. Pesanan diproses setelah pembayaran berhasil.

---

## Status Pesanan

Urutan status:

Pending
↓
Menunggu Pembayaran
↓
Diproses
↓
Dicetak
↓
Dikirim
↓
Selesai

Status Dibatalkan dapat terjadi sebelum proses produksi dimulai.

---

## Chat

1. Customer hanya dapat chat dengan Admin.
2. Riwayat chat disimpan dalam database.

---

## Upload Desain

1. Format file:

   * JPG
   * JPEG
   * PNG
   * PDF

2. Maksimal ukuran file:

   * 10 MB

3. File tersimpan pada storage aplikasi.

---

## Hak Akses

Customer:

* Melihat Produk
* Melakukan Pemesanan
* Melihat Tracking
* Chat Admin

Pegawai:

* Melihat Pesanan
* Mengubah Status Produksi

Admin:

* Akses Penuh Sistem
