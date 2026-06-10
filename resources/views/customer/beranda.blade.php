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
    .card-product { box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06); transition: box-shadow .25s ease; }
    .card-product:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.12), 0 4px 10px rgba(0,0,0,0.08); }
    .card-testi { box-shadow: 0 1px 3px rgba(0,0,0,0.06); transition: box-shadow .25s ease; }
    .card-testi:hover { box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .glow-cyan { box-shadow: 0 0 0 3px rgba(0,229,255,0.35), 0 0 16px rgba(0,229,255,0.4); }
    /* hide scrollbar */
    .no-scrollbar::-webkit-scrollbar { display:none; }
    .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
</style>
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- 1. HERO - Asymmetric Split Layout --}}
{{-- ============================================================ --}}
<section class="relative w-full bg-gradient-to-br from-[#1e3a5f] to-[#0f2040] overflow-hidden" style="min-height:600px">

    {{-- mesh overlay --}}
    <div class="absolute inset-0 opacity-[0.03]"
         style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px"></div>

    <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl"></div>
    <div class="absolute -bottom-32 -left-32 w-[400px] h-[400px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl"></div>

    {{-- content --}}
    <div class="relative z-10 max-w-[1200px] mx-auto px-6 grid md:grid-cols-2 gap-8 items-center" style="min-height:600px">

        {{-- Kiri: Content Panel --}}
        <div class="flex flex-col justify-center py-20 md:py-0">

            {{-- sub-headline badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 backdrop-blur border border-[#00e5ff]/25 w-fit mb-7">
                <span class="text-[#00e5ff] text-xs">✦</span>
                <span class="text-[#00e5ff] text-xs font-medium tracking-wide">Jersey Olahraga Custom Berkualitas</span>
            </div>

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
{{-- 2. PRODUK UNGGULAN --}}
{{-- ============================================================ --}}
<section class="bg-white py-20">
    <div class="max-w-[1200px] mx-auto px-6">

        {{-- heading --}}
        <div class="mb-10">
            <p class="text-xs font-semibold text-[#00e5ff] tracking-[0.2em] uppercase mb-2">Koleksi Kami</p>
            <h2 class="text-4xl font-bold text-[#1a237e] mb-2">Produk Unggulan</h2>
            <p class="text-[#757575]">Jersey terlaris pilihan customer kami</p>
        </div>

        {{-- horizontal scroll --}}
        <div class="flex gap-6 overflow-x-auto pb-2 no-scrollbar">
            @foreach([
                ['Sepak Bola', 'Jersey Tim Premium',   'Rp 150.000'],
                ['Basket',     'Jersey Basket Pro',     'Rp 175.000'],
                ['Futsal',     'Jersey Futsal Elite',   'Rp 135.000'],
                ['Custom',     'Jersey Custom Full',    'Rp 200.000'],
            ] as $p)
            <div class="card-product flex-shrink-0 w-[280px] bg-white rounded-xl overflow-hidden border border-[#f0f0f0]">
                {{-- image placeholder --}}
                <div class="relative bg-[#e8eaf6] flex items-center justify-center" style="aspect-ratio:4/3">
                    <svg class="w-16 h-16 text-[#9fa8da]" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/>
                    </svg>
                    <span class="absolute top-3 left-3 px-2.5 py-1 bg-[#1a237e] text-white text-[11px] font-semibold rounded-md">
                        {{ $p[0] }}
                    </span>
                </div>
                {{-- body --}}
                <div class="p-4">
                    <h3 class="text-sm font-bold text-[#1a237e] mb-1">{{ $p[1] }}</h3>
                    <p class="text-xs text-[#9e9e9e] mb-1">Mulai dari</p>
                    <p class="text-xl font-extrabold text-[#1a237e] mb-4">{{ $p[2] }}</p>
                    <a href="{{ route('customer.pemesanan') }}"
                       class="block w-full py-2.5 text-center border-2 border-[#1a237e] text-[#1a237e] text-sm font-semibold rounded-[4px] hover:bg-[#1a237e] hover:text-white transition-all">
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
<section class="bg-[#f5f5f5] py-20">
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
{{-- 4. STATS BANNER --}}
{{-- ============================================================ --}}
<section class="bg-[#1a237e] py-14">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-0 divide-x divide-[#00e5ff]/20">
            @foreach([
                ['500+',    'Pesanan Selesai'],
                ['50+',     'Desain Tersedia'],
                ['4.9★',    'Rating Customer'],
                ['3-7 Hari','Waktu Pengerjaan'],
            ] as $stat)
            <div class="flex flex-col items-center text-center py-6 px-4">
                <div class="w-8 h-[3px] bg-[#00e5ff] rounded-full mb-4"></div>
                <p class="text-4xl md:text-5xl font-extrabold text-white mb-1">{{ $stat[0] }}</p>
                <p class="text-sm text-[#00e5ff] font-medium">{{ $stat[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- 5. TESTIMONI --}}
{{-- ============================================================ --}}
<section class="bg-white py-20">
    <div class="max-w-[1200px] mx-auto px-6">

        <h2 class="text-4xl font-bold text-[#1a237e] text-center mb-12">Apa Kata Mereka?</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['Jerseynya bagus banget, bahan adem dan jahitannya rapi. Desain sesuai request. Pasti order lagi!', 'Rina A.'],
                ['Proses cepat banget, 5 hari jadi. Komunikasi dengan admin juga responsif. Recommended!',           'Dimas P.'],
                ['Hasil jahitan rapi, sablon nempel kuat, warna sesuai mockup. Tim Novos profesional banget.',       'Sari W.'],
            ] as $t)
            <div class="card-testi bg-white rounded-xl border border-[#e5e7eb] p-6">

                {{-- stars --}}
                <div class="flex items-center gap-0.5 mb-4">
                    @for($s = 0; $s < 5; $s++)
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="#00e5ff">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77
                                 l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    @endfor
                </div>

                {{-- quote --}}
                <p class="text-sm text-[#616161] italic leading-relaxed mb-5">
                    &ldquo;{{ $t[0] }}&rdquo;
                </p>

                {{-- author --}}
                <div class="border-t border-[#e5e7eb] pt-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#e8eaf6] flex-shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#9fa8da]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm-7 8a7 7 0 0 1 14 0H5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#212121]">{{ $t[1] }}</p>
                        <p class="text-xs text-[#9e9e9e]">Customer Novos</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
