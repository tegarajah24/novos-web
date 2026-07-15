<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Sales Order #{{ $order->order_number }}</title>
<style>
    @page {
        margin: 25px;
    }
    
    /* Global Styles for PDF / Print */
    body {
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-size: 10px;
        color: #000;
        line-height: 1.3;
        background: #fff;
        margin: 0;
        padding: 0;
    }
    .invoice-card {
        background: #fff;
        padding: 0;
        box-sizing: border-box;
    }
    
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .bold { font-weight: bold; }
    
    /* Company & Meta Layout */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    .header-table td {
        vertical-align: top;
        border: none;
    }
    .company-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 2px;
        color: #000;
    }
    .company-details {
        font-size: 8.5px;
        color: #333;
        line-height: 1.4;
    }
    .meta-table {
        border-collapse: collapse;
        margin-top: 5px;
    }
    .meta-table td {
        font-size: 10px;
        padding: 2px 5px;
    }
    
    /* Sales Order Title Divider */
    .title-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    .title-table td {
        vertical-align: middle;
        border: none;
    }
    .title-line {
        border-bottom: 1px solid #000;
    }
    .title-text {
        font-size: 15px;
        font-weight: bold;
        text-align: center;
        padding: 0 10px;
        white-space: nowrap;
    }
    
    /* Customer & Due Box Layout */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }
    .info-table td {
        vertical-align: top;
        border: none;
    }
    .info-box {
        border: 1px solid #000;
        padding: 8px;
        min-height: 85px;
        box-sizing: border-box;
    }
    .info-box-title {
        font-size: 9px;
        font-weight: bold;
        border-bottom: 1px solid #000;
        padding-bottom: 3px;
        margin-bottom: 5px;
        text-transform: uppercase;
    }
    .info-box-content table {
        width: 100%;
        border-collapse: collapse;
    }
    .info-box-content td {
        font-size: 9px;
        padding: 2px 0;
        vertical-align: top;
    }
    
    /* Items Table */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }
    .items-table th {
        border: 1px solid #000;
        padding: 6px;
        font-size: 9px;
        font-weight: bold;
        background-color: #fff;
    }
    .items-table td {
        border: 1px solid #000;
        padding: 6px;
        font-size: 9px;
        vertical-align: middle;
    }
    .items-table tbody tr {
        page-break-inside: avoid;
    }
    
    /* Bottom Summary & Notes Layout */
    .bottom-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    .bottom-table td {
        vertical-align: top;
        border: none;
    }
    .notes-box {
        border: 1px solid #000;
        padding: 6px;
        margin-bottom: 8px;
        min-height: 40px;
        box-sizing: border-box;
    }
    .notes-title {
        font-size: 8.5px;
        font-weight: bold;
        margin-bottom: 2px;
        text-transform: uppercase;
    }
    .notes-body {
        font-size: 8.5px;
        color: #333;
    }
    
    /* Totals Box */
    .totals-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
    }
    .totals-table td {
        padding: 5px 8px;
        font-size: 9px;
        border-bottom: 1px solid #ddd;
    }
    .totals-table tr:last-child td {
        border-bottom: none;
    }
    .totals-bg {
        background-color: #e9ecef;
        font-weight: bold;
    }
    
    /* Footer Style */
    .footer-section {
        margin-top: 30px;
        width: 100%;
        border-collapse: collapse;
    }
    .footer-section td {
        border: none;
        font-size: 8px;
        color: #555;
    }
    .footer-line {
        border-top: 1px solid #000;
        margin-top: 5px;
        padding-top: 3px;
    }

    /* Screen Styles (Browser View) */
    @media screen {
        body {
            background-color: #f3f4f6;
            padding: 2rem 1rem;
        }
        .invoice-card {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
        }
    }

    /* Mobile Screen Responsiveness */
    @media screen and (max-width: 210mm) {
        body {
            padding: 0;
            background-color: #fff;
        }
        .invoice-card {
            width: 100%;
            min-height: auto;
            padding: 15px;
            border: none;
            box-shadow: none;
        }
    }
