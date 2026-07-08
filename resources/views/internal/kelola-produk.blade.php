@extends('layouts.internal')

@section('title', 'Kelola Produk')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Kelola Produk</h1>
@endsection

@section('internal-content')
<div x-data="kelolaProdukApp()" x-init="initApp()" class="space-y-6">

    @if(auth()->user()->role->name === 'Super Admin')
    {{-- Tab Navigation --}}
    <div class="flex gap-1 mb-6 bg-white border border-gray-200 rounded-2xl p-1.5 w-fit shadow-sm">
        <button @click="tab='katalog'; $nextTick(() => renderIcons())"
            :class="tab==='katalog' ? 'bg-[#1a237e] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100'"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
            <i data-lucide="layers" class="w-4 h-4"></i> Katalog Produk
        </button>
        <button @click="tab='referensi'; $nextTick(() => renderIcons())"
            :class="tab==='referensi' ? 'bg-[#1a237e] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100'"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
            <i data-lucide="settings" class="w-4 h-4"></i> Referensi Jersey
        </button>
    </div>
    @endif

    <div x-show="tab==='katalog'">
        <!-- Tabel Data -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <!-- Search -->
                <div class="relative w-full max-w-[240px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </span>
                    <input type="text" x-model="filters.search" @input.debounce="$nextTick(() => renderIcons())" placeholder="Cari nama jersey..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] bg-white text-gray-900">
                </div>

                <!-- Filter Kategori -->
                <select x-model="filters.category" @change="$nextTick(() => renderIcons())" class="w-full max-w-[180px] px-3 py-2.5 rounded-xl border border-gray-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] bg-white text-gray-700">
                    <option value="">Semua Kategori</option>
                    <template x-for="cat in categories" :key="cat.id">
                        <option :value="cat.id" x-text="cat.name"></option>
                    </template>
                </select>
            </div>

            <!-- Tombol Tambah -->
            <div>
                <button @click="openCreateForm()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors shadow-sm">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Tambah Produk Baru
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200">
                        <th class="w-16 text-center">ID</th>
                        <th>Foto</th>
                        <th>Nama Jersey</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <template x-for="(prod, index) in filteredProducts" :key="prod.id">
                        <tr class="hover:bg-gray-50 border-b border-gray-100 transition">
                            <td class="text-center text-gray-500 font-medium" x-text="prod.id"></td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                        <template x-if="prod.image_depan">
                                            <img :src="prod.image_depan" class="object-cover w-full h-full" alt="Depan">
                                        </template>
                                        <template x-if="!prod.image_depan">
                                            <i data-lucide="image" class="w-4 h-4 text-gray-400"></i>
                                        </template>
                                    </div>
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                        <template x-if="prod.image_belakang">
                                            <img :src="prod.image_belakang" class="object-cover w-full h-full" alt="Belakang">
                                        </template>
                                        <template x-if="!prod.image_belakang">
                                            <i data-lucide="image" class="w-4 h-4 text-gray-400"></i>
                                        </template>
                                    </div>
                                </div>
                            </td>
                            <td class="font-bold text-gray-900" x-text="prod.name"></td>
                            <td>
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-semibold" x-text="getCategoryName(prod.category_id)"></span>
                            </td>
                            <td class="font-semibold text-emerald-600" x-text="formatRupiah(prod.price)"></td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button @click="openEditForm(prod)" title="Edit Produk" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    <button @click="confirmDelete(prod.id)" title="Hapus Produk" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredProducts.length === 0">
                        <td colspan="7" class="text-center py-8 text-gray-500 font-medium">
                            Tidak ada produk ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    </div>
 
    @if(auth()->user()->role->name === 'Super Admin')
    <div x-show="tab==='referensi'" x-cloak class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Bagian 1: Jenis Kerah --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-[#1a237e]/10 rounded-xl flex items-center justify-center text-[#1a237e]">
                            <i data-lucide="info" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Jenis Kerah</h3>
                            <p class="text-xs text-gray-500">Kelola daftar opsi kerah dan gambar panduannya</p>
                        </div>
                    </div>
                    
                    {{-- Input Tambah Opsi --}}
                    <div class="flex gap-2 mb-4">
                        <input type="text" x-model="refInputs.collar" @keydown.enter.prevent="addRefOption('collar')" placeholder="Tambah jenis kerah baru..."
                               class="flex-1 rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] bg-white text-gray-900">
                        <button type="button" @click="addRefOption('collar')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-750 text-xs font-bold rounded-xl transition-all cursor-pointer">Tambah</button>
                    </div>
 
                    {{-- List Opsi --}}
                    <div class="flex flex-wrap gap-1.5 mb-5 max-h-36 overflow-y-auto p-1.5 border border-gray-100 rounded-xl bg-gray-50/50">
                        <template x-for="(opt, idx) in referensi.collar.options" :key="idx">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700">
                                <span x-text="opt"></span>
                                <button type="button" @click="removeRefOption('collar', idx)" class="text-gray-400 hover:text-red-500 focus:outline-none ml-0.5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </span>
                        </template>
                        <template x-if="referensi.collar.options.length === 0">
                            <span class="text-xs text-gray-400 p-1">Belum ada opsi</span>
                        </template>
                    </div>
 
                    {{-- Gambar Panduan --}}
                    <div class="space-y-2 mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Gambar Panduan / Referensi</label>
                        <div class="w-full h-44 rounded-xl border border-gray-200 bg-gray-50 overflow-hidden flex items-center justify-center relative group">
                            <img :src="refPreviews.collar || referensi.collar.image" class="w-full h-full object-contain" alt="Kerah Guide">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <label class="px-3 py-1.5 bg-white text-xs font-bold text-gray-700 rounded-lg shadow cursor-pointer hover:bg-gray-50">
                                    Ganti Gambar
                                    <input type="file" @change="handleRefImageChange($event, 'collar')" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" @click="saveReferensi('collar')" :disabled="refSaving.collar"
                        class="w-full py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-all disabled:opacity-50 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span x-show="!refSaving.collar">Simpan Perubahan</span>
                    <span x-show="refSaving.collar">Menyimpan...</span>
                </button>
            </div>
 
            {{-- Bagian 2: Bahan Jersey --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-[#1a237e]/10 rounded-xl flex items-center justify-center text-[#1a237e]">
                            <i data-lucide="shirt" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Bahan Jersey</h3>
                            <p class="text-xs text-gray-500">Kelola daftar opsi bahan dan gambar panduannya</p>
                        </div>
                    </div>
                    
                    {{-- Input Tambah Opsi --}}
                    <div class="flex gap-2 mb-4">
                        <input type="text" x-model="refInputs.bahan" @keydown.enter.prevent="addRefOption('bahan')" placeholder="Tambah bahan baru..."
                               class="flex-1 rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] bg-white text-gray-900">
                        <button type="button" @click="addRefOption('bahan')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-750 text-xs font-bold rounded-xl transition-all cursor-pointer">Tambah</button>
                    </div>
 
                    {{-- List Opsi --}}
                    <div class="flex flex-wrap gap-1.5 mb-5 max-h-36 overflow-y-auto p-1.5 border border-gray-100 rounded-xl bg-gray-50/50">
                        <template x-for="(opt, idx) in referensi.bahan.options" :key="idx">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700">
                                <span x-text="opt"></span>
                                <button type="button" @click="removeRefOption('bahan', idx)" class="text-gray-400 hover:text-red-500 focus:outline-none ml-0.5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </span>
                        </template>
                        <template x-if="referensi.bahan.options.length === 0">
                            <span class="text-xs text-gray-400 p-1">Belum ada opsi</span>
                        </template>
                    </div>
 
                    {{-- Gambar Panduan --}}
                    <div class="space-y-2 mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Gambar Panduan / Referensi</label>
                        <div class="w-full h-44 rounded-xl border border-gray-200 bg-gray-50 overflow-hidden flex items-center justify-center relative group">
                            <img :src="refPreviews.bahan || referensi.bahan.image" class="w-full h-full object-contain" alt="Bahan Guide">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <label class="px-3 py-1.5 bg-white text-xs font-bold text-gray-700 rounded-lg shadow cursor-pointer hover:bg-gray-50">
                                    Ganti Gambar
                                    <input type="file" @change="handleRefImageChange($event, 'bahan')" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" @click="saveReferensi('bahan')" :disabled="refSaving.bahan"
                        class="w-full py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-all disabled:opacity-50 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span x-show="!refSaving.bahan">Simpan Perubahan</span>
                    <span x-show="refSaving.bahan">Menyimpan...</span>
                </button>
            </div>
 
            {{-- Bagian 3: Jenis Potongan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-[#1a237e]/10 rounded-xl flex items-center justify-center text-[#1a237e]">
                            <i data-lucide="scissors" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Jenis Potongan</h3>
                            <p class="text-xs text-gray-500">Kelola daftar opsi potongan dan gambar panduannya</p>
                        </div>
                    </div>
                    
                    {{-- Input Tambah Opsi --}}
                    <div class="flex gap-2 mb-4">
                        <input type="text" x-model="refInputs.potongan" @keydown.enter.prevent="addRefOption('potongan')" placeholder="Tambah potongan baru..."
                               class="flex-1 rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] bg-white text-gray-900">
                        <button type="button" @click="addRefOption('potongan')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-750 text-xs font-bold rounded-xl transition-all cursor-pointer">Tambah</button>
                    </div>
 
                    {{-- List Opsi --}}
                    <div class="flex flex-wrap gap-1.5 mb-5 max-h-36 overflow-y-auto p-1.5 border border-gray-100 rounded-xl bg-gray-50/50">
                        <template x-for="(opt, idx) in referensi.potongan.options" :key="idx">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700">
                                <span x-text="opt"></span>
                                <button type="button" @click="removeRefOption('potongan', idx)" class="text-gray-400 hover:text-red-500 focus:outline-none ml-0.5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </span>
                        </template>
                        <template x-if="referensi.potongan.options.length === 0">
                            <span class="text-xs text-gray-400 p-1">Belum ada opsi</span>
                        </template>
                    </div>
 
                    {{-- Gambar Panduan --}}
                    <div class="space-y-2 mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Gambar Panduan / Referensi</label>
                        <div class="w-full h-44 rounded-xl border border-gray-200 bg-gray-50 overflow-hidden flex items-center justify-center relative group">
                            <img :src="refPreviews.potongan || referensi.potongan.image" class="w-full h-full object-contain" alt="Potongan Guide">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <label class="px-3 py-1.5 bg-white text-xs font-bold text-gray-700 rounded-lg shadow cursor-pointer hover:bg-gray-50">
                                    Ganti Gambar
                                    <input type="file" @change="handleRefImageChange($event, 'potongan')" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" @click="saveReferensi('potongan')" :disabled="refSaving.potongan"
                        class="w-full py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-all disabled:opacity-50 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span x-show="!refSaving.potongan">Simpan Perubahan</span>
                    <span x-show="refSaving.potongan">Menyimpan...</span>
                </button>
            </div>
 
            {{-- Bagian 4: Model Lengan & Jahitan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-[#1a237e]/10 rounded-xl flex items-center justify-center text-[#1a237e]">
                            <i data-lucide="settings" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Model Lengan & Jahitan</h3>
                            <p class="text-xs text-gray-500">Kelola daftar opsi lengan/jahitan dan gambar panduannya</p>
                        </div>
                    </div>
                    
                    {{-- Input Tambah Opsi --}}
                    <div class="flex gap-2 mb-4">
                        <input type="text" x-model="refInputs.lengan" @keydown.enter.prevent="addRefOption('lengan')" placeholder="Tambah model lengan baru..."
                               class="flex-1 rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] bg-white text-gray-900">
                        <button type="button" @click="addRefOption('lengan')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-750 text-xs font-bold rounded-xl transition-all cursor-pointer">Tambah</button>
                    </div>
 
                    {{-- List Opsi --}}
                    <div class="flex flex-wrap gap-1.5 mb-5 max-h-36 overflow-y-auto p-1.5 border border-gray-100 rounded-xl bg-gray-50/50">
                        <template x-for="(opt, idx) in referensi.lengan.options" :key="idx">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700">
                                <span x-text="opt"></span>
                                <button type="button" @click="removeRefOption('lengan', idx)" class="text-gray-400 hover:text-red-500 focus:outline-none ml-0.5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </span>
                        </template>
                        <template x-if="referensi.lengan.options.length === 0">
                            <span class="text-xs text-gray-400 p-1">Belum ada opsi</span>
                        </template>
                    </div>
 
                    {{-- Gambar Panduan --}}
                    <div class="space-y-2 mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Gambar Panduan / Referensi</label>
                        <div class="w-full h-44 rounded-xl border border-gray-200 bg-gray-50 overflow-hidden flex items-center justify-center relative group">
                            <img :src="refPreviews.lengan || referensi.lengan.image" class="w-full h-full object-contain" alt="Lengan Guide">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <label class="px-3 py-1.5 bg-white text-xs font-bold text-gray-700 rounded-lg shadow cursor-pointer hover:bg-gray-50">
                                    Ganti Gambar
                                    <input type="file" @change="handleRefImageChange($event, 'lengan')" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" @click="saveReferensi('lengan')" :disabled="refSaving.lengan"
                        class="w-full py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-all disabled:opacity-50 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span x-show="!refSaving.lengan">Simpan Perubahan</span>
                    <span x-show="refSaving.lengan">Menyimpan...</span>
                </button>
            </div>
 
        </div>
    </div>
    @endif

    <!-- Modal Form -->
    <template x-teleport="body">
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/40"></div>
        <div x-show="showModal" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-2xl w-full max-w-[564px] max-h-[665px] flex flex-col overflow-hidden mx-4">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="box" class="w-5 h-5 text-[#1a237e]"></i>
                    <span x-text="formMode === 'create' ? 'Tambah Produk Baru' : 'Edit Produk'"></span>
                </h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 flex-1 overflow-y-auto">
                <form @submit.prevent="saveProduct" class="space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.name" required class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-gray-25" placeholder="Contoh: Novos Performance Jersey">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Kategori <span class="text-red-500">*</span></label>
                            <select x-model="formData.category_id" required class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-gray-25">
                                <option value="" disabled>Pilih Kategori</option>
                                <template x-for="cat in categories" :key="cat.id">
                                    <option :value="cat.id" x-text="cat.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Harga (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" x-model="formData.price" required min="0" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-gray-25" placeholder="Contoh: 150000">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Deskripsi Produk</label>
                            <textarea x-model="formData.description" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-gray-25" rows="3" placeholder="Detail bahan, printing, dsb..." style="resize:none;"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Jenis Kerah</label>
                            <select x-model="formData.kerah" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-gray-25">
                                <option value="">Pilih (opsional)</option>
                                <option value="O-NECK V.1">O-NECK V.1</option>
                                <option value="O-NECK V.2">O-NECK V.2</option>
                                <option value="O-NECK V.3">O-NECK V.3</option>
                                <option value="O-NECK V.4">O-NECK V.4</option>
                                <option value="V-NECK V.5">V-NECK V.5</option>
                                <option value="V-NECK V.1">V-NECK V.1</option>
                                <option value="V-NECK V.2">V-NECK V.2</option>
                                <option value="V-NECK V.3">V-NECK V.3</option>
                                <option value="V-NECK V.4">V-NECK V.4</option>
                                <option value="V-NECK V.5">V-NECK V.5</option>
                                <option value="CLASSIC V.1">CLASSIC V.1</option>
                                <option value="CLASSIC V.2">CLASSIC V.2</option>
                                <option value="CLASSIC V.3">CLASSIC V.3</option>
                                <option value="CLASSIC V.4">CLASSIC V.4</option>
                                <option value="CLASSIC V.5">CLASSIC V.5</option>
                                <option value="V-NECK V3 TUMPUK">V-NECK V3 TUMPUK</option>
                                <option value="TIMNAS">TIMNAS</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Bahan Jersey</label>
                            <select x-model="formData.bahan" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-gray-25">
                                <option value="">Pilih (opsional)</option>
                                <option value="BINTIK JARUM GRADE B">BINTIK JARUM GRADE B</option>
                                <option value="MILANO GRADE B">MILANO GRADE B</option>
                                <option value="BINTIK JARUM PREMIUM">BINTIK JARUM PREMIUM</option>
                                <option value="MILANO PREMIUM">MILANO PREMIUM</option>
                                <option value="RABBIT">RABBIT</option>
                                <option value="DROPPEDDLE">DROPPEDDLE</option>
                                <option value="SMASH">SMASH</option>
                                <option value="WAFFLE">WAFFLE</option>
                                <option value="EMBOSH">EMBOSH</option>
                                <option value="MICROCOOL">MICROCOOL</option>
                                <option value="JAQUARD AERO">JAQUARD AERO</option>
                                <option value="COTTON 24S">COTTON 24S</option>
                                <option value="COTTON 30S">COTTON 30S</option>
                                <option value="LOTTO">LOTTO</option>
                                <option value="PARASUT">PARASUT</option>
                                <option value="PUMA">PUMA</option>
                                <option value="ULTRALIGHT A">ULTRALIGHT A</option>
                                <option value="ULTRALIGHT B">ULTRALIGHT B</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Jenis Potongan</label>
                            <select x-model="formData.jenis_potongan" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-gray-25">
                                <option value="">Pilih (opsional)</option>
                                <option value="REGULER">REGULER</option>
                                <option value="SLIMFIT CEWE">SLIMFIT CEWE</option>
                                <option value="OVERSIZE">OVERSIZE</option>
                                <option value="TUNIK">TUNIK</option>
                                <option value="SLIM FIT UNISEX">SLIM FIT UNISEX</option>
                                <option value="BOXY CUT">BOXY CUT</option>
                                <option value="KIDS">KIDS</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Model Lengan & Jahitan</label>
                            <select x-model="formData.lengan_jahitan" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-gray-25">
                                <option value="">Pilih (opsional)</option>
                                <option value="REGULER OVERDECK">REGULER OVERDECK</option>
                                <option value="REGULER PAKAI MANSET">REGULER PAKAI MANSET</option>
                                <option value="RAGLAN A OVERDECK">RAGLAN A OVERDECK</option>
                                <option value="RAGLAN A PAKAI MANSET">RAGLAN A PAKAI MANSET</option>
                                <option value="RAGLAN B OVERDECK">RAGLAN B OVERDECK</option>
                                <option value="RAGLAN B PAKAI MANSET">RAGLAN B PAKAI MANSET</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Foto Tampak Depan</label>
                            <input type="file" class="filepond" id="pondDepan" name="image" accept="image/*" data-max-file-size="5MB" data-allow-multiple="false">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Foto Tampak Belakang</label>
                            <input type="file" class="filepond" id="pondBelakang" name="image_belakang" accept="image/*" data-max-file-size="5MB" data-allow-multiple="false">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                        <button type="button" @click="showModal = false" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm rounded-xl hover:bg-gray-50 transition-colors font-medium bg-white">
                            Batal
                        </button>
                        <button type="submit" :disabled="submitting" class="px-6 py-2.5 bg-[#1a237e] text-white text-sm rounded-xl hover:bg-[#283593] transition-colors font-semibold flex items-center gap-2 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <svg x-show="!submitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span x-text="submitting ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </template>

<script>
function kelolaProdukApp() {
    return {
        tab: 'katalog',
        formMode: 'create',
        showModal: false,
        submitting: false,

        categories: @json($categories),

        products: @json($products),

        filters: {
            search: '',
            category: ''
        },

        referensi: {
            collar: { options: [], image: '' },
            bahan: { options: [], image: '' },
            potongan: { options: [], image: '' },
            lengan: { options: [], image: '' }
        },
        refInputs: { collar: '', bahan: '', potongan: '', lengan: '' },
        refFiles: { collar: null, bahan: null, potongan: null, lengan: null },
        refPreviews: { collar: null, bahan: null, potongan: null, lengan: null },
        refSaving: { collar: false, bahan: false, potongan: false, lengan: false },

        formData: {
            id: null,
            name: '',
            category_id: '',
            price: '',
            description: '',
            kerah: '',
            bahan: '',
            jenis_potongan: '',
            lengan_jahitan: ''
        },

        originalImageDepan: null,
        originalImageBelakang: null,

        initApp() {
            this.renderIcons();
            @if(auth()->user()->role->name === 'Super Admin')
            this.fetchReferensi();
            @endif
        },

        get filteredProducts() {
            return this.products.filter(p => {
                const matchSearch = p.name.toLowerCase().includes(this.filters.search.toLowerCase());
                const matchCat = this.filters.category ? p.category_id == this.filters.category : true;
                return matchSearch && matchCat;
            });
        },

        getCategoryName(id) {
            const cat = this.categories.find(c => c.id == id);
            return cat ? cat.name : '-';
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        },

        openCreateForm() {
            this.formMode = 'create';
            this.formData = {
                id: null,
                name: '',
                category_id: '',
                price: '',
                description: '',
                kerah: '',
                bahan: '',
                jenis_potongan: '',
                lengan_jahitan: ''
            };
            this.originalImageDepan = null;
            this.originalImageBelakang = null;
            this.clearFilePonds();
            this.showModal = true;
            this.$nextTick(() => this.renderIcons());
        },

        openEditForm(product) {
            this.formMode = 'edit';
            this.formData = {
                id: product.id,
                name: product.name,
                category_id: product.category_id,
                price: product.price,
                description: product.description,
                kerah: product.kerah || '',
                bahan: product.bahan || '',
                jenis_potongan: product.jenis_potongan || '',
                lengan_jahitan: product.lengan_jahitan || ''
            };
            this.originalImageDepan = product.image_depan || null;
            this.originalImageBelakang = product.image_belakang || null;
            this.clearFilePonds();
            if (product.image_depan) {
                const p1 = FilePond.find(document.querySelector('#pondDepan'));
                if (p1) p1.addFile(product.image_depan);
            }
            if (product.image_belakang) {
                const p2 = FilePond.find(document.querySelector('#pondBelakang'));
                if (p2) p2.addFile(product.image_belakang);
            }
            this.showModal = true;
            this.$nextTick(() => this.renderIcons());
        },

        closeForm() {
            this.showModal = false;
            this.clearFilePonds();
        },

        clearFilePonds() {
            ['pondDepan', 'pondBelakang'].forEach(id => {
                const pond = FilePond.find(document.querySelector('#' + id));
                if (pond) pond.removeFiles();
            });
        },

        async saveProduct() {
            if (this.submitting) return;
            this.submitting = true;
            const fd = new FormData();
            fd.append('name', this.formData.name);
            fd.append('category_id', this.formData.category_id);
            fd.append('price', this.formData.price);
            fd.append('description', this.formData.description || '');
            fd.append('kerah', this.formData.kerah || '');
            fd.append('bahan', this.formData.bahan || '');
            fd.append('jenis_potongan', this.formData.jenis_potongan || '');
            fd.append('lengan_jahitan', this.formData.lengan_jahitan || '');

            const pondDepan = FilePond.find(document.querySelector('#pondDepan'));
            if (pondDepan && pondDepan.getFiles().length > 0) {
                const f = pondDepan.getFiles()[0];
                if (f.file instanceof File) fd.append('image', f.file, f.file.name);
            } else if (this.formMode === 'edit' && this.originalImageDepan) {
                fd.append('image', '');
            }

            const pondBelakang = FilePond.find(document.querySelector('#pondBelakang'));
            if (pondBelakang && pondBelakang.getFiles().length > 0) {
                const f = pondBelakang.getFiles()[0];
                if (f.file instanceof File) fd.append('image_belakang', f.file, f.file.name);
            } else if (this.formMode === 'edit' && this.originalImageBelakang) {
                fd.append('image_belakang', '');
            }

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            let url = '{{ route("staf.kelola-produk.store") }}';
            let method = 'POST';

            if (this.formMode === 'edit') {
                url = '{{ url("staf/kelola-produk") }}/' + this.formData.id;
                method = 'POST';
                fd.append('_method', 'PUT');
            }

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: fd
                });

                const data = await res.json();

                if (data.success) {
                    if (this.formMode === 'create') {
                        this.products.push(data.product);
                    } else {
                        const idx = this.products.findIndex(p => p.id === data.product.id);
                        if (idx !== -1) {
                            this.products[idx] = data.product;
                        }
                    }

                    Notify.success(data.message);
                    this.closeForm();
                    this.renderIcons();
                } else {
                    Notify.error(data.message || 'Terjadi kesalahan');
                }
            } catch (e) {
                Notify.error('Gagal terhubung ke server.');
            } finally {
                this.submitting = false;
            }
        },

        confirmDelete(id) {
            window.Swal.fire({
                title: 'Hapus Produk?',
                text: "Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (!result.isConfirmed) return;

                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                try {
                    const res = await fetch('{{ url("staf/kelola-produk") }}/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();

                    if (data.success) {
                        this.products = this.products.filter(p => p.id !== id);
                        Notify.success(data.message, 'Terhapus!');
                    } else {
                        Notify.error(data.message || 'Terjadi kesalahan');
                    }
                } catch (e) {
                    Notify.error('Gagal terhubung ke server.');
                }
            });
        },

        renderIcons() {
            this.$nextTick(() => {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons({ icons: window.lucide.icons });
                }
            });
        },
 
        fetchReferensi() {
            fetch('{{ route('staf.kelola-produk.get-referensi') }}')
                .then(res => res.json())
                .then(data => {
                    this.referensi = data;
                })
                .catch(e => console.error(e));
        },
 
        addRefOption(type) {
            const val = this.refInputs[type].trim();
            if (!val) return;
            if (this.referensi[type].options.includes(val)) {
                this.refInputs[type] = '';
                return;
            }
            this.referensi[type].options.push(val);
            this.refInputs[type] = '';
        },
 
        removeRefOption(type, index) {
            this.referensi[type].options.splice(index, 1);
        },
 
        handleRefImageChange(e, type) {
            const file = e.target.files[0];
            if (!file) return;
            this.refFiles[type] = file;
            this.refPreviews[type] = URL.createObjectURL(file);
        },
 
        saveReferensi(type) {
            if (this.refSaving[type]) return;
            this.refSaving[type] = true;
 
            const fd = new FormData();
            fd.append('type', type);
            fd.append('options', JSON.stringify(this.referensi[type].options));
            if (this.refFiles[type]) {
                fd.append('image', this.refFiles[type]);
            }
 
            fetch('{{ route('staf.kelola-produk.update-referensi') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                this.refSaving[type] = false;
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Referensi ' + type + ' berhasil disimpan!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    if (this.refFiles[type]) {
                        this.refFiles[type] = null;
                        this.refPreviews[type] = null;
                    }
                    this.fetchReferensi();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message
                    });
                }
            })
            .catch(err => {
                this.refSaving[type] = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem'
                });
            });
        }
    }
}
</script>
@endsection
