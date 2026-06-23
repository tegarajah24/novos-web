@extends('layouts.customer')

@section('title', 'Tracking Pesanan — Novos')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8" x-data="trackingForm()">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Tracking Pesanan</h1>
        <p class="text-gray-500 mt-1" x-show="!order.id">Silakan pilih pesanan dari riwayat pembelian Anda</p>
    </div>

    {{-- No order selected --}}
    <div x-show="!order.id" x-cloak class="text-center py-16">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-300 mb-4">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <line x1="3" y1="9" x2="21" y2="9"/>
            <line x1="9" y1="21" x2="9" y2="9"/>
        </svg>
        <p class="text-gray-500 font-medium">Belum ada pesanan yang dipilih</p>
        <p class="text-gray-400 text-sm mt-1">Klik tombol "Tracking" pada pesanan Anda di halaman profil</p>
        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 mt-6 px-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Lihat Riwayat Pesanan
        </a>
    </div>

    {{-- Result Card --}}
    <div x-show="order.id" x-cloak x-transition:enter.duration.300>
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
            </div>
        </div>

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
                        <div class="flex flex-col items-center text-center relative z-10" style="width: 16.666%">
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

        {{-- ACC Desain Section (Hero Grid) --}}
        <div x-show="showAcc" x-cloak x-transition:enter.duration.300>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="grid md:grid-cols-[70%_30%]">
                    {{-- Kiri: Gambar Mockup --}}
                    <div class="p-6 md:p-8 border-b md:border-b-0 md:border-r border-gray-100">
                        <template x-if="order.design_files && order.design_files.length > 0">
                            <div class="grid sm:grid-cols-2 gap-4 h-full">
                                <template x-for="(file, idx) in order.design_files" :key="idx">
                                    <div class="relative group cursor-zoom-in rounded-xl overflow-hidden bg-gray-50 border border-gray-200 min-h-[260px]"
                                         @click="openLightbox(file.url)">
                                        <img :src="file.url" :alt="file.name"
                                             class="w-full h-full object-cover absolute inset-0 transition-transform duration-500 group-hover:scale-105">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300 flex items-center justify-center">
                                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-12 h-12 bg-white/90 rounded-full flex items-center justify-center backdrop-blur-sm">
                                                <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                            </div>
                                        </div>
                                        <span class="absolute top-3 left-3 px-2 py-0.5 bg-white/80 backdrop-blur-sm text-[10px] font-semibold text-gray-600 rounded-md">Mockup Design</span>
                                        <span class="absolute top-3 right-3 px-2 py-0.5 bg-black/40 backdrop-blur-sm text-[10px] font-semibold text-white rounded-md truncate max-w-[120px]" x-text="file.name"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!order.design_files || order.design_files.length === 0">
                            <div class="h-full min-h-[260px] flex flex-col items-center justify-center text-gray-400 p-8">
                                <svg class="w-12 h-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                <p class="text-sm font-medium">Belum ada file desain</p>
                                <p class="text-xs mt-1">Tim design akan mengunggah mockup di sini</p>
                            </div>
                        </template>
                    </div>

                    {{-- Kanan: Panel Informasi & Tombol --}}
                    <div class="p-6 md:p-8 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Konfirmasi Desain</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">Desain jersey Anda sudah selesai. Silakan periksa detailnya dan berikan persetujuan atau ajukan revisi jika ada yang perlu diperbaiki.</p>

                            {{-- Info ringkas --}}
                            <div class="mt-5 space-y-2.5">
                                <div class="flex items-center gap-2.5 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>Bahan: <strong>Dryfit Premium</strong></span>
                                </div>
                                <div class="flex items-center gap-2.5 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>Ukuran: <strong>S - 3XL</strong></span>
                                </div>
                                <div class="flex items-center gap-2.5 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>Sablon: <strong>Rubber PVC</strong></span>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="mt-6 space-y-3">
                            <button @click="accDesign"
                                class="relative overflow-hidden w-full px-6 py-3 bg-[#1a237e] text-white rounded-xl font-semibold hover:bg-[#283593] transition-colors flex items-center justify-center gap-2 group/btn">
                                <span class="absolute inset-0 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/20 to-transparent"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                ACC Desain
                            </button>

                            <button @click="toggleRevision"
                                class="w-full px-6 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors flex items-center justify-center gap-2">
                                <svg :class="revisionOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                Minta Revisi
                            </button>

                            {{-- Inline Revision Form --}}
                            <div x-show="revisionOpen" x-cloak x-collapse.duration.300 class="space-y-3 pt-1">
                                <textarea x-model="revisionNote"
                                    placeholder="Jelaskan bagian mana yang perlu direvisi"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow resize-none"
                                    rows="3"></textarea>
                                <div class="flex gap-2">
                                    <button @click="sendRevision"
                                        class="flex-1 px-4 py-2.5 bg-[#1a237e] text-white text-sm rounded-xl font-semibold hover:bg-[#283593] transition-colors">
                                        Kirim Revisi
                                    </button>
                                    <button @click="revisionOpen = false; revisionNote = ''"
                                        class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-600 text-sm rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lightbox Fullscreen --}}
        <div x-show="lightboxOpen" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeLightbox"
             class="fixed inset-0 z-[999] bg-black/90 flex items-center justify-center p-4 cursor-zoom-out">
            <button @click="closeLightbox" class="absolute top-6 right-6 w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors">
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <img :src="lightboxImage" alt="Preview Desain"
                 class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl"
                 @click.stop>
        </div>
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
        animateProgress: false,
        lightboxOpen: false,
        lightboxImage: '',
        revisionOpen: false,
        revisionNote: '',
        order: @json($orderData ?? null) || { id: '', date: '', status: 'pending' },

        get statusLabel() {
            const labels = {
                'menunggu_validasi': 'Menunggu Validasi',
                'menunggu_pembayaran': 'Menunggu Pembayaran',
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
                'menunggu_validasi': 'bg-yellow-100 text-yellow-800',
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
                { key: 'menunggu_validasi', label: 'Pesanan Masuk' },
                { key: 'menunggu_pembayaran', label: 'Menunggu Pembayaran' },
                { key: 'dikonfirmasi', label: 'Pembayaran Dikonfirmasi' },
                { key: 'di_design', label: 'Tahap Desain' },
                { key: 'siap_cetak', label: 'Menunggu ACC Customer' },
                { key: 'diproduksi', label: 'Produksi & Selesai' }
            ];

            const statusOrder = ['menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'];
            const currentIdx = statusOrder.indexOf(this.order.status);

            return stageDefs.map((s, i) => {
                const stageIdx = statusOrder.indexOf(s.key);
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

        async accDesign() {
            try {
                const res = await fetch('/tracking/' + this.order.id + '/acc', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!data.success) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                    return;
                }
                this.order.status = data.status;
                this.$nextTick(() => { setTimeout(() => { this.animateProgress = true; }, 200); });
                Swal.fire({
                    icon: 'success',
                    title: 'Desain Disetujui!',
                    text: 'Desain telah Anda setujui. Pesanan akan dilanjutkan ke tahap berikutnya.',
                    confirmButtonColor: '#1a237e',
                    confirmButtonText: 'OK'
                });
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan' });
            }
        },

        openLightbox(img) {
            this.lightboxImage = img;
            this.lightboxOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeLightbox() {
            this.lightboxOpen = false;
            this.lightboxImage = '';
            document.body.style.overflow = '';
        },

        toggleRevision() {
            this.revisionOpen = !this.revisionOpen;
            if (!this.revisionOpen) this.revisionNote = '';
        },

        async sendRevision() {
            if (!this.revisionNote.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan kosong',
                    text: 'Silakan tulis catatan revisi terlebih dahulu.',
                    confirmButtonColor: '#1a237e',
                    confirmButtonText: 'OK'
                });
                return;
            }
            try {
                const res = await fetch('/tracking/' + this.order.id + '/revision', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ note: this.revisionNote })
                });
                const data = await res.json();
                if (!data.success) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                    return;
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Revisi Dikirim!',
                    text: 'Catatan revisi Anda telah dikirim ke tim desain.',
                    confirmButtonColor: '#1a237e',
                    confirmButtonText: 'OK'
                }).then(() => {
                    this.revisionOpen = false;
                    this.revisionNote = '';
                });
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan' });
            }
        }
    }
}
</script>
@endsection
