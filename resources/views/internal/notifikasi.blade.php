@extends('layouts.internal')

@section('title', 'Notifikasi')

@section('topbar-left')
    <h1 class="text-xl font-bold text-gray-900">Notifikasi</h1>
@endsection

@section('internal-content')
<div x-data="notifPage()" class="flex flex-col gap-4">

    {{-- Header Card --}}
    <div class="glass-card rounded-2xl px-6 py-4 flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">
                Anda memiliki <strong x-text="notifications.filter(n => !n.read).length" class="text-red-500"></strong> notifikasi yang belum dibaca.
            </p>
        </div>
        <button @click="markAllRead()"
                class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#1a237e] border border-[#1a237e]/30 rounded-xl hover:bg-[#1a237e]/5 transition-colors">
            <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
            Tandai Semua Dibaca
        </button>
    </div>

    {{-- Main Panel --}}
    <div class="glass-card rounded-2xl overflow-hidden">

        {{-- Tab Navigation --}}
        <div class="flex items-center justify-between px-6 pt-4 pb-0 border-b border-gray-100">
            <div class="flex items-center gap-0">
                <template x-for="tab in tabs" :key="tab.key">
                    <button @click="activeTab = tab.key"
                            :class="activeTab === tab.key
                                ? 'text-[#1a237e] border-b-2 border-[#1a237e] font-semibold'
                                : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent'"
                            class="text-sm px-4 pb-3 transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <span x-text="tab.label"></span>
                        <span x-show="tab.count > 0"
                              x-text="tab.count"
                              class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold text-white bg-red-500 rounded-full min-w-[18px]">
                        </span>
                    </button>
                </template>
            </div>
            {{-- Filter icon --}}
            <button class="mb-3 p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Notification List --}}
        <div class="divide-y divide-gray-50">
            <template x-for="notif in filteredNotifications" :key="notif.id">
                <div @click="markRead(notif.id)"
                     :class="notif.read ? '' : 'bg-blue-50/40'"
                     class="px-6 py-4 hover:bg-white/60 transition-colors cursor-pointer group">
                    <div class="flex items-start gap-4">

                        {{-- Avatar --}}
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm"
                                 :style="`background: linear-gradient(135deg, ${notif.color}, ${notif.color}cc)`"
                                 x-text="notif.initials">
                            </div>
                            <span x-show="!notif.read"
                                  class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-red-500 border-2 border-white rounded-full shadow">
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm text-gray-800 leading-relaxed" x-html="notif.message"></p>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-xs text-gray-400 whitespace-nowrap" x-text="notif.time"></span>
                                    <button @click.stop="dismiss(notif.id)"
                                            class="opacity-0 group-hover:opacity-100 text-gray-300 hover:text-gray-500 transition-all">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Badge & Role --}}
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-full"
                                      :class="notif.badgeClass" x-text="notif.badge"></span>
                                <span class="text-[11px] text-gray-400">·</span>
                                <div class="flex items-center gap-1">
                                    <div class="w-3.5 h-3.5 rounded-full flex items-center justify-center text-white text-[8px] font-bold"
                                         :style="`background: ${notif.roleColor}`"
                                         x-text="notif.roleInitial">
                                    </div>
                                    <span class="text-[11px] text-gray-500" x-text="notif.role"></span>
                                </div>
                            </div>

                            {{-- Type: Approval — tombol aksi cepat --}}
                            <div x-show="notif.type === 'approval'" class="flex items-center gap-2 mt-3">
                                <button @click.stop
                                        class="px-4 py-1.5 text-xs font-medium border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                                    Lihat Detail
                                </button>
                                <a :href="'{{ url('/staf/daftar-pesanan') }}'"
                                   @click.stop
                                   class="px-4 py-1.5 text-xs font-medium bg-[#1a237e] text-white rounded-lg hover:bg-[#1a237e]/90 transition-colors">
                                    Teruskan ke Design
                                </a>
                            </div>

                            {{-- Type: New order — tombol konfirmasi --}}
                            <div x-show="notif.type === 'new_order'" class="flex items-center gap-2 mt-3">
                                <button @click.stop
                                        class="px-4 py-1.5 text-xs font-medium border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                                    Lihat Detail
                                </button>
                                <button @click.stop
                                        class="px-4 py-1.5 text-xs font-medium bg-[#1a237e] text-white rounded-lg hover:bg-[#1a237e]/90 transition-colors">
                                    Konfirmasi Pesanan
                                </button>
                            </div>

                            {{-- Type: Attachment —  file desain --}}
                            <div x-show="notif.type === 'attachment'"
                                 class="mt-3 inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 bg-white/80 shadow-sm hover:shadow-md transition-shadow cursor-default">
                                <svg class="w-4 h-4 text-purple-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.583 6.583a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <span class="text-xs font-medium text-gray-700" x-text="notif.fileName"></span>
                                <span class="text-xs text-gray-400" x-text="notif.fileSize"></span>
                            </div>

                            {{-- Type: Comment / Chat --}}
                            <div x-show="notif.type === 'comment'"
                                 class="mt-3 px-3 py-2.5 rounded-xl border border-gray-100 bg-white/70 shadow-sm">
                                <p class="text-xs text-gray-600 italic leading-relaxed" x-text="notif.snippet"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Empty State --}}
            <div x-show="filteredNotifications.length === 0" class="px-6 py-16 text-center">
                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="bell-off" class="w-7 h-7 text-gray-400"></i>
                </div>
                <p class="text-sm font-semibold text-gray-500">Tidak ada notifikasi</p>
                <p class="text-xs text-gray-400 mt-1">Semua sudah tertangani dengan baik!</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-3 border-t border-gray-100 bg-white/40 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                Menampilkan <strong x-text="filteredNotifications.length"></strong> dari <strong x-text="notifications.length"></strong> notifikasi
            </p>
            <button @click="showSettings = true"
                    class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-700 font-medium transition-colors">
                <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                Kelola Notifikasi
            </button>
        </div>
    </div>

    {{-- ========== MODAL KELOLA NOTIFIKASI ========== --}}
    <div x-show="showSettings"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.35); backdrop-filter: blur(4px);">

        <div x-show="showSettings"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             @click.away="showSettings = false"
             class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-[#1a237e]/10 rounded-xl flex items-center justify-center">
                        <i data-lucide="bell-ring" class="w-4 h-4 text-[#1a237e]"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Kelola Notifikasi</h2>
                        <p class="text-xs text-gray-400">Atur preferensi notifikasi yang ingin Anda terima</p>
                    </div>
                </div>
                <button @click="showSettings = false"
                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-6 py-4 max-h-[60vh] overflow-y-auto">

                {{-- Section: Pesanan --}}
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Pesanan</p>
                <div class="space-y-3 mb-5">
                    <template x-for="pref in settings.filter(s => s.group === 'pesanan')" :key="pref.key">
                        <div class="flex items-center justify-between py-2.5 px-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                                     :style="`background: ${pref.color}18`">
                                    <i :data-lucide="pref.icon" class="w-3.5 h-3.5" :style="`color: ${pref.color}`"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800" x-text="pref.label"></p>
                                    <p class="text-xs text-gray-400" x-text="pref.desc"></p>
                                </div>
                            </div>
                            {{-- Toggle --}}
                            <button @click="pref.enabled = !pref.enabled; saveSettings()"
                                    :class="pref.enabled ? 'bg-[#1a237e]' : 'bg-gray-200'"
                                    class="relative inline-flex h-5 w-9 shrink-0 rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="pref.enabled ? 'translate-x-4' : 'translate-x-0.5'"
                                      class="inline-block h-4 w-4 mt-0.5 rounded-full bg-white shadow-sm transition-transform duration-200 ease-in-out"></span>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Section: Produksi --}}
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Produksi</p>
                <div class="space-y-3 mb-5">
                    <template x-for="pref in settings.filter(s => s.group === 'produksi')" :key="pref.key">
                        <div class="flex items-center justify-between py-2.5 px-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                                     :style="`background: ${pref.color}18`">
                                    <i :data-lucide="pref.icon" class="w-3.5 h-3.5" :style="`color: ${pref.color}`"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800" x-text="pref.label"></p>
                                    <p class="text-xs text-gray-400" x-text="pref.desc"></p>
                                </div>
                            </div>
                            <button @click="pref.enabled = !pref.enabled; saveSettings()"
                                    :class="pref.enabled ? 'bg-[#1a237e]' : 'bg-gray-200'"
                                    class="relative inline-flex h-5 w-9 shrink-0 rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="pref.enabled ? 'translate-x-4' : 'translate-x-0.5'"
                                      class="inline-block h-4 w-4 mt-0.5 rounded-full bg-white shadow-sm transition-transform duration-200 ease-in-out"></span>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Section: Komunikasi --}}
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Komunikasi</p>
                <div class="space-y-3">
                    <template x-for="pref in settings.filter(s => s.group === 'komunikasi')" :key="pref.key">
                        <div class="flex items-center justify-between py-2.5 px-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                                     :style="`background: ${pref.color}18`">
                                    <i :data-lucide="pref.icon" class="w-3.5 h-3.5" :style="`color: ${pref.color}`"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800" x-text="pref.label"></p>
                                    <p class="text-xs text-gray-400" x-text="pref.desc"></p>
                                </div>
                            </div>
                            <button @click="pref.enabled = !pref.enabled; saveSettings()"
                                    :class="pref.enabled ? 'bg-[#1a237e]' : 'bg-gray-200'"
                                    class="relative inline-flex h-5 w-9 shrink-0 rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="pref.enabled ? 'translate-x-4' : 'translate-x-0.5'"
                                      class="inline-block h-4 w-4 mt-0.5 rounded-full bg-white shadow-sm transition-transform duration-200 ease-in-out"></span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between px-6 py-3 border-t border-gray-100 bg-gray-50/60">
                <button @click="resetSettings()"
                        class="text-xs text-gray-500 hover:text-gray-700 font-medium transition-colors">
                    Reset ke default
                </button>
                <button @click="showSettings = false"
                        class="px-5 py-2 text-xs font-semibold bg-[#1a237e] text-white rounded-xl hover:bg-[#1a237e]/90 transition-colors">
                    Simpan & Tutup
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function notifPage() {
    const defaultSettings = [
        // --- Pesanan ---
        { key: 'pesanan_baru',    group: 'pesanan',    enabled: true,  label: 'Pesanan Baru Masuk',       desc: 'Notifikasi saat ada pesanan baru dari customer',        icon: 'shopping-bag',    color: '#1a237e' },
        { key: 'pesanan_acc',     group: 'pesanan',    enabled: true,  label: 'Customer ACC Pesanan',      desc: 'Saat customer menyetujui & meng-ACC pesanan',           icon: 'check-circle',    color: '#16a34a' },
        { key: 'pesanan_batal',   group: 'pesanan',    enabled: true,  label: 'Pesanan Dibatalkan',        desc: 'Notifikasi jika pesanan dibatalkan oleh siapapun',      icon: 'x-circle',        color: '#dc2626' },
        // --- Produksi ---
        { key: 'design_selesai',  group: 'produksi',   enabled: true,  label: 'Design Selesai (Siap Cetak)', desc: 'Tim Design menyelesaikan & upload file pola cetak',   icon: 'palette',         color: '#6b46c1' },
        { key: 'produksi_mulai',  group: 'produksi',   enabled: false, label: 'Produksi Dimulai',          desc: 'Saat status pesanan berubah ke Diproduksi',             icon: 'factory',         color: '#d97706' },
        { key: 'produksi_selesai',group: 'produksi',   enabled: true,  label: 'Produksi Selesai',          desc: 'Tim Produksi menandai pesanan sebagai selesai',         icon: 'package-check',   color: '#0284c7' },
        // --- Komunikasi ---
        { key: 'pesan_chat',      group: 'komunikasi', enabled: true,  label: 'Pesan Chat Baru',           desc: 'Customer mengirim pesan terkait pesanan aktif',         icon: 'message-circle',  color: '#0891b2' },
        { key: 'mention',         group: 'komunikasi', enabled: false, label: 'Sebutan (@mention)',         desc: 'Saat nama Anda disebut dalam catatan internal',         icon: 'at-sign',         color: '#7c3aed' },
    ];

    return {
        showSettings: false,
        activeTab: 'all',
        tabs: [
            { key: 'all',      label: 'Semua',       get count() { return 0; } },
            { key: 'unread',   label: 'Belum Dibaca', get count() { return 0; } },
            { key: 'assigned', label: 'Divisi Saya',  get count() { return 0; } },
            { key: 'archived', label: 'Diarsipkan',   get count() { return 0; } },
        ],
        notifications: [
            {
                id: 1, read: false, archived: false,
                type: 'approval',
                initials: 'BS', color: '#e53e3e',
                role: 'Customer', roleInitial: 'C', roleColor: '#e53e3e',
                message: 'Customer <strong>Budi Santoso</strong> telah melakukan <strong>ACC</strong> pesanan <strong class="text-[#1a237e]">NVS-20240620-003</strong>. Pesanan siap diteruskan ke tim Design.',
                time: '5 menit lalu',
                badge: 'Disetujui', badgeClass: 'bg-green-100 text-green-700',
                assigned: true,
            },
            {
                id: 2, read: false, archived: false,
                type: 'new_order',
                initials: 'RP', color: '#1a237e',
                role: 'Sistem', roleInitial: 'S', roleColor: '#6b7280',
                message: 'Pesanan baru masuk dari <strong>Rizky Pratama</strong> — <strong class="text-[#1a237e]">NVS-20240620-004</strong> (Kaos Tim Futsal, 22 pcs). Segera lakukan konfirmasi.',
                time: '22 menit lalu',
                badge: 'Pending', badgeClass: 'bg-yellow-100 text-yellow-700',
                assigned: true,
            },
            {
                id: 3, read: false, archived: false,
                type: 'attachment',
                initials: 'RK', color: '#6b46c1',
                role: 'Tim Design', roleInitial: 'D', roleColor: '#6b46c1',
                message: 'Tim <strong>Design</strong> (Riko) mengunggah file pola cetak untuk pesanan <strong class="text-[#1a237e]">NVS-20240620-001</strong>. Status berubah ke <strong>Siap Cetak</strong>.',
                time: '1 jam lalu',
                badge: 'Siap Cetak', badgeClass: 'bg-purple-100 text-purple-700',
                fileName: 'pola_nvs20240620_001.cdr',
                fileSize: '(18 MB)',
                assigned: false,
            },
            {
                id: 4, read: false, archived: false,
                type: 'info',
                initials: 'SR', color: '#2d7a43',
                role: 'Tim Produksi', roleInitial: 'P', roleColor: '#2d7a43',
                message: 'Tim <strong>Produksi</strong> (Sari) menandai pesanan <strong class="text-[#1a237e]">NVS-20240619-007</strong> sebagai <strong>Selesai</strong>. Pesanan siap diserahkan ke customer.',
                time: '3 jam lalu',
                badge: 'Selesai', badgeClass: 'bg-blue-100 text-blue-700',
                assigned: false,
            },
            {
                id: 5, read: true, archived: false,
                type: 'comment',
                initials: 'DA', color: '#d97706',
                role: 'Customer', roleInitial: 'C', roleColor: '#e53e3e',
                message: 'Customer <strong>Dewi Anggraini</strong> mengirim pesan baru pada pesanan <strong class="text-[#1a237e]">NVS-20240619-005</strong>.',
                time: '1 hari lalu',
                badge: 'Pesan Chat', badgeClass: 'bg-gray-100 text-gray-600',
                snippet: '"Min, tolong warna merah di bagian kerah jersey-nya dibuat lebih gelap ya, seperti contoh yang saya kirim kemarin..."',
                assigned: true,
            },
            {
                id: 6, read: true, archived: true,
                type: 'info',
                initials: 'AD', color: '#1a237e',
                role: 'Admin', roleInitial: 'A', roleColor: '#1a237e',
                message: 'Admin mengkonfirmasi pesanan <strong class="text-[#1a237e]">NVS-20240615-002</strong> milik <strong>Hendra Wijaya</strong> dan diteruskan ke tim Design.',
                time: '5 hari lalu',
                badge: 'Di Design', badgeClass: 'bg-indigo-100 text-indigo-700',
                assigned: false,
            },
        ],
        get filteredNotifications() {
            return this.notifications.filter(n => {
                if (this.activeTab === 'all')      return !n.archived;
                if (this.activeTab === 'unread')   return !n.read && !n.archived;
                if (this.activeTab === 'assigned') return n.assigned && !n.archived;
                if (this.activeTab === 'archived') return n.archived;
                return true;
            });
        },
        markRead(id) {
            const n = this.notifications.find(n => n.id === id);
            if (n) n.read = true;
        },
        markAllRead() {
            this.notifications.forEach(n => n.read = true);
        },
        dismiss(id) {
            const n = this.notifications.find(n => n.id === id);
            if (n) n.archived = true;
        },
        settings: [],
        saveSettings() {
            try {
                const saved = this.settings.map(s => ({ key: s.key, enabled: s.enabled }));
                localStorage.setItem('novos_notif_settings', JSON.stringify(saved));
            } catch(e) {}
        },
        resetSettings() {
            this.settings.forEach(s => {
                const def = defaultSettings.find(d => d.key === s.key);
                if (def) s.enabled = def.enabled;
            });
            this.saveSettings();
        },
        init() {
            // Load settings, merge with defaults
            this.settings = JSON.parse(JSON.stringify(defaultSettings));
            try {
                const saved = JSON.parse(localStorage.getItem('novos_notif_settings') || '[]');
                saved.forEach(sv => {
                    const s = this.settings.find(s => s.key === sv.key);
                    if (s) s.enabled = sv.enabled;
                });
            } catch(e) {}

            // Trigger lucide re-render after modal opens
            this.$watch('showSettings', (val) => {
                if (val) this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            });

            // Update tab counts dynamically
            this.$watch('notifications', () => {
                this.tabs[0].count = 0;
                this.tabs[1].count = this.notifications.filter(n => !n.read && !n.archived).length;
                this.tabs[2].count = this.notifications.filter(n => n.assigned && !n.read && !n.archived).length;
                this.tabs[3].count = 0;
            }, { immediate: true });
        }
    }
}
</script>
@endsection
