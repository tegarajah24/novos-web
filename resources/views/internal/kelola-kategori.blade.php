@extends('layouts.internal')

@section('title', 'Kelola Kategori')

@section('topbar-left')
    <h1 class="text-xl font-bold text-gray-900">Kelola Kategori</h1>
    <p class="text-sm text-gray-500 mt-0.5">Atur kategori produk</p>
@endsection

@section('internal-content')
<div x-data="kategoriApp()" x-init="init()">
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 text-sm">Daftar Kategori</h2>
            <button @click="openModal()" class="px-4 py-2 bg-[#1a237e] text-white text-xs font-semibold rounded-xl hover:bg-blue-900 transition-colors">
                + Tambah Kategori
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500">
                    <tr>
                        <th class="px-6 py-4 font-medium">Nama Kategori</th>
                        <th class="px-6 py-4 font-medium text-center">Jumlah Produk</th>
                        <th class="px-6 py-4 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="cat in categories" :key="cat.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="cat.name"></td>
                            <td class="px-6 py-4 text-center text-gray-600" x-text="cat.products_count"></td>
                            <td class="px-6 py-4 text-right">
                                <button @click="openModal(cat)" class="text-gray-400 hover:text-[#1a237e] p-1.5 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <button @click="hapus(cat)" class="text-gray-400 hover:text-red-600 p-1.5 hover:bg-red-50 rounded-lg transition-colors ml-1" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="categories.length === 0">
                        <td colspan="3" class="px-6 py-10 text-center text-gray-400">Belum ada kategori</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <template x-teleport="body">
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div x-show="modalOpen" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4" x-text="editId ? 'Edit Kategori' : 'Tambah Kategori'"></h3>
            <form @submit.prevent="simpan">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                    <input type="text" x-model="name" required
                           class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:ring-[#1a237e] focus:border-[#1a237e]"
                           placeholder="Contoh: Jersey Basket">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-blue-900 transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    </template>
</div>

<script>
function kategoriApp() {
    return {
        categories: [],
        modalOpen: false,
        editId: null,
        name: '',

        async init() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("staf.kategori.data") }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                });
                this.categories = await res.json();
            } catch (e) {}
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        openModal(cat) {
            if (cat) {
                this.editId = cat.id;
                this.name = cat.name;
            } else {
                this.editId = null;
                this.name = '';
            }
            this.modalOpen = true;
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        async simpan() {
            if (!this.name.trim()) return;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = this.editId
                ? '{{ route("staf.kategori.update", "") }}/' + this.editId
                : '{{ route("staf.kategori.store") }}';
            const method = this.editId ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: this.name.trim() })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false });
                    this.modalOpen = false;
                    this.init();
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan server.' });
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
                    const res = await fetch('{{ route("staf.kategori.destroy", "") }}/' + cat.id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false });
                        this.init();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                    }
                } catch (e) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan server.' });
                }
            });
        }
    }
}
</script>
@endsection
