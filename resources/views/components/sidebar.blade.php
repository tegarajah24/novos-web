<?php
// PHP converts dots in cookie names to underscores in $_COOKIE superglobal
// 'sidebar.open' (JS) → 'sidebar_open' (PHP). Always use sidebar_open.
$isSidebarOpen = !(isset($_COOKIE['sidebar_open']) && $_COOKIE['sidebar_open'] === 'false');
$isOnDmc = request()->routeIs('staf.daily-mental-check');
$dmcRoute = route('staf.daily-mental-check');
?>

{{-- Sidebar wrapper: shared Alpine scope for backdrop + aside --}}
<div
    x-data="{
        sidebarOpen: window.innerWidth >= 1280 ? {{ $isSidebarOpen ? 'true' : 'false' }} : false,
        isOnDmc: {{ $isOnDmc ? 'true' : 'false' }},
        dmcOpen: location.hash.startsWith('#dmc='),
        dmcHash: location.hash,
        setDmcTab(tab) {
            location.hash = 'dmc=' + tab;
            this.dmcHash = '#dmc=' + tab;
            if (window.innerWidth < 1280) this.sidebarOpen = false;
        },
        toggleDmc() {
            this.dmcOpen = !this.dmcOpen;
        },
        isDmcActive(index) {
            if (index === 0) return this.dmcHash === '' || this.dmcHash === '#' || this.dmcHash === '#dmc=0';
            return this.dmcHash === '#dmc=' + index;
        },
        isAnyDmcActive() {
            return this.dmcOpen || (this.isOnDmc && [0, 1, 2, 3].some(i => this.isDmcActive(i)));
        },
        toggle() {
            this.sidebarOpen = !this.sidebarOpen;
            document.cookie = 'sidebar_open=' + this.sidebarOpen + '; path=/; SameSite=Lax; max-age=' + (60 * 60 * 24 * 365);
        }
    }"
    x-init="window.addEventListener('hashchange', () => { dmcHash = location.hash; })"
    @sidebar-toggle.window="toggle()"
    class="contents">

    {{-- Backdrop for mobile sidebar --}}
    <div x-show="sidebarOpen" x-cloak
         @click="toggle()"
         class="fixed inset-0 z-40 bg-black/50 xl:hidden">
    </div>

    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        :style="window.innerWidth >= 1280 ? { width: sidebarOpen ? '16rem' : '5rem' } : { width: '18rem' }"
        style="transition: all 0.3s ease;"
        class="bg-white min-h-screen border-r border-gray-200 flex flex-col shrink-0 z-50
               fixed inset-y-0 left-0 w-72 -translate-x-full
               xl:relative xl:z-auto xl:block xl:translate-x-0">

    {{-- Logo Area --}}
    <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200 overflow-hidden">
        <a href="{{ route('staf.dashboard') }}" @click="if(window.innerWidth < 1280) sidebarOpen = false" class="flex items-center gap-1">
<div class="w-10 h-10 shrink-0 flex items-center justify-center">
    <img src="{{ asset('images/logo.png') }}" alt="Novos Logo" class="w-10 h-10 object-contain">
