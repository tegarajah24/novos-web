@extends('layouts.customer')

@section('title', 'Notifikasi — Novos')

@section('content')
<div x-data="notifPage()" x-init="init()" class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
        <button @click="markAllRead()" class="text-sm text-blue-600 hover:underline">Tandai semua dibaca</button>
    </div>

    @if($notifications->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0018 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-gray-500">Belum ada notifikasi</p>
            <p class="text-sm text-gray-400 mt-1">Notifikasi akan muncul di sini saat ada aktivitas terkait pesanan Anda</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm divide-y divide-gray-100">
            @foreach($notifications as $notif)
                <div @click="markRead({{ $notif->id }})" class="p-5 hover:bg-gray-50 transition-colors cursor-pointer {{ !$notif->is_read ? 'bg-blue-50/30' : '' }}">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0018 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900 {{ !$notif->is_read ? '' : 'text-gray-700' }}">{{ $notif->title }}</h3>
                                <span class="text-xs text-gray-400">{{ $notif->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $notif->message }}</p>
                            @if($notif->data && isset($notif->data['order_number']))
                                <a href="{{ route('tracking', ['q' => $notif->data['order_number']]) }}" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline mt-2">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 11c0-2 0-5 .001-7 0 0 2.334.667 5 .001 0 0 0 0 0 0h7.657v7.657z" />
                                    </svg>
                                    Lacak pesanan {{ $notif->data['order_number'] }}
                                </a>
                            @endif
                        </div>
                        @if(!$notif->is_read)
                            <div class="w-5 h-5 rounded-full bg-blue-600 shrink-0"></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6 flex justify-center">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
function notifPage() {
    return {
        init() {},
        async markRead(id) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                await fetch('{{ route("notifikasi.read", ":id") }}'.replace(':id', id), {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
                location.reload();
            } catch (e) {}
        },
        async markAllRead() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("notifikasi.read-all") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) location.reload();
            } catch (e) {}
        }
    }
}
</script>
@endpush
@endsection