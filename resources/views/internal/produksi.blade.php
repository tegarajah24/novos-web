@extends('layouts.internal')

@section('title', 'Tugas Produksi')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Produksi</h1>
@endsection

@section('internal-content')
<div x-data="produksiApp()" x-init="init()">


    {{-- Tabs Navigation (hidden on mobile, shown on desktop) --}}
    <div class="hidden xl:flex max-w-5xl gap-1 bg-white rounded-2xl p-1.5 shadow-sm border border-gray-200 mb-8">
        <template x-for="tab in tabs" :key="tab.key">
            <button @click="activeTab = tab.key"
                :class="activeTab === tab.key ? 'bg-[#1a237e] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                class="flex-1 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2">
                <span x-text="tab.label"></span>
                <span :class="activeTab === tab.key ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500'"
                    class="px-2.5 py-0.5 rounded-full text-xs font-bold transition-all"
                    x-text="orders.filter(o => o.stage === tab.key).length"></span>
            </button>
        </template>
    </div>

    {{-- Tabel Antrean Produksi berdasarkan Active Tab --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                <i x-show="activeTab === 'printing'" data-lucide="printer" class="w-4 h-4 text-[#1a237e]"></i>
                <i x-show="activeTab === 'press'" data-lucide="flame" class="w-4 h-4 text-[#1a237e]"></i>
                <i x-show="activeTab === 'jahit'" data-lucide="scissors" class="w-4 h-4 text-[#1a237e]"></i>
                <i x-show="activeTab === 'qc'" data-lucide="shield-check" class="w-4 h-4 text-[#1a237e]"></i>
                <span x-text="activeTab === 'printing' ? 'Daftar Antrean Cetak (Printing)' : (activeTab === 'press' ? 'Daftar Antrean Press (Heat Press)' : (activeTab === 'jahit' ? 'Daftar Antrean Jahit' : 'Daftar Antrean QC & Finishing'))"></span>
            </h2>
            <div class="flex gap-2">
                <span :class="activeTab === 'printing' ? 'bg-blue-100 text-blue-700' : (activeTab === 'press' ? 'bg-orange-100 text-orange-700' : (activeTab === 'jahit' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'))"
                    class="px-3 py-1 text-xs font-semibold rounded-full flex items-center gap-1 transition-all duration-300">
                    <span x-text="filteredOrders().length"></span> Antrean
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
                        <th class="px-6 py-4 text-center font-medium">Total Qty</th>
                        <th class="px-6 py-4 font-medium">Deadline</th>
                        <th class="px-6 py-4 font-medium">Prioritas</th>
                        <th class="px-6 py-4 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-if="filteredOrders().length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="check-circle-2" class="w-10 h-10 mx-auto text-green-400 mb-2"></i>
                                <p class="font-medium text-gray-800">Tidak ada antrean di divisi ini.</p>
                                <p class="text-xs mt-1 text-gray-400" x-text="activeTab === 'printing' ? 'Semua pesanan selesai diprint!' : (activeTab === 'press' ? 'Semua pesanan selesai dipress!' : (activeTab === 'jahit' ? 'Semua pesanan selesai dijahit!' : 'Semua pesanan lolos QC!'))"></p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="order in filteredOrders()" :key="order.id">
                        <tr class="hover:bg-indigo-50/30 transition-colors cursor-pointer group" @click="openDetail(order)">
                            <td class="px-6 py-4">
                                <span class="font-bold text-[#1a237e] group-hover:underline" x-text="order.order_id"></span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900" x-text="order.customer"></td>
                            <td class="px-6 py-4" x-text="order.team_name"></td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-gray-900 bg-gray-100 px-2.5 py-1 rounded-md text-xs border border-gray-200" x-text="order.total_qty + ' pcs'"></span>
                            </td>
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
                                <button @click.stop="openDetail(order)" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 text-xs font-medium hover:bg-gray-50 hover:text-[#1a237e] hover:border-[#1a237e] transition-colors flex items-center gap-1.5 ml-auto">
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
            <template x-if="filteredOrders().length === 0">
                <div class="px-6 py-12 text-center text-gray-500">
                    <i data-lucide="check-circle-2" class="w-10 h-10 mx-auto text-green-400 mb-2"></i>
                    <p class="font-medium text-gray-800">Tidak ada antrean di divisi ini.</p>
                    <p class="text-xs mt-1 text-gray-400" x-text="activeTab === 'printing' ? 'Semua pesanan selesai diprint!' : (activeTab === 'press' ? 'Semua pesanan selesai dipress!' : (activeTab === 'jahit' ? 'Semua pesanan selesai dijahit!' : 'Semua pesanan lolos QC!'))"></p>
                </div>
            </template>
            <div class="p-3 space-y-2.5">
                <template x-for="order in filteredOrders()" :key="order.id">
                    <div x-data="{ open: false }"
                         class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-shadow duration-200"
                         :class="open ? 'shadow-md border-[#1a237e]/20' : 'shadow-sm'">

                        {{-- Collapsed Header --}}
                        <button @click="open = !open"
                                class="flex items-center justify-between w-full px-4 py-3.5 text-left transition-colors"
                                :class="open ? 'bg-[#1a237e]/5' : 'bg-white hover:bg-gray-50'">
                            <div class="min-w-0 flex-1 mr-2">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-2 h-2 rounded-full shrink-0"
                                         :class="activeTab === 'printing' ? 'bg-blue-500' : (activeTab === 'press' ? 'bg-orange-500' : (activeTab === 'jahit' ? 'bg-amber-500' : 'bg-emerald-500'))"></div>
                                    <span class="font-bold text-[#1a237e] text-xs tracking-wide" x-text="order.order_id"></span>
                                    <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded" x-text="order.total_qty + ' pcs'"></span>
                                </div>
                                <div class="flex items-center gap-1.5 ml-4 text-xs text-gray-600">
                                    <span class="truncate font-medium" x-text="order.customer"></span>
                                    <span class="text-gray-300 shrink-0">•</span>
                                    <span class="truncate text-gray-400" x-text="order.team_name"></span>
                                </div>
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
                                <span x-show="order.priority === 'normal'"
                                      class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 text-[10px] font-bold border border-gray-200">
                                    Normal
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

    {{-- Modal Detail Pesanan & Penyelesaian --}}
    <template x-teleport="body">
    <div x-show="isDetailOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">

            <div x-show="isDetailOpen" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/40" aria-hidden="true"></div>

            <div x-show="isDetailOpen" x-transition.scale.origin.bottom class="inline-block w-full max-w-7xl p-4 sm:p-6 my-4 sm:my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl border border-gray-200">

                {{-- Header Modal --}}
                <div class="flex justify-between items-center mb-4 sm:mb-6 bg-white -mx-4 -mt-4 p-4 sm:-mx-6 sm:-mt-6 sm:p-6 border-b border-gray-200">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-1">
                            <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md bg-purple-100 text-purple-700 text-[10px] sm:text-xs font-bold border border-purple-200 uppercase"
                                x-text="selectedOrder?.stage"></span>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Detail Pesanan: <span x-text="selectedOrder?.order_id" class="text-[#1a237e]"></span></h3>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                            <span class="flex items-center gap-1"><i data-lucide="user" class="w-3.5 h-3.5"></i> <span x-text="selectedOrder?.customer"></span></span>
                            <span class="hidden sm:inline">&bull;</span>
                            <span class="flex items-center gap-1"><i data-lucide="phone" class="w-3.5 h-3.5"></i> <span x-text="selectedOrder?.customer_contact"></span></span>
                        </p>
                    </div>
                    <button @click="isDetailOpen = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
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
                            Tindakan
                        </button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

                        {{-- KIRI: Spesifikasi, Ukuran, Referensi (2 Kolom) --}}
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
                                    <span class="text-gray-500 text-xs block mb-0.5">Model Lengan &amp; Jahitan</span>
                                    <div class="font-medium text-gray-900" x-text="selectedOrder?.model_lengan_jahitan || '-'"></div>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs block mb-0.5">Total Qty</span>
                                    <div class="font-medium text-[#1a237e] font-bold" x-text="selectedOrder?.total_qty + ' pcs'"></div>
                                </div>
                            </div>



                        </div>

                        {{-- Detail Item Pesanan --}}
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                                <h4 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                                    <i data-lucide="list" class="w-4 h-4 text-[#1a237e]"></i>
                                    Detail Item Pesanan
                                </h4>
                                <button x-show="selectedOrder?.item_details?.length > 5"
                                        @click="isItemsExpanded = !isItemsExpanded"
                                        class="text-xs font-semibold text-[#1a237e] hover:underline focus:outline-none flex items-center gap-1">
                                    <span x-text="isItemsExpanded ? 'Sembunyikan' : 'Lihat Semua (' + selectedOrder?.item_details?.length + ')'"></span>
                                    <svg class="w-3 h-3 transition-transform duration-300" :class="isItemsExpanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>
                            <div :style="isItemsExpanded ? 'max-height: 10000px;' : 'max-height: 250px;'" class="overflow-y-auto rounded-lg border border-gray-200 transition-all duration-300 ease-in-out relative">
                                {{-- Desktop: Table --}}
                                <table class="hidden xl:table w-full text-sm">
                                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide sticky top-0 z-10">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-semibold">No Punggung</th>
                                            <th class="px-3 py-2 text-left font-semibold">Nama Punggung</th>
                                            <th class="px-3 py-2 text-left font-semibold">Size</th>
                                            <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        <template x-if="!selectedOrder?.item_details || selectedOrder.item_details.length === 0">
                                            <tr>
                                                <td colspan="4" class="px-3 py-6 text-center text-gray-400 text-sm">Tidak ada detail item.</td>
                                            </tr>
                                        </template>
                                        <template x-for="(detail, idx) in selectedOrder?.item_details || []" :key="idx">
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-3 py-2 text-gray-800 font-medium" x-text="detail.no_punggung ?? '-'"></td>
                                                <td class="px-3 py-2 text-gray-700" x-text="detail.nama_punggung ?? '-'"></td>
                                                <td class="px-3 py-2 text-gray-700" x-text="detail.size ?? '-'"></td>
                                                <td class="px-3 py-2 text-gray-700" x-text="detail.keterangan ?? '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>

                                {{-- Mobile: Accordion per item --}}
                                <div class="xl:hidden">
                                    <template x-if="!selectedOrder?.item_details || selectedOrder.item_details.length === 0">
                                        <div class="px-3 py-6 text-center text-gray-400 text-sm">Tidak ada detail item.</div>
                                    </template>
                                    <template x-for="(detail, idx) in selectedOrder?.item_details || []" :key="idx">
                                        <div x-data="{ open: false }" class="border-b border-gray-100 last:border-b-0">
                                            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 text-left hover:bg-gray-50 transition-colors">
                                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                                    <span class="text-xs font-semibold text-gray-800 shrink-0" x-text="detail.no_punggung ?? '-'"></span>
                                                    <span class="text-xs text-gray-600 truncate" x-text="detail.nama_punggung ?? '-'"></span>
                                                    <span class="text-[11px] text-gray-400 shrink-0" x-text="detail.size ? 'Size ' + detail.size : ''"></span>
                                                </div>
                                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0 ml-2" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="px-3 pb-3 space-y-1.5">
                                                <div class="flex items-start gap-2 text-xs text-gray-600">
                                                    <span class="text-gray-400 font-medium w-20 shrink-0">Nama</span>
                                                    <span x-text="detail.nama_punggung ?? '-'"></span>
                                                </div>
                                                <div class="flex items-start gap-2 text-xs text-gray-600">
                                                    <span class="text-gray-400 font-medium w-20 shrink-0">Ket</span>
                                                    <span x-text="detail.keterangan ?? '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                
                                {{-- Fade overlay when collapsed --}}
                                <div x-show="selectedOrder?.item_details?.length > 5 && !isItemsExpanded" 
                                     class="absolute bottom-0 left-0 right-0 h-10 bg-gradient-to-t from-white to-transparent pointer-events-none z-10"></div>
                            </div>
                        </div>


                        {{-- File Desain dari Tim Design --}}
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                <i data-lucide="file-check-2" class="w-4 h-4 text-[#1a237e]"></i>
                                File Desain &amp; Pola Cetak (Dari Tim Design)
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Kategori 1: Gambar Desain (Kiri) --}}
                                <div class="space-y-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Gambar Desain (Mockup &amp; Detail Tampak)</span>
                                    <div class="space-y-2">
                                        <template x-for="(file, fi) in (selectedOrder?.design_files || []).filter(f => f.type?.startsWith('image/'))" :key="file.name">
                                            <div class="flex items-center gap-3 p-2.5 bg-blue-50/50 border border-blue-100 rounded-lg">
                                                <img :src="file.path"
                                                     @click="window.openPhotoSwipe(selectedOrder.design_files, selectedOrder.design_files.indexOf(file))"
                                                     class="w-10 h-10 rounded-full object-cover shrink-0 shadow-sm border-2 border-blue-200 cursor-zoom-in hover:opacity-80 transition-opacity"
                                                     :title="'Lihat ' + file.name">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-medium text-gray-800 truncate" x-text="file.name"></p>
                                                    <p class="text-[10px] text-gray-400">GAMBAR DESAIN</p>
                                                </div>
                                                <a :href="file.path" :download="file.name" class="text-[#1a237e] bg-white border border-blue-100 hover:bg-[#1a237e] hover:text-white p-1.5 rounded-md transition-colors shrink-0 flex items-center justify-center" title="Download">
                                                    <i data-lucide="download" class="w-4 h-4"></i>
                                                </a>
                                            </div>
                                        </template>
                                        <template x-if="!(selectedOrder?.design_files || []).filter(f => f.type?.startsWith('image/')).length">
                                            <p class="text-xs text-gray-400 italic p-2 bg-gray-50 rounded-lg border border-gray-100">Belum ada file gambar desain.</p>
                                        </template>
                                    </div>
                                </div>

                                {{-- Kategori 2: File Pola (Kanan) --}}
                                <div class="space-y-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">File Pola (CDR / Vector)</span>
                                    <div class="space-y-2">
                                        <template x-for="(file, fi) in (selectedOrder?.design_files || []).filter(f => !f.type?.startsWith('image/'))" :key="file.name">
                                            <div class="flex items-center gap-3 p-2.5 bg-purple-50/50 border border-purple-100 rounded-lg">
                                                <div class="w-10 h-10 rounded bg-white flex items-center justify-center shrink-0 shadow-sm border border-purple-200">
                                                    <i data-lucide="file-type" class="w-5 h-5 text-purple-600"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-medium text-gray-800 truncate" x-text="file.name"></p>
                                                    <p class="text-[10px] text-purple-400">VECTOR PATTERN (CDR)</p>
                                                </div>
                                                <a :href="file.path" :download="file.name" class="text-purple-600 bg-white border border-purple-100 hover:bg-purple-600 hover:text-white p-1.5 rounded-md transition-colors shrink-0 flex items-center justify-center" title="Download">
                                                    <i data-lucide="download" class="w-4 h-4"></i>
                                                </a>
                                            </div>
                                        </template>
                                        <template x-if="!(selectedOrder?.design_files || []).filter(f => !f.type?.startsWith('image/')).length">
                                            <p class="text-xs text-gray-400 italic p-2 bg-gray-50 rounded-lg border border-gray-100">Belum ada file pola (CDR).</p>
                                        </template>
                                    </div>
                                </div>
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

                    {{-- KANAN: Update Status & Penyelesaian (1 Kolom) --}}
                    <div class="lg:col-span-1 space-y-6" :class="mobileTab === 'tindakan' ? 'block' : 'hidden lg:block'">

                        <div class="bg-white rounded-xl border border-[#1a237e]/20 shadow-lg shadow-[#1a237e]/5 overflow-hidden sticky top-6">
                            <div class="bg-[#1a237e] px-5 py-4">
                                <h4 class="font-semibold text-white flex items-center gap-2 text-sm">
                                    <i data-lucide="check-square" class="w-4 h-4"></i>
                                    Tindakan Produksi
                                </h4>
                            </div>

                            <div class="p-5 space-y-5">



                                {{-- Printing Checklist (hanya tampil di stage printing) --}}
                                <div x-show="selectedOrder?.stage === 'printing'" x-cloak>
                                    <label class="block text-xs font-semibold text-gray-700 mb-3 uppercase tracking-wider flex items-center gap-1.5">
                                        <i data-lucide="printer" class="w-3.5 h-3.5 text-blue-600"></i>
                                        1. Checklist Printing
                                    </label>
                                    <div class="space-y-2.5">
                                        <!-- Item 1: Acc Tes Warna -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-colors group">
                                            <input type="checkbox" x-model="printingChecklist.tesWarna"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-blue-800">Acc Tes Warna (Sample Print)</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Mesin cetak sedikit sampel warna dulu untuk memastikan kecerahan & kesesuaian warna.</p>
                                            </div>
                                        </label>
                                        <!-- Item 2: Validasi Kelengkapan Pola Desain -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-colors group">
                                            <input type="checkbox" x-model="printingChecklist.kelengkapanPola"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-blue-800">Validasi Kelengkapan Pola Desain</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Memastikan pola badan depan/belakang, lengan kiri/kanan, & kerah lengkap di file.</p>
                                            </div>
                                        </label>

                                    </div>
                                    <!-- Progress Bar Checklist -->
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-[11px] text-gray-500">Progress Printing</span>
                                            <span class="text-[11px] font-bold text-blue-600" x-text="printingProgress() + '/2 item'"></span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-500"
                                                :style="'width:' + (printingProgress() / 2 * 100) + '%'"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Press Checklist (hanya tampil di stage press) --}}
                                <div x-show="selectedOrder?.stage === 'press'" x-cloak>
                                    <label class="block text-xs font-semibold text-gray-700 mb-3 uppercase tracking-wider flex items-center gap-1.5">
                                        <i data-lucide="flame" class="w-3.5 h-3.5 text-orange-600"></i>
                                        1. Checklist Press &amp; Cutting
                                    </label>
                                    <div class="space-y-2.5">
                                        <!-- Item 1: Potong Kertas Print -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-orange-50 hover:border-orange-200 transition-colors group">
                                            <input type="checkbox" x-model="pressChecklist.potongKertas"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-orange-800">Potong Kertas Print</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Memotong gulungan kertas hasil print sesuai bagian agar siap di-press.</p>
                                            </div>
                                        </label>
                                        <!-- Item 2: Cek Kualitas Press Warna -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-orange-50 hover:border-orange-200 transition-colors group">
                                            <input type="checkbox" x-model="pressChecklist.kualitasPress"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-orange-800">Cek Kualitas Press Warna</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Gambar menempel sempurna di kain, warna matang, tidak luntur/berbayang.</p>
                                            </div>
                                        </label>
                                        <!-- Item 3: Proses Potong Kain (Cutting) -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-orange-50 hover:border-orange-200 transition-colors group">
                                            <input type="checkbox" x-model="pressChecklist.potongKain"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-orange-800">Proses Potong Kain (Cutting)</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Memotong kain yang sudah selesai di-press mengikuti garis polanya.</p>
                                            </div>
                                        </label>
                                        <!-- Item 4: Cek Warna & Hitung Kelengkapan Pola -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-orange-50 hover:border-orange-200 transition-colors group">
                                            <input type="checkbox" x-model="pressChecklist.hitungPola"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-orange-800">Cek Warna &amp; Hitung Kelengkapan Pola</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Warna konsisten dan jumlah potongan kain pas per baju sesuai total pesanan.</p>
                                            </div>
                                        </label>
                                        <!-- Item 5: Persiapan Detail Jahit (Sewing Prep) -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-orange-50 hover:border-orange-200 transition-colors group">
                                            <input type="checkbox" x-model="pressChecklist.persiapanDetailJahit"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-orange-800">Persiapan Detail Jahit (Sewing Prep)</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Menyiapkan aksesoris, resleting, label leher, atau benang warna tertentu.</p>
                                            </div>
                                        </label>
                                    </div>
                                    <!-- Progress Bar Checklist -->
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-[11px] text-gray-500">Progress Press &amp; Cutting</span>
                                            <span class="text-[11px] font-bold text-orange-600" x-text="pressProgress() + '/5 item'"></span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-500"
                                                :style="'width:' + (pressProgress() / 5 * 100) + '%'"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- QC Checklist (hanya tampil di stage QC) --}}
                                <div x-show="selectedOrder?.stage === 'qc'" x-cloak>
                                    <label class="block text-xs font-semibold text-gray-700 mb-3 uppercase tracking-wider flex items-center gap-1.5">
                                        <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        1. Checklist Quality Control
                                    </label>
                                    <div class="space-y-2.5">
                                        <!-- Item 1: Kualitas Jahitan -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition-colors group">
                                            <input type="checkbox" x-model="qcChecklist.jahitan"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-emerald-800">Kualitas Jahitan</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Jahitan rapi, benang tidak loncat, kelim lurus dan sesuai pola.</p>
                                            </div>
                                        </label>
                                        <!-- Item 2: Tidak Ada Cacat -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition-colors group">
                                            <input type="checkbox" x-model="qcChecklist.cacat"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-emerald-800">Bebas Cacat Produksi</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Tidak ada lubang, sobekan, noda, atau warna tidak merata.</p>
                                            </div>
                                        </label>
                                        <!-- Item 3: Ukuran Sesuai -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition-colors group">
                                            <input type="checkbox" x-model="qcChecklist.ukuran"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-emerald-800">Ukuran & Kuantitas Sesuai</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Jumlah pcs per ukuran sesuai dengan pesanan customer.</p>
                                            </div>
                                        </label>
                                        <!-- Item 4: Desain & Sablon -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition-colors group">
                                            <input type="checkbox" x-model="qcChecklist.desain"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-emerald-800">Desain & Sablon/Bordir</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Warna, posisi, dan kualitas sablon/bordir sesuai file desain.</p>
                                            </div>
                                        </label>
                                        <!-- Item 5: Setrika & Lipat -->
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition-colors group">
                                            <input type="checkbox" x-model="qcChecklist.setrika"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-emerald-800">Setrika &amp; Pelipatan</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">Jersey disetrika rapi, bebas kusut, dilipat, dan siap dikemas.</p>
                                            </div>
                                        </label>
                                        <!-- Item 6: Perlu Revisi -->
                                        <label class="flex items-start gap-3 p-3 bg-red-50 rounded-lg border border-red-200 cursor-pointer hover:bg-red-100 hover:border-red-300 transition-colors group">
                                            <input type="checkbox" x-model="qcChecklist.perluRevisi"
                                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-red-700 group-hover:text-red-900">Perlu Revisi / Pengerjaan Ulang</p>
                                                <p class="text-[11px] text-red-400 mt-0.5">Centang jika ada bagian yang perlu diperbaiki sebelum diserahkan.</p>
                                            </div>
                                        </label>
                                    </div>
                                    <!-- Progress Bar Checklist -->
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-[11px] text-gray-500">Progress QC</span>
                                            <span class="text-[11px] font-bold text-emerald-600" x-text="qcProgress() + '/5 item'"></span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500"
                                                :style="'width:' + (qcProgress() / 5 * 100) + '%'"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Target Stage Revisi (hanya untuk revisi QC) --}}
                                <div x-show="selectedOrder?.stage === 'qc' && updateStatus === 'revisi_qc'" x-cloak>
                                    <label class="block text-xs font-semibold text-gray-700 mb-3 uppercase tracking-wider flex items-center gap-1.5">
                                        <i data-lucide="corner-down-right" class="w-3.5 h-3.5 text-amber-600"></i>
                                        1b. Kirim Revisi ke Bagian
                                    </label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                                            :class="targetStage === 'printing' ? 'bg-blue-50 border-blue-300' : 'bg-gray-50 border-gray-200 hover:bg-blue-50/50 hover:border-blue-200'">
                                            <input type="radio" value="printing" x-model="targetStage"
                                                class="w-4 h-4 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                            <div>
                                                <p class="text-xs font-semibold" :class="targetStage === 'printing' ? 'text-blue-700' : 'text-gray-700'">Printing</p>
                                                <p class="text-[11px] text-gray-400">Cetak ulang</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                                            :class="targetStage === 'press' ? 'bg-orange-50 border-orange-300' : 'bg-gray-50 border-gray-200 hover:bg-orange-50/50 hover:border-orange-200'">
                                            <input type="radio" value="press" x-model="targetStage"
                                                class="w-4 h-4 text-orange-600 focus:ring-orange-500 cursor-pointer">
                                            <div>
                                                <p class="text-xs font-semibold" :class="targetStage === 'press' ? 'text-orange-700' : 'text-gray-700'">Press</p>
                                                <p class="text-[11px] text-gray-400">Heat press ulang</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                                            :class="targetStage === 'jahit' ? 'bg-amber-50 border-amber-300' : 'bg-gray-50 border-gray-200 hover:bg-amber-50/50 hover:border-amber-200'">
                                            <input type="radio" value="jahit" x-model="targetStage"
                                                class="w-4 h-4 text-amber-600 focus:ring-amber-500 cursor-pointer">
                                            <div>
                                                <p class="text-xs font-semibold" :class="targetStage === 'jahit' ? 'text-amber-700' : 'text-gray-700'">Jahit</p>
                                                <p class="text-[11px] text-gray-400">Perbaikan jahitan</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Catatan Opsional --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider"
                                        x-text="['printing', 'press', 'qc'].includes(selectedOrder?.stage) ? (selectedOrder?.stage === 'qc' ? '2. Catatan QC (Opsional)' : '2. Catatan (Opsional)') : '1. Catatan (Opsional)'"></label>
                                    <textarea x-model="productionNote" rows="3"
                                        class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm resize-none"
                                        :placeholder="selectedOrder?.stage === 'qc' ? 'Misal: jahitan bagian bahu kanan perlu dirapikan, ukuran XL ada 1 pcs cacat...' : 'Misal: ada kelebihan 1 pcs size L, warna sedikit lebih tua...'"></textarea>
                                </div>

                                {{-- Status Dropdown berdasarkan stage --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider"
                                         x-text="['printing', 'press', 'qc'].includes(selectedOrder?.stage) ? '3. Pilih Tindakan' : '2. Pilih Tindakan'"></label>
                                    
                                    <!-- Printing Stage Actions -->
                                    <div x-show="selectedOrder?.stage === 'printing'">
                                        <select x-model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                            <option value="proses_printing">Sedang Proses</option>
                                            <option value="selesai_printing">Selesai</option>
                                        </select>
                                    </div>

                                    <!-- Press Stage Actions -->
                                    <div x-show="selectedOrder?.stage === 'press'">
                                        <select x-model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                            <option value="proses_press">Sedang Proses</option>
                                            <option value="selesai_press">Selesai</option>
                                        </select>
                                    </div>

                                    <!-- Jahit Stage Actions -->
                                    <div x-show="selectedOrder?.stage === 'jahit'">
                                        <select x-model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                            <option value="proses_jahit">Sedang Proses</option>
                                            <option value="selesai_jahit">Selesai</option>
                                        </select>
                                    </div>

                                    <!-- QC Stage Actions -->
                                    <div x-show="selectedOrder?.stage === 'qc'">
                                        <select x-model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                            <option value="selesai_qc">Selesai (Lolos QC)</option>
                                            <option value="revisi_qc">Revisi / Pengerjaan Ulang</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Tombol Submit --}}
                                <div class="pt-2">
                                    <button @click="submitProduksi"
                                        :disabled="!canSubmit()"
                                        class="w-full py-3 px-4 bg-[#1a237e] hover:bg-[#283593] text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-[#1a237e]/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none flex items-center justify-center gap-2">
                                        <i data-lucide="send" class="w-4 h-4"></i>
                                        <span x-text="'Update'"></span>
                                    </button>
                                </div>

                                <div class="pt-2 border-t border-gray-100">
                                    <p class="text-[11px] text-gray-400 text-center leading-relaxed" x-show="selectedOrder?.stage === 'printing'">
                                        Pilih <strong class="text-gray-600">Selesai</strong> untuk mengirim pesanan ke divisi Press (Heat Press).
                                    </p>
                                    <p class="text-[11px] text-gray-400 text-center leading-relaxed" x-show="selectedOrder?.stage === 'press'">
                                        Pilih <strong class="text-gray-600">Selesai</strong> untuk mengirim pesanan ke divisi Jahit.
                                    </p>
                                    <p class="text-[11px] text-gray-400 text-center leading-relaxed" x-show="selectedOrder?.stage === 'jahit'">
                                        Pilih <strong class="text-gray-600">Selesai</strong> untuk mengirim pesanan ke Quality Control (QC).
                                    </p>
                                    <p class="text-[11px] text-gray-400 text-center leading-relaxed" x-show="selectedOrder?.stage === 'qc'">
                                        Pilih <strong class="text-gray-600">Sedang Proses</strong> untuk mencatat progres QC, atau <strong class="text-gray-600">Selesai</strong> untuk memfinalisasi dan menyelesaikan pesanan ini.
                                    </p>
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
function produksiApp() {
    return {
        isDetailOpen: false,
        selectedOrder: null,
        isItemsExpanded: false,
        updateStatus: '',
        productionNote: '',
        activeTab: 'printing',
        tabs: [
            { key: 'printing', label: 'Printing' },
            { key: 'press', label: 'Press (Heat Press)' },
            { key: 'jahit', label: 'Jahit (Sewing)' },
            { key: 'qc', label: 'Quality Control (QC)' },
        ],


        targetStage: 'jahit',
        mobileTab: 'info',
        qcChecklist: {
            jahitan: false,
            cacat: false,
            ukuran: false,
            desain: false,
            setrika: false,
            perluRevisi: false
        },
        printingChecklist: {
            tesWarna: false,
            kelengkapanPola: false
        },
        pressChecklist: {
            potongKertas: false,
            kualitasPress: false,
            potongKain: false,
            hitungPola: false,
            persiapanDetailJahit: false
        },

        orders: @json($orders).map(order => ({
            ...order,
            stage: order.production_stage || (order.status === 'siap_cetak' ? 'printing' : 'printing')
        })),

        init() {
            if (location.hash.startsWith('#produksi=')) {
                var tab = location.hash.replace('#produksi=', '');
                if (this.tabs.some(function(t) { return t.key === tab; })) {
                    this.activeTab = tab;
                }
            }
            var self = this;
            window.addEventListener('hashchange', function() {
                if (location.hash.startsWith('#produksi=')) {
                    var tab = location.hash.replace('#produksi=', '');
                    if (self.tabs.some(function(t) { return t.key === tab; })) {
                        self.activeTab = tab;
                    }
                }
            });
            this.$watch('activeTab', value => {
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons({ icons: window.lucide.icons });
                });
            });
        },

        filteredOrders() {
            return this.orders.filter(o => o.stage === this.activeTab);
        },

        qcProgress() {
            let count = 0;
            if (this.qcChecklist.jahitan) count++;
            if (this.qcChecklist.cacat) count++;
            if (this.qcChecklist.ukuran) count++;
            if (this.qcChecklist.desain) count++;
            if (this.qcChecklist.setrika) count++;
            return count;
        },

        printingProgress() {
            let count = 0;
            if (this.printingChecklist.tesWarna) count++;
            if (this.printingChecklist.kelengkapanPola) count++;
            return count;
        },

        pressProgress() {
            let count = 0;
            if (this.pressChecklist.potongKertas) count++;
            if (this.pressChecklist.kualitasPress) count++;
            if (this.pressChecklist.potongKain) count++;
            if (this.pressChecklist.hitungPola) count++;
            if (this.pressChecklist.persiapanDetailJahit) count++;
            return count;
        },

        openDetail(order) {
            this.selectedOrder = order;
            if (order.stage === 'printing') {
                this.updateStatus = 'selesai_printing';
            } else if (order.stage === 'press') {
                this.updateStatus = 'selesai_press';
            } else if (order.stage === 'jahit') {
                this.updateStatus = 'selesai_jahit';
            } else if (order.stage === 'qc') {
                this.updateStatus = 'selesai_qc';
            } else {
                this.updateStatus = 'proses_qc';
            }
            this.productionNote = '';
            this.targetStage = 'jahit';
            this.mobileTab = 'info';
            this.isItemsExpanded = false;
            // Reset checklist QC setiap buka modal
            this.qcChecklist = { jahitan: false, cacat: false, ukuran: false, desain: false, setrika: false, perluRevisi: false };
            this.printingChecklist = { tesWarna: false, kelengkapanPola: false };
            this.pressChecklist = { potongKertas: false, kualitasPress: false, potongKain: false, hitungPola: false, persiapanDetailJahit: false };
            this.isDetailOpen = true;
            setTimeout(() => {
                if (window.lucide) window.lucide.createIcons({ icons: window.lucide.icons });
            }, 50);
        },

        canSubmit() {
            if (!this.updateStatus) return false;
            if (this.selectedOrder?.stage === 'printing' && this.updateStatus === 'selesai_printing') {
                return this.printingChecklist.tesWarna && this.printingChecklist.kelengkapanPola;
            }
            if (this.selectedOrder?.stage === 'press' && this.updateStatus === 'selesai_press') {
                return this.pressChecklist.potongKertas && this.pressChecklist.kualitasPress && this.pressChecklist.potongKain && this.pressChecklist.hitungPola && this.pressChecklist.persiapanDetailJahit;
            }
            if (this.selectedOrder?.stage === 'qc') {
                if (this.updateStatus === 'selesai_qc') return this.qcProgress() === 5 && !this.qcChecklist.perluRevisi;
                if (this.updateStatus === 'revisi_qc') return this.qcChecklist.perluRevisi && !!this.targetStage;
            }
            return true;
        },

        submitProduksi() {
            if (!this.canSubmit()) return;

            const currentStage = this.selectedOrder.stage;
            const targetStatus = this.updateStatus;
            let isSelesai = targetStatus === 'selesai_qc';
            let title = '';
            let text = '';
            let confirmButtonText = '';
            let successText = '';

            if (currentStage === 'printing') {
                if (targetStatus === 'proses_printing') {
                    title = 'Update Status Printing?';
                    text = 'Status pesanan akan diperbarui menjadi Sedang Proses.';
                    confirmButtonText = 'Ya, Update!';
                    successText = 'Status pesanan berhasil diperbarui.';
                } else {
                    if (!this.printingChecklist.tesWarna || !this.printingChecklist.kelengkapanPola) {
                        Notify.warning('Semua checklist printing wajib dicentang untuk menyelesaikan printing.', 'Checklist Belum Lengkap');
                        return;
                    }
                    title = 'Selesaikan Printing?';
                    text = 'Proses printing selesai dan pesanan akan dikirim ke divisi Press (Heat Press).';
                    confirmButtonText = 'Ya, Kirim!';
                    successText = 'Proses printing selesai. Pesanan dikirim ke divisi Press.';
                }
            } else if (currentStage === 'press') {
                if (targetStatus === 'proses_press') {
                    title = 'Update Status Press?';
                    text = 'Status pesanan akan diperbarui menjadi Sedang Proses.';
                    confirmButtonText = 'Ya, Update!';
                    successText = 'Status pesanan berhasil diperbarui.';
                } else {
                    if (!this.pressChecklist.potongKertas || !this.pressChecklist.kualitasPress || !this.pressChecklist.potongKain || !this.pressChecklist.hitungPola || !this.pressChecklist.persiapanDetailJahit) {
                        Notify.warning('Semua checklist press wajib dicentang untuk menyelesaikan tahap press.', 'Checklist Belum Lengkap');
                        return;
                    }
                    title = 'Selesaikan Press (Heat Press)?';
                    text = 'Proses press selesai dan pesanan akan dikirim ke divisi Jahit.';
                    confirmButtonText = 'Ya, Kirim!';
                    successText = 'Proses press selesai. Pesanan dikirim ke divisi Jahit.';
                }
            } else if (currentStage === 'jahit') {
                if (targetStatus === 'proses_jahit') {
                    title = 'Update Status Jahit?';
                    text = 'Status pesanan akan diperbarui menjadi Sedang Proses.';
                    confirmButtonText = 'Ya, Update!';
                    successText = 'Status pesanan berhasil diperbarui.';
                } else {
                    title = 'Selesaikan Jahit?';
                    text = 'Proses jahit selesai dan pesanan akan dikirim ke divisi QC.';
                    confirmButtonText = 'Ya, Kirim!';
                    successText = 'Proses jahit selesai. Pesanan dikirim ke divisi QC.';
                }
            } else if (currentStage === 'qc') {
                if (targetStatus === 'selesai_qc') {
                    if (!this.qcChecklist.jahitan || !this.qcChecklist.cacat || !this.qcChecklist.ukuran || !this.qcChecklist.desain || !this.qcChecklist.setrika) {
                        Notify.warning('Semua item checklist (Kualitas Jahitan, Bebas Cacat, Ukuran & Kuantitas, Desain & Sablon, serta Setrika) wajib dicentang untuk menyelesaikan QC.', 'Checklist Belum Lengkap');
                        return;
                    }
                    if (this.qcChecklist.perluRevisi) {
                        Notify.warning('Checklist "Perlu Revisi" tidak boleh dicentang jika ingin menyelesaikan QC. Hapus centang atau pilih tindakan Revisi.', 'Tidak Bisa Selesaikan');
                        return;
                    }
                    title = 'QC Selesai – Finalisasi Pesanan?';
                    text = 'Semua item QC telah diperiksa. Pesanan akan ditandai SELESAI dan siap diserahkan ke customer.';
                    confirmButtonText = 'Ya, Selesaikan!';
                    successText = 'Quality Control selesai. Pesanan dinyatakan selesai diproduksi.';
                } else if (targetStatus === 'revisi_qc') {
                    if (this.qcChecklist.jahitan || this.qcChecklist.cacat || this.qcChecklist.ukuran || this.qcChecklist.desain || this.qcChecklist.setrika) {
                        Notify.warning('Untuk revisi, hanya checklist "Perlu Revisi / Pengerjaan Ulang" yang boleh dicentang. Checklist lainnya harus dikosongkan.', 'Checklist Tidak Sesuai');
                        return;
                    }
                    if (!this.qcChecklist.perluRevisi) {
                        Notify.warning('Centang checklist "Perlu Revisi / Pengerjaan Ulang" untuk mengirim pesanan kembali ke bagian Jahit.', 'Centang Perlu Revisi');
                        return;
                    }
                    if (!this.productionNote.trim()) {
                        Notify.warning('Harap isi catatan QC dengan detail bagian yang perlu diperbaiki sebelum mengirim revisi.', 'Catatan Revisi Wajib Diisi');
                        return;
                    }
                    const targetLabel = this.targetStage === 'printing' ? 'Printing' : (this.targetStage === 'press' ? 'Press' : 'Jahit');
                    title = `Kirim Revisi ke ${targetLabel}?`;
                    text = `Pesanan akan dikembalikan ke bagian ${targetLabel} untuk pengerjaan ulang sesuai catatan QC.`;
                    confirmButtonText = 'Ya, Kirim Revisi!';
                    successText = `Pesanan dikembalikan ke bagian ${targetLabel} untuk revisi.`;
                }
            }

            Swal.fire({
                title: title,
                text: text,
                icon: isSelesai ? 'success' : 'question',
                showCancelButton: true,
                confirmButtonColor: isSelesai ? '#16a34a' : (targetStatus === 'revisi_qc' ? '#d97706' : (targetStatus === 'proses_printing' || targetStatus === 'proses_press' || targetStatus === 'proses_jahit' ? '#0891b2' : '#1a237e')),
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    this._doSubmit(targetStatus, currentStage, successText);
                }
            });
        },

        _doSubmit(targetStatus, currentStage, successText) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('/staf/produksi/update/' + this.selectedOrder.order_id, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: targetStatus,
                    notes: this.productionNote,
                    target_stage: targetStatus === 'revisi_qc' ? this.targetStage : undefined
                })
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    Notify.success(successText || res.message, 'Berhasil!');
                    setTimeout(() => {
                        this.isDetailOpen = false;
                        if (targetStatus === 'selesai_qc') {
                            this.orders = this.orders.filter(o => o.id !== this.selectedOrder.id);
                        } else {
                            this.orders = this.orders.map(o => {
                                if (o.id === this.selectedOrder.id) {
                                    o.stage = targetStatus === 'revisi_qc' ? (res.target_stage || 'jahit') : (res.production_stage || currentStage);
                                    o.status = res.status;
                                }
                                return o;
                            });
                        }
                    }, 1200);
                }
            })
            .catch(() => {
                Swal.close();
                Notify.error('Terjadi kesalahan server.');
            });
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
