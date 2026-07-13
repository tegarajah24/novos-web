@extends('layouts.internal')

@section('title', 'Kelola Kategori')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Kategori</h1>
@endsection

@section('internal-content')
<div x-data="kategoriApp()" x-init="init()">
    <div class="bg-white shadow-sm rounded-2xl overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 text-sm">Daftar Kategori</h2>
            <button @click="openModal()" class="px-4 py-2 bg-[#1a237e] text-white text-xs font-semibold rounded-xl hover:bg-[#283593] transition-colors">
                + Tambah Kategori
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500">
                    <tr>
                        <th class="px-6 py-4 font-medium">Nama Kategori</th>
                        <th class="px-6 py-4 font-medium">Induk Kategori</th>
                        <th class="px-6 py-4 font-medium text-center">Ikon</th>
                        <th class="px-6 py-4 font-medium text-center">Jumlah Produk</th>
                        <th class="px-6 py-4 font-medium text-center">Jumlah Atribut</th>
                        <th class="px-6 py-4 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-if="loading">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="loading loading-spinner loading-md text-[#1a237e]"></span>
                            </td>
                        </tr>
                    </template>
                    <template x-for="cat in categories" :key="cat.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="cat.name"></td>
                            <td class="px-6 py-4 text-gray-600">
                                <span x-text="cat.parent_name || '—'" :class="cat.parent_name ? 'text-[#1a237e] font-semibold' : 'text-gray-400'"></span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                <template x-if="cat.icon && (cat.icon.includes('/') || cat.icon.includes('.'))">
                                    <img :src="'/storage/' + cat.icon" class="w-8 h-8 mx-auto object-contain rounded border border-gray-100 bg-white" />
                                </template>
                                <template x-if="cat.icon && !cat.icon.includes('/') && !cat.icon.includes('.')">
                                    <i :data-lucide="cat.icon" class="w-5 h-5 mx-auto text-gray-500"></i>
                                </template>
                                <span x-show="!cat.icon" class="text-gray-400">—</span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600" x-text="cat.products_count"></td>
                            <td class="px-6 py-4 text-center">
                                <template x-if="cat.attributes_schema && cat.attributes_schema.length > 0">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-medium">
                                        <i data-lucide="layers" class="w-3 h-3"></i>
                                        <span x-text="cat.attributes_schema.length + ' atribut'"></span>
                                    </span>
                                </template>
                                <template x-if="!cat.attributes_schema || cat.attributes_schema.length === 0">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-400 text-xs">
                                        Belum ada
                                    </span>
                                </template>
                            </td>
                            <td class="px-6 py-4 text-right flex items-center justify-end gap-1">
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
                    <template x-if="!loading && categories.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada kategori</td>
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
        <div x-show="modalOpen" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
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
                <div class="flex justify-end gap-3">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                    <button type="submit" :disabled="submitting" class="px-4 py-2 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan'"></span>
                    </button>
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
            <div class="px-6 py-3 bg-blue-50 border-b border-blue-100">
                <p class="text-xs text-blue-700 leading-relaxed">
                    <strong>Atribut</strong> adalah pilihan kustomisasi yang akan diisi customer saat memesan produk dari kategori ini.
                    Contoh: Jenis Kerah, Bahan, Ukuran Bawah Jaket, dll.
                    Atribut bertipe <code class="bg-blue-100 px-1 rounded">depends_on</code> hanya muncul jika atribut induk dipilih nilai tertentu.
                </p>
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
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Tipe</label>
                                            <select x-model="attr.type" class="w-full rounded-lg border-gray-300 text-xs px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
                                                <option value="select">Select (Dropdown)</option>
                                                <option value="radio">Radio (Pilihan)</option>
                                                <option value="text">Text (Isian bebas)</option>
                                            </select>
                                        </div>
                                        <div class="flex items-end pb-2">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" x-model="attr.required" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                                <span class="text-xs text-gray-600">Wajib diisi</span>
                                            </label>
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
                                                            class="flex-1 rounded-lg border-gray-200 bg-white text-xs px-3 py-1.5 focus:ring-purple-500 focus:border-purple-500"
                                                            placeholder="Nilai opsi...">
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
        loading: true,
        modalOpen: false,
        editId: null,
        name: '',
        parent_id: '',
        icon: '',
        description: '',
        form_config: {
            show_team_name: true,
            show_nama_artikel: true,
            show_detail_sponsor: true
        },
        submitting: false,

        // --- Atribut Dinamis ---
        attrModalOpen: false,
        attrCategoryId: null,
        attrCategoryName: '',
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

        async simpan() {
            if (!this.name.trim() || this.submitting) return;
            this.submitting = true;
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
                formData.append('form_config[show_team_name]', this.form_config.show_team_name ? '1' : '0');
                formData.append('form_config[show_nama_artikel]', this.form_config.show_nama_artikel ? '1' : '0');
                formData.append('form_config[show_detail_sponsor]', this.form_config.show_detail_sponsor ? '1' : '0');
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
                    this.modalOpen = false;
                    await this.loadCategories();
                } else {
                    Notify.error(data.message || 'Gagal menyimpan.');
                }
            } catch (e) {
                Notify.error('Terjadi kesalahan server.');
            } finally {
                this.submitting = false;
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
                // Deep clone dan normalisasi depends_on
                this.schema = (data.attributes_schema || []).map(attr => ({
                    id:               attr.id || '',
                    name:             attr.name || '',
                    type:             attr.type || 'select',
                    required:         attr.required !== false,
                    reference_image:  attr.reference_image || '',
                    options:          (attr.options || []).map(o => ({ value: o.value || '' })),
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
                reference_image: '',
                options: [{ value: '' }],
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
            attr.options.push({ value: '' });
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
                    reference_image: attr.reference_image || '',
                    options:        (attr.options || []).filter(o => o.value.trim()).map(o => ({ value: o.value.trim() })),
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
