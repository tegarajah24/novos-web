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

    [data-aos] {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.7s ease-out, transform 0.7s ease-out;
    }
    [data-aos].aos-visible {
        opacity: 1;
        transform: translateY(0);
    }
    [data-aos="fade-in"] {
        opacity: 0;
        transform: none;
    }
    [data-aos="fade-in"].aos-visible {
        opacity: 1;
    }
    [data-aos="zoom-in"] {
        opacity: 0;
        transform: scale(0.95);
    }
    [data-aos="zoom-in"].aos-visible {
        opacity: 1;
        transform: scale(1);
    }
    [data-aos-delay="100"].aos-visible { transition-delay: 0.1s; }
    [data-aos-delay="200"].aos-visible { transition-delay: 0.2s; }
    [data-aos-delay="300"].aos-visible { transition-delay: 0.3s; }
    [data-aos-delay="400"].aos-visible { transition-delay: 0.4s; }
    [data-aos-delay="500"].aos-visible { transition-delay: 0.5s; }
</style>
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- 1. HERO - Asymmetric Split Layout --}}
{{-- ============================================================ --}}
<section class="relative w-full bg-[#0f2040] overflow-x-clip" style="min-height:400px">

    {{-- background image --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/hero-bg.png') }}" alt=""
             class="w-full h-full object-cover opacity-[0.50]">
    </div>

    {{-- mesh overlay --}}
    <div class="absolute inset-0 opacity-[0.03] z-[1]"
         style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px"></div>

    <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl z-[1]"></div>
    <div class="absolute -bottom-32 -left-32 w-[400px] h-[400px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl z-[1]"></div>

    {{-- content --}}
    <div class="relative z-10 max-w-[1200px] mx-auto px-6 md:grid md:grid-cols-2 gap-3 md:gap-8 items-center" style="min-height:400px">

        {{-- Kiri: Content Panel --}}
        <div class="relative z-10 w-[70%] md:w-auto min-h-[400px] md:min-h-0 flex flex-col justify-center py-4 md:py-0">

            {{-- headline --}}
            <h1 class="text-4xl md:text-[56px] font-bold leading-tight text-white mb-5 max-w-2xl" data-aos="fade-up" data-aos-delay="100">
                Pesan Jersey Custom Impianmu
            </h1>

            {{-- deskripsi --}}
            <p class="text-base md:text-lg text-[#c8d6e0] max-w-xl mb-10 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                Desain bebas, kualitas premium, pengerjaan cepat dan tepat waktu
            </p>

            {{-- CTA inline --}}
            <div class="flex flex-wrap items-center gap-3" data-aos="fade-up" data-aos-delay="300">
                <a href="{{ route('pemesanan') }}"
                   class="px-3 py-1.5 md:px-8 md:py-3.5 bg-[#00e5ff] text-[#1a237e] text-sm font-bold rounded-[4px] hover:bg-[#00d0ea] transition-all shadow-lg shadow-[#00e5ff]/20">
                    Buat Pesanan Sekarang
                </a>
            </div>
        </div>

        {{-- Kanan: Visual Showcase --}}
        <div class="absolute -right-[30%] top-0 bottom-0 w-[65%] z-0 overflow-visible flex items-center justify-center
                    md:static md:w-auto md:z-auto md:flex md:items-center md:justify-center md:py-0 md:relative"
             data-aos="zoom-in" data-aos-delay="400">
            {{-- background glow --}}
            <div class="absolute w-[200px] h-[200px] md:w-[400px] md:h-[400px] bg-[#00e5ff] opacity-[0.08] rounded-full blur-3xl"></div>

            {{-- primary image (depan) dengan floating animation --}}
            <div class="relative z-10 group animate-float">
                <img src="{{ asset('images/jersey-depan.png') }}"
                     alt="Jersey Custom Tampak Depan"
                     class="w-full md:max-w-[380px] h-auto object-contain drop-shadow-2xl
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
<section x-data="{ scrolledLeft: false, scrolledRight: false, updateScroll() { let el = $refs.scroll; this.scrolledLeft = el.scrollLeft > 5; this.scrolledRight = (el.scrollLeft + el.clientWidth) >= (el.scrollWidth - 5); }, scrollLeft() { let el = $refs.scroll; el.scrollBy({ left: -320, behavior: 'smooth' }); }, scrollRight() { let el = $refs.scroll; el.scrollBy({ left: 320, behavior: 'smooth' }); } }" class="bg-white py-20">
    <div class="max-w-[1200px] mx-auto px-6">

        <div class="flex items-end justify-between mb-2" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-[#1a237e]">Produk Terlaris</h2>
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('katalog') }}" class="text-sm font-semibold text-black border-b border-black transition-colors">
                    Lihat Semua
                </a>
                <button @click="scrollLeft()"
                    :class="scrolledLeft ? 'text-black hover:text-gray-500 cursor-pointer' : 'text-gray-300 cursor-default'"
                    class="px-1 transition-colors font-thin text-2xl leading-none">
                    &lt;
                </button>
                <button @click="scrollRight()"
                    :class="!scrolledRight ? 'text-black hover:text-gray-500 cursor-pointer' : 'text-gray-300 cursor-default'"
                    class="px-1 transition-colors font-thin text-2xl leading-none">
                    &gt;
                </button>
            </div>
        </div>
        <div class="w-full h-0.5 bg-gradient-to-r from-[#00e5ff] to-transparent mb-8"></div>

        {{-- horizontal scroll --}}
        <div x-ref="scroll" id="product-scroll" @scroll="updateScroll()" class="flex gap-6 overflow-x-auto overflow-y-hidden pb-4 no-scrollbar scroll-smooth snap-x snap-mandatory">
            @forelse($bestSellers as $i => $product)
            <div class="snap-start shrink-0 w-[calc(50%-12px)] md:w-[270px] group bg-gray-50" data-aos="fade-up" data-aos-delay="{{ ($i % 4) * 100 }}">
                <div class="p-2">
                    <div class="relative w-full overflow-hidden" style="aspect-ratio:3/4">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/300x300/1a237e/ffffff?text=Jersey' }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover transition-transform duration-300 ease-out
                                    group-hover:scale-105">
                        <span class="absolute top-3 left-3 px-2.5 py-1 bg-[#1a237e]/80 text-white text-[10px] font-semibold">
                            {{ $product->category?->name ?? 'Kategori' }}
                        </span>
                    </div>
                </div>
                <div class="p-3 text-center bg-gray-50">
                    <h3 class="text-sm font-semibold text-[#1a237e] leading-snug">{{ $product->name }}</h3>
                    <p class="text-sm font-bold text-[#1a237e] mt-0.5">{{ $product->price ? 'Rp ' . number_format($product->price, 0, ',', '.') : '' }}</p>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-400 py-10 w-full">Belum ada produk</div>
            @endforelse
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- 2B. PRODUK TERBARU --}}
{{-- ============================================================ --}}
<section x-data="{ scrolledLeft2: false, scrolledRight2: false, updateScroll2() { let el = $refs.scroll2; this.scrolledLeft2 = el.scrollLeft > 5; this.scrolledRight2 = (el.scrollLeft + el.clientWidth) >= (el.scrollWidth - 5); }, scrollLeft2() { let el = $refs.scroll2; el.scrollBy({ left: -320, behavior: 'smooth' }); }, scrollRight2() { let el = $refs.scroll2; el.scrollBy({ left: 320, behavior: 'smooth' }); } }" class="bg-white pb-20">
    <div class="max-w-[1200px] mx-auto px-6">

        <div class="flex items-end justify-between mb-2" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-[#1a237e]">Produk Terbaru</h2>
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('katalog') }}" class="text-sm font-semibold text-black border-b border-black transition-colors">
                    Lihat Semua
                </a>
                <button @click="scrollLeft2()"
                    :class="scrolledLeft2 ? 'text-black hover:text-gray-500 cursor-pointer' : 'text-gray-300 cursor-default'"
                    class="px-1 transition-colors font-thin text-2xl leading-none">
                    &lt;
                </button>
                <button @click="scrollRight2()"
                    :class="!scrolledRight2 ? 'text-black hover:text-gray-500 cursor-pointer' : 'text-gray-300 cursor-default'"
                    class="px-1 transition-colors font-thin text-2xl leading-none">
                    &gt;
                </button>
            </div>
        </div>
        <div class="w-full h-0.5 bg-gradient-to-r from-[#00e5ff] to-transparent mb-8"></div>

        {{-- horizontal scroll --}}
        <div x-ref="scroll2" id="product-scroll2" @scroll="updateScroll2()" class="flex gap-6 overflow-x-auto overflow-y-hidden pb-4 no-scrollbar scroll-smooth snap-x snap-mandatory">
            @forelse($latestProducts as $i => $product)
            <div class="snap-start shrink-0 w-[calc(50%-12px)] md:w-[270px] group bg-gray-50" data-aos="fade-up" data-aos-delay="{{ ($i % 4) * 100 }}">
                <div class="p-2">
                    <div class="relative w-full overflow-hidden" style="aspect-ratio:3/4">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/300x300/1a237e/ffffff?text=Jersey' }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover transition-transform duration-300 ease-out
                                    group-hover:scale-105">
                        <span class="absolute top-3 left-3 px-2.5 py-1 bg-[#1a237e]/80 text-white text-[10px] font-semibold">
                            {{ $product->category?->name ?? 'Kategori' }}
                        </span>
                    </div>
                </div>
                <div class="p-3 text-center bg-gray-50">
                    <h3 class="text-sm font-semibold text-[#1a237e] leading-snug">{{ $product->name }}</h3>
                    <p class="text-sm font-bold text-[#1a237e] mt-0.5">{{ $product->price ? 'Rp ' . number_format($product->price, 0, ',', '.') : '' }}</p>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-400 py-10 w-full">Belum ada produk</div>
            @endforelse
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- 3. CARA PEMESANAN --}}
{{-- ============================================================ --}}
<section class="bg-white py-20">
    <div class="max-w-[1200px] mx-auto px-6">

        {{-- heading --}}
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold text-[#1a237e] mb-2">Cara Pemesanan</h2>
            <p class="text-[#757575]">Proses mudah dalam 5 langkah</p>
        </div>

        {{-- steps --}}
        <div class="relative flex flex-col md:flex-row md:justify-between md:items-start gap-8 md:gap-0">

            {{-- dashed connector: mobile vertical, desktop horizontal --}}
            <div class="absolute left-1/2 top-[22px] bottom-[52px] w-0
                        border-l-2 border-dashed border-[#1a237e]/[0.05] md:hidden z-0"></div>
            <div class="absolute top-[50px] left-[10%] right-[10%] h-0
                        border-t-2 border-dashed border-[#1a237e]/20 z-0 hidden md:block"></div>

            @foreach([
                ['Buat Pesanan',       'Isi form & upload desain kamu',   true,  false],
                ['Verifikasi Admin',   'Admin cek & konfirmasi pesanan',   false, true ],
                ['Pembayaran',         'Bayar dp atau lunas via transfer', true,  false],
                ['ACC Desain',         'Setujui desain final dari tim',    false, false],
                ['Produksi & Selesai', 'Diproduksi & dikirim ke kamu',    false, false],
            ] as $i => $s)
            <div class="flex-shrink-0 flex flex-col items-center text-center relative z-10 w-full md:flex-1 px-2" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">

                {{-- step label --}}
                <span class="text-[10px] font-semibold text-black uppercase tracking-widest mb-2">
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
<section class="bg-[#f8f9fa] py-16" data-aos="fade-in">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="bg-white rounded-2xl shadow-sm px-6 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-0">
                @php
                    $stats = [
                        [$totalOrders . '+', 'Pesanan Selesai'],
                        [$totalProducts . '+', 'Desain Tersedia'],
                        ['4.9★', 'Rating Customer'],
                        ['3-7 Hari', 'Waktu Pengerjaan'],
                    ];
                @endphp
                @foreach($stats as $i => $stat)
                <div class="flex flex-col items-center text-center py-4 px-4 {{ $i < 3 ? 'border-b md:border-r border-[#1a237e]/20 md:border-b-0' : '' }}">
                    <p class="text-4xl md:text-5xl font-extrabold text-[#1a237e] mb-1">{{ $stat[0] }}</p>
                    <p class="text-sm text-[#1a237e]/70 font-medium">{{ $stat[1] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- 5. TESTIMONI — Bento Grid Asimetris --}}
{{-- ============================================================ --}}
<section class="bg-[#f8f9fa] py-20">
    <div class="max-w-[1200px] mx-auto px-6">

        <h2 class="text-4xl font-bold text-[#1a237e] text-center mb-12" data-aos="fade-up">Apa Kata Mereka?</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-min">

            {{-- Testi 1 — besar, col-span-2 row-span-2 --}}
            <div class="bento-card md:col-span-2 md:row-span-2 bg-white rounded-2xl p-8 md:p-10 shadow-sm" data-aos="fade-up" data-aos-delay="100">
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
            <div class="bento-card bg-white rounded-2xl p-6 shadow-sm" data-aos="fade-up" data-aos-delay="200">
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
            <div class="bento-card bg-white rounded-2xl p-6 shadow-sm" data-aos="fade-up" data-aos-delay="300">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('aos-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    document.querySelectorAll('[data-aos]').forEach(function(el) {
        observer.observe(el);
    });
});
</script>
@endpush
