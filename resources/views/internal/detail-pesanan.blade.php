@extends('layouts.internal')

@php
$noteColors = ['bg-green-500','bg-yellow-500','bg-blue-500','bg-purple-500'];
function rh($n){ return 'Rp '.number_format($n,0,',','.'); }
@endphp

@section('title', 'Detail Pesanan')

@section('topbar-left')
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-bold text-[#1a237e]">{{ $order['order_id'] }}</h1>
            <x-badge type="{{ $badgeType }}">{{ $badgeLabel }}</x-badge>
        </div>
        <p class="text-sm text-gray-500 mt-0.5">{{ $order['last_update'] }}</p>
    </div>
@endsection

@section('internal-content')
{{-- Kembali --}}
<div class="mb-5">
    <a href="{{ route('staf.daftar-pesanan') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-[#1a237e] transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Pesanan
    </a>
</div>

{{-- 2-COLUMN LAYOUT --}}
<div class="flex gap-6 items-start">

    {{-- ── KOLOM KIRI ─────────────────────────────────────────────── --}}
    <div class="flex-1 space-y-5 min-w-0">

        {{-- Info Pesanan (Stepper) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-6 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Info Pesanan
            </h3>

            {{-- Stepper --}}
            <div class="relative flex items-start">
                {{-- Connector line (behind circles) --}}
                <div class="absolute top-4 left-4 right-4 h-0.5 bg-gray-200 z-0" style="left: calc(100% / {{ count($steps) * 2 }}); right: calc(100% / {{ count($steps) * 2 }});">
                    @php $doneCount = collect($steps)->filter(fn($s)=>$s['done'])->count(); @endphp
                    <div class="h-full bg-[#1a237e] transition-all" style="width: {{ max(0, (($doneCount - 1) / (count($steps) - 1)) * 100) }}%"></div>
                </div>

                {{-- Steps --}}
                <div class="relative z-10 flex w-full justify-between">
                @foreach($steps as $idx => $step)
                <div class="flex flex-col items-center" style="width: {{ 100 / count($steps) }}%">
                    {{-- Circle --}}
                    @if($step['current'])
                    <div class="w-8 h-8 rounded-full bg-[#1a237e] border-4 border-[#1a237e]/20 flex items-center justify-center shadow-md shadow-[#1a237e]/25">
                        <div class="w-2.5 h-2.5 rounded-full bg-white"></div>
                    </div>
                    @elseif($step['done'])
                    <div class="w-8 h-8 rounded-full bg-[#1a237e] flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    @else
                    <div class="w-8 h-8 rounded-full bg-gray-100 border-2 border-gray-300 flex items-center justify-center">
                        <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                    </div>
                    @endif

                    {{-- Label + Date --}}
                    <div class="mt-3 text-center px-1">
                        <p class="text-xs font-semibold leading-tight {{ $step['done'] || $step['current'] ? 'text-gray-800' : 'text-gray-400' }}">
                            {{ $step['label'] }}
                        </p>
                        @if($step['date'])
                        <p class="text-xs text-gray-400 mt-0.5">{{ $step['date'] }}</p>
                        @else
                        <p class="text-xs text-gray-300 mt-0.5">—</p>
                        @endif
                    </div>
                </div>
                @endforeach
                </div>
            </div>
        </div>

        {{-- Info Customer --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Info Customer
                </h3>
                <div class="flex items-center gap-2">
                    {{-- Tombol Chat (sinkron ke halaman chat internal) --}}
                    <a href="{{ route('staf.chat') }}" title="Chat dengan Customer" class="flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 font-medium hover:underline">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Chat
                    </a>
                </div>
            </div>
            {{-- Grid 2 kolom: label fixed-width, value rapat di sebelahnya --}}
            <div class="space-y-2 text-sm">
                <div class="grid grid-cols-[80px_1fr] gap-2 items-center">
                    <span class="text-gray-400 text-xs font-medium">Nama</span>
                    <span class="font-medium text-gray-800">{{ $order['customer']['name'] }}</span>
                </div>
                <div class="grid grid-cols-[80px_1fr] gap-2 items-center">
                    <span class="text-gray-400 text-xs font-medium">Email</span>
                    <span class="font-medium text-[#1a237e]">{{ $order['customer']['email'] }}</span>
                </div>
                <div class="grid grid-cols-[80px_1fr] gap-2 items-center">
                    <span class="text-gray-400 text-xs font-medium">No HP</span>
                    <span class="font-medium text-gray-800">{{ $order['customer']['phone'] }}</span>
                </div>
            </div>
        </div>


        {{-- Detail Produk --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Detail Produk
            </h3>
            <div class="grid grid-cols-2 gap-x-8 gap-y-2.5 text-sm mb-4">
                <div><span class="text-gray-500 text-xs">Jenis</span><div class="font-medium text-gray-900">{{ $order['product']['type'] }}</div></div>
                <div><span class="text-gray-500 text-xs">Olahraga</span><div class="font-medium text-gray-900">{{ $order['product']['sport'] }}</div></div>
                <div><span class="text-gray-500 text-xs">Nama Tim</span><div class="font-medium text-gray-900">Jersey Custom</div></div>
            </div>
            {{-- Item Details Table --}}
            @if(!empty($order['item_details']))
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-600">Detail Item Pesanan</span>
                    <a href="{{ route('staf.pesanan.export-csv', $order['order_id']) }}"
                       class="inline-flex items-center gap-1 text-xs font-medium text-[#1a237e] hover:underline">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export CSV
                    </a>
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">No Punggung</th>
                                <th class="px-3 py-2 text-left font-semibold">Nama Punggung</th>
                                <th class="px-3 py-2 text-left font-semibold">Model Lengan</th>
                                <th class="px-3 py-2 text-left font-semibold">Size</th>
                                <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order['item_details'] as $detail)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-2 text-gray-800 font-medium">{{ $detail['no_punggung'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $detail['nama_punggung'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $detail['model_lengan'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $detail['size'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700 max-w-[200px] truncate">{{ $detail['keterangan'] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            {{-- Catatan --}}
            <div class="text-xs text-gray-500 mt-2">
                <span class="font-medium text-gray-600">Catatan / Spesifikasi:</span>
                <p class="mt-1 text-gray-500 whitespace-pre-line">{{ $order['product']['notes'] }}</p>
            </div>
        </div>

        {{-- File Desain Customer --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                File Desain Customer
            </h3>
            <div class="grid grid-cols-3 gap-4">
                @forelse($order['design_files'] as $f)
                <div class="relative group bg-gray-100 rounded-xl aspect-square border border-gray-200 hover:border-[#1a237e]/40 transition-colors overflow-hidden">
                    @if(isset($f['url']) && ($f['mime'] ?? '') && str_starts_with($f['mime'], 'image/'))
                        <img src="{{ $f['url'] }}" alt="{{ $f['name'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center gap-2">
                            <svg class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-[#1a237e]/80 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center transition-opacity gap-2 p-2">
                        <a href="{{ $f['url'] }}" target="_blank" class="text-gray-900 text-xs font-medium bg-white/90 px-3 py-1 rounded hover:bg-white inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download</span>
                        </a>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                        <span class="text-xs text-white text-center truncate block">{{ $f['name'] }}</span>
                    </div>
                </div>
                @empty
                <div class="col-span-3 py-8 text-center text-gray-400 text-sm">Belum ada file desain dari customer.</div>
                @endforelse
            </div>
        </div>

        {{-- History Catatan --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    History Catatan
                </h3>
            </div>
            <div class="space-y-3">
                @forelse($order['history_notes'] as $i => $h)
                <div class="flex gap-3">
                    <div class="mt-1.5 w-2 h-2 rounded-full {{ $noteColors[$i % count($noteColors)] }} shrink-0"></div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">{{ $h['date'] }} — <span class="font-medium text-gray-600">{{ $h['user'] }}</span></p>
                        <p class="text-sm text-gray-700">{{ $h['note'] }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-2">Belum ada catatan.</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Riwayat Status
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold">Tanggal</th>
                            <th class="px-6 py-3 text-left font-semibold">Status</th>
                            <th class="px-6 py-3 text-left font-semibold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($order['status_history'] as $sh)
                        @php
                        $st = match($sh['status']) { 'menunggu_pembayaran'=>'orange','tahap_desain'=>'blue','menunggu_acc'=>'orange','tahap_produksi'=>'purple','selesai'=>'green',default=>'gray' };
                        $sl = match($sh['status']) { 'menunggu_pembayaran'=>'Menunggu Pembayaran','tahap_desain'=>'Tahap Desain','menunggu_acc'=>'Menunggu ACC','tahap_produksi'=>'Produksi','selesai'=>'Selesai',default=>$sh['status'] };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3.5 text-gray-700">{{ $sh['date'] }}</td>
                            <td class="px-6 py-3.5"><x-badge type="{{ $st }}">{{ $sl }}</x-badge></td>
                            <td class="px-6 py-3.5 text-gray-700">{{ $sh['note'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada riwayat status.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── KOLOM KANAN ─────────────────────────────────────────────── --}}
    <div class="w-80 shrink-0 space-y-5">

        {{-- Pembayaran --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900 mb-4 text-sm">Pembayaran</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-medium text-gray-900">{{ rh($order['payment']['subtotal']) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Biaya Prioritas</span><span class="font-medium text-gray-900">{{ rh($order['payment']['biaya_prioritas']) }}</span></div>
                <div class="border-t border-gray-100 pt-2 flex justify-between"><span class="font-semibold text-gray-700">Total</span><span class="font-bold text-gray-900">{{ rh($order['payment']['total']) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Metode</span><span class="font-medium text-gray-900">{{ $order['payment']['method'] }}</span></div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Status</span>
                    @if($order['payment']['status'] === 'lunas')
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Lunas
                    </span>
                    @else
                    <span class="text-xs font-semibold text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full">Pending</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Update Status --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5" x-data="updateStatusSection()" x-init="init()">
            <h3 class="font-semibold text-gray-900 mb-4 text-sm">Update Status</h3>
            <template x-if="allowedStatuses.length > 0">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5 font-medium">Status Baru</label>
                        <select x-model="selectedStatus" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                            <option value="">-- Pilih Status --</option>
                            <template x-for="s in allowedStatuses" :key="s.value">
                                <option :value="s.value" x-text="s.label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5 font-medium">Catatan</label>
                        <textarea x-model="statusNote" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 resize-none" placeholder="Catatan update status..."></textarea>
                    </div>
                    <button @click="submitStatus()" :disabled="!selectedStatus || updating"
                        class="w-full py-2.5 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2"
                        :class="selectedStatus && !updating ? 'bg-[#1a237e] hover:bg-[#1a237e]/90 text-white cursor-pointer' : 'bg-gray-300 text-white cursor-not-allowed'">
                        <svg x-show="updating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="updating ? 'Memperbarui...' : 'Update Status'"></span>
                    </button>
                </div>
            </template>
            <template x-if="allowedStatuses.length === 0">
                <p class="text-sm text-gray-400 text-center py-4">Tidak ada perubahan status yang tersedia untuk saat ini.</p>
            </template>
        </div>

        {{-- File Hasil Desain (Dari Tim Design) --}}
        <div class="bg-white rounded-xl border border-[#1a237e]/20 shadow-sm shadow-[#1a237e]/5 p-5">
            <h3 class="font-semibold text-gray-900 mb-3 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Hasil Desain (Siap ACC)
            </h3>
            <p class="text-sm text-gray-400 text-center py-6">Belum ada file desain</p>
        </div>

    </div>{{-- end kolom kanan --}}

</div>{{-- end flex --}}
@endsection

<script>
function updateStatusSection() {
    return {
        allowedStatuses: [],
        selectedStatus: '',
        statusNote: '',
        updating: false,
        async init() {
            try {
                const res = await fetch('{{ route("staf.pesanan.allowed-statuses", $order["order_id"]) }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.allowedStatuses = data.statuses || [];
            } catch (e) {
                this.allowedStatuses = [];
            }
        },
        async submitStatus() {
            if (!this.selectedStatus || this.updating) return;

            const statusObj = this.allowedStatuses.find(s => s.value === this.selectedStatus);
            const statusLabel = statusObj ? statusObj.label : this.selectedStatus;

            const result = await Swal.fire({
                title: 'Update Status?',
                html: 'Status akan diubah menjadi <strong>' + statusLabel + '</strong>.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a237e',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Update!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            this.updating = true;
            try {
                const res = await fetch('{{ route("staf.pesanan.update-status", $order["order_id"]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: this.selectedStatus,
                        notes: this.statusNote
                    })
                });

                const data = await res.json();

                if (data.success) {
                    Notify.success(data.message, 'Status Diperbarui!');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    Notify.error(data.message || 'Terjadi kesalahan.');
                }
            } catch (e) {
                Notify.error('Terjadi kesalahan sistem.');
            } finally {
                this.updating = false;
            }
        }
    }
}
</script>
