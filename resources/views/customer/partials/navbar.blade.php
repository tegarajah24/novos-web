{{-- ============================================================ --}}
{{-- NAVBAR CUSTOMER --}}
{{-- ============================================================ --}}
<nav x-data="{ lastScroll: 0, hidden: false }"
     @scroll.window="let y = window.scrollY; if (y > lastScroll && y > 80) { hidden = true } else if (y < lastScroll) { hidden = false }; lastScroll = y"
     :class="hidden ? '-translate-y-full' : 'translate-y-0'"
     class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[1440px] h-16 bg-white shadow-[0_1px_4px_rgba(0,0,0,0.06)] z-50 transition-transform duration-300">
    <div class="max-w-[1200px] mx-auto px-6 h-full flex items-center justify-between">
        {{-- Left: Logo --}}
        <a href="{{ route('customer.beranda') }}" class="text-[#1a237e] text-2xl font-extrabold tracking-tight">NOVOS</a>

        {{-- Center: Nav links --}}
        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('customer.beranda') }}"
               class="nav-link text-sm font-medium transition-colors {{ request()->routeIs('customer.beranda') ? 'font-semibold text-[#1a237e] nav-link-active' : 'text-[#616161] hover:text-[#1a237e]' }}">Beranda</a>

            <a href="{{ route('customer.tentang') }}"
               class="nav-link text-sm font-medium transition-colors {{ request()->routeIs('customer.tentang') ? 'font-semibold text-[#1a237e] nav-link-active' : 'text-[#616161] hover:text-[#1a237e]' }}">Tentang Kami</a>

            <div x-data="{ katalogOpen: false, hoverTimer: null }" @mouseenter="clearTimeout(hoverTimer); katalogOpen = true" @mouseleave="hoverTimer = setTimeout(() => katalogOpen = false, 150)" class="relative flex items-center">
                <a href="{{ route('customer.katalog') }}"
                   class="nav-link text-sm font-medium transition-colors {{ request()->routeIs('customer.katalog') ? 'font-semibold text-[#1a237e] nav-link-active' : 'text-[#616161] hover:text-[#1a237e]' }}">Katalog</a>
                <div x-show="katalogOpen" x-cloak @mouseenter="clearTimeout(hoverTimer); katalogOpen = true" @mouseleave="hoverTimer = setTimeout(() => katalogOpen = false, 150)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute top-full left-0 pt-2 w-64 z-50">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 py-2">
                    <a href="{{ route('customer.katalog') }}"
                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors font-medium">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                        Semua Produk
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <a href="{{ route('customer.katalog', ['kategori' => 'running']) }}"
                       class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors">Jersey Running</a>
                    <a href="{{ route('customer.katalog', ['kategori' => 'sepak-bola-futsal']) }}"
                       class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors">Jersey Sepak Bola / Futsal</a>
                    <a href="{{ route('customer.katalog', ['kategori' => 'tenis']) }}"
                       class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors">Jersey Tenis</a>
                    <a href="{{ route('customer.katalog', ['kategori' => 'basket']) }}"
                       class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors">Jersey Basket</a>
                    <a href="{{ route('customer.katalog', ['kategori' => 'gym-training']) }}"
                       class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors">Jersey Gym / Training</a>
                    </div>
                </div>
            </div>

            <a href="{{ route('customer.pemesanan') }}"
               class="nav-link text-sm font-medium transition-colors {{ request()->routeIs('customer.pemesanan') ? 'font-semibold text-[#1a237e] nav-link-active' : 'text-[#616161] hover:text-[#1a237e]' }}">Buat Pesanan</a>
        </div>

        {{-- Right: Auth --}}
        <div class="flex items-center gap-3" x-data="authSidebar()">
            @auth
                {{-- Wrap with password sidebar state --}}
                <div x-data="{ passwordOpen: false, profileOpen: false }" class="flex items-center gap-3">
                {{-- Tracking icon --}}
                <a href="{{ route('customer.tracking') }}" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Tracking Pesanan">
                    <svg class="w-6 h-6 text-[#616161] hover:text-[#1a237e] transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                </a>

                {{-- Chat icon --}}
                <a href="{{ route('customer.chat') }}" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Chat">
                    <svg class="w-6 h-6 text-[#616161] hover:text-[#1a237e] transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </a>

                {{-- User dropdown --}}
                <div class="relative" x-data="{ userOpen: false, hoverTimer: null }"
                     @mouseenter="clearTimeout(hoverTimer); userOpen = true"
                     @mouseleave="hoverTimer = setTimeout(() => userOpen = false, 150)">
                    <button @click="userOpen = !userOpen"
                        class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                        <svg class="w-7 h-7 text-[#1a237e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-sm font-medium text-[#1a237e] hidden sm:block">{{ Auth::user()->name }}</span>
                        <svg class="w-3.5 h-3.5 text-gray-400 hidden sm:block transition-transform" :class="userOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>

                    <div x-show="userOpen" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute right-0 top-full z-[70]">
                        <div class="h-2"></div>
                        <div class="w-56 bg-white border border-gray-100 rounded-xl shadow-lg py-2">
                        {{-- User info --}}
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        {{-- Menu items --}}
                        <button @click="profileOpen = true"
                           class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors text-left">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Profil Saya
                        </button>
                        <button @click="passwordOpen = true"
                           class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors text-left">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                            Ganti Password
                        </button>

                        {{-- Dashboard (khusus internal) --}}
                        @if(Auth::user()->role?->name !== 'Customer')
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                            Dashboard
                        </a>
                        @endif

                        <a href="{{ route('customer.pemesanan') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5A2.5 2.5 0 0 1 4 19.5Z"/><path d="M12 6v7l2-2 2 2V6"/></svg>
                            Buat Pesanan
                        </a>
                        <a href="{{ route('customer.tracking') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                            Tracking
                        </a>

                        {{-- Logout --}}
                        <div class="border-t border-gray-100 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ===== OFF-CANVAS GANTI PASSWORD ===== --}}
                <template x-teleport="body">
                    <div x-show="passwordOpen" x-cloak
                        class="fixed inset-0 z-[60]" @click.away="passwordOpen = false">
                        <div class="absolute inset-0 bg-black/40" @click="passwordOpen = false"></div>
                        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl overflow-y-auto">
                            {{-- Header --}}
                            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                                <h2 class="text-lg font-bold text-gray-900">Ganti Password</h2>
                                <button @click="passwordOpen = false" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="p-6">
                                <p class="text-sm text-gray-500 mb-6">Pastikan akun Anda menggunakan password yang kuat dan aman.</p>

                                <form method="POST" action="{{ route('password.update') }}" @submit.prevent="if ($event.target.checkValidity()) $event.target.submit()">
                                    @csrf
                                    @method('put')

                                    <div class="space-y-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat Ini</label>
                                            <input type="password" name="current_password" required autocomplete="current-password"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm"
                                                placeholder="Masukkan password saat ini">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                                            <input type="password" name="password" required autocomplete="new-password" minlength="8"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm"
                                                placeholder="Minimal 8 karakter">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                                            <input type="password" name="password_confirmation" required autocomplete="new-password"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm"
                                                placeholder="Ulangi password baru">
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="w-full mt-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                        Simpan Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- ===== OFF-CANVAS PROFIL ===== --}}
                <template x-teleport="body">
                    <div x-show="profileOpen" x-cloak
                        class="fixed inset-0 z-[60]" @click.away="profileOpen = false">
                        <div class="absolute inset-0 bg-black/40" @click="profileOpen = false"></div>
                        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl overflow-y-auto">
                            {{-- Header --}}
                            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                                <h2 class="text-lg font-bold text-gray-900">Profil Saya</h2>
                                <button @click="profileOpen = false" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="p-6">
                                {{-- Avatar section --}}
                                <div class="flex items-center gap-4 pb-6 border-b border-gray-100 mb-6">
                                    <div class="w-16 h-16 rounded-full bg-[#e8eaf6] flex items-center justify-center">
                                        <svg class="w-8 h-8 text-[#1a237e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>

                                {{-- Form --}}
                                <form method="POST" action="{{ route('profile.update') }}" @submit.prevent="if ($event.target.checkValidity()) $event.target.submit()">
                                    @csrf
                                    @method('patch')

                                    <div class="space-y-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required autocomplete="name"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required autocomplete="username"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="w-full mt-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                        Simpan Perubahan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
                </div>
            @else
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @click.away="open = false">
                    <svg class="w-8 h-8 text-[#9e9e9e] hover:text-[#1a237e] transition-colors cursor-pointer" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div x-show="open" x-cloak @mouseenter="open = true"
                        class="absolute right-0 top-full mt-2 w-44 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-[70]">
                        <a href="#" @click.prevent="open = false; openSidebar('login')"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                            Masuk
                        </a>
                        <a href="#" @click.prevent="open = false; openSidebar('register')"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1a237e] transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            Daftar
                        </a>
                    </div>
                </div>
            @endauth

            {{-- ===== OFF-CANVAS AUTH SIDEBAR ===== --}}
            <template x-teleport="body">
                <div x-show="sidebarOpen" x-cloak
                    class="fixed inset-0 z-[60]" @click.away="sidebarOpen = false">
                    {{-- Backdrop --}}
                    <div class="absolute inset-0 bg-black/40" @click="sidebarOpen = false"></div>

                    {{-- Panel --}}
                    <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl overflow-y-auto"
                        @click.outside="sidebarOpen = false">
                        {{-- Header --}}
                        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                            <h2 class="text-lg font-bold text-gray-900" x-text="tab === 'login' ? 'Masuk' : 'Daftar Akun'"></h2>
                            <button @click="sidebarOpen = false" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>

                        {{-- Tab switcher --}}
                        <div class="flex border-b border-gray-100 px-6">
                            <button @click="tab = 'login'" :class="tab === 'login' ? 'text-[#1a237e] border-b-2 border-[#1a237e] font-semibold' : 'text-gray-500 hover:text-gray-700'" class="py-3 px-4 text-sm transition-colors">Masuk</button>
                            <button @click="tab = 'register'" :class="tab === 'register' ? 'text-[#1a237e] border-b-2 border-[#1a237e] font-semibold' : 'text-gray-500 hover:text-gray-700'" class="py-3 px-4 text-sm transition-colors">Daftar</button>
                        </div>

                        {{-- Form --}}
                        <div class="p-6">
                            {{-- Login --}}
                            <form x-show="tab === 'login'" x-cloak method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                                        <input type="text" name="name" :value="old('name')" required autofocus autocomplete="username"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                                        <input type="password" name="password" required autocomplete="current-password"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                                    </div>
                                    <p class="text-xs text-gray-400 -mt-1">
                                        Belum punya akun?
                                        <button type="button" @click="tab = 'register'" class="text-[#1a237e] font-medium hover:underline">Daftar</button>
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#1a237e] accent-[#1a237e]" checked>
                                            <span class="text-sm text-gray-600">Ingat saya</span>
                                        </label>
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="text-sm text-[#1a237e] hover:underline">Lupa password?</a>
                                        @endif
                                    </div>
                                </div>

                            <button type="submit"
                                class="w-full mt-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors flex items-center justify-center gap-2">
                                Masuk
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                            </button>
                        </form>

                        {{-- Register --}}
                        <form x-show="tab === 'register'" x-cloak method="POST" action="{{ route('register') }}" @submit.prevent="submitForm($event.target)">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                                    <input type="text" name="name" :value="old('name')" required autofocus autocomplete="username"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                                    <input type="email" name="email" :value="old('email')" required autocomplete="username"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                                    <input type="password" name="password" required autocomplete="new-password"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                                </div>
                            </div>

                            {{-- Checklists --}}
                            <div class="mt-6 space-y-3 border-t border-gray-100 pt-5"
                                 :class="showWarning ? 'animate-shake' : ''">
                                {{-- Warning banner --}}
                                <div x-show="showWarning" x-cloak
                                     @click="dismissWarn"
                                     class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3 cursor-pointer transition-all duration-300"
                                     x-transition:enter="animate-bounceIn"
                                     x-transition:leave="animate-fadeOut">
                                    <span class="text-2xl shrink-0">🥹</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-red-700">Waduh, jangan lupa!</p>
                                        <p class="text-xs text-red-600">Centang dulu persyaratan yang wajib (*) ya 😅</p>
                                    </div>
                                    <button type="button" @click.stop="dismissWarn" class="ml-auto shrink-0 text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </div>

                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Persyaratan</p>
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <input type="checkbox" x-model="agreeTerms" @change="showWarning = false" class="w-4 h-4 mt-0.5 rounded border-gray-300 text-[#1a237e] accent-[#1a237e]">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-800 leading-relaxed">Saya telah membaca dan menyetujui <a href="#" class="text-[#1a237e] font-medium hover:underline">Syarat & Ketentuan</a> yang berlaku. <span class="text-red-500">*</span></span>
                                </label>
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <input type="checkbox" x-model="agreePrivacy" @change="showWarning = false" class="w-4 h-4 mt-0.5 rounded border-gray-300 text-[#1a237e] accent-[#1a237e]">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-800 leading-relaxed">Saya telah membaca dan memahami <a href="#" class="text-[#1a237e] font-medium hover:underline">Kebijakan Privasi</a> terkait pengelolaan data pribadi saya. <span class="text-red-500">*</span></span>
                                </label>
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <input type="checkbox" x-model="agreeMarketing" @change="showWarning = false" class="w-4 h-4 mt-0.5 rounded border-gray-300 text-[#1a237e] accent-[#1a237e]">
                                    <span class="text-sm text-gray-500 leading-relaxed">Saya bersedia menerima informasi, promo, dan pembaruan layanan melalui email atau media komunikasi lainnya. <span class="text-gray-400">(Opsional)</span></span>
                                </label>
                            </div>

                            <button type="submit"
                                class="w-full mt-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors flex items-center justify-center gap-2">
                                Daftar
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            </button>

                                {{-- Switch to login --}}
                                <p class="text-center text-sm text-gray-500 mt-4">
                                    Sudah punya akun?
                                    <button type="button" @click="tab = 'login'" class="text-[#1a237e] font-medium hover:underline">Masuk</button>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Mobile hamburger --}}
        <button id="mobile-menu-btn" class="md:hidden p-2 text-[#616161] hover:text-[#1a237e]">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    {{-- Mobile menu dropdown --}}
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-[#f0f0f0] px-6 py-4 space-y-3">
        <a href="{{ route('customer.beranda') }}" class="block text-sm font-medium {{ request()->routeIs('customer.beranda') ? 'text-[#1a237e] font-semibold' : 'text-[#616161]' }}">Beranda</a>
        <a href="{{ route('customer.tentang') }}" class="block text-sm font-medium {{ request()->routeIs('customer.tentang') ? 'text-[#1a237e] font-semibold' : 'text-[#616161]' }}">Tentang Kami</a>

        {{-- Mobile Katalog with sub menu --}}
        <div x-data="{ katalogOpen: false }">
            <button @click="katalogOpen = !katalogOpen"
                class="flex items-center justify-between w-full text-sm font-medium {{ request()->routeIs('customer.katalog') ? 'text-[#1a237e] font-semibold' : 'text-[#616161]' }}">
                Katalog
                <svg class="w-4 h-4 transition-transform" :class="katalogOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div x-show="katalogOpen" x-cloak class="mt-2 ml-4 space-y-2">
                <a href="{{ route('customer.katalog') }}" class="block text-sm text-gray-500 hover:text-[#1a237e]">Semua Produk</a>
                <a href="{{ route('customer.katalog', ['kategori' => 'running']) }}" class="block text-sm text-gray-500 hover:text-[#1a237e]">Jersey Running</a>
                <a href="{{ route('customer.katalog', ['kategori' => 'sepak-bola-futsal']) }}" class="block text-sm text-gray-500 hover:text-[#1a237e]">Jersey Sepak Bola / Futsal</a>
                <a href="{{ route('customer.katalog', ['kategori' => 'tenis']) }}" class="block text-sm text-gray-500 hover:text-[#1a237e]">Jersey Tenis</a>
                <a href="{{ route('customer.katalog', ['kategori' => 'basket']) }}" class="block text-sm text-gray-500 hover:text-[#1a237e]">Jersey Basket</a>
                <a href="{{ route('customer.katalog', ['kategori' => 'gym-training']) }}" class="block text-sm text-gray-500 hover:text-[#1a237e]">Jersey Gym / Training</a>
            </div>
        </div>

        <a href="{{ route('customer.pemesanan') }}" class="block text-sm font-medium {{ request()->routeIs('customer.pemesanan') ? 'text-[#1a237e] font-semibold' : 'text-[#616161]' }}">Buat Pesanan</a>
    </div>
</nav>

<style>
    .nav-link {
        position: relative;
        padding-bottom: 4px;
    }
    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: #00e5ff;
        border-radius: 999px;
        transition: width 0.25s ease;
    }
    .nav-link:hover::after {
        width: 100%;
    }
    .nav-link-active::after {
        width: 100% !important;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 50%, 90% { transform: translateX(-6px); }
        30%, 70% { transform: translateX(6px); }
    }
    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
    @keyframes bounceIn {
        0% { transform: scale(0.5); opacity: 0; }
        60% { transform: scale(1.08); }
        80% { transform: scale(0.95); }
        100% { transform: scale(1); opacity: 1; }
    }
    .animate-bounceIn {
        animation: bounceIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    @keyframes fadeOut {
        0% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-10px); }
    }
    .animate-fadeOut {
        animation: fadeOut 0.3s ease-out forwards;
    }
</style>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    function authSidebar() {
        return {
            sidebarOpen: false,
            tab: 'login',
            agreeTerms: false,
            agreePrivacy: false,
            agreeMarketing: false,
            showWarning: false,
            warnTimer: null,

            init() {
                @guest
                var params = new URLSearchParams(window.location.search);
                var auth = params.get('auth');
                if (auth === 'login' || auth === 'register') {
                    this.openSidebar(auth);
                    if (history.replaceState) {
                        var url = window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.hash;
                        history.replaceState(null, '', url);
                    }
                }
                @endguest
            },

            submitForm(formEl) {
                if (!this.agreeTerms || !this.agreePrivacy) {
                    this.showWarning = true;
                    clearTimeout(this.warnTimer);
                    this.warnTimer = setTimeout(() => { this.showWarning = false; }, 4000);
                    return;
                }
                formEl.submit();
            },

            dismissWarn() {
                this.showWarning = false;
                clearTimeout(this.warnTimer);
            },

            openSidebar(tab) {
                this.agreeTerms = false;
                this.agreePrivacy = false;
                this.agreeMarketing = false;
                this.showWarning = false;
                this.tab = tab;
                this.sidebarOpen = true;
            }
        }
    }
</script>
