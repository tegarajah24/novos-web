@extends('layouts.customer')

@section('title', 'Buat Pesanan — Novos')
 
@php
    $collarOptions = json_decode(App\Models\Setting::get('jersey_collar_options', json_encode([
        "O-NECK V.1", "O-NECK V.2", "O-NECK V.3", "O-NECK V.4", "V-NECK V.5", 
        "V-NECK V.1", "V-NECK V.2", "V-NECK V.3", "V-NECK V.4", "V-NECK V.5", 
        "CLASSIC V.1", "CLASSIC V.2", "CLASSIC V.3", "CLASSIC V.4", "CLASSIC V.5", 
        "V-NECK V3 TUMPUK", "TIMNAS"
    ])), true);
    $collarImage = App\Models\Setting::get('jersey_collar_image', 'images/jersey_collar_guide.png');
    $collarImageUrl = (str_starts_with($collarImage, 'images/') || str_starts_with($collarImage, 'http'))
        ? asset($collarImage)
        : asset('storage/' . $collarImage);

    $bahanOptions = json_decode(App\Models\Setting::get('jersey_bahan_options', json_encode([
        "BINTIK JARUM GRADE B","MILANO GRADE B","BINTIK JARUM PREMIUM","MILANO PREMIUM","RABBIT","DROPPEDDLE","SMASH","WAFFLE","EMBOSH","MICROCOOL","JAQUARD AERO","COTTON 24S","COTTON 30S","LOTTO","PARASUT","PUMA","ULTRALIGHT A","ULTRALIGHT B"
    ])), true);
    $bahanImage = App\Models\Setting::get('jersey_bahan_image', 'images/Bahan Jersey.png');
    $bahanImageUrl = (str_starts_with($bahanImage, 'images/') || str_starts_with($bahanImage, 'http'))
        ? asset($bahanImage)
        : asset('storage/' . $bahanImage);

    $potonganOptions = json_decode(App\Models\Setting::get('jersey_potongan_options', json_encode([
        "REGULER","SLIMFIT CEWE","OVERSIZE","TUNIK","SLIM FIT UNISEX","BOXY CUT","KIDS"
    ])), true);
    $potonganImage = App\Models\Setting::get('jersey_potongan_image', 'images/Jenis Potongan.png');
    $potonganImageUrl = (str_starts_with($potonganImage, 'images/') || str_starts_with($potonganImage, 'http'))
        ? asset($potonganImage)
        : asset('storage/' . $potonganImage);

    $lenganOptions = json_decode(App\Models\Setting::get('jersey_lengan_options', json_encode([
        "REGULER OVERDECK","REGULER PAKAI MANSET","RAGLAN A OVERDECK","RAGLAN A PAKAI MANSET","RAGLAN B OVERDECK","RAGLAN B PAKAI MANSET"
    ])), true);
    $lenganImage = App\Models\Setting::get('jersey_lengan_image', 'images/Model Lengan & Jahitan.png');
    $lenganImageUrl = (str_starts_with($lenganImage, 'images/') || str_starts_with($lenganImage, 'http'))
        ? asset($lenganImage)
        : asset('storage/' . $lenganImage);

    $adminWaPhone = preg_replace('/[^0-9]/', '', App\Models\Setting::get('company_phone', '6281234567890'));
    if (str_starts_with($adminWaPhone, '0')) { $adminWaPhone = '62' . substr($adminWaPhone, 1); }

    // Prioritas Pengerjaan Dynamic Settings
    $prioritasNormalEstimasi = App\Models\Setting::get('prioritas_normal_estimasi', '7-14 hari kerja');
    $prioritasNormalBiaya = (int) App\Models\Setting::get('prioritas_normal_biaya', 0);
    $prioritasNormalStatus = App\Models\Setting::get('prioritas_normal_status', 'active');

    $prioritasExpressEstimasi = App\Models\Setting::get('prioritas_express_estimasi', '3-6 hari kerja');
    $prioritasExpressBiaya = (int) App\Models\Setting::get('prioritas_express_biaya', 50000);
    $prioritasExpressStatus = App\Models\Setting::get('prioritas_express_status', 'active');

    $prioritasSuperExpressEstimasi = App\Models\Setting::get('prioritas_super_express_estimasi', '1-2 hari kerja');
    $prioritasSuperExpressBiaya = (int) App\Models\Setting::get('prioritas_super_express_biaya', 150000);
    $prioritasSuperExpressStatus = App\Models\Setting::get('prioritas_super_express_status', 'active');

    $dynamicPriorities = [];
    if ($prioritasNormalStatus === 'active') {
        $dynamicPriorities[] = [
            'value' => 'normal',
            'label' => 'Normal',
            'desc' => $prioritasNormalEstimasi,
            'biaya' => $prioritasNormalBiaya,
            'harga' => $prioritasNormalBiaya > 0 ? '+Rp' . number_format($prioritasNormalBiaya, 0, ',', '.') : 'Gratis'
        ];
    }
    if ($prioritasExpressStatus === 'active') {
        $dynamicPriorities[] = [
            'value' => 'express',
            'label' => 'Express',
            'desc' => $prioritasExpressEstimasi,
            'biaya' => $prioritasExpressBiaya,
            'harga' => $prioritasExpressBiaya > 0 ? '+Rp' . number_format($prioritasExpressBiaya, 0, ',', '.') : 'Gratis'
        ];
    }
    if ($prioritasSuperExpressStatus === 'active') {
        $dynamicPriorities[] = [
            'value' => 'super_express',
            'label' => 'Super Express',
            'desc' => $prioritasSuperExpressEstimasi,
            'biaya' => $prioritasSuperExpressBiaya,
            'harga' => $prioritasSuperExpressBiaya > 0 ? '+Rp' . number_format($prioritasSuperExpressBiaya, 0, ',', '.') : 'Gratis'
        ];
    }
    if (empty($dynamicPriorities)) {
        $dynamicPriorities[] = [
            'value' => 'normal',
            'label' => 'Normal',
            'desc' => '7-14 hari kerja',
            'biaya' => 0,
            'harga' => 'Gratis'
        ];
    }
@endphp

@section('content')
@auth

