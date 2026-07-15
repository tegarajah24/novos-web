<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Novos — Custom Sports Jersey')</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Scripts (Vite: Tailwind + Alpine + FilePond) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/filepond.js', 'resources/js/photoswipe.js'])

    {{-- Global styles --}}
    <style>
        * { font-family: 'Poppins', sans-serif; }
        html { scroll-behavior: smooth; }
        [x-cloak] { display: none !important; }
        .mesh-texture {
            background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>

    @notifyCss
    @stack('styles')
</head>
<body class="bg-[#f5f5f5] text-[#212121] antialiased">

<div class="w-full min-h-screen flex flex-col bg-white">

    {{-- Navbar --}}
    @include('customer.partials.navbar')

    {{-- Flash Messages --}}
    @include('components.alert')

    {{-- Page Content (padded for fixed navbar height) --}}
    <main class="pt-16 flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('customer.partials.footer')

    {{-- Floating WhatsApp Button --}}
    @php
        $adminWaPhone = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('company_phone', '6281234567890'));
        if (str_starts_with($adminWaPhone, '0')) { $adminWaPhone = '62' . substr($adminWaPhone, 1); }
        $adminWaUrl = 'https://wa.me/' . $adminWaPhone . '?text=' . urlencode('Halo Novos, saya ingin bertanya mengenai pesanan saya');
    @endphp
    @if(!request()->routeIs('pemesanan'))
    <div class="fixed bottom-6 right-6 z-50" x-data="{ hover: false }">
        <a href="{{ $adminWaUrl }}" target="_blank" rel="noopener" 
           @mouseenter="hover = true" @mouseleave="hover = false"
           :style="hover ? 'background-color: #20ba5a; box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);' : 'background-color: #25D366; box-shadow: 0 4px 14px rgba(37, 211, 102, 0.4);'"
           class="flex items-center gap-2 text-white font-semibold text-xs px-4 py-2.5 rounded-full transition-all duration-300 transform hover:-translate-y-1 active:scale-95 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.089.534 4.055 1.474 5.766L0 24l6.395-1.472A11.955 11.955 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.886 0-3.653-.498-5.176-1.37l-.368-.216-3.817.879.906-3.717-.24-.381A9.95 9.95 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
            </svg>
            <span>Tanya Admin</span>
        </a>
    </div>
    @endif

</div>

    @include('notify::components.notify')
    @stack('scripts')
    @notifyJs

<script>
document.addEventListener('alpine:init', function () {
    if (window.Alpine && window.lucide && typeof window.lucide.createIcons === 'function') {
        window.Alpine.nextTick(function () {
            window.lucide.createIcons({ icons: window.lucide.icons });
        });
    }
});
</script>
</body>
</html>
