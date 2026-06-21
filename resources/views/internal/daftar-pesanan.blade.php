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
            <input type="text" placeholder="Cari pesanan..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
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
                        <option>Semua</option>
                        <option>Menunggu Verifikasi</option>
                        <option>Tahap Desain</option>
                        <option>Menunggu ACC</option>
                        <option>Produksi</option>
                        <option>Selesai</option>
                        <option>Sedang Diproses</option>
                        <option>Selesai Hari Ini</option>
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
                <div class="flex gap-2 pt-1">
                    <button @click="open = false" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">Reset</button>
                    <button @click="open = false" class="flex-1 px-3 py-2 text-sm bg-[#1a237e] text-white rounded-lg hover:bg-[#1a237e]/90 font-medium">Terapkan</button>
                </div>
            </div>
        </div>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                Group
                <svg class="w-3 h-3" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" @click.outside="open = false" x-cloak x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-30 py-1">
                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Tidak Digroup</button>
                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Berdasarkan Assignee</button>
                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Berdasarkan Status</button>
            </div>
        </div>

        <div class="ml-auto flex items-center gap-2">
            <button class="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-400 cursor-not-allowed">
                <svg class="w-4 h-4" style="color: #EF4444" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 13H8"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 17H8"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 9H8"/></svg>
                PDF
            </button>
            <button class="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-400 cursor-not-allowed">
                <svg class="w-4 h-4" style="color: #22C55E" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 13h2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 17h2"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 11h2"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 15h2"/></svg>
                Excel
            </button>
            <button class="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-400 cursor-not-allowed">
                <svg class="w-4 h-4" style="color: #3B82F6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 13h2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 17h2"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 11h2"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 15h2"/></svg>
                CSV
            </button>
        </div>
    </div>

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
                    @foreach($orders as $o)
                    @php
                        $ac = $o['assignee'] ? collect($assignees)->firstWhere('name', $o['assignee']) : null;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors" x-data="{ editMode: false }">
                        <td class="px-5 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $o['order_id'] }}</td>
                        <td class="px-5 py-4 text-gray-700 whitespace-nowrap">{{ $o['customer'] }}</td>
                        <td class="px-5 py-4 text-gray-700 whitespace-nowrap">{{ $o['produk'] }}</td>
                        <td class="px-5 py-4 text-gray-700 text-center whitespace-nowrap">{{ $o['qty'] }}</td>
                        <td class="px-5 py-4 text-gray-700 whitespace-nowrap font-medium">{{ rupiah($o['total']) }}</td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($ac)
                            <div class="flex items-center gap-2">
                                {!! avatar($ac['name'], $ac['color']) !!}
                                <span class="text-sm text-gray-700">{{ $ac['name'] }}</span>
                            </div>
                            @else
                            <span class="text-sm text-gray-400 italic">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">{!! badge($o['status']) !!}</td>
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('staf.detail-pesanan', $o['order_id']) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-[#1a237e] hover:bg-gray-100 transition-colors" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <button @click="editMode = true" class="p-1.5 rounded-lg text-gray-400 hover:text-[#1a237e] hover:bg-gray-100 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button class="p-1.5 rounded-lg text-gray-300 cursor-not-allowed" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            {{-- EDIT MODAL --}}
                            <template x-teleport="body">
                            <div x-show="editMode" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" @keydown.escape.window="editMode = false">
                                <div class="absolute inset-0 bg-black/30"></div>
                                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6">
                                    <div class="flex items-center justify-between mb-5">
                                        <h3 class="text-lg font-bold text-gray-900">Edit Pesanan</h3>
                                        <button @click="editMode = false" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1 font-medium">Order ID</label>
                                            <input type="text" value="{{ $o['order_id'] }}" readonly class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-400 bg-gray-50">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1 font-medium">Customer</label>
                                            <input type="text" value="{{ $o['customer'] }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1 font-medium">Produk</label>
                                            <input type="text" value="{{ $o['produk'] }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs text-gray-500 mb-1 font-medium">Jumlah</label>
                                                <input type="number" value="{{ $o['qty'] }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 mb-1 font-medium">Total</label>
                                                <input type="text" value="{{ rupiah($o['total']) }}" readonly class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-400 bg-gray-50">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1 font-medium">Assignee</label>
                                            <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                                                @foreach($assignees as $a) <option {{ $o['assignee'] === $a['name'] ? 'selected' : '' }}>{{ $a['name'] }}</option> @endforeach
                                                <option {{ !$o['assignee'] ? 'selected' : '' }}>Unassigned</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1 font-medium">Status</label>
                                            <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                                                <option {{ $o['status'] === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                                <option {{ $o['status'] === 'tahap_desain' ? 'selected' : '' }}>Tahap Desain</option>
                                                <option {{ $o['status'] === 'menunggu_acc' ? 'selected' : '' }}>Menunggu ACC</option>
                                                <option {{ $o['status'] === 'tahap_produksi' ? 'selected' : '' }}>Produksi</option>
                                                <option {{ $o['status'] === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex gap-3 mt-6">
                                        <button @click="editMode = false" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 font-medium">Batal</button>
                                        <button @click="editMode = false" class="flex-1 px-4 py-2.5 bg-[#1a237e] text-white rounded-lg text-sm hover:bg-[#1a237e]/90 font-medium">Simpan</button>
                                    </div>
                                </div>
                            </div>
                            </template>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="flex items-center justify-between text-sm text-gray-500">
        <span>Menampilkan {{ count($orders) }} pesanan</span>
        <div class="flex items-center gap-1">
            <button class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs hover:bg-gray-50 disabled:opacity-40" disabled>&lt; Prev</button>
            <button class="px-3 py-1.5 bg-[#1a237e] text-white rounded-lg text-xs">1</button>
            <button class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs hover:bg-gray-50">2</button>
            <button class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs hover:bg-gray-50 disabled:opacity-40">Next &gt;</button>
        </div>
    </div>
</div>

<script>
function orderManager() {
    return {
        //
    }
}
</script>
@endsection
