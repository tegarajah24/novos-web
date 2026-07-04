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
<div class="max-w-6xl mx-auto px-4 py-8" x-data="profileDashboard(window.profileOrders, window.profileUser, window.profileAddresses, window.profileCart)">
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
                    {{-- Tab: Alamat --}}
                    <button @click="setActiveTab('alamat')"
                        :class="activeTab === 'alamat' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Alamat Pengiriman
                    </button>
                    {{-- Tab: Keamanan --}}
                    <button @click="setActiveTab('keamanan')"
                        :class="activeTab === 'keamanan' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Keamanan Akun
                    </button>
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
                        Menunggu Pembayaran
                    </button>
                    <button @click="orderFilter = 'proses'"
                        :class="orderFilter === 'proses' ? 'bg-blue-100 text-blue-800 shadow-sm' : 'text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                        Proses Produksi
                    </button>
                    <button @click="orderFilter = 'kirim'"
                        :class="orderFilter === 'kirim' ? 'bg-blue-100 text-blue-800 shadow-sm' : 'text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-xs font-semibold transition-all">
                        Sedang Dikirim
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
                                <div class="text-sm">
                                    <span class="text-gray-500">Total: </span>
                                    <span class="font-bold text-[#1a237e]" x-text="formatRupiah(order.total_price)"></span>
                                    <template x-if="order.order_items && order.order_items.length">
                                        <span class="text-gray-400 text-xs ml-1">(<span x-text="totalQty(order.order_items) + ' pcs'"></span>)</span>
                                    </template>
                                </div>
                                <div class="flex gap-2 items-center">
                                    <button @click="openDetail(order)" class="px-4 py-2 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-50 transition-colors">Lihat Detail Transaksi</button>
                                    <template x-if="order.status === 'menunggu_pembayaran'">
                                        <button @click="payOrder(order.order_number)" class="px-4 py-2 bg-[#1a237e] text-white rounded-lg text-xs font-bold hover:bg-[#283593] transition-colors">Setujui Detail & Bayar Sekarang</button>
                                    </template>
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
                    <div x-show="selectedOrder" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
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
                                <span class="text-xs text-gray-500">Nama Tim</span>
                                <span class="text-sm font-medium text-gray-900" x-text="selectedOrder?.design_request?.team_name || 'Katalog'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Bahan Jersey</span>
                                <span class="text-sm font-medium text-gray-900" x-text="selectedOrder?.design_request?.material || '-'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Kerah</span>
                                <span class="text-sm font-medium text-gray-900" x-text="selectedOrder?.design_request?.collar_style || '-'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Pola Jahitan</span>
                                <span class="text-sm font-medium text-gray-900" x-text="selectedOrder?.design_request?.pattern || '-'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Jumlah</span>
                                <span class="text-sm font-medium text-gray-900" x-text="(selectedOrder?.order_items ? totalQty(selectedOrder.order_items) : '-') + ' pcs'"></span>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <span class="text-sm font-semibold text-gray-700">Total Bayar</span>
                                <span class="text-base font-bold text-[#1a237e]" x-text="formatRupiah(selectedOrder?.total_price)"></span>
                            </div>
                            <template x-if="selectedOrder?.notes">
                                <div class="bg-amber-50/50 rounded-xl p-4 border border-amber-200/60">
                                    <p class="text-xs text-gray-500 font-medium mb-1">Catatan Pesanan</p>
                                    <p class="text-sm text-gray-700" x-text="selectedOrder.notes"></p>
                                </div>
                            </template>
                            <template x-if="selectedOrder?.design_request?.all_design_files?.length">
                                <div class="pt-3 border-t border-gray-100">
                                    <p class="text-xs text-gray-500 font-medium mb-3">File Desain</p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <template x-for="(file, idx) in selectedOrder.design_request.all_design_files" :key="idx">
                                            <a :href="'/storage/' + file.path" target="_blank"
                                               class="group relative aspect-square rounded-xl overflow-hidden bg-gray-50 border border-gray-200">
                                                <img :src="'/storage/' + file.path" :alt="file.name"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                <span class="absolute top-2 left-2 px-1.5 py-0.5 bg-white text-[10px] font-semibold text-gray-600 rounded truncate max-w-[100px]" x-text="file.name"></span>
                                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                                                </div>
                                            </a>
                                        </template>
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
                            <div class="hidden md:grid grid-cols-[40px_60px_1fr_100px_120px_100px] gap-4 px-4 py-3 bg-gray-50 rounded-xl text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                <span></span>
                                <span></span>
                                <span>Produk</span>
                                <span>Ukuran</span>
                                <span>Harga</span>
                                <span class="text-center">Jumlah</span>
                            </div>
                            <template x-for="(item, index) in cartItems" :key="item.id">
                                <div class="grid grid-cols-[40px_60px_1fr_100px_120px_100px_72px] md:grid-cols-[40px_60px_1fr_100px_120px_100px_72px] gap-4 items-center px-4 py-4 hover:bg-gray-50 rounded-xl transition-colors border-b border-gray-100 last:border-0">
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
                                                <p class="text-xs text-gray-400 truncate" x-text="item.design_data.bahan + ' | ' + item.design_data.kerah"></p>
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
                                    <div class="flex items-center gap-1 justify-center">
                                        <button @click="updateCartQty(item, item.qty - 1)" 
                                            class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 transition-colors"
                                            :disabled="item.qty <= 1">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/></svg>
                                        </button>
                                        <span class="w-10 text-center text-sm font-semibold text-gray-700" x-text="item.qty"></span>
                                        <button @click="updateCartQty(item, item.qty + 1)"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 transition-colors">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <template x-if="item.design_data">
                                            <button @click="checkoutFromCart(item)" class="p-1.5 text-[#1a237e] hover:text-[#283593] transition-colors rounded-lg hover:bg-blue-50" title="Lanjutkan Pesanan">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                            </button>
                                        </template>
                                        <button @click="deleteCartItem(item, index)" class="p-1.5 text-gray-300 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        </button>
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

            {{-- 2. TAB: PENGATURAN PROFIL --}}
            <div x-show="activeTab === 'pengaturan'" x-cloak class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Pengaturan Profil</h3>
                    <p class="text-sm text-gray-500 mb-6">Kelola biodata diri, kontak utama, dan foto profil Anda.</p>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        {{-- Foto Profil Section --}}
                        <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-gray-100 mb-6" x-data="{ 
                            imagePreview: user.avatar ? '/storage/' + user.avatar : null,
                            handleFileChange(e) {
                                const file = e.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (event) => {
                                        this.imagePreview = event.target.result;
                                    };
                                    reader.readAsDataURL(file);
                                }
                            }
                        }">
                            <div class="w-24 h-24 rounded-full overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 profile-avatar-glow">
                                <template x-if="imagePreview">
                                    <img :src="imagePreview" alt="Preview Avatar" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!imagePreview">
                                    <div class="w-full h-full bg-[#1a237e] text-white flex items-center justify-center text-3xl font-bold">
                                        <span x-text="getUserInitials()"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="text-center sm:text-left space-y-2">
                                <h4 class="font-bold text-sm text-gray-900">Foto Profil</h4>
                                <p class="text-xs text-gray-500">Mendukung PNG, JPG atau JPEG (Maksimal 5MB).</p>
                                <label class="inline-block px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 hover:text-gray-900 transition-colors cursor-pointer">
                                    Pilih Foto
                                    <input type="file" name="avatar" class="hidden" accept="image/*" @change="handleFileChange($event)">
                                </label>
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
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow bg-white appearance-none text-sm"
                                            :disabled="addressLoading.provinces">
                                            <option value="">Pilih Provinsi</option>
                                            <template x-for="prov in provinces" :key="prov.id">
                                                <option :value="prov.id" x-text="prov.name"></option>
                                            </template>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <template x-if="addressLoading.provinces">
                                                <svg class="animate-spin h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                            </template>
                                            <template x-if="!addressLoading.provinces">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </template>
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
                        <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Novos,%20saya%20butuh%20bantuan%20terkait%20pesanan%20saya"
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
                            <p class="text-xs text-gray-500 leading-relaxed">Kami memiliki 3 pilihan prioritas pengerjaan saat checkout: <strong>Normal</strong> (7-14 hari kerja), <strong>Express</strong> (3-6 hari kerja), and <strong>Super Express</strong> (1-2 hari kerja) terhitung setelah pembayaran DP/Lunas dikonfirmasi.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
