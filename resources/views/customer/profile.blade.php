@extends('layouts.customer')

@section('title', 'Profil Saya — Novos')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(229, 231, 235, 0.5);
    }
    .profile-avatar-glow {
        box-shadow: 0 0 20px rgba(26, 35, 126, 0.15);
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8" x-data="profileDashboard(window.profileOrders, window.profileUser)">
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

    <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
        {{-- ==================== SIDEBAR (LEFT) ==================== --}}
        <div class="space-y-6">
            {{-- Navigation Menu --}}
            <div class="glass-card bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
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
                        <div x-data="{ showMenu: false }" :class="showMenu ? 'relative z-10' : ''" class="glass-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
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
                         class="glass-card bg-white rounded-2xl py-16 px-6 text-center shadow-sm border border-gray-100 flex flex-col items-center">
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
                                                <span class="absolute top-2 left-2 px-1.5 py-0.5 bg-white/80 backdrop-blur-sm text-[10px] font-semibold text-gray-600 rounded truncate max-w-[100px]" x-text="file.name"></span>
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

            {{-- 2. TAB: PENGATURAN PROFIL --}}
            <div x-show="activeTab === 'pengaturan'" x-cloak class="space-y-6">
                <div class="glass-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
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
                                <p class="text-xs text-gray-500">Mendukung PNG, JPG, JPEG atau WEBP (Maksimal 2MB).</p>
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
                <div class="glass-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Alamat Pengiriman</h3>
                    <p class="text-sm text-gray-500 mb-6">Alamat default pengiriman pesanan konveksi Anda.</p>

                    <form method="POST" action="{{ route('profile.update') }}" @submit.prevent="if ($event.target.checkValidity()) $event.target.submit()">
                        @csrf
                        @method('patch')

                        {{-- Hidden inputs to preserve biodata --}}
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="phone" value="{{ $user->phone }}">
                        <input type="hidden" name="fullname" value="{{ $user->fullname }}">

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap Pengiriman <span class="text-red-500">*</span></label>
                            <textarea name="address" rows="5" required placeholder="Tuliskan alamat lengkap beserta kecamatan, kota, dan kode pos..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm resize-none">{{ old('address', $user->address) }}</textarea>
                        </div>

                        <div class="flex justify-end pt-3">
                            <button type="submit"
                                class="px-6 py-3 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors flex items-center justify-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Simpan Alamat
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 4. TAB: KEAMANAN --}}
            <div x-show="activeTab === 'keamanan'" x-cloak class="space-y-6">
                <div class="glass-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
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
                <div class="glass-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
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

function profileDashboard(orders = [], user = {}) {
    return {
        activeTab: (new URLSearchParams(window.location.search)).get('tab') || 'pengaturan',
        orderFilter: 'menunggu_pembayaran',
        orders: orders,
        user: user,
        selectedOrder: null,
        currentPage: 1,
        perPage: 5,

        init() {
            this.$watch('orderFilter', () => this.currentPage = 1);
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
