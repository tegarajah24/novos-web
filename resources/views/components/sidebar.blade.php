<?php
// PHP converts dots in cookie names to underscores in $_COOKIE superglobal
// 'sidebar.open' (JS) → 'sidebar_open' (PHP). Always use sidebar_open.
$isSidebarOpen = !(isset($_COOKIE['sidebar_open']) && $_COOKIE['sidebar_open'] === 'false');
?>

{{-- Sidebar wrapper: shared Alpine scope for backdrop + aside --}}
<div
    x-data="{
        sidebarOpen: {{ $isSidebarOpen ? 'true' : 'false' }},
        mobileOpen: false,
        isDesktop: window.innerWidth >= 1280,
        toggle() {
            if (this.isDesktop) {
                this.sidebarOpen = !this.sidebarOpen;
                document.cookie = 'sidebar_open=' + this.sidebarOpen + '; path=/; SameSite=Lax; max-age=' + (60 * 60 * 24 * 365);
            } else {
                this.mobileOpen = !this.mobileOpen;
            }
        }
    }"
    @sidebar-toggle.window="toggle()"
    @resize.window="isDesktop = window.innerWidth >= 1280"
    class="contents">

    {{-- Backdrop for mobile sidebar --}}
    <div x-show="mobileOpen" x-cloak
         @click="mobileOpen = false"
         class="fixed inset-0 z-40 bg-black/50 xl:hidden">
    </div>
    <aside
        :class="isDesktop ? '' : (mobileOpen ? 'translate-x-0' : '-translate-x-full')"
        :style="isDesktop ? 'width: ' + (sidebarOpen ? '16rem' : '5rem') + '; transition: all 0.3s ease;' : 'transition: all 0.3s ease;'"
        style="width: {{ $isSidebarOpen ? '16rem' : '5rem' }}; transition: all 0.3s ease;"
        class="bg-white min-h-screen border-r border-gray-200 flex flex-col shrink-0 z-50
               fixed inset-y-0 left-0 w-64 -translate-x-full
               xl:relative xl:z-auto xl:block xl:translate-x-0 transition-all duration-300 ease-in-out">

    {{-- Logo Area --}}
    <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200 overflow-hidden">
        <a href="{{ route('staf.dashboard') }}" @click="if(!isDesktop) mobileOpen = false" class="flex items-center gap-1">
<div class="w-10 h-10 shrink-0 flex items-center justify-center">
    <img src="{{ asset('images/logo.png') }}" alt="Novos Logo" class="w-10 h-10 object-contain">
</div>

            <span x-show="!isDesktop || sidebarOpen"
                  @if(!$isSidebarOpen) style="display:none" @endif
                  class="text-xl font-bold text-gray-900 whitespace-nowrap">
                Novos
            </span>
        </a>

        {{-- Close button for mobile --}}
        <button @click="mobileOpen = false"
                class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors xl:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    {{-- Menu Items --}}
    <nav @click="if(!isDesktop) mobileOpen = false" class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        @canAccess('dashboard')
        <a href="{{ route('staf.dashboard') }}"
           data-ajax-nav
           :class="(!isDesktop || sidebarOpen) ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.dashboard') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.dashboard') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="!isDesktop || sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Dashboard</span>
        </a>
        @endcanAccess

        @canAccess('summary')
        <a href="{{ route('staf.summary') }}"
           data-ajax-nav
           :class="(!isDesktop || sidebarOpen) ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.summary') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="pie-chart" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.summary') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="!isDesktop || sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Summary</span>
        </a>
        @endcanAccess

        @canAccess('orders')
        <a href="{{ route('staf.daftar-pesanan') }}"
           data-ajax-nav
           :class="(!isDesktop || sidebarOpen) ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.daftar-pesanan') || request()->routeIs('staf.detail-pesanan') || request()->routeIs('staf.chat') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="shopping-bag" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.daftar-pesanan') || request()->routeIs('staf.detail-pesanan') || request()->routeIs('staf.chat') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="!isDesktop || sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Daftar Pesanan</span>
        </a>
        @endcanAccess

        @canAccess('design')
        <a href="{{ route('staf.design') }}"
           data-ajax-nav
           :class="(!isDesktop || sidebarOpen) ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.design') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="pen-tool" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.design') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="!isDesktop || sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Design</span>
        </a>
        @endcanAccess

        @canAccess('production')
        <a href="{{ route('staf.produksi') }}"
           data-ajax-nav
           :class="(!isDesktop || sidebarOpen) ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.produksi') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="scissors" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.produksi') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="!isDesktop || sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Produksi</span>
        </a>
        @endcanAccess

        @canAccess('daily-mental-check')
        <a href="{{ route('staf.daily-mental-check') }}"
           data-ajax-nav
           :class="(!isDesktop || sidebarOpen) ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.daily-mental-check') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="heart" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.daily-mental-check') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="!isDesktop || sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Daily Mental Check</span>
        </a>
        @endcanAccess

        @canAccess('reports')
        <a href="{{ route('staf.laporan') }}"
           data-ajax-nav
           :class="(!isDesktop || sidebarOpen) ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.laporan') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="file-text" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.laporan') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="!isDesktop || sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Laporan</span>
        </a>
        @endcanAccess

        @canAccess('manage-products')
        <a href="{{ route('staf.kelola-produk') }}"
           data-ajax-nav
           :class="(!isDesktop || sidebarOpen) ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.kelola-produk') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="package" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.kelola-produk') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="!isDesktop || sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Kelola Produk</span>
        </a>
        @endcanAccess

        @canAccess('categories')
        <a href="{{ route('staf.kategori') }}"
           data-ajax-nav
           :class="(!isDesktop || sidebarOpen) ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.kategori') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="folder-tree" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.kategori') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="!isDesktop || sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Kategori</span>
        </a>
        @endcanAccess

        @canAccess('manage-users')
        <a href="{{ route('staf.kelola-pengguna') }}"
           data-ajax-nav
           :class="(!isDesktop || sidebarOpen) ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.kelola-pengguna') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="users" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.kelola-pengguna') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="!isDesktop || sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Kelola Pengguna</span>
        </a>
        @endcanAccess

        <a href="{{ route('staf.pengaturan') }}"
           data-ajax-nav
           :class="(!isDesktop || sidebarOpen) ? 'justify-start gap-3 px-4' : 'justify-center gap-0 px-0'"
           class="flex items-center py-3 rounded-xl transition-colors {{ request()->routeIs('staf.pengaturan') ? 'bg-[#1a237e]/90 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <i data-lucide="settings" class="w-5 h-5 shrink-0 {{ request()->routeIs('staf.pengaturan') ? 'text-white' : 'text-[#1a237e]' }}"></i>
            <span x-show="!isDesktop || sidebarOpen" @if(!$isSidebarOpen) style="display:none" @endif class="font-medium whitespace-nowrap">Pengaturan</span>
        </a>
    </nav>

    {{-- (Footer profile removed) --}}
