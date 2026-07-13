@extends('layouts.internal')

@section('title', 'Laporan')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Laporan</h1>
@endsection

@section('internal-content')
<div x-data="laporanApp()" x-init="init()" class="space-y-6">
    {{-- Filter Bar --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4">
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative w-36">
                <select x-model="filter" @change="applyFilter($event.target.value)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all">
                    <option value="today">Hari Ini</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="custom">Custom</option>
                </select>
            </div>

            <div x-show="filter === 'custom'" class="flex items-center gap-1.5" x-cloak>
                <div>
                    <label class="block text-[11px] text-gray-500 mb-0.5">Dari</label>
                    <input type="date" x-model="customStart"
                           class="text-xs border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] px-2 py-1.5">
                </div>
                <div>
                    <label class="block text-[11px] text-gray-500 mb-0.5">Sampai</label>
                    <input type="date" x-model="customEnd"
                           class="text-xs border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] px-2 py-1.5">
                </div>
                <button @click="applyCustomFilter()"
                        class="px-3 py-1.5 bg-[#1a237e] text-white text-xs rounded-lg hover:bg-[#283593] transition-colors mt-4">
                    Terapkan
                </button>
            </div>

            <div class="ml-auto flex gap-1.5">
                <a :href="'{{ route('staf.laporan.csv') }}?filter=' + filter + '&start_date=' + customStart + '&end_date=' + customEnd"
                   class="flex items-center gap-1 px-3 py-1.5 bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 text-xs font-medium rounded-lg transition-colors">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-100">
                        <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-blue-600"></i>
                    </span>
                    CSV
                </a>
                <a :href="'{{ route('staf.laporan.excel') }}?filter=' + filter + '&start_date=' + customStart + '&end_date=' + customEnd"
                   class="flex items-center gap-1 px-3 py-1.5 bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 text-xs font-medium rounded-lg transition-colors">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-green-100">
                        <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-green-600"></i>
                    </span>
                    Excel
                </a>
                <a :href="'{{ route('staf.laporan.pdf') }}?filter=' + filter + '&start_date=' + customStart + '&end_date=' + customEnd"
                   class="flex items-center gap-1 px-3 py-1.5 bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 text-xs font-medium rounded-lg transition-colors">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-red-100">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-red-600"></i>
                    </span>
                    PDF
                </a>
            </div>
        </div>

        <p class="text-[11px] text-gray-400 mt-2">
            <span class="inline-flex items-center gap-1">
                <i data-lucide="calendar-range" class="w-3 h-3"></i>
                Periode:
            </span>
            <strong class="text-gray-600" x-text="startDate"></strong> — <strong class="text-gray-600" x-text="endDate"></strong>
        </p>
    </div>

    {{-- Loading --}}
    <div x-show="loading" class="flex items-center justify-center py-12" x-cloak>
        <div class="flex items-center gap-3 text-gray-500">
            <div class="w-5 h-5 border-2 border-[#1a237e] border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm">Memuat data...</span>
        </div>
    </div>

    {{-- A. Ringkasan Statistik --}}
    <div x-show="!loading" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6" x-cloak>
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
                    <template x-for="(row, index) in ringkasan" :key="index">
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-3.5 text-gray-400 text-center" x-text="index + 1"></td>
                            <td class="px-6 py-3.5 font-medium text-gray-800 text-left" x-text="row[0]"></td>
                            <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]" x-text="row[1]"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- B. Statistik Produk --}}
    <div x-show="!loading" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6" x-cloak>
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
                        <td class="px-6 py-3.5 font-medium text-gray-800 text-center" x-text="produkTerbanyak.produk ?? '-'"></td>
                        <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]" x-text="fmt(produkTerbanyak.jumlah_pesanan ?? 0)"></td>
                    </tr>
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-3.5 text-gray-500">Paling Sedikit Dipesan</td>
                        <td class="px-6 py-3.5 font-medium text-gray-800 text-center" x-text="produkTersedikit.produk ?? '-'"></td>
                        <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]" x-text="fmt(produkTersedikit.jumlah_pesanan ?? 0)"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- C. Pesanan per Kategori --}}
    <div x-show="!loading" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6" x-cloak>
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
                    <template x-if="pesananPerKategori.length === 0">
                        <tr>
                            <td colspan="3" class="px-6 py-6 text-center text-gray-400 italic">Belum ada data</td>
                        </tr>
                    </template>
                    <template x-for="(row, index) in pesananPerKategori" :key="index">
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-3.5 text-gray-400" x-text="index + 1"></td>
                            <td class="px-6 py-3.5 font-medium text-gray-800" x-text="row.kategori ?? '-'"></td>
                            <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]" x-text="fmt(row.jumlah)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- D. Pesanan Diselesaikan per Admin --}}
    <div x-show="!loading" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6" x-cloak>
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
                    <template x-if="pesananPerAdmin.length === 0">
                        <tr>
                            <td colspan="3" class="px-6 py-6 text-center text-gray-400 italic">Belum ada data</td>
                        </tr>
                    </template>
                    <template x-for="(row, index) in pesananPerAdmin" :key="index">
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-3.5 text-gray-400" x-text="index + 1"></td>
                            <td class="px-6 py-3.5 font-medium text-gray-800" x-text="row.admin_name"></td>
                            <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]" x-text="fmt(row.jumlah)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- E. Pendapatan per Periode --}}
    <div x-show="!loading" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6" x-cloak>
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
                    <template x-if="pendapatanHarian.length === 0">
                        <tr>
                            <td colspan="3" class="px-6 py-6 text-center text-gray-400 italic">Belum ada data</td>
                        </tr>
                    </template>
                    <template x-for="(row, index) in pendapatanHarian" :key="index">
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-3.5 text-gray-600" x-text="fmtDate(row.tanggal)"></td>
                            <td class="px-6 py-3.5 text-center font-medium text-gray-800" x-text="fmt(row.jumlah)"></td>
                            <td class="px-6 py-3.5 text-right font-bold text-[#1a237e]" x-text="rp(row.pendapatan)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <p class="text-[11px] text-gray-400 text-center pb-2 inline-flex items-center justify-center gap-1 w-full">
        <i data-lucide="info" class="w-3 h-3"></i>
        <span>Data diperbarui secara real-time &bull; <span x-text="now"></span></span>
    </p>
