@extends('layouts.customer')

@section('content')
<script>
function katalogData() {
    return {
            selectedCats: [],
            maxPrice: 150000,
            searchQuery: '',
            sort: 'terbaru',
            currentPage: 1,
            perPage: 9,
            products: [
                { id: 1,  name: 'Jersey Basket Elite',        category: 'Basket',     price: 98000,  badge: null,       image: 'https://images.unsplash.com/photo-1546519638-68e109498ffc' },
                { id: 2,  name: 'Jersey Sepak Bola Ultra',    category: 'Sepak Bola', price: 98000,  badge: null,       image: 'https://images.unsplash.com/photo-1579952363873-27f3bade9f55' },
                { id: 3,  name: 'Jersey Running Pro',         category: 'Running',    price: 77000,  badge: 'Populer',  image: 'https://images.unsplash.com/photo-1552674605-15c2145efa38' },
                { id: 4,  name: 'Jersey Voli Premium',        category: 'Voli',       price: 79000,  badge: null,       image: 'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1' },
                { id: 5,  name: 'Jersey Basket Street',       category: 'Basket',     price: 82000,  badge: null,       image: 'https://images.unsplash.com/photo-1519861531473-9200262188bf' },
                { id: 6,  name: 'Jersey Futsal Pro Max',      category: 'Futsal',     price: 88000,  badge: 'Populer',  image: 'https://images.unsplash.com/photo-1536560032543-39d115ee60cb' },
                { id: 7,  name: 'Jersey Sepak Bola Classic',  category: 'Sepak Bola', price: null,   badge: null,       image: 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2' },
                { id: 8,  name: 'Jersey Running Lite',        category: 'Running',    price: null,   badge: null,       image: 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211' },
                { id: 9,  name: 'Jersey Voli Classic',        category: 'Voli',       price: null,   badge: null,       image: 'https://images.unsplash.com/photo-1593787406536-3676a152d9ce' },
                { id: 10, name: 'Jersey Futsal Elite',        category: 'Futsal',     price: 95000,  badge: null,       image: 'https://images.unsplash.com/photo-1518605368461-1e1e11415082' },
                { id: 11, name: 'Jersey Basket Pro',          category: 'Basket',     price: 105000, badge: 'Terlaris', image: 'https://images.unsplash.com/photo-1504450758481-7338eba7524a' },
                { id: 12, name: 'Jersey Running Speed',       category: 'Running',    price: 85000,  badge: null,       image: 'https://images.unsplash.com/photo-1538333581680-29ead0704dd7' },
            ],

            // ─── FIX 1: minPrice dihapus, maxPrice dinaikkan ke 150000 ───────────────
            // ─── FIX 2: priceMatch pakai p.price === null, bukan !p.price ────────────
            get filteredProducts() {
                let result = this.products.filter(p => {
                    const catMatch   = this.selectedCats.length === 0 || this.selectedCats.includes(p.category);
                    const priceMatch = p.price === null || p.price <= this.maxPrice;
                    const searchMatch = !this.searchQuery || p.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                    return catMatch && priceMatch && searchMatch;
                });

                if (this.sort === 'termurah')      result.sort((a, b) => (a.price ?? 0) - (b.price ?? 0));
                else if (this.sort === 'termahal') result.sort((a, b) => (b.price ?? 999999) - (a.price ?? 999999));
                else                               result.sort((a, b) => a.id - b.id);

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
            toggleCat(cat) {
                const idx = this.selectedCats.indexOf(cat);
                if (idx === -1) this.selectedCats.push(cat);
                else            this.selectedCats.splice(idx, 1);
                this.currentPage = 1;
            },
            resetFilter() {
                this.selectedCats = [];
                this.maxPrice     = 150000; // ─── FIX 3: sesuaikan dengan nilai default baru
                this.searchQuery  = '';
                this.sort         = 'terbaru';
                this.currentPage  = 1;
            },
            goPage(p) {
                if (p >= 1 && p <= this.totalPages) this.currentPage = p;
            }
    }
}
</script>

<div
    x-data="katalogData()"
    class="min-h-screen bg-[#f5f6f8]"
>
    {{-- ===== PAGE HEADER ===== --}}
    <div class="max-w-[1200px] mx-auto px-6 pt-8 pb-4">
        <h1 class="text-2xl font-bold text-gray-900">Katalog Produk</h1>
        <p class="text-gray-500 mt-1">Temukan Jersey custom sempurna untuk tim Anda</p>
    </div>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="max-w-[1200px] mx-auto px-6 pb-10 flex flex-col md:flex-row gap-8 items-start">

        {{-- ============================== SIDEBAR ============================== --}}
        <aside class="w-full md:w-64 flex-shrink-0 space-y-5">

            {{-- Filter Header with Reset --}}
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-800">Filter Produk</h2>
                <button
                    @click="resetFilter()"
                    class="text-xs font-medium border border-gray-300 text-gray-500 px-3 py-1 rounded hover:border-gray-400 hover:text-gray-700 transition-colors"
                >Reset Filter</button>
            </div>

            {{-- Kategori --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-sm font-semibold text-[#1a237e] mb-3">Kategori</p>
                <div class="space-y-2.5">
                    @foreach(['Sepak Bola', 'Futsal', 'Basket', 'Voli', 'Running'] as $cat)
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input
                            type="checkbox"
                            :value="'{{ $cat }}'"
                            @change="toggleCat('{{ $cat }}')"
                            :checked="selectedCats.includes('{{ $cat }}')"
                            class="w-4 h-4 rounded border-gray-300 text-[#1a237e] accent-[#1a237e] cursor-pointer"
                        >
                        <span class="text-sm text-[#424242] group-hover:text-[#1a237e] transition-colors">{{ $cat }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Harga Maksimal — FIX: max dinaikkan ke 150000 --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-sm font-semibold text-gray-700 mb-4">
                    Harga Maksimal: <span x-text="formatRupiah(maxPrice)"></span>
                </p>
                <input
                    type="range"
                    min="50000" max="150000" step="5000"
                    x-model.number="maxPrice"
                    @input="currentPage = 1"
                    class="w-full h-1.5 rounded-full accent-[#1a237e] cursor-pointer mb-2"
                >
                <div class="flex justify-between text-xs text-gray-400">
                    <span>Rp 50.000</span>
                    <span>Rp 150.000</span>
                </div>
            </div>

            {{-- Urutkan --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-sm font-semibold text-[#1a237e] mb-3">Urutkan</p>
                <select
                    x-model="sort"
                    @change="currentPage = 1"
                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 text-[#424242] bg-white focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] cursor-pointer"
                >
                    <option value="terbaru">Terbaru</option>
                    <option value="termurah">Termurah</option>
                    <option value="termahal">Termahal</option>
                </select>
            </div>

            {{-- Reset Button --}}
            <button
                @click="resetFilter()"
                class="w-full py-2.5 border-2 border-[#1a237e] text-[#1a237e] text-sm font-semibold rounded-lg hover:bg-[#1a237e] hover:text-white transition-all duration-200"
            >
                Reset Semua Filter
            </button>
        </aside>

        {{-- ============================== PRODUCT AREA ============================== --}}
        <div class="flex-1 min-w-0">

            {{-- Search & Info Bar --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-5">
                {{-- Search Input --}}
                <div class="relative flex-1 w-full">
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
                <p class="text-sm text-gray-500 whitespace-nowrap">
                    Menampilkan <span class="font-semibold text-[#1a237e]" x-text="pagedProducts.length"></span>
                    dari <span class="font-semibold text-[#1a237e]" x-text="filteredProducts.length"></span>
                    produk
                </p>
            </div>

            {{-- Product Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <template x-for="product in pagedProducts" :key="product.id">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
                        {{-- Image --}}
                        <div class="relative aspect-[4/3] bg-[#e8eaf6] overflow-hidden">
                            <img
                                :src="product.image || 'https://placehold.co/300x300/1a237e/ffffff?text=Jersey'"
                                :alt="product.name"
                                class="w-full h-full object-cover"
                            >
                            {{-- Category Badge --}}
                            <span
                                class="absolute top-3 left-3 px-2.5 py-1 bg-[#1a237e]/80 text-white text-[10px] font-semibold rounded-md backdrop-blur-sm"
                                x-text="product.category"
                            ></span>
                            {{-- Optional Product Badge --}}
                            <template x-if="product.badge">
                                <span
                                    class="absolute top-3 right-3 px-2.5 py-1 bg-[#00bcd4] text-white text-[10px] font-semibold rounded-md shadow-sm"
                                    x-text="product.badge"
                                ></span>
                            </template>
                        </div>
                        {{-- Card Body --}}
                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-[#1a237e] mb-1 leading-snug" x-text="product.name"></h3>
                            {{-- FIX: gunakan x-if dengan perbandingan eksplisit agar price: 0 tidak dianggap "tanpa harga" --}}
                            <template x-if="product.price !== null">
                                <p class="text-xs text-gray-500 mb-1">
                                    Mulai <span class="text-lg font-bold text-[#1a237e]" x-text="formatRupiah(product.price)"></span>/pcs
                                </p>
                            </template>
                            <template x-if="product.price === null">
                                <p class="text-xs text-gray-400 mb-1">Hubungi CS</p>
                            </template>
                            <div class="mt-3">
                                <button
                                    @click="window.location.href = '{{ route('customer.pemesanan') }}?produk=' + encodeURIComponent(product.name) + '&kategori=' + encodeURIComponent(product.category) + '&harga=' + (product.price ?? '') + '&gambar=' + encodeURIComponent(product.image ?? '')"
                                    class="w-full py-2 border border-[#1a237e] text-[#1a237e] text-xs font-semibold rounded-lg hover:bg-[#1a237e]/5 transition-colors"
                                >
                                    Pesan Sekarang
                                </button>
                            </div>
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
                <p class="text-sm text-[#9e9e9e] mb-5">Coba ubah filter atau reset untuk melihat semua produk</p>
                <button
                    @click="resetFilter()"
                    class="px-5 py-2 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors"
                >
                    Reset Filter
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
@endsection