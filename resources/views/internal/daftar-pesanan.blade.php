@extends('layouts.internal')

@section('title', 'Daftar Pesanan')

@section('topbar-left')
    <h1 class="text-xl font-bold text-black">Daftar Pesanan</h1>
@endsection

@section('internal-content')
@php
function avatar($name, $color) {
    $initials = collect(explode(' ', $name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode('');
    $colors = ['purple' => 'bg-purple-500', 'blue' => 'bg-blue-500', 'orange' => 'bg-orange-500', 'green' => 'bg-green-500', 'gray' => 'bg-gray-400'];
    $c = $colors[$color] ?? 'bg-gray-400';
    return '<span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white text-xs font-semibold '.$c.' shrink-0">'.$initials.'</span>';
}

function badge($status) {
    $map = [
        'menunggu_verifikasi' => ['Menunggu Verifikasi', 'bg-yellow-100 text-yellow-700'],
        'menunggu_pembayaran' => ['Menunggu Pembayaran', 'bg-orange-100 text-orange-700'],
        'tahap_desain' => ['Tahap Desain', 'bg-blue-100 text-blue-700'],
        'menunggu_acc' => ['Menunggu ACC', 'bg-orange-100 text-orange-700'],
        'tahap_produksi' => ['Produksi', 'bg-purple-100 text-purple-700'],
        'selesai' => ['Selesai', 'bg-green-100 text-green-700'],
    ];
    $m = $map[$status] ?? [$status, 'bg-gray-100 text-gray-700'];
    return '<span class="px-3 py-1 rounded-full text-xs font-semibold '.$m[1].'">'.$m[0].'</span>';
}

function rupiah($n) {
    return 'Rp' . number_format($n, 0, ',', '.');
}
@endphp

<div x-data="orderManager()" class="space-y-5">
    {{-- ─── SEARCH & TOOLBAR ─────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="search" placeholder="Cari pesanan..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
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
                    <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                        <option {{ !$activeFilter ? 'selected' : '' }}>Semua</option>
                        <option value="menunggu_verifikasi" {{ $activeFilter === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="tahap_desain" {{ $activeFilter === 'tahap_desain' ? 'selected' : '' }}>Tahap Desain</option>
                        <option value="menunggu_acc" {{ $activeFilter === 'menunggu_acc' ? 'selected' : '' }}>Menunggu ACC</option>
                        <option value="tahap_produksi" {{ $activeFilter === 'tahap_produksi' ? 'selected' : '' }}>Produksi</option>
                        <option value="selesai" {{ $activeFilter === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1 font-medium">Assignee</label>
                    <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                        <option>Semua</option>
                        @foreach($assignees as $a) <option>{{ $a['name'] }}</option> @endforeach
                        <option>Unassigned</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1 font-medium">Prioritas</label>
                    <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                        <option>Semua</option>
                        <option>Normal</option>
                        <option>Express</option>
                        <option>Super Express</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1 font-medium">Tanggal Dari</label>
                    <input type="date" id="filter-date-from" value="{{ $activeDateFrom ?? '' }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1 font-medium">Tanggal Sampai</label>
                    <input type="date" id="filter-date-to" value="{{ $activeDateTo ?? '' }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                </div>
                <div class="flex gap-2 pt-1">
                    <button @click="window.location.href='{{ route('staf.daftar-pesanan') }}'" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">Reset</button>
                    <button @click="
                        let params = new URLSearchParams();
                        let s = document.querySelector('#filter-date-from');
                        let e = document.querySelector('#filter-date-to');
                        if (s && s.value) params.set('date_from', s.value);
                        if (e && e.value) params.set('date_to', e.value);
                        window.location.href='{{ route('staf.daftar-pesanan') }}' + (params.toString() ? '?' + params.toString() : '');
                    " class="flex-1 px-3 py-2 text-sm bg-[#1a237e] text-white rounded-lg hover:bg-[#1a237e]/90 font-medium">Terapkan</button>
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

    @if($activeFilter || $activeDateFrom || $activeDateTo)
    <div class="flex items-center gap-3 flex-wrap">
        @if($activeFilter)
        <div class="flex items-center gap-2 px-3 py-1.5 bg-[#1a237e]/5 border border-[#1a237e]/15 rounded-lg text-sm text-[#1a237e]">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            <span class="font-medium">{{ ucwords(str_replace('_', ' ', $activeFilter)) }}</span>
            <a href="{{ route('staf.daftar-pesanan') }}{{ ($activeDateFrom || $activeDateTo) ? '?'.http_build_query(array_filter(['date_from' => $activeDateFrom, 'date_to' => $activeDateTo])) : '' }}" class="ml-1 text-[#1a237e]/50 hover:text-[#1a237e]">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>
        @endif
        @if($activeDateFrom)
        <div class="flex items-center gap-2 px-3 py-1.5 bg-[#1a237e]/5 border border-[#1a237e]/15 rounded-lg text-sm text-[#1a237e]">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="font-medium">Dari {{ \Carbon\Carbon::parse($activeDateFrom)->format('d M Y') }}</span>
            <a href="{{ route('staf.daftar-pesanan') }}?{{ http_build_query(array_filter(['status' => $activeFilter, 'date_to' => $activeDateTo])) }}" class="ml-1 text-[#1a237e]/50 hover:text-[#1a237e]">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>
        @endif
        @if($activeDateTo)
        <div class="flex items-center gap-2 px-3 py-1.5 bg-[#1a237e]/5 border border-[#1a237e]/15 rounded-lg text-sm text-[#1a237e]">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="font-medium">Sampai {{ \Carbon\Carbon::parse($activeDateTo)->format('d M Y') }}</span>
            <a href="{{ route('staf.daftar-pesanan') }}?{{ http_build_query(array_filter(['status' => $activeFilter, 'date_from' => $activeDateFrom])) }}" class="ml-1 text-[#1a237e]/50 hover:text-[#1a237e]">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>
        @endif
        @if($activeFilter || $activeDateFrom || $activeDateTo)
        <a href="{{ route('staf.daftar-pesanan') }}" class="text-xs text-gray-400 hover:text-gray-600 underline">Reset semua</a>
        @endif
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
                    @forelse($orders as $o)
                    <tr class="hover:bg-gray-50 transition-colors" x-show="search === '' || '{{ addslashes(strtolower($o['customer'])) }}'.includes(search.toLowerCase()) || '{{ addslashes(strtolower($o['order_id'])) }}'.includes(search.toLowerCase()) || '{{ addslashes(strtolower($o['produk'])) }}'.includes(search.toLowerCase())">
                        <td class="px-5 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $o['order_id'] }}</td>
                        <td class="px-5 py-4 text-gray-700 whitespace-nowrap">{{ $o['customer'] }}</td>
                        <td class="px-5 py-4 text-gray-700 whitespace-nowrap">{{ $o['produk'] }}</td>
                        <td class="px-5 py-4 text-gray-700 text-center whitespace-nowrap">{{ $o['qty'] }}</td>
                        <td class="px-5 py-4 text-gray-700 whitespace-nowrap font-medium">{{ rupiah($o['total']) }}</td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <select @change="assignOrder('{{ $o['id'] }}', $event.target.value)" class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-white">
                                <option value="">Unassigned</option>
                                @foreach($assignees as $a)
                                    <option value="{{ $a['id'] }}" {{ $o['assignee_id'] == $a['id'] ? 'selected' : '' }}>
                                        {{ $a['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">{!! badge($o['status']) !!}</td>
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('staf.detail-pesanan', $o['order_id']) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-[#1a237e] hover:bg-gray-100 transition-colors inline-flex" title="Lihat Detail">
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

    {{-- PAGINATION INFO --}}
    <div class="text-sm text-gray-500">
        <span>Menampilkan {{ count($orders) }} pesanan</span>
    </div>
</div>

<script>
function orderManager() {
    return {
        search: '',
        assignOrder(orderId, assigneeId) {
            fetch(`/staf/pesanan/${orderId}/assign`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ assignee_id: assigneeId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    Swal.fire('Error', data.message || 'Terjadi kesalahan.', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            });
        }
    }
}
</script>
@endsection
