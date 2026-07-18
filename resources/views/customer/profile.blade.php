@extends('layouts.customer')

@section('title', 'Profil Saya — Novos')

@push('styles')
<style>

    .profile-avatar-glow {
        box-shadow: 0 0 20px rgba(26, 35, 126, 0.15);
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8" x-data="profileDashboard(window.profileOrders, window.profileUser, window.profileAddresses, window.profileCart, window.profileProvinces, window.profileWishlist)">
    {{-- Alerts --}}
    @if (session('status') === 'profile-updated')
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3.5 shadow-sm">
        <svg class="w-5 h-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <p class="text-sm font-medium text-emerald-800">Profil berhasil diperbarui!</p>
    </div>
    @endif

    @if (session('status') === 'password-updated')
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3.5 shadow-sm">
        <svg class="w-5 h-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <p class="text-sm font-medium text-emerald-800">Password berhasil diubah!</p>
    </div>
    @endif

    {{-- Header Profil + Tombol Close --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 mb-6 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Profil Saya</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola informasi pribadi, alamat pengiriman, dan riwayat pesanan Anda.</p>
        </div>
        <div>
            <button @click="closeProfile()" class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:text-gray-900 transition-all duration-200 shadow-sm cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                Tutup Profil
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
        {{-- ==================== SIDEBAR (LEFT) ==================== --}}
        <div class="space-y-6">
            {{-- Navigation Menu --}}
            <div class="bg-white rounded-2xl shadow-sm p-4 shadow-sm border border-gray-100">
                <nav class="space-y-1">
                    {{-- Tab: Pengaturan --}}
                    <button @click="setActiveTab('pengaturan')"
                        :class="activeTab === 'pengaturan' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        Pengaturan Profil
                    </button>
                    {{-- Tab: Pembelian --}}
                    <button @click="setActiveTab('pembelian')"
                        :class="activeTab === 'pembelian' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                        Riwayat Pemesanan
                    </button>
                    {{-- Tab: Keranjang --}}
                    <button @click="setActiveTab('keranjang')"
                        :class="activeTab === 'keranjang' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        Keranjang
                    </button>
                    {{-- Tab: Produk Favorit --}}
                    <button @click="setActiveTab('favorit')"
                        :class="activeTab === 'favorit' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Produk Favorit
                    </button>
                    {{-- Tab: Alamat --}}
                    <button @click="setActiveTab('alamat')"
                        :class="activeTab === 'alamat' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Alamat Pengiriman
                    </button>
                    @if($user->role->name === 'Customer')
                    {{-- Tab: Keamanan --}}
                    <button @click="setActiveTab('keamanan')"
                        :class="activeTab === 'keamanan' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Keamanan Akun
                    </button>
                    @endif
                    {{-- Tab: Bantuan --}}
                    <button @click="setActiveTab('bantuan')"
                        :class="activeTab === 'bantuan' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        Pusat Bantuan
                    </button>
                </nav>
            </div>
        </div>

        {{-- ==================== CONTENT PANEL (RIGHT) ==================== --}}
        <div class="space-y-6">

            {{-- 1. TAB: RIWAYAT PEMBELIAN --}}
            <div x-show="activeTab === 'pembelian'" x-cloak class="space-y-6">
                {{-- Status Filter Tracker --}}
                <div class="bg-gray-100 rounded-xl p-1.5 inline-flex flex-wrap gap-1">
                    <button @click="orderFilter = 'menunggu_pembayaran'"
                        :class="orderFilter === 'menunggu_pembayaran' ? 'bg-blue-100 text-blue-800 shadow-sm' : 'text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                        Menunggu Konfirmasi
                    </button>
                    <button @click="orderFilter = 'proses'"
                        :class="orderFilter === 'proses' ? 'bg-blue-100 text-blue-800 shadow-sm' : 'text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                        Proses Produksi
                    </button>

                    <button @click="orderFilter = 'selesai'"
                        :class="orderFilter === 'selesai' ? 'bg-blue-100 text-blue-800 shadow-sm' : 'text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                        Pesanan Selesai
                    </button>
                </div>

                {{-- Order List --}}
                <div class="space-y-4">
                    <template x-for="order in displayedOrders()" :key="order.id">
                        <div x-data="{ showMenu: false }" :class="showMenu ? 'relative z-10' : ''" class="bg-white rounded-2xl shadow-sm p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">No. Pesanan</p>
                                    <p class="font-bold text-gray-900 text-base font-mono mt-0.5 truncate" x-text="order.order_number"></p>
                                </div>
                                <span :class="getStatusBadgeClass(order.status)" class="shrink-0 px-3 py-1 rounded-full text-xs font-bold capitalize" x-text="getStatusLabel(order.status)"></span>
                            </div>
                            <div class="mt-1.5 text-xs text-gray-500">
                                <span x-text="formatDate(order.created_at)"></span>
                                <template x-if="order.design_request">
                                    <span> · <span class="font-medium text-gray-700" x-text="order.design_request.team_name"></span></span>
                                </template>
                            </div>
                            <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-gray-100 pt-4">
                                <div class="text-sm text-gray-500">
                                    <template x-if="order.order_items && order.order_items.length">
                                        <span><span x-text="totalQty(order.order_items)"></span> pcs</span>
                                    </template>
                                </div>
                                <div class="flex gap-2 items-center">
                                    <template x-if="order.status === 'selesai'">
                                        <div class="flex items-center gap-0.5 mr-2" x-data="{ localHover: 0 }">
                                            <template x-for="i in 5">
                                                <button type="button" 
                                                        @click="openReviewModal(order, i)" 
                                                        @mouseenter="localHover = i" 
                                                        @mouseleave="localHover = 0"
                                                        class="p-0.5 focus:outline-none transition-transform hover:scale-110 cursor-pointer"
                                                        :title="'Beri rating ' + i + ' bintang'">
                                                    <svg class="w-5 h-5 transition-colors" 
                                                         :class="((localHover || (order.review ? order.review.rating : 0)) >= i) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'"
                                                         viewBox="0 0 24 24" 
                                                         stroke="currentColor" 
                                                         stroke-width="1.5">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                </button>
                                            </template>
                                        </div>
                                    </template>
                                    <button @click="openDetail(order)" class="px-4 py-2 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-50 transition-colors">Lihat Detail Transaksi</button>

                                    <div class="relative">
                                        <button @click="showMenu = !showMenu" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                                        </button>
                                        <div x-show="showMenu" @click.outside="showMenu = false" x-cloak class="absolute right-0 top-full mt-1 w-44 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                                            <a :href="'/chat'" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                Tanya Admin
                                            </a>
                                            <a :href="'/tracking?q=' + order.order_number" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                Tracking
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Empty State --}}
                    <div x-show="getFilteredOrders().length === 0" x-cloak
                         class="bg-white rounded-2xl shadow-sm py-16 px-6 text-center shadow-sm border border-gray-100 flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-3xl mb-4">📭</div>
                        <h4 class="font-bold text-gray-800 text-base mb-1">Belum Ada Pesanan</h4>
                        <p class="text-sm text-gray-400 max-w-sm mx-auto">Tidak menemukan transaksi pada kategori status ini.</p>
                    </div>

                    {{-- Pagination --}}
                    <div x-show="getFilteredOrders().length > 0" x-cloak class="flex items-center justify-between pt-4">
                        <p class="text-xs text-gray-500">
                            Menampilkan <span class="font-medium text-gray-700" x-text="Math.min(getFilteredOrders().length, currentPage * perPage)"></span>
                            dari <span class="font-medium text-gray-700" x-text="getFilteredOrders().length"></span> pesanan
                        </p>
                        <div class="flex items-center gap-1.5">
                            <button @click="prevPage()" :disabled="currentPage === 1"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <template x-for="page in totalPages()" :key="page">
                                <button @click="goToPage(page)"
                                    :class="page === currentPage ? 'bg-[#1a237e] text-white border-[#1a237e]' : 'text-gray-600 border-gray-200 hover:bg-gray-50'"
                                    class="w-8 h-8 text-xs font-bold rounded-lg border transition-colors"
                                    x-text="page"></button>
                            </template>
                            <button @click="nextPage()" :disabled="currentPage === totalPages()"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL DETAIL TRANSAKSI --}}
            <template x-teleport="body">
                <div x-show="selectedOrder" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="closeDetail()">
                    <div class="absolute inset-0 bg-black/40"></div>
                    <div x-show="selectedOrder" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Detail Transaksi</p>
                                <h3 class="font-bold text-gray-900 text-base font-mono mt-0.5" x-text="selectedOrder?.order_number"></h3>
                            </div>
                            <button @click="closeDetail()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Status</span>
                                <span :class="getStatusBadgeClass(selectedOrder?.status)" class="px-3 py-1 rounded-full text-xs font-bold capitalize" x-text="getStatusLabel(selectedOrder?.status)"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Tanggal</span>
                                <span class="text-sm font-medium text-gray-900" x-text="formatDate(selectedOrder?.created_at)"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Nama Pemesan</span>
                                <span class="text-sm font-medium text-gray-900" x-text="selectedOrder?.design_request?.nama_pemesan || '-'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Nama Artikel</span>
                                <span class="text-sm font-medium text-gray-900" x-text="selectedOrder?.design_request?.nama_artikel || '-'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Nama Tim</span>
                                <span class="text-sm font-medium text-gray-900" x-text="selectedOrder?.design_request?.team_name || 'Katalog'"></span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 border-t border-b border-gray-100 py-4">
                                <div>
                                    <span class="text-xs text-gray-500 block mb-0.5">Bahan Jersey</span>
                                    <span class="text-sm font-medium text-gray-900" x-text="selectedOrder?.design_request?.material || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block mb-0.5">Kerah</span>
                                    <span class="text-sm font-medium text-gray-900" x-text="selectedOrder?.design_request?.collar_style || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block mb-0.5">Jenis Potongan</span>
                                    <span class="text-sm font-medium text-gray-900" x-text="getJenisPotongan()"></span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block mb-0.5">Pola Jahitan</span>
                                    <span class="text-sm font-medium text-gray-900" x-text="selectedOrder?.design_request?.pattern || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block mb-0.5">Model Lengan & Jahitan</span>
                                    <span class="text-sm font-medium text-gray-900" x-text="getLenganJahitan()"></span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block mb-0.5">Jumlah</span>
                                    <span class="text-sm font-medium text-gray-900" x-text="(selectedOrder?.order_items ? totalQty(selectedOrder.order_items) : '-') + ' pcs'"></span>
                                </div>
                            </div>

                            <template x-if="selectedOrder?.item_details?.length">
                                <div class="pt-3 border-t border-gray-100">
                                    <p class="text-xs text-gray-500 font-medium mb-3">Detail Pesanan</p>
                                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                                                <tr>
                                                    <th class="px-3 py-2 text-left font-semibold">No Punggung</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Nama Punggung</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Model Lengan</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Size</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                <template x-for="(detail, idx) in selectedOrder.item_details" :key="idx">
                                                    <tr class="hover:bg-gray-50 transition-colors">
                                                        <td class="px-3 py-2 text-gray-800 font-medium" x-text="detail.no_punggung || '-'"></td>
                                                        <td class="px-3 py-2 text-gray-700" x-text="detail.nama_punggung || '-'"></td>
                                                        <td class="px-3 py-2 text-gray-700" x-text="detail.model_lengan || '-'"></td>
                                                        <td class="px-3 py-2 text-gray-700" x-text="detail.size || '-'"></td>
                                                        <td class="px-3 py-2 text-gray-700 max-w-[200px] truncate" x-text="detail.keterangan || '-'"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </template>
                             <div class="flex gap-3 pt-2">
                                <a :href="'/tracking?q=' + selectedOrder?.order_number" class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-50 transition-colors flex items-center justify-center gap-1.5">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Lacak Pesanan
                                </a>
                                <button @click="closeDetail()" class="flex-1 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-200 transition-colors">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
 
            {{-- 1C. MODAL: BERIKAN ULASAN --}}
            <template x-teleport="body">
                <div x-show="showReviewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/45" @keydown.escape.window="showReviewModal = false">
                    <div x-show="showReviewModal" x-transition.opacity class="fixed inset-0 bg-black/40"></div>
                    <div x-show="showReviewModal" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden z-10">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#1a237e]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                Berikan Ulasan Pesanan
                            </h3>
                            <button @click="showReviewModal = false" class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
 
                        {{-- Body --}}
                        <form @submit.prevent="saveReview()" class="p-6 space-y-6">
                            {{-- Rating Stars --}}
                            <div class="text-center space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Bagaimana kualitas produk kami?</label>
                                <div class="flex justify-center items-center gap-1.5">
                                    <template x-for="i in 5">
                                        <button type="button" 
                                                @click="reviewForm.rating = i" 
                                                @mouseenter="hoverRating = i" 
                                                @mouseleave="hoverRating = 0"
                                                class="p-1 focus:outline-none transition-transform hover:scale-110 cursor-pointer">
                                            <svg class="w-10 h-10 transition-colors" 
                                                 :class="((hoverRating || reviewForm.rating) >= i) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-200'"
                                                 viewBox="0 0 24 24" 
                                                 stroke="currentColor" 
                                                 stroke-width="1.5">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
 
                            {{-- Textarea Comment --}}
                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-gray-700">Tulis Ulasan Anda</label>
                                <textarea x-model="reviewForm.comment" 
                                          rows="4" 
                                          placeholder="Bagikan pengalaman belanja Anda di Novos Jersey..."
                                          class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] bg-white text-gray-900 resize-none"></textarea>
                            </div>
 
                            {{-- Actions --}}
                            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                                <button type="button" @click="showReviewModal = false" class="flex-1 py-2.5 border border-gray-300 text-gray-700 text-xs font-bold rounded-xl hover:bg-gray-50 transition-colors bg-white">
                                    Batal
                                </button>
                                <button type="submit" :disabled="reviewSaving" class="flex-1 py-2.5 bg-[#1a237e] text-white text-xs font-bold rounded-xl hover:bg-[#283593] transition-colors disabled:opacity-50 flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg x-show="reviewSaving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    <span x-text="reviewSaving ? 'Menyimpan...' : 'Simpan Ulasan'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            {{-- 1B. TAB: KERANJANG --}}
            <div x-show="activeTab === 'keranjang'" x-cloak class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">Keranjang Belanja</h3>
                            <p class="text-sm text-gray-500">Kelola produk yang akan dipesan.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400">Total Dipilih</p>
                            <p class="text-lg font-bold text-[#1a237e]" x-text="formatRupiah(cartTotalSelected)"></p>
                        </div>
                    </div>

                    <template x-if="cartItems.length === 0">
                        <div class="py-16 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            <h4 class="font-bold text-gray-800 text-sm mb-1">Keranjang Kosong</h4>
                            <p class="text-xs text-gray-400 mb-4">Belum ada produk di keranjang Anda.</p>
                            <a href="{{ route('katalog') }}" class="inline-block px-5 py-2.5 bg-[#1a237e] text-white rounded-lg text-xs font-bold hover:bg-[#283593] transition-colors">Jelajahi Katalog</a>
                        </div>
                    </template>

                    <template x-if="cartItems.length > 0">
                        <div class="space-y-0">
                            <div class="hidden md:grid grid-cols-[40px_60px_1fr_100px_120px_80px_72px] gap-4 px-4 py-3 bg-gray-50 rounded-xl text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                <span></span>
                                <span></span>
                                <span>Produk</span>
                                <span>Ukuran</span>
                                <span>Harga</span>
                                <span class="text-center">Total</span>
                                <span></span>
                            </div>
                            <template x-for="(item, index) in cartItems" :key="item.id">
                                <div>
                                    <div class="grid grid-cols-[40px_60px_1fr_100px_120px_80px_72px] md:grid-cols-[40px_60px_1fr_100px_120px_80px_72px] gap-4 items-center px-4 py-4 hover:bg-gray-50 rounded-xl transition-colors border-b border-gray-100 last:border-0">
                                        <div>
                                            <input type="checkbox" :checked="item.is_selected" @change="toggleSelect(item)"
                                                class="w-4 h-4 rounded border-gray-300 text-[#1a237e] accent-[#1a237e] cursor-pointer">
                                        </div>
                                        <div class="w-14 h-14 rounded-lg bg-gray-100 overflow-hidden">
                                            <template x-if="item.design_data">
                                                <div class="w-full h-full bg-gradient-to-br from-[#1a237e] to-blue-400 flex items-center justify-center text-white text-xs font-bold">Custom</div>
                                            </template>
                                            <template x-if="!item.design_data">
                                                <img :src="item.product?.image ? '/storage/' + item.product.image : '/images/placeholder.png'" 
                                                     :alt="item.product?.name" class="w-full h-full object-cover">
                                            </template>
                                        </div>
                                        <div class="min-w-0">
                                            <template x-if="item.design_data">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900 truncate" x-text="'Custom: ' + (item.design_data.team_name || 'Pesanan')"></p>
                                                    <p class="text-xs text-gray-400 truncate" x-text="getMajoritySpec(item).bahan + ' | ' + getMajoritySpec(item).kerah"></p>
                                                </div>
                                            </template>
                                            <template x-if="!item.design_data">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900 truncate" x-text="item.product?.name || 'Produk'"></p>
                                                    <p class="text-xs text-gray-400 truncate" x-text="item.product?.category?.name || ''"></p>
                                                </div>
                                            </template>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-700" x-text="item.size"></span>
                                        </div>
                                        <div>
                                            <template x-if="item.design_data">
                                                <span class="text-sm font-semibold text-[#1a237e]" x-text="'Rp ' + parseInt(item.design_data.estimasi_total || 0).toLocaleString('id-ID')"></span>
                                            </template>
                                            <template x-if="!item.design_data">
                                                <span class="text-sm font-semibold text-[#1a237e]" x-text="'Rp ' + parseInt(item.product?.price || 0).toLocaleString('id-ID')"></span>
                                            </template>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-sm font-semibold text-gray-700" x-text="item.qty + ' pcs'"></span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button @click="toggleDetail(item)" class="underline p-0 text-gray-600 rounded-lg text-xs font-bold hover:text-gray-900 transition-colors" x-text="expandedItemId === item.id ? 'Tutup' : 'Detail'"></button>
                                            <button @click="deleteCartItem(item, index)" class="p-1.5 text-gray-300 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    {{-- PANEL RINGKASAN DETAIL PESANAN --}}
                                    <div x-show="expandedItemId === item.id" x-cloak
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-1"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 -translate-y-1"
                                         class="mt-2 mb-3 mx-1">

                                        {{-- DESIGN ITEM --}}
                                        <template x-if="item.design_data">
                                            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                                                <div class="p-5 space-y-4">

                                                    {{-- Header --}}
                                                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                                                        <div class="w-7 h-7 rounded-lg bg-[#1a237e]/10 flex items-center justify-center shrink-0">
                                                            <svg class="w-4 h-4 text-[#1a237e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        </div>
                                                        <span class="font-bold text-sm text-gray-800">Ringkasan Detail Pesanan</span>
                                                        <span class="ml-auto text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full" x-text="'Custom: ' + (item.design_data.team_name || '-')"></span>
                                                    </div>

                                                    {{-- SECTION 1: Informasi Pesanan --}}
                                                    <div>
                                                        <p class="text-xs font-bold text-[#1a237e] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                                            Informasi Pesanan
                                                        </p>
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8">
                                                            <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                                <span class="text-xs text-gray-500 w-32 shrink-0">Nama Pemesan</span>
                                                                <span class="text-xs font-medium text-gray-800" x-text="item.design_data.nama_pemesan || '-'"></span>
                                                            </div>
                                                            <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                                <span class="text-xs text-gray-500 w-32 shrink-0">Nama Tim / Event</span>
                                                                <span class="text-xs font-medium text-gray-800" x-text="item.design_data.team_name || '-'"></span>
                                                            </div>
                                                            <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                                <span class="text-xs text-gray-500 w-32 shrink-0">Nama Artikel</span>
                                                                <span class="text-xs font-medium text-gray-800" x-text="item.design_data.nama_artikel || '-'"></span>
                                                            </div>
                                                            <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                                <span class="text-xs text-gray-500 w-32 shrink-0">Detail Sponsor</span>
                                                                <span class="text-xs font-medium text-gray-800" x-text="item.design_data.detail_sponsor || '-'"></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- SECTION 2: Spesifikasi Jersey --}}
                                                    <div class="pt-1">
                                                        <p class="text-xs font-bold text-[#1a237e] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                                                            Spesifikasi Jersey
                                                        </p>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8">
                                                             <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                                 <span class="text-xs text-gray-500 w-32 shrink-0">Jenis Kerah</span>
                                                                 <span class="text-xs font-medium text-gray-800" x-text="getMajoritySpec(item).kerah || '-'"></span>
                                                             </div>
                                                             <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                                 <span class="text-xs text-gray-500 w-32 shrink-0">Jenis Potongan</span>
                                                                 <span class="text-xs font-medium text-gray-800" x-text="getMajoritySpec(item).jenis_potongan || '-'"></span>
                                                             </div>
                                                             <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                                 <span class="text-xs text-gray-500 w-32 shrink-0">Bahan Jersey</span>
                                                                 <span class="text-xs font-medium text-gray-800" x-text="getMajoritySpec(item).bahan || '-'"></span>
                                                             </div>
                                                             <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                                 <span class="text-xs text-gray-500 w-32 shrink-0">Model Lengan</span>
                                                                 <span class="text-xs font-medium text-gray-800" x-text="getMajoritySpec(item).lengan_jahitan || '-'"></span>
                                                             </div>
                                                            <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                                <span class="text-xs text-gray-500 w-32 shrink-0">Total Quantity</span>
                                                                <span class="text-xs font-semibold text-[#1a237e]" x-text="(item.design_data.total_qty || item.qty || '-') + ' pcs'"></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- SECTION 3: Detail Ukuran (tabel per size) --}}
                                                    <template x-if="item.design_data.ukuran && Object.keys(item.design_data.ukuran).length > 0">
                                                        <div class="pt-1">
                                                            <p class="text-xs font-bold text-[#1a237e] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                                                                Detail Ukuran
                                                            </p>
                                                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                                                <table class="w-full text-xs">
                                                                    <thead>
                                                                        <tr class="bg-gray-50 text-gray-600 text-left">
                                                                            <th class="px-3 py-2 font-semibold">Size</th>
                                                                            <th class="px-3 py-2 font-semibold text-center">Qty (pcs)</th>
                                                                            <th class="px-3 py-2 font-semibold text-right">Subtotal</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <template x-for="(qty, sizeKey) in item.design_data.ukuran" :key="sizeKey">
                                                                            <tr class="border-t border-gray-100 even:bg-gray-50/50">
                                                                                <td class="px-3 py-2 font-semibold text-gray-800" x-text="sizeKey"></td>
                                                                                <td class="px-3 py-2 text-center">
                                                                                    <div class="flex items-center justify-center gap-1">
                                                                                        <button @click="updateDesignSize(item, sizeKey, parseInt(qty) - 1)"
                                                                                            class="w-5 h-5 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:bg-gray-100 transition-colors"
                                                                                            :disabled="parseInt(qty) <= 1">
                                                                                            <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14"/></svg>
                                                                                        </button>
                                                                                        <span class="w-7 text-center font-bold text-gray-800" x-text="qty"></span>
                                                                                        <button @click="updateDesignSize(item, sizeKey, parseInt(qty) + 1)"
                                                                                            class="w-5 h-5 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:bg-gray-100 transition-colors">
                                                                                            <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 5v14M5 12h14"/></svg>
                                                                                        </button>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="px-3 py-2 text-right font-semibold text-[#1a237e]"
                                                                                    x-text="'Rp ' + (parseInt(qty) * ((item.design_data.base_price_per_pcs || 85000) + (item.design_data.size_price_modifiers ? (Number(item.design_data.size_price_modifiers[sizeKey]) || 0) : 0))).toLocaleString('id-ID')">
                                                                                </td>
                                                                            </tr>
                                                                        </template>
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr class="border-t-2 border-gray-200 bg-gray-50 font-bold text-gray-800">
                                                                            <td class="px-3 py-2">Total</td>
                                                                            <td class="px-3 py-2 text-center" x-text="(item.design_data.total_qty || item.qty) + ' pcs'"></td>
                                                                            <td class="px-3 py-2 text-right text-[#1a237e]"
                                                                                x-text="'Rp ' + parseInt(Object.keys(item.design_data.ukuran || {}).reduce((sum, k) => sum + ((parseInt(item.design_data.ukuran[k]) || 0) * ((item.design_data.base_price_per_pcs || 85000) + (item.design_data.size_price_modifiers ? (Number(item.design_data.size_price_modifiers[k]) || 0) : 0))), 0)).toLocaleString('id-ID')">
                                                                            </td>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    {{-- Catatan: tabel detail jersey per pemain --}}
                                                    <template x-if="item.design_data.items && item.design_data.items.length > 0">
                                                        <div class="pt-1">
                                                            <p class="text-xs font-bold text-[#1a237e] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                                                                Detail Jersey per Pemain
                                                            </p>
                                                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                                                <table class="w-full text-xs">
                                                                    <thead>
                                                                        <tr class="bg-[#1a237e] text-white text-left">
                                                                            <th class="px-3 py-2 font-semibold whitespace-nowrap">No</th>
                                                                            <th class="px-3 py-2 font-semibold whitespace-nowrap">Nama Punggung</th>
                                                                            <th class="px-3 py-2 font-semibold whitespace-nowrap">NPG</th>
                                                                            <th class="px-3 py-2 font-semibold">Size</th>
                                                                            <th class="px-3 py-2 font-semibold">Keterangan</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <template x-for="(detail, idx) in item.design_data.items" :key="idx">
                                                                            <tr :class="idx % 2 === 0 ? 'bg-white' : 'bg-gray-50'" class="border-t border-gray-100">
                                                                                <td class="px-3 py-2 font-bold text-[#1a237e]" x-text="idx + 1"></td>
                                                                                <td class="px-3 py-2 font-medium text-gray-800" x-text="detail.nama || '-'"></td>
                                                                                <td class="px-3 py-2 text-gray-700 whitespace-nowrap" x-text="detail.no || '-'"></td>
                                                                                <td class="px-3 py-2 text-center">
                                                                                    <span class="inline-block bg-blue-100 text-blue-800 text-[10px] font-bold px-1.5 py-0.5 rounded" x-text="detail.size || '-'"></span>
                                                                                </td>
                                                                                <td class="px-3 py-2 text-gray-500">
                                                                                     <template x-if="detail.customizations && Object.keys(detail.customizations).length > 0 && getMinorityCustomizations(item, detail)">
                                                                                         <span x-text="getMinorityCustomizations(item, detail)"></span>
                                                                                     </template>
                                                                                     <template x-if="!detail.customizations || Object.keys(detail.customizations).length === 0 || !getMinorityCustomizations(item, detail)">-</template>
                                                                                 </td>
                                                                            </tr>
                                                                        </template>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template x-if="!item.design_data.items || item.design_data.items.length === 0">
                                                        <div class="pt-1">
                                                            <p class="text-xs font-bold text-[#1a237e] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                                                                Detail Jersey per Pemain
                                                            </p>
                                                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                                                <table class="w-full text-xs">
                                                                    <thead>
                                                                        <tr class="bg-[#1a237e] text-white text-left">
                                                                            <th class="px-3 py-2 font-semibold whitespace-nowrap">No</th>
                                                                            <th class="px-3 py-2 font-semibold whitespace-nowrap">Nama Punggung</th>
                                                                            <th class="px-3 py-2 font-semibold whitespace-nowrap">NPG</th>
                                                                            <th class="px-3 py-2 font-semibold">Size</th>
                                                                            <th class="px-3 py-2 font-semibold">Keterangan</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <template x-for="(row, idx) in parseCatatan(item.design_data.catatan)" :key="idx">
                                                                            <tr :class="idx % 2 === 0 ? 'bg-white' : 'bg-gray-50'" class="border-t border-gray-100">
                                                                                <td class="px-3 py-2 font-bold text-[#1a237e]" x-text="idx + 1"></td>
                                                                                <td class="px-3 py-2 font-medium text-gray-800" x-text="row.nama"></td>
                                                                                <td class="px-3 py-2 text-gray-700 whitespace-nowrap" x-text="row.no"></td>
                                                                                <td class="px-3 py-2 text-center">
                                                                                    <span class="inline-block bg-blue-100 text-blue-800 text-[10px] font-bold px-1.5 py-0.5 rounded" x-text="row.size"></span>
                                                                                </td>
                                                                                <td class="px-3 py-2 text-gray-500" x-text="row.ket || '-'"></td>
                                                                            </tr>
                                                                        </template>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    {{-- CTA --}}
                                                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                                                        <button @click="checkoutFromCart(item)"
                                                            class="px-5 py-2 bg-[#1a237e] text-white rounded-lg text-xs font-bold hover:bg-[#283593] transition-colors flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                                                            Pesan Sekarang
                                                        </button>
                                                        <span class="text-xs text-gray-400">atau centang &amp; klik Pesan Sekarang di bawah untuk checkout bersama</span>
                                                    </div>

                                                </div>
                                            </div>
                                        </template>

                                        {{-- CATALOG ITEM --}}
                                        <template x-if="!item.design_data">
                                            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                                                <div class="p-5 space-y-4">
                                                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                                                        <div class="w-7 h-7 rounded-lg bg-[#1a237e]/10 flex items-center justify-center shrink-0">
                                                            <svg class="w-4 h-4 text-[#1a237e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                                                        </div>
                                                        <span class="font-bold text-sm text-gray-800">Detail Produk</span>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8">
                                                        <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                            <span class="text-xs text-gray-500 w-28 shrink-0">Nama Produk</span>
                                                            <span class="text-xs font-medium text-gray-800" x-text="item.product?.name || '-'"></span>
                                                        </div>
                                                        <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                            <span class="text-xs text-gray-500 w-28 shrink-0">Kategori</span>
                                                            <span class="text-xs font-medium text-gray-800" x-text="item.product?.category?.name || '-'"></span>
                                                        </div>
                                                        <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                            <span class="text-xs text-gray-500 w-28 shrink-0">Ukuran</span>
                                                            <span class="text-xs font-medium text-gray-800" x-text="item.size"></span>
                                                        </div>
                                                        <div class="flex gap-2 py-1.5 border-b border-gray-100">
                                                            <span class="text-xs text-gray-500 w-28 shrink-0">Harga Satuan</span>
                                                            <span class="text-xs font-semibold text-[#1a237e]" x-text="'Rp ' + parseInt(item.product?.price || 0).toLocaleString('id-ID')"></span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 mb-2">Ubah Jumlah</p>
                                                        <div class="flex items-center gap-2">
                                                            <button @click="updateCartQty(item, item.qty - 1)"
                                                                class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 transition-colors"
                                                                :disabled="item.qty <= 1">
                                                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/></svg>
                                                            </button>
                                                            <span class="w-10 text-center text-sm font-bold text-gray-800" x-text="item.qty"></span>
                                                            <button @click="updateCartQty(item, item.qty + 1)"
                                                                class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 transition-colors">
                                                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                                                            </button>
                                                            <span class="text-xs text-gray-400 ml-1">pcs</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                    </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div x-show="cartItems.length > 0" class="flex items-center justify-between pt-6 border-t border-gray-100 mt-4">
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" @change="toggleSelectAll($event.target.checked)" 
                                    class="w-4 h-4 rounded border-gray-300 text-[#1a237e] accent-[#1a237e] cursor-pointer">
                                <span class="text-sm text-gray-600">Pilih Semua</span>
                            </label>
                            <button @click="deleteSelected()" class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                Hapus Terpilih
                            </button>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-xs text-gray-400">Total</p>
                                <p class="text-lg font-bold text-[#1a237e]" x-text="formatRupiah(cartTotalSelected)"></p>
                            </div>
                            <button @click="checkoutFromCartMultiple()" 
                               class="px-6 py-2.5 bg-[#1a237e] text-white rounded-lg text-sm font-semibold hover:bg-[#283593] transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                                Pesan Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 1C. TAB: PRODUK FAVORIT --}}
            <div x-show="activeTab === 'favorit'" x-cloak class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Produk Favorit</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Produk yang kamu sukai dan simpan untuk nanti.</p>
                    </div>
                    <span class="text-xs text-gray-400 font-medium" x-text="wishlist.length + ' produk'"></span>
                </div>

                <template x-if="wishlist.length === 0">
                    <div class="bg-white rounded-2xl shadow-sm py-16 px-6 text-center shadow-sm border border-gray-100 flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <h4 class="font-bold text-gray-800 text-base mb-1">Belum Ada Produk Favorit</h4>
                        <p class="text-sm text-gray-400 max-w-sm mx-auto">Jelajahi katalog dan sukai produk untuk menyimpannya di sini.</p>
                        <a href="{{ route('katalog') }}" class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-[#1a237e] text-white text-xs font-bold rounded-lg hover:bg-[#283593] transition-colors">
                            Jelajahi Katalog
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </template>

                <template x-if="wishlist.length > 0">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="(item, index) in wishlist" :key="item.id">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
                                <a :href="'/katalog/' + item.id" class="block">
                                    <div class="relative bg-gray-50" style="aspect-ratio: 3/4">
                                        <img :src="item.image" :alt="item.name"
                                            class="w-full h-full object-cover"
                                            x-show="item.image"
                                            x-on:error="$el.style.display = 'none'">
                                        <div x-show="!item.image" class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375 0 11-.75 0 .375 0 01.75 0z"/></svg>
                                        </div>
                                    </div>
                                </a>
                                <div class="p-3">
                                    <p class="text-[10px] font-bold text-black uppercase tracking-wider mb-0.5" x-text="item.category"></p>
                                    <a :href="'/katalog/' + item.id" class="block text-sm font-bold text-[#1a237e] leading-tight hover:underline truncate" x-text="item.name"></a>
                                    <p class="text-sm font-extrabold text-gray-900 mt-1.5" x-show="item.price" x-text="'Rp ' + Number(item.price).toLocaleString('id-ID')"></p>
                                    <button @click.prevent="removeWishlist(item.id)"
                                        class="mt-2 w-full py-1.5 text-xs font-bold text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition-colors flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- 2. TAB: PENGATURAN PROFIL --}}
            <div x-show="activeTab === 'pengaturan'" x-cloak class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Pengaturan Profil</h3>
                    <p class="text-sm text-gray-500 mb-6">Kelola biodata diri, kontak utama, dan foto profil Anda.</p>

                    <form id="profileForm" @submit.prevent="submitProfile" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        {{-- Foto Profil Section --}}
                        <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-gray-100 mb-6">
                            <div class="w-24 h-24 rounded-full overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 profile-avatar-glow">
                                <img :src="avatarPreview ?? (user.avatar ? '/storage/' + user.avatar : undefined)"
                                     alt="Preview Avatar" class="w-full h-full object-cover"
                                     x-show="avatarPreview || user.avatar">
                                <div x-show="!avatarPreview && !user.avatar"
                                     class="w-full h-full bg-[#1a237e] text-white flex items-center justify-center text-3xl font-bold">
                                    <span x-text="getUserInitials()"></span>
                                </div>
                            </div>
                            <div class="text-center sm:text-left space-y-2">
                                <h4 class="font-bold text-sm text-gray-900">Foto Profil</h4>
                                <p class="text-xs text-gray-500">Mendukung PNG, JPG atau JPEG (Maksimal 5MB).</p>
                                <input type="file" class="filepond" id="pondAvatar" name="avatar" accept="image/png,image/jpeg,image/jpg" data-max-file-size="5MB" data-allow-multiple="false">
                            </div>
                        </div>

                        {{-- Biodata Fields --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                                <input type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}" placeholder="Nama Lengkap Anda"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Username <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon (WhatsApp) <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required placeholder="Contoh: 081234567890"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                            </div>
                        </div>

                        {{-- Hidden Address input to prevent overwriting it to null during profile update --}}
                        <input type="hidden" name="address" value="{{ $user->address }}">

                        <div class="flex justify-end pt-3">
                            <button type="submit"
                                class="px-6 py-3 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors flex items-center justify-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 3. TAB: ALAMAT PENGIRIMAN --}}
            <div x-show="activeTab === 'alamat'" x-cloak class="space-y-6">

                {{-- MODE LIST: Tampilkan alamat tersimpan --}}
                <div x-show="alamatMode === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="bg-white rounded-2xl shadow-sm p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-1">
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Alamat Pengiriman</h3>
                                <p class="text-sm text-gray-500">Kelola alamat pengiriman pesanan Anda.</p>
                            </div>
                            <button @click="openNewAddressForm()"
                                class="px-4 py-2 bg-[#1a237e] text-white rounded-lg text-xs font-bold hover:bg-[#283593] transition-colors flex items-center gap-1.5 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                Tambah Baru
                            </button>
                        </div>
                    </div>

                    {{-- Daftar Alamat --}}
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <template x-for="(addr, index) in addresses" :key="addr.id">
                            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:border-[#1a237e]/30 hover:shadow-md transition-all duration-200 relative group">
                                <div class="flex items-start gap-3">
                                    <div class="p-2.5 bg-blue-50 rounded-xl text-[#1a237e] shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-bold text-gray-900 text-sm" x-text="addr.first_name + (addr.last_name ? ' ' + addr.last_name : '')"></p>
                                            <span :class="addr.address_type === 'rumah' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'"
                                                class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase tracking-wider" x-text="addr.address_type"></span>
                                            <span x-show="addr.is_primary"
                                                class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-[#1a237e]/10 text-[#1a237e] uppercase tracking-wider">Utama</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1.5 leading-relaxed" x-text="addr.detail_address"></p>
                                        <p class="text-xs text-gray-500 mt-0.5" x-text="addr.district + ', ' + addr.city + ', ' + addr.province + ' ' + addr.postal_code"></p>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="editAddress(addr)"
                                        class="flex items-center gap-1 text-xs font-semibold text-[#1a237e] hover:text-[#283593] transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Edit
                                    </button>
                                    <button @click="deleteAddress(addr, index)"
                                        class="flex items-center gap-1 text-xs font-semibold text-red-600 hover:text-red-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        Hapus
                                    </button>
                                    <button x-show="!addr.is_primary" @click="setPrimaryAddress(addr)"
                                        class="flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        Jadikan Utama
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- Empty State --}}
                        <div x-show="addresses.length === 0" x-cloak class="md:col-span-2">
                            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center flex flex-col items-center">
                                <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm mb-1">Belum Ada Alamat Tersimpan</h4>
                                <p class="text-xs text-gray-400 mb-4">Tambahkan alamat pengiriman baru untuk pesanan Anda.</p>
                                <button @click="openNewAddressForm()"
                                    class="px-5 py-2.5 bg-[#1a237e] text-white rounded-lg text-xs font-bold hover:bg-[#283593] transition-colors flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                    Tambah Alamat Baru
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODE FORM: Tambah / Edit Alamat --}}
                <div x-show="alamatMode === 'form'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="bg-white rounded-2xl shadow-sm p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg" x-text="editingAddressId ? 'Edit Alamat' : 'Tambah Alamat Baru'"></h3>
                                <p class="text-sm text-gray-500">Lengkapi detail alamat pengiriman Anda.</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            {{-- Nama Depan & Nama Belakang --}}
                            <div class="grid md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Depan <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="addressForm.first_name" placeholder="Nama Depan"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Belakang</label>
                                    <input type="text" x-model="addressForm.last_name" placeholder="Nama Belakang"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                                </div>
                            </div>

                            {{-- Provinsi, Kabupaten/Kota, Kecamatan --}}
                            <div class="grid md:grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Provinsi <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select x-model="selectedProvinceId"
                                            @change="const prov = provinces.find(p => p.id === selectedProvinceId); addressForm.province = prov ? prov.name : ''; fetchRegencies();"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow bg-white appearance-none text-sm">
                                            <option value="">Pilih Provinsi</option>
                                            <template x-for="prov in provinces" :key="prov.id">
                                                <option :value="prov.id" x-text="prov.name"></option>
                                            </template>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kabupaten / Kota <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select x-model="selectedRegencyId"
                                            @change="const reg = regencies.find(r => r.id === selectedRegencyId); addressForm.city = reg ? reg.name : ''; fetchDistricts();"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow bg-white appearance-none text-sm"
                                            :disabled="!selectedProvinceId || addressLoading.regencies">
                                            <option value="">Pilih Kabupaten/Kota</option>
                                            <template x-for="reg in regencies" :key="reg.id">
                                                <option :value="reg.id" x-text="reg.name"></option>
                                            </template>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <template x-if="addressLoading.regencies">
                                                <svg class="animate-spin h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                            </template>
                                            <template x-if="!addressLoading.regencies">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kecamatan <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select x-model="selectedDistrictId"
                                            @change="const dist = districts.find(d => d.id === selectedDistrictId); addressForm.district = dist ? dist.name : '';"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow bg-white appearance-none text-sm"
                                            :disabled="!selectedRegencyId || addressLoading.districts">
                                            <option value="">Pilih Kecamatan</option>
                                            <template x-for="dist in districts" :key="dist.id">
                                                <option :value="dist.id" x-text="dist.name"></option>
                                            </template>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <template x-if="addressLoading.districts">
                                                <svg class="animate-spin h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                            </template>
                                            <template x-if="!addressLoading.districts">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Detail Alamat & Kode Pos --}}
                            <div class="grid md:grid-cols-3 gap-5">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Detail Alamat <span class="text-red-500">*</span></label>
                                    <textarea x-model="addressForm.detail_address" rows="2" placeholder="Nama jalan, Gedung, No. Rumah, RT/RW, dll."
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Pos <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="addressForm.postal_code" placeholder="Contoh: 12345"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                                </div>
                            </div>

                            {{-- Tandai Sebagai --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tandai Sebagai</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer bg-gray-50 border border-gray-200 rounded-xl px-5 py-3 hover:bg-gray-100 transition-colors select-none">
                                        <input type="radio" name="address_type" value="rumah" x-model="addressForm.address_type" class="radio radio-primary">
                                        <span class="text-sm font-semibold text-gray-800">Rumah</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer bg-gray-50 border border-gray-200 rounded-xl px-5 py-3 hover:bg-gray-100 transition-colors select-none">
                                        <input type="radio" name="address_type" value="kantor" x-model="addressForm.address_type" class="radio radio-primary">
                                        <span class="text-sm font-semibold text-gray-800">Kantor</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between pt-6 border-t border-gray-100 mt-6">
                            <button @click="cancelAddressForm()"
                                class="px-6 py-2.5 border-2 border-gray-300 text-gray-600 rounded-lg text-xs font-bold hover:border-gray-400 hover:text-gray-800 transition-colors">
                                Batal
                            </button>
                            <button @click="saveAddress()"
                                :disabled="!validateAddress || addressLoading.submit"
                                :class="(validateAddress && !addressLoading.submit) ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                                class="px-6 py-2.5 text-white rounded-lg text-xs font-bold transition-colors flex items-center gap-2 shadow-sm">
                                <span x-show="!addressLoading.submit" class="inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                    <span x-text="editingAddressId ? 'Simpan Perubahan' : 'Simpan Alamat'"></span>
                                </span>
                                <span x-show="addressLoading.submit" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if($user->role->name === 'Customer')
            {{-- 4. TAB: KEAMANAN --}}
            <div x-show="activeTab === 'keamanan'" x-cloak class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Ganti Password</h3>
                    <p class="text-sm text-gray-500 mb-6">Menjaga keamanan akun Anda. Pastikan password baru Anda menggunakan minimal 8 karakter.</p>

                    <form method="POST" action="{{ route('password.update') }}" @submit.prevent="if ($event.target.checkValidity()) $event.target.submit()">
                        @csrf
                        @method('put')

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat Ini</label>
                                <input type="password" name="current_password" required autocomplete="current-password"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm"
                                    placeholder="Masukkan password saat ini">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                                <input type="password" name="password" required autocomplete="new-password" minlength="8"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm"
                                    placeholder="Minimal 8 karakter">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" required autocomplete="new-password"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm"
                                    placeholder="Ulangi password baru">
                            </div>
                        </div>

                        <div class="flex justify-end pt-5">
                            <button type="submit"
                                class="px-6 py-3 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors flex items-center justify-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- 5. TAB: PUSAT BANTUAN --}}
            <div x-show="activeTab === 'bantuan'" x-cloak class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Pusat Bantuan Novos</h3>
                    <p class="text-sm text-gray-500 mb-6">Mengalami kendala pemesanan, revisi desain, atau pembayaran? Customer service kami siap membantu Anda.</p>

                    <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-8">
                        <div>
                            <h4 class="font-bold text-gray-900 text-base mb-1">Butuh Respon Cepat?</h4>
                            <p class="text-sm text-gray-600">Hubungi CS Novos via WhatsApp untuk perubahan data pesanan mendesak.</p>
                        </div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', app(\App\Models\Setting::class)->get('company_phone', '6281234567890')) }}?text=Halo%20Admin%20Novos,%20saya%20butuh%20bantuan%20terkait%20pesanan%20saya"
                           target="_blank"
                           class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-bold flex items-center gap-2 transition-colors shrink-0 shadow-sm">
                            {{-- WhatsApp Phone Icon --}}
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            Hubungi WhatsApp CS
                        </a>
                    </div>

                    <h4 class="font-bold text-gray-900 text-base mb-4">Pertanyaan Populer (FAQ)</h4>
                    <div class="space-y-4">
                        <div class="border border-gray-150 rounded-xl p-4 hover:bg-gray-50/50 transition-colors">
                            <h5 class="font-bold text-gray-900 text-sm mb-1.5">Bagaimana cara revisi desain jersey?</h5>
                            <p class="text-xs text-gray-500 leading-relaxed">Anda dapat melacak pesanan ke menu <strong>Lacak Pesanan</strong>. Jika status pesanan berada pada tahap <i>Menunggu ACC Customer</i>, Anda akan melihat tombol <strong>Minta Revisi</strong> untuk menuliskan feedback desain kepada tim design kami.</p>
                        </div>
                        <div class="border border-gray-150 rounded-xl p-4 hover:bg-gray-50/50 transition-colors">
                            <h5 class="font-bold text-gray-900 text-sm mb-1.5">Berapa lama estimasi pengerjaan jersey custom?</h5>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Kami memiliki pilihan prioritas pengerjaan saat checkout: 
                                <strong>Normal</strong> ({{ App\Models\Setting::get('prioritas_normal_estimasi', '7-14 hari kerja') }})
                                @if(App\Models\Setting::get('prioritas_express_status', 'active') === 'active')
                                    , <strong>Express</strong> ({{ App\Models\Setting::get('prioritas_express_estimasi', '3-6 hari kerja') }})
                                @endif
                                @if(App\Models\Setting::get('prioritas_super_express_status', 'active') === 'active')
                                    , dan <strong>Super Express</strong> ({{ App\Models\Setting::get('prioritas_super_express_estimasi', '1-2 hari kerja') }})
                                @endif
                                terhitung setelah pembayaran DP/Lunas dikonfirmasi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.profileOrders = @json($orders);
window.profileUser = @json($user);
window.profileAddresses = @json($addresses);
window.profileCart = @json($cartItems);
window.profileProvinces = @json($provinces);
window.profileWishlist = @json($wishlistItems);

function profileDashboard(orders = [], user = {}, initialAddresses = [], initialCart = [], provinces = [], initialWishlist = []) {
    return {
        activeTab: (new URLSearchParams(window.location.search)).get('tab') || 'pengaturan',
        orderFilter: 'menunggu_pembayaran',
        orders: orders,
        user: user,
        selectedOrder: null,
        currentPage: 1,
        perPage: 5,
 
        avatarPreview: null,

        wishlist: initialWishlist,

        init() {
            this.$watch('orderFilter', () => this.currentPage = 1);
            const pondEl = document.querySelector('#pondAvatar');
            if (pondEl) {
                const pond = FilePond.find(pondEl);
                if (pond) {
                    if (this.user.avatar) {
                        pond.addFile('/storage/' + this.user.avatar);
                    }
                    pond.on('addfile', (err, fileItem) => {
                        if (!err && fileItem.file instanceof File) {
                            this.avatarPreview = URL.createObjectURL(fileItem.file);
                        }
                    });
                    pond.on('removefile', () => {
                        this.avatarPreview = null;
                    });
                }
            }
        },

        submitProfile() {
            const form = document.querySelector('#profileForm');
            const fd = new FormData(form);
            fd.append('_method', 'patch');
            const pond = FilePond.find(document.querySelector('#pondAvatar'));
            if (pond && pond.getFiles().length > 0) {
                const f = pond.getFiles()[0];
                if (f.file instanceof File) fd.append('avatar', f.file, f.file.name);
            } else if (!this.user.avatar) {
                fd.append('avatar', '');
            }
            fetch('{{ route('profile.update') }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: fd
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Profil berhasil diperbarui!', timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    let msg = data.message || 'Gagal memperbarui profil';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join('\n');
                    }
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem' }));
        },

        showReviewModal: false,
        reviewForm: { order_id: null, rating: 5, comment: '' },
        hoverRating: 0,
        reviewSaving: false,

        // Address management
        addresses: initialAddresses,
        cartItems: initialCart,
        expandedItemId: null,
        alamatMode: 'list',
        editingAddressId: null,
        addressForm: {
            first_name: '',
            last_name: '',
            province: '',
            city: '',
            district: '',
            detail_address: '',
            postal_code: '',
            address_type: 'rumah',
        },
        provinces: provinces,
        regencies: [],
        districts: [],
        selectedProvinceId: '',
        selectedRegencyId: '',
        selectedDistrictId: '',
        addressLoading: {
            provinces: false,
            regencies: false,
            districts: false,
            submit: false,
        },

        get cartTotalSelected() {
            return this.cartItems
                .filter(item => item.is_selected)
                .reduce((sum, item) => {
                    if (item.design_data) {
                        const ukuranQty = Object.values(item.design_data.ukuran || {}).reduce((a, b) => a + (parseInt(b) || 0), 0);
                        const qty = item.design_data.total_qty || ukuranQty || item.qty || 1;
                        const basePrice = item.design_data.base_price_per_pcs || 85000;
                        const biayaPrioritas = item.design_data.biaya_prioritas || 0;
                        return sum + (qty * basePrice) + biayaPrioritas;
                    }
                    return sum + (item.qty * (item.product?.price || 0));
                }, 0);
        },

        get validateAddress() {
            return this.addressForm.first_name &&
                this.addressForm.province &&
                this.addressForm.city &&
                this.addressForm.district &&
                this.addressForm.detail_address &&
                this.addressForm.postal_code;
        },

        // ─── Address Methods ───

        getAddressFormData() {
            return {
                first_name: this.addressForm.first_name,
                last_name: this.addressForm.last_name,
                province: this.addressForm.province,
                city: this.addressForm.city,
                district: this.addressForm.district,
                detail_address: this.addressForm.detail_address,
                postal_code: this.addressForm.postal_code,
                address_type: this.addressForm.address_type,
            };
        },

        resetAddressForm() {
            this.addressForm = {
                first_name: '',
                last_name: '',
                province: '',
                city: '',
                district: '',
                detail_address: '',
                postal_code: '',
                address_type: 'rumah',
            };
            this.selectedProvinceId = '';
            this.selectedRegencyId = '';
            this.selectedDistrictId = '';
            this.regencies = [];
            this.districts = [];
        },

        openNewAddressForm() {
            this.editingAddressId = null;
            this.resetAddressForm();
            this.alamatMode = 'form';
            this.$nextTick(() => this.fetchProvinces());
        },

        cancelAddressForm() {
            this.alamatMode = 'list';
            this.editingAddressId = null;
            this.resetAddressForm();
        },

        editAddress(address) {
            this.editingAddressId = address.id;
            this.addressForm = {
                first_name: address.first_name,
                last_name: address.last_name || '',
                province: address.province,
                city: address.city,
                district: address.district,
                detail_address: address.detail_address,
                postal_code: address.postal_code,
                address_type: address.address_type,
            };
            this.alamatMode = 'form';
            this.$nextTick(() => {
                this.fetchProvinces().then(() => {
                    const prov = this.provinces.find(p => p.name === address.province);
                    if (prov) {
                        this.selectedProvinceId = prov.id;
                        this.fetchRegencies().then(() => {
                            const reg = this.regencies.find(r => r.name === address.city);
                            if (reg) {
                                this.selectedRegencyId = reg.id;
                                this.fetchDistricts().then(() => {
                                    const dist = this.districts.find(d => d.name === address.district);
                                    if (dist) this.selectedDistrictId = dist.id;
                                });
                            }
                        });
                    }
                });
            });
        },

        async saveAddress() {
            if (!this.validateAddress) return;
            this.addressLoading.submit = true;
            const payload = this.getAddressFormData();
            const isEdit = !!this.editingAddressId;

            try {
                const url = isEdit ? '/address/' + this.editingAddressId : '/address';
                const method = isEdit ? 'PUT' : 'POST';
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Gagal menyimpan alamat');

                if (isEdit) {
                    const idx = this.addresses.findIndex(a => a.id === this.editingAddressId);
                    if (idx !== -1) this.addresses[idx] = data.address;
                } else {
                    this.addresses.unshift(data.address);
                }

                this.alamatMode = 'list';
                this.editingAddressId = null;
                this.resetAddressForm();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                });
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message,
                });
            } finally {
                this.addressLoading.submit = false;
            }
        },

        async deleteAddress(address, index) {
            const result = await Swal.fire({
                title: 'Hapus Alamat?',
                text: 'Alamat "' + address.first_name + ' - ' + address.detail_address.substring(0, 30) + (address.detail_address.length > 30 ? '...' : '') + '" akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            });
            if (!result.isConfirmed) return;

            try {
                const res = await fetch('/address/' + address.id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Gagal menghapus alamat');
                this.addresses.splice(index, 1);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                });
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message,
                });
            }
        },

        async setPrimaryAddress(address) {
            try {
                const res = await fetch('/address/' + address.id, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        first_name: address.first_name,
                        last_name: address.last_name || '',
                        province: address.province,
                        city: address.city,
                        district: address.district,
                        detail_address: address.detail_address,
                        postal_code: address.postal_code,
                        address_type: address.address_type,
                        is_primary: true,
                    }),
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Gagal mengubah alamat utama');

                this.addresses = this.addresses.map(a => ({
                    ...a,
                    is_primary: a.id === address.id,
                }));

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Alamat utama berhasil diperbarui.',
                    timer: 2000,
                    showConfirmButton: false,
                });
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message,
                });
            }
        },

        // ─── Cascade Address Fetch ───

        async fetchProvinces() {
        },

        async fetchRegencies() {
            if (!this.selectedProvinceId) return;
            this.addressLoading.regencies = true;
            this.regencies = [];
            this.districts = [];
            this.selectedRegencyId = '';
            this.selectedDistrictId = '';
            this.addressForm.city = '';
            this.addressForm.district = '';
            try {
                const res = await fetch('/api/wilayah/regencies/' + this.selectedProvinceId);
                this.regencies = await res.json();
            } catch (e) {
                console.error('Gagal memuat kabupaten/kota', e);
            } finally {
                this.addressLoading.regencies = false;
            }
        },

        async fetchDistricts() {
            if (!this.selectedRegencyId) return;
            this.addressLoading.districts = true;
            this.districts = [];
            this.selectedDistrictId = '';
            this.addressForm.district = '';
            try {
                const res = await fetch('/api/wilayah/districts/' + this.selectedRegencyId);
                this.districts = await res.json();
            } catch (e) {
                console.error('Gagal memuat kecamatan', e);
            } finally {
                this.addressLoading.districts = false;
            }
        },

        closeProfile() {
            const fromCheckout = sessionStorage.getItem('from_checkout');
            if (fromCheckout === 'true') {
                sessionStorage.removeItem('from_checkout');
                window.location.href = '{{ route('pemesanan') }}';
            } else {
                if (document.referrer && document.referrer !== window.location.href) {
                    window.location.href = document.referrer;
                } else {
                    window.location.href = '/';
                }
            }
        },

        setActiveTab(tab) {
            this.activeTab = tab;
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        },

        displayedOrders() {
            const filtered = this.getFilteredOrders();
            const start = (this.currentPage - 1) * this.perPage;
            return filtered.slice(start, start + this.perPage);
        },

        totalPages() {
            return Math.ceil(this.getFilteredOrders().length / this.perPage) || 1;
        },

        nextPage() {
            if (this.currentPage < this.totalPages()) this.currentPage++;
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        goToPage(page) {
            this.currentPage = page;
        },

        openDetail(order) {
            this.selectedOrder = order;
        },

        closeDetail() {
            this.selectedOrder = null;
        },
 
        openReviewModal(order, initialRating = null) {
            this.reviewForm.order_id = order.id;
            this.reviewForm.rating = initialRating !== null ? initialRating : (order.review ? order.review.rating : 5);
            this.reviewForm.comment = order.review ? order.review.comment : '';
            this.hoverRating = 0;
            this.showReviewModal = true;
        },
 
        async saveReview() {
            if (this.reviewSaving) return;
            this.reviewSaving = true;
 
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
 
            try {
                const res = await fetch('{{ route('profile.pembelian.review') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.reviewForm)
                });
                const data = await res.json();
                this.reviewSaving = false;
 
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Ulasan Anda berhasil disimpan!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    this.showReviewModal = false;
                    // Update dynamic review state in list
                    const orderIdx = this.orders.findIndex(o => o.id === this.reviewForm.order_id);
                    if (orderIdx !== -1) {
                        this.orders[orderIdx].review = data.review;
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message
                    });
                }
            } catch (e) {
                this.reviewSaving = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal terhubung ke server.'
                });
            }
        },

        getJenisPotongan() {
            if (this.selectedOrder?.design_request?.jenis_potongan) {
                return this.selectedOrder.design_request.jenis_potongan;
            }
            const notes = this.selectedOrder?.notes || '';
            const match = notes.match(/Jenis Potongan:\s*([^\n]+)/i);
            return match ? match[1].trim() : '-';
        },

        getLenganJahitan() {
            if (this.selectedOrder?.design_request?.lengan_jahitan) {
                return this.selectedOrder.design_request.lengan_jahitan;
            }
            const notes = this.selectedOrder?.notes || '';
            const match = notes.match(/Model Lengan & Jahitan:\s*([^\n]+)/i);
            return match ? match[1].trim() : '-';
        },

        totalQty(items) {
            if (!items || !items.length) return 0;
            return items.reduce((sum, item) => sum + parseInt(item.qty), 0);
        },

        getUserInitials() {
            const displayName = this.user.fullname || this.user.name;
            if (!displayName) return 'U';
            const parts = displayName.split(' ');
            if (parts.length > 1) {
                return (parts[0][0] + parts[1][0]).toUpperCase();
            }
            return displayName.substring(0, 2).toUpperCase();
        },

        getOrdersCountByFilter(filter) {
            return this.getFilteredOrders(filter).length;
        },

        getFilteredOrders(customFilter = null) {
            const filter = customFilter || this.orderFilter;
            
            return this.orders.filter(order => {
                if (filter === 'menunggu_pembayaran') {
                    return order.status === 'menunggu_pembayaran';
                }
                if (filter === 'proses') {
                    return ['dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi'].includes(order.status);
                }
                if (filter === 'selesai') {
                    return order.status === 'selesai';
                }
                return false;
            });
        },

        getStatusLabel(status) {
            const labels = {
                'menunggu_pembayaran': 'Menunggu Konfirmasi',
                'dikonfirmasi': 'Dikonfirmasi',
                'disetujui': 'Desain Dikerjakan',
                'di_design': 'Tahap Desain',
                'siap_cetak': 'Menunggu ACC Desain',
                'diproduksi': 'Sedang Diproduksi / Kirim',
                'menunggu_spk': 'Tahap Desain',
                'selesai': 'Pesanan Selesai',
                'dibatalkan': 'Pesanan Dibatalkan'
            };
            return labels[status] || status;
        },

        getStatusBadgeClass(status) {
            const classes = {
                'menunggu_pembayaran': 'bg-orange-100 text-orange-800',
                'dikonfirmasi': 'bg-blue-100 text-blue-800',
                'disetujui': 'bg-indigo-100 text-indigo-800',
                'di_design': 'bg-purple-100 text-purple-800',
                'siap_cetak': 'bg-pink-100 text-pink-800',
                'diproduksi': 'bg-orange-100 text-orange-800',
                'menunggu_spk': 'bg-purple-100 text-purple-800',
                'selesai': 'bg-green-100 text-green-800',
                'dibatalkan': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        formatRupiah(amount) {
            if (!amount) return 'Rp 0';
            return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        },

        // ─── Cart Methods ───

        async toggleSelect(item) {
            // Optimistic update — ubah state dulu agar total langsung reaktif
            item.is_selected = !item.is_selected;
            try {
                const res = await fetch('/cart/' + item.id + '/toggle-select', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (!data.success) {
                    // Revert jika server gagal
                    item.is_selected = !item.is_selected;
                }
            } catch (e) {
                // Revert jika network error
                item.is_selected = !item.is_selected;
            }
        },

        async updateCartQty(item, newQty) {
            if (newQty < 1) return;
            try {
                const res = await fetch('/cart/' + item.id, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ qty: newQty }),
                });
                const data = await res.json();
                if (data.success) {
                    item.qty = newQty;
                }
            } catch (e) {}
        },

        async deleteCartItem(item, index) {
            const result = await Swal.fire({
                title: 'Hapus Produk?',
                text: 'Produk "' + (item.product?.name || 'Custom Design') + '" akan dihapus dari keranjang.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            });
            if (!result.isConfirmed) return;

            try {
                const res = await fetch('/cart/' + item.id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.cartItems.splice(index, 1);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                    });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan' });
            }
        },

        toggleSelectAll(checked) {
            this.cartItems.forEach(item => {
                if (item.is_selected !== checked) {
                    this.toggleSelect(item);
                }
            });
        },

        async deleteSelected() {
            const selected = this.cartItems.filter(i => i.is_selected);
            if (!selected.length) {
                Swal.fire({ icon: 'info', title: 'Tidak ada produk terpilih', text: 'Centang produk yang ingin dihapus.' });
                return;
            }

            const result = await Swal.fire({
                title: 'Hapus Terpilih?',
                text: selected.length + ' produk akan dihapus dari keranjang.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            });
            if (!result.isConfirmed) return;

            for (let i = this.cartItems.length - 1; i >= 0; i--) {
                if (this.cartItems[i].is_selected) {
                    try {
                        await fetch('/cart/' + this.cartItems[i].id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        });
                    } catch (e) {}
                    this.cartItems.splice(i, 1);
                }
            }

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Produk terpilih berhasil dihapus.',
                timer: 1500,
                showConfirmButton: false,
            });
        },

        toggleDetail(item) {
            this.expandedItemId = this.expandedItemId === item.id ? null : item.id;
        },

        // Parse string catatan CSV menjadi array baris tabel
        // Format: NoPunggung,NamaPunggung,ModelLengan,Size[,Keterangan] per pemain
        getMajoritySpec(item) {
            const specKeys = ['kerah', 'bahan', 'jenis_potongan', 'lengan_jahitan'];
            const result = {};
            specKeys.forEach(key => {
                if (item.design_data[key]) {
                    result[key] = item.design_data[key];
                    return;
                }
                if (!item.design_data.items || item.design_data.items.length === 0) {
                    result[key] = '-';
                    return;
                }
                const counts = {};
                item.design_data.items.forEach(detail => {
                    const val = detail.customizations?.[key] || '';
                    if (val) counts[val] = (counts[val] || 0) + 1;
                });
                let maxCount = 0, maxVal = '';
                Object.entries(counts).forEach(([val, count]) => {
                    if (count > maxCount) { maxCount = count; maxVal = val; }
                });
                result[key] = maxVal || '-';
            });
            return result;
        },

        getMinorityCustomizations(item, detail) {
            if (!detail.customizations || Object.keys(detail.customizations).length === 0) return '';
            const specKeys = ['kerah', 'bahan', 'jenis_potongan', 'lengan_jahitan'];
            const majority = this.getMajoritySpec(item);
            return Object.entries(detail.customizations)
                .filter(([k, v]) => {
                    if (v === undefined || v === null || v === '') return false;
                    if (specKeys.includes(k) && majority[k] && majority[k] !== '-' && v === majority[k]) return false;
                    return true;
                })
                .map(([k, v]) => v)
                .join(', ');
        },

        parseCatatan(catatan) {
            if (!catatan) return [];
            const parts = catatan.split(',').map(s => s.trim());
            const rows = [];
            let i = 0;
            while (i < parts.length) {
                if (!parts[i]) { i++; continue; }
                const no   = parts[i]   || '-';
                const nama = parts[i+1] || '-';
                const model= parts[i+2] || '-';
                const size = parts[i+3] || '-';
                let ket = '-';
                let step = 4;
                if (parts[i+4] !== undefined) {
                    const next = parts[i+4].trim();
                    const isNextNo = /^[0-9]/.test(next) || /^(captain|c|gk)/i.test(next);
                    const isSleeve = /sleeve|polos|custom/i.test(next);
                    if (!isNextNo && !isSleeve && next !== '') {
                        ket = next;
                        step = 5;
                    }
                }
                rows.push({ no, nama, model, size, ket });
                i += step;
            }
            return rows;
        },

        async updateDesignSize(item, sizeKey, newQty) {
            if (newQty < 1) return;
            try {
                const res = await fetch('/cart/' + item.id + '/update-sizes', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ sizes: { ...item.design_data.ukuran, [sizeKey]: newQty } }),
                });
                const data = await res.json();
                if (data.success) {
                    item.design_data.ukuran = data.ukuran;
                    item.qty = data.qty;
                }
            } catch (e) {}
        },

        checkoutFromCart(item) {
            const state = {
                mode: 'cart_checkout',
                cartItems: [item],
                jenis: item.design_data ? (item.design_data.jenis || 'custom') : 'katalog',
                step: 2,
                subStep: 2,
                prioritas: item.design_data ? (item.design_data.prioritas || 'normal') : 'normal',
            };
            localStorage.setItem('checkout_state', JSON.stringify(state));
            window.location.href = '{{ route('pemesanan') }}';
        },

        checkoutFromCartMultiple() {
            const selected = this.cartItems.filter(i => i.is_selected);
            if (selected.length === 0) {
                Swal.fire({ icon: 'info', title: 'Pilih Produk', text: 'Pilih setidaknya satu produk untuk dicheckout.' });
                return;
            }
            const state = {
                mode: 'cart_checkout',
                cartItems: selected,
                jenis: 'custom',
                step: 2,
                subStep: 2,
                prioritas: 'normal',
            };
            localStorage.setItem('checkout_state', JSON.stringify(state));
            window.location.href = '{{ route('pemesanan') }}';
        },

        openPhotoSwipeProfile(files, index) {
            var imageFiles = files.filter(function(f) { return f.path; }).map(function(f) {
                return { path: '/storage/' + f.path, name: f.name || 'File' };
            });
            if (imageFiles.length && window.openPhotoSwipe) {
                window.openPhotoSwipe(imageFiles, index);
            }
        },

        async removeWishlist(productId) {
            if (!window.Swal) return;
            const result = await Swal.fire({
                title: 'Hapus dari Favorit?',
                text: 'Produk akan dihapus dari daftar favorit Anda.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            });
            if (!result.isConfirmed) return;
            try {
                const res = await fetch('{{ route("api.wishlist.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ product_id: productId })
                });
                const data = await res.json();
                if (data.success) {
                    this.wishlist = this.wishlist.filter(item => item.id !== productId);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Produk dihapus dari favorit',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            } catch(e) {
                console.error(e);
            }
        },

    }
}
</script>
@endpush
