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
@php
$spkRoles = ['mockup_depan','mockup_belakang','detail_depan','detail_belakang','sponsor'];
$dewasaSizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '6XL'];
$anakSizes   = ['KIDS_S', 'KIDS_M', 'KIDS_L', 'KIDS_XL', 'KIDS_XXL'];

$sizingGrid = [
    'dewasa' => [
        'short' => array_fill_keys($dewasaSizes, 0),
        'long'  => array_fill_keys($dewasaSizes, 0),
    ],
    'anak' => [
        'short' => array_fill_keys($anakSizes, 0),
        'long'  => array_fill_keys($anakSizes, 0),
    ]
];

if (!empty($order['item_details'])) {
    foreach ($order['item_details'] as $detail) {
        $sz = strtoupper(trim($detail['size'] ?? ''));
        $modelLengan = strtolower(trim($detail['model_lengan'] ?? ''));
        $isLong = str_contains($modelLengan, 'long') || str_contains($modelLengan, 'panjang');
        
        $model = $isLong ? 'long' : 'short';
        
        if (in_array($sz, $dewasaSizes)) {
            $sizingGrid['dewasa'][$model][$sz]++;
        } elseif (in_array($sz, $anakSizes)) {
            $sizingGrid['anak'][$model][$sz]++;
        } else {
            if (in_array('KIDS_' . $sz, $anakSizes)) {
                $sizingGrid['anak'][$model]['KIDS_' . $sz]++;
            } elseif ($sz === 'XXL' && in_array('2XL', $dewasaSizes)) {
                $sizingGrid['dewasa'][$model]['2XL']++;
            } elseif ($sz === 'XXXL' && in_array('3XL', $dewasaSizes)) {
                $sizingGrid['dewasa'][$model]['3XL']++;
            }
        }
    }
}
@endphp

<div x-data="detailPesananApp()">
{{-- Kembali --}}
<div class="mb-5">
    <a href="{{ route('staf.daftar-pesanan') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-[#1a237e] transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Pesanan
    </a>
</div>

{{-- Tabs Navigation --}}
<div class="flex max-w-2xl gap-1 bg-white rounded-2xl p-1.5 shadow-sm border border-gray-200 mb-8">
    <button @click="activeTab = 'detail'"
        :class="activeTab === 'detail' ? 'bg-[#1a237e] text-white shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-semibold'"
        class="flex-1 px-5 py-2.5 rounded-xl text-sm transition-all flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Detail Pesanan
    </button>
    <button @click="activeTab = 'spk'"
        :class="activeTab === 'spk' ? 'bg-[#1a237e] text-white shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-semibold'"
        class="flex-1 px-5 py-2.5 rounded-xl text-sm transition-all flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Surat Perintah Kerja (SPK)
    </button>
</div>