<div class="max-w-6xl mx-auto px-4 py-8" x-data="pemesananForm({{ json_encode($produkData) }}, {{ json_encode($addresses) }}, {{ $hasOrders ? 'true' : 'false' }}, {{ json_encode($provinces) }}, {{ json_encode($categories) }}, {{ json_encode($bankAccounts) }})">
    {{-- Header --}}
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900">Buat Pesanan</h1>
        <p class="text-gray-500 mt-1">Pesan produk custom impianmu dalam 4 langkah mudah</p>
    </div>

    {{-- Step Indicator --}}
    <div class="flex items-center justify-center mb-10 px-4">
        <template x-for="(s, index) in steps" :key="index">
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div
                        :class="step >= index + 1 ? 'bg-[#1a237e] text-white' : 'bg-gray-200 text-gray-500'"
                        class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                    >
                        <svg x-show="step > index + 1" class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span x-show="step <= index + 1" x-text="index + 1"></span>
                    </div>
                    <span
                        :class="step >= index + 1 ? 'text-blue-900 font-semibold' : 'text-gray-400'"
                        class="text-xs mt-1.5 transition-colors hidden sm:block whitespace-nowrap"
                        x-text="s"
                    ></span>
                </div>
                <div
                    x-show="index < steps.length - 1"
                    :class="step > index + 1 ? 'bg-[#1a237e]' : 'bg-gray-200'"
                    class="w-10 sm:w-16 md:w-24 h-0.5 mx-1 sm:mx-2 transition-colors"
                ></div>
            </div>
        </template>
    </div>

    {{-- Step 1: Pilih Jenis & Kategori --}}
    <div x-show="step === 1" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-6">

        {{-- Phase 1: Pilih Jenis --}}
        <div x-show="jenisPhase === 1"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">
            <h2 class="text-lg font-semibold text-gray-900">Pilih Jenis Pesanan</h2>
            <p class="text-sm text-gray-500 mt-1">Pilih jenis pesanan yang sesuai kebutuhan Anda</p>

            {{-- Banner Konsultasi WhatsApp --}}
            <div class="mt-4 mb-5 flex items-center justify-between gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-2.5 sm:py-3 animate-fade-slide">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="shrink-0 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.089.534 4.055 1.474 5.766L0 24l6.395-1.472A11.955 11.955 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.886 0-3.653-.498-5.176-1.37l-.368-.216-3.817.879.906-3.717-.24-.381A9.95 9.95 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs sm:text-sm font-semibold text-green-800">Konsultasi via WhatsApp sebelum pesan!</p>
                        <p class="text-[11px] text-green-700 mt-0.5 leading-relaxed hidden sm:block">Hubungi admin kami terlebih dahulu agar brief desain Anda bisa diproses dengan tepat.</p>
                    </div>
                </div>
                <a href="https://wa.me/{{ $adminPhone }}?text={{ urlencode('Halo Novos, saya ingin konsultasi pesanan produk custom') }}"
                   target="_blank" rel="noopener"
                   class="shrink-0 inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-[11px] sm:text-xs font-semibold px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-lg transition-colors whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.089.534 4.055 1.474 5.766L0 24l6.395-1.472A11.955 11.955 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.886 0-3.653-.498-5.176-1.37l-.368-.216-3.817.879.906-3.717-.24-.381A9.95 9.95 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                    Chat Admin
                </a>
            </div>            <div class="grid md:grid-cols-2 gap-6 mt-6">
                {{-- Produk Custom --}}
                <div
                    @click="jenis = 'custom'"
                    :class="jenis === 'custom' ? 'border-[#1a237e] bg-blue-50/70 ring-2 ring-[#1a237e]' : 'border-gray-200 hover:border-gray-300'"
                    class="border-2 rounded-xl p-4 md:p-6 cursor-pointer transition-all duration-200 animate-fade-slide relative overflow-hidden flex flex-row md:flex-col items-center md:items-start gap-4 md:gap-0"
                    style="animation-delay:0.1s"
                >
                    <div :class="jenis === 'custom' ? 'bg-[#1a237e] text-white' : 'bg-gray-100 text-gray-400'" class="w-11 h-11 md:w-14 md:h-14 rounded-xl flex items-center justify-center transition-colors shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" class="md:w-7 md:h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1 md:mt-4">
                        <h3 class="font-bold text-base md:text-lg text-gray-900">Produk Custom</h3>
                        <p class="text-gray-500 text-xs md:text-sm mt-1 leading-relaxed">Desain produk sepenuhnya sesuai keinginan Anda. Upload logo, pilih spesifikasi, dan tentukan detail sendiri.</p>
                    </div>
                    <!-- Selected Checkmark -->
                    <div x-show="jenis === 'custom'" class="absolute top-2.5 right-2.5 w-5 h-5 rounded-full bg-[#1a237e] text-white flex items-center justify-center text-xs shadow-sm">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                </div>

                {{-- Produk Katalog --}}
                <div
                    @click="jenis = 'katalog'"
                    :class="jenis === 'katalog' ? 'border-[#1a237e] bg-blue-50/70 ring-2 ring-[#1a237e]' : 'border-gray-200 hover:border-gray-300'"
                    class="border-2 rounded-xl p-4 md:p-6 cursor-pointer transition-all duration-200 animate-fade-slide relative overflow-hidden flex flex-row md:flex-col items-center md:items-start gap-4 md:gap-0"
                    style="animation-delay:0.2s"
                >
                    <div :class="jenis === 'katalog' ? 'bg-[#1a237e] text-white' : 'bg-gray-100 text-gray-400'" class="w-11 h-11 md:w-14 md:h-14 rounded-xl flex items-center justify-center transition-colors shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" class="md:w-7 md:h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5A2.5 2.5 0 0 1 4 19.5Z"/><path d="M12 6v7l2-2 2 2V6"/></svg>
                    </div>
                    <div class="min-w-0 flex-1 md:mt-4">
                        <h3 class="font-bold text-base md:text-lg text-gray-900">Produk Katalog</h3>
                        <p class="text-gray-500 text-xs md:text-sm mt-1 leading-relaxed">Pilih dari koleksi desain yang sudah tersedia. Tinggal pilih ukuran dan jumlah pesanan.</p>
                    </div>
                    <!-- Selected Checkmark -->
                    <div x-show="jenis === 'katalog'" class="absolute top-2.5 right-2.5 w-5 h-5 rounded-full bg-[#1a237e] text-white flex items-center justify-center text-xs shadow-sm">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-8">
                <button
                    type="button"
                    @click="if(jenis === 'katalog') window.location.href = '{{ route('katalog') }}'; else if (!hasOrders) showFirstOrderAlert(); else jenisPhase = 2;"
                    :disabled="!jenis"
                    :class="jenis ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                    class="w-full sm:w-auto text-white px-5 py-2.5 sm:px-8 sm:py-3 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                >
                    Selanjutnya
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        </div>

        {{-- Phase 2: Pilih Kategori --}}
        <div x-show="jenisPhase === 2"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">
            <h2 class="text-lg font-semibold text-gray-900">Pilih Kategori</h2>
            <p class="text-sm text-gray-500 mt-1">Pilih kategori produk custom yang ingin Anda pesan</p>

            <div class="mt-6 space-y-6">
                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-gray-700">
                        Pilih Kategori Produk Custom <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <template x-for="cat in categories" :key="cat.id">
                            <div
                                @click="selectCategoryByName(cat.name)"
                                :class="selectedCategoryId == cat.id ? 'border-[#1a237e] bg-blue-50/70 ring-2 ring-[#1a237e] shadow-md scale-[1.02]' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50/50'"
                                class="border-2 rounded-xl p-5 cursor-pointer transition-all duration-300 flex flex-col items-center text-center relative group"
                            >
                                <div :class="selectedCategoryId == cat.id ? 'bg-[#1a237e] text-white' : 'bg-gray-100 text-gray-500 group-hover:bg-[#1a237e]/10 group-hover:text-[#1a237e]'" class="w-16 h-16 rounded-2xl flex items-center justify-center transition-all duration-300 overflow-hidden">
                                    <template x-if="cat.icon && (cat.icon.includes('/') || cat.icon.includes('.'))">
                                        <img :src="'/storage/' + cat.icon" class="w-10 h-10 object-contain rounded" />
                                    </template>
                                    <template x-if="!cat.icon || (!cat.icon.includes('/') && !cat.icon.includes('.'))">
                                        <i :data-lucide="cat.icon || 'shirt'" class="w-8 h-8"></i>
                                    </template>
                                </div>
                                <h3 class="font-bold text-sm mt-4 text-gray-800" x-text="cat.name"></h3>
                                <p x-show="cat.description" class="text-gray-400 text-[11px] mt-1.5 leading-relaxed max-w-[200px]" x-text="cat.description"></p>

                                <div x-show="selectedCategoryId == cat.id" class="absolute top-3 right-3 w-2.5 h-2.5 rounded-full bg-[#1a237e]"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-between gap-3 mt-8">
                <button
                    type="button"
                    @click="jenisPhase = 1"
                    class="w-full sm:w-auto px-5 py-2.5 sm:px-8 sm:py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors text-sm sm:text-base flex items-center justify-center"
                >
                    Kembali
                </button>
                <button
                    type="button"
                    @click="step = 2"
                    :disabled="!selectedCategoryId"
                    :class="selectedCategoryId ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                    class="w-full sm:w-auto text-white px-5 py-2.5 sm:px-8 sm:py-3 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                >
                    Selanjutnya
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Step 3: Detail & Upload --}}
    <div x-show="step === 2" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-6">
        <h2 class="text-lg font-semibold text-gray-900" x-text="subStep === 1 ? 'Detail & Upload' : 'Alamat Pengiriman'"></h2>
        <p class="text-sm text-gray-500 mt-1" x-text="subStep === 1 ? 'Lengkapi informasi pesanan dan upload file desain Anda' : 'Lengkapi alamat pengiriman untuk pesanan Anda'"></p>

            {{-- Sub-Step 1: Detail & Upload Form --}}
            <div x-show="subStep === 1" class="pb-24">
                <div class="space-y-6">

        {{-- Selected product from catalog --}}
        <template x-if="catalogProduct">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mt-6 flex items-center gap-4">
                <img :src="catalogProduct.gambar || 'https://placehold.co/80x80/1a237e/ffffff?text=Jersey'" class="w-16 h-16 object-cover rounded-lg shrink-0">
                <div class="min-w-0">
                    <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">Produk dari Katalog</p>
                    <p class="font-semibold text-gray-900 truncate" x-text="catalogProduct.produk"></p>
                    <p class="text-sm text-gray-500" x-text="catalogProduct.kategori"></p>
                    <template x-if="catalogProduct.harga">
                        <p class="text-sm font-bold text-blue-900" x-text="'Rp ' + parseInt(catalogProduct.harga).toLocaleString('id-ID') + '/pcs'"></p>
                    </template>
                </div>
            </div>
        </template>

        <div class="space-y-5 mt-6">
            {{-- Row: Nama Pemesan + Nama Tim --}}
            <div :class="formFieldsGridClass">
                {{-- Kiri: Nama Pemesan, Nama Tim, Nama Artikel --}}
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Pemesan
                            <span class="text-red-500">*</span>
                            <span class="text-[10px] text-red-500 font-normal ml-1">(wajib)</span>
                        </label>
                        <input
                            type="text"
                            x-model="form.nama_pemesan"
                            @blur="touched.nama_pemesan = true"
                            placeholder="Contoh: John Doe"
                            :class="touched.nama_pemesan && !form.nama_pemesan.trim() ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-300 focus:ring-[#1a237e] focus:border-[#1a237e]'"
                            class="w-full px-4 py-2.5 border rounded-lg outline-none transition-shadow"
                        >
                        <template x-if="touched.nama_pemesan && !form.nama_pemesan.trim()">
                            <p class="text-xs text-red-500 mt-1">Nama pemesan wajib diisi</p>
                        </template>
                    </div>
                    <div x-show="showTeamNameField" x-transition x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Tim / Event
                            <span class="text-red-500">*</span>
                            <span class="text-[10px] text-red-500 font-normal ml-1">(wajib)</span>
                        </label>
                        <input
                            type="text"
                            x-model="form.team_name"
                            @blur="touched.team_name = true"
                            placeholder="Contoh: FC Harapan Jaya"
                            :class="touched.team_name && !form.team_name.trim() ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-300 focus:ring-[#1a237e] focus:border-[#1a237e]'"
                            class="w-full px-4 py-2.5 border rounded-lg outline-none transition-shadow"
                        >
                        <template x-if="touched.team_name && !form.team_name.trim()">
                            <p class="text-xs text-red-500 mt-1">Nama tim / event wajib diisi</p>
                        </template>
                    </div>
                </div>

                {{-- Kanan: Nama Artikel & Detail Sponsor --}}
                <div class="space-y-5">
                    <div x-show="showNamaArtikelField" x-transition x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Artikel</label>
                        <input
                            type="text"
                            x-model="form.nama_artikel"
                            placeholder="Contoh: Jersey Tim Futsal"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none transition-shadow"
                        >
                    </div>
                    <div x-show="showDetailSponsorField" x-transition x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Detail Sponsor</label>
                        <input
                            type="text"
                            x-model="form.detail_sponsor"
                            placeholder="Contoh: Logo sponsor di dada"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none transition-shadow"
                        >
                    </div>
                </div>
            </div>
        </div>

            {{-- Dynamic details wrapped by selectedCategory --}}
            <div x-show="selectedCategoryId" x-cloak class="space-y-6">

            {{-- Total Quantity --}}
            <div class="grid lg:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Total Quantity (pcs) <span class="text-red-500">*</span></label>
                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-white w-fit">
                        <button
                            @click="form.total_qty = Math.max(1, (parseInt(form.total_qty) || 1) - 1)"
                            :class="(parseInt(form.total_qty) || 1) <= 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-600 hover:bg-gray-100 cursor-pointer'"
                            class="w-9 h-[42px] flex items-center justify-center transition-colors text-lg font-semibold shrink-0"
                            type="button"
                        >−</button>
                        <input
                            type="number"
                            x-model.number="form.total_qty"
                            @input="if (parseInt(form.total_qty) < 1 || isNaN(parseInt(form.total_qty))) form.total_qty = 1"
                            class="w-16 text-center text-sm font-semibold text-gray-900 border-0 outline-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            min="1"
                        >
                        <button
                            @click="form.total_qty = (parseInt(form.total_qty) || 1) + 1"
                            class="w-9 h-[42px] flex items-center justify-center text-gray-600 hover:bg-gray-100 cursor-pointer transition-colors text-lg font-semibold shrink-0"
                            type="button"
                        >+</button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Total keseluruhan jumlah jersey yang dipesan</p>
                </div>
            </div>

            {{-- Spesifikasi Utama (Atribut Global) --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mt-6" x-show="selectedCategoryId">
                <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center justify-between cursor-pointer select-none" @click="expandedSpecs = !expandedSpecs">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1a237e]"></span>
                        <span>Spesifikasi Utama</span>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600 transition-transform duration-200" :class="expandedSpecs ? 'rotate-180' : ''">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" x-show="expandedSpecs">
                    <template x-for="attr in activeSchema.filter(a => !a.depends_on || !a.depends_on.attribute_id || form.customizations[a.depends_on.attribute_id] === a.depends_on.value)" :key="attr.id">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-semibold text-gray-600" x-text="attr.name"></span>
                                    <template x-if="attr.system_tag === 'is_size_type' || attr.required">
                                        <span class="text-red-500 text-xs">*</span>
                                    </template>
                                </div>
                                <template x-if="attr.reference_image">
                                    <button type="button" @click="showAttrGuide(attr)" class="text-[10px] text-blue-900 hover:underline flex items-center gap-0.5">
                                        Panduan
                                    </button>
                                </template>
                            </div>
                            <template x-if="attr.type === 'select' || attr.type === 'radio'">
                                <div>
                                    <template x-if="attr.system_tag === 'is_size_type'">
                                        <select
                                            x-model="form.size"
                                            @change="onGlobalSizeChange()"
                                            class="w-full border border-gray-300 p-2 rounded-lg bg-white text-xs outline-none focus:ring-1 focus:ring-[#1a237e]"
                                        >
                                            <option value="">- Pilih -</option>
                                            <template x-for="(opt, oIdx) in (attr.options || [])" :key="oIdx">
                                                <option :value="opt.value" x-text="opt.value"></option>
                                            </template>
                                        </select>
                                    </template>
                                    <template x-if="attr.system_tag !== 'is_size_type'">
                                        <select
                                            x-model="form.customizations[attr.id]"
                                            class="w-full border border-gray-300 p-2 rounded-lg bg-white text-xs outline-none focus:ring-1 focus:ring-[#1a237e]"
                                        >
                                            <option value="">- Pilih -</option>
                                            <template x-for="(opt, oIdx) in (attr.options || [])" :key="oIdx">
                                                <option :value="opt.value" x-text="opt.value"></option>
                                            </template>
                                        </select>
                                    </template>
                                    <template x-if="getSelectedOptionPrice(attr) > 0">
                                        <div class="mt-1 flex items-center gap-1 text-[10px] text-emerald-600 font-semibold">
                                            <span x-text="'+ ' + formatRupiah(getSelectedOptionPrice(attr))"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="attr.type === 'text'">
                                <input
                                    type="text"
                                    x-model="form.customizations[attr.id]"
                                    class="w-full border border-gray-300 p-2 rounded-lg text-xs outline-none focus:ring-1 focus:ring-[#1a237e]"
                                    :placeholder="'Masukkan ' + attr.name"
                                >
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Detail Pesanan --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-sm font-medium text-gray-700">Detail Pesanan</label>
                    <button
                        type="button"
                        @click="showUkuranRef = true"
                        class="underline p-0 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-50 transition-colors"
                    >
                        Referensi Ukuran
                    </button>
                </div>

                {{-- Modal Referensi Ukuran --}}
                <template x-teleport="body">
                <div
                    x-show="showUkuranRef"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/55"
                    @click.self="showUkuranRef = false"
                >
                    <div
                        x-show="showUkuranRef"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                        class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden"
                        @click.stop
                        x-data="{ ukuranTab: 'potongan' }"
                    >
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.4 2.4 0 0 1 0-3.4l2.6-2.6a2.4 2.4 0 0 1 3.4 0Z"/>
                                        <path d="m14.5 12.5 2-2"/>
                                        <path d="m11.5 9.5 2-2"/>
                                        <path d="m8.5 6.5 2-2"/>
                                        <path d="m17.5 15.5 2-2"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900">Referensi Ukuran</h3>
                            </div>
                            <button
                                @click="showUkuranRef = false"
                                class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>

                        {{-- Tabs --}}
                        <div class="flex border-b border-gray-100 px-6">
                            <button
                                type="button"
                                @click="ukuranTab = 'potongan'"
                                class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors"
                                :class="ukuranTab === 'potongan' ? 'border-[#1a237e] text-[#1a237e]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            >
                                Atasan
                            </button>
                            <button
                                type="button"
                                @click="ukuranTab = 'training'"
                                class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors"
                                :class="ukuranTab === 'training' ? 'border-[#1a237e] text-[#1a237e]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            >
                                Bawahan
                            </button>
                        </div>

                        {{-- Tab Content --}}
                        <div class="px-6 py-4 overflow-y-auto flex-1 min-h-0">
                            {{-- Tab: Ukuran Potongan --}}
                            <template x-if="ukuranTab === 'potongan'">
                                <div>
                                    <template x-if="!form.jenis_potongan">
                                        <div class="flex flex-col items-center justify-center py-16 text-center">
                                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="11" cy="11" r="8"/>
                                                    <path d="m21 21-4.3-4.3"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-700 mb-1">Belum ada referensi ukuran</p>
                                            <p class="text-xs text-gray-400 max-w-xs">Silakan pilih <strong>Jenis Potongan</strong> terlebih dahulu untuk melihat referensi ukuran yang sesuai.</p>
                                        </div>
                                    </template>
                                    <template x-if="form.jenis_potongan">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-4">Klik gambar untuk melihat ukuran <strong class="text-[#1a237e]" x-text="form.jenis_potongan"></strong> secara penuh.</p>
                                            <div @click="openAtasanGallery" class="cursor-pointer group">
                                                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-50 aspect-[10/7]">
                                                    <img
                                                        :src="activeSizePdf"
                                                        class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
                                                        alt="Referensi Ukuran"
                                                    >
                                                </div>
                                                <p class="text-xs font-semibold text-gray-500 mt-1.5 text-center">Klik untuk perbesar</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Tab: Training --}}
                            <template x-if="ukuranTab === 'training'">
                                <div>
                                    <p class="text-xs text-gray-500 mb-4">Klik gambar untuk melihat ukuran <strong class="text-[#1a237e]">Bawahan</strong> secara penuh. Gunakan scroll / pinch untuk zoom, atau klik tombol fullscreen.</p>
                                    <div class="space-y-5">
                                        <div @click="openBawahanGallery(0)" class="cursor-pointer group">
                                            <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-50 aspect-[10/7]">
                                                <img src="/images/referensi-ukuran/TRAININGLONG.png" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" alt="Celana Panjang">
                                            </div>
                                            <p class="text-xs font-semibold text-gray-500 mt-1.5 text-center">Celana Panjang — klik untuk perbesar</p>
                                        </div>
                                        <div @click="openBawahanGallery(1)" class="cursor-pointer group">
                                            <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-50 aspect-[10/7]">
                                                <img src="/images/referensi-ukuran/TRAININGSHORT.png" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" alt="Celana Pendek">
                                            </div>
                                            <p class="text-xs font-semibold text-gray-500 mt-1.5 text-center">Celana Pendek — klik untuk perbesar</p>
                                        </div>
                                        <div @click="openBawahanGallery(2)" class="cursor-pointer group">
                                            <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-50 aspect-[10/7] relative">
                                                <img src="/images/referensi-ukuran/TRAININGLONG.png" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" alt="Rok">
                                                <span class="absolute top-2 right-2 px-2 py-0.5 bg-yellow-400 text-[11px] font-bold text-yellow-900 rounded">Sementara</span>
                                            </div>
                                            <p class="text-xs font-semibold text-gray-500 mt-1.5 text-center">Rok — klik untuk perbesar</p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                            <button
                                @click="showUkuranRef = false"
                                class="px-6 py-2 bg-[#1a237e] hover:bg-[#283593] text-white text-sm font-semibold rounded-lg transition-colors"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
                </template>

                {{-- Tabel Data Produksi Dinamis --}}
                <div x-show="showPlayerListField" class="mt-4 bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
                        <h4 class="text-sm font-bold text-gray-800">Daftar Pemain & Ukuran</h4>
                        <div class="text-xs text-gray-500 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg flex items-center gap-1.5 animate-pulse">
                            <span class="flex h-2 w-2 rounded-full bg-blue-600"></span>
                            <span>Secara default, pemain mengikuti <strong>Spesifikasi Utama</strong> di atas. Gunakan kustomisasi jika ada yang berbeda.</span>
                        </div>
                    </div>
                               <div class="p-4 bg-slate-50/50 border-b border-gray-100 flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-semibold text-gray-600">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="w-4 h-4 rounded cursor-pointer accent-[#1a237e] border-gray-300 bg-white">
                            <span>Pilih Semua Pemain</span>
                        </label>
                        <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full" x-text="`${items.length} Pemain`"></span>
                    </div>

                    <div class="p-4 space-y-3 bg-slate-50/30 max-h-[600px] overflow-y-auto">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-white border rounded-xl shadow-sm transition-all duration-200 overflow-hidden relative"
                                 :class="[
                                     item.selected ? 'border-[#1a237e] bg-blue-50/10' : 'border-gray-200',
                                     !isRowComplete(item) ? 'hover:border-amber-300' : 'hover:border-gray-300'
                                 ]">
                                
                                <!-- Card Number Badge (Top Left Corner) -->
                                <div class="absolute -top-0.5 -left-0.5 w-8 h-6 bg-[#1a237e] text-white flex items-center justify-center text-[10px] font-bold rounded-br-lg rounded-tl-xl shadow-sm z-10">
                                    <span x-text="index + 1"></span>
                                </div>

                                <!-- Card Header -->
                                <div class="p-3.5 flex items-center justify-between gap-3 select-none">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <!-- Checkbox -->
                                        <input type="checkbox" :checked="item.selected" @change="handleCheck($event, index)" 
                                               class="w-4 h-4 rounded cursor-pointer accent-[#1a237e] border-gray-300 bg-white">

                                        <!-- Player Info Summary (Inputs inline) -->
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <!-- No Punggung Input -->
                                                <input type="text" x-model="item.no" placeholder="No" maxlength="2"
                                                       :id="'no-' + index"
                                                       @input="item.no = item.no.replace(/[^0-9]/g, '')"
                                                       @keydown.enter.prevent="document.getElementById('nama-' + index)?.focus()"
                                                       class="w-14 text-center border border-gray-300 px-2 py-1 rounded text-xs font-bold text-gray-800 outline-none focus:ring-1 focus:ring-[#1a237e] focus:border-[#1a237e] bg-white placeholder-gray-400 placeholder:font-normal placeholder:text-[10px]">
                                                
                                                <!-- Nama Punggung Input -->
                                                <input type="text" x-model="item.nama" placeholder="Nama Punggung" maxlength="20"
                                                       :id="'nama-' + index"
                                                       @keydown.enter.prevent="document.getElementById('no-' + (index + 1))?.focus()"
                                                       class="flex-1 min-w-0 border border-gray-300 px-2 py-1 rounded text-xs font-bold text-gray-800 outline-none focus:ring-1 focus:ring-[#1a237e] focus:border-[#1a237e] bg-white placeholder-gray-400 placeholder:font-normal placeholder:text-[10px]">
                                            </div>
                                            <!-- Badges -->
                                            <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                                <!-- Size Badge -->
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-bold border border-slate-200">
                                                    Size <span x-text="item.size || '-'"></span>
                                                </span>
                                                <!-- Set Bawahan Badge if Jersey and selected -->
                                                <template x-if="selectedCategoryName.toLowerCase() === 'jersey' && item.customizations['set_bawahan'] === 'Ya' && item.customizations['tipe_bawahan']">
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-indigo-50 text-[#1a237e] text-[10px] font-bold border border-indigo-100">
                                                        Set: <span x-text="`${item.customizations['tipe_bawahan']} (${item.size || '-'})`"></span>
                                                    </span>
                                                </template>
                                                <!-- Customizations list -->
                                                <template x-for="cust in getCompiledCustomizations(item)" :key="cust.key">
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-100">
                                                        <span x-text="cust.value"></span>
                                                    </span>
                                                </template>
                                                <!-- Warning Badge -->
                                                <template x-if="!isRowComplete(item)">
                                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[9px] font-bold border border-amber-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                        Belum lengkap
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- FLOATING BULK BAR (EDIT MASAL UNTUK CUSTOMER) -->
                <div x-show="countSelected() > 0" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-5 py-3.5 sm:px-6 sm:py-4 rounded-2xl shadow-2xl border border-gray-800 flex flex-col sm:flex-row items-center gap-3 sm:gap-6 z-50 w-[calc(100%-2rem)] sm:w-auto max-w-md sm:max-w-none">
                    <div class="border-b sm:border-b-0 sm:border-r border-gray-800 pb-2 sm:pb-0 pr-0 sm:pr-4 w-full sm:w-auto flex items-center justify-between sm:block gap-4">
                        <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">Terpilih</p>
                        <p class="text-sm sm:text-base font-bold text-orange-400" x-text="countSelected() + ' Pemain'"></p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 text-xs w-full sm:w-auto">
                        <span class="font-semibold text-gray-300 hidden sm:inline">Aksi Massal:</span>
                        
                        <!-- Main Tools -->
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="flex-1 sm:flex-initial flex items-center gap-1.5 bg-gray-800 border border-gray-700 rounded-lg p-1">
                                <span class="text-[10px] text-gray-400 font-semibold px-1">Ukuran:</span>
                                <select x-model="bulkForm.size" @change="applyBulkSize()" class="bg-transparent text-white text-xs outline-none cursor-pointer pr-2 w-full sm:w-auto">
                                    <option value="" class="bg-gray-800">-- Pilih --</option>
                                    <template x-for="opt in sizeOptions" :key="opt.value">
                                        <option :value="opt.value" class="bg-gray-800" x-text="opt.value"></option>
                                    </template>
                                </select>
                            </div>

                            <button type="button" @click="openOverrideModal(null)" class="flex-1 sm:flex-initial bg-[#1a237e] hover:bg-[#283593] text-white font-bold px-3 py-1.5 rounded-lg transition-colors shadow flex items-center justify-center gap-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                Kustom Atribut
                            </button>
                        </div>
                        
                        <!-- Reset & Cancel (Aligned far apart on mobile) -->
                        <div class="flex items-center justify-between sm:justify-start gap-4 w-full sm:w-auto border-t sm:border-t-0 border-gray-800/60 pt-2.5 sm:pt-0 mt-1 sm:mt-0">
                            <button type="button" @click="resetSelectedOverrides()" class="bg-red-900/60 hover:bg-red-800 text-red-100 font-semibold px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                                Reset
                            </button>
                            
                            <button type="button" @click="clearSelection()" class="text-xs text-gray-400 hover:text-white underline">Batal</button>
                        </div>
                    </div>
                </div>
            </div>

        {{-- Upload Section --}}
        <div class="grid lg:grid-cols-2 gap-6 mt-8">
            {{-- Upload Logo Tim --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Logo Tim</label>
                <input type="file" class="filepond" id="pondLogo" name="logo_files[]" accept="image/png,image/jpeg,image/jpg,image/svg+xml" data-max-file-size="5MB" data-allow-multiple="true">
            </div>

            {{-- Referensi Desain --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Referensi Desain</label>
                <input type="file" class="filepond" id="pondRef" name="design_files[]" accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp" data-max-file-size="10MB" data-allow-multiple="true">
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-between gap-3 mt-8">
            <button
                @click="step = 1"
                class="w-full sm:w-auto px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors"
            >
                Kembali
            </button>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <button
                    @click="addToCart"
                    :disabled="!validateStep2 || loading"
                    :class="(validateStep2 && !loading) ? 'border-2 border-[#1a237e] text-[#1a237e] hover:bg-blue-50 cursor-pointer' : 'border-gray-300 text-gray-400 cursor-not-allowed'"
                    class="w-full sm:w-auto px-8 py-3 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2"
                >
                    <span x-show="!loading" class="inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        Masukkan ke Keranjang
                    </span>
                    <span x-show="loading" class="inline-flex items-center justify-center gap-2">
                        <svg class="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
                <button
                    @click="nextFromStep2()"
                    :disabled="!validateStep2"
                    :class="validateStep2 ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                    class="w-full sm:w-auto text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2"
                >
                    Pesan Langsung
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        </div>
            </div>
            </div>
        </div>

            {{-- Sticky Bottom Pricing Bar (Desktop & Mobile) --}}
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 py-3.5 shadow-[0_-4px_12px_rgba(0,0,0,0.06)] z-40">
                <div class="max-w-6xl mx-auto px-6 flex items-center justify-between">
                    <div>
                        <p class="text-[9px] sm:text-[10px] text-gray-400 font-bold uppercase tracking-wider">Estimasi Harga</p>
                        <p class="text-base sm:text-lg font-extrabold text-[#1a237e]" x-text="formatRupiah(hargaDasar)"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs sm:text-sm font-semibold text-gray-700" x-text="totalQty + ' pcs'"></p>
                        <p class="text-[10px] sm:text-xs text-gray-400" x-text="'@ ' + formatRupiah(hargaPerJersey)"></p>
                    </div>
                </div>
            </div>

        {{-- Sub-Step 2: Alamat Form / Select --}}
            <div x-show="subStep === 2" class="pb-24">
                {{-- Mode 1: Detail Kontak & Alamat --}}
                <div x-show="addressMode === 'select'" class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Detail Kontak & Alamat</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Pastikan data kontak dan alamat pengiriman Anda sudah benar sebelum melanjutkan.</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        {{-- Box 1: Detail Kontak --}}
                        <div class="bg-white border border-gray-200 rounded-2xl p-6 flex flex-col gap-4 hover:border-[#1a237e]/30 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1a237e] flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Detail Kontak</h4>
                            </div>
                            <div class="text-sm text-gray-600 space-y-2.5 grow">
                                <p class="font-bold text-gray-900 text-base" x-text="contactInfo.name || '{{ auth()->user()->name }}'"></p>
                                <p class="flex items-start gap-2">
                                    <svg class="text-gray-400 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    <span x-text="contactInfo.email || '{{ auth()->user()->email }}'"></span>
                                </p>
                                <p class="flex items-start gap-2">
                                    <svg class="text-gray-400 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
                                    <span x-text="(contactInfo.phone || '{{ auth()->user()->phone ?? '-' }}')"></span>
                                </p>
                            </div>
                            <div class="pt-2 border-t border-gray-100">
                                <button
                                    type="button"
                                    @click="openContactModal()"
                                    class="flex items-center gap-1.5 text-xs font-bold text-[#1a237e] hover:text-[#283593] group transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="group-hover:scale-110 transition-transform"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Ubah Kontak
                                </button>
                            </div>
                        </div>

                        {{-- Box 2: Alamat Pengiriman --}}
                        <div class="bg-white border border-gray-200 rounded-2xl p-6 flex flex-col gap-4 hover:border-[#1a237e]/30 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1a237e] flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                                <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Alamat Pengiriman</h4>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1.5 grow">
                                <template x-if="selectedAddress">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-bold text-gray-900 text-base" x-text="selectedAddress.first_name + (selectedAddress.last_name ? ' ' + selectedAddress.last_name : '')"></p>
                                            <span
                                                :class="selectedAddress.address_type === 'rumah' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'"
                                                class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase tracking-wider"
                                                x-text="selectedAddress.address_type"
                                            ></span>
                                        </div>
                                        <p x-text="selectedAddress.detail_address"></p>
                                        <p x-text="selectedAddress.district + ', ' + selectedAddress.city"></p>
                                        <p x-text="selectedAddress.province + ' ' + selectedAddress.postal_code"></p>
                                    </div>
                                </template>
                                <template x-if="!selectedAddress">
                                    <p class="text-gray-400 italic">Belum ada alamat pengiriman.</p>
                                </template>
                            </div>
                            <div class="pt-2 border-t border-gray-100 flex items-center gap-4">
                                <button
                                    type="button"
                                    @click="openAddressModal()"
                                    class="flex items-center gap-1.5 text-xs font-bold text-[#1a237e] hover:text-[#283593] group transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="group-hover:scale-110 transition-transform"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Ubah Alamat
                                </button>
                                <button
                                    type="button"
                                    @click="addressMode = 'list'"
                                    class="flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 group transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="group-hover:scale-110 transition-transform"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                                    Pilih Alamat Lain
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-between gap-3 mt-6">
                        <button
                            type="button"
                            @click="backFromSubStep2()"
                            class="w-full sm:w-auto px-5 py-2.5 sm:px-8 sm:py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors text-sm sm:text-base flex items-center justify-center"
                        >
                            Kembali
                        </button>
                        <button
                            type="button"
                            @click="useSelectedAddress()"
                            :disabled="!selectedAddressId"
                            :class="selectedAddressId ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                            class="w-full sm:w-auto text-white px-5 py-2.5 sm:px-8 sm:py-3 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                        >
                            Lanjutkan ke Pembayaran
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Mode 2: Pilih Alamat yang Sudah Ada (Card List) --}}
                <div x-show="addressMode === 'list'" class="space-y-6">
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <h3 class="font-bold text-lg text-gray-900">Pilih Alamat Lain</h3>
                            <p class="text-xs text-gray-500">Pilih salah satu alamat yang sudah tersimpan di bawah ini.</p>
                        </div>
                        <button
                            type="button"
                            @click="addNewAddressMode()"
                            class="px-4 py-2 border border-[#1a237e] text-[#1a237e] hover:bg-blue-50 rounded-lg font-semibold text-sm transition-all flex items-center gap-1.5"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            Tambah Alamat Baru
                        </button>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <template x-for="addr in addresses" :key="addr.id">
                            <div
                                @click="selectedAddressId = addr.id; addressMode = 'select';"
                                :class="selectedAddressId === addr.id ? 'border-2 border-[#1a237e] bg-blue-50/50' : 'border border-gray-200 hover:border-gray-300'"
                                class="bg-white rounded-xl p-5 cursor-pointer transition-all relative overflow-hidden select-none"
                            >
                                <!-- Badge Selected -->
                                <div x-show="selectedAddressId === addr.id" class="absolute top-0 right-0 bg-[#1a237e] text-white px-3 py-1 text-xs font-semibold rounded-bl-lg flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    Terpilih
                                </div>

                                <div class="flex gap-3">
                                    <div class="p-2 bg-blue-100/80 rounded-lg text-[#1a237e] h-10 w-10 shrink-0 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-bold text-gray-900 truncate" x-text="addr.first_name + (addr.last_name ? ' ' + addr.last_name : '')"></p>
                                            <span
                                                :class="addr.address_type === 'rumah' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'"
                                                class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase tracking-wider"
                                                x-text="addr.address_type"
                                            ></span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-2 leading-relaxed" x-text="addr.detail_address"></p>
                                        <p class="text-xs text-gray-500 mt-1" x-text="addr.district + ', ' + addr.city + ', ' + addr.province + ' ' + addr.postal_code"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-start mt-8">
                        <button
                            type="button"
                            @click="addressMode = 'select'"
                            class="w-full sm:w-auto px-5 py-2.5 sm:px-8 sm:py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors text-sm sm:text-base flex items-center justify-center"
                        >
                            Batal
                        </button>
                    </div>
                </div>

                {{-- Mode 3: Form Input Alamat Baru --}}
                <div x-show="addressMode === 'create'" class="space-y-6">
                    <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- Nama Depan --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Depan <span class="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    x-model="addressForm.first_name"
                                    placeholder="Nama Depan"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none transition-shadow"
                                >
                            </div>

                            {{-- Nama Belakang --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Belakang</label>
                                <input
                                    type="text"
                                    x-model="addressForm.last_name"
                                    placeholder="Nama Belakang"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none transition-shadow"
                                >
                            </div>
                        </div>

                        {{-- Nomor WhatsApp --}}
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    x-model="formPhone"
                                    placeholder="Contoh: 081234567890"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none transition-shadow"
                                >
                                <p class="text-xs text-gray-400 mt-1">Nomor ini akan tersimpan di profil Anda.</p>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-6">
                            {{-- Provinsi --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Provinsi <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select
                                        x-model="selectedProvinceId"
                                        @change="const prov = provinces.find(p => p.id === selectedProvinceId); addressForm.province = prov ? prov.name : ''; fetchRegencies();"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none transition-shadow bg-white appearance-none"
                                    >
                                        <option value="">Pilih Provinsi</option>
                                        <template x-for="prov in provinces" :key="prov.id">
                                            <option :value="prov.id" x-text="prov.name"></option>
                                        </template>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Kabupaten / Kota --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kabupaten / Kota <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select
                                        x-model="selectedRegencyId"
                                        @change="const reg = regencies.find(r => r.id === selectedRegencyId); addressForm.city = reg ? reg.name : ''; fetchDistricts();"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none transition-shadow bg-white appearance-none"
                                        :disabled="!selectedProvinceId || addressLoading.regencies"
                                    >
                                        <option value="">Pilih Kabupaten/Kota</option>
                                        <template x-for="reg in regencies" :key="reg.id">
                                            <option :value="reg.id" x-text="reg.name"></option>
                                        </template>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <template x-if="addressLoading.regencies">
                                            <svg class="animate-spin h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                        </template>
                                        <template x-if="!addressLoading.regencies">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Kecamatan --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kecamatan <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select
                                        x-model="selectedDistrictId"
                                        @change="const dist = districts.find(d => d.id === selectedDistrictId); addressForm.district = dist ? dist.name : '';"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none transition-shadow bg-white appearance-none"
                                        :disabled="!selectedRegencyId || addressLoading.districts"
                                    >
                                        <option value="">Pilih Kecamatan</option>
                                        <template x-for="dist in districts" :key="dist.id">
                                            <option :value="dist.id" x-text="dist.name"></option>
                                        </template>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <template x-if="addressLoading.districts">
                                            <svg class="animate-spin h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                        </template>
                                        <template x-if="!addressLoading.districts">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-6">
                            {{-- Detail Alamat --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Detail Alamat <span class="text-red-500">*</span></label>
                                <textarea
                                    x-model="addressForm.detail_address"
                                    rows="2"
                                    placeholder="Nama jalan, Gedung, No. Rumah, RT/RW, dll."
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none transition-shadow resize-none"
                                ></textarea>
                            </div>

                            {{-- Kode Pos --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Pos <span class="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    x-model="addressForm.postal_code"
                                    placeholder="Contoh: 12345"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none transition-shadow"
                                >
                            </div>
                        </div>

                        {{-- Tandai Sebagai --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tandai Sebagai</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-50 border border-gray-200 rounded-xl px-5 py-3 hover:bg-gray-100 transition-colors select-none">
                                    <input
                                        type="radio"
                                        name="address_type"
                                        value="rumah"
                                        x-model="addressForm.address_type"
                                        class="radio radio-primary"
                                    >
                                    <span class="text-sm font-semibold text-gray-800">Rumah</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-50 border border-gray-200 rounded-xl px-5 py-3 hover:bg-gray-100 transition-colors select-none">
                                    <input
                                        type="radio"
                                        name="address_type"
                                        value="kantor"
                                        x-model="addressForm.address_type"
                                        class="radio radio-primary"
                                    >
                                    <span class="text-sm font-semibold text-gray-800">Kantor</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-between gap-3 mt-8">
                        <button
                            type="button"
                            @click="addresses.length > 0 ? addressMode = 'list' : subStep = 1"
                            class="w-full sm:w-auto px-5 py-2.5 sm:px-8 sm:py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors text-sm sm:text-base flex items-center justify-center"
                        >
                            Kembali
                        </button>
                        <button
                            type="button"
                            @click="saveAddress()"
                            :disabled="!validateAddress || addressLoading.submit"
                            :class="(validateAddress && !addressLoading.submit) ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                            class="w-full sm:w-auto text-white px-5 py-2.5 sm:px-8 sm:py-3 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                        >
                            <span x-show="!addressLoading.submit" class="inline-flex items-center gap-2">
                                Simpan Alamat Baru
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </span>
                            <span x-show="addressLoading.submit" class="inline-flex items-center gap-2">
                                <svg class="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>



            </div>
    </div>

    {{-- Step 4: Prioritas & Pembayaran --}}
    <div x-show="step === 3" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-6">
        <h2 class="text-lg font-semibold text-gray-900">Prioritas &amp; Estimasi Biaya</h2>
        <p class="text-sm text-gray-500 mt-1">Estimasi biaya berdasarkan jumlah pesanan. Pembayaran dilakukan setelah admin memvalidasi pesanan.</p>

        <div class="grid lg:grid-cols-2 gap-8 mt-6">
            {{-- Left: Prioritas Pengerjaan --}}
            <div>
                <h3 class="text-base font-semibold text-gray-800 mb-4">Prioritas Pengerjaan</h3>
                <div class="grid grid-cols-3 gap-3">
                    <template x-for="(p, i) in prioritasOptions" :key="i">
                        <div
                            @click="prioritas = p.value"
                            :class="prioritas === p.value ? 'border-[#1a237e] bg-blue-50 ring-2 ring-[#1a237e]' : 'border-gray-200 hover:border-gray-300'"
                            class="border-2 rounded-xl py-3 px-4 cursor-pointer transition-all animate-fade-slide flex items-center gap-2"
                            :style="`animation-delay: ${0.05 + i * 0.07}s`"
                        >
                            <div :class="prioritas === p.value ? 'bg-[#1a237e] border-[#1a237e]' : 'border-gray-300'" class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-colors shrink-0">
                                <div x-show="prioritas === p.value" class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            </div>
                            <span class="font-semibold text-gray-900 text-sm" x-text="p.label"></span>
                        </div>
                    </template>
                </div>
                <div class="mt-3 flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                    <span class="text-sm text-gray-500" x-text="'Estimasi ' + (prioritasOptions.find(o => o.value === prioritas)?.desc || '')"></span>
                    <span class="text-sm font-bold text-gray-900" x-text="prioritasOptions.find(o => o.value === prioritas)?.harga || 'Gratis'"></span>
                </div>

                {{-- Info DP --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mt-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center shrink-0 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">DP Minimal 10%</p>
                            <p class="text-xs text-amber-700 mt-0.5">Pembayaran dilakukan setelah admin memvalidasi pesanan. DP minimal <strong>10% dari total</strong> (Rp <span x-text="Math.ceil(estimasiTotal * 0.1).toLocaleString('id-ID')"></span>) via transfer bank ke rekening di bawah.</p>
                        </div>
                    </div>
                </div>

                {{-- Info Rekening --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5 mt-4" x-show="bankAccounts.length > 0">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                        Pembayaran via Transfer Bank
                    </h4>
                    <div class="space-y-3">
                        <template x-for="(bank, idx) in bankAccounts" :key="idx">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900" x-text="bank.bank_name"></p>
                                    <p class="text-xs text-gray-500" x-text="'a.n. ' + (bank.account_name || '-')"></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-mono font-bold text-[#1a237e]" x-text="bank.account_number"></p>
                                    <button @click="copyRekening(bank.account_number)" class="p-1.5 hover:bg-gray-200 rounded-lg transition-colors" title="Salin nomor rekening">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">Setelah transfer, kirimkan bukti pembayaran melalui halaman konfirmasi atau chat dengan admin.</p>
                </div>
            </div>

            {{-- Right: Ringkasan Pesanan --}}
            <div>
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h3>
                    
                    <template x-if="mode === 'single'">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Jenis</span>
                                <span class="font-medium text-gray-900" x-text="jenis === 'custom' ? 'Custom' : 'Katalog'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tim</span>
                                <span class="font-medium text-gray-900" x-text="form.team_name || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Sponsor</span>
                                <span class="font-medium text-gray-900" x-text="form.detail_sponsor || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Kerah</span>
                                <span class="font-medium text-gray-900" x-text="form.kerah || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Bahan</span>
                                <span class="font-medium text-gray-900" x-text="form.bahan || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Jenis Potongan</span>
                                <span class="font-medium text-gray-900" x-text="form.jenis_potongan || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Lengan & Jahitan</span>
                                <span class="font-medium text-gray-900" x-text="form.lengan_jahitan || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Jumlah</span>
                                <span class="font-medium text-gray-900" x-text="totalQty + ' pcs'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Harga dasar</span>
                                <span class="font-medium text-gray-900" x-text="formatRupiah(hargaDasar)"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="mode === 'cart_checkout'">
                        <div class="space-y-4">
                            <template x-for="(item, idx) in cartItemsToCheckout" :key="idx">
                                <div class="text-sm border-b border-gray-100 pb-3 mb-3 last:border-0 last:pb-0 last:mb-0">
                                    <div class="font-bold text-gray-800 mb-1" x-text="'Produk ' + (idx + 1) + ': ' + (item.design_data ? item.design_data.team_name : item.product?.name)"></div>
                                    <div class="flex justify-between text-gray-500 text-xs">
                                        <span x-text="item.design_data ? (item.design_data.bahan + ' | ' + item.design_data.kerah) : item.size"></span>
                                        <span class="font-medium text-gray-900" x-text="item.qty + ' pcs'"></span>
                                    </div>
                                    <div class="flex justify-between text-gray-500 text-xs mt-1">
                                        <span>Subtotal</span>
                                        <span class="font-medium text-[#1a237e]" x-text="formatRupiah(item.design_data ? (item.qty * (item.design_data.base_price_per_pcs || 85000)) : (item.qty * item.product?.price))"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="mode === 'katalog_direct'">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Nama Produk</span>
                                <span class="font-medium text-gray-900" x-text="form.team_name || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Kategori</span>
                                <span class="font-medium text-gray-900" x-text="catalogProduct?.kategori || form.nama_artikel || '-'"></span>
                            </div>
                            <template x-if="form.customizations && Object.keys(form.customizations).length > 0">
                                <div class="contents">
                                    <template x-for="keyName in Object.keys(form.customizations)" :key="keyName">
                                        <div class="flex justify-between" x-show="form.customizations[keyName]">
                                            <span class="text-gray-500" x-text="keyName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                                            <span class="font-medium text-gray-900" x-text="form.customizations[keyName] || '-'"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Ukuran</span>
                                <span class="font-medium text-gray-900" x-text="form.size || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Jumlah</span>
                                <span class="font-medium text-gray-900" x-text="totalQty + ' pcs'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Harga per pcs</span>
                                <span class="font-medium text-gray-900" x-text="formatRupiah(basePricePerPcs)"></span>
                            </div>
                        </div>
                    </template>

                    <div class="space-y-3 text-sm mt-3 pt-3 border-t border-gray-100">
                        <template x-if="biayaPrioritas > 0">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Biaya prioritas</span>
                                <span class="font-medium text-gray-900" x-text="formatRupiah(biayaPrioritas)"></span>
                            </div>
                        </template>
                    </div>

                    <hr class="my-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-semibold">Total</span>
                        <span class="text-xl font-bold text-[#1a237e]" x-text="formatRupiah(estimasiTotal)"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-between gap-3 mt-8 pb-24">
            <button
                type="button"
                x-show="jenis === 'katalog'"
                @click="window.location.href = '/katalog/' + (katalogProductId || '')"
                class="w-full sm:w-auto px-5 py-2.5 sm:px-8 sm:py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors text-sm sm:text-base flex items-center justify-center"
            >
                Kembali ke Produk
            </button>
            <button
                type="button"
                x-show="jenis !== 'katalog'"
                @click="step = 2"
                class="w-full sm:w-auto px-5 py-2.5 sm:px-8 sm:py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors text-sm sm:text-base flex items-center justify-center"
            >
                Kembali
            </button>
            <button
                type="button"
                @click="mode === 'cart_checkout' ? submitCartCheckout() : submitOrder()"
                :disabled="!validateStep3 || loading"
                :class="(validateStep3 && !loading) ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                class="w-full sm:w-auto text-white px-5 py-2.5 sm:px-8 sm:py-3 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
            >
                <span x-show="!loading" class="inline-flex items-center gap-2">
                    Buat Pesanan
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </span>
                <span x-show="loading" class="inline-flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Memproses...
                </span>
            </button>
        </div>
    </div>

    {{-- Step 5: Konfirmasi --}}
    <div x-show="step === 4" x-cloak>
        <div class="text-center max-w-lg mx-auto py-4">
            {{-- Green Checkmark --}}
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center animate-[scaleIn_0.5s_ease-out]">
                    <svg class="w-10 h-10 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
            </div>

            <h2 class="text-xl font-bold text-green-600 mb-2" style="animation-delay:0.15s">Pesanan Berhasil Dibuat!</h2>
            <p class="text-gray-500 text-sm mb-8" style="animation-delay:0.3s">Tim kami akan segera memproses pesanan Anda. Pantau status pesanan melalui halaman Tracking.</p>

            {{-- Order ID --}}
            <div class="mb-6" style="animation-delay:0.45s">
                <p class="text-sm text-gray-500 mb-1">Order ID:</p>
                <div class="flex items-center justify-center gap-2">
                    <span class="text-lg font-mono font-bold text-gray-900 tracking-wider" x-text="orderNumber" id="orderNumber"></span>
                    <button
                        @click="copyOrderNumber"
                        class="text-gray-400 hover:text-blue-900 transition-colors p-1.5 rounded-lg hover:bg-blue-50"
                        title="Salin nomor pesanan"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                    </button>
                </div>
            </div>

            {{-- Ringkasan Pesanan --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 mb-8 text-left max-w-sm mx-auto" style="animation-delay:0.6s">
                <template x-if="mode === 'single'">
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tim</span>
                            <span class="font-medium text-gray-900" x-text="form.team_name || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Sponsor</span>
                            <span class="font-medium text-gray-900" x-text="form.detail_sponsor || '-'"></span>
                        </div>
                        <template x-if="form.customizations && Object.keys(form.customizations).length > 0">
                            <div class="contents">
                                <template x-for="keyName in Object.keys(form.customizations)" :key="keyName">
                                    <div class="flex justify-between" x-show="form.customizations[keyName]">
                                        <span class="text-gray-500" x-text="keyName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                                        <span class="font-medium text-gray-900" x-text="form.customizations[keyName] || '-'"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!form.customizations || Object.keys(form.customizations).length === 0">
                            <div class="contents">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Kerah</span>
                                    <span class="font-medium text-gray-900" x-text="form.kerah || '-'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Bahan</span>
                                    <span class="font-medium text-gray-900" x-text="form.bahan || '-'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Jenis Potongan</span>
                                    <span class="font-medium text-gray-900" x-text="form.jenis_potongan || '-'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Lengan & Jahitan</span>
                                    <span class="font-medium text-gray-900" x-text="form.lengan_jahitan || '-'"></span>
                                </div>
                            </div>
                        </template>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Jumlah</span>
                            <span class="font-medium text-gray-900" x-text="totalQty + ' pcs'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Prioritas</span>
                            <span class="font-medium text-gray-900 capitalize" x-text="prioritasText"></span>
                        </div>
                    </div>
                </template>

                <template x-if="mode === 'cart_checkout'">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Produk</span>
                            <span class="font-medium text-gray-900" x-text="cartItemsToCheckout.length + ' item'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Prioritas</span>
                            <span class="font-medium text-gray-900 capitalize" x-text="prioritasText"></span>
                        </div>
                    </div>
                </template>

                <template x-if="mode === 'katalog_direct'">
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Nama Produk</span>
                            <span class="font-medium text-gray-900" x-text="form.team_name || '-'"></span>
                        </div>
                        <template x-if="form.customizations && Object.keys(form.customizations).length > 0">
                            <div class="contents">
                                <template x-for="keyName in Object.keys(form.customizations)" :key="keyName">
                                    <div class="flex justify-between" x-show="form.customizations[keyName]">
                                        <span class="text-gray-500" x-text="keyName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                                        <span class="font-medium text-gray-900" x-text="form.customizations[keyName] || '-'"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Jumlah</span>
                            <span class="font-medium text-gray-900" x-text="totalQty + ' pcs'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Prioritas</span>
                            <span class="font-medium text-gray-900 capitalize" x-text="prioritasText"></span>
                        </div>
                    </div>
                </template>

                <hr class="my-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-semibold">Total</span>
                    <span class="text-lg font-bold text-blue-900" x-text="formatRupiah(estimasiTotal)"></span>
                </div>
            </div>

            {{-- Info Pembayaran DP --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6 text-left max-w-sm mx-auto" style="animation-delay:0.7s" x-show="bankAccounts.length > 0">
                <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    Pembayaran DP Minimal 10%
                </h4>
                <p class="text-xs text-gray-500 mb-3">Setelah admin memvalidasi pesanan, lakukan transfer DP minimal 10% ke salah satu rekening berikut:</p>
                <div class="space-y-2 mb-3">
                    <template x-for="(bank, idx) in bankAccounts" :key="idx">
                        <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg">
                            <span class="text-xs font-semibold text-gray-700" x-text="bank.bank_name"></span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-mono font-bold text-[#1a237e]" x-text="bank.account_number"></span>
                                <button @click="copyRekening(bank.account_number)" class="p-1 hover:bg-gray-200 rounded-lg transition-colors" title="Salin nomor rekening">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <p class="text-xs text-gray-400">a.n. <strong>Novos Jersey</strong></p>
            </div>

            {{-- Kirim Bukti Bayar ke WhatsApp --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6 text-left max-w-sm mx-auto" style="animation-delay:0.8s">
                <h4 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#25D366" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    Kirim Bukti Pembayaran
                </h4>
                <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                    Setelah melakukan transfer, silakan kirim foto bukti pembayaran (DP) Anda ke WhatsApp Admin untuk validasi dan konfirmasi pesanan Anda secara instan.
                </p>
                <a :href="'https://wa.me/{{ $adminWaPhone }}?text=' + encodeURIComponent('Halo Admin Novos, saya ingin mengirimkan bukti pembayaran transfer untuk pesanan dengan nomor: ' + orderNumber)" 
                   target="_blank" 
                   rel="noopener"
                   class="flex items-center justify-center gap-2 w-full py-2.5 bg-[#25D366] hover:bg-[#20ba5a] text-white rounded-lg text-xs font-bold transition-all shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.458 5.704 1.459h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    <span>Kirim Bukti via WhatsApp</span>
                </a>
            </div>

            {{-- Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-center" style="animation-delay:0.9s">
                <a :href="'/tracking?q=' + orderNumber" class="px-8 py-3 bg-[#1a237e] text-white rounded-lg font-semibold hover:bg-[#283593] transition-colors text-center">
                    Tracking Pesanan
                </a>
                <a href="{{ route('beranda') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors text-center">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

<style>
[x-cloak] { display: none !important; }

@keyframes scaleIn {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.animate-fade-slide {
    opacity: 1;
    animation: fadeSlideUp 0.5s ease-out;
}

/* SweetAlert2 toast — di bawah navbar (h-16 = 64px) */
.swal2-container.swal2-top-end,
.swal2-container.swal2-top-start,
.swal2-container.swal2-top {
    top: 72px !important;
}
</style>

<script>
function pemesananForm(catalogProduct = null, userAddresses = [], hasOrders = true, provinces = [], categories = [], bankAccounts = []) {
    return {
        step: 1,
        mode: 'single',
        cartItemsToCheckout: [],
        steps: ['Pilih Jenis & Kategori', 'Detail & Upload', 'Prioritas & Bayar', 'Konfirmasi'],
        jenis: null,
        jenisPhase: 1,
        catalogProduct: catalogProduct,
        katalogProductId: null,
        categories: categories,
        selectedCategoryId: '',
        subStep: 1,
        expandedSpecs: true,
        expandedBawahan: true,
        addresses: userAddresses,
        addressMode: 'select',
        selectedAddressId: null,
        provinces: provinces,
        regencies: [],
        districts: [],
        selectedProvinceId: '',
        selectedRegencyId: '',
        selectedDistrictId: '',
        // Modal state
        showContactModal: false,
        showAddressModal: false,
        contactSaving: false,
        addressEditSaving: false,
        modalSelectedProvinceId: '',
        modalSelectedRegencyId: '',
        modalSelectedDistrictId: '',
        modalRegencies: [],
        modalDistricts: [],
        contactInfo: {
            name: '{{ auth()->user()->name }}',
            fullname: '{{ auth()->user()->fullname ?? '' }}',
            email: '{{ auth()->user()->email }}',
            phone: '{{ auth()->user()->phone ?? '' }}'
        },
        formPhone: '{{ auth()->user()->phone ?? '' }}',
        contactForm: { name: '', fullname: '', email: '', phone: '' },
        editAddressForm: {
            first_name: '', last_name: '', province: '', city: '',
            district: '', detail_address: '', postal_code: '', address_type: 'rumah'
        },
        addressLoading: {
            provinces: false,
            regencies: false,
            districts: false,
            submit: false
        },
        addressForm: {
            first_name: '',
            last_name: '',
            province: '',
            city: '',
            district: '',
            detail_address: '',
            postal_code: '',
            address_type: 'rumah'
        },
        touched: {
            nama_pemesan: false,
            team_name: false,
        },
        form: {
            team_name: '',
            nama_artikel: '',
            nama_pemesan: '',
            detail_sponsor: '',
            kerah: '',
            bahan: '',
            jenis_potongan: '',
            lengan_jahitan: '',
            customizations: {}, // dynamic fields
            catatan: '',
            total_qty: 1,
            size: '',
        },
        oldGlobalSize: '',
        items: [],
        selectAll: false,
        selectionMode: 'single',
        lastCheckedIndex: null,
        bulkForm: {},
        showGuideAttr: null,
        showGuideModal: false,
        showOverrideModal: false,
        overrideForm: {},
        overrideSingleIndex: null,
        prioritas: 'normal',
        showUkuranRef: false,
        orderNumber: null,
        buktiBayarFile: null,
        loading: false,
        hasOrders: hasOrders,
        bankAccounts: bankAccounts,
        prioritasOptions: @json($dynamicPriorities),
        basePricePerPcs: 85000,

        init() {
            let restoredFromCheckout = false;
            const savedState = localStorage.getItem('checkout_state');
            if (savedState) {
                try {
                    restoredFromCheckout = true;
                    const state = JSON.parse(savedState);
                    this.mode = state.mode || 'single';
                    this.step = state.step;
                    this.subStep = (state.step === 2 && state.mode !== 'cart_checkout') ? 1 : (state.subStep || 1);
                    this.prioritas = state.prioritas || 'normal';
                    this.selectedAddressId = state.selectedAddressId || null;

                    if (this.mode === 'cart_checkout') {
                        this.cartItemsToCheckout = state.cartItems || [];
                        this.jenis = 'custom'; // For cart it handles both, but step 1 is skipped
                    } else if (this.mode === 'katalog_direct' && state.katalogItem) {
                        const item = state.katalogItem;
                        this.jenis = 'katalog';
                        this.katalogProductId = state.katalogProductId || item.product_id || null;
                        this.basePricePerPcs = parseInt(item.price) || 85000;
                        this.form.total_qty = item.qty || 1;
                        this.form.size = item.size || '';
                        this.form.team_name = item.name || '';
                        this.form.nama_artikel = item.category || 'Katalog';
                        this.form.nama_pemesan = '{{ auth()->user()->name }}';

                        const cat = this.categories.find(c => c.name.toLowerCase() === (item.category || '').toLowerCase());
                        if (cat) {
                            this.selectedCategoryId = cat.id;
                            this.onCategoryChange();
                        }
                    } else {
                        this.jenis = state.jenis;
                        this.form = state.form || this.form;
                        if (state.selectedCategoryId) {
                            this.selectedCategoryId = state.selectedCategoryId;
                        }
                        if (state.items) {
                            this.items = state.items;
                        }
                    }
                    
                    localStorage.removeItem('checkout_state');
                } catch (e) {
                    console.error('Gagal memuat state checkout', e);
                }
            } else {
                if (this.catalogProduct) {
                    this.jenis = 'katalog';
                    this.katalogProductId = this.catalogProduct.id || null;
                    if (this.catalogProduct.harga) {
                        this.basePricePerPcs = parseInt(this.catalogProduct.harga);
                    }
                    const cat = this.categories.find(c => c.name.toLowerCase() === (this.catalogProduct.kategori || '').toLowerCase());
                    if (cat) {
                        this.selectedCategoryId = cat.id;
                        this.onCategoryChange();
                        if (this.catalogProduct.kerah) this.form.customizations['kerah'] = this.catalogProduct.kerah;
                        if (this.catalogProduct.bahan) this.form.customizations['bahan'] = this.catalogProduct.bahan;
                        if (this.catalogProduct.jenis_potongan) this.form.customizations['jenis_potongan'] = this.catalogProduct.jenis_potongan;
                        if (this.catalogProduct.lengan_jahitan) this.form.customizations['lengan_jahitan'] = this.catalogProduct.lengan_jahitan;
                    }
                    this.form.size = this.catalogProduct.ukuran || this.form.size || '';
                    this.step = 3;
                }
            }

            if (this.addresses && this.addresses.length > 0) {
                this.addressMode = 'select';
                if (!this.selectedAddressId) {
                    const primary = this.addresses.find(a => a.is_primary) || this.addresses[0];
                    this.selectedAddressId = primary.id;
                }
            } else {
                this.addressMode = 'create';
            }

            // Watch total_qty to update items table rows
            this.$watch('form.total_qty', (val) => {
                this.updateItemsRows(val);
            });
            // Watch category change to reset and align schemas on all item rows
            this.$watch('selectedCategoryId', () => {
                this.updateItemsRows(this.form.total_qty);
            });

            this.updateItemsRows(this.form.total_qty);

            this.setupBuktiBayarPond();

            // Restore auto-save draft (only if not restored from checkout_state)
            const restoreDraft = () => {
                if (restoredFromCheckout) return;
                const draft = localStorage.getItem('pesanan_draft');
                if (!draft) return;
                let data;
                try {
                    data = JSON.parse(draft);
                } catch(e) {
                    console.warn('Gagal membaca draft', e);
                    localStorage.removeItem('pesanan_draft');
                    return;
                }
                // Draft expired jika lebih dari 7 hari — clear silently
                const DRAFT_EXPIRY_MS = 3 * 24 * 60 * 60 * 1000;
                if (new Date() - new Date(data.savedAt) > DRAFT_EXPIRY_MS) {
                    localStorage.removeItem('pesanan_draft');
                    return;
                }
                const savedTime = new Date(data.savedAt);
                const now = new Date();
                const diffMin = Math.floor((now - savedTime) / 60000);

                const askRestore = () => {
                    if (typeof window.Swal !== 'undefined') {
                        window.Swal.fire({
                            icon: 'question',
                            title: 'Lanjutkan Pesanan Sebelumnya?',
                            html: 'Kami menemukan data pesanan yang belum selesai dari ' + (diffMin > 0 ? diffMin + ' menit yang lalu' : 'beberapa saat yang lalu') + '.<br><br>Apakah kamu ingin melanjutkan?',
                            showConfirmButton: true,
                            confirmButtonText: '<svg class="w-4 h-4 inline mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg> Lanjutkan',
                            confirmButtonColor: '#1a237e',
                            showCancelButton: true,
                            cancelButtonText: 'Mulai Baru',
                            cancelButtonColor: '#6b7280',
                        }).then(result => {
                            if (result.isConfirmed) {
                                this.restoreDraft(data);
                            } else {
                                this.clearDraft();
                            }
                        });
                    } else {
                        // Fallback: restore langsung tanpa konfirmasi
                        this.restoreDraft(data);
                    }
                };

                // Tunggu Swal tersedia (maks 5 detik)
                if (typeof window.Swal !== 'undefined') {
                    askRestore();
                } else {
                    let tries = 0;
                    const waitSwal = setInterval(() => {
                        tries++;
                        if (typeof window.Swal !== 'undefined' || tries >= 50) {
                            clearInterval(waitSwal);
                            askRestore();
                        }
                    }, 100);
                }
            };

            this.$nextTick(restoreDraft);

            // Ensure selected prioritas is valid based on settings
            this.$nextTick(() => {
                if (this.prioritasOptions.length > 0 && !this.prioritasOptions.some(o => o.value === this.prioritas)) {
                    this.prioritas = this.prioritasOptions[0].value;
                }
            });

            // Re-render icons setelah render ulang
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons({ icons: window.lucide.icons });
            });

            // Auto-save setiap kali ada perubahan form (debounce 800ms)
            let saveTimer;
            const triggerSave = () => {
                clearTimeout(saveTimer);
                saveTimer = setTimeout(() => {
                    if (this.step < 4) this.saveDraft();
                }, 800);
            };
            this.$watch('form.nama_pemesan', triggerSave);
            this.$watch('form.team_name', triggerSave);
            this.$watch('form.nama_artikel', triggerSave);
            this.$watch('form.detail_sponsor', triggerSave);
            this.$watch('form.total_qty', triggerSave);
            this.$watch('form.size', triggerSave);
            this.$watch('prioritas', triggerSave);
            this.$watch('selectedAddressId', triggerSave);
            this.$watch('formPhone', triggerSave);
            // Fallback: save saat ada perubahan di form (termasuk items & customizations)
            document.addEventListener('change', (e) => {
                if (this.step < 4 && e.target.closest('[x-data^="pemesananForm"]')) {
                    clearTimeout(saveTimer);
                    saveTimer = setTimeout(() => this.saveDraft(), 800);
                }
            }, true);

            // Backup: auto-save tiap 3 detik
            this._draftTimer = setInterval(() => {
                if (this.step < 4) this.saveDraft();
            }, 3000);

            // Save draft saat pindah halaman
            const pageUnload = () => this.saveDraft();
            window.addEventListener('beforeunload', pageUnload);
            window.addEventListener('pagehide', pageUnload);
        },

        setupBuktiBayarPond() {
            const el = document.querySelector('#pondBuktiBayar');
            if (!el) return;
            const pond = FilePond.find(el) || FilePond.create(el);
            pond.on('addfile', (err, fileItem) => {
                if (!err && this.orderNumber && fileItem.file instanceof File) {
                    this.uploadPaymentProof(this.orderNumber, fileItem.file);
                }
            });
        },

        onGlobalSizeChange() {
            const newSize = this.form.size;
            const oldSize = this.oldGlobalSize;
            this.items.forEach(item => {
                if (item.size === oldSize) {
                    item.size = newSize;
                }
            });
            this.oldGlobalSize = newSize;
        },

        onCategoryChange() {
            this.form.customizations = {};
            const schema = this.activeSchema;
            if (schema) {
                schema.forEach(attr => {
                    this.form.customizations[attr.id] = '';
                });
            }
            this.updateItemsRows(this.form.total_qty);
        },

        updateItemsRows(val) {
            const count = parseInt(val) || 1;
            
            if (this.items.length > count) {
                this.items = this.items.slice(0, count);
            } else {
                const diff = count - this.items.length;
                for (let i = 0; i < diff; i++) {
                    this.items.push({
                        selected: false,
                        no: '',
                        nama: '',
                        size: this.form.size || '',
                        customizations: {}
                    });
                }
            }
        },

        isAttrActive(attr, item) {
            if (!attr.depends_on || !attr.depends_on.attribute_id) return true;
            const parentVal = item.customizations[attr.depends_on.attribute_id];
            return parentVal === attr.depends_on.value;
        },

        toggleSelectAll() {
            this.items.forEach(item => item.selected = this.selectAll);
            this.lastCheckedIndex = null;
        },

        handleCheck(event, index) {
            let isChecked = event.target.checked;
            if (this.selectionMode === 'range' || event.shiftKey) {
                if (this.lastCheckedIndex !== null) {
                    let start = Math.min(this.lastCheckedIndex, index);
                    let fontEnd = Math.max(this.lastCheckedIndex, index);
                    for (let i = start; i <= fontEnd; i++) { this.items[i].selected = isChecked; }
                } else { this.items[index].selected = isChecked; }
            } else { this.items[index].selected = isChecked; }
            this.lastCheckedIndex = index;
        },

        countSelected() { 
            return this.items.filter(item => item.selected).length; 
        },

        clearSelection() { 
            this.items.forEach(item => item.selected = false); 
            this.selectAll = false; 
            this.lastCheckedIndex = null; 
        },

        getOverrideCount(item) {
            if (!item.customizations) return 0;
            return Object.keys(item.customizations).filter(k => {
                const val = item.customizations[k];
                return val !== undefined && val !== null && val.toString().trim() !== '';
            }).length;
        },

        selectCategoryByName(name) {
            if (this.catalogProduct) return;
            const cat = this.categories.find(c => c.name.toLowerCase() === name.toLowerCase());
            if (cat) {
                this.selectedCategoryId = cat.id;
                this.onCategoryChange();
            }
        },

        getCategoryIdByName(name) {
            const cat = this.categories.find(c => c.name.toLowerCase() === name.toLowerCase());
            return cat ? cat.id : null;
        },

        isRowComplete(item) {
            if (!item.size) return false;

            const schema = this.activeSchema;
            const compiled = Object.assign({}, this.form.customizations, item.customizations);
            const allOk = schema.every(attr => {
                if (!attr.required) return true;
                if (attr.depends_on && attr.depends_on.attribute_id) {
                    const parentVal = compiled[attr.depends_on.attribute_id];
                    if (parentVal !== attr.depends_on.value) return true;
                }
                const val = compiled[attr.id];
                return val !== undefined && val !== null && val.toString().trim() !== '';
            });
            if (!allOk) return false;

            return true;
        },

        getCompiledCustomizations(item) {
            const compiled = Object.assign({}, this.form.customizations, item.customizations);
            const schema = this.activeSchema;
            
            const result = [];
            
            schema.forEach(attr => {
                if (this.isAttrActive(attr, item)) {
                    const val = compiled[attr.id];
                    if (val) {
                        result.push({
                            key: attr.id,
                            value: val
                        });
                    }
                }
            });
            
            return result;
        },

        openOverrideModal(index = null) {
            this.overrideSingleIndex = index;
            this.overrideForm = {};
            
            this.activeSchema.forEach(attr => {
                this.overrideForm[attr.id] = '';
            });

            if (index !== null) {
                const item = this.items[index];
                if (item.customizations) {
                    this.activeSchema.forEach(attr => {
                        this.overrideForm[attr.id] = item.customizations[attr.id] || '';
                    });
                }
            }
            this.showOverrideModal = true;
        },

        saveOverrides() {
            this.items.forEach((item, index) => {
                if (item.selected || index === this.overrideSingleIndex) {
                    if (!item.customizations) item.customizations = {};
                    this.activeSchema.forEach(attr => {
                        const val = this.overrideForm[attr.id];
                        if (val !== undefined && val !== null && val.toString().trim() !== '') {
                            item.customizations[attr.id] = val;
                        } else {
                            delete item.customizations[attr.id];
                        }
                    });
                }
            });
            this.showOverrideModal = false;
            this.overrideForm = {};
            this.clearSelection();
        },

        resetSelectedOverrides() {
            this.items.forEach(item => {
                if (item.selected) {
                    item.customizations = {};
                }
            });
            this.clearSelection();
        },

        applyBulkSize() {
            if (!this.bulkForm.size) return;
            this.items.forEach(item => {
                if (item.selected) {
                    item.size = this.bulkForm.size;
                }
            });
            this.clearSelection();
            this.bulkForm = {};
        },

        isOverrideAttrActive(attr) {
            if (!attr.depends_on || !attr.depends_on.attribute_id) return true;
            const parentVal = this.overrideForm[attr.depends_on.attribute_id] !== undefined && this.overrideForm[attr.depends_on.attribute_id] !== ''
                ? this.overrideForm[attr.depends_on.attribute_id]
                : this.form.customizations[attr.depends_on.attribute_id];
            return parentVal === attr.depends_on.value;
        },

        showAttrGuide(attr) {
            this.showGuideAttr = attr;
            this.showGuideModal = true;
        },

        get selectedCategory() {
            return this.categories.find(c => c.id == this.selectedCategoryId);
        },
        get selectedCategoryName() {
            const cat = this.selectedCategory;
            return cat ? cat.name : '';
        },
        get showTeamNameField() {
            if (!this.selectedCategoryId) return true;
            const cat = this.selectedCategory;
            return cat && cat.form_config ? !!cat.form_config.show_team_name : true;
        },
        get showNamaArtikelField() {
            if (!this.selectedCategoryId) return true;
            const cat = this.selectedCategory;
            return cat && cat.form_config ? !!cat.form_config.show_nama_artikel : true;
        },
        get showDetailSponsorField() {
            if (!this.selectedCategoryId) return true;
            const cat = this.selectedCategory;
            return cat && cat.form_config ? !!cat.form_config.show_detail_sponsor : true;
        },
        get showPlayerListField() {
            if (!this.selectedCategoryId) return true;
            const cat = this.selectedCategory;
            return cat && cat.form_config ? (cat.form_config.show_player_list !== false && cat.form_config.show_player_list !== 0 && cat.form_config.show_player_list !== '0') : true;
        },
        get formFieldsGridClass() {
            const hasRightCol = this.showTeamNameField || this.showDetailSponsorField;
            return hasRightCol ? 'grid lg:grid-cols-2 gap-6' : 'grid grid-cols-1 gap-6';
        },
        get activeSchema() {
            const cat = this.categories.find(c => c.id == this.selectedCategoryId);
            return (cat && cat.attributes_schema) ? cat.attributes_schema : [];
        },
        
        get sizeOptions() {
            const schema = this.activeSchema;
            if (!schema) return [];
            const sizeAttr = schema.find(attr => attr.system_tag === 'is_size_type');
            return sizeAttr ? (sizeAttr.options || []) : [];
        },

        saveCheckoutState() {
            const state = {
                mode: this.mode,
                jenis: this.jenis,
                step: this.step,
                subStep: this.subStep,
                form: this.form,
                prioritas: this.prioritas,
                selectedAddressId: this.selectedAddressId,
                cartItems: this.cartItemsToCheckout
            };
            localStorage.setItem('checkout_state', JSON.stringify(state));
            sessionStorage.setItem('from_checkout', 'true');
        },



        get totalQty() {
            return parseInt(this.form.total_qty) || 1;
        },

        get selectedAddress() {
            if (!this.addresses) return null;
            return this.addresses.find(a => a.id === this.selectedAddressId) || this.addresses.find(a => a.is_primary) || this.addresses[0] || null;
        },

        get prioritasText() {
            const p = this.prioritasOptions.find(o => o.value === this.prioritas);
            return p ? p.label : '';
        },

        get basePriceFromCategory() {
            if (this.jenis === 'katalog') {
                return this.basePricePerPcs;
            }
            return this.selectedCategory?.base_price ? Number(this.selectedCategory.base_price) : 85000;
        },

        get totalAttrModifier() {
            let total = 0;
            const schema = this.activeSchema;
            schema.forEach(attr => {
                if (attr.depends_on && attr.depends_on.attribute_id) return;
                const val = this.form.customizations[attr.id];
                const opt = (attr.options || []).find(o => o.value === val);
                total += Number(opt?.price_modifier || 0);
            });
            return total;
        },

        getItemModifier(item) {
            let total = 0;
            const compiled = Object.assign({}, this.form.customizations, item.customizations || {});
            
            // Modifier for custom attributes
            this.activeSchema.forEach(attr => {
                if (attr.system_tag === 'is_size_type') return; // handled separately
                if (attr.depends_on && attr.depends_on.attribute_id) {
                    const parentVal = compiled[attr.depends_on.attribute_id];
                    if (parentVal !== attr.depends_on.value) return;
                }
                const val = compiled[attr.id];
                if (!val) return;
                const opt = (attr.options || []).find(o => o.value === val);
                total += Number(opt?.price_modifier || 0);
            });
            
            // Modifier for size
            if (item.size) {
                const sizeAttr = this.activeSchema.find(attr => attr.system_tag === 'is_size_type');
                if (sizeAttr && sizeAttr.options) {
                    const sizeOpt = sizeAttr.options.find(o => o.value === item.size);
                    if (sizeOpt) {
                        total += Number(sizeOpt.price_modifier || 0);
                    }
                }
            }
            
            return total;
        },

        get hargaPerJersey() {
            return this.basePriceFromCategory + this.totalAttrModifier;
        },

        getSelectedOptionPrice(attr) {
            const val = attr.system_tag === 'is_size_type' ? this.form.size : this.form.customizations[attr.id];
            if (!val) return 0;
            const opt = (attr.options || []).find(o => o.value === val);
            return Number(opt?.price_modifier || 0);
        },

        get hargaDasar() {
            if (this.mode === 'cart_checkout') {
                return this.cartItemsToCheckout.reduce((sum, item) => {
                    if (item.design_data) {
                        return sum + (item.qty * (item.design_data.base_price_per_pcs || 85000));
                    }
                    return sum + (item.qty * (item.product?.price || 0));
                }, 0);
            }
            const base = this.basePriceFromCategory;
            return this.items.reduce((sum, item) => {
                return sum + base + this.getItemModifier(item);
            }, 0);
        },

        get biayaPrioritas() {
            const opt = this.prioritasOptions.find(o => o.value === this.prioritas);
            return opt ? parseInt(opt.biaya) : 0;
        },

        get activeSizePdf() {
            const map = {
                'REGULER': '/images/referensi-ukuran/REGCUT-NVS-2026.png',
                'SLIMFIT CEWE': '/images/referensi-ukuran/WMNSLMCUT-NVS-2026.png',
                'OVERSIZE': '/images/referensi-ukuran/OVRCUT-NVS-2026.png',
                'TUNIK': '/images/referensi-ukuran/TUNIKCUT-NVS-2026.png',
                'SLIM FIT UNISEX': '/images/referensi-ukuran/UNISEXSLMCUT-NVS-2026.png',
                'BOXY CUT': '/images/referensi-ukuran/BOXYCUT-NVS-2026.png',
                'KIDS': '/images/referensi-ukuran/KIDSCUT-NVS-2026.png',
            };
            return map[this.form.jenis_potongan] || '/images/referensi-ukuran/REGCUT-NVS-2026.pdf';
        },

        get estimasiTotal() {
            return this.hargaDasar + this.biayaPrioritas;
        },

        get validateStep2() {
            const isTeam = this.showTeamNameField;
            const basic = this.form.nama_pemesan.trim() !== '' && (!isTeam || this.form.team_name.trim() !== '') && this.selectedCategoryId !== '' && this.totalQty >= 1;
            if (!basic) return false;
            
            const schema = this.activeSchema;

            return this.items.every(item => {
                if (!item.size) return false;
                
                const compiled = Object.assign({}, this.form.customizations, item.customizations);
                
                // Check all attributes (jersey + bawahan via depends_on)
                const allOk = schema.every(attr => {
                    if (!attr.required) return true;
                    if (attr.depends_on && attr.depends_on.attribute_id) {
                        const parentVal = compiled[attr.depends_on.attribute_id];
                        if (parentVal !== attr.depends_on.value) return true;
                    }
                    const val = compiled[attr.id];
                    return val !== undefined && val !== null && val.toString().trim() !== '';
                });
                if (!allOk) return false;

                return true;
            });
        },

        get validateStep3() {
            return true;
        },

        nextFromStep2() {
            this.subStep = 2;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (this.addresses && this.addresses.length > 0) {
                this.addressMode = 'select';
                if (!this.selectedAddressId) {
                    const primary = this.addresses.find(a => a.is_primary) || this.addresses[0];
                    this.selectedAddressId = primary.id;
                }
            } else {
                this.addressMode = 'create';
            }
        },

        backFromSubStep2() {
            this.subStep = 1;
        },

        addNewAddressMode() {
            this.addressMode = 'create';
            this.addressForm = {
                first_name: '',
                last_name: '',
                province: '',
                city: '',
                district: '',
                detail_address: '',
                postal_code: '',
                address_type: 'rumah'
            };
            this.selectedProvinceId = '';
            this.selectedRegencyId = '';
            this.selectedDistrictId = '';
        },

        useSelectedAddress() {
            this.step = 3;
        },

        openContactModal() {
            this.saveCheckoutState();
            window.location.href = '/profile?tab=pengaturan';
        },

        openAddressModal() {
            this.saveCheckoutState();
            window.location.href = '/profile?tab=alamat';
        },

        fetchRegencies() {
            if (!this.selectedProvinceId) return;
            this.addressLoading.regencies = true;
            this.regencies = [];
            this.districts = [];
            this.addressForm.city = '';
            this.addressForm.district = '';
            this.selectedRegencyId = '';
            this.selectedDistrictId = '';
            fetch(`/api/wilayah/regencies/${this.selectedProvinceId}`)
                .then(res => res.json())
                .then(data => {
                    this.regencies = data;
                    this.addressLoading.regencies = false;
                })
                .catch(err => {
                    console.error('Gagal mengambil data kabupaten/kota', err);
                    this.addressLoading.regencies = false;
                });
        },

        fetchDistricts() {
            if (!this.selectedRegencyId) return;
            this.addressLoading.districts = true;
            this.districts = [];
            this.addressForm.district = '';
            this.selectedDistrictId = '';
            fetch(`/api/wilayah/districts/${this.selectedRegencyId}`)
                .then(res => res.json())
                .then(data => {
                    this.districts = data;
                    this.addressLoading.districts = false;
                })
                .catch(err => {
                    console.error('Gagal mengambil data kecamatan', err);
                    this.addressLoading.districts = false;
                });
        },

        get validateAddress() {
            return this.addressForm.first_name.trim() !== '' &&
                   this.addressForm.province !== '' &&
                   this.addressForm.city !== '' &&
                   this.addressForm.district !== '' &&
                   this.addressForm.detail_address.trim() !== '' &&
                   this.addressForm.postal_code.trim() !== '';
        },

        saveAddress() {
            if (this.addressLoading.submit) return;
            this.addressLoading.submit = true;

            fetch('{{ route('address.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.addressForm)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (!this.addresses) {
                        this.addresses = [];
                    }
                    this.addresses.push(data.address);
                    this.selectedAddressId = data.address.id;
                    this.addressMode = 'select';
                    Swal.fire({
                        icon: 'success',
                        title: 'Alamat Disimpan',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    this.step = 3;
                } else {
                    throw new Error(data.message || 'Gagal menyimpan alamat');
                }
                this.addressLoading.submit = false;
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message || 'Terjadi kesalahan'
                });
                this.addressLoading.submit = false;
            });
        },

        formatRupiah(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        },

        uploadPaymentProof(orderNumber, file) {
            if (!file) return;

            const formData = new FormData();
            formData.append('payment_proof', file);

            fetch('/pesan/' + orderNumber + '/payment-proof', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.buktiBayarFile = file;
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Bukti pembayaran berhasil diupload. Admin akan segera memvalidasi.',
                        confirmButtonColor: '#1a237e',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal upload bukti pembayaran.',
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan. Silakan coba lagi atau kirim via chat.',
                });
            });
        },

        submitOrder() {
            if (this.loading) return;
            this.loading = true;

            // Generate catatan legacy from items array
            this.form.catatan = this.items.map(item => {
                const compiled = Object.assign({}, this.form.customizations, item.customizations);
                const customParts = Object.entries(compiled)
                    .filter(([k, v]) => v !== undefined && v !== null && v !== '')
                    .map(([k, v]) => v)
                    .join(', ');
                const modelLengan = compiled.lengan_jahitan || compiled.model_lengan || item.model_lengan || '-';
                return `${item.no || '-'}, ${item.nama || '-'}, ${modelLengan}, ${item.size || 'M'}${customParts ? ', ' + customParts : ''}`;
            }).join('\n');

            const formData = new FormData();
            formData.append('team_name', this.form.team_name);
            formData.append('nama_artikel', this.form.nama_artikel);
            formData.append('nama_pemesan', this.form.nama_pemesan);
            formData.append('detail_sponsor', this.form.detail_sponsor);
            // Dynamic customizations JSON
            formData.append('customizations', JSON.stringify(this.form.customizations || {}));
            formData.append('catatan', this.form.catatan);
            formData.append('total_qty', this.form.total_qty || 1);
            formData.append('prioritas', this.prioritas);
            formData.append('phone', this.formPhone);
            if (this.selectedAddressId) {
                formData.append('address_id', this.selectedAddressId);
            }
            if (this.selectedCategoryId) {
                formData.append('category_id', this.selectedCategoryId);
            }

            // Append structured items array
            this.items.forEach((item, index) => {
                const compiled = Object.assign({}, this.form.customizations, item.customizations);
                formData.append(`items[${index}][no]`, item.no || '');
                formData.append(`items[${index}][nama]`, item.nama || '');
                formData.append(`items[${index}][size]`, item.size || 'M');
                formData.append(`items[${index}][customizations]`, JSON.stringify(compiled));
            });

            // Logo tim (logo_files) & Referensi Desain (design_files) dari FilePond
            const pondLogo = FilePond.find(document.querySelector('#pondLogo'));
            if (pondLogo) {
                pondLogo.getFiles().forEach(f => {
                    if (f.file instanceof File) formData.append('logo_files[]', f.file, f.file.name);
                });
            }
            const pondRef = FilePond.find(document.querySelector('#pondRef'));
            if (pondRef) {
                pondRef.getFiles().forEach(f => {
                    if (f.file instanceof File) formData.append('design_files[]', f.file, f.file.name);
                });
            }

            fetch('{{ route('pesan.store') }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData,
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => { throw new Error(text.substring(0, 150)); });
                }
                return res.json();
            })
            .then(data => {
                if (!data.success) throw new Error('Gagal membuat pesanan');
                this.orderNumber = data.orderNumber;
                this.step = 4;
                this.loading = false;
                this.clearDraft();
                this.$nextTick(() => this.setupBuktiBayarPond());
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Oops...', text: err.message || 'Terjadi kesalahan' });
                this.loading = false;
            });
        },

        submitCartCheckout() {
            if (this.loading) return;
            this.loading = true;

            const payload = {
                cart_item_ids: this.cartItemsToCheckout.map(i => i.id),
                prioritas: this.prioritas,
                address_id: this.selectedAddressId
            };

            fetch('{{ route('pesan.store-cart') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => { throw new Error(text.substring(0, 150)); });
                }
                return res.json();
            })
            .then(data => {
                if (!data.success) throw new Error('Gagal memproses checkout keranjang');
                this.orderNumber = data.orderNumber;
                this.step = 4;
                this.loading = false;
                this.clearDraft();
                this.$nextTick(() => this.setupBuktiBayarPond());
                const store = window.Alpine.store('summary');
                if (store) store.fetch();
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Oops...', text: err.message || 'Terjadi kesalahan' });
                this.loading = false;
            });
        },

        copyOrderNumber() {
            navigator.clipboard.writeText(this.orderNumber).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin!',
                    text: 'Nomor pesanan berhasil disalin',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        },

        addToCart() {
            if (this.loading) return;
            this.loading = true;

            const specKeys = ['kerah', 'bahan', 'jenis_potongan', 'lengan_jahitan'];

            // Build compiled items first
            const compiledItems = this.items.map(item => {
                const compiled = Object.assign({}, this.form.customizations, item.customizations);
                return {
                    no: item.no,
                    nama: item.nama,
                    size: item.size,
                    model_lengan: compiled.lengan_jahitan || compiled.model_lengan || item.model_lengan || '-',
                    customizations: compiled
                };
            });

            // Compute majority values for spec attributes
            const majority = {};
            specKeys.forEach(key => {
                const counts = {};
                compiledItems.forEach(item => {
                    const val = item.customizations[key] || '';
                    if (val) {
                        counts[val] = (counts[val] || 0) + 1;
                    }
                });
                let maxCount = 0;
                let maxVal = '';
                Object.entries(counts).forEach(([val, count]) => {
                    if (count > maxCount) {
                        maxCount = count;
                        maxVal = val;
                    }
                });
                majority[key] = maxVal;
            });

            // Generate catatan legacy — only include minority customizations in keterangan
            this.form.catatan = compiledItems.map(item => {
                const customParts = Object.entries(item.customizations)
                    .filter(([k, v]) => {
                        if (v === undefined || v === null || v === '') return false;
                        if (specKeys.includes(k) && majority[k] && v === majority[k]) return false;
                        return true;
                    })
                    .map(([k, v]) => v)
                    .join(', ');
                return `${item.no || '-'}, ${item.nama || '-'}, ${item.model_lengan || '-'}, ${item.size || 'M'}${customParts ? ', ' + customParts : ''}`;
            }).join('\n');

            const getFirstImage = () => {
                const pond = FilePond.find(document.querySelector('#pondLogo'));
                if (pond && pond.getFiles().length > 0) {
                    const f = pond.getFiles()[0];
                    return f.file instanceof File ? f.file.name : null;
                }
                return null;
            };

            const payload = {
                team_name: this.form.team_name,
                notes: this.form.catatan,
                image: getFirstImage(),
                design_data: {
                    team_name: this.form.team_name,
                    nama_artikel: this.form.nama_artikel,
                    nama_pemesan: this.form.nama_pemesan,
                    detail_sponsor: this.form.detail_sponsor,
                    kerah: majority.kerah || '',
                    bahan: majority.bahan || '',
                    jenis_potongan: majority.jenis_potongan || '',
                    lengan_jahitan: majority.lengan_jahitan || '',
                    customizations: this.form.customizations || {},
                    size_price_modifiers: this.sizeOptions.reduce((acc, opt) => {
                        acc[opt.value] = Number(opt.price_modifier || 0);
                        return acc;
                    }, {}),
                    items: compiledItems,
                    catatan: this.form.catatan,
                    total_qty: this.form.total_qty,
                    prioritas: this.prioritas,
                    base_price_per_pcs: this.basePricePerPcs,
                    biaya_prioritas: this.biayaPrioritas,
                    estimasi_total: this.estimasiTotal,
                    jenis: this.jenis,
                }
            };

            fetch('{{ route('cart.store-design') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload),
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Disimpan ke Keranjang!',
                        text: 'Pesanan berhasil disimpan ke keranjang. Anda bisa check out nanti.',
                        showCancelButton: true,
                        confirmButtonColor: '#1a237e',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Lihat Keranjang',
                        cancelButtonText: 'Lanjut Belanja',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('profile.edit') }}?tab=keranjang';
                        } else {
                            this.step = 1;
                            this.resetForm();
                        }
                    });
                } else {
                    throw new Error(data.message || 'Gagal menyimpan ke keranjang');
                }
            })
            .catch(err => {
                this.loading = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message || 'Terjadi kesalahan'
                });
            });
        },

        resetForm() {
            this.jenis = null;
            this.jenisPhase = 1;
            this.selectedCategoryId = '';
            this.form = {
                team_name: '',
                nama_artikel: '',
                nama_pemesan: '',
                detail_sponsor: '',
                kerah: '',
                bahan: '',
                jenis_potongan: '',
                lengan_jahitan: '',
                customizations: {},
                catatan: '',
                total_qty: 1,
            };
            ['pondLogo', 'pondRef'].forEach(id => {
                const pond = FilePond.find(document.querySelector('#' + id));
                if (pond) pond.removeFiles();
            });
            this.prioritas = 'normal';
            this.selectedAddressId = null;
            this.clearDraft();
        },

        showFirstOrderAlert() {
            Swal.fire({
                icon: 'success',
                iconColor: '#25d366',
                title: '\uD83D\uDCAC Konsultasi via WhatsApp Dulu Yuk!',
                html: '<p class="text-sm text-gray-600">Karena ini pemesanan pertamamu, yuk brief admin Novos terlebih dahulu agar jersey yang dihasilkan sesuai ekspektasi kamu.</p>',
                confirmButtonColor: '#25d366',
                confirmButtonText: '\uD83D\uDCF1 Chat Admin Sekarang',
                showCancelButton: true,
                cancelButtonColor: '#6b7280',
                cancelButtonText: 'Saya Sudah Konsultasi',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open('https://wa.me/{{ $adminPhone }}?text={{ urlencode("Halo Novos, saya ingin konsultasi pesanan produk custom") }}', '_blank');
                } else {
                    this.jenisPhase = 2;
                }
            });
        },

        copyRekening(norek) {
            const norekBersih = norek.replace(/\s+/g, '');
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });

            const berhasil = () => Toast.fire({ icon: 'success', title: 'Nomor rekening tersalin!', text: norekBersih });
            const gagal   = () => Toast.fire({ icon: 'error',   title: 'Gagal menyalin', text: 'Salin manual: ' + norekBersih });

            // Gunakan Clipboard API jika tersedia (HTTPS / localhost)
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(norekBersih).then(berhasil).catch(() => {
                    // Fallback jika ditolak permission
                    fallbackCopy(norekBersih) ? berhasil() : gagal();
                });
            } else {
                // Fallback execCommand untuk HTTP / Laragon local
                fallbackCopy(norekBersih) ? berhasil() : gagal();
            }

            function fallbackCopy(text) {
                const el = document.createElement('textarea');
                el.value = text;
                el.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0';
                document.body.appendChild(el);
                el.focus();
                el.select();
                let ok = false;
                try { ok = document.execCommand('copy'); } catch(e) {}
                document.body.removeChild(el);
                return ok;
            }
        },

        openBawahanGallery(index) {
            window.openPhotoSwipe([
                {
                    src: '/images/referensi-ukuran/TRAININGLONG.png',
                    width: 2345,
                    height: 1660,
                    alt: 'Celana Panjang'
                },
                {
                    src: '/images/referensi-ukuran/TRAININGSHORT.png',
                    width: 2345,
                    height: 1660,
                    alt: 'Celana Pendek'
                },
                {
                    src: '/images/referensi-ukuran/TRAININGLONG.png',
                    width: 2345,
                    height: 1660,
                    alt: 'Rok'
                },
            ], index)
        },

        saveDraft() {
            if (this.step >= 4 || (!this.jenis && this.step <= 1)) return;
            try {
                const draft = {
                    jenis: this.jenis || '',
                    jenisPhase: this.jenisPhase,
                    selectedCategoryId: this.selectedCategoryId,
                    step: this.step,
                    subStep: this.subStep,
                    form: JSON.parse(JSON.stringify(this.form)),
                    items: JSON.parse(JSON.stringify(this.items)),
                    prioritas: this.prioritas,
                    selectedAddressId: this.selectedAddressId,
                    formPhone: this.formPhone,
                    expandedSpecs: this.expandedSpecs,
                    expandedBawahan: this.expandedBawahan,
                    addressMode: this.addressMode,
                    contactInfo: JSON.parse(JSON.stringify(this.contactInfo)),
                    addressForm: JSON.parse(JSON.stringify(this.addressForm)),
                    savedAt: new Date().toISOString()
                };
                localStorage.setItem('pesanan_draft', JSON.stringify(draft));
            } catch (e) {
                console.warn('Gagal menyimpan draft', e);
            }
        },

        clearDraft() {
            localStorage.removeItem('pesanan_draft');
            if (this._draftTimer) {
                clearInterval(this._draftTimer);
                this._draftTimer = null;
            }
        },

        restoreDraft(data) {
            this.jenis = data.jenis;
            this.jenisPhase = data.jenisPhase || 1;
            this.selectedCategoryId = data.selectedCategoryId || '';

            Object.assign(this.form, data.form || {});

            this.items = data.items || [];
            this.step = data.step || 2;
            this.subStep = data.subStep || 1;
            this.prioritas = data.prioritas || 'normal';
            this.selectedAddressId = data.selectedAddressId || null;
            this.formPhone = data.formPhone || this.formPhone;
            this.expandedSpecs = data.expandedSpecs !== undefined ? data.expandedSpecs : true;
            this.expandedBawahan = data.expandedBawahan !== undefined ? data.expandedBawahan : true;
            this.addressMode = data.addressMode || 'select';
            if (data.contactInfo) Object.assign(this.contactInfo, data.contactInfo);
            if (data.addressForm) Object.assign(this.addressForm, data.addressForm);

            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons({ icons: window.lucide.icons });
            });
        },

        openAtasanGallery() {
            window.openPhotoSwipe([
                {
                    src: this.activeSizePdf,
                    width: 2345,
                    height: 1660,
                    alt: this.form.jenis_potongan || 'Referensi Ukuran'
                },
            ], 0)
        },
    }
}
</script>

