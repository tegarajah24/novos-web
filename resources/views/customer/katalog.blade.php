@extends('layouts.customer')

@section('title', 'Katalog — Novos')

@section('content')
<style>
    @keyframes cardFadeIn {
        from { opacity: 0; transform: translateY(20px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-card {
        opacity: 0;
        animation: cardFadeIn 0.45s ease-out forwards;
    }

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

    @keyframes badge-pop {
        0% { transform: scale(1); }
        50% { transform: scale(1.6); }
        100% { transform: scale(1); }
    }
    .animate-badge-pop {
        animation: badge-pop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

</style>
<script>
function katalogData() {
    return {
            selectedCats: [],
            searchQuery: '',
            currentPage: 1,
            perPage: 12,
            loading: false,
            currentImages: {},
            products: @json($products),
            categories: @json($categories),
            flyItem: {
                active: false,
                src: '',
                startX: 0, startY: 0,
                dx: 0, dy: 0,
                width: 0, height: 0,
            },

            init() {
                const params = new URLSearchParams(window.location.search);
                const slug = params.get('kategori');
                if (slug) {
                    const found = this.categories.find(c => c.slug === slug);
                    if (found) {
                        this.selectedCats = [found.name];
                    }
                }
            },

            get activeLabel() {
                if (this.selectedCats.length === 0) return 'Semua Produk';
                const params = new URLSearchParams(window.location.search);
                const slug = params.get('kategori');
                if (!slug) return 'Semua Produk';
                const found = this.categories.find(c => c.slug === slug);
                return found ? found.name : 'Semua Produk';
            },

            get filteredProducts() {
                let result = this.products;
                if (this.selectedCats.length > 0) {
                    result = result.filter(p => this.selectedCats.includes(p.category));
                }
                if (this.searchQuery) {
                    const q = this.searchQuery.toLowerCase();
                    result = result.filter(p => p.name.toLowerCase().includes(q));
                }
                return result;
            },
            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredProducts.length / this.perPage));
            },
            get pagedProducts() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.filteredProducts.slice(start, start + this.perPage);
            },
            get pageNumbers() {
                const pages = [];
                for (let i = 1; i <= this.totalPages; i++) pages.push(i);
                return pages;
            },
            formatRupiah(val) {
                return 'Rp ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            },
            resetFilter() {
                this.searchQuery  = '';
                this.currentPage  = 1;
            },
            goPage(p) {
                if (p >= 1 && p <= this.totalPages) this.currentPage = p;
            },

            async flyToCart(event, product) {
                const btn = event.currentTarget;
                const card = btn.closest('[class*="group"]');
                const img = card.querySelector('img');
                const rect = img.getBoundingClientRect();
                const cartBtn = document.querySelector('.cart-icon-btn');
                if (!cartBtn) {
                    this.addToCart(product);
                    return;
                }
                const cartRect = cartBtn.getBoundingClientRect();
                const dx = cartRect.left + cartRect.width / 2 - rect.left;
                const dy = cartRect.top + cartRect.height / 2 - rect.top;

                this.flyItem = {
                    active: true,
                    src: img.src,
                    startX: rect.left, startY: rect.top,
                    dx, dy,
                    width: rect.width, height: rect.height,
                };

                await this.$nextTick();
                this.$nextTick(() => {
                    const outer = this.$refs.flyOuter;
                    const inner = this.$refs.flyInner;
                    if (outer) outer.style.transform = 'translateX(' + dx + 'px)';
                    if (inner) { inner.style.transform = 'translateY(' + dy + 'px) scale(0.12)'; inner.style.opacity = '0.4'; }
                });

                setTimeout(() => {
                    this.flyItem.active = false;
                    this.addToCart(product);
                    const badge = document.querySelector('.cart-badge');
                    if (badge) { badge.classList.add('animate-badge-pop'); setTimeout(() => badge.classList.remove('animate-badge-pop'), 500); }
                }, 750);
            },

            async addToCart(product) {
                try {
                    const res = await fetch('{{ route("cart.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            product_id: product.id,
                            size: 'M',
                            qty: 1,
                        }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        const store = Alpine.store('summary');
                        if (store) { store.cartCount = data.count || store.cartCount; store.fetch(); }
                    }
                } catch (e) {}
            },
    }
}
</script>

