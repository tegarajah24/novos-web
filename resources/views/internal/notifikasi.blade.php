@extends('layouts.internal')

@section('title', 'Notifikasi')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Notifikasi</h1>
@endsection

@section('internal-content')
<div x-data="notifPage()" x-init="init()" class="flex flex-col gap-4">

    {{-- Header Card --}}
    <div class="bg-white shadow-sm rounded-2xl px-6 py-4 flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">
                Anda memiliki <strong x-text="unreadCount" class="text-red-500"></strong> notifikasi yang belum dibaca.
            </p>
        </div>
        <button @click="markAllRead()"
                class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#1a237e] border border-[#1a237e]/30 rounded-xl hover:bg-[#1a237e]/5 transition-colors">
            <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
            Tandai Semua Dibaca
        </button>
    </div>

    {{-- Main Panel --}}
    <div class="bg-white shadow-sm rounded-2xl overflow-hidden">

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
        </div>

        {{-- Notification List --}}
        <div class="divide-y divide-gray-50">
            <template x-for="notif in filteredNotifications" :key="notif.id">
                <div @click="markRead(notif.id)"
                     :class="notif.read ? '' : 'bg-blue-50/40'"
                     class="px-6 py-4 hover:bg-gray-50 transition-colors cursor-pointer group">
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
                                <div>
                                    <p class="text-sm text-gray-800 leading-relaxed" x-html="notif.message"></p>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="notif.datetime"></p>
                                </div>
                            </div>

                            {{-- Badge & Role --}}
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-full"
                                      :class="notif.badgeClass" x-text="notif.badge"></span>
                                <span class="text-[11px] text-gray-400">·</span>
                                <span class="text-[11px] text-gray-500" x-text="notif.role"></span>
                            </div>

                            {{-- Tombol lihat detail --}}
                            <div x-show="notif.order_url" class="mt-2">
                                <a :href="notif.order_url"
                                   class="inline-flex items-center gap-1 text-xs font-medium text-[#1a237e] hover:underline">
                                    Lihat Detail Pesanan
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </a>
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
        <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                Menampilkan <strong x-text="filteredNotifications.length"></strong> notifikasi
            </p>
        </div>
    </div>
</div>

<script>
function notifPage() {
    return {
        activeTab: 'all',
        notifications: [],
        unreadCount: 0,
        tabs: [
            { key: 'all', label: 'Semua', count: 0 },
            { key: 'unread', label: 'Belum Dibaca', count: 0 },
        ],

        get filteredNotifications() {
            if (this.activeTab === 'unread') {
                return this.notifications.filter(n => !n.read);
            }
            return this.notifications;
        },

        init() {
            this.loadNotifications();
        },

        async loadNotifications() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("staf.notifikasi.data") }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                });
                const data = await res.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
                this.updateTabCounts();
                this.$nextTick(() => {
                    if (window.lucide) lucide.createIcons();
                });
            } catch (e) {}
        },

        async markRead(id) {
            const n = this.notifications.find(n => n.id === id);
            if (!n || n.read) return;
            n.read = true;
            this.unreadCount = Math.max(0, this.unreadCount - 1);
            this.updateTabCounts();

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                await fetch('{{ route("staf.notifikasi.read", ":id") }}'.replace(':id', id), {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
            } catch (e) {}
        },

        async markAllRead() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("staf.notifikasi.read-all") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.notifications.forEach(n => n.read = true);
                    this.unreadCount = 0;
                    this.updateTabCounts();
                }
            } catch (e) {}
        },

        updateTabCounts() {
            this.tabs[0].count = 0;
            this.tabs[1].count = this.notifications.filter(n => !n.read).length;
        }
    }
}
</script>
@endsection
