# Status Pesanan

Status disimpan di kolom `status` tabel `orders` sebagai enum.
Setiap perubahan status dicatat di tabel `order_status_histories`.

## Daftar Status

| Status | Kode | Keterangan |
|--------|------|------------|
| Menunggu Pembayaran | `menunggu_pembayaran` | Pesanan baru masuk, menunggu customer bayar DP |
| Dikonfirmasi | `dikonfirmasi` | Customer konfirmasi, menunggu admin proses |
| Disetujui | `disetujui` | Admin setujui & teruskan ke Design |
| Di Design | `di_design` | Sedang dikerjakan tim Design |
| Siap Cetak | `siap_cetak` | Design selesai, siap diprint & ke Produksi |
| Diproduksi | `diproduksi` | Sedang dikerjakan tim Produksi |
| Selesai | `selesai` | Pesanan selesai |
| Dibatalkan | `dibatalkan` | Pesanan dibatalkan |

## Alur Status

```
menunggu_pembayaran
  ↓ (customer konfirmasi)
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
| menunggu_pembayaran | dikonfirmasi | Customer |
| dikonfirmasi | disetujui / di_design | Admin |
| disetujui | di_design | Admin |
| di_design | siap_cetak | Design |
| siap_cetak | diproduksi | Admin / Design |
| diproduksi | selesai | Produksi |
| apapun | dibatalkan | Admin / Super Admin |

## Auto Chat

Setiap perubahan status otomatis mengirim pesan ke chat room:
- `menunggu_pembayaran` → chat ke customer: "Pesanan Anda telah dibuat..."
