<div class="flex flex-col gap-4">
    {{-- Header Card --}}
    <div class="bg-white shadow-sm rounded-2xl px-6 py-4 flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">
                Anda memiliki <strong class="text-red-500">{{ $unreadCount }}</strong> notifikasi yang belum dibaca.
            </p>
        </div>
        <button wire:click="markAllRead" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#1a237e] border border-[#1a237e]/30 rounded-xl hover:bg-[#1a237e]/5 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Tandai Semua Dibaca
        </button>
    </div>

    {{-- Main Panel --}}
    <div class="bg-white shadow-sm rounded-2xl overflow-hidden">
        {{-- Tab Navigation --}}
        <div class="flex items-center justify-between px-6 pt-4 pb-0 border-b border-gray-100">
            <div class="flex items-center gap-0">
                @foreach($this->tabs as $tab)
                <button wire:click="setTab('{{ $tab['key'] }}')"
                        class="text-sm px-4 pb-3 transition-colors flex items-center gap-1.5 whitespace-nowrap border-b-2
                            {{ $activeTab === $tab['key'] ? 'text-[#1a237e] border-[#1a237e] font-semibold' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">
                    <span>{{ $tab['label'] }}</span>
                    @if($tab['count'] > 0)
                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold text-white bg-red-500 rounded-full min-w-[18px]">{{ $tab['count'] }}</span>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

        {{-- Notification List --}}
        <div class="divide-y divide-gray-50">
            @forelse($this->filteredNotifications as $notif)
            <div wire:click="markRead({{ $notif['id'] }})"
                 class="px-6 py-4 hover:bg-gray-50 transition-colors cursor-pointer group {{ $notif['read'] ? '' : 'bg-blue-50/40' }}">
                <div class="flex items-start gap-4">
                    {{-- Avatar --}}
                    <div class="relative shrink-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm"
                             style="background: linear-gradient(135deg, {{ $notif['color'] }}, {{ $notif['color'] }}cc)">
                            {{ $notif['initials'] }}
                        </div>
                        @if(!$notif['read'])
                        <span class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-red-500 border-2 border-white rounded-full shadow"></span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm text-gray-800 leading-relaxed">{!! $notif['message'] !!}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $notif['datetime'] }}</p>
                            </div>
                        </div>

                        {{-- Badge & Role --}}
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $notif['badgeClass'] }}">{{ $notif['badge'] }}</span>
                            <span class="text-[11px] text-gray-400">·</span>
                            <span class="text-[11px] text-gray-500">{{ $notif['role'] }}</span>
                        </div>

                        {{-- Tombol lihat detail --}}
                        @if($notif['order_url'])
                        <div class="mt-2">
                            <a href="{{ $notif['order_url'] }}"
                               class="inline-flex items-center gap-1 text-xs font-medium text-[#1a237e] hover:underline"
                               wire:click.stop>
                                Lihat Detail Pesanan
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500">Tidak ada notifikasi</p>
                <p class="text-xs text-gray-400 mt-1">Semua sudah tertangani dengan baik!</p>
            </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                Menampilkan <strong>{{ count($this->filteredNotifications) }}</strong> notifikasi
            </p>
        </div>
    </div>
</div>
