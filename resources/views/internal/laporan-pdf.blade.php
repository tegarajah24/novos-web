<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Novos - {{ $startDate->format('d M Y') }}</title>
    <style>
        * { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; box-sizing: border-box; }
        body { padding: 30px; color: #111; background: #fff; }
        
        .no-print { margin-bottom: 20px; }
        .no-print button { padding: 8px 16px; background: #1a237e; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .no-print button.close-btn { background: #6b7280; margin-left: 8px; }
        
        .header { margin-bottom: 30px; border-bottom: 2px solid #1a237e; padding-bottom: 10px; }
        .header h1 { font-size: 18px; color: #1a237e; margin-bottom: 4px; }
        .header p { color: #555; }

        .section-title { font-size: 13px; font-weight: bold; color: #111; margin-bottom: 8px; margin-top: 25px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { border-bottom: 1px solid #ccc; text-align: left; padding: 8px 4px; font-weight: bold; }
        td { border-bottom: 1px solid #eee; text-align: left; padding: 8px 4px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .w-16 { width: 40px; }

        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #888; border-top: 1px solid #eee; padding-top: 10px; }

        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">🖨️ Cetak / Save PDF</button>
    <button class="close-btn" onclick="window.close()">✕ Tutup</button>
</div>

<div class="header">
    <h1>Laporan Novos Konveksi</h1>
    <p>Periode: <strong>{{ $startDate->format('d M Y') }}</strong> s/d <strong>{{ $endDate->format('d M Y') }}</strong></p>
</div>

{{-- A. Ringkasan Statistik --}}
<div class="section-title">A. Ringkasan Statistik</div>
<table>
    <thead>
        <tr>
            <th class="w-16">No</th>
            <th>Metrik</th>
            <th class="text-right">Nilai</th>
        </tr>
    </thead>
    <tbody>
        @php
            $ringkasan = [
                ['Total Pesanan', number_format($totalPesanan)],
                ['Pesanan Selesai', number_format($pesananSelesai)],
                ['Pesanan Diproses', number_format($pesananDiproses)],
                ['Pesanan Dibatalkan', number_format($pesananDibatalkan)],
                ['Pesanan Pending', number_format($pesananPending)],
                ['Total Customer Aktif', number_format($totalCustomer)],
                ['Total Pendapatan', 'Rp ' . number_format($totalPendapatan, 0, ',', '.')],
                ['Rata-rata Nilai Transaksi', 'Rp ' . number_format($avgTransaksi, 0, ',', '.')],
                ['Pesanan Terlambat Selesai', number_format($pesananTerlambat)],
                ['Total Produk Terjual', number_format($totalProdukTerjual) . ' pcs'],
                ['Rata-rata Waktu Proses', $avgProcessingDays ? number_format($avgProcessingDays, 1) . ' hari' : '-'],
            ];
        @endphp
        @foreach($ringkasan as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row[0] }}</td>
                <td class="text-right"><strong>{{ $row[1] }}</strong></td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- B. Statistik Produk --}}
<div class="section-title">B. Statistik Produk</div>
<table>
    <thead>
        <tr>
            <th>Keterangan</th>
            <th>Produk</th>
            <th class="text-right">Jumlah Pesanan</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Paling Banyak Dipesan</td>
            <td>{{ $produkTerbanyak->produk ?? '-' }}</td>
            <td class="text-right"><strong>{{ $produkTerbanyak->jumlah_pesanan ?? 0 }}</strong></td>
        </tr>
        <tr>
            <td>Paling Sedikit Dipesan</td>
            <td>{{ $produkTersedikit->produk ?? '-' }}</td>
            <td class="text-right"><strong>{{ $produkTersedikit->jumlah_pesanan ?? 0 }}</strong></td>
        </tr>
    </tbody>
</table>

{{-- C. Pesanan per Kategori --}}
<div class="section-title">C. Pesanan per Kategori</div>
<table>
    <thead>
        <tr>
            <th class="w-16">No</th>
            <th>Kategori</th>
            <th class="text-right">Jumlah Pesanan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pesananPerKategori as $index => $kat)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $kat->kategori ?? '-' }}</td>
                <td class="text-right"><strong>{{ $kat->jumlah }}</strong></td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center" style="color: #888;">Belum ada data</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- D. Pesanan Diselesaikan per Admin --}}
<div class="section-title">D. Pesanan Diselesaikan per Admin</div>
<table>
    <thead>
        <tr>
            <th class="w-16">No</th>
            <th>Nama Admin</th>
            <th class="text-right">Jumlah Pesanan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pesananPerAdmin as $index => $admin)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $admin->admin_name }}</td>
                <td class="text-right"><strong>{{ $admin->jumlah }}</strong></td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center" style="color: #888;">Belum ada data</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- E. Pendapatan per Periode --}}
<div class="section-title">E. Pendapatan per Periode</div>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th class="text-center">Jumlah Pesanan</th>
            <th class="text-right">Pendapatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pendapatanHarian as $pendapatan)
            <tr>
                <td>{{ \Carbon\Carbon::parse($pendapatan->tanggal)->format('d-m-Y') }}</td>
                <td class="text-center">{{ $pendapatan->jumlah }}</td>
                <td class="text-right"><strong>Rp {{ number_format($pendapatan->pendapatan, 0, ',', '.') }}</strong></td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center" style="color: #888;">Belum ada data</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Dihasilkan otomatis oleh sistem Novos &bull; {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} WIB
</div>

</body>
</html>