<div x-data="katalogData()">
    {{-- Hero --}}
    <section class="relative w-full bg-[#0f2040] overflow-hidden" style="min-height:400px">
        <div class="absolute inset-0 z-0">
            @php $heroBg = \App\Models\Setting::get('hero_katalog_bg'); @endphp
            <img src="{{ $heroBg ? asset('storage/hero-backgrounds/' . $heroBg) : asset('images/hero-katalog.png') }}" alt=""
                 class="w-full h-full object-cover opacity-[0.50]">
        </div>
        <div class="absolute inset-0 opacity-[0.03] z-[1]"
             style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px"></div>
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl z-[1] animate-float"></div>
        <div class="absolute -bottom-32 -left-32 w-[400px] h-[400px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl z-[1] animate-float-slow"></div>
        <div class="relative z-10 max-w-[1200px] mx-auto px-6 flex items-center" style="min-height:400px">
            <div class="max-w-2xl">
                <h1 class="text-4xl md:text-[56px] font-bold leading-tight text-white mb-5" data-aos="fade-up" data-aos-delay="100">
                    Katalog <span class="text-[#00e5ff]">Produk</span>
                </h1>
                <p class="text-base md:text-lg text-[#c8d6e0] leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                    Temukan jersey custom sempurna untuk tim dan komunitas Anda.
                </p>
            </div>
        </div>
    </section>

    <div class="min-h-screen bg-white">

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="max-w-[1200px] mx-auto px-6 pt-10 pb-10 flex flex-col">

        {{-- ============================== PRODUCT AREA ============================== --}}
        <div class="flex-1 min-w-0">

            {{-- Search & Info Bar --}}
            <div class="flex justify-end mb-5">
                {{-- Search Input --}}
                <div class="relative w-72">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input
                        type="text"
                        x-model="searchQuery"
                        @input="currentPage = 1"
                        placeholder="Cari produk..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e]"
                    >
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
                <template x-for="(product, index) in pagedProducts" :key="`${product.id}-${currentPage}`">
                    <div @click="window.location.href = '/katalog/' + product.id" :style="`animation-delay: ${index * 0.06}s`" class="group cursor-pointer bg-gray-50 animate-card flex flex-col">
                        {{-- Image --}}
                        <div class="p-2">
                            <div class="relative w-full overflow-hidden" style="aspect-ratio:3/4">
                                {{-- Sliding carousel track --}}
                                <div class="flex h-full transition-transform duration-300 ease-out"
                                     :style="`transform: translateX(-${(currentImages[product.id] || 0) * 100}%)`">
                                    <div class="w-full h-full shrink-0 group-hover:scale-105 transition-transform duration-300 ease-out">
                                        <img :src="product.image || 'https://placehold.co/300x300/1a237e/ffffff?text=Jersey'" :alt="product.name" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27300%27 height=%27300%27 viewBox=%270 0 300 300%27%3E%3Crect fill=%27%231a237e%27 width=%27300%27 height=%27300%27/%3E%3Ctext fill=%27white%27 font-family=%27sans-serif%27 font-size=%2716%27 text-anchor=%27middle%27 x=%27150%27 y=%27150%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                                    </div>
                                    <template x-if="product.image_belakang">
                                        <div class="w-full h-full shrink-0 group-hover:scale-105 transition-transform duration-300 ease-out">
                                            <img :src="product.image_belakang" :alt="product.name" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27300%27 height=%27300%27 viewBox=%270 0 300 300%27%3E%3Crect fill=%27%231a237e%27 width=%27300%27 height=%27300%27/%3E%3Ctext fill=%27white%27 font-family=%27sans-serif%27 font-size=%2716%27 text-anchor=%27middle%27 x=%27150%27 y=%27150%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                                        </div>
                                    </template>
                                </div>
                                {{-- Prev Arrow --}}
                                <template x-if="product.image_belakang && (currentImages[product.id] || 0) === 1">
                                    <button @click.stop="currentImages[product.id] = 0"
                                        class="absolute left-1 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 text-gray-700 shadow-sm opacity-0 md:group-hover:opacity-100 transition-opacity duration-200 hover:bg-white">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                                    </button>
                                </template>
                                {{-- Next Arrow --}}
                                <template x-if="product.image_belakang && (currentImages[product.id] || 0) === 0">
                                    <button @click.stop="currentImages[product.id] = 1"
                                        class="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 text-gray-700 shadow-sm opacity-0 md:group-hover:opacity-100 transition-opacity duration-200 hover:bg-white">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                                    </button>
                                </template>
                                {{-- Dots --}}
                                <template x-if="product.image_belakang">
                                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1.5 opacity-0 md:group-hover:opacity-100 transition-opacity duration-200">
                                        <span :class="(currentImages[product.id] || 0) === 0 ? 'bg-white' : 'bg-white/50'" class="w-1.5 h-1.5 rounded-full"></span>
                                        <span :class="(currentImages[product.id] || 0) === 1 ? 'bg-white' : 'bg-white/50'" class="w-1.5 h-1.5 rounded-full"></span>
                                    </div>
                                </template>
                                {{-- Category Badge --}}
                                <span
                                    class="absolute top-3 left-3 px-2.5 py-1 bg-[#1a237e]/80 text-white text-[10px] font-semibold"
                                    x-text="product.category"
                                ></span>
                                {{-- Optional Product Badge --}}
                                <template x-if="product.badge">
                                    <span
                                        class="absolute top-3 right-3 px-2.5 py-1 bg-[#00bcd4] text-white text-[10px] font-semibold shadow-sm"
                                        x-text="product.badge"
                                    ></span>
                                </template>
                            </div>
                        </div>
                        {{-- Card Body --}}
                        <div class="p-3 text-center bg-gray-50 flex flex-col flex-1 justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-[#1a237e] leading-snug" x-text="product.name"></h3>
                                <template x-if="product.price !== null">
                                    <p class="text-sm font-bold text-[#1a237e] mt-0.5" x-text="formatRupiah(product.price)"></p>
                                </template>
                                <template x-if="product.price === null">
                                    <p class="text-xs text-gray-400 mt-0.5">Hubungi CS</p>
                                </template>
                            </div>
                            {{-- Buy button: outline, sharp corners, slides up on hover --}}
                            <button @click.stop="flyToCart($event, product)"
                                class="mt-2 w-full py-2 text-xs font-semibold transition-all duration-300
                                       border border-[#1a237e] text-[#1a237e] bg-transparent
                                       md:opacity-0 md:translate-y-1 md:group-hover:opacity-100 md:group-hover:translate-y-0
                                       hover:bg-[#1a237e]/5">
                                + Beli
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Empty State --}}
            <div
                x-show="filteredProducts.length === 0"
                class="flex flex-col items-center justify-center py-24 text-center"
            >
                <div class="w-16 h-16 bg-[#e8eaf6] rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-[#9fa8da]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <p class="text-[#424242] font-semibold mb-1">Produk tidak ditemukan</p>
                <p class="text-sm text-[#9e9e9e] mb-5">Coba ubah kata pencarian untuk melihat semua produk</p>
                <button
                    @click="resetFilter()"
                    class="px-5 py-2 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors"
                >
                    Reset Pencarian
                </button>
            </div>

            {{-- ===== PAGINATION ===== --}}
            <div
                x-show="!loading && filteredProducts.length > 0"
                class="mt-8 flex items-center justify-center gap-1.5"
            >
                {{-- Prev --}}
                <button
                    @click="goPage(currentPage - 1)"
                    :disabled="currentPage === 1"
                    :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-[#1a237e] hover:text-white hover:border-[#1a237e]'"
                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-[#424242] transition-all duration-200"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </button>

                {{-- Page Numbers --}}
                <template x-for="p in pageNumbers" :key="p">
                    <button
                        @click="goPage(p)"
                        :class="currentPage === p
                            ? 'bg-[#1a237e] text-white border-[#1a237e]'
                            : 'bg-white text-[#424242] border-gray-200 hover:bg-[#e8eaf6] hover:border-[#9fa8da]'"
                        class="w-9 h-9 flex items-center justify-center rounded-lg border text-sm font-semibold transition-all duration-200"
                        x-text="p"
                    ></button>
                </template>

                {{-- Next --}}
                <button
                    @click="goPage(currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    :class="currentPage === totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-[#1a237e] hover:text-white hover:border-[#1a237e]'"
                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-[#424242] transition-all duration-200"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>
</div>
</div>

{{-- Parabolic Fly Animation --}}
<template x-teleport="body">
    <div x-show="flyItem.active" x-cloak
         x-ref="flyOuter"
         class="fixed z-[9999] pointer-events-none"
         :style="`left: ${flyItem.startX}px; top: ${flyItem.startY}px;
                  width: ${flyItem.width}px; height: ${flyItem.height}px;
                  transition: transform 0.75s cubic-bezier(0.22, 0.61, 0.36, 1);`">
        <div x-ref="flyInner"
             class="w-full h-full"
             :style="`transition: transform 0.75s ease-in, opacity 0.75s;`">
            <img :src="flyItem.src"
                 class="w-full h-full object-cover rounded-lg shadow-xl"
                 style="will-change: transform; backface-visibility: hidden;">
        </div>
    </div>
</template>

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
@endsection