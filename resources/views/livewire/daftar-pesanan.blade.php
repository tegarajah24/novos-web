<div>
    @php
        $statusMap = [
            'menunggu_validasi' => ['Menunggu Verifikasi', 'bg-yellow-100 text-yellow-700'],
            'menunggu_pembayaran' => ['Menunggu Pembayaran', 'bg-orange-100 text-orange-700'],
            'tahap_desain' => ['Tahap Desain', 'bg-blue-100 text-blue-700'],
            'menunggu_acc' => ['Menunggu ACC', 'bg-orange-100 text-orange-700'],
            'tahap_produksi' => ['Produksi', 'bg-purple-100 text-purple-700'],
            'selesai' => ['Selesai', 'bg-green-100 text-green-700'],
            'dibatalkan' => ['Dibatalkan', 'bg-red-100 text-red-700'],
        ];
        $badgeStatusMap = [
            'menunggu_validasi' => 'menunggu_verifikasi',
            'menunggu_pembayaran' => 'menunggu_pembayaran',
            'dikonfirmasi' => 'menunggu_acc',
            'disetujui' => 'tahap_desain',
            'di_design' => 'tahap_desain',
            'siap_cetak' => 'tahap_produksi',
            'diproduksi' => 'tahap_produksi',
            'selesai' => 'selesai',
            'dibatalkan' => 'dibatalkan',
        ];
    @endphp

    <div class="space-y-5" wire:key="daftar-pesanan-wrapper">
        {{-- ─── SEARCH & TOOLBAR ─────────────────────────────────────────────── --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari pesanan..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
            </div>

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Filter
                    <svg class="w-3 h-3" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak x-transition class="absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-100 z-30 p-5 space-y-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1 font-medium">Status</label>
                        <select wire:model.live="filterStatus" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                            <option value="">Semua</option>
                            <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                            <option value="menunggu_pembayaran">Menunggu Pembayaran</option>
                            <option value="menunggu_acc">Menunggu ACC</option>
                            <option value="tahap_desain">Tahap Desain</option>
                            <option value="tahap_produksi">Produksi</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1 font-medium">Tanggal Dari</label>
                        <input type="date" wire:model.live="filterDateFrom" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1 font-medium">Tanggal Sampai</label>
                        <input type="date" wire:model.live="filterDateTo" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button wire:click="resetFilters" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">Reset</button>
                    </div>
                </div>
            </div>

            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('staf.laporan') }}" class="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Lihat Laporan
                </a>
            </div>
        </div>

        {{-- Active filter badges --}}
        @if($filterStatus || $filterDateFrom || $filterDateTo)
        <div class="flex items-center gap-3 flex-wrap">
            @if($filterStatus)
            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#1a237e]/5 border border-[#1a237e]/15 rounded-lg text-sm text-[#1a237e]">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                <span class="font-medium">{{ ucwords(str_replace('_', ' ', $filterStatus)) }}</span>
                <button wire:click="$set('filterStatus', '')" class="ml-1 text-[#1a237e]/50 hover:text-[#1a237e]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @endif
            @if($filterDateFrom)
            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#1a237e]/5 border border-[#1a237e]/15 rounded-lg text-sm text-[#1a237e]">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="font-medium">Dari {{ \Carbon\Carbon::parse($filterDateFrom)->format('d M Y') }}</span>
                <button wire:click="$set('filterDateFrom', '')" class="ml-1 text-[#1a237e]/50 hover:text-[#1a237e]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @endif
            @if($filterDateTo)
            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#1a237e]/5 border border-[#1a237e]/15 rounded-lg text-sm text-[#1a237e]">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="font-medium">Sampai {{ \Carbon\Carbon::parse($filterDateTo)->format('d M Y') }}</span>
                <button wire:click="$set('filterDateTo', '')" class="ml-1 text-[#1a237e]/50 hover:text-[#1a237e]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @endif
            <button wire:click="resetFilters" class="text-xs text-gray-400 hover:text-gray-600 underline">Reset semua</button>
        </div>
        @endif

        {{-- ─── TABLE ────────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-5 py-3.5 text-left font-semibold">Order ID</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Customer</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Produk</th>
                            <th class="px-5 py-3.5 text-center font-semibold">Jml</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Total</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Assignee</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Status</th>
                            <th class="px-5 py-3.5 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($this->orders as $o)
                        @php
                            $uiStatus = $badgeStatusMap[$o->status] ?? $o->status;
                            $badge = $statusMap[$uiStatus] ?? [$uiStatus, 'bg-gray-100 text-gray-700'];
                            $produk = $o->designRequest ? 'Jersey ' . $o->designRequest->team_name : 'Jersey Custom';
                            $qty = $o->orderItems->sum('qty');
                            $total = 'Rp ' . number_format(($o->total_price ?? 0), 0, ',', '.');
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors" wire:key="order-{{ $o->id }}">
                            <td class="px-5 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $o->order_number }}</td>
                            <td class="px-5 py-4 text-gray-700 whitespace-nowrap">{{ $o->user->name ?? 'Unknown' }}</td>
                            <td class="px-5 py-4 text-gray-700 whitespace-nowrap">{{ $produk }}</td>
                            <td class="px-5 py-4 text-gray-700 text-center whitespace-nowrap">{{ $qty }}</td>
                            <td class="px-5 py-4 text-gray-700 whitespace-nowrap font-medium">{{ $total }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <select wire:change="assignOrder('{{ $o->order_number }}', $event.target.value)" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-white min-w-[140px]">
                                    <option value="">Unassigned</option>
                                    @foreach($this->assignees as $a)
                                        <option value="{{ $a['id'] }}" {{ $o->assignee_id == $a['id'] ? 'selected' : '' }}>
                                            {{ $a['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge[1] }}">{{ $badge[0] }}</span>
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <a href="{{ route('staf.detail-pesanan', $o->order_number) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-[#1a237e] hover:bg-gray-100 transition-colors inline-flex" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-gray-500 font-medium">Tidak ada pesanan</p>
                                <p class="text-gray-400 text-sm mt-1">Belum ada pesanan yang masuk.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Menampilkan {{ $this->orders->firstItem() ?? 0 }} - {{ $this->orders->lastItem() ?? 0 }} dari {{ $this->orders->total() }} pesanan
            </div>
            <div class="join">
                {{ $this->orders->links() }}
            </div>
        </div>
    </div>
</div>
