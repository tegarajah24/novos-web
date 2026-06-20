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
                <div class="flex items-center gap-5">
                <!-- Chat & Notifikasi -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('staf.chat') }}" class="relative p-2 text-gray-500 hover:text-[#1a237e]">
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-[#1a237e] rounded-full min-w-[18px] h-[18px]">18</span>
                    </a>
                    {{-- Notifikasi Dropdown --}}
                    <div x-data="notifDropdown()" class="relative" @click.away="open = false">
                        <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-[#1a237e] transition-colors">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span x-show="unreadCount > 0" x-cloak
                                  x-text="unreadCount > 9 ? '9+' : unreadCount"
                                  class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-red-500 rounded-full min-w-[18px] h-[18px] animate-pulse">
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
                             class="absolute right-0 mt-2 w-96 bg-white/95 backdrop-blur-xl border border-white/60 rounded-2xl shadow-2xl z-50 overflow-hidden"
                             style="top: 100%;">

                            {{-- Header Dropdown --}}
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900">Notifikasi</span>
                                    <span x-show="unreadCount > 0" x-text="unreadCount"
                                          class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold text-white bg-red-500 rounded-full min-w-[18px]">
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
                                        {{-- Avatar / Dot --}}
                                        <div class="relative shrink-0">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                                 :style="`background: ${notif.color}`"
                                                 x-text="notif.initials">
                                            </div>
                                            <span x-show="!notif.read"
                                                  class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full">
                                            </span>
                                        </div>
                                        {{-- Content --}}
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
            @yield('internal-content')
        </main>
        
    </div>

</body>

<script>
function notifDropdown() {
    return {
        open: false,
        notifications: [
            {
                id: 1, read: false, initials: 'CS',
                color: '#e53e3e',
                message: 'Customer <strong>Budi Santoso</strong> melakukan <strong>ACC</strong> pesanan <strong class="text-[#1a237e]">NVS-20240620-003</strong>. Siap diteruskan ke tim Design.',
                time: '5 menit lalu', badge: 'Disetujui', badgeClass: 'bg-green-100 text-green-700'
            },
            {
                id: 2, read: false, initials: 'AD',
                color: '#1a237e',
                message: 'Pesanan baru masuk dari <strong>Rizky Pratama</strong> — <strong class="text-[#1a237e]">NVS-20240620-004</strong>. Segera lakukan konfirmasi.',
                time: '22 menit lalu', badge: 'Pending', badgeClass: 'bg-yellow-100 text-yellow-700'
            },
            {
                id: 3, read: false, initials: 'DS',
                color: '#6b46c1',
                message: 'Tim <strong>Design</strong> (Riko) menyelesaikan desain untuk <strong class="text-[#1a237e]">NVS-20240620-001</strong>. Status berubah ke <strong>Siap Cetak</strong>.',
                time: '1 jam lalu', badge: 'Siap Cetak', badgeClass: 'bg-purple-100 text-purple-700'
            },
            {
                id: 4, read: false, initials: 'PR',
                color: '#2d7a43',
                message: 'Tim <strong>Produksi</strong> (Sari) menyelesaikan pesanan <strong class="text-[#1a237e]">NVS-20240619-007</strong>. Status berubah ke <strong>Selesai</strong>.',
                time: '3 jam lalu', badge: 'Selesai', badgeClass: 'bg-blue-100 text-blue-700'
            },
            {
                id: 5, read: true, initials: 'CS',
                color: '#d97706',
                message: 'Customer <strong>Dewi Anggraini</strong> mengirim pesan baru pada pesanan <strong class="text-[#1a237e]">NVS-20240619-005</strong>.',
                time: '1 hari lalu', badge: 'Pesan', badgeClass: 'bg-gray-100 text-gray-600'
            },
        ],
        get unreadCount() {
            return this.notifications.filter(n => !n.read).length;
        },
        get previewNotifs() {
            return this.notifications.slice(0, 4);
        },
        markRead(id) {
            const n = this.notifications.find(n => n.id === id);
            if (n) n.read = true;
        },
        markAllRead() {
            this.notifications.forEach(n => n.read = true);
        }
    }
}
</script>
</html>
