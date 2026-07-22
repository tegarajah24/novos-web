@extends('layouts.internal')

@section('title', 'Kelola Kategori')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Kategori</h1>
@endsection

@section('internal-content')
<div x-data="kategoriApp()" x-init="init()">
    <div class="bg-white shadow-sm rounded-2xl overflow-hidden">
        <div class="p-4 md:p-5 border-b border-gray-100 flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-2">
                <h2 class="font-semibold text-gray-900 text-sm">Daftar Kategori</h2>
                <button @click="toggleAllCollapse()"
                    class="p-1 text-gray-400 hover:text-[#1a237e] hover:bg-gray-100 rounded-md transition-colors"
                    title="Lipat / Buka Semua Sub-Kategori">
                    <i data-lucide="chevrons-up-down" class="w-4 h-4"></i>
                </button>
            </div>
            <button @click="openModal()" class="px-4 py-2 bg-[#1a237e] text-white text-xs font-semibold rounded-xl hover:bg-[#283593] transition-colors">
                + Tambah Kategori
            </button>
        </div>

        <div class="overflow-x-auto max-h-[70vh]">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 font-medium">Nama Kategori</th>
                        <th class="px-6 py-4 font-medium">Induk Kategori</th>
                        <th class="px-6 py-4 font-medium text-center">Ikon</th>
                        <th class="px-6 py-4 font-medium text-center">Harga Dasar</th>
                        <th class="px-6 py-4 font-medium text-center">Jumlah Produk</th>
                        <th class="px-6 py-4 font-medium text-center">Jumlah Atribut</th>
                        <th class="px-6 py-4 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-if="loading">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <span class="loading loading-spinner loading-md text-[#1a237e]"></span>
                            </td>
                        </tr>
                    </template>
                    <template x-for="cat in treeCategories" :key="cat.id">
                        <tr :class="cat.isParent ? 'bg-white font-bold border-t-2 border-slate-200/90 hover:bg-slate-50/70' : 'bg-slate-50/70 hover:bg-slate-100/70'"
                            class="transition-colors">
                            {{-- Nama Kategori --}}
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-2" :class="!cat.isParent ? 'pl-6 sm:pl-8' : ''">
                                    <template x-if="cat.isParent">
                                        <button type="button" @click="toggleCollapse(cat.id)" class="p-1 text-gray-500 hover:text-[#1a237e] hover:bg-gray-200/60 rounded-md transition-colors shrink-0">
                                            <i :data-lucide="isCollapsed(cat.id) ? 'chevron-right' : 'chevron-down'" class="w-4 h-4"></i>
                                        </button>
                                    </template>
                                    <template x-if="!cat.isParent">
                                        <i data-lucide="corner-down-right" class="w-4 h-4 text-indigo-400 shrink-0"></i>
                                    </template>

                                    <span :class="cat.isParent ? 'font-bold text-sm text-gray-900' : 'font-medium text-sm text-gray-700'" x-text="cat.name"></span>

                                    <template x-if="cat.isParent && cat.childrenCount > 0">
                                        <span @click="toggleCollapse(cat.id)"
                                              class="ml-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100/80 cursor-pointer hover:bg-indigo-100 transition-colors"
                                              x-text="cat.childrenCount + ' sub-kategori'">
                                        </span>
                                    </template>
                                </div>
                            </td>

                            {{-- Induk Kategori --}}
                            <td class="px-6 py-3.5 text-gray-600">
                                <template x-if="cat.isParent">
                                    <span class="text-xs font-bold px-2 py-0.5 bg-gray-200/70 text-gray-600 rounded-md uppercase tracking-wider">Induk Utama</span>
                                </template>
                                <template x-if="!cat.isParent">
                                    <span class="text-[#1a237e] font-semibold text-xs px-2 py-0.5 bg-blue-50 rounded-md cursor-pointer hover:bg-blue-100 transition-colors"
                                          @click="toggleCollapse(cat.parentId)"
                                          x-text="cat.parent_name || '—'"></span>
                                </template>
                            </td>

                            {{-- Ikon --}}
                            <td class="px-6 py-3.5 text-center text-gray-600">
                                <template x-if="cat.icon && (cat.icon.includes('/') || cat.icon.includes('.'))">
                                    <img :src="'/storage/' + cat.icon" class="w-7 h-7 mx-auto object-contain rounded border border-gray-100 bg-white" />
                                </template>
                                <template x-if="cat.icon && !cat.icon.includes('/') && !cat.icon.includes('.')">
                                    <i :data-lucide="cat.icon" class="w-4 h-4 mx-auto text-gray-500"></i>
                                </template>
                                <span x-show="!cat.icon" class="text-gray-400">—</span>
                            </td>

                            {{-- Harga Dasar --}}
                            <td class="px-6 py-3.5 text-center font-semibold text-[#1a237e]" x-text="cat.base_price ? 'Rp ' + Number(cat.base_price).toLocaleString('id-ID') : 'Rp 0'"></td>

                            {{-- Jumlah Produk --}}
                            <td class="px-6 py-3.5 text-center text-gray-600" x-text="cat.products_count"></td>

                            {{-- Jumlah Atribut --}}
                            <td class="px-6 py-3.5 text-center">
                                {{-- Atribut Langsung --}}
                                <template x-if="cat.attributes_schema && cat.attributes_schema.length > 0">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold" title="Atribut khusus kategori ini">
                                        <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                                        <span x-text="cat.attributes_schema.length + ' atribut'"></span>
                                    </span>
                                </template>

                                {{-- Atribut Warisan dari Induk --}}
                                <template x-if="(!cat.attributes_schema || cat.attributes_schema.length === 0) && (cat.effective_attributes_schema && cat.effective_attributes_schema.length > 0)">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-semibold" :title="'Mewarisi ' + cat.effective_attributes_schema.length + ' atribut dari induk ' + (cat.parent_name || '')">
                                        <i data-lucide="corner-down-right" class="w-3.5 h-3.5"></i>
                                        <span x-text="cat.effective_attributes_schema.length + ' atribut (Induk)'"></span>
                                    </span>
                                </template>

                                {{-- Belum Ada Atribut --}}
                                <template x-if="(!cat.attributes_schema || cat.attributes_schema.length === 0) && (!cat.effective_attributes_schema || cat.effective_attributes_schema.length === 0)">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-400 text-xs">
                                        Belum ada
                                    </span>
                                </template>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-3.5 text-right flex items-center justify-end gap-1">
                                <button @click="openAttrModal(cat)"
                                    class="text-gray-400 hover:text-purple-600 p-1.5 hover:bg-purple-50 rounded-lg transition-colors"
                                    title="Kelola Atribut">
                                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                                </button>
                                <button @click="openModal(cat)" class="text-gray-400 hover:text-[#1a237e] p-1.5 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <button @click="hapus(cat)" class="text-gray-400 hover:text-red-600 p-1.5 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loading && treeCategories.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">Belum ada kategori</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah/Edit Kategori --}}
    <template x-teleport="body">
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-black/40"></div>
        <div x-show="modalOpen" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-bold text-gray-900 mb-4" x-text="editId ? 'Edit Kategori' : 'Tambah Kategori'"></h3>
            <form @submit.prevent="simpan">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                    <input type="text" x-model="name" required
                           class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:ring-[#1a237e] focus:border-[#1a237e]"
                           placeholder="Contoh: Jersey Basket, Jaket, Celana Training">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Induk Kategori (Optional)</label>
                    <select x-model="parent_id"
                            class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:ring-[#1a237e] focus:border-[#1a237e]">
                        <option value="">— Tidak ada (Kategori Utama) —</option>
                        <template x-for="c in categories" :key="c.id">
                            <option x-show="c.id !== editId && !c.parent_id" :value="c.id" x-text="c.name"></option>
                        </template>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Pilih kategori induk jika kategori ini merupakan sub-kategori.</p>
                </div>
                <template x-if="!parent_id">
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Dasar per Jersey</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm">Rp</span>
                                <input type="number" x-model="base_price" min="0" step="1000"
                                       @if(auth()->user()->role->name !== 'Super Admin') disabled @endif
                                       class="w-full rounded-xl border-gray-300 pl-10 pr-4 py-2.5 text-sm focus:ring-[#1a237e] focus:border-[#1a237e] disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"
                                       placeholder="Contoh: 85000">
                            </div>
                            @if(auth()->user()->role->name !== 'Super Admin')
                                <p class="text-[10px] text-gray-400 mt-1">Hanya Super Admin yang dapat mengubah harga dasar.</p>
                            @endif
                        </div>
                        <template x-if="editId && icon && (icon.includes('/') || icon.includes('.'))">
                            <div class="mb-3 flex items-center gap-3 p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                                <img :src="'/storage/' + icon" class="w-12 h-12 object-contain rounded-lg bg-white border border-gray-200">
                                <div>
                                    <span class="text-xs font-semibold text-gray-700 block">Ikon Saat Ini</span>
                                    <span class="text-[10px] text-gray-400">Biarkan kosong jika tidak ingin mengubah</span>
                                </div>
                            </div>
                        </template>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ikon Kategori</label>
                            <input type="file" class="filepond" id="category-icon-pond" accept="image/png,image/jpeg,image/jpg,image/webp" data-max-file-size="2MB">
                            <p class="text-xs text-gray-400 mt-1">Gunakan gambar berformat PNG/JPG/WebP dengan rasio 1:1 (Square). Rekomendasi resolusi: 512x512 piksel, maks 2MB.</p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea x-model="description" rows="3"
                                       class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:ring-[#1a237e] focus:border-[#1a237e]"
                                       placeholder="Deskripsi singkat untuk card kategori..."></textarea>
                        </div>
                        <div class="mb-5 bg-gray-50 border border-gray-100 p-4 rounded-xl">
                            <span class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2.5">Konfigurasi Kolom Form</span>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-600 cursor-pointer">
                                    <input type="checkbox" x-model="form_config.show_team_name" class="rounded border-gray-300 text-[#1a237e] focus:ring-[#1a237e] h-4 w-4">
                                    <span>Tampilkan kolom <strong>Nama Tim / Event</strong></span>
                                </label>
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-600 cursor-pointer">
                                    <input type="checkbox" x-model="form_config.show_nama_artikel" class="rounded border-gray-300 text-[#1a237e] focus:ring-[#1a237e] h-4 w-4">
                                    <span>Tampilkan kolom <strong>Nama Artikel</strong></span>
                                </label>
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-600 cursor-pointer">
                                    <input type="checkbox" x-model="form_config.show_detail_sponsor" class="rounded border-gray-300 text-[#1a237e] focus:ring-[#1a237e] h-4 w-4">
                                    <span>Tampilkan kolom <strong>Detail Sponsor</strong></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </template>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>

                    {{-- Split Button Group --}}
                    <div class="inline-flex rounded-xl shadow-sm bg-[#1a237e] text-white">
                        <button type="submit" @click="simpan(false)" :disabled="submitting" 
                                class="px-4 py-2 text-sm font-semibold hover:bg-[#283593] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                :class="!editId ? 'rounded-l-xl' : 'rounded-xl'">
                            <svg x-show="submitting && !submittingAndAddAnother" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="submitting && !submittingAndAddAnother ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>

                        <template x-if="!editId">
                            <button type="button" @click="simpan(true)" :disabled="submitting" 
                                    title="Simpan & Tambah Lagi" 
                                    class="px-3 py-2 border-l border-white/20 rounded-r-xl hover:bg-[#283593] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center group">
                                <svg x-show="submittingAndAddAnother" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <svg x-show="!submittingAndAddAnother" class="w-4 h-4 text-blue-100 group-hover:text-white transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 8v8M8 12h8"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- Modal Kelola Atribut Dinamis --}}
    <template x-teleport="body">
    <div x-show="attrModalOpen" class="fixed inset-0 z-50 flex items-start justify-center pt-6 pb-6 overflow-y-auto" x-cloak>
        <div x-show="attrModalOpen" x-transition.opacity class="fixed inset-0 bg-black/50" @click="attrModalOpen = false"></div>
        <div x-show="attrModalOpen" x-transition class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 my-auto">
            {{-- Header --}}
            <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Kelola Atribut</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Kategori: <span class="font-semibold text-purple-700" x-text="attrCategoryName"></span></p>
                </div>
                <button @click="attrModalOpen = false" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- Info Box --}}
            <div class="px-6 py-3 bg-blue-50 border-b border-blue-100 space-y-2">
                <p class="text-xs text-blue-700 leading-relaxed">
                    <strong>Atribut</strong> adalah pilihan kustomisasi yang akan diisi customer saat memesan produk dari kategori ini (Contoh: Jenis Kerah, Bahan, Ukuran, dll).
                </p>
                <template x-if="parentName && parentSchema.length > 0">
                    <div class="p-2.5 bg-purple-100/80 border border-purple-200 rounded-lg text-xs text-purple-900 flex items-start gap-2">
                        <i data-lucide="corner-down-right" class="w-4 h-4 text-purple-700 shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-bold">Mewarisi <span x-text="parentSchema.length"></span> Atribut dari Induk (<span x-text="parentName"></span>)</p>
                            <p class="text-[11px] text-purple-800 mt-0.5">
                                Sub-kategori ini otomatis menyertakan atribut dari <strong x-text="parentName"></strong> saat pembuatan produk atau checkout katalog.
                            </p>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 max-h-[60vh] overflow-y-auto" x-ref="attrScrollArea">
                <template x-if="attrLoading">
                    <div class="flex items-center justify-center py-12">
                        <span class="loading loading-spinner loading-md text-purple-600"></span>
                    </div>
                </template>

                <template x-if="!attrLoading">
                    <div>
                        <template x-if="schema.length === 0">
                            <p class="text-sm text-gray-400 text-center py-8">Belum ada atribut. Klik "Tambah Atribut" untuk mulai.</p>
                        </template>

                        {{-- Daftar Atribut --}}
                        <div class="space-y-3">
                            <template x-for="(attr, idx) in schema" :key="idx">
                                <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 hover:border-purple-200 transition-colors">
                                    {{-- Row 1: ID, Name, Type, Required --}}
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">ID Atribut <span class="text-red-500">*</span></label>
                                            <input type="text" x-model="attr.id"
                                                class="w-full rounded-lg border-gray-300 text-xs px-3 py-2 focus:ring-purple-500 focus:border-purple-500"
                                                placeholder="contoh: kerah, bahan, tipe_jaket">
                                            <p class="text-[10px] text-gray-400 mt-0.5">Huruf kecil, underscore saja. Tidak bisa diubah setelah ada pesanan.</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Nama Tampilan <span class="text-red-500">*</span></label>
                                            <input type="text" x-model="attr.name"
                                                class="w-full rounded-lg border-gray-300 text-xs px-3 py-2 focus:ring-purple-500 focus:border-purple-500"
                                                placeholder="contoh: Jenis Kerah">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-3 mb-3">
                                        <div class="col-span-3 sm:col-span-1">
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Tipe</label>
                                            <select x-model="attr.type" class="w-full rounded-lg border-gray-300 text-xs px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
                                                <option value="select">Select (Dropdown)</option>
                                            </select>
                                        </div>
                                        <div class="flex items-center gap-2 col-span-3 sm:col-span-1 mt-6">
                                            <input type="checkbox" x-model="attr.required" class="rounded border-purple-500 text-purple-600 focus:ring-purple-500 h-4 w-4">
                                            <span class="text-xs font-medium text-gray-700">Wajib Diisi</span>
                                        </div>
                                        <div class="flex items-center gap-2 col-span-3 sm:col-span-1 mt-6">
                                            <input type="checkbox" x-model="attr.apply_to_catalog" class="rounded border-blue-500 text-blue-600 focus:ring-blue-500 h-4 w-4">
                                            <span class="text-xs font-medium text-gray-700" title="Terapkan atribut ini ke produk Katalog (Sub-kategori)">Terapkan ke Katalog</span>
                                        </div>
                                    </div>

                                    {{-- Row 2: Depends On & Reference Image --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4 p-3 rounded-lg border border-gray-200 bg-white">
                                        {{-- System Tag --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Tag</label>
                                            <select x-model="attr.system_tag"
                                                class="w-full rounded-lg border-gray-300 text-xs px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
                                                <option value="">— Tidak ada —</option>
                                                <option value="is_fabric_type">Bahan</option>
                                                <option value="is_collar_type">Kerah</option>
                                                <option value="is_cut_type">Potongan</option>
                                                <option value="is_sleeve_joint_type">Lengan Jahitan</option>
                                                <option value="is_sleeve_type">Lengan</option>
                                                <option value="is_size_type">Ukuran (Size)</option>
                                            </select>
                                        </div>
                                        <div class="flex items-end justify-end pb-1">
                                            <button @click="removeAttr(idx)"
                                                class="text-red-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded-lg transition-colors"
                                                title="Hapus atribut">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>


                                    {{-- Depends On --}}
                                    <div class="mb-3">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">
                                            Tampilkan hanya jika... (Opsional — untuk atribut bertingkat)
                                        </label>
                                        <div class="flex gap-2">
                                            <select x-model="attr.depends_on_id"
                                                class="flex-1 rounded-lg border-gray-300 text-xs px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
                                                <option value="">— Selalu tampil —</option>
                                                <template x-for="(other, oi) in schema" :key="oi">
                                                    <option x-show="oi !== idx && other.id" :value="other.id" x-text="other.name || other.id"></option>
                                                </template>
                                            </select>
                                            <span class="flex items-center text-xs text-gray-400 px-1">bernilai</span>
                                            <input type="text" x-model="attr.depends_on_value"
                                                class="flex-1 rounded-lg border-gray-300 text-xs px-3 py-2 focus:ring-purple-500 focus:border-purple-500"
                                                placeholder="Nilai yang memicu">
                                        </div>
                                    </div>

                                    {{-- Options (untuk select/radio) --}}
                                    <template x-if="attr.type === 'select' || attr.type === 'radio'">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">
                                                Opsi Pilihan
                                                <span class="text-[10px] text-gray-400 ml-1">(min. 1)</span>
                                            </label>
                                            <div class="space-y-1.5 mb-2">
                                                <template x-for="(opt, oi) in attr.options" :key="oi">
                                                    <div class="flex items-center gap-2">
                                                        <input type="text" x-model="opt.value"
                                                            class="flex-1 rounded-lg border-gray-200 bg-white text-xs px-3 py-1.5 focus:ring-[#1a237e] focus:border-[#1a237e]"
                                                            placeholder="Nilai opsi...">
                                                        <div class="relative w-32 shrink-0">
                                                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-gray-400 text-[10px]">Rp</span>
                                                            <input type="number" x-model="opt.price_modifier" step="1000"
                                                                @if(auth()->user()->role->name !== 'Super Admin') disabled @endif
                                                                class="w-full rounded-lg border-gray-200 bg-white pl-7 pr-2 py-1.5 text-xs focus:ring-[#1a237e] focus:border-[#1a237e] disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                                                                placeholder="± Harga">
                                                        </div>
                                                        <select x-show="attr.system_tag === 'is_sleeve_type'"
                                                            x-model="opt.sleeve"
                                                            class="w-20 shrink-0 rounded-lg border-gray-200 bg-white text-[10px] px-1.5 py-1.5 focus:ring-[#1a237e] focus:border-[#1a237e]">
                                                            <option value="">—</option>
                                                            <option value="short">Pendek</option>
                                                            <option value="long">Panjang</option>
                                                        </select>
                                                        <button @click="removeOption(attr, oi)"
                                                            class="text-gray-300 hover:text-red-500 transition-colors p-1" title="Hapus opsi">
                                                            <i data-lucide="x" class="w-3 h-3"></i>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                            <button @click="addOption(attr)"
                                                class="text-xs text-purple-600 hover:text-purple-800 font-medium flex items-center gap-1 hover:underline">
                                                <i data-lucide="plus" class="w-3 h-3"></i>
                                                Tambah Opsi
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Tombol Tambah Atribut --}}
                        <button @click="addAttr()"
                            class="mt-4 w-full border-2 border-dashed border-purple-200 text-purple-600 hover:border-purple-400 hover:bg-purple-50 rounded-xl py-3 text-sm font-medium transition-all flex items-center justify-center gap-2">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            Tambah Atribut
                        </button>
                    </div>
                </template>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50 rounded-b-2xl">
                <span class="text-xs text-gray-400" x-text="schema.length + ' atribut terdefinisi'"></span>
                <div class="flex gap-3">
                    <button @click="attrModalOpen = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>
                    <button @click="simpanAtribut()" :disabled="attrSaving"
                        class="px-5 py-2 bg-purple-600 text-white text-sm font-semibold rounded-xl hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg x-show="attrSaving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="attrSaving ? 'Menyimpan...' : 'Simpan Schema'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    </template>
</div>

<script>
function kategoriApp() {
    return {
        // --- Kategori ---
        categories: [],
        collapsedParents: {}, // { [parentId]: false (default expanded) }
        loading: true,
        modalOpen: false,
        editId: null,
        name: '',
        parent_id: '',
        icon: '',
        description: '',
        base_price: 0,
        form_config: {
            show_team_name: true,
            show_nama_artikel: true,
            show_detail_sponsor: true
        },
        submitting: false,
        submittingAndAddAnother: false,

        isCollapsed(parentId) {
            return !!this.collapsedParents[parentId];
        },

        toggleCollapse(parentId) {
            this.collapsedParents[parentId] = !this.collapsedParents[parentId];
            this.$nextTick(() => { if (window.lucide) lucide.createIcons({ icons: window.lucide.icons }); });
        },

        toggleAllCollapse() {
            const parents = this.categories.filter(c => !c.parent_id);
            const allCollapsed = parents.every(p => this.collapsedParents[p.id]);
            parents.forEach(p => {
                this.collapsedParents[p.id] = !allCollapsed;
            });
            this.$nextTick(() => { if (window.lucide) lucide.createIcons({ icons: window.lucide.icons }); });
        },

        get treeCategories() {
            if (!this.categories || !this.categories.length) return [];

            // 1. Induk utama (parent_id is null / empty)
            const parents = this.categories.filter(c => !c.parent_id);
            const result = [];

            parents.forEach(parent => {
                const children = this.categories.filter(c => c.parent_id == parent.id);
                result.push({
                    ...parent,
                    isParent: true,
                    childrenCount: children.length
                });

                // Jika parent TIDAK dikolaps, tampilkan anak-anaknya di bawahnya
                if (!this.collapsedParents[parent.id]) {
                    children.forEach(child => {
                        result.push({
                            ...child,
                            isParent: false,
                            parentId: parent.id
                        });
                    });
                }
            });

            // 2. Sub-kategori yang parent-nya tidak ditemukan (orphan)
            const parentIds = new Set(parents.map(p => p.id));
            const orphans = this.categories.filter(c => c.parent_id && !parentIds.has(c.parent_id));
            orphans.forEach(child => {
                result.push({
                    ...child,
                    isParent: false
                });
            });

            return result;
        },

        // --- Atribut Dinamis ---
        attrModalOpen: false,
        attrCategoryId: null,
        attrCategoryName: '',
        parentName: '',
        parentSchema: [],
        attrLoading: false,
        attrSaving: false,
        schema: [], // array of attribute objects

        async init() {
            await this.loadCategories();
        },

        async loadCategories() {
            this.loading = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("staf.kategori.data") }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                });
                this.categories = await res.json();
                this.$nextTick(() => { if (window.lucide) lucide.createIcons({ icons: window.lucide.icons }); });
            } catch (e) {
                Notify.error('Gagal memuat data kategori.');
            } finally {
                this.loading = false;
                this.$nextTick(() => { if (window.lucide) lucide.createIcons({ icons: window.lucide.icons }); });
            }
        },

        openModal(cat) {
            // Find and clear FilePond files
            const pond = FilePond.find(document.querySelector('#category-icon-pond'));
            if (pond) {
                pond.removeFiles();
            }

            if (cat) {
                this.editId = cat.id;
                this.name = cat.name;
                this.parent_id = cat.parent_id || '';
                this.icon = cat.icon || '';
                this.description = cat.description || '';
                this.base_price = cat.base_price || 0;
                this.form_config = Object.assign({
                    show_team_name: true,
                    show_nama_artikel: true,
                    show_detail_sponsor: true
                }, cat.form_config || {});
            } else {
                this.editId = null;
                this.name = '';
                this.parent_id = '';
                this.icon = '';
                this.description = '';
                this.base_price = 0;
                this.form_config = {
                    show_team_name: true,
                    show_nama_artikel: true,
                    show_detail_sponsor: true
                };
            }
            this.modalOpen = true;
            this.$nextTick(() => { 
                if (window.lucide) lucide.createIcons({ icons: window.lucide.icons }); 
                if (window.FilePond) {
                    FilePond.parse(document.body);
                }
            });
        },

        async simpan(addAnother = false) {
            if (!this.name.trim() || this.submitting) return;
            this.submitting = true;
            this.submittingAndAddAnother = addAnother;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = this.editId
                ? '/staf/kategori/' + this.editId
                : '{{ route("staf.kategori.store") }}';

            try {
                const formData = new FormData();
                formData.append('name', this.name.trim());
                if (this.parent_id) {
                    formData.append('parent_id', this.parent_id);
                }
                if (this.description) {
                    formData.append('description', this.description.trim());
                }
                formData.append('base_price', this.base_price || 0);
                formData.append('form_config[show_team_name]', this.form_config.show_team_name ? '1' : '0');
                formData.append('form_config[show_nama_artikel]', this.form_config.show_nama_artikel ? '1' : '0');
                formData.append('form_config[show_detail_sponsor]', this.form_config.show_detail_sponsor ? '1' : '0');

                if (this.editId) {
                    formData.append('_method', 'PUT');
                }

                // Add FilePond icon file if selected
                const pond = FilePond.find(document.querySelector('#category-icon-pond'));
                if (pond) {
                    pond.getFiles().forEach(f => {
                        if (f.file instanceof File) {
                            formData.append('icon', f.file);
                        }
                    });
                }

                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    Notify.success(data.message);
                    await this.loadCategories();

                    if (addAnother && !this.editId) {
                        const currentParentId = this.parent_id;
                        this.name = '';
                        this.icon = '';
                        this.description = '';
                        this.base_price = 0;
                        this.form_config = {
                            show_team_name: true,
                            show_nama_artikel: true,
                            show_detail_sponsor: true
                        };
                        this.parent_id = currentParentId;
                        if (pond) {
                            pond.removeFiles();
                        }
                        this.$nextTick(() => {
                            const nameInput = document.querySelector('input[x-model="name"]');
                            if (nameInput) nameInput.focus();
                        });
                    } else {
                        this.modalOpen = false;
                    }
                } else {
                    Notify.error(data.message || 'Gagal menyimpan.');
                }
            } catch (e) {
                Notify.error('Terjadi kesalahan server.');
            } finally {
                this.submitting = false;
                this.submittingAndAddAnother = false;
            }
        },

        hapus(cat) {
            Swal.fire({
                title: 'Hapus Kategori?',
                text: `Yakin ingin menghapus "${cat.name}"?`,
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
                    const res = await fetch('/staf/kategori/' + cat.id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Notify.success(data.message);
                        await this.loadCategories();
                    } else {
                        Notify.error(data.message);
                    }
                } catch (e) {
                    Notify.error('Terjadi kesalahan server.');
                }
            });
        },

        // =====================
        // Kelola Atribut Dinamis
        // =====================

        async openAttrModal(cat) {
            this.attrCategoryId = cat.id;
            this.attrCategoryName = cat.name;
            this.parentName = '';
            this.parentSchema = [];
            this.schema = [];
            this.attrModalOpen = true;
            this.attrLoading = true;

            this.$nextTick(() => { if (window.lucide) lucide.createIcons({ icons: window.lucide.icons }); });

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch(`/staf/kategori/${cat.id}/attributes`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                });
                const data = await res.json();
                this.parentName = data.parent_name || '';
                this.parentSchema = data.parent_attributes_schema || [];
                // Deep clone dan normalisasi depends_on
                this.schema = (data.attributes_schema || []).map(attr => ({
                    id:               attr.id || '',
                    name:             attr.name || '',
                    type:             attr.type || 'select',
                    required:         attr.required !== false,
                    apply_to_catalog: attr.apply_to_catalog !== false,
                    reference_image:  attr.reference_image || '',
                    system_tag:       attr.system_tag || '',
                    options:          (attr.options || []).map(o => ({ 
                        value: o.value || '',
                        price_modifier: Number(o.price_modifier) || 0,
                        sleeve: o.sleeve || '',
                    })),
                    // Pisahkan depends_on jadi 2 field agar mudah di-bind Alpine
                    depends_on_id:    attr.depends_on?.attribute_id || '',
                    depends_on_value: attr.depends_on?.value || '',
                }));
            } catch (e) {
                Notify.error('Gagal memuat schema atribut.');
                this.attrModalOpen = false;
            } finally {
                this.attrLoading = false;
                this.$nextTick(() => { if (window.lucide) lucide.createIcons({ icons: window.lucide.icons }); });
            }
        },

        addAttr() {
            this.schema.push({
                id: '',
                name: '',
                type: 'select',
                required: true,
                apply_to_catalog: true,
                reference_image: '',
                system_tag: '',
                options: [{ value: '', price_modifier: 0, sleeve: '' }],
                depends_on_id: '',
                depends_on_value: '',
            });
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons({ icons: window.lucide.icons });
                // Scroll ke bawah di area atribut
                const el = this.$refs.attrScrollArea;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        removeAttr(idx) {
            this.schema.splice(idx, 1);
        },

        addOption(attr) {
            if (!attr.options) attr.options = [];
            attr.options.push({ value: '', price_modifier: 0, sleeve: '' });
            this.$nextTick(() => { if (window.lucide) lucide.createIcons({ icons: window.lucide.icons }); });
        },

        removeOption(attr, oi) {
            attr.options.splice(oi, 1);
        },

        async simpanAtribut() {
            if (this.attrSaving) return;

            // Validasi dasar
            for (const attr of this.schema) {
                if (!attr.id || !attr.id.trim()) {
                    Notify.error('Semua atribut harus memiliki ID.');
                    return;
                }
                if (!attr.name || !attr.name.trim()) {
                    Notify.error('Semua atribut harus memiliki Nama Tampilan.');
                    return;
                }
                if ((attr.type === 'select' || attr.type === 'radio') && (!attr.options || attr.options.filter(o => o.value.trim()).length === 0)) {
                    Notify.error(`Atribut "${attr.name}" harus punya minimal 1 opsi.`);
                    return;
                }
            }

            // Cek ID duplikat
            const ids = this.schema.map(a => a.id.trim());
            const hasDuplicate = ids.some((id, i) => ids.indexOf(id) !== i);
            if (hasDuplicate) {
                Notify.error('Terdapat ID atribut yang duplikat. Setiap atribut harus punya ID unik.');
                return;
            }

            this.attrSaving = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Transformasi balik: gabungkan depends_on_id & depends_on_value ke object depends_on
            const schemaToSend = this.schema.map(attr => {
                const obj = {
                    id:             attr.id.trim(),
                    name:           attr.name.trim(),
                    type:           attr.type,
                    required:       !!attr.required,
                    apply_to_catalog: !!attr.apply_to_catalog,
                    reference_image: attr.reference_image || '',
                    system_tag:     attr.system_tag || '',
                    options:        (attr.options || []).filter(o => o.value.trim()).map(o => ({ 
                        value: o.value.trim(),
                        price_modifier: Number(o.price_modifier) || 0,
                        sleeve: o.sleeve || '',
                    })),
                };
                if (attr.depends_on_id && attr.depends_on_value) {
                    obj.depends_on = {
                        attribute_id: attr.depends_on_id.trim(),
                        value:        attr.depends_on_value.trim(),
                    };
                }
                return obj;
            });

            try {
                const res = await fetch(`/staf/kategori/${this.attrCategoryId}/attributes`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ attributes_schema: schemaToSend }),
                });
                const data = await res.json();
                if (data.success) {
                    Notify.success(data.message);
                    this.attrModalOpen = false;
                    await this.loadCategories();
                } else {
                    Notify.error(data.message || 'Gagal menyimpan schema.');
                }
            } catch (e) {
                Notify.error('Terjadi kesalahan server.');
            } finally {
                this.attrSaving = false;
            }
        }
    }
}
</script>
@endsection
