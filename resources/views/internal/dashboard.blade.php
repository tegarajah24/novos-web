@extends('layouts.internal')

@section('title', 'Dashboard')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Dashboard</h1>
@endsection

@section('internal-content')
@php
function statusLabel($status) {
    return match($status) {
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'tahap_desain' => 'Tahap Desain',
        'menunggu_acc' => 'Menunggu ACC',
        'tahap_produksi' => 'Produksi',
        'selesai' => 'Selesai',
        default => ucwords(str_replace('_', ' ', $status)),
    };
}
function statusBadgeType($status) {
    return match($status) {
        'menunggu_pembayaran' => 'orange',
        'tahap_desain' => 'blue',
        'menunggu_acc' => 'orange',
        'tahap_produksi' => 'purple',
        'selesai' => 'green',
        default => 'gray',
    };
}
@endphp
    <!-- Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">

        @if($isDesign)

        <!-- Card D1: Menunggu Desain -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=dikonfirmasi" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-purple-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $designWaiting }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Menunggu Desain</p>
        </a>

        <!-- Card D2: Sedang Di Desain -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=di_design" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-blue-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $designInProgress }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Sedang Di Desain</p>
        </a>

        <!-- Card D3: Menunggu ACC -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=disetujui" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-orange-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $designWaitingAcc }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Menunggu ACC</p>
        </a>

        <!-- Card D4: Selesai Hari Ini -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=selesai&date_from={{ now()->format('Y-m-d') }}&date_to={{ now()->format('Y-m-d') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-green-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $completedToday }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Selesai Hari Ini</p>
        </a>

        @elseif($isProduction)

        <!-- Card P1: Total Pesanan -->
        <a href="{{ route('staf.daftar-pesanan') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-[#1a237e]/30 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-[#1a237e] flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $totalTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    @if($totalTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $totalTrend >= 0 ? '+'.$totalTrend : $totalTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $totalOrders }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Total Pesanan</p>
        </a>

        <!-- Card P2: Antrian Cetak -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=siap_cetak" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-red-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $printQueue }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Antrian Cetak</p>
        </a>

        <!-- Card P3: Sedang Diproduksi -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=diproduksi" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-purple-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $sewingQueue }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Sedang Diproduksi</p>
        </a>

        <!-- Card P4: Selesai Hari Ini -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=selesai&date_from={{ now()->format('Y-m-d') }}&date_to={{ now()->format('Y-m-d') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-green-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $completedTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    @if($completedTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $completedTrend >= 0 ? '+'.$completedTrend : $completedTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $completedToday }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Selesai Hari Ini</p>
        </a>

        @else

        <!-- Card 1: Total Pesanan -->
        <a href="{{ route('staf.daftar-pesanan') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-[#1a237e]/30 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-[#1a237e] flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    {{-- Clipboard List icon (Heroicons) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $totalTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    @if($totalTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $totalTrend >= 0 ? '+'.$totalTrend : $totalTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $totalOrders }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Total Pesanan</p>
        </a>

        <!-- Card 2: Menunggu Pembayaran -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=menunggu_pembayaran" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-orange-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    {{-- Clock icon (Heroicons) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $pendingTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    @if($pendingTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $pendingTrend >= 0 ? '+'.$pendingTrend : $pendingTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $pendingOrders }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Menunggu Pembayaran</p>
        </a>

        <!-- Card 3: Sedang Diproses -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=tahap_produksi" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-blue-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    {{-- Cog / Settings icon (Heroicons) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $processTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    @if($processTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $processTrend >= 0 ? '+'.$processTrend : $processTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $inProcessOrders }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Sedang Diproses</p>
        </a>

        <!-- Card 4: Selesai Hari Ini -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=selesai&date_from={{ now()->format('Y-m-d') }}&date_to={{ now()->format('Y-m-d') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-green-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    {{-- Check Circle icon (Heroicons) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $completedTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    @if($completedTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $completedTrend >= 0 ? '+'.$completedTrend : $completedTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight stats-counter" data-target="{{ $completedToday }}">0</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Selesai Hari Ini</p>
        </a>

        @endif

    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Line Chart -->
        <div class="bg-white shadow-sm rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-900 text-lg">Pesanan</h3>
                <div class="relative">
                    <select id="chartFilterSelect" class="block w-28 px-3 py-1.5 text-xs font-semibold bg-gray-50 border border-gray-200 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all">
                        <option value="day">Harian</option>
                        <option value="week" selected>Mingguan</option>
                        <option value="month">Bulanan</option>
                        <option value="year">Tahunan</option>
                    </select>
                </div>
            </div>
            <div class="h-64">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
        <!-- Donut Chart -->
        <div class="bg-white shadow-sm rounded-xl p-6">
            <h3 class="font-bold text-gray-900 mb-6 text-lg">Status Pesanan Saat Ini</h3>
            <div class="h-64 flex justify-center">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Table Row -->
    <div class="bg-white shadow-sm rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-900 text-lg">Pesanan Terbaru</h3>
            <a href="{{ route('staf.daftar-pesanan') }}" class="text-sm font-semibold text-[#1a237e] hover:underline flex items-center gap-1">
                Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
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
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $order->order_number }}</td>
                        <td class="px-6 py-4">{{ $order->user->name }}</td>
                        <td class="px-6 py-4">{{ $order->designRequest?->team_name ?? 'Pesanan #'.$order->id }}</td>
                        <td class="px-6 py-4">{{ $order->created_at->format('j M Y') }}</td>
                        <td class="px-6 py-4"><x-badge type="{{ statusBadgeType($order->status) }}">{{ statusLabel($order->status) }}</x-badge></td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('staf.detail-pesanan', $order->order_number) }}" class="text-gray-400 hover:text-[#1a237e] inline-block">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada pesanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Tampilkan angka statistik langsung (tanpa animasi counter)
            document.querySelectorAll('.stats-counter').forEach(function(el) {
                el.textContent = el.getAttribute('data-target') || '0';
            });

            // ==================== LINE CHART ====================
            var lineChart = null;

            function loadChart(filter) {
                fetch('{{ route("staf.dashboard.chart-data") }}?filter=' + filter, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(function(res) { return res.json(); })
                .then(function(result) {
                    var ctxLine = document.getElementById('lineChart');
                    if (!ctxLine) return;

                    if (lineChart) {
                        lineChart.destroy();
                    }

                    lineChart = new Chart(ctxLine.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: result.labels,
                            datasets: [{
                                label: 'Pesanan',
                                data: result.data,
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
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1a237e',
                                    padding: 12,
                                    titleFont: { size: 13, family: "'Poppins', sans-serif" },
                                    bodyFont: { size: 13, family: "'Poppins', sans-serif" },
                                    displayColors: false,
                                }
                            },
                            scales: {
                                y: { 
                                    beginAtZero: true, 
                                    grid: { color: '#f3f4f6', borderDash: [4, 4] },
                                    border: { display: false }
                                },
                                x: { 
                                    grid: { display: false },
                                    border: { display: false }
                                }
                            }
                        }
                    });
                })
                .catch(function() {});
            }

            // Filter dropdown select handler
            var filterSelect = document.getElementById('chartFilterSelect');
            if (filterSelect) {
                filterSelect.addEventListener('change', function(e) {
                    loadChart(this.value);
                });
            }

            loadChart('week');

            // ==================== DOUGHNUT CHART ====================
            var ctxDonut = document.getElementById('donutChart');
            if (ctxDonut) {
                new Chart(ctxDonut.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: @json($statusLabels),
                        datasets: [{
                            data: @json($statusData),
                            backgroundColor: [
                                '#eab308',
                                '#3b82f6',
                                '#f97316',
                                '#a855f7',
                                '#22c55e'
                            ],
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
                            legend: {
                                position: 'bottom',
                                labels: { 
                                    padding: 20, 
                                    usePointStyle: true, 
                                    pointStyle: 'circle',
                                    font: { family: "'Poppins', sans-serif", size: 12 }
                                }
                            },
                            tooltip: {
                                padding: 12,
                                titleFont: { size: 13, family: "'Poppins', sans-serif" },
                                bodyFont: { size: 13, family: "'Poppins', sans-serif" },
                            }
                        }
                    }
                });
            }

        });
    </script>

@endsection
