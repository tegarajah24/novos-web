<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Dikonfirmasi = 'dikonfirmasi';
    case Disetujui = 'disetujui';
    case DiDesign = 'di_design';
    case SiapCetak = 'siap_cetak';
    case Diproduksi = 'diproduksi';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';
}