{{-- 2-COLUMN LAYOUT --}}
<div class="flex gap-6 items-start">

    {{-- ── KOLOM KIRI ─────────────────────────────────────────────── --}}
    <div class="flex-1 min-w-0">
        <div x-show="activeTab === 'detail'" class="space-y-5">
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
                    {{-- Tombol WA ke customer --}}
                    @php
                        $custPhone = preg_replace('/[^0-9]/', '', $order['customer']['phone'] ?? '');
                        if (str_starts_with($custPhone, '0')) { $custPhone = '62' . substr($custPhone, 1); }
                    @endphp
                    @if($custPhone)
                    <a href="https://wa.me/{{ $custPhone }}?text={{ urlencode('Halo, ini dari tim Novos mengenai pesanan ' . $order['order_id']) }}" target="_blank" rel="noopener" title="Chat via WhatsApp" class="flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 font-medium hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.089.534 4.055 1.474 5.766L0 24l6.395-1.472A11.955 11.955 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.886 0-3.653-.498-5.176-1.37l-.368-.216-3.817.879.906-3.717-.24-.381A9.95 9.95 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                        WhatsApp
                    </a>
                    @endif
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
        <div x-data="editProduk()" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Detail Produk
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('staf.pesanan.export-excel', $order['order_id']) }}"
                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Excel
                    </a>
                    <button @click="openModal()" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-[#1a237e] bg-[#1a237e]/5 hover:bg-[#1a237e]/10 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>
                </div>
            </h3>
            <div class="grid grid-cols-3 gap-x-8 gap-y-2.5 text-sm mb-4">
                <div><span class="text-gray-500 text-xs">Jenis</span><div class="font-medium text-gray-900">{{ $order['product']['type'] }}</div></div>
                <div><span class="text-gray-500 text-xs">Nama Tim</span><div class="font-medium text-gray-900" x-text="form.team_name || 'Jersey Custom'">{{ $order['product']['team_name'] ?? 'Jersey Custom' }}</div></div>
                <div><span class="text-gray-500 text-xs">Nama Artikel</span><div class="font-medium text-gray-900" :class="{ 'text-gray-400 italic': !form.nama_artikel }" x-text="form.nama_artikel || 'Belum diisi'">{{ $order['product']['nama_artikel'] ?? '-' }}</div></div>
                <div><span class="text-gray-500 text-xs">Nama Pemesan</span><div class="font-medium text-gray-900" x-text="form.nama_pemesan || '-'">{{ $order['product']['nama_pemesan'] ?? '-' }}</div></div>
                <div><span class="text-gray-500 text-xs">Detail Sponsor</span><div class="font-medium text-gray-900" x-text="form.detail_sponsor || '-'">{{ $order['product']['detail_sponsor'] ?? '-' }}</div></div>
                <div><span class="text-gray-500 text-xs">Bahan</span><div class="font-medium text-gray-900" x-text="form.material || '-'">{{ $order['product']['material'] ?? '-' }}</div></div>
                <div><span class="text-gray-500 text-xs">Kerah</span><div class="font-medium text-gray-900" x-text="form.collar_style || '-'">{{ $order['product']['collar_style'] ?? '-' }}</div></div>
                <div><span class="text-gray-500 text-xs">Jenis Potongan</span><div class="font-medium text-gray-900" x-text="form.jenis_potongan || '-'">{{ $order['product']['jenis_potongan'] ?? '-' }}</div></div>
                <div><span class="text-gray-500 text-xs">Lengan & Jahitan</span><div class="font-medium text-gray-900" x-text="form.lengan_jahitan || '-'">{{ $order['product']['lengan_jahitan'] ?? '-' }}</div></div>
                <div><span class="text-gray-500 text-xs">Prioritas</span><div class="font-medium" :class="{'text-green-600 font-semibold': form.priority === 'normal', 'text-orange-600 font-semibold': form.priority === 'express', 'text-red-600 font-bold': form.priority === 'super_express'}" x-text="form.priority === 'express' ? 'Express' : form.priority === 'super_express' ? 'Super Express' : 'Normal'">{{ $order['product']['priority'] ?? 'normal' }}</div></div>
                <div><span class="text-gray-500 text-xs">Tanggal Masuk</span><div class="font-medium text-gray-900">{{ $order['tanggal_masuk'] }}</div></div>
                <div><span class="text-gray-500 text-xs">Deadline</span><div class="font-medium text-red-600">{{ $order['deadline'] }}</div></div>
                <div><span class="text-gray-500 text-xs">Total Qty</span><div class="font-medium text-gray-900">{{ $order['total_qty'] }} pcs</div></div>
            </div>
            {{-- Item Details Table --}}
            @if(!empty($order['item_details']))
            <div class="mb-4">
                <span class="text-xs font-medium text-gray-600 mb-2 block">Detail Item Pesanan</span>
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
            {{-- Edit Modal --}}
            <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/40" @click="editModalOpen = false"></div>
                    <div x-show="editModalOpen" x-transition.scale.origin.bottom class="relative w-full max-w-3xl p-6 my-8 bg-white rounded-2xl shadow-2xl border border-gray-200">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-bold text-gray-900">Edit Detail Produk</h3>
                            <button @click="editModalOpen = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Tim</label>
                                <input type="text" x-model="form.team_name" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Artikel <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.nama_artikel" placeholder="Isi nama artikel" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Pemesan</label>
                                <input type="text" x-model="form.nama_pemesan" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Detail Sponsor</label>
                                <input type="text" x-model="form.detail_sponsor" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Bahan</label>
                                <input type="text" x-model="form.material" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Kerah</label>
                                <input type="text" x-model="form.collar_style" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Potongan</label>
                                <input type="text" x-model="form.jenis_potongan" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Lengan & Jahitan</label>
                                <input type="text" x-model="form.lengan_jahitan" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Prioritas</label>
                                <select x-model="form.priority" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all bg-white">
                                    <option value="normal">Normal (7–14 hari)</option>
                                    <option value="express">Express (3–6 hari)</option>
                                    <option value="super_express">Super Express (1–2 hari)</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-gray-100">
                            <button @click="editModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button @click="save()" :disabled="loading" class="px-5 py-2 text-sm font-semibold text-white bg-[#1a237e] hover:bg-[#0d124a] rounded-lg transition-colors disabled:opacity-50 flex items-center gap-2">
                                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- File Desain Customer --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            {{-- Logo Tim --}}
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Logo Tim
            </h3>
            <div class="grid grid-cols-4 gap-4 mb-8">
                @php $logoIdx = 0; @endphp
                @forelse(collect($order['design_files'])->where('type', 'logo')->reject(fn($f) => in_array($f['role'] ?? '', $spkRoles)) as $f)
                @php
                    $isImg = isset($f['url']) && ($f['mime'] ?? '') && str_starts_with($f['mime'], 'image/');
                    $currentIdx = $logoIdx;
                    $logoIdx++;
                @endphp
                <div class="relative group bg-gray-100 rounded-xl aspect-square border border-gray-200 hover:border-[#1a237e]/40 transition-colors overflow-hidden">
                    @if($isImg)
                        <img src="{{ $f['url'] }}" alt="{{ $f['name'] }}" class="w-full h-full object-cover cursor-zoom-in"
                             onclick="PhotoSwipePreview.open('logos', {{ $currentIdx }})">
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
                <div class="col-span-3 py-6 text-center text-gray-400 text-sm">Tidak ada logo tim.</div>
                @endforelse
            </div>

            {{-- Referensi Desain --}}
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Referensi Desain
            </h3>
            <div class="grid grid-cols-4 gap-4">
                @php $desainIdx = 0; @endphp
                @forelse(collect($order['design_files'])->where('type', 'design')->reject(fn($f) => in_array($f['role'] ?? '', $spkRoles)) as $f)

                @php
                    $isImg = isset($f['url']) && ($f['mime'] ?? '') && str_starts_with($f['mime'], 'image/');
                    $currentIdx = $desainIdx;
                    $desainIdx++;
                @endphp
                <div class="relative group bg-gray-100 rounded-xl aspect-square border border-gray-200 hover:border-[#1a237e]/40 transition-colors overflow-hidden">
                    @if($isImg)
                        <img src="{{ $f['url'] }}" alt="{{ $f['name'] }}" class="w-full h-full object-cover cursor-zoom-in"
                             onclick="PhotoSwipePreview.open('designs', {{ $currentIdx }})">
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
                <div class="col-span-3 py-6 text-center text-gray-400 text-sm">Belum ada referensi desain.</div>
                @endforelse
            </div>
        </div>

        @php
            $allImages = collect($order['design_files'])
                ->reject(fn($f) => in_array($f['role'] ?? '', $spkRoles))
                ->filter(fn($f) => isset($f['url']) && ($f['mime'] ?? '') && str_starts_with($f['mime'], 'image/'))
                ->map(fn($f) => ['src' => $f['url'], 'alt' => $f['name'], 'width' => 1200, 'height' => 1200])
                ->values()
                ->toArray();

            $logoImages = collect($order['design_files'])
                ->where('type', 'logo')
                ->reject(fn($f) => in_array($f['role'] ?? '', $spkRoles))
                ->filter(fn($f) => isset($f['url']) && ($f['mime'] ?? '') && str_starts_with($f['mime'], 'image/'))
                ->map(fn($f) => ['src' => $f['url'], 'alt' => $f['name'], 'width' => 1200, 'height' => 1200])
                ->values()
                ->toArray();

            $designImages = collect($order['design_files'])
                ->where('type', 'design')
                ->reject(fn($f) => in_array($f['role'] ?? '', $spkRoles))
                ->filter(fn($f) => isset($f['url']) && ($f['mime'] ?? '') && str_starts_with($f['mime'], 'image/'))
                ->map(fn($f) => ['src' => $f['url'], 'alt' => $f['name'], 'width' => 1200, 'height' => 1200])
                ->values()
                ->toArray();
        @endphp
        <script>
        window.PhotoSwipePreview = {
            logos: @json($logoImages),
            designs: @json($designImages),
            open: function(category, index) {
                var files = this[category];
                if (files && files.length > 0 && window.openPhotoSwipe) {
                    window.openPhotoSwipe(files, index);
                }
            }
        };
        </script>

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
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400 mb-0.5">
                            {{ $h['date'] }} — <span class="font-medium text-gray-600">{{ $h['user'] }}</span>
                            <span class="inline-block ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-medium {{ match($h['origin'] ?? '') { 'Customer' => 'bg-blue-100 text-blue-700', 'Design' => 'bg-purple-100 text-purple-700', 'Produksi', 'Produksi (Printing)', 'Produksi (Jahit)', 'Produksi (QC)' => 'bg-amber-100 text-amber-700', default => 'bg-gray-100 text-gray-600' } }}">
                                [{{ $h['origin'] ?? 'Sistem' }}]
                            </span>
                        </p>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $h['note'] }}</p>
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
                        $st = match($sh['status']) { 'menunggu_pembayaran'=>'orange','tahap_desain'=>'blue','menunggu_acc'=>'orange','siap_cetak'=>'indigo','menunggu_spk'=>'yellow','tahap_produksi'=>'purple','selesai'=>'green','dibatalkan'=>'red',default=>'gray' };
                        $sl = match($sh['status']) { 'menunggu_pembayaran'=>'Menunggu Pembayaran','tahap_desain'=>'Tahap Desain','menunggu_acc'=>'Menunggu ACC','siap_cetak'=>'Siap Cetak','menunggu_spk'=>'Menunggu SPK','tahap_produksi'=>'Produksi','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan',default=>$sh['status'] };
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

        {{-- ── TAB SPK CONTENT ── --}}
        <div x-show="activeTab === 'spk'" x-cloak class="space-y-5">
            {{-- SPK Header / Specs --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Surat Perintah Kerja (SPK) — Produksi
                    </h3>
                    
                    {{-- SPK Download Button --}}
                    <a href="{{ route('staf.pesanan.export-spk', $order['order_id']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export SPK (Excel)
                    </a>
                </div>

                {{-- Specs Grid --}}
                <div class="grid grid-cols-3 gap-x-8 gap-y-3.5 text-sm">
                    <div><span class="text-gray-500 text-xs">Jenis</span><div class="font-semibold text-gray-900">Jersey Custom</div></div>
                    <div><span class="text-gray-500 text-xs">Nama Tim</span><div class="font-semibold text-gray-900">{{ $order['product']['team_name'] ?? 'Jersey Custom' }}</div></div>
                    <div><span class="text-gray-500 text-xs">Nama Artikel</span><div class="font-semibold text-gray-900">{{ $order['product']['nama_artikel'] ?? '-' }}</div></div>
                    <div><span class="text-gray-500 text-xs">Nama Pemesan</span><div class="font-semibold text-gray-900">{{ $order['product']['nama_pemesan'] ?? '-' }}</div></div>
                    <div><span class="text-gray-500 text-xs">Bahan</span><div class="font-semibold text-gray-900">{{ $order['product']['material'] ?? '-' }}</div></div>
                    <div><span class="text-gray-500 text-xs">Kerah</span><div class="font-semibold text-gray-900">{{ $order['product']['collar_style'] ?? '-' }}</div></div>
                    <div><span class="text-gray-500 text-xs">Jenis Potongan</span><div class="font-semibold text-gray-900">{{ $order['product']['jenis_potongan'] ?? '-' }}</div></div>
                    <div><span class="text-gray-500 text-xs">Lengan & Jahitan</span><div class="font-semibold text-gray-900">{{ $order['product']['lengan_jahitan'] ?? '-' }}</div></div>
                    <div><span class="text-gray-500 text-xs">Prioritas</span><div class="font-semibold text-gray-900">{{ $order['product']['priority'] ?? 'normal' }}</div></div>
                </div>
            </div>

            {{-- Sizing Matrix --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4 text-sm">Rincian Total Ukuran</h3>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm text-center">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Tipe</th>
                                <th class="px-2 py-2 font-semibold">XS</th>
                                <th class="px-2 py-2 font-semibold">S</th>
                                <th class="px-2 py-2 font-semibold">M</th>
                                <th class="px-2 py-2 font-semibold">L</th>
                                <th class="px-2 py-2 font-semibold">XL</th>
                                <th class="px-2 py-2 font-semibold">2XL</th>
                                <th class="px-2 py-2 font-semibold">3XL</th>
                                <th class="px-2 py-2 font-semibold">4XL</th>
                                <th class="px-2 py-2 font-semibold">6XL</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="px-3 py-2.5 font-medium text-gray-800 text-left bg-gray-50/50">Lengan Pendek</td>
                                @foreach($dewasaSizes as $sz)
                                <td class="px-2 py-2.5">{{ $sizingGrid['dewasa']['short'][$sz] ?? 0 }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="px-3 py-2.5 font-medium text-gray-800 text-left bg-gray-50/50">Lengan Panjang</td>
                                @foreach($dewasaSizes as $sz)
                                <td class="px-2 py-2.5">{{ $sizingGrid['dewasa']['long'][$sz] ?? 0 }}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mockup Desain Final (Tampak Depan & Belakang) --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-6 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Mockup Jersey Final (Untuk SPK)
                </h3>
                
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Tampak Depan Box --}}
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">Tampak Depan (Portrait)</span>
                        <div class="relative rounded-xl overflow-hidden transition-all">
                            
                            <template x-if="mockupDepan">
                                <div class="bg-gray-50 border border-gray-200 rounded-xl aspect-[3/4] flex flex-col items-center justify-center overflow-hidden relative group">
                                    <img :src="mockupDepan.url" class="w-full h-full object-cover cursor-zoom-in"
                                     @click="window.openPhotoSwipe?.([{path: mockupDepan.url, name: 'Mockup Depan'}], 0)">
                                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all gap-2">
                                        <button @click="deleteFile(mockupDepan.path)" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-md text-xs font-medium flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="!mockupDepan">
                                <div data-pond-role="mockup_depan">
                                    <input type="file" accept="image/*">
                                </div>
                            </template>
                            
                            <div x-show="uploadingSlot === 'mockup_depan'" class="absolute inset-0 bg-white/85 flex items-center justify-center z-10">
                                <span class="loading loading-spinner text-[#1a237e]"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Tampak Belakang Box --}}
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">Tampak Belakang (Landscape)</span>
                        <div class="relative rounded-xl overflow-hidden transition-all">
                            
                            <template x-if="mockupBelakang">
                                <div class="bg-gray-50 border border-gray-200 rounded-xl aspect-[4/3] flex flex-col items-center justify-center overflow-hidden relative group">
                                    <img :src="mockupBelakang.url" class="w-full h-full object-cover cursor-zoom-in"
                                         @click="window.openPhotoSwipe?.([{path: mockupBelakang.url, name: 'Mockup Belakang'}], 0)">
                                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all gap-2">
                                        <button @click="deleteFile(mockupBelakang.path)" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-md text-xs font-medium flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="!mockupBelakang">
                                <div data-pond-role="mockup_belakang">
                                    <input type="file" accept="image/*">
                                </div>
                            </template>
                            
                            <div x-show="uploadingSlot === 'mockup_belakang'" class="absolute inset-0 bg-white/85 flex items-center justify-center z-10">
                                <span class="loading loading-spinner text-[#1a237e]"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Tampak Depan & Belakang --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-6 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Detail Tampak Depan & Tampak Belakang Zoom
                </h3>
                
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Detail Depan Box --}}
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">Detail Tampak Depan</span>
                        <div class="relative rounded-xl overflow-hidden transition-all">
                            
                            <template x-if="detailDepan">
                                <div class="bg-gray-50 border border-gray-200 rounded-xl aspect-[3/4] flex flex-col items-center justify-center overflow-hidden relative group">
                                    <img :src="detailDepan.url" class="w-full h-full object-cover cursor-zoom-in"
                                         @click="window.openPhotoSwipe?.([{path: detailDepan.url, name: 'Detail Depan'}], 0)">
                                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all gap-2">
                                        <button @click="deleteFile(detailDepan.path)" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-md text-xs font-medium flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="!detailDepan">
                                <div data-pond-role="detail_depan">
                                    <input type="file" accept="image/*">
                                </div>
                            </template>
                            
                            <div x-show="uploadingSlot === 'detail_depan'" class="absolute inset-0 bg-white/85 flex items-center justify-center z-10">
                                <span class="loading loading-spinner text-[#1a237e]"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Belakang Box --}}
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">Detail Tampak Belakang</span>
                        <div class="relative rounded-xl overflow-hidden transition-all">
                            
                            <template x-if="detailBelakang">
                                <div class="bg-gray-50 border border-gray-200 rounded-xl aspect-[3/4] flex flex-col items-center justify-center overflow-hidden relative group">
                                    <img :src="detailBelakang.url" class="w-full h-full object-cover cursor-zoom-in"
                                         @click="window.openPhotoSwipe?.([{path: detailBelakang.url, name: 'Detail Belakang'}], 0)">
                                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all gap-2">
                                        <button @click="deleteFile(detailBelakang.path)" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-md text-xs font-medium flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="!detailBelakang">
                                <div data-pond-role="detail_belakang">
                                    <input type="file" accept="image/*">
                                </div>
                            </template>
                            
                            <div x-show="uploadingSlot === 'detail_belakang'" class="absolute inset-0 bg-white/85 flex items-center justify-center z-10">
                                <span class="loading loading-spinner text-[#1a237e]"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sponsor & Logo Final --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-900 mb-2 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    Sponsor & Patch Final
                </h3>
                
                <div class="grid grid-cols-4 gap-4" x-show="sponsorFiles.length > 0">
                    <template x-for="(file, idx) in sponsorFiles" :key="file.path">
                        <div class="relative bg-gray-50 border border-gray-200 rounded-xl aspect-square flex items-center justify-center overflow-hidden group">
                            <img :src="file.url" class="w-full h-full object-cover cursor-zoom-in"
                                 @click="window.openPhotoSwipe?.(sponsorFiles, idx)">
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                                <button @click="deleteFile(file.path)" class="p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-md text-xs font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div data-pond-role="sponsor">
                    <input type="file" accept="image/*">
                </div>
            </div>

            {{-- Catatan Desain (Editable) --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Catatan Desain / Detail Kerja SPK
                </h3>
                <div class="space-y-4">
                    <textarea x-model="spkNotes" rows="5" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 resize-none bg-white" placeholder="Tuliskan spesifikasi detail printing, jenis bahan kerah, nama font, dll..."></textarea>
                    
                    <div class="flex justify-end">
                        <button @click="saveNotes()" :disabled="isSavingNotes" class="px-5 py-2.5 bg-[#1a237e] hover:bg-[#1a237e]/90 text-white rounded-lg text-sm font-semibold flex items-center gap-2 transition-all disabled:opacity-50 cursor-pointer">
                            <svg x-show="isSavingNotes" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="isSavingNotes ? 'Menyimpan...' : 'Simpan Catatan SPK'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── KOLOM KANAN ─────────────────────────────────────────────── --}}
    <div class="w-80 shrink-0 space-y-5">

        {{-- Pembayaran --}}
        @php
        $role = auth()->user()->role->name;
        $canValidate = in_array($role, ['Admin', 'Manager', 'Super Admin']);
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5" x-data="paymentSection()">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-900 text-sm">Pembayaran</h3>
                @if($order['payment']['status'] === 'lunas')
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Lunas
                </span>
                @else
                <span class="text-xs font-semibold text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full">Pending</span>
                @endif
            </div>

            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Total</span>
                <span class="font-bold text-gray-900">{{ rh($order['payment']['total']) }}</span>
            </div>

            {{-- Tombol Validasi Pembayaran --}}
            @if($canValidate && $order['payment']['status'] !== 'lunas')
            <div class="mt-3 pt-3 border-t border-gray-100 space-y-2">
                @if($rawStatus === 'menunggu_pembayaran')
                <button @click="validateDp()" :disabled="loading"
                    class="w-full py-2.5 rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5 bg-green-600 hover:bg-green-700 text-white cursor-pointer">
                    <svg x-show="loading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Validasi Pembayaran (DP)
                </button>
                @else
                <button @click="validatePayment('success')" :disabled="loading"
                    class="w-full py-2.5 rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5 bg-green-600 hover:bg-green-700 text-white cursor-pointer">
                    <svg x-show="loading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Validasi Pembayaran (Lunas)
                </button>
                @endif
            </div>
            @endif
        </div>

        {{-- Update Status --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5" x-data="updateStatusSection()" x-init="init()">
            <h3 class="font-semibold text-gray-900 mb-4 text-sm">Update Status</h3>
            <template x-if="loading">
                <div class="flex flex-col items-center justify-center py-6 gap-2">
                    <svg class="w-5 h-5 animate-spin text-[#1a237e]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <p class="text-xs text-gray-400">Memuat status...</p>
                </div>
            </template>
            <template x-if="!loading && allowedStatuses.length > 0">
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
            <template x-if="!loading && allowedStatuses.length === 0">
                <p class="text-sm text-gray-400 text-center py-4">Tidak ada perubahan status yang tersedia untuk saat ini.</p>
            </template>
        </div>

    </div>{{-- end kolom kanan --}}

</div>{{-- end flex --}}

</div>{{-- end x-data wrapper --}}

@endsection

<script>
function detailPesananApp() {
    return {
        activeTab: 'detail',
        designFiles: @json($order['design_files'] ?? []),
        spkNotes: @json($order['product']['notes'] ?? ''),
        isSavingNotes: false,
        uploadingSlot: null,
        pondRefs: {},
        get mockupDepan() {
            return this.designFiles.find(f => f.role === 'mockup_depan') || null;
        },
        get mockupBelakang() {
            return this.designFiles.find(f => f.role === 'mockup_belakang') || null;
        },
        get detailDepan() {
            return this.designFiles.find(f => f.role === 'detail_depan') || null;
        },
        get detailBelakang() {
            return this.designFiles.find(f => f.role === 'detail_belakang') || null;
        },
        get sponsorFiles() {
            return this.designFiles.filter(f => f.role === 'sponsor');
        },
        init() {
            if (this.activeTab === 'spk') {
                this.$nextTick(() => this.initPonds());
            }
            this.$watch('activeTab', value => {
                if (value === 'spk') {
                    this.$nextTick(() => this.initPonds());
                }
            });
        },

        initPonds() {
            if (this.activeTab !== 'spk') return;
            setTimeout(() => {
                const containers = document.querySelectorAll('[data-pond-role]');
                containers.forEach(container => {
                    const role = container.getAttribute('data-pond-role');
                    this.initPond(container, role);
                });
            }, 50);
        },

        initPond(containerEl, role) {
            if (!window.FilePond) {
                setTimeout(() => this.initPond(containerEl, role), 50);
                return;
            }
            if (this.pondRefs[role]) return;
            const input = containerEl.querySelector('input[type="file"]');
            if (!input) return;
            if (window.FilePond.find(input)) return;

            const pond = FilePond.create(input, {
                allowMultiple: role === 'sponsor',
                instantUpload: false,
                allowProcess: false,
                credits: false,
                labelIdle: role === 'sponsor'
                    ? 'Drag & drop atau <span class="filepond--label-action">Cari</span>'
                    : 'Drag & drop atau <span class="filepond--label-action">Cari</span>',
                stylePanelLayout: 'compact',
                imagePreviewHeight: 80,
            });

            pond.on('addfile', async (error, fileItem) => {
                if (error) return;
                if (fileItem.origin === FilePond.FileOrigin.LOCAL) return;
                const file = fileItem.file;
                if (file instanceof File) {
                    await this.uploadFileDirectly(file, role, pond);
                }
            });

            this.pondRefs[role] = pond;
        },

        async uploadFileDirectly(file, role, pond) {
            this.uploadingSlot = role;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('role', role);

            try {
                const res = await fetch('{{ route("staf.pesanan.upload-spk-file", $order["order_id"]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    if (['mockup_depan', 'mockup_belakang', 'detail_depan', 'detail_belakang'].includes(role)) {
                        this.designFiles = this.designFiles.filter(f => f.role !== role);
                        delete this.pondRefs[role];
                    }
                    this.designFiles = [...this.designFiles, data.file];
                    Notify.success(data.message, 'Berhasil!');
                } else {
                    Notify.error(data.message || 'Terjadi kesalahan saat upload.');
                    try { pond.removeFile(file); } catch(e) {}
                }
            } catch (err) {
                Notify.error('Gagal mengupload file.');
                try { pond.removeFile(file); } catch(e) {}
            } finally {
                this.uploadingSlot = null;
                if (role === 'sponsor') {
                    try { pond.removeFiles(); } catch(e) {}
                }
            }
        },

        async deleteFile(path) {
            const result = await Swal.fire({
                title: 'Hapus File?',
                text: 'Apakah Anda yakin ingin menghapus file ini dari SPK?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            try {
                const res = await fetch('{{ route("staf.pesanan.delete-spk-file", $order["order_id"]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ path: path })
                });
                const data = await res.json();
                if (data.success) {
                    this.designFiles = this.designFiles.filter(f => f.path !== path);
                    this.$nextTick(() => this.initPonds());
                    Notify.success(data.message, 'Dihapus!');
                } else {
                    Notify.error(data.message || 'Gagal menghapus file.');
                }
            } catch (err) {
                Notify.error('Gagal menghapus file.');
            }
        },

        async saveNotes() {
            this.isSavingNotes = true;
            try {
                const res = await fetch('{{ route("staf.pesanan.update-spk-notes", $order["order_id"]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ notes: this.spkNotes })
                });
                const data = await res.json();
                if (data.success) {
                    Notify.success(data.message, 'Tersimpan!');
                } else {
                    Notify.error(data.message || 'Gagal menyimpan catatan.');
                }
            } catch (err) {
                Notify.error('Gagal menyimpan catatan.');
            } finally {
                this.isSavingNotes = false;
            }
        }
    };
}

function paymentSection() {
    return {
        loading: false,
        async validateDp() {
            if (this.loading) return;

            const result = await Swal.fire({
                title: 'Validasi Pembayaran DP?',
                text: 'Status pesanan akan diubah menjadi Dikonfirmasi.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Validasi DP!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            this.loading = true;
            try {
                const res = await fetch('{{ route("staf.pesanan.update-status", $order["order_id"]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: 'dikonfirmasi',
                        notes: 'Pembayaran DP divalidasi'
                    })
                });
                const data = await res.json();
                if (data.success) {
                    Notify.success('Pembayaran DP berhasil divalidasi!', 'Berhasil!');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    Notify.error(data.message || 'Terjadi kesalahan.');
                }
            } catch (e) {
                Notify.error('Terjadi kesalahan sistem.');
            } finally {
                this.loading = false;
            }
        },
        async validatePayment(status) {
            if (this.loading) return;

            const result = await Swal.fire({
                title: 'Validasi Pembayaran Lunas?',
                text: 'Pembayaran akan ditandai sebagai Lunas.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Validasi Lunas!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            this.loading = true;
            try {
                const res = await fetch('{{ route("staf.pesanan.payment-status", $order["order_id"]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: status,
                        notes: ''
                    })
                });
                const data = await res.json();
                if (data.success) {
                    Notify.success('Pembayaran Lunas berhasil divalidasi!', 'Berhasil!');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    Notify.error(data.message || 'Terjadi kesalahan.');
                }
            } catch (e) {
                Notify.error('Terjadi kesalahan sistem.');
            } finally {
                this.loading = false;
            }
        }
    };
}

