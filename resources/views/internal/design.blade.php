@extends('layouts.internal')

@section('title', 'Tugas Design')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Design</h1>
@endsection

@section('internal-content')
<div x-data="designApp()">

    {{-- Tabel Antrean Design --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                <i data-lucide="pen-tool" class="w-4 h-4 text-[#1a237e]"></i>
                Daftar Pesanan Belum Didesain
            </h2>
            <div class="flex gap-2">
                <span class="px-3 py-1 bg-blue-100 text-[#1a237e] text-xs font-semibold rounded-full flex items-center gap-1">
                    <span x-text="orders.length"></span> Antrean
                </span>
            </div>
        </div>
        <div class="hidden xl:block overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500">
                    <tr>
                        <th class="px-6 py-4 font-medium">ID Pesanan</th>
                        <th class="px-6 py-4 font-medium">Customer</th>
                        <th class="px-6 py-4 font-medium">Tim / Produk</th>
                        <th class="px-6 py-4 font-medium">Deadline</th>
                        <th class="px-6 py-4 font-medium">Prioritas</th>
                        <th class="px-6 py-4 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-if="orders.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <i data-lucide="check-circle-2" class="w-10 h-10 mx-auto text-green-400 mb-2"></i>
                                <p class="font-medium">Tidak ada antrean desain.</p>
                                <p class="text-xs">Kerja bagus, semua pesanan sudah dikerjakan!</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="order in orders" :key="order.id">
                        <tr class="hover:bg-blue-50/50 transition-colors cursor-pointer group" @click="openDetail(order)">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-[#1a237e] group-hover:underline" x-text="order.order_id"></span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="order.customer"></td>
                            <td class="px-6 py-4" x-text="order.team_name"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-gray-500">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    <span x-text="order.deadline"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span x-show="order.priority === 'express' || order.priority === 'super_express'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-50 text-red-700 text-xs font-semibold border border-red-100">
                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> <span x-text="order.priority === 'super_express' ? 'Super Express' : 'Express'"></span>
                                </span>
                                <span x-show="order.priority === 'normal'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-semibold border border-gray-200">
                                    Normal
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 text-xs font-medium hover:bg-gray-50 hover:text-[#1a237e] transition-colors flex items-center gap-1.5 ml-auto">
                                    Lihat Detail <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Mobile: Expandable Queue Cards --}}
        <div class="xl:hidden">
            <template x-if="orders.length === 0">
                <div class="px-6 py-10 text-center text-gray-500">
                    <i data-lucide="check-circle-2" class="w-10 h-10 mx-auto text-green-400 mb-2"></i>
                    <p class="font-medium">Tidak ada antrean desain.</p>
                    <p class="text-xs">Kerja bagus, semua pesanan sudah dikerjakan!</p>
                </div>
            </template>
            <div class="p-3 space-y-2.5">
                <template x-for="order in orders" :key="order.id">
                    <div x-data="{ open: false }"
                         class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-shadow duration-200"
                         :class="open ? 'shadow-md border-[#1a237e]/20' : 'shadow-sm'">

                        {{-- Collapsed Header --}}
                        <button @click="open = !open"
                                class="flex items-center justify-between w-full px-4 py-3.5 text-left transition-colors"
                                :class="open ? 'bg-[#1a237e]/5' : 'bg-white hover:bg-gray-50'">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <div class="w-2 h-2 rounded-full shrink-0 bg-purple-500"></div>
                                <span class="font-bold text-[#1a237e] text-xs tracking-wide" x-text="order.order_id"></span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span x-show="order.priority === 'super_express'"
                                      class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-red-50 text-red-600 text-[10px] font-bold border border-red-200">
                                    <i data-lucide="zap" class="w-2.5 h-2.5"></i> Super Express
                                </span>
                                <span x-show="order.priority === 'express'"
                                      class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-orange-50 text-orange-600 text-[10px] font-bold border border-orange-200">
                                    <i data-lucide="zap" class="w-2.5 h-2.5"></i> Express
                                </span>
                                <div class="w-6 h-6 rounded-full flex items-center justify-center transition-all duration-300"
                                     :class="open ? 'bg-[#1a237e] rotate-180' : 'bg-gray-100'">
                                    <svg class="w-3.5 h-3.5 transition-colors duration-300" :class="open ? 'text-white' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </button>

                        {{-- Expandable Body --}}
                        <div x-show="open"
                             x-cloak
                             x-transition:enter="transition ease-out duration-250"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0">

                            {{-- Divider --}}
                            <div class="mx-4 border-t border-gray-100"></div>

                            {{-- Detail Grid --}}
                            <div class="px-4 pt-3 pb-1 space-y-3">
                                {{-- Customer - Full Width --}}
                                <div>
                                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Customer</p>
                                    <p class="text-xs font-semibold text-gray-800" x-text="order.customer || '-'"></p>
                                </div>
                                {{-- 2 Kolom: Tim/Produk & Total Qty --}}
                                <div class="grid grid-cols-2 gap-x-4">
                                    <div>
                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Tim / Produk</p>
                                        <p class="text-xs font-semibold text-gray-800 leading-snug" x-text="order.team_name || '-'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Total Qty</p>
                                        <p class="text-xs font-bold text-[#1a237e]" x-text="order.total_qty + ' pcs'"></p>
                                    </div>
                                </div>
                                {{-- 2 Kolom: Deadline & Prioritas --}}
                                <div class="grid grid-cols-2 gap-x-4">
                                    <div>
                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Deadline</p>
                                        <p class="text-xs font-semibold text-red-600 flex items-center gap-1">
                                            <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span x-text="order.deadline"></span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Prioritas</p>
                                        <span x-show="order.priority === 'super_express'" class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-red-50 text-red-600 text-[10px] font-bold border border-red-200">
                                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            Super Express
                                        </span>
                                        <span x-show="order.priority === 'express'" class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-orange-50 text-orange-600 text-[10px] font-bold border border-orange-200">
                                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            Express
                                        </span>
                                        <span x-show="order.priority === 'normal'" class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold border border-gray-200">
                                            Normal
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div class="mx-4 mt-3 border-t border-gray-100"></div>

                            {{-- Action Buttons --}}
                            <div class="px-4 py-3 flex gap-2">
                                <button @click="open = false"
                                        class="flex-1 py-2 text-xs font-semibold rounded-xl border border-gray-300 text-gray-600 bg-white hover:bg-gray-50 transition-colors flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Tutup
                                </button>
                                <button @click.stop="openDetail(order)"
                                        class="flex-1 py-2 text-xs font-bold rounded-xl bg-[#1a237e] text-white hover:bg-[#283593] transition-colors flex items-center justify-center gap-1.5 shadow-sm shadow-[#1a237e]/20">
                                    Lihat Detail
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Modal Detail Pesanan & Upload --}}
    <template x-teleport="body">
    <div x-show="isDetailOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            
            <div x-show="isDetailOpen" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/40" aria-hidden="true"></div>

            <div x-show="isDetailOpen" x-transition.scale.origin.bottom class="inline-block w-full max-w-7xl p-4 sm:p-6 my-4 sm:my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl border border-gray-200">
                
                {{-- Header Modal --}}
                <div class="flex justify-between items-center mb-4 sm:mb-6 bg-white -mx-4 -mt-4 p-4 sm:-mx-6 sm:-mt-6 sm:p-6 border-b border-gray-200">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-1">
                            <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md bg-amber-100 text-amber-700 text-[10px] sm:text-xs font-bold border border-amber-200">Di Design</span>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Detail Pesanan: <span x-text="selectedOrder?.order_id" class="text-[#1a237e]"></span></h3>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                            <span class="flex items-center gap-1"><i data-lucide="user" class="w-3.5 h-3.5"></i> <span x-text="selectedOrder?.customer"></span></span>
                            <span class="hidden sm:inline">&bull;</span>
                            <span class="flex items-center gap-1"><i data-lucide="phone" class="w-3.5 h-3.5"></i> <span x-text="selectedOrder?.customer_contact"></span></span>
                        </p>
                    </div>
                    <button @click="closeModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </button>
                </div>

                {{-- Scrollable Content Area --}}
                <div class="max-h-[70vh] lg:max-h-[75vh] overflow-y-auto -mx-4 px-4 sm:-mx-6 sm:px-6">

                    {{-- Tabs on Mobile --}}
                    <div class="flex border border-gray-200 mb-4 lg:hidden p-1 bg-gray-50 rounded-xl">
                        <button @click="mobileTab = 'info'" 
                                :class="mobileTab === 'info' ? 'bg-white text-[#1a237e] shadow-sm font-bold' : 'text-gray-500 hover:text-gray-700'" 
                                class="flex-1 py-1.5 px-3 rounded-lg text-center text-xs transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Info Pesanan
                        </button>
                        <button @click="mobileTab = 'tindakan'" 
                                :class="mobileTab === 'tindakan' ? 'bg-white text-[#1a237e] shadow-sm font-bold' : 'text-gray-500 hover:text-gray-700'" 
                                class="flex-1 py-1.5 px-3 rounded-lg text-center text-xs transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Penyelesaian
                        </button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                        
                        {{-- KIRI: Spesifikasi & Referensi (2 Kolom) --}}
                        <div class="lg:col-span-2 space-y-4 sm:space-y-6" :class="mobileTab === 'info' ? 'block' : 'hidden lg:block'">
                        
                        {{-- Spesifikasi Produk --}}
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                <i data-lucide="shirt" class="w-4 h-4 text-[#1a237e]"></i>
                                Spesifikasi Produk
                            </h4>
                            <div class="grid grid-cols-3 gap-x-8 gap-y-4 text-sm">
                                <div class="col-span-2">
                                    <span class="text-gray-500 text-xs block mb-0.5">Nama Tim / Instansi</span>
                                    <div class="font-medium text-gray-900 text-base" x-text="selectedOrder?.team_name"></div>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs block mb-0.5">Deadline</span>
                                    <div class="font-medium text-red-600 text-base" x-text="selectedOrder?.deadline"></div>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs block mb-0.5">Bahan</span>
                                    <div class="font-medium text-gray-900" x-text="selectedOrder?.material || '-'"></div>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs block mb-0.5">Kerah</span>
                                    <div class="font-medium text-gray-900" x-text="selectedOrder?.collar || '-'"></div>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs block mb-0.5">Jenis Potongan</span>
                                    <div class="font-medium text-gray-900" x-text="selectedOrder?.jenis_potongan || '-'"></div>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs block mb-0.5">Lengan &amp; Jahitan</span>
                                    <div class="font-medium text-gray-900" x-text="selectedOrder?.lengan_jahitan || '-'"></div>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs block mb-0.5">Total Qty</span>
                                    <div class="font-medium text-[#1a237e] font-bold" x-text="selectedOrder?.total_qty + ' pcs'"></div>
                                </div>
                            </div>

                            <div x-show="selectedOrder?.revision_note" class="mt-5 pt-4 border-t border-gray-100">
                                <span class="text-orange-600 block mb-2 text-xs font-medium uppercase tracking-wider flex items-center gap-1.5">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Revisi Terakhir dari Customer
                                </span>
                                <div class="text-gray-700 bg-orange-50 p-4 rounded-xl border border-orange-200/60 leading-relaxed text-sm" x-text="selectedOrder?.revision_note"></div>
                            </div>
                             <div class="mt-5 pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-gray-500 block text-xs font-medium uppercase tracking-wider flex items-center gap-1.5">
                                        <i data-lucide="list" class="w-3.5 h-3.5"></i> Detail Item Pesanan
                                    </span>
                                    <button x-show="selectedOrder?.item_details?.length > 5"
                                            @click="isItemsExpanded = !isItemsExpanded"
                                            class="text-xs font-semibold text-[#1a237e] hover:underline focus:outline-none flex items-center gap-1">
                                        <span x-text="isItemsExpanded ? 'Sembunyikan' : 'Lihat Semua (' + selectedOrder?.item_details?.length + ')'"></span>
                                        <svg class="w-3 h-3 transition-transform duration-300" :class="isItemsExpanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                                <div :style="isItemsExpanded ? 'max-height: 10000px;' : 'max-height: 250px;'" class="overflow-y-auto rounded-lg border border-gray-200 transition-all duration-300 ease-in-out relative">
                                    <table class="w-full text-sm" x-show="selectedOrder?.item_details?.length">
                                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide sticky top-0 z-10">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-semibold">No Punggung</th>
                                                <th class="px-3 py-2 text-left font-semibold">Nama Punggung</th>
                                                <th class="px-3 py-2 text-left font-semibold">Size</th>
                                                <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 bg-white">
                                            <template x-for="(d, i) in selectedOrder?.item_details || []" :key="i">
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-3 py-2 text-gray-800 font-medium" x-text="d.no_punggung || '-'"></td>
                                                    <td class="px-3 py-2 text-gray-700" x-text="d.nama_punggung || '-'"></td>
                                                    <td class="px-3 py-2 text-gray-700" x-text="d.size"></td>
                                                    <td class="px-3 py-2 text-gray-700" x-text="d.keterangan || '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                    <div class="text-sm text-gray-400 text-center py-4" x-show="!selectedOrder?.item_details?.length">
                                        Belum ada item detail pesanan.
                                    </div>
                                    
                                    {{-- Fade overlay when collapsed --}}
                                    <div x-show="selectedOrder?.item_details?.length > 5 && !isItemsExpanded" 
                                         class="absolute bottom-0 left-0 right-0 h-10 bg-gradient-to-t from-white to-transparent pointer-events-none z-10"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Logo & Referensi --}}
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                <i data-lucide="image" class="w-4 h-4 text-[#1a237e]"></i>
                                File & Logo Referensi
                            </h4>
                            <div class="grid grid-cols-4 gap-4">
                                 <template x-for="(img, idx) in selectedOrder?.reference_files" :key="img">
                                    <div
                                       class="aspect-square rounded-xl border border-gray-200 overflow-hidden bg-gray-100 relative group cursor-zoom-in hover:border-[#1a237e] hover:shadow-md transition-all block"
                                       @click="openPhotoSwipeDesign(selectedOrder.reference_files, idx)">
                                        <img :src="img" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-[#1a237e]/80 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center transition-opacity gap-2">
                                            <i data-lucide="download" class="w-6 h-6 text-white"></i>
                                            <span class="text-white text-xs font-medium">Download</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- History Catatan --}}
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                History Catatan
                            </h4>
                            <div class="space-y-3">
                                <template x-if="selectedOrder?.history_notes?.length === 0">
                                    <p class="text-sm text-gray-400 text-center py-2">Belum ada catatan.</p>
                                </template>
                                <template x-for="(h, i) in selectedOrder?.history_notes || []" :key="i">
                                    <div class="flex gap-3">
                                        <div class="mt-1.5 w-2 h-2 rounded-full shrink-0"
                                             :class="'bg-' + (['green','yellow','blue','purple'][i % 4]) + '-500'"></div>
                                        <div class="min-w-0">
                                            <p class="text-xs text-gray-400 mb-0.5">
                                                <span x-text="h.date + ' — ' + h.user"></span>
                                                <span class="inline-block ml-1 font-semibold text-[11px]"
                                                      :class="h.origin === 'Customer' ? 'text-blue-600' : (h.origin === 'Design' ? 'text-purple-600' : (h.origin?.includes('Produksi') ? 'text-amber-600' : 'text-gray-500'))">
                                                    <span x-text="'[' + (h.origin || 'Sistem') + ']'"></span>
                                                </span>
                                            </p>
                                            <p class="text-sm text-gray-700 whitespace-pre-wrap" x-text="h.note"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    {{-- KANAN: Upload Design & Action (1 Kolom) --}}
                    <div class="lg:col-span-1 space-y-6">
                        
                        {{-- Form Upload & Update --}}
                        <div class="bg-white rounded-xl border border-[#1a237e]/20 shadow-lg shadow-[#1a237e]/5 overflow-hidden sticky top-6">
                            <div class="bg-[#1a237e] px-5 py-4">
                                <h4 class="font-semibold text-white flex items-center gap-2 text-sm">
                                    <i data-lucide="check-square" class="w-4 h-4"></i>
                                    Penyelesaian Design
                                </h4>
                            </div>
                            
                            <div class="p-5 space-y-5">
                                {{-- Mockup Depan & Belakang --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">1. Mockup Depan & Belakang</label>
                                    <input type="file" class="filepond" id="mockup-pond" name="mockup_files[]" multiple accept="image/*" data-max-file-size="20MB">
                                    <p class="text-xs text-gray-400 mt-1">Upload gambar mockup depan & belakang (1 gambar atau pisah)</p>
                                </div>

                                {{-- Detail Depan --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">2. Detail Depan</label>
                                    <input type="file" class="filepond" id="detail-depan-pond" name="detail_depan_files[]" multiple accept="image/*" data-max-file-size="20MB">
                                    <p class="text-xs text-gray-400 mt-1">Detail desain bagian depan (multi upload)</p>
                                </div>

                                {{-- Nama & No Punggung --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">3. Nama & No Punggung</label>
                                    <input type="file" class="filepond" id="nama-punggung-pond" name="nama_punggung_files[]" multiple accept="image/*" data-max-file-size="20MB">
                                    <p class="text-xs text-gray-400 mt-1">Upload nama & nomor punggung (multi upload)</p>
                                </div>

                                {{-- Detail Sponsor --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">4. Detail Sponsor</label>
                                    <input type="file" class="filepond" id="detail-sponsor-pond" name="detail_sponsor_files[]" multiple accept="image/*" data-max-file-size="20MB">
                                    <p class="text-xs text-gray-400 mt-1">Upload semua gambar sponsor (multi upload)</p>
                                </div>

                                {{-- Pola --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">5. Pola</label>
                                    <input type="file" class="filepond" id="pola-pond" name="pola_files[]" multiple accept=".cdr,.pdf,.svg,.ai,.eps,.zip,.rar" data-max-file-size="50MB">
                                    <p class="text-xs text-gray-400 mt-1">Upload pola jersey/bawahan/jaket (CDR, PDF, SVG, AI, EPS, ZIP)</p>
                                </div>

                                {{-- Status Dropdown --}}
                                <div class="border-t border-gray-100 pt-5">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">6. Update Status</label>
                                    <select x-model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                        <option value="">-- Pilih status selanjutnya --</option>
                                        <option value="menunggu_spk">Kirim ke SPK</option>
                                    </select>
                                </div>

                                {{-- Submit Button --}}
                                <div class="pt-2">
                                    <button @click="submitDesign" :disabled="!updateStatus" 
                                            class="w-full py-3 px-4 bg-[#1a237e] hover:bg-[#283593] text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-[#1a237e]/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none flex items-center justify-center gap-2">
                                        <i data-lucide="send" class="w-4 h-4"></i>
                                        Simpan & Teruskan
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                </div>

            </div>
        </div>
    </div>
    </template>
</div>

<script>
function designApp() {
    return {
        isDetailOpen: false,
        selectedOrder: null,
        isItemsExpanded: false,
        updateStatus: '',
        mobileTab: 'info',
        pondRefs: {},

        orders: @json($orders),

        openDetail(order) {
            this.selectedOrder = order;
            this.updateStatus = '';
            this.mobileTab = 'info';
            this.isItemsExpanded = false;
            this.isDetailOpen = true;

            setTimeout(() => {
                if(window.lucide) {
                    window.lucide.createIcons({ icons: window.lucide.icons });
                }
                this.initFilePond();
            }, 300);
        },

        initFilePond() {
            if (!window.FilePond) return;
            this.destroyFilePond();

            const ids = ['mockup-pond', 'detail-depan-pond', 'nama-punggung-pond', 'detail-sponsor-pond', 'pola-pond'];
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    const opts = {
                        allowMultiple: true,
                        instantUpload: false,
                        allowProcess: false,
                        credits: false,
                        stylePanelLayout: 'compact',
                        imagePreviewHeight: 80,
                    };

                    const pond = FilePond.create(el, opts);

                    pond.on('activatefile', (fileItem) => {
                        const file = fileItem.file;
                        if (file instanceof File && file.type.startsWith('image/')) {
                            const url = URL.createObjectURL(file);
                            window.openPhotoSwipe([{
                                path: url,
                                name: file.name,
                                type: 'image/',
                            }], 0);
                        }
                    });

                    this.pondRefs[id] = pond;
                }
            });
        },

        destroyFilePond() {
            Object.values(this.pondRefs).forEach(pond => {
                try { pond.destroy(); } catch(e) {}
            });
            this.pondRefs = {};
        },

        closeModal() {
            this.destroyFilePond();
            this.isDetailOpen = false;
        },

        submitDesign() {
            if(!this.updateStatus) return;

            const pondIds = ['mockup-pond', 'detail-depan-pond', 'nama-punggung-pond', 'detail-sponsor-pond', 'pola-pond'];
            const fieldNames = ['mockup_files', 'detail_depan_files', 'nama_punggung_files', 'detail_sponsor_files', 'pola_files'];

            let totalFiles = 0;
            pondIds.forEach((id, idx) => {
                const pond = this.pondRefs[id];
                if (pond) {
                    totalFiles += pond.getFiles().filter(f => f.file instanceof File).length;
                }
            });

            if (totalFiles === 0) {
                Notify.error('Silakan upload minimal 1 file desain.', 'Tidak Ada File');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menyelesaikan desain dan meneruskannya ke produksi?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a237e',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Teruskan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) return;

                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const formData = new FormData();
                formData.append('status', this.updateStatus);

                pondIds.forEach((id, idx) => {
                    const pond = this.pondRefs[id];
                    if (pond) {
                        pond.getFiles().forEach(fileItem => {
                            if (fileItem.file instanceof File) {
                                formData.append(fieldNames[idx] + '[]', fileItem.file);
                            }
                        });
                    }
                });

                this.destroyFilePond();

                let progress = 0;
                let loadingEl = document.createElement('div');
                loadingEl.id = 'upload-loading-overlay';
                loadingEl.className = 'fixed inset-0 z-[9999] flex items-center justify-center bg-black/40';
                loadingEl.innerHTML = '<div class="bg-white rounded-xl px-6 py-5 shadow-xl w-80"><div class="flex items-center gap-3 mb-3"><svg class="animate-spin h-5 w-5 text-[#1a237e]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="text-sm font-medium text-gray-700">Mengupload...</span></div><div class="w-full bg-gray-200 rounded-full h-2.5"><div class="bg-[#1a237e] h-2.5 rounded-full transition-all duration-300" style="width: 0%" id="upload-progress"></div></div><p class="text-xs text-gray-400 mt-2 text-center" id="upload-status">0%</p></div>';
                document.body.appendChild(loadingEl);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/staf/design/update/' + this.selectedOrder.order_id);

                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.upload.onprogress = (e) => {
                    if (e.lengthComputable) {
                        progress = Math.round((e.loaded / e.total) * 100);
                        const bar = document.getElementById('upload-progress');
                        const status = document.getElementById('upload-status');
                        if (bar) bar.style.width = progress + '%';
                        if (status) status.textContent = progress + '%';
                    }
                };

                xhr.onload = () => {
                    document.getElementById('upload-loading-overlay')?.remove();
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (res.success) {
                                Notify.success(res.message);
                                setTimeout(() => {
                                    this.isDetailOpen = false;
                                    this.orders = this.orders.filter(o => o.id !== this.selectedOrder.id);
                                }, 1200);
                            } else {
                                Notify.error(res.message || 'Terjadi kesalahan.');
                            }
                        } catch (e) {
                            Notify.error('Response tidak valid.');
                        }
                    } else {
                        let msg = 'Server error (' + xhr.status + ').';
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (res.message) msg = res.message;
                        } catch (e) {}
                        Notify.error(msg);
                    }
                };

                xhr.onerror = () => {
                    document.getElementById('upload-loading-overlay')?.remove();
                    Notify.error('Koneksi terputus. Coba lagi.');
                };

                xhr.ontimeout = () => {
                    document.getElementById('upload-loading-overlay')?.remove();
                    Notify.error('Upload terlalu lama. Coba file yang lebih kecil.', 'Timeout');
                };

                xhr.timeout = 120000;
                xhr.send(formData);
            })
        },

        openPhotoSwipeDesign(files, index) {
            var imageFiles = files.map(function(url) {
                return { path: url, name: 'Referensi', type: 'image/' };
            });
            if (imageFiles.length && window.openPhotoSwipe) {
                window.openPhotoSwipe(imageFiles, index);
            }
        },
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
input.filepond {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}
</style>
@endsection
