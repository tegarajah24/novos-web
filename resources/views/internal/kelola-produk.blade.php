@extends('layouts.internal')

@section('title', 'Katalog Produk')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Katalog Produk</h1>
    <p class="text-sm text-gray-500 mt-0.5">Kelola data jersey dan tampilan produk unggulan</p>
@endsection

@section('internal-content')
<div x-data="kelolaProdukApp()" x-init="initApp()" class="space-y-6">

    <!-- Tabel Data -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <!-- Search -->
                <div class="relative w-full md:max-w-xs">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </span>
                    <input type="text" x-model="filters.search" placeholder="Cari nama jersey..." class="input input-sm input-bordered w-full pl-9 rounded-lg border-gray-300 focus:border-[#1a237e] focus:ring-[#1a237e]/20">
                </div>

                <!-- Filter Kategori -->
                <select x-model="filters.category" class="select select-sm select-bordered rounded-lg border-gray-300 text-sm font-medium">
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
                        <th class="text-center">Hero Utama</th>
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
                                <label class="cursor-pointer inline-flex items-center">
                                    <input type="checkbox" class="toggle toggle-primary toggle-sm" 
                                           :checked="prod.is_featured" 
                                           @change="toggleFeatured(prod.id)">
                                </label>
                                <div x-show="prod.is_featured" class="text-[10px] font-bold text-[#1a237e] mt-1">AKTIF</div>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openEditForm(prod)" class="btn btn-xs bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 border-0 rounded-md flex items-center gap-1">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                    </button>
                                    <button @click="confirmDelete(prod.id)" class="btn btn-xs bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border-0 rounded-md flex items-center gap-1">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
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
    <style>
        #modal_tambah_produk::backdrop {
            background: transparent !important;
            background-color: transparent !important;
        }
    </style>
    <dialog id="modal_tambah_produk" class="modal backdrop:bg-transparent">
        <div class="modal-box max-w-3xl bg-white shadow-2xl">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between -mx-6 -mt-6 mb-6">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="box" class="w-5 h-5 text-[#1a237e]"></i>
                    <span x-text="formMode === 'create' ? 'Tambah Produk Baru' : 'Edit Produk'"></span>
                </h3>
                <button @click="closeForm()" class="btn btn-ghost btn-sm btn-circle">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="p-1">
                <form @submit.prevent="saveProduct" class="space-y-5">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.name" required class="input input-bordered w-full rounded-lg border-gray-300 focus:border-[#1a237e] focus:ring-[#1a237e]/20" placeholder="Contoh: Novos Performance Jersey">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Kategori <span class="text-red-500">*</span></label>
                            <select x-model="formData.category_id" required class="select select-bordered w-full rounded-lg border-gray-300 focus:border-[#1a237e] focus:ring-[#1a237e]/20">
                                <option value="" disabled>Pilih Kategori</option>
                                <template x-for="cat in categories" :key="cat.id">
                                    <option :value="cat.id" x-text="cat.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-gray-700">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" x-model="formData.price" required min="0" class="input input-bordered w-full rounded-lg border-gray-300 focus:border-[#1a237e] focus:ring-[#1a237e]/20" placeholder="Contoh: 150000">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-gray-700">Deskripsi Produk</label>
                        <textarea x-model="formData.description" class="textarea textarea-bordered w-full rounded-lg border-gray-300 focus:border-[#1a237e] focus:ring-[#1a237e]/20" rows="3" placeholder="Detail bahan, printing, dsb..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Foto Tampak Depan</label>
                            <input type="file" @change="handleUploadDepan" accept="image/*" class="file-input file-input-bordered w-full rounded-lg border-gray-300 focus:border-[#1a237e] focus:ring-[#1a237e]/20 text-sm">
                            <div x-show="formData.imageDepanPreview" class="mt-2 w-24 h-24 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                <img :src="formData.imageDepanPreview" class="object-cover w-full h-full">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Foto Tampak Belakang</label>
                            <input type="file" @change="handleUploadBelakang" accept="image/*" class="file-input file-input-bordered w-full rounded-lg border-gray-300 focus:border-[#1a237e] focus:ring-[#1a237e]/20 text-sm">
                            <div x-show="formData.imageBelakangPreview" class="mt-2 w-24 h-24 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                <img :src="formData.imageBelakangPreview" class="object-cover w-full h-full">
                            </div>
                        </div>
                    </div>

                    <div class="pt-5 border-t border-gray-100 flex items-center justify-end gap-3 mt-6">
                        <button type="button" @click="closeForm()" class="btn btn-outline border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg">
                            Batal
                        </button>
                        <button type="submit" class="btn bg-[#1a237e] hover:bg-[#283593] text-white border-0 rounded-lg flex items-center gap-1.5">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-transparent" @click="closeForm()">
            <button>close</button>
        </form>
    </dialog>

</div>

<script>
function kelolaProdukApp() {
    return {
        formMode: 'create',
        
        categories: [
            { id: 1, name: 'Sepak Bola' },
            { id: 2, name: 'Futsal' },
            { id: 3, name: 'Basket' },
            { id: 4, name: 'Running' },
            { id: 5, name: 'Gym' },
            { id: 6, name: 'Tenis' },
            { id: 7, name: 'E-Sports' }
        ],
        
        products: [],
        
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
            is_featured: false
        },
        
        initApp() {
            const stored = localStorage.getItem('nvs_dummy_products');
            if (stored) {
                this.products = JSON.parse(stored);
            } else {
                this.products = [
                    {
                        id: 1,
                        name: 'Novos Red Maroon FC',
                        category_id: 1,
                        price: 155000,
                        description: 'Bahan dry-fit premium dengan sublimasi anti luntur.',
                        image_depan: null,
                        image_belakang: null,
                        is_featured: true
                    },
                    {
                        id: 2,
                        name: 'Novos Velocity Runner',
                        category_id: 4,
                        price: 135000,
                        description: 'Ultra light running tee, sangat ringan dan cepat kering.',
                        image_depan: null,
                        image_belakang: null,
                        is_featured: false
                    },
                    {
                        id: 3,
                        name: 'Novos Hoop Legend',
                        category_id: 3,
                        price: 165000,
                        description: 'Setelan jersey basket lengkap dengan celana.',
                        image_depan: null,
                        image_belakang: null,
                        is_featured: false
                    }
                ];
                this.saveToStorage();
            }
            
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
                is_featured: false
            };
            document.getElementById('modal_tambah_produk').showModal();
            this.renderIcons();
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
                is_featured: product.is_featured
            };
            document.getElementById('modal_tambah_produk').showModal();
            this.renderIcons();
        },
        
        closeForm() {
            document.getElementById('modal_tambah_produk').close();
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
        
        saveProduct() {
            if (this.formMode === 'create') {
                const newId = this.products.length > 0 ? Math.max(...this.products.map(p => p.id)) + 1 : 1;
                this.products.push({
                    id: newId,
                    name: this.formData.name,
                    category_id: parseInt(this.formData.category_id),
                    price: parseInt(this.formData.price),
                    description: this.formData.description,
                    image_depan: this.formData.imageDepanPreview,
                    image_belakang: this.formData.imageBelakangPreview,
                    is_featured: this.formData.is_featured
                });
                
                window.Swal.fire({
                    title: 'Berhasil!',
                    text: 'Produk baru telah ditambahkan.',
                    icon: 'success',
                    confirmButtonColor: '#1a237e',
                    timer: 1500
                });
            } else {
                const idx = this.products.findIndex(p => p.id === this.formData.id);
                if (idx !== -1) {
                    this.products[idx] = {
                        ...this.products[idx],
                        name: this.formData.name,
                        category_id: parseInt(this.formData.category_id),
                        price: parseInt(this.formData.price),
                        description: this.formData.description,
                        image_depan: this.formData.imageDepanPreview,
                        image_belakang: this.formData.imageBelakangPreview,
                        is_featured: this.formData.is_featured
                    };
                    
                    window.Swal.fire({
                        title: 'Tersimpan!',
                        text: 'Perubahan produk telah disimpan.',
                        icon: 'success',
                        confirmButtonColor: '#1a237e',
                        timer: 1500
                    });
                }
            }
            
            if (this.formData.is_featured) {
                this.unfeatureOthers(this.formData.id || Math.max(...this.products.map(p=>p.id)));
            }
            
            this.saveToStorage();
            this.closeForm();
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
            }).then((result) => {
                if (result.isConfirmed) {
                    this.products = this.products.filter(p => p.id !== id);
                    this.saveToStorage();
                    window.Swal.fire({
                        title: 'Terhapus!',
                        text: 'Produk berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#1a237e',
                        timer: 1500
                    });
                }
            });
        },
        
        toggleFeatured(id) {
            const product = this.products.find(p => p.id === id);
            if (product) {
                const newVal = !product.is_featured;
                product.is_featured = newVal;
                
                if (newVal) {
                    this.unfeatureOthers(id);
                    window.Swal.fire({
                        title: 'Hero Beranda Diperbarui!',
                        text: `Produk ${product.name} sekarang menjadi model utama di beranda.`,
                        icon: 'success',
                        confirmButtonColor: '#1a237e',
                        timer: 2000
                    });
                }
                this.saveToStorage();
            }
        },
        
        unfeatureOthers(excludeId) {
            this.products.forEach(p => {
                if (p.id !== excludeId) {
                    p.is_featured = false;
                }
            });
        },
        
        saveToStorage() {
            localStorage.setItem('nvs_dummy_products', JSON.stringify(this.products));
        },
        
        renderIcons() {
            this.$nextTick(() => {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        }
    }
}
</script>
@endsection
