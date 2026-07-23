@extends('layouts.internal')

@section('title', 'Kelola Produk')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Kelola Produk</h1>
@endsection

@section('internal-content')
<div x-data="kelolaProdukApp()" x-init="initApp()" class="space-y-6">

    <div>
        <!-- Tabel Data -->
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
                                    <template x-for="(img, i) in (prod.images || [])" :key="i">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                            <img :src="img" class="object-cover w-full h-full" :alt="'Foto ' + (i+1)">
                                        </div>
                                    </template>
                                    <template x-if="!prod.images || prod.images.length === 0">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                            <i data-lucide="image" class="w-4 h-4 text-gray-400"></i>
                                        </div>
                                    </template>
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
                            <select x-model="formData.category_id" @change="onCategoryChange()" required class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-gray-25">
                                <option value="" disabled>Pilih Kategori</option>
                                <template x-for="parent in parentCategories" :key="parent.id">
                                    <optgroup :label="parent.name">
                                        <template x-if="subCategories(parent.id).length > 0">
                                            <template x-for="child in subCategories(parent.id)" :key="child.id">
                                                <option :value="child.id" x-text="child.name"></option>
                                            </template>
                                        </template>
                                        <template x-if="subCategories(parent.id).length === 0">
                                            <option :value="parent.id" x-text="parent.name + ' (Kategori Utama)'"></option>
                                        </template>
                                    </optgroup>
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

                    {{-- Atribut Dinamis — Render dari schema kategori yang dipilih --}}
                    <template x-if="selectedCategorySchema.length > 0">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Atribut Default Produk</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="attr in selectedCategorySchema" :key="attr.id">
                                    <div x-show="isAttrVisible(attr)" class="space-y-1.5">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide" x-text="attr.name"></label>
                                        <template x-if="attr.type === 'select' || attr.type === 'radio'">
                                            <select :name="'product_attributes[' + attr.id + ']'"
                                                x-model="formData.product_attributes[attr.id]"
                                                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-gray-25">
                                                <option value="">Pilih (opsional)</option>
                                                <template x-for="opt in (attr.options || [])" :key="opt.value">
                                                    <option :value="opt.value" x-text="opt.value"></option>
                                                </template>
                                            </select>
                                        </template>
                                        <template x-if="attr.type === 'text'">
                                            <input type="text" :name="'product_attributes[' + attr.id + ']'"
                                                x-model="formData.product_attributes[attr.id]"
                                                class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 bg-gray-25"
                                                :placeholder="'Isi ' + attr.name">
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="formData.category_id && selectedCategorySchema.length === 0">
                        <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-xs text-amber-700">
                            <strong>Info:</strong> Kategori ini belum punya atribut. Tambahkan dulu di menu <strong>Kelola Kategori → Kelola Atribut</strong>.
                        </div>
                    </template>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Foto Produk</label>
                        <input type="file" class="filepond" id="pondImages" name="images[]" accept="image/*" data-max-file-size="5MB" data-allow-multiple="true">
                        <p class="text-xs text-gray-400 mt-1">Upload 2-4 foto produk (depan, belakang, samping, dll)</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                            Batal
                        </button>

                        {{-- Split Button Group --}}
                        <div class="inline-flex rounded-xl shadow-sm bg-[#1a237e] text-white">
                            <button type="button" @click="saveProduct(false)" :disabled="submitting" 
                                    class="px-5 py-2.5 text-sm font-semibold hover:bg-[#283593] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                    :class="formMode === 'create' ? 'rounded-l-xl' : 'rounded-xl'">
                                <svg x-show="submitting && !submittingAndAddAnother" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <svg x-show="!submitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="submitting && !submittingAndAddAnother ? 'Menyimpan...' : 'Simpan'"></span>
                            </button>

                            <template x-if="formMode === 'create'">
                                <button type="button" @click="saveProduct(true)" :disabled="submitting" 
                                        title="Simpan & Tambah Lagi" 
                                        class="px-3.5 py-2.5 border-l border-white/20 rounded-r-xl hover:bg-[#283593] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center group">
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
    </div>
    </template>
</div>