</style>
</head>
<body>

    <div class="invoice-card">
        <!-- Header Section (Company on left, Logo & meta on right) -->
        <table class="header-table">
            <tr>
                <!-- Left Side: Company Details -->
                <td style="width: 55%;">
                    <div class="company-title">{{ $company_name }}</div>
                    <div class="company-details">
                        {{ $company_address }}<br>
                        Telp: {{ $company_phone }}<br>
                        Email: {{ $company_email }}<br>
                        {{ $company_npwp }}
                    </div>
                </td>
                <!-- Right Side: Logo & Sales Order Info -->
                <td style="width: 45%; text-align: right;">
                    <div style="margin-bottom: 10px;">
                        @if(file_exists(public_path('images/logo.png')))
                            <img src="{{ public_path('images/logo.png') }}" style="height: 35px; width: auto;" alt="Logo">
                        @else
                            <div class="company-title">{{ $company_name }}</div>
                        @endif
                    </div>
                    <table class="meta-table" style="margin-left: auto;">
                        <tr>
                            <td class="bold" style="text-align: left; padding-right: 15px;">PEMESANAN #</td>
                            <td class="bold">:</td>
                            <td class="bold" style="text-align: left;">{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left; padding-right: 15px;">TANGGAL</td>
                            <td>:</td>
                            <td style="text-align: left;">{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Title Banner with horizontal lines -->
        <table class="title-table">
            <tr>
                <td class="title-line" style="width: 35%;"></td>
                <td class="title-text" style="width: 30%;">PEMESANAN PENJUALAN</td>
                <td class="title-line" style="width: 35%;"></td>
            </tr>
        </table>

        <!-- Customer & Due Date Boxes -->
        <table class="info-table">
            <tr>
                <!-- Customer Box -->
                <td style="width: 68%; padding-right: 15px;">
                    <div class="info-box">
                        <div class="info-box-title">Pelanggan</div>
                        <div class="info-box-content">
                            <table>
                                <tr>
                                    <td style="width: 18%;" class="bold">NAMA</td>
                                    <td style="width: 5%;">:</td>
                                    <td style="width: 77%;">{{ $customer_address ? $customer_address->full_name : ($design->nama_pemesan ?? $order->user->name) }}</td>
                                </tr>
                                <tr>
                                    <td class="bold">ALAMAT</td>
                                    <td>:</td>
                                    <td>
                                        {{ $customer_address ? ($customer_address->detail_address . ', ' . $customer_address->district . ', ' . $customer_address->city . ', ' . $customer_address->province . ' ' . $customer_address->postal_code) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bold">TELP</td>
                                    <td>:</td>
                                    <td>{{ $order->user->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="bold">FAX</td>
                                    <td>:</td>
                                    <td>-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <!-- Due Date Box -->
                <td style="width: 32%;">
                    <div class="info-box" style="min-height: 87px;">
                        <div class="info-box-content" style="margin-top: 15px;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="font-size: 10px; text-align: left;" class="bold">JATUH TEMPO</td>
                                    <td style="font-size: 10px; text-align: right;" class="bold">
                                        {{ $order->created_at->copy()->addDays(\App\Models\Setting::getDeadlineDaysForPriority($order->designRequest?->priority))->format('d/m/Y') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Main Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">NO.</th>
                    <th style="width: 55%; text-align: left;">KETERANGAN</th>
                    <th style="width: 8%;" class="text-center">QTY</th>
                    <th style="width: 15%; text-align: right;">HARGA SATUAN (Rp.)</th>
                    <th style="width: 17%; text-align: right;">JUMLAH (Rp.)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grouped_items as $i => $group)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        <div class="bold">{{ $order->designRequest ? 'JERSEY CUSTOM' : 'PRODUK KATALOG' }} (SIZE {{ strtoupper($group['size']) }})</div>
                        @if($group['customizations'])
                            <div style="font-size: 8px; color: #555; margin-top: 2px;">
                                {{ implode(', ', $group['customizations']) }}
                            </div>
                        @endif
                    </td>
                    <td class="text-center">{{ $group['qty'] }} pcs</td>
                    <td class="text-right">{{ number_format($group['price'], 2, ',', '.') }}</td>
                    <td class="text-right bold">{{ number_format($group['subtotal'], 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="font-style: italic; color: #999; padding: 15px;">Belum ada rincian item pesanan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Bottom Section: Notes & Spelled-Out on left, Summary/Totals on right (Aligned column widths) -->
        <table class="bottom-table">
            <tr>
                <!-- Left Side: Message, Spelled-Out, Payment Instructions (width: 68% to align with Col 1 + 2 + 3) -->
                <td style="width: 68%; padding-right: 15px; vertical-align: top; border: none;">
                    <!-- Pesan Box -->
                    <div class="notes-box">
                        <div class="notes-title">Pesan</div>
                        <div class="notes-body">
                            <strong>Artikel:</strong> {{ $order->designRequest?->nama_artikel ?? 'Jersey Custom' }}<br>
                            <strong>Nama Tim:</strong> {{ $order->designRequest?->team_name ?? '-' }}
                            @if($order->designRequest && $order->designRequest->detail_sponsor)
                                <br><strong>Sponsor:</strong> {{ $order->designRequest->detail_sponsor }}
                            @endif
                        </div>
                    </div>
                    <!-- Terbilang Box -->
                    <div class="notes-box">
                        <div class="notes-title">Terbilang</div>
                        <div class="notes-body bold">{{ $terbilang }}</div>
                    </div>
                    <!-- Bank Info / Payment Note -->
                    <div style="font-size: 8px; color: #333; line-height: 1.4; margin-top: 5px;">
                        <span class="bold">Detail Pembayaran Rekening:</span><br>
                        Pilihan #1: BRI 6965 01 003981 53 8<br>
                        Pilihan #2: Mandiri 1380019031454<br>
                        Pilihan #3: BNI 0899192812<br>
                        <span class="bold">Minimal DP 10% Dulu Baru Akan Di Produksi.</span>
                    </div>
                </td>
                <!-- Right Side: Totals Summary (width: 32% to align with Col 4 + 5) -->
                <td style="width: 32%; vertical-align: top; border: none;">
                    <table class="totals-table">
                        <tr>
                            <td style="width: 47%; padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #ddd; border-top: none; border-left: none; border-right: none;">Subtotal</td>
                            <td style="width: 53%; padding: 5px 8px; font-size: 9px; text-align: right; border-bottom: 1px solid #ddd; border-top: none; border-left: none; border-right: none;">{{ number_format($subtotal, 2, ',', '.') }}</td>
                        </tr>
                        @if($dp_paid > 0)
                        <tr>
                            <td style="padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #ddd; border-top: none; border-left: none; border-right: none;">DP Sudah Dibayar</td>
                            <td style="padding: 5px 8px; font-size: 9px; text-align: right; border-bottom: 1px solid #ddd; border-top: none; border-left: none; border-right: none;">-{{ number_format($dp_paid, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="totals-bg">
                            <td style="border: 1px solid #000; border-right: none; padding: 6px 8px; font-size: 9px;">TOTAL</td>
                            <td style="border: 1px solid #000; border-left: none; padding: 6px 8px; font-size: 9px; text-align: right;">{{ number_format($subtotal, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="bold" style="padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #ddd; border-top: none; border-left: none; border-right: none;">Sisa Tagihan</td>
                            <td style="padding: 5px 8px; font-size: 9px; text-align: right; border-bottom: 1px solid #ddd; border-top: none; border-left: none; border-right: none;" class="bold">{{ number_format($sisa_bayar, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Footer Section -->
        <table class="footer-section">
            <tr>
                <td style="width: 50%; border-top: 1px solid #ddd; padding-top: 3px;">
                    Sales Order #{{ $order->order_number }}
                </td>
                <td style="width: 50%; text-align: right; border-top: 1px solid #ddd; padding-top: 3px;">
                    Page 1 of 1
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
