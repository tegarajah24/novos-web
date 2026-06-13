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
    class="glass-sidebar min-h-screen border-r border-white/30 flex flex-col shrink-0">

    {{-- Logo Area --}}
    <div class="h-16 flex items-center px-6 border-b border-gray-200 overflow-hidden">
        <a href="{{ url('/staf/dashboard') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#1a237e] rounded-lg flex items-center justify-center shrink-0">
                <span class="text-white font-bold text-lg">N</span>
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
                ['label' => 'Dashboard',       'url' => url('staf/dashboard'),       'icon' => 'layout-dashboard', 'active' => request()->is('staf/dashboard')],
                ['label' => 'Summary',         'url' => url('staf/summary'),         'icon' => 'pie-chart',        'active' => request()->is('staf/summary')],
                ['label' => 'Daftar Pesanan',  'url' => url('staf/daftar-pesanan'),  'icon' => 'shopping-bag',     'active' => request()->is('staf/daftar-pesanan') || request()->is('staf/detail-pesanan*') || request()->is('staf/chat*')],
                ['label' => 'Design',          'url' => url('staf/design'),          'icon' => 'pen-tool',         'active' => request()->is('staf/design')],
                ['label' => 'Produksi',        'url' => url('staf/produksi'),        'icon' => 'scissors',         'active' => request()->is('staf/produksi')],
                ['label' => 'Stress Test',     'url' => url('staf/stress-test'),     'icon' => 'activity',         'active' => request()->is('staf/stress-test')],
                ['label' => 'Laporan',         'url' => url('staf/laporan'),         'icon' => 'file-text',        'active' => request()->is('staf/laporan')],
                ['label' => 'Kelola Produk',   'url' => url('staf/kelola-produk'),   'icon' => 'package',          'active' => request()->is('staf/kelola-produk')],
                ['label' => 'Kelola Pengguna', 'url' => url('staf/kelola-pengguna'), 'icon' => 'users',            'active' => request()->is('staf/kelola-pengguna*')],
            ];
        @endphp

        @foreach($menus as $menu)
            <a href="{{ $menu['url'] }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ $menu['active'] ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-white/25' }}">
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