<script>
function kelolaProdukApp() {
    return {
        tab: 'katalog',
        formMode: 'create',
        showModal: false,
        submitting: false,
        submittingAndAddAnother: false,

        categories: @json($categories),

        products: @json($products),

        get parentCategories() {
            return this.categories.filter(c => !c.parent_id);
        },

        subCategories(parentId) {
            return this.categories.filter(c => c.parent_id == parentId);
        },

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

        refSaving: { collar: false, bahan: false, potongan: false, lengan: false },

        formData: {
            id: null,
            name: '',
            category_id: '',
            price: '',
            description: '',
            product_attributes: {}, // Object dinamis {attr_id: value}
        },

        get selectedCategorySchema() {
            if (!this.formData.category_id) return [];
            const cat = this.categories.find(c => c.id == this.formData.category_id);
            return (cat && cat.attributes_schema) ? cat.attributes_schema : [];
        },

        isAttrVisible(attr) {
            if (!attr || !attr.depends_on || !attr.depends_on.attribute_id) return true;
            const parentVal = this.formData.product_attributes ? this.formData.product_attributes[attr.depends_on.attribute_id] : null;
            return parentVal == attr.depends_on.value;
        },

        onCategoryChange() {
            if (!this.formData.category_id) return;
            const cat = this.categories.find(c => c.id == this.formData.category_id);
            if (!cat) return;

            const catName = (cat.name || '').trim().toLowerCase();
            const schema = cat.attributes_schema || [];

            schema.forEach(attr => {
                if (!attr.options || !Array.isArray(attr.options)) return;
                const matchingOpt = attr.options.find(opt => (opt.value || '').trim().toLowerCase() === catName);
                if (matchingOpt) {
                    if (!this.formData.product_attributes) this.formData.product_attributes = {};
                    this.formData.product_attributes[attr.id] = matchingOpt.value;
                }
            });
        },

        originalImages: [],

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
                product_attributes: {},
            };
            this.originalImages = [];
            this.clearFilePonds();
            this.showModal = true;
            this.$nextTick(() => {
                this.renderIcons();
                if (window.FilePond) {
                    FilePond.parse(document.body);
                }
            });
        },

        openEditForm(product) {
            this.formMode = 'edit';
            this.formData = {
                id: product.id,
                name: product.name,
                category_id: product.category_id,
                price: product.price,
                description: product.description,
                product_attributes: product.product_attributes || {},
            };
            this.originalImages = product.images || [];
            this.showModal = true;
            this.$nextTick(() => {
                this.renderIcons();
                if (window.FilePond) {
                    FilePond.parse(document.body);
                }
                var pond = FilePond.find(document.querySelector('#pondImages'));
                if (pond) {
                    (product.images || []).forEach(function(url) {
                        pond.addFile(url);
                    });
                }
            });
        },

        closeForm() {
            this.showModal = false;
            this.clearFilePonds();
        },

        clearFilePonds() {
            var pond = FilePond.find(document.querySelector('#pondImages'));
            if (pond) pond.removeFiles();
        },

        async saveProduct(addAnother = false) {
            if (this.submitting) return;
            this.submitting = true;
            this.submittingAndAddAnother = addAnother;
            const fd = new FormData();
            fd.append('name', this.formData.name);
            fd.append('category_id', this.formData.category_id);
            fd.append('price', this.formData.price);
            fd.append('description', this.formData.description || '');
            fd.append('product_attributes', JSON.stringify(this.formData.product_attributes || {}));

            var pond = FilePond.find(document.querySelector('#pondImages'));
            var newFiles = [];
            var keptPaths = [];
            if (pond) {
                pond.getFiles().forEach(function(f) {
                    if (f.file instanceof File) {
                        newFiles.push(f.file);
                    } else if (f.file) {
                        keptPaths.push(f.file.name || f.file);
                    }
                });
            }
            newFiles.forEach(function(file) {
                fd.append('images[]', file, file.name);
            });
            if (this.formMode === 'edit') {
                keptPaths.forEach(function(path, i) {
                    fd.append('existing_images[' + i + ']', path);
                });
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

                    if (addAnother && this.formMode === 'create') {
                        const currentCategoryId = this.formData.category_id;
                        this.formData = {
                            id: null,
                            name: '',
                            category_id: currentCategoryId,
                            price: '',
                            description: '',
                            product_attributes: {},
                        };
                        this.originalImages = [];
                        this.clearFilePonds();
                        this.$nextTick(() => {
                            const nameInput = document.querySelector('input[x-model="formData.name"]');
                            if (nameInput) nameInput.focus();
                        });
                    } else {
                        this.closeForm();
                    }
                    this.renderIcons();
                } else {
                    Notify.error(data.message || 'Terjadi kesalahan');
                }
            } catch (e) {
                Notify.error('Gagal terhubung ke server.');
            } finally {
                this.submitting = false;
                this.submittingAndAddAnother = false;
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
        }
    }
}
</script>
@endsection