<template x-teleport="body">
    <div
        x-show="showGuideModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/55"
        @click.self="showGuideModal = false"
    >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900" x-text="showGuideAttr ? 'Panduan: ' + showGuideAttr.name : 'Panduan'"></h3>
                <button @click="showGuideModal = false" class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 flex items-center justify-center transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 overflow-y-auto max-h-[70vh] flex items-center justify-center bg-gray-50/50">
                <template x-if="showGuideAttr && showGuideAttr.reference_image">
                    <img :src="showGuideAttr.reference_image.startsWith('http') || showGuideAttr.reference_image.startsWith('images/') ? '/' + showGuideAttr.reference_image.replace(/^\//, '') : '/storage/' + showGuideAttr.reference_image" class="max-h-[60vh] w-auto object-contain rounded-xl border border-gray-200 shadow-sm">
                </template>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                <button @click="showGuideModal = false" class="px-6 py-2 bg-[#1a237e] hover:bg-[#283593] text-white text-sm font-semibold rounded-lg">Mengerti</button>
            </div>
        </div>
    </div>
</template>

<template x-teleport="body">
    <div
        x-show="showOverrideModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
        @click.self="showOverrideModal = false"
    >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-900" x-text="overrideSingleIndex !== null ? 'Kustomisasi Atribut Pemain' : 'Kustomisasi Atribut Massal'"></h3>
                    <p class="text-[10px] text-gray-500 mt-0.5" x-text="overrideSingleIndex !== null ? 'Mengubah spesifikasi pemain baris #' + (overrideSingleIndex + 1) : 'Mengubah ' + countSelected() + ' pemain terpilih'"></p>
                </div>
                <button @click="showOverrideModal = false" class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 flex items-center justify-center transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 overflow-y-auto max-h-[60vh] space-y-4 bg-gray-50/50 grow">
                <p class="text-xs text-gray-500">Atribut yang dikosongkan atau diset ke <strong>Default</strong> akan otomatis mengikuti <strong>Spesifikasi Utama</strong> di halaman pemesanan.</p>
                
                <template x-for="attr in activeSchema.filter(a => a.system_tag !== 'is_size_type' && (!a.depends_on || !a.depends_on.attribute_id || form.customizations[a.depends_on.attribute_id] === a.depends_on.value))" :key="attr.id">
                    <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700" x-text="attr.name"></span>
                            <template x-if="overrideForm[attr.id]">
                                <span class="text-[9px] font-bold text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded-full border border-orange-100">Kustom</span>
                            </template>
                            <template x-if="!overrideForm[attr.id]">
                                <span class="text-[9px] font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-full">Default (Utama)</span>
                            </template>
                        </div>
                        
                        <template x-if="attr.type === 'select' || attr.type === 'radio'">
                            <select
                                x-model="overrideForm[attr.id]"
                                class="w-full border border-gray-300 p-2 rounded-lg bg-white text-xs outline-none focus:ring-1 focus:ring-[#1a237e]"
                            >
                                <option value="">- Ikuti Spesifikasi Utama -</option>
                                <template x-for="(opt, oIdx) in (attr.options || [])" :key="oIdx">
                                    <option :value="opt.value" x-text="opt.value"></option>
                                </template>
                            </select>
                        </template>
                        
                        <template x-if="attr.type === 'text'">
                            <input
                                type="text"
                                x-model="overrideForm[attr.id]"
                                class="w-full border border-gray-300 p-2 rounded-lg text-xs outline-none focus:ring-1 focus:ring-[#1a237e]"
                                placeholder="- Ikuti Spesifikasi Utama -"
                            >
                        </template>
                    </div>
                </template>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2 shrink-0 bg-white">
                <button @click="showOverrideModal = false" class="px-4 py-2 border border-gray-300 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-50">Batal</button>
                <button @click="saveOverrides()" class="px-5 py-2 bg-[#1a237e] hover:bg-[#283593] text-white text-xs font-semibold rounded-lg">Terapkan</button>
            </div>
        </div>
    </div>
</template>
</div>

@else
<div class="max-w-5xl mx-auto px-4 py-16">
    <div class="text-center">
        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Masuk untuk Memesan</h1>
        <p class="text-gray-500 mb-8 max-w-md mx-auto">Silakan login atau daftar akun terlebih dahulu untuk dapat membuat pesanan jersey custom.</p>
        <a href="{{ request()->fullUrlWithQuery(['auth' => 'login']) }}"
           class="inline-flex items-center gap-2 px-8 py-3 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            Login / Daftar
        </a>
        <p class="mt-6 text-sm text-gray-400">
            <a href="{{ route('beranda') }}" class="text-blue-900 hover:underline">Kembali ke Beranda</a>
        </p>
    </div>
</div>
@endauth
@endsection
