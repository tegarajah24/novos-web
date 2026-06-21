# Flow Pesanan Novos

## Flow Lengkap

```
[CUSTOMER]
Isi form pesanan:
- Identitas (nama, kontak)
- Detail desain (nama tim, logo, warna, motif, bahan, bentuk kerah)
- Ukuran & jumlah
- Catatan tambahan
  ↓
Pesanan tersimpan → status: MENUNGGU_VALIDASI
  ↓ chat otomatis ke customer: "Pesanan Anda telah dibuat dan menunggu validasi admin."

[ADMIN]
Notifikasi pesanan baru masuk
  ↓
Admin cek kelengkapan & validasi pesanan
  ↓
Admin validasi → status: MENUNGGU_PEMBAYARAN
  ↓ chat otomatis ke customer: "Pesanan telah divalidasi. Silakan lakukan pembayaran."

[CUSTOMER]
Customer buka Profil → lihat tombol "Setujui Detail & Bayar Sekarang"
  ↓
Klik tombol → ACC detail pesanan + Midtrans payment popup
  ↓
Bayar sukses → status: DIKONFIRMASI
  ↓ chat otomatis ke admin: "Pembayaran untuk pesanan {number} telah dikonfirmasi."

[ADMIN]
Admin teruskan ke tim Design → status: DISETUJUI / DI_DESIGN

[DESIGN]
Tim Design menerima detail pesanan & desain
  ↓
Design selesai → status: SIAP_CETAK

[PRODUKSI]
Produksi menerima tugas → status: DIPRODUKSI
  ↓
Kerjakan pesanan (Printing → Jahit → QC)
  ↓
Selesai → status: SELESAI

[CUSTOMER]
Customer bisa tracking status pesanan & chat kapan saja
```

## Catatan Penting

- Setiap perubahan status dicatat di `order_status_histories`
- Customer bisa chat dengan admin di setiap tahap pesanan
- Chat terikat ke pesanan (`order_id`), bukan chat umum
- Pembayaran via Midtrans — dilakukan setelah admin validasi
- Auto chat notification dikirim ke chat room terkait setiap perubahan status
