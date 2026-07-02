<div>
    @php
    function statusLabel($s) {
        return match($s) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi', 'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'tahap_desain' => 'Tahap Desain', 'menunggu_acc' => 'Menunggu ACC',
            'tahap_produksi' => 'Produksi', 'selesai' => 'Selesai',
            default => ucwords(str_replace('_', ' ', $s)),
        };
    }
    function statusBadgeType($s) {
        return match($s) {
            'menunggu_verifikasi' => 'yellow', 'menunggu_pembayaran' => 'orange',
            'tahap_desain' => 'blue', 'menunggu_acc' => 'orange',
            'tahap_produksi' => 'purple', 'selesai' => 'green',
            default => 'gray',
        };
    }
    @endphp

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 {{ $isSAOManager ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-6 mb-8">

        @if($isDesign)
        <a href="{{ route('staf.daftar-pesanan') }}?status=dikonfirmasi" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-purple-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></div></div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $designWaiting }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Menunggu Desain</p>
        </a>
        <a href="{{ route('staf.daftar-pesanan') }}?status=di_design" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-blue-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div></div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $designInProgress }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Sedang Di Desain</p>
        </a>
        <a href="{{ route('staf.daftar-pesanan') }}?status=disetujui" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-orange-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div></div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $designWaitingAcc }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Menunggu ACC</p>
        </a>
        <a href="{{ route('staf.daftar-pesanan') }}?status=selesai&date_from={{ now()->format('Y-m-d') }}&date_to={{ now()->format('Y-m-d') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-green-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div></div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $completedToday }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Selesai Hari Ini</p>
        </a>

        @elseif($isProduction)
        <a href="{{ route('staf.daftar-pesanan') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-[#1a237e]/30 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-[#1a237e] flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg></div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $totalTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M{{ $totalTrend >= 0 ? '13 7l5 5m0 0l-5 5m5-5H6' : '11 17l-5-5m0 0l5-5m-5 5h12' }}"/></svg>
                    <span>{{ $totalTrend >= 0 ? '+'.$totalTrend : $totalTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $totalOrders }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Total Pesanan</p>
        </a>
        <a href="{{ route('staf.daftar-pesanan') }}?status=siap_cetak" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-red-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div></div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $printQueue }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Antrian Cetak</p>
        </a>
        <a href="{{ route('staf.daftar-pesanan') }}?status=diproduksi" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-purple-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div></div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $sewingQueue }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Sedang Diproduksi</p>
        </a>
        <a href="{{ route('staf.daftar-pesanan') }}?status=selesai&date_from={{ now()->format('Y-m-d') }}&date_to={{ now()->format('Y-m-d') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-green-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $completedTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M{{ $completedTrend >= 0 ? '13 7l5 5m0 0l-5 5m5-5H6' : '11 17l-5-5m0 0l5-5m-5 5h12' }}"/></svg>
                    <span>{{ $completedTrend >= 0 ? '+'.$completedTrend : $completedTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $completedToday }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Selesai Hari Ini</p>
        </a>

        @else
        <a href="{{ route('staf.daftar-pesanan') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-[#1a237e]/30 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-[#1a237e] flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg></div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $totalTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M{{ $totalTrend >= 0 ? '13 7l5 5m0 0l-5 5m5-5H6' : '11 17l-5-5m0 0l5-5m-5 5h12' }}"/></svg>
                    <span>{{ $totalTrend >= 0 ? '+'.$totalTrend : $totalTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $totalOrders }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Total Pesanan</p>
        </a>
        <a href="{{ route('staf.daftar-pesanan') }}?status=menunggu_verifikasi" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-orange-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $pendingTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M{{ $pendingTrend >= 0 ? '13 7l5 5m0 0l-5 5m5-5H6' : '11 17l-5-5m0 0l5-5m-5 5h12' }}"/></svg>
                    <span>{{ $pendingTrend >= 0 ? '+'.$pendingTrend : $pendingTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $pendingOrders }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Menunggu Verifikasi</p>
        </a>
        <a href="{{ route('staf.daftar-pesanan') }}?status=tahap_produksi" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-blue-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $processTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M{{ $processTrend >= 0 ? '13 7l5 5m0 0l-5 5m5-5H6' : '11 17l-5-5m0 0l5-5m-5 5h12' }}"/></svg>
                    <span>{{ $processTrend >= 0 ? '+'.$processTrend : $processTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $inProcessOrders }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Sedang Diproses</p>
        </a>
        <a href="{{ route('staf.daftar-pesanan') }}?status=selesai&date_from={{ now()->format('Y-m-d') }}&date_to={{ now()->format('Y-m-d') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-green-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $completedTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M{{ $completedTrend >= 0 ? '13 7l5 5m0 0l-5 5m5-5H6' : '11 17l-5-5m0 0l5-5m-5 5h12' }}"/></svg>
                    <span>{{ $completedTrend >= 0 ? '+'.$completedTrend : $completedTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $completedToday }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Selesai Hari Ini</p>
        </a>
        @endif

        @if($isSAOManager)
        <a href="{{ route('staf.laporan', ['filter' => 'month']) }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-green-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"><svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $revenueTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M{{ $revenueTrend >= 0 ? '13 7l5 5m0 0l-5 5m5-5H6' : '11 17l-5-5m0 0l5-5m-5 5h12' }}"/></svg>
                    <span>{{ $revenueTrend >= 0 ? '+'.$revenueTrend.'%' : $revenueTrend.'%' }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $totalRevenue > 0 ? 'Rp '.number_format($totalRevenue / 1000000, 1).'jt' : 'Rp 0' }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Revenue</p>
        </a>
        @endif
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8" 
         x-data="dashboardCharts()" 
         x-init="initCharts(@js($weeklyLabels), @js($weeklyData), @js($statusLabels), @js($statusData))">
        <div class="bg-white shadow-sm rounded-xl p-6">
            <h3 class="font-bold text-gray-900 mb-6 text-lg">Pesanan Per Minggu</h3>
            <div class="h-64"><canvas id="lineChart"></canvas></div>
        </div>
        <div class="bg-white shadow-sm rounded-xl p-6">
            <h3 class="font-bold text-gray-900 mb-6 text-lg">Status Pesanan Saat Ini</h3>
            <div class="h-64 flex justify-center"><canvas id="donutChart"></canvas></div>
        </div>
    </div>

    {{-- Recent Orders Table --}}
    <div class="bg-white shadow-sm rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-900 text-lg">Pesanan Terbaru</h3>
            <a href="{{ route('staf.daftar-pesanan') }}" class="text-sm font-semibold text-[#1a237e] hover:underline flex items-center gap-1">
                Lihat Semua <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold text-center">Order ID</th>
                        <th class="px-6 py-4 font-semibold text-center">Customer</th>
                        <th class="px-6 py-4 font-semibold text-center">Produk</th>
                        <th class="px-6 py-4 font-semibold text-center">Tanggal</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($recentOrders as $o)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $o['order_number'] }}</td>
                        <td class="px-6 py-4">{{ $o['customer_name'] }}</td>
                        <td class="px-6 py-4">{{ $o['team_name'] }}</td>
                        <td class="px-6 py-4">{{ $o['created_at'] }}</td>
                        <td class="px-6 py-4"><x-badge type="{{ statusBadgeType($o['status']) }}">{{ statusLabel($o['status']) }}</x-badge></td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ $o['detail_url'] }}" class="text-gray-400 hover:text-[#1a237e] inline-block">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada pesanan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    function dashboardCharts() {
        return {
            initCharts(weeklyLabels, weeklyData, statusLabels, statusData) {
                this.$nextTick(() => {
                    const lineEl = document.getElementById('lineChart');
                    if (lineEl) {
                        new Chart(lineEl.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: weeklyLabels,
                                datasets: [{
                                    label: 'Pesanan',
                                    data: weeklyData,
                                    borderColor: '#1a237e',
                                    backgroundColor: 'rgba(26, 35, 126, 0.05)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: '#1a237e',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1a237e', padding: 12, displayColors: false } },
                                scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6', borderDash: [4, 4] }, border: { display: false } }, x: { grid: { display: false }, border: { display: false } } }
                            }
                        });
                    }

                    const donutEl = document.getElementById('donutChart');
                    if (donutEl) {
                        new Chart(donutEl.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: statusLabels,
                                datasets: [{
                                    data: statusData,
                                    backgroundColor: ['#eab308', '#3b82f6', '#f97316', '#a855f7', '#22c55e'],
                                    borderWidth: 2,
                                    borderColor: '#ffffff',
                                    hoverOffset: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '75%',
                                plugins: {
                                    legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, pointStyle: 'circle', font: { size: 12 } } },
                                    tooltip: { padding: 12 }
                                }
                            }
                        });
                    }
                });
            }
        }
    }
    </script>
</div>
