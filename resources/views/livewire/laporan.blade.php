<div class="space-y-6">
    {{-- Filter Bar --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4">
        <div class="flex flex-wrap items-center gap-2">
            <div class="flex gap-1.5">
                @foreach(['today' => 'Hari Ini', 'week' => 'Minggu Ini', 'month' => 'Bulan Ini', 'custom' => 'Custom'] as $key => $label)
                <button wire:click="applyFilter('{{ $key }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                        {{ $filter === $key ? 'bg-[#1a237e] text-white border-[#1a237e]' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            @if($filter === 'custom')
            <div class="flex items-center gap-1.5">
                <div>
                    <label class="block text-[11px] text-gray-500 mb-0.5">Dari</label>
                    <input type="date" wire:model="customStart" class="text-xs border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] px-2 py-1.5">
                </div>
                <div>
                    <label class="block text-[11px] text-gray-500 mb-0.5">Sampai</label>
                    <input type="date" wire:model="customEnd" class="text-xs border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] px-2 py-1.5">
                </div>
                <button wire:click="applyCustomFilter" class="px-3 py-1.5 bg-[#1a237e] text-white text-xs rounded-lg hover:bg-[#283593] transition-colors mt-4">Terapkan</button>
            </div>
            @endif

            <div class="ml-auto flex gap-1.5">
                <a href="{{ route('staf.laporan.csv') }}?filter={{ $filter }}&start_date={{ $customStart }}&end_date={{ $customEnd }}"
                   class="flex items-center gap-1 px-3 py-1.5 bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-medium rounded-lg transition-colors">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-100">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    CSV
                </a>
                <a href="{{ route('staf.laporan.excel') }}?filter={{ $filter }}&start_date={{ $customStart }}&end_date={{ $customEnd }}"
                   class="flex items-center gap-1 px-3 py-1.5 bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-medium rounded-lg transition-colors">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-green-100">
                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    Excel
                </a>
                <a href="{{ route('staf.laporan.pdf') }}?filter={{ $filter }}&start_date={{ $customStart }}&end_date={{ $customEnd }}"
                   class="flex items-center gap-1 px-3 py-1.5 bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-medium rounded-lg transition-colors">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-red-100">
                        <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </span>
                    PDF
                </a>
            </div>
        </div>
        <p class="text-[11px] text-gray-400 mt-2">
            <span class="inline-flex items-center gap-1">Periode:</span>
            <strong class="text-gray-600">{{ $startDate }}</strong> — <strong class="text-gray-600">{{ $endDate }}</strong>
        </p>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        @php $cardAttrs = fn($label, $value, $color) => compact('label', 'value', 'color'); @endphp
        @foreach([
            ['label' => 'Total Pesanan', 'value' => number_format($totalPesanan), 'color' => 'text-blue-600 bg-blue-50'],
            ['label' => 'Diproses', 'value' => number_format($pesananDiproses), 'color' => 'text-amber-600 bg-amber-50'],
            ['label' => 'Selesai', 'value' => number_format($pesananSelesai), 'color' => 'text-emerald-600 bg-emerald-50'],
            ['label' => 'Dibatalkan', 'value' => number_format($pesananDibatalkan), 'color' => 'text-red-600 bg-red-50'],
            ['label' => 'Pending', 'value' => number_format($pesananPending), 'color' => 'text-gray-600 bg-gray-50'],
            ['label' => 'Customer Aktif', 'value' => number_format($totalCustomer), 'color' => 'text-indigo-600 bg-indigo-50'],
            ['label' => 'Pendapatan', 'value' => 'Rp' . number_format($totalPendapatan, 0, ',', '.'), 'color' => 'text-green-600 bg-green-50'],
            ['label' => 'Rata-rata Transaksi', 'value' => 'Rp' . number_format($avgTransaksi, 0, ',', '.'), 'color' => 'text-teal-600 bg-teal-50'],
            ['label' => 'Produk Terjual', 'value' => number_format($totalProdukTerjual) . ' pcs', 'color' => 'text-purple-600 bg-purple-50'],
            ['label' => 'Terlambat', 'value' => number_format($pesananTerlambat), 'color' => 'text-orange-600 bg-orange-50'],
        ] as $card)
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-medium">{{ $card['label'] }}</p>
            <p class="text-lg font-bold mt-1 {{ explode(' ', $card['color'])[0] }}">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Produk --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h4 class="font-bold text-gray-900 text-sm mb-4">Statistik Produk</h4>
            <table class="w-full text-sm">
                <thead><tr class="text-gray-500 text-xs"><th class="text-left pb-2">Keterangan</th><th class="text-left pb-2">Produk</th><th class="text-right pb-2">Jumlah</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @if($produkTerbanyak)
                    <tr><td class="py-2 text-gray-600">Paling Banyak</td><td class="py-2 font-medium">{{ $produkTerbanyak['produk'] ?? '-' }}</td><td class="py-2 text-right font-bold">{{ $produkTerbanyak['jumlah_pesanan'] ?? 0 }}</td></tr>
                    @endif
                    @if($produkTersedikit)
                    <tr><td class="py-2 text-gray-600">Paling Sedikit</td><td class="py-2 font-medium">{{ $produkTersedikit['produk'] ?? '-' }}</td><td class="py-2 text-right font-bold">{{ $produkTersedikit['jumlah_pesanan'] ?? 0 }}</td></tr>
                    @endif
                    @if(!$produkTerbanyak)
                    <tr><td colspan="3" class="py-4 text-center text-gray-400">Belum ada data</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Kategori --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h4 class="font-bold text-gray-900 text-sm mb-4">Pesanan per Kategori</h4>
            <table class="w-full text-sm">
                <thead><tr class="text-gray-500 text-xs"><th class="text-left pb-2">Kategori</th><th class="text-right pb-2">Jumlah</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pesananPerKategori as $row)
                    <tr><td class="py-2 font-medium">{{ $row->kategori ?? '-' }}</td><td class="py-2 text-right font-bold">{{ $row->jumlah }}</td></tr>
                    @empty
                    <tr><td colspan="2" class="py-4 text-center text-gray-400">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Admin --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h4 class="font-bold text-gray-900 text-sm mb-4">Pesanan Diselesaikan per Admin</h4>
            <table class="w-full text-sm">
                <thead><tr class="text-gray-500 text-xs"><th class="text-left pb-2">Admin</th><th class="text-right pb-2">Jumlah</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pesananPerAdmin as $row)
                    <tr><td class="py-2 font-medium">{{ $row->admin_name }}</td><td class="py-2 text-right font-bold">{{ $row->jumlah }}</td></tr>
                    @empty
                    <tr><td colspan="2" class="py-4 text-center text-gray-400">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pendapatan --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h4 class="font-bold text-gray-900 text-sm mb-4">Pendapatan Harian</h4>
            <div class="max-h-60 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-gray-500 text-xs"><th class="text-left pb-2 sticky top-0 bg-white">Tanggal</th><th class="text-right pb-2 sticky top-0 bg-white">Jumlah</th><th class="text-right pb-2 sticky top-0 bg-white">Pendapatan</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pendapatanHarian as $row)
                        <tr><td class="py-2">{{ Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td><td class="py-2 text-right">{{ $row->jumlah }}</td><td class="py-2 text-right font-semibold text-green-600">Rp{{ number_format($row->pendapatan, 0, ',', '.') }}</td></tr>
                        @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="text-xs text-gray-400 text-center">Rata-rata waktu proses: <strong>{{ $avgProcessingDays ? number_format($avgProcessingDays, 1) . ' hari' : '-' }}</strong></p>
</div>
