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
        color: #000;
        background: #fff;
        padding: 32px;
    }

    /* ── HEADER ── */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 3px solid #000;
        padding-bottom: 16px;
        margin-bottom: 16px;
    }
    .brand-name {
        font-size: 22px;
        font-weight: 700;
        color: #000;
        letter-spacing: 1px;
    }
    .brand-tagline {
        font-size: 10px;
        color: #555;
        margin-top: 2px;
    }
    .invoice-meta {
        text-align: right;
    }
    .invoice-meta .label {
        font-size: 22px;
        font-weight: 700;
        color: #000;
        letter-spacing: 2px;
    }
    .invoice-meta .number {
        font-size: 12px;
        font-weight: 600;
        color: #333;
        margin-top: 4px;
    }
    .invoice-meta .date {
        font-size: 10px;
        color: #555;
        margin-top: 2px;
    }

    /* ── TABLE ── */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }
    table thead th {
        background: #fff;
        color: #000;
        padding: 6px 10px;
        font-size: 9px;
        font-weight: 600;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: 2px solid #000;
    }
    table thead th:last-child { text-align: right; }
    table tbody tr:nth-child(odd) td { background: #f5f5f5; }
    table tbody tr:nth-child(even) td { background: #fff; }
    table tbody td {
        padding: 6px 10px;
        font-size: 10px;
        color: #333;
    }
    table tbody td:last-child { text-align: right; font-weight: 600; }
    .empty-row td {
        text-align: center;
        color: #888;
        padding: 16px;
        font-style: italic;
    }

    /* ── SUMMARY ── */
    .summary-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 16px;
    }
    .summary-box {
        width: 260px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 14px;
        font-size: 10px;
        border-bottom: 1px solid #eee;
    }
    .summary-row:last-child { border-bottom: none; }
    .summary-row .s-label { color: #555; }
    .summary-row .s-value { font-weight: 600; color: #000; }
    .summary-row.dp-row .s-value { color: #000; }
    .summary-row.total-final {
        background: #fff;
        border-top: 2px solid #000;
        padding: 12px 14px;
    }
    .summary-row.total-final .s-label { color: #000; font-weight: 700; font-size: 12px; }
    .summary-row.total-final .s-value { color: #000; font-weight: 800; font-size: 13px; }

    /* ── DISCLAIMER ── */
    .disclaimer {
        border-top: 1px solid #ccc;
        padding-top: 8px;
        margin-bottom: 16px;
        font-size: 10px;
        color: #555;
    }
    @media screen {
        html { overflow-x: auto; }
        body {
            width: 210mm;
            margin: 0 auto;
        }
    }

    @media print {
        @page { margin: 8mm; }
        body {
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    .bank-line {
        font-size: 11px;
        font-weight: 700;
        color: #000;
    }

    /* ── FOOTER ── */
    .footer {
        border-top: 1px solid #ddd;
        padding-top: 8px;
    }
    .footer-left {
        font-size: 9px;
        color: #888;
        line-height: 1.6;
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
            <div class="brand-tagline" style="margin-top:4px">{{ $company_phone }}</div>
            @endif
            <div class="brand-tagline" style="margin-top:4px">Invoice Date: {{ now()->format('d F Y') }}</div>
        </div>
        <div class="invoice-meta">
            <div class="label">FAKTUR</div>
            <div class="number">{{ $order->order_number }}</div>
            <div class="date">Tanggal: {{ $order->created_at->format('d F Y') }}</div>
        </div>
    </div>

    {{-- ── INFO PESANAN ── --}}
    <div style="margin-bottom:16px; font-size:11px; color:#333; line-height:1.8">
        @if($design && $design->nama_pemesan)
        <span><strong>Pemesan:</strong> {{ $design->nama_pemesan }}</span><br>
        @endif
        @if($design && $design->team_name)
        <span><strong>Tim:</strong> {{ $design->team_name }}</span>
        @endif
    </div>

    {{-- ── TABEL RINCIAN PESANAN ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:8%">Ukuran</th>
                <th style="width:32%">Atribut</th>
                <th style="width:8%; text-align:center">Qty</th>
                <th style="width:15%; text-align:right">Harga/pcs</th>
                <th style="width:18%; text-align:right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $maxRows = 22; @endphp
            @forelse($grouped_items as $i => $group)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ strtoupper($group['size']) }}</td>
                <td>
                    @if($group['customizations'])
                        {{ implode(', ', $group['customizations']) }}
                    @else
                        <span style="color:#999">-</span>
                    @endif
                </td>
                <td style="text-align:center">{{ $group['qty'] }}</td>
                <td style="text-align:right">Rp {{ number_format($group['price'], 0, ',', '.') }}</td>
                <td style="text-align:right">Rp {{ number_format($group['subtotal'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr class="empty-row">
                <td colspan="6">Belum ada rincian item pesanan</td>
            </tr>
            @endforelse
            @if($grouped_items->count() > 0)
            @for($e = $grouped_items->count(); $e < $maxRows; $e++)
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor
            @endif
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
    <div class="disclaimer">Perhatian — Harga Belum Termasuk Ongkos Kirim</div>

    {{-- ── DETAIL PEMBAYARAN ── --}}
    <table class="payment-info">
        <thead>
            <tr>
                <th colspan="2" style="border-bottom: 2px solid #000; background: #fff; color: #000; text-transform: uppercase; letter-spacing: 0.6px; font-size: 11px; text-align: left;">Detail Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background: #fff;">
                <td style="padding: 6px 0; font-size: 10px; color: #000; line-height: 1.6; text-align: left; font-weight: 700; vertical-align: top; width: 70%;">
                    Pilihan #1: BRI 6965 01 003981 53 8<br>
                    Pilihan #2: Mandiri 1380019031454<br>
                    Pilihan #3: BNI 0899192812<br>
                    <br>
                    Minimal DP 10% Dulu Baru Akan Di Produksi
                </td>
                <td style="padding: 6px 0; font-size: 10px; color: #000; line-height: 1.6; text-align: left; font-weight: 700; vertical-align: top;">
                    @if($company_instagram)
                        Instagram : {{ '@' . ltrim($company_instagram, '@') }}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-left">
            Dokumen ini diterbitkan secara digital oleh sistem {{ $company_name }}.
        </div>

    </div>

</body>
</html>
