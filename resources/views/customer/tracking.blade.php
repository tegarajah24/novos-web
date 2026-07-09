@extends('layouts.customer')

@section('title', 'Tracking Pesanan — Novos')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8" x-data="trackingForm()">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Tracking Pesanan</h1>
        <p class="text-gray-500 mt-1" x-show="state === 'empty'">Cari pesanan Anda berdasarkan nomor pesanan</p>
    </div>

    {{-- STATE 1: EMPTY --}}
    <div x-show="state === 'empty'" x-cloak x-transition:enter.duration.200 class="text-center py-10">
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-300 mb-5">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <line x1="3" y1="9" x2="21" y2="9"/>
            <line x1="9" y1="21" x2="9" y2="9"/>
        </svg>
        <p class="text-gray-500 font-medium mb-1">Cek Status Pesanan</p>
        <p class="text-gray-400 text-sm mb-6">Masukkan nomor pesanan untuk melihat status terkini</p>

        {{-- Search input --}}
        <div class="max-w-md mx-auto flex gap-3">
            <input type="text" x-model="searchQuery" @keydown.enter="fetchOrder()"
                   placeholder="Contoh: NVS-20240601-001"
                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow">
            <button @click="fetchOrder()"
                    class="px-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors shrink-0 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Lacak
            </button>
        </div>

        <div class="mt-6 border-t border-gray-100 pt-6">
            <p class="text-xs text-gray-400 mb-3">Atau lihat dari</p>
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-5 py-2 border-2 border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:border-gray-300 hover:text-gray-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Riwayat Pesanan
            </a>
        </div>
    </div>

    {{-- STATE 2: LOADING (SPINNER) --}}
    <div x-show="state === 'loading'" x-cloak x-transition:enter.duration.200 class="flex flex-col items-center justify-center py-20">
        <svg class="animate-spin w-10 h-10 text-[#1a237e] mb-4" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <p class="text-gray-500 text-sm">Mencari pesanan...</p>
    </div>

    {{-- STATE 3: ERROR --}}
    <div x-show="state === 'error'" x-cloak x-transition:enter.duration.200 class="text-center py-10">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-red-300 mb-4">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <p class="text-gray-600 font-medium mb-1" x-text="errorMessage"></p>
        <p class="text-gray-400 text-sm mb-6">Periksa kembali nomor pesanan Anda</p>
        <button @click="state = 'empty'"
                class="px-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors">
            Coba Lagi
        </button>
    </div>

    {{-- STATE 4: RESULT --}}
    <div x-show="state === 'result'" x-cloak x-transition:enter.duration.300>
        {{-- Header Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Nomor Pesanan</p>
                    <h2 class="text-xl font-bold text-gray-900 tracking-wider" x-text="order.id"></h2>
                </div>
                <div class="text-right sm:text-left">
                    <p class="text-sm text-gray-500 mb-1">Tanggal Order</p>
                    <p class="font-semibold text-gray-900" x-text="order.date"></p>
                </div>
                <div>
                    <span
                        :class="statusBadgeClass"
                        class="inline-block px-4 py-1.5 rounded-full text-sm font-semibold"
                        x-text="statusLabel"
                    ></span>
                </div>
                <template x-if="!shared && order.id">
                    <button @click="copyShareLink"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all text-gray-600 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                        Bagikan
                    </button>
                </template>
            </div>
        </div>

        {{-- Shared banner --}}
        <template x-if="shared">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-500 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                <p class="text-sm text-blue-700">
                    🔗 Tracking dibagikan oleh <strong x-text="order.team_name || 'Customer'"></strong>
                </p>
            </div>
        </template>

        {{-- Stepper Horizontal --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 md:p-8 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-8">Riwayat Status</h3>

            <div class="overflow-x-auto pb-2 stepper-scroll">
                <div class="relative flex items-start justify-between min-w-[640px] md:min-w-0 px-1 pt-6">
                    {{-- Progress bar background --}}
                    <div class="absolute top-[43px] left-[4%] right-[4%] h-1 bg-gray-200 rounded-full"></div>

                    {{-- Progress bar fill (animated) --}}
                    <div class="absolute top-[43px] left-[4%] h-1 bg-gradient-to-r from-green-400 to-green-500 rounded-full transition-all duration-1000 ease-out"
                         :style="`width: ${animateProgress ? progressPercent : 0}%`"></div>

                    {{-- Stages --}}
                    <template x-for="(stage, i) in stages" :key="i">
                        <div class="flex flex-col items-center text-center relative z-10" style="width: 14.285%">
                            {{-- Circle --}}
                            <div class="relative">
                                {{-- Ping ring for active --}}
                                <template x-if="stage.active && !stage.done">
                                    <span class="absolute -inset-1.5 flex">
                                        <span class="animate-ping absolute inset-0 rounded-full bg-blue-400/40"></span>
                                        <span class="absolute inset-0 rounded-full bg-blue-300/20"></span>
                                    </span>
                                </template>

                                {{-- Circle body --}}
                                <div :class="stage.done ? 'bg-green-500 shadow-lg shadow-green-200' : stage.active ? 'bg-[#1a237e] ring-4 ring-[#1a237e]/20 shadow-lg shadow-blue-200 animate-glow' : 'bg-gray-200'"
                                     class="w-[38px] h-[38px] rounded-full flex items-center justify-center transition-all duration-500 relative">
                                    {{-- Checkmark for done --}}
                                    <svg x-show="stage.done" class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    {{-- White dot for active --}}
                                    <span x-show="stage.active && !stage.done" class="w-3 h-3 bg-white rounded-full"></span>
                                    {{-- Number for pending --}}
                                    <span x-show="!stage.done && !stage.active" class="text-xs font-bold"
                                          :class="stage.pending ? 'text-gray-400' : 'text-white'" x-text="i + 1"></span>
                                </div>
                            </div>

                            {{-- Label below --}}
                            <div class="mt-3 max-w-[90px]">
                                <p :class="stage.active ? 'text-[#1a237e] font-bold' : stage.done ? 'text-gray-900 font-semibold' : 'text-gray-400'"
                                   class="text-[11px] md:text-xs leading-tight transition-colors" x-text="stage.label"></p>
                                <p class="text-[10px] mt-1 font-medium"
                                   :class="stage.done ? 'text-green-600' : stage.active ? 'text-blue-600' : 'text-gray-300'"
                                   x-text="stage.done ? 'Selesai' : stage.active ? 'Berjalan' : 'Belum'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>


        {{-- Lightbox via PhotoSwipe (di-handle oleh openPhotoSwipe) --}}
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
.stepper-scroll { overflow-x: auto; }
.stepper-scroll::-webkit-scrollbar { height: 4px; }
.stepper-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
.stepper-scroll::-webkit-scrollbar-thumb { background: #c4c4c4; border-radius: 10px; }
.stepper-scroll::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
@keyframes glow-pulse {
    0%, 100% { box-shadow: 0 0 8px rgba(37, 99, 235, 0.3); }
    50% { box-shadow: 0 0 20px rgba(37, 99, 235, 0.6); }
}
.animate-glow { animation: glow-pulse 2s ease-in-out infinite; }
</style>

<script>
function trackingForm() {
    return {
        state: '{{ ($orderData ?? false) ? 'result' : 'empty' }}',
        searchQuery: '',
        errorMessage: '',
        animateProgress: false,
        shared: @json($shared ?? false),
        shareUrl: @json($shareUrl ?? null),
        order: @json($orderData ?? null) || { id: '', date: '', status: 'pending' },

        async fetchOrder() {
            const q = this.searchQuery.trim();
            if (!q) return;

            this.state = 'loading';
            this.errorMessage = '';

            try {
                const res = await fetch('{{ route("tracking.search") }}?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!data.found) {
                    this.errorMessage = data.message || 'Pesanan tidak ditemukan';
                    this.state = 'error';
                    return;
                }

                this.order = data.data;
                this.shareUrl = data.share_url || null;
                this.state = 'result';

                this.$nextTick(() => {
                    setTimeout(() => { this.animateProgress = true; }, 200);
                });
            } catch (e) {
                this.errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                this.state = 'error';
            }
        },

        get statusLabel() {
            const labels = {
                'menunggu_pembayaran': 'Pembayaran DP',
                'dikonfirmasi': 'Dikonfirmasi',
                'disetujui': 'Disetujui',
                'di_design': 'Di Design',
                'siap_cetak': 'Siap Cetak',
                'diproduksi': 'Diproduksi',
                'selesai': 'Selesai',
                'dibatalkan': 'Dibatalkan'
            };
            return labels[this.order.status] || this.order.status;
        },

        get statusBadgeClass() {
            const colors = {
                'menunggu_pembayaran': 'bg-orange-100 text-orange-800',
                'dikonfirmasi': 'bg-blue-100 text-blue-800',
                'disetujui': 'bg-green-100 text-green-800',
                'di_design': 'bg-purple-100 text-purple-800',
                'siap_cetak': 'bg-indigo-100 text-indigo-800',
                'diproduksi': 'bg-orange-100 text-orange-800',
                'selesai': 'bg-green-100 text-green-800',
                'dibatalkan': 'bg-red-100 text-red-800'
            };
            return colors[this.order.status] || 'bg-gray-100 text-gray-800';
        },

        get showAcc() {
            return ['di_design', 'siap_cetak'].includes(this.order.status);
        },

        get stages() {
            const stageDefs = [
                { key: 'menunggu_pembayaran', label: 'Pembayaran DP' },
                { key: 'dikonfirmasi', label: 'Dikonfirmasi' },
                { key: 'di_design', label: 'Tahap Desain' },
                { key: 'diproduksi', label: 'Produksi' },
                { key: 'pelunasan', label: 'Pelunasan' },
                { key: 'selesai', label: 'Selesai' }
            ];

            const statusToStageMap = {
                'menunggu_pembayaran': 'menunggu_pembayaran',
                'dikonfirmasi': 'dikonfirmasi',
                'disetujui': 'dikonfirmasi',
                'di_design': 'di_design',
                'siap_cetak': 'di_design',
                'menunggu_spk': 'di_design',
                'diproduksi': 'diproduksi',
                'pelunasan': 'pelunasan',
                'selesai': 'selesai'
            };

            const currentStageKey = statusToStageMap[this.order.status] || 'menunggu_pembayaran';
            const stageOrder = ['menunggu_pembayaran', 'dikonfirmasi', 'di_design', 'diproduksi', 'pelunasan', 'selesai'];
            const currentIdx = stageOrder.indexOf(currentStageKey);

            return stageDefs.map((s, i) => {
                const stageIdx = stageOrder.indexOf(s.key);
                return {
                    ...s,
                    done: stageIdx < currentIdx,
                    active: stageIdx === currentIdx,
                    pending: stageIdx > currentIdx
                };
            });
        },

        get progressPercent() {
            const doneCount = this.stages.filter(s => s.done).length;
            const total = this.stages.length - 1;
            return total > 0 ? (doneCount / total) * 100 : 0;
        },

        get progressReady() {
            return this.progressPercent;
        },

        init() {
            if (this.order.id) {
                this.$nextTick(() => {
                    setTimeout(() => { this.animateProgress = true; }, 200);
                });
            }
        },

        async copyShareLink() {
            let url = this.shareUrl;

            if (!url) {
                try {
                    const res = await fetch('/tracking/' + this.order.id + '/share-token', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (!data.url) {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal membuat link sharing' });
                        return;
                    }
                    url = data.url;
                    this.shareUrl = url;
                } catch (e) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan' });
                    return;
                }
            }

            try {
                await navigator.clipboard.writeText(url);
                Swal.fire({
                    icon: 'success',
                    title: 'Link Disalin!',
                    text: 'Link tracking berhasil disalin. Bagikan ke grup WhatsApp tim Anda!',
                    confirmButtonColor: '#1a237e',
                    confirmButtonText: 'OK'
                });
            } catch (e) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal Menyalin',
                    text: 'Salin manual: ' + url,
                    confirmButtonColor: '#1a237e',
                    confirmButtonText: 'OK'
                });
            }
        }
    }
}
</script>
@endsection
