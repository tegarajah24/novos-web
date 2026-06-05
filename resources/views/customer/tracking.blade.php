@extends('layouts.customer')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8" x-data="trackingForm()">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Tracking Pesanan</h1>
        <p class="text-gray-500 mt-1">Cek status terbaru pesanan jersey Anda</p>
    </div>

    {{-- Search Bar --}}
    <div class="flex gap-3 max-w-xl mb-8">
        <div class="relative flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input
                type="text"
                x-model="searchQuery"
                placeholder="Masukkan nomor pesanan"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow"
            >
        </div>
        <button
            @click="searchOrder"
            class="px-6 py-3 bg-blue-900 text-white rounded-xl font-semibold hover:bg-blue-800 transition-colors flex items-center gap-2"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            Cari
        </button>
    </div>

    {{-- Result Card --}}
    <div x-show="searched" x-cloak x-transition:enter.duration.300>
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

        {{-- Timeline --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Riwayat Status</h3>

            <div class="relative">
                <template x-for="(stage, i) in stages" :key="i">
                    <div class="flex gap-5 pb-8 relative last:pb-0">
                        {{-- Connecting Line --}}
                        <div
                            x-show="i < stages.length - 1"
                            :class="stage.done ? 'bg-green-500' : 'bg-gray-200'"
                            class="absolute left-[15px] top-10 w-0.5 bottom-0 -z-10 transition-colors"
                        ></div>

                        {{-- Circle Indicator --}}
                        <div class="flex flex-col items-center shrink-0">
                            <div
                                :class="stage.done ? 'bg-green-500' : stage.active ? 'bg-blue-900 ring-4 ring-blue-100' : 'bg-gray-200'"
                                class="w-[30px] h-[30px] rounded-full flex items-center justify-center transition-all duration-300"
                            >
                                {{-- Checkmark for done --}}
                                <svg x-show="stage.done" class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                {{-- Pulse dot for active --}}
                                <span x-show="stage.active && !stage.done" class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-900"></span>
                                </span>
                                {{-- Number for pending --}}
                                <span x-show="!stage.done && !stage.active" class="text-xs font-bold text-white" x-text="i + 1"></span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0 pt-1">
                            <p
                                :class="stage.active ? 'text-blue-900 font-bold' : stage.done ? 'text-gray-900 font-semibold' : 'text-gray-400'"
                                class="transition-colors"
                                x-text="stage.label"
                            ></p>
                            <p x-show="stage.done" class="text-sm text-gray-500 mt-0.5">Selesai</p>
                            <p x-show="stage.active" class="text-sm text-blue-600 font-medium mt-0.5">Sedang berjalan</p>
                            <p x-show="!stage.done && !stage.active" class="text-sm text-gray-400 mt-0.5">Belum</p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ACC Desain Section --}}
        <div x-show="showAcc" class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Desain</h3>
            <p class="text-sm text-gray-500 mb-4">Desain jersey Anda sudah selesai. Silakan periksa dan berikan persetujuan atau ajukan revisi.</p>

            {{-- Design Preview --}}
            <div class="grid sm:grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 flex items-center justify-center min-h-[200px]">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-300 mb-2"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/></svg>
                        <p class="text-sm text-gray-400">Tampak Depan</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 flex items-center justify-center min-h-[200px]">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-300 mb-2"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/></svg>
                        <p class="text-sm text-gray-400">Tampak Belakang</p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <button
                    @click="accDesign"
                    class="flex-1 px-6 py-3 bg-blue-900 text-white rounded-xl font-semibold hover:bg-blue-800 transition-colors flex items-center justify-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    ACC Desain
                </button>
                <button
                    @click="requestRevision"
                    class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors flex items-center justify-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    Minta Revisi
                </button>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function trackingForm() {
    return {
        searched: false,
        searchQuery: 'NVS-20240601-001',
        order: {
            id: 'NVS-20240601-001',
            date: '1 Juni 2024',
            status: 'di_design'
        },

        get statusLabel() {
            const labels = {
                'pending': 'Pending',
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
                'pending': 'bg-yellow-100 text-yellow-800',
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
                { key: 'pending', label: 'Pesanan Masuk' },
                { key: 'dikonfirmasi', label: 'Pembayaran' },
                { key: 'disetujui', label: 'Verifikasi Admin' },
                { key: 'di_design', label: 'Tahap Desain' },
                { key: 'siap_cetak', label: 'Menunggu ACC Customer' },
                { key: 'diproduksi', label: 'Produksi & Selesai' }
            ];

            const statusOrder = ['pending', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'];
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

        searchOrder() {
            this.searched = true;
        },

        accDesign() {
            Swal.fire({
                icon: 'success',
                title: 'Desain Disetujui!',
                text: 'Desain telah Anda setujui. Pesanan akan dilanjutkan ke produksi.',
                confirmButtonColor: '#1e3a5f',
                confirmButtonText: 'OK'
            });
        },

        requestRevision() {
            Swal.fire({
                title: 'Minta Revisi',
                text: 'Tuliskan catatan revisi untuk tim desain',
                input: 'textarea',
                inputPlaceholder: 'Contoh: warna utama diganti biru, logo diperbesar...',
                showCancelButton: true,
                confirmButtonColor: '#1e3a5f',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Kirim',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) return 'Catatan revisi harus diisi';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Revisi Dikirim!',
                        text: 'Catatan revisi Anda telah dikirim ke tim desain.',
                        confirmButtonColor: '#1e3a5f',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    }
}
</script>
@endsection
