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
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/filepond.js'])

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

</div>

@stack('scripts')
</body>
</html>
