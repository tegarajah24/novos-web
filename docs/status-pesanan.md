# Status Pesanan

Status disimpan di kolom `status` tabel `orders` sebagai enum.
Setiap perubahan status dicatat di tabel `order_status_histories`.

## Daftar Status

| Status | Kode | Keterangan |
|--------|------|------------|
| Menunggu Validasi | `menunggu_validasi` | Pesanan baru masuk, menunggu validasi admin |
| Menunggu Pembayaran | `menunggu_pembayaran` | Admin sudah validasi, menunggu customer bayar |
| Dikonfirmasi | `dikonfirmasi` | Pembayaran sukses dikonfirmasi |
| Disetujui | `disetujui` | Admin setujui & teruskan ke Design |
| Di Design | `di_design` | Sedang dikerjakan tim Design |
| Siap Cetak | `siap_cetak` | Design selesai, siap diprint & ke Produksi |
| Diproduksi | `diproduksi` | Sedang dikerjakan tim Produksi |
| Selesai | `selesai` | Pesanan selesai |
| Dibatalkan | `dibatalkan` | Pesanan dibatalkan |

## Alur Status

```
menunggu_validasi
  ↓ (admin validasi)
menunggu_pembayaran
  ↓ (customer ACC + bayar via Midtrans)
dikonfirmasi
  ↓ (admin teruskan ke design)
disetujui
  ↓ (admin teruskan ke design)
di_design
  ↓ (design selesai, print)
siap_cetak
  ↓ (diserahkan ke produksi)
diproduksi
  ↓ (produksi selesai)
selesai
```

Dari status manapun bisa → `dibatalkan`

## Siapa yang Bisa Ubah Status

| Dari | Ke | Siapa |
|------|----|-------|
| menunggu_validasi | menunggu_pembayaran | Admin |
| menunggu_pembayaran | dikonfirmasi | Customer (via Midtrans) |
| dikonfirmasi | disetujui / di_design | Admin |
| disetujui | di_design | Admin |
| di_design | siap_cetak | Design |
| siap_cetak | diproduksi | Admin / Design |
| diproduksi | selesai | Produksi |
| apapun | dibatalkan | Admin / Super Admin |

## Auto Chat

Setiap perubahan status otomatis mengirim pesan ke chat room:
- `menunggu_validasi` → chat ke customer: "Pesanan Anda telah dibuat..."
- `menunggu_pembayaran` → chat ke customer: "Pesanan telah divalidasi..."
- `dikonfirmasi` → chat ke admin: "Pembayaran untuk pesanan {number} telah dikonfirmasi."
