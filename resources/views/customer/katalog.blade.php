@extends('layouts.customer')

@section('content')
<script>
function katalogData() {
    return {
            selectedCats: [],
            searchQuery: '',
            currentPage: 1,
            perPage: 12,
            products: [
                { id: 1,  name: 'Jersey Basket Elite',        category: 'Basket',     price: 98000,  badge: null,       image: '{{ asset("images/produk/novos-Photoroom.png") }}' },
                { id: 2,  name: 'Jersey Sepak Bola Ultra',    category: 'Sepak Bola', price: 98000,  badge: null,       image: '{{ asset("images/produk/novos2-Photoroom.png") }}' },
                { id: 3,  name: 'Jersey Running Pro',         category: 'Running',    price: 77000,  badge: 'Populer',  image: '{{ asset("images/produk/novos3-Photoroom.png") }}' },
                { id: 4,  name: 'Jersey Voli Premium',        category: 'Voli',       price: 79000,  badge: null,       image: '{{ asset("images/produk/novos4-Photoroom.png") }}' },
                { id: 5,  name: 'Jersey Basket Street',       category: 'Basket',     price: 82000,  badge: null,       image: '{{ asset("images/produk/novos5-Photoroom.png") }}' },
                { id: 6,  name: 'Jersey Futsal Pro Max',      category: 'Futsal',     price: 88000,  badge: 'Populer',  image: '{{ asset("images/produk/novos6-Photoroom.png") }}' },
                { id: 7,  name: 'Jersey Sepak Bola Classic',  category: 'Sepak Bola', price: null,   badge: null,       image: '{{ asset("images/produk/novos7-Photoroom.png") }}' },
                { id: 8,  name: 'Jersey Running Lite',        category: 'Running',    price: null,   badge: null,       image: '{{ asset("images/produk/novos8-Photoroom.png") }}' },
                { id: 9,  name: 'Jersey Voli Classic',        category: 'Voli',       price: null,   badge: null,       image: '{{ asset("images/produk/novos-coklat-Photoroom.png") }}' },
                { id: 10, name: 'Jersey Futsal Elite',        category: 'Futsal',     price: 95000,  badge: null,       image: '{{ asset("images/produk/novos-hijau-Photoroom.png") }}' },
                { id: 11, name: 'Jersey Basket Pro',          category: 'Basket',     price: 105000, badge: 'Terlaris', image: '{{ asset("images/produk/novos-merah-Photoroom.png") }}' },
                { id: 12, name: 'Jersey Running Speed',       category: 'Running',    price: 85000,  badge: null,       image: '{{ asset("images/produk/novos2-Photoroom.png") }}' },
            ],

            init() {
                const params = new URLSearchParams(window.location.search);
                const slug = params.get('kategori');
                if (slug) {
                    const map = {
                        'running':           ['Running'],
                        'sepak-bola-futsal':  ['Sepak Bola', 'Futsal'],
                        'tenis':             ['Tenis'],
                        'basket':            ['Basket'],
                        'gym-training':       ['Gym', 'Training'],
                    };
                    this.selectedCats = map[slug] || [];
                }
            },

            get activeLabel() {
                if (this.selectedCats.length === 0) return 'Semua Produk';
                const labels = {
                    'running':          'Jersey Running',
                    'sepak-bola-futsal': 'Jersey Sepak Bola / Futsal',
                    'tenis':            'Jersey Tenis',
                    'basket':           'Jersey Basket',
                    'gym-training':      'Jersey Gym / Training',
                };
                const params = new URLSearchParams(window.location.search);
                return labels[params.get('kategori')] || 'Semua Produk';
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
            }
    }
}
</script>

<div x-data="katalogData()">
    {{-- Hero --}}
    <section class="relative w-full bg-[#0f2040] overflow-hidden" style="min-height:400px">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/hero-katalog.png') }}" alt=""
                 class="w-full h-full object-cover opacity-[0.50]">
        </div>
        <div class="absolute inset-0 opacity-[0.03] z-[1]"
             style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px"></div>
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl z-[1]"></div>
        <div class="absolute -bottom-32 -left-32 w-[400px] h-[400px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl z-[1]"></div>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <template x-for="product in pagedProducts" :key="product.id">
                    <div @click="window.location.href = '{{ route('customer.pemesanan') }}?produk=' + encodeURIComponent(product.name) + '&kategori=' + encodeURIComponent(product.category) + '&harga=' + (product.price ?? '') + '&gambar=' + encodeURIComponent(product.image ?? '')" class="group cursor-pointer bg-gray-50">
                        {{-- Image --}}
                        <div class="p-2">
                            <div class="relative w-full overflow-hidden" style="aspect-ratio:3/4">
                                <img
                                    :src="product.image || 'https://placehold.co/300x300/1a237e/ffffff?text=Jersey'"
                                    :alt="product.name"
                                    class="w-full h-full object-cover transition-transform duration-300 ease-out group-hover:scale-105"
                                >
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
                        <div class="p-3 text-center bg-gray-50">
                            <h3 class="text-sm font-semibold text-[#1a237e] leading-snug" x-text="product.name"></h3>
                            <template x-if="product.price !== null">
                                <p class="text-sm font-bold text-[#1a237e] mt-0.5" x-text="formatRupiah(product.price)"></p>
                            </template>
                            <template x-if="product.price === null">
                                <p class="text-xs text-gray-400 mt-0.5">Hubungi CS</p>
                            </template>
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
                x-show="filteredProducts.length > 0"
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
@endsection