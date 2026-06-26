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
        @php
            $menus = [
                ['label' => 'Dashboard',       'url' => route('staf.dashboard'),       'icon' => 'layout-dashboard', 'active' => request()->routeIs('staf.dashboard')],
                ['label' => 'Summary',         'url' => route('staf.summary'),         'icon' => 'pie-chart',        'active' => request()->routeIs('staf.summary')],
                ['label' => 'Daftar Pesanan',  'url' => route('staf.daftar-pesanan'),  'icon' => 'shopping-bag',     'active' => request()->routeIs('staf.daftar-pesanan') || request()->routeIs('staf.detail-pesanan') || request()->routeIs('staf.chat')],
                ['label' => 'Design',          'url' => route('staf.design'),          'icon' => 'pen-tool',         'active' => request()->routeIs('staf.design')],
                ['label' => 'Produksi',        'url' => route('staf.produksi'),        'icon' => 'scissors',         'active' => request()->routeIs('staf.produksi')],
                ['label' => 'Daily Mental Check', 'url' => route('staf.daily-mental-check'), 'icon' => 'heart',         'active' => request()->routeIs('staf.daily-mental-check')],
                ['label' => 'Laporan',         'url' => route('staf.laporan'),         'icon' => 'file-text',        'active' => request()->routeIs('staf.laporan')],
                ['label' => 'Kelola Produk',   'url' => route('staf.kelola-produk'),   'icon' => 'package',          'active' => request()->routeIs('staf.kelola-produk')],
                ['label' => 'Kategori',        'url' => route('staf.kategori'),        'icon' => 'folder-tree',      'active' => request()->routeIs('staf.kategori')],
                ['label' => 'Kelola Pengguna', 'url' => route('staf.kelola-pengguna'), 'icon' => 'users',            'active' => request()->routeIs('staf.kelola-pengguna')],
                ['label' => 'Pengaturan',      'url' => route('staf.pengaturan'),      'icon' => 'settings',         'active' => request()->routeIs('staf.pengaturan')],
            ];
        @endphp

        @foreach($menus as $menu)
            <a href="{{ $menu['url'] }}"
               :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
               class="flex items-center py-3 rounded-xl transition-colors {{ $menu['active'] ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <i data-lucide="{{ $menu['icon'] }}" class="w-5 h-5 shrink-0 {{ $menu['active'] ? 'text-white' : 'text-[#1a237e]' }}"></i>
                <span x-show="sidebarOpen"
                      @if(!$isSidebarOpen) style="display:none" @endif
                      class="font-medium whitespace-nowrap">
                    {{ $menu['label'] }}
                </span>
            </a>
        @endforeach
    </nav>

    {{-- (Footer profile removed) --}}
</aside>
