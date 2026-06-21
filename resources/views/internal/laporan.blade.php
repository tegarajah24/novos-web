@extends('layouts.internal')

@section('title', 'Laporan')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Laporan</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
@endsection

@section('internal-content')

{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route(request()->route()?->getName() ?? 'staf.laporan') }}" id="filterForm">
        <div class="flex flex-wrap items-center gap-2">
            <div class="flex gap-1.5">
                @foreach(['today' => 'Hari Ini', 'week' => 'Minggu Ini', 'month' => 'Bulan Ini', 'custom' => 'Custom'] as $key => $label)
                    <a href="{{ $key !== 'custom' ? route(request()->route()?->getName() ?? 'staf.laporan', ['filter' => $key]) : '#' }}"
                       onclick="{{ $key === 'custom' ? "document.getElementById('customRange').classList.toggle('hidden');return false;" : '' }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                               {{ ($filter ?? 'today') === $key ? 'bg-[#1a237e] text-white border-[#1a237e]' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div id="customRange" class="{{ ($filter ?? 'today') === 'custom' ? 'flex' : 'hidden' }} items-center gap-1.5">
                <div>
                    <label class="block text-[11px] text-gray-500 mb-0.5">Dari</label>
                    <input type="date" name="start_date" value="{{ request('start_date', ($startDate ?? \Carbon\Carbon::today())->format('Y-m-d')) }}"
                           class="text-xs border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] px-2 py-1.5">
                </div>
                <div>
                    <label class="block text-[11px] text-gray-500 mb-0.5">Sampai</label>
                    <input type="date" name="end_date" value="{{ request('end_date', ($endDate ?? \Carbon\Carbon::now())->format('Y-m-d')) }}"
                           class="text-xs border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] px-2 py-1.5">
                </div>
                <input type="hidden" name="filter" value="custom">
                <button type="submit" class="px-3 py-1.5 bg-[#1a237e] text-white text-xs rounded-lg hover:bg-blue-900 transition-colors mt-4">
                    Terapkan
                </button>
            </div>

            <div class="ml-auto flex gap-1.5">
                <a href="{{ route('staf.laporan.csv', request()->query()) }}"
                   class="flex items-center gap-1 px-3 py-1.5 bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 text-xs font-medium rounded-lg transition-colors">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-100">
                        <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-blue-600"></i>
                    </span>
                    CSV
                </a>
                <a href="{{ route('staf.laporan.excel', request()->query()) }}"
                   class="flex items-center gap-1 px-3 py-1.5 bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 text-xs font-medium rounded-lg transition-colors">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-green-100">
                        <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-green-600"></i>
                    </span>
                    Excel
                </a>
                <a href="{{ route('staf.laporan.pdf', request()->query()) }}"
                   class="flex items-center gap-1 px-3 py-1.5 bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 text-xs font-medium rounded-lg transition-colors">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-red-100">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-red-600"></i>
                    </span>
                    PDF
                </a>
            </div>
        </div>

        <p class="text-[11px] text-gray-400 mt-2">
            <i data-lucide="calendar-range" class="w-3 h-3 inline-block mr-1"></i>
            Periode: <strong class="text-gray-600">{{ ($startDate ?? \Carbon\Carbon::today())->format('d M Y') }}</strong> — <strong class="text-gray-600">{{ ($endDate ?? \Carbon\Carbon::now())->format('d M Y') }}</strong>
        </p>
    </form>
</div>

{{-- A. Ringkasan Statistik --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
        <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-[#1a237e]"></i>
            A. Ringkasan Statistik
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 w-16 font-semibold text-center">No</th>
                    <th class="px-6 py-3 font-semibold text-left">Metrik</th>
                    <th class="px-6 py-3 font-semibold text-right">Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
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
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-3.5 text-gray-400 text-center">{{ $index + 1 }}</td>
                        <td class="px-6 py-3.5 font-medium text-gray-800 text-left">{{ $row[0] }}</td>
                        <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]">{{ $row[1] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- B. Statistik Produk --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
        <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="package" class="w-4 h-4 text-[#1a237e]"></i>
            B. Statistik Produk
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 w-1/3 font-semibold text-left">Keterangan</th>
                    <th class="px-6 py-3 font-semibold text-center">Produk</th>
                    <th class="px-6 py-3 font-semibold text-right">Jumlah Pesanan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-3.5 text-gray-500">Paling Banyak Dipesan</td>
                    <td class="px-6 py-3.5 font-medium text-gray-800 text-center">{{ $produkTerbanyak->produk ?? '-' }}</td>
                    <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]">{{ $produkTerbanyak->jumlah_pesanan ?? 0 }}</td>
                </tr>
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-3.5 text-gray-500">Paling Sedikit Dipesan</td>
                    <td class="px-6 py-3.5 font-medium text-gray-800 text-center">{{ $produkTersedikit->produk ?? '-' }}</td>
                    <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]">{{ $produkTersedikit->jumlah_pesanan ?? 0 }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- C. Pesanan per Kategori --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
        <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="layers" class="w-4 h-4 text-[#1a237e]"></i>
            C. Pesanan per Kategori
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 w-16 font-semibold">No</th>
                    <th class="px-6 py-3 font-semibold">Kategori</th>
                    <th class="px-6 py-3 text-right font-semibold">Jumlah Pesanan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pesananPerKategori as $index => $kat)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-3.5 text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-3.5 font-medium text-gray-800">{{ $kat->kategori ?? '-' }}</td>
                        <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]">{{ $kat->jumlah }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-6 text-center text-gray-400 italic">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- D. Pesanan Diselesaikan per Admin --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
        <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="user-check" class="w-4 h-4 text-[#1a237e]"></i>
            D. Pesanan Diselesaikan per Admin
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 w-16 font-semibold">No</th>
                    <th class="px-6 py-3 font-semibold">Nama Admin</th>
                    <th class="px-6 py-3 text-right font-semibold">Jumlah Pesanan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pesananPerAdmin as $index => $admin)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-3.5 text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-3.5 font-medium text-gray-800">{{ $admin->admin_name }}</td>
                        <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]">{{ $admin->jumlah }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-6 text-center text-gray-400 italic">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- E. Pendapatan per Periode --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
        <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="trending-up" class="w-4 h-4 text-[#1a237e]"></i>
            E. Pendapatan per Periode
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 font-semibold">Tanggal</th>
                    <th class="px-6 py-3 text-center font-semibold">Jumlah Pesanan</th>
                    <th class="px-6 py-3 text-right font-semibold">Pendapatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pendapatanHarian as $index => $pendapatan)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-3.5 text-gray-600">{{ \Carbon\Carbon::parse($pendapatan->tanggal)->format('d-m-Y') }}</td>
                        <td class="px-6 py-3.5 text-center font-medium text-gray-800">{{ $pendapatan->jumlah }}</td>
                        <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]">Rp {{ number_format($pendapatan->pendapatan, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-6 text-center text-gray-400 italic">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<p class="text-[11px] text-gray-400 text-center pb-2">
    <i data-lucide="info" class="w-3 h-3 inline-block mr-1"></i>
    Data diperbarui secara real-time &bull; {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB
</p>

@endsection
