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

    @keyframes shine {
        0% { transform: translateX(-100%) skewX(-12deg); }
        15% { transform: translateX(400%) skewX(-12deg); }
        100% { transform: translateX(400%) skewX(-12deg); }
    }
    .animate-shine { animation: shine 5s ease-in-out infinite; }

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
                   class="relative overflow-hidden px-3 py-1.5 md:px-8 md:py-3.5 bg-[#00e5ff] text-[#1a237e] text-sm font-bold rounded-[4px] hover:bg-[#00d0ea] transition-all shadow-lg shadow-[#00e5ff]/20">
                    Buat Pesanan Sekarang
                    <span class="absolute inset-0 w-14 h-full bg-gradient-to-r from-transparent via-white/35 to-transparent -translate-x-full skew-x-12 animate-shine"></span>
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
                        [$formattedRating, 'Rating Customer'],
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
<section class="bg-[#f8f9fa] py-20" x-data="{ showAllModal: false, allReviews: window.berandaAllReviewsForModal }">
    <div class="max-w-[1200px] mx-auto px-6">
 
        <h2 class="text-4xl font-bold text-[#1a237e] text-center mb-12" data-aos="fade-up">Apa Kata Mereka?</h2>
 
        <div x-data="reviewCarousel(window.berandaReviews)" x-init="startAutoplay()" class="relative">
            <div class="overflow-hidden">
                <div class="flex gap-6 transition-all duration-700 ease-in-out" :style="`transform: translateX(-${activeIndex * (100 / cardsPerView)}%)`" style="width: 100%;">
                    <template x-for="(review, index) in reviews" :key="index">
                        <div class="shrink-0 px-3 animate-fade-in" :style="`width: calc(100% / ${cardsPerView});`">
                            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 h-64 flex flex-col justify-between hover:shadow-md transition-all duration-300">
                                <div>
                                    {{-- stars --}}
                                    <div class="flex items-center gap-1 mb-4">
                                        <template x-for="star in Array.from({length: review.rating})">
                                            <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </template>
                                    </div>
                                    <p class="text-sm text-[#424242] leading-relaxed mb-6 font-medium line-clamp-4" x-text="'“' + review.comment + '”'"></p>
                                </div>
                                <div class="flex items-center gap-3 pt-4 border-t border-gray-100 mt-auto">
                                    <div class="w-10 h-10 rounded-full bg-[#e0f7fa] flex-shrink-0 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-[#00acc1]" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm-7 8a7 7 0 0 1 14 0H5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-[#212121]" x-text="review.user_name"></p>
                                        <p class="text-xs text-[#9e9e9e]">Customer Novos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
            {{-- Prev / Next Navigation & Semua Button --}}
            <div class="relative flex items-center justify-center mt-8">
                {{-- Prev / Next Navigation Buttons --}}
                <div class="flex gap-3">
                    <button @click="prev(); stopAutoplay(); startAutoplay()" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-[#1a237e] hover:text-white transition-all shadow-sm cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="next(); stopAutoplay(); startAutoplay()" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-[#1a237e] hover:text-white transition-all shadow-sm cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
 
                {{-- Semua Button --}}
                <div class="absolute right-0" data-aos="fade-up">
                    <button @click="showAllModal = true" class="inline-flex items-center gap-1 px-4 py-1.5 bg-transparent text-[#1a237e]/80 hover:text-[#1a237e] text-xs font-bold rounded-lg border border-[#1a237e]/20 hover:border-[#1a237e]/40 hover:bg-[#1a237e]/5 transition-colors cursor-pointer">
                        Semua
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
 
    {{-- Modal Semua Ulasan --}}
    <template x-teleport="body">
        <div x-show="showAllModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/45" x-cloak @keydown.escape.window="showAllModal = false">
            <div x-show="showAllModal" x-transition.opacity class="fixed inset-0 bg-black/40"></div>
            <div x-show="showAllModal" x-transition.scale.origin.bottom class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden relative z-10 flex flex-col max-h-[85vh]">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#1a237e]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Semua Ulasan Customer
                    </h3>
                    <button @click="showAllModal = false" class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                {{-- Body --}}
                <div class="px-6 py-5 overflow-y-auto flex-1 space-y-4 bg-gray-50/50">
                    <template x-for="(review, index) in allReviews" :key="index">
                        <div class="p-5 rounded-xl border border-gray-200 bg-white flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-1">
                                    <template x-for="star in Array.from({length: review.rating})">
                                        <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </template>
                                </div>
                                <span class="text-xs text-gray-400" x-text="review.created_at || 'Baru-baru ini'"></span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed mb-4 font-medium" x-text="review.comment"></p>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#e0f7fa] flex items-center justify-center">
                                    <svg class="w-4.5 h-4.5 text-[#00acc1]" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm-7 8a7 7 0 0 1 14 0H5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-800" x-text="review.user_name"></p>
                                    <p class="text-[10px] text-gray-400">Customer Novos</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</section>

@endsection

@push('scripts')
<script>
window.berandaReviews = @json($allReviews);
window.berandaAllReviewsForModal = @json($allReviewsForModal);

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

function reviewCarousel(allReviews) {
    return {
        reviews: allReviews,
        activeIndex: 0,
        cardsPerView: 3,
        autoplayInterval: null,
        init() {
            this.updateCardsPerView();
            window.addEventListener('resize', () => this.updateCardsPerView());
        },
        updateCardsPerView() {
            if (window.innerWidth < 640) {
                this.cardsPerView = 1;
            } else if (window.innerWidth < 1024) {
                this.cardsPerView = 2;
            } else {
                this.cardsPerView = 3;
            }
        },
        startAutoplay() {
            this.autoplayInterval = setInterval(() => {
                this.next();
            }, 5000);
        },
        stopAutoplay() {
            if (this.autoplayInterval) clearInterval(this.autoplayInterval);
        },
        next() {
            const maxIndex = this.reviews.length - this.cardsPerView;
            if (this.activeIndex >= maxIndex) {
                this.activeIndex = 0;
            } else {
                this.activeIndex++;
            }
        },
        prev() {
            const maxIndex = this.reviews.length - this.cardsPerView;
            if (this.activeIndex <= 0) {
                this.activeIndex = maxIndex > 0 ? maxIndex : 0;
            } else {
                this.activeIndex--;
            }
        }
    }
}
</script>
@endpush