</div>
<style>
    [x-cloak] { display: none !important; }
</style>
<script>
function laporanApp() {
    return {
        loading: true,
        filter: '{{ $filter ?? 'today' }}',
        customStart: '{{ \Carbon\Carbon::today()->format('Y-m-d') }}',
        customEnd: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}',
        startDate: '',
        endDate: '',
        now: '',
        _data: {},

        get data() { return this._data; },
        set data(v) { this._data = v; },

        get ringkasan() {
            const d = this.data;
            if (!d.totalPesanan && d.totalPesanan !== 0) return [];
            return [
                ['Total Pesanan', this.fmt(d.totalPesanan)],
                ['Pesanan Selesai', this.fmt(d.pesananSelesai)],
                ['Pesanan Diproses', this.fmt(d.pesananDiproses)],
                ['Pesanan Dibatalkan', this.fmt(d.pesananDibatalkan)],
                ['Pesanan Pending', this.fmt(d.pesananPending)],
                ['Total Customer Aktif', this.fmt(d.totalCustomer)],
                ['Total Pendapatan', this.rp(d.totalPendapatan)],
                ['Rata-rata Nilai Transaksi', this.rp(d.avgTransaksi)],
                ['Pesanan Terlambat Selesai', this.fmt(d.pesananTerlambat)],
                ['Total Produk Terjual', this.fmt(d.totalProdukTerjual) + ' pcs'],
                ['Rata-rata Waktu Proses', d.avgProcessingDays ? this.fmt(d.avgProcessingDays, 1) + ' hari' : '-'],
            ];
        },

        get produkTerbanyak() { return this.data.produkTerbanyak || {produk: '-', jumlah_pesanan: 0}; },
        get produkTersedikit() { return this.data.produkTersedikit || {produk: '-', jumlah_pesanan: 0}; },
        get pesananPerKategori() { return this.data.pesananPerKategori || []; },
        get pesananPerAdmin() { return this.data.pesananPerAdmin || []; },
        get pendapatanHarian() { return this.data.pendapatanHarian || []; },

        init() {
            this.fetchData();
        },

        applyFilter(key) {
            this.filter = key;
            if (key !== 'custom') this.fetchData();
        },

        applyCustomFilter() {
            this.fetchData();
        },

        fetchData() {
            this.loading = true;
            let url = '{{ route('staf.laporan.data') }}?filter=' + this.filter;
            if (this.filter === 'custom') {
                url += '&start_date=' + this.customStart + '&end_date=' + this.customEnd;
            }
            fetch(url)
                .then(r => r.json())
                .then(d => {
                    this.data = d;
                    this.startDate = d.startDate ? this.fmtDate(d.startDate) : '';
                    this.endDate = d.endDate ? this.fmtDate(d.endDate) : '';
                    this.loading = false;
                    this.updateTimestamp();
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined' && lucide.createIcons) {
                            lucide.createIcons({ icons: window.lucide.icons });
                        }
                    });
                });
        },

        updateTimestamp() {
            const now = new Date();
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            this.now = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear() + ' ' +
                String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ' WIB';
        },

        fmt(n, decimals) {
            if (n == null || isNaN(n)) n = 0;
            return Number(n).toLocaleString('id-ID', {
                minimumFractionDigits: decimals || 0,
                maximumFractionDigits: decimals || 0
            });
        },

        rp(n) {
            if (n == null || isNaN(n)) n = 0;
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        },

        fmtDate(iso) {
            if (!iso) return '-';
            const d = new Date(iso);
            if (isNaN(d.getTime())) return '-';
            const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            return String(d.getDate()).padStart(2,'0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        },
    };
}
</script>
@endsection
