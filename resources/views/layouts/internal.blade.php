{{-- Layout Internal --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Novos') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }

        body.internal-body {
            background: linear-gradient(135deg, #f0f4ff 0%, #f5f3ff 50%, #f0fdf4 100%) !important;
        }

        .glass-sidebar {
            background: rgba(255, 255, 255, 0.68) !important;
            backdrop-filter: blur(8px) saturate(110%) !important;
            -webkit-backdrop-filter: blur(8px) saturate(110%) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.45) !important;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.72) !important;
            backdrop-filter: blur(8px) saturate(110%) !important;
            -webkit-backdrop-filter: blur(8px) saturate(110%) !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04) !important;
        }

        main .bg-white {
            background-color: rgba(255, 255, 255, 0.75) !important;
            backdrop-filter: blur(6px) saturate(105%) !important;
            -webkit-backdrop-filter: blur(6px) saturate(105%) !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
        }

        main table thead tr {
            background: rgba(255, 255, 255, 0.5) !important;
        }
        main tbody tr:hover {
            background: rgba(255, 255, 255, 0.55) !important;
        }
        main .bg-gray-50,
        main .bg-gray-50\/50 {
            background: rgba(255, 255, 255, 0.5) !important;
        }
    </style>
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
            <div class="flex items-center gap-8">
                <!-- Chat & Notifikasi -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('staf.chat') }}" class="relative p-2 text-gray-500 hover:text-[#1a237e]">
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-[#1a237e] rounded-full min-w-[18px] h-[18px]">18</span>
                    </a>
                    <a href="#" class="relative p-2 text-gray-500 hover:text-[#1a237e]">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-[#1a237e] rounded-full min-w-[18px] h-[18px]">52</span>
                    </a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                        <div class="w-8 h-8 bg-[#1a237e] rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-xs">SA</span>
                        </div>
                        <span class="text-gray-700 font-medium text-sm">Super Admin</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-48 bg-white/90 backdrop-blur-xl border border-white/60 rounded-xl shadow-lg z-50">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil Saya</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Pengaturan</a>
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
            @yield('internal-content')
        </main>
        
    </div>

</body>
</html>