</div>

            <span x-show="sidebarOpen"
                  @if(!$isSidebarOpen) style="display:none" @endif
                  class="text-xl font-bold text-gray-900 whitespace-nowrap">
                Novos
            </span>
        </a>

        {{-- Close button for mobile --}}
        <button @click="toggle()"
                class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors xl:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    {{-- Menu Items --}}
    <nav @click="if(window.innerWidth < 1280) sidebarOpen = false" class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        @canAccess('dashboard')
        <a href="{{ route('staf.dashboard') }}"
           @click="if(window.innerWidth < 1280) sidebarOpen = false"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.dashboard') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.dashboard') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="window.innerWidth < 1280 || sidebarOpen" class="font-medium whitespace-nowrap">Dashboard</span>
        </a>
        @endcanAccess

        @canAccess('summary')
        <a href="{{ route('staf.summary') }}"
           @click="if(window.innerWidth < 1280) sidebarOpen = false"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.summary') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="pie-chart" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.summary') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="window.innerWidth < 1280 || sidebarOpen" class="font-medium whitespace-nowrap">Summary</span>
        </a>
        @endcanAccess

        @canAccess('orders')
        <a href="{{ route('staf.daftar-pesanan') }}"
           @click="if(window.innerWidth < 1280) sidebarOpen = false"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.daftar-pesanan') || request()->routeIs('staf.detail-pesanan') || request()->routeIs('staf.chat') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="shopping-bag" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.daftar-pesanan') || request()->routeIs('staf.detail-pesanan') || request()->routeIs('staf.chat') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="window.innerWidth < 1280 || sidebarOpen" class="font-medium whitespace-nowrap">Daftar Pesanan</span>
        </a>
        @endcanAccess

        @canAccess('design')
        <a href="{{ route('staf.design') }}"
           @click="if(window.innerWidth < 1280) sidebarOpen = false"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.design') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="pen-tool" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.design') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="window.innerWidth < 1280 || sidebarOpen" class="font-medium whitespace-nowrap">Design</span>
        </a>
        @endcanAccess

        @canAccess('production')
        <a href="{{ route('staf.produksi') }}"
           @click="if(window.innerWidth < 1280) sidebarOpen = false"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.produksi') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="scissors" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.produksi') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="window.innerWidth < 1280 || sidebarOpen" class="font-medium whitespace-nowrap">Produksi</span>
        </a>
        @endcanAccess

        @canAccess('daily-mental-check')
        <div class="xl:hidden mb-3 pb-3 border-b border-gray-100">
            <button @click.stop="toggleDmc()"
                    :class="isAnyDmcActive() ? 'bg-[#1a237e]/90 text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                    class="flex items-center justify-between w-full px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                <div class="flex items-center gap-3 transition-colors duration-200" :class="isAnyDmcActive() ? 'text-white' : 'text-[#1a237e]'">
                    <i data-lucide="heart" class="w-5 h-5 shrink-0"></i>
                    <span class="whitespace-nowrap">Daily Mental Check</span>
                </div>
                <span class="w-4 h-4 transition-transform duration-200" :class="[dmcOpen ? '' : '-rotate-90', isAnyDmcActive() ? 'text-white' : 'text-gray-400']">
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </span>
            </button>
            <div x-show="dmcOpen" x-cloak class="space-y-3 mt-3">
                @if(request()->routeIs('staf.daily-mental-check'))
                <button @click.stop="setDmcTab(0)"
                        :class="isDmcActive(0) ? 'bg-[#1a237e]/10 text-[#1a237e] font-medium' : 'text-gray-600 hover:bg-gray-50'"
                        class="flex items-center w-full pl-11 pr-4 py-2 rounded-lg text-sm transition-colors">
                    Dashboard
                </button>
                <button @click.stop="setDmcTab(1)"
                        :class="isDmcActive(1) ? 'bg-[#1a237e]/10 text-[#1a237e] font-medium' : 'text-gray-600 hover:bg-gray-50'"
                        class="flex items-center w-full pl-11 pr-4 py-2 rounded-lg text-sm transition-colors">
                    Isi Daily Check
                </button>
                <button @click.stop="setDmcTab(2)"
                        :class="isDmcActive(2) ? 'bg-[#1a237e]/10 text-[#1a237e] font-medium' : 'text-gray-600 hover:bg-gray-50'"
                        class="flex items-center w-full pl-11 pr-4 py-2 rounded-lg text-sm transition-colors">
                    Micro-Break
                </button>
                @if(in_array(auth()->user()->role->name, ['Super Admin', 'Manager']))
                <button @click.stop="setDmcTab(3)"
                        :class="isDmcActive(3) ? 'bg-[#1a237e]/10 text-[#1a237e] font-medium' : 'text-gray-600 hover:bg-gray-50'"
                        class="flex items-center w-full pl-11 pr-4 py-2 rounded-lg text-sm transition-colors">
                    Laporan
                </button>
                @endif
                @else
                <a href="{{ $dmcRoute }}#dmc=0"
                   class="flex items-center w-full pl-11 pr-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    Dashboard
                </a>
                <a href="{{ $dmcRoute }}#dmc=1"
                   class="flex items-center w-full pl-11 pr-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    Isi Daily Check
                </a>
                <a href="{{ $dmcRoute }}#dmc=2"
                   class="flex items-center w-full pl-11 pr-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    Micro-Break
                </a>
                @if(in_array(auth()->user()->role->name, ['Super Admin', 'Manager']))
                <a href="{{ $dmcRoute }}#dmc=3"
                   class="flex items-center w-full pl-11 pr-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    Laporan
                </a>
                @endif
                @endif
            </div>
        </div>
        <a href="{{ $dmcRoute }}"
           @click="if(window.innerWidth < 1280) sidebarOpen = false"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="hidden xl:flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.daily-mental-check') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="heart" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.daily-mental-check') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="window.innerWidth < 1280 || sidebarOpen" class="font-medium whitespace-nowrap">Daily Mental Check</span>
        </a>
        @endcanAccess

        @canAccess('reports')
        <a href="{{ route('staf.laporan') }}"
           @click="if(window.innerWidth < 1280) sidebarOpen = false"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.laporan') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="file-text" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.laporan') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="window.innerWidth < 1280 || sidebarOpen" class="font-medium whitespace-nowrap">Laporan</span>
        </a>
        @endcanAccess

        @canAccess('manage-products')
        <a href="{{ route('staf.kelola-produk') }}"
           @click="if(window.innerWidth < 1280) sidebarOpen = false"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.kelola-produk') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="package" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.kelola-produk') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="window.innerWidth < 1280 || sidebarOpen" class="font-medium whitespace-nowrap">Kelola Produk</span>
        </a>
        @endcanAccess

        @canAccess('categories')
        <a href="{{ route('staf.kategori') }}"
           @click="if(window.innerWidth < 1280) sidebarOpen = false"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.kategori') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="folder-tree" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.kategori') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="window.innerWidth < 1280 || sidebarOpen" class="font-medium whitespace-nowrap">Kategori</span>
        </a>
        @endcanAccess

        @canAccess('manage-users')
        <a href="{{ route('staf.kelola-pengguna') }}"
           @click="if(window.innerWidth < 1280) sidebarOpen = false"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.kelola-pengguna') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="users" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.kelola-pengguna') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="window.innerWidth < 1280 || sidebarOpen" class="font-medium whitespace-nowrap">Kelola Pengguna</span>
        </a>
        @endcanAccess

        <a href="{{ route('staf.pengaturan') }}"
           @click="if(window.innerWidth < 1280) sidebarOpen = false"
           :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.pengaturan') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="settings" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.pengaturan') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="window.innerWidth < 1280 || sidebarOpen" class="font-medium whitespace-nowrap">Pengaturan</span>
        </a>
    </nav>


    {{-- (Footer profile removed) --}}
</aside>
</div>{{-- end sidebar wrapper --}}
