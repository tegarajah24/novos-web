<div>
    <div class="max-w-4xl mx-auto px-4 py-8"
         x-data="{
             animateProgress: false,
             lightboxOpen: false,
             lightboxImage: '',
             revisionOpen: false,
             stageDefs: [
                 { key: 'menunggu_validasi', label: 'Pesanan Masuk' },
                 { key: 'menunggu_pembayaran', label: 'Menunggu Pembayaran' },
                 { key: 'dikonfirmasi', label: 'Pembayaran Dikonfirmasi' },
                 { key: 'di_design', label: 'Tahap Desain' },
                 { key: 'siap_cetak', label: 'Menunggu ACC Customer' },
                 { key: 'diproduksi', label: 'Produksi & Selesai' }
             ],
             statusOrder: ['menunggu_validasi','menunggu_pembayaran','dikonfirmasi','disetujui','di_design','siap_cetak','diproduksi','selesai'],
             get currentIdx() { return this.statusOrder.indexOf($wire?.order?.status || 'menunggu_validasi'); },
             stageActive(i) { return this.statusOrder.indexOf(this.stageDefs[i].key) === this.currentIdx; },
             stageDone(i) { return this.statusOrder.indexOf(this.stageDefs[i].key) < this.currentIdx; },
             get progressPercent() { const done = this.stageDefs.filter((_,i) => this.stageDone(i)).length; return this.stageDefs.length > 1 ? (done / (this.stageDefs.length - 1)) * 100 : 0; },
             get showAcc() { return ['di_design','siap_cetak'].includes($wire?.order?.status || ''); },
             triggerProgress() { this.$nextTick(() => { setTimeout(() => { this.animateProgress = true; }, 200); }); },
             openLightbox(img) { this.lightboxImage = img; this.lightboxOpen = true; document.body.style.overflow = 'hidden'; },
             closeLightbox() { this.lightboxOpen = false; this.lightboxImage = ''; document.body.style.overflow = ''; },
             copyShareLink(url) {
                 if (!url) { $wire.generateShareToken(); return; }
                 navigator.clipboard.writeText(url).then(() => { Swal.fire({ icon:'success', title:'Link Disalin!', text:'Link tracking berhasil disalin. Bagikan ke grup WhatsApp tim Anda!', confirmButtonColor:'#1a237e' }); })
                 .catch(() => { Swal.fire({ icon:'warning', title:'Gagal Menyalin', text:'Salin manual: '+url, confirmButtonColor:'#1a237e' }); });
             }
         }"
         x-init="if ($wire.state === 'result') triggerProgress()"
         @revision-sent.window="revisionOpen = false"
         @share-url-ready.window="copyShareLink($event.detail.url)">

        <style>
            [x-cloak] { display: none !important; }
            .stepper-scroll { overflow-x: auto; }
            .stepper-scroll::-webkit-scrollbar { height: 4px; }
            .stepper-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
            .stepper-scroll::-webkit-scrollbar-thumb { background: #c4c4c4; border-radius: 10px; }
            @keyframes glow-pulse { 0%,100% { box-shadow: 0 0 8px rgba(37,99,235,0.3); } 50% { box-shadow: 0 0 20px rgba(37,99,235,0.6); } }
            .animate-glow { animation: glow-pulse 2s ease-in-out infinite; }
        </style>

        {{-- HEADER --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Tracking Pesanan</h1>
            <p class="text-gray-500 mt-1" x-show="$wire.state === 'empty'">Cari pesanan Anda berdasarkan nomor pesanan</p>
        </div>

        {{-- STATE: EMPTY --}}
        <div x-show="$wire.state === 'empty'" x-cloak x-transition:enter.duration.200 class="text-center py-10">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-300 mb-5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            <p class="text-gray-500 font-medium mb-1">Cek Status Pesanan</p>
            <p class="text-gray-400 text-sm mb-6">Masukkan nomor pesanan untuk melihat status terkini</p>
            <div class="max-w-md mx-auto flex gap-3">
                <input type="text" wire:model="searchQuery" wire:keydown.enter="search" placeholder="Contoh: NVS-20240601-001" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow">
                <button wire:click="search" wire:loading.attr="disabled" class="px-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors shrink-0 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Lacak
                </button>
            </div>
            <div class="mt-6 border-t border-gray-100 pt-6">
                <p class="text-xs text-gray-400 mb-3">Atau lihat dari</p>
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-5 py-2 border-2 border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:border-gray-300 hover:text-gray-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Riwayat Pesanan
                </a>
            </div>
        </div>

        {{-- STATE: LOADING --}}
        <div wire:loading wire:target="search" class="flex flex-col items-center justify-center py-20">
            <svg class="animate-spin w-10 h-10 text-[#1a237e] mb-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <p class="text-gray-500 text-sm">Mencari pesanan...</p>
        </div>

        {{-- STATE: ERROR --}}
        <div x-show="$wire.state === 'error'" x-cloak x-transition:enter.duration.200 class="text-center py-10">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-red-300 mb-4"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p class="text-gray-600 font-medium mb-1" x-text="$wire.errorMessage"></p>
            <p class="text-gray-400 text-sm mb-6">Periksa kembali nomor pesanan Anda</p>
            <button wire:click="$set('state', 'empty')" class="px-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors">Coba Lagi</button>
        </div>

        {{-- STATE: RESULT --}}
        <div x-show="$wire.state === 'result'" x-cloak x-transition:enter.duration.300>

            {{-- HEADER INFO --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div><p class="text-sm text-gray-500 mb-1">Nomor Pesanan</p><h2 class="text-xl font-bold text-gray-900 tracking-wider" x-text="$wire.order.id"></h2></div>
                    <div class="text-right sm:text-left"><p class="text-sm text-gray-500 mb-1">Tanggal Order</p><p class="font-semibold text-gray-900" x-text="$wire.order.date"></p></div>
                    <div>
                        <span class="inline-block px-4 py-1.5 rounded-full text-sm font-semibold"
                              :class="{
                                  'bg-yellow-100 text-yellow-800': $wire.order.status === 'menunggu_validasi',
                                  'bg-orange-100 text-orange-800': $wire.order.status === 'menunggu_pembayaran',
                                  'bg-blue-100 text-blue-800': $wire.order.status === 'dikonfirmasi',
                                  'bg-green-100 text-green-800': ['disetujui','selesai'].includes($wire.order.status),
                                  'bg-purple-100 text-purple-800': $wire.order.status === 'di_design',
                                  'bg-indigo-100 text-indigo-800': $wire.order.status === 'siap_cetak',
                                  'bg-orange-100 text-orange-800': $wire.order.status === 'diproduksi',
                                  'bg-red-100 text-red-800': $wire.order.status === 'dibatalkan'
                              }"
                              x-text="{
                                  menunggu_validasi:'Menunggu Validasi', menunggu_pembayaran:'Menunggu Pembayaran',
                                  dikonfirmasi:'Dikonfirmasi', disetujui:'Disetujui', di_design:'Di Design',
                                  siap_cetak:'Siap Cetak', diproduksi:'Diproduksi', selesai:'Selesai', dibatalkan:'Dibatalkan'
                              }[$wire.order.status] || $wire.order.status"></span>
                    </div>
                    <template x-if="!$wire.shared && $wire.order.id">
                        <button @click="copyShareLink($wire.shareUrl)" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all text-gray-600 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg> Bagikan
                        </button>
                    </template>
                </div>
            </div>

            {{-- SHARED BANNER --}}
            <template x-if="$wire.shared">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-500 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    <p class="text-sm text-blue-700">🔗 Tracking dibagikan oleh <strong x-text="$wire.order.team_name || 'Customer'"></strong></p>
                </div>
            </template>

            {{-- STEPPER --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 md:p-8 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-8">Riwayat Status</h3>
                <div class="overflow-x-auto pb-2 stepper-scroll">
                    <div class="relative flex items-start justify-between min-w-[640px] md:min-w-0 px-1 pt-6">
                        <div class="absolute top-[43px] left-[4%] right-[4%] h-1 bg-gray-200 rounded-full"></div>
                        <div class="absolute top-[43px] left-[4%] h-1 bg-gradient-to-r from-green-400 to-green-500 rounded-full transition-all duration-1000 ease-out"
                             :style="`width: ${animateProgress ? progressPercent : 0}%`"></div>
                        <template x-for="(stage, i) in stageDefs" :key="i">
                            <div class="flex flex-col items-center text-center relative z-10" style="width: 16.666%">
                                <div class="relative">
                                    <span class="absolute -inset-1.5 flex" x-show="stageActive(i) && !stageDone(i)">
                                        <span class="animate-ping absolute inset-0 rounded-full bg-blue-400/40"></span>
                                    </span>
                                    <div :class="stageDone(i) ? 'bg-green-500 shadow-lg shadow-green-200' : stageActive(i) ? 'bg-[#1a237e] ring-4 ring-[#1a237e]/20 shadow-lg shadow-blue-200 animate-glow' : 'bg-gray-200'"
                                         class="w-[38px] h-[38px] rounded-full flex items-center justify-center transition-all duration-500 relative">
                                        <svg x-show="stageDone(i)" class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span x-show="stageActive(i) && !stageDone(i)" class="w-3 h-3 bg-white rounded-full"></span>
                                        <span x-show="!stageDone(i) && !stageActive(i)" class="text-xs font-bold" :class="'text-gray-400'" x-text="i + 1"></span>
                                    </div>
                                </div>
                                <div class="mt-3 max-w-[90px]">
                                    <p :class="stageActive(i) ? 'text-[#1a237e] font-bold' : stageDone(i) ? 'text-gray-900 font-semibold' : 'text-gray-400'"
                                       class="text-[11px] md:text-xs leading-tight transition-colors" x-text="stage.label"></p>
                                    <p class="text-[10px] mt-1 font-medium"
                                       :class="stageDone(i) ? 'text-green-600' : stageActive(i) ? 'text-blue-600' : 'text-gray-300'"
                                       x-text="stageDone(i) ? 'Selesai' : stageActive(i) ? 'Berjalan' : 'Belum'"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ACC / REVISI --}}
            <div x-show="showAcc && !$wire.shared" x-cloak x-transition:enter.duration.300>
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="grid md:grid-cols-[70%_30%]">
                        <div class="p-6 md:p-8 border-b md:border-b-0 md:border-r border-gray-100">
                            <template x-if="$wire.order.design_files && $wire.order.design_files.length > 0">
                                <div class="grid sm:grid-cols-2 gap-4 h-full">
                                    <template x-for="(file, idx) in $wire.order.design_files" :key="idx">
                                        <div class="relative group cursor-zoom-in rounded-xl overflow-hidden bg-gray-50 border border-gray-200 min-h-[260px]"
                                             @click="openLightbox(file.url)">
                                            <img :src="file.url" :alt="file.name" class="w-full h-full object-cover absolute inset-0 transition-transform duration-500 group-hover:scale-105" draggable="false">
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300 flex items-center justify-center">
                                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center"><svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div>
                                            </div>
                                            <span class="absolute top-3 left-3 px-2 py-0.5 bg-white/90 text-[10px] font-semibold text-gray-600 rounded-md">Mockup Design</span>
                                            <span class="absolute top-3 right-3 px-2 py-0.5 bg-black/50 text-[10px] font-semibold text-white rounded-md truncate max-w-[120px]" x-text="file.name"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!$wire.order.design_files || $wire.order.design_files.length === 0">
                                <div class="h-full min-h-[260px] flex flex-col items-center justify-center text-gray-400 p-8">
                                    <svg class="w-12 h-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                    <p class="text-sm font-medium">Belum ada file desain</p>
                                    <p class="text-xs mt-1">Tim design akan mengunggah mockup di sini</p>
                                </div>
                            </template>
                        </div>
                        <div class="p-6 md:p-8 flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Konfirmasi Desain</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Desain jersey Anda sudah selesai. Silakan periksa detailnya dan berikan persetujuan atau ajukan revisi jika ada yang perlu diperbaiki.</p>
                            </div>
                            <div class="mt-6 space-y-3">
                                <button wire:click="accDesign" wire:loading.attr="disabled"
                                    class="relative overflow-hidden w-full px-6 py-3 bg-[#1a237e] text-white rounded-xl font-semibold hover:bg-[#283593] transition-colors flex items-center justify-center gap-2 group/btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> ACC Desain
                                </button>
                                <button @click="revisionOpen = !revisionOpen" class="w-full px-6 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors flex items-center justify-center gap-2">
                                    <svg :class="revisionOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg> Minta Revisi
                                </button>
                                <div x-show="revisionOpen" x-cloak x-collapse.duration.300 class="space-y-3 pt-1">
                                    <textarea wire:model="revisionNote" placeholder="Jelaskan bagian mana yang perlu direvisi" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow resize-none" rows="3"></textarea>
                                    <div class="flex gap-2">
                                        <button wire:click="sendRevision" wire:loading.attr="disabled" class="flex-1 px-4 py-2.5 bg-[#1a237e] text-white text-sm rounded-xl font-semibold hover:bg-[#283593] transition-colors">Kirim Revisi</button>
                                        <button @click="revisionOpen = false; $wire.set('revisionNote', '')" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-600 text-sm rounded-xl font-semibold hover:bg-gray-50 transition-colors">Batal</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LIGHTBOX --}}
            <div x-show="lightboxOpen" x-cloak x-transition.opacity.duration.300 @click="closeLightbox"
                 class="fixed inset-0 z-[999] bg-black/90 flex items-center justify-center p-4 cursor-zoom-out">
                <button @click="closeLightbox" class="absolute top-6 right-6 w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <img :src="lightboxImage" alt="Preview Desain" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl" @click.stop>
            </div>
        </div>
    </div>

    {{-- Notification listener for Livewire --}}
    <script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('notify', (data) => {
            Swal.fire({
                icon: data.type || 'info',
                title: data.title || '',
                text: data.message || '',
                confirmButtonColor: '#1a237e'
            });
        });
    });
    </script>
</div>
