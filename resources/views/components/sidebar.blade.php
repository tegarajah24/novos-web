<aside class="w-64 bg-white min-h-screen border-r border-gray-200 flex flex-col shrink-0">
    <!-- Logo Area -->
    <div class="h-16 flex items-center px-6 border-b border-gray-200">
        <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#1a237e] rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-lg">N</span>
            </div>
            <span class="text-xl font-bold text-gray-900">Novos</span>
        </a>
    </div>

    <!-- Menu Items -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        @php
            $menus = [
                ['label' => 'Dashboard', 'url' => url('admin/dashboard'), 'icon' => 'layout-dashboard', 'active' => request()->is('admin/dashboard')],
                ['label' => 'Summary', 'url' => url('admin/summary'), 'icon' => 'pie-chart', 'active' => request()->is('admin/summary')],
                ['label' => 'Daftar Pesanan', 'url' => url('admin/daftar-pesanan'), 'icon' => 'shopping-bag', 'active' => request()->is('admin/daftar-pesanan*') || request()->is('internal/detail-pesanan*') || request()->is('internal/chat*')],
                ['label' => 'Design', 'url' => url('admin/design'), 'icon' => 'pen-tool', 'active' => request()->is('admin/design')],
                ['label' => 'Produksi', 'url' => url('admin/produksi'), 'icon' => 'scissors', 'active' => request()->is('admin/produksi')],
                ['label' => 'Stress Test', 'url' => url('admin/stress-test'), 'icon' => 'activity', 'active' => request()->is('admin/stress-test')],
                ['label' => 'Laporan', 'url' => url('admin/laporan'), 'icon' => 'file-text', 'active' => request()->is('admin/laporan')],
                ['label' => 'Kelola Produk', 'url' => url('admin/kelola-produk'), 'icon' => 'package', 'active' => request()->is('admin/kelola-produk')],
                ['label' => 'Kelola Pengguna', 'url' => url('admin/kelola-pengguna'), 'icon' => 'users', 'active' => request()->is('admin/kelola-pengguna*')],
            ];
        @endphp

        @foreach($menus as $menu)
            <a href="{{ $menu['url'] }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ $menu['active'] ? 'bg-[#1a237e] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <i data-lucide="{{ $menu['icon'] }}" class="w-5 h-5 {{ $menu['active'] ? 'text-white' : '' }}"></i>
                <span class="font-medium">{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <!-- (Footer profile removed) -->
</aside>
