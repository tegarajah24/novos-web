@extends('layouts.customer')

@section('title', 'Notifikasi — Novos')

@section('content')
<div x-data="notifPage()" x-init="init()" class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
        <button @click="markAllRead()" :disabled="loading || notifications.length === 0" class="text-sm text-blue-600 hover:underline disabled:text-gray-300 disabled:cursor-not-allowed">Tandai semua dibaca</button>
    </div>

    {{-- Loading state --}}
    <template x-if="loading && notifications.length === 0">
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <svg class="w-8 h-8 mx-auto text-gray-300 animate-spin mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-gray-500">Memuat notifikasi...</p>
        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="!loading && notifications.length === 0">
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0018 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-gray-500">Belum ada notifikasi</p>
            <p class="text-sm text-gray-400 mt-1">Notifikasi akan muncul di sini saat ada aktivitas terkait pesanan Anda</p>
        </div>
    </template>

    {{-- Notification list --}}
    <template x-if="notifications.length > 0">
        <div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm divide-y divide-gray-100">
                <template x-for="notif in notifications" :key="notif.id">
                    <div @click="markRead(notif.id)" class="p-5 hover:bg-gray-50 transition-colors cursor-pointer" :class="!notif.is_read ? 'bg-blue-50/30' : ''">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0018 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-900" :class="!notif.is_read ? '' : 'text-gray-700'" x-text="notif.title"></h3>
                                    <span class="text-xs text-gray-400" x-text="formatDate(notif.created_at)"></span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1" x-text="notif.message"></p>
                                <template x-if="notif.data && notif.data.order_number">
                                    <a :href="'/tracking?q=' + notif.data.order_number" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline mt-2" @click.stop>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 11c0-2 0-5 .001-7 0 0 2.334.667 5 .001 0 0 0 0 0 0h7.657v7.657z" />
                                        </svg>
                                        <span x-text="'Lacak pesanan ' + notif.data.order_number"></span>
                                    </a>
                                </template>
                            </div>
                            <template x-if="!notif.is_read">
                                <div class="w-5 h-5 rounded-full bg-blue-600 shrink-0"></div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Loading overlay for page switch --}}
            <div x-show="loading && notifications.length > 0" class="flex justify-center py-4">
                <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>

            {{-- Pagination --}}
            <div class="mt-6 flex justify-center items-center gap-2" x-show="lastPage > 1">
                <button @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1" class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    &laquo;
                </button>
                <template x-for="p in pageRange" :key="p">
                    <button @click="goToPage(p)" 
                            class="px-3 py-1.5 text-sm rounded-lg border transition-colors"
                            :class="p === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-100'"
                            x-text="p">
                    </button>
                </template>
                <button @click="goToPage(currentPage + 1)" :disabled="currentPage >= lastPage" class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    &raquo;
                </button>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
function notifPage() {
    return {
        notifications: [],
        currentPage: 1,
        lastPage: 1,
        total: 0,
        loading: false,

        init() {
            this.fetchPage(1);
        },

        async fetchPage(page) {
            this.loading = true;
            try {
                const res = await fetch('{{ route("notifikasi.json") }}?page=' + page, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const json = await res.json();
                this.notifications = json.data;
                this.currentPage = json.current_page;
                this.lastPage = json.last_page;
                this.total = json.total;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (e) {} finally {
                this.loading = false;
            }
        },

        goToPage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage) return;
            this.fetchPage(page);
        },

        get pageRange() {
            const range = [];
            const maxVisible = 5;
            let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
            let end = Math.min(this.lastPage, start + maxVisible - 1);
            if (end - start + 1 < maxVisible) {
                start = Math.max(1, end - maxVisible + 1);
            }
            for (let i = start; i <= end; i++) range.push(i);
            return range;
        },

        async markRead(id) {
            const notif = this.notifications.find(n => n.id === id);
            if (!notif || notif.is_read) return;
            notif.is_read = true;
            Alpine.store('summary').notifUnread = Math.max(0, (Alpine.store('summary').notifUnread || 1) - 1);
            try {
                await fetch('{{ route("notifikasi.read", ":id") }}'.replace(':id', id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
            } catch (e) {}
        },

        async markAllRead() {
            try {
                const res = await fetch('{{ route("notifikasi.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    this.notifications.forEach(n => n.is_read = true);
                    Alpine.store('summary').notifUnread = 0;
                }
            } catch (e) {}
        },

        formatDate(dateString) {
            const d = new Date(dateString);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    }
}
</script>
@endpush
@endsection
