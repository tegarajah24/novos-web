<?php
// PHP converts dots in cookie names to underscores in $_COOKIE superglobal
// 'sidebar.open' (JS) → 'sidebar_open' (PHP). Always use sidebar_open.
$isSidebarOpen = !(isset($_COOKIE['sidebar_open']) && $_COOKIE['sidebar_open'] === 'false');
?>

<aside
    x-data="{
        sidebarOpen: {{ $isSidebarOpen ? 'true' : 'false' }},
        toggle() {
            this.sidebarOpen = !this.sidebarOpen;
            document.cookie = 'sidebar_open=' + this.sidebarOpen + '; path=/; SameSite=Lax; max-age=' + (60 * 60 * 24 * 365);
        }
    }"
    @sidebar-toggle.window="toggle()"
    :style="{ width: sidebarOpen ? '16rem' : '5rem' }"
    style="width: {{ $isSidebarOpen ? '16rem' : '5rem' }}; transition: width 0.3s ease;"
    class="bg-white min-h-screen border-r border-gray-200 flex flex-col shrink-0">

    {{-- Logo Area --}}
    <div class="h-16 flex items-center px-6 border-b border-gray-200 overflow-hidden">
        <a href="{{ route('staf.dashboard') }}" class="flex items-center gap-1">
<div class="w-10 h-10 shrink-0 flex items-center justify-center">
    <img src="{{ asset('images/logo.png') }}" alt="Novos Logo" class="w-10 h-10 object-contain">
</div>

            <span x-show="sidebarOpen"
                  @if(!$isSidebarOpen) style="display:none" @endif
                  class="text-xl font-bold text-gray-900 whitespace-nowrap">
                Novos
            </span>
        </a>
    </div>

    {{-- Menu Items --}}
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        @canAccess('dashboard')
        <a href="{{ route('staf.dashboard') }}"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.dashboard') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.dashboard') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Dashboard</span>
        </a>
        @endcanAccess

        @canAccess('summary')
        <a href="{{ route('staf.summary') }}"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.summary') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="pie-chart" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.summary') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Summary</span>
        </a>
        @endcanAccess

        @canAccess('orders')
        <a href="{{ route('staf.daftar-pesanan') }}"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.daftar-pesanan') || request()->routeIs('staf.detail-pesanan') || request()->routeIs('staf.chat') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="shopping-bag" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.daftar-pesanan') || request()->routeIs('staf.detail-pesanan') || request()->routeIs('staf.chat') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Daftar Pesanan</span>
        </a>
        @endcanAccess

        @canAccess('design')
        <a href="{{ route('staf.design') }}"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.design') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="pen-tool" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.design') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Design</span>
        </a>
        @endcanAccess

        @canAccess('production')
        <a href="{{ route('staf.produksi') }}"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.produksi') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="scissors" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.produksi') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Produksi</span>
        </a>
        @endcanAccess

        @canAccess('daily-mental-check')
        <a href="{{ route('staf.daily-mental-check') }}"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.daily-mental-check') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="heart" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.daily-mental-check') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Daily Mental Check</span>
        </a>
        @endcanAccess

        @canAccess('reports')
        <a href="{{ route('staf.laporan') }}"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.laporan') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="file-text" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.laporan') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Laporan</span>
        </a>
        @endcanAccess

        @canAccess('manage-products')
        <a href="{{ route('staf.kelola-produk') }}"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.kelola-produk') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="package" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.kelola-produk') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Kelola Produk</span>
        </a>
        @endcanAccess

        @canAccess('categories')
        <a href="{{ route('staf.kategori') }}"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.kategori') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="folder-tree" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.kategori') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Kategori</span>
        </a>
        @endcanAccess

        @canAccess('manage-users')
        <a href="{{ route('staf.kelola-pengguna') }}"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.kelola-pengguna') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="users" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.kelola-pengguna') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Kelola Pengguna</span>
        </a>
        @endcanAccess

        @canAccess('settings')
        <a href="{{ route('staf.pengaturan') }}"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.pengaturan') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="settings" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.pengaturan') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Pengaturan</span>
        </a>
        @endcanAccess
    </nav>

    {{-- (Footer profile removed) --}}
</aside>