window.profileOrders = @json($orders);
window.profileUser = @json($user);
window.profileAddresses = @json($addresses);
window.profileCart = @json($cartItems);

function profileDashboard(orders = [], user = {}, initialAddresses = [], initialCart = []) {
    return {
        activeTab: (new URLSearchParams(window.location.search)).get('tab') || 'pengaturan',
        orderFilter: 'menunggu_pembayaran',
        orders: orders,
        user: user,
        selectedOrder: null,
        currentPage: 1,
        perPage: 5,

        // Address management
        addresses: initialAddresses,
        cartItems: initialCart,
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
        provinces: [],
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
                        const qty = Object.values(item.design_data.ukuran || {}).reduce((a, b) => a + (parseInt(b) || 0), 0);
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

        init() {
            this.$watch('orderFilter', () => this.currentPage = 1);
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
            if (this.provinces.length > 0) return;
            this.addressLoading.provinces = true;
            try {
                const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                this.provinces = await res.json();
            } catch (e) {
                console.error('Gagal memuat provinsi', e);
            } finally {
                this.addressLoading.provinces = false;
            }
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
                const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/' + this.selectedProvinceId + '.json');
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
                const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/districts/' + this.selectedRegencyId + '.json');
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
                    return order.status === 'menunggu_validasi' || order.status === 'menunggu_pembayaran';
                }
                if (filter === 'proses') {
                    return ['dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak'].includes(order.status);
                }
                if (filter === 'kirim') {
                    return order.status === 'diproduksi';
                }
                if (filter === 'selesai') {
                    return order.status === 'selesai';
                }
                return false;
            });
        },

        getStatusLabel(status) {
            const labels = {
                'menunggu_validasi': 'Menunggu Validasi',
                'menunggu_pembayaran': 'Menunggu Pembayaran',
                'dikonfirmasi': 'Dikonfirmasi',
                'disetujui': 'Desain Dikerjakan',
                'di_design': 'Tahap Desain',
                'siap_cetak': 'Menunggu ACC Desain',
                'diproduksi': 'Sedang Diproduksi / Kirim',
                'selesai': 'Pesanan Selesai',
                'dibatalkan': 'Pesanan Dibatalkan'
            };
            return labels[status] || status;
        },

        getStatusBadgeClass(status) {
            const classes = {
                'menunggu_validasi': 'bg-amber-100 text-amber-800',
                'menunggu_pembayaran': 'bg-orange-100 text-orange-800',
                'dikonfirmasi': 'bg-blue-100 text-blue-800',
                'disetujui': 'bg-indigo-100 text-indigo-800',
                'di_design': 'bg-purple-100 text-purple-800',
                'siap_cetak': 'bg-pink-100 text-pink-800',
                'diproduksi': 'bg-orange-100 text-orange-800',
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
            try {
                const res = await fetch('/cart/' + item.id + '/toggle-select', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    item.is_selected = !item.is_selected;
                }
            } catch (e) {}
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

        checkoutFromCart(item) {
            const state = {
                mode: 'cart_checkout',
                cartItems: [item],
                jenis: item.design_data ? (item.design_data.jenis || 'custom') : 'katalog',
                step: 2,
                subStep: 2,
                prioritas: item.design_data ? (item.design_data.prioritas || 'normal') : 'normal',
                pembayaran: 'midtrans',
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
                pembayaran: 'midtrans',
            };
            localStorage.setItem('checkout_state', JSON.stringify(state));
            window.location.href = '{{ route('pemesanan') }}';
        },

        async payOrder(orderId) {
            const confirm = await Swal.fire({
                title: 'Setujui Detail Pesanan?',
                text: 'Dengan melanjutkan, Anda menyetujui detail pesanan dan akan diarahkan ke pembayaran.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a237e',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            });

            if (!confirm.isConfirmed) return;

            Swal.fire({
                title: 'Memproses...',
                text: 'Harap tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const res = await fetch('/payment/approve/' + orderId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await res.json();
                Swal.close();

                if (!data.snap_token) {
                    throw new Error(data.message || 'Gagal mendapatkan token pembayaran');
                }

                window.snap.pay(data.snap_token, {
                    onSuccess: () => {
                        window.location.href = '/payment/finish?order_id=' + data.midtrans_order_id;
                    },
                    onPending: () => {
                        Swal.fire({
                            icon: 'info',
                            title: 'Pembayaran Tertunda',
                            text: 'Harap selesaikan pembayaran Anda.',
                            confirmButtonColor: '#1a237e'
                        });
                    },
                    onClose: () => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pembayaran Dibatalkan',
                            text: 'Selesaikan transaksi di tab Menunggu Pembayaran.',
                            confirmButtonColor: '#1a237e'
                        });
                    },
                    onError: () => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Pembayaran Gagal',
                            text: 'Gagal melakukan pembayaran. Silakan coba kembali.',
                            confirmButtonColor: '#1a237e'
                        });
                    }
                });
            } catch (err) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: err.message || 'Terjadi kesalahan sistem'
                });
            }
        }
    }
}
</script>
@endpush
