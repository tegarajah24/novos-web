@extends('layouts.internal')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush

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
    @if($isDesign)
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-6 mb-8">

        <!-- Card D1: Menunggu Desain -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=dikonfirmasi" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-purple-300/50 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-purple-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $designWaiting }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Menunggu Desain</p>
        </a>

        <!-- Card D2: Sedang Di Desain -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=di_design" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-blue-300/50 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $designInProgress }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Sedang Di Desain</p>
        </a>

        <!-- Card D3: Menunggu ACC -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=disetujui" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-orange-300/50 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-orange-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $designWaitingAcc }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Menunggu ACC</p>
        </a>

        <!-- Card D4: Selesai Hari Ini -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=selesai&date_from={{ now()->format('Y-m-d') }}&date_to={{ now()->format('Y-m-d') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-green-300/50 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $completedToday }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Selesai Hari Ini</p>
        </a>

    </div>

    @elseif($isProduction)
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-6 mb-8">

        <!-- Card P1: Total Pesanan -->
        <a href="{{ route('staf.daftar-pesanan') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-[#1a237e]/30 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-[#1a237e] flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-[10px] lg:text-xs font-semibold {{ $totalTrend >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} px-1.5 lg:px-2 py-0.5 rounded-full">
                    @if($totalTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $totalTrend >= 0 ? '+'.$totalTrend : $totalTrend }}</span>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $totalOrders }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Total Pesanan</p>
        </a>

        <!-- Card P2: Antrian Cetak -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=siap_cetak" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-red-300/50 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-red-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $printQueue }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Antrian Cetak</p>
        </a>

        <!-- Card P3: Sedang Diproduksi -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=diproduksi" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-purple-300/50 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-purple-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $sewingQueue }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Sedang Diproduksi</p>
        </a>

        <!-- Card P4: Selesai Hari Ini -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=selesai&date_from={{ now()->format('Y-m-d') }}&date_to={{ now()->format('Y-m-d') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-green-300/50 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-[10px] lg:text-xs font-semibold {{ $completedTrend >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} px-1.5 lg:px-2 py-0.5 rounded-full">
                    @if($completedTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $completedTrend >= 0 ? '+'.$completedTrend : $completedTrend }}</span>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $completedToday }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Selesai Hari Ini</p>
        </a>

    </div>

    @else
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-6 mb-8">

        <!-- Card 1: Total Pesanan -->
        <a href="{{ route('staf.daftar-pesanan') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-[#1a237e]/30 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-[#1a237e] flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-[10px] lg:text-xs font-semibold {{ $totalTrend >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} px-1.5 lg:px-2 py-0.5 rounded-full">
                    @if($totalTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $totalTrend >= 0 ? '+'.$totalTrend : $totalTrend }}</span>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $totalOrders }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Total Pesanan</p>
        </a>

        <!-- Card 2: Menunggu Pembayaran -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=menunggu_pembayaran" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-orange-300/50 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-orange-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-[10px] lg:text-xs font-semibold {{ $pendingTrend >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} px-1.5 lg:px-2 py-0.5 rounded-full">
                    @if($pendingTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $pendingTrend >= 0 ? '+'.$pendingTrend : $pendingTrend }}</span>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $pendingOrders }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Menunggu Pembayaran</p>
        </a>

        <!-- Card 3: Sedang Diproses -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=tahap_produksi" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-blue-300/50 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-[10px] lg:text-xs font-semibold {{ $processTrend >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} px-1.5 lg:px-2 py-0.5 rounded-full">
                    @if($processTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $processTrend >= 0 ? '+'.$processTrend : $processTrend }}</span>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $inProcessOrders }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Sedang Diproses</p>
        </a>

        <!-- Card 4: Selesai Hari Ini -->
        <a href="{{ route('staf.daftar-pesanan') }}?status=selesai&date_from={{ now()->format('Y-m-d') }}&date_to={{ now()->format('Y-m-d') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 flex flex-col justify-between hover:shadow-xl hover:border-green-300/50 lg:hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 lg:w-5 lg:h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-[10px] lg:text-xs font-semibold {{ $completedTrend >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} px-1.5 lg:px-2 py-0.5 rounded-full">
                    @if($completedTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $completedTrend >= 0 ? '+'.$completedTrend : $completedTrend }}</span>
                </div>
            </div>
            <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight stats-counter" data-target="{{ $completedToday }}">0</h3>
            <p class="text-gray-500 text-xs lg:text-sm mt-1 lg:mt-1.5 font-medium">Selesai Hari Ini</p>
        </a>

    </div>
    @endif

    <!-- Charts Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Line Chart -->
        <div class="bg-white shadow-sm rounded-xl p-4 sm:p-6">
            <div class="flex items-center justify-between mb-3 md:mb-6">
                <h3 class="font-bold text-gray-900 text-sm md:text-lg">Pesanan</h3>
                {{-- Desktop: pill tab buttons --}}
                <div class="hidden lg:flex gap-1 bg-gray-100 rounded-lg p-1" id="chartFilters">
                    <button data-filter="day" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all text-gray-500 hover:text-gray-700">Harian</button>
                    <button data-filter="week" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all bg-white shadow-sm text-[#1a237e]">Mingguan</button>
                    <button data-filter="month" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all text-gray-500 hover:text-gray-700">Bulanan</button>
                    <button data-filter="year" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all text-gray-500 hover:text-gray-700">Tahunan</button>
                </div>
                {{-- Mobile: select dropdown --}}
                <select id="chartFilterSelect" class="lg:hidden block w-24 px-2 py-1 text-xs font-semibold bg-gray-50 border border-gray-200 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all">
                    <option value="day">Harian</option>
                    <option value="week" selected>Mingguan</option>
                    <option value="month">Bulanan</option>
                    <option value="year">Tahunan</option>
                </select>
            </div>
            <div class="h-28 md:h-64">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
        <!-- Donut Chart -->
        <div class="bg-white shadow-sm rounded-xl p-4 sm:p-6 overflow-hidden">
            <h3 class="font-bold text-gray-900 mb-3 text-sm sm:text-lg">Status Pesanan Saat Ini</h3>
            <div class="flex flex-col lg:flex-row items-center lg:items-center gap-4 lg:gap-6">
                <!-- Donut + Center Label -->
                <div class="relative w-36 h-36 lg:w-64 lg:h-64 flex justify-center flex-shrink-0">
                    <canvas id="donutChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-xl lg:text-2xl font-extrabold text-gray-900 leading-none">{{ $statusData[0] + $statusData[1] + $statusData[2] + $statusData[3] }}</span>
                        <span class="text-[10px] lg:text-xs text-gray-400 font-medium mt-0.5">Total Aktif</span>
                    </div>
                </div>
                <!-- Rich List Legend -->
                <div class="w-full lg:w-auto lg:flex-1 max-w-[250px] lg:pt-2 space-y-1.5">
                    @php $colors = ['#eab308', '#3b82f6', '#f97316', '#a855f7', '#22c55e']; @endphp
                    @foreach($statusLabels as $i => $label)
                    <div class="flex items-center justify-between py-0.5">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $colors[$i] }}"></span>
                            <span class="text-xs font-medium text-gray-600 truncate">{{ $label }}</span>
                        </div>
                        <span class="text-xs font-bold text-gray-900 flex-shrink-0 ml-2">{{ $statusData[$i] }}</span>
                    </div>
                    @endforeach
                </div>
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
        <div class="overflow-x-auto max-h-72 overflow-y-auto lg:max-h-none lg:overflow-y-visible">
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
                        <td class="px-6 py-4 font-medium text-gray-900 lg:text-center">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 lg:text-center">{{ $order->user->name }}</td>
                        <td class="px-6 py-4 lg:text-center">{{ $order->designRequest?->team_name ?? 'Pesanan #'.$order->id }}</td>
                        <td class="px-6 py-4 lg:text-center">{{ $order->created_at->format('j M Y') }}</td>
                        <td class="px-6 py-4 lg:text-center"><x-badge type="{{ statusBadgeType($order->status) }}">{{ statusLabel($order->status) }}</x-badge></td>
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
        function initDashboardCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(initDashboardCharts, 100);
                return;
            }

            // Preloaded chart data
            var allChartData = @json($allChartData);

            // Animate stats counter smoothly on load
            document.querySelectorAll('.stats-counter').forEach(function(el) {
                var target = parseInt(el.getAttribute('data-target')) || 0;
                if (target === 0) {
                    el.textContent = '0';
                    return;
                }
                var duration = 1000; // 1 second
                var start = 0;
                var startTime = null;

                function animateCounter(currentTime) {
                    if (!startTime) startTime = currentTime;
                    var progress = currentTime - startTime;
                    var percent = Math.min(progress / duration, 1);
                    // Ease out quadratic
                    var easePercent = percent * (2 - percent);
                    var current = Math.floor(easePercent * target);
                    el.textContent = current;

                    if (percent < 1) {
                        requestAnimationFrame(animateCounter);
                    } else {
                        el.textContent = target;
                    }
                }
                requestAnimationFrame(animateCounter);
            });

            // ==================== LINE CHART ====================
            var lineChart = null;

            function initLineChart(filter) {
                var ctxLine = document.getElementById('lineChart');
                if (!ctxLine) return;

                var chartInfo = allChartData[filter];
                if (!chartInfo) return;

                lineChart = new Chart(ctxLine.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: chartInfo.labels,
                        datasets: [{
                            label: 'Pesanan',
                            data: chartInfo.data,
                            borderColor: '#1a237e',
                            backgroundColor: 'rgba(26, 35, 126, 0.05)',
                            borderWidth: window.innerWidth < 768 ? 1.5 : 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#1a237e',
                            pointBorderColor: '#fff',
                            pointBorderWidth: window.innerWidth < 768 ? 1 : 2,
                            pointRadius: window.innerWidth < 768 ? 2 : 4,
                            pointHoverRadius: window.innerWidth < 768 ? 4 : 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1000,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1a237e',
                                padding: window.innerWidth < 768 ? 8 : 12,
                                titleFont: { size: window.innerWidth < 768 ? 11 : 13, family: "'Poppins', sans-serif" },
                                bodyFont: { size: window.innerWidth < 768 ? 11 : 13, family: "'Poppins', sans-serif" },
                                displayColors: false,
                                intersect: window.innerWidth >= 768,
                                mode: window.innerWidth < 768 ? 'index' : 'nearest',
                                caretPadding: 8,
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { color: window.innerWidth < 768 ? 'transparent' : '#f3f4f6', borderDash: [4, 4] },
                                border: { display: false },
                                ticks: { display: window.innerWidth >= 768 }
                            },
                            x: { 
                                grid: { display: false },
                                border: { display: false },
                                ticks: { display: window.innerWidth >= 768, maxRotation: 0 }
                            }
                        }
                    }
                });
            }

            function updateLineChart(filter) {
                var chartInfo = allChartData[filter];
                if (lineChart && chartInfo) {
                    lineChart.data.labels = chartInfo.labels;
                    lineChart.data.datasets[0].data = chartInfo.data;
                    lineChart.update();
                }
            }

            // Filter: pill tab buttons (desktop)
            var filterBtns = document.querySelectorAll('#chartFilters button');
            filterBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(function(b) {
                        b.className = 'px-3 py-1.5 text-xs font-semibold rounded-md transition-all text-gray-500 hover:text-gray-700';
                    });
                    this.className = 'px-3 py-1.5 text-xs font-semibold rounded-md transition-all bg-white shadow-sm text-[#1a237e]';
                    
                    var filterVal = this.dataset.filter;
                    var sel = document.getElementById('chartFilterSelect');
                    if (sel) sel.value = filterVal;
                    
                    updateLineChart(filterVal);
                });
            });

            // Filter: select dropdown (mobile)
            var filterSelect = document.getElementById('chartFilterSelect');
            if (filterSelect) {
                filterSelect.addEventListener('change', function() {
                    var filterVal = this.value;
                    updateLineChart(filterVal);
                    
                    filterBtns.forEach(function(b) {
                        if (b.dataset.filter === filterVal) {
                            b.className = 'px-3 py-1.5 text-xs font-semibold rounded-md transition-all bg-white shadow-sm text-[#1a237e]';
                        } else {
                            b.className = 'px-3 py-1.5 text-xs font-semibold rounded-md transition-all text-gray-500 hover:text-gray-700';
                        }
                    });
                });
            }

            // Default ke Harian di mobile, Mingguan di desktop
            var initialFilter = window.innerWidth < 1024 ? 'day' : 'week';
            if (filterSelect) filterSelect.value = initialFilter;
            
            // Set initial active state on buttons
            filterBtns.forEach(function(b) {
                if (b.dataset.filter === initialFilter) {
                    b.className = 'px-3 py-1.5 text-xs font-semibold rounded-md transition-all bg-white shadow-sm text-[#1a237e]';
                } else {
                    b.className = 'px-3 py-1.5 text-xs font-semibold rounded-md transition-all text-gray-500 hover:text-gray-700';
                }
            });

            initLineChart(initialFilter);

            // ==================== DOUGHNUT CHART ====================
            var ctxDonut = document.getElementById('donutChart');
            if (ctxDonut) {
                var donutChart = new Chart(ctxDonut.getContext('2d'), {
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
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1000,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                padding: window.innerWidth < 768 ? 8 : 12,
                                titleFont: { size: window.innerWidth < 768 ? 11 : 13, family: "'Poppins', sans-serif" },
                                bodyFont: { size: window.innerWidth < 768 ? 11 : 13, family: "'Poppins', sans-serif" },
                                caretPadding: 8,
                                displayColors: window.innerWidth >= 1024,
                                callbacks: {
                                    title: function(tooltipItems) {
                                        if (window.innerWidth < 1024) {
                                            return '';
                                        }
                                        return tooltipItems[0].label;
                                    },
                                    label: function(context) {
                                        if (window.innerWidth < 1024) {
                                            return context.raw.toString();
                                        }
                                        return context.label + ': ' + context.formattedValue;
                                    }
                                }
                            }
                        }
                    }
                });

                // Dynamically toggle tooltip configurations on window resize
                window.addEventListener('resize', function() {
                    var isDesktop = window.innerWidth >= 1024;
                    var needsUpdate = false;

                    if (donutChart.options.tooltip.displayColors !== isDesktop) {
                        donutChart.options.plugins.tooltip.displayColors = isDesktop;
                        needsUpdate = true;
                    }

                    if (needsUpdate) {
                        donutChart.update();
                    }
                });
            }

        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDashboardCharts);
        } else {
            initDashboardCharts();
        }
    </script>

@endsection
