# Catatan Client — Novos

## Profil Bisnis
- **Nama**: Novos
- **Bisnis**: Custom jersey / konveksi
- **Web**: Sistem pemesanan custom jersey online

## Yang Sudah Dikonfirmasi Client

- Hanya melayani **custom order** (bukan produk katalog siap pakai)
- Pembayaran menggunakan **Midtrans**
- Ada fitur **Daily Mental Check & Micro Break** untuk menjaga kesehatan mental staf pengrajin jersi
- Form desain berisi: identitas, logo, warna, motif, bahan, bentuk kerah

## Yang Telah Diputuskan

- [x] Pembayaran dilakukan **setelah** customer ACC pesanan (bukan saat pesan pertama)
- [x] Notifikasi via **in-app notification**, bukan email
- [x] Pembatalan pesanan hanya oleh **Admin / Super Admin**, bukan oleh customer
- [x] Format nomor pesanan: **NVS-YYYYMMDD-XXX** (contoh: NVS-20240601-001)
- [x] Harga diinput **manual oleh Admin** per pesanan (base price Rp 85.000/pcs)
- [x] Multi ukuran dalam satu pesanan **sudah didukung** (S, M, L, XL, XXL, XXXL)

## Catatan Teknis

- Stack: Laravel + Blade + Tailwind + DaisyUI
- Auth: Laravel Breeze
- Payment: Midtrans
- Pengolahan gambar menggunakan fungsi native bawaan Laravel Storage, dan Dashboard Admin/Staf dibangun kustom menggunakan Blade + Alpine.js (tanpa Filament/Livewire)
- Database: MySQL via Laragon (lokal), migrasi ke server saat deploy

## Prioritas Pengembangan

1. Auth & Role (login semua role)
2. Form pesanan customer
3. Dashboard & kelola pesanan admin
4. Flow status pesanan lengkap
5. Chat per pesanan
6. Pembayaran Midtrans
7. Produksi & Design view
8. Stress Test
9. Laporan
10. Deploy
