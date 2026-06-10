@extends('layouts.customer')

@section('title', 'Novos — Custom Sports Jersey')

@push('styles')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-12px); }
    }
    @keyframes float-slow {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-float-slow { animation: float-slow 5s ease-in-out infinite; }
    .glow-cyan { box-shadow: 0 0 0 3px rgba(0,229,255,0.35), 0 0 16px rgba(0,229,255,0.4); }

    .product-card-glow {
        transition: box-shadow .3s ease, transform .3s ease;
    }
    .product-card-glow:hover {
        box-shadow: 0 0 30px rgba(0,229,255,0.15), 0 20px 60px rgba(0,0,0,0.4);
        transform: translateY(-4px);
    }

    .stat-glow {
        text-shadow: 0 0 20px rgba(255,255,255,0.3), 0 0 60px rgba(255,255,255,0.1);
    }

    .bento-card {
        transition: box-shadow .3s ease, transform .3s ease;
    }
    .bento-card:hover {
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .no-scrollbar::-webkit-scrollbar { display:none; }
    .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
</style>
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- 1. HERO - Asymmetric Split Layout --}}
{{-- ============================================================ --}}
<section class="relative w-full bg-gradient-to-br from-[#1e3a5f] to-[#0f2040] overflow-hidden" style="min-height:600px">

    {{-- background image --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/hero-bg.png') }}" alt=""
             class="w-full h-full object-cover opacity-[0.12]">
    </div>

    {{-- mesh overlay --}}
    <div class="absolute inset-0 opacity-[0.03] z-[1]"
         style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px"></div>

    <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl z-[1]"></div>
    <div class="absolute -bottom-32 -left-32 w-[400px] h-[400px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl z-[1]"></div>

    {{-- content --}}
    <div class="relative z-10 max-w-[1200px] mx-auto px-6 grid md:grid-cols-2 gap-8 items-center" style="min-height:600px">

        {{-- Kiri: Content Panel --}}
        <div class="flex flex-col justify-center py-20 md:py-0">

            {{-- headline --}}
            <h1 class="text-5xl md:text-[56px] font-extrabold leading-tight text-white mb-5 max-w-2xl">
                Pesan Jersey Custom Impianmu
            </h1>

            {{-- deskripsi --}}
            <p class="text-base md:text-lg text-[#c8d6e0] max-w-xl mb-10 leading-relaxed">
                Desain bebas, kualitas premium, pengerjaan cepat dan tepat waktu
            </p>

            {{-- CTA inline --}}
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('customer.pemesanan') }}"
                   class="px-8 py-3.5 bg-[#00e5ff] text-[#1a237e] text-sm font-bold rounded-[4px] hover:bg-[#00d0ea] transition-all shadow-lg shadow-[#00e5ff]/20">
                    Buat Pesanan Sekarang
                </a>
                <a href="{{ route('customer.katalog') }}"
                   class="px-8 py-3.5 border-2 border-white/60 text-white text-sm font-semibold rounded-[4px] hover:bg-white/10 hover:border-white transition-all">
                    Lihat Katalog
                </a>
            </div>
        </div>

        {{-- Kanan: Visual Showcase --}}
        <div class="flex items-center justify-center py-16 md:py-0 relative">
            {{-- background glow --}}
            <div class="absolute w-[400px] h-[400px] bg-[#00e5ff] opacity-[0.08] rounded-full blur-3xl"></div>

            {{-- primary image (depan) dengan floating animation --}}
            <div class="relative z-10 group animate-float">
                <img src="{{ asset('images/jersey-depan.png') }}"
                     alt="Jersey Custom Tampak Depan"
                     class="w-full max-w-[380px] h-auto object-contain drop-shadow-2xl
                            -rotate-[15deg] transition-all duration-700 ease-out
                            group-hover:-rotate-[10deg] group-hover:scale-[1.02]">
            </div>

            {{-- secondary decorative image (belakang) — efek depth of field --}}
            <div class="absolute bottom-0 right-0 md:-right-6 z-0 opacity-[0.45] hidden md:block pointer-events-none animate-float-slow">
                <img src="{{ asset('images/jersey-belakang.png') }}"
                     alt=""
                     class="w-[220px] h-auto object-contain drop-shadow-lg
                            rotate-[12deg] transition-all duration-700 ease-out
                            opacity-70">
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- 2. PRODUK UNGGULAN — Dark Theme + Hover Glow --}}
{{-- ============================================================ --}}
<section class="bg-[#1a1a2e] py-20">
    <div class="max-w-[1200px] mx-auto px-6">

        {{-- heading --}}
        <div class="mb-10">
            <p class="text-xs font-semibold text-[#00e5ff] tracking-[0.2em] uppercase mb-2">Koleksi Kami</p>
            <h2 class="text-4xl font-bold text-white mb-2">Produk Unggulan</h2>
            <p class="text-[#9e9e9e]">Jersey terlaris pilihan customer kami</p>
        </div>

        {{-- horizontal scroll --}}
        <div class="flex gap-6 overflow-x-auto pb-4 no-scrollbar">
            @foreach([
                ['Sepak Bola', 'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=400&q=80', 'Jersey Tim Premium',   'Rp 150.000'],
                ['Basket',     'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=400&q=80', 'Jersey Basket Pro',     'Rp 175.000'],
                ['Futsal',     'https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=400&q=80', 'Jersey Futsal Elite',   'Rp 135.000'],
                ['Custom',     'https://images.unsplash.com/photo-1552674605-15c2145efa38?w=400&q=80', 'Jersey Custom Full',    'Rp 200.000'],
            ] as $p)
            <div class="product-card-glow flex-shrink-0 w-[280px] group relative rounded-2xl bg-[#16213e]/60 backdrop-blur-sm border border-white/5 p-5">

                {{-- image area --}}
                <div class="relative flex items-center justify-center min-h-[200px]">
                    <img src="{{ $p[1] }}"
                         alt="{{ $p[0] }}"
                         class="w-full h-auto max-h-[200px] object-contain drop-shadow-lg
                                transition-transform duration-300 ease-out
                                group-hover:scale-105">
                    <span class="absolute top-0 left-0 px-3 py-1 bg-[#00e5ff] text-[#1a1a2e] text-[11px] font-bold rounded-md">
                        {{ $p[0] }}
                    </span>
                </div>

                {{-- body --}}
                <div class="text-center mt-4">
                    <h3 class="text-white text-sm font-bold">{{ $p[2] }}</h3>
                    <p class="text-[#757575] text-xs mt-1">Mulai dari</p>
                    <p class="text-[#00e5ff] text-2xl font-extrabold mt-1">{{ $p[3] }}</p>
                    <a href="{{ route('customer.pemesanan') }}"
                       class="block w-full mt-4 py-2.5 border border-white/20 text-white text-sm font-semibold rounded-xl
                              hover:bg-white/10 hover:border-white/40 transition-all">
                        Pesan Sekarang
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- 3. CARA PEMESANAN --}}
{{-- ============================================================ --}}
<section class="bg-white py-20">
    <div class="max-w-[1200px] mx-auto px-6">

        {{-- heading --}}
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-[#1a237e] mb-2">Cara Pemesanan</h2>
            <p class="text-[#757575]">Proses mudah dalam 5 langkah</p>
        </div>

        {{-- steps --}}
        <div class="relative flex justify-between items-start overflow-x-auto no-scrollbar">

            {{-- dashed connector --}}
            <div class="absolute top-[28px] left-[10%] right-[10%] h-0
                        border-t-2 border-dashed border-[#1a237e]/20 z-0 hidden md:block"></div>

            @foreach([
                ['Buat Pesanan',       'Isi form & upload desain kamu',   true,  false],
                ['Pembayaran',         'Bayar dp atau lunas via transfer', true,  false],
                ['Verifikasi Admin',   'Admin cek & konfirmasi pesanan',   false, true ],
                ['ACC Desain',         'Setujui desain final dari tim',    false, false],
                ['Produksi & Selesai', 'Diproduksi & dikirim ke kamu',    false, false],
            ] as $i => $s)
            <div class="flex-shrink-0 flex flex-col items-center text-center relative z-10 w-[140px] md:w-auto md:flex-1 px-2">

                {{-- step label --}}
                <span class="text-[10px] font-semibold text-[#00e5ff] uppercase tracking-widest mb-2">
                    Langkah {{ $i + 1 }}
                </span>

                {{-- icon circle --}}
                <div class="w-14 h-14 rounded-full bg-[#1a237e] flex items-center justify-center mb-3
                            {{ $s[3] ? 'glow-cyan ring-2 ring-[#00e5ff]' : '' }}">

                    @if($s[2])
                    {{-- checkmark --}}
                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>

                    @elseif($s[3])
                    {{-- shield-check (cyan) --}}
                    <svg class="w-6 h-6 text-[#00e5ff]" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <polyline points="9 12 11 14 15 10"/>
                    </svg>

                    @else
                    {{-- envelope / generic --}}
                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488
                                 M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488
                                 m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55
                                 m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25
                                 V8.844a2.25 2.25 0 011.183-1.98l7.5-4.04a2.25 2.25 0 012.134 0l7.5 4.04
                                 a2.25 2.25 0 011.183 1.98V19.5z"/>
                    </svg>
                    @endif
                </div>

                <p class="text-sm font-bold text-[#1a237e] mb-1">{{ $s[0] }}</p>
                <p class="text-xs text-[#757575] leading-snug">{{ $s[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- 4. STATS BANNER — Gradient Cyan --}}
{{-- ============================================================ --}}
<section class="bg-gradient-to-r from-[#00e5ff] to-[#0097a7] py-16">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-0">
            @foreach([
                ['500+',    'Pesanan Selesai'],
                ['50+',     'Desain Tersedia'],
                ['4.9★',    'Rating Customer'],
                ['3-7 Hari','Waktu Pengerjaan'],
            ] as $i => $stat)
            <div class="flex flex-col items-center text-center py-6 px-4
                        {{ $i < 3 ? 'border-r border-white/20' : '' }}">
                <p class="text-4xl md:text-5xl font-extrabold text-white mb-1 stat-glow">{{ $stat[0] }}</p>
                <p class="text-sm text-white/80 font-medium">{{ $stat[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- 5. TESTIMONI — Bento Grid Asimetris --}}
{{-- ============================================================ --}}
<section class="bg-[#f8f9fa] py-20">
    <div class="max-w-[1200px] mx-auto px-6">

        <h2 class="text-4xl font-bold text-[#1a237e] text-center mb-12">Apa Kata Mereka?</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-min">

            {{-- Testi 1 — besar, col-span-2 row-span-2 --}}
            <div class="bento-card md:col-span-2 md:row-span-2 bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                {{-- stars --}}
                <div class="flex items-center gap-1 mb-5">
                    @for($s = 0; $s < 5; $s++)
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#00e5ff">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    @endfor
                </div>
                {{-- quote besar --}}
                <p class="text-lg md:text-xl text-[#424242] leading-relaxed mb-6">
                    &ldquo;Jerseynya bagus banget, bahan adem dan jahitannya rapi. Desain sesuai request. Pasti order lagi!&rdquo;
                </p>
                {{-- author --}}
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-[#e0f7fa] flex-shrink-0 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#00acc1]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm-7 8a7 7 0 0 1 14 0H5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-bold text-[#212121]">Rina A.</p>
                        <p class="text-sm text-[#9e9e9e]">Customer Novos</p>
                    </div>
                </div>
            </div>

            {{-- Testi 2 --}}
            <div class="bento-card bg-white rounded-2xl p-6 shadow-sm">
                {{-- stars --}}
                <div class="flex items-center gap-0.5 mb-3">
                    @for($s = 0; $s < 5; $s++)
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="#00e5ff">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    @endfor
                </div>
                <p class="text-sm text-[#616161] italic leading-relaxed mb-4">
                    &ldquo;Proses cepat banget, 5 hari jadi. Komunikasi dengan admin juga responsif. Recommended!&rdquo;
                </p>
                <div class="border-t border-[#e5e7eb] pt-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#e8eaf6] flex-shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#9fa8da]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm-7 8a7 7 0 0 1 14 0H5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#212121]">Dimas P.</p>
                        <p class="text-xs text-[#9e9e9e]">Customer Novos</p>
                    </div>
                </div>
            </div>

            {{-- Testi 3 --}}
            <div class="bento-card bg-white rounded-2xl p-6 shadow-sm">
                {{-- stars --}}
                <div class="flex items-center gap-0.5 mb-3">
                    @for($s = 0; $s < 5; $s++)
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="#00e5ff">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    @endfor
                </div>
                <p class="text-sm text-[#616161] italic leading-relaxed mb-4">
                    &ldquo;Hasil jahitan rapi, sablon nempel kuat, warna sesuai mockup. Tim Novos profesional banget.&rdquo;
                </p>
                <div class="border-t border-[#e5e7eb] pt-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#e8eaf6] flex-shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#9fa8da]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm-7 8a7 7 0 0 1 14 0H5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#212121]">Sari W.</p>
                        <p class="text-xs text-[#9e9e9e]">Customer Novos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
