<?php

namespace App\Enums;

enum OrderStatus: string
{
    case MenungguValidasi = 'menunggu_validasi';
    case MenungguPembayaran = 'menunggu_pembayaran';
    case Dikonfirmasi = 'dikonfirmasi';
    case Disetujui = 'disetujui';
    case DiDesign = 'di_design';
    case SiapCetak = 'siap_cetak';
    case Diproduksi = 'diproduksi';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';
}