</aside>
</div>{{-- end sidebar wrapper --}}

<script>
window.navigateAjax = function (url, isPopState) {
    if (!url || url === window.location.href) return;
    const main = document.querySelector('main');
    if (!main) { window.location.href = url; return; }

    if (window.FilePond) {
        document.querySelectorAll('.filepond').forEach(function(el) {
            var pond = window.FilePond.find(el);
            if (pond) pond.destroy();
        });
    }
    main.innerHTML = '<div class="flex items-center justify-center py-20"><div class="flex flex-col items-center gap-3"><div class="w-8 h-8 border-2 border-[#1a237e] border-t-transparent rounded-full animate-spin"></div><p class="text-sm text-gray-500">Memuat...</p></div></div>';

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html, */*'
        }
    })
    .then(r => {
        if (!r.ok) throw new Error('Page load failed');
        return r.text();
    })
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const newTitle = doc.getElementById('ajax-page-title')?.textContent || document.title;
        const newTopbar = doc.getElementById('ajax-topbar-title')?.innerHTML || '';
        const newContent = doc.getElementById('ajax-content')?.innerHTML || '';

        if (newContent) {
            document.title = newTitle;
            const topbarEl = document.querySelector('#topbar-title');
            if (topbarEl) topbarEl.innerHTML = newTopbar;
            if (main) {
                main.innerHTML = '<div class="animate-entrance">' + newContent + '</div>';
            }
            if (!isPopState) window.history.pushState({ url: url }, '', url);
            updateSidebarActive(url);
            var allScripts = main.querySelectorAll('script');
            allScripts.forEach(function(oldScript) {
                var newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(function(attr) {
                    newScript.setAttribute(attr.name, attr.value);
                });
                newScript.textContent = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });
            if (window.lucide && window.lucide.createIcons) {
                window.lucide.createIcons({ icons: window.lucide.icons });
            }
            if (window.Alpine) {
                window.Alpine.initTree(main);
            }
            if (window.FilePond) {
                window.FilePond.parse(main);
            }
        }
    })
    .catch(function() { window.location.href = url; });
}

function updateSidebarActive(url) {
    document.querySelectorAll('nav a[data-ajax-nav]').forEach(function(link) {
        var href = link.getAttribute('href');
        var isActive = url === href || (url + '/') === href || (url.endsWith('/') && url.slice(0, -1) === href);
        link.classList.remove('bg-[#1a237e]/90', 'text-white');
        link.classList.add('text-gray-700', 'hover:bg-gray-100');
        var icon = link.querySelector('[data-lucide]');
        if (icon) {
            icon.classList.remove('text-white');
            icon.classList.add('text-[#1a237e]');
        }
        if (isActive) {
            link.classList.add('bg-[#1a237e]/90', 'text-white');
            link.classList.remove('text-gray-700', 'hover:bg-gray-100');
            if (icon) {
                icon.classList.add('text-white');
                icon.classList.remove('text-[#1a237e]');
            }
        }
    });
}

document.addEventListener('click', function (e) {
    var link = e.target.closest('a[data-ajax-nav]');
    if (!link) return;
    var url = link.getAttribute('href');
    if (!url || url === window.location.href || url.startsWith('#')) return;
    e.preventDefault();
    navigateAjax(url);
});

window.addEventListener('popstate', function (e) {
    if (e.state && e.state.url) {
        navigateAjax(e.state.url, true);
    }
});
</script>