function updateStatusSection() {
    return {
        allowedStatuses: [],
        selectedStatus: '',
        statusNote: '',
        updating: false,
        loading: true,
        async init() {
            try {
                const res = await fetch('{{ route("staf.pesanan.allowed-statuses", $order["order_id"]) }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.allowedStatuses = data.statuses || [];
            } catch (e) {
                this.allowedStatuses = [];
            } finally {
                this.loading = false;
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

    const __product = @json($order['product']);

    function editProduk() {
        return {
            editModalOpen: false,
            loading: false,
            form: {
                team_name: '',
                nama_artikel: '',
                nama_pemesan: '',
                detail_sponsor: '',
                material: '',
                collar_style: '',
                jenis_potongan: '',
                lengan_jahitan: '',
                priority: 'normal',
                additional_notes: '',
            },
            openModal() {
                const p = __product;
                this.form.team_name = p.team_name || '';
                this.form.nama_artikel = p.nama_artikel || '';
                this.form.nama_pemesan = p.nama_pemesan || '';
                this.form.detail_sponsor = p.detail_sponsor || '';
                this.form.material = p.material || '';
                this.form.collar_style = p.collar_style || '';
                this.form.jenis_potongan = p.jenis_potongan || '';
                this.form.lengan_jahitan = p.lengan_jahitan || '';
                this.form.priority = p.priority || 'normal';
                this.form.additional_notes = p.notes || '';
                this.editModalOpen = true;
            },
            async save() {
                this.loading = true;
                try {
                    const res = await fetch('{{ route("staf.pesanan.update-design-request", $order["order_id"]) }}', {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.form)
                    });
                    const data = await res.json();
                    if (data.success) {
                        Object.assign(__product, this.form);
                        Notify.success(data.message, 'Berhasil!');
                        this.editModalOpen = false;
                    } else {
                        Notify.error(data.message || 'Terjadi kesalahan.');
                    }
                } catch (e) {
                    Notify.error('Terjadi kesalahan sistem.');
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
