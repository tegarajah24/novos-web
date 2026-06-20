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
        box-shadow: 0 0 20px rgba(26, 35, 126, 0.1);
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8" x-data="profileDashboard(@json($orders), @json($user))">
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
            {{-- Profile Card --}}
            <div class="glass-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col items-center text-center">
                {{-- Avatar --}}
                <div class="w-20 h-20 rounded-full bg-[#1a237e] text-white flex items-center justify-center text-2xl font-bold mb-4 profile-avatar-glow">
                    <span x-text="getUserInitials()"></span>
                </div>
                {{-- Name & Role --}}
                <h2 class="font-bold text-gray-900 text-lg leading-tight" x-text="user.name"></h2>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#1a237e] mt-2" x-text="user.role ? user.role.name : 'Customer'"></span>

                {{-- Contact Info --}}
                <div class="w-full border-t border-gray-100 mt-5 pt-4 text-left space-y-3">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <span class="truncate" x-text="user.email"></span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span x-text="user.phone || 'Nomor HP Belum Diisi'"></span>
                    </div>
                </div>
            </div>

            {{-- Navigation Menu --}}
            <div class="glass-card bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                <nav class="space-y-1">
                    {{-- Tab: Pembelian --}}
                    <button @click="activeTab = 'pembelian'"
                        :class="activeTab === 'pembelian' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                        Riwayat Pembelian
                    </button>
                    {{-- Tab: Desain Saya --}}
                    <button @click="activeTab = 'desain'"
                        :class="activeTab === 'desain' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"/><path d="M12 8V16"/><path d="M8 12H16"/></svg>
                        Desain &amp; Kustom Saya
                    </button>
                    {{-- Tab: Alamat --}}
                    <button @click="activeTab = 'alamat'"
                        :class="activeTab === 'alamat' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Info &amp; Alamat Saya
                    </button>
                    {{-- Tab: Keamanan --}}
                    <button @click="activeTab = 'keamanan'"
                        :class="activeTab === 'keamanan' ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Keamanan Akun
                    </button>
                    {{-- Tab: Bantuan --}}
                    <button @click="activeTab = 'bantuan'"
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
                <div class="glass-card bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button @click="orderFilter = 'menunggu_pembayaran'"
                            :class="orderFilter === 'menunggu_pembayaran' ? 'bg-amber-550 border-amber-500 text-amber-900 bg-amber-50' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                            class="px-3 py-3.5 border rounded-xl text-xs font-bold transition-all flex flex-col items-center gap-1.5">
                            <span class="text-lg">📌</span>
                            Menunggu Pembayaran
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-200 text-amber-900 font-semibold"
                                  x-text="getOrdersCountByFilter('menunggu_pembayaran')"></span>
                        </button>

                        <button @click="orderFilter = 'proses'"
                            :class="orderFilter === 'proses' ? 'bg-blue-50 border-blue-200 text-blue-900' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                            class="px-3 py-3.5 border rounded-xl text-xs font-bold transition-all flex flex-col items-center gap-1.5">
                            <span class="text-lg">⚙️</span>
                            Proses Produksi
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-200 text-blue-900 font-semibold"
                                  x-text="getOrdersCountByFilter('proses')"></span>
                        </button>

                        <button @click="orderFilter = 'kirim'"
                            :class="orderFilter === 'kirim' ? 'bg-orange-50 border-orange-200 text-orange-900' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                            class="px-3 py-3.5 border rounded-xl text-xs font-bold transition-all flex flex-col items-center gap-1.5">
                            <span class="text-lg">🚚</span>
                            Sedang Dikirim
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-orange-200 text-orange-900 font-semibold"
                                  x-text="getOrdersCountByFilter('kirim')"></span>
                        </button>

                        <button @click="orderFilter = 'selesai'"
                            :class="orderFilter === 'selesai' ? 'bg-green-50 border-green-200 text-green-900' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                            class="px-3 py-3.5 border rounded-xl text-xs font-bold transition-all flex flex-col items-center gap-1.5">
                            <span class="text-lg">✅</span>
                            Pesanan Selesai
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-green-200 text-green-900 font-semibold"
                                  x-text="getOrdersCountByFilter('selesai')"></span>
                        </button>
                    </div>
                </div>

                {{-- Order List --}}
                <div class="space-y-4">
                    <template x-for="order in getFilteredOrders()" :key="order.id">
                        <div class="glass-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            {{-- Card Header --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">No. Pesanan</p>
                                    <h3 class="font-bold text-gray-900 text-base font-mono mt-0.5" x-text="order.order_number"></h3>
                                </div>
                                <div class="sm:text-right">
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Tanggal</p>
                                    <p class="text-sm font-semibold text-gray-700 mt-0.5" x-text="formatDate(order.created_at)"></p>
                                </div>
                                <div>
                                    <span :class="getStatusBadgeClass(order.status)"
                                          class="px-3.5 py-1.5 rounded-full text-xs font-bold capitalize"
                                          x-text="getStatusLabel(order.status)"></span>
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="py-5 grid md:grid-cols-2 gap-6">
                                {{-- Detail Desain --}}
                                <div class="space-y-3">
                                    <h4 class="font-bold text-sm text-gray-900">Spesifikasi &amp; Detail Desain</h4>
                                    <div class="space-y-2 text-xs text-gray-600">
                                        <div class="flex justify-between">
                                            <span>Nama Tim:</span>
                                            <span class="font-bold text-gray-900" x-text="order.design_request ? order.design_request.team_name : 'Katalog'"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Bahan Jersey:</span>
                                            <span class="font-semibold text-gray-900" x-text="order.design_request ? order.design_request.material : '-'"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Kerah:</span>
                                            <span class="font-semibold text-gray-900" x-text="order.design_request ? order.design_request.collar_style : '-'"></span>
                                        </div>
                                        <div class="flex justify-between" x-show="order.notes">
                                            <span>Catatan:</span>
                                            <span class="font-medium text-gray-700 max-w-[200px] truncate" x-text="order.notes" :title="order.notes"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Total & Summary --}}
                                <div class="bg-gray-50/50 rounded-xl p-4 border border-gray-100 flex flex-col justify-between">
                                    <div class="space-y-1.5 text-xs text-gray-600 mb-4">
                                        <div class="flex justify-between font-semibold">
                                            <span>Jumlah Total:</span>
                                            <span class="text-gray-900" x-text="(order.order_item ? order.order_item.qty : '-') + ' pcs'"></span>
                                        </div>
                                        <div class="flex justify-between font-semibold">
                                            <span>Total Bayar:</span>
                                            <span class="text-blue-900 font-bold" x-text="formatRupiah(order.total_price)"></span>
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex gap-2.5">
                                        <template x-if="order.status === 'pending'">
                                            <button @click="payOrder(order.id)"
                                                class="flex-1 py-2.5 bg-blue-900 text-white rounded-lg text-xs font-bold hover:bg-blue-800 transition-colors shadow-sm flex items-center justify-center gap-1.5">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                                Bayar Sekarang
                                            </button>
                                        </template>
                                        <a :href="'/tracking?q=' + order.order_number"
                                           class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-50 hover:text-gray-800 transition-colors flex items-center justify-center gap-1.5 text-center">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            Lacak Pesanan
                                        </a>
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
                        <a href="{{ route('pemesanan') }}" class="mt-5 px-6 py-2.5 bg-blue-900 text-white rounded-lg text-xs font-bold hover:bg-blue-800 transition-colors">Buat Pesanan Baru</a>
                    </div>
                </div>
            </div>

            {{-- 2. TAB: DESAIN & KUSTOM SAYA --}}
            <div x-show="activeTab === 'desain'" x-cloak class="space-y-6">
                <div class="glass-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Desain &amp; Kustom Saya</h3>
                    <p class="text-sm text-gray-500 mb-6">Koleksi mockup desain jersey custom yang pernah Anda pesan di Novos.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <template x-for="order in orders.filter(o => o.design_request)" :key="order.id">
                            <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                <div class="bg-gray-50 p-4 relative flex items-center justify-center min-h-[160px]">
                                    {{-- Default Mockup Image --}}
                                    <img src="{{ asset('images/jersey-depan.png') }}" class="h-28 object-contain drop-shadow-md">
                                    <span class="absolute top-3 left-3 bg-[#1a237e] text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider" x-text="order.order_number"></span>
                                </div>
                                <div class="p-4 space-y-3.5">
                                    <h4 class="font-bold text-gray-900 text-sm" x-text="order.design_request.team_name"></h4>
                                    <div class="grid grid-cols-2 gap-2.5 text-xs text-gray-600">
                                        <div>
                                            <span class="text-gray-400">Bahan:</span>
                                            <p class="font-semibold text-gray-800 mt-0.5" x-text="order.design_request.material"></p>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Kerah:</span>
                                            <p class="font-semibold text-gray-800 mt-0.5" x-text="order.design_request.collar_style"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 pt-3 border-t border-gray-100">
                                        <a :href="'/tracking?q=' + order.order_number"
                                           class="w-full text-center py-2 bg-gray-50 hover:bg-gray-100 rounded-lg text-xs font-semibold text-gray-700 hover:text-gray-900 transition-colors">
                                            Lihat Detail Mockup &amp; ACC
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Empty State --}}
                    <div x-show="orders.filter(o => o.design_request).length === 0" x-cloak
                         class="py-16 text-center flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-3xl mb-4">🎨</div>
                        <h4 class="font-bold text-gray-800 text-base mb-1">Belum Ada Desain Custom</h4>
                        <p class="text-sm text-gray-400 max-w-sm mx-auto">Anda belum pernah melakukan order jersey custom dengan upload rancangan sendiri.</p>
                        <a href="{{ route('pemesanan') }}" class="mt-5 px-6 py-2.5 bg-blue-900 text-white rounded-lg text-xs font-bold hover:bg-blue-800 transition-colors">Buat Pesanan Custom</a>
                    </div>
                </div>
            </div>

            {{-- 3. TAB: INFO & ALAMAT PENGIRIMAN --}}
            <div x-show="activeTab === 'alamat'" x-cloak class="space-y-6">
                <div class="glass-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Profil &amp; Alamat Pengiriman</h3>
                    <p class="text-sm text-gray-500 mb-6">Informasi kontak utama dan alamat default pengiriman pesanan konveksi Anda.</p>

                    <form method="POST" action="{{ route('profile.update') }}" @submit.prevent="if ($event.target.checkValidity()) $event.target.submit()">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon (WhatsApp) <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required placeholder="Contoh: 081234567890"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap Pengiriman <span class="text-red-500">*</span></label>
                            <textarea name="address" rows="4" required placeholder="Tuliskan alamat lengkap beserta kecamatan, kota, dan kode pos..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm resize-none">{{ old('address', $user->address) }}</textarea>
                        </div>

                        <div class="flex justify-end pt-3">
                            <button type="submit"
                                class="px-6 py-3 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors flex items-center justify-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Simpan Profil &amp; Alamat
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
                            <p class="text-xs text-gray-500 leading-relaxed">Kami memiliki 3 pilihan prioritas pengerjaan saat checkout: <strong>Normal</strong> (7-14 hari kerja), <strong>Express</strong> (3-6 hari kerja), dan <strong>Super Express</strong> (1-2 hari kerja) terhitung setelah pembayaran DP/Lunas dikonfirmasi.</p>
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
function profileDashboard(orders = [], user = {}) {
    return {
        activeTab: (new URLSearchParams(window.location.search)).get('tab') || 'pembelian',
        orderFilter: 'menunggu_pembayaran',
        orders: orders,
        user: user,

        getUserInitials() {
            if (!this.user.name) return 'U';
            const parts = this.user.name.split(' ');
            if (parts.length > 1) {
                return (parts[0][0] + parts[1][0]).toUpperCase();
            }
            return this.user.name.substring(0, 2).toUpperCase();
        },

        getOrdersCountByFilter(filter) {
            return this.getFilteredOrders(filter).length;
        },

        getFilteredOrders(customFilter = null) {
            const filter = customFilter || this.orderFilter;
            
            return this.orders.filter(order => {
                if (filter === 'menunggu_pembayaran') {
                    return order.status === 'pending';
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
                'pending': 'Menunggu Pembayaran',
                'dikonfirmasi': 'Menunggu Konfirmasi',
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
                'pending': 'bg-amber-100 text-amber-800',
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
            Swal.fire({
                title: 'Menghubungkan ke Pembayaran...',
                text: 'Harap tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const res = await fetch('/payment/snap/' + orderId, {
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Sukses!',
                            text: 'Terima kasih, pembayaran Anda berhasil dikonfirmasi.',
                            confirmButtonColor: '#1a237e'
                        }).then(() => {
                            window.location.reload();
                        });
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
