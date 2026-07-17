@extends('layouts.internal')

@php
$noteColors = ['bg-green-500','bg-yellow-500','bg-blue-500','bg-purple-500'];
function rh($n){ return 'Rp '.number_format($n,0,',','.'); }
$role = auth()->user()->role->name;
$canValidate = in_array($role, ['Admin', 'Manager', 'Super Admin']);
@endphp

@section('title', 'Detail Pesanan')

@section('topbar-left')
    <div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-1.5 sm:gap-3">
            <h1 class="text-lg sm:text-xl font-bold text-[#1a237e] whitespace-nowrap">{{ $order['order_id'] }}</h1>
            <x-badge type="{{ $badgeType }}">{{ $badgeLabel }}</x-badge>
        </div>
        <p class="text-sm text-gray-500 mt-0.5">{{ $order['last_update'] }}</p>
    </div>
@endsection

@section('internal-content')
@php
$spkRoles = ['mockup_depan','mockup_belakang','detail_depan','detail_belakang','sponsor','pola'];
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

// Majority-rule calculation for product specifications
$majoritySpecs = [];
if (!empty($order['item_details'])) {
    $fields = [
        'material' => 'material',
        'collar_style' => 'collar_style', 
        'jenis_potongan' => 'jenis_potongan',
        'lengan_jahitan' => 'lengan_jahitan',
    ];
    
    // Add dynamic customization fields from attributes_schema
    if (!empty($attributesSchema)) {
        foreach ($attributesSchema as $attr) {
            $fields[$attr['id']] = $attr['id'];
        }
    }
    
    foreach ($fields as $fieldKey => $fieldName) {
        $counts = [];
        foreach ($order['item_details'] as $detail) {
            $val = null;
            if ($fieldKey === 'material') {
                $val = $detail['customizations']['bahan'] ?? $order['product']['material'] ?? null;
            } elseif ($fieldKey === 'collar_style') {
                $val = $detail['customizations']['kerah'] ?? $order['product']['collar_style'] ?? null;
            } elseif ($fieldKey === 'jenis_potongan') {
                $val = $detail['customizations']['jenis_potongan'] ?? $order['product']['jenis_potongan'] ?? null;
            } elseif ($fieldKey === 'lengan_jahitan') {
                $val = $detail['customizations']['lengan_jahitan'] ?? $order['product']['lengan_jahitan'] ?? null;
            } else {
                $val = $detail['customizations'][$fieldKey] ?? null;
            }
            
            if ($val) {
                $val = trim($val);
                $counts[$val] = ($counts[$val] ?? 0) + 1;
            }
        }
        
        if (!empty($counts)) {
            arsort($counts);
            $majoritySpecs[$fieldKey] = [
                'value' => key($counts),
                'count' => current($counts),
                'total' => count($order['item_details']),
            ];
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
{{-- Desktop --}}
<div class="hidden lg:flex max-w-2xl gap-1 bg-white rounded-2xl p-1.5 shadow-sm border border-gray-200 mb-8">
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
{{-- Mobile --}}
<div class="lg:hidden flex gap-1 bg-white rounded-xl p-1 shadow-sm border border-gray-200 mb-4">
    <button @click="activeTab = 'detail'"
        :class="activeTab === 'detail' ? 'bg-[#1a237e] text-white shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-semibold'"
        class="flex-1 px-3 py-2 rounded-lg text-xs transition-all flex items-center justify-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Detail
    </button>
    <button @click="activeTab = 'spk'"
        :class="activeTab === 'spk' ? 'bg-[#1a237e] text-white shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-semibold'"
        class="flex-1 px-3 py-2 rounded-lg text-xs transition-all flex items-center justify-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        SPK
    </button>
</div>

{{-- 2-COLUMN LAYOUT --}}
<div class="flex flex-col lg:flex-row gap-4 lg:gap-6 items-start">

    {{-- ── KOLOM KIRI ─────────────────────────────────────────────── --}}
    <div class="flex-1 min-w-0 overflow-hidden w-full">
        <div x-show="activeTab === 'detail'" class="space-y-5">
            {{-- Info Pesanan --}}
        @php
            $currentStep = collect($steps)->firstWhere('current', true);
            $nextStep = collect($steps)->filter(fn($s) => !$s['done'] && !$s['current'])->first();
            $doneSteps = collect($steps)->filter(fn($s) => $s['done'])->count();
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 md:p-6">
            <h3 class="font-semibold text-gray-900 mb-4 md:mb-6 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Info Pesanan
            </h3>

            {{-- Mobile: Compact Status --}}
            <div class="md:hidden space-y-2">
                <div class="flex items-center gap-3 bg-[#1a237e]/5 rounded-xl p-3">
                    <div class="w-9 h-9 rounded-full bg-[#1a237e] border-4 border-[#1a237e]/20 flex items-center justify-center shadow-md shadow-[#1a237e]/25 shrink-0">
                        <div class="w-2.5 h-2.5 rounded-full bg-white"></div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] text-gray-500 uppercase tracking-wide">Status Saat Ini</p>
                        <p class="text-sm font-bold text-[#1a237e] truncate">{{ $currentStep['label'] ?? '-' }}</p>
                    </div>
                    <span class="text-[11px] font-bold text-[#1a237e] bg-[#1a237e]/10 px-2 py-0.5 rounded-full">{{ $doneSteps }}/{{ count($steps) }}</span>
                </div>
                @if($nextStep)
                <div class="flex items-center gap-2 text-xs text-gray-500 pl-1">
                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    <span>Selanjutnya: <span class="font-semibold text-gray-700">{{ $nextStep['label'] }}</span></span>
                </div>
                @endif
            </div>

            {{-- Desktop: Full Stepper --}}
            <div class="hidden md:block relative">
                <div class="absolute top-4 left-4 right-4 h-0.5 bg-gray-200 z-0" style="left: calc(100% / {{ count($steps) * 2 }}); right: calc(100% / {{ count($steps) * 2 }});">
                    @php $doneCount = collect($steps)->filter(fn($s)=>$s['done'])->count(); @endphp
                    <div class="h-full bg-[#1a237e] transition-all" style="width: {{ max(0, (($doneCount - 1) / (count($steps) - 1)) * 100) }}%"></div>
                </div>
                <div class="relative z-10 flex w-full justify-between">
                @foreach($steps as $idx => $step)
                <div class="flex flex-col items-center">
                    @if($step['current'])
                    <div class="w-8 h-8 rounded-full bg-[#1a237e] border-4 border-[#1a237e]/20 flex items-center justify-center shadow-md shadow-[#1a237e]/25 shrink-0">
                        <div class="w-2.5 h-2.5 rounded-full bg-white"></div>
                    </div>
                    @elseif($step['done'])
                    <div class="w-8 h-8 rounded-full bg-[#1a237e] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    @else
                    <div class="w-8 h-8 rounded-full bg-gray-100 border-2 border-gray-300 flex items-center justify-center shrink-0">
                        <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                    </div>
                    @endif
                    <div class="text-center px-1 mt-2">
                        <p class="text-xs font-semibold leading-tight {{ $step['done'] || $step['current'] ? 'text-gray-800' : 'text-gray-400' }}">{{ $step['label'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $step['date'] ?? '—' }}</p>
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
            <div class="grid grid-cols-2 md:grid-cols-3 gap-x-4 md:gap-x-8 gap-y-2.5 text-sm mb-4">
                <div><span class="text-gray-500 text-xs">Jenis</span><div class="font-medium text-gray-900">{{ $order['product']['type'] }}</div></div>
                <div><span class="text-gray-500 text-xs">Nama Tim</span><div class="font-medium text-gray-900" x-text="form.team_name || 'Jersey Custom'">{{ $order['product']['team_name'] ?? 'Jersey Custom' }}</div></div>
                <div><span class="text-gray-500 text-xs">Nama Artikel</span><div class="font-medium text-gray-900" :class="{ 'text-gray-400 italic': !form.nama_artikel }" x-text="form.nama_artikel || 'Belum diisi'">{{ $order['product']['nama_artikel'] ?? '-' }}</div></div>
                <div><span class="text-gray-500 text-xs">Nama Pemesan</span><div class="font-medium text-gray-900" x-text="form.nama_pemesan || '-'">{{ $order['product']['nama_pemesan'] ?? '-' }}</div></div>
                <div><span class="text-gray-500 text-xs">Detail Sponsor</span><div class="font-medium text-gray-900" x-text="form.detail_sponsor || '-'">{{ $order['product']['detail_sponsor'] ?? '-' }}</div></div>
                
                {{-- Majority-based Spesifikasi Jersey --}}
                @php
                    $getMajority = function($key, $fallback = '-') use ($majoritySpecs, $order) {
                        if (isset($majoritySpecs[$key])) {
                            return $majoritySpecs[$key]['value'];
                        }
                        // Fallback to product-level data
                        $productKey = $key;
                        if ($key === 'material') $productKey = 'material';
                        elseif ($key === 'collar_style') $productKey = 'collar_style';
                        elseif ($key === 'jenis_potongan') $productKey = 'jenis_potongan';
                        elseif ($key === 'lengan_jahitan') $productKey = 'lengan_jahitan';
                        return $order['product'][$productKey] ?? $fallback;
                    };
                @endphp
                
                <div><span class="text-gray-500 text-xs">Bahan</span><div class="font-medium text-gray-900">{{ $getMajority('material') }}</div></div>
                <div><span class="text-gray-500 text-xs">Kerah</span><div class="font-medium text-gray-900">{{ $getMajority('collar_style') }}</div></div>
                <div><span class="text-gray-500 text-xs">Jenis Potongan</span><div class="font-medium text-gray-900">{{ $getMajority('jenis_potongan') }}</div></div>
                <div><span class="text-gray-500 text-xs">Lengan & Jahitan</span><div class="font-medium text-gray-900">{{ $getMajority('lengan_jahitan') }}</div></div>
                
                {{-- Dynamic customization attributes (majority) --}}
                @if(!empty($attributesSchema))
                    @foreach($attributesSchema as $attr)
                        @if(isset($majoritySpecs[$attr['id']]))
                            <div>
                                <span class="text-gray-500 text-xs">{{ $attr['name'] }}</span>
                                <div class="font-medium text-gray-900">{{ $majoritySpecs[$attr['id']]['value'] }}</div>
                            </div>
                        @endif
                    @endforeach
                @endif
                
                <div><span class="text-gray-500 text-xs">Prioritas</span><div class="font-medium" :class="{'text-green-600 font-semibold': form.priority === 'normal', 'text-orange-600 font-semibold': form.priority === 'express', 'text-red-600 font-bold': form.priority === 'super_express'}" x-text="form.priority === 'express' ? 'Express' : form.priority === 'super_express' ? 'Super Express' : 'Normal'">{{ $order['product']['priority'] ?? 'normal' }}</div></div>
                <div><span class="text-gray-500 text-xs">Tanggal Masuk</span><div class="font-medium text-gray-900">{{ $order['tanggal_masuk'] }}</div></div>
                <div><span class="text-gray-500 text-xs">Deadline</span><div class="font-medium text-red-600">{{ $order['deadline'] }}</div></div>
                <div><span class="text-gray-500 text-xs">Total Qty</span><div class="font-medium text-gray-900">{{ $order['total_qty'] }} pcs</div></div>
            </div>
            {{-- Item Details Table --}}
            @if(!empty($order['item_details']))
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-600 block">Detail Item Pesanan</span>
                    @if($canValidate)
                    <button @click="openItemsModal()" class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold text-white bg-[#1a237e] hover:bg-[#0d124a] rounded-lg transition-colors cursor-pointer shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Kelola Item & Harga
                    </button>
                    @endif
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">No</th>
                                <th class="px-3 py-2 text-left font-semibold">Nama Punggung</th>
                                <th class="px-3 py-2 text-left font-semibold">NPG</th>
                                <th class="px-3 py-2 text-left font-semibold">Size</th>
                                <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                                <th class="px-3 py-2 text-right font-semibold">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order['item_details'] as $detail)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-2 text-gray-800 font-medium">{{ $loop->iteration }}</td>
                                <td class="px-3 py-2 text-gray-700 font-medium">{{ $detail['nama_punggung'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $detail['no_punggung'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $detail['size'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700">
                                    {{ $detail['keterangan_simple'] ?? '-' }}
                                </td>
                                <td class="px-3 py-2 text-right font-medium text-gray-900">{{ 'Rp ' . number_format($detail['price'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Kelola Item & Harga Modal --}}
            <template x-teleport="body">
            <div x-show="itemsModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div x-show="itemsModalOpen" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/40" @click="itemsModalOpen = false"></div>
                    <div x-show="itemsModalOpen" x-transition.scale.origin.bottom class="relative w-full max-w-5xl p-6 my-8 bg-white rounded-2xl shadow-2xl border border-gray-200">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-bold text-gray-900">Kelola Item Pesanan & Harga Invoice</h3>
                            <button @click="itemsModalOpen = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 mb-4 max-h-[60vh]">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide border-b border-gray-200">
                                    <tr>
                                        <th class="px-3 py-3 font-semibold w-20">No Punggung</th>
                                        <th class="px-3 py-3 font-semibold w-36">Nama Punggung</th>
                                        <th class="px-3 py-3 font-semibold w-20">Size</th>
                                        <th class="px-3 py-3 font-semibold">Keterangan</th>
                                        <th class="px-3 py-3 font-semibold w-44">Harga (Rp)</th>
                                        <th class="px-3 py-3 font-semibold w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    <template x-for="(item, idx) in items" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-3 py-2">
                                                <input type="text" x-model="item.no_punggung" class="w-full px-2 py-1 text-sm border border-gray-200 rounded focus:ring-1 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="text" x-model="item.nama_punggung" class="w-full px-2 py-1 text-sm border border-gray-200 rounded focus:ring-1 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="text" x-model="item.size" class="w-full px-2 py-1 text-sm border border-gray-200 rounded focus:ring-1 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none">
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="text-xs text-gray-500" x-text="getKeteranganSummary(item) || '-'"></span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="relative rounded-md shadow-sm">
                                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5">
                                                        <span class="text-xs text-gray-500 font-medium">Rp</span>
                                                    </div>
                                                    <input
                                                        type="number"
                                                        x-model="item.price"
                                                        @keydown.enter.prevent="applyShortcut(idx)"
                                                        @keydown.down.prevent="applyShortcut(idx)"
                                                        @blur="applyShortcut(idx)"
                                                        class="w-full pl-8 pr-2.5 py-1 text-sm border border-gray-200 rounded focus:ring-1 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none font-semibold text-gray-900"
                                                        placeholder="85000"
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <button @click="openItemAttrModal(idx)" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-[#1a237e] bg-[#1a237e]/10 hover:bg-[#1a237e]/20 rounded-lg transition-colors">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-xs text-gray-500 mb-4 bg-blue-50 p-3 rounded-lg flex items-start gap-2 border border-blue-100">
                            <svg class="w-4 h-4 text-blue-900 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <span class="font-bold text-blue-900">Tips Shortcut Admin:</span> Ketik ribuan (misal: 115 atau 122.5), lalu tekan <kbd class="bg-white border border-gray-300 rounded px-1 text-[10px]">Enter</kbd> / <kbd class="bg-white border border-gray-300 rounded px-1 text-[10px]">Panah Bawah</kbd> / pindah input untuk otomatis melipatgandakan menjadi ribuan (<span class="font-semibold">Rp 115.000</span> / <span class="font-semibold">Rp 122.500</span>).
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button @click="itemsModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button @click="saveItems()" :disabled="loading" class="px-5 py-2 text-sm font-semibold text-white bg-[#1a237e] hover:bg-[#0d124a] rounded-lg transition-colors disabled:opacity-50 flex items-center gap-2">
                                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Simpan Item & Update Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </template>

            {{-- Modal Edit Atribut Per Item --}}
            <template x-teleport="body">
            <div x-show="attrModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div x-show="attrModalOpen" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/40" @click="attrModalOpen = false"></div>
                    <div x-show="attrModalOpen" x-transition.scale.origin.bottom class="relative w-full max-w-lg p-6 my-8 bg-white rounded-2xl shadow-2xl border border-gray-200">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Edit Keterangan Item</h3>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="attrModalItem ? (attrModalItem.nama_punggung || 'Baris ' + (attrModalIdx + 1)) : ''"></p>
                            </div>
                            <button @click="attrModalOpen = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <template x-if="attrModalItem">
                            <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-1">
                                @foreach($attributesSchema as $attr)
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $attr['name'] }}</label>
                                    @if(!empty($attr['options']))
                                        <select x-model="attrModalItem.customizations['{{ $attr['id'] }}']" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all">
                                            <option value="">- Pilih -</option>
                                            @foreach($attr['options'] as $opt)
                                                @php $val = is_array($opt) ? ($opt['value'] ?? '') : $opt; @endphp
                                                <option value="{{ $val }}">{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" x-model="attrModalItem.customizations['{{ $attr['id'] }}']" placeholder="Masukkan {{ $attr['name'] }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all">
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </template>
                        <div class="flex justify-end gap-3 mt-5 pt-4 border-t border-gray-100">
                            <button @click="attrModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button @click="attrModalOpen = false" class="px-5 py-2 text-sm font-semibold text-white bg-[#1a237e] hover:bg-[#0d124a] rounded-lg transition-colors">
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </template>
            {{-- Edit Modal --}}
            <template x-teleport="body">
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
                            <template x-if="form.customizations && Object.keys(form.customizations).length > 0">
                                <div class="col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <template x-for="keyName in Object.keys(form.customizations)" :key="keyName">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1" x-text="keyName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())"></label>
                                            <input type="text" x-model="form.customizations[keyName]" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-all">
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!form.customizations || Object.keys(form.customizations).length === 0">
                                <div class="col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                                </div>
                            </template>
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
            </template>
        </div>

        {{-- File Desain Customer --}}
        @php
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
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            {{-- Logo Tim --}}
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Logo Tim
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
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
                             onclick="window.openPhotoSwipe(@json($logoImages), {{ $currentIdx }})">
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
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
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
                             onclick="window.openPhotoSwipe(@json($designImages), {{ $currentIdx }})">
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
                            <span class="inline-block ml-1 font-semibold text-[11px] {{ match($h['origin'] ?? '') { 'Customer' => 'text-blue-600', 'Design' => 'text-purple-600', 'Produksi', 'Produksi (Printing)', 'Produksi (Jahit)', 'Produksi (QC)' => 'text-amber-600', default => 'text-gray-500' } }}">
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
            </template>
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
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 sm:gap-x-8 gap-y-3.5 text-sm">
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

            {{-- Mockup Jersey Final --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-6 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Mockup Jersey Final (Untuk SPK)
                </h3>

                <div class="flex flex-wrap gap-4">
                    <template x-if="mockupDepan">
                        <div class="bg-gray-50 border border-gray-200 rounded-xl w-full sm:w-64 overflow-hidden cursor-zoom-in"
                             @click="window.openPhotoSwipe?.([{path: mockupDepan.url, name: 'Mockup Depan'}], 0)">
                            <img :src="mockupDepan.url" class="w-full h-full object-cover">
                        </div>
                    </template>
                    <template x-if="!mockupDepan">
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl w-full sm:w-64 aspect-[3/4] flex items-center justify-center">
                            <p class="text-xs text-gray-400">Belum ada mockup depan</p>
                        </div>
                    </template>
                    <template x-if="mockupBelakang">
                        <div class="bg-gray-50 border border-gray-200 rounded-xl w-full sm:w-64 overflow-hidden cursor-zoom-in"
                             @click="window.openPhotoSwipe?.([{path: mockupBelakang.url, name: 'Mockup Belakang'}], 0)">
                            <img :src="mockupBelakang.url" class="w-full h-full object-cover">
                        </div>
                    </template>
                    <template x-if="!mockupBelakang">
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl w-full sm:w-64 aspect-[3/4] flex items-center justify-center">
                            <p class="text-xs text-gray-400">Belum ada mockup belakang</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Detail Tampak Depan & Belakang --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-6 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Detail Tampak Depan & Tampak Belakang Zoom
                </h3>

                <div class="flex flex-wrap gap-4">
                    <template x-if="detailDepan">
                        <div class="bg-gray-50 border border-gray-200 rounded-xl w-full sm:w-64 overflow-hidden cursor-zoom-in"
                             @click="window.openPhotoSwipe?.([{path: detailDepan.url, name: 'Detail Depan'}], 0)">
                            <img :src="detailDepan.url" class="w-full h-full object-cover">
                        </div>
                    </template>
                    <template x-if="!detailDepan">
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl w-full sm:w-64 aspect-[3/4] flex items-center justify-center">
                            <p class="text-xs text-gray-400">Belum ada detail depan</p>
                        </div>
                    </template>
                    <template x-if="detailBelakang">
                        <div class="bg-gray-50 border border-gray-200 rounded-xl w-full sm:w-64 overflow-hidden cursor-zoom-in"
                             @click="window.openPhotoSwipe?.([{path: detailBelakang.url, name: 'Detail Belakang'}], 0)">
                            <img :src="detailBelakang.url" class="w-full h-full object-cover">
                        </div>
                    </template>
                    <template x-if="!detailBelakang">
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl w-full sm:w-64 aspect-[3/4] flex items-center justify-center">
                            <p class="text-xs text-gray-400">Belum ada detail belakang</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Sponsor & Patch Final --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-6 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    Sponsor & Patch Final
                </h3>
                
                <template x-if="sponsorFiles.length > 0">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <template x-for="(file, idx) in sponsorFiles" :key="file.path">
                            <div class="bg-gray-50 border border-gray-200 rounded-xl aspect-square overflow-hidden cursor-zoom-in"
                                 @click="window.openPhotoSwipe?.(sponsorFiles, idx)">
                                <img :src="file.url" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="sponsorFiles.length === 0">
                    <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-8 flex items-center justify-center">
                        <p class="text-xs text-gray-400">Belum ada file sponsor dari tim design</p>
                    </div>
                </template>
            </div>


        </div>
    </div>

    {{-- ── KOLOM KANAN ─────────────────────────────────────────────── --}}
    <div class="w-full lg:w-80 shrink-0 space-y-5">

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

            <div class="flex justify-between text-sm mt-1">
                <span class="text-gray-500">DP Dibayar</span>
                <span class="font-semibold text-green-600" x-text="dpDisplay">Rp {{ number_format($order['payment']['dp_amount'] ?? 0, 0, ',', '.') }}</span>
            </div>
            
            <div class="flex justify-between text-sm mt-1">
                <span class="text-gray-500">Sisa Bayar</span>
                <span class="font-bold text-gray-900" x-text="sisaBayarDisplay">Rp {{ number_format(max(0, $order['payment']['total'] - ($order['payment']['dp_amount'] ?? 0)), 0, ',', '.') }}</span>
            </div>

            {{-- Form Input DP (Hanya Admin / Super Admin / Manager) --}}
            @if($canValidate)
            <div class="mt-3 pt-3 border-t border-gray-100">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Set Nominal DP (Rp)</label>
                <div class="flex gap-1.5">
                    <input type="number" x-model="dpAmount" placeholder="Contoh: 50000" 
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    <button @click="saveDp()" :disabled="savingDp"
                        class="px-3 py-1.5 bg-[#1a237e] text-white text-xs font-bold rounded-lg hover:bg-[#283593] transition-colors flex items-center justify-center shrink-0">
                        <svg x-show="savingDp" class="w-3 h-3 animate-spin mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Simpan
                    </button>
                </div>
            </div>
            @endif

            {{-- Tombol Link Invoice --}}
            <div class="mt-3 pt-3 border-t border-gray-100 space-y-2">
                <a href="{{ route('staf.pesanan.invoice', $order['order_id']) }}" target="_blank"
                    class="w-full py-2 rounded-lg text-xs font-bold border border-[#1a237e] text-[#1a237e] hover:bg-blue-50 transition-colors flex items-center justify-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Lihat Invoice Faktur
                </a>
                <a href="{{ route('staf.pesanan.invoice.download', $order['order_id']) }}" target="_blank"
                    class="w-full py-2 rounded-lg text-xs font-bold bg-[#1a237e] text-white hover:bg-[#283593] transition-colors flex items-center justify-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download PDF Invoice
                </a>
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
    };
}

function paymentSection() {
    return {
        loading: false,
        savingDp: false,
        dpAmount: '{{ $order['payment']['dp_amount'] ?? '' }}',
        paymentTotal: {{ $order['payment']['total'] ?? 0 }},
        get dpDisplay() {
            const val = parseInt(this.dpAmount);
            return isNaN(val) || val === 0 ? 'Rp 0' : 'Rp ' + val.toLocaleString('id-ID');
        },
        get sisaBayar() {
            const dp = parseInt(this.dpAmount);
            return isNaN(dp) ? this.paymentTotal : Math.max(0, this.paymentTotal - dp);
        },
        get sisaBayarDisplay() {
            return 'Rp ' + this.sisaBayar.toLocaleString('id-ID');
        },
        async saveDp() {
            if (this.savingDp) return;
            const amount = parseInt(this.dpAmount);
            if (isNaN(amount) || amount < 0) {
                Notify.error('Jumlah DP tidak valid.');
                return;
            }

            this.savingDp = true;
            try {
                const res = await fetch('{{ route("staf.pesanan.invoice.dp", $order["order_id"]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        dp_amount: amount
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.dpAmount = data.dp_amount;
                    Notify.success('DP berhasil disimpan!', 'Berhasil!');
                } else {
                    Notify.error(data.message || 'Gagal menyimpan DP');
                }
            } catch (e) {
                Notify.error('Terjadi kesalahan sistem.');
            } finally {
                this.savingDp = false;
            }
        },
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
        const p = __product;
        return {
            editModalOpen: false,
            itemsModalOpen: false,
            attrModalOpen: false,
            attrModalItem: null,
            attrModalIdx: null,
            loading: false,
            items: [],
            form: {
                team_name: p.team_name || '',
                nama_artikel: p.nama_artikel || '',
                nama_pemesan: p.nama_pemesan || '',
                detail_sponsor: p.detail_sponsor || '',
                material: p.material || '',
                collar_style: p.collar_style || '',
                jenis_potongan: p.jenis_potongan || '',
                lengan_jahitan: p.lengan_jahitan || '',
                priority: p.priority || 'normal',
                additional_notes: p.notes || '',
                customizations: p.customizations ? JSON.parse(JSON.stringify(p.customizations)) : {},
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
                this.form.customizations = p.customizations ? JSON.parse(JSON.stringify(p.customizations)) : {};
                this.editModalOpen = true;
            },
            openItemsModal() {
                this.items = JSON.parse(JSON.stringify(@json($order['item_details'])));
                this.items.forEach(item => {
                    if (!item.customizations) item.customizations = {};
                });
                this.itemsModalOpen = true;
            },
            applyShortcut(index) {
                let val = parseFloat(this.items[index].price);
                if (val > 0 && val < 1000) {
                    this.items[index].price = val * 1000;
                }
            },
            openItemAttrModal(idx) {
                this.attrModalIdx = idx;
                this.attrModalItem = this.items[idx];
                this.attrModalOpen = true;
            },
            getKeteranganSummary(item) {
                if (!item.customizations) return '';
                const labels = @json(array_map(fn($a) => ['id' => $a['id'], 'name' => $a['name']], $attributesSchema));
                return labels
                    .filter(l => item.customizations[l.id] && item.customizations[l.id] !== '' && item.customizations[l.id] !== '-')
                    .map(l => item.customizations[l.id])
                    .join(', ');
            },
            async saveItems() {
                this.loading = true;
                try {
                    const res = await fetch('{{ route("staf.pesanan.update-items", $order["order_id"]) }}', {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ items: this.items })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Notify.success(data.message, 'Berhasil!');
                        this.itemsModalOpen = false;
                        setTimeout(() => location.reload(), 800);
                    } else {
                        Notify.error(data.message || 'Terjadi kesalahan.');
                    }
                } catch (e) {
                    Notify.error('Terjadi kesalahan sistem.');
                } finally {
                    this.loading = false;
                }
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
