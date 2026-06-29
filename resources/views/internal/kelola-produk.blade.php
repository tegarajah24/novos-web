@extends('layouts.internal')

@section('title', 'Kelola Produk')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Kelola Produk</h1>
@endsection

@section('internal-content')
<div x-data="kelolaProdukApp()" x-init="initApp()" class="space-y-6">

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
                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center gap-2 px-4 py-2 bg-[#1a237e] text-white text-sm rounded-xl hover:bg-[#283593] transition-colors font-medium cursor-pointer">
                                    <i data-lucide="upload" class="w-4 h-4"></i> Pilih File
                                    <input type="file" x-ref="inputDepan" class="hidden" accept="image/*" @change="handleUploadDepan">
                                </label>
                                <span class="text-sm text-gray-500" x-text="formData.imageDepanPreview ? 'File dipilih' : 'No file chosen'"></span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Foto Tampak Belakang</label>
                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center gap-2 px-4 py-2 bg-[#1a237e] text-white text-sm rounded-xl hover:bg-[#283593] transition-colors font-medium cursor-pointer">
                                    <i data-lucide="upload" class="w-4 h-4"></i> Pilih File
                                    <input type="file" x-ref="inputBelakang" class="hidden" accept="image/*" @change="handleUploadBelakang">
                                </label>
                                <span class="text-sm text-gray-500" x-text="formData.imageBelakangPreview ? 'File dipilih' : 'No file chosen'"></span>
                            </div>
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
        formMode: 'create',
        showModal: false,
        submitting: false,

        categories: @json($categories),

        products: @json($products),

        filters: {
            search: '',
            category: ''
        },

            formData: {
                id: null,
                name: '',
                category_id: '',
                price: '',
                description: '',
                imageDepanPreview: null,
                imageBelakangPreview: null,
                kerah: '',
                bahan: '',
                jenis_potongan: '',
                lengan_jahitan: ''
            },

        initApp() {
            this.renderIcons();
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
                imageDepanPreview: null,
                imageBelakangPreview: null,
                kerah: '',
                bahan: '',
                jenis_potongan: '',
                lengan_jahitan: ''
            };
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
                imageDepanPreview: product.image_depan,
                imageBelakangPreview: product.image_belakang,
                kerah: product.kerah || '',
                bahan: product.bahan || '',
                jenis_potongan: product.jenis_potongan || '',
                lengan_jahitan: product.lengan_jahitan || ''
            };
            this.showModal = true;
            this.$nextTick(() => this.renderIcons());
        },

        closeForm() {
            this.showModal = false;
        },

        handleUploadDepan(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.formData.imageDepanPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        handleUploadBelakang(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.formData.imageBelakangPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
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

            const fileInput = this.$refs.inputDepan;
            if (fileInput && fileInput.files[0]) {
                fd.append('image', fileInput.files[0]);
            }

            const fileInputBelakang = this.$refs.inputBelakang;
            if (fileInputBelakang && fileInputBelakang.files[0]) {
                fd.append('image_belakang', fileInputBelakang.files[0]);
            }

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            let url = '{{ route("staf.kelola-produk.store") }}';
            let method = 'POST';

            if (this.formMode === 'edit') {
                url = '{{ url("staf/kelola-produk") }}/' + this.formData.id;
                method = 'PUT';
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
        }
    }
}
</script>
@endsection
