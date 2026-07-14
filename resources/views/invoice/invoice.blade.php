<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice {{ $order->order_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        font-size: 12px;
        color: #1a1a2e;
        background: #fff;
        padding: 32px;
    }

    /* ── HEADER ── */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 3px solid #1a237e;
        padding-bottom: 20px;
        margin-bottom: 24px;
    }
    .brand-name {
        font-size: 26px;
        font-weight: 700;
        color: #1a237e;
        letter-spacing: 1px;
    }
    .brand-tagline {
        font-size: 10px;
        color: #6b7280;
        margin-top: 2px;
    }
    .invoice-meta {
        text-align: right;
    }
    .invoice-meta .label {
        font-size: 22px;
        font-weight: 700;
        color: #1a237e;
        letter-spacing: 2px;
    }
    .invoice-meta .number {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        margin-top: 4px;
    }
    .invoice-meta .date {
        font-size: 10px;
        color: #6b7280;
        margin-top: 2px;
    }

    /* ── INFO GRID ── */
    .info-grid {
        display: flex;
        gap: 20px;
        margin-bottom: 24px;
    }
    .info-box {
        flex: 1;
        background: #f8f9ff;
        border: 1px solid #e5e7f0;
        border-radius: 8px;
        padding: 14px 16px;
    }
    .info-box-title {
        font-size: 9px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 8px;
    }
    .info-row {
        font-size: 11px;
        color: #374151;
        margin-bottom: 3px;
    }
    .info-row strong {
        color: #111827;
    }

    /* ── TABLE ── */
    .section-title {
        font-size: 11px;
        font-weight: 700;
        color: #1a237e;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 10px;
        border-left: 3px solid #1a237e;
        padding-left: 8px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
    }
    table thead th {
        background: #1a237e;
        color: #fff;
        padding: 9px 10px;
        font-size: 10px;
        font-weight: 600;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    table thead th:last-child { text-align: right; }
    table tbody tr:nth-child(odd) td { background: #f8f9ff; }
    table tbody tr:nth-child(even) td { background: #fff; }
    table tbody td {
        padding: 8px 10px;
        font-size: 11px;
        color: #374151;
        border-bottom: 1px solid #e9ecf5;
    }
    table tbody td:last-child { text-align: right; font-weight: 600; }
    .customization-tag {
        display: inline-block;
        font-size: 9px;
        background: #e8eaf6;
        color: #3949ab;
        padding: 1px 5px;
        border-radius: 3px;
        margin: 1px 2px 1px 0;
    }
    .empty-row td {
        text-align: center;
        color: #9ca3af;
        padding: 16px;
        font-style: italic;
    }

    /* ── SUMMARY ── */
    .summary-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 24px;
    }
    .summary-box {
        width: 260px;
        border: 1px solid #e5e7f0;
        border-radius: 8px;
        overflow: hidden;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 9px 14px;
        font-size: 11px;
        border-bottom: 1px solid #f1f3f9;
    }
    .summary-row:last-child { border-bottom: none; }
    .summary-row .s-label { color: #6b7280; }
    .summary-row .s-value { font-weight: 600; color: #111827; }
    .summary-row.dp-row .s-value { color: #16a34a; }
    .summary-row.total-final {
        background: #1a237e;
        padding: 12px 14px;
    }
    .summary-row.total-final .s-label { color: #c7d2fe; font-weight: 700; font-size: 12px; }
    .summary-row.total-final .s-value { color: #fff; font-weight: 800; font-size: 13px; }

    /* ── DISCLAIMER ── */
    .disclaimer {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 24px;
    }
    .disclaimer-title {
        font-size: 10px;
        font-weight: 700;
        color: #92400e;
        margin-bottom: 4px;
    }
    .disclaimer p {
        font-size: 10px;
        color: #78350f;
        line-height: 1.5;
    }

    .bank-line {
        font-size: 11px;
        font-weight: 700;
        color: #111827;
    }

    /* ── FOOTER ── */
    .footer {
        border-top: 1px solid #e5e7f0;
        padding-top: 16px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .footer-left {
        font-size: 9px;
        color: #9ca3af;
        line-height: 1.6;
    }
    .footer-right {
        text-align: center;
    }
    .ttd-box {
        width: 120px;
        border-top: 1px solid #374151;
        padding-top: 4px;
        font-size: 9px;
        color: #6b7280;
        text-align: center;
    }
    .ttd-label {
        font-size: 9px;
        color: #9ca3af;
        margin-bottom: 40px;
    }
</style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="header">
        <div>
            <div class="brand-name">{{ $company_name }}</div>
            <div class="brand-tagline">Konveksi & Custom Jersey</div>
            @if($company_phone)
            <div class="brand-tagline" style="margin-top:4px">📱 {{ $company_phone }}</div>
            @endif
        </div>
        <div class="invoice-meta">
            <div class="label">FAKTUR</div>
            <div class="number">{{ $order->order_number }}</div>
            <div class="date">Tanggal: {{ $order->created_at->format('d F Y') }}</div>
        </div>
    </div>

    {{-- ── INFO CUSTOMER + ORDER ── --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-box-title">Informasi Customer</div>
            <div class="info-row"><strong>{{ $order->user->fullname ?? $order->user->name }}</strong></div>
            @if($order->user->phone)
            <div class="info-row">📱 {{ $order->user->phone }}</div>
            @endif
            @if($order->user->email)
            <div class="info-row">✉ {{ $order->user->email }}</div>
            @endif
        </div>
        <div class="info-box">
            <div class="info-box-title">Detail Pesanan</div>
            @if($design && $design->team_name)
            <div class="info-row"><strong>Nama Tim:</strong> {{ $design->team_name }}</div>
            @endif
            @if($design && $design->nama_artikel)
            <div class="info-row"><strong>Nama Artikel:</strong> {{ $design->nama_artikel }}</div>
            @endif
            @if($design && $design->nama_pemesan)
            <div class="info-row"><strong>Nama Pemesan:</strong> {{ $design->nama_pemesan }}</div>
            @endif
            <div class="info-row"><strong>Status:</strong>
                {{ match($order->status) {
                    'menunggu_pembayaran' => 'Menunggu Pembayaran DP',
                    'dikonfirmasi'        => 'Dikonfirmasi',
                    'disetujui'           => 'Disetujui',
                    'di_design'           => 'Tahap Desain',
                    'siap_cetak'          => 'Siap Cetak',
                    'diproduksi'          => 'Diproduksi',
                    'selesai'             => 'Selesai',
                    default               => $order->status,
                } }}
            </div>
        </div>
    </div>

    {{-- ── TABEL RINCIAN ITEM ── --}}
    <div class="section-title">Rincian Pesanan</div>
    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:20%">Nama</th>
                <th style="width:8%">No.</th>
                <th style="width:8%">Ukuran</th>
                <th style="width:35%">Kustomisasi</th>
                <th style="width:12%; text-align:right">Harga/pcs</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->nama_punggung ?: '-' }}</td>
                <td>{{ $item->no_punggung ?: '-' }}</td>
                <td>{{ strtoupper($item->size) }}</td>
                <td>
                    @if($item->customizations)
                        @foreach($item->customizations as $key => $val)
                            @if($val && !in_array($key, ['tipe_bawahan', 'size_bawahan']))
                            <span class="customization-tag">{{ $val }}</span>
                            @endif
                        @endforeach
                        @if(!empty($item->customizations['tipe_bawahan']))
                        <span class="customization-tag">Bawahan: {{ $item->customizations['tipe_bawahan'] }}</span>
                        @endif
                        @if(!empty($item->customizations['size_bawahan']))
                        <span class="customization-tag">Ukuran Bawahan: {{ strtoupper($item->customizations['size_bawahan']) }}</span>
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
            </tr>
            @empty
            {{-- Fallback jika tidak ada item detail, tampilkan dari order_items --}}
            @foreach($order->orderItems as $i => $oi)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td colspan="3">Jersey Custom</td>
                <td>{{ $oi->qty }} pcs</td>
                <td>Rp {{ number_format($oi->price_per_item, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @if($order->orderItems->isEmpty())
            <tr class="empty-row">
                <td colspan="6">Belum ada rincian item pesanan</td>
            </tr>
            @endif
            @endforelse
        </tbody>
    </table>

    {{-- ── SUMMARY HARGA ── --}}
    <div class="summary-wrapper">
        <div class="summary-box">
            <div class="summary-row">
                <span class="s-label">Subtotal Jersey</span>
                <span class="s-value">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            @if($dp_paid > 0)
            <div class="summary-row dp-row">
                <span class="s-label">DP Sudah Dibayar</span>
                <span class="s-value">− Rp {{ number_format($dp_paid, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="summary-row total-final">
                <span class="s-label">{{ $dp_paid > 0 ? 'Sisa yang Harus Dibayar' : 'Total Tagihan' }}</span>
                <span class="s-value">Rp {{ number_format($sisa_bayar > 0 ? $sisa_bayar : $subtotal, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- ── DISCLAIMER ONGKIR ── --}}
    <div class="disclaimer">
        <div class="disclaimer-title">⚠ Perhatian — Harga Belum Termasuk Ongkos Kirim</div>
        <p>
            Total tagihan di atas merupakan biaya produksi jersey. Ongkos kirim akan ditentukan setelah pesanan selesai diproduksi
            berdasarkan berat paket dan lokasi pengiriman, dan akan dikomunikasikan oleh tim kami via WhatsApp.
        </p>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-left">
            Dokumen ini diterbitkan secara digital oleh sistem {{ $company_name }}.<br>
            Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB
        </div>
        <div class="footer-right">
            <div class="ttd-label">Hormat kami,</div>
            <div class="ttd-box">{{ $company_name }}</div>
        </div>
    </div>

</body>
</html>
