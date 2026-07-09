<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @auth
                        <a href="{{ Auth::user()->role?->name === 'Customer' ? route('beranda') : route('staf.dashboard') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        </a>
                    @endauth
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if(Auth::user()->role?->name !== 'Customer')
                            <x-nav-link :href="route('staf.dashboard')" :active="request()->routeIs('staf.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @endif

                        @if(Auth::user()->role?->name === 'Customer')
                            <x-nav-link :href="route('beranda')" :active="request()->routeIs('beranda')">
                                {{ __('Beranda') }}
                            </x-nav-link>
                            <x-nav-link :href="route('katalog')" :active="request()->routeIs('katalog')">
                                {{ __('Katalog') }}
                            </x-nav-link>
                            <x-nav-link :href="route('pemesanan')" :active="request()->routeIs('pemesanan')">
                                {{ __('Buat Pesanan') }}
                            </x-nav-link>
                            <x-nav-link :href="route('tracking')" :active="request()->routeIs('tracking')">
                                {{ __('Tracking') }}
                            </x-nav-link>
                            @php
                                $waPhone = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('company_phone', '6281234567890'));
                                if (str_starts_with($waPhone, '0')) { $waPhone = '62' . substr($waPhone, 1); }
                            @endphp
                            <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Halo Novos, saya ingin bertanya tentang pesanan') }}" target="_blank" rel="noopener"
                               class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-green-600"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.089.534 4.055 1.474 5.766L0 24l6.395-1.472A11.955 11.955 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.886 0-3.653-.498-5.176-1.37l-.368-.216-3.817.879.906-3.717-.24-.381A9.95 9.95 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                                WhatsApp
                            </a>
                            <x-nav-link :href="route('tentang')" :active="request()->routeIs('tentang')">
                                {{ __('Tentang Kami') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if(Auth::user()->role?->name !== 'Customer')
                    <x-responsive-nav-link :href="route('staf.dashboard')" :active="request()->routeIs('staf.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->role?->name === 'Customer')
                    <x-responsive-nav-link :href="route('beranda')" :active="request()->routeIs('beranda')">
                        {{ __('Beranda') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('katalog')" :active="request()->routeIs('katalog')">
                        {{ __('Katalog') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pemesanan')" :active="request()->routeIs('pemesanan')">
                        {{ __('Buat Pesanan') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tracking')" :active="request()->routeIs('tracking')">
                        {{ __('Tracking') }}
                    </x-responsive-nav-link>
                    @php
                        $waPhone = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('company_phone', '6281234567890'));
                        if (str_starts_with($waPhone, '0')) { $waPhone = '62' . substr($waPhone, 1); }
                    @endphp
                    <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Halo Novos, saya ingin bertanya tentang pesanan') }}" target="_blank" rel="noopener"
                       class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        WhatsApp Admin
                    </a>
                    <x-responsive-nav-link :href="route('tentang')" :active="request()->routeIs('tentang')">
                        {{ __('Tentang Kami') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>
