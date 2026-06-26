{{-- Layout Internal --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Novos') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @notifyCss
    @stack('styles')
    
    <style>
        :root {
            --color-primary: #1a237e;
            --color-primary-rgb: 26, 35, 126;
            --color-secondary: #3949ab;
            --font-size-base: 15px;
            --radius-base: 12px;
            --font-family-base: 'Poppins';

        }

        * { font-family: var(--font-family-base), sans-serif; font-size: var(--font-size-base); }
        [x-cloak] { display: none !important; }

        body.internal-body {
            background: linear-gradient(135deg, #f4f6f9 0%, #eef2f7 100%) !important;
            transition: background 0.4s ease, color 0.3s ease;
            color: #212121;
        }

        /* ── Dynamic Brand Color Mappings ── */
        .bg-\[\#1a237e\] { background-color: var(--color-primary) !important; }
        .bg-\[\#1a237e\]\/90 { background-color: var(--color-primary) !important; }
        .hover\:bg-\[\#283593\]:hover { background-color: var(--color-secondary) !important; }
        .text-\[\#1a237e\] { color: var(--color-primary) !important; }
        .hover\:text-\[\#1a237e\]:hover { color: var(--color-primary) !important; }
        .border-\[\#1a237e\] { border-color: var(--color-primary) !important; }
        
        .bg-\[\#1a237e\]\/5 { background-color: rgba(var(--color-primary-rgb), 0.05) !important; }
        .bg-\[\#1a237e\]\/10 { background-color: rgba(var(--color-primary-rgb), 0.1) !important; }
        .bg-\[\#1a237e\]\/20 { background-color: rgba(var(--color-primary-rgb), 0.2) !important; }
        .bg-\[\#1a237e\]\/80 { background-color: rgba(var(--color-primary-rgb), 0.8) !important; }
        .border-\[\#1a237e\]\/20 { border-color: rgba(var(--color-primary-rgb), 0.2) !important; }
        .border-\[\#1a237e\]\/30 { border-color: rgba(var(--color-primary-rgb), 0.3) !important; }
        .shadow-\[\#1a237e\]\/5 { --tw-shadow-color: rgba(var(--color-primary-rgb), 0.05) !important; }
        .shadow-\[\#1a237e\]\/20 { --tw-shadow-color: rgba(var(--color-primary-rgb), 0.2) !important; }

        .focus\:ring-\[\#1a237e\]:focus, .focus\:ring-\[\#1a237e\/30\]:focus {
            --tw-ring-color: var(--color-primary) !important;
        }
        .focus\:border-\[\#1a237e\]:focus {
            border-color: var(--color-primary) !important;
        }

        /* ── Button Styles ── */
        /* 3D Button Style */
        html[data-btn-style='3d'] button.bg-\[\#1a237e\],
        html[data-btn-style='3d'] button[type='submit'],
        html[data-btn-style='3d'] .btn-primary,
        html[data-btn-style='3d'] [class*="bg-[#1a237e]"],
        html[data-btn-style='3d'] .btn-primary-dynamic {
            border-bottom: none !important;
            box-shadow: 0 4px 0 rgba(0,0,0,0.3) !important;
            transform: translateY(0);
            transition: transform 0.1s, box-shadow 0.1s;
        }
        html[data-btn-style='3d'] button.bg-\[\#1a237e\]:active,
        html[data-btn-style='3d'] button[type='submit']:active,
        html[data-btn-style='3d'] .btn-primary:active,
        html[data-btn-style='3d'] [class*="bg-[#1a237e]"]:active,
        html[data-btn-style='3d'] .btn-primary-dynamic:active {
            box-shadow: 0 1px 0 rgba(0,0,0,0.3) !important;
            transform: translateY(3px) !important;
        }

        /* Outline Button Style */
        html[data-btn-style='outline'] button.bg-\[\#1a237e\],
        html[data-btn-style='outline'] button[type='submit'],
        html[data-btn-style='outline'] .btn-primary,
        html[data-btn-style='outline'] [class*="bg-[#1a237e]"],
        html[data-btn-style='outline'] .btn-primary-dynamic {
            background-color: transparent !important;
            background-image: none !important;
            border: 2px solid var(--color-primary) !important;
            color: var(--color-primary) !important;
            box-shadow: none !important;
            text-shadow: none !important;
        }
        html[data-btn-style='outline'] button.bg-\[\#1a237e\]:hover,
        html[data-btn-style='outline'] button[type='submit']:hover,
        html[data-btn-style='outline'] .btn-primary:hover,
        html[data-btn-style='outline'] [class*="bg-[#1a237e]"]:hover,
        html[data-btn-style='outline'] .btn-primary-dynamic:hover {
            background-color: var(--color-primary) !important;
            color: white !important;
        }

        /* ── Component Rounding (Rectangular to Capsule) ── */
        .rounded-xl,
        .rounded-lg,
        .rounded-md,
        .rounded-sm,
        .rounded,
        .input,
        .select,
        .textarea,
        .btn,
        button {
            border-radius: var(--radius-base) !important;
        }

        /* Large Container Capped Rounding */
        .rounded-2xl,
        .rounded-3xl {
            border-radius: min(var(--radius-base), 24px) !important;
        }

        /* ── Layout Density Overrides ── */
        html[data-density='compact'] .p-5,
        html[data-density='compact'] .p-6,
        html[data-density='compact'] .p-7,
        html[data-density='compact'] .p-8 { padding: 0.85rem !important; }
        html[data-density='compact'] th,
        html[data-density='compact'] td { padding: 0.35rem 0.5rem !important; }
        html[data-density='compact'] .py-3 { padding-top: 0.4rem !important; padding-bottom: 0.4rem !important; }
        html[data-density='compact'] .space-y-6 > * + * { margin-top: 0.75rem !important; }

        html[data-density='spacious'] .p-5,
        html[data-density='spacious'] .p-6,
        html[data-density='spacious'] .p-7,
        html[data-density='spacious'] .p-8 { padding: 2.25rem !important; }
        html[data-density='spacious'] th,
        html[data-density='spacious'] td { padding: 1.25rem 1.5rem !important; }
        html[data-density='spacious'] .py-3 { padding-top: 1.1rem !important; padding-bottom: 1.1rem !important; }
        html[data-density='spacious'] .space-y-6 > * + * { margin-top: 2rem !important; }

        
        /* ── Page Transition Effects ── */
        @keyframes transition-fade-in {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes transition-slide-up-in {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes transition-slide-down-in {
            from { opacity: 0; transform: translateY(-24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes transition-zoom-in {
            from { opacity: 0; transform: scale(0.95); }
            to   { opacity: 1; transform: scale(1); }
        }
        @keyframes transition-flip-in {
            from { opacity: 0; transform: perspective(600px) rotateX(12deg); }
            to   { opacity: 1; transform: perspective(600px) rotateX(0); }
        }
        @keyframes transition-elastic-in {
            0%   { opacity: 0; transform: scale(0.85); }
            60%  { opacity: 1; transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
        }

        html[data-transition='fade'] #transition-content {
            animation: transition-fade-in 0.4s ease both;
        }
        html[data-transition='slide-up'] #transition-content {
            animation: transition-slide-up-in 0.4s ease both;
        }
        html[data-transition='slide-down'] #transition-content {
            animation: transition-slide-down-in 0.4s ease both;
        }
        html[data-transition='zoom-in'] #transition-content {
            animation: transition-zoom-in 0.4s ease both;
        }
        html[data-transition='flip'] #transition-content {
            animation: transition-flip-in 0.5s ease both;
            transform-origin: top center;
        }
        html[data-transition='elastic'] #transition-content {
            animation: transition-elastic-in 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) both;
        }

        /* ── Dark Mode Theme Styles ── */
        body.theme-dark.internal-body {
            background: linear-gradient(135deg, #0e111a 0%, #121422 50%, #0e121d 100%) !important;
            color: #cbd5e1 !important;
        }
        body.theme-dark input,
        body.theme-dark textarea,
        body.theme-dark select {
            background-color: rgba(13, 17, 28, 0.8) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #f8fafc !important;
        }
        body.theme-dark input::placeholder,
        body.theme-dark textarea::placeholder {
            color: #475569 !important;
        }
        body.theme-dark h1,
        body.theme-dark h2,
        body.theme-dark h3,
        body.theme-dark h4,
        body.theme-dark h5,
        body.theme-dark h6,
        body.theme-dark label,
        body.theme-dark p,
        body.theme-dark span,
        body.theme-dark th,
        body.theme-dark td,
        body.theme-dark tr,
        body.theme-dark select option {
            color: #cbd5e1 !important;
        }
        body.theme-dark .text-gray-900,
        body.theme-dark .text-gray-800,
        body.theme-dark .text-gray-700,
        body.theme-dark .text-gray-600,
        body.theme-dark .text-gray-500 {
            color: #cbd5e1 !important;
        }
        body.theme-dark .text-gray-400 {
            color: #94a3b8 !important;
        }
        body.theme-dark .text-\[\#1a237e\] {
            color: #a5b4fc !important;
        }
        body.theme-dark .bg-gray-25,
        body.theme-dark .bg-gray-50,
        body.theme-dark .bg-gray-50\/50,
        body.theme-dark .bg-gray-100,
        body.theme-dark .bg-gray-200 {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #cbd5e1 !important;
        }
        body.theme-dark main table thead tr {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        body.theme-dark main tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
        }
        body.theme-dark .border-gray-100,
        body.theme-dark .border-gray-200,
        body.theme-dark .border-gray-300 {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
    </style>

    {{-- Apply saved appearance BEFORE first paint to avoid flash --}}
    <script>
    (function(){
        try {
            var s = localStorage.getItem('novos_appearance');
            if (!s) return;
            var a = JSON.parse(s);
            var root = document.documentElement;
            if (a.primary) {
                root.style.setProperty('--color-primary', a.primary);
                var hex = a.primary.replace('#', '');
                if (hex.length === 3) {
                    hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
                }
                var r = parseInt(hex.substring(0,2), 16);
                var g = parseInt(hex.substring(2,4), 16);
                var b = parseInt(hex.substring(4,6), 16);
                if (!isNaN(r) && !isNaN(g) && !isNaN(b)) {
                    root.style.setProperty('--color-primary-rgb', r + ',' + g + ',' + b);
                }
            }
            if (a.secondary) root.style.setProperty('--color-secondary', a.secondary);
            var fsMap = {sm:'13px', md:'15px', lg:'17px', xl:'19px'};
            if (a.fontSize)  root.style.setProperty('--font-size-base', fsMap[a.fontSize]||'15px');
            var rrMap = {none:'0px', sm:'6px', xl:'12px', full:'9999px'};
            if (a.rounded)   root.style.setProperty('--radius-base', rrMap[a.rounded]||'12px');
            if (a.buttonStyle) root.setAttribute('data-btn-style', a.buttonStyle);
            if (a.density) root.setAttribute('data-density', a.density);
            var fontMap = { poppins: "'Poppins'", inter: "'Inter'", outfit: "'Outfit'" };
            if (a.fontFamily) root.style.setProperty('--font-family-base', fontMap[a.fontFamily] || "'Poppins'");
            if (a.transition) root.setAttribute('data-transition', a.transition);
            var dark = a.theme==='dark' || (a.theme==='auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (dark) document.documentElement.classList.add('theme-dark-pending');
        } catch(e){}
    })();
    </script>
    {{-- Migrate old cookie name (sidebar.open) → sidebar_open to fix PHP dot-conversion bug --}}
    <script>
        (function () {
            var oldVal = document.cookie.match(/(?:^|;\s*)sidebar\.open=([^;]*)/);
            if (oldVal) {
                var val = oldVal[1];
                // Delete the old cookie
                document.cookie = 'sidebar.open=; path=/; max-age=0';
                // Write new cookie only if sidebar_open not already set
                if (!document.cookie.match(/(?:^|;\s*)sidebar_open=/)) {
                    document.cookie = 'sidebar_open=' + val + '; path=/; SameSite=Lax; max-age=' + (60 * 60 * 24 * 365);
                }
            }
        })();
    </script>
</head>
<body class="internal-body text-[#212121] antialiased flex h-screen overflow-hidden" x-data>
<script>
(function(){
    if (document.documentElement.classList.contains('theme-dark-pending')) {
        document.body.classList.add('theme-dark');
        document.documentElement.classList.remove('theme-dark-pending');
    }
})();
</script>

    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- Topbar -->
        <header class="py-3 px-8 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <button @click="$dispatch('sidebar-toggle')" class="text-gray-500 hover:text-[#1a237e]">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <div>
                    @yield('topbar-left')
                </div>
            </div>

            <!-- Right Section: Chat, Notifikasi, Profil -->
                <div class="flex items-center gap-5">
                <!-- Chat & Notifikasi -->
                <div class="flex items-center gap-4">
                    <div x-data="staffChatBadge()" x-init="init()" class="relative">
                        <a href="{{ route('staf.chat') }}" class="relative p-2 text-gray-500 hover:text-[#1a237e] block">
                            <i data-lucide="message-circle" class="w-5 h-5"></i>
                            <span x-show="unreadCount > 0" x-cloak
                                  x-text="unreadCount > 9 ? '9+' : unreadCount"
                                  class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-red-500 rounded-full min-w-[18px] h-[18px]">
                            </span>
                        </a>
                    </div>
                    {{-- Notifikasi Dropdown --}}
                    <div x-data="notifDropdown()" x-init="init()" class="relative" @mouseenter="open = true" @mouseleave="open = false" @click.away="open = false">
                        <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-[#1a237e] transition-colors">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span x-show="unreadCount > 0" x-cloak
                                  x-text="unreadCount > 9 ? '9+' : unreadCount"
                                  class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-[#1a237e] rounded-full min-w-[18px] h-[18px]">
                            </span>
                        </button>

                        {{-- Dropdown Panel --}}
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                             class="absolute right-0 w-96 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 overflow-hidden"
                             style="top: 100%;">

                            {{-- Header Dropdown --}}
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900">Notifikasi</span>
                                    <span x-show="unreadCount > 0" x-text="unreadCount"
                                          class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold text-white bg-[#1a237e] rounded-full min-w-[18px]">
                                    </span>
                                </div>
                                <button @click="markAllRead()" class="text-xs font-medium text-[#1a237e] hover:underline">Tandai semua dibaca</button>
                            </div>

                            {{-- Notif List --}}
                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                                <template x-for="notif in previewNotifs" :key="notif.id">
                                    <div @click="markRead(notif.id)"
                                         :class="notif.read ? 'bg-white' : 'bg-blue-50/50'"
                                         class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors">
                                        <div class="relative shrink-0">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                                 :style="`background: ${notif.color}`"
                                                 x-text="notif.initials">
                                            </div>
                                            <span x-show="!notif.read"
                                                  class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full">
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-800 leading-relaxed" x-html="notif.message"></p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[10px] text-gray-400" x-text="notif.time"></span>
                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full"
                                                      :class="notif.badgeClass" x-text="notif.badge"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="previewNotifs.length === 0" class="px-4 py-8 text-center">
                                    <p class="text-xs text-gray-400">Tidak ada notifikasi baru</p>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="border-t border-gray-100 px-4 py-2.5 bg-gray-50/50">
                                <a :href="'{{ route('staf.notifikasi') }}'" class="block text-center text-xs font-semibold text-[#1a237e] hover:underline">
                                    Lihat semua notifikasi
                                </a>
                            </div>
                        </div>
                    </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover shrink-0">
                        @else
                            <div class="w-8 h-8 bg-[#1a237e] rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-xs">{{ collect(explode(' ', auth()->user()->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode('') }}</span>
                            </div>
                        @endif
                        <span class="text-gray-700 font-medium text-sm">{{ auth()->user()->role->name ?? auth()->user()->name }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-lg z-50">
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil Saya</a>
                            <a href="{{ route('staf.pengaturan') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Pengaturan</a>
                            <a href="{{ route('beranda') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Beranda</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-8">
            @include('components.alert')
            <div id="transition-content">
                @yield('internal-content')
            </div>
        </main>
        
    </div>

    {{-- Micro-Break Reminder --}}
    <div x-data="microBreakReminder()" x-init="init()">
        {{-- Test button --}}
        <button @click="showTestMenu = !showTestMenu"
            class="fixed bottom-6 left-6 z-[9999] w-10 h-10 bg-gray-200 hover:bg-gray-300 text-gray-500 rounded-full shadow flex items-center justify-center transition-all"
            title="Test Reminder">
            <span class="text-lg font-bold" x-show="!showTestMenu">?</span>
            <span class="text-lg leading-none" x-show="showTestMenu" x-cloak>&times;</span>
        </button>
        <div x-show="showTestMenu" x-cloak @click.away="showTestMenu = false"
            class="fixed bottom-20 left-6 z-[9999] bg-white border border-gray-100 rounded-xl shadow-xl p-3 space-y-1.5 min-w-[180px]">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Test Reminder</p>
            <button @click="triggerReminder(0)" class="block w-full text-left px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">⏰ Jam 10.00</button>
            <button @click="triggerReminder(1)" class="block w-full text-left px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">🧠 Jam 13.00</button>
            <button @click="triggerReminder(2)" class="block w-full text-left px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">😊 Jam 15.00</button>
        </div>
        <template x-teleport="body">
            <div x-show="activeReminder" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 translate-x-4"
                x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 translate-x-0"
                x-transition:leave-end="opacity-0 translate-y-4 translate-x-4"
                class="fixed bottom-6 right-6 z-[9999] w-80">
                <div class="bg-white border border-gray-100 rounded-2xl shadow-2xl p-5 h-44 flex flex-col">

                    <p class="font-semibold text-gray-900" x-text="activeReminder?.title"></p>
                    <div class="text-gray-600 text-sm leading-relaxed flex-1 flex items-center" x-html="activeReminder?.body"></div>
                    <button @click="dismissReminder()"
                        class="w-full py-2 bg-[#1a237e]/5 hover:bg-[#1a237e]/10 text-[#1a237e] font-medium text-sm rounded-xl transition-colors">
                        Baik, Saya Akan Istirahat
                    </button>
                </div>
            </div>
        </template>
    </div>

    <script>
        function microBreakReminder() {
            return {
                shownSlots: JSON.parse(localStorage.getItem('microbreak_shown') || '[]'),
                today: localStorage.getItem('microbreak_date') || '',
                activeReminder: null,
                showTestMenu: false,
                reminders: [
                    {
                        key: '10',
                        icon: '⏰',
                        time: 'Pukul 10.00',
                        title: 'Saatnya Micro-Break!',
                        body: 'Minum air putih, peregangan ringan, dan tarik napas 3 kali. (3–5 menit)',
                        hour: 10,
                        min: 0
                    }, {
                        key: '13',
                        icon: '🧠',
                        time: 'Pukul 13.00',
                        title: 'Istirahat Sejenak',
                        body: 'Tubuh Anda juga membutuhkan perhatian. Lakukan teknik STOP — Stop, Take a breath, Observe, Proceed.',
                        hour: 13,
                        min: 0
                    }, {
                        key: '15',
                        icon: '😊',
                        time: 'Pukul 15.00',
                        title: 'Sudahkah Anda Beristirahat?',
                        body: 'Berdiri, tarik napas, dan kembali bekerja dengan lebih segar.',
                        hour: 15,
                        min: 0
                    }
                ],
                init() {
                    const d = new Date().toDateString();
                    if (this.today !== d) {
                        this.shownSlots = [];
                        this.today = d;
                        try {
                            localStorage.setItem('microbreak_shown', '[]');
                            localStorage.setItem('microbreak_date', d);
                        } catch (e) {}
                    }
                    this.checkTime();
                    setInterval(() => this.checkTime(), 60000);
                },
                checkTime() {
                    const now = new Date();
                    const h = now.getHours();
                    const m = now.getMinutes();
                    for (const r of this.reminders) {
                        if (h === r.hour && m === r.min && !this.shownSlots.includes(r.key)) {
                            this.activeReminder = r;
                            this.shownSlots.push(r.key);
                            try {
                                localStorage.setItem('microbreak_shown', JSON.stringify(this.shownSlots));
                            } catch (e) {}
                            break;
                        }
                    }
                },
                dismissReminder() {
                    this.activeReminder = null;
                },
                triggerReminder(index) {
                    this.activeReminder = this.reminders[index];
                    this.showTestMenu = false;
                }
            }
        }
    </script>

    @include('notify::components.notify')

    <script>
    window.Notify = {
        _icons: {
            success: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 shrink-0 text-green-500"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
            error: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 shrink-0 text-red-500"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            warning: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 shrink-0 text-yellow-500"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            info: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 shrink-0 text-blue-500"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
            close: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'
        },
        _borders: {
            success: 'border-green-500', error: 'border-red-500',
            warning: 'border-yellow-500', info: 'border-blue-500'
        },
        _show(type, message, title, duration) {
            let container = document.getElementById('notify-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'notify-container';
                container.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none';
                document.body.appendChild(container);
            }
            let el = document.createElement('div');
            el.className = 'pointer-events-auto notify-item border-l-4 ' + (this._borders[type] || 'border-blue-500') + ' bg-white rounded-lg shadow-lg p-4 transition-all duration-300 translate-x-4 opacity-0 max-w-sm';
            el.innerHTML = '<div class="flex items-start gap-3">' + (this._icons[type] || this._icons.info) + '<div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900">' + title + '</p><p class="text-sm text-gray-600 mt-1">' + message + '</p></div><button onclick="this.closest(\'.notify-item\').remove()" class="text-gray-400 hover:text-gray-600 shrink-0">' + this._icons.close + '</button></div>';
            container.appendChild(el);
            requestAnimationFrame(function() { el.classList.remove('translate-x-4', 'opacity-0'); });
            setTimeout(function() {
                el.classList.add('translate-x-4', 'opacity-0');
                setTimeout(function() { el.remove(); }, 300);
            }, duration);
        },
        success: function(message, title) { this._show('success', message, title || 'Berhasil', 3000); },
        error: function(message, title) { this._show('error', message, title || 'Gagal', 5000); },
        warning: function(message, title) { this._show('warning', message, title || 'Peringatan', 4000); },
        info: function(message, title) { this._show('info', message, title || 'Informasi', 4000); }
    };
    </script>

    @stack('scripts')
</body>

<script>
function staffChatBadge() {
    return {
        unreadCount: 0,
        init() {
            this.fetchUnread();
            setInterval(() => this.fetchUnread(), 30000);
        },
        async fetchUnread() {
            try {
                const res = await fetch('{{ route("staf.chat.unread-count") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.unreadCount = data.count || 0;
            } catch (e) {}
        }
    }
}

function notifDropdown() {
    return {
        open: false,
        notifications: [],
        get unreadCount() {
            return this.notifications.filter(n => !n.read).length;
        },
        get previewNotifs() {
            return this.notifications.slice(0, 5);
        },
        init() {
            this.loadNotifications();
            setInterval(() => this.loadNotifications(), 30000);
        },
        async loadNotifications() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("staf.notifikasi.preview") }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                });
                const data = await res.json();
                this.notifications = data.notifications;
            } catch (e) {}
        },
        async markRead(id) {
            const n = this.notifications.find(n => n.id === id);
            if (!n || n.read) return;
            n.read = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                await fetch('{{ url("staf/notifikasi") }}/' + id + '/read', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
            } catch (e) {}
        },
        async markAllRead() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("staf.notifikasi.read-all") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.notifications.forEach(n => n.read = true);
                }
            } catch (e) {}
        }
    }
}
</script>
</html>
