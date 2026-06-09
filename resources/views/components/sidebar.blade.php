<aside class="sidebar bg-base-200 min-h-screen w-64 p-4">
    <ul class="menu">
        @php
            $userRole = Auth::check() ? Auth::user()->role->name : null;
        @endphp

        @if(in_array($userRole, ['Admin', 'Manager', 'Super Admin']))
            @php
                $dashboardActive = request()->routeIs('admin.dashboard') || request()->routeIs('superadmin.dashboard') || request()->routeIs('manager.dashboard');
            @endphp
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ $dashboardActive ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 {{ $dashboardActive ? '' : 'text-blue-600' }}"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.summary') }}" class="{{ request()->routeIs('admin.summary') ? 'active' : '' }}">
                    <i data-lucide="trending-up" class="w-5 h-5 {{ request()->routeIs('admin.summary') ? '' : 'text-blue-600' }}"></i>
                    Summary
                </a>
            </li>
            <li>
                <a href="{{ route('admin.daftar-pesanan') }}" class="{{ request()->routeIs('admin.daftar-pesanan') ? 'active' : '' }}">
                    <i data-lucide="clipboard-list" class="w-5 h-5 {{ request()->routeIs('admin.daftar-pesanan') ? '' : 'text-blue-600' }}"></i>
                    Daftar Pesanan
                </a>
            </li>
            <li>
                <a href="{{ route('admin.design') }}" class="{{ request()->routeIs('admin.design') ? 'active' : '' }}">
                    <i data-lucide="palette" class="w-5 h-5 {{ request()->routeIs('admin.design') ? '' : 'text-blue-600' }}"></i>
                    Design
                </a>
            </li>
            <li>
                <a href="{{ route('admin.produksi') }}" class="{{ request()->routeIs('admin.produksi') ? 'active' : '' }}">
                    <i data-lucide="factory" class="w-5 h-5 {{ request()->routeIs('admin.produksi') ? '' : 'text-blue-600' }}"></i>
                    Produksi
                </a>
            </li>
            <li>
                <a href="{{ route('admin.stress-test') }}" class="{{ request()->routeIs('admin.stress-test') ? 'active' : '' }}">
                    <i data-lucide="sparkles" class="w-5 h-5 {{ request()->routeIs('admin.stress-test') ? '' : 'text-blue-600' }}"></i>
                    Stress Test
                </a>
            </li>
            <li>
                <a href="{{ route('admin.laporan') }}" class="{{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
                    <i data-lucide="file-text" class="w-5 h-5 {{ request()->routeIs('admin.laporan') ? '' : 'text-blue-600' }}"></i>
                    Laporan
                </a>
            </li>
            <li>
                <a href="{{ route('admin.kelola-pengguna') }}" class="{{ request()->routeIs('admin.kelola-pengguna') ? 'active' : '' }}">
                    <i data-lucide="users" class="w-5 h-5 {{ request()->routeIs('admin.kelola-pengguna') ? '' : 'text-blue-600' }}"></i>
                    Kelola Pengguna
                </a>
            </li>
        @endif

        @if($userRole === 'Design' || $userRole === 'Super Admin')
            <li>
                <a href="{{ route('design.dashboard') }}" class="{{ request()->routeIs('design.dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('design.dashboard') ? '' : 'text-blue-600' }}"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('design.daftar-pesanan') }}" class="{{ request()->routeIs('design.daftar-pesanan') ? 'active' : '' }}">
                    <i data-lucide="clipboard-list" class="w-5 h-5 {{ request()->routeIs('design.daftar-pesanan') ? '' : 'text-blue-600' }}"></i>
                    Daftar Pesanan
                </a>
            </li>
            <li>
                <a href="{{ route('design.design') }}" class="{{ request()->routeIs('design.design') ? 'active' : '' }}">
                    <i data-lucide="palette" class="w-5 h-5 {{ request()->routeIs('design.design') ? '' : 'text-blue-600' }}"></i>
                    Design
                </a>
            </li>
        @endif

        @if($userRole === 'Produksi' || $userRole === 'Super Admin')
            <li>
                <a href="{{ route('produksi.dashboard') }}" class="{{ request()->routeIs('produksi.dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('produksi.dashboard') ? '' : 'text-blue-600' }}"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('produksi.daftar-pesanan') }}" class="{{ request()->routeIs('produksi.daftar-pesanan') ? 'active' : '' }}">
                    <i data-lucide="clipboard-list" class="w-5 h-5 {{ request()->routeIs('produksi.daftar-pesanan') ? '' : 'text-blue-600' }}"></i>
                    Daftar Pesanan
                </a>
            </li>
            <li>
                <a href="{{ route('produksi.produksi') }}" class="{{ request()->routeIs('produksi.produksi') ? 'active' : '' }}">
                    <i data-lucide="factory" class="w-5 h-5 {{ request()->routeIs('produksi.produksi') ? '' : 'text-blue-600' }}"></i>
                    Produksi
                </a>
            </li>
        @endif

        @if($userRole === 'Customer')
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('dashboard') ? '' : 'text-blue-600' }}"></i>
                    Dashboard
                </a>
            </li>
        @endif
    </ul>
</aside>
